from fastapi import APIRouter, Depends, Query
from sqlalchemy.orm import Session
from sqlalchemy import func, cast, Date
from datetime import date
from database import get_db
from models import Transaction, MachineStatus
from schemas import PublicStats, TransactionOut

router = APIRouter(prefix="/api/transactions", tags=["transactions"])


@router.get("/stats", response_model=PublicStats)
def get_stats(db: Session = Depends(get_db)):
    today = date.today()
    today_bottles = db.query(func.count(Transaction.id)).filter(
        func.date(Transaction.created_at) == today,
        Transaction.status == "Accepted",
    ).scalar() or 0
    today_rewards = db.query(func.sum(Transaction.reward_amount)).filter(
        func.date(Transaction.created_at) == today,
    ).scalar() or 0
    total_bottles = db.query(func.count(Transaction.id)).filter(
        Transaction.status == "Accepted"
    ).scalar() or 0
    machine = db.query(MachineStatus).order_by(MachineStatus.updated_at.desc()).first()
    return PublicStats(
        today_bottles=today_bottles,
        today_rewards=today_rewards,
        total_bottles=total_bottles,
        bin_level=machine.bin_level if machine else None,
        is_online=machine.is_online if machine else False,
    )


@router.get("/recent", response_model=list[TransactionOut])
def get_recent(limit: int = Query(15, le=50), db: Session = Depends(get_db)):
    return (
        db.query(Transaction)
        .order_by(Transaction.created_at.desc())
        .limit(limit)
        .all()
    )
