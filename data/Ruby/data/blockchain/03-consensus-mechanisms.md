# Consensus Mechanisms in Ruby
# Comprehensive guide to blockchain consensus algorithms and implementations

## 🤝 Consensus Fundamentals

### 1. Consensus Concepts

Core consensus mechanism concepts:

```ruby
class ConsensusFundamentals
  def self.explain_consensus_concepts
    puts "Consensus Mechanisms Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Consensus",
        description: "Agreement among distributed nodes on system state",
        importance: "Ensures all nodes have same view of blockchain",
        challenges: ["Byzantine faults", "Network delays", "Sybil attacks"]
      },
      {
        concept: "Byzantine Fault Tolerance",
        description: "System tolerates up to 1/3 malicious nodes",
        requirement: "Consensus despite some nodes being malicious",
        examples: ["PBFT", "Tendermint", "HoneyBadgerBFT"]
      },
      {
        concept: "Sybil Attack",
        description: "Attacker creates multiple fake identities",
        prevention: ["Proof of Work", "Proof of Stake", "Identity verification"],
        impact: "Undermines voting power distribution"
      },
      {
        concept: "Finality",
        description: "Irreversibility of confirmed transactions",
        types: ["Probabilistic finality", "Instant finality"],
        importance: "Prevents double spending"
      },
      {
        concept: "Liveness",
        description: "System continues to make progress",
        requirement: "New blocks are added regularly",
        tradeoff: "Often conflicts with safety"
      },
      {
        concept: "Safety",
        description: "All honest nodes agree on same state",
        requirement: "No conflicting blocks",
        tradeoff: "Often conflicts with liveness"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Requirement: #{concept[:requirement]}" if concept[:requirement]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Prevention: #{concept[:prevention].join(', ')}" if concept[:prevention]
      puts "  Impact: #{concept[:impact]}" if concept[:impact]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Tradeoff: #{concept[:tradeoff]}" if concept[:tradeoff]
      puts
    end
  end
  
  def self.consensus_properties
    puts "\nConsensus Properties:"
    puts "=" * 50
    
    properties = [
      {
        property: "Agreement",
        description: "All honest nodes decide on same value",
        blockchain_use: "All nodes agree on same blockchain state"
      },
      {
        property: "Validity",
        description: "Decided value must be valid according to rules",
        blockchain_use: "Only valid transactions and blocks are accepted"
      },
      {
        property: "Termination",
        description: "All honest nodes eventually decide",
        blockchain_use: "Consensus is reached in finite time"
      },
      {
        property: "Fault Tolerance",
        description: "System tolerates some faulty nodes",
        blockchain_use: "Network continues despite malicious nodes"
      }
    ]
    
    properties.each do |property|
      puts "#{property[:property]}:"
      puts "  Description: #{property[:description]}"
      puts "  Blockchain Use: #{property[:blockchain_use]}"
      puts
    end
  end
  
  def self.consensus_trilemma
    puts "\nBlockchain Trilemma:"
    puts "=" * 50
    
    trilemma = [
      {
        aspect: "Decentralization",
        description: "No single point of control",
        tradeoff: "Often sacrificed for scalability",
        examples: ["Bitcoin", "Ethereum (pre-sharding)"]
      },
      {
        aspect: "Security",
        description: "Resistance to attacks and censorship",
        tradeoff: "May be reduced for scalability",
        examples: ["Proof of Work", "Proof of Stake"]
      },
      {
        aspect: "Scalability",
        description: "High throughput and low latency",
        tradeoff: "Often requires centralization or reduced security",
        examples: ["EOS", "Solana", "Polygon"]
      }
    ]
    
    puts "Most blockchain systems can only achieve 2 out of 3:"
    trilemma.each do |aspect|
      puts "#{aspect[:aspect]}:"
      puts "  Description: #{aspect[:description]}"
      puts "  Tradeoff: #{aspect[:tradeoff]}"
      puts "  Examples: #{aspect[:examples].join(', ')}"
      puts
    end
  end
  
  # Run consensus fundamentals
  explain_consensus_concepts
  consensus_properties
  consensus_trilemma
end
```

### 2. Proof of Work

Mining-based consensus mechanism:

```ruby
class ProofOfWork
  def initialize(difficulty = 4)
    @difficulty = difficulty
    @target = '0' * difficulty
    @max_nonce = 2**32 - 1
  end
  
  attr_reader :difficulty, :target
  
  def mine_block(block_data, miner_address)
    puts "Mining block with difficulty #{@difficulty}..."
    
    start_time = Time.now
    nonce = 0
    
    loop do
      # Create block header
      header = create_block_header(block_data, miner_address, nonce)
      
      # Calculate hash
      hash = calculate_hash(header)
      
      # Check if hash meets difficulty
      if hash.start_with?(@target)
        end_time = Time.now
        mining_time = end_time - start_time
        
        return {
          header: header,
          hash: hash,
          nonce: nonce,
          miner: miner_address,
          mining_time: mining_time,
          hash_rate: nonce / mining_time
        }
      end
      
      # Increment nonce
      nonce += 1
      
      # Check if we've exceeded max nonce
      if nonce > @max_nonce
        puts "Max nonce reached, restarting with new timestamp"
        block_data[:timestamp] = Time.now.to_i
        nonce = 0
      end
    end
  end
  
  def verify_block(block)
    # Recalculate hash
    calculated_hash = calculate_hash(block[:header])
    
    # Verify hash matches
    hash_matches = calculated_hash == block[:hash]
    
    # Verify hash meets difficulty
    valid_proof = block[:hash].start_with?(@target)
    
    # Verify nonce is within bounds
    valid_nonce = block[:nonce] <= @max_nonce
    
    {
      hash_valid: hash_matches,
      proof_valid: valid_proof,
      nonce_valid: valid_nonce,
      block_valid: hash_matches && valid_proof && valid_nonce
    }
  end
  
  def adjust_difficulty(previous_blocks, target_time = 600, adjustment_period = 2016)
    return @difficulty if previous_blocks.length < adjustment_period
    
    # Calculate actual time for last adjustment_period blocks
    time_span = previous_blocks[-1][:header][:timestamp] - previous_blocks[-adjustment_period][:header][:timestamp]
    
    # Calculate new difficulty
    time_ratio = time_span.to_f / (target_time * adjustment_period)
    new_difficulty = @difficulty * time_ratio
    
    # Limit difficulty changes
    max_change = @difficulty * 0.25
    new_difficulty = [@difficulty - max_change, new_difficulty, @difficulty + max_change].sort[1]
    
    # Ensure minimum difficulty
    new_difficulty = [new_difficulty, 1].max
    
    new_difficulty.round
  end
  
  def self.demonstrate_pow
    puts "Proof of Work Demonstration:"
    puts "=" * 50
    
    # Create sample block data
    block_data = {
      index: 1,
      previous_hash: "0000000000000000000000000000000000000000000000000000000000000000",
      transactions: ["Alice pays Bob 10 BTC", "Bob pays Charlie 5 BTC"],
      timestamp: Time.now.to_i
    }
    
    miner_address = "miner1_address"
    
    # Mine blocks with different difficulties
    [1, 2, 3].each do |difficulty|
      puts "\nMining with difficulty #{difficulty}:"
      
      pow = ProofOfWork.new(difficulty)
      result = pow.mine_block(block_data, miner_address)
      
      puts "Block mined!"
      puts "  Hash: #{result[:hash]}"
      puts "  Nonce: #{result[:nonce]}"
      puts "  Mining time: #{result[:mining_time].round(2)}s"
      puts "  Hash rate: #{result[:hash_rate].round(0)} hashes/s"
      
      # Verify block
      verification = pow.verify_block(result)
      puts "  Verification: #{verification[:block_valid] ? 'Valid' : 'Invalid'}"
    end
    
    puts "\nProof of Work Properties:"
    puts "- Energy-intensive mining process"
    puts "- Probabilistic finality"
    puts "- 51% attack resistance"
    puts "- Difficulty adjustment mechanism"
    puts "- Used by Bitcoin, Ethereum (pre-merge)"
  end
  
  private
  
  def create_block_header(block_data, miner_address, nonce)
    {
      index: block_data[:index],
      previous_hash: block_data[:previous_hash],
      transactions: block_data[:transactions].join('|'),
      timestamp: block_data[:timestamp],
      miner: miner_address,
      nonce: nonce
    }
  end
  
  def calculate_hash(header)
    header_string = "#{header[:index]}#{header[:previous_hash]}#{header[:transactions]}#{header[:timestamp]}#{header[:miner]}#{header[:nonce]}"
    HashFunctions.sha256_simulated(header_string)
  end
end
```

### 3. Proof of Stake

Stake-based consensus mechanism:

```ruby
class ProofOfStake
  def initialize
    @validators = {}
    @current_epoch = 0
    @stake_threshold = 1000
    @max_validators = 100
  end
  
  def stake(address, amount)
    @validators[address] ||= { stake: 0, rewards: 0, age: 0 }
    @validators[address][:stake] += amount
    @validators[address][:age] = 0  # Reset age on new stake
    
    puts "#{address} staked #{amount} tokens"
    puts "Total stake: #{@validators[address][:stake]}"
  end
  
  def unstake(address, amount)
    return unless @validators[address]
    
    if @validators[address][:stake] >= amount
      @validators[address][:stake] -= amount
      puts "#{address} unstaked #{amount} tokens"
      puts "Remaining stake: #{@validators[address][:stake]}"
      
      # Remove validator if no stake left
      if @validators[address][:stake] == 0
        @validators.delete(address)
        puts "#{address} removed as validator"
      end
    else
      puts "Insufficient stake for #{address}"
    end
  end
  
  def select_validator(block_data)
    eligible_validators = @validators.select { |_, data| data[:stake] >= @stake_threshold }
    
    if eligible_validators.empty?
      puts "No eligible validators (minimum stake: #{@stake_threshold})"
      return nil
    end
    
    # Weighted random selection based on stake
    total_stake = eligible_validators.values.sum { |v| v[:stake] }
    random_value = rand(total_stake)
    
    cumulative_stake = 0
    selected_validator = nil
    
    eligible_validators.each do |address, data|
      cumulative_stake += data[:stake]
      if cumulative_stake >= random_value
        selected_validator = address
        break
      end
    end
    
    selected_validator
  end
  
  def create_block(block_data, validator_address)
    puts "Creating block with validator: #{validator_address}"
    
    # Update validator age and rewards
    @validators[validator_address][:age] += 1
    
    # Calculate rewards
    block_reward = calculate_block_reward(block_data)
    transaction_fees = calculate_transaction_fees(block_data)
    total_reward = block_reward + transaction_fees
    
    @validators[validator_address][:rewards] += total_reward
    
    # Create block
    block = {
      header: {
        index: block_data[:index],
        previous_hash: block_data[:previous_hash],
        validator: validator_address,
        timestamp: Time.now.to_i,
        transactions: block_data[:transactions]
      },
      signature: sign_block(block_data, validator_address),
      rewards: {
        block_reward: block_reward,
        transaction_fees: transaction_fees,
        total_reward: total_reward
      }
    }
    
    block
  end
  
  def verify_block(block)
    # Verify validator signature (simplified)
    signature_valid = verify_signature(block[:header], block[:signature], block[:header][:validator])
    
    # Verify validator has sufficient stake
    validator = @validators[block[:header][:validator]]
    has_stake = validator && validator[:stake] >= @stake_threshold
    
    {
      signature_valid: signature_valid,
      stake_valid: has_stake,
      block_valid: signature_valid && has_stake
    }
  end
  
  def slash_validator(address, reason)
    return unless @validators[address]
    
    # Slash portion of stake
    slash_amount = @validators[address][:stake] * 0.1
    @validators[address][:stake] -= slash_amount
    
    puts "Validator #{address} slashed #{slash_amount} tokens"
    puts "Reason: #{reason}"
    puts "Remaining stake: #{@validators[address][:stake]}"
    
    # Remove if stake below threshold
    if @validators[address][:stake] < @stake_threshold
      @validators.delete(address)
      puts "#{address} removed as validator"
    end
  end
  
  def get_validator_stats
    total_stake = @validators.values.sum { |v| v[:stake] }
    total_rewards = @validators.values.sum { |v| v[:rewards] }
    
    {
      total_validators: @validators.length,
      total_stake: total_stake,
      total_rewards: total_rewards,
      average_stake: @validators.empty? ? 0 : total_stake / @validators.length,
      top_validators: @validators.sort_by { |_, data| -data[:stake] }.first(5).to_h
    }
  end
  
  def self.demonstrate_pos
    puts "Proof of Stake Demonstration:"
    puts "=" * 50
    
    pos = ProofOfStake.new
    
    # Add validators
    validators = [
      { address: "validator1", amount: 5000 },
      { address: "validator2", amount: 3000 },
      { address: "validator3", amount: 8000 },
      { address: "validator4", amount: 1500 },
      { address: "validator5", amount: 6000 }
    ]
    
    puts "Adding validators:"
    validators.each do |validator|
      pos.stake(validator[:address], validator[:amount])
    end
    
    # Create block data
    block_data = {
      index: 1,
      previous_hash: "0000000000000000000000000000000000000000000000000000000000000000",
      transactions: ["Alice pays Bob 10 BTC", "Bob pays Charlie 5 BTC"]
    }
    
    # Select validator and create block
    puts "\nBlock creation:"
    selected_validator = pos.select_validator(block_data)
    
    if selected_validator
      block = pos.create_block(block_data, selected_validator)
      
      puts "Block created by: #{selected_validator}"
      puts "Block reward: #{block[:rewards][:block_reward]}"
      puts "Transaction fees: #{block[:rewards][:transaction_fees]}"
      puts "Total reward: #{block[:rewards][:total_reward]}"
      
      # Verify block
      verification = pos.verify_block(block)
      puts "Block verification: #{verification[:block_valid] ? 'Valid' : 'Invalid'}"
    end
    
    # Show validator stats
    puts "\nValidator Statistics:"
    stats = pos.get_validator_stats
    puts "Total validators: #{stats[:total_validators]}"
    puts "Total stake: #{stats[:total_stake]}"
    puts "Total rewards: #{stats[:total_rewards]}"
    puts "Average stake: #{stats[:average_stake]}"
    
    puts "\nTop validators by stake:"
    stats[:top_validators].each do |address, data|
      puts "  #{address}: #{data[:stake]} stake, #{data[:rewards]} rewards"
    end
    
    # Demonstrate slashing
    puts "\nSlashing demonstration:"
    pos.slash_validator("validator3", "Double signing detected")
    
    puts "\nProof of Stake Properties:"
    puts "- Energy-efficient consensus"
    puts "- Stake-based validator selection"
    puts "- Economic incentives and penalties"
    puts "- Faster block times"
    puts "- Used by Ethereum 2.0, Cardano, Polkadot"
  end
  
  private
  
  def calculate_block_reward(block_data)
    # Fixed block reward (simplified)
    2.0
  end
  
  def calculate_transaction_fees(block_data)
    # Calculate fees based on transaction complexity
    block_data[:transactions].length * 0.1
  end
  
  def sign_block(block_data, validator_address)
    # Simplified signature (in practice, use private key)
    "signature_#{validator_address}_#{block_data[:index]}_#{Time.now.to_i}"
  end
  
  def verify_signature(block_header, signature, validator_address)
    # Simplified verification
    signature.include?(validator_address) && signature.include?(block_header[:index].to_s)
  end
end
```

## 🔄 Advanced Consensus

### 4. Delegated Proof of Stake

Delegated staking mechanism:

```ruby
class DelegatedProofOfStake
  def initialize
    @validators = {}
    @delegators = {}
    @witnesses = []
    @max_witnesses = 21
    @vote_threshold = 100
  end
  
  def register_witness(address, url)
    return if @witnesses.include?(address)
    return if @witnesses.length >= @max_witnesses
    
    @witnesses << address
    @validators[address] = {
      url: url,
      votes: 0,
      rewards: 0,
      blocks_produced: 0,
      uptime: 100.0
    }
    
    puts "Witness #{address} registered with URL: #{url}"
    puts "Total witnesses: #{@witnesses.length}/#{@max_witnesses}"
  end
  
  def vote_for_witness(voter_address, witness_address, votes)
    return unless @witnesses.include?(witness_address)
    
    # Remove previous votes
    if @delegators[voter_address]
      previous_witness = @delegators[voter_address][:witness]
      @validators[previous_witness][:votes] -= @delegators[voter_address][:votes]
    end
    
    # Add new votes
    @delegators[voter_address] = {
      witness: witness_address,
      votes: votes
    }
    
    @validators[witness_address][:votes] += votes
    
    puts "#{voter_address} voted #{votes} for #{witness_address}"
    puts "#{witness_address} total votes: #{@validators[witness_address][:votes]}"
  end
  
  def get_active_witnesses
    # Sort witnesses by votes and take top N
    sorted_witnesses = @witnesses.sort_by do |witness|
      -@validators[witness][:votes]
    end
    
    sorted_witnesses.first(@max_witnesses)
  end
  
  def select_witness(round_number)
    active_witnesses = get_active_witnesses
    
    if active_witnesses.empty?
      puts "No active witnesses available"
      return nil
    end
    
    # Round-robin selection based on round number
    selected_index = round_number % active_witnesses.length
    selected_witness = active_witnesses[selected_index]
    
    puts "Selected witness: #{selected_witness} (round #{round_number})"
    selected_witness
  end
  
  def create_block(block_data, witness_address, round_number)
    puts "Creating block with witness: #{witness_address}"
    
    # Update witness stats
    @validators[witness_address][:blocks_produced] += 1
    
    # Calculate rewards
    block_reward = calculate_block_reward(block_data)
    witness_reward = block_reward * 0.1  # 10% to witness
    
    @validators[witness_address][:rewards] += witness_reward
    
    # Distribute rewards to delegators
    delegator_reward = (block_reward - witness_reward) / @validators[witness_address][:votes]
    
    @delegators.each do |delegator, data|
      if data[:witness] == witness_address
        delegator_reward_total = delegator_reward * data[:votes]
        # In practice, this would be credited to delegator account
        puts "#{delegator} earned #{delegator_reward_total.round(4)} tokens"
      end
    end
    
    # Create block
    block = {
      header: {
        index: block_data[:index],
        previous_hash: block_data[:previous_hash],
        witness: witness_address,
        round_number: round_number,
        timestamp: Time.now.to_i,
        transactions: block_data[:transactions]
      },
      witness_info: {
        url: @validators[witness_address][:url],
        votes: @validators[witness_address][:votes]
      },
      rewards: {
        block_reward: block_reward,
        witness_reward: witness_reward,
        delegator_reward: block_reward - witness_reward
      }
    }
    
    block
  end
  
  def update_witness_performance(witness_address, uptime, missed_blocks)
    return unless @validators[witness_address]
    
    @validators[witness_address][:uptime] = uptime
    @validators[witness_address][:missed_blocks] = missed_blocks
    
    # Remove witness if performance is poor
    if uptime < 95.0 || missed_blocks > 10
      remove_witness(witness_address, "Poor performance")
    end
  end
  
  def remove_witness(witness_address, reason)
    return unless @witnesses.include?(witness_address)
    
    @witnesses.delete(witness_address)
    @validators.delete(witness_address)
    
    # Redistribute votes
    @delegators.each do |delegator, data|
      if data[:witness] == witness_address
        @delegators.delete(delegator)
        puts "#{delegator}'s votes redistributed (witness removed)"
      end
    end
    
    puts "Witness #{witness_address} removed: #{reason}"
    puts "Active witnesses: #{@witnesses.length}/#{@max_witnesses}"
  end
  
  def get_witness_rankings
    active_witnesses = get_active_witnesses
    
    active_witnesses.map.with_index do |witness, rank|
      {
        rank: rank + 1,
        address: witness,
        votes: @validators[witness][:votes],
        blocks_produced: @validators[witness][:blocks_produced],
        uptime: @validators[witness][:uptime],
        url: @validators[witness][:url]
      }
    end
  end
  
  def self.demonstrate_dpos
    puts "Delegated Proof of Stake Demonstration:"
    puts "=" * 50
    
    dpos = DelegatedProofOfStake.new
    
    # Register witnesses
    witnesses = [
      { address: "witness1", url: "https://witness1.example.com" },
      { address: "witness2", url: "https://witness2.example.com" },
      { address: "witness3", url: "https://witness3.example.com" },
      { address: "witness4", url: "https://witness4.example.com" },
      { address: "witness5", url: "https://witness5.example.com" }
    ]
    
    puts "Registering witnesses:"
    witnesses.each do |witness|
      dpos.register_witness(witness[:address], witness[:url])
    end
    
    # Vote for witnesses
    puts "\nVoting for witnesses:"
    votes = [
      { voter: "voter1", witness: "witness1", votes: 1000 },
      { voter: "voter2", witness: "witness2", votes: 800 },
      { voter: "voter3", witness: "witness3", votes: 1200 },
      { voter: "voter4", witness: "witness1", votes: 500 },
      { voter: "voter5", witness: "witness4", votes: 600 }
    ]
    
    votes.each do |vote|
      dpos.vote_for_witness(vote[:voter], vote[:witness], vote[:votes])
    end
    
    # Show active witnesses
    puts "\nActive witnesses:"
    active_witnesses = dpos.get_active_witnesses
    active_witnesses.each_with_index do |witness, i|
      votes = dpos.instance_variable_get(:@validators)[witness][:votes]
      puts "  #{i + 1}. #{witness}: #{votes} votes"
    end
    
    # Create blocks
    puts "\nBlock creation:"
    block_data = {
      index: 1,
      previous_hash: "0000000000000000000000000000000000000000000000000000000000000000",
      transactions: ["Alice pays Bob 10 BTC"]
    }
    
    3.times do |round|
      selected_witness = dpos.select_witness(round)
      
      if selected_witness
        block = dpos.create_block(block_data, selected_witness, round)
        puts "Round #{round}: Block created by #{selected_witness}"
      end
    end
    
    # Show witness rankings
    puts "\nWitness Rankings:"
    rankings = dpos.get_witness_rankings
    rankings.each do |ranking|
      puts "#{ranking[:rank]}. #{ranking[:address]}: #{ranking[:votes]} votes, #{ranking[:blocks_produced]} blocks, #{ranking[:uptime]}% uptime"
    end
    
    # Update performance
    puts "\nPerformance update:"
    dpos.update_witness_performance("witness2", 92.0, 15)
    
    puts "\nDelegated Proof of Stake Properties:"
    puts "- Democratic validator selection"
    puts "- Vote-based witness system"
    puts "- Fast block confirmation"
    puts "- Scalable consensus"
    puts "- Used by EOS, TRON, Lisk"
  end
end
```

### 5. Practical Byzantine Fault Tolerance

BFT consensus algorithm:

```ruby
class PracticalByzantineFaultTolerance
  def initialize(nodes, fault_tolerance = 1)
    @nodes = nodes
    @fault_tolerance = fault_tolerance
    @primary_index = 0
    @view_number = 0
    @prepared = {}
    @committed = {}
    @pre_prepare_messages = {}
    @prepare_messages = {}
    @commit_messages = {}
  end
  
  def get_primary_node
    @nodes[@primary_index % @nodes.length]
  end
  
  def rotate_primary
    @primary_index += 1
    @view_number += 1
    
    puts "Rotating primary to: #{get_primary_node}"
    puts "View number: #{@view_number}"
  end
  
  def pre_prepare(primary, sequence_number, request)
    message = {
      type: :pre_prepare,
      view: @view_number,
      sequence_number: sequence_number,
      digest: calculate_digest(request),
      request: request,
      sender: primary
    }
    
    @pre_prepare_messages[sequence_number] = message
    
    puts "PRE-PREPARE: #{primary} for sequence #{sequence_number}"
    broadcast_message(message)
    
    message
  end
  
  def prepare(node, sequence_number, pre_prepare_msg)
    message = {
      type: :prepare,
      view: @view_number,
      sequence_number: sequence_number,
      digest: pre_prepare_msg[:digest],
      sender: node
    }
    
    @prepare_messages[sequence_number] ||= []
    @prepare_messages[sequence_number] << message
    
    puts "PREPARE: #{node} for sequence #{sequence_number}"
    
    # Check if we have 2f+1 prepares
    prepares = @prepare_messages[sequence_number] || []
    unique_senders = prepares.map { |msg| msg[:sender] }.uniq
    
    if unique_senders.length >= (2 * @fault_tolerance + 1)
      @prepared[sequence_number] = true
      puts "PREPARED: Sequence #{sequence_number} (2f+1 prepares received)"
      
      # Send commit
      commit(node, sequence_number, pre_prepare_msg)
    end
    
    message
  end
  
  def commit(node, sequence_number, pre_prepare_msg)
    message = {
      type: :commit,
      view: @view_number,
      sequence_number: sequence_number,
      digest: pre_prepare_msg[:digest],
      sender: node
    }
    
    @commit_messages[sequence_number] ||= []
    @commit_messages[sequence_number] << message
    
    puts "COMMIT: #{node} for sequence #{sequence_number}"
    
    # Check if we have 2f+1 commits
    commits = @commit_messages[sequence_number] || []
    unique_senders = commits.map { |msg| msg[:sender] }.uniq
    
    if unique_senders.length >= (2 * @fault_tolerance + 1)
      @committed[sequence_number] = true
      puts "COMMITTED: Sequence #{sequence_number} (2f+1 commits received)"
      
      # Execute the request
      execute_request(pre_prepare_msg[:request])
    end
    
    message
  end
  
  def handle_timeout(sequence_number)
    puts "TIMEOUT: Sequence #{sequence_number}"
    
    # Start view change
    start_view_change(sequence_number)
  end
  
  def start_view_change(sequence_number)
    puts "VIEW CHANGE: Starting view change for sequence #{sequence_number}"
    
    # Rotate primary
    rotate_primary
    
    # Clear prepared and committed for this sequence
    @prepared.delete(sequence_number)
    @committed.delete(sequence_number)
    
    # In practice, would broadcast view change messages
    puts "View change initiated, new primary: #{get_primary_node}"
  end
  
  def execute_request(request)
    puts "EXECUTING: #{request[:type]} - #{request[:data]}"
    
    # In practice, would execute the actual request
    # and update the state machine
    {
      executed: true,
      request: request,
      timestamp: Time.now
    }
  end
  
  def get_consensus_state
    {
      primary: get_primary_node,
      view_number: @view_number,
      fault_tolerance: @fault_tolerance,
      total_nodes: @nodes.length,
      prepared_sequences: @prepared.keys,
      committed_sequences: @committed.keys
    }
  end
  
  def self.demonstrate_pbft
    puts "Practical Byzantine Fault Tolerance Demonstration:"
    puts "=" * 50
    
    # Create nodes
    nodes = ["Node1", "Node2", "Node3", "Node4"]
    fault_tolerance = 1
    
    puts "Creating PBFT system:"
    puts "Nodes: #{nodes.join(', ')}"
    puts "Fault tolerance: #{fault_tolerance} malicious nodes"
    puts "Required prepares: #{2 * fault_tolerance + 1}"
    puts "Required commits: #{2 * fault_tolerance + 1}"
    
    pbft = PracticalByzantineFaultTolerance.new(nodes, fault_tolerance)
    
    # Sample request
    request = {
      type: :transfer,
      from: "Alice",
      to: "Bob",
      amount: 10,
      timestamp: Time.now
    }
    
    puts "\nProcessing request: #{request[:type]} from #{request[:from]} to #{request[:to]}"
    
    # Phase 1: Pre-prepare
    puts "\nPhase 1: Pre-prepare"
    primary = pbft.get_primary_node
    sequence_number = 1
    
    pre_prepare_msg = pbft.pre_prepare(primary, sequence_number, request)
    
    # Phase 2: Prepare
    puts "\nPhase 2: Prepare"
    nodes.each do |node|
      next if node == primary
      
      pbft.prepare(node, sequence_number, pre_prepare_msg)
    end
    
    # Phase 3: Commit
    puts "\nPhase 3: Commit"
    nodes.each do |node|
      pbft.commit(node, sequence_number, pre_prepare_msg)
    end
    
    # Show consensus state
    puts "\nConsensus State:"
    state = pbft.get_consensus_state
    puts "Primary: #{state[:primary]}"
    puts "View number: #{state[:view_number]}"
    puts "Prepared sequences: #{state[:prepared_sequences]}"
    puts "Committed sequences: #{state[:committed_sequences]}"
    
    # Demonstrate view change
    puts "\nView Change Demonstration:"
    pbft.handle_timeout(2)
    
    puts "\nPBFT Properties:"
    puts "- Byzantine fault tolerance"
    puts "- Deterministic consensus"
    puts "- Instant finality"
    puts "- Three-phase protocol"
    puts "- Used by Hyperledger Fabric"
  end
  
  private
  
  def broadcast_message(message)
    # In practice, would broadcast to all nodes
    puts "Broadcasting #{message[:type]} message from #{message[:sender]}"
  end
  
  def calculate_digest(request)
    # Simplified digest calculation
    request.to_s.hash
  end
end
```

## 🎯 Consensus Comparison

### 6. Consensus Algorithm Comparison

Compare different consensus mechanisms:

```ruby
class ConsensusComparison
  def self.compare_algorithms
    puts "Consensus Algorithm Comparison:"
    puts "=" * 60
    
    algorithms = [
      {
        name: "Proof of Work (PoW)",
        energy_efficiency: "Very Low",
        finality: "Probabilistic",
        scalability: "Low",
        security: "Very High",
        decentralization: "Very High",
        examples: ["Bitcoin", "Ethereum (pre-merge)"],
        pros: ["High security", "Decentralized", "Well-tested"],
        cons: ["Energy intensive", "Slow", "Expensive"]
      },
      {
        name: "Proof of Stake (PoS)",
        energy_efficiency: "Very High",
        finality: "Fast",
        scalability: "Medium",
        security: "High",
        decentralization: "High",
        examples: ["Ethereum 2.0", "Cardano", "Polkadot"],
        pros: ["Energy efficient", "Fast", "Low cost"],
        cons: ["Rich get richer", "Complex", "Newer technology"]
      },
      {
        name: "Delegated PoS (DPoS)",
        energy_efficiency: "Very High",
        finality: "Instant",
        scalability: "High",
        security: "Medium",
        decentralization: "Medium",
        examples: ["EOS", "TRON", "Lisk"],
        pros: ["Very fast", "Democratic", "Scalable"],
        cons: ["Less decentralized", "Cartel risk", "Complex voting"]
      },
      {
        name: "Practical BFT (PBFT)",
        energy_efficiency: "Very High",
        finality: "Instant",
        scalability: "Low",
        security: "Medium",
        decentralization: "Low",
        examples: ["Hyperledger Fabric", "Stellar"],
        pros: ["Instant finality", "Byzantine tolerant", "Deterministic"],
        cons: ["Limited scalability", "Complex", "Permissioned often"]
      }
    ]
    
    # Create comparison table
    puts "| Algorithm | Energy | Finality | Scalability | Security | Decentralization |"
    puts "|-----------|--------|----------|-------------|----------|------------------|"
    
    algorithms.each do |algo|
      puts "| #{algo[:name].ljust(17)} | #{algo[:energy_efficiency].ljust(6)} | #{algo[:finality].ljust(8)} | #{algo[:scalability].ljust(11)} | #{algo[:security].ljust(8)} | #{algo[:decentralization].ljust(16)} |"
    end
    
    puts "\nDetailed Comparison:"
    algorithms.each do |algo|
      puts "\n#{algo[:name]}:"
      puts "  Examples: #{algo[:examples].join(', ')}"
      puts "  Pros: #{algo[:pros].join(', ')}"
      puts "  Cons: #{algo[:cons].join(', ')}"
    end
  end
  
  def self.use_case_recommendations
    puts "\nUse Case Recommendations:"
    puts "=" * 50
    
    use_cases = [
      {
        use_case: "Public Cryptocurrency",
        requirements: ["High security", "Decentralization", "Trustless"],
        recommended: ["Proof of Work", "Proof of Stake"],
        reasoning: "Need maximum security and decentralization"
      },
      {
        use_case: "Enterprise Blockchain",
        requirements: ["Performance", "Permissioned", "Compliance"],
        recommended: ["Practical BFT", "Raft"],
        reasoning: "Need instant finality and known participants"
      },
      {
        use_case: "High-Throughput dApps",
        requirements: ["Scalability", "Low cost", "Fast"],
        recommended: ["Delegated PoS", "Proof of Stake"],
        reasoning: "Need high TPS and low fees"
      },
      {
        use_case: "IoT Networks",
        requirements: ["Energy efficient", "Lightweight", "Fast"],
        recommended: ["Proof of Stake", "Light BFT"],
        reasoning: "Resource-constrained devices"
      },
      {
        use_case: "Digital Identity",
        requirements: ["Fast", "Secure", "Scalable"],
        recommended: ["Proof of Stake", "Hybrid"],
        reasoning: "Need quick verification and low cost"
      }
    ]
    
    use_cases.each do |use_case|
      puts "#{use_case[:use_case]}:"
      puts "  Requirements: #{use_case[:requirements].join(', ')}"
      puts "  Recommended: #{use_case[:recommended].join(', ')}"
      puts "  Reasoning: #{use_case[:reasoning]}"
      puts
    end
  end
  
  def self.performance_metrics
    puts "\nPerformance Metrics:"
    puts "=" * 50
    
    metrics = [
      {
        algorithm: "Bitcoin (PoW)",
        tps: "7",
        block_time: "10 minutes",
        finality: "6 confirmations (~1 hour)",
        energy_per_tx: "1,500 kWh"
      },
      {
        algorithm: "Ethereum (PoS)",
        tps: "15-30",
        block_time: "12 seconds",
        finality: "~1 minute",
        energy_per_tx: "0.01 kWh"
      },
      {
        algorithm: "EOS (DPoS)",
        tps: "4,000",
        block_time: "0.5 seconds",
        finality: "Instant",
        energy_per_tx: "Negligible"
      },
      {
        algorithm: "Hyperledger (PBFT)",
        tps: "10,000+",
        block_time: "Sub-second",
        finality: "Instant",
        energy_per_tx: "Negligible"
      }
    ]
    
    puts "| Algorithm | TPS | Block Time | Finality | Energy/TX |"
    puts "|-----------|-----|------------|----------|-----------|"
    
    metrics.each do |metric|
      puts "| #{metric[:algorithm].ljust(19)} | #{metric[:tps].ljust(3)} | #{metric[:block_time].ljust(10)} | #{metric[:finality].ljust(8)} | #{metric[:energy_per_tx].ljust(9)} |"
    end
    
    puts "\nKey Insights:"
    puts "- PoW: Most secure but slowest and most expensive"
    puts "- PoS: Good balance of security and efficiency"
    puts "- DPoS: Fastest but less decentralized"
    puts "- PBFT: Fastest for permissioned networks"
  end
  
  # Run all comparisons
  compare_algorithms
  use_case_recommendations
  performance_metrics
end
```

## 🎓 Exercises

### Beginner Exercises

1. **PoW Mining**: Implement simple mining algorithm
2. **PoS Staking**: Create staking mechanism
3. **Consensus Basics**: Understand consensus properties
4. **Hash Verification**: Verify block integrity

### Intermediate Exercises

1. **DPoS Voting**: Implement delegate voting
2. **BFT Protocol**: Build BFT consensus
3. **Consensus Comparison**: Analyze different algorithms
4. **Performance Testing**: Measure consensus performance

### Advanced Exercises

1. **Hybrid Consensus**: Combine multiple algorithms
2. **Optimization**: Improve consensus efficiency
3. **Security Analysis**: Analyze consensus security
4. **Real Implementation**: Build production-ready consensus

---

## 🎯 Summary

Consensus Mechanisms in Ruby provide:

- **Consensus Fundamentals** - Core concepts and properties
- **Proof of Work** - Mining-based consensus implementation
- **Proof of Stake** - Stake-based validator selection
- **DPoS** - Delegated voting mechanism
- **BFT** - Byzantine fault tolerance
- **Consensus Comparison** - Algorithm analysis and comparison
- **Practical Applications** - Real-world consensus implementations

Master these consensus mechanisms for robust blockchain development!
