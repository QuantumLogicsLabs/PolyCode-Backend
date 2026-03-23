# Smart Contracts in Ruby
# Comprehensive guide to smart contract development and execution

## 📝 Smart Contract Fundamentals

### 1. Smart Contract Concepts

Core smart contract concepts:

```ruby
class SmartContractBasics
  def self.explain_contract_concepts
    puts "Smart Contract Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Smart Contract",
        description: "Self-executing contract with terms written in code",
        characteristics: ["Self-executing", "Immutable", "Transparent", "Autonomous"],
        benefits: ["No intermediaries", "Reduced costs", "Increased efficiency", "Enhanced security"]
      },
      {
        concept: "Blockchain Integration",
        description: "Contracts deployed on blockchain for trust and immutability",
        features: ["Distributed execution", "Cryptographic security", "Consensus validation"],
        benefits: ["Tamper-resistant", "Transparent execution", "Global accessibility"]
      },
      {
        concept: "Contract State",
        description: "Data stored and managed by the contract",
        types: ["Storage variables", "Mappings", "Arrays", "Structures"],
        persistence: "Permanent until contract destruction"
      },
      {
        concept: "Contract Functions",
        description: "Executable code that manipulates contract state",
        types: ["Public functions", "Private functions", "View functions", "Payable functions"],
        visibility: "Controls access to contract functionality"
      },
      {
        concept: "Gas/Energy",
        description: "Cost of executing contract operations",
        purpose: "Prevent infinite loops, resource allocation",
        calculation: "Based on computational complexity"
      },
      {
        concept: "Contract Deployment",
        description: "Process of publishing contract to blockchain",
        steps: ["Compilation", "Deployment transaction", "Contract address generation"],
        irreversibility: "Cannot be modified after deployment"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Persistence: #{concept[:persistence]}" if concept[:persistence]
      puts "  Visibility: #{concept[:visibility]}" if concept[:visibility]
      puts "  Purpose: #{concept[:purpose]}" if concept[:purpose]
      puts "  Calculation: #{concept[:calculation]}" if concept[:calculation]
      puts "  Steps: #{concept[:steps].join(', ')}" if concept[:steps]
      puts "  Irreversibility: #{concept[:irreversibility]}" if concept[:irreversibility]
      puts
    end
  end
  
  def self.contract_lifecycle
    puts "\nSmart Contract Lifecycle:"
    puts "=" * 50
    
    lifecycle = [
      {
        phase: "Development",
        description: "Writing and testing contract code",
        activities: ["Coding", "Unit testing", "Integration testing", "Security audit"],
        tools: ["IDE", "Testing frameworks", "Static analyzers"]
      },
      {
        phase: "Compilation",
        description: "Converting source code to bytecode",
        activities: ["Syntax checking", "Optimization", "Bytecode generation"],
        output: "Executable bytecode and ABI"
      },
      {
        phase: "Deployment",
        description: "Publishing contract to blockchain",
        activities: ["Transaction creation", "Gas estimation", "Network submission"],
        result: "Contract address and initialization"
      },
      {
        phase: "Interaction",
        description: "Calling contract functions",
        activities: ["Function calls", "State queries", "Event listening"],
        methods: ["Transactions", "Calls", "Events"]
      },
      {
        phase: "Maintenance",
        description: "Contract updates and management",
        activities: ["Bug fixes", "Upgrades", "Migration"],
        approaches: ["Proxy patterns", "Immutable contracts"]
      },
      {
        phase: "Destruction",
        description: "Removing contract from blockchain",
        activities: ["Self-destruct", "State cleanup", "Fund return"],
        finality: "Permanent removal"
      }
    ]
    
    lifecycle.each do |phase|
      puts "#{phase[:phase]}:"
      puts "  Description: #{phase[:description]}"
      puts "  Activities: #{phase[:activities].join(', ')}"
      puts "  Tools: #{phase[:tools].join(', ')}" if phase[:tools]
      puts "  Output: #{phase[:output]}" if phase[:output]
      puts "  Result: #{phase[:result]}" if phase[:result]
      puts "  Methods: #{phase[:methods].join(', ')}" if phase[:methods]
      puts "  Approaches: #{phase[:approaches].join(', ')}" if phase[:approaches]
      puts "  Finality: #{phase[:finality]}" if phase[:finality]
      puts
    end
  end
  
  def self.contract_design_principles
    puts "\nSmart Contract Design Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Simplicity",
        description: "Keep contracts simple and focused",
        guidelines: ["Single responsibility", "Clear interfaces", "Minimal complexity"],
        benefits: ["Easier audit", "Reduced bugs", "Lower gas costs"]
      },
      {
        principle: "Security",
        description: "Prioritize security over features",
        guidelines: ["Input validation", "Access control", "Reentrancy protection"],
        techniques: ["Checks-effects-interactions", "Fail-fast", "Rate limiting"]
      },
      {
        principle: "Efficiency",
        description: "Optimize for gas usage",
        guidelines: ["Minimize storage", "Optimize loops", "Use appropriate data types"],
        benefits: ["Lower costs", "Faster execution", "Better UX"]
      },
      {
        principle: "Upgradability",
        description: "Design for future changes",
        patterns: ["Proxy contracts", "State separation", "Logic contracts"],
        approaches: ["Immutable logic", "Migratable state"]
      },
      {
        principle: "Interoperability",
        description: "Enable interaction with other contracts",
        standards: ["ERC20", "ERC721", "ERC1155"],
        benefits: ["Ecosystem integration", "Standard interfaces", "Composability"]
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Guidelines: #{principle[:guidelines].join(', ')}"
      puts "  Benefits: #{principle[:benefits].join(', ')}" if principle[:benefits]
      puts "  Techniques: #{principle[:techniques].join(', ')}" if principle[:techniques]
      puts "  Patterns: #{principle[:patterns].join(', ')}" if principle[:patterns]
      puts "  Approaches: #{principle[:approaches].join(', ')}" if principle[:approaches]
      puts "  Standards: #{principle[:standards].join(', ')}" if principle[:standards]
      puts
    end
  end
  
  # Run smart contract basics
  explain_contract_concepts
  contract_lifecycle
  contract_design_principles
end
```

### 2. Simple Smart Contract Framework

Basic smart contract execution engine:

```ruby
class SmartContract
  def initialize(address, owner)
    @address = address
    @owner = owner
    @storage = {}
    @code = nil
    @balance = 0
    @events = []
    @paused = false
  end
  
  attr_reader :address, :owner, :balance, :events, :paused
  
  def deploy(code, initial_balance = 0)
    @code = code
    @balance = initial_balance
    
    # Execute constructor
    execute_function(:constructor, [])
    
    puts "Contract deployed at #{@address}"
    puts "Owner: #{@owner}"
    puts "Initial balance: #{@balance}"
    
    @address
  end
  
  def execute_function(function_name, args, caller = nil, value = 0)
    return { success: false, error: "Contract paused" } if @paused
    return { success: false, error: "No code deployed" } unless @code
    
    # Check function exists
    unless @code[:functions].include?(function_name)
      return { success: false, error: "Function #{function_name} not found" }
    end
    
    function = @code[:functions][function_name]
    
    # Check visibility
    if function[:visibility] == :private && caller != @owner
      return { success: false, error: "Private function access denied" }
    end
    
    # Check payable for value transfers
    if value > 0 && !function[:payable]
      return { success: false, error: "Function not payable" }
    end
    
    # Add value to contract balance
    @balance += value
    
    begin
      # Execute function
      result = instance_eval(&function[:code])
      
      # Emit event if specified
      if function[:event]
        emit_event(function[:event], { result: result, caller: caller, args: args })
      end
      
      { success: true, result: result, gas_used: calculate_gas(function) }
    rescue => e
      { success: false, error: e.message }
    end
  end
  
  def call_function(function_name, args, caller = nil)
    # Read-only function call (no state changes)
    return { success: false, error: "No code deployed" } unless @code
    
    unless @code[:functions].include?(function_name)
      return { success: false, error: "Function #{function_name} not found" }
    end
    
    function = @code[:functions][function_name]
    
    # Check if it's a view function
    unless function[:view]
      return { success: false, error: "Function not view-only" }
    end
    
    begin
      result = instance_eval(&function[:code])
      { success: true, result: result }
    rescue => e
      { success: false, error: e.message }
    end
  end
  
  def transfer(to_address, amount)
    return { success: false, error: "Insufficient balance" } if @balance < amount
    return { success: false, error: "Invalid transfer" } if amount <= 0
    
    @balance -= amount
    
    emit_event(:Transfer, { from: @address, to: to_address, amount: amount })
    
    { success: true, amount: amount }
  end
  
  def get_storage(key)
    @storage[key]
  end
  
  def set_storage(key, value)
    @storage[key] = value
  end
  
  def pause
    @paused = true
    emit_event(:Paused, { contract: @address })
  end
  
  def unpause
    @paused = false
    emit_event(:Unpaused, { contract: @address })
  end
  
  def self_event(event_name, data)
    emit_event(event_name, data)
  end
  
  def get_events(event_type = nil)
    if event_type
      @events.select { |event| event[:type] == event_type }
    else
      @events
    end
  
  def clear_events
    @events.clear
  end
  
  def self.create_contract_template
    {
      functions: {},
      storage: {},
      events: []
    }
  end
  
  def self.define_function(template, name, visibility: :public, payable: false, view: false, event: nil, &block)
    template[:functions][name] = {
      visibility: visibility,
      payable: payable,
      view: view,
      event: event,
      code: block
    }
  end
  
  private
  
  def emit_event(event_type, data)
    event = {
      type: event_type,
      data: data,
      timestamp: Time.now,
      contract: @address
    }
    
    @events << event
    puts "Event emitted: #{event_type} - #{data}"
  end
  
  def calculate_gas(function)
    # Simplified gas calculation
    base_gas = 21000
    storage_gas = @storage.length * 200
    computation_gas = function[:complexity] || 0
    
    base_gas + storage_gas + computation_gas
  end
  
  def require(condition, message)
    raise message unless condition
  end
  
  def assert(condition, message)
    puts "Assertion: #{message}" unless condition
  end
end

class ContractFactory
  def self.create_token_contract(name, symbol, initial_supply, owner)
    contract = SmartContract.new(generate_address, owner)
    
    template = SmartContract.create_contract_template
    
    # Define constructor
    SmartContract.define_function(template, :constructor) do
      set_storage(:name, name)
      set_storage(:symbol, symbol)
      set_storage(:total_supply, initial_supply)
      set_storage(:owner, @owner)
      set_storage(:balances, { @owner => initial_supply })
    end
    
    # Define balanceOf function
    SmartContract.define_function(template, :balanceOf, view: true) do |address|
      balances = get_storage(:balances) || {}
      balances[address] || 0
    end
    
    # Define transfer function
    SmartContract.define_function(template, :transfer, event: :Transfer, payable: false) do |to, amount|
      require(amount > 0, "Amount must be positive")
      
      balances = get_storage(:balances) || {}
      from_balance = balances[@caller] || 0
      
      require(from_balance >= amount, "Insufficient balance")
      
      # Update balances
      balances[@caller] = from_balance - amount
      balances[to] = (balances[to] || 0) + amount
      
      set_storage(:balances, balances)
      
      true
    end
    
    # Define approve function
    SmartContract.define_function(template, :approve, event: :Approval) do |spender, amount|
      allowances = get_storage(:allowances) || {}
      allowances[@caller] ||= {}
      allowances[@caller][spender] = amount
      
      set_storage(:allowances, allowances)
      
      true
    end
    
    # Define transferFrom function
    SmartContract.define_function(template, :transferFrom, event: :Transfer) do |from, to, amount|
      require(amount > 0, "Amount must be positive")
      
      balances = get_storage(:balances) || {}
      allowances = get_storage(:allowances) || {}
      
      from_balance = balances[from] || 0
      allowance = allowances[from][@caller] || 0
      
      require(from_balance >= amount, "Insufficient balance")
      require(allowance >= amount, "Insufficient allowance")
      
      # Update balances
      balances[from] = from_balance - amount
      balances[to] = (balances[to] || 0) + amount
      
      # Update allowance
      allowances[from][@caller] = allowance - amount
      
      set_storage(:balances, balances)
      set_storage(:allowances, allowances)
      
      true
    end
    
    # Deploy contract
    contract.instance_variable_set(:@code, template)
    contract.deploy(template, 0)
    
    contract
  end
  
  def self.create_voting_contract(owner, proposals)
    contract = SmartContract.new(generate_address, owner)
    
    template = SmartContract.create_contract_template
    
    # Define constructor
    SmartContract.define_function(template, :constructor) do
      set_storage(:owner, @owner)
      set_storage(:proposals, proposals)
      set_storage(:votes, {})
      set_storage(:voters, Set.new)
      set_storage(:ended, false)
    end
    
    # Define vote function
    SmartContract.define_function(template, :vote, event: :VoteCast) do |proposal_index|
      require(!get_storage(:ended), "Voting has ended")
      require(!get_storage(:voters).include?(@caller), "Already voted")
      
      proposals = get_storage(:proposals)
      require(proposal_index < proposals.length, "Invalid proposal")
      
      # Record vote
      votes = get_storage(:votes) || {}
      votes[@caller] = proposal_index
      
      voters = get_storage(:voters)
      voters.add(@caller)
      
      set_storage(:votes, votes)
      set_storage(:voters, voters)
      
      proposal = proposals[proposal_index]
      proposal[:votes] = (proposal[:votes] || 0) + 1
      
      true
    end
    
    # Define endVoting function
    SmartContract.define_function(template, :endVoting, event: :VotingEnded) do
      require(@caller == get_storage(:owner), "Only owner can end voting")
      require(!get_storage(:ended), "Voting already ended")
      
      set_storage(:ended, true)
      
      # Calculate winner
      proposals = get_storage(:proposals)
      votes = get_storage(:votes)
      
      proposals.each do |proposal|
        proposal[:votes] = votes.values.count(proposal_index)
      end
      
      winner = proposals.max_by { |p| p[:votes] }
      
      set_storage(:winner, winner)
      
      true
    end
    
    # Define getResults function
    SmartContract.define_function(template, :getResults, view: true) do
      proposals = get_storage(:proposals)
      votes = get_storage(:votes)
      
      results = proposals.map do |proposal|
        {
          description: proposal[:description],
          votes: votes.values.count(proposals.index(proposal))
        }
      end
      
      {
        proposals: results,
        ended: get_storage(:ended),
        winner: get_storage(:winner)
      }
    end
    
    # Deploy contract
    contract.instance_variable_set(:@code, template)
    contract.deploy(template, 0)
    
    contract
  end
  
  private
  
  def self.generate_address
    "0x" + Array.new(40) { rand(16).to_s(16) }.join
  end
end
```

## 💰 Token Contracts

### 3. ERC-20 Token Implementation

Standard fungible token contract:

```ruby
class ERC20Token
  def self.create(name, symbol, decimals, initial_supply, owner)
    contract = ContractFactory.create_token_contract(name, symbol, initial_supply, owner)
    
    # Add ERC-20 specific functions
    template = contract.instance_variable_get(:@code)
    
    # Add decimals function
    SmartContract.define_function(template, :decimals, view: true) do
      decimals
    end
    
    # Add name function
    SmartContract.define_function(template, :name, view: true) do
      get_storage(:name)
    end
    
    # Add symbol function
    SmartContract.define_function(template, :symbol, view: true) do
      get_storage(:symbol)
    end
    
    # Add totalSupply function
    SmartContract.define_function(template, :totalSupply, view: true) do
      get_storage(:total_supply)
    end
    
    contract.instance_variable_set(:@code, template)
    contract
  end
  
  def self.demonstrate_erc20
    puts "ERC-20 Token Demonstration:"
    puts "=" * 50
    
    # Create token
    owner = "0x1234567890abcdef"
    token = ERC20Token.create("MyToken", "MTK", 18, 1000000, owner)
    
    puts "Token created:"
    puts "  Name: #{token.call_function(:name, nil)[:result]}"
    puts "  Symbol: #{token.call_function(:symbol, nil)[:result]}"
    puts "  Decimals: #{token.call_function(:decimals, nil)[:result]}"
    puts "  Total Supply: #{token.call_function(:totalSupply, nil)[:result]}"
    
    # Check initial balance
    initial_balance = token.call_function(:balanceOf, [owner], owner)[:result]
    puts "  Owner balance: #{initial_balance}"
    
    # Transfer tokens
    recipient = "0xabcdef1234567890"
    transfer_amount = 1000
    
    puts "\nTransferring #{transfer_amount} tokens to #{recipient}:"
    transfer_result = token.execute_function(:transfer, [recipient, transfer_amount], owner)
    
    if transfer_result[:success]
      puts "Transfer successful!"
      puts "Gas used: #{transfer_result[:gas_used]}"
      
      # Check balances
      owner_balance = token.call_function(:balanceOf, [owner], owner)[:result]
      recipient_balance = token.call_function(:balanceOf, [recipient], owner)[:result]
      
      puts "Owner balance: #{owner_balance}"
      puts "Recipient balance: #{recipient_balance}"
    else
      puts "Transfer failed: #{transfer_result[:error]}"
    end
    
    # Approve and transferFrom
    spender = "0x9876543210fedcba"
    approve_amount = 500
    
    puts "\nApproving #{approve_amount} tokens for #{spender}:"
    approve_result = token.execute_function(:approve, [spender, approve_amount], owner)
    
    if approve_result[:success]
      puts "Approval successful!"
      
      # Transfer from owner to recipient using spender
      transfer_from_amount = 200
      puts "\nTransferFrom #{transfer_from_amount} tokens from owner to #{recipient} using spender:"
      
      transfer_from_result = token.execute_function(:transferFrom, [owner, recipient, transfer_from_amount], spender)
      
      if transfer_from_result[:success]
        puts "TransferFrom successful!"
        
        # Check final balances
        owner_balance = token.call_function(:balanceOf, [owner], owner)[:result]
        recipient_balance = token.call_function(:balanceOf, [recipient], owner)[:result]
        
        puts "Owner balance: #{owner_balance}"
        puts "Recipient balance: #{recipient_balance}"
      else
        puts "TransferFrom failed: #{transfer_from_result[:error]}"
      end
    else
      puts "Approval failed: #{approve_result[:error]}"
    end
    
    # Show events
    puts "\nEvents emitted:"
    token.get_events.each do |event|
      puts "  #{event[:type]}: #{event[:data]}"
    end
    
    puts "\nERC-20 Features:"
    puts "- Standard fungible token interface"
    puts "- Transfer and approval mechanisms"
    puts "- Balance tracking"
    puts "- Event emission"
    puts "- Gas optimization"
  end
end
```

### 4. NFT Contract (ERC-721)

Non-fungible token implementation:

```ruby
class ERC721Token
  def self.create(name, symbol, owner)
    contract = SmartContract.new(generate_address, owner)
    
    template = SmartContract.create_contract_template
    
    # Define constructor
    SmartContract.define_function(template, :constructor) do
      set_storage(:name, name)
      set_storage(:symbol, symbol)
      set_storage(:owner, @owner)
      set_storage(:tokens, {})
      set_storage(:owners, {})
      set_storage(:token_approvals, {})
      set_storage(:operator_approvals, {})
      set_storage(:next_token_id, 1)
    end
    
    # Define mint function
    SmartContract.define_function(template, :mint, event: :Transfer) do |to|
      require(@caller == get_storage(:owner), "Only owner can mint")
      
      token_id = get_storage(:next_token_id)
      tokens = get_storage(:tokens) || {}
      owners = get_storage(:owners) || {}
      
      tokens[token_id] = to
      owners[to] ||= Set.new
      owners[to].add(token_id)
      
      set_storage(:tokens, tokens)
      set_storage(:owners, owners)
      set_storage(:next_token_id, token_id + 1)
      
      token_id
    end
    
    # Define balanceOf function
    SmartContract.define_function(template, :balanceOf, view: true) do |owner|
      owners = get_storage(:owners) || {}
      owners[owner]&.size || 0
    end
    
    # Define ownerOf function
    SmartContract.define_function(template, :ownerOf, view: true) do |token_id|
      tokens = get_storage(:tokens) || {}
      tokens[token_id]
    end
    
    # Define transfer function
    SmartContract.define_function(template, :transfer, event: :Transfer) do |to, token_id|
      tokens = get_storage(:tokens) || {}
      owners = get_storage(:owners) || {}
      
      from = tokens[token_id]
      require(from == @caller, "Not token owner")
      require(to != "", "Invalid recipient")
      
      # Update ownership
      tokens[token_id] = to
      owners[from].delete(token_id)
      owners[to] ||= Set.new
      owners[to].add(token_id)
      
      # Clear approvals
      token_approvals = get_storage(:token_approvals) || {}
      token_approvals.delete(token_id)
      
      set_storage(:tokens, tokens)
      set_storage(:owners, owners)
      set_storage(:token_approvals, token_approvals)
      
      true
    end
    
    # Define approve function
    SmartContract.define_function(template, :approve, event: :Approval) do |approved, token_id|
      tokens = get_storage(:tokens) || {}
      owners = get_storage(:owners) || {}
      
      owner = tokens[token_id]
      require(owner == @caller, "Not token owner")
      
      token_approvals = get_storage(:token_approvals) || {}
      token_approvals[token_id] = approved
      
      set_storage(:token_approvals, token_approvals)
      
      true
    end
    
    # Define getApproved function
    SmartContract.define_function(template, :getApproved, view: true) do |token_id|
      token_approvals = get_storage(:token_approvals) || {}
      token_approvals[token_id]
    end
    
    # Define tokensOfOwner function
    SmartContract.define_function(template, :tokensOfOwner, view: true) do |owner|
      owners = get_storage(:owners) || {}
      (owners[owner] || Set.new).to_a
    end
    
    # Deploy contract
    contract.instance_variable_set(:@code, template)
    contract.deploy(template, 0)
    
    contract
  end
  
  def self.demonstrate_erc721
    puts "ERC-721 NFT Demonstration:"
    puts "=" * 50
    
    # Create NFT contract
    owner = "0x1234567890abcdef"
    nft = ERC721Token.create("MyNFT", "MNFT", owner)
    
    puts "NFT Contract created:"
    puts "  Name: #{nft.call_function(:name, nil)[:result]}"
    puts "  Symbol: #{nft.call_function(:symbol, nil)[:result]}"
    
    # Mint NFTs
    recipients = ["0xabcdef1234567890", "0x9876543210fedcba", "0x1111222233334444"]
    
    puts "\nMinting NFTs:"
    token_ids = []
    
    recipients.each_with_index do |recipient, i|
      mint_result = nft.execute_function(:mint, [recipient], owner)
      
      if mint_result[:success]
        token_id = mint_result[:result]
        token_ids << token_id
        
        puts "  NFT #{token_id} minted for #{recipient}"
      end
    end
    
    # Check balances
    puts "\nToken balances:"
    recipients.each do |recipient|
      balance = nft.call_function(:balanceOf, [recipient], owner)[:result]
      puts "  #{recipient}: #{balance} NFTs"
    end
    
    # Transfer NFT
    if token_ids.any?
      from = recipients[0]
      to = "0x5555666677778888"
      token_id = token_ids[0]
      
      puts "\nTransferring NFT #{token_id} from #{from} to #{to}:"
      transfer_result = nft.execute_function(:transfer, [to, token_id], from)
      
      if transfer_result[:success]
        puts "Transfer successful!"
        
        # Check new balances
        from_balance = nft.call_function(:balanceOf, [from], owner)[:result]
        to_balance = nft.call_function(:balanceOf, [to], owner)[:result]
        
        puts "  #{from} balance: #{from_balance}"
        puts "  #{to} balance: #{to_balance}"
        
        # Check ownership
        new_owner = nft.call_function(:ownerOf, [token_id], owner)[:result]
        puts "  NFT #{token_id} owner: #{new_owner}"
      else
        puts "Transfer failed: #{transfer_result[:error]}"
      end
    end
    
    # Show all tokens for a specific owner
    puts "\nTokens for #{recipients[1]}:"
    tokens = nft.call_function(:tokensOfOwner, [recipients[1]], owner)[:result]
    tokens.each do |token_id|
      puts "  Token #{token_id}"
    end
    
    # Show events
    puts "\nEvents emitted:"
    nft.get_events.each do |event|
      puts "  #{event[:type]}: #{event[:data]}"
    end
    
    puts "\nERC-721 Features:"
    puts "- Non-fungible token standard"
    puts "- Unique token ownership"
    puts "- Transfer and approval mechanisms"
    puts "- Balance and ownership tracking"
    puts "- Event emission for transfers"
  end
  
  private
  
  def self.generate_address
    "0x" + Array.new(40) { rand(16).to_s(16) }.join
  end
end
```

## 🗳️ Governance Contracts

### 5. Voting Contract

Democratic decision-making contract:

```ruby
class VotingContract
  def self.create(owner, proposals)
    contract = ContractFactory.create_voting_contract(owner, proposals)
    
    # Add additional voting functions
    template = contract.instance_variable_get(:@code)
    
    # Define addProposal function
    SmartContract.define_function(template, :addProposal, event: :ProposalAdded) do |description|
      require(@caller == get_storage(:owner), "Only owner can add proposals")
      require(!get_storage(:ended), "Voting has ended")
      
      proposals = get_storage(:proposals) || []
      new_proposal = {
        id: proposals.length,
        description: description,
        votes: 0,
        created_at: Time.now
      }
      
      proposals << new_proposal
      set_storage(:proposals, proposals)
      
      new_proposal[:id]
    end
    
    # Define hasVoted function
    SmartContract.define_function(template, :hasVoted, view: true) do |voter|
      get_storage(:voters).include?(voter)
    end
    
    # Define isEnded function
    SmartContract.define_function(template, :isEnded, view: true) do
      get_storage(:ended)
    end
    
    contract.instance_variable_set(:@code, template)
    contract
  end
  
  def self.demonstrate_voting
    puts "Voting Contract Demonstration:"
    puts "=" * 50
    
    # Create proposals
    proposals = [
      { description: "Increase block size", votes: 0 },
      { description: "Lower transaction fees", votes: 0 },
      { description: "Implement new feature", votes: 0 }
    ]
    
    # Create voting contract
    owner = "0x1234567890abcdef"
    voting = VotingContract.create(owner, proposals)
    
    puts "Voting contract created:"
    puts "  Owner: #{owner}"
    puts "  Proposals: #{proposals.length}"
    
    # Show initial proposals
    puts "\nInitial proposals:"
    proposals.each_with_index do |proposal, i|
      puts "  #{i + 1}. #{proposal[:description]}"
    end
    
    # Add voters
    voters = ["0xabcdef1234567890", "0x9876543210fedcba", "0x1111222233334444", "0x5555666677778888", "0xaaaabbbbccccdddd"]
    
    # Cast votes
    puts "\nCasting votes:"
    voters.each_with_index do |voter, i|
      proposal_index = i % proposals.length
      vote_result = voting.execute_function(:vote, [proposal_index], voter)
      
      if vote_result[:success]
        puts "  #{voter} voted for proposal #{proposal_index + 1}"
      else
        puts "  Vote failed: #{vote_result[:error]}"
      end
    end
    
    # Check voting results before ending
    puts "\nVoting results (before ending):"
    results = voting.call_function(:getResults, nil, owner)[:result]
    
    results[:proposals].each_with_index do |proposal, i|
      puts "  #{i + 1}. #{proposal[:description]}: #{proposal[:votes]} votes"
    end
    
    # End voting
    puts "\nEnding voting:"
    end_result = voting.execute_function(:endVoting, [], owner)
    
    if end_result[:success]
      puts "Voting ended successfully!"
      
      # Show final results
      puts "\nFinal voting results:"
      results = voting.call_function(:getResults, nil, owner)[:result]
      
      results[:proposals].each_with_index do |proposal, i|
        status = results[:winner] && results[:winner][:description] == proposal[:description] ? "WINNER" : ""
        puts "  #{i + 1}. #{proposal[:description]}: #{proposal[:votes]} votes #{status}"
      end
      
      if results[:winner]
        puts "\nWinning proposal: #{results[:winner][:description]} with #{results[:winner][:votes]} votes"
      end
    else
      puts "Failed to end voting: #{end_result[:error]}"
    end
    
    # Show events
    puts "\nEvents emitted:"
    voting.get_events.each do |event|
      puts "  #{event[:type]}: #{event[:data]}"
    end
    
    puts "\nVoting Contract Features:"
    puts "- Democratic proposal system"
    puts "- One vote per address"
    puts "- Transparent voting results"
    puts "- Event emission for tracking"
    puts "- Owner-controlled voting period"
  end
end
```

## 🔧 Advanced Contract Features

### 6. Contract Interaction and Composability

Contract-to-contract communication:

```ruby
class ContractInteraction
  def self.create_oracle_contract(owner, data_sources)
    contract = SmartContract.new(generate_address, owner)
    
    template = SmartContract.create_contract_template
    
    # Define constructor
    SmartContract.define_function(template, :constructor) do
      set_storage(:owner, @owner)
      set_storage(:data_sources, data_sources)
      set_storage(:data, {})
      set_storage(:subscribers, Set.new)
      set_storage(:update_frequency, 60) # 60 seconds
    end
    
    # Define updateData function
    SmartContract.define_function(template, :updateData, event: :DataUpdated) do |source, value|
      require(@caller == get_storage(:owner), "Only owner can update data")
      require(get_storage(:data_sources).include?(source), "Invalid data source")
      
      data = get_storage(:data) || {}
      old_value = data[source]
      data[source] = {
        value: value,
        timestamp: Time.now,
        updated_by: @caller
      }
      
      set_storage(:data, data)
      
      # Notify subscribers
      notify_subscribers(source, value, old_value)
      
      true
    end
    
    # Define getData function
    SmartContract.define_function(template, :getData, view: true) do |source|
      data = get_storage(:data) || {}
      data[source]
    end
    
    # Define subscribe function
    SmartContract.define_function(template, :subscribe, event: :Subscribed) do
      subscribers = get_storage(:subscribers) || Set.new
      subscribers.add(@caller)
      set_storage(:subscribers, subscribers)
      
      true
    end
    
    # Define unsubscribe function
    SmartContract.define_function(template, :unsubscribe, event: :Unsubscribed) do
      subscribers = get_storage(:subscribers) || Set.new
      subscribers.delete(@caller)
      set_storage(:subscribers, subscribers)
      
      true
    end
    
    # Deploy contract
    contract.instance_variable_set(:@code, template)
    contract.deploy(template, 0)
    
    contract
  end
  
  def self.create_consumer_contract(owner, oracle_address)
    contract = SmartContract.new(generate_address, owner)
    
    template = SmartContract.create_contract_template
    
    # Define constructor
    SmartContract.define_function(template, :constructor) do
      set_storage(:owner, @owner)
      set_storage(:oracle_address, oracle_address)
      set_storage(:cached_data, {})
      set_storage(:last_update, {})
    end
    
    # define updateCache function
    SmartContract.define_function(template, :updateCache, event: :CacheUpdated) do |source|
      oracle_address = get_storage(:oracle_address)
      
      # In practice, this would make a contract call to the oracle
      # For demonstration, we'll simulate the call
      puts "Calling oracle at #{oracle_address} for data from #{source}"
      
      # Simulate oracle response
      oracle_data = {
        value: rand(1000),
        timestamp: Time.now,
        updated_by: "oracle"
      }
      
      cached_data = get_storage(:cached_data) || {}
      old_data = cached_data[source]
      
      cached_data[source] = oracle_data
      set_storage(:cached_data, cached_data)
      
      last_update = get_storage(:last_update) || {}
      last_update[source] = Time.now
      set_storage(:last_update, last_update)
      
      true
    end
    
    # Define getCachedData function
    SmartContract.define_function(template, :getCachedData, view: true) do |source|
      cached_data = get_storage(:cached_data) || {}
      cached_data[source]
    end
    
    # Deploy contract
    contract.instance_variable_set(:@code, template)
    contract.deploy(template, 0)
    
    contract
  end
  
  def self.demonstrate_interaction
    puts "Contract Interaction Demonstration:"
    puts "=" * 50
    
    # Create oracle contract
    owner = "0x1234567890abcdef"
    data_sources = ["price_feed", "weather_data", "sports_scores"]
    
    oracle = create_oracle_contract(owner, data_sources)
    
    puts "Oracle contract created:"
    puts "  Address: #{oracle.address}"
    puts "  Data sources: #{data_sources.join(', ')}"
    
    # Create consumer contracts
    consumers = []
    3.times do |i|
      consumer = create_consumer_contract("consumer#{i}", oracle.address)
      consumers << consumer
      
      puts "Consumer contract #{i + 1} created at #{consumer.address}"
    end
    
    # Update oracle data
    puts "\nUpdating oracle data:"
    data_sources.each do |source|
      value = rand(1000)
      update_result = oracle.execute_function(:updateData, [source, value], owner)
      
      if update_result[:success]
        puts "  #{source}: #{value}"
      end
    end
    
    # Subscribe consumers to oracle
    puts "\nSubscribing consumers to oracle:"
    consumers.each_with_index do |consumer, i|
      subscribe_result = consumer.execute_function(:subscribe, [], "consumer#{i}")
      
      if subscribe_result[:success]
        puts "  Consumer #{i + 1} subscribed"
      end
    end
    
    # Update consumer caches
    puts "\nUpdating consumer caches:"
    consumers.each_with_index do |consumer, i|
      data_sources.each do |source|
        update_result = consumer.execute_function(:updateCache, [source], "consumer#{i}")
        
        if update_result[:success]
          puts "  Consumer #{i + 1}: #{source} cache updated"
        end
      end
    end
    
    # Get cached data
    puts "\nCached data from consumers:"
    consumers.each_with_index do |consumer, i|
      puts "  Consumer #{i + 1}:"
      
      data_sources.each do |source|
        cached_data = consumer.call_function(:getCachedData, [source], "consumer#{i}")[:result]
        
        if cached_data
          puts "    #{source}: #{cached_data[:value]} (updated at #{cached_data[:timestamp]})"
        else
          puts "    #{source}: No data"
        end
      end
    end
    
    # Show events
    puts "\nEvents from oracle:"
    oracle.get_events.each do |event|
      puts "  #{event[:type]}: #{event[:data]}"
    end
    
    puts "\nContract Interaction Features:"
    puts "- Contract-to-contract communication"
    puts "- Oracle pattern for external data"
    puts "- Publisher-subscriber pattern"
    puts "- Data caching mechanisms"
    puts "- Event-driven architecture"
  end
  
  private
  
  def self.generate_address
    "0x" + Array.new(40) { rand(16).to_s(16) }.join
  end
end
```

## 🎯 Security and Best Practices

### 7. Security Patterns

Common security vulnerabilities and patterns:

```ruby
class SecurityPatterns
  def self.demonstrate_security_patterns
    puts "Smart Contract Security Patterns:"
    puts "=" * 50
    
    patterns = [
      {
        pattern: "Checks-Effects-Interactions",
        description: "Perform checks first, then effects, then external calls",
        example: "Check balance → Update state → Call external contract",
        prevents: "Reentrancy attacks"
      },
      {
        pattern: "Reentrancy Guard",
        description: "Prevent recursive calls to sensitive functions",
        example: "Use mutex or state variable to track execution",
        prevents: "Reentrancy attacks"
      },
      {
        pattern: "Input Validation",
        description: "Validate all external inputs",
        example: "Check addresses, amounts, array bounds",
        prevents: "Invalid state, overflow attacks"
      },
      {
        pattern: "Access Control",
        description: "Restrict function access based on permissions",
        example: "Only owner, only modifier, role-based access",
        prevents: "Unauthorized actions"
      },
      {
        pattern: "Integer Overflow Protection",
        description: "Prevent integer overflow/underflow",
        example: "Use SafeMath libraries, check before operations",
        prevents: "Overflow attacks"
      },
      {
        pattern: "Pull Over Push",
        description: "Let users withdraw funds rather than pushing",
        example: "Withdrawal pattern vs direct transfers",
        prevents: "Gas limit issues, reentrancy"
      }
    ]
    
    patterns.each do |pattern|
      puts "#{pattern[:pattern]}:"
      puts "  Description: #{pattern[:description]}"
      puts "  Example: #{pattern[:example]}"
      puts "  Prevents: #{pattern[:prevents]}"
      puts
    end
  end
  
  def self.common_vulnerabilities
    puts "\nCommon Smart Contract Vulnerabilities:"
    puts "=" * 50
    
    vulnerabilities = [
      {
        vulnerability: "Reentrancy",
        description: "Recursive calls before state update",
        example: "The DAO hack ($50M loss)",
        prevention: "Checks-Effects-Interactions pattern"
      },
      {
        vulnerability: "Integer Overflow/Underflow",
        description: "Arithmetic operations exceed bounds",
        example: "Token supply manipulation",
        prevention: "SafeMath, input validation"
      },
      {
        vulnerability: "Untrusted External Calls",
        description: "Calling untrusted contracts",
        example: "Malicious contract stealing funds",
        prevention: "Access control, reentrancy guards"
      },
      {
        vulnerability: "Front-Running",
        description: "Ordering transactions based on gas price",
        example: "MEV attacks, sandwich attacks",
        prevention: "Commit-reveal schemes"
      },
      {
        vulnerability: "Denial of Service",
        description: "Exhausting gas or storage",
        example: "Unbounded loops, array operations",
        prevention: "Gas limits, input validation"
      },
      {
        vulnerability: "Bad Randomness",
        description: "Predictable random number generation",
        example: "Lottery manipulation",
        prevention: "Chainlink VRF, commit-reveal"
      }
    ]
    
    vulnerabilities.each do |vuln|
      puts "#{vuln[:vulnerability]}:"
      puts "  Description: #{vuln[:description]}"
      puts "  Example: #{vuln[:example]}"
      puts "  Prevention: #{vuln[:prevention]}"
      puts
    end
  end
  
  def self.audit_checklist
    puts "\nSmart Contract Audit Checklist:"
    puts "=" * 50
    
    checklist = [
      "✓ Input validation for all external inputs",
      "✓ Access control for sensitive functions",
      "✓ Reentrancy protection on state-changing functions",
      "✓ Integer overflow/underflow protection",
      "✓ Gas optimization and limit considerations",
      "✓ Event emission for important state changes",
      "✓ Proper error handling and revert messages",
      "✓ Comprehensive test coverage",
      "✓ Documentation of contract logic",
      "✓ External call safety (pull over push)",
      "✓ Time-dependent operation protection",
      "✓ Upgrade mechanism (if applicable)",
      "✓ Emergency stop/pause functionality"
    ]
    
    checklist.each { |item| puts item }
    
    puts "\nAudit Process:"
    puts "1. Code Review - Manual review of source code"
    puts "2. Static Analysis - Automated security scanning"
    puts "3. Formal Verification - Mathematical proofs"
    puts "4. Penetration Testing - Active security testing"
    puts "5. Bug Bounty - Community security testing"
  end
  
  # Run all security demonstrations
  demonstrate_security_patterns
  common_vulnerabilities
  audit_checklist
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Contract**: Create simple storage contract
2. **Token Contract**: Implement basic ERC-20 token
3. **Function Calls**: Understand contract execution
4. **Event Handling**: Implement event emission

### Intermediate Exercises

1. **NFT Contract**: Build ERC-721 implementation
2. **Voting System**: Create governance contract
3. **Contract Interaction**: Implement contract-to-contract calls
4. **Security Patterns**: Apply security best practices

### Advanced Exercises

1. **DeFi Protocol**: Build decentralized exchange
2. **Oracle System**: Create data oracle contract
3. **Upgradeable Contracts**: Implement proxy patterns
4. **Security Audit**: Perform comprehensive security analysis

---

## 🎯 Summary

Smart Contracts in Ruby provide:

- **Contract Fundamentals** - Core concepts and lifecycle
- **Contract Framework** - Basic execution engine
- **Token Contracts** - ERC-20 and ERC-721 implementations
- **Governance Contracts** - Voting and decision-making systems
- **Contract Interaction** - Inter-contract communication
- **Security Patterns** - Best practices and vulnerability prevention
- **Practical Examples** - Working contract implementations

Master these smart contract concepts for blockchain development!
