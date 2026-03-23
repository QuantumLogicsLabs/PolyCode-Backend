# Database Integration with FastAPI

## SQLAlchemy Setup

### 1. Installation
```bash
pip install sqlalchemy
pip install psycopg2-binary  # For PostgreSQL
pip install pymysql          # For MySQL
```

### 2. Basic Configuration
```java
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

DATABASE_URL = "sqlite:///./test.db"
engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# Dependency to get DB session
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
```

## Database Models

### 1. User Model
```java
from sqlalchemy import Column, Integer, String, Boolean, DateTime
from sqlalchemy.sql import func

class User(Base):
    __tablename__ = "users"
    
    id = Column(Integer, primary_key=True, index=True)
    username = Column(String, unique=True, index=True)
    email = Column(String, unique=True, index=True)
    hashed_password = Column(String)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    updated_at = Column(DateTime(timezone=True), onupdate=func.now())
```

### 2. Product Model
```java
from sqlalchemy import Column, Integer, String, Float, ForeignKey
from sqlalchemy.orm import relationship

class Product(Base):
    __tablename__ = "products"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, index=True)
    description = Column(String)
    price = Column(Float)
    category_id = Column(Integer, ForeignKey("categories.id"))
    
    # Relationship
    category = relationship("Category", back_populates="products")

class Category(Base):
    __tablename__ = "categories"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, unique=True, index=True)
    
    # Relationship
    products = relationship("Product", back_populates="category")
```

## Pydantic Schemas

### 1. Base Schemas
```java
from pydantic import BaseModel
from typing import Optional
from datetime import datetime

class ProductBase(BaseModel):
    name: str
    description: Optional[str] = None
    price: float
    category_id: int

class ProductCreate(ProductBase):
    pass

class ProductUpdate(BaseModel):
    name: Optional[str] = None
    description: Optional[str] = None
    price: Optional[float] = None
    category_id: Optional[int] = None

class Product(ProductBase):
    id: int
    created_at: datetime
    
    class Config:
        orm_mode = True
```

### 2. Response Schemas
```java
class ProductResponse(Product):
    category: Optional["CategoryResponse"] = None

class CategoryBase(BaseModel):
    name: str

class Category(CategoryBase):
    id: int
    products: list[ProductResponse] = []
    
    class Config:
        orm_mode = True

# Forward reference resolution
ProductResponse.model_rebuild()
```

## CRUD Operations

### 1. Create Operations
```java
from sqlalchemy.orm import Session
from . import models, schemas

def create_product(db: Session, product: schemas.ProductCreate):
    db_product = models.Product(**product.dict())
    db.add(db_product)
    db.commit()
    db.refresh(db_product)
    return db_product

def create_user(db: Session, user: schemas.UserCreate):
    hashed_password = get_password_hash(user.password)
    db_user = models.User(
        email=user.email,
        hashed_password=hashed_password,
        username=user.username
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user
```

### 2. Read Operations
```java
def get_product(db: Session, product_id: int):
    return db.query(models.Product).filter(models.Product.id == product_id).first()

def get_products(db: Session, skip: int = 0, limit: int = 100):
    return db.query(models.Product).offset(skip).limit(limit).all()

def get_user_by_email(db: Session, email: str):
    return db.query(models.User).filter(models.User.email == email).first()
```

### 3. Update Operations
```java
def update_product(db: Session, product_id: int, product: schemas.ProductUpdate):
    db_product = db.query(models.Product).filter(models.Product.id == product_id).first()
    if db_product:
        update_data = product.dict(exclude_unset=True)
        for key, value in update_data.items():
            setattr(db_product, key, value)
        db.commit()
        db.refresh(db_product)
    return db_product
```

### 4. Delete Operations
```java
def delete_product(db: Session, product_id: int):
    db_product = db.query(models.Product).filter(models.Product.id == product_id).first()
    if db_product:
        db.delete(db_product)
        db.commit()
    return db_product
```

## API Endpoints with Database

### 1. CRUD Endpoints
```java
from fastapi import Depends, HTTPException
from sqlalchemy.orm import Session
from . import crud, models, schemas
from .database import get_db

@app.post("/products/", response_model=schemas.Product)
def create_product(product: schemas.ProductCreate, db: Session = Depends(get_db)):
    return crud.create_product(db=db, product=product)

@app.get("/products/", response_model=list[schemas.Product])
def read_products(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    products = crud.get_products(db, skip=skip, limit=limit)
    return products

@app.get("/products/{product_id}", response_model=schemas.Product)
def read_product(product_id: int, db: Session = Depends(get_db)):
    db_product = crud.get_product(db, product_id=product_id)
    if db_product is None:
        raise HTTPException(status_code=404, detail="Product not found")
    return db_product

@app.put("/products/{product_id}", response_model=schemas.Product)
def update_product(
    product_id: int, 
    product: schemas.ProductUpdate, 
    db: Session = Depends(get_db)
):
    db_product = crud.update_product(db, product_id=product_id, product=product)
    if db_product is None:
        raise HTTPException(status_code=404, detail="Product not found")
    return db_product

@app.delete("/products/{product_id}")
def delete_product(product_id: int, db: Session = Depends(get_db)):
    success = crud.delete_product(db, product_id=product_id)
    if not success:
        raise HTTPException(status_code=404, detail="Product not found")
    return {"message": "Product deleted successfully"}
```

## Database Migrations with Alembic

### 1. Installation
```bash
pip install alembic
```

### 2. Initialize Alembic
```bash
alembic init alembic
```

### 3. Configure Alembic
```java
# alembic.ini
sqlalchemy.url = sqlite:///./test.db

# alembic/env.py
from myapp.models import Base
target_metadata = Base.metadata
```

### 4. Create Migration
```bash
alembic revision --autogenerate -m "Create initial tables"
```

### 5. Apply Migration
```bash
alembic upgrade head
```

## Advanced Database Features

### 1. Relationships and Joins
```java
def get_products_with_categories(db: Session):
    return db.query(models.Product).join(models.Category).all()

def get_category_with_products(db: Session, category_id: int):
    return db.query(models.Category).filter(models.Category.id == category_id).first()
```

### 2. Filtering and Searching
```java
def search_products(db: Session, query: str):
    return db.query(models.Product).filter(
        models.Product.name.contains(query)
    ).all()

def filter_products_by_price(db: Session, min_price: float, max_price: float):
    return db.query(models.Product).filter(
        models.Product.price >= min_price,
        models.Product.price <= max_price
    ).all()
```

### 3. Pagination
```java
def get_products_paginated(db: Session, page: int = 1, size: int = 10):
    offset = (page - 1) * size
    return db.query(models.Product).offset(offset).limit(size).all()
```

## Database Testing

### 1. Test Database Setup
```java
import pytest
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from fastapi.testclient import TestClient

# Test database
SQLALCHEMY_DATABASE_URL = "sqlite:///./test.db"
engine = create_engine(SQLALCHEMY_DATABASE_URL, connect_args={"check_same_thread": False})
TestingSessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

@pytest.fixture(scope="function")
def db_session():
    Base.metadata.create_all(bind=engine)
    session = TestingSessionLocal()
    try:
        yield session
    finally:
        session.close()
        Base.metadata.drop_all(bind=engine)

@pytest.fixture(scope="function")
def client(db_session):
    def override_get_db():
        try:
            yield db_session
        finally:
            db_session.close()
    
    app.dependency_overrides[get_db] = override_get_db
    with TestClient(app) as test_client:
        yield test_client
    app.dependency_overrides.clear()
```

### 2. Database Tests
```java
def test_create_product(client: TestClient):
    response = client.post(
        "/products/",
        json={"name": "Test Product", "price": 10.99, "category_id": 1}
    )
    assert response.status_code == 200
    data = response.json()
    assert data["name"] == "Test Product"
    assert data["price"] == 10.99
```

## Performance Optimization

### 1. Database Indexing
```java
class Product(Base):
    __tablename__ = "products"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String, index=True)  # Add index for name searches
    price = Column(Float, index=True)  # Add index for price filters
    created_at = Column(DateTime, index=True)  # Add index for date queries
```

### 2. Query Optimization
```java
from sqlalchemy.orm import joinedload

def get_products_with_categories_optimized(db: Session):
    return db.query(models.Product).options(
        joinedload(models.Product.category)
    ).all()
```

### 3. Connection Pooling
```java
from sqlalchemy import create_engine

engine = create_engine(
    DATABASE_URL,
    pool_size=10,
    max_overflow=20,
    pool_pre_ping=True,
    pool_recycle=3600
)
```

## Production Considerations

### 1. Environment-Specific Configuration
```java
import os

if os.getenv("ENVIRONMENT") == "production":
    DATABASE_URL = os.getenv("DATABASE_URL")
    engine = create_engine(DATABASE_URL, pool_pre_ping=True)
else:
    DATABASE_URL = "sqlite:///./test.db"
    engine = create_engine(DATABASE_URL)
```

### 2. Database Health Checks
```java
@app.get("/health/db")
def check_database_health():
    try:
        db = SessionLocal()
        db.execute("SELECT 1")
        db.close()
        return {"status": "healthy"}
    except Exception as e:
        raise HTTPException(status_code=503, detail="Database unavailable")
```

This comprehensive database integration guide covers all aspects of working with databases in FastAPI applications.
