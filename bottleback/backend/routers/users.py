from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from sqlalchemy import func
from datetime import datetime, timezone
from database import get_db
from models import User, Transaction, MachineStatus
from schemas import UserCreate, UserLogin, UserOut, UserUpdate, Token
from auth import hash_password, verify_password, create_token, get_current_user_id

router = APIRouter(prefix="/api/users", tags=["users"])


@router.post("/register", response_model=Token)
def register(payload: UserCreate, db: Session = Depends(get_db)):
    existing = db.query(User).filter_by(email=payload.email).first()
    if existing:
        raise HTTPException(status_code=400, detail="Email already registered")
    user = User(
        first_name=payload.first_name,
        last_name=payload.last_name,
        email=payload.email,
        password_hash=hash_password(payload.password),
        barangay=payload.barangay,
    )
    db.add(user)
    db.commit()
    db.refresh(user)
    token = create_token({"sub": str(user.id), "type": "user"})
    return Token(access_token=token)


@router.post("/login", response_model=Token)
def login(payload: UserLogin, db: Session = Depends(get_db)):
    user = db.query(User).filter_by(email=payload.email).first()
    if not user:
        raise HTTPException(status_code=401, detail="No account found with that email")
    if not user.is_active:
        raise HTTPException(status_code=403, detail="Account deactivated")
    if not verify_password(payload.password, user.password_hash):
        raise HTTPException(status_code=401, detail="Incorrect password")
    user.last_login = datetime.now(timezone.utc)
    db.commit()
    token = create_token({"sub": str(user.id), "type": "user"})
    return Token(access_token=token)


@router.get("/me", response_model=UserOut)
def get_me(user_id: int = Depends(get_current_user_id), db: Session = Depends(get_db)):
    user = db.query(User).get(user_id)
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    return user


@router.patch("/me", response_model=UserOut)
def update_me(
    payload: UserUpdate,
    user_id: int = Depends(get_current_user_id),
    db: Session = Depends(get_db),
):
    user = db.query(User).get(user_id)
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    if payload.first_name is not None:
        user.first_name = payload.first_name
    if payload.last_name is not None:
        user.last_name = payload.last_name
    if payload.barangay is not None:
        user.barangay = payload.barangay
    if payload.password:
        user.password_hash = hash_password(payload.password)
    db.commit()
    db.refresh(user)
    return user


@router.get("/me/community")
def get_community(user_id: int = Depends(get_current_user_id), db: Session = Depends(get_db)):
    community_bottles = db.query(func.count(Transaction.id)).filter(
        Transaction.status == "Accepted"
    ).scalar() or 0
    machine = db.query(MachineStatus).order_by(MachineStatus.updated_at.desc()).first()
    recent_tx = (
        db.query(Transaction).order_by(Transaction.created_at.desc()).limit(6).all()
    )
    return {
        "community_bottles": community_bottles,
        "machine": {
            "bin_level": machine.bin_level if machine else 0,
            "is_online": machine.is_online if machine else False,
        },
        "recent_transactions": [
            {
                "id": t.id,
                "bottle_count": t.bottle_count,
                "reward_amount": t.reward_amount,
                "status": t.status,
                "created_at": t.created_at.isoformat(),
            }
            for t in recent_tx
        ],
    }
