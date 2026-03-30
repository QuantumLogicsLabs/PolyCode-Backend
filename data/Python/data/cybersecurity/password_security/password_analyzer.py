"""
Password Security Analyzer
==========================

Comprehensive password security analysis tool.
Demonstrates password strength evaluation, common patterns, and security best practices.
"""

import re
import hashlib
import secrets
import string
import time
from typing import Dict, List, Tuple, Optional
import json
import numpy as np
import matplotlib.pyplot as plt

class PasswordAnalyzer:
    """Password security analysis and evaluation tool"""
    
    def __init__(self):
        self.common_passwords = self._load_common_passwords()
        self.leaked_passwords = set()
        self.analysis_history = []
        
        # Character sets
        self.lowercase = string.ascii_lowercase
        self.uppercase = string.ascii_uppercase
        self.digits = string.digits
        self.special = "!@#$%^&*()_+-=[]{}|;:,.<>?"
        
        # Scoring weights
        self.length_weight = 0.3
        self.complexity_weight = 0.4
        self.pattern_weight = 0.2
        self.common_weight = 0.1
    
    def _load_common_passwords(self) -> set:
        """Load common password list"""
        # Top 100 most common passwords (simplified version)
        common_passwords = {
            '123456', 'password', '12345678', 'qwerty', '123456789', '12345',
            '1234', '111111', '1234567', 'dragon', '123123', 'baseball',
            'abc123', 'football', 'monkey', 'letmein', '696969', 'shadow',
            'master', '666666', 'qwertyuiop', '123321', 'mustang', '1234567890',
            'michael', '654321', 'pussy', 'superman', '1qaz2wsx', '7777777',
            'fuckyou', '121212', '000000', 'qazwsx', '123qwe', 'killer',
            'trustno1', 'jordan', 'jennifer', 'zxcvbnm', 'asdfgh', 'hunter',
            'buster', 'soccer', 'harley', 'batman', 'andrew', 'tigger',
            'sunshine', 'iloveyou', '2000', 'charlie', 'joshua', 'internet',
            'matrix', 'asshole', 'freedom', 'princess', 'ginger', 'silver',
            'anthony', 'scorpion', 'cameron', 'nicholas', 'merlin', 'thomas',
            'tomcat', 'daniel', 'patrick', 'robert', 'welcome', 'chelsea',
            'biteme', 'pepper', 'james', 'azerty', 'william', 'joseph',
            'michael1', 'maggie', 'sammy', 'patricia', 'matthew', 'scooter',
            'brandon', 'samantha', 'george', 'jessica', 'angel', 'diamond',
            'tiffany', 'steven', 'danielle', 'rangers', 'victoria', 'florida',
            'dakota', 'gandalf', 'raiders', 'redskins', 'eagles', 'yankees'
        }
        return common_passwords
    
    def check_common_passwords(self, password: str) -> Dict[str, bool]:
        """Check if password is in common password lists"""
        checks = {
            'in_top_100': password.lower() in self.common_passwords,
            'is_numeric': password.isdigit(),
            'is_alpha': password.isalpha(),
            'is_keyboard_pattern': self._is_keyboard_pattern(password),
            'is_repeated_char': self._is_repeated_character(password),
            'is_sequential': self._is_sequential(password)
        }
        return checks
    
    def _is_keyboard_pattern(self, password: str) -> bool:
        """Check if password follows keyboard patterns"""
        keyboard_patterns = [
            'qwertyuiop', 'asdfghjkl', 'zxcvbnm',
            'qazwsx', 'wsxedc', 'edcrfv', 'rfvtgb', 'tgbyhn', 'yhnujm',
            '1234567890', '0987654321'
        ]
        
        password_lower = password.lower()
        for pattern in keyboard_patterns:
            if pattern in password_lower or pattern[::-1] in password_lower:
                return True
        return False
    
    def _is_repeated_character(self, password: str) -> bool:
        """Check if password uses repeated characters"""
        if len(set(password)) == 1:
            return True
        return False
    
    def _is_sequential(self, password: str) -> bool:
        """Check if password contains sequential patterns"""
        # Check for sequential numbers
        for i in range(len(password) - 2):
            if (password[i:i+3].isdigit() and 
                int(password[i:i+3]) in range(1000)):
                return True
        return False
    
    def calculate_entropy(self, password: str) -> float:
        """Calculate password entropy"""
        char_types = 0
        
        if any(c in self.lowercase for c in password):
            char_types += len(self.lowercase)
        if any(c in self.uppercase for c in password):
            char_types += len(self.uppercase)
        if any(c in self.digits for c in password):
            char_types += len(self.digits)
        if any(c in self.special for c in password):
            char_types += len(self.special)
        
        if char_types == 0:
            return 0
        
        entropy = len(password) * np.log2(char_types)
        return entropy
    
    def analyze_complexity(self, password: str) -> Dict[str, bool]:
        """Analyze password complexity"""
        complexity = {
            'has_lowercase': any(c in self.lowercase for c in password),
            'has_uppercase': any(c in self.uppercase for c in password),
            'has_digits': any(c in self.digits for c in password),
            'has_special': any(c in self.special for c in password),
            'min_length': len(password) >= 8,
            'strong_length': len(password) >= 12,
            'very_strong_length': len(password) >= 16
        }
        return complexity
    
    def calculate_strength_score(self, password: str) -> Dict[str, float]:
        """Calculate comprehensive password strength score"""
        # Length score (0-100)
        length_score = min(100, len(password) * 5)
        
        # Complexity score (0-100)
        complexity = self.analyze_complexity(password)
        complexity_score = sum(complexity.values()) / len(complexity) * 100
        
        # Pattern penalty
        common_checks = self.check_common_passwords(password)
        pattern_penalty = sum(common_checks.values()) * 20
        
        # Entropy score (0-100)
        entropy = self.calculate_entropy(password)
        entropy_score = min(100, entropy / 4)  # Normalize to 0-100
        
        # Overall score
        overall_score = max(0, min(100, 
            length_score * self.length_weight + 
            complexity_score * self.complexity_weight + 
            entropy_score * 0.2 - 
            pattern_penalty * self.pattern_weight))
        
        return {
            'overall': overall_score,
            'length': length_score,
            'complexity': complexity_score,
            'entropy': entropy_score,
            'pattern_penalty': pattern_penalty
        }
    
    def estimate_crack_time(self, password: str) -> Dict[str, str]:
        """Estimate time to crack password"""
        entropy = self.calculate_entropy(password)
        
        # Different attack scenarios
        scenarios = {
            'online_throttled': 1000,  # 1000 guesses per second
            'online_unthrottled': 1000000,  # 1M guesses per second
            'offline_slow_hash': 10000000000,  # 10B guesses per second
            'offline_fast_hash': 1000000000000  # 1T guesses per second
        }
        
        crack_times = {}
        for scenario, guesses_per_sec in scenarios.items.items():
            total_combinations = 2 ** entropy
            seconds_to_crack = total_combinations / (2 * guesses_per_sec)
            
            if seconds_to_crack < 60:
                crack_times[scenario] = f"{seconds_to_crack:.2f} seconds"
            elif seconds_to_crack < 3600:
                crack_times[scenario] = f"{seconds_to_crack / 60:.2f} minutes"
            elif seconds_to_crack < 86400:
                crack_times[scenario] = f"{seconds_to_crack / 3600:.2f} hours"
            elif seconds_to_crack < 31536000:
                crack_times[scenario] = f"{seconds_to_crack / 86400:.2f} days"
            elif seconds_to_crack < 3153600000:
                crack_times[scenario] = f"{seconds_to_crack / 31536000:.2f} years"
            else:
                crack_times[scenario] = f"centuries"
        
        return crack_times
    
    def generate_password(self, length: int = 16, include_special: bool = True) -> str:
        """Generate secure random password"""
        chars = self.lowercase + self.uppercase + self.digits
        if include_special:
            chars += self.special
        
        password = ''.join(secrets.choice(chars) for _ in range(length))
        return password
    
    def generate_passphrase(self, word_count: int = 4, separator: str = '-') -> str:
        """Generate secure passphrase"""
        word_list = [
            'correct', 'horse', 'battery', 'staple', 'trouble', 'monkey', 'dragon',
            'computer', 'keyboard', 'monitor', 'speaker', 'printer', 'scanner',
            'internet', 'network', 'server', 'client', 'database', 'firewall',
            'security', 'privacy', 'encryption', 'decryption', 'authentication',
            'authorization', 'validation', 'verification', 'configuration'
        ]
        
        words = [secrets.choice(word_list) for _ in range(word_count)]
        return separator.join(words)
    
    def analyze_password(self, password: str) -> Dict:
        """Comprehensive password analysis"""
        analysis = {
            'password': password,
            'length': len(password),
            'complexity': self.analyze_complexity(password),
            'common_checks': self.check_common_passwords(password),
            'entropy': self.calculate_entropy(password),
            'strength_scores': self.calculate_strength_score(password),
            'crack_times': self.estimate_crack_time(password),
            'recommendations': self._get_recommendations(password)
        }
        
        self.analysis_history.append(analysis)
        return analysis
    
    def _get_recommendations(self, password: str) -> List[str]:
        """Get password improvement recommendations"""
        recommendations = []
        complexity = self.analyze_complexity(password)
        common_checks = self.check_common_passwords(password)
        
        if len(password) < 12:
            recommendations.append("Use at least 12 characters")
        
        if not complexity['has_lowercase']:
            recommendations.append("Include lowercase letters")
        
        if not complexity['has_uppercase']:
            recommendations.append("Include uppercase letters")
        
        if not complexity['has_digits']:
            recommendations.append("Include numbers")
        
        if not complexity['has_special']:
            recommendations.append("Include special characters")
        
        if common_checks['in_top_100']:
            recommendations.append("Avoid common passwords")
        
        if common_checks['is_keyboard_pattern']:
            recommendations.append("Avoid keyboard patterns")
        
        if common_checks['is_sequential']:
            recommendations.append("Avoid sequential patterns")
        
        return recommendations
    
    def hash_password(self, password: str, algorithm: str = 'sha256') -> str:
        """Hash password using specified algorithm"""
        if algorithm == 'sha256':
            return hashlib.sha256(password.encode()).hexdigest()
        elif algorithm == 'sha512':
            return hashlib.sha512(password.encode()).hexdigest()
        elif algorithm == 'md5':
            return hashlib.md5(password.encode()).hexdigest()
        else:
            raise ValueError(f"Unsupported algorithm: {algorithm}")
    
    def check_password_leak(self, password_hash: str) -> bool:
        """Check if password hash is in leaked database (simulated)"""
        # In real implementation, this would check against HaveIBeenPwned API
        return password_hash in self.leaked_passwords
    
    def save_analysis_history(self, filename: str) -> None:
        """Save analysis history to file"""
        with open(filename, 'w') as f:
            json.dump(self.analysis_history, f, indent=2)
        
        print(f"Analysis history saved to {filename}")
    
    def visualize_strength_distribution(self) -> None:
        """Visualize password strength distribution"""
        if not self.analysis_history:
            print("No analysis history available")
            return
        
        strengths = [analysis['strength_scores']['overall'] 
                    for analysis in self.analysis_history]
        
        plt.figure(figsize=(10, 6))
        plt.hist(strengths, bins=20, alpha=0.7, color='skyblue', edgecolor='black')
        plt.xlabel('Password Strength Score')
        plt.ylabel('Frequency')
        plt.title('Password Strength Distribution')
        plt.grid(True, alpha=0.3)
        plt.show()

def main():
    """Main function to demonstrate password analyzer"""
    print("=== Password Security Analyzer ===\n")
    
    analyzer = PasswordAnalyzer()
    
    # Test passwords
    test_passwords = [
        'password',
        '123456',
        'Password123',
        'MyStr0ng!P@ssw0rd',
        'correct-horse-battery-staple',
        'Qwerty123!',
        'aaaaaaaa',
        '1234567890',
        'MySecurePassword2023!',
        'xG7!kP9@mN2$qR5#'
    ]
    
    print("Analyzing test passwords...\n")
    
    for password in test_passwords:
        print(f"Analyzing: '{password}'")
        analysis = analyzer.analyze_password(password)
        
        print(f"  Length: {analysis['length']}")
        print(f"  Entropy: {analysis['entropy']:.2f} bits")
        print(f"  Overall Strength: {analysis['strength_scores']['overall']:.1f}/100")
        
        # Show most relevant crack time
        crack_time = analysis['crack_times']['offline_slow_hash']
        print(f"  Estimated crack time (offline): {crack_time}")
        
        if analysis['recommendations']:
            print(f"  Recommendations: {', '.join(analysis['recommendations'])}")
        
        print()
    
    # Generate secure passwords
    print("=== Generating Secure Passwords ===")
    
    secure_password = analyzer.generate_password(16, True)
    print(f"Generated secure password: {secure_password}")
    
    secure_passphrase = analyzer.generate_passphrase(4, '-')
    print(f"Generated secure passphrase: {secure_passphrase}")
    
    # Analyze generated passwords
    print("\nAnalysis of generated passwords:")
    
    for pwd in [secure_password, secure_passphrase]:
        analysis = analyzer.analyze_password(pwd)
        print(f"'{pwd}': Strength {analysis['strength_scores']['overall']:.1f}/100")
    
    # Visualize strength distribution
    print("\n=== Password Strength Distribution ===")
    analyzer.visualize_strength_distribution()
    
    # Save analysis history
    analyzer.save_analysis_history('password_analysis_history.json')
    
    # Interactive password analysis
    print("\n=== Interactive Password Analysis ===")
    
    while True:
        user_password = input("Enter a password to analyze (or 'quit' to exit): ")
        
        if user_password.lower() == 'quit':
            break
        
        if not user_password:
            print("Please enter a password")
            continue
        
        analysis = analyzer.analyze_password(user_password)
        
        print(f"\nPassword Analysis for '{user_password}':")
        print(f"  Length: {analysis['length']} characters")
        print(f"  Entropy: {analysis['entropy']:.2f} bits")
        print(f"  Overall Strength: {analysis['strength_scores']['overall']:.1f}/100")
        
        print(f"  Complexity:")
        for key, value in analysis['complexity'].items():
            status = "✓" if value else "✗"
            print(f"    {key}: {status}")
        
        print(f"  Crack Time Estimates:")
        for scenario, time_str in analysis['crack_times'].items():
            print(f"    {scenario}: {time_str}")
        
        if analysis['recommendations']:
            print(f"  Recommendations:")
            for rec in analysis['recommendations']:
                print(f"    • {rec}")
        
        print()

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Run analyzer: python password_analyzer.py
2. Analyzes test passwords and shows security metrics
3. Generates secure passwords and passphrases
4. Provides interactive password analysis
5. Saves analysis history to JSON file

Key Concepts:
- Password Entropy: Measure of password unpredictability
- Complexity Analysis: Character type diversity
- Common Password Detection: Pattern recognition
- Crack Time Estimation: Security strength assessment
- Secure Generation: Cryptographically random passwords

Applications:
- Password policy enforcement
- Security auditing
- User education
- Password strength validation
- Security compliance
- Penetration testing

Security Features:
- Entropy calculation
- Common pattern detection
- Multiple hash algorithms
- Crack time estimation
- Secure password generation
- Comprehensive recommendations

Best Practices:
- Use minimum 12 characters
- Include mixed character types
- Avoid common patterns
- Use passphrases for memorability
- Enable two-factor authentication
- Regular password rotation
"""
