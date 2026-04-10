"""
Testing with Pytest
Comprehensive testing examples using pytest framework.
"""

import pytest
import unittest
from unittest.mock import Mock, patch, MagicMock
import tempfile
import os
import json
from datetime import datetime
from typing import List, Dict, Any

# Sample classes for testing
class Calculator:
    """Simple calculator class for testing."""
    
    def add(self, a: float, b: float) -> float:
        """Add two numbers."""
        return a + b
    
    def subtract(self, a: float, b: float) -> float:
        """Subtract two numbers."""
        return a - b
    
    def multiply(self, a: float, b: float) -> float:
        """Multiply two numbers."""
        return a * b
    
    def divide(self, a: float, b: float) -> float:
        """Divide two numbers."""
        if b == 0:
            raise ValueError("Cannot divide by zero")
        return a / b
    
    def power(self, base: float, exponent: float) -> float:
        """Raise base to the power of exponent."""
        return base ** exponent

class UserManager:
    """User management system for testing."""
    
    def __init__(self):
        self.users = {}
        self.next_id = 1
    
    def create_user(self, username: str, email: str) -> Dict[str, Any]:
        """Create a new user."""
        if not username or not email:
            raise ValueError("Username and email are required")
        
        if username in self.users:
            raise ValueError("Username already exists")
        
        if '@' not in email:
            raise ValueError("Invalid email format")
        
        user = {
            'id': self.next_id,
            'username': username,
            'email': email,
            'created_at': datetime.now(),
            'is_active': True
        }
        
        self.users[username] = user
        self.next_id += 1
        
        return user
    
    def get_user(self, username: str) -> Dict[str, Any]:
        """Get user by username."""
        if username not in self.users:
            raise ValueError("User not found")
        return self.users[username]
    
    def update_user(self, username: str, **kwargs) -> Dict[str, Any]:
        """Update user information."""
        if username not in self.users:
            raise ValueError("User not found")
        
        user = self.users[username]
        
        for key, value in kwargs.items():
            if key in ['username', 'email']:
                if key == 'username' and value != username and value in self.users:
                    raise ValueError("Username already exists")
                if key == 'email' and '@' not in value:
                    raise ValueError("Invalid email format")
            user[key] = value
        
        return user
    
    def delete_user(self, username: str) -> bool:
        """Delete a user."""
        if username in self.users:
            del self.users[username]
            return True
        return False
    
    def list_users(self) -> List[Dict[str, Any]]:
        """List all users."""
        return list(self.users.values())

class DataProcessor:
    """Data processing class for testing."""
    
    def __init__(self):
        self.data = []
    
    def add_data(self, item: Any) -> None:
        """Add data item."""
        self.data.append(item)
    
    def get_data(self) -> List[Any]:
        """Get all data."""
        return self.data.copy()
    
    def clear_data(self) -> None:
        """Clear all data."""
        self.data.clear()
    
    def filter_data(self, condition) -> List[Any]:
        """Filter data based on condition."""
        return [item for item in self.data if condition(item)]
    
    def sort_data(self, key=None, reverse=False) -> List[Any]:
        """Sort data."""
        return sorted(self.data, key=key, reverse=reverse)
    
    def save_to_file(self, filename: str) -> None:
        """Save data to file."""
        with open(filename, 'w') as f:
            json.dump(self.data, f)
    
    def load_from_file(self, filename: str) -> None:
        """Load data from file."""
        with open(filename, 'r') as f:
            self.data = json.load(f)

# Pytest Fixtures
@pytest.fixture
def calculator():
    """Fixture providing a Calculator instance."""
    return Calculator()

@pytest.fixture
def user_manager():
    """Fixture providing a UserManager instance."""
    return UserManager()

@pytest.fixture
def data_processor():
    """Fixture providing a DataProcessor instance."""
    return DataProcessor()

@pytest.fixture
def sample_data():
    """Fixture providing sample data."""
    return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

@pytest.fixture
def temp_file():
    """Fixture providing a temporary file."""
    with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.json') as f:
        f.write('["test", "data"]')
        temp_filename = f.name
    
    yield temp_filename
    
    # Cleanup
    if os.path.exists(temp_filename):
        os.unlink(temp_filename)

# Pytest Tests
class TestCalculator:
    """Test cases for Calculator class."""
    
    def test_add_positive_numbers(self, calculator):
        """Test adding positive numbers."""
        assert calculator.add(2, 3) == 5
        assert calculator.add(10, 20) == 30
    
    def test_add_negative_numbers(self, calculator):
        """Test adding negative numbers."""
        assert calculator.add(-2, -3) == -5
        assert calculator.add(-10, 5) == -5
    
    def test_add_zero(self, calculator):
        """Test adding zero."""
        assert calculator.add(5, 0) == 5
        assert calculator.add(0, 0) == 0
    
    def test_subtract_positive_numbers(self, calculator):
        """Test subtracting positive numbers."""
        assert calculator.subtract(10, 3) == 7
        assert calculator.subtract(5, 5) == 0
    
    def test_subtract_negative_numbers(self, calculator):
        """Test subtracting negative numbers."""
        assert calculator.subtract(-5, -3) == -2
        assert calculator.subtract(5, -3) == 8
    
    def test_multiply_positive_numbers(self, calculator):
        """Test multiplying positive numbers."""
        assert calculator.multiply(3, 4) == 12
        assert calculator.multiply(10, 0) == 0
    
    def test_multiply_negative_numbers(self, calculator):
        """Test multiplying negative numbers."""
        assert calculator.multiply(-3, 4) == -12
        assert calculator.multiply(-3, -4) == 12
    
    def test_divide_positive_numbers(self, calculator):
        """Test dividing positive numbers."""
        assert calculator.divide(10, 2) == 5
        assert calculator.divide(6, 3) == 2
    
    def test_divide_by_zero(self, calculator):
        """Test division by zero raises exception."""
        with pytest.raises(ValueError, match="Cannot divide by zero"):
            calculator.divide(10, 0)
    
    def test_power_positive_exponent(self, calculator):
        """Test power with positive exponent."""
        assert calculator.power(2, 3) == 8
        assert calculator.power(5, 2) == 25
    
    def test_power_zero_exponent(self, calculator):
        """Test power with zero exponent."""
        assert calculator.power(5, 0) == 1
        assert calculator.power(0, 5) == 0
    
    @pytest.mark.parametrize("a,b,expected", [
        (2, 3, 5),
        (10, 20, 30),
        (-5, 5, 0),
        (0, 0, 0)
    ])
    def test_add_parametrized(self, calculator, a, b, expected):
        """Parametrized test for add method."""
        assert calculator.add(a, b) == expected

class TestUserManager:
    """Test cases for UserManager class."""
    
    def test_create_user_success(self, user_manager):
        """Test successful user creation."""
        user = user_manager.create_user("testuser", "test@example.com")
        
        assert user['username'] == "testuser"
        assert user['email'] == "test@example.com"
        assert user['id'] == 1
        assert user['is_active'] is True
        assert 'created_at' in user
    
    def test_create_user_missing_username(self, user_manager):
        """Test user creation with missing username."""
        with pytest.raises(ValueError, match="Username and email are required"):
            user_manager.create_user("", "test@example.com")
    
    def test_create_user_missing_email(self, user_manager):
        """Test user creation with missing email."""
        with pytest.raises(ValueError, match="Username and email are required"):
            user_manager.create_user("testuser", "")
    
    def test_create_user_duplicate_username(self, user_manager):
        """Test user creation with duplicate username."""
        user_manager.create_user("testuser", "test@example.com")
        
        with pytest.raises(ValueError, match="Username already exists"):
            user_manager.create_user("testuser", "another@example.com")
    
    def test_create_user_invalid_email(self, user_manager):
        """Test user creation with invalid email."""
        with pytest.raises(ValueError, match="Invalid email format"):
            user_manager.create_user("testuser", "invalid-email")
    
    def test_get_user_success(self, user_manager):
        """Test successful user retrieval."""
        created_user = user_manager.create_user("testuser", "test@example.com")
        retrieved_user = user_manager.get_user("testuser")
        
        assert retrieved_user['id'] == created_user['id']
        assert retrieved_user['username'] == created_user['username']
        assert retrieved_user['email'] == created_user['email']
    
    def test_get_user_not_found(self, user_manager):
        """Test retrieving non-existent user."""
        with pytest.raises(ValueError, match="User not found"):
            user_manager.get_user("nonexistent")
    
    def test_update_user_success(self, user_manager):
        """Test successful user update."""
        user_manager.create_user("testuser", "test@example.com")
        updated_user = user_manager.update_user("testuser", email="new@example.com")
        
        assert updated_user['email'] == "new@example.com"
    
    def test_delete_user_success(self, user_manager):
        """Test successful user deletion."""
        user_manager.create_user("testuser", "test@example.com")
        result = user_manager.delete_user("testuser")
        
        assert result is True
        with pytest.raises(ValueError, match="User not found"):
            user_manager.get_user("testuser")
    
    def test_delete_user_not_found(self, user_manager):
        """Test deleting non-existent user."""
        result = user_manager.delete_user("nonexistent")
        assert result is False
    
    def test_list_users_empty(self, user_manager):
        """Test listing users when empty."""
        users = user_manager.list_users()
        assert len(users) == 0
    
    def test_list_users_with_data(self, user_manager):
        """Test listing users with data."""
        user_manager.create_user("user1", "user1@example.com")
        user_manager.create_user("user2", "user2@example.com")
        
        users = user_manager.list_users()
        assert len(users) == 2

class TestDataProcessor:
    """Test cases for DataProcessor class."""
    
    def test_add_data(self, data_processor):
        """Test adding data."""
        data_processor.add_data("item1")
        data_processor.add_data("item2")
        
        data = data_processor.get_data()
        assert len(data) == 2
        assert "item1" in data
        assert "item2" in data
    
    def test_get_data_returns_copy(self, data_processor):
        """Test that get_data returns a copy."""
        data_processor.add_data("item1")
        data1 = data_processor.get_data()
        data2 = data_processor.get_data()
        
        assert data1 is not data2  # Different objects
        assert data1 == data2  # Same content
    
    def test_clear_data(self, data_processor):
        """Test clearing data."""
        data_processor.add_data("item1")
        data_processor.add_data("item2")
        
        assert len(data_processor.get_data()) == 2
        
        data_processor.clear_data()
        assert len(data_processor.get_data()) == 0
    
    def test_filter_data(self, data_processor, sample_data):
        """Test filtering data."""
        for item in sample_data:
            data_processor.add_data(item)
        
        # Filter even numbers
        even_numbers = data_processor.filter_data(lambda x: x % 2 == 0)
        assert even_numbers == [2, 4, 6, 8, 10]
        
        # Filter numbers greater than 5
        greater_than_5 = data_processor.filter_data(lambda x: x > 5)
        assert greater_than_5 == [6, 7, 8, 9, 10]
    
    def test_sort_data(self, data_processor, sample_data):
        """Test sorting data."""
        for item in sample_data:
            data_processor.add_data(item)
        
        # Sort ascending
        sorted_data = data_processor.sort_data()
        assert sorted_data == sorted(sample_data)
        
        # Sort descending
        sorted_desc = data_processor.sort_data(reverse=True)
        assert sorted_desc == sorted(sample_data, reverse=True)
    
    def test_save_and_load_file(self, data_processor, temp_file):
        """Test saving and loading from file."""
        test_data = ["item1", "item2", "item3"]
        
        for item in test_data:
            data_processor.add_data(item)
        
        # Save to file
        data_processor.save_to_file(temp_file)
        
        # Clear and load from file
        data_processor.clear_data()
        assert len(data_processor.get_data()) == 0
        
        data_processor.load_from_file(temp_file)
        loaded_data = data_processor.get_data()
        
        assert loaded_data == test_data

# Mocking Tests
class TestExternalAPI:
    """Test cases with mocking external APIs."""
    
    @patch('requests.get')
    def test_api_call_success(self, mock_get):
        """Test successful API call with mock."""
        # Mock response
        mock_response = Mock()
        mock_response.status_code = 200
        mock_response.json.return_value = {'data': 'test_data'}
        mock_get.return_value = mock_response
        
        # Test function that uses requests.get
        def fetch_data(url):
            import requests
            response = requests.get(url)
            if response.status_code == 200:
                return response.json()
            return None
        
        result = fetch_data('https://api.example.com/data')
        
        assert result == {'data': 'test_data'}
        mock_get.assert_called_once_with('https://api.example.com/data')
    
    @patch('requests.get')
    def test_api_call_failure(self, mock_get):
        """Test API call failure with mock."""
        # Mock response
        mock_response = Mock()
        mock_response.status_code = 404
        mock_get.return_value = mock_response
        
        def fetch_data(url):
            import requests
            response = requests.get(url)
            if response.status_code == 200:
                return response.json()
            return None
        
        result = fetch_data('https://api.example.com/data')
        
        assert result is None
        mock_get.assert_called_once_with('https://api.example.com/data')

# Integration Tests
class TestIntegration:
    """Integration tests combining multiple components."""
    
    def test_user_and_data_integration(self):
        """Test integration between UserManager and DataProcessor."""
        user_manager = UserManager()
        data_processor = DataProcessor()
        
        # Create users
        user1 = user_manager.create_user("user1", "user1@example.com")
        user2 = user_manager.create_user("user2", "user2@example.com")
        
        # Add user data to processor
        data_processor.add_data(user1)
        data_processor.add_data(user2)
        
        # Test data processing
        all_data = data_processor.get_data()
        assert len(all_data) == 2
        
        # Filter by username
        user1_data = data_processor.filter_data(lambda x: x['username'] == 'user1')
        assert len(user1_data) == 1
        assert user1_data[0]['username'] == 'user1'

# Performance Tests
class TestPerformance:
    """Performance-related tests."""
    
    def test_large_data_processing(self, data_processor):
        """Test processing large amounts of data."""
        import time
        
        # Add large dataset
        large_data = list(range(10000))
        start_time = time.time()
        
        for item in large_data:
            data_processor.add_data(item)
        
        add_time = time.time() - start_time
        
        # Test filtering
        start_time = time.time()
        even_numbers = data_processor.filter_data(lambda x: x % 2 == 0)
        filter_time = time.time() - start_time
        
        assert len(even_numbers) == 5000
        assert add_time < 1.0  # Should complete in less than 1 second
        assert filter_time < 0.5  # Filtering should be fast

# Unittest Examples
class TestCalculatorUnittest(unittest.TestCase):
    """Calculator tests using unittest framework."""
    
    def setUp(self):
        """Set up test fixtures."""
        self.calculator = Calculator()
    
    def test_add(self):
        """Test addition method."""
        self.assertEqual(self.calculator.add(2, 3), 5)
        self.assertEqual(self.calculator.add(-1, 1), 0)
    
    def test_divide_by_zero(self):
        """Test division by zero."""
        with self.assertRaises(ValueError):
            self.calculator.divide(10, 0)
    
    def tearDown(self):
        """Clean up after tests."""
        pass

# Test Configuration
@pytest.mark.slow
class TestSlowOperations:
    """Tests marked as slow."""
    
    def test_slow_operation(self):
        """Test that takes a long time."""
        import time
        time.sleep(0.1)  # Simulate slow operation
        assert True

# Custom Markers
def pytest_configure(config):
    """Configure custom pytest markers."""
    config.addinivalue_line("markers", "slow: marks tests as slow")
    config.addinivalue_line("markers", "integration: marks tests as integration tests")

# Test Utilities
def assert_valid_user(user):
    """Utility function to validate user object."""
    assert 'id' in user
    assert 'username' in user
    assert 'email' in user
    assert 'created_at' in user
    assert 'is_active' in user
    assert '@' in user['email']

def create_test_user_data():
    """Utility function to create test user data."""
    return [
        {'username': 'user1', 'email': 'user1@example.com'},
        {'username': 'user2', 'email': 'user2@example.com'},
        {'username': 'user3', 'email': 'user3@example.com'}
    ]

# Run tests if this file is executed directly
if __name__ == "__main__":
    print("This file contains pytest test cases.")
    print("Run tests with: pytest test_file.py")
    print("Run specific test: pytest test_file.py::TestCalculator::test_add_positive_numbers")
    print("Run with verbose output: pytest -v test_file.py")
    print("Run with coverage: pytest --cov=. test_file.py")
