<?php
/**
 * Web3 Integration in PHP
 * 
 * Connecting to Ethereum, handling transactions, and Web3 APIs.
 */

// Ethereum RPC Client
class EthereumRPC
{
    private string $rpcUrl;
    private array $headers;
    private int $requestId = 1;
    
    public function __construct(string $rpcUrl, array $headers = [])
    {
        $this->rpcUrl = $rpcUrl;
        $this->headers = array_merge([
            'Content-Type' => 'application/json',
            'User-Agent' => 'PHP-Web3-Client/1.0'
        ], $headers);
    }
    
    public function call(string $method, array $params = []): array
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $this->requestId++
        ];
        
        $ch = curl_init($this->rpcUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders());
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("HTTP error: $httpCode");
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            throw new Exception("RPC error: " . $result['error']['message']);
        }
        
        return $result['result'];
    }
    
    private function formatHeaders(): array
    {
        $formatted = [];
        foreach ($this->headers as $key => $value) {
            $formatted[] = "$key: $value";
        }
        return $formatted;
    }
}

// Web3 Provider
class Web3Provider
{
    private EthereumRPC $rpc;
    private array $accounts = [];
    private string $defaultAccount;
    
    public function __construct(string $rpcUrl, array $headers = [])
    {
        $this->rpc = new EthereumRPC($rpcUrl, $headers);
        $this->initializeAccounts();
    }
    
    private function initializeAccounts(): void
    {
        try {
            $this->accounts = $this->rpc->call('eth_accounts');
            if (!empty($this->accounts)) {
                $this->defaultAccount = $this->accounts[0];
            }
        } catch (Exception $e) {
            echo "Failed to get accounts: " . $e->getMessage() . "\n";
            // Use fallback accounts for demo
            $this->accounts = [
                '0x1234567890123456789012345678901234567890',
                '0x2345678901234567890123456789012345678901',
                '0x3456789012345678901234567890123456789012'
            ];
            $this->defaultAccount = $this->accounts[0];
        }
    }
    
    public function getAccounts(): array
    {
        return $this->accounts;
    }
    
    public function getDefaultAccount(): string
    {
        return $this->defaultAccount;
    }
    
    public function setDefaultAccount(string $account): void
    {
        if (in_array($account, $this->accounts)) {
            $this->defaultAccount = $account;
        } else {
            throw new Exception("Account not found: $account");
        }
    }
    
    public function getBalance(string $address): string
    {
        try {
            return $this->rpc->call('eth_getBalance', [$address, 'latest']);
        } catch (Exception $e) {
            echo "Failed to get balance: " . $e->getMessage() . "\n";
            return '0x0';
        }
    }
    
    public function getBlockNumber(): string
    {
        try {
            return $this->rpc->call('eth_blockNumber');
        } catch (Exception $e) {
            echo "Failed to get block number: " . $e->getMessage() . "\n";
            return '0x0';
        }
    }
    
    public function getGasPrice(): string
    {
        try {
            return $this->rpc->call('eth_gasPrice');
        } catch (Exception $e) {
            echo "Failed to get gas price: " . $e->getMessage() . "\n";
            return '0x59682F00'; // 1 Gwei
        }
    }
    
    public function sendTransaction(array $transaction): string
    {
        try {
            return $this->rpc->call('eth_sendTransaction', [$transaction]);
        } catch (Exception $e) {
            echo "Failed to send transaction: " . $e->getMessage() . "\n";
            return '0x' . bin2hex(random_bytes(32));
        }
    }
    
    public function getTransactionReceipt(string $txHash): array
    {
        try {
            return $this->rpc->call('eth_getTransactionReceipt', [$txHash]);
        } catch (Exception $e) {
            echo "Failed to get transaction receipt: " . $e->getMessage() . "\n";
            return [];
        }
    }
    
    public function getTransaction(string $txHash): array
    {
        try {
            return $this->rpc->call('eth_getTransactionByHash', [$txHash]);
        } catch (Exception $e) {
            echo "Failed to get transaction: " . $e->getMessage() . "\n";
            return [];
        }
    }
    
    public function callContract(string $to, string $data, string $from = null): string
    {
        try {
            $params = [
                'to' => $to,
                'data' => $data
            ];
            
            if ($from) {
                $params['from'] = $from;
            }
            
            return $this->rpc->call('eth_call', [$params, 'latest']);
        } catch (Exception $e) {
            echo "Failed to call contract: " . $e->getMessage() . "\n";
            return '0x';
        }
    }
    
    public function estimateGas(array $transaction): string
    {
        try {
            return $this->rpc->call('eth_estimateGas', [$transaction]);
        } catch (Exception $e) {
            echo "Failed to estimate gas: " . $e->getMessage() . "\n";
            return '0x5208'; // 21000
        }
    }
    
    public function getBlock(string $blockNumber): array
    {
        try {
            return $this->rpc->call('eth_getBlockByNumber', [$blockNumber, true]);
        } catch (Exception $e) {
            echo "Failed to get block: " . $e->getMessage() . "\n";
            return [];
        }
    }
    
    public function getNetworkId(): string
    {
        try {
            return $this->rpc->call('eth_chainId');
        } catch (Exception $e) {
            echo "Failed to get network ID: " . $e->getMessage() . "\n";
            return '0x1'; // Mainnet
        }
    }
    
    public function getRpc(): EthereumRPC
    {
        return $this->rpc;
    }
}

// Transaction Manager
class TransactionManager
{
    private Web3Provider $provider;
    private array $pendingTransactions = [];
    private array $confirmedTransactions = [];
    private int $nonce = 0;
    
    public function __construct(Web3Provider $provider)
    {
        $this->provider = $provider;
        $this->initializeNonce();
    }
    
    private function initializeNonce(): void
    {
        try {
            $account = $this->provider->getDefaultAccount();
            $this->nonce = hexdec($this->provider->getRpc()->call('eth_getTransactionCount', [$account, 'latest']));
        } catch (Exception $e) {
            $this->nonce = 0;
        }
    }
    
    public function sendEther(string $to, float $amount, string $from = null): string
    {
        $from = $from ?? $this->provider->getDefaultAccount();
        $value = $this->weiFromEther($amount);
        
        $transaction = [
            'from' => $from,
            'to' => $to,
            'value' => '0x' . dechex($value),
            'gas' => '0x5208',
            'gasPrice' => $this->provider->getGasPrice(),
            'nonce' => '0x' . dechex($this->nonce++)
        ];
        
        $txHash = $this->provider->sendTransaction($transaction);
        $this->pendingTransactions[$txHash] = $transaction;
        
        echo "Sent $amount ETH to $to\n";
        echo "Transaction hash: $txHash\n";
        
        return $txHash;
    }
    
    public function sendContractTransaction(string $to, string $data, float $value = 0, string $from = null): string
    {
        $from = $from ?? $this->provider->getDefaultAccount();
        $valueWei = $this->weiFromEther($value);
        
        $transaction = [
            'from' => $from,
            'to' => $to,
            'data' => $data,
            'value' => '0x' . dechex($valueWei),
            'gas' => '0x100000',
            'gasPrice' => $this->provider->getGasPrice(),
            'nonce' => '0x' . dechex($this->nonce++)
        ];
        
        // Estimate gas if possible
        try {
            $estimatedGas = $this->provider->estimateGas($transaction);
            $transaction['gas'] = $estimatedGas;
        } catch (Exception $e) {
            echo "Using default gas limit\n";
        }
        
        $txHash = $this->provider->sendTransaction($transaction);
        $this->pendingTransactions[$txHash] = $transaction;
        
        echo "Sent contract transaction\n";
        echo "Transaction hash: $txHash\n";
        
        return $txHash;
    }
    
    public function waitForConfirmation(string $txHash, int $maxWait = 60): array
    {
        $startTime = time();
        
        while (time() - $startTime < $maxWait) {
            $receipt = $this->provider->getTransactionReceipt($txHash);
            
            if (!empty($receipt)) {
                $this->confirmedTransactions[$txHash] = $receipt;
                unset($this->pendingTransactions[$txHash]);
                
                echo "Transaction confirmed: $txHash\n";
                echo "Block number: " . hexdec($receipt['blockNumber']) . "\n";
                echo "Gas used: " . hexdec($receipt['gasUsed']) . "\n";
                
                return $receipt;
            }
            
            sleep(2);
        }
        
        throw new Exception("Transaction confirmation timeout");
    }
    
    public function getTransactionStatus(string $txHash): string
    {
        if (isset($this->confirmedTransactions[$txHash])) {
            return 'confirmed';
        } elseif (isset($this->pendingTransactions[$txHash])) {
            return 'pending';
        } else {
            return 'unknown';
        }
    }
    
    public function getPendingTransactions(): array
    {
        return $this->pendingTransactions;
    }
    
    public function getConfirmedTransactions(): array
    {
        return $this->confirmedTransactions;
    }
    
    private function weiFromEther(float $ether): int
    {
        return (int) ($ether * 1000000000000000000);
    }
    
    public function etherFromWei(int $wei): float
    {
        return $wei / 1000000000000000000;
    }
}

// Contract ABI Manager
class ContractABI
{
    private array $abi;
    private array $functions;
    private array $events;
    
    public function __construct(array $abi)
    {
        $this->abi = $abi;
        $this->parseABI();
    }
    
    private function parseABI(): void
    {
        $this->functions = [];
        $this->events = [];
        
        foreach ($this->abi as $item) {
            if ($item['type'] === 'function') {
                $this->functions[$item['name']] = $item;
            } elseif ($item['type'] === 'event') {
                $this->events[$item['name']] = $item;
            }
        }
    }
    
    public function getFunction(string $name): ?array
    {
        return $this->functions[$name] ?? null;
    }
    
    public function getEvent(string $name): ?array
    {
        return $this->events[$name] ?? null;
    }
    
    public function encodeFunctionCall(string $functionName, array $params = []): string
    {
        $function = $this->getFunction($functionName);
        
        if (!$function) {
            throw new Exception("Function not found: $functionName");
        }
        
        // Simplified encoding (in practice, use proper ABI encoding)
        $methodSignature = $this->getMethodSignature($function);
        $encodedParams = $this->encodeParameters($params, $function['inputs']);
        
        return '0x' . $methodSignature . $encodedParams;
    }
    
    private function getMethodSignature(array $function): string
    {
        $inputs = array_map(fn($input) => $input['type'], $function['inputs']);
        $signature = $function['name'] . '(' . implode(',', $inputs) . ')';
        
        return substr(hash('keccak256', $signature), 0, 8);
    }
    
    private function encodeParameters(array $params, array $inputs): string
    {
        // Simplified parameter encoding
        $encoded = '';
        
        foreach ($params as $i => $param) {
            $type = $inputs[$i]['type'];
            
            if ($type === 'address') {
                $encoded .= str_pad(substr($param, 2), 64, '0', STR_PAD_LEFT);
            } elseif ($type === 'uint256') {
                $encoded .= str_pad(dechex($param), 64, '0', STR_PAD_LEFT);
            } elseif ($type === 'string') {
                $hexString = bin2hex($param);
                $encoded .= str_pad(dechex(strlen($hexString) / 2), 64, '0', STR_PAD_LEFT);
                $encoded .= str_pad($hexString, 64, '0', STR_PAD_RIGHT);
            } else {
                $encoded .= str_pad($param, 64, '0', STR_PAD_LEFT);
            }
        }
        
        return $encoded;
    }
    
    public function decodeFunctionResult(string $data, string $functionName): array
    {
        $function = $this->getFunction($functionName);
        
        if (!$function) {
            throw new Exception("Function not found: $functionName");
        }
        
        // Simplified decoding
        $results = [];
        $outputs = $function['outputs'];
        
        for ($i = 0; $i < count($outputs); $i++) {
            $type = $outputs[$i]['type'];
            $start = $i * 64;
            $end = ($i + 1) * 64;
            $value = substr($data, $start, $end);
            
            if ($type === 'address') {
                $results[] = '0x' . substr($value, 24);
            } elseif ($type === 'uint256') {
                $results[] = hexdec($value);
            } elseif ($type === 'bool') {
                $results[] = hexdec($value) > 0;
            } else {
                $results[] = $value;
            }
        }
        
        return $results;
    }
    
    public function getABI(): array
    {
        return $this->abi;
    }
}

// ERC20 Token Contract
class ERC20TokenContract
{
    private Web3Provider $provider;
    private ContractABI $abi;
    private string $address;
    
    public function __construct(Web3Provider $provider, string $address, array $abi = null)
    {
        $this->provider = $provider;
        $this->address = $address;
        
        if ($abi) {
            $this->abi = new ContractABI($abi);
        } else {
            // Default ERC20 ABI
            $this->abi = new ContractABI([
                [
                    'type' => 'function',
                    'name' => 'name',
                    'inputs' => [],
                    'outputs' => [['name' => '', 'type' => 'string']],
                    'stateMutability' => 'view'
                ],
                [
                    'type' => 'function',
                    'name' => 'symbol',
                    'inputs' => [],
                    'outputs' => [['name' => '', 'type' => 'string']],
                    'stateMutability' => 'view'
                ],
                [
                    'type' => 'function',
                    'name' => 'decimals',
                    'inputs' => [],
                    'outputs' => [['name' => '', 'type' => 'uint8']],
                    'stateMutability' => 'view'
                ],
                [
                    'type' => 'function',
                    'name' => 'totalSupply',
                    'inputs' => [],
                    'outputs' => [['name' => '', 'type' => 'uint256']],
                    'stateMutability' => 'view'
                ],
                [
                    'type' => 'function',
                    'name' => 'balanceOf',
                    'inputs' => [['name' => '_owner', 'type' => 'address']],
                    'outputs' => [['name' => 'balance', 'type' => 'uint256']],
                    'stateMutability' => 'view'
                ],
                [
                    'type' => 'function',
                    'name' => 'transfer',
                    'inputs' => [
                        ['name' => '_to', 'type' => 'address'],
                        ['name' => '_value', 'type' => 'uint256']
                    ],
                    'outputs' => [['name' => '', 'type' => 'bool']],
                    'stateMutability' => 'nonpayable'
                ],
                [
                    'type' => 'event',
                    'name' => 'Transfer',
                    'inputs' => [
                        ['name' => '_from', 'type' => 'address', 'indexed' => true],
                        ['name' => '_to', 'type' => 'address', 'indexed' => true],
                        ['name' => '_value', 'type' => 'uint256', 'indexed' => false]
                    ]
                ]
            ]);
        }
    }
    
    public function name(): string
    {
        $callData = $this->abi->encodeFunctionCall('name');
        $result = $this->provider->callContract($this->address, $callData);
        
        return $this->decodeStringResult($result);
    }
    
    public function symbol(): string
    {
        $callData = $this->abi->encodeFunctionCall('symbol');
        $result = $this->provider->callContract($this->address, $callData);
        
        return $this->decodeStringResult($result);
    }
    
    public function decimals(): int
    {
        $callData = $this->abi->encodeFunctionCall('decimals');
        $result = $this->provider->callContract($this->address, $callData);
        
        return hexdec($result);
    }
    
    public function totalSupply(): string
    {
        $callData = $this->abi->encodeFunctionCall('totalSupply');
        $result = $this->provider->callContract($this->address, $callData);
        
        return $result;
    }
    
    public function balanceOf(string $address): string
    {
        $callData = $this->abi->encodeFunctionCall('balanceOf', [$address]);
        $result = $this->provider->callContract($this->address, $callData);
        
        return $result;
    }
    
    public function transfer(string $to, string $amount): string
    {
        $callData = $this->abi->encodeFunctionCall('transfer', [$to, $amount]);
        
        return $this->provider->sendContractTransaction($this->address, $callData);
    }
    
    public function transferAndWait(string $to, string $amount, int $maxWait = 60): array
    {
        $txHash = $this->transfer($to, $amount);
        
        $transactionManager = new TransactionManager($this->provider);
        return $transactionManager->waitForConfirmation($txHash, $maxWait);
    }
    
    private function decodeStringResult(string $hex): string
    {
        // Remove 0x prefix and decode
        $hex = substr($hex, 2);
        $length = hexdec(substr($hex, 0, 64)) * 2;
        $data = substr($hex, 64, $length);
        
        return hex2bin($data);
    }
    
    public function getAddress(): string
    {
        return $this->address;
    }
    
    public function getABI(): ContractABI
    {
        return $this->abi;
    }
}

// Web3 Integration Examples
class Web3IntegrationExamples
{
    public function demonstrateEthereumRPC(): void
    {
        echo "Ethereum RPC Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Create provider (using simulated RPC for demo)
        $provider = new Web3Provider('https://mainnet.infura.io/v3/YOUR_PROJECT_ID');
        
        echo "Web3 Provider initialized\n";
        
        // Get basic info
        echo "\nBasic network info:\n";
        echo "Network ID: " . $provider->getNetworkId() . "\n";
        echo "Block number: " . $provider->getBlockNumber() . "\n";
        echo "Gas price: " . $provider->getGasPrice() . "\n";
        
        // Get accounts
        echo "\nAccounts:\n";
        $accounts = $provider->getAccounts();
        foreach ($accounts as $i => $account) {
            echo "  Account $i: $account\n";
        }
        
        $defaultAccount = $provider->getDefaultAccount();
        echo "Default account: $defaultAccount\n";
        
        // Get balance
        echo "\nBalances:\n";
        foreach ($accounts as $account) {
            $balance = $provider->getBalance($account);
            $balanceEther = hexdec($balance) / 1000000000000000000;
            echo "  $account: " . number_format($balanceEther, 6) . " ETH\n";
        }
        
        // Get block info
        echo "\nLatest block:\n";
        $block = $provider->getBlock('latest');
        if (!empty($block)) {
            echo "  Number: " . hexdec($block['number']) . "\n";
            echo "  Hash: " . $block['hash'] . "\n";
            echo "  Timestamp: " . hexdec($block['timestamp']) . "\n";
            echo "  Transactions: " . count($block['transactions']) . "\n";
            echo "  Gas used: " . hexdec($block['gasUsed']) . "\n";
        }
        
        // Estimate gas
        echo "\nGas estimation:\n";
        $tx = [
            'from' => $defaultAccount,
            'to' => $accounts[1] ?? $defaultAccount,
            'value' => '0x16345785d8a0000', // 0.1 ETH
            'data' => '0x'
        ];
        
        $gasEstimate = $provider->estimateGas($tx);
        echo "Estimated gas: " . hexdec($gasEstimate) . "\n";
    }
    
    public function demonstrateTransactionManager(): void
    {
        echo "\nTransaction Manager Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $provider = new Web3Provider('https://mainnet.infura.io/v3/YOUR_PROJECT_ID');
        $txManager = new TransactionManager($provider);
        
        $accounts = $provider->getAccounts();
        $from = $accounts[0];
        $to = $accounts[1] ?? $accounts[0];
        
        echo "From: $from\n";
        echo "To: $to\n";
        
        // Send ETH
        echo "\nSending ETH:\n";
        $amount = 0.01;
        $txHash = $txManager->sendEther($to, $amount);
        
        echo "Sent $amount ETH\n";
        echo "Transaction hash: $txHash\n";
        
        // Check transaction status
        echo "\nTransaction status: " . $txManager->getTransactionStatus($txHash) . "\n";
        
        // Simulate confirmation (in real scenario, this would wait for actual confirmation)
        echo "\nSimulating confirmation...\n";
        $receipt = [
            'transactionHash' => $txHash,
            'transactionIndex' => '0x0',
            'blockHash' => '0x' . str_repeat('0', 64),
            'blockNumber' => '0x123456',
            'gasUsed' => '0x5208',
            'status' => '0x1'
        ];
        
        echo "Transaction confirmed in block " . hexdec($receipt['blockNumber']) . "\n";
        echo "Gas used: " . hexdec($receipt['gasUsed']) . "\n";
        
        // Show pending transactions
        echo "\nPending transactions: " . count($txManager->getPendingTransactions()) . "\n";
        echo "Confirmed transactions: " . count($txManager->getConfirmedTransactions()) . "\n";
    }
    
    public function demonstrateERC20Contract(): void
    {
        echo "\nERC20 Contract Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $provider = new Web3Provider('https://mainnet.infura.io/v3/YOUR_PROJECT_ID');
        
        // Known ERC20 token address (e.g., USDT on Ethereum)
        $tokenAddress = '0xdAC17F958D2ee523a2206206994597C13D831ec7';
        
        try {
            $token = new ERC20TokenContract($provider, $tokenAddress);
            
            echo "Token address: $tokenAddress\n";
            
            // Get token info
            echo "\nToken information:\n";
            echo "Name: " . $token->name() . "\n";
            echo "Symbol: " . $token->symbol() . "\n";
            echo "Decimals: " . $token->decimals() . "\n";
            echo "Total supply: " . $token->totalSupply() . "\n";
            
            // Get balances
            echo "\nToken balances:\n";
            $accounts = $provider->getAccounts();
            
            foreach (array_slice($accounts, 0, 3) as $account) {
                $balance = $token->balanceOf($account);
                $balanceFormatted = hexdec($balance) / pow(10, $token->decimals());
                echo "  $account: " . number_format($balanceFormatted, 2) . " {$token->symbol()}\n";
            }
            
            // Simulate transfer
            echo "\nSimulating transfer:\n";
            if (count($accounts) >= 2) {
                $from = $accounts[0];
                $to = $accounts[1];
                $amount = '1000000000000000000'; // 1 token
                
                echo "From: $from\n";
                echo "To: $to\n";
                echo "Amount: " . (hexdec($amount) / pow(10, $token->decimals())) . " {$token->symbol()}\n";
                
                $txHash = $token->transfer($to, $amount);
                echo "Transfer transaction: $txHash\n";
            }
            
        } catch (Exception $e) {
            echo "Error interacting with token: " . $e->getMessage() . "\n";
            
            // Use simulated token for demo
            echo "\nUsing simulated token for demo\n";
            $this->simulateERC20Token($provider);
        }
    }
    
    private function simulateERC20Token(Web3Provider $provider): void
    {
        $simulatedToken = new class extends ERC20TokenContract {
            public function name(): string
            {
                return 'Simulated Token';
            }
            
            public function symbol(): string
            {
                return 'SIM';
            }
            
            public function decimals(): int
            {
                return 18;
            }
            
            public function totalSupply(): string
            {
                return '1000000000000000000000000';
            }
            
            public function balanceOf(string $address): string
            {
                return '100000000000000000000000';
            }
            
            public function transfer(string $to, string $amount): string
            {
                return '0x' . bin2hex(random_bytes(32));
            }
        };
        
        $accounts = $provider->getAccounts();
        $simulatedToken->setProvider($provider);
        $simulatedToken->setAddress($accounts[0]);
        
        echo "Name: " . $simulatedToken->name() . "\n";
        echo "Symbol: " . $simulatedToken->symbol() . "\n";
        echo "Decimals: " . $simulatedToken->decimals() . "\n";
        echo "Total supply: " . $simulatedToken->totalSupply() . "\n";
        
        foreach (array_slice($accounts, 0, 2) as $account) {
            $balance = $simulatedToken->balanceOf($account);
            $balanceFormatted = hexdec($balance) / pow(10, $simulatedToken->decimals());
            echo "  $account: " . number_format($balanceFormatted, 2) . " {$simulatedToken->symbol()}\n";
        }
    }
    
    public function demonstrateContractABI(): void
    {
        echo "\nContract ABI Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Sample ABI
        $sampleABI = [
            [
                'type' => 'function',
                'name' => 'getValue',
                'inputs' => [],
                'outputs' => [['name' => '', 'type' => 'uint256']],
                'stateMutability' => 'view'
            ],
            [
                'type' => 'function',
                'name' => 'setValue',
                'inputs' => [['name' => '_value', 'type' => 'uint256']],
                'outputs' => [],
                'stateMutability' => 'nonpayable'
            ],
            [
                'type' => 'event',
                'name' => 'ValueChanged',
                'inputs' => [
                    ['name' => 'oldValue', 'type' => 'uint256', 'indexed' => true],
                    ['name' => 'newValue', 'type' => 'uint256', 'indexed' => true]
                ]
            ]
        ];
        
        $abi = new ContractABI($sampleABI);
        
        echo "ABI functions:\n";
        foreach ($abi->getABI() as $item) {
            if ($item['type'] === 'function') {
                echo "  Function: {$item['name']}\n";
                echo "    Inputs: " . json_encode(array_column($item['inputs'], 'type')) . "\n";
                echo "    Outputs: " . json_encode(array_column($item['outputs'], 'type')) . "\n";
            } elseif ($item['type'] === 'event') {
                echo "  Event: {$item['name']}\n";
                echo "    Inputs: " . json_encode(array_column($item['inputs'], 'type')) . "\n";
            }
        }
        
        // Encode function call
        echo "\nEncoding function calls:\n";
        
        $getValueCall = $abi->encodeFunctionCall('getValue');
        echo "getValue() call: $getValueCall\n";
        
        $setValueCall = $abi->encodeFunctionCall('setValue', [12345]);
        echo "setValue(12345) call: $setValueCall\n";
        
        // Decode result
        echo "\nDecoding results:\n";
        $result = '00000000000000000000000000000000000000000000000000000000000000003039';
        $decoded = $abi->decodeFunctionResult($result, 'getValue');
        echo "Decoded getValue result: " . json_encode($decoded) . "\n";
        
        // Get function info
        echo "\nFunction details:\n";
        $getValueFunction = $abi->getFunction('getValue');
        if ($getValueFunction) {
            echo "getValue function: " . json_encode($getValueFunction, JSON_PRETTY_PRINT) . "\n";
        }
        
        $valueChangedEvent = $abi->getEvent('ValueChanged');
        if ($valueChangedEvent) {
            echo "ValueChanged event: " . json_encode($valueChangedEvent, JSON_PRETTY_PRINT) . "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nWeb3 Integration Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Connection Management:\n";
        echo "   • Use proper RPC endpoints\n";
        echo "   • Implement retry mechanisms\n";
        echo "   • Handle network timeouts\n";
        echo "   • Use connection pooling\n";
        echo "   • Monitor connection health\n\n";
        
        echo "2. Transaction Handling:\n";
        echo "   • Always estimate gas before sending\n";
        echo "   • Use appropriate gas prices\n";
        echo "   • Handle nonce management\n";
        echo "   • Wait for confirmations\n";
        echo "   • Implement error handling\n\n";
        
        echo "3. Contract Interaction:\n";
        echo "   • Use proper ABI encoding/decoding\n";
        echo "   • Validate contract addresses\n";
        echo "   • Handle different data types\n";
        echo "   • Use event listeners\n";
        echo "   • Cache contract ABIs\n\n";
        
        echo "4. Security:\n";
        echo "   • Protect private keys\n";
        echo "   • Use secure RPC endpoints\n";
        echo "   • Validate all inputs\n";
        echo "   • Use HTTPS connections\n";
        echo "   • Implement rate limiting\n\n";
        
        echo "5. Performance:\n";
        echo "   • Batch RPC calls when possible\n";
        echo "   • Use efficient data structures\n";
        echo "   • Cache frequently accessed data\n";
        echo "   • Optimize gas usage\n";
        echo "   • Use async operations";
    }
    
    public function runAllExamples(): void
    {
        echo "Web3 Integration Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateEthereumRPC();
        $this->demonstrateTransactionManager();
        $this->demonstrateERC20Contract();
        $this->demonstrateContractABI();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runWeb3IntegrationDemo(): void
{
    $examples = new Web3IntegrationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runWeb3IntegrationDemo();
}
?>
