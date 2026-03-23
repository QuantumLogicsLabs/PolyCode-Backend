# Blockchain Development in Ruby

## Overview

This comprehensive guide explores blockchain technology implementation using Ruby. While Ruby may not be the traditional choice for blockchain development, it provides excellent capabilities for understanding blockchain concepts, building prototypes, and creating educational implementations.

## Blockchain Fundamentals

### Block Structure

```ruby
class Block
  attr_reader :index, :timestamp, :data, :previous_hash, :hash, :nonce
  
  def initialize(index, data, previous_hash = nil)
    @index = index
    @timestamp = Time.now
    @data = data
    @previous_hash = previous_hash
    @nonce = 0
    @hash = calculate_hash
  end
  
  def calculate_hash
    header = "#{@index}#{@timestamp}#{@data}#{@previous_hash}#{@nonce}"
    Digest::SHA256.hexdigest(header)
  end
  
  def mine_block(difficulty)
    target = "0" * difficulty
    
    while @hash[0...difficulty] != target
      @nonce += 1
      @hash = calculate_hash
    end
    
    puts "Block mined: #{@hash}"
  end
  
  def to_json
    {
      index: @index,
      timestamp: @timestamp.to_s,
      data: @data,
      previous_hash: @previous_hash,
      hash: @hash,
      nonce: @nonce
    }
  end
  
  def self.from_json(json_data)
    block = new(
      json_data['index'],
      json_data['data'],
      json_data['previous_hash']
    )
    block.instance_variable_set(:@timestamp, Time.parse(json_data['timestamp']))
    block.instance_variable_set(:@hash, json_data['hash'])
    block.instance_variable_set(:@nonce, json_data['nonce'])
    block
  end
  
  def valid?
    @hash == calculate_hash
  end
end
```

### Blockchain Implementation

```ruby
class Blockchain
  attr_reader :chain, :difficulty, :pending_transactions, :mining_reward
  
  def initialize(difficulty = 4)
    @chain = [create_genesis_block]
    @difficulty = difficulty
    @pending_transactions = []
    @mining_reward = 10.0
    @wallets = {}
  end
  
  def create_genesis_block
    Block.new(0, "Genesis Block", "0")
  end
  
  def get_latest_block
    @chain.last
  end
  
  def add_transaction(transaction)
    # Validate transaction
    return false unless transaction.valid?
    
    # Check sender balance
    sender_balance = get_balance(transaction.sender)
    return false if sender_balance < transaction.amount + transaction.fee
    
    @pending_transactions << transaction
    true
  end
  
  def mine_pending_transactions(mining_reward_address)
    # Create reward transaction
    reward_transaction = Transaction.new(
      nil,
      mining_reward_address,
      @mining_reward,
      0.0
    )
    @pending_transactions.unshift(reward_transaction)
    
    # Create new block
    block = Block.new(
      @chain.length,
      @pending_transactions.dup,
      get_latest_block.hash
    )
    
    # Mine block
    block.mine_block(@difficulty)
    
    # Add block to chain
    @chain << block
    
    # Clear pending transactions
    @pending_transactions.clear
    
    puts "Block successfully mined!"
    block
  end
  
  def get_balance(address)
    balance = 0.0
    
    @chain.each do |block|
      next unless block.data.is_a?(Array)
      
      block.data.each do |transaction|
        next unless transaction.is_a?(Transaction)
        
        if transaction.sender == address
          balance -= transaction.amount + transaction.fee
        end
        
        if transaction.recipient == address
          balance += transaction.amount
        end
      end
    end
    
    balance
  end
  
  def get_transaction_history(address)
    history = []
    
    @chain.each do |block|
      next unless block.data.is_a?(Array)
      
      block.data.each do |transaction|
        next unless transaction.is_a?(Transaction)
        
        if transaction.sender == address || transaction.recipient == address
          history << {
            block_index: block.index,
            timestamp: block.timestamp,
            transaction: transaction
          }
        end
      end
    end
    
    history
  end
  
  def is_chain_valid?
    @chain.each_with_index do |block, i|
      next if i == 0  # Skip genesis block
      
      previous_block = @chain[i - 1]
      
      # Check if current block's previous hash matches
      return false if block.previous_hash != previous_block.hash
      
      # Check if block's hash is valid
      return false unless block.valid?
      
      # Check if transactions are valid
      if block.data.is_a?(Array)
        block.data.each do |transaction|
          return false unless transaction.valid?
        end
      end
    end
    
    true
  end
  
  def get_block_by_hash(hash)
    @chain.find { |block| block.hash == hash }
  end
  
  def get_block_by_index(index)
    @chain[index]
  end
  
  def get_transaction_by_id(transaction_id)
    @chain.each do |block|
      next unless block.data.is_a?(Array)
      
      block.data.each do |transaction|
        next unless transaction.is_a?(Transaction)
        return transaction if transaction.id == transaction_id
      end
    end
    
    nil
  end
  
  def get_chain_stats
    {
      total_blocks: @chain.length,
      total_transactions: @chain.sum { |block| block.data.is_a?(Array) ? block.data.length : 0 },
      difficulty: @difficulty,
      pending_transactions: @pending_transactions.length,
      latest_block_hash: get_latest_block.hash,
      chain_valid: is_chain_valid?
    }
  end
  
  def to_json
    {
      chain: @chain.map(&:to_json),
      difficulty: @difficulty,
      pending_transactions: @pending_transactions.map(&:to_json),
      mining_reward: @mining_reward
    }
  end
  
  def self.from_json(json_data)
    blockchain = new(json_data['difficulty'])
    blockchain.instance_variable_set(:@chain, 
      json_data['chain'].map { |block_json| Block.from_json(block_json) })
    blockchain.instance_variable_set(:@pending_transactions,
      json_data['pending_transactions'].map { |tx_json| Transaction.from_json(tx_json) })
    blockchain.instance_variable_set(:@mining_reward, json_data['mining_reward'])
    blockchain
  end
end
```

### Transaction System

```ruby
class Transaction
  attr_reader :id, :sender, :recipient, :amount, :fee, :timestamp, :signature
  
  def initialize(sender, recipient, amount, fee = 0.01)
    @id = SecureRandom.uuid
    @sender = sender
    @recipient = recipient
    @amount = amount.to_f
    @fee = fee.to_f
    @timestamp = Time.now
    @signature = nil
  end
  
  def calculate_hash
    transaction_data = "#{@sender}#{@recipient}#{@amount}#{@fee}#{@timestamp}"
    Digest::SHA256.hexdigest(transaction_data)
  end
  
  def sign_transaction(private_key)
    return false if @sender.nil?  # Mining reward transaction
    
    transaction_hash = calculate_hash
    @signature = sign_data(transaction_hash, private_key)
  end
  
  def valid?
    # Mining reward transaction is always valid
    return true if @sender.nil?
    
    return false if @signature.nil?
    
    transaction_hash = calculate_hash
    verify_signature(transaction_hash, @signature, @sender)
  end
  
  def to_json
    {
      id: @id,
      sender: @sender,
      recipient: @recipient,
      amount: @amount,
      fee: @fee,
      timestamp: @timestamp.to_s,
      signature: @signature
    }
  end
  
  def self.from_json(json_data)
    transaction = new(
      json_data['sender'],
      json_data['recipient'],
      json_data['amount'],
      json_data['fee']
    )
    transaction.instance_variable_set(:@id, json_data['id'])
    transaction.instance_variable_set(:@timestamp, Time.parse(json_data['timestamp']))
    transaction.instance_variable_set(:@signature, json_data['signature'])
    transaction
  end
  
  private
  
  def sign_data(data, private_key)
    # Simplified signing - in practice, use proper cryptographic libraries
    "#{data}_signed_with_#{private_key}"
  end
  
  def verify_signature(data, signature, public_key)
    # Simplified verification - in practice, use proper cryptographic libraries
    signature.include?(data) && signature.include?(public_key)
  end
end

class Wallet
  attr_reader :address, :private_key, :public_key
  
  def initialize
    @private_key = generate_private_key
    @public_key = generate_public_key(@private_key)
    @address = generate_address(@public_key)
  end
  
  def sign_transaction(transaction)
    transaction.sign_transaction(@private_key)
  end
  
  def get_balance(blockchain)
    blockchain.get_balance(@address)
  end
  
  def send_funds(blockchain, recipient_address, amount)
    fee = 0.01
    transaction = Transaction.new(@address, recipient_address, amount, fee)
    sign_transaction(transaction)
    
    if blockchain.add_transaction(transaction)
      puts "Transaction sent successfully!"
      transaction
    else
      puts "Failed to send transaction"
      nil
    end
  end
  
  def to_json
    {
      address: @address,
      public_key: @public_key,
      private_key: @private_key  # In practice, never expose private key
    }
  end
  
  private
  
  def generate_private_key
    # Simplified private key generation
    SecureRandom.hex(32)
  end
  
  def generate_public_key(private_key)
    # Simplified public key generation
    "public_#{private_key}"
  end
  
  def generate_address(public_key)
    # Simplified address generation
    Digest::SHA256.hexdigest(public_key)[0...40]
  end
end
```

### Smart Contract System

```ruby
class SmartContract
  attr_reader :address, :code, :storage, :owner, :balance
  
  def initialize(code, owner_address)
    @address = generate_contract_address
    @code = code
    @storage = {}
    @owner = owner_address
    @balance = 0.0
    @state = :deployed
  end
  
  def execute(method_name, *args, sender: nil, value: 0.0)
    return { success: false, error: "Contract not deployed" } unless @state == :deployed
    
    # Add funds to contract
    @balance += value if value > 0
    
    # Execute method
    begin
      result = instance_eval(@code)
      
      case method_name
      when :get_balance
        { success: true, result: @balance }
      when :get_storage
        key = args.first
        { success: true, result: @storage[key] }
      when :set_storage
        key, value = args
        @storage[key] = value
        { success: true, result: "Storage updated" }
      when :transfer
        recipient, amount = args
        if @balance >= amount
          @balance -= amount
          { success: true, result: "Transfer initiated", amount: amount, recipient: recipient }
        else
          { success: false, error: "Insufficient balance" }
        end
      when :self_destruct
        if sender == @owner
          @state = :destroyed
          { success: true, result: "Contract destroyed", refund_balance: @balance }
        else
          { success: false, error: "Only owner can destroy contract" }
        end
      else
        { success: false, error: "Unknown method" }
      end
    rescue => e
      { success: false, error: e.message }
    end
  end
  
  def deploy
    @state = :deployed
  end
  
  def destroy
    @state = :destroyed
  end
  
  def to_json
    {
      address: @address,
      code: @code,
      storage: @storage,
      owner: @owner,
      balance: @balance,
      state: @state
    }
  end
  
  private
  
  def generate_contract_address
    "contract_#{SecureRandom.hex(20)}"
  end
end

class ContractManager
  def initialize(blockchain)
    @blockchain = blockchain
    @contracts = {}
  end
  
  def deploy_contract(code, owner_address)
    contract = SmartContract.new(code, owner_address)
    @contracts[contract.address] = contract
    
    # Create deployment transaction
    deployment_tx = Transaction.new(
      owner_address,
      contract.address,
      0.0,
      0.01
    )
    
    @blockchain.add_transaction(deployment_tx)
    contract.deploy
    
    puts "Contract deployed at address: #{contract.address}"
    contract
  end
  
  def execute_contract(contract_address, method_name, *args, sender: nil, value: 0.0)
    contract = @contracts[contract_address]
    return { success: false, error: "Contract not found" } unless contract
    
    result = contract.execute(method_name, *args, sender: sender, value: value)
    
    # Create execution transaction if successful
    if result[:success] && value > 0
      execution_tx = Transaction.new(
        sender,
        contract_address,
        value,
        0.01
      )
      @blockchain.add_transaction(execution_tx)
    end
    
    result
  end
  
  def get_contract(contract_address)
    @contracts[contract_address]
  end
  
  def get_all_contracts
    @contracts.values
  end
  
  def get_contract_storage(contract_address, key = nil)
    contract = @contracts[contract_address]
    return nil unless contract
    
    if key
      contract.storage[key]
    else
      contract.storage
    end
  end
end
```

### Consensus Algorithms

```ruby
class ConsensusAlgorithm
  def initialize(blockchain)
    @blockchain = blockchain
  end
  
  def validate_block(block)
    # Basic validation
    return false unless block.valid?
    return false unless block.previous_hash == @blockchain.get_latest_block.hash
    
    # Validate transactions
    if block.data.is_a?(Array)
      block.data.each do |transaction|
        return false unless transaction.valid?
      end
    end
    
    true
  end
  
  def select_validator(validators)
    raise NotImplementedError, "Subclasses must implement select_validator"
  end
end

class ProofOfWork < ConsensusAlgorithm
  def initialize(blockchain, difficulty = 4)
    super(blockchain)
    @difficulty = difficulty
  end
  
  def mine_block(miner_address)
    block = Block.new(
      @blockchain.chain.length,
      @blockchain.pending_transactions.dup,
      @blockchain.get_latest_block.hash
    )
    
    block.mine_block(@difficulty)
    
    if validate_block(block)
      @blockchain.chain << block
      @blockchain.pending_transactions.clear
      
      # Add mining reward
      reward_transaction = Transaction.new(nil, miner_address, @blockchain.mining_reward, 0.0)
      @blockchain.pending_transactions << reward_transaction
      
      puts "Block successfully mined by #{miner_address}"
      block
    else
      puts "Failed to validate mined block"
      nil
    end
  end
  
  def select_validator(validators)
    # In PoW, validator is determined by who solves the puzzle first
    validators.first
  end
end

class ProofOfStake < ConsensusAlgorithm
  def initialize(blockchain)
    super(blockchain)
    @validators = {}
    @stake_pool = {}
  end
  
  def register_validator(address, stake)
    @validators[address] = stake
    @stake_pool[address] = stake
  end
  
  def select_validator(validators)
    # Select validator based on stake weight
    total_stake = @stake_pool.values.sum
    random_value = rand * total_stake
    
    cumulative_stake = 0
    @stake_pool.each do |address, stake|
      cumulative_stake += stake
      return address if random_value <= cumulative_stake
    end
    
    validators.first
  end
  
  def validate_block(block)
    super(block) && @validators.keys.include?(block.miner)
  end
  
  def add_stake(address, amount)
    @stake_pool[address] = (@stake_pool[address] || 0) + amount
  end
  
  def remove_stake(address, amount)
    if @stake_pool[address] && @stake_pool[address] >= amount
      @stake_pool[address] -= amount
      true
    else
      false
    end
  end
  
  def get_validator_stake(address)
    @stake_pool[address] || 0
  end
  
  def get_total_stake
    @stake_pool.values.sum
  end
end

class DelegatedProofOfStake < ProofOfStake
  def initialize(blockchain)
    super(blockchain)
    @delegates = []
    @votes = {}
  end
  
  def register_delegate(address)
    @delegates << address unless @delegates.include?(address)
  end
  
  def vote_for_delegate(voter_address, delegate_address)
    return false unless @delegates.include?(delegate_address)
    
    @votes[voter_address] = delegate_address
    true
  end
  
  def select_validator(validators)
    # Select from top delegates by vote weight
    delegate_votes = Hash.new(0)
    
    @votes.each do |voter, delegate|
      voter_stake = get_validator_stake(voter)
      delegate_votes[delegate] += voter_stake
    end
    
    # Select delegate with highest vote weight
    top_delegate = delegate_votes.max_by { |_, votes| votes }&.first
    top_delegate || @delegates.first
  end
  
  def get_delegate_votes
    delegate_votes = Hash.new(0)
    
    @votes.each do |voter, delegate|
      voter_stake = get_validator_stake(voter)
      delegate_votes[delegate] += voter_stake
    end
    
    delegate_votes
  end
end
```

### Network Layer

```ruby
class NetworkNode
  attr_reader :id, :address, :port, :blockchain, :peers
  
  def initialize(address, port)
    @id = SecureRandom.uuid
    @address = address
    @port = port
    @blockchain = Blockchain.new
    @peers = []
    @server = nil
    @running = false
  end
  
  def start_server
    @server = TCPServer.new(@address, @port)
    @running = true
    
    puts "Node #{@id} listening on #{@address}:#{@port}"
    
    Thread.new do
      while @running
        begin
          client = @server.accept
          handle_client(client)
        rescue => e
          puts "Error handling client: #{e.message}"
        end
      end
    end
  end
  
  def stop_server
    @running = false
    @server&.close
  end
  
  def connect_to_peer(peer_address, peer_port)
    return false if peer_connected?(peer_address, peer_port)
    
    begin
      socket = TCPSocket.new(peer_address, peer_port)
      
      # Send handshake
      handshake = {
        type: 'handshake',
        node_id: @id,
        address: @address,
        port: @port
      }
      
      socket.puts(JSON.generate(handshake))
      
      # Wait for response
      response = JSON.parse(socket.gets)
      
      if response['type'] == 'handshake_ack'
        @peers << {
          id: response['node_id'],
          address: peer_address,
          port: peer_port,
          socket: socket
        }
        
        puts "Connected to peer #{response['node_id']}"
        return true
      end
      
      socket.close
    rescue => e
      puts "Failed to connect to peer: #{e.message}"
    end
    
    false
  end
  
  def broadcast_transaction(transaction)
    message = {
      type: 'transaction',
      data: transaction.to_json
    }
    
    broadcast(message)
  end
  
  def broadcast_block(block)
    message = {
      type: 'block',
      data: block.to_json
    }
    
    broadcast(message)
  end
  
  def request_blockchain
    message = {
      type: 'blockchain_request',
      node_id: @id
    }
    
    broadcast(message)
  end
  
  def get_network_stats
    {
      node_id: @id,
      address: "#{@address}:#{@port}",
      peers: @peers.length,
      blockchain_length: @blockchain.chain.length,
      blockchain_valid: @blockchain.is_chain_valid?
    }
  end
  
  private
  
  def handle_client(client)
    Thread.new do
      begin
        while (line = client.gets)
          message = JSON.parse(line)
          handle_message(message, client)
        end
      rescue => e
        puts "Error handling message: #{e.message}"
      ensure
        client.close
      end
    end
  end
  
  def handle_message(message, client)
    case message['type']
    when 'handshake'
      handle_handshake(message, client)
    when 'transaction'
      handle_transaction(message)
    when 'block'
      handle_block(message)
    when 'blockchain_request'
      handle_blockchain_request(message)
    when 'blockchain_response'
      handle_blockchain_response(message)
    end
  end
  
  def handle_handshake(message, client)
    response = {
      type: 'handshake_ack',
      node_id: @id,
      address: @address,
      port: @port
    }
    
    client.puts(JSON.generate(response))
    
    @peers << {
      id: message['node_id'],
      address: message['address'],
      port: message['port'],
      socket: client
    }
    
    puts "Peer connected: #{message['node_id']}"
  end
  
  def handle_transaction(message)
    transaction = Transaction.from_json(message['data'])
    
    if @blockchain.add_transaction(transaction)
      # Relay to other peers
      relay_message(message, nil)
    end
  end
  
  def handle_block(message)
    block = Block.from_json(message['data'])
    
    # Validate block
    latest_block = @blockchain.get_latest_block
    
    if block.previous_hash == latest_block.hash && block.valid?
      @blockchain.chain << block
      
      # Relay to other peers
      relay_message(message, nil)
      
      puts "New block added: #{block.hash}"
    end
  end
  
  def handle_blockchain_request(message)
    response = {
      type: 'blockchain_response',
      node_id: @id,
      data: @blockchain.to_json
    }
    
    # Send to requesting peer
    peer = @peers.find { |p| p[:id] == message['node_id'] }
    peer[:socket].puts(JSON.generate(response)) if peer
  end
  
  def handle_blockchain_response(message)
    received_blockchain = Blockchain.from_json(message['data'])
    
    # Validate received blockchain
    if received_blockchain.is_chain_valid? && 
       received_blockchain.chain.length > @blockchain.chain.length
      
      @blockchain = received_blockchain
      puts "Blockchain updated from peer #{message['node_id']}"
    end
  end
  
  def broadcast(message)
    @peers.each do |peer|
      begin
        peer[:socket].puts(JSON.generate(message))
      rescue => e
        puts "Failed to send message to peer #{peer[:id]}: #{e.message}"
        remove_peer(peer[:id])
      end
    end
  end
  
  def relay_message(message, exclude_peer_id)
    @peers.each do |peer|
      next if peer[:id] == exclude_peer_id
      
      begin
        peer[:socket].puts(JSON.generate(message))
      rescue => e
        puts "Failed to relay message to peer #{peer[:id]}: #{e.message}"
        remove_peer(peer[:id])
      end
    end
  end
  
  def peer_connected?(address, port)
    @peers.any? { |peer| peer[:address] == address && peer[:port] == port }
  end
  
  def remove_peer(peer_id)
    @peers.reject! { |peer| peer[:id] == peer_id }
  end
end

class NetworkManager
  def initialize
    @nodes = {}
    @discovery_enabled = true
  end
  
  def create_node(address, port)
    node = NetworkNode.new(address, port)
    @nodes[node.id] = node
    node
  end
  
  def start_network
    @nodes.each do |id, node|
      node.start_server
    end
    
    # Connect nodes to each other
    connect_all_nodes
    
    puts "Network started with #{@nodes.length} nodes"
  end
  
  def stop_network
    @nodes.each do |id, node|
      node.stop_server
    end
    
    puts "Network stopped"
  end
  
  def get_network_stats
    total_peers = @nodes.values.sum { |node| node.peers.length }
    
    {
      total_nodes: @nodes.length,
      total_connections: total_peers,
      average_connections: total_peers.to_f / @nodes.length,
      network_health: calculate_network_health
    }
  end
  
  def simulate_transaction(sender_address, recipient_address, amount)
    # Pick random node to initiate transaction
    node = @nodes.values.sample
    
    wallet = Wallet.new
    transaction = Transaction.new(wallet.address, recipient_address, amount, 0.01)
    wallet.sign_transaction(transaction)
    
    node.broadcast_transaction(transaction)
    
    puts "Transaction broadcast: #{transaction.id}"
    transaction
  end
  
  def simulate_mining(miner_address)
    # Pick random node to mine
    node = @nodes.values.sample
    
    block = node.blockchain.mine_pending_transactions(miner_address)
    node.broadcast_block(block) if block
    
    puts "Block mined and broadcast: #{block.hash}" if block
    block
  end
  
  private
  
  def connect_all_nodes
    node_list = @nodes.values
    
    node_list.each_with_index do |node1, i|
      node_list.each_with_index do |node2, j|
        next if i == j
        
        # Connect nodes with some probability to create realistic network topology
        if rand < 0.7  # 70% chance of connection
          node1.connect_to_peer(node2.address, node2.port)
        end
      end
    end
  end
  
  def calculate_network_health
    return 0.0 if @nodes.empty?
    
    # Calculate network health based on connectivity
    connected_nodes = @nodes.values.count { |node| node.peers.length > 0 }
    connectivity_ratio = connected_nodes.to_f / @nodes.length
    
    # Check blockchain consistency
    blockchain_hashes = @nodes.values.map { |node| node.blockchain.get_latest_block.hash }
    consistency = blockchain_hashes.uniq.length == 1 ? 1.0 : 0.5
    
    (connectivity_ratio + consistency) / 2.0
  end
end
```

### Advanced Features

```ruby
class Token
  attr_reader :name, :symbol, :total_supply, :decimals, :balances
  
  def initialize(name, symbol, total_supply, decimals = 18)
    @name = name
    @symbol = symbol
    @total_supply = total_supply
    @decimals = decimals
    @balances = {}
    @allowances = {}
  end
  
  def mint(address, amount)
    @balances[address] = (@balances[address] || 0) + amount
    @total_supply += amount
  end
  
  def burn(address, amount)
    return false unless @balances[address] >= amount
    
    @balances[address] -= amount
    @total_supply -= amount
    true
  end
  
  def transfer(from, to, amount)
    return false unless @balances[from] >= amount
    
    @balances[from] -= amount
    @balances[to] = (@balances[to] || 0) + amount
    true
  end
  
  def approve(owner, spender, amount)
    @allowances[owner] ||= {}
    @allowances[owner][spender] = amount
  end
  
  def transfer_from(owner, spender, to, amount)
    allowance = @allowances.dig(owner, spender) || 0
    return false unless allowance >= amount && @balances[owner] >= amount
    
    @balances[owner] -= amount
    @balances[to] = (@balances[to] || 0) + amount
    @allowances[owner][spender] = allowance - amount
    true
  end
  
  def balance_of(address)
    @balances[address] || 0
  end
  
  def allowance(owner, spender)
    @allowances.dig(owner, spender) || 0
  end
end

class DecentralizedExchange
  def initialize
    @pairs = {}
    @orders = []
    @trades = []
  end
  
  def create_pair(token_a, token_b)
    pair_id = "#{token_a.symbol}-#{token_b.symbol}"
    
    @pairs[pair_id] = {
      token_a: token_a,
      token_b: token_b,
      reserve_a: 0,
      reserve_b: 0,
      total_liquidity: 0
    }
    
    pair_id
  end
  
  def add_liquidity(pair_id, amount_a, amount_b, provider)
    pair = @pairs[pair_id]
    return false unless pair
    
    # Calculate liquidity tokens
    if pair[:total_liquidity] == 0
      liquidity = Math.sqrt(amount_a * amount_b)
    else
      liquidity = [
        amount_a * pair[:total_liquidity] / pair[:reserve_a],
        amount_b * pair[:total_liquidity] / pair[:reserve_b]
      ].min
    end
    
    # Transfer tokens to pair
    pair[:token_a].transfer(provider, pair_id, amount_a)
    pair[:token_b].transfer(provider, pair_id, amount_b)
    
    # Update reserves and liquidity
    pair[:reserve_a] += amount_a
    pair[:reserve_b] += amount_b
    pair[:total_liquidity] += liquidity
    
    liquidity
  end
  
  def swap(pair_id, token_in, amount_in, amount_out_min, recipient)
    pair = @pairs[pair_id]
    return false unless pair
    
    # Calculate output amount (including 0.3% fee)
    amount_in_with_fee = amount_in * 0.997
    amount_out = amount_in_with_fee * pair[:reserve_b] / (pair[:reserve_a] + amount_in_with_fee)
    
    return false if amount_out < amount_out_min
    
    # Perform swap
    if token_in == pair[:token_a].symbol
      pair[:token_a].transfer(pair_id, recipient, amount_in)
      pair[:token_b].transfer(pair_id, recipient, amount_out)
      
      pair[:reserve_a] += amount_in
      pair[:reserve_b] -= amount_out
    else
      pair[:token_b].transfer(pair_id, recipient, amount_in)
      pair[:token_a].transfer(pair_id, recipient, amount_out)
      
      pair[:reserve_b] += amount_in
      pair[:reserve_a] -= amount_out
    end
    
    # Record trade
    @trades << {
      pair_id: pair_id,
      token_in: token_in,
      amount_in: amount_in,
      amount_out: amount_out,
      recipient: recipient,
      timestamp: Time.now
    }
    
    amount_out
  end
  
  def get_pair_reserves(pair_id)
    pair = @pairs[pair_id]
    return nil unless pair
    
    {
      reserve_a: pair[:reserve_a],
      reserve_b: pair[:reserve_b],
      total_liquidity: pair[:total_liquidity]
    }
  end
  
  def get_price(pair_id, token_in)
    pair = @pairs[pair_id]
    return nil unless pair
    
    if token_in == pair[:token_a].symbol
      pair[:reserve_b] / pair[:reserve_a]
    else
      pair[:reserve_a] / pair[:reserve_b]
    end
  end
  
  def get_trade_history(limit = 100)
    @trades.last(limit)
  end
end

class Oracle
  def initialize
    @data_sources = {}
    @price_feeds = {}
    @last_update = {}
  end
  
  def register_data_source(name, url)
    @data_sources[name] = url
  end
  
  def update_price_feed(asset, price)
    @price_feeds[asset] = price
    @last_update[asset] = Time.now
  end
  
  def get_price(asset)
    @price_feeds[asset]
  end
  
  def get_price_history(asset, period = 24.hours)
    # In practice, this would fetch from database
    []
  end
  
  def verify_data_integrity(asset, expected_price, tolerance = 0.01)
    current_price = get_price(asset)
    return false unless current_price
    
    difference = (current_price - expected_price).abs / expected_price
    difference <= tolerance
  end
  
  def aggregate_prices(asset, sources)
    prices = sources.map { |source| fetch_price_from_source(asset, source) }.compact
    return nil if prices.empty?
    
    # Remove outliers (simple implementation)
    sorted_prices = prices.sort
    middle_prices = sorted_prices[1..-2] if sorted_prices.length > 2
    middle_prices ||= sorted_prices
    
    # Calculate average
    middle_prices.sum / middle_prices.length
  end
  
  private
  
  def fetch_price_from_source(asset, source)
    # In practice, this would make HTTP requests
    case source
    when 'coinbase'
      rand(1000.0..2000.0)  # Mock price
    when 'binance'
      rand(1000.0..2000.0)  # Mock price
    when 'kraken'
      rand(1000.0..2000.0)  # Mock price
    else
      nil
    end
  end
end
```

## Practice Exercises

### Exercise 1: Complete Blockchain Implementation
Build a production-ready blockchain with:
- Advanced consensus algorithms
- Smart contract execution
- Token standards (ERC-20 equivalent)
- Decentralized exchange

### Exercise 2: Network Simulation
Create a comprehensive network simulator:
- Multiple consensus algorithms
- Network topology visualization
- Performance metrics
- Attack simulation

### Exercise 3: DeFi Platform
Build a complete DeFi platform:
- Lending and borrowing
- Yield farming
- Liquidity pools
- Governance tokens

### Exercise 4: Blockchain Analysis Tools
Create blockchain analysis tools:
- Transaction pattern analysis
- Network topology analysis
- Performance monitoring
- Security audit tools

---

**Ready to build the future of decentralized applications? Let's dive into blockchain development! ⛓**
