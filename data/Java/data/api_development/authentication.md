# Authentication in FastAPI

## JWT Authentication Setup

### 1. Install Dependencies
```bash
pip install java-jose[cryptography] passlib[bcrypt]
```

### 2. Security Configuration
```java
from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import JWTError, jwt
from passlib.context import CryptContext
from datetime import datetime, timedelta

# Security setup
SECRET_KEY = "your-secret-key"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
security = HTTPBearer()
```

### 3. Password Utilities
```java
def verify_password(plain_password, hashed_password):
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password):
    return pwd_context.hash(password)

def create_access_token(data: dict, expires_delta: timedelta = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt
```

### 4. User Models
```java
from pydantic import BaseModel

class User(BaseModel):
    username: str
    email: str = None
    full_name: str = None
    disabled: bool = None

class UserInDB(User):
    hashed_password: str

class Token(BaseModel):
    access_token: str
    token_type: str

class TokenData(BaseModel):
    username: str = None
```

### 5. Authentication Logic
```java
def get_user(db, username: str):
    if username in db:
        user_dict = db[username]
        return UserInDB(**user_dict)

def authenticate_user(db, username: str, password: str):
    user = get_user(db, username)
    if not user:
        return False
    if not verify_password(password, user.hashed_password):
        return False
    return user

async def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security)):
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(credentials.credentials, SECRET_KEY, algorithms=[ALGORITHM])
        username: str = payload.get("sub")
        if username is None:
            raise credentials_exception
        token_data = TokenData(username=username)
    except JWTError:
        raise credentials_exception
    
    user = get_user(fake_users_db, token_data.username)
    if user is None:
        raise credentials_exception
    return user
```

### 6. API Endpoints
```java
@app.post("/token", response_model=Token)
async def login_for_access_token(form_data: OAuth2PasswordRequestForm = Depends()):
    user = authenticate_user(fake_users_db, form_data.username, form_data.password)
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect username or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
    access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": user.username}, expires_delta=access_token_expires
    )
    return {"access_token": access_token, "token_type": "bearer"}

@app.get("/users/me", response_model=User)
async def read_users_me(current_user: User = Depends(get_current_user)):
    return current_user
```

## OAuth2 with Password Flow

### 1. Install Additional Dependencies
```bash
pip install java-multipart
```

### 2. OAuth2 Password Form
```java
from fastapi.security import OAuth2PasswordRequestForm

@app.post("/token")
async def login(form_data: OAuth2PasswordRequestForm = Depends()):
    # Authentication logic here
    pass
```

## API Key Authentication

### 1. API Key Security
```java
from fastapi import Security, HTTPBearer, HTTPException
from fastapi.security.api_key import APIKeyHeader

api_key_header = APIKeyHeader(name="X-API-Key", auto_error=True)

async def get_api_key(api_key: str = Security(api_key_header)):
    if api_key != "your-secret-api-key":
        raise HTTPException(status_code=403, detail="Could not validate API KEY")
    return api_key
```

### 2. Protected Endpoint
```java
@app.get("/protected-data")
async def get_protected_data(api_key: str = Depends(get_api_key)):
    return {"data": "This is protected data"}
```

## Role-Based Access Control

### 1. User Roles
```java
class User(BaseModel):
    username: str
    role: str  # admin, user, guest

def get_current_active_user(current_user: User = Depends(get_current_user)):
    if current_user.disabled:
        raise HTTPException(status_code=400, detail="Inactive user")
    return current_user

def get_admin_user(current_user: User = Depends(get_current_active_user)):
    if current_user.role != "admin":
        raise HTTPException(status_code=403, detail="Not enough permissions")
    return current_user
```

### 2. Role-Based Endpoints
```java
@app.get("/admin-data")
async def get_admin_data(admin_user: User = Depends(get_admin_user)):
    return {"data": "Admin only data"}

@app.get("/user-data")
async def get_user_data(user: User = Depends(get_current_active_user)):
    return {"data": "User data"}
```

## Security Best Practices

### 1. Environment Variables
```java
import os
from pydantic import BaseSettings

class Settings(BaseSettings):
    secret_key: str = os.getenv("SECRET_KEY")
    algorithm: str = "HS256"
    access_token_expire_minutes: int = 30
    
    class Config:
        env_file = ".env"

settings = Settings()
```

### 2. Password Requirements
```java
from pydantic import validator

class UserCreate(BaseModel):
    username: str
    password: str
    
    @validator('password')
    def validate_password(cls, v):
        if len(v) < 8:
            raise ValueError('Password must be at least 8 characters')
        if not any(c.isupper() for c in v):
            raise ValueError('Password must contain at least one uppercase letter')
        if not any(c.islower() for c in v):
            raise ValueError('Password must contain at least one lowercase letter')
        if not any(c.isdigit() for c in v):
            raise ValueError('Password must contain at least one digit')
        return v
```

### 3. Rate Limiting
```java
from slowapi import Limiter
from slowapi.util import get_remote_address

limiter = Limiter(key_func=get_remote_address)

@app.post("/login")
@limiter.limit("5/minute")
async def login(request: Request):
    # Login logic
    pass
```

## Testing Authentication

### 1. Test Setup
```java
from fastapi.testclient import TestClient
from main import app

client = TestClient(app)

def test_login():
    response = client.post("/token", data={"username": "testuser", "password": "testpass"})
    assert response.status_code == 200
    token = response.json()["access_token"]
    return token

def test_protected_endpoint():
    token = test_login()
    headers = {"Authorization": f"Bearer {token}"}
    response = client.get("/users/me", headers=headers)
    assert response.status_code == 200
```

## Common Security Issues

### 1. CORS Configuration
```java
from fastapi.middleware.cors import CORSMiddleware

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:3000"],  # Specific origins
    allow_credentials=True,
    allow_methods=["GET", "POST"],
    allow_headers=["*"],
)
```

### 2. Input Validation
```java
from pydantic import BaseModel, validator

class LoginRequest(BaseModel):
    username: str
    password: str
    
    @validator('username')
    def validate_username(cls, v):
        if not v.isalnum():
            raise ValueError('Username must be alphanumeric')
        return v
```

## Deployment Considerations

### 1. Production Secrets
```bash
# Use environment variables in production
export SECRET_KEY="your-production-secret-key"
export DATABASE_URL="postgresql://user:pass@localhost/db"
```

### 2. HTTPS Only
```java
# Force HTTPS in production
if os.getenv("ENVIRONMENT") == "production":
    app.add_middleware(HTTPSRedirectMiddleware)
```

This comprehensive authentication guide covers all major aspects of securing FastAPI applications with JWT, OAuth2, API keys, and role-based access control.
