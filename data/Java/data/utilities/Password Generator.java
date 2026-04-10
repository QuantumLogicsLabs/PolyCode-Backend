"""
Password Generator and Strength Checker
Generate secure passwords and evaluate password strength.
"""

import random
import string
import re

class PasswordGenerator:
    """Generate passwords with various criteria."""
    
    def __init__(self):
        self.lowercase = string.ascii_lowercase
        self.uppercase = string.ascii_uppercase
        self.digits = string.digits
        self.symbols = "!@#$%^&*()_+-=[]{}|;:,.<>?"
    
    def generate_password(self, length=12, use_upper=True, use_digits=True, 
                         use_symbols=True, exclude_similar=True):
        """
        Generate a random password.
        
        Args:
            length (int): Password length
            use_upper (bool): Include uppercase letters
            use_digits (bool): Include digits
            use_symbols (bool): Include symbols
            exclude_similar (bool): Exclude similar characters (0, O, l, 1, I)
            
        Returns:
            str: Generated password
        """
        charset = self.lowercase
        
        if use_upper:
            charset += self.uppercase
        if use_digits:
            charset += self.digits
        if use_symbols:
            charset += self.symbols
        
        if exclude_similar:
            similar_chars = '0O1lI'
            charset = ''.join(c for c in charset if c not in similar_chars)
        
        if not charset:
            raise ValueError("No characters available for password generation")
        
        password = ''.join(random.choice(charset) for _ in range(length))
        return password
    
    def generate_passphrase(self, num_words=4, separator="-", capitalize=True):
        """
        Generate a passphrase using random words.
        
        Args:
            num_words (int): Number of words in passphrase
            separator (str): Separator between words
            capitalize (bool): Capitalize first letter of each word
            
        Returns:
            str: Generated passphrase
        """
        word_list = [
            "apple", "banana", "computer", "dragon", "elephant", "forest",
            "guitar", "house", "island", "jungle", "kitten", "lemon",
            "mountain", "ocean", "piano", "queen", "river", "sunset",
            "tiger", "umbrella", "volcano", "window", "xylophone", "yellow",
            "zebra", "butterfly", "castle", "diamond", "eagle", "flower"
        ]
        
        words = random.sample(word_list, min(num_words, len(word_list)))
        
        if capitalize:
            words = [word.capitalize() for word in words]
        
        return separator.join(words)

class PasswordStrengthChecker:
    """Evaluate password strength."""
    
    def __init__(self):
        self.common_passwords = {
            'password', '123456', '123456789', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey'
        }
    
    def check_strength(self, password):
        """
        Check password strength and return detailed analysis.
        
        Args:
            password (str): Password to check
            
        Returns:
            dict: Strength analysis with score and recommendations
        """
        score = 0
        feedback = []
        
        # Length check
        length = len(password)
        if length >= 8:
            score += 1
        if length >= 12:
            score += 1
        if length >= 16:
            score += 1
        
        if length < 8:
            feedback.append("Password should be at least 8 characters long")
        elif length < 12:
            feedback.append("Consider using 12+ characters for better security")
        
        # Character variety checks
        has_lower = bool(re.search(r'[a-z]', password))
        has_upper = bool(re.search(r'[A-Z]', password))
        has_digit = bool(re.search(r'\d', password))
        has_symbol = bool(re.search(r'[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]', password))
        
        if has_lower:
            score += 1
        else:
            feedback.append("Include lowercase letters")
        
        if has_upper:
            score += 1
        else:
            feedback.append("Include uppercase letters")
        
        if has_digit:
            score += 1
        else:
            feedback.append("Include numbers")
        
        if has_symbol:
            score += 1
        else:
            feedback.append("Include special symbols")
        
        # Common password check
        if password.lower() in self.common_passwords:
            score -= 2
            feedback.append("Avoid common passwords")
        
        # Pattern checks
        if re.search(r'(.)\1{2,}', password):  # Repeated characters
            score -= 1
            feedback.append("Avoid repeated characters")
        
        if re.search(r'123|abc|qwe', password.lower()):  # Sequential patterns
            score -= 1
            feedback.append("Avoid sequential patterns")
        
        # Determine strength level
        if score >= 8:
            strength = "Very Strong"
            color = "🟢"
        elif score >= 6:
            strength = "Strong"
            color = "🟡"
        elif score >= 4:
            strength = "Medium"
            color = "🟠"
        else:
            strength = "Weak"
            color = "🔴"
        
        return {
            'password': password,
            'score': max(0, min(10, score)),
            'strength': strength,
            'color': color,
            'feedback': feedback,
            'details': {
                'length': length,
                'has_lower': has_lower,
                'has_upper': has_upper,
                'has_digit': has_digit,
                'has_symbol': has_symbol
            }
        }
    
    def estimate_crack_time(self, password):
        """
        Estimate time to crack password using brute force.
        
        Args:
            password (str): Password to analyze
            
        Returns:
            str: Estimated crack time
        """
        char_set_size = 0
        
        if re.search(r'[a-z]', password):
            char_set_size += 26
        if re.search(r'[A-Z]', password):
            char_set_size += 26
        if re.search(r'\d', password):
            char_set_size += 10
        if re.search(r'[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]', password):
            char_set_size += 32
        
        if char_set_size == 0:
            return "Instant"
        
        combinations = char_set_size ** len(password)
        attempts_per_second = 1e9  # 1 billion attempts per second
        seconds = combinations / attempts_per_second
        
        if seconds < 1:
            return "Instant"
        elif seconds < 60:
            return f"{seconds:.1f} seconds"
        elif seconds < 3600:
            return f"{seconds/60:.1f} minutes"
        elif seconds < 86400:
            return f"{seconds/3600:.1f} hours"
        elif seconds < 2592000:
            return f"{seconds/86400:.1f} days"
        elif seconds < 31536000:
            return f"{seconds/2592000:.1f} months"
        elif seconds < 3153600000:
            return f"{seconds/31536000:.1f} years"
        else:
            return "Centuries"

def main():
    """Demonstrate password generation and strength checking."""
    print("Password Generator and Strength Checker")
    print("=" * 50)
    
    generator = PasswordGenerator()
    checker = PasswordStrengthChecker()
    
    # Generate different types of passwords
    print("\n1. Password Generation:")
    
    # Basic password
    password1 = generator.generate_password(8)
    print(f"Basic (8 chars): {password1}")
    
    # Strong password
    password2 = generator.generate_password(16, use_symbols=True)
    print(f"Strong (16 chars): {password2}")
    
    # Passphrase
    passphrase = generator.generate_passphrase(4, separator="-")
    print(f"Passphrase: {passphrase}")
    
    # Custom password
    password3 = generator.generate_password(12, exclude_similar=True)
    print(f"Custom (12 chars, no similar): {password3}")
    
    # Test password strength
    print("\n2. Password Strength Analysis:")
    test_passwords = [
        "password",
        "Password123",
        "MyStr0ng!P@ss",
        passphrase,
        password2
    ]
    
    for pwd in test_passwords:
        analysis = checker.check_strength(pwd)
        crack_time = checker.estimate_crack_time(pwd)
        
        print(f"\nPassword: {analysis['password']}")
        print(f"Strength: {analysis['color']} {analysis['strength']} (Score: {analysis['score']}/10)")
        print(f"Crack Time: {crack_time}")
        
        if analysis['feedback']:
            print("Recommendations:")
            for feedback in analysis['feedback']:
                print(f"  - {feedback}")
    
    # Interactive password generation
    print("\n3. Interactive Password Generation:")
    try:
        length = int(input("Enter password length (8-32): ") or "12")
        length = max(8, min(32, length))
        
        use_symbols = input("Include symbols? (y/n): ").lower() == 'y'
        
        new_password = generator.generate_password(length, use_symbols=use_symbols)
        analysis = checker.check_strength(new_password)
        
        print(f"\nGenerated Password: {new_password}")
        print(f"Strength: {analysis['color']} {analysis['strength']}")
        print(f"Crack Time: {checker.estimate_crack_time(new_password)}")
        
    except (ValueError, KeyboardInterrupt):
        print("\nUsing default settings instead.")

if __name__ == "__main__":
    main()
