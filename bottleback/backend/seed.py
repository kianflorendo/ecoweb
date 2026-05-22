from database import engine, SessionLocal
from models import Base, MachineStatus


def seed():
    Base.metadata.create_all(bind=engine)
    db = SessionLocal()
    try:
        existing = db.query(MachineStatus).filter_by(node_id="node_001").first()
        if not existing:
            db.add(MachineStatus(node_id="node_001", bin_level=0, is_online=False))
            db.commit()
            print("Seeded initial machine_status row.")
        else:
            print("Database already seeded.")
    finally:
        db.close()


if __name__ == "__main__":
    seed()
