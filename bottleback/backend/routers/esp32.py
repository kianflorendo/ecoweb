from fastapi import APIRouter, Depends, Form
from sqlalchemy.orm import Session
from datetime import datetime, timezone, timedelta
from database import get_db
from models import Transaction, MachineStatus

PHT = timezone(timedelta(hours=8))  # Philippine Time UTC+8

router = APIRouter(prefix="/api", tags=["esp32"])


@router.post("/receive-data")
def receive_data(
    bottle_count: int = Form(1),
    reward_amount: int = Form(1),
    status: str = Form("Accepted"),
    bin_level: int | None = Form(None),
    node_id: str = Form("node_001"),
    db: Session = Depends(get_db),
):
    if status not in ("Accepted", "Rejected"):
        status = "Accepted"

    now_pht = datetime.now(PHT)

    tx = Transaction(
        bottle_count=bottle_count,
        reward_amount=reward_amount,
        status=status,
        node_id=node_id,
        created_at=now_pht,
    )
    db.add(tx)
    db.flush()

    if bin_level is not None:
        bin_level = max(0, min(100, bin_level))
        machine = db.query(MachineStatus).filter_by(node_id=node_id).first()
        if machine:
            machine.bin_level = bin_level
            machine.is_online = True
            machine.updated_at = now_pht
        else:
            db.add(MachineStatus(node_id=node_id, bin_level=bin_level, is_online=True))

    db.commit()
    db.refresh(tx)
    return {
        "status": "success",
        "transaction_id": tx.id,
        "timestamp": tx.created_at.isoformat(),
    }
