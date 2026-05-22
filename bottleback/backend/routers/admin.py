from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.orm import Session
from sqlalchemy import func
from datetime import date, datetime, timezone, timedelta
from database import get_db
from models import Transaction, MachineStatus, ContactMessage, User
from schemas import Token, AdminLogin, MachineUpdate, TransactionOut, ContactOut, UserOut
from auth import ADMIN_USERNAME, ADMIN_PASSWORD, create_token, get_current_admin

router = APIRouter(prefix="/api/admin", tags=["admin"])


@router.post("/login", response_model=Token)
def admin_login(payload: AdminLogin):
    if payload.username != ADMIN_USERNAME or payload.password != ADMIN_PASSWORD:
        raise HTTPException(status_code=401, detail="Invalid credentials")
    token = create_token({"sub": payload.username, "type": "admin"})
    return Token(access_token=token)


# ── Dashboard stats ─────────────────────────────────────────────
@router.get("/stats")
def admin_stats(_: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    today = date.today()
    today_bottles = db.query(func.count(Transaction.id)).filter(
        func.date(Transaction.created_at) == today, Transaction.status == "Accepted"
    ).scalar() or 0
    today_rewards = db.query(func.sum(Transaction.reward_amount)).filter(
        func.date(Transaction.created_at) == today
    ).scalar() or 0
    total_bottles = db.query(func.count(Transaction.id)).filter(
        Transaction.status == "Accepted"
    ).scalar() or 0
    total_rewards = db.query(func.sum(Transaction.reward_amount)).scalar() or 0
    rejected = db.query(func.count(Transaction.id)).filter(
        Transaction.status == "Rejected"
    ).scalar() or 0
    machine = db.query(MachineStatus).order_by(MachineStatus.updated_at.desc()).first()
    new_msgs = db.query(func.count(ContactMessage.id)).filter(
        func.date(ContactMessage.created_at) == today
    ).scalar() or 0

    # 7-day daily data
    daily = {}
    for i in range(6, -1, -1):
        d = (datetime.now() - timedelta(days=i)).date()
        daily[str(d)] = 0
    rows = db.query(
        func.date(Transaction.created_at).label("d"),
        func.count(Transaction.id).label("c"),
    ).filter(
        Transaction.status == "Accepted",
        func.date(Transaction.created_at) >= (date.today() - timedelta(days=6)),
    ).group_by(func.date(Transaction.created_at)).all()
    for row in rows:
        daily[str(row.d)] = row.c

    recent_tx = (
        db.query(Transaction).order_by(Transaction.created_at.desc()).limit(8).all()
    )

    return {
        "today_bottles": today_bottles,
        "today_rewards": today_rewards,
        "total_bottles": total_bottles,
        "total_rewards": total_rewards,
        "rejected": rejected,
        "bin_level": machine.bin_level if machine else 0,
        "new_msgs": new_msgs,
        "daily_data": daily,
        "recent_transactions": [
            {
                "id": t.id,
                "bottle_count": t.bottle_count,
                "reward_amount": t.reward_amount,
                "status": t.status,
                "node_id": t.node_id,
                "created_at": t.created_at.isoformat(),
            }
            for t in recent_tx
        ],
    }


# ── Transactions ────────────────────────────────────────────────
@router.get("/transactions")
def list_transactions(
    status: str = Query(""),
    q: str = Query(""),
    page: int = Query(1, ge=1),
    per_page: int = Query(20, le=100),
    _: str = Depends(get_current_admin),
    db: Session = Depends(get_db),
):
    query = db.query(Transaction)
    if status in ("Accepted", "Rejected"):
        query = query.filter(Transaction.status == status)
    if q:
        query = query.filter(Transaction.node_id.contains(q))
    total = query.count()
    items = query.order_by(Transaction.created_at.desc()).offset((page - 1) * per_page).limit(per_page).all()
    return {
        "total": total,
        "page": page,
        "per_page": per_page,
        "total_pages": max(1, -(-total // per_page)),
        "items": [
            {
                "id": t.id,
                "bottle_count": t.bottle_count,
                "reward_amount": t.reward_amount,
                "status": t.status,
                "node_id": t.node_id,
                "created_at": t.created_at.isoformat(),
            }
            for t in items
        ],
    }


@router.delete("/transactions/{tx_id}")
def delete_transaction(tx_id: int, _: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    tx = db.query(Transaction).get(tx_id)
    if not tx:
        raise HTTPException(status_code=404, detail="Not found")
    db.delete(tx)
    db.commit()
    return {"status": "deleted"}


@router.delete("/transactions")
def clear_transactions(_: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    db.query(Transaction).delete()
    db.commit()
    return {"status": "cleared"}


# ── Machine ─────────────────────────────────────────────────────
@router.get("/machine")
def get_machines(_: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    machines = db.query(MachineStatus).order_by(MachineStatus.updated_at.desc()).all()
    return [
        {
            "id": m.id,
            "node_id": m.node_id,
            "bin_level": m.bin_level,
            "is_online": m.is_online,
            "updated_at": m.updated_at.isoformat(),
        }
        for m in machines
    ]


@router.patch("/machine/{node_id}")
def update_machine(
    node_id: str,
    payload: MachineUpdate,
    _: str = Depends(get_current_admin),
    db: Session = Depends(get_db),
):
    machine = db.query(MachineStatus).filter_by(node_id=node_id).first()
    if not machine:
        machine = MachineStatus(node_id=node_id)
        db.add(machine)
    if payload.bin_level is not None:
        machine.bin_level = max(0, min(100, payload.bin_level))
    if payload.is_online is not None:
        machine.is_online = payload.is_online
    machine.updated_at = datetime.now(timezone.utc)
    db.commit()
    db.refresh(machine)
    return {"status": "updated", "bin_level": machine.bin_level, "is_online": machine.is_online}


# ── Messages ────────────────────────────────────────────────────
@router.get("/messages")
def list_messages(
    q: str = Query(""),
    page: int = Query(1, ge=1),
    per_page: int = Query(15, le=100),
    _: str = Depends(get_current_admin),
    db: Session = Depends(get_db),
):
    query = db.query(ContactMessage)
    if q:
        query = query.filter(
            ContactMessage.name.contains(q)
            | ContactMessage.email.contains(q)
            | ContactMessage.subject.contains(q)
        )
    total = query.count()
    items = query.order_by(ContactMessage.created_at.desc()).offset((page - 1) * per_page).limit(per_page).all()
    return {
        "total": total,
        "total_pages": max(1, -(-total // per_page)),
        "items": [
            {
                "id": m.id,
                "name": m.name,
                "email": m.email,
                "subject": m.subject,
                "message": m.message,
                "created_at": m.created_at.isoformat(),
            }
            for m in items
        ],
    }


@router.get("/messages/{msg_id}")
def get_message(msg_id: int, _: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    msg = db.query(ContactMessage).get(msg_id)
    if not msg:
        raise HTTPException(status_code=404, detail="Not found")
    return {"id": msg.id, "name": msg.name, "email": msg.email, "subject": msg.subject, "message": msg.message, "created_at": msg.created_at.isoformat()}


@router.delete("/messages/{msg_id}")
def delete_message(msg_id: int, _: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    msg = db.query(ContactMessage).get(msg_id)
    if not msg:
        raise HTTPException(status_code=404, detail="Not found")
    db.delete(msg)
    db.commit()
    return {"status": "deleted"}


@router.delete("/messages")
def clear_messages(_: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    db.query(ContactMessage).delete()
    db.commit()
    return {"status": "cleared"}


# ── Users ───────────────────────────────────────────────────────
@router.get("/users")
def list_users(
    q: str = Query(""),
    page: int = Query(1, ge=1),
    per_page: int = Query(20, le=100),
    _: str = Depends(get_current_admin),
    db: Session = Depends(get_db),
):
    query = db.query(User)
    if q:
        query = query.filter(
            User.first_name.contains(q)
            | User.last_name.contains(q)
            | User.email.contains(q)
            | User.barangay.contains(q)
        )
    total = query.count()
    kpi_total = db.query(func.count(User.id)).scalar() or 0
    kpi_active = db.query(func.count(User.id)).filter(User.is_active == True).scalar() or 0
    kpi_today = db.query(func.count(User.id)).filter(func.date(User.created_at) == date.today()).scalar() or 0
    items = query.order_by(User.created_at.desc()).offset((page - 1) * per_page).limit(per_page).all()
    return {
        "total": total,
        "total_pages": max(1, -(-total // per_page)),
        "kpi_total": kpi_total,
        "kpi_active": kpi_active,
        "kpi_today": kpi_today,
        "kpi_inactive": kpi_total - kpi_active,
        "items": [
            {
                "id": u.id,
                "first_name": u.first_name,
                "last_name": u.last_name,
                "email": u.email,
                "barangay": u.barangay,
                "total_bottles": u.total_bottles,
                "total_rewards": u.total_rewards,
                "is_active": u.is_active,
                "created_at": u.created_at.isoformat(),
            }
            for u in items
        ],
    }


@router.patch("/users/{user_id}/toggle")
def toggle_user(user_id: int, _: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    user = db.query(User).get(user_id)
    if not user:
        raise HTTPException(status_code=404, detail="Not found")
    user.is_active = not user.is_active
    db.commit()
    return {"status": "updated", "is_active": user.is_active}


@router.delete("/users/{user_id}")
def delete_user(user_id: int, _: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    user = db.query(User).get(user_id)
    if not user:
        raise HTTPException(status_code=404, detail="Not found")
    db.delete(user)
    db.commit()
    return {"status": "deleted"}


# ── Export ──────────────────────────────────────────────────────
@router.get("/export/transactions")
def export_transactions(_: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    rows = db.query(Transaction).order_by(Transaction.created_at.desc()).all()
    lines = ["id,bottle_count,reward_amount,status,node_id,created_at"]
    for t in rows:
        lines.append(f"{t.id},{t.bottle_count},{t.reward_amount},{t.status},{t.node_id},{t.created_at.isoformat()}")
    from fastapi.responses import PlainTextResponse
    return PlainTextResponse(
        "\n".join(lines),
        headers={"Content-Disposition": "attachment; filename=transactions.csv"},
        media_type="text/csv",
    )


@router.get("/export/messages")
def export_messages(_: str = Depends(get_current_admin), db: Session = Depends(get_db)):
    rows = db.query(ContactMessage).order_by(ContactMessage.created_at.desc()).all()
    lines = ["id,name,email,subject,created_at"]
    for m in rows:
        lines.append(f'{m.id},"{m.name}",{m.email},"{m.subject}",{m.created_at.isoformat()}')
    from fastapi.responses import PlainTextResponse
    return PlainTextResponse(
        "\n".join(lines),
        headers={"Content-Disposition": "attachment; filename=messages.csv"},
        media_type="text/csv",
    )
