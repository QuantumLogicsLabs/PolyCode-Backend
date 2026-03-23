# Cybersecurity Examples in Ruby
# Demonstrating security concepts and implementations

require 'digest'
require 'openssl'
require 'base64'
require 'json'

class CybersecurityExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🔒 Cybersecurity Examples in Ruby"
    puts "================================="
    puts "Explore security concepts and implementations!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Cybersecurity Examples Menu:"
      puts "1. Encryption and Decryption"
      puts "2. Hash Functions and Salting"
      puts "3. Digital Signatures"
      puts "4. Password Security"
      puts "5. SSL/TLS Implementation"
      puts "6. Security Auditing"
      puts "7. Vulnerability Scanning"
      puts "8. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-8): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        encryption_decryption
      when 2
        hash_functions_salting
      when 3
        digital_signatures
      when 4
        password_security
      when 5
        ssl_tls_implementation
      when 6
        security_auditing
      when 7
        vulnerability_scanning
      when 8
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def encryption_decryption
    puts "\n🔐 Example 1: Encryption and Decryption"
    puts "=" * 50
    puts "Implementing symmetric and asymmetric encryption."
    puts ""
    
    # Symmetric encryption (AES)
    puts "🔐 AES Symmetric Encryption:"
    
    class AESEncryption
      def initialize(key)
        @key = key
        @cipher = OpenSSL::Cipher::AES.new(256, :CBC)
      end
      
      def encrypt(plaintext)
        @cipher.encrypt
        @cipher.key = @key
        @cipher.iv = iv = @cipher.random_iv
        
        encrypted = @cipher.update(plaintext) + @cipher.final
        Base64.strict_encode64(iv + encrypted)
      end
      
      def decrypt(ciphertext)
        data = Base64.strict_decode64(ciphertext)
        iv = data[0...16]
        encrypted = data[16..-1]
        
        @cipher.decrypt
        @cipher.key = @key
        @cipher.iv = iv
        
        @cipher.update(encrypted) + @cipher.final
      end
    end
    
    # Asymmetric encryption (RSA)
    puts "\n🔑 RSA Asymmetric Encryption:"
    
    class RSAEncryption
      def initialize(key_size = 2048)
        @key = OpenSSL::PKey::RSA.new(key_size)
      end
      
      def encrypt(plaintext, public_key = nil)
        key = public_key || @key.public_key
        Base64.strict_encode64(key.public_encrypt(plaintext))
      end
      
      def decrypt(ciphertext, private_key = nil)
        key = private_key || @key
        key.private_decrypt(Base64.strict_decode64(ciphertext))
      end
      
      def get_public_key
        @key.public_key.to_pem
      end
      
      def get_private_key
        @key.to_pem
      end
      
      def self.from_pem(public_pem, private_pem = nil)
        rsa = new
        if private_pem
          rsa.instance_variable_set(:@key, OpenSSL::PKey::RSA.new(private_pem))
        else
          rsa.instance_variable_set(:@key, OpenSSL::PKey::RSA.new(public_pem))
        end
        rsa
      end
    end
    
    # Encryption demonstrations
    puts "\nEncryption Demonstrations:"
    
    # AES encryption
    puts "\nAES Encryption:"
    aes_key = "my_secret_key_32_bytes_long_aes_key!"
    aes = AESEncryption.new(aes_key)
    
    plaintext = "This is a secret message that will be encrypted using AES."
    puts "  Original: #{plaintext}"
    
    encrypted_aes = aes.encrypt(plaintext)
    puts "  Encrypted: #{encrypted_aes[0..50]}..."
    
    decrypted_aes = aes.decrypt(encrypted_aes)
    puts "  Decrypted: #{decrypted_aes}"
    puts "  Success: #{plaintext == decrypted_aes}"
    
    # RSA encryption
    puts "\nRSA Encryption:"
    rsa = RSAEncryption.new(1024)  # Smaller key for demo
    
    plaintext_rsa = "This message will be encrypted with RSA."
    puts "  Original: #{plaintext_rsa}"
    
    encrypted_rsa = rsa.encrypt(plaintext_rsa)
    puts "  Encrypted: #{encrypted_rsa[0..50]}..."
    
    decrypted_rsa = rsa.decrypt(encrypted_rsa)
    puts "  Decrypted: #{decrypted_rsa}"
    puts "  Success: #{plaintext_rsa == decrypted_rsa}"
    
    # Key exchange demonstration
    puts "\nKey Exchange:"
    
    # Generate key pairs for two parties
    alice = RSAEncryption.new(1024)
    bob = RSAEncryption.new(1024)
    
    # Alice sends message to Bob
    alice_to_bob = alice.encrypt("Hello Bob!", bob.get_public_key)
    puts "  Alice to Bob encrypted: #{alice_to_bob[0..30]}..."
    
    # Bob decrypts message
    bob_decrypts = bob.decrypt(alice_to_bob)
    puts "  Bob decrypts: #{bob_decrypts}"
    
    # Bob replies to Alice
    bob_to_alice = bob.encrypt("Hello Alice!", alice.get_public_key)
    alice_decrypts = alice.decrypt(bob_to_alice)
    puts "  Alice decrypts: #{alice_decrypts}"
    
    @examples << {
      title: "Encryption and Decryption",
      description: "AES symmetric and RSA asymmetric encryption",
      code: <<~RUBY
        class AESEncryption
          def initialize(key)
            @cipher = OpenSSL::Cipher::AES.new(256, :CBC)
            @key = key
          end
          
          def encrypt(plaintext)
            @cipher.encrypt
            @cipher.key = @key
            Base64.strict_encode64(@cipher.update(plaintext) + @cipher.final)
          end
        end
      RUBY
    }
    
    puts "\n✅ Encryption and Decryption example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def hash_functions_salting
    puts "\n🔒 Example 2: Hash Functions and Salting"
    puts "=" * 50
    puts "Implementing secure hashing with salting."
    puts ""
    
    # Secure hash implementation
    puts "🔒 Secure Hash Implementation:"
    
    class SecureHash
      def self.hash_password(password, salt = nil)
        salt ||= generate_salt
        hash = Digest::SHA256.hexdigest(salt + password)
        { hash: hash, salt: salt }
      end
      
      def self.verify_password(password, stored_hash, salt)
        computed_hash = Digest::SHA256.hexdigest(salt + password)
        computed_hash == stored_hash
      end
      
      def self.generate_salt
        SecureRandom.hex(16)
      end
      
      def self.hash_file(filepath)
        return nil unless File.exist?(filepath)
        content = File.read(filepath)
        Digest::SHA256.hexdigest(content)
      end
      
      def self.hash_data(data)
        Digest::SHA256.hexdigest(data.to_s)
      end
    end
    
    # Password hashing demonstration
    puts "\nPassword Hashing:"
    
    passwords = ["password123", "secret456", "admin789"]
    
    passwords.each do |password|
      result = SecureHash.hash_password(password)
      puts "  Password: #{password}"
      puts "  Hash: #{result[:hash]}"
      puts "  Salt: #{result[:salt]}"
      puts "  Verification: #{SecureHash.verify_password(password, result[:hash], result[:salt])}"
      puts
    end
    
    # Rainbow table resistance
    puts "\nRainbow Table Resistance:"
    
    same_password = "mypassword"
    hash1 = SecureHash.hash_password(same_password)
    hash2 = SecureHash.hash_password(same_password)
    
    puts "  Same password with different salts:"
    puts "  Hash 1: #{hash1[:hash]}"
    puts "  Salt 1: #{hash1[:salt]}"
    puts "  Hash 2: #{hash2[:hash]}"
    puts "  Salt 2: #{hash2[:salt]}"
    puts "  Different hashes: #{hash1[:hash] != hash2[:hash]}"
    
    # File integrity checking
    puts "\nFile Integrity Checking:"
    
    # Create a test file
    test_content = "This is a test file for integrity checking."
    File.write('test_file.txt', test_content)
    
    original_hash = SecureHash.hash_file('test_file.txt')
    puts "  Original file hash: #{original_hash}"
    
    # Modify file
    File.write('test_file.txt', test_content + " Modified!")
    modified_hash = SecureHash.hash_file('test_file.txt')
    puts "  Modified file hash: #{modified_hash}"
    puts "  Hashes different: #{original_hash != modified_hash}"
    
    # Clean up
    File.delete('test_file.txt') if File.exist?('test_file.txt')
    
    # Data integrity with HMAC
    puts "\nHMAC for Data Integrity:"
    
    class HMACSecurity
      def self.generate_hmac(data, secret_key)
        OpenSSL::HMAC.hexdigest('SHA256', secret_key, data)
      end
      
      def self.verify_hmac(data, secret_key, received_hmac)
        computed_hmac = generate_hmac(data, secret_key)
        computed_hmac == received_hmac
      end
    end
    
    secret_key = "my_secret_key"
    message = "Important message"
    
    hmac = HMACSecurity.generate_hmac(message, secret_key)
    puts "  Message: #{message}"
    puts "  HMAC: #{hmac}"
    puts "  Verification: #{HMACSecurity.verify_hmac(message, secret_key, hmac)}"
    
    # Tampered message
    tampered_message = "Important message (tampered)"
    puts "  Tampered verification: #{HMACSecurity.verify_hmac(tampered_message, secret_key, hmac)}"
    
    @examples << {
      title: "Hash Functions and Salting",
      description: "Secure password hashing with salting",
      code: <<~RUBY
        class SecureHash
          def self.hash_password(password, salt = nil)
            salt ||= SecureRandom.hex(16)
            hash = Digest::SHA256.hexdigest(salt + password)
            { hash: hash, salt: salt }
          end
          
          def self.verify_password(password, stored_hash, salt)
            computed_hash = Digest::SHA256.hexdigest(salt + password)
            computed_hash == stored_hash
          end
        end
      RUBY
    }
    
    puts "\n✅ Hash Functions and Salting example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def digital_signatures
    puts "\n✍️ Example 3: Digital Signatures"
    puts "=" * 50
    puts "Implementing digital signatures for authentication."
    puts ""
    
    # Digital signature implementation
    puts "✍️ Digital Signature Implementation:"
    
    class DigitalSignature
      def initialize(key_size = 2048)
        @key = OpenSSL::PKey::RSA.new(key_size)
      end
      
      def sign(message)
        digest = Digest::SHA256.digest(message)
        signature = @key.sign(OpenSSL::Digest::SHA256.new, digest)
        Base64.strict_encode64(signature)
      end
      
      def verify(message, signature, public_key_pem)
        public_key = OpenSSL::PKey::RSA.new(public_key_pem)
        digest = Digest::SHA256.digest(message)
        signature_data = Base64.strict_decode64(signature)
        
        public_key.verify(OpenSSL::Digest::SHA256.new, digest, signature_data)
      end
      
      def get_public_key
        @key.public_key.to_pem
      end
      
      def get_private_key
        @key.to_pem
      end
    end
    
    # Certificate implementation
    puts "\n📜 Digital Certificate:"
    
    class DigitalCertificate
      attr_reader :subject, :issuer, :public_key, :valid_from, :valid_to, :signature
      
      def initialize(subject, issuer, public_key, valid_days = 365)
        @subject = subject
        @issuer = issuer
        @public_key = public_key
        @valid_from = Time.now
        @valid_to = @valid_from + (valid_days * 24 * 60 * 60)
        @signature = nil
      end
      
      def sign(private_key)
        certificate_data = to_json
        @signature = sign_data(certificate_data, private_key)
        self
      end
      
      def verify(public_key)
        return false unless @signature
        
        certificate_data = to_json
        verify_signature(certificate_data, @signature, public_key)
      end
      
      def is_valid?
        Time.now >= @valid_from && Time.now <= @valid_to
      end
      
      def to_json
        {
          subject: @subject,
          issuer: @issuer,
          public_key: @public_key,
          valid_from: @valid_from.iso8601,
          valid_to: @valid_to.iso8601
        }.to_json
      end
      
      private
      
      def sign_data(data, private_key)
        digest = Digest::SHA256.digest(data)
        signature = private_key.sign(OpenSSL::Digest::SHA256.new, digest)
        Base64.strict_encode64(signature)
      end
      
      def verify_signature(data, signature, public_key)
        digest = Digest::SHA256.digest(data)
        signature_data = Base64.strict_decode64(signature)
        public_key.verify(OpenSSL::Digest::SHA256.new, digest, signature_data)
      end
    end
    
    # Digital signature demonstration
    puts "\nDigital Signature Demonstration:"
    
    # Create signer
    signer = DigitalSignature.new(1024)
    
    # Sign a message
    message = "This is an important message that needs to be authenticated."
    puts "  Original message: #{message}"
    
    signature = signer.sign(message)
    puts "  Digital signature: #{signature[0..50]}..."
    
    # Verify signature
    public_key = signer.get_public_key
    is_valid = signer.verify(message, signature, public_key)
    puts "  Signature verification: #{is_valid ? 'Valid' : 'Invalid'}"
    
    # Tampered message verification
    tampered_message = "This is a tampered message."
    tampered_valid = signer.verify(tampered_message, signature, public_key)
    puts "  Tampered message verification: #{tampered_valid ? 'Valid' : 'Invalid'}"
    
    # Certificate demonstration
    puts "\nDigital Certificate Demonstration:"
    
    # Create certificate authority
    ca = DigitalSignature.new(1024)
    
    # Create certificate for user
    user = DigitalSignature.new(1024)
    user_public_key = user.get_public_key
    
    certificate = DigitalCertificate.new(
      "CN=Alice, O=Example Corp, C=US",
      "CN=CA, O=Example Corp, C=US",
      user_public_key,
      365
    )
    
    # CA signs the certificate
    certificate.sign(ca.get_private_key)
    
    puts "  Certificate created and signed by CA"
    puts "  Certificate subject: #{certificate.instance_variable_get(:@subject)}"
    puts "  Certificate issuer: #{certificate.instance_variable_get(:@issuer)}"
    puts "  Certificate valid: #{certificate.is_valid?}"
    
    # Verify certificate
    ca_public_key = ca.get_public_key
    cert_valid = certificate.verify(ca_public_key)
    puts "  Certificate verification: #{cert_valid ? 'Valid' : 'Invalid'}"
    
    # User signs message with their private key
    user_message = "Message signed by user certificate"
    user_signature = user.sign(user_message)
    
    # Verify with certificate's public key
    user_cert_public_key = OpenSSL::PKey::RSA.new(user_public_key)
    user_valid = OpenSSL::PKey::RSA.new(user_cert_public_key).verify(
      OpenSSL::Digest::SHA256.new,
      Digest::SHA256.digest(user_message),
      Base64.strict_decode64(user_signature)
    )
    puts "  User message verification: #{user_valid ? 'Valid' : 'Invalid'}"
    
    @examples << {
      title: "Digital Signatures",
      description: "Digital signatures and certificates implementation",
      code: <<~RUBY
        class DigitalSignature
          def sign(message)
            digest = Digest::SHA256.digest(message)
            signature = @key.sign(OpenSSL::Digest::SHA256.new, digest)
            Base64.strict_encode64(signature)
          end
          
          def verify(message, signature, public_key_pem)
            public_key = OpenSSL::PKey::RSA.new(public_key_pem)
            digest = Digest::SHA256.digest(message)
            signature_data = Base64.strict_decode64(signature)
            public_key.verify(OpenSSL::Digest::SHA256.new, digest, signature_data)
          end
        end
      RUBY
    }
    
    puts "\n✅ Digital Signatures example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Cybersecurity Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate cybersecurity concepts!"
  end
end

if __FILE__ == $0
  examples = CybersecurityExamples.new
  examples.start_examples
end
