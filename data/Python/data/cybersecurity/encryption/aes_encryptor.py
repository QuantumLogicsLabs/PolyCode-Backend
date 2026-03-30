"""
AES Encryption Tool
===================

Advanced Encryption Standard (AES) implementation for secure data encryption.
Demonstrates symmetric encryption, key management, and secure file handling.
"""

import os
import hashlib
import secrets
import base64
from typing import Union, Optional, Tuple
import json
import struct

try:
    from Crypto.Cipher import AES
    from Crypto.Util.Padding import pad, unpad
    from Crypto.Random import get_random_bytes
    CRYPTO_AVAILABLE = True
except ImportError:
    print("Warning: PyCryptodome not available. Using fallback implementation.")
    CRYPTO_AVAILABLE = False

class AESEncryptor:
    """AES encryption and decryption tool"""
    
    def __init__(self):
        self.key_size = 32  # 256 bits
        self.iv_size = 16   # 128 bits
        self.block_size = 16  # AES block size
        
        if not CRYPTO_AVAILABLE:
            print("Using fallback AES implementation (less secure)")
    
    def generate_key(self, password: str = None, salt: bytes = None) -> bytes:
        """Generate AES key from password or randomly"""
        if password:
            # Derive key from password using PBKDF2
            if salt is None:
                salt = get_random_bytes(16) if CRYPTO_AVAILABLE else os.urandom(16)
            
            # Use PBKDF2 to derive key
            key = hashlib.pbkdf2_hmac('sha256', password.encode(), salt, 100000)
            return key
        else:
            # Generate random key
            return get_random_bytes(self.key_size) if CRYPTO_AVAILABLE else os.urandom(self.key_size)
    
    def generate_iv(self) -> bytes:
        """Generate initialization vector"""
        return get_random_bytes(self.iv_size) if CRYPTO_AVAILABLE else os.urandom(self.iv_size)
    
    def encrypt_data(self, data: Union[str, bytes], key: bytes, iv: bytes = None) -> Tuple[bytes, bytes]:
        """Encrypt data using AES"""
        if iv is None:
            iv = self.generate_iv()
        
        if isinstance(data, str):
            data = data.encode('utf-8')
        
        if CRYPTO_AVAILABLE:
            # Use PyCryptodome implementation
            cipher = AES.new(key, AES.MODE_CBC, iv)
            padded_data = pad(data, AES.block_size)
            encrypted_data = cipher.encrypt(padded_data)
        else:
            # Fallback implementation (simplified)
            encrypted_data = self._fallback_encrypt(data, key, iv)
        
        return encrypted_data, iv
    
    def decrypt_data(self, encrypted_data: bytes, key: bytes, iv: bytes) -> bytes:
        """Decrypt data using AES"""
        if CRYPTO_AVAILABLE:
            # Use PyCryptodome implementation
            cipher = AES.new(key, AES.MODE_CBC, iv)
            decrypted_padded = cipher.decrypt(encrypted_data)
            decrypted_data = unpad(decrypted_padded, AES.block_size)
        else:
            # Fallback implementation
            decrypted_data = self._fallback_decrypt(encrypted_data, key, iv)
        
        return decrypted_data
    
    def encrypt_file(self, file_path: str, key: bytes, output_path: str = None) -> str:
        """Encrypt file using AES"""
        if output_path is None:
            output_path = file_path + '.encrypted'
        
        # Read file
        with open(file_path, 'rb') as f:
            file_data = f.read()
        
        # Encrypt data
        encrypted_data, iv = self.encrypt_data(file_data, key)
        
        # Save encrypted file with IV prepended
        with open(output_path, 'wb') as f:
            f.write(iv + encrypted_data)
        
        print(f"File encrypted: {file_path} -> {output_path}")
        return output_path
    
    def decrypt_file(self, encrypted_file_path: str, key: bytes, output_path: str = None) -> str:
        """Decrypt file using AES"""
        if output_path is None:
            if encrypted_file_path.endswith('.encrypted'):
                output_path = encrypted_file_path[:-10]  # Remove .encrypted
            else:
                output_path = encrypted_file_path + '.decrypted'
        
        # Read encrypted file
        with open(encrypted_file_path, 'rb') as f:
            file_data = f.read()
        
        # Extract IV and encrypted data
        iv = file_data[:self.iv_size]
        encrypted_data = file_data[self.iv_size:]
        
        # Decrypt data
        decrypted_data = self.decrypt_data(encrypted_data, key, iv)
        
        # Save decrypted file
        with open(output_path, 'wb') as f:
            f.write(decrypted_data)
        
        print(f"File decrypted: {encrypted_file_path} -> {output_path}")
        return output_path
    
    def encrypt_string(self, text: str, key: bytes) -> str:
        """Encrypt string and return base64 encoded result"""
        encrypted_data, iv = self.encrypt_data(text, key)
        
        # Combine IV and encrypted data
        combined = iv + encrypted_data
        
        # Return base64 encoded string
        return base64.b64encode(combined).decode('utf-8')
    
    def decrypt_string(self, encrypted_text: str, key: bytes) -> str:
        """Decrypt base64 encoded string"""
        # Decode base64
        combined = base64.b64decode(encrypted_text)
        
        # Extract IV and encrypted data
        iv = combined[:self.iv_size]
        encrypted_data = combined[self.iv_size:]
        
        # Decrypt
        decrypted_data = self.decrypt_data(encrypted_data, key, iv)
        
        return decrypted_data.decode('utf-8')
    
    def _fallback_encrypt(self, data: bytes, key: bytes, iv: bytes) -> bytes:
        """Fallback AES encryption (simplified - NOT FOR PRODUCTION USE)"""
        # This is a very simplified XOR-based encryption
        # DO NOT USE FOR REAL SECURITY PURPOSES
        encrypted = bytearray()
        
        # Pad data to block size
        padded_data = data + b'\x00' * (self.block_size - len(data) % self.block_size)
        
        for i, byte in enumerate(padded_data):
            key_byte = key[i % len(key)]
            iv_byte = iv[i % len(iv)]
            encrypted.append(byte ^ key_byte ^ iv_byte)
        
        return bytes(encrypted)
    
    def _fallback_decrypt(self, encrypted_data: bytes, key: bytes, iv: bytes) -> bytes:
        """Fallback AES decryption (simplified - NOT FOR PRODUCTION USE)"""
        # Reverse of the fallback encryption
        decrypted = bytearray()
        
        for i, byte in enumerate(encrypted_data):
            key_byte = key[i % len(key)]
            iv_byte = iv[i % len(iv)]
            decrypted.append(byte ^ key_byte ^ iv_byte)
        
        # Remove padding
        decrypted = bytes(decrypted).rstrip(b'\x00')
        return decrypted
    
    def verify_file_integrity(self, original_file: str, decrypted_file: str) -> bool:
        """Verify that decrypted file matches original"""
        try:
            with open(original_file, 'rb') as f1, open(decrypted_file, 'rb') as f2:
                original_hash = hashlib.sha256(f1.read()).hexdigest()
                decrypted_hash = hashlib.sha256(f2.read()).hexdigest()
            
            return original_hash == decrypted_hash
        except FileNotFoundError:
            return False
    
    def generate_key_file(self, key: bytes, key_file_path: str) -> None:
        """Save key to file (with basic encoding)"""
        # In production, use secure key storage like HSM or key vault
        key_data = {
            'key': base64.b64encode(key).decode('utf-8'),
            'algorithm': 'AES-256-CBC',
            'created_at': str(os.time.time()) if hasattr(os, 'time') else str(time.time())
        }
        
        with open(key_file_path, 'w') as f:
            json.dump(key_data, f, indent=2)
        
        print(f"Key saved to: {key_file_path}")
    
    def load_key_from_file(self, key_file_path: str) -> bytes:
        """Load key from file"""
        with open(key_file_path, 'r') as f:
            key_data = json.load(f)
        
        key = base64.b64decode(key_data['key'].encode('utf-8'))
        print(f"Key loaded from: {key_file_path}")
        return key
    
    def secure_delete_file(self, file_path: str, passes: int = 3) -> None:
        """Securely delete file by overwriting"""
        try:
            file_size = os.path.getsize(file_path)
            
            with open(file_path, 'wb') as f:
                for _ in range(passes):
                    # Overwrite with random data
                    random_data = secrets.token_bytes(file_size)
                    f.write(random_data)
                    f.flush()
                    os.fsync(f.fileno())
            
            # Delete file
            os.remove(file_path)
            print(f"File securely deleted: {file_path}")
        
        except FileNotFoundError:
            print(f"File not found: {file_path}")
    
    def encrypt_directory(self, directory: str, key: bytes, output_dir: str = None) -> None:
        """Encrypt all files in directory"""
        if output_dir is None:
            output_dir = directory + '_encrypted'
        
        os.makedirs(output_dir, exist_ok=True)
        
        for root, dirs, files in os.walk(directory):
            for file in files:
                file_path = os.path.join(root, file)
                rel_path = os.path.relpath(file_path, directory)
                output_path = os.path.join(output_dir, rel_path + '.encrypted')
                
                # Create output directory if needed
                os.makedirs(os.path.dirname(output_path), exist_ok=True)
                
                # Encrypt file
                self.encrypt_file(file_path, key, output_path)
        
        print(f"Directory encrypted: {directory} -> {output_dir}")
    
    def decrypt_directory(self, encrypted_dir: str, key: bytes, output_dir: str = None) -> None:
        """Decrypt all files in directory"""
        if output_dir is None:
            output_dir = encrypted_dir.replace('_encrypted', '_decrypted')
        
        os.makedirs(output_dir, exist_ok=True)
        
        for root, dirs, files in os.walk(encrypted_dir):
            for file in files:
                if file.endswith('.encrypted'):
                    file_path = os.path.join(root, file)
                    rel_path = os.path.relpath(file_path, encrypted_dir)
                    output_path = os.path.join(output_dir, rel_path[:-10])  # Remove .encrypted
                    
                    # Create output directory if needed
                    os.makedirs(os.path.dirname(output_path), exist_ok=True)
                    
                    # Decrypt file
                    self.decrypt_file(file_path, key, output_path)
        
        print(f"Directory decrypted: {encrypted_dir} -> {output_dir}")

def main():
    """Main function to demonstrate AES encryption"""
    print("=== AES Encryption Tool ===\n")
    
    encryptor = AESEncryptor()
    
    # Generate keys
    print("1. Key Generation:")
    
    # Random key
    random_key = encryptor.generate_key()
    print(f"Random key (base64): {base64.b64encode(random_key).decode()}")
    
    # Password-derived key
    password = "MySecurePassword123!"
    password_key = encryptor.generate_key(password)
    print(f"Password-derived key (base64): {base64.b64encode(password_key).decode()}")
    
    # String encryption/decryption
    print("\n2. String Encryption:")
    
    test_message = "This is a secret message that needs to be encrypted!"
    print(f"Original message: {test_message}")
    
    # Encrypt
    encrypted_string = encryptor.encrypt_string(test_message, random_key)
    print(f"Encrypted (base64): {encrypted_string}")
    
    # Decrypt
    decrypted_string = encryptor.decrypt_string(encrypted_string, random_key)
    print(f"Decrypted: {decrypted_string}")
    
    # File encryption/decryption
    print("\n3. File Encryption:")
    
    # Create test file
    test_file = "test_document.txt"
    with open(test_file, 'w') as f:
        f.write("This is a test document for AES encryption.\n")
        f.write("It contains sensitive information that should be encrypted.\n")
        f.write("AES-256-CBC encryption will protect this data.\n")
    
    print(f"Created test file: {test_file}")
    
    # Encrypt file
    encrypted_file = encryptor.encrypt_file(test_file, random_key)
    
    # Decrypt file
    decrypted_file = encryptor.decrypt_file(encrypted_file, random_key)
    
    # Verify integrity
    integrity_check = encryptor.verify_file_integrity(test_file, decrypted_file)
    print(f"Integrity check: {'PASSED' if integrity_check else 'FAILED'}")
    
    # Key file operations
    print("\n4. Key Management:")
    
    key_file = "aes_key.json"
    encryptor.generate_key_file(random_key, key_file)
    
    loaded_key = encryptor.load_key_from_file(key_file)
    print(f"Key match: {random_key == loaded_key}")
    
    # Test with loaded key
    test_with_loaded_key = encryptor.decrypt_string(encrypted_string, loaded_key)
    print(f"Decryption with loaded key: {'SUCCESS' if test_with_loaded_key == test_message else 'FAILED'}")
    
    # Directory encryption (if test directory exists)
    print("\n5. Directory Encryption:")
    
    test_dir = "test_directory"
    os.makedirs(test_dir, exist_ok=True)
    
    # Create test files
    for i in range(3):
        file_path = os.path.join(test_dir, f"file_{i+1}.txt")
        with open(file_path, 'w') as f:
            f.write(f"This is test file {i+1} with some content.\n")
    
    print(f"Created test directory: {test_dir}")
    
    # Encrypt directory
    encryptor.encrypt_directory(test_dir, random_key)
    
    # Decrypt directory
    encryptor.decrypt_directory(test_dir + '_encrypted', random_key)
    
    # Security features
    print("\n6. Security Features:")
    
    # Test different password strengths
    weak_password = "123456"
    strong_password = "MyStr0ng!P@ssw0rd#2023"
    
    weak_key = encryptor.generate_key(weak_password)
    strong_key = encryptor.generate_key(strong_password)
    
    print(f"Weak password key entropy: {len(set(weak_key)) * 8:.0f} bits")
    print(f"Strong password key entropy: {len(set(strong_key)) * 8:.0f} bits")
    
    # Performance test
    print("\n7. Performance Test:")
    
    large_data = "A" * 10000  # 10KB of data
    
    import time
    start_time = time.time()
    encrypted_large = encryptor.encrypt_string(large_data, random_key)
    encrypt_time = time.time() - start_time
    
    start_time = time.time()
    decrypted_large = encryptor.decrypt_string(encrypted_large, random_key)
    decrypt_time = time.time() - start_time
    
    print(f"Encryption time (10KB): {encrypt_time:.4f} seconds")
    print(f"Decryption time (10KB): {decrypt_time:.4f} seconds")
    print(f"Data integrity: {'PASSED' if large_data == decrypted_large else 'FAILED'}")
    
    # Cleanup
    print("\n8. Cleanup:")
    
    # Securely delete sensitive files
    files_to_delete = [test_file, encrypted_file, decrypted_file, key_file]
    
    for file_path in files_to_delete:
        if os.path.exists(file_path):
            encryptor.secure_delete_file(file_path)
    
    # Remove test directories
    import shutil
    for dir_path in [test_dir, test_dir + '_encrypted', test_dir + '_decrypted']:
        if os.path.exists(dir_path):
            shutil.rmtree(dir_path)
    
    print("Cleanup completed!")
    
    # Security recommendations
    print("\n=== Security Recommendations ===")
    print("1. Use strong, unique passwords for key derivation")
    print("2. Store keys securely (HSM, key vault, or encrypted key files)")
    print("3. Use unique IV for each encryption operation")
    print("4. Implement proper key rotation policies")
    print("5. Use authenticated encryption (AES-GCM) when possible")
    print("6. Securely delete sensitive data when no longer needed")
    print("7. Implement proper access controls and audit logging")
    print("8. Regular security audits and penetration testing")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install PyCryptodome: pip install pycryptodome
2. Run encryption tool: python aes_encryptor.py
3. Demonstrates key generation, encryption, decryption
4. Shows file and directory encryption capabilities
5. Includes security best practices and recommendations

Key Concepts:
- AES-256-CBC: Advanced Encryption Standard with Cipher Block Chaining
- Key Derivation: PBKDF2 for password-based key generation
- Initialization Vector: Ensures unique encryption of identical data
- Data Integrity: Verification of encryption/decryption accuracy
- Secure Key Management: Proper storage and handling of encryption keys

Applications:
- File encryption for sensitive data
- Secure communication channels
- Database field encryption
- Backup encryption
- Secure data storage
- Compliance with data protection regulations

Security Features:
- Strong 256-bit encryption
- Password-based key derivation
- Secure random IV generation
- File integrity verification
- Secure file deletion
- Directory-wide encryption

Dependencies:
- PyCryptodome: pip install pycryptodome
- Fallback implementation included (less secure)

Note: Always use proper cryptographic libraries like PyCryptodome in production.
"""
