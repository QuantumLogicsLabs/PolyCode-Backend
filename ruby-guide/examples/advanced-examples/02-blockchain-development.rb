# Blockchain Development Examples in Ruby
# Demonstrating blockchain concepts and implementations

require 'digest'
require 'json'
require 'time'

class BlockchainDevelopmentExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "⛓️ Blockchain Development Examples in Ruby"
    puts "========================================"
    puts "Explore blockchain concepts and implementations!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Blockchain Development Menu:"
      puts "1. Basic Blockchain"
      puts "2. Cryptographic Hashing"
      puts "3. Mining and Proof of Work"
      puts "4. Digital Signatures"
      puts "5. Smart Contracts"
      puts "6. Wallet Implementation"
      puts "7. Consensus Algorithms"
      puts "8. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-8): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        basic_blockchain
      when 2
        cryptographic_hashing
      when 3
        mining_proof_of_work
      when 4
        digital_signatures
      when 5
        smart_contracts
      when 6
        wallet_implementation
      when 7
        consensus_algorithms
      when 8
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def basic_blockchain
    puts "\n⛓️ Example 1: Basic Blockchain"
    puts "=" * 50
    puts "Implementing a simple blockchain structure."
    puts ""
    
    # Block class
    puts "🧱 Block Implementation:"
    
    class Block
      attr_reader :index, :timestamp, :data, :previous_hash, :hash, :nonce
      
      def initialize(index, data, previous_hash)
        @index = index
        @timestamp = Time.now
        @data = data
        @previous_hash = previous_hash
        @nonce = 0
        @hash = calculate_hash
      end
      
      def calculate_hash
        Digest::SHA256.hexdigest(
          "#{@index}#{@timestamp}#{@data}#{@previous_hash}#{@nonce}"
        )
      end
      
      def mine_block(difficulty)
        target = "0" * difficulty
        while @hash[0...difficulty] != target
          @nonce += 1
          @hash = calculate_hash
        end
        puts "Block mined: #{@hash}"
      end
      
      def to_s
        "Block #{@index}: #{@hash[0..15]}... (#{@data})"
      end
      
      def to_json
        {
          index: @index,
          timestamp: @timestamp.iso8601,
          data: @data,
          previous_hash: @previous_hash,
          hash: @hash,
          nonce: @nonce
        }.to_json
      end
    end
    
    # Blockchain class
    puts "\n⛓️ Blockchain Implementation:"
    
    class Blockchain
      attr_reader :chain, :difficulty
      
      def initialize(difficulty = 2)
        @chain = [create_genesis_block]
        @difficulty = difficulty
      end
      
      def create_genesis_block
        Block.new(0, "Genesis Block", "0")
      end
      
      def get_latest_block
        @chain[-1]
      end
      
      def add_block(data)
        latest_block = get_latest_block
        new_block = Block.new(@chain.length, data, latest_block.hash)
        new_block.mine_block(@difficulty)
        @chain << new_block
      end
      
      def is_chain_valid?
        @chain.each_with_index do |block, index|
          return false if block.hash != block.calculate_hash
          
          if index > 0
            previous_block = @chain[index - 1]
            return false if block.previous_hash != previous_block.hash
          end
        end
        true
      end
      
      def display_chain
        puts "Blockchain:"
        @chain.each { |block| puts "  #{block}" }
      end
      
      def to_json
        @chain.map(&:to_json).to_json
      end
    end
    
    # Blockchain demonstration
    puts "\nBlockchain Demonstration:"
    
    # Create blockchain
    blockchain = Blockchain.new(2)
    
    # Add blocks
    puts "Adding blocks to blockchain:"
    blockchain.add_block("First transaction")
    blockchain.add_block("Second transaction")
    blockchain.add_block("Third transaction")
    
    # Display blockchain
    blockchain.display_chain
    
    # Validate blockchain
    puts "\nBlockchain validation: #{blockchain.is_chain_valid? ? 'Valid' : 'Invalid'}"
    
    # Tamper with blockchain
    puts "\nTampering with blockchain:"
    blockchain.chain[1].data = "Tampered transaction"
    puts "After tampering validation: #{blockchain.is_chain_valid? ? 'Valid' : 'Invalid'}"
    
    @examples << {
      title: "Basic Blockchain",
      description: "Simple blockchain implementation with proof of work",
      code: <<~RUBY
        class Block
          def initialize(index, data, previous_hash)
            @index = index
            @data = data
            @previous_hash = previous_hash
            @hash = calculate_hash
          end
          
          def calculate_hash
            Digest::SHA256.hexdigest("\#{@index}\#{@data}\#{@previous_hash}")
          end
        end
      RUBY
    }
    
    puts "\n✅ Basic Blockchain example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def cryptographic_hashing
    puts "\n🔐 Example 2: Cryptographic Hashing"
    puts "=" * 50
    puts "Understanding cryptographic hash functions."
    puts ""
    
    # Hash function demonstrations
    puts "🔐 Hash Function Examples:"
    
    def sha256_hash(data)
      Digest::SHA256.hexdigest(data)
    end
    
    def sha256_file(filename)
      return nil unless File.exist?(filename)
      content = File.read(filename)
      Digest::SHA256.hexdigest(content)
    end
    
    def merkle_root(data_list)
      return "" if data_list.empty?
      return data_list.first if data_list.length == 1
      
      # Hash each item
      hashed_list = data_list.map { |item| sha256_hash(item) }
      
      # Build Merkle tree
      while hashed_list.length > 1
        new_level = []
        (0...hashed_list.length).step(2) do |i|
          left = hashed_list[i]
          right = hashed_list[i + 1] || hashed_list[i]
          new_level << sha256_hash(left + right)
        end
        hashed_list = new_level
      end
      
      hashed_list.first
    end
    
    # Hash examples
    puts "\nSHA-256 Hash Examples:"
    
    test_data = ["Hello World", "Ruby Blockchain", "Cryptography", "Hash Functions"]
    
    test_data.each do |data|
      hash = sha256_hash(data)
      puts "  '#{data}' -> #{hash}"
    end
    
    # Hash properties demonstration
    puts "\nHash Properties:"
    
    # Deterministic
    data = "Test data"
    hash1 = sha256_hash(data)
    hash2 = sha256_hash(data)
    puts "  Deterministic: #{hash1 == hash2 ? 'Yes' : 'No'}"
    
    # Avalanche effect
    data1 = "Hello"
    data2 = "hello"
    hash1 = sha256_hash(data1)
    hash2 = sha256_hash(data2)
    puts "  Avalanche effect:"
    puts "    '#{data1}' -> #{hash1}"
    puts "    '#{data2}' -> #{hash2}"
    
    # Merkle tree
    puts "\nMerkle Tree:"
    
    transactions = [
      "Alice pays Bob 10 BTC",
      "Bob pays Charlie 5 BTC",
      "Charlie pays Dave 3 BTC",
      "Dave pays Eve 2 BTC"
    ]
    
    merkle_root = merkle_root(transactions)
    puts "  Transactions: #{transactions.length}"
    puts "  Merkle root: #{merkle_root}"
    
    # Hash chaining
    puts "\nHash Chaining:"
    
    def hash_chain(data_list)
      chain = []
      current_hash = "0"
      
      data_list.each do |data|
        current_hash = sha256_hash(current_hash + data)
        chain << current_hash
      end
      
      chain
    end
    
    chain = hash_chain(["Block 1", "Block 2", "Block 3"])
    puts "  Hash chain: #{chain.map { |h| h[0..15] }}"
    
    @examples << {
      title: "Cryptographic Hashing",
      description: "SHA-256 hashing and Merkle tree implementation",
      code: <<~RUBY
        def sha256_hash(data)
          Digest::SHA256.hexdigest(data)
        end
        
        def merkle_root(data_list)
          hashed_list = data_list.map { |item| sha256_hash(item) }
          # Build Merkle tree recursively
        end
      RUBY
    }
    
    puts "\n✅ Cryptographic Hashing example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def mining_proof_of_work
    puts "\n⛏️ Example 3: Mining and Proof of Work"
    puts "=" * 50
    puts "Implementing mining algorithms and proof of work."
    puts ""
    
    # Mining implementation
    puts "⛏️ Mining Implementation:"
    
    class Miner
      def initialize(difficulty = 3)
        @difficulty = difficulty
        @target = "0" * @difficulty
      end
      
      def mine_block(data, previous_hash)
        nonce = 0
        timestamp = Time.now
        
        loop do
          block_data = "#{data}#{previous_hash}#{timestamp}#{nonce}"
          hash = Digest::SHA256.hexdigest(block_data)
          
          if hash[0...@difficulty] == @target
            return {
              hash: hash,
              nonce: nonce,
              timestamp: timestamp,
              difficulty: @difficulty
            }
          end
          
          nonce += 1
        end
      end
      
      def verify_mining(result, data, previous_hash)
        block_data = "#{data}#{previous_hash}#{result[:timestamp]}#{result[:nonce]}"
        hash = Digest::SHA256.hexdigest(block_data)
        hash[0...@difficulty] == @target && hash == result[:hash]
      end
    end
    
    # Mining pool
    puts "\n🏊 Mining Pool:"
    
    class MiningPool
      def initialize(difficulty = 3)
        @difficulty = difficulty
        @target = "0" * @difficulty
        @workers = []
        @found_block = nil
      end
      
      def add_worker(worker_id)
        worker = Thread.new do
          mine_for_pool(worker_id)
        end
        @workers << worker
      end
      
      def start_mining(data, previous_hash)
        @data = data
        @previous_hash = previous_hash
        @found_block = nil
        
        # Start workers
        4.times { |i| add_worker(i) }
        
        # Wait for block to be found
        sleep(0.1) until @found_block
        
        # Stop workers
        @workers.each(&:kill)
        @workers.clear
        
        @found_block
      end
      
      private
      
      def mine_for_pool(worker_id)
        nonce = rand(1000)
        
        loop do
          break if @found_block
          
          block_data = "#{@data}#{@previous_hash}#{Time.now}#{nonce}"
          hash = Digest::SHA256.hexdigest(block_data)
          
          if hash[0...@difficulty] == @target
            @found_block = {
              hash: hash,
              nonce: nonce,
              worker: worker_id,
              timestamp: Time.now
            }
            break
          end
          
          nonce += 1
        end
      end
    end
    
    # Mining demonstrations
    puts "\nMining Demonstrations:"
    
    # Solo mining
    puts "\nSolo Mining:"
    miner = Miner.new(3)
    
    start_time = Time.now
    result = miner.mine_block("Test transaction", "0000000000000000000000000000000000000000000000000000000000000000")
    end_time = Time.now
    
    puts "  Mining time: #{(end_time - start_time).round(2)} seconds"
    puts "  Found hash: #{result[:hash]}"
    puts "  Nonce: #{result[:nonce]}"
    puts "  Difficulty: #{result[:difficulty]}"
    
    # Verify mining
    is_valid = miner.verify_mining(result, "Test transaction", "0000000000000000000000000000000000000000000000000000000000000000")
    puts "  Verification: #{is_valid ? 'Valid' : 'Invalid'}"
    
    # Mining pool
    puts "\nMining Pool:"
    pool = MiningPool.new(3)
    
    start_time = Time.now
    pool_result = pool.start_mining("Pool transaction", "0000000000000000000000000000000000000000000000000000000000000000")
    end_time = Time.now
    
    puts "  Pool mining time: #{(end_time - start_time).round(2)} seconds"
    puts "  Found hash: #{pool_result[:hash]}"
    puts "  Worker: #{pool_result[:worker]}"
    puts "  Nonce: #{pool_result[:nonce]}"
    
    # Difficulty adjustment
    puts "\nDifficulty Adjustment:"
    
    difficulties = [1, 2, 3, 4]
    difficulties.each do |diff|
      miner = Miner.new(diff)
      start_time = Time.now
      result = miner.mine_block("Diff test", "0" * 64)
      end_time = Time.now
      mining_time = (end_time - start_time).round(2)
      
      puts "  Difficulty #{diff}: #{mining_time}s (nonce: #{result[:nonce]})"
    end
    
    @examples << {
      title: "Mining and Proof of Work",
      description: "Mining algorithms and proof of work implementation",
      code: <<~RUBY
        class Miner
          def mine_block(data, previous_hash)
            nonce = 0
            loop do
              block_data = "\#{data}\#{previous_hash}\#{nonce}"
              hash = Digest::SHA256.hexdigest(block_data)
              return hash if hash.start_with?("000")
              nonce += 1
            end
          end
        end
      RUBY
    }
    
    puts "\n✅ Mining and Proof of Work example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Blockchain Development Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate blockchain development concepts!"
  end
end

if __FILE__ == $0
  examples = BlockchainDevelopmentExamples.new
  examples.start_examples
end
