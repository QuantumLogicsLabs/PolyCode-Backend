<?php
/**
 * Decentralized Applications (DApps) in PHP
 * 
 * Building DApps, Web3 integration, and decentralized services.
 */

// DApp Framework
class DAppFramework
{
    private Web3Client $web3;
    private array $contracts = [];
    private array $accounts = [];
    private string $currentAccount;
    private array $eventListeners = [];
    
    public function __construct(Web3Client $web3)
    {
        $this->web3 = $web3;
        $this->accounts = $web3->getProvider()->getAccounts();
        $this->currentAccount = $this->accounts[0] ?? '';
    }
    
    public function connect(): bool
    {
        if (empty($this->accounts)) {
            echo "No accounts available\n";
            return false;
        }
        
        echo "Connected to Web3\n";
        echo "Current account: {$this->currentAccount}\n";
        echo "Available accounts: " . count($this->accounts) . "\n";
        
        return true;
    }
    
    public function switchAccount(string $account): void
    {
        if (in_array($account, $this->accounts)) {
            $this->currentAccount = $account;
            $this->web3->setDefaultAccount($account);
            echo "Switched to account: $account\n";
        } else {
            echo "Account not found: $account\n";
        }
    }
    
    public function getCurrentAccount(): string
    {
        return $this->currentAccount;
    }
    
    public function getBalance(): float
    {
        return $this->web3->getBalance($this->currentAccount);
    }
    
    public function deployContract(string $name, SmartContract $contract): string
    {
        $address = $this->web3->deployContract($contract);
        $this->contracts[$name] = [
            'address' => $address,
            'contract' => $contract
        ];
        
        echo "Contract '$name' deployed at: $address\n";
        return $address;
    }
    
    public function callContract(string $name, string $method, array $params = []): mixed
    {
        if (!isset($this->contracts[$name])) {
            throw new Exception("Contract '$name' not found");
        }
        
        $contractInfo = $this->contracts[$name];
        return $this->web3->callContract($contractInfo['address'], $method, $params);
    }
    
    public function sendTransaction(string $to, float $value, array $data = []): string
    {
        return $this->web3->sendTransaction($to, $value, $data);
    }
    
    public function addEventListener(string $event, callable $listener): void
    {
        if (!isset($this->eventListeners[$event])) {
            $this->eventListeners[$event] = [];
        }
        
        $this->eventListeners[$event][] = $listener;
    }
    
    public function emitEvent(string $event, array $data = []): void
    {
        if (isset($this->eventListeners[$event])) {
            foreach ($this->eventListeners[$event] as $listener) {
                $listener($data);
            }
        }
    }
    
    public function getContract(string $name): ?SmartContract
    {
        return $this->contracts[$name]['contract'] ?? null;
    }
    
    public function getContractAddress(string $name): ?string
    {
        return $this->contracts[$name]['address'] ?? null;
    }
    
    public function getAccounts(): array
    {
        return $this->accounts;
    }
    
    public function getWeb3(): Web3Client
    {
        return $this->web3;
    }
}

// Decentralized Storage
class DecentralizedStorage
{
    private array $storage = [];
    private array $permissions = [];
    private string $owner;
    
    public function __construct(string $owner)
    {
        $this->owner = $owner;
    }
    
    public function store(string $key, string $data, string $owner): bool
    {
        if ($this->hasPermission($owner, 'write')) {
            $this->storage[$key] = [
                'data' => $data,
                'owner' => $owner,
                'timestamp' => time(),
                'hash' => hash('sha256', $data)
            ];
            
            echo "Stored data for key: $key\n";
            return true;
        }
        
        echo "Permission denied for storing data\n";
        return false;
    }
    
    public function retrieve(string $key, string $requester): ?string
    {
        if (!$this->hasPermission($requester, 'read')) {
            echo "Permission denied for retrieving data\n";
            return null;
        }
        
        if (!isset($this->storage[$key])) {
            echo "Data not found for key: $key\n";
            return null;
        }
        
        $data = $this->storage[$key];
        
        // Verify data integrity
        if (hash('sha256', $data['data']) !== $data['hash']) {
            echo "Data integrity check failed\n";
            return null;
        }
        
        echo "Retrieved data for key: $key\n";
        return $data['data'];
    }
    
    public function grantPermission(string $key, string $user, string $permission): bool
    {
        if ($this->isOwner($key)) {
            if (!isset($this->permissions[$key])) {
                $this->permissions[$key] = [];
            }
            
            $this->permissions[$key][$user][] = $permission;
            echo "Granted $permission permission to $user for key: $key\n";
            return true;
        }
        
        echo "Only owner can grant permissions\n";
        return false;
    }
    
    public function revokePermission(string $key, string $user, string $permission): bool
    {
        if ($this->isOwner($key)) {
            if (isset($this->permissions[$key][$user])) {
                $permissions = $this->permissions[$key][$user];
                $this->permissions[$key][$user] = array_filter($permissions, fn($p) => $p !== $permission);
                
                if (empty($this->permissions[$key][$user])) {
                    unset($this->permissions[$key][$user]);
                }
                
                echo "Revoked $permission permission from $user for key: $key\n";
                return true;
            }
        }
        
        echo "Only owner can revoke permissions\n";
        return false;
    }
    
    private function hasPermission(string $user, string $permission): bool
    {
        // Owner has all permissions
        if ($user === $this->owner) {
            return true;
        }
        
        // Check specific permissions
        foreach ($this->permissions as $key => $userPermissions) {
            if (isset($userPermissions[$user]) && in_array($permission, $userPermissions[$user])) {
                return true;
            }
        }
        
        // Default read permission for public data
        if ($permission === 'read') {
            return true;
        }
        
        return false;
    }
    
    private function isOwner(string $key): bool
    {
        return isset($this->storage[$key]) && $this->storage[$key]['owner'] === $this->owner;
    }
    
    public function getKeys(): array
    {
        return array_keys($this->storage);
    }
    
    public function getMetadata(string $key): ?array
    {
        if (isset($this->storage[$key])) {
            $data = $this->storage[$key];
            return [
                'owner' => $data['owner'],
                'timestamp' => $data['timestamp'],
                'hash' => $data['hash'],
                'permissions' => $this->permissions[$key] ?? []
            ];
        }
        
        return null;
    }
}

// Decentralized Identity
class DecentralizedIdentity
{
    private array $identities = [];
    private array $verifiableCredentials = [];
    private array $trustScores = [];
    
    public function createIdentity(string $address, array $profile = []): string
    {
        $did = 'did:ethr:' . substr($address, 2) . ':' . uniqid();
        
        $this->identities[$did] = [
            'address' => $address,
            'profile' => $profile,
            'created' => time(),
            'updated' => time(),
            'publicKey' => $this->generatePublicKey(),
            'verified' => false
        ];
        
        $this->trustScores[$did] = 50; // Initial trust score
        
        echo "Created identity: $did\n";
        return $did;
    }
    
    public function verifyIdentity(string $did): bool
    {
        if (!isset($this->identities[$did])) {
            echo "Identity not found: $did\n";
            return false;
        }
        
        $this->identities[$did]['verified'] = true;
        $this->identities[$did]['updated'] = time();
        
        echo "Verified identity: $did\n";
        return true;
    }
    
    public function issueCredential(string $issuer, string $subject, array $credential): string
    {
        $vcId = 'vc:' . uniqid();
        
        $verifiableCredential = [
            '@context' => ['https://www.w3.org/2018/credentials/v1'],
            'id' => $vcId,
            'type' => ['VerifiableCredential'],
            'issuer' => $issuer,
            'issuanceDate' => date('c'),
            'credentialSubject' => [
                'id' => $subject,
                'type' => $credential['type'] ?? 'Person',
                'claims' => $credential['claims'] ?? []
            ],
            'proof' => [
                'type' => 'Ed25519Signature2018',
                'creator' => $issuer,
                'proofPurpose' => 'assertionMethod',
                'verificationMethod' => $issuer . '#key-1',
                'created' => date('c'),
                'jws' => $this->signCredential($credential, $issuer)
            ]
        ];
        
        $this->verifiableCredentials[$vcId] = $verifiableCredential;
        
        // Update trust score
        $this->updateTrustScore($subject, 10);
        
        echo "Issued credential: $vcId\n";
        return $vcId;
    }
    
    public function verifyCredential(string $vcId): bool
    {
        if (!isset($this->verifiableCredentials[$vcId])) {
            echo "Credential not found: $vcId\n";
            return false;
        }
        
        $vc = $this->verifiableCredentials[$vcId];
        
        // Verify signature (simplified)
        $isValid = $this->verifySignature($vc['credentialSubject']['claims'], $vc['proof']['jws'], $vc['issuer']);
        
        if ($isValid) {
            echo "Credential verified: $vcId\n";
            return true;
        } else {
            echo "Credential verification failed: $vcId\n";
            return false;
        }
    }
    
    public function getIdentity(string $did): ?array
    {
        return $this->identities[$did] ?? null;
    }
    
    public function getCredential(string $vcId): ?array
    {
        return $this->verifiableCredentials[$vcId] ?? null;
    }
    
    public function getTrustScore(string $did): int
    {
        return $this->trustScores[$did] ?? 0;
    }
    
    public function updateTrustScore(string $did, int $change): void
    {
        if (isset($this->trustScores[$did])) {
            $this->trustScores[$did] = max(0, min(100, $this->trustScores[$did] + $change));
            echo "Updated trust score for $did: {$this->trustScores[$did]}\n";
        }
    }
    
    private function generatePublicKey(): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }
    
    private function signCredential(array $credential, string $issuer): string
    {
        // Simplified signing (in practice, use proper cryptographic signing)
        return hash_hmac('sha256', json_encode($credential), $issuer);
    }
    
    private function verifySignature(array $data, string $signature, string $publicKey): bool
    {
        // Simplified verification (in practice, use proper cryptographic verification)
        $expectedSignature = hash_hmac('sha256', json_encode($data), $publicKey);
        return hash_equals($expectedSignature, $signature);
    }
    
    public function searchIdentities(array $criteria): array
    {
        $results = [];
        
        foreach ($this->identities as $did => $identity) {
            $matches = true;
            
            foreach ($criteria as $key => $value) {
                if (!isset($identity[$key]) || $identity[$key] !== $value) {
                    $matches = false;
                    break;
                }
            }
            
            if ($matches) {
                $results[$did] = $identity;
            }
        }
        
        return $results;
    }
}

// Decentralized Marketplace
class DecentralizedMarketplace
{
    private DAppFramework $dapp;
    private array $listings = [];
    private array $orders = [];
    private array $reviews = [];
    private ERC20Token $token;
    
    public function __construct(DAppFramework $dapp, ERC20Token $token)
    {
        $this->dapp = $dapp;
        $this->token = $token;
    }
    
    public function createListing(string $seller, string $title, string $description, float $price, int $quantity): string
    {
        $listingId = uniqid('listing_');
        
        $this->listings[$listingId] = [
            'id' => $listingId,
            'seller' => $seller,
            'title' => $title,
            'description' => $description,
            'price' => $price,
            'quantity' => $quantity,
            'available' => $quantity,
            'created' => time(),
            'status' => 'active'
        ];
        
        $this->dapp->emitEvent('listing_created', [
            'listing_id' => $listingId,
            'seller' => $seller,
            'title' => $title
        ]);
        
        echo "Created listing: $listingId\n";
        return $listingId;
    }
    
    public function createOrder(string $buyer, string $listingId, int $quantity): string
    {
        if (!isset($this->listings[$listingId])) {
            throw new Exception("Listing not found: $listingId");
        }
        
        $listing = $this->listings[$listingId];
        
        if ($listing['available'] < $quantity) {
            throw new Exception("Insufficient quantity available");
        }
        
        $orderId = uniqid('order_');
        $totalPrice = $listing['price'] * $quantity;
        
        $this->orders[$orderId] = [
            'id' => $orderId,
            'buyer' => $buyer,
            'listing_id' => $listingId,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'created' => time()
        ];
        
        $this->dapp->emitEvent('order_created', [
            'order_id' => $orderId,
            'buyer' => $buyer,
            'listing_id' => $listingId,
            'quantity' => $quantity,
            'total_price' => $totalPrice
        ]);
        
        echo "Created order: $orderId\n";
        return $orderId;
    }
    
    public function executeOrder(string $orderId): bool
    {
        if (!isset($this->orders[$orderId])) {
            throw new Exception("Order not found: $orderId");
        }
        
        $order = $this->orders[$orderId];
        $listing = $this->listings[$order['listing_id']];
        
        // Check if buyer has sufficient tokens
        $buyerBalance = $this->dapp->callContract('marketplace_token', 'balanceOf', [$order['buyer']]);
        
        if (bccomp($buyerBalance, (string)$order['total_price']) < 0) {
            echo "Insufficient token balance\n";
            return false;
        }
        
        // Transfer tokens from buyer to seller
        $this->dapp->sendTransaction($this->dapp->getContractAddress('marketplace_token'), 0, [
            'method' => 'transfer',
            'params' => [$listing['seller'], (string)$order['total_price']]
        ]);
        
        // Update listing quantity
        $this->listings[$order['listing_id']]['available'] -= $order['quantity'];
        
        // Update order status
        $this->orders[$orderId]['status'] = 'completed';
        $this->orders[$orderId]['completed'] = time();
        
        $this->dapp->emitEvent('order_completed', [
            'order_id' => $orderId,
            'buyer' => $order['buyer'],
            'seller' => $listing['seller'],
            'quantity' => $order['quantity']
        ]);
        
        echo "Order completed: $orderId\n";
        return true;
    }
    
    public function addReview(string $reviewer, string $orderId, int $rating, string $comment): string
    {
        if (!isset($this->orders[$orderId])) {
            throw new Exception("Order not found: $orderId");
        }
        
        $order = $this->orders[$orderId];
        $listing = $this->listings[$order['listing_id']];
        
        if ($order['buyer'] !== $reviewer) {
            throw new Exception("Only buyer can review");
        }
        
        if ($order['status'] !== 'completed') {
            throw new Exception("Can only review completed orders");
        }
        
        $reviewId = uniqid('review_');
        
        $this->reviews[$reviewId] = [
            'id' => $reviewId,
            'reviewer' => $reviewer,
            'seller' => $listing['seller'],
            'order_id' => $orderId,
            'rating' => $rating,
            'comment' => $comment,
            'created' => time()
        ];
        
        $this->dapp->emitEvent('review_added', [
            'review_id' => $reviewId,
            'reviewer' => $reviewer,
            'seller' => $listing['seller'],
            'rating' => $rating
        ]);
        
        echo "Added review: $reviewId\n";
        return $reviewId;
    }
    
    public function getListings(array $filters = []): array
    {
        $results = [];
        
        foreach ($this->listings as $listing) {
            $matches = true;
            
            foreach ($filters as $key => $value) {
                if (!isset($listing[$key]) || $listing[$key] !== $value) {
                    $matches = false;
                    break;
                }
            }
            
            if ($matches) {
                $results[] = $listing;
            }
        }
        
        return $results;
    }
    
    public function getListing(string $listingId): ?array
    {
        return $this->listings[$listingId] ?? null;
    }
    
    public function getOrder(string $orderId): ?array
    {
        return $this->orders[$orderId] ?? null;
    }
    
    public function getReviews(string $seller = null): array
    {
        if ($seller) {
            return array_filter($this->reviews, fn($review) => $review['seller'] === $seller);
        }
        
        return $this->reviews;
    }
    
    public function getSellerRating(string $seller): float
    {
        $sellerReviews = $this->getReviews($seller);
        
        if (empty($sellerReviews)) {
            return 0.0;
        }
        
        $totalRating = 0;
        foreach ($sellerReviews as $review) {
            $totalRating += $review['rating'];
        }
        
        return $totalRating / count($sellerReviews);
    }
}

// DApp Examples
class DecentralizedAppsExamples
{
    public function demonstrateDAppFramework(): void
    {
        echo "DApp Framework Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Create Web3 provider and client
        $provider = new SimulatedWeb3Provider();
        $web3 = new Web3Client($provider);
        
        // Create DApp
        $dapp = new DAppFramework($web3);
        
        // Connect to Web3
        $connected = $dapp->connect();
        
        if ($connected) {
            echo "Current account: {$dapp->getCurrentAccount()}\n";
            echo "Balance: " . $dapp->getBalance() . " ETH\n";
            
            // Switch accounts
            $accounts = $dapp->getAccounts();
            if (count($accounts) > 1) {
                $dapp->switchAccount($accounts[1]);
                echo "Switched to: {$dapp->getCurrentAccount()}\n";
                echo "New balance: " . $dapp->getBalance() . " ETH\n";
            }
            
            // Deploy contract
            $token = new ERC20Token('DAppToken', 'DAPP', '1000000000000000000000000');
            $dapp->deployContract('token', $token);
            
            // Add event listeners
            $dapp->addEventListener('transfer', function($data) {
                echo "Transfer event: " . json_encode($data) . "\n";
            });
            
            // Emit event
            $dapp->emitEvent('transfer', [
                'from' => $accounts[0],
                'to' => $accounts[1],
                'amount' => 100
            ]);
            
            // Call contract
            $totalSupply = $dapp->callContract('token', 'totalSupply');
            echo "Token total supply: $totalSupply\n";
        }
    }
    
    public function demonstrateDecentralizedStorage(): void
    {
        echo "\nDecentralized Storage Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $storage = new DecentralizedStorage('owner_address');
        
        // Store data
        $storage->store('document1', 'This is a confidential document', 'owner_address');
        $storage->store('image1', 'base64_encoded_image_data', 'owner_address');
        
        // Grant permissions
        $storage->grantPermission('document1', 'user1', 'read');
        $storage->grantPermission('document1', 'user2', 'read');
        $storage->grantPermission('document1', 'user1', 'write');
        
        // Retrieve data
        echo "Retrieving data:\n";
        $data1 = $storage->retrieve('document1', 'owner_address');
        echo "Owner retrieved: $data1\n";
        
        $data2 = $storage->retrieve('document1', 'user1');
        echo "User1 retrieved: $data2\n";
        
        $data3 = $storage->retrieve('document1', 'user2');
        echo "User2 retrieved: $data3\n";
        
        // Try unauthorized access
        $data4 = $storage->retrieve('document1', 'unauthorized_user');
        echo "Unauthorized retrieved: " . ($data4 ?? 'null') . "\n";
        
        // Revoke permission
        $storage->revokePermission('document1', 'user2', 'read');
        $data5 = $storage->retrieve('document1', 'user2');
        echo "User2 after revoke: " . ($data5 ?? 'null') . "\n";
        
        // Show metadata
        echo "\nMetadata:\n";
        $metadata = $storage->getMetadata('document1');
        echo "Document1 metadata: " . json_encode($metadata, JSON_PRETTY_PRINT) . "\n";
        
        // Show all keys
        echo "\nAll keys: " . implode(', ', $storage->getKeys()) . "\n";
    }
    
    public function demonstrateDecentralizedIdentity(): void
    {
        echo "\nDecentralized Identity Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $identity = new DecentralizedIdentity();
        
        // Create identities
        $did1 = $identity->createIdentity('0x1234567890123456789012345678901234567890', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'type' => 'individual'
        ]);
        
        $did2 = $identity->createIdentity('0x2345678901234567890123456789012345678901', [
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'type' => 'individual'
        ]);
        
        // Verify identities
        $identity->verifyIdentity($did1);
        $identity->verifyIdentity($did2);
        
        // Issue credentials
        echo "\nIssuing credentials:\n";
        $vc1 = $identity->issueCredential($did1, $did2, [
            'type' => 'EducationCredential',
            'claims' => [
                'degree' => 'Bachelor of Science',
                'university' => 'Tech University',
                'year' => 2020
            ]
        ]);
        
        $vc2 = $identity->issueCredential($did2, $did1, [
            'type' => 'WorkExperience',
            'claims' => [
                'position' => 'Software Developer',
                'company' => 'Tech Corp',
                'years' => 3
            ]
        ]);
        
        // Verify credentials
        echo "\nVerifying credentials:\n";
        $identity->verifyCredential($vc1);
        $identity->verifyCredential($vc2);
        
        // Show trust scores
        echo "\nTrust scores:\n";
        echo "Alice: " . $identity->getTrustScore($did1) . "\n";
        echo "Bob: " . $identity->getTrustScore($did2) . "\n";
        
        // Update trust scores
        echo "\nUpdating trust scores:\n";
        $identity->updateTrustScore($did1, 20);
        $identity->updateTrustScore($did2, -10);
        
        echo "Alice: " . $identity->getTrustScore($did1) . "\n";
        echo "Bob: " . $identity->getTrustScore($did2) . "\n";
        
        // Search identities
        echo "\nSearching identities:\n";
        $results = $identity->searchIdentities(['verified' => true]);
        echo "Verified identities: " . count($results) . "\n";
        
        foreach ($results as $did => $identityData) {
            echo "  $did: {$identityData['profile']['name']}\n";
        }
    }
    
    public function demonstrateMarketplace(): void
    {
        echo "\nDecentralized Marketplace Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Setup DApp and token
        $provider = new SimulatedWeb3Provider();
        $web3 = new Web3Client($provider);
        $dapp = new DAppFramework($web3);
        
        $token = new ERC20Token('MarketToken', 'MT', '10000000000000000000000000');
        $dapp->deployContract('marketplace_token', $token);
        
        $marketplace = new DecentralizedMarketplace($dapp, $token);
        
        // Get accounts
        $accounts = $dapp->getAccounts();
        $seller = $accounts[0];
        $buyer = $accounts[1];
        
        // Create listings
        echo "Creating listings:\n";
        $listing1 = $marketplace->createListing($seller, 'Laptop', 'High-performance laptop', 1000.0, 5);
        $listing2 = $marketplace->createListing($seller, 'Phone', 'Latest smartphone', 800.0, 10);
        $listing3 = $marketplace->createListing($accounts[2], 'Tablet', 'Android tablet', 400.0, 3);
        
        // Show listings
        echo "\nAll listings:\n";
        $listings = $marketplace->getListings();
        foreach ($listings as $listing) {
            echo "  {$listing['title']}: \${$listing['price']} ({$listing['available']} available)\n";
        }
        
        // Create orders
        echo "\nCreating orders:\n";
        $order1 = $marketplace->createOrder($buyer, $listing1, 1);
        $order2 = $marketplace->createOrder($buyer, $listing2, 2);
        
        // Show orders
        echo "\nOrders:\n";
        foreach ([$order1, $order2] as $orderId) {
            $order = $marketplace->getOrder($orderId);
            echo "  Order {$order['id']}: {$order['quantity']} items, \${$order['total_price']}\n";
        }
        
        // Execute orders (simplified - in real DApp, this would require user approval)
        echo "\nExecuting orders:\n";
        $marketplace->executeOrder($order1);
        $marketplace->executeOrder($order2);
        
        // Add reviews
        echo "\nAdding reviews:\n";
        $review1 = $marketplace->addReview($buyer, $order1, 5, 'Great product, fast shipping!');
        $review2 = $marketplace->addReview($buyer, $order2, 4, 'Good quality, minor issues');
        
        // Show seller ratings
        echo "\nSeller ratings:\n";
        echo "$seller: " . $marketplace->getSellerRating($seller) . "/5\n";
        echo "{$accounts[2]}: " . $marketplace->getSellerRating($accounts[2]) . "/5\n";
        
        // Show all reviews
        echo "\nAll reviews:\n";
        $reviews = $marketplace->getReviews();
        foreach ($reviews as $review) {
            echo "  {$review['reviewer']} rated {$review['seller']}: {$review['rating']}/5\n";
            echo "    {$review['comment']}\n";
        }
        
        // Show updated listings
        echo "\nUpdated listings:\n";
        $updatedListings = $marketplace->getListings();
        foreach ($updatedListings as $listing) {
            echo "  {$listing['title']}: {$listing['available']}/{$listing['quantity']} available\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nDApp Best Practices\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "1. Architecture:\n";
        echo "   • Use modular design\n";
        echo "   • Implement proper error handling\n";
        echo "   • Use event-driven architecture\n";
        echo "   • Separate business logic from UI\n";
        echo "   • Implement proper state management\n\n";
        
        echo "2. Security:\n";
        echo "   • Use secure key management\n";
        echo "   • Implement proper authentication\n";
        echo "   • Validate all inputs\n";
        echo "   • Use secure communication\n";
        echo "   • Implement access controls\n\n";
        
        echo "3. User Experience:\n";
        echo "   • Provide clear feedback\n";
        echo "   • Handle network issues gracefully\n";
        echo "   • Implement proper loading states\n";
        echo "   • Use intuitive interfaces\n";
        echo "   • Provide transaction confirmations\n\n";
        
        echo "4. Performance:\n";
        echo "   • Optimize gas usage\n";
        echo "   • Use caching strategies\n";
        echo "   • Implement lazy loading\n";
        echo "   • Minimize contract interactions\n";
        echo "   • Use efficient data structures\n\n";
        
        echo "5. Testing:\n";
        echo "   • Test on testnets first\n";
        echo "   • Test edge cases\n";
        echo "   • Simulate network failures\n";
        echo "   • Test with different accounts\n";
        echo "   • Use automated testing";
    }
    
    public function runAllExamples(): void
    {
        echo "Decentralized Applications Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateDAppFramework();
        $this->demonstrateDecentralizedStorage();
        $this->demonstrateDecentralizedIdentity();
        $this->demonstrateMarketplace();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runDecentralizedAppsDemo(): void
{
    $examples = new DecentralizedAppsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runDecentralizedAppsDemo();
}
?>
