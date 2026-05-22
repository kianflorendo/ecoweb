from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from database import engine
from models import Base
from routers import esp32, transactions, machine, messages, users, admin
Base.metadata.create_all(bind=engine)

from seed import seed
seed()

app = FastAPI(title="BottleBack API", version="1.0.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:5173", "http://127.0.0.1:5173"],
    allow_credentials=True,
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
