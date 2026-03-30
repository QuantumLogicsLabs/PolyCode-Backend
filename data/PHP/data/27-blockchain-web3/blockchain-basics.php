<?php
/**
 * Blockchain Fundamentals in PHP
 * 
 * Basic blockchain concepts, cryptography, and implementation.
 */

// Cryptographic Hash Functions
class CryptoHash
{
    public static function hash(string $data): string
    {
        return hash('sha256', $data);
    }
    
    public static function hashWithSalt(string $data, string $salt): string
    {
        return hash('sha256', $data . $salt);
    }
    
    public static function generateSalt(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
    
    public static function merkleRoot(array $hashes): string
    {
        if (empty($hashes)) {
            return '';
        }
        
        if (count($hashes) === 1) {
            return $hashes[0];
        }
        
        // If odd number of hashes, duplicate the last one
        if (count($hashes) % 2 === 1) {
            $hashes[] = $hashes[count($hashes) - 1];
        }
        
        $newHashes = [];
        for ($i = 0; $i < count($hashes); $i += 2) {
            $newHashes[] = self::hash($hashes[$i] . $hashes[$i + 1]);
        }
        
        return self::merkleRoot($newHashes);
    }
    
    public static function verifyMerkleProof(string $merkleRoot, string $targetHash, array $proof): bool
    {
        $currentHash = $targetHash;
        
        foreach ($proof as $item) {
            $direction = $item['direction'];
            $hash = $item['hash'];
            
            if ($direction === 'left') {
                $currentHash = self::hash($hash . $currentHash);
            } else {
                $currentHash = self::hash($currentHash . $hash);
            }
        }
        
        return $currentHash === $merkleRoot;
    }
}

// Digital Signature
class DigitalSignature
{
    private string $privateKey;
    private string $publicKey;
    
    public function __construct()
    {
        $this->generateKeyPair();
    }
    
    private function generateKeyPair(): void
    {
        // Simplified key generation (in practice, use proper cryptographic libraries)
        $this->privateKey = bin2hex(random_bytes(32));
        $this->publicKey = hash('sha256', $this->privateKey);
    }
    
    public function sign(string $message): string
    {
        // Simplified signing (in practice, use proper digital signatures)
        $signature = hash_hmac('sha256', $message, $this->privateKey);
        return $signature;
    }
    
    public function verify(string $message, string $signature, string $publicKey): bool
    {
        // Simplified verification (in practice, use proper digital signatures)
        $expectedSignature = hash_hmac('sha256', $message, $publicKey);
        return hash_equals($expectedSignature, $signature);
    }
    
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }
    
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}

// Transaction
class Transaction
{
    public string $id;
    public string $from;
    public string $to;
    public float $amount;
    public int $timestamp;
    public string $signature;
    public array $data;
    
    public function __construct(string $from, string $to, float $amount, array $data = [])
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->timestamp = time();
        $this->data = $data;
        $this->id = $this->calculateHash();
    }
    
    public function calculateHash(): string
    {
        $data = [
            'from' => $this->from,
            'to' => $this->to,
            'amount' => $this->amount,
            'timestamp' => $this->timestamp,
            'data' => $this->data
        ];
        
        return CryptoHash::hash(json_encode($data));
    }
    
    public function sign(string $privateKey): void
    {
        $this->signature = hash_hmac('sha256', $this->id, $privateKey);
    }
    
    public function isValid(): bool
    {
        // Check if the transaction ID matches the calculated hash
        if ($this->id !== $this->calculateHash()) {
            return false;
        }
        
        // Check if signature is valid (simplified)
        if (empty($this->signature)) {
            return false;
        }
        
        // Check amount is positive
        if ($this->amount <= 0) {
            return false;
        }
        
        return true;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'amount' => $this->amount,
            'timestamp' => $this->timestamp,
            'data' => $this->data,
            'signature' => $this->signature
        ];
    }
    
    public static function fromArray(array $data): self
    {
        $transaction = new self($data['from'], $data['to'], $data['amount'], $data['data'] ?? []);
        $transaction->timestamp = $data['timestamp'];
        $transaction->signature = $data['signature'];
        $transaction->id = $data['id'];
        
        return $transaction;
    }
}

// Block
class Block
{
    public int $index;
    public string $previousHash;
    public string $hash;
    public array $transactions;
    public int $timestamp;
    public int $nonce;
    public string $merkleRoot;
    
    public function __construct(int $index, string $previousHash, array $transactions = [])
    {
        $this->index = $index;
        $this->previousHash = $previousHash;
        $this->transactions = $transactions;
        $this->timestamp = time();
        $this->nonce = 0;
        $this->merkleRoot = $this->calculateMerkleRoot();
        $this->hash = $this->calculateHash();
    }
    
    public function calculateHash(): string
    {
        $data = [
            'index' => $this->index,
            'previousHash' => $this->previousHash,
            'timestamp' => $this->timestamp,
            'nonce' => $this->nonce,
            'merkleRoot' => $this->merkleRoot
        ];
        
        return CryptoHash::hash(json_encode($data));
    }
    
    public function calculateMerkleRoot(): string
    {
        $transactionHashes = [];
        
        foreach ($this->transactions as $transaction) {
            $transactionHashes[] = $transaction->id;
        }
        
        return CryptoHash::merkleRoot($transactionHashes);
    }
    
    public function mine(int $difficulty): bool
    {
        $target = str_repeat('0', $difficulty);
        
        while (substr($this->hash, 0, $difficulty) !== $target) {
            $this->nonce++;
            $this->hash = $this->calculateHash();
            
            // Prevent infinite loop
            if ($this->nonce > 1000000) {
                return false;
            }
        }
        
        echo "Block mined: {$this->hash} (nonce: {$this->nonce})\n";
        return true;
    }
    
    public function isValid(): bool
    {
        // Check if hash matches calculated hash
        if ($this->hash !== $this->calculateHash()) {
            return false;
        }
        
        // Check if merkle root is correct
        if ($this->merkleRoot !== $this->calculateMerkleRoot()) {
            return false;
        }
        
        // Check if all transactions are valid
        foreach ($this->transactions as $transaction) {
            if (!$transaction->isValid()) {
                return false;
            }
        }
        
        return true;
    }
    
    public function hasValidTransactions(): bool
    {
        foreach ($this->transactions as $transaction) {
            if (!$transaction->isValid()) {
                return false;
            }
        }
        return true;
    }
    
    public function toArray(): array
    {
        $transactionsArray = [];
        foreach ($this->transactions as $transaction) {
            $transactionsArray[] = $transaction->toArray();
        }
        
        return [
            'index' => $this->index,
            'previousHash' => $this->previousHash,
            'hash' => $this->hash,
            'timestamp' => $this->timestamp,
            'nonce' => $this->nonce,
            'merkleRoot' => $this->merkleRoot,
            'transactions' => $transactionsArray
        ];
    }
    
    public static function fromArray(array $data): self
    {
        $transactions = [];
        foreach ($data['transactions'] as $txData) {
            $transactions[] = Transaction::fromArray($txData);
        }
        
        $block = new self($data['index'], $data['previousHash'], $transactions);
        $block->timestamp = $data['timestamp'];
        $block->nonce = $data['nonce'];
        $block->hash = $data['hash'];
        $block->merkleRoot = $data['merkleRoot'];
        
        return $block;
    }
}

// Blockchain
class Blockchain
{
    private array $chain = [];
    private array $pendingTransactions = [];
    private int $difficulty;
    private float $miningReward;
    private array $balances = [];
    
    public function __construct(int $difficulty = 2, float $miningReward = 10.0)
    {
        $this->difficulty = $difficulty;
        $this->miningReward = $miningReward;
        $this->createGenesisBlock();
    }
    
    private function createGenesisBlock(): void
    {
        $genesisBlock = new Block(0, '0', []);
        $genesisBlock->mine($this->difficulty);
        $this->chain[] = $genesisBlock;
        
        echo "Genesis block created: {$genesisBlock->hash}\n";
    }
    
    public function getLatestBlock(): Block
    {
        return $this->chain[count($this->chain) - 1];
    }
    
    public function addTransaction(Transaction $transaction): bool
    {
        if (!$transaction->isValid()) {
            echo "Invalid transaction rejected\n";
            return false;
        }
        
        // Check if sender has sufficient balance
        if ($transaction->from !== 'system') {
            $balance = $this->getBalance($transaction->from);
            if ($balance < $transaction->amount) {
                echo "Insufficient balance for transaction\n";
                return false;
            }
        }
        
        $this->pendingTransactions[] = $transaction;
        echo "Transaction added to pending: {$transaction->id}\n";
        return true;
    }
    
    public function minePendingTransactions(string $minerAddress): Block
    {
        // Create mining reward transaction
        $rewardTransaction = new Transaction('system', $minerAddress, $this->miningReward);
        $rewardTransaction->sign('system');
        
        // Add reward transaction to pending
        $this->pendingTransactions[] = $rewardTransaction;
        
        // Create new block
        $block = new Block(
            count($this->chain),
            $this->getLatestBlock()->hash,
            $this->pendingTransactions
        );
        
        // Mine the block
        echo "Mining block...\n";
        $block->mine($this->difficulty);
        
        // Add block to chain
        $this->chain[] = $block;
        
        // Clear pending transactions
        $this->pendingTransactions = [];
        
        // Update balances
        $this->updateBalances();
        
        echo "Block added to chain: {$block->hash}\n";
        return $block;
    }
    
    public function getBalance(string $address): float
    {
        return $this->balances[$address] ?? 0.0;
    }
    
    private function updateBalances(): void
    {
        // Reset balances
        $this->balances = [];
        
        // Process all blocks
        foreach ($this->chain as $block) {
            foreach ($block->transactions as $transaction) {
                $this->balances[$transaction->from] = ($this->balances[$transaction->from] ?? 0) - $transaction->amount;
                $this->balances[$transaction->to] = ($this->balances[$transaction->to] ?? 0) + $transaction->amount;
            }
        }
    }
    
    public function isValid(): bool
    {
        // Check genesis block
        if ($this->chain[0]->index !== 0 || $this->chain[0]->previousHash !== '0') {
            return false;
        }
        
        // Check all blocks
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];
            
            // Check block hash
            if ($currentBlock->hash !== $currentBlock->calculateHash()) {
                echo "Invalid block hash at index {$currentBlock->index}\n";
                return false;
            }
            
            // Check previous hash
            if ($currentBlock->previousHash !== $previousBlock->hash) {
                echo "Invalid previous hash at index {$currentBlock->index}\n";
                return false;
            }
            
            // Check block validity
            if (!$currentBlock->isValid()) {
                echo "Invalid block at index {$currentBlock->index}\n";
                return false;
            }
        }
        
        return true;
    }
    
    public function getChain(): array
    {
        $chainArray = [];
        foreach ($this->chain as $block) {
            $chainArray[] = $block->toArray();
        }
        return $chainArray;
    }
    
    public function getPendingTransactions(): array
    {
        $transactionsArray = [];
        foreach ($this->pendingTransactions as $transaction) {
            $transactionsArray[] = $transaction->toArray();
        }
        return $transactionsArray;
    }
    
    public function getTransaction(string $transactionId): ?Transaction
    {
        // Search in pending transactions
        foreach ($this->pendingTransactions as $transaction) {
            if ($transaction->id === $transactionId) {
                return $transaction;
            }
        }
        
        // Search in blockchain
        foreach ($this->chain as $block) {
            foreach ($block->transactions as $transaction) {
                if ($transaction->id === $transactionId) {
                    return $transaction;
                }
            }
        }
        
        return null;
    }
    
    public function getBlock(int $index): ?Block
    {
        if (isset($this->chain[$index])) {
            return $this->chain[$index];
        }
        return null;
    }
    
    public function getStats(): array
    {
        $totalTransactions = 0;
        $totalAmount = 0;
        
        foreach ($this->chain as $block) {
            $totalTransactions += count($block->transactions);
            foreach ($block->transactions as $transaction) {
                $totalAmount += $transaction->amount;
            }
        }
        
        return [
            'blocks' => count($this->chain),
            'pending_transactions' => count($this->pendingTransactions),
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'difficulty' => $this->difficulty,
            'mining_reward' => $this->miningReward,
            'unique_addresses' => count($this->balances)
        ];
    }
}

// Blockchain Basics Examples
class BlockchainBasicsExamples
{
    public function demonstrateCryptography(): void
    {
        echo "Cryptography Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Hash functions
        echo "Hash Functions:\n";
        $data = "Hello, Blockchain!";
        $hash = CryptoHash::hash($data);
        echo "Data: $data\n";
        echo "SHA-256 Hash: $hash\n\n";
        
        // Hash with salt
        $salt = CryptoHash::generateSalt(16);
        $saltedHash = CryptoHash::hashWithSalt($data, $salt);
        echo "Salt: $salt\n";
        echo "Salted Hash: $saltedHash\n\n";
        
        // Merkle tree
        echo "Merkle Tree:\n";
        $transactions = [
            "tx1: Alice -> Bob: 10",
            "tx2: Bob -> Charlie: 5",
            "tx3: Charlie -> David: 3",
            "tx4: David -> Alice: 2"
        ];
        
        $hashes = array_map('CryptoHash::hash', $transactions);
        $merkleRoot = CryptoHash::merkleRoot($hashes);
        
        echo "Transactions:\n";
        foreach ($transactions as $tx) {
            echo "  $tx\n";
        }
        echo "Merkle Root: $merkleRoot\n\n";
        
        // Merkle proof
        echo "Merkle Proof:\n";
        $targetHash = $hashes[1]; // Second transaction
        $proof = [
            ['direction' => 'left', 'hash' => $hashes[0]],
            ['direction' => 'right', 'hash' => CryptoHash::hash($hashes[2] . $hashes[3])]
        ];
        
        $isValid = CryptoHash::verifyMerkleProof($merkleRoot, $targetHash, $proof);
        echo "Target Hash: $targetHash\n";
        echo "Proof Valid: " . ($isValid ? 'Yes' : 'No') . "\n\n";
        
        // Digital signatures
        echo "Digital Signatures:\n";
        $message = "This is a test message";
        $signature = new DigitalSignature();
        
        $signedMessage = $signature->sign($message);
        $publicKey = $signature->getPublicKey();
        
        echo "Message: $message\n";
        echo "Signature: $signedMessage\n";
        echo "Public Key: $publicKey\n";
        
        $isValid = $signature->verify($message, $signedMessage, $publicKey);
        echo "Signature Valid: " . ($isValid ? 'Yes' : 'No') . "\n";
        
        // Try to verify with different message
        $fakeMessage = "This is a fake message";
        $isFakeValid = $signature->verify($fakeMessage, $signedMessage, $publicKey);
        echo "Fake Message Valid: " . ($isFakeValid ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateTransactions(): void
    {
        echo "\nTransaction Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Create transactions
        $tx1 = new Transaction('Alice', 'Bob', 10.5, ['memo' => 'Payment for services']);
        $tx2 = new Transaction('Bob', 'Charlie', 5.0);
        $tx3 = new Transaction('Charlie', 'David', 2.75, ['type' => 'refund']);
        
        echo "Created transactions:\n";
        foreach ([$tx1, $tx2, $tx3] as $tx) {
            echo "  {$tx->id}\n";
            echo "    From: {$tx->from}\n";
            echo "    To: {$tx->to}\n";
            echo "    Amount: {$tx->amount}\n";
            echo "    Timestamp: {$tx->timestamp}\n";
            echo "    Data: " . json_encode($tx->data) . "\n\n";
        }
        
        // Sign transactions
        echo "Signing transactions:\n";
        $signature = new DigitalSignature();
        
        $tx1->sign($signature->getPrivateKey());
        $tx2->sign($signature->getPrivateKey());
        $tx3->sign($signature->getPrivateKey());
        
        echo "Transactions signed\n\n";
        
        // Validate transactions
        echo "Validating transactions:\n";
        foreach ([$tx1, $tx2, $tx3] as $tx) {
            $isValid = $tx->isValid();
            echo "  {$tx->id}: " . ($isValid ? 'Valid' : 'Invalid') . "\n";
        }
        
        // Create invalid transaction
        echo "\nCreating invalid transaction:\n";
        $invalidTx = new Transaction('Alice', 'Bob', -5.0); // Negative amount
        $isInvalidValid = $invalidTx->isValid();
        echo "  Invalid transaction: " . ($isInvalidValid ? 'Valid' : 'Invalid') . "\n";
        
        // Serialize and deserialize
        echo "\nSerialization:\n";
        $serialized = $tx1->toArray();
        echo "Serialized: " . json_encode($serialized, JSON_PRETTY_PRINT) . "\n";
        
        $deserialized = Transaction::fromArray($serialized);
        echo "Deserialized ID: {$deserialized->id}\n";
        echo "Deserialized matches original: " . ($deserialized->id === $tx1->id ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateBlockchain(): void
    {
        echo "\nBlockchain Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Create blockchain
        $blockchain = new Blockchain(2, 10.0);
        
        echo "Blockchain created with difficulty 2\n";
        echo "Genesis block hash: {$blockchain->getLatestBlock()->hash}\n\n";
        
        // Create transactions
        echo "Adding transactions:\n";
        $tx1 = new Transaction('Alice', 'Bob', 25.0);
        $tx2 = new Transaction('Bob', 'Charlie', 15.0);
        $tx3 = new Transaction('Charlie', 'David', 10.0);
        
        $signature = new DigitalSignature();
        $tx1->sign($signature->getPrivateKey());
        $tx2->sign($signature->getPrivateKey());
        $tx3->sign($signature->getPrivateKey());
        
        $blockchain->addTransaction($tx1);
        $blockchain->addTransaction($tx2);
        $blockchain->addTransaction($tx3);
        
        echo "Pending transactions: " . count($blockchain->getPendingTransactions()) . "\n\n";
        
        // Mine block
        echo "Mining block 1:\n";
        $block1 = $blockchain->minePendingTransactions('Miner1');
        echo "Block 1 hash: {$block1->hash}\n";
        echo "Block 1 transactions: " . count($block1->transactions) . "\n\n";
        
        // Add more transactions
        echo "Adding more transactions:\n";
        $tx4 = new Transaction('David', 'Alice', 5.0);
        $tx5 = new Transaction('Alice', 'Eve', 8.0);
        
        $tx4->sign($signature->getPrivateKey());
        $tx5->sign($signature->getPrivateKey());
        
        $blockchain->addTransaction($tx4);
        $blockchain->addTransaction($tx5);
        
        // Mine another block
        echo "\nMining block 2:\n";
        $block2 = $blockchain->minePendingTransactions('Miner2');
        echo "Block 2 hash: {$block2->hash}\n";
        echo "Block 2 transactions: " . count($block2->transactions) . "\n\n";
        
        // Show blockchain
        echo "Blockchain:\n";
        foreach ($blockchain->getChain() as $i => $block) {
            echo "  Block $i:\n";
            echo "    Hash: {$block['hash']}\n";
            echo "    Previous: {$block['previousHash']}\n";
            echo "    Transactions: " . count($block['transactions']) . "\n";
            echo "    Nonce: {$block['nonce']}\n";
            echo "    Merkle Root: {$block['merkleRoot']}\n\n";
        }
        
        // Show balances
        echo "Balances:\n";
        $addresses = ['Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Miner1', 'Miner2'];
        foreach ($addresses as $address) {
            $balance = $blockchain->getBalance($address);
            echo "  $address: $balance\n";
        }
        
        // Validate blockchain
        echo "\nBlockchain validation:\n";
        $isValid = $blockchain->isValid();
        echo "Blockchain is valid: " . ($isValid ? 'Yes' : 'No') . "\n";
        
        // Show stats
        echo "\nBlockchain stats:\n";
        $stats = $blockchain->getStats();
        foreach ($stats as $key => $value) {
            echo "  $key: $value\n";
        }
    }
    
    public function demonstrateMining(): void
    {
        echo "\nMining Demo\n";
        echo str_repeat("-", 15) . "\n";
        
        // Create blockchain with different difficulties
        echo "Testing different mining difficulties:\n";
        
        $difficulties = [1, 2, 3];
        
        foreach ($difficulties as $difficulty) {
            echo "\nDifficulty: $difficulty\n";
            
            $blockchain = new Blockchain($difficulty, 10.0);
            
            // Add transaction
            $tx = new Transaction('Alice', 'Bob', 5.0);
            $signature = new DigitalSignature();
            $tx->sign($signature->getPrivateKey());
            
            $blockchain->addTransaction($tx);
            
            // Mine and time it
            $startTime = microtime(true);
            $block = $blockchain->minePendingTransactions('Miner');
            $endTime = microtime(true);
            
            $miningTime = $endTime - $startTime;
            echo "Mining time: " . round($miningTime, 3) . " seconds\n";
            echo "Block hash: {$block->hash}\n";
            echo "Nonce: {$block->nonce}\n";
        }
        
        // Demonstrate proof of work
        echo "\nProof of Work demonstration:\n";
        $target = '00'; // Difficulty 1
        echo "Target: Hash must start with '$target'\n";
        
        $data = 'test data';
        $nonce = 0;
        $hash = '';
        
        echo "Mining for hash starting with '$target':\n";
        
        while (substr($hash, 0, strlen($target)) !== $target) {
            $nonce++;
            $hash = CryptoHash::hash($data . $nonce);
            
            if ($nonce % 10000 === 0) {
                echo "  Trying nonce: $nonce\n";
            }
        }
        
        echo "Found hash: $hash\n";
        echo "Nonce: $nonce\n";
        echo "Proof of work valid: " . (substr($hash, 0, strlen($target)) === $target ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nBlockchain Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Cryptography:\n";
        echo "   • Use strong hash functions (SHA-256, SHA-512)\n";
        echo "   • Implement proper digital signatures\n";
        echo "   • Use secure random number generation\n";
        echo "   • Protect private keys\n";
        echo "   • Use salt for hashing sensitive data\n\n";
        
        echo "2. Transactions:\n";
        echo "   • Validate all transaction fields\n";
        echo "   • Implement proper signature verification\n";
        echo "   • Check for sufficient funds\n";
        echo "   • Prevent double spending\n";
        echo "   • Use transaction fees\n\n";
        
        echo "3. Blocks:\n";
        echo "   • Include merkle root for transaction verification\n";
        echo "   • Implement proper proof of work\n";
        echo "   • Validate block structure\n";
        echo "   • Check previous hash linkage\n";
        echo "   • Limit block size\n\n";
        
        echo "4. Blockchain:\n";
        echo "   • Validate entire chain integrity\n";
        echo "   • Implement consensus mechanism\n";
        echo "   • Handle forks gracefully\n";
        echo "   • Maintain transaction pool\n";
        echo "   • Implement proper mining rewards\n\n";
        
        echo "5. Security:\n";
        echo "   • Use proper cryptographic libraries\n";
        echo "   • Implement network security\n";
        echo "   • Protect against 51% attacks\n";
        echo "   • Validate all inputs\n";
        echo "   • Use secure key management";
    }
    
    public function runAllExamples(): void
    {
        echo "Blockchain Fundamentals Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateCryptography();
        $this->demonstrateTransactions();
        $this->demonstrateBlockchain();
        $this->demonstrateMining();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runBlockchainBasicsDemo(): void
{
    $examples = new BlockchainBasicsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runBlockchainBasicsDemo();
}
?>
