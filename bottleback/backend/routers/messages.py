from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from models import ContactMessage
from schemas import ContactCreate

router = APIRouter(prefix="/api/contact", tags=["contact"])


@router.post("")
def submit_contact(payload: ContactCreate, db: Session = Depends(get_db)):
    msg = ContactMessage(
        name=payload.name,
        email=payload.email,
        subject=payload.subject,
        message=payload.message,
    )
    db.add(msg)
    db.commit()
    return {"status": "success", "message": "Message received"}
