<?php
/**
 * Advanced Pattern Combinations
 * 
 * Combining multiple design patterns to solve complex problems.
 */

// Combination: Repository + Unit of Work + Factory
interface UnitOfWork
{
    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;
    public function registerNew(object $entity): void;
    public function registerDirty(object $entity): void;
    public function registerDeleted(object $entity): void;
    public function commit(): void;
}

class DatabaseUnitOfWork implements UnitOfWork
{
    private array $newEntities = [];
    private array $dirtyEntities = [];
    private array $deletedEntities = [];
    private array $repositories = [];
    
    public function __construct()
    {
        $this->repositories['user'] = new UserRepository(User::class);
        $this->repositories['product'] = new ProductRepository(Product::class);
    }
    
    public function begin(): void
    {
        echo "Unit of Work: Begin transaction\n";
    }
    
    public function registerNew(object $entity): void
    {
        $this->newEntities[] = $entity;
        echo "Unit of Work: Registered new entity\n";
    }
    
    public function registerDirty(object $entity): void
    {
        $this->dirtyEntities[] = $entity;
        echo "Unit of Work: Registered dirty entity\n";
    }
    
    public function registerDeleted(object $entity): void
    {
        $this->deletedEntities[] = $entity;
        echo "Unit of Work: Registered deleted entity\n";
    }
    
    public function commit(): void
    {
        echo "Unit of Work: Committing changes\n";
        
        // Insert new entities
        foreach ($this->newEntities as $entity) {
            $this->getRepositoryForEntity($entity)->save($entity);
        }
        
        // Update dirty entities
        foreach ($this->dirtyEntities as $entity) {
            $this->getRepositoryForEntity($entity)->save($entity);
        }
        
        // Delete entities
        foreach ($this->deletedEntities as $entity) {
            $this->getRepositoryForEntity($entity)->delete($entity);
        }
        
        $this->clear();
        echo "Unit of Work: Transaction committed\n";
    }
    
    public function rollback(): void
    {
        echo "Unit of Work: Rolling back changes\n";
        $this->clear();
    }
    
    private function getRepositoryForEntity(object $entity): Repository
    {
        $className = get_class($entity);
        $entityType = strtolower(substr($className, strrpos($className, '\\') + 1));
        
        if (isset($this->repositories[$entityType])) {
            return $this->repositories[$entityType];
        }
        
        throw new Exception("No repository found for entity type: $entityType");
    }
    
    private function clear(): void
    {
        $this->newEntities = [];
        $this->dirtyEntities = [];
        $this->deletedEntities = [];
    }
    
    public function getRepository(string $type): Repository
    {
        return $this->repositories[$type] ?? throw new Exception("Repository not found: $type");
    }
}

// Entity Factory
class EntityFactory
{
    public static function createUser(string $name, string $email, int $age): User
    {
        return new User($name, $email, $age);
    }
    
    public static function createProduct(string $name, float $price, int $quantity): Product
    {
        return new Product($name, $price, $quantity);
    }
    
    public static function createOrder(array $items = [], string $status = 'pending'): Order
    {
        return new Order($items, $status);
    }
}

// Product Entity
class Product implements Entity
{
    private string $id;
    private string $name;
    private float $price;
    private int $quantity;
    
    public function __construct(string $name = '', float $price = 0.0, int $quantity = 0)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id): void
    {
        $this->id = $id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    public function getPrice(): float
    {
        return $this->price;
    }
    
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
    
    public function getQuantity(): int
    {
        return $this->quantity;
    }
    
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity
        ];
    }
    
    public function fromArray(array $data): void
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->price = $data['price'] ?? 0.0;
        $this->quantity = $data['quantity'] ?? 0;
    }
}

class ProductRepository implements Repository
{
    private array $storage = [];
    
    public function findById($id): ?object
    {
        return $this->storage[$id] ?? null;
    }
    
    public function findAll(): array
    {
        return array_values($this->storage);
    }
    
    public function save(object $entity): bool
    {
        $id = $entity->getId();
        
        if (!$id) {
            $id = uniqid('product_');
            $entity->setId($id);
        }
        
        $this->storage[$id] = $entity;
        echo "Product saved with ID: $id\n";
        return true;
    }
    
    public function delete(object $entity): bool
    {
        $id = $entity->getId();
        
        if (!isset($this->storage[$id])) {
            return false;
        }
        
        unset($this->storage[$id]);
        echo "Product deleted: $id\n";
        return true;
    }
    
    public function findBy(array $criteria): array
    {
        $results = [];
        
        foreach ($this->storage as $entity) {
            if ($this->matchesCriteria($entity, $criteria)) {
                $results[] = $entity;
            }
        }
        
        return $results;
    }
    
    private function matchesCriteria(object $entity, array $criteria): bool
    {
        $data = $entity->toArray();
        
        foreach ($criteria as $field => $value) {
            if (!isset($data[$field]) || $data[$field] !== $value) {
                return false;
            }
        }
        
        return true;
    }
}

// Order Entity
class Order implements Entity
{
    private string $id;
    private array $items;
    private string $status;
    private float $total;
    
    public function __construct(array $items = [], string $status = 'pending')
    {
        $this->items = $items;
        $this->status = $status;
        $this->total = $this->calculateTotal();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id): void
    {
        $this->id = $id;
    }
    
    public function getItems(): array
    {
        return $this->items;
    }
    
    public function setItems(array $items): void
    {
        $this->items = $items;
        $this->total = $this->calculateTotal();
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    
    public function getTotal(): float
    {
        return $this->total;
    }
    
    public function addItem(array $item): void
    {
        $this->items[] = $item;
        $this->total = $this->calculateTotal();
    }
    
    public function removeItem(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->total = $this->calculateTotal();
        }
    }
    
    private function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'items' => $this->items,
            'status' => $this->status,
            'total' => $this->total
        ];
    }
    
    public function fromArray(array $data): void
    {
        $this->id = $data['id'] ?? '';
        $this->items = $data['items'] ?? [];
        $this->status = $data['status'] ?? 'pending';
        $this->total = $data['total'] ?? 0;
    }
}

// Combination: Observer + Command + Memento
interface Observer
{
    public function update(string $event, array $data): void;
}

interface Subject
{
    public function attach(Observer $observer): void;
    public function detach(Observer $observer): void;
    public function notify(string $event, array $data): void;
}

class EventManager implements Subject
{
    private array $observers = [];
    
    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer): void
    {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
            $this->observers = array_values($this->observers);
        }
    }
    
    public function notify(string $event, array $data): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
}

class TextEditorWithHistory
{
    private string $content = '';
    private array $history = [];
    private int $historyIndex = -1;
    private EventManager $eventManager;
    
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->eventManager->notify('content_changed', ['content' => $content]);
    }
    
    public function insertText(string $text, int $position = null): void
    {
        $pos = $position ?? strlen($this->content);
        $this->content = substr($this->content, 0, $pos) . $text . substr($this->content, $pos);
        
        $this->eventManager->notify('text_inserted', [
            'text' => $text,
            'position' => $pos
        ]);
    }
    
    public function deleteText(int $start, int $length): void
    {
        $deletedText = substr($this->content, $start, $length);
        $this->content = substr($this->content, 0, $start) . substr($this->content, $start + $length);
        
        $this->eventManager->notify('text_deleted', [
            'text' => $deletedText,
            'position' => $start
        ]);
    }
    
    public function saveState(): void
    {
        $this->history = array_slice($this->history, 0, $this->historyIndex + 1);
        $this->history[] = $this->content;
        $this->historyIndex++;
        
        $this->eventManager->notify('state_saved', [
            'content' => $this->content,
            'index' => $this->historyIndex
        ]);
    }
    
    public function undo(): bool
    {
        if ($this->historyIndex > 0) {
            $this->historyIndex--;
            $this->content = $this->history[$this->historyIndex];
            
            $this->eventManager->notify('undo_performed', [
                'content' => $this->content,
                'index' => $this->historyIndex
            ]);
            
            return true;
        }
        return false;
    }
    
    public function redo(): bool
    {
        if ($this->historyIndex < count($this->history) - 1) {
            $this->historyIndex++;
            $this->content = $this->history[$this->historyIndex];
            
            $this->eventManager->notify('redo_performed', [
                'content' => $this->content,
                'index' => $this->historyIndex
            ]);
            
            return true;
        }
        return false;
    }
}

class CommandHistory implements Observer
{
    private array $commands = [];
    private int $currentCommand = -1;
    
    public function update(string $event, array $data): void
    {
        switch ($event) {
            case 'text_inserted':
                $this->commands[] = [
                    'type' => 'insert',
                    'text' => $data['text'],
                    'position' => $data['position']
                ];
                $this->currentCommand++;
                break;
                
            case 'text_deleted':
                $this->commands[] = [
                    'type' => 'delete',
                    'text' => $data['text'],
                    'position' => $data['position']
                ];
                $this->currentCommand++;
                break;
        }
    }
    
    public function getCommandHistory(): array
    {
        return $this->commands;
    }
    
    public function getCurrentCommandIndex(): int
    {
        return $this->currentCommand;
    }
}

class Logger implements Observer
{
    private array $logs = [];
    
    public function update(string $event, array $data): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $log = "[$timestamp] Event: $event";
        
        if (isset($data['text'])) {
            $log .= " - Text: '{$data['text']}'";
        }
        if (isset($data['position'])) {
            $log .= " - Position: {$data['position']}";
        }
        if (isset($data['content'])) {
            $log .= " - Content length: " . strlen($data['content']);
        }
        
        $this->logs[] = $log;
        echo "Logger: $log\n";
    }
    
    public function getLogs(): array
    {
        return $this->logs;
    }
}

// Combination: Strategy + Template Method + Factory
interface PaymentStrategy
{
    public function processPayment(float $amount, array $data): array;
    public function validate(array $data): bool;
    public function getPaymentDetails(): array;
}

abstract class AbstractPaymentProcessor
{
    protected PaymentStrategy $strategy;
    protected array $transactionData = [];
    
    public function __construct(PaymentStrategy $strategy)
    {
        $this->strategy = $strategy;
    }
    
    public function setStrategy(PaymentStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }
    
    // Template method
    public function processPayment(float $amount, array $paymentData): array
    {
        $this->transactionData = [
            'amount' => $amount,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'processing'
        ];
        
        // Step 1: Validate payment data
        if (!$this->validatePaymentData($paymentData)) {
            return $this->createErrorResponse('Invalid payment data');
        }
        
        // Step 2: Pre-process payment
        $processedData = $this->preProcessPayment($amount, $paymentData);
        
        // Step 3: Execute payment using strategy
        $result = $this->strategy->processPayment($amount, $processedData);
        
        // Step 4: Post-process result
        return $this->postProcessResult($result);
    }
    
    protected function validatePaymentData(array $data): bool
    {
        return $this->strategy->validate($data);
    }
    
    protected function preProcessPayment(float $amount, array $data): array
    {
        // Common preprocessing logic
        $data['processed_amount'] = $amount;
        $data['currency'] = $data['currency'] ?? 'USD';
        $data['timestamp'] = date('Y-m-d H:i:s');
        
        return $data;
    }
    
    protected function postProcessResult(array $result): array
    {
        // Common postprocessing logic
        $result['processor'] = get_class($this);
        $result['strategy'] = get_class($this->strategy);
        $result['processed_at'] = date('Y-m-d H:i:s');
        
        return $result;
    }
    
    protected function createErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

class CreditCardPaymentStrategy implements PaymentStrategy
{
    private array $details = [];
    
    public function processPayment(float $amount, array $data): array
    {
        $this->details = $data;
        
        // Simulate credit card processing
        $transactionId = uniqid('cc_');
        $success = rand(1, 10) <= 9; // 90% success rate
        
        return [
            'success' => $success,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'payment_method' => 'credit_card',
            'last_four' => substr($data['card_number'], -4),
            'message' => $success ? 'Payment processed successfully' : 'Payment failed'
        ];
    }
    
    public function validate(array $data): bool
    {
        return !empty($data['card_number']) &&
               !empty($data['expiry_date']) &&
               !empty($data['cvv']) &&
               !empty($data['cardholder_name']);
    }
    
    public function getPaymentDetails(): array
    {
        return $this->details;
    }
}

class PayPalPaymentStrategy implements PaymentStrategy
{
    private array $details = [];
    
    public function processPayment(float $amount, array $data): array
    {
        $this->details = $data;
        
        // Simulate PayPal processing
        $transactionId = uniqid('pp_');
        $success = rand(1, 10) <= 8; // 80% success rate
        
        return [
            'success' => $success,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'payment_method' => 'paypal',
            'paypal_email' => $data['paypal_email'],
            'message' => $success ? 'Payment processed successfully' : 'Payment failed'
        ];
    }
    
    public function validate(array $data): bool
    {
        return !empty($data['paypal_email']) &&
               filter_var($data['paypal_email'], FILTER_VALIDATE_EMAIL);
    }
    
    public function getPaymentDetails(): array
    {
        return $this->details;
    }
}

class PaymentProcessorFactory
{
    public static function create(string $type): AbstractPaymentProcessor
    {
        switch (strtolower($type)) {
            case 'credit_card':
                return new CreditCardPaymentProcessor(new CreditCardPaymentStrategy());
            case 'paypal':
                return new PayPalPaymentProcessor(new PayPalPaymentStrategy());
            default:
                throw new Exception("Unsupported payment type: $type");
        }
    }
}

class CreditCardPaymentProcessor extends AbstractPaymentProcessor
{
    protected function preProcessPayment(float $amount, array $data): array
    {
        $processed = parent::preProcessPayment($amount, $data);
        
        // Credit card specific preprocessing
        $processed['card_type'] = $this->detectCardType($data['card_number']);
        $processed['is_test'] = true;
        
        return $processed;
    }
    
    private function detectCardType(string $cardNumber): string
    {
        $firstDigit = substr($cardNumber, 0, 1);
        
        switch ($firstDigit) {
            case '4':
                return 'Visa';
            case '5':
                return 'MasterCard';
            case '3':
                return 'American Express';
            default:
                return 'Unknown';
        }
    }
}

class PayPalPaymentProcessor extends AbstractPaymentProcessor
{
    protected function preProcessPayment(float $amount, array $data): array
    {
        $processed = parent::preProcessPayment($amount, $data);
        
        // PayPal specific preprocessing
        $processed['account_type'] = 'personal';
        $processed['verification_required'] = $amount > 1000;
        
        return $processed;
    }
}

// Pattern Combinations Examples
class PatternCombinationsExamples
{
    public function demonstrateUnitOfWorkRepositoryFactory(): void
    {
        echo "Unit of Work + Repository + Factory Demo\n";
        echo str_repeat("-", 45) . "\n";
        
        $unitOfWork = new DatabaseUnitOfWork();
        $unitOfWork->begin();
        
        // Create entities using factory
        echo "Creating entities with factory:\n";
        $user1 = EntityFactory::createUser('Alice Johnson', 'alice@example.com', 28);
        $user2 = EntityFactory::createUser('Bob Smith', 'bob@example.com', 32);
        
        $product1 = EntityFactory::createProduct('Laptop', 999.99, 10);
        $product2 = EntityFactory::createProduct('Mouse', 29.99, 50);
        
        $order = EntityFactory::createOrder([
            ['product_id' => 'product_1', 'price' => 999.99, 'quantity' => 1],
            ['product_id' => 'product_2', 'price' => 29.99, 'quantity' => 2]
        ], 'pending');
        
        // Register entities with Unit of Work
        echo "\nRegistering entities with Unit of Work:\n";
        $unitOfWork->registerNew($user1);
        $unitOfWork->registerNew($user2);
        $unitOfWork->registerNew($product1);
        $unitOfWork->registerNew($product2);
        $unitOfWork->registerNew($order);
        
        // Modify an entity
        echo "\nModifying entity (dirty):\n";
        $product1->setPrice(899.99);
        $unitOfWork->registerDirty($product1);
        
        // Commit transaction
        echo "\nCommitting transaction:\n";
        $unitOfWork->commit();
        
        // Verify entities were saved
        echo "\nVerifying saved entities:\n";
        $userRepo = $unitOfWork->getRepository('user');
        $productRepo = $unitOfWork->getRepository('product');
        
        $users = $userRepo->findAll();
        $products = $productRepo->findAll();
        
        echo "Users saved: " . count($users) . "\n";
        echo "Products saved: " . count($products) . "\n";
        
        foreach ($users as $user) {
            echo "- {$user->getName()} ({$user->getEmail()})\n";
        }
        
        foreach ($products as $product) {
            echo "- {$product->getName()} (\${$product->getPrice()})\n";
        }
    }
    
    public function demonstrateObserverCommandMemento(): void
    {
        echo "\nObserver + Command + Memento Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        $eventManager = new EventManager();
        $editor = new TextEditorWithHistory($eventManager);
        
        // Attach observers
        $commandHistory = new CommandHistory();
        $logger = new Logger();
        
        $eventManager->attach($commandHistory);
        $eventManager->attach($logger);
        
        // Perform operations
        echo "Performing text operations:\n";
        $editor->setContent('Hello World');
        $editor->saveState();
        
        $editor->insertText(' Beautiful', 5);
        $editor->saveState();
        
        $editor->deleteText(6, 9);
        $editor->saveState();
        
        // Show command history
        echo "\nCommand History:\n";
        $commands = $commandHistory->getCommandHistory();
        foreach ($commands as $index => $command) {
            echo "$index: {$command['type']} '{$command['text']}' at position {$command['position']}\n";
        }
        
        // Undo operations
        echo "\nUndoing operations:\n";
        $editor->undo();
        echo "After undo: {$editor->getContent()}\n";
        
        $editor->undo();
        echo "After undo: {$editor->getContent()}\n";
        
        // Redo operations
        echo "\nRedoing operations:\n";
        $editor->redo();
        echo "After redo: {$editor->getContent()}\n";
        
        // Show logs
        echo "\nEvent Logs:\n";
        $logs = $logger->getLogs();
        foreach (array_slice($logs, -5) as $log) {
            echo "$log\n";
        }
    }
    
    public function demonstrateStrategyTemplateMethodFactory(): void
    {
        echo "\nStrategy + Template Method + Factory Demo\n";
        echo str_repeat("-", 45) . "\n";
        
        // Process payments using different strategies
        $payments = [
            [
                'type' => 'credit_card',
                'amount' => 100.00,
                'data' => [
                    'card_number' => '4532015112830366',
                    'expiry_date' => '12/25',
                    'cvv' => '123',
                    'cardholder_name' => 'John Doe'
                ]
            ],
            [
                'type' => 'paypal',
                'amount' => 250.50,
                'data' => [
                    'paypal_email' => 'john.doe@example.com'
                ]
            ]
        ];
        
        foreach ($payments as $payment) {
            echo "\nProcessing {$payment['type']} payment of \${$payment['amount']}:\n";
            
            try {
                // Create processor using factory
                $processor = PaymentProcessorFactory::create($payment['type']);
                
                // Process payment using template method
                $result = $processor->processPayment($payment['amount'], $payment['data']);
                
                echo "Result: " . ($result['success'] ? 'Success' : 'Failed') . "\n";
                echo "Transaction ID: " . ($result['transaction_id'] ?? 'N/A') . "\n";
                echo "Message: " . ($result['message'] ?? 'No message') . "\n";
                echo "Strategy: " . $result['strategy'] . "\n";
                echo "Processed at: " . $result['processed_at'] . "\n";
                
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
        
        // Demonstrate strategy switching
        echo "\nDemonstrating strategy switching:\n";
        $processor = PaymentProcessorFactory::create('credit_card');
        
        echo "Initial strategy: " . get_class($processor->getStrategy()) . "\n";
        
        // Switch to PayPal strategy
        $processor->setStrategy(new PayPalPaymentStrategy());
        echo "Switched strategy: " . get_class($processor->getStrategy()) . "\n";
        
        $result = $processor->processPayment(75.00, [
            'paypal_email' => 'jane.smith@example.com'
        ]);
        
        echo "Result with switched strategy: " . ($result['success'] ? 'Success' : 'Failed') . "\n";
    }
    
    public function demonstrateComplexCombination(): void
    {
        echo "\nComplex Pattern Combination Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Combining multiple patterns for an e-commerce system
        echo "E-commerce System with Multiple Patterns:\n";
        
        // 1. Unit of Work + Repository + Factory for data management
        $unitOfWork = new DatabaseUnitOfWork();
        $unitOfWork->begin();
        
        // 2. Observer pattern for event handling
        $eventManager = new EventManager();
        
        // 3. Strategy pattern for payment processing
        $paymentProcessor = PaymentProcessorFactory::create('credit_card');
        
        // Create order
        echo "\n1. Creating order and entities:\n";
        $user = EntityFactory::createUser('Customer', 'customer@example.com', 30);
        $product = EntityFactory::createProduct('Smartphone', 699.99, 5);
        $order = EntityFactory::createOrder([
            ['product_id' => $product->getId(), 'price' => 699.99, 'quantity' => 1]
        ], 'pending');
        
        // Register with Unit of Work
        $unitOfWork->registerNew($user);
        $unitOfWork->registerNew($product);
        $unitOfWork->registerNew($order);
        
        // Notify observers
        $eventManager->notify('order_created', [
            'order_id' => $order->getId(),
            'user_id' => $user->getId(),
            'total' => $order->getTotal()
        ]);
        
        // 4. Command pattern for order processing
        echo "\n2. Processing payment with strategy:\n";
        $paymentResult = $paymentProcessor->processPayment($order->getTotal(), [
            'card_number' => '4532015112830366',
            'expiry_date' => '12/25',
            'cvv' => '123',
            'cardholder_name' => $user->getName()
        ]);
        
        if ($paymentResult['success']) {
            $order->setStatus('paid');
            $unitOfWork->registerDirty($order);
            
            $eventManager->notify('payment_processed', [
                'order_id' => $order->getId(),
                'transaction_id' => $paymentResult['transaction_id']
            ]);
        }
        
        // 5. Commit transaction
        echo "\n3. Committing all changes:\n";
        $unitOfWork->commit();
        
        // 6. Repository pattern for data retrieval
        echo "\n4. Retrieving data with repositories:\n";
        $userRepo = $unitOfWork->getRepository('user');
        $productRepo = $unitOfWork->getRepository('product');
        
        $savedUser = $userRepo->findById($user->getId());
        $savedProduct = $productRepo->findById($product->getId());
        
        echo "Saved User: {$savedUser->getName()}\n";
        echo "Saved Product: {$savedProduct->getName()} (Stock: {$savedProduct->getQuantity()})\n";
        echo "Order Status: {$order->getStatus()}\n";
        echo "Payment Success: " . ($paymentResult['success'] ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nPattern Combinations Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Pattern Selection:\n";
        echo "   • Choose patterns based on problem complexity\n";
        echo "   • Avoid over-engineering simple problems\n";
        echo "   • Consider performance implications\n";
        echo "   • Maintain consistency across the application\n";
        echo "   • Document pattern interactions\n\n";
        
        echo "2. Integration Guidelines:\n";
        echo "   • Keep pattern boundaries clear\n";
        echo "   • Use interfaces for pattern contracts\n";
        echo "   • Implement proper error handling\n";
        echo "   • Consider dependency injection\n";
        echo "   • Test pattern combinations thoroughly\n\n";
        
        echo "3. Common Combinations:\n";
        echo "   • Repository + Unit of Work for data management\n";
        echo "   • Observer + Command for event systems\n";
        echo "   • Strategy + Template Method for algorithms\n";
        echo "   • Factory + Builder for object creation\n";
        echo "   • Decorator + Composite for UI components\n\n";
        
        echo "4. Performance Considerations:\n";
        echo "   • Monitor memory usage\n";
        echo "   • Optimize database queries\n";
        echo "   • Cache frequently accessed data\n";
        echo "   • Use lazy loading where appropriate\n";
        echo "   • Profile and optimize bottlenecks\n\n";
        
        echo "5. Maintenance Tips:\n";
        echo "   • Keep patterns loosely coupled\n";
        echo "   • Use clear naming conventions\n";
        echo "   • Write comprehensive tests\n";
        echo "   • Document pattern usage\n";
        echo "   • Regularly review and refactor";
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Pattern Combinations Examples\n";
        echo str_repeat("=", 40) . "\n";
        
        $this->demonstrateUnitOfWorkRepositoryFactory();
        $this->demonstrateObserverCommandMemento();
        $this->demonstrateStrategyTemplateMethodFactory();
        $this->demonstrateComplexCombination();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runPatternCombinationsDemo(): void
{
    $examples = new PatternCombinationsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runPatternCombinationsDemo();
}
?>
