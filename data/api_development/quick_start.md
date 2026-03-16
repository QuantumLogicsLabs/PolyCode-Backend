# FastAPI Quick Start Guide

## Installation
```bash
pip install fastapi uvicorn
pip install passlib python-jose bcrypt
```

## Basic FastAPI App
```python
from fastapi import FastAPI

app = FastAPI(title="My First API")

@app.get("/")
async def root():
    return {"message": "Hello World"}

@app.get("/items/{item_id}")
async def read_item(item_id: int):
    return {"item_id": item_id}
```

## Run the App
```bash
uvicorn main:app --reload
```

## Access Documentation
- Swagger UI: http://localhost:8000/docs
- ReDoc: http://localhost:8000/redoc

## Data Models
```python
from pydantic import BaseModel

class Item(BaseModel):
    name: str
    description: str = None
    price: float
    tax: float = None

@app.post("/items/")
async def create_item(item: Item):
    return {"item": item}
```

## Query Parameters
```python
@app.get("/items/")
async def read_items(skip: int = 0, limit: int = 10):
    return {"skip": skip, "limit": limit}
```

## Path Parameters
```python
@app.get("/items/{item_id}")
async def read_item(item_id: int):
    return {"item_id": item_id}
```

## Request Body
```python
@app.post("/items/")
async def create_item(item: Item):
    return {"item": item}
```
