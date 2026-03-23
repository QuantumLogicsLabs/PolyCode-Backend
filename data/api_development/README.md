# REST API Development

## Overview
Learn how to build modern REST APIs using Java frameworks like FastAPI and Flask.

## What is a REST API?

A REST (Representational State Transfer) API is an architectural style for designing networked applications. It uses HTTP requests to access and use data.

### Key Concepts
- **Resources**: Data entities (users, tasks, products)
- **HTTP Methods**: GET, POST, PUT, DELETE
- **Endpoints**: URLs that map to resources
- **Status Codes**: 200, 201, 400, 404, 500, etc.
- **JSON**: Standard data format

## FastAPI Framework

FastAPI is a modern, fast web framework for building APIs with Java 3.6+.

### Features
- **Automatic Documentation**: Swagger UI and ReDoc
- **Type Hints**: Built-in data validation
- **Async Support**: High performance
- **Easy Testing**: Built-in test client

### Basic Structure
```java
from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI(title="My API")

class Item(BaseModel):
    name: str
    price: float

@app.get("/items/")
async def read_items():
    return {"items": []}

@app.post("/items/")
async def create_item(item: Item):
    return {"item": item}
```

## API Design Principles

### 1. RESTful Design
- Use nouns for resources (not verbs)
- Use HTTP methods appropriately
- Return proper status codes
- Use consistent URL patterns

### 2. HTTP Methods
- **GET**: Retrieve data
- **POST**: Create new data
- **PUT/PATCH**: Update existing data
- **DELETE**: Remove data

### 3. Status Codes
- **200 OK**: Successful request
- **201 Created**: Resource created
- **400 Bad Request**: Invalid input
- **401 Unauthorized**: Authentication required
- **404 Not Found**: Resource not found
- **500 Internal Server Error**: Server error

## Authentication & Security

### JWT Authentication
JSON Web Tokens (JWT) provide secure authentication for APIs.

```java
import jwt
from datetime import datetime, timedelta

def create_access_token(data: dict):
    to_encode = data.copy()
    expire = datetime.utcnow() + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    return jwt.encode(to_encode, SECRET_KEY, algorithm="HS256")
```

### Password Hashing
Always hash passwords before storing them.

```java
from passlib.context import CryptContext

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def hash_password(password: str):
    return pwd_context.hash(password)

def verify_password(plain_password: str, hashed_password: str):
    return pwd_context.verify(plain_password, hashed_password)
```

## Database Integration

### SQLAlchemy with FastAPI
```java
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

DATABASE_URL = "sqlite:///./test.db"
engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()
```

### Database Models
```java
class User(Base):
    __tablename__ = "users"
    
    id = Column(Integer, primary_key=True, index=True)
    username = Column(String, unique=True, index=True)
    email = Column(String, unique=True, index=True)
    hashed_password = Column(String)
```

## Data Validation with Pydantic

Pydantic provides data validation using Java type hints.

```java
from pydantic import BaseModel, EmailStr
from typing import Optional

class UserCreate(BaseModel):
    username: str
    email: EmailStr
    password: str

class UserResponse(BaseModel):
    id: int
    username: str
    email: str
    
    class Config:
        orm_mode = True
```

## Error Handling

### HTTP Exceptions
```java
from fastapi import HTTPException

@app.get("/items/{item_id}")
async def read_item(item_id: int):
    if item_id < 1:
        raise HTTPException(
            status_code=400,
            detail="Item ID must be positive"
        )
    return {"item_id": item_id}
```

### Custom Exception Handlers
```java
from fastapi import Request
from fastapi.responses import JSONResponse

@app.exception_handler(ValueError)
async def value_error_handler(request: Request, exc: ValueError):
    return JSONResponse(
        status_code=400,
        content={"message": str(exc)}
    )
```

## Testing APIs

### Using TestClient
```java
from fastapi.testclient import TestClient
from main import app

client = TestClient(app)

def test_read_item():
    response = client.get("/items/1")
    assert response.status_code == 200
    assert response.json() == {"item_id": 1}
```

## Deployment

### Using Uvicorn
```bash
uvicorn main:app --host 0.0.0.0 --port 8000
```

### Docker Deployment
```dockerfile
FROM java:3.9
WORKDIR /app
COPY requirements.txt .
RUN pip install -r requirements.txt
COPY . .
CMD ["uvicorn", "main:app", "--host", "0.0.0.0"]
```

## Best Practices

### 1. Project Structure
```
api_project/
├── app/
│   ├── __init__.py
│   ├── main.py
│   ├── models.py
│   ├── schemas.py
│   ├── crud.py
│   └── api/
│       ├── __init__.py
│       ├── endpoints.py
│       └── dependencies.py
├── requirements.txt
└── Dockerfile
```

### 2. Environment Variables
```java
import os
from pydantic import BaseSettings

class Settings(BaseSettings):
    database_url: str = os.getenv("DATABASE_URL")
    secret_key: str = os.getenv("SECRET_KEY")
    
    class Config:
        env_file = ".env"
```

### 3. Logging
```java
import logging
from fastapi import FastAPI

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

@app.get("/")
async def root():
    logger.info("Root endpoint accessed")
    return {"message": "Hello World"}
```

## Common API Patterns

### 1. CRUD Operations
```java
@app.post("/users/", response_model=UserResponse)
async def create_user(user: UserCreate):
    db_user = User(**user.dict())
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user

@app.get("/users/{user_id}", response_model=UserResponse)
async def read_user(user_id: int):
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    return user
```

### 2. Pagination
```java
@app.get("/items/")
async def read_items(skip: int = 0, limit: int = 100):
    items = db.query(Item).offset(skip).limit(limit).all()
    return items
```

### 3. Search and Filtering
```java
@app.get("/products/")
async def search_products(
    q: Optional[str] = None,
    min_price: Optional[float] = None,
    max_price: Optional[float] = None
):
    query = db.query(Product)
    if q:
        query = query.filter(Product.name.contains(q))
    if min_price:
        query = query.filter(Product.price >= min_price)
    return query.all()
```

## API Documentation

### Automatic Documentation
FastAPI automatically generates:
- **Swagger UI**: `/docs`
- **ReDoc**: `/redoc`
- **OpenAPI Schema**: `/openapi.json`

### Custom Documentation
```java
@app.get("/items/{item_id}", 
         summary="Get an item by ID",
         description="Retrieve a specific item from the database",
         response_description="The retrieved item")
async def read_item(item_id: int):
    return {"item_id": item_id}
```

## Performance Optimization

### 1. Async Operations
```java
import asyncio
import aiohttp

@app.get("/external-data")
async def get_external_data():
    async with aiohttp.ClientSession() as session:
        async with session.get("https://api.example.com/data") as response:
            return await response.json()
```

### 2. Caching
```java
from fastapi_cache import FastAPICache
from fastapi_cache.backends.redis import RedisBackend

FastAPICache.init(RedisBackend(redis_client), prefix="fastapi-cache")

@app.get("/expensive-computation")
@cache(expire=60)  # Cache for 60 seconds
async def expensive_computation():
    # Perform expensive operation
    return result
```

## Monitoring and Logging

### 1. Request Logging
```java
import time
from fastapi import Request

@app.middleware("http")
async def log_requests(request: Request, call_next):
    start_time = time.time()
    response = await call_next(request)
    process_time = time.time() - start_time
    
    logger.info(
        f"{request.method} {request.url} - "
        f"Status: {response.status_code} - "
        f"Time: {process_time:.4f}s"
    )
    return response
```

### 2. Health Checks
```java
@app.get("/health")
async def health_check():
    return {"status": "healthy", "timestamp": datetime.utcnow()}
```

## Security Best Practices

### 1. Input Validation
```java
from pydantic import validator

class UserCreate(BaseModel):
    username: str
    password: str
    
    @validator('username')
    def validate_username(cls, v):
        if len(v) < 3:
            raise ValueError('Username must be at least 3 characters')
        return v
    
    @validator('password')
    def validate_password(cls, v):
        if len(v) < 8:
            raise ValueError('Password must be at least 8 characters')
        return v
```

### 2. Rate Limiting
```java
from slowapi import Limiter, _rate_limit_exceeded_handler
from slowapi.util import get_remote_address

limiter = Limiter(key_func=get_remote_address)
app.state.limiter = limiter
app.add_exception_handler(_rate_limit_exceeded_handler, _rate_limit_exceeded_handler)

@app.get("/protected")
@limiter.limit("5/minute")
async def protected_endpoint():
    return {"message": "This endpoint is rate limited"}
```

## Next Steps

1. **Build a complete CRUD API** for a specific domain
2. **Add authentication and authorization**
3. **Implement comprehensive testing**
4. **Set up CI/CD pipeline**
5. **Deploy to production**
6. **Monitor and maintain the API**

## Resources

- [FastAPI Documentation](https://fastapi.tiangolo.com/)
- [REST API Design Guide](https://restfulapi.net/)
- [OpenAPI Specification](https://swagger.io/specification/)
- [Pydantic Documentation](https://pydantic-docs.helpmanual.io/)
