<?php
/**
 * Smart Contracts in PHP
 * 
 * Basic smart contract implementation and Web3 integration.
 */

// Smart Contract Interface
interface SmartContract
{
    public function deploy(string $address): void;
    public function call(string $function, array $params = []): mixed;
    public function getBalance(): float;
    public function getAddress(): string;
    public function getAbi(): array;
}

// Solidity-like Smart Contract
class SolidityContract implements SmartContract
{
    private string $address;
    private array $storage = [];
    private array $abi;
    private float $balance = 0.0;
    private array $functions = [];
    
    public function __construct(array $abi = [])
    {
        $this->abi = $abi;
        $this->initializeFunctions();
    }
    
    private function initializeFunctions(): void
    {
        // Define contract functions
        $this->functions = [
            'balanceOf' => [
                'inputs' => ['address'],
                'outputs' => ['uint256'],
                'type' => 'view'
            ],
            'transfer' => [
                'inputs' => ['address', 'uint256'],
                'outputs' => ['bool'],
                'type' => 'payable'
            ],
            'approve' => [
                'inputs' => ['address', 'uint256'],
                'outputs' => ['bool'],
                'type' => 'nonpayable'
            ],
            'totalSupply' => [
                'inputs' => [],
                'outputs' => ['uint256'],
                'type' => 'view'
            ]
        ];
    }
    
    public function deploy(string $address): void
    {
        $this->address = $address;
        $this->storage = [
            'balances' => [],
            'allowances' => [],
            'totalSupply' => 1000000
        ];
        
        echo "Contract deployed at address: $address\n";
    }
    
    public function call(string $function, array $params = []): mixed
    {
        if (!isset($this->functions[$function])) {
            throw new Exception("Function '$function' not found");
        }
        
        $funcInfo = $this->functions[$function];
        
        // Check parameter count
        if (count($params) !== count($funcInfo['inputs'])) {
            throw new Exception("Parameter count mismatch for function '$function'");
        }
        
        // Execute function
        switch ($function) {
            case 'balanceOf':
                return $this->balanceOf($params[0]);
            case 'transfer':
                return $this->transfer($params[0], $params[1]);
            case 'approve':
                return $this->approve($params[0], $params[1]);
            case 'totalSupply':
                return $this->totalSupply();
            default:
                throw new Exception("Function '$function' not implemented");
        }
    }
    
    private function balanceOf(string $address): string
    {
        return $this->storage['balances'][$address] ?? '0';
    }
    
    private function transfer(string $to, string $amount): bool
    {
        $from = 'msg.sender'; // Simulate message sender
        
        $fromBalance = $this->storage['balances'][$from] ?? '0';
        $toBalance = $this->storage['balances'][$to] ?? '0';
        
        if (bccomp($fromBalance, $amount) < 0) {
            echo "Insufficient balance for transfer\n";
            return false;
        }
        
        $this->storage['balances'][$from] = bcsub($fromBalance, $amount);
        $this->storage['balances'][$to] = bcadd($toBalance, $amount);
        
        echo "Transferred $amount tokens from $from to $to\n";
        return true;
    }
    
    private function approve(string $spender, string $amount): bool
    {
        $owner = 'msg.sender';
        
        $this->storage['allowances'][$owner][$spender] = $amount;
        echo "Approved $amount tokens for $spender\n";
        return true;
    }
    
    private function totalSupply(): string
    {
        return (string) $this->storage['totalSupply'];
    }
    
    public function getBalance(): float
    {
        return $this->balance;
    }
    
    public function getAddress(): string
    {
        return $this->address;
    }
    
    public function getAbi(): array
    {
        return $this->abi;
    }
    
    public function getStorage(): array
    {
        return $this->storage;
    }
    
    public function setStorage(array $storage): void
    {
        $this->storage = $storage;
    }
    
    public function addBalance(float $amount): void
    {
        $this->balance += $amount;
    }
    
    public function subtractBalance(float $amount): void
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
        }
    }
}

// ERC20 Token Contract
class ERC20Token extends SolidityContract
{
    private string $name;
    private string $symbol;
    private string $decimals;
    
    public function __construct(string $name, string $symbol, uint256 $totalSupply)
    {
        parent::__construct();
        $this->name = $name;
        $this->symbol = $symbol;
        $this->decimals = '18';
        
        // Override functions
        $this->functions = array_merge($this->functions, [
            'name' => [
                'inputs' => [],
                'outputs' => ['string'],
                'type' => 'view'
            ],
            'symbol' => [
                'inputs' => [],
                'outputs' => ['string'],
                'type' => 'view'
            ],
            'decimals' => [
                'inputs' => [],
                'outputs' => ['uint8'],
                'type' => 'view'
            ]
        ]);
    }
    
    public function deploy(string $address): void
    {
        parent::deploy($address);
        
        // Initialize with total supply to deployer
        $this->storage['totalSupply'] = $totalSupply;
        $this->storage['balances'][$address] = (string) $totalSupply;
        
        echo "ERC20 Token '{$this->name}' deployed\n";
        echo "Symbol: {$this->symbol}\n";
        echo "Total Supply: $totalSupply\n";
    }
    
    public function call(string $function, array $params = []): mixed
    {
        switch ($function) {
            case 'name':
                return $this->name;
            case 'symbol':
                return $this->symbol;
            case 'decimals':
                return $this->decimals;
            default:
                return parent::call($function, $params);
        }
    }
    
    public function mint(string $to, string $amount): void
    {
        $toBalance = $this->storage['balances'][$to] ?? '0';
        $this->storage['balances'][$to] = bcadd($toBalance, $amount);
        $this->storage['totalSupply'] = bcadd($this->storage['totalSupply'], $amount);
        
        echo "Minted $amount tokens to $to\n";
    }
    
    public function burn(string $amount): void
    {
        $from = 'msg.sender';
        $fromBalance = $this->storage['balances'][$from] ?? '0';
        
        if (bccomp($fromBalance, $amount) < 0) {
            throw new Exception("Insufficient balance for burning");
        }
        
        $this->storage['balances'][$from] = bcsub($fromBalance, $amount);
        $this->storage['totalSupply'] = bcsub($this->storage['totalSupply'], $amount);
        
        echo "Burned $amount tokens from $from\n";
    }
}

// Web3 Provider Interface
interface Web3Provider
{
    public function call(string $contract, string $method, array $params = []): mixed;
    public function sendTransaction(string $from, string $to, float $value, array $data = []): string;
    public function getBalance(string $address): float;
    public function getTransactionReceipt(string $txHash): array;
    public function getBlockNumber(): int;
    public function getGasPrice(): float;
}

// Simulated Web3 Provider
class SimulatedWeb3Provider implements Web3Provider
{
    private array $contracts = [];
    private array $accounts = [];
    private array $transactions = [];
    private int $blockNumber = 0;
    private float $gasPrice = 0.00000002; // 20 Gwei
    
    public function __construct()
    {
        $this->initializeAccounts();
    }
    
    private function initializeAccounts(): void
    {
        // Create test accounts with balances
        $this->accounts = [
            '0x1234567890123456789012345678901234567890' => 1000.0,
            '0x2345678901234567890123456789012345678901' => 500.0,
            '0x3456789012345678901234567890123456789012' => 750.0,
            '0x4567890123456789012345678901234567890123' => 200.0,
            '0x5678901234567890123456789012345678901234' => 300.0
        ];
        
        echo "Initialized " . count($this->accounts) . " test accounts\n";
    }
    
    public function deployContract(SmartContract $contract, string $from): string
    {
        $address = $this->generateContractAddress();
        $contract->deploy($address);
        
        $this->contracts[$address] = $contract;
        
        // Simulate deployment transaction
        $txHash = $this->sendTransaction($from, $address, 0.01);
        
        echo "Contract deployed at $address with tx hash $txHash\n";
        return $address;
    }
    
    public function call(string $contract, string $method, array $params = []): mixed
    {
        if (!isset($this->contracts[$contract])) {
            throw new Exception("Contract not found at address: $contract");
        }
        
        $contractInstance = $this->contracts[$contract];
        return $contractInstance->call($method, $params);
    }
    
    public function sendTransaction(string $from, string $to, float $value, array $data = []): string
    {
        if (!isset($this->accounts[$from])) {
            throw new Exception("Account not found: $from");
        }
        
        if ($this->accounts[$from] < $value) {
            throw new Exception("Insufficient balance");
        }
        
        // Generate transaction hash
        $txHash = $this->generateTransactionHash();
        
        // Create transaction
        $transaction = [
            'hash' => $txHash,
            'from' => $from,
            'to' => $to,
            'value' => $value,
            'data' => $data,
            'gasPrice' => $this->gasPrice,
            'gasLimit' => 21000,
            'blockNumber' => $this->blockNumber,
            'timestamp' => time()
        ];
        
        $this->transactions[$txHash] = $transaction;
        
        // Update balances
        $this->accounts[$from] -= $value;
        
        // If recipient is an account, add to balance
        if (isset($this->accounts[$to])) {
            $this->accounts[$to] += $value;
        }
        
        // If recipient is a contract, handle contract call
        if (isset($this->contracts[$to]) && !empty($data)) {
            $this->handleContractCall($to, $data);
        }
        
        echo "Transaction sent: $txHash\n";
        return $txHash;
    }
    
    private function handleContractCall(string $contract, array $data): void
    {
        $contractInstance = $this->contracts[$contract];
        
        if (isset($data['method']) && isset($data['params'])) {
            $result = $contractInstance->call($data['method'], $data['params']);
            echo "Contract call result: " . json_encode($result) . "\n";
        }
    }
    
    public function getBalance(string $address): float
    {
        return $this->accounts[$address] ?? 0.0;
    }
    
    public function setBalance(string $address, float $balance): void
    {
        $this->accounts[$address] = $balance;
    }
    
    public function getTransactionReceipt(string $txHash): array
    {
        if (!isset($this->transactions[$txHash])) {
            throw new Exception("Transaction not found: $txHash");
        }
        
        $tx = $this->transactions[$txHash];
        
        return [
            'transactionHash' => $txHash,
            'transactionIndex' => 0,
            'blockHash' => '0x' . str_repeat('0', 64),
            'blockNumber' => $tx['blockNumber'],
            'from' => $tx['from'],
            'to' => $tx['to'],
            'cumulativeGasUsed' => $tx['gasLimit'],
            'gasUsed' => $tx['gasLimit'],
            'contractAddress' => isset($this->contracts[$tx['to']]) ? $tx['to'] : null,
            'logs' => [],
            'logsBloom' => '0x' . str_repeat('0', 512),
            'status' => 1
        ];
    }
    
    public function getBlockNumber(): int
    {
        return $this->blockNumber;
    }
    
    public function getGasPrice(): float
    {
        return $this->gasPrice;
    }
    
    public function setGasPrice(float $gasPrice): void
    {
        $this->gasPrice = $gasPrice;
    }
    
    public function mineBlock(): void
    {
        $this->blockNumber++;
        echo "Mined block #$this->blockNumber\n";
    }
    
    public function getAccounts(): array
    {
        return array_keys($this->accounts);
    }
    
    public function getContract(string $address): ?SmartContract
    {
        return $this->contracts[$address] ?? null;
    }
    
    private function generateContractAddress(): string
    {
        return '0x' . bin2hex(random_bytes(20));
    }
    
    private function generateTransactionHash(): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }
}

// Web3 Client
class Web3Client
{
    private Web3Provider $provider;
    private string $defaultAccount;
    
    public function __construct(Web3Provider $provider)
    {
        $this->provider = $provider;
        $this->defaultAccount = $provider->getAccounts()[0] ?? '';
    }
    
    public function setDefaultAccount(string $address): void
    {
        $this->defaultAccount = $address;
    }
    
    public function getDefaultAccount(): string
    {
        return $this->defaultAccount;
    }
    
    public function deployContract(SmartContract $contract): string
    {
        return $this->provider->deployContract($contract, $this->defaultAccount);
    }
    
    public function callContract(string $contractAddress, string $method, array $params = []): mixed
    {
        return $this->provider->call($contractAddress, $method, $params);
    }
    
    public function sendTransaction(string $to, float $value, array $data = []): string
    {
        return $this->provider->sendTransaction($this->defaultAccount, $to, $value, $data);
    }
    
    public function getBalance(string $address = null): float
    {
        $address = $address ?? $this->defaultAccount;
        return $this->provider->getBalance($address);
    }
    
    public function getTransactionReceipt(string $txHash): array
    {
        return $this->provider->getTransactionReceipt($txHash);
    }
    
    public function getBlockNumber(): int
    {
        return $this->provider->getBlockNumber();
    }
    
    public function getGasPrice(): float
    {
        return $this->provider->getGasPrice();
    }
    
    public function getProvider(): Web3Provider
    {
        return $this->provider;
    }
}

// Smart Contract Examples
class SmartContractExamples
{
    public function demonstrateBasicContract(): void
    {
        echo "Basic Smart Contract Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create Web3 provider and client
        $provider = new SimulatedWeb3Provider();
        $web3 = new Web3Client($provider);
        
        // Set default account
        $accounts = $provider->getAccounts();
        $web3->setDefaultAccount($accounts[0]);
        
        echo "Default account: {$web3->getDefaultAccount()}\n";
        echo "Initial balance: " . $web3->getBalance() . " ETH\n\n";
        
        // Create and deploy contract
        $contract = new SolidityContract();
        $contractAddress = $web3->deployContract($contract);
        
        echo "Contract deployed at: $contractAddress\n\n";
        
        // Test contract functions
        echo "Testing contract functions:\n";
        
        // Get total supply
        $totalSupply = $web3->callContract($contractAddress, 'totalSupply');
        echo "Total Supply: $totalSupply\n";
        
        // Get balance of default account
        $balance = $web3->callContract($contractAddress, 'balanceOf', [$web3->getDefaultAccount()]);
        echo "Contract Balance: $balance\n";
        
        // Test transfer
        echo "\nTesting transfer:\n";
        $toAccount = $accounts[1];
        
        $txHash = $web3->sendTransaction($contractAddress, 0, [
            'method' => 'transfer',
            'params' => [$toAccount, '100']
        ]);
        
        echo "Transfer transaction: $txHash\n";
        
        // Check balances after transfer
        $fromBalance = $web3->callContract($contractAddress, 'balanceOf', [$web3->getDefaultAccount()]);
        $toBalance = $web3->callContract($contractAddress, 'balanceOf', [$toAccount]);
        
        echo "From balance: $fromBalance\n";
        echo "To balance: $toBalance\n";
        
        // Test approve
        echo "\nTesting approve:\n";
        $spender = $accounts[2];
        
        $approveTx = $web3->sendTransaction($contractAddress, 0, [
            'method' => 'approve',
            'params' => [$spender, '50']
        ]);
        
        echo "Approve transaction: $approveTx\n";
    }
    
    public function demonstrateERC20Token(): void
    {
        echo "\nERC20 Token Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Create Web3 provider and client
        $provider = new SimulatedWeb3Provider();
        $web3 = new Web3Client($provider);
        
        $accounts = $provider->getAccounts();
        $web3->setDefaultAccount($accounts[0]);
        
        // Create ERC20 token
        $token = new ERC20Token('MyToken', 'MTK', '1000000000000000000000000');
        $tokenAddress = $web3->deployContract($token);
        
        echo "Token deployed at: $tokenAddress\n\n";
        
        // Test token functions
        echo "Testing ERC20 functions:\n";
        
        $name = $web3->callContract($tokenAddress, 'name');
        $symbol = $web3->callContract($tokenAddress, 'symbol');
        $decimals = $web3->callContract($tokenAddress, 'decimals');
        $totalSupply = $web3->callContract($tokenAddress, 'totalSupply');
        
        echo "Name: $name\n";
        echo "Symbol: $symbol\n";
        echo "Decimals: $decimals\n";
        echo "Total Supply: $totalSupply\n";
        
        // Check initial balances
        echo "\nInitial balances:\n";
        foreach ($accounts as $account) {
            $balance = $web3->callContract($tokenAddress, 'balanceOf', [$account]);
            echo "$account: $balance\n";
        }
        
        // Transfer tokens
        echo "\nTransferring tokens:\n";
        $toAccount = $accounts[1];
        $transferAmount = '100000000000000000000'; // 100 tokens
        
        $transferTx = $web3->sendTransaction($tokenAddress, 0, [
            'method' => 'transfer',
            'params' => [$toAccount, $transferAmount]
        ]);
        
        echo "Transfer transaction: $transferTx\n";
        
        // Check balances after transfer
        echo "\nBalances after transfer:\n";
        foreach ([$accounts[0], $toAccount] as $account) {
            $balance = $web3->callContract($tokenAddress, 'balanceOf', [$account]);
            echo "$account: $balance\n";
        }
        
        // Mint new tokens
        echo "\nMinting new tokens:\n";
        $mintAmount = '500000000000000000000'; // 500 tokens
        
        // Direct call to mint function
        $token->mint($accounts[2], $mintAmount);
        
        $mintedBalance = $web3->callContract($tokenAddress, 'balanceOf', [$accounts[2]]);
        echo "Minted to {$accounts[2]}: $mintedBalance\n";
        
        // Burn tokens
        echo "\nBurning tokens:\n";
        $burnAmount = '10000000000000000000'; // 10 tokens
        
        $web3->setDefaultAccount($accounts[2]);
        $token->burn($burnAmount);
        
        $burnedBalance = $web3->callContract($tokenAddress, 'balanceOf', [$accounts[2]]);
        echo "Balance after burn: $burnedBalance\n";
        
        // Check new total supply
        $newTotalSupply = $web3->callContract($tokenAddress, 'totalSupply');
        echo "New total supply: $newTotalSupply\n";
    }
    
    public function demonstrateContractInteraction(): void
    {
        echo "\nContract Interaction Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Create Web3 provider and client
        $provider = new SimulatedWeb3Provider();
        $web3 = new Web3Client($provider);
        
        $accounts = $provider->getAccounts();
        $web3->setDefaultAccount($accounts[0]);
        
        // Deploy multiple contracts
        $contracts = [];
        
        // Deploy basic contract
        $basicContract = new SolidityContract();
        $contracts['basic'] = $web3->deployContract($basicContract);
        
        // Deploy ERC20 token
        $token = new ERC20Token('TestToken', 'TT', '1000000000000000000000000');
        $contracts['token'] = $web3->deployContract($token);
        
        echo "Deployed contracts:\n";
        foreach ($contracts as $name => $address) {
            echo "  $name: $address\n";
        }
        
        // Interact with contracts
        echo "\nContract interactions:\n";
        
        // Get contract storage
        $basicContractInstance = $provider->getContract($contracts['basic']);
        $tokenInstance = $provider->getContract($contracts['token']);
        
        echo "Basic contract storage: " . json_encode($basicContractInstance->getStorage()) . "\n";
        echo "Token contract storage: " . json_encode($tokenInstance->getStorage()) . "\n";
        
        // Send ETH to contracts
        echo "\nSending ETH to contracts:\n";
        
        $ethAmount = 0.1;
        $txHash1 = $web3->sendTransaction($contracts['basic'], $ethAmount);
        $txHash2 = $web3->sendTransaction($contracts['token'], $ethAmount);
        
        echo "Sent $ethAmount ETH to basic contract: $txHash1\n";
        echo "Sent $ethAmount ETH to token contract: $txHash2\n";
        
        // Check contract balances
        echo "\nContract ETH balances:\n";
        $basicBalance = $basicContractInstance->getBalance();
        $tokenBalance = $tokenInstance->getBalance();
        
        echo "Basic contract: $basicBalance ETH\n";
        echo "Token contract: $tokenBalance ETH\n";
        
        // Get transaction receipts
        echo "\nTransaction receipts:\n";
        $receipt1 = $web3->getTransactionReceipt($txHash1);
        $receipt2 = $web3->getTransactionReceipt($txHash2);
        
        echo "Receipt 1: " . json_encode($receipt1, JSON_PRETTY_PRINT) . "\n";
        echo "Receipt 2: " . json_encode($receipt2, JSON_PRETTY_PRINT) . "\n";
        
        // Mine blocks
        echo "\nMining blocks:\n";
        for ($i = 0; $i < 3; $i++) {
            $provider->mineBlock();
        }
        
        echo "Current block number: " . $web3->getBlockNumber() . "\n";
        
        // Check gas price
        echo "Current gas price: " . $web3->getGasPrice() . " ETH\n";
    }
    
    public function demonstrateContractSecurity(): void
    {
        echo "\nContract Security Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create Web3 provider and client
        $provider = new SimulatedWeb3Provider();
        $web3 = new Web3Client($provider);
        
        $accounts = $provider->getAccounts();
        
        // Deploy vulnerable contract
        echo "Deploying vulnerable contract:\n";
        
        $vulnerableContract = new class extends SolidityContract {
            private array $owner;
            
            public function __construct() {
                parent::__construct();
                $this->owner = null;
            }
            
            public function deploy(string $address): void {
                parent::deploy($address);
                $this->storage['owner'] = $address;
                $this->storage['balances'] = ['0x' . str_repeat('0', 40) => '1000000'];
            }
            
            public function call(string $function, array $params = []): mixed {
                switch ($function) {
                    case 'withdraw':
                        return $this->withdraw();
                    case 'setOwner':
                        return $this->setOwner($params[0]);
                    case 'getOwner':
                        return $this->storage['owner'];
                    default:
                        return parent::call($function, $params);
                }
            }
            
            private function withdraw(): bool {
                $owner = $this->storage['owner'];
                $msgSender = 'msg.sender';
                
                // Vulnerable: No proper access control
                $this->storage['balances'][$owner] = '0';
                echo "Withdrawn all funds by $msgSender\n";
                return true;
            }
            
            private function setOwner(string $newOwner): bool {
                // Vulnerable: Anyone can set owner
                $this->storage['owner'] = $newOwner;
                echo "Owner changed to $newOwner\n";
                return true;
            }
        };
        
        $web3->setDefaultAccount($accounts[0]);
        $vulnerableAddress = $web3->deployContract($vulnerableContract);
        
        echo "Vulnerable contract deployed at: $vulnerableAddress\n\n";
        
        // Demonstrate vulnerabilities
        echo "Demonstrating vulnerabilities:\n";
        
        // Check initial owner
        $initialOwner = $web3->callContract($vulnerableAddress, 'getOwner');
        echo "Initial owner: $initialOwner\n";
        
        // Attack: Change owner
        echo "\nAttack: Changing owner\n";
        $web3->setDefaultAccount($accounts[1]);
        
        $setOwnerTx = $web3->sendTransaction($vulnerableAddress, 0, [
            'method' => 'setOwner',
            'params' => [$accounts[1]]
        ]);
        
        $newOwner = $web3->callContract($vulnerableAddress, 'getOwner');
        echo "New owner after attack: $newOwner\n";
        
        // Attack: Withdraw funds
        echo "\nAttack: Withdrawing funds\n";
        $withdrawTx = $web3->sendTransaction($vulnerableAddress, 0, [
            'method' => 'withdraw',
            'params' => []
        ]);
        
        // Deploy secure contract
        echo "\nDeploying secure contract:\n";
        
        $secureContract = new class extends SolidityContract {
            private string $owner;
            
            public function __construct() {
                parent::__construct();
            }
            
            public function deploy(string $address): void {
                parent::deploy($address);
                $this->owner = $address;
                $this->storage['balances'] = [$address => '1000000'];
            }
            
            public function call(string $function, array $params = []): mixed {
                switch ($function) {
                    case 'withdraw':
                        return $this->withdraw();
                    case 'transferOwnership':
                        return $this->transferOwnership($params[0]);
                    case 'getOwner':
                        return $this->owner;
                    default:
                        return parent::call($function, $params);
                }
            }
            
            private function withdraw(): bool {
                $msgSender = 'msg.sender';
                
                // Secure: Only owner can withdraw
                if ($msgSender !== $this->owner) {
                    echo "Withdrawal failed: Only owner can withdraw\n";
                    return false;
                }
                
                $this->storage['balances'][$this->owner] = '0';
                echo "Withdrawn all funds by owner\n";
                return true;
            }
            
            private function transferOwnership(string $newOwner): bool {
                $msgSender = 'msg.sender';
                
                // Secure: Only owner can transfer ownership
                if ($msgSender !== $this->owner) {
                    echo "Ownership transfer failed: Only owner can transfer\n";
                    return false;
                }
                
                $this->owner = $newOwner;
                echo "Ownership transferred to $newOwner\n";
                return true;
            }
        };
        
        $web3->setDefaultAccount($accounts[0]);
        $secureAddress = $web3->deployContract($secureContract);
        
        echo "Secure contract deployed at: $secureAddress\n";
        
        // Test secure contract
        echo "\nTesting secure contract:\n";
        
        $secureOwner = $web3->callContract($secureAddress, 'getOwner');
        echo "Secure contract owner: $secureOwner\n";
        
        // Try to attack secure contract
        echo "\nAttempting attack on secure contract:\n";
        $web3->setDefaultAccount($accounts[1]);
        
        $attackTx = $web3->sendTransaction($secureAddress, 0, [
            'method' => 'transferOwnership',
            'params' => [$accounts[1]]
        ]);
        
        $ownerAfterAttack = $web3->callContract($secureAddress, 'getOwner');
        echo "Owner after attack: $ownerAfterAttack\n";
        
        // Proper ownership transfer
        echo "\nProper ownership transfer:\n";
        $web3->setDefaultAccount($accounts[0]);
        
        $properTx = $web3->sendTransaction($secureAddress, 0, [
            'method' => 'transferOwnership',
            'params' => [$accounts[2]]
        ]);
        
        $finalOwner = $web3->callContract($secureAddress, 'getOwner');
        echo "Final owner: $finalOwner\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nSmart Contract Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Contract Design:\n";
        echo "   • Keep contracts simple and focused\n";
        echo "   • Use proper access control\n";
        echo "   • Implement input validation\n";
        echo "   • Use events for important actions\n";
        echo "   • Follow the Checks-Effects-Interactions pattern\n\n";
        
        echo "2. Security:\n";
        echo "   • Protect against reentrancy\n";
        echo "   • Use proper modifiers\n";
        echo "   • Implement overflow/underflow protection\n";
        echo "   • Use secure random number generation\n";
        echo "   • Audit contracts before deployment\n\n";
        
        echo "3. Gas Optimization:\n";
        echo "   • Minimize storage operations\n";
        echo "   • Use appropriate data types\n";
        echo "   • Optimize loops and iterations\n";
        echo "   • Use libraries for common functions\n";
        echo "   • Batch operations when possible\n\n";
        
        echo "4. Testing:\n";
        echo "   • Write comprehensive tests\n";
        echo "   • Test edge cases\n";
        echo "   • Use testnets before mainnet\n";
        echo "   • Test with different accounts\n";
        echo "   • Simulate various scenarios\n\n";
        
        echo "5. Deployment:\n";
        echo "   • Use verified contracts\n";
        echo "   • Implement proper versioning\n";
        echo "   • Use upgrade patterns when needed\n";
        echo "   • Document contract interfaces\n";
        echo "   • Monitor contract performance";
    }
    
    public function runAllExamples(): void
    {
        echo "Smart Contracts Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateBasicContract();
        $this->demonstrateERC20Token();
        $this->demonstrateContractInteraction();
        $this->demonstrateContractSecurity();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runSmartContractsDemo(): void
{
    $examples = new SmartContractExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSmartContractsDemo();
}
?>
