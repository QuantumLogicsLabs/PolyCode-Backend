"""
FastAPI REST API
Modern REST API with FastAPI, Pydantic models, and automatic documentation.
"""

from fastapi import FastAPI, HTTPException, Depends, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, EmailStr
from typing import List, Optional
from datetime import datetime, timedelta
import jwt
from passlib.context import CryptContext
import sqlite3
from contextlib import contextmanager

# FastAPI app initialization
app = FastAPI(
    title="Task Management API",
    description="A comprehensive task management REST API",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Security
security = HTTPBearer()
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
SECRET_KEY = "your-secret-key-here"
ALGORITHM = "HS256"

# Database setup
DATABASE_URL = "tasks.db"

@contextmanager
def get_db():
    """Database context manager."""
    conn = sqlite3.connect(DATABASE_URL)
    conn.row_factory = sqlite3.Row
    try:
        yield conn
    finally:
        conn.close

def init_db():
    """Initialize database tables."""
    with get_db() as conn:
        conn.execute("""
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        """)
        
        conn.execute("""
            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                status TEXT DEFAULT 'pending',
                priority TEXT DEFAULT 'medium',
                user_id INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id)
            )
        """)
        
        conn.commit()

# Pydantic models
class User(BaseModel):
    username: str
    email: EmailStr

class UserCreate(User):
    password: str

class UserResponse(User):
    id: int
    created_at: datetime
    
    class Config:
        from_attributes = True

class Task(BaseModel):
    title: str
    description: Optional[str] = None
    status: str = "pending"
    priority: str = "medium"

class TaskCreate(Task):
    pass

class TaskUpdate(BaseModel):
    title: Optional[str] = None
    description: Optional[str] = None
    status: Optional[str] = None
    priority: Optional[str] = None

class TaskResponse(Task):
    id: int
    user_id: int
    created_at: datetime
    updated_at: datetime
    
    class Config:
        from_attributes = True

class Token(BaseModel):
    access_token: str
    token_type: str

class TokenData(BaseModel):
    username: Optional[str] = None

# Helper functions
def verify_password(plain_password: str, hashed_password: str) -> bool:
    """Verify password against hash."""
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password: str) -> str:
    """Generate password hash."""
    return pwd_context.hash(password)

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    """Create JWT access token."""
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

def verify_token(credentials: HTTPAuthorizationCredentials = Depends(security)):
    """Verify JWT token."""
    token = credentials.credentials
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        username: str = payload.get("sub")
        if username is None:
            raise credentials_exception
        token_data = TokenData(username=username)
    except jwt.PyJWTError:
        raise credentials_exception
    
    return token_data

def get_current_user(token_data: TokenData = Depends(verify_token)):
    """Get current user from token."""
    with get_db() as conn:
        cursor = conn.execute(
            "SELECT id, username, email, created_at FROM users WHERE username = ?",
            (token_data.username,)
        )
        user = cursor.fetchone()
        
        if user is None:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="User not found"
            )
        
        return dict(user)

# Authentication endpoints
@app.post("/register", response_model=UserResponse)
def register(user: UserCreate):
    """Register a new user."""
    try:
        with get_db() as conn:
            # Check if user already exists
            cursor = conn.execute(
                "SELECT id FROM users WHERE username = ? OR email = ?",
                (user.username, user.email)
            )
            if cursor.fetchone():
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Username or email already registered"
                )
            
            # Create new user
            password_hash = get_password_hash(user.password)
            cursor = conn.execute(
                "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)",
                (user.username, user.email, password_hash)
            )
            user_id = cursor.lastrowid
            conn.commit()
            
            # Return created user
            cursor = conn.execute(
                "SELECT id, username, email, created_at FROM users WHERE id = ?",
                (user_id,)
            )
            return dict(cursor.fetchone())
    
    except sqlite3.IntegrityError:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Username or email already registered"
        )

@app.post("/login", response_model=Token)
def login(user: UserCreate):
    """Authenticate user and return JWT token."""
    with get_db() as conn:
        cursor = conn.execute(
            "SELECT id, username, password_hash FROM users WHERE username = ?",
            (user.username,)
        )
        db_user = cursor.fetchone()
        
        if not db_user or not verify_password(user.password, db_user['password_hash']):
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Incorrect username or password",
                headers={"WWW-Authenticate": "Bearer"},
            )
        
        access_token_expires = timedelta(minutes=30)
        access_token = create_access_token(
            data={"sub": db_user['username']}, expires_delta=access_token_expires
        )
        
        return {"access_token": access_token, "token_type": "bearer"}

# Task management endpoints
@app.get("/tasks", response_model=List[TaskResponse])
def get_tasks(current_user: dict = Depends(get_current_user)):
    """Get all tasks for the current user."""
    with get_db() as conn:
        cursor = conn.execute(
            "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC",
            (current_user['id'],)
        )
        tasks = [dict(row) for row in cursor.fetchall()]
        return tasks

@app.post("/tasks", response_model=TaskResponse)
def create_task(task: TaskCreate, current_user: dict = Depends(get_current_user)):
    """Create a new task."""
    with get_db() as conn:
        cursor = conn.execute(
            """INSERT INTO tasks (title, description, status, priority, user_id) 
               VALUES (?, ?, ?, ?, ?)""",
            (task.title, task.description, task.status, task.priority, current_user['id'])
        )
        task_id = cursor.lastrowid
        conn.commit()
        
        cursor = conn.execute("SELECT * FROM tasks WHERE id = ?", (task_id,))
        return dict(cursor.fetchone())

@app.get("/tasks/{task_id}", response_model=TaskResponse)
def get_task(task_id: int, current_user: dict = Depends(get_current_user)):
    """Get a specific task."""
    with get_db() as conn:
        cursor = conn.execute(
            "SELECT * FROM tasks WHERE id = ? AND user_id = ?",
            (task_id, current_user['id'])
        )
        task = cursor.fetchone()
        
        if not task:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Task not found"
            )
        
        return dict(task)

@app.put("/tasks/{task_id}", response_model=TaskResponse)
def update_task(
    task_id: int, 
    task_update: TaskUpdate, 
    current_user: dict = Depends(get_current_user)
):
    """Update a task."""
    with get_db() as conn:
        # Check if task exists and belongs to user
        cursor = conn.execute(
            "SELECT * FROM tasks WHERE id = ? AND user_id = ?",
            (task_id, current_user['id'])
        )
        task = cursor.fetchone()
        
        if not task:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Task not found"
            )
        
        # Update task with provided fields
        update_fields = []
        update_values = []
        
        if task_update.title is not None:
            update_fields.append("title = ?")
            update_values.append(task_update.title)
        
        if task_update.description is not None:
            update_fields.append("description = ?")
            update_values.append(task_update.description)
        
        if task_update.status is not None:
            update_fields.append("status = ?")
            update_values.append(task_update.status)
        
        if task_update.priority is not None:
            update_fields.append("priority = ?")
            update_values.append(task_update.priority)
        
        if update_fields:
            update_fields.append("updated_at = CURRENT_TIMESTAMP")
            update_values.append(task_id)
            update_values.append(current_user['id'])
            
            conn.execute(
                f"UPDATE tasks SET {', '.join(update_fields)} WHERE id = ? AND user_id = ?",
                update_values
            )
            conn.commit()
        
        # Return updated task
        cursor = conn.execute(
            "SELECT * FROM tasks WHERE id = ? AND user_id = ?",
            (task_id, current_user['id'])
        )
        return dict(cursor.fetchone())

@app.delete("/tasks/{task_id}")
def delete_task(task_id: int, current_user: dict = Depends(get_current_user)):
    """Delete a task."""
    with get_db() as conn:
        cursor = conn.execute(
            "DELETE FROM tasks WHERE id = ? AND user_id = ?",
            (task_id, current_user['id'])
        )
        
        if cursor.rowcount == 0:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Task not found"
            )
        
        conn.commit()
        return {"message": "Task deleted successfully"}

# User endpoints
@app.get("/me", response_model=UserResponse)
def get_current_user_info(current_user: dict = Depends(get_current_user)):
    """Get current user information."""
    return current_user

@app.get("/users/{user_id}/tasks", response_model=List[TaskResponse])
def get_user_tasks(user_id: int, current_user: dict = Depends(get_current_user)):
    """Get tasks for a specific user (admin only)."""
    # In a real app, add admin check here
    with get_db() as conn:
        cursor = conn.execute(
            "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC",
            (user_id,)
        )
        tasks = [dict(row) for row in cursor.fetchall()]
        return tasks

# Statistics endpoint
@app.get("/stats")
def get_task_stats(current_user: dict = Depends(get_current_user)):
    """Get task statistics for the current user."""
    with get_db() as conn:
        cursor = conn.execute(
            """SELECT status, COUNT(*) as count 
               FROM tasks 
               WHERE user_id = ? 
               GROUP BY status""",
            (current_user['id'],)
        )
        status_stats = {row['status']: row['count'] for row in cursor.fetchall()}
        
        cursor = conn.execute(
            """SELECT priority, COUNT(*) as count 
               FROM tasks 
               WHERE user_id = ? 
               GROUP BY priority""",
            (current_user['id'],)
        )
        priority_stats = {row['priority']: row['count'] for row in cursor.fetchall()}
        
        cursor = conn.execute(
            "SELECT COUNT(*) as total FROM tasks WHERE user_id = ?",
            (current_user['id'],)
        )
        total_tasks = cursor.fetchone()['total']
        
        return {
            "total_tasks": total_tasks,
            "by_status": status_stats,
            "by_priority": priority_stats
        }

# Health check
@app.get("/health")
def health_check():
    """Health check endpoint."""
    return {"status": "healthy", "timestamp": datetime.utcnow()}

# Initialize database on startup
@app.on_event("startup")
def startup_event():
    """Initialize database on startup."""
    init_db()
    print("Database initialized successfully")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
