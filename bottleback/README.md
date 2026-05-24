# BottleBack — FastAPI + React

Refactored from PHP/MySQL to **FastAPI (Python) + React (Vite) + SQLite**.

## Quick Start

### Backend
```bash
cd backend
pip install -r requirements.txt
 uvicorn main:app --host 0.0.0.0 --port 8000 --reload
# API runs at http://localhost:8000
```

### Frontend
```bash
cd frontend
npm install
npm run dev
# App runs at http://localhost:5173
```

## Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `bottleback2027` |

Change in `backend/auth.py` before deployment.

## ESP32 / Arduino Endpoint

```
POST http://localhost:8000/api/receive-data
Form fields: bottle_count, reward_amount, status, bin_level, node_id
```

## Project Structure

```
bottleback/
├── backend/
│   ├── main.py          ← FastAPI app entry
│   ├── database.py      ← SQLite + SQLAlchemy
│   ├── models.py        ← ORM models
│   ├── schemas.py       ← Pydantic schemas
│   ├── auth.py          ← JWT + admin credentials
│   ├── seed.py          ← DB initialization
│   └── routers/
│       ├── esp32.py     ← POST /api/receive-data
│       ├── transactions.py
│       ├── machine.py
│       ├── messages.py
│       ├── users.py
│       └── admin.py
└── frontend/
    └── src/
        ├── pages/       ← All pages (public + auth + admin)
        ├── components/  ← Nav, Footer, AdminLayout
        ├── hooks/       ← useAuth, useAdminAuth
        └── assets/css/  ← style.css, admin.css, auth.css
```
