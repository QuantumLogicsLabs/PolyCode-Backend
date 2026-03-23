# DApps Development in Ruby
# Comprehensive guide to decentralized application development

## 🌐 DApp Fundamentals

### 1. DApp Architecture

Decentralized application concepts:

```ruby
class DAppFundamentals
  def self.explain_dapp_concepts
    puts "Decentralized Application Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "DApp Architecture",
        description: "Application architecture with decentralized components",
        layers: ["Frontend", "Smart Contracts", "Blockchain", "Storage"],
        characteristics: ["No single point of failure", "Transparent", "Censorship-resistant"]
      },
      {
        concept: "Frontend Layer",
        description: "User interface for interacting with blockchain",
        technologies: ["Web3.js", "Ethers.js", "WalletConnect", "MetaMask"],
        responsibilities: ["User interaction", "Wallet connection", "Transaction signing"]
      },
      {
        concept: "Smart Contract Layer",
        description: "Business logic deployed on blockchain",
        components: ["Contract code", "State management", "Event handling"],
        features: ["Immutable logic", "Transparent execution", "Event emission"]
      },
      {
        concept: "Storage Layer",
        description: "Decentralized storage for application data",
        solutions: ["IPFS", "Arweave", "Filecoin", "Swarm"],
        benefits: ["Permanent storage", "Content addressing", "Distributed access"]
      },
      {
        concept: "Blockchain Layer",
        description: "Underlying blockchain network",
        networks: ["Ethereum", "Polygon", "BSC", "Arbitrum"],
        provides: ["Consensus", "Security", "Transaction finality"]
      },
      {
        concept: "Wallet Integration",
        description: "User wallet management and transaction signing",
        wallets: ["MetaMask", "WalletConnect", "Coinbase Wallet", "Phantom"],
        functions: ["Account management", "Transaction signing", "Network switching"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Layers: #{concept[:layers].join(', ')}" if concept[:layers]
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Technologies: #{concept[:technologies].join(', ')}" if concept[:technologies]
      puts "  Responsibilities: #{concept[:responsibilities].join(', ')}" if concept[:responsibilities]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Solutions: #{concept[:solutions].join(', ')}" if concept[:solutions]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Networks: #{concept[:networks].join(', ')}" if concept[:networks]
      puts "  Provides: #{concept[:provides].join(', ')}" if concept[:provides]
      puts "  Wallets: #{concept[:wallets].join(', ')}" if concept[:wallets]
      puts "  Functions: #{concept[:functions].join(', ')}" if concept[:functions]
      puts
    end
  end
  
  def self.dapp_vs_traditional
    puts "\nDApps vs Traditional Applications:"
    puts "=" * 50
    
    comparison = [
      {
        aspect: "Architecture",
        traditional: "Client-server with central database",
        dapp: "Peer-to-peer with blockchain"
      },
      {
        aspect: "Data Storage",
        traditional: "Centralized database",
        dapp: "Distributed blockchain/storage"
      },
      {
        aspect: "Trust Model",
        traditional: "Trust in service provider",
        dapp: "Trust in code and consensus"
      },
      {
        aspect: "Downtime",
        traditional: "Single point of failure",
        dapp: "No single point of failure"
      },
      {
        aspect: "Censorship",
        traditional: "Provider can censor content",
        dapp: "Censorship-resistant"
      },
      {
        aspect: "Updates",
        traditional: "Provider can update anytime",
        dapp: "Immutable unless upgradeable"
      },
      {
        aspect: "Cost",
        traditional: "Server and maintenance costs",
        dapp: "Gas fees and development costs"
      },
      {
        aspect: "User Control",
        traditional: "Provider controls user data",
        dapp: "User controls own data"
      }
    ]
    
    comparison.each do |item|
      puts "#{item[:aspect]}:"
      puts "  Traditional: #{item[:traditional]}"
      puts "  DApp: #{item[:dapp]}"
      puts
    end
  end
  
  def self.dapp_categories
    puts "\nDApp Categories:"
    puts "=" * 50
    
    categories = [
      {
        category: "DeFi (Decentralized Finance)",
        description: "Financial applications on blockchain",
        examples: ["Uniswap", "Aave", "Compound", "MakerDAO"],
        features: ["Lending", "Trading", "Yield farming", "Stablecoins"]
      },
      {
        category: "NFT Marketplaces",
        description: "Platforms for trading digital collectibles",
        examples: ["OpenSea", "Rarible", "Foundation", "SuperRare"],
        features: ["NFT trading", "Creator royalties", "Collections"]
      },
      {
        category: "Gaming DApps",
        description: "Blockchain-based gaming platforms",
        examples: ["Axie Infinity", "The Sandbox", "Gods Unchained", "CryptoKitties"],
        features: ["Play-to-earn", "NFT assets", "Tournaments"]
      },
      {
        category: "Social DApps",
        description: "Decentralized social platforms",
        examples: ["Lens Protocol", "Mirror", "Audius", "Peepeth"],
        features: ["Content creation", "Social tokens", "Decentralized identity"]
      },
      {
        category: "Infrastructure",
        description: "Tools and platforms for DApp development",
        examples: ["Infura", "The Graph", "Chainlink", "IPFS"],
        features: ["Node services", "Indexing", "Oracles", "Storage"]
      },
      {
        category: "DAOs",
        description: "Decentralized autonomous organizations",
        examples: ["MakerDAO", "Uniswap DAO", "Ethereum Name Service"],
        features: ["Governance", "Treasury management", "Voting"]
      }
    ]
    
    categories.each do |category|
      puts "#{category[:category]}:"
      puts "  Description: #{category[:description]}"
      puts "  Examples: #{category[:examples].join(', ')}"
      puts "  Features: #{category[:features].join(', ')}"
      puts
    end
  end
  
  # Run DApp fundamentals
  explain_dapp_concepts
  dapp_vs_traditional
  dapp_categories
end
```

### 2. Web3 Integration

Blockchain connection and interaction:

```ruby
class Web3Integration
  def self.create_web3_provider(network = "ethereum")
    puts "Creating Web3 Provider for #{network}:"
    puts "=" * 40
    
    provider = {
      network: network,
      rpc_url: get_rpc_url(network),
      chain_id: get_chain_id(network),
      block_time: get_block_time(network),
      gas_price: get_gas_price(network)
    }
    
    puts "Network: #{provider[:network]}"
    puts "RPC URL: #{provider[:rpc_url]}"
    puts "Chain ID: #{provider[:chain_id]}"
    puts "Block time: #{provider[:block_time]}s"
    puts "Gas price: #{provider[:gas_price]} gwei"
    
    provider
  end
  
  def self.connect_wallet(wallet_type = "metamask")
    puts "Connecting to #{wallet_type}:"
    puts "=" * 40
    
    # Simulate wallet connection
    connection = {
      wallet_type: wallet_type,
      address: generate_wallet_address,
      chain_id: 1,
      connected: true,
      accounts: generate_accounts(3)
    }
    
    puts "Wallet type: #{connection[:wallet_type]}"
    puts "Connected: #{connection[:connected]}"
    puts "Chain ID: #{connection[:chain_id]}"
    puts "Accounts: #{connection[:accounts].length}"
    
    connection[:accounts].each_with_index do |account, i|
      puts "  Account #{i + 1}: #{account}"
    end
    
    connection
  end
  
  def self.send_transaction(provider, wallet, to, value, data = nil)
    puts "Sending transaction:"
    puts "=" * 40
    
    transaction = {
      from: wallet[:accounts].first,
      to: to,
      value: value,
      data: data || "0x",
      gas_limit: estimate_gas(data),
      gas_price: provider[:gas_price],
      nonce: get_nonce(wallet[:accounts].first),
      chain_id: provider[:chain_id]
    }
    
    puts "From: #{transaction[:from]}"
    puts "To: #{transaction[:to]}"
    puts "Value: #{transaction[:value]} ETH"
    puts "Gas limit: #{transaction[:gas_limit]}"
    puts "Gas price: #{transaction[:gas_price]} gwei"
    puts "Nonce: #{transaction[:nonce]}"
    
    # Simulate transaction signing
    signed_transaction = sign_transaction(transaction, wallet)
    
    puts "\nTransaction signed:"
    puts "Hash: #{signed_transaction[:hash]}"
    puts "Signature: #{signed_transaction[:signature][0...16]}..."
    
    # Simulate transaction submission
    tx_hash = submit_transaction(signed_transaction)
    
    puts "\nTransaction submitted:"
    puts "Transaction hash: #{tx_hash}"
    
    # Wait for confirmation
    receipt = wait_for_confirmation(tx_hash, provider)
    
    if receipt[:success]
      puts "\nTransaction confirmed!"
      puts "Block number: #{receipt[:block_number]}"
      puts "Gas used: #{receipt[:gas_used]}"
      puts "Status: #{receipt[:status]}"
    else
      puts "\nTransaction failed!"
      puts "Error: #{receipt[:error]}"
    end
    
    receipt
  end
  
  def self.interact_with_contract(provider, wallet, contract_address, function_name, args = [])
    puts "Interacting with contract:"
    puts "=" * 40
    
    # Get contract ABI (simplified)
    abi = get_contract_abi(function_name)
    
    # Encode function call
    encoded_data = encode_function_call(function_name, args, abi)
    
    puts "Contract: #{contract_address}"
    puts "Function: #{function_name}"
    puts "Arguments: #{args.join(', ')}"
    puts "Encoded data: #{encoded_data[0...32]}..."
    
    # Send transaction
    receipt = send_transaction(provider, wallet, contract_address, 0, encoded_data)
    
    if receipt[:success]
      # Decode result (simplified)
      result = decode_transaction_result(receipt, abi)
      
      puts "\nFunction executed successfully!"
      puts "Result: #{result}"
      
      # Listen for events
      events = get_contract_events(receipt, contract_address)
      
      if events.any?
        puts "\nEvents emitted:"
        events.each do |event|
          puts "  #{event[:name]}: #{event[:args]}"
        end
      end
    end
    
    receipt
  end
  
  def self.demonstrate_web3_integration
    puts "Web3 Integration Demonstration:"
    puts "=" * 50
    
    # Create provider
    provider = create_web3_provider("ethereum")
    
    # Connect wallet
    wallet = connect_wallet("metamask")
    
    # Send simple transfer
    puts "\nSending ETH transfer:"
    to_address = "0xabcdef1234567890abcdef1234567890abcdef12345678"
    transfer_receipt = send_transaction(provider, wallet, to_address, "0.1")
    
    # Interact with contract
    puts "\nInteracting with ERC-20 token:"
    token_address = "0x1234567890abcdef1234567890abcdef12345678"
    token_function = "transfer"
    token_args = [to_address, "100000000000000000000"] # 100 tokens
    
    contract_receipt = interact_with_contract(provider, wallet, token_address, token_function, token_args)
    
    puts "\nWeb3 Integration Features:"
    puts "- Provider connection to blockchain networks"
    puts "- Wallet integration and transaction signing"
    puts "- Contract interaction and function calls"
    puts "- Event listening and decoding"
    puts "- Transaction monitoring and confirmation"
  end
  
  private
  
  def self.get_rpc_url(network)
    urls = {
      "ethereum" => "https://mainnet.infura.io/v3/YOUR_PROJECT_ID",
      "polygon" => "https://polygon-rpc.com",
      "bsc" => "https://bsc-dataseed.binance.org",
      "arbitrum" => "https://arb1.arbitrum.io/rpc"
    }
    urls[network] || "https://mainnet.infura.io/v3/YOUR_PROJECT_ID"
  end
  
  def self.get_chain_id(network)
    chain_ids = {
      "ethereum" => 1,
      "polygon" => 137,
      "bsc" => 56,
      "arbitrum" => 42161
    }
    chain_ids[network] || 1
  end
  
  def self.get_block_time(network)
    block_times = {
      "ethereum" => 12,
      "polygon" => 2,
      "bsc" => 3,
      "arbitrum" => 0.75
    }
    block_times[network] || 12
  end
  
  def self.get_gas_price(network)
    gas_prices = {
      "ethereum" => 20,
      "polygon" => 30,
      "bsc" => 5,
      "arbitrum" => 0.1
    }
    gas_prices[network] || 20
  end
  
  def self.generate_wallet_address
    "0x" + Array.new(40) { rand(16).to_s(16) }.join
  end
  
  def self.generate_accounts(count)
    (1..count).map { generate_wallet_address }
  end
  
  def self.estimate_gas(data)
    # Simplified gas estimation
    base_gas = 21000
    data_gas = data ? data.length / 2 : 0
    base_gas + data_gas
  end
  
  def self.get_nonce(address)
    # Simplified nonce calculation
    address.hash % 1000000
  end
  
  def self.sign_transaction(transaction, wallet)
    # Simplified transaction signing
    {
      hash: "0x" + Array.new(64) { rand(16).to_s(16) }.join,
      signature: ["0x" + Array.new(65) { rand(16).to_s(16) }.join, 27]
    }
  end
  
  def self.submit_transaction(signed_transaction)
    # Simulate transaction submission
    tx_hash = "0x" + Array.new(64) { rand(16).to_s(16) }.join
    
    # Simulate random success/failure
    success = rand > 0.1 # 90% success rate
    
    {
      tx_hash: tx_hash,
      success: success,
      error: success ? nil : "Insufficient gas"
    }
  end
  
  def self.wait_for_confirmation(tx_hash, provider)
    # Simulate waiting for confirmation
    sleep(1)
    
    if rand > 0.1 # 90% success rate
      {
        success: true,
        block_number: Time.now.to_i,
        gas_used: rand(50000..100000),
        status: 1
      }
    else
      {
        success: false,
        error: "Transaction failed"
      }
    end
  end
  
  def self.get_contract_abi(function_name)
    # Simplified ABI
    {
      "transfer" => {
        "type" => "function",
        "name" => "transfer",
        "inputs" => [{"name" => "to", "type" => "address"}, {"name" => "amount", "type" => "uint256"}],
        "outputs" => [{"name" => "success", "type" => "bool"}]
      }
    }[function_name] || {}
  end
  
  def self.encode_function_call(function_name, args, abi)
    # Simplified encoding
    "0x" + function_name + args.map(&:to_s).join(',')
  end
  
  def self.decode_transaction_result(receipt, abi)
    # Simplified decoding
    { success: true, result: "Transaction executed" }
  end
  
  def self.get_contract_events(receipt, contract_address)
    # Simplified event generation
    [
      {
        name: "Transfer",
        args: {
          from: "0x1234567890abcdef",
          to: "0xabcdef1234567890",
          value: "100000000000000000000"
        }
      }
    ]
  end
end
```

## 🎨 Frontend Development

### 3. React-like DApp Frontend

Web interface for DApp interaction:

```ruby
class DAppFrontend
  def self.create_dapp_frontend
    puts "Creating DApp Frontend:"
    puts "=" * 50
    
    frontend = {
      name: "MyDApp",
      description: "Decentralized application frontend",
      components: [
        "WalletConnector",
        "ContractInteractor",
        "TransactionManager",
        "EventListener",
        "UIComponents"
      ],
      technologies: [
        "React/Vue/Angular",
        "Web3.js/Ethers.js",
        "MetaMask/WalletConnect",
        "IPFS/Web3Storage"
      ]
    }
    
    puts "Frontend: #{frontend[:name]}"
    puts "Description: #{frontend[:description]}"
    puts "Components: #{frontend[:components].join(', ')}"
    puts "Technologies: #{frontend[:technologies].join(', ')}"
    
    frontend
  end
  
  def self.wallet_connector_component
    puts "\nWallet Connector Component:"
    puts "=" * 50
    
    component_code = <<~JAVASCRIPT
      import { useState, useEffect } from 'react';
      import { ethers } from 'ethers';
      
      const WalletConnector = ({ onConnect, onDisconnect }) => {
        const [account, setAccount] = useState(null);
        const [provider, setProvider] = useState(null);
        const [chainId, setChainId] = useState(null);
        const [isConnecting, setIsConnecting] = useState(false);
        
        useEffect(() => {
          checkConnection();
        }, []);
        
        const checkConnection = async () => {
          if (window.ethereum) {
            try {
              const accounts = await window.ethereum.request({ 
                method: 'eth_accounts' 
              });
              
              if (accounts.length > 0) {
                connectWallet();
              }
            } catch (error) {
              console.error('Error checking connection:', error);
            }
          }
        };
        
        const connectWallet = async () => {
          if (!window.ethereum) {
            alert('Please install MetaMask!');
            return;
          }
          
          setIsConnecting(true);
          
          try {
            // Request account access
            const accounts = await window.ethereum.request({
              method: 'eth_requestAccounts'
            });
            
            // Create provider
            const provider = new ethers.providers.Web3Provider(window.ethereum);
            const signer = provider.getSigner();
            const network = await provider.getNetwork();
            
            setAccount(accounts[0]);
            setProvider(provider);
            setChainId(network.chainId);
            
            onConnect({
              account: accounts[0],
              provider,
              signer,
              chainId: network.chainId
            });
            
          } catch (error) {
            console.error('Error connecting wallet:', error);
            alert('Failed to connect wallet');
          } finally {
            setIsConnecting(false);
          }
        };
        
        const disconnectWallet = () => {
          setAccount(null);
          setProvider(null);
          setChainId(null);
          onDisconnect();
        };
        
        const switchNetwork = async (targetChainId) => {
          try {
            await window.ethereum.request({
              method: 'wallet_switchEthereumChain',
              params: [{ chainId: targetChainId }]
            });
          } catch (error) {
            console.error('Error switching network:', error);
          }
        };
        
        return (
          <div className="wallet-connector">
            {account ? (
              <div className="wallet-info">
                <span className="account">
                  {account.slice(0, 6)}...{account.slice(-4)}
                </span>
                <span className="chain-id">Chain: {chainId}</span>
                <button onClick={disconnectWallet}>Disconnect</button>
                <button onClick={() => switchNetwork(1)}>Ethereum</button>
                <button onClick={() => switchNetwork(137)}>Polygon</button>
              </div>
            ) : (
              <button 
                onClick={connectWallet} 
                disabled={isConnecting}
                className="connect-button"
              >
                {isConnecting ? 'Connecting...' : 'Connect Wallet'}
              </button>
            )}
          </div>
        );
      };
      
      export default WalletConnector;
    JAVASCRIPT
    
    puts "Wallet Connector Component:"
    puts "- Connects to MetaMask/WalletConnect"
    puts "- Manages account and network state"
    "- Handles connection/disconnection"
    "- Supports network switching"
    puts "\nCode preview:"
    puts component_code[0...500] + "..."
    
    component_code
  end
  
  def self.contract_interactor_component
    puts "\nContract Interactor Component:"
    puts "=" * 50
    
    component_code = <<~JAVASCRIPT
      import { useState, useEffect } from 'react';
      import { ethers } from 'ethers';
      
      const ContractInteractor = ({ 
        provider, 
        contractAddress, 
        abi, 
        account 
      }) => {
        const [contract, setContract] = useState(null);
        const [loading, setLoading] = useState(false);
        const [error, setError] = useState(null);
        
        useEffect(() => {
          if (provider && contractAddress && abi) {
            const contractInstance = new ethers.Contract(
              contractAddress,
              abi,
              provider
            );
            setContract(contractInstance);
          }
        }, [provider, contractAddress, abi]);
        
        const callFunction = async (functionName, args = [], value = 0) => {
          if (!contract) {
            setError('Contract not initialized');
            return null;
          }
          
          setLoading(true);
          setError(null);
          
          try {
            let result;
            
            if (value > 0) {
              // Send transaction
              const signer = provider.getSigner();
              const contractWithSigner = contract.connect(signer);
              
              const tx = await contractWithSigner[functionName](...args, {
                value: ethers.utils.parseEther(value.toString())
              });
              
              const receipt = await tx.wait();
              result = {
                success: true,
                receipt,
                hash: tx.hash
              };
            } else {
              // Call function (read-only)
              result = await contract[functionName](...args);
            }
            
            return result;
            
          } catch (err) {
            setError(err.message);
            return { success: false, error: err.message };
          } finally {
            setLoading(false);
          }
        };
        
        const listenToEvents = (eventName, callback) => {
          if (!contract) return;
          
          contract.on(eventName, (...args) => {
            callback(args);
          });
          
          return () => {
            contract.off(eventName, callback);
          };
        };
        
        return {
          contract,
          loading,
          error,
          callFunction,
          listenToEvents
        };
      };
      
      export default ContractInteractor;
    JAVASCRIPT
    
    puts "Contract Interactor Component:"
    puts "- Interacts with smart contracts"
    "- Handles function calls and transactions"
    "- Manages loading and error states"
    "- Provides event listening"
    puts "\nCode preview:"
    puts component_code[0...500] + "..."
    
    component_code
  end
  
  def self.transaction_manager_component
    puts "\nTransaction Manager Component:"
    puts "=" * 50
    
    component_code = <<~JAVASCRIPT
      import { useState, useEffect } from 'react';
      
      const TransactionManager = ({ provider }) => {
        const [transactions, setTransactions] = useState([]);
        const [pendingTransactions, setPendingTransactions] = useState(new Set());
        
        const addTransaction = (tx) => {
          const newTransaction = {
            hash: tx.hash,
            from: tx.from,
            to: tx.to,
            value: tx.value,
            status: 'pending',
            timestamp: Date.now(),
            gasUsed: null,
            blockNumber: null
          };
          
          setTransactions(prev => [newTransaction, ...prev]);
          setPendingTransactions(prev => new Set(prev).add(tx.hash));
          
          // Wait for confirmation
          waitForConfirmation(tx.hash);
        };
        
        const waitForConfirmation = async (txHash) => {
          try {
            const receipt = await provider.waitForTransaction(txHash, 1);
            
            setTransactions(prev => 
              prev.map(tx => 
                tx.hash === txHash 
                  ? { ...tx, status: 'confirmed', receipt, 
                      gasUsed: receipt.gasUsed.toNumber(),
                      blockNumber: receipt.blockNumber }
                  : tx
              )
            );
            
            setPendingTransactions(prev => {
              const newSet = new Set(prev);
              newSet.delete(txHash);
              return newSet;
            });
            
          } catch (error) {
            setTransactions(prev => 
              prev.map(tx => 
                tx.hash === txHash 
                  ? { ...tx, status: 'failed', error: error.message }
                  : tx
              )
            );
            
            setPendingTransactions(prev => {
              const newSet = new Set(prev);
              newSet.delete(txHash);
              return newSet;
            });
          }
        };
        
        const getTransactionStatus = (txHash) => {
          const transaction = transactions.find(tx => tx.hash === txHash);
          return transaction ? transaction.status : 'unknown';
        };
        
        const getTransactionsByStatus = (status) => {
          return transactions.filter(tx => tx.status === status);
        };
        
        return {
          transactions,
          pendingTransactions,
          addTransaction,
          getTransactionStatus,
          getTransactionsByStatus
        };
      };
      
      export default TransactionManager;
    JAVASCRIPT
    
    puts "Transaction Manager Component:"
    puts "- Tracks transaction status"
    "- Waits for confirmations"
    "- Manages pending transactions"
    "- Provides transaction history"
    puts "\nCode preview:"
    puts component_code[0...500] + "..."
    
    component_code
  end
  
  def self.demonstrate_frontend
    puts "DApp Frontend Development:"
    puts "=" * 50
    
    # Create frontend structure
    frontend = create_dapp_frontend
    
    # Show components
    wallet_connector_component
    contract_interactor_component
    transaction_manager_component
    
    puts "\nFrontend Development Features:"
    puts "- React/Vue/Angular integration"
    puts "- Web3.js/Ethers.js blockchain interaction"
    puts "- Wallet connection management"
    puts "- Contract interaction patterns"
    puts "- Transaction status tracking"
    puts "- Event handling and UI updates"
    puts "- Responsive design principles"
    puts "- Error handling and user feedback"
  end
end
```

## 🎯 Real-World DApp Examples

### 4. DeFi DApp Implementation

Decentralized finance application:

```ruby
class DeFiDApp
  def self.create_defi_dapp
    puts "Creating DeFi DApp:"
    puts "=" * 50
    
    defi_dapp = {
      name: "DeFiSwap",
      description: "Decentralized exchange and liquidity protocol",
      features: [
        "Token swapping",
        "Liquidity pools",
        "Yield farming",
        "Staking rewards",
        "Governance token"
      ],
      contracts: [
        "Router",
        "Factory",
        "Pair",
        "ERC20 Token",
        "Governance Token"
      ],
      user_flows: [
        "Swap tokens",
        "Add liquidity",
        "Remove liquidity",
        "Stake tokens",
        "Vote on proposals"
      ]
    }
    
    puts "DeFi DApp: #{defi_dapp[:name]}"
    puts "Description: #{defi_dapp[:description]}"
    puts "Features: #{defi_dapp[:features].join(', ')}"
    puts "Contracts: #{defi_dapp[:contracts].join(', ')}"
    puts "User flows: #{defi_dapp[:user_flows].join(', ')}"
    
    defi_dapp
  end
  
  def self.token_swap_logic
    puts "\nToken Swap Logic:"
    puts "=" * 50
    
    swap_logic = <<~JAVASCRIPT
      class TokenSwap {
        constructor(router, factory) {
          this.router = router;
          this.factory = factory;
          this.slippageTolerance = 0.005; // 0.5%
        }
        
        async swapTokens(tokenA, tokenB, amountA, amountAMin, deadline) {
          try {
            // Get pair
            const pair = await this.factory.getPair(tokenA, tokenB);
            
            if (pair.address === ethers.constants.AddressZero) {
              throw new Error('Pair does not exist');
            }
            
            // Get reserves
            const reserves = await pair.getReserves();
            const reserveA = reserves[0];
            const reserveB = reserves[1];
            
            // Calculate amount out
            const amountB = this.getAmountOut(amountA, reserveA, reserveB);
            
            // Apply slippage tolerance
            const amountBMin = amountB.mul(1 - this.slippageTolerance);
            
            // Check deadline
            if (Date.now() / 1000 > deadline) {
              throw new Error('Transaction expired');
            }
            
            // Approve token transfer
            const tokenAContract = new ethers.Contract(
              tokenA,
              ERC20_ABI,
              this.router.provider
            );
            
            const approveTx = await tokenAContract.approve(
              this.router.address,
              amountA
            );
            await approveTx.wait();
            
            // Execute swap
            const swapTx = await this.router.swapExactTokensForTokens(
              amountA,
              amountBMin,
              [tokenA, tokenB],
              this.router.address,
              deadline
            );
            
            const receipt = await swapTx.wait();
            
            return {
              success: true,
              amountIn: amountA,
              amountOut: amountB,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        getAmountOut(amountIn, reserveIn, reserveOut) {
          // Uniswap V2 formula
          const amountInWithFee = amountIn.mul(997);
          const numerator = amountInWithFee.mul(reserveOut);
          const denominator = reserveIn.mul(1000).add(amountInWithFee);
          
          return numerator.div(denominator);
        }
        
        async getSwapRate(tokenA, tokenB, amountA) {
          try {
            const pair = await this.factory.getPair(tokenA, tokenB);
            
            if (pair.address === ethers.constants.AddressZero) {
              return { rate: 0, exists: false };
            }
            
            const reserves = await pair.getReserves();
            const reserveA = reserves[0];
            const reserveB = reserves[1];
            
            const amountB = this.getAmountOut(amountA, reserveA, reserveB);
            const rate = amountB.div(amountA);
            
            return {
              rate,
              exists: true,
              reserveA,
              reserveB
            };
            
          } catch (error) {
            return { rate: 0, exists: false, error: error.message };
          }
        }
      }
      
      export default TokenSwap;
    JAVASCRIPT
    
    puts "Token Swap Logic:"
    puts "- Calculates optimal swap amounts"
    puts "- Applies slippage tolerance"
    puts "- Handles token approvals"
    puts "- Executes router swap"
    puts "- Provides swap rate calculation"
    puts "\nCode preview:"
    puts swap_logic[0...500] + "..."
    
    swap_logic
  end
  
  def self.liquidity_pool_logic
    puts "\nLiquidity Pool Logic:"
    puts "=" * 50
    
    pool_logic = <<~JAVASCRIPT
      class LiquidityPool {
        constructor(factory, router) {
          this.factory = factory;
          this.router = router;
        }
        
        async addLiquidity(tokenA, tokenB, amountA, amountBMin, amountB, deadline) {
          try {
            // Create pair if it doesn't exist
            const pair = await this.factory.getPair(tokenA, tokenB);
            
            if (pair.address === ethers.constants.AddressZero) {
              // Create new pair
              const tx = await this.factory.createPair(tokenA, tokenB);
              await tx.wait();
            }
            
            // Approve tokens
            const tokenAContract = new ethers.Contract(
              tokenA,
              ERC20_ABI,
              this.router.provider
            );
            
            const tokenBContract = new ethers.Contract(
              tokenB,
              ERC20_ABI,
              this.router.provider
            );
            
            // Approve tokenA
            const approveA = await tokenAContract.approve(
              this.router.address,
              amountA
            );
            await approveA.wait();
            
            // Approve tokenB
            const approveB = await tokenBContract.approve(
              this.router.address,
              amountB
            );
            await approveB.wait();
            
            // Add liquidity
            const tx = await this.router.addLiquidity(
              tokenA,
              tokenB,
              amountA,
              amountBMin,
              amountB,
              deadline,
              this.router.address
            );
            
            const receipt = await tx.wait();
            
            return {
              success: true,
              liquidityTokens: receipt.events[0].args.liquidity,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async removeLiquidity(tokenA, tokenB, liquidity, amountAMin, amountBMin, deadline) {
          try {
            // Approve LP tokens
            const pair = await this.factory.getPair(tokenA, tokenB);
            const lpTokenContract = new ethers.Contract(
              await pair.lpToken(),
              ERC20_ABI,
              this.router.provider
            );
            
            const approveTx = await lpTokenContract.approve(
              this.router.address,
              liquidity
            );
            await approveTx.wait();
            
            // Remove liquidity
            const tx = await this.router.removeLiquidity(
              tokenA,
              tokenB,
              liquidity,
              amountAMin,
              amountBMin,
              deadline
            );
            
            const receipt = await tx.wait();
            
            return {
              success: true,
              amountA: receipt.events[0].args.amountA,
              amountB: receipt.events[0].args.amountB,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async getLiquidityPosition(tokenA, tokenB, user) {
          try {
            const pair = await this.factory.getPair(tokenA, tokenB);
            const lpTokenContract = new ethers.Contract(
              await pair.lpToken(),
              ERC20_ABI,
              this.router.provider
            );
            
            const liquidity = await lpTokenContract.balanceOf(user);
            const totalSupply = await lpTokenContract.totalSupply();
            const reserves = await pair.getReserves();
            
            const userShare = liquidity.div(totalSupply);
            const userA = reserves[0].mul(userShare);
            const userB = reserves[1].mul(userShare);
            
            return {
              liquidity,
              userShare,
              userA,
              userB,
              totalSupply,
              reserves
            };
            
          } catch (error) {
            return { error: error.message };
          }
        }
      }
      
      export default LiquidityPool;
    JAVASCRIPT
    
    puts "Liquidity Pool Logic:"
    puts "- Creates pairs if needed"
    puts "- Manages token approvals"
    puts "- Adds/removes liquidity"
    puts "- Calculates user positions"
    puts "- Handles LP token distribution"
    puts "\nCode preview:"
    puts pool_logic[0...500] + "..."
    
    pool_logic
  end
  
  def self.yield_farming_logic
    puts "\nYield Farming Logic:"
    puts "=" * 50
    
    farming_logic = <<~JAVASCRIPT
      class YieldFarm {
        constructor(stakingToken, rewardToken, rewardRate, duration) {
          this.stakingToken = stakingToken;
          this.rewardToken = rewardToken;
          this.rewardRate = rewardRate; // tokens per second
          this.duration = duration; // in seconds
          this.deposits = new Map();
          this.rewards = new Map();
          this.startTime = 0;
          this.endTime = 0;
        }
        
        startFarm() {
          this.startTime = Date.now() / 1000;
          this.endTime = this.startTime + this.duration;
          
          // Reset rewards
          this.rewards.clear();
        }
        
        async deposit(amount, user) {
          if (Date.now() / 1000 > this.endTime) {
            throw new Error('Farming period has ended');
          }
          
          // Check allowance
          const tokenContract = new ethers.Contract(
            this.stakingToken,
            ERC20_ABI,
            this.provider
          );
          
          const allowance = await tokenContract.allowance(
            user,
            this.address
          );
          
          if (allowance.lt(amount)) {
            throw new Error('Insufficient allowance');
          }
          
          // Transfer tokens
          const tx = await tokenContract.transferFrom(
            user,
            this.address,
            amount
          );
          await tx.wait();
          
          // Update deposit
          const currentDeposit = this.deposits.get(user) || 0;
          this.deposits.set(user, currentDeposit + amount);
          
          // Update rewards
          await this.updateReward(user);
          
          return {
            success: true,
            newDeposit: currentDeposit + amount,
            transactionHash: tx.hash
          };
        }
        
        async withdraw(user) {
          const deposit = this.deposits.get(user) || 0;
          
          if (deposit === 0) {
            throw new Error('No deposit to withdraw');
          }
          
          // Update rewards
          await this.updateReward(user);
          
          // Transfer tokens back
          const tokenContract = new ethers.Contract(
            this.stakingToken,
            ERC20_ABI,
            this.provider
          );
          
          const tx = await tokenContract.transfer(user, deposit);
          await tx.wait();
          
          // Clear deposit
          this.deposits.set(user, 0);
          
          // Get rewards
          const rewards = this.rewards.get(user) || 0;
          this.rewards.set(user, 0);
          
          // Transfer rewards
          const rewardTokenContract = new ethers.Contract(
            this.rewardToken,
            ERC20_ABI,
            this.provider
          );
          
          const rewardTx = await rewardTokenContract.transfer(user, rewards);
          await rewardTx.wait();
          
          return {
            success: true,
            withdrawn: deposit,
            rewards,
            transactionHash: tx.hash
          };
        }
        
        async updateReward(user) {
          const deposit = this.deposits.get(user) || 0;
          const lastReward = this.rewards.get(user) || 0;
          
          const currentTime = Date.now() / 1000;
          const timePassed = Math.min(currentTime - this.startTime, this.duration);
          
          const newReward = deposit * this.rewardRate * timePassed;
          const totalReward = lastReward + newReward;
          
          this.rewards.set(user, totalReward);
        }
        
        async getPendingReward(user) {
          await this.updateReward(user);
          return this.rewards.get(user) || 0;
        }
        
        async getUserInfo(user) {
          await this.updateReward(user);
          
          return {
            deposit: this.deposits.get(user) || 0,
            pendingReward: this.rewards.get(user) || 0,
            apr: this.calculateAPR()
          };
        }
        
        calculateAPR() {
          const yearlyReward = this.rewardRate * 365 * 24 * 60 * 60;
          const totalDeposits = Array.from(this.deposits.values())
            .reduce((sum, deposit) => sum + deposit, 0);
          
          if (totalDeposits === 0) return 0;
          
          return (yearlyReward / totalDeposits) * 100;
        }
      }
      
      export default YieldFarm;
    JAVASCRIPT
    
    puts "Yield Farming Logic:"
    puts "- Manages staking deposits"
    puts "- Calculates reward distribution"
    puts "- Handles deposit/withdrawal"
    puts "- Provides APY calculation"
    puts "- Time-based reward accrual"
    puts "\nCode preview:"
    puts farming_logic[0...500] + "..."
    
    farming_logic
  end
  
  def self.demonstrate_defi_dapp
    puts "DeFi DApp Implementation:"
    puts "=" * 50
    
    # Create DeFi DApp
    defi_dapp = create_defi_dapp
    
    # Show core logic
    token_swap_logic
    liquidity_pool_logic
    yield_farming_logic
    
    puts "\nDeFi DApp Features:"
    puts "- Decentralized token swapping"
    puts "- Automated market making (AMM)"
    puts "- Liquidity provision and rewards"
    puts "- Yield farming opportunities"
    puts "- Governance token distribution"
    puts "- Flash loan integration"
    puts "- Multi-chain compatibility"
    puts "- Risk management tools"
    
    puts "\nDeFi DApp Architecture:"
    puts "- Smart contracts for core logic"
    puts "- Web3 frontend for user interface"
    puts "- Oracles for price feeds"
    puts "- Governance for protocol upgrades"
    puts "- Insurance for risk mitigation"
  end
end
```

### 5. NFT Marketplace DApp

Non-fungible token marketplace:

```ruby
class NFTMarketplaceDApp
  def self.create_nft_marketplace
    puts "Creating NFT Marketplace DApp:"
    puts "=" * 50
    
    marketplace = {
      name: "NFTMarket",
      description: "Decentralized NFT trading platform",
      features: [
        "NFT listing",
        "Bidding system",
        "Royalty distribution",
        "Collection management",
        "Search and filtering"
      ],
      contracts: [
        "Marketplace",
        "NFT (ERC-721)",
        "Auction",
        "Royalty Manager"
      ],
      user_flows: [
        "List NFT for sale",
        "Place bids",
        "Buy NFT",
        "Create collection",
        "Manage royalties"
      ]
    }
    
    puts "NFT Marketplace: #{marketplace[:name]}"
    puts "Description: #{marketplace[:description]}"
    puts "Features: #{marketplace[:features].join(', ')}"
    puts "Contracts: #{marketplace[:contracts].join(', ')}"
    puts "User flows: #{marketplace[:user_flows].join(', ')}"
    
    marketplace
  end
  
  def self.nft_listing_logic
    puts "\nNFT Listing Logic:"
    puts "=" * 50
    
    listing_logic = <<~JAVASCRIPT
      class NFTListing {
        constructor(marketplaceContract, nftContract) {
          this.marketplace = marketplaceContract;
          this.nft = nftContract;
          this.listings = new Map();
          this.bids = new Map();
          this.royalties = new Map();
        }
        
        async listNFT(tokenId, price, seller, duration = 7 * 24 * 60 * 60) {
          try {
            // Check if NFT exists and is owned by seller
            const owner = await this.nft.ownerOf(tokenId);
            if (owner !== seller) {
              throw new Error('Not the owner of this NFT');
            }
            
            // Check if already listed
            if (this.listings.has(tokenId)) {
              throw new Error('NFT already listed');
            }
            
            // Approve marketplace to transfer NFT
            const approveTx = await this.nft.approve(
              this.marketplace.address,
              tokenId
            );
            await approveTx.wait();
            
            // Create listing
            const endTime = Date.now() / 1000 + duration;
            
            const tx = await this.marketplace.listNFT(
              tokenId,
              ethers.utils.parseEther(price.toString()),
              endTime
            );
            
            const receipt = await tx.wait();
            
            // Store listing
            this.listings.set(tokenId, {
              tokenId,
              price: ethers.utils.parseEther(price.toString()),
              seller,
              startTime: Date.now() / 1000,
              endTime,
              active: true
            });
            
            return {
              success: true,
              listingId: receipt.events[0].args.listingId,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async buyNFT(tokenId, buyer, price) {
          try {
            const listing = this.listings.get(tokenId);
            
            if (!listing || !listing.active) {
              throw new Error('NFT not listed or not active');
            }
            
            if (Date.now() / 1000 > listing.endTime) {
              throw new Error('Listing has expired');
            }
            
            if (listing.price.gt(price)) {
              throw new Error('Price too low');
            }
            
            // Transfer NFT to marketplace
            const owner = await this.nft.ownerOf(tokenId);
            const transferTx = await this.nft.transferFrom(
              owner,
              this.marketplace.address,
              tokenId
            );
            await transferTx.wait();
            
            // Execute purchase
            const tx = await this.marketplace.buyNFT(
              tokenId,
              {
                value: listing.price
              }
            );
            
            const receipt = await tx.wait();
            
            // Handle royalties
            await this.handleRoyalties(tokenId, listing.price, seller);
            
            // Update listing
            this.listings.delete(tokenId);
            
            // Transfer NFT to buyer
            const finalTransfer = await this.marketplace.transferNFT(
              tokenId,
              buyer
            );
            await finalTransfer.wait();
            
            return {
              success: true,
              tokenId,
              buyer,
              seller,
              price: listing.price,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async placeBid(tokenId, bidder, amount) {
          try {
            const listing = this.listings.get(tokenId);
            
            if (!listing || !listing.active) {
              throw new Error('NFT not listed or not active');
            }
            
            // Check if bid is higher than current highest
            const currentBids = this.bids.get(tokenId) || [];
            const highestBid = currentBids.length > 0 
              ? Math.max(...currentBids.map(bid => bid.amount))
              : 0;
            
            if (amount <= highestBid) {
              throw new Error('Bid too low');
            }
            
            // Place bid
            const tx = await this.marketplace.placeBid(
              tokenId,
              {
                value: ethers.utils.parseEther(amount.toString())
              }
            );
            
            const receipt = await tx.wait();
            
            return {
              success: true,
              bidId: receipt.events[0].args.bidId,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async acceptBid(tokenId, seller) {
          try {
            const listing = this.listings.get(tokenId);
            const bids = this.bids.get(tokenId) || [];
            
            if (bids.length === 0) {
              throw new Error('No bids to accept');
            }
            
            if (listing.seller !== seller) {
              throw new Error('Only seller can accept bid');
            }
            
            // Get highest bid
            const highestBid = bids.reduce((highest, current) =>
              current.amount > highest.amount ? current : highest
            );
            
            // Transfer NFT to marketplace
            const owner = await this.nft.ownerOf(tokenId);
            const transferTx = await this.nft.transferFrom(
              owner,
              this.marketplace.address,
              tokenId
            );
            await transferTx.wait();
            
            // Accept bid
            const tx = await this.marketplace.acceptBid(
              tokenId,
              highestBid.bidder
            );
            
            const receipt = await tx.wait();
            
            // Handle royalties
            await this.handleRoyalties(tokenId, highestBid.amount, seller);
            
            // Transfer NFT to bidder
            const finalTransfer = await this.marketplace.transferNFT(
              tokenId,
              highestBid.bidder
            );
            await finalTransfer.wait();
            
            // Refund other bidders
            await this.refundBids(tokenId, [highestBid.bidder]);
            
            // Update listing
            this.listings.delete(tokenId);
            this.bids.delete(tokenId);
            
            return {
              success: true,
              tokenId,
              buyer: highestBid.bidder,
              seller,
              price: highestBid.amount,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async handleRoyalties(tokenId, salePrice, seller) {
          const royaltyInfo = await this.royalties.get(tokenId);
          
          if (royaltyInfo) {
            const royaltyAmount = salePrice.mul(royaltyInfo.percentage).div(100);
            
            // Send royalties to creator
            const tx = await this.marketplace.transfer(
              royaltyInfo.creator,
              royaltyAmount
            );
            
            await tx.wait();
          }
        }
        
        async refundBids(tokenId, excludeBidders = []) {
          const bids = this.bids.get(tokenId) || [];
          
          for (const bid of bids) {
            if (!excludeBidders.includes(bid.bidder)) {
              const tx = await this.marketplace.refundBid(
                bid.bidder,
                bid.amount
              );
              
              await tx.wait();
            }
          }
        }
      }
      
      export default NFTListing;
    JAVASCRIPT
    
    puts "NFT Listing Logic:"
    puts "- Lists NFTs for sale"
    puts "- Handles bidding system"
    puts "- Manages royalties"
    puts "- Supports auction functionality"
    puts "- Handles ownership transfers"
    puts "\nCode preview:"
    puts listing_logic[0...500] + "..."
    
    listing_logic
  end
  
  def self.collection_management_logic
    puts "\nCollection Management Logic:"
    puts "=" * 50
    
    collection_logic = <<~JAVASCRIPT
      class CollectionManager {
        constructor(marketplaceContract) {
          this.marketplace = marketplaceContract;
          this.collections = new Map();
          this.collectionsByOwner = new Map();
        }
        
        async createCollection(name, symbol, owner, royaltyPercentage = 2.5) {
          try {
            // Create collection contract
            const collectionFactory = new ethers.Contract(
              COLLECTION_FACTORY_ADDRESS,
              COLLECTION_FACTORY_ABI,
              this.marketplace.provider
            );
            
            const tx = await collectionFactory.createCollection(
              name,
              symbol,
              owner,
              royaltyPercentage
            );
            
            const receipt = await tx.wait();
            const collectionAddress = receipt.events[0].args.collection;
            
            // Store collection
            this.collections.set(collectionAddress, {
              address: collectionAddress,
              name,
              symbol,
              owner,
              royaltyPercentage,
              nfts: new Set(),
              createdAt: Date.now() / 1000
            });
            
            // Update owner collections
            const ownerCollections = this.collectionsByOwner.get(owner) || [];
            ownerCollections.push(collectionAddress);
            this.collectionsByOwner.set(owner, ownerCollections);
            
            return {
              success: true,
              collectionAddress,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async mintNFT(collectionAddress, recipient, tokenURI, creator) {
          try {
            const collection = this.collections.get(collectionAddress);
            
            if (!collection) {
              throw new Error('Collection not found');
            }
            
            // Create NFT contract
            const nftContract = new ethers.Contract(
              collectionAddress,
              NFT_ABI,
              this.marketplace.provider
            );
            
            const tx = await nftContract.mintTo(recipient, tokenURI);
            const receipt = await tx.wait();
            
            const tokenId = receipt.events[0].args.tokenId;
            
            // Update collection
            collection.nfts.add(tokenId);
            
            // Set royalty
            await this.setRoyalty(collectionAddress, tokenId, creator);
            
            return {
              success: true,
              tokenId,
              collectionAddress,
              transactionHash: receipt.hash
            };
            
          } catch (error) {
            return {
              success: false,
              error: error.message
            };
          }
        }
        
        async setRoyalty(collectionAddress, tokenId, creator) {
          const collection = this.collections.get(collectionAddress);
          
          if (!collection) {
            throw new Error('Collection not found');
          }
          
          const nftContract = new ethers.Contract(
            collectionAddress,
            NFT_ABI,
            this.marketplace.provider
          );
          
          const tx = await nftContract.setRoyalty(
            tokenId,
            creator,
            collection.royaltyPercentage
          );
          
          await tx.wait();
        }
        
        async getCollectionInfo(collectionAddress) {
          try {
            const collection = this.collections.get(collectionAddress);
            const nftContract = new ethers.Contract(
              collectionAddress,
              NFT_ABI,
              this.marketplace.provider
            );
            
            const totalSupply = await nftContract.totalSupply();
            const owner = await nftContract.owner();
            
            return {
              ...collection,
              totalSupply,
              owner,
              nftCount: collection.nfts.size
            };
            
          } catch (error) {
            return { error: error.message };
          }
        }
        
        async getCollectionNFTs(collectionAddress, offset = 0, limit = 50) {
          try {
            const collection = this.collections.get(collectionAddress);
            const nftContract = new ethers.Contract(
              collectionAddress,
              NFT_ABI,
              this.marketplace.provider
            );
            
            const nfts = [];
            
            for (let i = offset; i < offset + limit && i < collection.totalSupply; i++) {
              try {
                const owner = await nftContract.ownerOf(i);
                const tokenURI = await nftContract.tokenURI(i);
                
                nfts.push({
                  tokenId: i,
                  owner,
                  tokenURI
                });
              } catch (error) {
                // Token might not exist
                continue;
              }
            }
            
            return nfts;
            
          } catch (error) {
            return { error: error.message };
          }
        }
        
        async getCollectionsByOwner(owner) {
          const collectionAddresses = this.collectionsByOwner.get(owner) || [];
          const collections = [];
          
          for (const address of collectionAddresses) {
            const info = await this.getCollectionInfo(address);
            collections.push(info);
          }
          
          return collections;
        }
      }
      
      export default CollectionManager;
    JAVASCRIPT
    
    puts "Collection Management Logic:"
    puts "- Creates NFT collections"
    "- Manages collection metadata"
    "- Handles NFT minting"
    "- Sets royalty information"
    "- Provides collection statistics"
    puts "\nCode preview:"
    puts collection_logic[0...500] + "..."
    
    collection_logic
  end
  
  def self.demonstrate_nft_marketplace
    puts "NFT Marketplace DApp Implementation:"
    puts "=" * 50
    
    # Create NFT marketplace
    nft_marketplace = create_nft_marketplace
    
    # Show core logic
    nft_listing_logic
    collection_management_logic
    
    puts "\nNFT Marketplace Features:"
    puts "- NFT listing and trading"
    puts "- Bidding and auction system"
    puts "- Royalty distribution"
    "- Collection management"
    "- Search and filtering"
    "- Creator earnings"
    
    puts "\nNFT Marketplace Architecture:"
    puts "- ERC-721 compliant NFTs"
    "- Marketplace smart contracts"
    "- Frontend for browsing and trading"
    "- IPFS for metadata storage"
    "- Oracle for price feeds"
    "- Governance for platform rules"
  end
end
```

## 📊 DApp Analytics and Monitoring

### 6. Analytics and Monitoring

DApp performance and usage tracking:

```ruby
class DAppAnalytics
  def self.create_analytics_system
    puts "Creating DApp Analytics System:"
    puts "=" * 50
    
    analytics = {
      name: "DAppAnalytics",
      description: "Comprehensive analytics for decentralized applications",
      metrics: [
        "User activity",
        "Transaction volume",
        "Contract interactions",
        "Gas usage",
        "Error rates",
        "Performance metrics"
      ],
      tools: [
        "Google Analytics",
        "Mixpanel",
        "Amplitude",
        "Custom dashboards",
        "On-chain analytics"
      ],
      tracking: [
        "Page views",
        "User sessions",
        "Function calls",
        "Transaction success/failure",
        "Error tracking"
      ]
    }
    
    puts "Analytics System: #{analytics[:name]}"
    puts "Description: #{analytics[:description]}"
    puts "Metrics: #{analytics[:metrics].join(', ')}"
    puts "Tools: #{analytics[:tools].join(', ')}"
    puts "Tracking: #{analytics[:tracking].join(', ')}"
    
    analytics
  end
  
  def self.user_analytics_logic
    puts "\nUser Analytics Logic:"
    puts "=" * 50
    
    analytics_logic = <<~JAVASCRIPT
      class UserAnalytics {
        constructor() {
          this.users = new Map();
          this.sessions = new Map();
          this.events = [];
          this.metrics = {
            totalUsers: 0,
            activeUsers: 0,
            newUsers: 0,
            returningUsers: 0,
            totalSessions: 0,
            avgSessionDuration: 0,
            bounceRate: 0
          };
        }
        
        trackUser(walletAddress, userAgent, timestamp = Date.now()) {
          let user = this.users.get(walletAddress);
          
          if (!user) {
            // New user
            user = {
              walletAddress,
              firstSeen: timestamp,
              lastSeen: timestamp,
              sessions: [],
              totalSessions: 0,
              totalDuration: 0,
              userAgent,
              isNew: true
            };
            
            this.users.set(walletAddress, user);
            this.metrics.totalUsers++;
            this.metrics.newUsers++;
          } else {
            // Returning user
            user.isNew = false;
            user.lastSeen = timestamp;
            this.metrics.returningUsers++;
          }
          
          // Create session
          const sessionId = this.generateSessionId();
          const session = {
            sessionId,
            walletAddress,
            startTime: timestamp,
            endTime: null,
            duration: null,
            pageViews: 0,
            interactions: 0,
            userAgent,
            isNew: user.isNew
          };
          
          user.sessions.push(session);
          this.sessions.set(sessionId, session);
          this.metrics.totalSessions++;
          this.metrics.activeUsers++;
          
          // Update user session count
          user.totalSessions = user.sessions.length;
          
          return sessionId;
        }
        
        trackPageView(sessionId, page, timestamp = Date.now()) {
          const session = this.sessions.get(sessionId);
          
          if (session) {
            session.pageViews++;
            session.interactions++;
            
            // Update user
            const user = this.users.get(session.walletAddress);
            if (user) {
              user.lastSeen = timestamp;
            }
            
            // Track event
            this.trackEvent('page_view', {
              sessionId,
              page,
              timestamp,
              walletAddress: session.walletAddress
            });
          }
        }
        
        trackInteraction(sessionId, interaction, timestamp = Date.now()) {
          const session = this.sessions.get(sessionId);
          
          if (session) {
            session.interactions++;
            
            // Update user
            const user = this.users.get(session.walletAddress);
            if (user) {
              user.lastSeen = timestamp;
            }
            
            // Track event
            this.trackEvent('interaction', {
              sessionId,
              interaction,
              timestamp,
              walletAddress: session.walletAddress
            });
          }
        }
        
        trackEvent(eventName, data) {
          this.events.push({
            eventName,
            data,
            timestamp: data.timestamp || Date.now()
          });
          
          // In production, send to analytics service
          this.sendToAnalyticsService({
            event: eventName,
            data
          });
        }
        
        endSession(sessionId, timestamp = Date.now()) {
          const session = this.sessions.get(sessionId);
          
          if (session) {
            session.endTime = timestamp;
            session.duration = timestamp - session.startTime;
            
            // Update user
            const user = this.users.get(session.walletAddress);
            if (user) {
              user.totalDuration += session.duration;
              user.lastSeen = timestamp;
            }
            
            this.metrics.activeUsers--;
            
            // Recalculate metrics
            this.calculateMetrics();
          }
        }
        
        calculateMetrics() {
          const allSessions = Array.from(this.sessions.values());
          const completedSessions = allSessions.filter(s => s.endTime);
          const totalDuration = completedSessions.reduce((sum, s) => sum + s.duration, 0);
          
          this.metrics.avgSessionDuration = completedSessions.length > 0 
            ? totalDuration / completedSessions.length 
            : 0;
          
          this.metrics.bounceRate = allSessions.length > 0
            ? (allSessions.filter(s => s.pageViews === 1).length / allSessions.length) * 100
            : 0;
        }
        
        getUserStats(walletAddress) {
          const user = this.users.get(walletAddress);
          
          if (!user) {
            return { error: 'User not found' };
          }
          
          return {
            walletAddress,
            firstSeen: user.firstSeen,
            lastSeen: user.lastSeen,
            totalSessions: user.totalSessions,
            totalDuration: user.totalDuration,
            avgSessionDuration: user.totalSessions > 0 
              ? user.totalDuration / user.totalSessions 
              : 0,
            isNew: user.isNew,
            userAgent: user.userAgent
          };
        }
        
        getMetrics() {
          this.calculateMetrics();
          return this.metrics;
        }
        
        getActiveUsers(timeWindow = 300000) { // 5 minutes
          const cutoff = Date.now() - timeWindow;
          return Array.from(this.users.values())
            .filter(user => user.lastSeen > cutoff)
            .length;
        }
        
        generateSessionId() {
          return 'session_' + Math.random().toString(36).substr(2, 9);
        }
        
        async sendToAnalyticsService(data) {
          // In production, send to analytics service
          console.log('Analytics Event:', data);
          
          // Example: Send to Google Analytics
          if (window.gtag) {
            window.gtag('event', data.eventName, {
              event_category: 'dapp',
              event_label: JSON.stringify(data.data),
              value: 1
            });
          }
          
          // Example: Send to custom analytics
          await fetch('/api/analytics', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          });
        }
      }
      
      export default UserAnalytics;
    JAVASCRIPT
    
    puts "User Analytics Logic:"
    puts "- Tracks user sessions and activity"
    "- Calculates user metrics"
    "- Monitors user retention"
    "- Provides user segmentation"
    puts "\nCode preview:"
    puts analytics_logic[0...500] + "..."
    
    analytics_logic
  end
  
  def self.transaction_analytics_logic
    puts "\nTransaction Analytics Logic:"
    puts "=" * 50
    
    transaction_analytics = <<~JAVASCRIPT
      class TransactionAnalytics {
        constructor() {
          this.transactions = [];
          this.metrics = {
            totalTransactions: 0,
            successfulTransactions: 0,
            failedTransactions: 0,
            avgGasUsed: 0,
            avgGasPrice: 0,
            avgBlockTime: 0,
            totalGasUsed: 0,
            totalGasCost: 0
          };
        }
        
        trackTransaction(txData) {
          const transaction = {
            hash: txData.hash,
            from: txData.from,
            to: txData.to,
            value: txData.value,
            gasUsed: txData.gasUsed,
            gasPrice: txData.gasPrice,
            blockNumber: txData.blockNumber,
            timestamp: txData.timestamp,
            status: txData.status,
            error: txData.error,
            functionName: txData.functionName,
            contractAddress: txData.contractAddress
          };
          
          this.transactions.push(transaction);
          this.updateMetrics();
          
          // Track event
          this.trackEvent('transaction', {
            hash: transaction.hash,
            status: transaction.status,
            gasUsed: transaction.gasUsed,
            gasPrice: transaction.gasPrice,
            value: transaction.value
          });
        }
        
        updateMetrics() {
          const successful = this.transactions.filter(tx => tx.status === 'success');
          const failed = this.transactions.filter(tx => tx.status === 'failed');
          
          this.metrics.totalTransactions = this.transactions.length;
          this.metrics.successfulTransactions = successful.length;
          this.metrics.failedTransactions = failed.length;
          
          if (successful.length > 0) {
            const totalGasUsed = successful.reduce((sum, tx) => sum + tx.gasUsed, 0);
            const totalGasPrice = successful.reduce((sum, tx) => sum + tx.gasPrice, 0);
            
            this.metrics.avgGasUsed = totalGasUsed / successful.length;
            this.metrics.avgGasPrice = totalGasPrice / successful.length;
          }
          
          // Calculate total gas cost
          this.metrics.totalGasUsed = this.transactions.reduce((sum, tx) => sum + tx.gasUsed, 0);
          this.metrics.totalGasCost = this.transactions.reduce((sum, tx) => 
            sum + (tx.gasUsed * tx.gasPrice), 0
          );
        }
        
        getMetrics() {
          return this.metrics;
        }
        
        getTransactionStats(timeWindow = 3600000) { // 1 hour
          const cutoff = Date.now() - timeWindow;
          const recentTransactions = this.transactions.filter(
            tx => tx.timestamp > cutoff
          );
          
          return {
            count: recentTransactions.length,
            successRate: recentTransactions.length > 0 
              ? (recentTransactions.filter(tx => tx.status === 'success').length / recentTransactions.length) * 100
              : 0,
            avgGasUsed: recentTransactions.length > 0
              ? recentTransactions.reduce((sum, tx) => sum + tx.gasUsed, 0) / recentTransactions.length
              : 0,
            avgGasPrice: recentTransactions.length > 0
              ? recentTransactions.reduce((sum, tx) => sum + tx.gasPrice, 0) / recentTransactions.length
              : 0
          };
        }
        
        getTopContracts(limit = 10) {
          const contractStats = {};
          
          this.transactions.forEach(tx => {
            if (!contractStats[tx.contractAddress]) {
              contractStats[tx.contractAddress] = {
                address: tx.contractAddress,
                count: 0,
                successRate: 0,
                avgGasUsed: 0,
                totalVolume: 0
              };
            }
            
            const stats = contractStats[tx.contractAddress];
            stats.count++;
            stats.totalVolume += parseFloat(tx.value);
            
            if (tx.status === 'success') {
              stats.successRate = (stats.successRate * (stats.count - 1) + 1)) / stats.count;
            }
            
            stats.avgGasUsed = (stats.avgGasUsed * (stats.count - 1) + tx.gasUsed) / stats.count;
          });
          
          return Object.values(contractStats)
            .sort((a, b) => b.count - a.count)
            .slice(0, limit);
        }
        
        trackEvent(eventName, data) {
          // Send to analytics service
          this.sendToAnalyticsService({
            event: eventName,
            data
          });
        }
        
        async sendToAnalyticsService(data) {
          console.log('Transaction Analytics Event:', data);
          
          // Send to analytics service
          await fetch('/api/analytics/transactions', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          });
        }
      }
      
      export default TransactionAnalytics;
    JAVASCRIPT
    
    puts "Transaction Analytics Logic:"
    puts "- Tracks transaction success/failure"
    "- Monitors gas usage and costs"
    "- Calculates performance metrics"
    "- Provides contract statistics"
    puts "\nCode preview:"
    puts transaction_analytics[0...500] + "..."
    
    transaction_analytics
  end
  
  def self.performance_monitoring_logic
    puts "\nPerformance Monitoring Logic:"
    puts "=" * 50
    
    monitoring_logic = <<~JAVASCRIPT
      class PerformanceMonitor {
        constructor() {
          this.metrics = {
            pageLoadTime: 0,
            firstContentfulPaint: 0,
            largestContentfulPaint: 0,
            timeToInteractive: 0,
            cumulativeLayoutShift: 0,
            firstInputDelay: 0,
            errorRate: 0,
            apiResponseTime: 0,
            contractInteractionTime: 0
          };
          
          this.measurePerformance();
          this.observeErrors();
          this.observeAPICalls();
          this.observeContractInteractions();
        }
        
        measurePerformance() {
          if (typeof window !== 'undefined' && window.performance) {
            // Page Load Time
            window.addEventListener('load', () => {
              const navigation = window.performance.getEntriesByType('navigation')[0];
              this.metrics.pageLoadTime = navigation.loadEventEnd - navigation.fetchStart;
              
              // Web Vitals
              this.observeWebVitals();
            });
            
            // Observe performance
            const observer = new PerformanceObserver((list) => {
              list.getEntries().forEach(entry => {
                if (entry.entryType === 'layout-shift') {
                  this.metrics.cumulativeLayoutShift += entry.value;
                } else if (entry.entryType === 'first-input') {
                  this.metrics.firstInputDelay = entry.processingStart - entry.startTime;
                }
              });
            });
            
            observer.observe({ entryTypes: ['layout-shift', 'first-input'] });
          }
        }
        
        observeWebVitals() {
          // First Contentful Paint
          new PerformanceObserver((list) => {
            list.getEntries().forEach(entry => {
              if (entry.name === 'first-contentful-paint') {
                this.metrics.firstContentfulPaint = entry.startTime;
              } else if (entry.name === 'largest-contentful-paint') {
                this.metrics.largestContentfulPaint = entry.startTime;
              } else if (entry.name === 'time-to-interactive') {
                this.metrics.timeToInteractive = entry.startTime;
              }
            });
          }).observe({ entryTypes: ['paint'] });
        }
        
        observeErrors() {
          window.addEventListener('error', (event) => {
            this.metrics.errorRate++;
            
            this.trackEvent('error', {
              message: event.message,
              filename: event.filename,
              lineno: event.lineno,
              colno: event.colno,
              stack: event.error?.stack
            });
          });
          
          window.addEventListener('unhandledrejection', (event) => {
            this.metrics.errorRate++;
            
            this.trackEvent('unhandled_rejection', {
              reason: event.reason,
              stack: event.reason?.stack
            });
          });
        }
        
        observeAPICalls() {
          const originalFetch = window.fetch;
          
          window.fetch = async (...args) => {
            const startTime = performance.now();
            
            try {
              const response = await originalFetch(...args);
              const endTime = performance.now();
              
              const responseTime = endTime - startTime;
              this.metrics.apiResponseTime = 
                (this.metrics.apiResponseTime + responseTime) / 2;
              
              this.trackEvent('api_call', {
                url: args[0],
                method: args[1]?.method || 'GET',
                status: response.status,
                responseTime
              });
              
              return response;
            } catch (error) {
              const endTime = performance.now();
              const responseTime = endTime - startTime;
              
              this.metrics.apiResponseTime = 
                (this.metrics.apiResponseTime + responseTime) / 2;
              
              this.trackEvent('api_error', {
                url: args[0],
                error: error.message,
                responseTime
              });
              
              throw error;
            }
          };
        }
        
        observeContractInteractions() {
          // Monitor contract interaction performance
          const originalSend = window.ethereum?.request;
          
          if (originalSend) {
            window.ethereum.request = async (...args) => {
              const startTime = performance.now();
              
              try {
                const result = await originalSend(...args);
                const endTime = performance.now();
                
                const interactionTime = endTime - startTime;
                this.metrics.contractInteractionTime = 
                  (this.metrics.contractInteractionTime + interactionTime) / 2;
                
                this.trackEvent('contract_interaction', {
                  method: args[0]?.method,
                  interactionTime
                });
                
                return result;
              } catch (error) {
                const endTime = performance.now();
                const interactionTime = endTime - startTime;
                
                this.metrics.contractInteractionTime = 
                  (this.metrics.contractInteractionTime + interactionTime) / 2;
                
                this.trackEvent('contract_error', {
                  method: args[0]?.method,
                  error: error.message,
                  interactionTime
                });
                
                throw error;
              }
            };
          }
        }
        
        getMetrics() {
          return this.metrics;
        }
        
        getPerformanceScore() {
          const weights = {
            pageLoadTime: 0.25,
            firstContentfulPaint: 0.15,
            largestContentfulPaint: 0.15,
            timeToInteractive: 0.20,
            cumulativeLayoutShift: 0.15,
            firstInputDelay: 0.10
          };
          
          let score = 100;
          
          // Page Load Time (lower is better)
          if (this.metrics.pageLoadTime > 3000) score -= 25;
          else if (this.metrics.pageLoadTime > 1000) score -= 15;
          else if (this.metrics.pageLoadTime > 500) score -= 5;
          
          // First Contentful Paint (lower is better)
          if (this.metrics.firstContentfulPaint > 2000) score -= 15;
          else if (this.metrics.firstContentfulPaint > 1000) score -= 10;
          else if (this.metrics.firstContentfulPaint > 500) score -= 5;
          
          // Largest Contentful Paint (lower is better)
          if (this.metrics.largestContentfulPaint > 4000) score -= 15;
          else if (this.metrics.largestContentfulPaint > 2500) score -= 10;
          else if (this.metrics.largestContentfulPaint > 1500) score -= 5;
          
          // Time to Interactive (lower is better)
          if (this.metrics.timeToInteractive > 5000) score -= 20;
          else if (this.metrics.timeToInteractive > 3000) score -= 10;
          else if (this.metrics.timeToInteractive > 1000) score -= 5;
          
          // Cumulative Layout Shift (lower is better)
          if (this.metrics.cumulativeLayoutShift > 0.25) score -= 25;
          else if (this.metrics.cumulativeLayoutShift > 0.1) score -= 10;
          else if (this.metrics.cumulativeLayoutShift > 0.05) score -= 5;
          
          // First Input Delay (lower is better)
          if (this.metrics.firstInputDelay > 300) score -= 10;
          else if (this.metrics.firstInputDelay > 100) score -= 5;
          
          return Math.max(0, score);
        }
        
        trackEvent(eventName, data) {
          // Send to analytics service
          this.sendToAnalyticsService({
            event: eventName,
            data
          });
        }
        
        async sendToAnalyticsService(data) {
          console.log('Performance Analytics Event:', data);
          
          await fetch('/api/analytics/performance', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          });
        }
      }
      
      export default PerformanceMonitor;
    JAVASCRIPT
    
    puts "Performance Monitoring Logic:"
    puts "- Monitors Web Vitals"
    "- Tracks API response times"
    "- Measures contract interaction performance"
    "- Calculates performance scores"
    "- Observes errors and crashes"
    puts "\nCode preview:"
    puts monitoring_logic[0...500] + "..."
    
    monitoring_logic
  end
  
  def self.demonstrate_analytics
    puts "DApp Analytics and Monitoring:"
    puts "=" * 50
    
    # Create analytics system
    analytics = create_analytics_system
    
    # Show analytics logic
    user_analytics_logic
    transaction_analytics_logic
    performance_monitoring_logic
    
    puts "\nDApp Analytics Features:"
    puts "- User behavior tracking"
    "- Transaction analytics"
    "- Performance monitoring"
    "- Error tracking and reporting"
    "- Custom event tracking"
    "- Real-time dashboards"
    puts "- A/B testing support"
    puts "- Funnel analysis"
    puts "- Retention analysis"
    
    puts "\nAnalytics Implementation:"
    puts "- Frontend event tracking"
    "- On-chain data analysis"
    "- Backend analytics service"
    "- Dashboard visualization"
    "- Alert system for anomalies"
    puts "- Data export capabilities"
    puts "- Privacy-compliant tracking"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic DApp**: Create simple DApp frontend
2. **Wallet Integration**: Connect to MetaMask
3. **Contract Interaction**: Call smart contracts
4. **Event Handling**: Listen to contract events

### Intermediate Exercises

1. **DeFi DApp**: Build decentralized exchange
2. **NFT Marketplace**: Create NFT trading platform
3. **Analytics System**: Implement tracking
4. **Performance Optimization**: Optimize DApp performance

### Advanced Exercises

1. **Multi-chain DApp**: Support multiple blockchains
2. **Advanced Analytics**: Build comprehensive analytics
3. **Real-time Features**: Implement real-time updates
4. **Security**: Implement security best practices

---

## 🎯 Summary

DApps Development in Ruby provide:

- **DApp Fundamentals** - Architecture and concepts
- **Web3 Integration** - Blockchain connection and interaction
- **Frontend Development** - React-like components
- **Real-World Examples** - DeFi and NFT applications
- **Analytics Monitoring** - Performance and usage tracking
- **Practical Implementations** - Working DApp code

Master these DApp development techniques for blockchain applications!
