from fastapi import APIRouter, Depends, Form
from sqlalchemy.orm import Session
from datetime import datetime, timezone
from database import get_db
from models import MachineStatus
from schemas import MachineOut

router = APIRouter(prefix="/api/machine", tags=["machine"])


@router.get("/status", response_model=MachineOut | None)
def get_status(db: Session = Depends(get_db)):
    return db.query(MachineStatus).order_by(MachineStatus.updated_at.desc()).first()


@router.post("/ping")
def machine_ping(
    node_id: str = Form("node_001"),
    bin_level: int = Form(0),
    db: Session = Depends(get_db),
):
    bin_level = max(0, min(100, bin_level))
    machine = db.query(MachineStatus).filter_by(node_id=node_id).first()
    if machine:
        machine.bin_level = bin_level
        machine.is_online = True
        machine.updated_at = datetime.now(timezone.utc)
    else:
        db.add(MachineStatus(node_id=node_id, bin_level=bin_level, is_online=True))
    db.commit()
    return {"status": "ok", "bin_level": bin_level}
