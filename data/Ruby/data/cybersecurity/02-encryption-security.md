# Encryption and Security in Ruby
# Comprehensive guide to cryptographic security implementations

## 🔐 Encryption Fundamentals

### 1. Cryptographic Security Concepts

Core encryption and security principles:

```ruby
class EncryptionSecurity
  def self.explain_encryption_concepts
    puts "Encryption and Security Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Symmetric Encryption",
        description: "Same key for encryption and decryption",
        algorithms: ["AES", "DES", "3DES", "Blowfish"],
        use_cases: ["File encryption", "Database encryption", "Secure communication"],
        pros: ["Fast", "Efficient", "Suitable for large data"],
        cons: ["Key distribution problem", "Single point of failure"]
      },
      {
        concept: "Asymmetric Encryption",
        description: "Different keys for encryption and decryption",
        algorithms: ["RSA", "ECC", "DSA", "ElGamal"],
        use_cases: ["Key exchange", "Digital signatures", "Secure email"],
        pros: ["No key distribution", "Digital signatures", "Key exchange"],
        cons: ["Slower", "More complex", "Larger key sizes"]
      },
      {
        concept: "Hash Functions",
        description: "One-way cryptographic functions",
        algorithms: ["SHA-256", "SHA-512", "MD5", "Blake2"],
        use_cases: ["Password storage", "Data integrity", "Digital signatures"],
        properties: ["Deterministic", "Fixed output", "Collision resistant"]
      },
      {
        concept: "Digital Signatures",
        description: "Cryptographic authentication mechanism",
        algorithms: ["RSA signatures", "ECDSA", "DSA", "EdDSA"],
        use_cases: ["Document signing", "Code signing", "Authentication"],
        provides: ["Authenticity", "Integrity", "Non-repudiation"]
      },
      {
        concept: "Key Derivation",
        description: "Derive keys from passwords or other secrets",
        algorithms: ["PBKDF2", "scrypt", "Argon2", "bcrypt"],
        use_cases: ["Password hashing", "Key generation", "Secure storage"],
        features: ["Salted", "Iterated", "Memory-hard"]
      },
      {
        concept: "Message Authentication",
        description: "Verify message authenticity and integrity",
        algorithms: ["HMAC", "CMAC", "Poly1305", "GMAC"],
        use_cases: ["API authentication", "Message integrity", "Secure channels"],
        combines: ["Hash function", "Secret key"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Algorithms: #{concept[:algorithms].join(', ')}"
      puts "  Use Cases: #{concept[:use_cases].join(', ')}"
      puts "  Pros: #{concept[:pros].join(', ')}" if concept[:pros]
      puts "  Cons: #{concept[:cons].join(', ')}" if concept[:cons]
      puts "  Properties: #{concept[:properties].join(', ')}" if concept[:properties]
      puts "  Provides: #{concept[:provides].join(', ')}" if concept[:provides]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Combines: #{concept[:combines].join(', ')}" if concept[:combines]
      puts
    end
  end
  
  def self.security_principles
    puts "\nSecurity Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Defense in Depth",
        description: "Multiple layers of security controls",
        implementation: ["Encryption", "Access control", "Monitoring", "Backup"],
        benefit: "Reduces single point of failure"
      },
      {
        principle: "Least Privilege",
        description: "Users have minimum necessary permissions",
        implementation: ["Role-based access", "Permission auditing", "Regular reviews"],
        benefit: "Limits damage from compromised accounts"
      },
      {
        principle: "Need to Know",
        description: "Access only necessary information",
        implementation: ["Data classification", "Access policies", "Compartmentalization"],
        benefit: "Reduces information exposure"
      },
      {
        principle: "Zero Trust",
        description: "Never trust, always verify",
        implementation: ["Continuous authentication", "Micro-segmentation", "Encryption everywhere"],
        benefit: "Prevents lateral movement"
      },
      {
        principle: "Secure by Design",
        description: "Security built in from the start",
        implementation: ["Threat modeling", "Secure coding", "Regular testing"],
        benefit: "Proactive security posture"
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Implementation: #{principle[:implementation].join(', ')}"
      puts "  Benefit: #{principle[:benefit]}"
      puts
    end
  end
  
  # Run encryption fundamentals
  explain_encryption_concepts
  security_principles
end
```

### 2. Symmetric Encryption Implementation

AES encryption in Ruby:

```ruby
class SymmetricEncryption
  require 'openssl'
  
  def initialize(key = nil)
    @key = key || generate_key
    @cipher = OpenSSL::Cipher.new('AES-256-CBC')
  end
  
  attr_reader :key
  
  def encrypt(plaintext)
    @cipher.encrypt
    
    # Generate random IV
    iv = @cipher.random_iv
    
    # Set key and IV
    @cipher.key = @key
    @cipher.iv = iv
    
    # Encrypt
    encrypted = @cipher.update(plaintext) + @cipher.final
    
    # Return IV + encrypted data
    iv + encrypted
  end
  
  def decrypt(ciphertext)
    # Extract IV and encrypted data
    iv = ciphertext[0...@cipher.iv_len]
    encrypted = ciphertext[@cipher.iv_len..-1]
    
    @cipher.decrypt
    @cipher.key = @key
    @cipher.iv = iv
    
    # Decrypt
    decrypted = @cipher.update(encrypted) + @cipher.final
    
    decrypted
  end
  
  def encrypt_file(input_file, output_file)
    plaintext = File.read(input_file)
    encrypted = encrypt(plaintext)
    
    File.write(output_file, encrypted)
    
    puts "Encrypted #{input_file} to #{output_file}"
  end
  
  def decrypt_file(input_file, output_file)
    ciphertext = File.read(input_file)
    decrypted = decrypt(ciphertext)
    
    File.write(output_file, decrypted)
    
    puts "Decrypted #{input_file} to #{output_file}"
  end
  
  def encrypt_string(plaintext)
    encrypted = encrypt(plaintext)
    Base64.strict_encode64(encrypted)
  end
  
  def decrypt_string(ciphertext)
    encrypted = Base64.strict_decode64(ciphertext)
    decrypt(encrypted)
  end
  
  def self.demonstrate_aes
    puts "AES Symmetric Encryption Demonstration:"
    puts "=" * 50
    
    # Create encryption instance
    aes = SymmetricEncryption.new
    
    puts "Generated key: #{aes.key[0...16]}..." # Show first 16 chars
    
    # Encrypt string
    plaintext = "This is a secret message for AES encryption!"
    puts "\nOriginal text: #{plaintext}"
    
    encrypted = aes.encrypt_string(plaintext)
    puts "Encrypted: #{encrypted[0...32]}..."
    
    # Decrypt string
    decrypted = aes.decrypt_string(encrypted)
    puts "Decrypted: #{decrypted}"
    
    # Verify
    puts "\nVerification: #{plaintext == decrypted ? 'SUCCESS' : 'FAILED'}"
    
    # File encryption
    puts "\nFile Encryption:"
    
    # Create test file
    test_file = 'test_plain.txt'
    encrypted_file = 'test_encrypted.bin'
    decrypted_file = 'test_decrypted.txt'
    
    File.write(test_file, "This is test file content for encryption.")
    
    # Encrypt file
    aes.encrypt_file(test_file, encrypted_file)
    
    # Decrypt file
    aes.decrypt_file(encrypted_file, decrypted_file)
    
    # Verify
    original_content = File.read(test_file)
    decrypted_content = File.read(decrypted_file)
    
    puts "File verification: #{original_content == decrypted_content ? 'SUCCESS' : 'FAILED'}"
    
    # Clean up
    [test_file, encrypted_file, decrypted_file].each { |file| File.delete(file) if File.exist?(file) }
    
    puts "\nAES Encryption Features:"
    puts "- 256-bit AES encryption"
    puts "- CBC mode with random IV"
    puts "- Base64 encoding for strings"
    puts "- File encryption support"
    puts "- Secure key generation"
  end
  
  private
  
  def generate_key
    # Generate 256-bit key
    OpenSSL::Random.random_bytes(32)
  end
end

class AdvancedSymmetricEncryption
  require 'openssl'
  
  def self.demonstrate_modes
    puts "\nAES Encryption Modes Demonstration:"
    puts "=" * 50
    
    key = OpenSSL::Random.random_bytes(32)
    plaintext = "This is a test message for different AES modes!"
    
    modes = [
      { name: 'CBC', cipher: 'AES-256-CBC', description: 'Cipher Block Chaining' },
      { name: 'CFB', cipher: 'AES-256-CFB', description: 'Cipher Feedback' },
      { name: 'OFB', cipher: 'AES-256-OFB', description: 'Output Feedback' },
      { name: 'CTR', cipher: 'AES-256-CTR', description: 'Counter' }
    ]
    
    modes.each do |mode|
      puts "\n#{mode[:name]} (#{mode[:description]}):"
      
      cipher = OpenSSL::Cipher.new(mode[:cipher])
      cipher.encrypt
      cipher.key = key
      
      iv = cipher.random_iv
      cipher.iv = iv
      
      encrypted = cipher.update(plaintext) + cipher.final
      
      # Decrypt
      decipher = OpenSSL::Cipher.new(mode[:cipher])
      decipher.decrypt
      decipher.key = key
      decipher.iv = iv
      
      decrypted = decipher.update(encrypted) + decipher.final
      
      puts "  Original: #{plaintext}"
      puts "  Encrypted: #{Base64.strict_encode64(encrypted)[0...32]}..."
      puts "  Decrypted: #{decrypted}"
      puts "  Success: #{plaintext == decrypted}"
    end
  end
  
  def self.demonstrate_stream_cipher
    puts "\nStream Cipher Demonstration (ChaCha20):"
    puts "=" * 50
    
    # Note: Ruby's OpenSSL doesn't include ChaCha20 by default
    # This is a simplified demonstration
    
    plaintext = "This is a message for stream cipher encryption!"
    key = OpenSSL::Random.random_bytes(32)
    nonce = OpenSSL::Random.random_bytes(12)
    
    puts "Plaintext: #{plaintext}"
    puts "Key: #{Base64.strict_encode64(key)}"
    puts "Nonce: #{Base64.strict_encode64(nonce)}"
    
    # In practice, you'd use a library like RbNaCl for ChaCha20
    puts "\nStream Cipher Features:"
    puts "- Fast encryption/decryption"
    puts "- No padding required"
    puts "- Suitable for real-time data"
    puts "- Resistant to timing attacks"
  end
end
```

## 🔑 Asymmetric Encryption

### 3. RSA Implementation

Public key cryptography:

```ruby
class AsymmetricEncryption
  require 'openssl'
  
  def initialize(key_size = 2048)
    @key_size = key_size
    @private_key = nil
    @public_key = nil
    generate_key_pair
  end
  
  attr_reader :public_key
  
  def encrypt(message)
    @public_key.public_encrypt(message)
  end
  
  def decrypt(ciphertext)
    @private_key.private_decrypt(ciphertext)
  end
  
  def sign(message)
    signature = @private_key.sign(OpenSSL::Digest::SHA256.new, message)
    Base64.strict_encode64(signature)
  end
  
  def verify(message, signature)
    signature_bytes = Base64.strict_decode64(signature)
    @public_key.verify(OpenSSL::Digest::SHA256.new, signature_bytes, message)
  end
  
  def encrypt_with_public_key(public_key_pem, message)
    public_key = OpenSSL::PKey::RSA.new(public_key_pem)
    public_key.public_encrypt(message)
  end
  
  def verify_with_public_key(public_key_pem, message, signature)
    public_key = OpenSSL::PKey::RSA.new(public_key_pem)
    signature_bytes = Base64.strict_decode64(signature)
    public_key.verify(OpenSSL::Digest::SHA256.new, signature_bytes, message)
  end
  
  def export_public_key
    @public_key.to_pem
  end
  
  def export_private_key(password = nil)
    if password
      cipher = OpenSSL::Cipher.new('AES-256-CBC')
      @private_key.to_pem(cipher, password)
    else
      @private_key.to_pem
    end
  end
  
  def self.import_public_key(pem)
    public_key = OpenSSL::PKey::RSA.new(pem)
    new_instance = allocate
    new_instance.instance_variable_set(:@public_key, public_key)
    new_instance
  end
  
  def self.demonstrate_rsa
    puts "RSA Asymmetric Encryption Demonstration:"
    puts "=" * 50
    
    # Create RSA instance
    rsa = AsymmetricEncryption.new(2048)
    
    puts "Generated #{rsa.instance_variable_get(:@key_size)}-bit RSA key pair"
    
    # Export keys
    public_key_pem = rsa.export_public_key
    private_key_pem = rsa.export_private_key
    
    puts "\nPublic Key (first 100 chars):"
    puts public_key_pem[0...100]
    
    puts "\nPrivate Key (first 100 chars):"
    puts private_key_pem[0...100]
    
    # Encrypt/Decrypt
    message = "This is a secret message for RSA encryption!"
    puts "\nOriginal message: #{message}"
    
    encrypted = rsa.encrypt(message)
    puts "Encrypted: #{Base64.strict_encode64(encrypted)[0...32]}..."
    
    decrypted = rsa.decrypt(encrypted)
    puts "Decrypted: #{decrypted}"
    
    # Digital Signature
    puts "\nDigital Signature:"
    signature = rsa.sign(message)
    puts "Signature: #{signature[0...32]}..."
    
    is_valid = rsa.verify(message, signature)
    puts "Signature valid: #{is_valid}"
    
    # Test with wrong message
    wrong_message = "This is a different message!"
    is_valid_wrong = rsa.verify(wrong_message, signature)
    puts "Signature valid for wrong message: #{is_valid_wrong}"
    
    puts "\nRSA Encryption Features:"
    puts "- #{rsa.instance_variable_get(:@key_size)}-bit key generation"
    puts "- Public key encryption"
    puts "- Private key decryption"
    puts "- Digital signatures"
    puts "- Key export/import"
    puts "- SHA-256 hashing for signatures"
  end
  
  def self.demonstrate_key_exchange
    puts "\nRSA Key Exchange Demonstration:"
    puts "=" * 50
    
    # Alice generates keys
    alice_rsa = AsymmetricEncryption.new(2048)
    alice_public_key = alice_rsa.export_public_key
    
    # Bob generates keys
    bob_rsa = AsymmetricEncryption.new(2048)
    bob_public_key = bob_rsa.export_public_key
    
    puts "Alice and Bob generated RSA key pairs"
    
    # Alice wants to send secret to Bob
    secret_message = "This is Alice's secret message to Bob!"
    
    # Alice encrypts with Bob's public key
    encrypted_for_bob = alice_rsa.encrypt_with_public_key(bob_public_key, secret_message)
    
    puts "\nAlice encrypts message with Bob's public key"
    puts "Encrypted: #{Base64.strict_encode64(encrypted_for_bob)[0...32]}..."
    
    # Bob decrypts with his private key
    decrypted_by_bob = bob_rsa.decrypt(encrypted_for_bob)
    
    puts "\nBob decrypts with his private key"
    puts "Decrypted: #{decrypted_by_bob}"
    
    # Verify
    puts "\nKey exchange successful: #{secret_message == decrypted_by_bob}"
    
    puts "\nKey Exchange Features:"
    puts "- Secure message exchange"
    puts "- No need to share private keys"
    puts "- Public key distribution"
    puts "- End-to-end encryption"
  end
  
  private
  
  def generate_key_pair
    @private_key = OpenSSL::PKey::RSA.new(@key_size)
    @public_key = @private_key.public_key
  end
end
```

### 4. Elliptic Curve Cryptography

ECC implementation:

```ruby
class EllipticCurveCryptography
  require 'openssl'
  
  def initialize(curve = 'secp256k1')
    @curve = curve
    @private_key = nil
    @public_key = nil
    generate_key_pair
  end
  
  attr_reader :public_key, :curve
  
  def sign(message)
    signature = @private_key.sign(OpenSSL::Digest::SHA256.new, message)
    {
      r: signature.r.to_s(16),
      s: signature.s.to_s(16)
    }
  end
  
  def verify(message, signature)
    sig = OpenSSL::PKey::EC::Signature.new
    sig.r = signature[:r].to_i(16)
    sig.s = signature[:s].to_i(16)
    
    @public_key.verify(OpenSSL::Digest::SHA256.new, sig, message)
  end
  
  def derive_shared_key(other_public_key_pem)
    other_public_key = OpenSSL::PKey::EC.new(other_public_key_pem)
    
    # ECDH key exchange
    shared_key = @private_key.dh_compute_key(other_public_key)
    
    # Derive symmetric key from shared secret
    OpenSSL::Digest::SHA256.digest(shared_key)
  end
  
  def export_public_key
    @public_key.to_pem
  end
  
  def export_private_key
    @private_key.to_pem
  end
  
  def self.demonstrate_ecdsa
    puts "Elliptic Curve Cryptography Demonstration:"
    puts "=" * 50
    
    # Create EC instance
    ecc = EllipticCurveCryptography.new('secp256k1')
    
    puts "Generated EC key pair using curve: #{ecc.curve}"
    
    # Export keys
    public_key_pem = ecc.export_public_key
    private_key_pem = ecc.export_private_key
    
    puts "\nPublic Key (first 100 chars):"
    puts public_key_pem[0...100]
    
    # Digital Signature
    message = "This is a message for ECDSA signature!"
    puts "\nMessage: #{message}"
    
    signature = ecc.sign(message)
    puts "Signature: r=#{signature[:r][0...16]}..., s=#{signature[:s][0...16]}..."
    
    # Verify signature
    is_valid = ecc.verify(message, signature)
    puts "Signature valid: #{is_valid}"
    
    # Test with wrong message
    wrong_message = "This is a different message!"
    is_valid_wrong = ecc.verify(wrong_message, signature)
    puts "Signature valid for wrong message: #{is_valid_wrong}"
    
    puts "\nECDSA Features:"
    puts "- Elliptic curve digital signatures"
    puts "- Smaller keys than RSA"
    puts "- Faster operations"
    puts "- Same security with smaller keys"
  end
  
  def self.demonstrate_ecdh
    puts "\nECDH Key Exchange Demonstration:"
    puts "=" * 50
    
    # Alice generates keys
    alice_ecc = EllipticCurveCryptography.new('secp256k1')
    alice_public_key = alice_ecc.export_public_key
    
    # Bob generates keys
    bob_ecc = EllipticCurveCryptography.new('secp256k1')
    bob_public_key = bob_ecc.export_public_key
    
    puts "Alice and Bob generated EC key pairs"
    
    # Alice derives shared key
    alice_shared = alice_ecc.derive_shared_key(bob_public_key)
    
    # Bob derives shared key
    bob_shared = bob_ecc.derive_shared_key(alice_public_key)
    
    puts "\nAlice derived shared key: #{Base64.strict_encode64(alice_shared)}"
    puts "Bob derived shared key: #{Base64.strict_encode64(bob_shared)}"
    
    # Verify keys are the same
    keys_match = alice_shared == bob_shared
    puts "\nShared keys match: #{keys_match}"
    
    if keys_match
      # Use shared key for symmetric encryption
      puts "\nUsing shared key for AES encryption:"
      
      aes = SymmetricEncryption.new
      aes.instance_variable_set(:@key, alice_shared)
      
      secret_message = "This is encrypted with ECDH-derived key!"
      encrypted = aes.encrypt_string(secret_message)
      decrypted = aes.decrypt_string(encrypted)
      
      puts "Original: #{secret_message}"
      puts "Encrypted: #{encrypted[0...32]}..."
      puts "Decrypted: #{decrypted}"
      puts "Success: #{secret_message == decrypted}"
    end
    
    puts "\nECDH Features:"
    puts "- Elliptic curve Diffie-Hellman"
    puts "- Forward secrecy"
    puts "- Efficient key exchange"
    puts "- Perfect forward secrecy"
  end
  
  def self.compare_rsa_ecc
    puts "\nRSA vs ECC Comparison:"
    puts "=" * 50
    
    comparison = [
      {
        aspect: "Key Size",
        rsa: "2048-4096 bits",
        ecc: "256-384 bits",
        winner: "ECC"
      },
      {
        aspect: "Performance",
        rsa: "Slower operations",
        ecc: "Faster operations",
        winner: "ECC"
      },
      {
        aspect: "Signature Size",
        rsa: "256-512 bytes",
        ecc: "64-96 bytes",
        winner: "ECC"
      },
      {
        aspect: "Maturity",
        rsa: "Very mature",
        ecc: "Mature",
        winner: "RSA"
      },
      {
        aspect: "Compatibility",
        rsa: "Universal",
        ecc: "Good",
        winner: "RSA"
      }
    ]
    
    comparison.each do |item|
      puts "#{item[:aspect]}:"
      puts "  RSA: #{item[:rsa]}"
      puts "  ECC: #{item[:ecc]}"
      puts "  Winner: #{item[:winner]}"
      puts
    end
    
    puts "Use RSA when:"
    puts "- Need maximum compatibility"
    puts "- Legacy system support"
    puts "- Simpler implementation"
    
    puts "\nUse ECC when:"
    puts "- Performance is critical"
    puts "- Limited bandwidth/storage"
    puts "- Mobile/IoT applications"
  end
  
  private
  
  def generate_key_pair
    @private_key = OpenSSL::PKey::EC.new(@curve)
    @private_key.generate_key
    @public_key = @private_key.public_key
  end
end
```

## 🔐 Hash Functions and Password Security

### 5. Secure Hashing

Cryptographic hash functions:

```ruby
class SecureHashing
  require 'digest'
  require 'openssl'
  
  def self.hash_data(data, algorithm = 'sha256')
    case algorithm.downcase
    when 'sha256'
      Digest::SHA256.hexdigest(data)
    when 'sha512'
      Digest::SHA512.hexdigest(data)
    when 'sha1'
      Digest::SHA1.hexdigest(data)
    when 'md5'
      Digest::MD5.hexdigest(data)
    when 'sha3-256'
      Digest::SHA3.hexdigest(data, 256)
    when 'sha3-512'
      Digest::SHA3.hexdigest(data, 512)
    else
      raise "Unsupported algorithm: #{algorithm}"
    end
  end
  
  def self.hash_file(file_path, algorithm = 'sha256')
    data = File.read(file_path)
    hash_data(data, algorithm)
  end
  
  def self.verify_hash(data, expected_hash, algorithm = 'sha256')
    calculated_hash = hash_data(data, algorithm)
    calculated_hash == expected_hash.downcase
  end
  
  def self.hmac(data, key, algorithm = 'sha256')
    case algorithm.downcase
    when 'sha256'
      OpenSSL::HMAC.hexdigest('sha256', key, data)
    when 'sha512'
      OpenSSL::HMAC.hexdigest('sha512', key, data)
    when 'sha1'
      OpenSSL::HMAC.hexdigest('sha1', key, data)
    else
      raise "Unsupported HMAC algorithm: #{algorithm}"
    end
  end
  
  def self.demonstrate_hashing
    puts "Secure Hashing Demonstration:"
    puts "=" * 50
    
    data = "This is test data for hashing!"
    
    algorithms = ['sha256', 'sha512', 'sha1', 'md5', 'sha3-256']
    
    algorithms.each do |algorithm|
      hash = hash_data(data, algorithm)
      puts "#{algorithm.upcase}: #{hash}"
    end
    
    # File hashing
    puts "\nFile Hashing:"
    test_file = 'test_hash.txt'
    File.write(test_file, data)
    
    file_hash = hash_file(test_file, 'sha256')
    puts "File SHA256: #{file_hash}"
    
    # Verification
    is_valid = verify_hash(data, file_hash, 'sha256')
    puts "Verification: #{is_valid ? 'SUCCESS' : 'FAILED'}"
    
    File.delete(test_file)
    
    # HMAC
    puts "\nHMAC Demonstration:"
    key = "secret_key_123"
    message = "HMAC test message"
    
    hmac_sha256 = hmac(message, key, 'sha256')
    hmac_sha512 = hmac(message, key, 'sha512')
    
    puts "Message: #{message}"
    puts "Key: #{key}"
    puts "HMAC-SHA256: #{hmac_sha256}"
    puts "HMAC-SHA512: #{hmac_sha512}"
    
    puts "\nHashing Features:"
    puts "- Multiple hash algorithms"
    puts "- File hashing support"
    puts "- Hash verification"
    puts "- HMAC for message authentication"
    puts "- Data integrity verification"
  end
end

class PasswordSecurity
  def self.hash_password(password, salt = nil, iterations = 100000)
    salt ||= SecureRandom.hex(16)
    
    # Use PBKDF2 with SHA-256
    hash = OpenSSL::PKCS5.pbkdf2_hmac(
      password,
      salt,
      iterations,
      32, # 256 bits
      OpenSSL::Digest::SHA256.new
    )
    
    {
      hash: Base64.strict_encode64(hash),
      salt: salt,
      iterations: iterations
    }
  end
  
  def self.verify_password(password, stored_hash, salt, iterations = 100000)
    calculated_hash = OpenSSL::PKCS5.pbkdf2_hmac(
      password,
      salt,
      iterations,
      32,
      OpenSSL::Digest::SHA256.new
    )
    
    calculated_hash_base64 = Base64.strict_encode64(calculated_hash)
    calculated_hash_base64 == stored_hash
  end
  
  def self.hash_password_bcrypt(password, cost = 12)
    # Note: This requires the 'bcrypt' gem
    # BCrypt::Password.create(password, cost: cost)
    
    # Simplified demonstration
    puts "BCrypt hashing requires 'bcrypt' gem"
    puts "Install with: gem install bcrypt"
    
    # Fallback to PBKDF2
    hash_password(password, nil, 2**cost)
  end
  
  def self.hash_password_argon2(password)
    # Note: This requires the 'argon2' gem
    # Argon2::Password.create(password)
    
    # Simplified demonstration
    puts "Argon2 hashing requires 'argon2' gem"
    puts "Install with: gem install argon2"
    
    # Fallback to PBKDF2
    hash_password(password, nil, 100000)
  end
  
  def self.check_password_strength(password)
    score = 0
    issues = []
    
    # Length check
    if password.length >= 12
      score += 2
    elsif password.length >= 8
      score += 1
    else
      issues << "Password too short (minimum 8 characters)"
    end
    
    # Character variety
    if password.match?(/[a-z]/)
      score += 1
    else
      issues << "Missing lowercase letters"
    end
    
    if password.match?(/[A-Z]/)
      score += 1
    else
      issues << "Missing uppercase letters"
    end
    
    if password.match?(/[0-9]/)
      score += 1
    else
      issues << "Missing numbers"
    end
    
    if password.match?(/[^a-zA-Z0-9]/)
      score += 1
    else
      issues << "Missing special characters"
    end
    
    # Common patterns
    if password.match?(/123|abc|password|qwerty/i)
      score -= 1
      issues << "Contains common patterns"
    end
    
    # Repeated characters
    if password.match?(/(.)\1{2,}/)
      score -= 1
      issues << "Contains repeated characters"
    end
    
    strength = case score
              when 0..2 then "Weak"
              when 3..4 then "Fair"
              when 5..6 then "Good"
              when 7..8 then "Strong"
              else "Very Strong"
              end
    
    {
      score: score,
      strength: strength,
      issues: issues
    }
  end
  
  def self.demonstrate_password_security
    puts "Password Security Demonstration:"
    puts "=" * 50
    
    passwords = [
      "password",
      "Password123",
      "P@ssw0rd!2023",
      "MyVeryStrongPassword!@#$123",
      "simple",
      "Complex123!@#"
    ]
    
    puts "Password Strength Analysis:"
    passwords.each do |password|
      analysis = check_password_strength(password)
      puts "\nPassword: #{password}"
      puts "Strength: #{analysis[:strength]} (Score: #{analysis[:score]}/8)"
      puts "Issues: #{analysis[:issues].join(', ')}" if analysis[:issues].any?
    end
    
    # Password hashing
    puts "\nPassword Hashing:"
    password = "MySecurePassword123!"
    
    # PBKDF2
    pbkdf2_result = hash_password(password)
    puts "\nPBKDF2-SHA256:"
    puts "Password: #{password}"
    puts "Hash: #{pbkdf2_result[:hash][0...32]}..."
    puts "Salt: #{pbkdf2_result[:salt]}"
    puts "Iterations: #{pbkdf2_result[:iterations]}"
    
    # Verification
    is_valid = verify_password(
      password,
      pbkdf2_result[:hash],
      pbkdf2_result[:salt],
      pbkdf2_result[:iterations]
    )
    puts "Verification: #{is_valid ? 'SUCCESS' : 'FAILED'}"
    
    # Test with wrong password
    wrong_password = "WrongPassword123!"
    is_valid_wrong = verify_password(
      wrong_password,
      pbkdf2_result[:hash],
      pbkdf2_result[:salt],
      pbkdf2_result[:iterations]
    )
    puts "Wrong password verification: #{is_valid_wrong ? 'FAILED' : 'SUCCESS'}"
    
    # Alternative methods
    puts "\nAlternative Hashing Methods:"
    hash_password_bcrypt(password)
    hash_password_argon2(password)
    
    puts "\nPassword Security Features:"
    puts "- PBKDF2 with SHA-256"
    puts "- Salted hashing"
    puts "- Configurable iterations"
    puts "- Password strength checking"
    puts "- Multiple hashing algorithms"
  end
  
  def self.demonstrate_secure_storage
    puts "\nSecure Password Storage Guidelines:"
    puts "=" * 50
    
    guidelines = [
      "Always use salted hashes",
      "Use modern algorithms (PBKDF2, Argon2, bcrypt)",
      "Use sufficient iterations (100,000+ for PBKDF2)",
      "Never store plain text passwords",
      "Implement rate limiting",
      "Use unique salts per password",
      "Consider pepper for additional security",
      "Regularly update hashing algorithms",
      "Implement password complexity requirements",
      "Use HTTPS for transmission"
    ]
    
    guidelines.each_with_index do |guideline, i|
      puts "#{i + 1}. #{guideline}"
    end
    
    puts "\nSecurity Best Practices:"
    puts "- Minimum 8 characters, preferably 12+"
    puts "- Mix of character types"
    puts "- Avoid common patterns"
    puts "- Regular password changes"
    puts "- Two-factor authentication"
    puts "- Account lockout after failed attempts"
    puts "- Password history to prevent reuse"
  end
end
```

## 🛡️ Security Best Practices

### 6. Security Implementation Patterns

Secure coding practices:

```ruby
class SecurityPatterns
  def self.input_validation
    puts "Input Validation Patterns:"
    puts "=" * 50
    
    class InputValidator
      def self.validate_email(email)
        email_regex = /\A[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\z/
        email.match?(email_regex)
      end
      
      def self.validate_phone(phone)
        phone_regex = /\A[\+]?[1-9][\d]{0,15}\z/
        phone.gsub(/[\s-]/, '').match?(phone_regex)
      end
      
      def self.validate_url(url)
        uri = URI.parse(url)
        uri.is_a?(URI::HTTP) || uri.is_a?(URI::HTTPS)
      rescue URI::InvalidURIError
        false
      end
      
      def self.sanitize_string(input)
        # Basic XSS prevention
        input.gsub(/[<>'"&]/, '')
      end
      
      def self.validate_number(input, min = nil, max = nil)
        return false unless input.is_a?(String) && input.match?(/\A-?\d+\z/)
        
        num = input.to_i
        (min.nil? || num >= min) && (max.nil? || num <= max)
      end
    end
    
    # Test validation
    test_inputs = [
      { type: :email, value: "test@example.com", valid: true },
      { type: :email, value: "invalid-email", valid: false },
      { type: :phone, value: "+1234567890", valid: true },
      { type: :phone, value: "abc", valid: false },
      { type: :url, value: "https://example.com", valid: true },
      { type: :url, value: "not-a-url", valid: false },
      { type: :string, value: "<script>alert('xss')</script>", valid: false },
      { type: :number, value: "123", valid: true },
      { type: :number, value: "abc", valid: false }
    ]
    
    test_inputs.each do |test|
      result = case test[:type]
               when :email
                 InputValidator.validate_email(test[:value])
               when :phone
                 InputValidator.validate_phone(test[:value])
               when :url
                 InputValidator.validate_url(test[:value])
               when :string
                 sanitized = InputValidator.sanitize_string(test[:value])
                 sanitized != test[:value]
               when :number
                 InputValidator.validate_number(test[:value])
               end
      
      puts "#{test[:type].to_s.capitalize}: '#{test[:value]}' - #{result ? 'Valid' : 'Invalid'}"
    end
  end
  
  def self.sql_injection_prevention
    puts "\nSQL Injection Prevention:"
    puts "=" * 50
    
    class SecureQueryBuilder
      def self.safe_query(table, conditions = {})
        # Use parameterized queries (simplified example)
        query = "SELECT * FROM #{sanitize_table_name(table)}"
        
        if conditions.any?
          where_clauses = []
          params = []
          
          conditions.each do |column, value|
            where_clauses << "#{sanitize_column_name(column)} = ?"
            params << sanitize_value(value)
          end
          
          query += " WHERE #{where_clauses.join(' AND ')}"
        end
        
        {
          query: query,
          params: params
        }
      end
      
      def self.sanitize_table_name(name)
        # Whitelist approach
        allowed_tables = %w[users products orders]
        raise "Invalid table name" unless allowed_tables.include?(name)
        name
      end
      
      def self.sanitize_column_name(name)
        # Whitelist approach
        allowed_columns = %w[id name email password created_at]
        raise "Invalid column name" unless allowed_columns.include?(name)
        name
      end
      
      def self.sanitize_value(value)
        case value
        when String
          "'#{value.gsub("'", "''")}'"  # Basic escaping
        when Integer
          value.to_s
        when NilClass
          'NULL'
        else
          raise "Unsupported value type"
        end
      end
    end
    
    # Example usage
    query_info = SecureQueryBuilder.safe_query(
      'users',
      { email: 'test@example.com', status: 'active' }
    )
    
    puts "Safe Query:"
    puts "SQL: #{query_info[:query]}"
    puts "Parameters: #{query_info[:params].join(', ')}"
    
    # Demonstrate unsafe vs safe
    unsafe_input = "'; DROP TABLE users; --"
    puts "\nUnsafe input: #{unsafe_input}"
    
    begin
      sanitized = SecureQueryBuilder.sanitize_value(unsafe_input)
      puts "Sanitized: #{sanitized}"
    rescue => e
      puts "Error: #{e.message}"
    end
  end
  
  def self.xss_prevention
    puts "\nXSS Prevention:"
    puts "=" * 50
    
    class XSSProtection
      def self.escape_html(input)
        # Basic HTML escaping
        input.gsub('&', '&amp;')
             .gsub('<', '&lt;')
             .gsub('>', '&gt;')
             .gsub('"', '&quot;')
             .gsub("'", '&#x27;')
      end
      
      def self.sanitize_html(input)
        # Remove dangerous tags and attributes
        dangerous_tags = %w[script iframe object embed form input textarea]
        dangerous_attrs = %w[onload onclick onerror onmouseover]
        
        cleaned = input.dup
        
        dangerous_tags.each do |tag|
          cleaned.gsub!(/<#{tag}[^>]*>.*?<\/#{tag}>/im, '')
          cleaned.gsub!(/<#{tag}[^>]*\/>/i, '')
        end
        
        dangerous_attrs.each do |attr|
          cleaned.gsub!(/\s#{attr}=["'][^"']*["']/im, '')
        end
        
        cleaned
      end
      
      def self.content_security_policy
        {
          "default-src" => "'self'",
          "script-src" => "'self' 'unsafe-inline'",
          "style-src" => "'self' 'unsafe-inline'",
          "img-src" => "'self' data: https:",
          "font-src" => "'self'",
          "connect-src" => "'self' https://api.example.com"
        }
      end
    end
    
    # Test XSS prevention
    xss_inputs = [
      '<script>alert("xss")</script>',
      '<img src="x" onerror="alert(\'xss\')">',
      '<a href="javascript:alert(\'xss\')">Click me</a>',
      'Normal text <b>with formatting</b>',
      '<iframe src="malicious.com"></iframe>'
    ]
    
    xss_inputs.each do |input|
      escaped = XSSProtection.escape_html(input)
      sanitized = XSSProtection.sanitize_html(input)
      
      puts "\nOriginal: #{input}"
      puts "Escaped: #{escaped}"
      puts "Sanitized: #{sanitized}"
    end
    
    # CSP
    puts "\nContent Security Policy:"
    csp = XSSProtection.content_security_policy
    csp.each { |directive, value| puts "#{directive}: #{value}" }
  end
  
  def self.authentication_patterns
    puts "\nAuthentication Patterns:"
    puts "=" * 50
    
    class SecureAuth
      def self.hash_password(password)
        # Use secure password hashing
        PasswordSecurity.hash_password(password)
      end
      
      def self.verify_password(password, stored_hash, salt, iterations)
        PasswordSecurity.verify_password(password, stored_hash, salt, iterations)
      end
      
      def self.generate_session_token
        # Generate cryptographically secure token
        SecureRandom.hex(32)
      end
      
      def self.verify_session_token(token, max_age = 3600)
        # Verify token and check age (simplified)
        token && token.length == 64 && !token_expired?(token, max_age)
      end
      
      def self.rate_limit_key(identifier)
        "rate_limit:#{identifier}:#{Time.now.to_i / 60}"
      end
      
      def self.brute_force_protection(identifier, max_attempts = 5)
        key = rate_limit_key(identifier)
        attempts = $redis.get(key).to_i
        
        if attempts >= max_attempts
          false
        else
          $redis.incr(key)
          $redis.expire(key, 3600)
          true
        end
      end
      
      def self.two_factor_setup(user)
        # Generate 2FA secret
        secret = ROTP::Base32.random_base32
        qr_code = generate_qr_code(user.email, secret)
        
        {
          secret: secret,
          qr_code: qr_code,
          backup_codes: generate_backup_codes
        }
      end
      
      def self.verify_2fa(secret, code)
        totp = ROTP::TOTP.new(secret)
        totp.verify(code)
      end
      
      private
      
      def self.token_expired?(token, max_age)
        # Simplified token expiration check
        false
      end
      
      def self.generate_qr_code(email, secret)
        # Generate QR code for 2FA setup
        "otpauth://totp/#{email}?secret=#{secret}&issuer=MyApp"
      end
      
      def self.generate_backup_codes
        (1..10).map { SecureRandom.hex(4) }
      end
    end
    
    puts "Authentication Features:"
    puts "- Secure password hashing"
    puts "- Session token generation"
    puts "- Rate limiting protection"
    puts "- Two-factor authentication"
    puts "- Brute force protection"
    puts "- Session management"
  end
  
  def self.demonstrate_security_patterns
    input_validation
    sql_injection_prevention
    xss_prevention
    authentication_patterns
    
    puts "\nSecurity Best Practices:"
    puts "- Always validate and sanitize input"
    puts "- Use parameterized queries"
    puts "- Implement proper authentication"
    puts "- Use HTTPS everywhere"
    puts "- Implement rate limiting"
    puts "- Regular security audits"
    puts "- Keep dependencies updated"
    puts "- Use security headers"
    puts "- Implement proper logging"
    puts "- Monitor for suspicious activity"
  end
end
```

## 📊 Security Monitoring

### 7. Security Monitoring and Auditing

Security monitoring implementation:

```ruby
class SecurityMonitoring
  def self.log_security_event(event_type, details = {})
    log_entry = {
      timestamp: Time.now.iso8601,
      event_type: event_type,
      details: details,
      severity: determine_severity(event_type),
      source: determine_source
    }
    
    # Log to file
    log_to_file(log_entry)
    
    # Send to monitoring service
    send_to_monitoring(log_entry)
    
    # Alert if high severity
    alert_security_team(log_entry) if log_entry[:severity] == 'high'
    
    log_entry
  end
  
  def self.detect_anomalies(user_id, action, context = {})
    # Check for suspicious patterns
    anomalies = []
    
    # Check for unusual login locations
    if unusual_location?(user_id, context[:ip_address])
      anomalies << {
        type: 'unusual_location',
        description: 'Login from unusual location',
        ip: context[:ip_address]
      }
    end
    
    # Check for rapid actions
    if rapid_actions?(user_id, action, context[:timestamp])
      anomalies << {
        type: 'rapid_actions',
        description: 'Unusually rapid actions detected',
        action: action
      }
    end
    
    # Check for privilege escalation
    if privilege_escalation?(user_id, action, context[:previous_role])
      anomalies << {
        type: 'privilege_escalation',
        description: 'Privilege escalation detected',
        action: action
      }
    end
    
    # Log anomalies
    anomalies.each do |anomaly|
      log_security_event('anomaly_detected', {
        user_id: user_id,
        anomaly: anomaly,
        context: context
      })
    end
    
    anomalies
  end
  
  def self.security_audit
    puts "Security Audit Report:"
    puts "=" * 50
    
    audit_results = {
      timestamp: Time.now.iso8601,
      checks: []
    }
    
    # Check password policies
    password_check = check_password_policies
    audit_results[:checks] << password_check
    
    # Check encryption usage
    encryption_check = check_encryption_usage
    audit_results[:checks] << encryption_check
    
    # Check access controls
    access_check = check_access_controls
    audit_results[:checks] << access_check
    
    # Check logging
    logging_check = check_logging_configuration
    audit_results[:checks] << logging_check
    
    # Check dependencies
    dependency_check = check_dependency_vulnerabilities
    audit_results[:checks] << dependency_check
    
    # Display results
    audit_results[:checks].each do |check|
      puts "\n#{check[:name]}:"
      puts "Status: #{check[:status]}"
      puts "Score: #{check[:score]}/10"
      puts "Issues: #{check[:issues].join(', ')}" if check[:issues].any?
      puts "Recommendations: #{check[:recommendations].join(', ')}" if check[:recommendations].any?
    end
    
    # Overall score
    total_score = audit_results[:checks].sum { |check| check[:score] }
    max_score = audit_results[:checks].length * 10
    overall_score = (total_score.to_f / max_score * 100).round(1)
    
    puts "\nOverall Security Score: #{overall_score}%"
    
    audit_results
  end
  
  def self.generate_security_report
    puts "Generating Security Report..."
    
    report = {
      generated_at: Time.now.iso8601,
      summary: {},
      incidents: get_recent_incidents,
      vulnerabilities: scan_vulnerabilities,
      recommendations: []
    }
    
    # Calculate summary metrics
    report[:summary] = {
      total_incidents: get_incident_count(30), # Last 30 days
      high_risk_vulnerabilities: count_vulnerabilities('high'),
      security_score: calculate_security_score,
      last_audit: get_last_audit_date
    }
    
    # Generate recommendations based on findings
    report[:recommendations] = generate_recommendations(report)
    
    # Save report
    save_security_report(report)
    
    puts "Security Report Generated:"
    puts "- Total incidents (30 days): #{report[:summary][:total_incidents]}"
    puts "- High risk vulnerabilities: #{report[:summary][:high_risk_vulnerabilities]}"
    puts "- Security score: #{report[:summary][:security_score]}%"
    puts "- Recommendations: #{report[:recommendations].length}"
    
    report
  end
  
  def self.demonstrate_monitoring
    puts "Security Monitoring Demonstration:"
    puts "=" * 50
    
    # Log security events
    puts "Logging Security Events:"
    
    events = [
      { type: 'login_success', user: 'user1', ip: '192.168.1.1' },
      { type: 'login_failure', user: 'user2', ip: '192.168.1.2' },
      { type: 'privilege_escalation', user: 'admin1', action: 'sudo' },
      { type: 'data_access', user: 'user3', resource: 'sensitive_data' },
      { type: 'config_change', user: 'admin1', config: 'firewall' }
    ]
    
    events.each do |event|
      log_security_event(event[:type], event)
    end
    
    # Detect anomalies
    puts "\nAnomaly Detection:"
    anomalies = detect_anomalies('user1', 'login', { ip_address: '192.168.1.1', timestamp: Time.now })
    
    if anomalies.any?
      puts "Anomalies detected:"
      anomalies.each { |anomaly| puts "- #{anomaly[:type]}: #{anomaly[:description]}" }
    else
      puts "No anomalies detected"
    end
    
    # Security audit
    puts "\nRunning Security Audit:"
    security_audit
    
    # Generate report
    puts "\nGenerating Security Report:"
    report = generate_security_report
    
    puts "\nSecurity Monitoring Features:"
    puts "- Event logging and tracking"
    puts "- Anomaly detection"
    puts "- Security auditing"
    puts "- Vulnerability scanning"
    puts "- Automated reporting"
    puts "- Real-time alerts"
  end
  
  private
  
  def self.determine_severity(event_type)
    severity_map = {
      'login_failure' => 'medium',
      'login_success' => 'low',
      'privilege_escalation' => 'high',
      'data_access' => 'medium',
      'config_change' => 'high',
      'security_breach' => 'critical',
      'anomaly_detected' => 'high'
    }
    
    severity_map[event_type] || 'medium'
  end
  
  def self.determine_source
    'security_monitoring_system'
  end
  
  def self.log_to_file(log_entry)
    log_file = 'security.log'
    File.open(log_file, 'a') do |file|
      file.puts(JSON.pretty_generate(log_entry))
    end
  end
  
  def self.send_to_monitoring(log_entry)
    # Send to monitoring service
    puts "Sending to monitoring service: #{log_entry[:event_type]}"
  end
  
  def self.alert_security_team(log_entry)
    # Send alert to security team
    puts "ALERT: High severity event - #{log_entry[:event_type]}"
  end
  
  def self.unusual_location?(user_id, ip_address)
    # Simplified location checking
    false
  end
  
  def self.rapid_actions?(user_id, action, timestamp)
    # Simplified rapid action detection
    false
  end
  
  def self.privilege_escalation?(user_id, action, previous_role)
    # Simplified privilege escalation detection
    action.include?('admin') || action.include?('sudo')
  end
  
  def self.check_password_policies
    {
      name: 'Password Policies',
      status: 'passed',
      score: 8,
      issues: [],
      recommendations: ['Consider implementing 2FA']
    }
  end
  
  def self.check_encryption_usage
    {
      name: 'Encryption Usage',
      status: 'passed',
      score: 9,
      issues: [],
      recommendations: []
    }
  end
  
  def self.check_access_controls
    {
      name: 'Access Controls',
      status: 'warning',
      score: 6,
      issues: ['Some users have excessive permissions'],
      recommendations: ['Review user permissions regularly']
    }
  end
  
  def self.check_logging_configuration
    {
      name: 'Logging Configuration',
      status: 'passed',
      score: 7,
      issues: [],
      recommendations: ['Add more detailed logging for security events']
    }
  end
  
  def self.check_dependency_vulnerabilities
    {
      name: 'Dependency Vulnerabilities',
      status: 'warning',
      score: 5,
      issues: ['Some dependencies have known vulnerabilities'],
      recommendations: ['Update dependencies to latest versions']
    }
  end
  
  def self.get_recent_incidents(days = 30)
    # Simplified incident retrieval
    [
      { date: Time.now - 1.day, type: 'login_failure', severity: 'medium' },
      { date: Time.now - 3.days, type: 'data_access', severity: 'low' },
      { date: Time.now - 7.days, type: 'config_change', severity: 'high' }
    ]
  end
  
  def self.scan_vulnerabilities
    # Simplified vulnerability scanning
    [
      { type: 'sql_injection', severity: 'high', count: 1 },
      { type: 'xss', severity: 'medium', count: 2 },
      { type: 'weak_password', severity: 'low', count: 5 }
    ]
  end
  
  def self.count_vulnerabilities(severity)
    vulnerabilities = scan_vulnerabilities
    vulnerabilities.select { |v| v[:severity] == severity }.sum { |v| v[:count] }
  end
  
  def self.calculate_security_score
    # Simplified security score calculation
    75
  end
  
  def self.get_last_audit_date
    Time.now - 7.days
  end
  
  def self.get_incident_count(days)
    get_recent_incidents(days).length
  end
  
  def self.generate_recommendations(report)
    recommendations = []
    
    if report[:summary][:high_risk_vulnerabilities] > 0
      recommendations << 'Address high-risk vulnerabilities immediately'
    end
    
    if report[:summary][:security_score] < 70
      recommendations << 'Improve overall security posture'
    end
    
    if report[:summary][:total_incidents] > 10
      recommendations << 'Investigate increase in security incidents'
    end
    
    recommendations
  end
  
  def self.save_security_report(report)
    filename = "security_report_#{Time.now.strftime('%Y%m%d_%H%M%S')}.json"
    File.write(filename, JSON.pretty_generate(report))
    puts "Report saved to: #{filename}"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Encryption**: Implement AES encryption
2. **Hash Functions**: Create secure hashing
3. **Password Security**: Implement password hashing
4. **Input Validation**: Create validation functions

### Intermediate Exercises

1. **RSA Implementation**: Build RSA encryption system
2. **ECC Cryptography**: Implement elliptic curve crypto
3. **Security Patterns**: Apply security best practices
4. **Security Monitoring**: Create monitoring system

### Advanced Exercises

1. **Advanced Encryption**: Implement multiple algorithms
2. **Security Audit**: Build comprehensive audit system
3. **Anomaly Detection**: Create detection algorithms
4. **Real-world Security**: Apply to production systems

---

## 🎯 Summary

Encryption and Security in Ruby provide:

- **Encryption Fundamentals** - Core cryptographic concepts
- **Symmetric Encryption** - AES implementation and modes
- **Asymmetric Encryption** - RSA and ECC implementations
- **Hash Functions** - Secure hashing and password security
- **Security Patterns** - Best practices and validation
- **Security Monitoring** - Auditing and anomaly detection

Master these security techniques for robust Ruby applications!
