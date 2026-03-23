# Cryptography Basics in Ruby
# Comprehensive guide to cryptographic concepts and implementations

## 🔐 Cryptography Fundamentals

### 1. Cryptographic Concepts

Core cryptographic principles:

```ruby
class CryptographyBasics
  def self.explain_crypto_concepts
    puts "Cryptography Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Encryption",
        description: "Process of converting plaintext to ciphertext",
        purpose: "Protect confidentiality of data",
        types: ["Symmetric encryption", "Asymmetric encryption"],
        examples: ["AES", "RSA", "ECC"]
      },
      {
        concept: "Decryption",
        description: "Process of converting ciphertext back to plaintext",
        purpose: "Recover original message",
        requires: ["Correct key", "Proper algorithm", "Valid ciphertext"]
      },
      {
        concept: "Hash Functions",
        description: "One-way functions that produce fixed-size output",
        properties: ["Deterministic", "Collision-resistant", "Preimage-resistant"],
        uses: ["Data integrity", "Password storage", "Digital signatures"]
      },
      {
        concept: "Digital Signatures",
        description: "Cryptographic proof of authenticity",
        components: ["Private key signing", "Public key verification"],
        provides: ["Authentication", "Non-repudiation", "Integrity"]
      },
      {
        concept: "Key Exchange",
        description: "Securely exchange cryptographic keys",
        protocols: ["Diffie-Hellman", "RSA key exchange", "ECDH"],
        purpose: "Establish shared secret over insecure channel"
      },
      {
        concept: "Public Key Infrastructure",
        description: "Framework for managing digital certificates",
        components: ["Certificate Authority", "Certificates", "Revocation"],
        purpose: "Trust management in public key cryptography"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Purpose: #{concept[:purpose]}" if concept[:purpose]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Requires: #{concept[:requires].join(', ')}" if concept[:requires]
      puts "  Properties: #{concept[:properties].join(', ')}" if concept[:properties]
      puts "  Uses: #{concept[:uses].join(', ')}" if concept[:uses]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Provides: #{concept[:provides].join(', ')}" if concept[:provides]
      puts "  Protocols: #{concept[:protocols].join(', ')}" if concept[:protocols]
      puts
    end
  end
  
  def self.symmetric_vs_asymmetric
    puts "\nSymmetric vs Asymmetric Cryptography:"
    puts "=" * 50
    
    comparison = [
      {
        aspect: "Key Usage",
        symmetric: "Same key for encryption and decryption",
        asymmetric: "Different keys (public/private)"
      },
      {
        aspect: "Performance",
        symmetric: "Fast, efficient for large data",
        asymmetric: "Slower, used for small data"
      },
      {
        aspect: "Key Management",
        symmetric: "Simple but requires secure distribution",
        asymmetric: "Complex but easier distribution"
      },
      {
        aspect: "Use Cases",
        symmetric: "Bulk data encryption, file encryption",
        asymmetric: "Key exchange, digital signatures"
      },
      {
        aspect: "Security",
        symmetric: "Security depends on key secrecy",
        asymmetric: "Security depends on private key secrecy"
      },
      {
        aspect: "Scalability",
        symmetric: "O(n) keys for n users",
        asymmetric: "O(2n) keys for n users"
      }
    ]
    
    comparison.each do |item|
      puts "#{item[:aspect]}:"
      puts "  Symmetric: #{item[:symmetric]}"
      puts "  Asymmetric: #{item[:asymmetric]}"
      puts
    end
  end
  
  def self.cryptographic_principles
    puts "\nCryptographic Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Confidentiality",
        description: "Information is not disclosed to unauthorized individuals",
        mechanism: "Encryption",
        blockchain_use: "Transaction privacy, wallet encryption"
      },
      {
        principle: "Integrity",
        description: "Information is not altered in transit",
        mechanism: "Hash functions, digital signatures",
        blockchain_use: "Block hashing, transaction verification"
      },
      {
        principle: "Authentication",
        description: "Identity of communicating parties is verified",
        mechanism: "Digital signatures, certificates",
        blockchain_use: "Wallet ownership, transaction signing"
      },
      {
        principle: "Non-repudiation",
        description: "Sender cannot deny sending a message",
        mechanism: "Digital signatures",
        blockchain_use: "Transaction accountability"
      },
      {
        principle: "Availability",
        description: "Information is accessible when needed",
        mechanism: "Redundancy, distributed systems",
        blockchain_use: "Decentralized network, consensus"
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Mechanism: #{principle[:mechanism]}"
      puts "  Blockchain Use: #{principle[:blockchain_use]}"
      puts
    end
  end
  
  # Run cryptography basics
  explain_crypto_concepts
  symmetric_vs_asymmetric
  cryptographic_principles
end
```

### 2. Hash Functions

Cryptographic hash implementations:

```ruby
class HashFunctions
  def self.simple_hash(data)
    # Simple hash function for demonstration
    hash = 0
    
    data.each_byte do |byte|
      hash = ((hash << 5) + hash) + byte
    end
    
    hash.abs
  end
  
  def self.sha256_simulated(data)
    # Simulated SHA-256 (simplified for demonstration)
    # Real SHA-256 would use proper cryptographic functions
    
    # Convert to bytes
    bytes = data.is_a?(String) ? data.bytes : data
    
    # Padding (simplified)
    padded = bytes.dup
    padded << 0x80
    while (padded.length % 64) != 56
      padded << 0x00
    end
    
    # Add length (simplified)
    padded.concat([bytes.length * 8].pack('Q').bytes)
    
    # Process in 512-bit blocks (simplified)
    hash = [0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a,
             0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19]
    
    (padded.length / 64).times do |i|
      block = padded[i * 64, 64]
      
      # Simplified processing
      block.each_slice(4) do |word|
        word_value = word.pack('C*').unpack('L').first
        hash[0] = (hash[0] + word_value) & 0xffffffff
      end
    end
    
    # Convert to hex string
    hash.map { |h| h.to_s(16).rjust(8, '0') }.join
  end
  
  def self.merkle_root(hashes)
    return '' if hashes.empty?
    return hashes.first if hashes.length == 1
    
    # Build Merkle tree
    level = hashes.dup
    
    while level.length > 1
      next_level = []
      
      (0...level.length).step(2) do |i|
        left = level[i]
        right = i + 1 < level.length ? level[i + 1] : level[i]
        
        # Hash concatenated values
        combined = left + right
        next_level << sha256_simulated(combined)
      end
      
      level = next_level
    end
    
    level.first
  end
  
  def self.proof_of_work(data, difficulty = 4)
    target = '0' * difficulty
    
    nonce = 0
    hash = ''
    
    loop do
      # Concatenate data with nonce
      combined = data + nonce.to_s
      hash = sha256_simulated(combined)
      
      break if hash.start_with?(target)
      nonce += 1
    end
    
    { hash: hash, nonce: nonce }
  end
  
  def self.demonstrate_hash_functions
    puts "Hash Functions Demonstration:"
    puts "=" * 50
    
    # Test data
    test_data = [
      "Hello, World!",
      "Blockchain technology",
      "Cryptography is essential",
      "Ruby programming language",
      "Decentralized systems"
    ]
    
    puts "Hash Function Comparisons:"
    puts "-" * 40
    
    test_data.each do |data|
      simple_hash = simple_hash(data)
      sha256_hash = sha256_simulated(data)
      
      puts "Data: #{data}"
      puts "  Simple Hash: #{simple_hash}"
      puts "  SHA-256 (sim): #{sha256_hash[0...16]}..."
      puts
    end
    
    # Merkle tree demonstration
    puts "Merkle Tree Demonstration:"
    puts "-" * 40
    
    transactions = [
      "Alice pays Bob 10 BTC",
      "Bob pays Charlie 5 BTC",
      "Charlie pays Dave 3 BTC",
      "Dave pays Eve 2 BTC"
    ]
    
    transaction_hashes = transactions.map { |tx| sha256_simulated(tx) }
    
    puts "Transaction Hashes:"
    transaction_hashes.each_with_index do |hash, i|
      puts "  TX#{i + 1}: #{hash[0...16]}..."
    end
    
    merkle_root = merkle_root(transaction_hashes)
    puts "Merkle Root: #{merkle_root[0...16]}..."
    
    # Proof of Work demonstration
    puts "\nProof of Work Demonstration:"
    puts "-" * 40
    
    block_data = "Block #1: Previous hash: 0000... Transactions: #{transactions.join('|')}"
    
    puts "Mining block with data: #{block_data[0...50]}..."
    
    result = proof_of_work(block_data, 3)
    
    puts "Found hash: #{result[:hash]}"
    puts "Nonce: #{result[:nonce]}"
    puts "Difficulty: 3 leading zeros"
    
    puts "\nHash Function Properties:"
    puts "- Deterministic: Same input always produces same output"
    puts "- Fixed-size output: Always produces same length hash"
    puts "- Avalanche effect: Small input change causes large output change"
    puts "- Preimage resistance: Difficult to find input for given hash"
    puts "- Collision resistance: Difficult to find two inputs with same hash"
  end
end
```

## 🔑 Symmetric Cryptography

### 3. Caesar Cipher

Simple substitution cipher:

```ruby
class CaesarCipher
  def initialize(shift = 3)
    @shift = shift
    @alphabet = ('A'..'Z').to_a
  end
  
  def encrypt(plaintext)
    plaintext.upcase.chars.map do |char|
      if @alphabet.include?(char)
        index = @alphabet.index(char)
        new_index = (index + @shift) % 26
        @alphabet[new_index]
      else
        char
      end
    end.join
  end
  
  def decrypt(ciphertext)
    ciphertext.upcase.chars.map do |char|
      if @alphabet.include?(char)
        index = @alphabet.index(char)
        new_index = (index - @shift) % 26
        @alphabet[new_index]
      else
        char
      end
    end.join
  end
  
  def brute_force(ciphertext)
    puts "Brute Force Analysis:"
    puts "-" * 40
    
    (1..25).each do |shift|
      cipher = CaesarCipher.new(shift)
      decrypted = cipher.decrypt(ciphertext)
      puts "Shift #{shift}: #{decrypted}"
    end
  end
  
  def frequency_analysis(ciphertext)
    puts "Frequency Analysis:"
    puts "-" * 40
    
    # Count letter frequencies
    frequencies = Hash.new(0)
    ciphertext.upcase.chars.each do |char|
      frequencies[char] += 1 if @alphabet.include?(char)
    end
    
    # Sort by frequency
    sorted_freq = frequencies.sort_by { |_, count| -count }
    
    puts "Letter frequencies:"
    sorted_freq.each do |char, count|
      percentage = (count.to_f / ciphertext.length * 100).round(2)
      puts "  #{char}: #{count} (#{percentage}%)"
    end
    
    # Most common letter likely 'E' in English
    most_common = sorted_freq.first[0]
    puts "Most common letter: #{most_common}"
    puts "Likely shift: #{(@alphabet.index(most_common) - 4) % 26}" # E is 4th letter (0-indexed)
  end
  
  def self.demonstrate_caesar
    puts "Caesar Cipher Demonstration:"
    puts "=" * 50
    
    cipher = CaesarCipher.new(3)
    
    plaintext = "HELLO WORLD"
    puts "Plaintext: #{plaintext}"
    
    encrypted = cipher.encrypt(plaintext)
    puts "Encrypted: #{encrypted}"
    
    decrypted = cipher.decrypt(encrypted)
    puts "Decrypted: #{decrypted}"
    
    puts "\nCryptanalysis:"
    cipher.brute_force(encrypted)
    cipher.frequency_analysis(encrypted)
    
    puts "\nCaesar Cipher Properties:"
    puts "- Simple substitution cipher"
    puts "- Shifts letters by fixed amount"
    puts "- Vulnerable to frequency analysis"
    puts "- Only 25 possible keys"
    puts "- Example of symmetric encryption"
  end
end
```

### 4. Vigenère Cipher

Polyalphabetic substitution cipher:

```ruby
class VigenereCipher
  def initialize(keyword)
    @keyword = keyword.upcase.gsub(/[^A-Z]/, '')
    @alphabet = ('A'..'Z').to_a
  end
  
  def encrypt(plaintext)
    plaintext.upcase.gsub(/[^A-Z]/, '').chars.map.with_index do |char, index|
      if @alphabet.include?(char)
        key_char = @keyword[index % @keyword.length]
        key_shift = @alphabet.index(key_char)
        char_shift = @alphabet.index(char)
        new_index = (char_shift + key_shift) % 26
        @alphabet[new_index]
      else
        char
      end
    end.join
  end
  
  def decrypt(ciphertext)
    ciphertext.upcase.gsub(/[^A-Z]/, '').chars.map.with_index do |char, index|
      if @alphabet.include?(char)
        key_char = @keyword[index % @keyword.length]
        key_shift = @alphabet.index(key_char)
        char_shift = @alphabet.index(char)
        new_index = (char_shift - key_shift) % 26
        @alphabet[new_index]
      else
        char
      end
    end.join
  end
  
  def find_key_length(ciphertext)
    puts "Finding Key Length (Kasiski Examination):"
    puts "-" * 40
    
    # Find repeated sequences
    sequences = Hash.new
    
    (3...ciphertext.length).each do |length|
      (0...ciphertext.length - length).each do |start|
        sequence = ciphertext[start, length]
        positions = []
        
        (start + length...ciphertext.length - length + 1).each do |pos|
          if ciphertext[pos, length] == sequence
            positions << pos
          end
        end
        
        if positions.length > 1
          sequences[sequence] = positions
        end
      end
    end
    
    # Calculate distances between repetitions
    distances = []
    sequences.each do |sequence, positions|
      positions.each_cons(2) do |pos1, pos2|
        distances << pos2 - pos1
      end
    end
    
    # Find factors of distances
    factors = Hash.new(0)
    distances.each do |distance|
      (2...distance).each do |factor|
        if distance % factor == 0
          factors[factor] += 1
        end
      end
    end
    
    # Most likely key length
    likely_length = factors.max_by { |_, count| count }&.first
    
    puts "Repeated sequences: #{sequences.length}"
    puts "Most likely key length: #{likely_length}"
    
    likely_length
  end
  
  def self.demonstrate_vigenere
    puts "Vigenère Cipher Demonstration:"
    puts "=" * 50
    
    cipher = VigenereCipher.new("KEYWORD")
    
    plaintext = "THIS IS A SECRET MESSAGE"
    puts "Plaintext: #{plaintext}"
    puts "Keyword: KEYWORD"
    
    encrypted = cipher.encrypt(plaintext)
    puts "Encrypted: #{encrypted}"
    
    decrypted = cipher.decrypt(encrypted)
    puts "Decrypted: #{decrypted}"
    
    puts "\nCryptanalysis:"
    cipher.find_key_length(encrypted)
    
    puts "\nVigenère Cipher Properties:"
    puts "- Polyalphabetic substitution cipher"
    puts "- Uses keyword for shifting"
    puts "- More secure than Caesar cipher"
    puts "- Vulnerable to Kasiski examination"
    puts "- Example of symmetric encryption"
  end
end
```

## 🔐 Asymmetric Cryptography

### 5. RSA Algorithm

Public key cryptography implementation:

```ruby
class RSA
  def initialize(bits = 512)
    @bits = bits
    @p, @q = generate_primes
    @n = @p * @q
    @phi = (@p - 1) * (@q - 1)
    @e = choose_public_exponent(@phi)
    @d = modular_inverse(@e, @phi)
  end
  
  attr_reader :n, :e, :d, :p, :q
  
  def encrypt(message)
    # Convert message to numbers
    message_bytes = message.bytes
    encrypted_bytes = []
    
    message_bytes.each do |byte|
      encrypted = mod_pow(byte, @e, @n)
      encrypted_bytes << encrypted
    end
    
    encrypted_bytes
  end
  
  def decrypt(encrypted_bytes)
    decrypted_bytes = []
    
    encrypted_bytes.each do |encrypted_byte|
      decrypted = mod_pow(encrypted_byte, @d, @n)
      decrypted_bytes << decrypted
    end
    
    decrypted_bytes.pack('C*')
  end
  
  def sign(message)
    # Create digital signature
    hash = simple_hash(message)
    signature = mod_pow(hash, @d, @n)
    signature
  end
  
  def verify(message, signature)
    # Verify digital signature
    hash = simple_hash(message)
    decrypted_signature = mod_pow(signature, @e, @n)
    
    hash == decrypted_signature
  end
  
  def self.generate_key_pair(bits = 512)
    rsa = RSA.new(bits)
    
    {
      public_key: { n: rsa.n, e: rsa.e },
      private_key: { n: rsa.n, d: rsa.d, p: rsa.p, q: rsa.q }
    }
  end
  
  def self.demonstrate_rsa
    puts "RSA Algorithm Demonstration:"
    puts "=" * 50
    
    # Generate key pair
    key_pair = generate_key_pair(512)
    
    puts "Key Pair Generated:"
    puts "Public Key (n, e):"
    puts "  n: #{key_pair[:public_key][:n]}"
    puts "  e: #{key_pair[:public_key][:e]}"
    puts "Private Key (n, d):"
    puts "  n: #{key_pair[:private_key][:n]}"
    puts "  d: #{key_pair[:private_key][:d]}"
    
    # Create RSA instance
    rsa = RSA.new(512)
    
    # Encrypt and decrypt message
    message = "Hello RSA!"
    puts "\nOriginal message: #{message}"
    
    encrypted = rsa.encrypt(message)
    puts "Encrypted: #{encrypted}"
    
    decrypted = rsa.decrypt(encrypted)
    puts "Decrypted: #{decrypted}"
    
    # Digital signature
    puts "\nDigital Signature:"
    signature = rsa.sign(message)
    puts "Signature: #{signature}"
    
    is_valid = rsa.verify(message, signature)
    puts "Signature valid: #{is_valid}"
    
    # Verify with wrong message
    wrong_message = "Wrong message!"
    is_valid_wrong = rsa.verify(wrong_message, signature)
    puts "Signature valid for wrong message: #{is_valid_wrong}"
    
    puts "\nRSA Properties:"
    puts "- Asymmetric encryption algorithm"
    puts "- Based on difficulty of factoring large numbers"
    puts "- Used for key exchange and digital signatures"
    puts "- Security depends on key size"
    puts "- Slower than symmetric encryption"
  end
  
  private
  
  def generate_primes
    # Simplified prime generation
    # In practice, use cryptographically secure random numbers
    p = find_prime(@bits / 2)
    q = find_prime(@bits / 2)
    
    [p, q]
  end
  
  def find_prime(bits)
    loop do
      candidate = rand(2 ** (bits - 1) + 1)
      return candidate if is_prime?(candidate)
    end
  end
  
  def is_prime?(n)
    return false if n < 2
    return true if n == 2 || n == 3
    return false if n.even?
    
    (3..Math.sqrt(n).to_i).step(2) do |i|
      return false if n % i == 0
    end
    
    true
  end
  
  def choose_public_exponent(phi)
    # Common choice is 65537
    65537
  end
  
  def modular_inverse(a, m)
    # Extended Euclidean algorithm
    g, x, y = extended_gcd(a, m)
    
    raise "No modular inverse exists" unless g == 1
    
    x % m
  end
  
  def extended_gcd(a, b)
    return [a, 1, 0] if b == 0
    
    g, x1, y1 = extended_gcd(b, a % b)
    x = y1
    y = x1 - (a / b) * y1
    
    [g, x, y]
  end
  
  def mod_pow(base, exponent, modulus)
    result = 1
    base = base % modulus
    
    while exponent > 0
      result = (result * base) % modulus if exponent.odd?
      base = (base * base) % modulus
      exponent >>= 1
    end
    
    result
  end
  
  def simple_hash(message)
    HashFunctions.simple_hash(message)
  end
end
```

### 6. Diffie-Hellman Key Exchange

Secure key exchange protocol:

```ruby
class DiffieHellman
  def initialize(prime = nil, generator = nil)
    @prime = prime || 23 # Small prime for demonstration
    @generator = generator || 5 # Small generator for demonstration
    @private_key = rand(@prime - 2) + 1
    @public_key = mod_pow(@generator, @private_key, @prime)
  end
  
  attr_reader :public_key, :private_key
  
  def compute_shared_key(other_public_key)
    mod_pow(other_public_key, @private_key, @prime)
  end
  
  def self.key_exchange
    puts "Diffie-Hellman Key Exchange:"
    puts "=" * 50
    
    # Public parameters
    prime = 23
    generator = 5
    
    puts "Public parameters:"
    puts "  Prime (p): #{prime}"
    puts "  Generator (g): #{generator}"
    
    # Alice generates keys
    alice = DiffieHellman.new(prime, generator)
    puts "\nAlice:"
    puts "  Private key: #{alice.private_key}"
    puts "  Public key: #{alice.public_key}"
    
    # Bob generates keys
    bob = DiffieHellman.new(prime, generator)
    puts "\nBob:"
    puts "  Private key: #{bob.private_key}"
    puts "  Public key: #{bob.public_key}"
    
    # Exchange public keys and compute shared secret
    alice_shared = alice.compute_shared_key(bob.public_key)
    bob_shared = bob.compute_shared_key(alice.public_key)
    
    puts "\nShared secret computation:"
    puts "  Alice computes: g^bob_private mod p = #{alice_shared}"
    puts "  Bob computes: g^alice_private mod p = #{bob_shared}"
    
    puts "\nShared secrets match: #{alice_shared == bob_shared}"
    puts "Shared secret: #{alice_shared}"
    
    puts "\nDiffie-Hellman Properties:"
    puts "- Allows secure key exchange over insecure channel"
    puts "- Based on discrete logarithm problem"
    puts "- Eavesdropper cannot compute shared secret"
    puts "- Vulnerable to man-in-the-middle without authentication"
    puts "- Foundation for many cryptographic protocols"
  end
  
  def self.demonstrate_man_in_middle
    puts "\nMan-in-the-Middle Attack Demonstration:"
    puts "=" * 50
    
    # Public parameters
    prime = 23
    generator = 5
    
    puts "Public parameters: p=#{prime}, g=#{generator}"
    
    # Alice and Bob generate keys
    alice = DiffieHellman.new(prime, generator)
    bob = DiffieHellman.new(prime, generator)
    
    # Mallory (attacker) generates keys for both sides
    mallory_alice = DiffieHellman.new(prime, generator)
    mallory_bob = DiffieHellman.new(prime, generator)
    
    puts "\nKey exchange with MITM attack:"
    puts "Alice sends public key to Bob, but Mallory intercepts"
    puts "Bob sends public key to Alice, but Mallory intercepts"
    
    # Mallory establishes separate shared secrets
    alice_shared = alice.compute_shared_key(mallory_bob.public_key)
    bob_shared = bob.compute_shared_key(mallory_alice.public_key)
    mallory_alice_shared = mallory_alice.compute_shared_key(alice.public_key)
    mallory_bob_shared = mallory_bob.compute_shared_key(bob.public_key)
    
    puts "\nShared secrets:"
    puts "Alice-Mallory: #{alice_shared}"
    puts "Bob-Mallory: #{bob_shared}"
    puts "Mallory-Alice: #{mallory_alice_shared}"
    puts "Mallory-Bob: #{mallory_bob_shared}"
    
    puts "\nAttack consequences:"
    puts "- Alice and Bob think they're communicating securely"
    puts "- Mallory can read and modify all messages"
    puts "- Need authentication to prevent MITM attacks"
  end
  
  private
  
  def mod_pow(base, exponent, modulus)
    result = 1
    base = base % modulus
    
    while exponent > 0
      result = (result * base) % modulus if exponent.odd?
      base = (base * base) % modulus
      exponent >>= 1
    end
    
    result
  end
end
```

## 🗝️ Digital Signatures

### 7. Digital Signature Implementation

Cryptographic signing and verification:

```ruby
class DigitalSignature
  def initialize
    @rsa = RSA.new(512)
  end
  
  def sign_message(message)
    # Create hash of message
    message_hash = HashFunctions.sha256_simulated(message)
    
    # Convert hash to number
    hash_number = message_hash.to_i(16)
    
    # Sign with private key
    signature = @rsa.send(:mod_pow, hash_number, @rsa.d, @rsa.n)
    
    {
      message: message,
      signature: signature,
      hash: message_hash
    }
  end
  
  def verify_signature(signed_data)
    message = signed_data[:message]
    signature = signed_data[:signature]
    original_hash = signed_data[:hash]
    
    # Verify signature with public key
    decrypted_signature = @rsa.send(:mod_pow, signature, @rsa.e, @rsa.n)
    
    # Calculate current hash
    current_hash = HashFunctions.sha256_simulated(message)
    current_hash_number = current_hash.to_i(16)
    
    {
      valid: decrypted_signature == current_hash_number,
      original_hash: original_hash,
      current_hash: current_hash,
      decrypted_signature: decrypted_signature
    }
  end
  
  def self.demonstrate_digital_signatures
    puts "Digital Signature Demonstration:"
    puts "=" * 50
    
    ds = DigitalSignature.new
    
    # Sign a message
    message = "This is a signed message"
    puts "Original message: #{message}"
    
    signed_data = ds.sign_message(message)
    puts "Signature created: #{signed_data[:signature]}"
    puts "Message hash: #{signed_data[:hash][0...16]}..."
    
    # Verify signature
    verification = ds.verify_signature(signed_data)
    puts "\nVerification results:"
    puts "Signature valid: #{verification[:valid]}"
    puts "Original hash: #{verification[:original_hash][0...16]}..."
    puts "Current hash: #{verification[:current_hash][0...16]}..."
    
    # Test with tampered message
    tampered_data = signed_data.dup
    tampered_data[:message] = "This is a TAMPERED message"
    
    tampered_verification = ds.verify_signature(tampered_data)
    puts "\nTampered message verification:"
    puts "Signature valid: #{tampered_verification[:valid]}"
    puts "Expected: false (signature should be invalid)"
    
    puts "\nDigital Signature Properties:"
    puts "- Provides authentication (proves signer identity)"
    puts "- Provides integrity (detects message tampering)"
    puts "- Provides non-repudiation (signer cannot deny)"
    puts "- Uses private key for signing, public key for verification"
    puts "- Foundation for blockchain transactions"
  end
end
```

## 🔒 Blockchain Cryptography

### 8. Blockchain Cryptographic Applications

Cryptography in blockchain systems:

```ruby
class BlockchainCryptography
  def self.create_block(index, previous_hash, transactions, timestamp = Time.now)
    # Create block header
    header = {
      index: index,
      previous_hash: previous_hash,
      timestamp: timestamp.to_i,
      transactions: transactions,
      nonce: 0
    }
    
    # Mine block (find valid hash)
    mined_block = mine_block(header)
    
    mined_block
  end
  
  def self.mine_block(header, difficulty = 4)
    target = '0' * difficulty
    
    loop do
      # Create block string
      block_string = "#{header[:index]}#{header[:previous_hash]}#{header[:timestamp]}#{header[:transactions].join('|')}#{header[:nonce]}"
      
      # Calculate hash
      block_hash = HashFunctions.sha256_simulated(block_string)
      
      # Check if hash meets difficulty
      if block_hash.start_with?(target)
        return {
          header: header,
          hash: block_hash,
          valid: true
        }
      end
      
      # Increment nonce and try again
      header[:nonce] += 1
    end
  end
  
  def self.verify_block(block)
    # Recreate block hash
    block_string = "#{block[:header][:index]}#{block[:header][:previous_hash]}#{block[:header][:timestamp]}#{block[:header][:transactions].join('|')}#{block[:header][:nonce]}"
    calculated_hash = HashFunctions.sha256_simulated(block_string)
    
    # Verify hash matches
    hash_matches = calculated_hash == block[:hash]
    
    # Verify hash meets difficulty (starts with zeros)
    valid_proof = block[:hash].start_with?('0000')
    
    {
      hash_valid: hash_matches,
      proof_valid: valid_proof,
      block_valid: hash_matches && valid_proof
    }
  end
  
  def self.create_wallet
    # Generate key pair
    key_pair = RSA.generate_key_pair(256)
    
    {
      address: key_pair[:public_key][:n].to_s(16)[0...16], # Simplified address
      public_key: key_pair[:public_key],
      private_key: key_pair[:private_key]
    }
  end
  
  def self.sign_transaction(from_wallet, to_address, amount)
    # Create transaction
    transaction = {
      from: from_wallet[:address],
      to: to_address,
      amount: amount,
      timestamp: Time.now.to_i
    }
    
    # Sign transaction
    rsa = RSA.new(256)
    rsa.instance_variable_set(:@n, from_wallet[:private_key][:n])
    rsa.instance_variable_set(:@d, from_wallet[:private_key][:d])
    
    transaction_hash = HashFunctions.sha256_simulated(transaction.to_json)
    signature = rsa.send(:mod_pow, transaction_hash.to_i(16), rsa.d, rsa.n)
    
    {
      transaction: transaction,
      signature: signature,
      hash: transaction_hash
    }
  end
  
  def self.verify_transaction(signed_transaction, public_key)
    transaction = signed_transaction[:transaction]
    signature = signed_transaction[:signature]
    original_hash = signed_transaction[:hash]
    
    # Verify signature
    rsa = RSA.new(256)
    rsa.instance_variable_set(:@n, public_key[:n])
    rsa.instance_variable_set(:@e, public_key[:e])
    
    decrypted_signature = rsa.send(:mod_pow, signature, rsa.e, rsa.n)
    current_hash = HashFunctions.sha256_simulated(transaction.to_json)
    
    {
      valid: decrypted_signature == current_hash.to_i(16),
      original_hash: original_hash,
      current_hash: current_hash
    }
  end
  
  def self.demonstrate_blockchain_crypto
    puts "Blockchain Cryptography Demonstration:"
    puts "=" * 50
    
    # Create wallets
    alice_wallet = create_wallet
    bob_wallet = create_wallet
    
    puts "Wallets created:"
    puts "Alice address: #{alice_wallet[:address]}"
    puts "Bob address: #{bob_wallet[:address]}"
    
    # Create and sign transaction
    puts "\nCreating transaction:"
    signed_tx = sign_transaction(alice_wallet, bob_wallet[:address], 10)
    
    puts "Transaction: #{signed_tx[:transaction]}"
    puts "Signature: #{signed_tx[:signature]}"
    puts "Hash: #{signed_tx[:hash][0...16]}..."
    
    # Verify transaction
    verification = verify_transaction(signed_tx, bob_wallet[:public_key])
    puts "\nTransaction verification:"
    puts "Valid: #{verification[:valid]}"
    puts "Original hash: #{verification[:original_hash][0...16]}..."
    puts "Current hash: #{verification[:current_hash][0...16]}..."
    
    # Create block
    puts "\nCreating block:"
    transactions = [signed_tx[:transaction]]
    block = create_block(1, "0000000000000000000000000000000000000000000000000000000000000000", transactions)
    
    puts "Block created:"
    puts "  Index: #{block[:header][:index]}"
    puts "  Previous hash: #{block[:header][:previous_hash][0...16]}..."
    puts "  Hash: #{block[:hash]}"
    puts "  Nonce: #{block[:header][:nonce]}"
    
    # Verify block
    block_verification = verify_block(block)
    puts "\nBlock verification:"
    puts "Hash valid: #{block_verification[:hash_valid]}"
    puts "Proof valid: #{block_verification[:proof_valid]}"
    puts "Block valid: #{block_verification[:block_valid]}"
    
    puts "\nBlockchain Cryptography Features:"
    puts "- Hash functions for block integrity"
    puts "- Digital signatures for transaction authenticity"
    puts "- Proof of work for consensus"
    puts "- Public key cryptography for wallet security"
    puts "- Merkle trees for transaction verification"
  end
end
```

## 🎯 Practical Applications

### 9. Cryptographic Tools

Useful cryptographic utilities:

```ruby
class CryptographicTools
  def self.password_hashing(password, salt = nil)
    # Generate salt if not provided
    salt ||= Array.new(16) { rand(256) }.pack('C*')
    
    # Hash password with salt (simplified PBKDF2)
    iterations = 1000
    hash = password + salt
    
    iterations.times do
      hash = HashFunctions.sha256_simulated(hash)
    end
    
    {
      hash: hash,
      salt: salt.unpack('H*').first
    }
  end
  
  def self.verify_password(password, stored_hash, salt)
    salt_bytes = [salt].pack('H*')
    computed = password_hashing(password, salt_bytes)
    
    computed[:hash] == stored_hash
  end
  
  def self.generate_random_bytes(length)
    Array.new(length) { rand(256) }.pack('C*')
  end
  
  def self.simple_xor_cipher(data, key)
    key_bytes = key.bytes
    data_bytes = data.bytes
    
    encrypted_bytes = data_bytes.map.with_index do |byte, index|
      key_byte = key_bytes[index % key_bytes.length]
      byte ^ key_byte
    end
    
    encrypted_bytes.pack('C*')
  end
  
  def self.demonstrate_tools
    puts "Cryptographic Tools Demonstration:"
    puts "=" * 50
    
    # Password hashing
    puts "Password Hashing:"
    password = "secret_password_123"
    hashed = password_hashing(password)
    
    puts "Password: #{password}"
    puts "Hashed: #{hashed[:hash][0...32]}..."
    puts "Salt: #{hashed[:salt]}"
    
    is_valid = verify_password(password, hashed[:hash], hashed[:salt])
    puts "Password valid: #{is_valid}"
    
    # Random bytes
    puts "\nRandom Bytes Generation:"
    random_bytes = generate_random_bytes(16)
    puts "Random bytes: #{random_bytes.unpack('H*').first}"
    
    # XOR cipher
    puts "\nXOR Cipher:"
    message = "Secret message"
    key = "key123"
    
    encrypted = simple_xor_cipher(message, key)
    decrypted = simple_xor_cipher(encrypted, key)
    
    puts "Original: #{message}"
    puts "Encrypted: #{encrypted.unpack('H*').first}"
    puts "Decrypted: #{decrypted}"
    
    puts "\nCryptographic Tools Features:"
    puts "- Secure password hashing with salt"
    puts "- Cryptographically secure random generation"
    puts "- Simple XOR cipher for demonstration"
    puts "- Password verification utilities"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Hash Functions**: Implement simple hash function
2. **Caesar Cipher**: Create substitution cipher
3. **Vigenère Cipher**: Build polyalphabetic cipher
4. **Password Hashing**: Implement secure password storage

### Intermediate Exercises

1. **RSA Implementation**: Build RSA encryption system
2. **Diffie-Hellman**: Implement key exchange
3. **Digital Signatures**: Create signing system
4. **Blockchain Crypto**: Apply to blockchain systems

### Advanced Exercises

1. **Advanced Ciphers**: Implement modern ciphers
2. **Cryptanalysis**: Break simple ciphers
3. **Secure Protocols**: Build secure communication
4. **Performance**: Optimize cryptographic operations

---

## 🎯 Summary

Cryptography Basics in Ruby provide:

- **Cryptographic Fundamentals** - Core concepts and principles
- **Hash Functions** - SHA-256, Merkle trees, proof of work
- **Symmetric Cryptography** - Caesar, Vigenère ciphers
- **Asymmetric Cryptography** - RSA, Diffie-Hellman
- **Digital Signatures** - Signing and verification
- **Blockchain Applications** - Cryptographic uses in blockchain
- **Practical Tools** - Password hashing, random generation

Master these cryptographic foundations for secure blockchain development!
