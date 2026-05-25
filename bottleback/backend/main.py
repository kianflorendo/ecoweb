from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from database import engine
from models import Base
from routers import esp32, transactions, machine, messages, users, admin

try:
    Base.metadata.create_all(bind=engine)
    from seed import seed
    seed()
except Exception as e:
    print(f"WARNING: Database not available at startup: {e}")
    print("Backend will start but DB operations will fail until connection is restored.")

app = FastAPI(title="BottleBack API", version="1.0.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=False,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(esp32.router)
app.include_router(transactions.router)
app.include_router(machine.router)
app.include_router(messages.router)
app.include_router(users.router)
app.include_router(admin.router)


@app.get("/")
def root():
    return {"project": "BottleBack", "version": "1.0.0", "status": "running"}
