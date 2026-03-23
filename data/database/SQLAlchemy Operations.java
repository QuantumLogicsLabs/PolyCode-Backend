"""
Database Operations with SQLAlchemy
Comprehensive database operations using SQLAlchemy ORM.
"""

from sqlalchemy import create_engine, Column, Integer, String, Float, DateTime, Boolean, ForeignKey, Text
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, relationship, Session
from sqlalchemy.sql import func, and_, or_, not_
from datetime import datetime, timedelta
import json
from typing import List, Optional, Dict, Any

# Create base class for models
Base = declarative_base()

# Database Models
class User(Base):
    """User model with relationships."""
    __tablename__ = 'users'
    
    id = Column(Integer, primary_key=True)
    username = Column(String(50), unique=True, nullable=False, index=True)
    email = Column(String(100), unique=True, nullable=False, index=True)
    password_hash = Column(String(255), nullable=False)
    first_name = Column(String(50))
    last_name = Column(String(50))
    age = Column(Integer)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    orders = relationship("Order", back_populates="user", cascade="all, delete-orphan")
    reviews = relationship("Review", back_populates="user", cascade="all, delete-orphan")
    
    def __repr__(self):
        return f"<User(id={self.id}, username='{self.username}')>"
    
    def to_dict(self):
        """Convert user to dictionary."""
        return {
            'id': self.id,
            'username': self.username,
            'email': self.email,
            'first_name': self.first_name,
            'last_name': self.last_name,
            'age': self.age,
            'is_active': self.is_active,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }

class Product(Base):
    """Product model."""
    __tablename__ = 'products'
    
    id = Column(Integer, primary_key=True)
    name = Column(String(100), nullable=False, index=True)
    description = Column(Text)
    price = Column(Float, nullable=False)
    category = Column(String(50), nullable=False, index=True)
    stock_quantity = Column(Integer, default=0)
    is_available = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    order_items = relationship("OrderItem", back_populates="product", cascade="all, delete-orphan")
    reviews = relationship("Review", back_populates="product", cascade="all, delete-orphan")
    
    def __repr__(self):
        return f"<Product(id={self.id}, name='{self.name}', price={self.price})>"
    
    def to_dict(self):
        """Convert product to dictionary."""
        return {
            'id': self.id,
            'name': self.name,
            'description': self.description,
            'price': self.price,
            'category': self.category,
            'stock_quantity': self.stock_quantity,
            'is_available': self.is_available,
            'created_at': self.created_at.isoformat() if self.created_at else None
        }

class Order(Base):
    """Order model."""
    __tablename__ = 'orders'
    
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey('users.id'), nullable=False)
    order_date = Column(DateTime, default=datetime.utcnow)
    total_amount = Column(Float, nullable=False)
    status = Column(String(20), default='pending')  # pending, confirmed, shipped, delivered, cancelled
    shipping_address = Column(Text)
    
    # Relationships
    user = relationship("User", back_populates="orders")
    order_items = relationship("OrderItem", back_populates="order", cascade="all, delete-orphan")
    
    def __repr__(self):
        return f"<Order(id={self.id}, user_id={self.user_id}, total={self.total_amount})>"
    
    def to_dict(self):
        """Convert order to dictionary."""
        return {
            'id': self.id,
            'user_id': self.user_id,
            'order_date': self.order_date.isoformat() if self.order_date else None,
            'total_amount': self.total_amount,
            'status': self.status,
            'shipping_address': self.shipping_address
        }

class OrderItem(Base):
    """Order item model (junction table)."""
    __tablename__ = 'order_items'
    
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey('orders.id'), nullable=False)
    product_id = Column(Integer, ForeignKey('products.id'), nullable=False)
    quantity = Column(Integer, nullable=False)
    unit_price = Column(Float, nullable=False)
    
    # Relationships
    order = relationship("Order", back_populates="order_items")
    product = relationship("Product", back_populates="order_items")
    
    def __repr__(self):
        return f"<OrderItem(order_id={self.order_id}, product_id={self.product_id}, qty={self.quantity})>"
    
    def to_dict(self):
        """Convert order item to dictionary."""
        return {
            'id': self.id,
            'order_id': self.order_id,
            'product_id': self.product_id,
            'quantity': self.quantity,
            'unit_price': self.unit_price
        }

class Review(Base):
    """Review model."""
    __tablename__ = 'reviews'
    
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey('users.id'), nullable=False)
    product_id = Column(Integer, ForeignKey('products.id'), nullable=False)
    rating = Column(Integer, nullable=False)  # 1-5 stars
    comment = Column(Text)
    review_date = Column(DateTime, default=datetime.utcnow)
    
    # Relationships
    user = relationship("User", back_populates="reviews")
    product = relationship("Product", back_populates="reviews")
    
    def __repr__(self):
        return f"<Review(id={self.id}, product_id={self.product_id}, rating={self.rating})>"
    
    def to_dict(self):
        """Convert review to dictionary."""
        return {
            'id': self.id,
            'user_id': self.user_id,
            'product_id': self.product_id,
            'rating': self.rating,
            'comment': self.comment,
            'review_date': self.review_date.isoformat() if self.review_date else None
        }

class DatabaseManager:
    """Comprehensive database operations manager."""
    
    def __init__(self, database_url='sqlite:///ecommerce.db'):
        """Initialize database manager."""
        self.engine = create_engine(database_url, echo=False)
        self.SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=self.engine)
        
    def create_tables(self):
        """Create all database tables."""
        Base.metadata.create_all(bind=self.engine)
        print("Database tables created successfully!")
    
    def drop_tables(self):
        """Drop all database tables."""
        Base.metadata.drop_all(bind=self.engine)
        print("Database tables dropped!")
    
    def get_session(self) -> Session:
        """Get a database session."""
        return self.SessionLocal()
    
    # CRUD Operations
    def create_user(self, username: str, email: str, password_hash: str, 
                   first_name: str = None, last_name: str = None, age: int = None) -> User:
        """Create a new user."""
        with self.get_session() as session:
            user = User(
                username=username,
                email=email,
                password_hash=password_hash,
                first_name=first_name,
                last_name=last_name,
                age=age
            )
            session.add(user)
            session.commit()
            session.refresh(user)
            return user
    
    def get_user(self, user_id: int) -> Optional[User]:
        """Get user by ID."""
        with self.get_session() as session:
            return session.query(User).filter(User.id == user_id).first()
    
    def get_user_by_username(self, username: str) -> Optional[User]:
        """Get user by username."""
        with self.get_session() as session:
            return session.query(User).filter(User.username == username).first()
    
    def update_user(self, user_id: int, **kwargs) -> bool:
        """Update user information."""
        with self.get_session() as session:
            user = session.query(User).filter(User.id == user_id).first()
            if user:
                for key, value in kwargs.items():
                    if hasattr(user, key):
                        setattr(user, key, value)
                user.updated_at = datetime.utcnow()
                session.commit()
                return True
            return False
    
    def delete_user(self, user_id: int) -> bool:
        """Delete a user."""
        with self.get_session() as session:
            user = session.query(User).filter(User.id == user_id).first()
            if user:
                session.delete(user)
                session.commit()
                return True
            return False
    
    def create_product(self, name: str, price: float, category: str, 
                      description: str = None, stock_quantity: int = 0) -> Product:
        """Create a new product."""
        with self.get_session() as session:
            product = Product(
                name=name,
                price=price,
                category=category,
                description=description,
                stock_quantity=stock_quantity
            )
            session.add(product)
            session.commit()
            session.refresh(product)
            return product
    
    def get_products(self, category: str = None, min_price: float = None, 
                   max_price: float = None, available_only: bool = True) -> List[Product]:
        """Get products with optional filters."""
        with self.get_session() as session:
            query = session.query(Product)
            
            if category:
                query = query.filter(Product.category == category)
            if min_price:
                query = query.filter(Product.price >= min_price)
            if max_price:
                query = query.filter(Product.price <= max_price)
            if available_only:
                query = query.filter(Product.is_available == True)
            
            return query.all()
    
    def create_order(self, user_id: int, items: List[Dict[str, Any]], 
                    shipping_address: str = None) -> Order:
        """Create a new order with items."""
        with self.get_session() as session:
            # Calculate total amount
            total_amount = 0.0
            order_items = []
            
            for item_data in items:
                product = session.query(Product).filter(Product.id == item_data['product_id']).first()
                if product and product.is_available and product.stock_quantity >= item_data['quantity']:
                    total_amount += product.price * item_data['quantity']
                    order_items.append({
                        'product': product,
                        'quantity': item_data['quantity'],
                        'unit_price': product.price
                    })
            
            if not order_items:
                raise ValueError("No valid items in order")
            
            # Create order
            order = Order(
                user_id=user_id,
                total_amount=total_amount,
                shipping_address=shipping_address
            )
            session.add(order)
            session.flush()  # Get the order ID
            
            # Create order items
            for item in order_items:
                order_item = OrderItem(
                    order_id=order.id,
                    product_id=item['product'].id,
                    quantity=item['quantity'],
                    unit_price=item['unit_price']
                )
                session.add(order_item)
                
                # Update product stock
                item['product'].stock_quantity -= item['quantity']
            
            session.commit()
            session.refresh(order)
            return order
    
    def get_user_orders(self, user_id: int) -> List[Order]:
        """Get all orders for a user."""
        with self.get_session() as session:
            return session.query(Order).filter(Order.user_id == user_id).all()
    
    def create_review(self, user_id: int, product_id: int, rating: int, 
                     comment: str = None) -> Review:
        """Create a product review."""
        with self.get_session() as session:
            review = Review(
                user_id=user_id,
                product_id=product_id,
                rating=rating,
                comment=comment
            )
            session.add(review)
            session.commit()
            session.refresh(review)
            return review
    
    def get_product_reviews(self, product_id: int) -> List[Review]:
        """Get all reviews for a product."""
        with self.get_session() as session:
            return session.query(Review).filter(Review.product_id == product_id).all()
    
    # Advanced Queries
    def get_top_selling_products(self, limit: int = 10) -> List[Dict[str, Any]]:
        """Get top selling products by quantity."""
        with self.get_session() as session:
            result = session.query(
                Product.id,
                Product.name,
                Product.category,
                func.sum(OrderItem.quantity).label('total_sold'),
                func.sum(OrderItem.quantity * OrderItem.unit_price).label('total_revenue')
            ).join(OrderItem).group_by(Product.id).order_by(
                func.sum(OrderItem.quantity).desc()
            ).limit(limit).all()
            
            return [
                {
                    'id': row.id,
                    'name': row.name,
                    'category': row.category,
                    'total_sold': row.total_sold,
                    'total_revenue': row.total_revenue
                }
                for row in result
            ]
    
    def get_user_statistics(self, user_id: int) -> Dict[str, Any]:
        """Get comprehensive statistics for a user."""
        with self.get_session() as session:
            # Order statistics
            order_stats = session.query(
                func.count(Order.id).label('total_orders'),
                func.sum(Order.total_amount).label('total_spent'),
                func.avg(Order.total_amount).label('avg_order_value')
            ).filter(Order.user_id == user_id).first()
            
            # Review statistics
            review_stats = session.query(
                func.count(Review.id).label('total_reviews'),
                func.avg(Review.rating).label('avg_rating')
            ).filter(Review.user_id == user_id).first()
            
            return {
                'total_orders': order_stats.total_orders or 0,
                'total_spent': float(order_stats.total_spent or 0),
                'avg_order_value': float(order_stats.avg_order_value or 0),
                'total_reviews': review_stats.total_reviews or 0,
                'avg_rating': float(review_stats.avg_rating or 0)
            }
    
    def get_product_statistics(self, product_id: int) -> Dict[str, Any]:
        """Get comprehensive statistics for a product."""
        with self.get_session() as session:
            # Sales statistics
            sales_stats = session.query(
                func.sum(OrderItem.quantity).label('total_sold'),
                func.sum(OrderItem.quantity * OrderItem.unit_price).label('total_revenue'),
                func.count(OrderItem.id).label('order_count')
            ).filter(OrderItem.product_id == product_id).first()
            
            # Review statistics
            review_stats = session.query(
                func.count(Review.id).label('total_reviews'),
                func.avg(Review.rating).label('avg_rating')
            ).filter(Review.product_id == product_id).first()
            
            return {
                'total_sold': sales_stats.total_sold or 0,
                'total_revenue': float(sales_stats.total_revenue or 0),
                'order_count': sales_stats.order_count or 0,
                'total_reviews': review_stats.total_reviews or 0,
                'avg_rating': float(review_stats.avg_rating or 0)
            }
    
    def search_products(self, query: str, category: str = None) -> List[Product]:
        """Search products by name or description."""
        with self.get_session() as session:
            search_filter = or_(
                Product.name.ilike(f'%{query}%'),
                Product.description.ilike(f'%{query}%')
            )
            
            db_query = session.query(Product).filter(search_filter)
            
            if category:
                db_query = db_query.filter(Product.category == category)
            
            return db_query.all()
    
    def get_inventory_report(self) -> List[Dict[str, Any]]:
        """Get inventory report for all products."""
        with self.get_session() as session:
            products = session.query(Product).all()
            
            report = []
            for product in products:
                stats = self.get_product_statistics(product.id)
                report.append({
                    'id': product.id,
                    'name': product.name,
                    'category': product.category,
                    'stock_quantity': product.stock_quantity,
                    'price': product.price,
                    'total_sold': stats['total_sold'],
                    'total_revenue': stats['total_revenue'],
                    'avg_rating': stats['avg_rating']
                })
            
            return sorted(report, key=lambda x: x['total_revenue'], reverse=True)
    
    def backup_database(self, output_file: str = 'database_backup.json'):
        """Backup database to JSON file."""
        with self.get_session() as session:
            backup_data = {
                'users': [user.to_dict() for user in session.query(User).all()],
                'products': [product.to_dict() for product in session.query(Product).all()],
                'orders': [order.to_dict() for order in session.query(Order).all()],
                'order_items': [item.to_dict() for item in session.query(OrderItem).all()],
                'reviews': [review.to_dict() for review in session.query(Review).all()]
            }
            
            with open(output_file, 'w') as f:
                json.dump(backup_data, f, indent=2)
            
            print(f"Database backed up to {output_file}")
    
    def restore_database(self, input_file: str = 'database_backup.json'):
        """Restore database from JSON file."""
        with open(input_file, 'r') as f:
            backup_data = json.load(f)
        
        with self.get_session() as session:
            # Clear existing data
            session.query(Review).delete()
            session.query(OrderItem).delete()
            session.query(Order).delete()
            session.query(Product).delete()
            session.query(User).delete()
            session.commit()
            
            # Restore data
            for user_data in backup_data['users']:
                user = User(**user_data)
                session.add(user)
            
            for product_data in backup_data['products']:
                product = Product(**product_data)
                session.add(product)
            
            session.commit()
            
            # Add relationships
            for order_data in backup_data['orders']:
                order = Order(**order_data)
                session.add(order)
            
            for item_data in backup_data['order_items']:
                item = OrderItem(**item_data)
                session.add(item)
            
            for review_data in backup_data['reviews']:
                review = Review(**review_data)
                session.add(review)
            
            session.commit()
            print(f"Database restored from {input_file}")

def main():
    """Demonstrate comprehensive database operations."""
    print("COMPREHENSIVE DATABASE OPERATIONS")
    print("=" * 50)
    
    # Initialize database manager
    db = DatabaseManager()
    
    # Create tables
    print("1. Creating database tables...")
    db.create_tables()
    
    # Create sample data
    print("\n2. Creating sample data...")
    
    # Create users
    users = []
    user_data = [
        ('john_doe', 'john@example.com', 'hash1', 'John', 'Doe', 25),
        ('jane_smith', 'jane@example.com', 'hash2', 'Jane', 'Smith', 30),
        ('bob_wilson', 'bob@example.com', 'hash3', 'Bob', 'Wilson', 35),
        ('alice_brown', 'alice@example.com', 'hash4', 'Alice', 'Brown', 28)
    ]
    
    for username, email, password, first, last, age in user_data:
        user = db.create_user(username, email, password, first, last, age)
        users.append(user)
        print(f"Created user: {user.username}")
    
    # Create products
    products = []
    product_data = [
        ('Laptop', 999.99, 'Electronics', 'High-performance laptop', 50),
        ('Smartphone', 699.99, 'Electronics', 'Latest smartphone model', 100),
        ('Headphones', 199.99, 'Electronics', 'Wireless headphones', 200),
        ('Book', 29.99, 'Books', 'Java programming book', 500),
        ('Coffee Maker', 149.99, 'Appliances', 'Automatic coffee maker', 30),
        ('Running Shoes', 89.99, 'Sports', 'Professional running shoes', 75)
    ]
    
    for name, price, category, desc, stock in product_data:
        product = db.create_product(name, price, category, desc, stock)
        products.append(product)
        print(f"Created product: {product.name}")
    
    # Create orders
    print("\n3. Creating orders...")
    orders = []
    
    # Order 1
    order1_items = [
        {'product_id': products[0].id, 'quantity': 1},  # Laptop
        {'product_id': products[3].id, 'quantity': 2}   # Books
    ]
    order1 = db.create_order(users[0].id, order1_items, "123 Main St")
    orders.append(order1)
    print(f"Created order for {users[0].username}: ${order1.total_amount}")
    
    # Order 2
    order2_items = [
        {'product_id': products[1].id, 'quantity': 1},  # Smartphone
        {'product_id': products[2].id, 'quantity': 1}   # Headphones
    ]
    order2 = db.create_order(users[1].id, order2_items, "456 Oak Ave")
    orders.append(order2)
    print(f"Created order for {users[1].username}: ${order2.total_amount}")
    
    # Create reviews
    print("\n4. Creating reviews...")
    reviews = []
    review_data = [
        (users[0].id, products[0].id, 5, "Excellent laptop! Very fast."),
        (users[1].id, products[1].id, 4, "Good phone, battery life could be better."),
        (users[2].id, products[2].id, 5, "Best headphones I've ever owned!"),
        (users[3].id, products[3].id, 4, "Great book for learning Java.")
    ]
    
    for user_id, product_id, rating, comment in review_data:
        review = db.create_review(user_id, product_id, rating, comment)
        reviews.append(review)
        print(f"Created review: {rating} stars")
    
    # Advanced queries
    print("\n5. Advanced queries and statistics...")
    
    # Top selling products
    top_products = db.get_top_selling_products(3)
    print("\nTop 3 Selling Products:")
    for product in top_products:
        print(f"  {product['name']}: {product['total_sold']} sold, ${product['total_revenue']:.2f} revenue")
    
    # User statistics
    print(f"\nUser Statistics for {users[0].username}:")
    user_stats = db.get_user_statistics(users[0].id)
    for key, value in user_stats.items():
        print(f"  {key}: {value}")
    
    # Product statistics
    print(f"\nProduct Statistics for {products[0].name}:")
    product_stats = db.get_product_statistics(products[0].id)
    for key, value in product_stats.items():
        print(f"  {key}: {value}")
    
    # Search products
    print("\n6. Search functionality...")
    search_results = db.search_products("laptop")
    print(f"Products matching 'laptop': {len(search_results)} found")
    for product in search_results:
        print(f"  - {product.name}: ${product.price}")
    
    # Inventory report
    print("\n7. Inventory report...")
    inventory = db.get_inventory_report()
    print(f"Total products in inventory: {len(inventory)}")
    for item in inventory[:3]:  # Show top 3
        print(f"  {item['name']}: {item['stock_quantity']} in stock, ${item['total_revenue']:.2f} revenue")
    
    # Backup database
    print("\n8. Database backup...")
    db.backup_database('ecommerce_backup.json')
    
    print("\nDatabase operations demonstration complete!")

if __name__ == "__main__":
    main()
