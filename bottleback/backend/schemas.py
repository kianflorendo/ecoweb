from datetime import datetime
from pydantic import BaseModel, EmailStr


# ── Transaction ────────────────────────────────────────────────
class TransactionCreate(BaseModel):
    bottle_count: int = 1
    reward_amount: int = 1
    status: str = "Accepted"
    bin_level: int | None = None
    node_id: str = "node_001"


class TransactionOut(BaseModel):
    id: int
    bottle_count: int
    reward_amount: int
    status: str
    node_id: str
    created_at: datetime

    model_config = {"from_attributes": True}


# ── Machine ────────────────────────────────────────────────────
class MachineOut(BaseModel):
    id: int
    node_id: str
    bin_level: int
    is_online: bool
    updated_at: datetime

    model_config = {"from_attributes": True}


class MachineUpdate(BaseModel):
    bin_level: int | None = None
    is_online: bool | None = None
    node_id: str = "node_001"


# ── Contact ────────────────────────────────────────────────────
class ContactCreate(BaseModel):
    name: str
    email: EmailStr
    subject: str
    message: str


class ContactOut(BaseModel):
    id: int
    name: str
    email: str
    subject: str
    message: str
    created_at: datetime

    model_config = {"from_attributes": True}


# ── User ───────────────────────────────────────────────────────
class UserCreate(BaseModel):
    first_name: str
    last_name: str
    email: EmailStr
    password: str
    barangay: str = "Muzon"


class UserLogin(BaseModel):
    email: EmailStr
    password: str


class UserOut(BaseModel):
    id: int
    first_name: str
    last_name: str
    email: str
    barangay: str
    total_bottles: int
    total_rewards: int
    is_active: bool
    created_at: datetime
    last_login: datetime | None

    model_config = {"from_attributes": True}


class UserUpdate(BaseModel):
    first_name: str | None = None
    last_name: str | None = None
    barangay: str | None = None
    password: str | None = None


# ── Auth ───────────────────────────────────────────────────────
class Token(BaseModel):
    access_token: str
    token_type: str = "bearer"


class AdminLogin(BaseModel):
    username: str
    password: str


# ── Stats ──────────────────────────────────────────────────────
class PublicStats(BaseModel):
    today_bottles: int
    today_rewards: int
    total_bottles: int
    bin_level: int | None
    is_online: bool
