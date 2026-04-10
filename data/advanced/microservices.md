# PHP Microservices

## Microservice Architecture

### Service Design Principles
```php
<?php
// Base microservice class
abstract class Microservice {
    protected string $serviceName;
    protected array $config;
    protected LoggerInterface $logger;
    protected MessageBusInterface $messageBus;
    
    public function __construct(string $serviceName, array $config, LoggerInterface $logger, MessageBusInterface $messageBus) {
        $this->serviceName = $serviceName;
        $this->config = $config;
        $this->logger = $logger;
        $this->messageBus = $messageBus;
    }
    
    abstract public function handle(RequestInterface $request): ResponseInterface;
    
    protected function validateRequest(RequestInterface $request): bool {
        // Implement request validation logic
        return true;
    }
    
    protected function logRequest(RequestInterface $request): void {
        $this->logger->info("Request received", [
            'service' => $this->serviceName,
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    protected function logResponse(ResponseInterface $response): void {
        $this->logger->info("Response sent", [
            'service' => $this->serviceName,
            'status' => $response->getStatusCode(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function getServiceName(): string {
        return $this->serviceName;
    }
    
    public function getConfig(): array {
        return $this->config;
    }
}

// User Service
class UserService extends Microservice {
    private UserRepository $userRepository;
    private CacheInterface $cache;
    private EventDispatcherInterface $eventDispatcher;
    
    public function __construct(
        string $serviceName,
        array $config,
        LoggerInterface $logger,
        MessageBusInterface $messageBus,
        UserRepository $userRepository,
        CacheInterface $cache,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($serviceName, $config, $logger, $messageBus);
        $this->userRepository = $userRepository;
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function handle(RequestInterface $request): ResponseInterface {
        $this->logRequest($request);
        
        try {
            if (!$this->validateRequest($request)) {
                return new JsonResponse(['error' => 'Invalid request'], 400);
            }
            
            $response = $this->processRequest($request);
            $this->logResponse($response);
            
            return $response;
        } catch (Exception $e) {
            $this->logger->error("Service error", [
                'service' => $this->serviceName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    private function processRequest(RequestInterface $request): ResponseInterface {
        $path = $request->getPath();
        $method = $request->getMethod();
        
        switch ("$method $path") {
            case 'GET /users':
                return $this->getUsers($request);
            case 'GET /users/{id}':
                return $this->getUser($request);
            case 'POST /users':
                return $this->createUser($request);
            case 'PUT /users/{id}':
                return $this->updateUser($request);
            case 'DELETE /users/{id}':
                return $this->deleteUser($request);
            default:
                return new JsonResponse(['error' => 'Not found'], 404);
        }
    }
    
    private function getUsers(RequestInterface $request): ResponseInterface {
        $page = (int)($request->getQueryParam('page', 1));
        $limit = (int)($request->getQueryParam('limit', 10));
        $filters = $request->getQueryParams();
        
        $cacheKey = "users:page:$page:limit:$limit:" . md5(serialize($filters));
        
        if ($cached = $this->cache->get($cacheKey)) {
            return new JsonResponse($cached);
        }
        
        $users = $this->userRepository->findAll($page, $limit, $filters);
        $this->cache->set($cacheKey, $users, 300); // 5 minutes
        
        return new JsonResponse($users);
    }
    
    private function getUser(RequestInterface $request): ResponseInterface {
        $id = $request->getPathParam('id');
        
        $cacheKey = "user:$id";
        
        if ($cached = $this->cache->get($cacheKey)) {
            return new JsonResponse($cached);
        }
        
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        
        $this->cache->set($cacheKey, $user, 600); // 10 minutes
        
        return new JsonResponse($user);
    }
    
    private function createUser(RequestInterface $request): ResponseInterface {
        $data = json_decode($request->getBody(), true);
        
        if (!$this->validateUserData($data)) {
            return new JsonResponse(['error' => 'Invalid user data'], 400);
        }
        
        $user = $this->userRepository->create($data);
        
        // Invalidate cache
        $this->cache->delete('users:page:1:limit:10:');
        
        // Dispatch event
        $this->eventDispatcher->dispatch(new UserCreatedEvent($user));
        
        // Send message to other services
        $this->messageBus->publish('user.created', $user);
        
        return new JsonResponse($user, 201);
    }
    
    private function updateUser(RequestInterface $request): ResponseInterface {
        $id = $request->getPathParam('id');
        $data = json_decode($request->getBody(), true);
        
        if (!$this->validateUserData($data, true)) {
            return new JsonResponse(['error' => 'Invalid user data'], 400);
        }
        
        $user = $this->userRepository->update($id, $data);
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        
        // Invalidate cache
        $this->cache->delete("user:$id");
        $this->cache->delete('users:page:1:limit:10:');
        
        // Dispatch event
        $this->eventDispatcher->dispatch(new UserUpdatedEvent($user));
        
        return new JsonResponse($user);
    }
    
    private function deleteUser(RequestInterface $request): ResponseInterface {
        $id = $request->getPathParam('id');
        
        if (!$this->userRepository->delete($id)) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        
        // Invalidate cache
        $this->cache->delete("user:$id");
        $this->cache->delete('users:page:1:limit:10:');
        
        // Dispatch event
        $this->eventDispatcher->dispatch(new UserDeletedEvent($id));
        
        return new JsonResponse(null, 204);
    }
    
    private function validateUserData(array $data, bool $isUpdate = false): bool {
        $requiredFields = ['email', 'name'];
        
        if (!$isUpdate) {
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return false;
                }
            }
        }
        
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return true;
    }
}

// Order Service
class OrderService extends Microservice {
    private OrderRepository $orderRepository;
    private ProductServiceClient $productService;
    private UserServiceClient $userService;
    private PaymentServiceClient $paymentService;
    
    public function __construct(
        string $serviceName,
        array $config,
        LoggerInterface $logger,
        MessageBusInterface $messageBus,
        OrderRepository $orderRepository,
        ProductServiceClient $productService,
        UserServiceClient $userService,
        PaymentServiceClient $paymentService
    ) {
        parent::__construct($serviceName, $config, $logger, $messageBus);
        $this->orderRepository = $orderRepository;
        $this->productService = $productService;
        $this->userService = $userService;
        $this->paymentService = $paymentService;
    }
    
    public function handle(RequestInterface $request): ResponseInterface {
        $this->logRequest($request);
        
        try {
            if (!$this->validateRequest($request)) {
                return new JsonResponse(['error' => 'Invalid request'], 400);
            }
            
            $response = $this->processRequest($request);
            $this->logResponse($response);
            
            return $response;
        } catch (Exception $e) {
            $this->logger->error("Service error", [
                'service' => $this->serviceName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    private function processRequest(RequestInterface $request): ResponseInterface {
        $path = $request->getPath();
        $method = $request->getMethod();
        
        switch ("$method $path") {
            case 'GET /orders':
                return $this->getOrders($request);
            case 'GET /orders/{id}':
                return $this->getOrder($request);
            case 'POST /orders':
                return $this->createOrder($request);
            case 'PUT /orders/{id}/status':
                return $this->updateOrderStatus($request);
            default:
                return new JsonResponse(['error' => 'Not found'], 404);
        }
    }
    
    private function createOrder(RequestInterface $request): ResponseInterface {
        $data = json_decode($request->getBody(), true);
        
        if (!$this->validateOrderData($data)) {
            return new JsonResponse(['error' => 'Invalid order data'], 400);
        }
        
        // Verify user exists
        $user = $this->userService->getUser($data['user_id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        
        // Verify products and calculate total
        $total = 0;
        $orderItems = [];
        
        foreach ($data['items'] as $item) {
            $product = $this->productService->getProduct($item['product_id']);
            if (!$product) {
                return new JsonResponse(['error' => "Product {$item['product_id']} not found"], 404);
            }
            
            if ($product['stock'] < $item['quantity']) {
                return new JsonResponse(['error' => "Insufficient stock for product {$item['product_id']}"], 400);
            }
            
            $subtotal = $product['price'] * $item['quantity'];
            $total += $subtotal;
            
            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product['price'],
                'subtotal' => $subtotal
            ];
        }
        
        // Create order
        $order = [
            'id' => uniqid(),
            'user_id' => $data['user_id'],
            'items' => $orderItems,
            'total' => $total,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $order = $this->orderRepository->create($order);
        
        // Process payment
        $paymentResult = $this->paymentService->processPayment([
            'order_id' => $order['id'],
            'amount' => $total,
            'user_id' => $data['user_id'],
            'payment_method' => $data['payment_method']
        ]);
        
        if (!$paymentResult['success']) {
            $order['status'] = 'payment_failed';
            $this->orderRepository->update($order['id'], $order);
            
            return new JsonResponse(['error' => 'Payment failed'], 400);
        }
        
        // Update product stock
        foreach ($data['items'] as $item) {
            $this->productService->updateStock($item['product_id'], $item['quantity'] * -1);
        }
        
        // Update order status
        $order['status'] = 'paid';
        $order['payment_id'] = $paymentResult['payment_id'];
        $this->orderRepository->update($order['id'], $order);
        
        // Send message to other services
        $this->messageBus->publish('order.created', $order);
        $this->messageBus->publish('order.paid', $order);
        
        return new JsonResponse($order, 201);
    }
    
    private function validateOrderData(array $data): bool {
        $requiredFields = ['user_id', 'items', 'payment_method'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        
        if (!is_array($data['items']) || empty($data['items'])) {
            return false;
        }
        
        foreach ($data['items'] as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || $item['quantity'] <= 0) {
                return false;
            }
        }
        
        return true;
    }
    
    private function getOrders(RequestInterface $request): ResponseInterface {
        $userId = $request->getQueryParam('user_id');
        $status = $request->getQueryParam('status');
        $page = (int)($request->getQueryParam('page', 1));
        $limit = (int)($request->getQueryParam('limit', 10));
        
        $orders = $this->orderRepository->findAll($page, $limit, [
            'user_id' => $userId,
            'status' => $status
        ]);
        
        return new JsonResponse($orders);
    }
    
    private function getOrder(RequestInterface $request): ResponseInterface {
        $id = $request->getPathParam('id');
        
        $order = $this->orderRepository->findById($id);
        
        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }
        
        return new JsonResponse($order);
    }
    
    private function updateOrderStatus(RequestInterface $request): ResponseInterface {
        $id = $request->getPathParam('id');
        $data = json_decode($request->getBody(), true);
        
        if (empty($data['status'])) {
            return new JsonResponse(['error' => 'Status is required'], 400);
        }
        
        $order = $this->orderRepository->updateStatus($id, $data['status']);
        
        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }
        
        // Send message to other services
        $this->messageBus->publish('order.status_updated', $order);
        
        return new JsonResponse($order);
    }
}

// Service Registry
class ServiceRegistry {
    private array $services = [];
    private array $loadBalancers = [];
    
    public function register(string $serviceName, string $host, int $port, array $metadata = []): void {
        $this->services[$serviceName][] = [
            'host' => $host,
            'port' => $port,
            'metadata' => $metadata,
            'registered_at' => time(),
            'health_check_url' => "http://$host:$port/health"
        ];
        
        $this->logger->info("Service registered", [
            'service' => $serviceName,
            'host' => $host,
            'port' => $port
        ]);
    }
    
    public function unregister(string $serviceName, string $host, int $port): void {
        if (isset($this->services[$serviceName])) {
            $this->services[$serviceName] = array_filter(
                $this->services[$serviceName],
                fn($service) => !($service['host'] === $host && $service['port'] === $port)
            );
        }
        
        $this->logger->info("Service unregistered", [
            'service' => $serviceName,
            'host' => $host,
            'port' => $port
        ]);
    }
    
    public function discover(string $serviceName): ?array {
        if (!isset($this->services[$serviceName]) || empty($this->services[$serviceName])) {
            return null;
        }
        
        // Simple round-robin load balancing
        $instances = $this->services[$serviceName];
        $healthyInstances = array_filter($instances, fn($instance) => $this->isHealthy($instance));
        
        if (empty($healthyInstances)) {
            return null;
        }
        
        return $healthyInstances[array_rand($healthyInstances)];
    }
    
    public function getAllServices(string $serviceName): array {
        return $this->services[$serviceName] ?? [];
    }
    
    private function isHealthy(array $instance): bool {
        // Implement health check logic
        $url = $instance['health_check_url'];
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 2,
                'method' => 'GET'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        return $response !== false && strpos($response, 'healthy') !== false;
    }
    
    public function healthCheck(): array {
        $status = [];
        
        foreach ($this->services as $serviceName => $instances) {
            $healthyCount = 0;
            $totalCount = count($instances);
            
            foreach ($instances as $instance) {
                if ($this->isHealthy($instance)) {
                    $healthyCount++;
                }
            }
            
            $status[$serviceName] = [
                'total_instances' => $totalCount,
                'healthy_instances' => $healthyCount,
                'unhealthy_instances' => $totalCount - $healthyCount
            ];
        }
        
        return $status;
    }
}

// API Gateway
class ApiGateway {
    private ServiceRegistry $serviceRegistry;
    private LoggerInterface $logger;
    private array $routes;
    
    public function __construct(ServiceRegistry $serviceRegistry, LoggerInterface $logger, array $routes = []) {
        $this->serviceRegistry = $serviceRegistry;
        $this->logger = $logger;
        $this->routes = array_merge($this->getDefaultRoutes(), $routes);
    }
    
    public function handle(RequestInterface $request): ResponseInterface {
        $path = $request->getPath();
        $method = $request->getMethod();
        
        // Find matching route
        $route = $this->findRoute($method, $path);
        
        if (!$route) {
            return new JsonResponse(['error' => 'Route not found'], 404);
        }
        
        // Discover service
        $service = $this->serviceRegistry->discover($route['service']);
        
        if (!$service) {
            return new JsonResponse(['error' => 'Service unavailable'], 503);
        }
        
        // Forward request
        return $this->forwardRequest($service, $request, $route);
    }
    
    private function findRoute(string $method, string $path): ?array {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->pathMatches($route['path'], $path)) {
                return $route;
            }
        }
        
        return null;
    }
    
    private function pathMatches(string $routePath, string $actualPath): bool {
        // Simple path matching (can be enhanced with proper routing library)
        $routePattern = preg_replace('/\{[^}]+\}/', '[^/]+', $routePath);
        $routePattern = '#^' . $routePattern . '$#';
        
        return preg_match($routePattern, $actualPath);
    }
    
    private function forwardRequest(array $service, RequestInterface $request, array $route): ResponseInterface {
        $url = "http://{$service['host']}:{$service['port']}{$route['target_path']}";
        
        $context = stream_context_create([
            'http' => [
                'method' => $request->getMethod(),
                'header' => $this->buildHeaders($request),
                'content' => $request->getBody(),
                'timeout' => 30
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return new JsonResponse(['error' => 'Service request failed'], 502);
        }
        
        $statusCode = $this->getStatusCode($http_response_header ?? []);
        $headers = $this->getResponseHeaders($http_response_header ?? []);
        
        return new JsonResponse(json_decode($response, true), $statusCode, $headers);
    }
    
    private function buildHeaders(RequestInterface $request): string {
        $headers = [];
        
        foreach ($request->getHeaders() as $name => $value) {
            $headers[] = "$name: $value";
        }
        
        return implode("\r\n", $headers);
    }
    
    private function getStatusCode(array $responseHeaders): int {
        foreach ($responseHeaders as $header) {
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                return (int)$matches[1];
            }
        }
        
        return 200;
    }
    
    private function getResponseHeaders(array $responseHeaders): array {
        $headers = [];
        
        foreach ($responseHeaders as $header) {
            if (strpos($header, ':') !== false) {
                list($name, $value) = explode(':', $header, 2);
                $headers[trim($name)] = trim($value);
            }
        }
        
        return $headers;
    }
    
    private function getDefaultRoutes(): array {
        return [
            [
                'method' => 'GET',
                'path' => '/users',
                'service' => 'user-service',
                'target_path' => '/users'
            ],
            [
                'method' => 'GET',
                'path' => '/users/{id}',
                'service' => 'user-service',
                'target_path' => '/users/{id}'
            ],
            [
                'method' => 'POST',
                'path' => '/users',
                'service' => 'user-service',
                'target_path' => '/users'
            ],
            [
                'method' => 'GET',
                'path' => '/orders',
                'service' => 'order-service',
                'target_path' => '/orders'
            ],
            [
                'method' => 'POST',
                'path' => '/orders',
                'service' => 'order-service',
                'target_path' => '/orders'
            ]
        ];
    }
}

// Usage examples
$logger = new SimpleLogger();
$messageBus = new SimpleMessageBus();
$serviceRegistry = new ServiceRegistry();

// Register services
$serviceRegistry->register('user-service', 'localhost', 8001);
$serviceRegistry->register('order-service', 'localhost', 8002);

// Create API Gateway
$apiGateway = new ApiGateway($serviceRegistry, $logger);

// Handle incoming request
$request = new SimpleRequest('GET', '/users', [], '');
$response = $apiGateway->handle($request);

echo "Response: " . $response->getBody() . "\n";
echo "Status: " . $response->getStatusCode() . "\n";
?>
```

## Inter-Service Communication

### Service Communication Patterns
```php
<?php
// HTTP Client for service communication
class ServiceHttpClient {
    private array $config;
    private LoggerInterface $logger;
    private CircuitBreaker $circuitBreaker;
    
    public function __construct(array $config, LoggerInterface $logger, CircuitBreaker $circuitBreaker) {
        $this->config = $config;
        $this->logger = $logger;
        $this->circuitBreaker = $circuitBreaker;
    }
    
    public function get(string $url, array $headers = []): array {
        return $this->request('GET', $url, null, $headers);
    }
    
    public function post(string $url, $data, array $headers = []): array {
        return $this->request('POST', $url, $data, $headers);
    }
    
    public function put(string $url, $data, array $headers = []): array {
        return $this->request('PUT', $url, $data, $headers);
    }
    
    public function delete(string $url, array $headers = []): array {
        return $this->request('DELETE', $url, null, $headers);
    }
    
    private function request(string $method, string $url, $data = null, array $headers = []): array {
        $serviceKey = $this->extractServiceKey($url);
        
        if (!$this->circuitBreaker->isAvailable($serviceKey)) {
            throw new ServiceUnavailableException("Service $serviceKey is not available");
        }
        
        try {
            $context = $this->createContext($method, $data, $headers);
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                $this->circuitBreaker->recordFailure($serviceKey);
                throw new ServiceCommunicationException("Failed to communicate with service");
            }
            
            $this->circuitBreaker->recordSuccess($serviceKey);
            
            return [
                'success' => true,
                'data' => json_decode($response, true),
                'status' => $this->getStatusCode($http_response_header ?? [])
            ];
            
        } catch (Exception $e) {
            $this->circuitBreaker->recordFailure($serviceKey);
            $this->logger->error("Service communication failed", [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    private function createContext(string $method, $data, array $headers): resource {
        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);
        
        $options = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'timeout' => $this->config['timeout'] ?? 30,
                'ignore_errors' => true
            ]
        ];
        
        if ($data !== null) {
            $options['http']['content'] = json_encode($data);
        }
        
        return stream_context_create($options);
    }
    
    private function extractServiceKey(string $url): string {
        $parsed = parse_url($url);
        return $parsed['host'] ?? 'unknown';
    }
    
    private function getStatusCode(array $responseHeaders): int {
        foreach ($responseHeaders as $header) {
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                return (int)$matches[1];
            }
        }
        
        return 200;
    }
}

// Circuit Breaker pattern
class CircuitBreaker {
    private array $states = [];
    private int $failureThreshold = 5;
    private int $recoveryTimeout = 60;
    private int $successThreshold = 3;
    
    public function __construct(int $failureThreshold = 5, int $recoveryTimeout = 60, int $successThreshold = 3) {
        $this->failureThreshold = $failureThreshold;
        $this->recoveryTimeout = $recoveryTimeout;
        $this->successThreshold = $successThreshold;
    }
    
    public function isAvailable(string $service): bool {
        if (!isset($this->states[$service])) {
            $this->states[$service] = [
                'state' => 'CLOSED',
                'failures' => 0,
                'last_failure_time' => null,
                'successes' => 0
            ];
        }
        
        $state = $this->states[$service];
        
        switch ($state['state']) {
            case 'CLOSED':
                return true;
                
            case 'OPEN':
                if (time() - $state['last_failure_time'] > $this->recoveryTimeout) {
                    $this->states[$service]['state'] = 'HALF_OPEN';
                    $this->states[$service]['successes'] = 0;
                    return true;
                }
                return false;
                
            case 'HALF_OPEN':
                return true;
                
            default:
                return false;
        }
    }
    
    public function recordSuccess(string $service): void {
        if (!isset($this->states[$service])) {
            return;
        }
        
        $state = &$this->states[$service];
        
        switch ($state['state']) {
            case 'CLOSED':
                $state['failures'] = 0;
                break;
                
            case 'HALF_OPEN':
                $state['successes']++;
                
                if ($state['successes'] >= $this->successThreshold) {
                    $state['state'] = 'CLOSED';
                    $state['failures'] = 0;
                    $state['successes'] = 0;
                }
                break;
        }
    }
    
    public function recordFailure(string $service): void {
        if (!isset($this->states[$service])) {
            $this->states[$service] = [
                'state' => 'CLOSED',
                'failures' => 0,
                'last_failure_time' => null,
                'successes' => 0
            ];
        }
        
        $state = &$this->states[$service];
        $state['failures']++;
        $state['last_failure_time'] = time();
        
        switch ($state['state']) {
            case 'CLOSED':
                if ($state['failures'] >= $this->failureThreshold) {
                    $state['state'] = 'OPEN';
                }
                break;
                
            case 'HALF_OPEN':
                $state['state'] = 'OPEN';
                break;
        }
    }
    
    public function getState(string $service): string {
        return $this->states[$service]['state'] ?? 'CLOSED';
    }
    
    public function getStats(): array {
        $stats = [];
        
        foreach ($this->states as $service => $state) {
            $stats[$service] = [
                'state' => $state['state'],
                'failures' => $state['failures'],
                'last_failure_time' => $state['last_failure_time'],
                'successes' => $state['successes']
            ];
        }
        
        return $stats;
    }
}

// Service Client with retry logic
class ServiceClient {
    private ServiceHttpClient $httpClient;
    private LoggerInterface $logger;
    private int $maxRetries = 3;
    private int $retryDelay = 100; // milliseconds
    
    public function __construct(ServiceHttpClient $httpClient, LoggerInterface $logger) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }
    
    public function call(string $method, string $url, $data = null, array $headers = []): array {
        $attempt = 0;
        $lastException = null;
        
        while ($attempt < $this->maxRetries) {
            try {
                $response = $this->httpClient->{$method}($url, $data ?? [], $headers);
                
                if ($response['status'] >= 500) {
                    throw new ServiceCommunicationException("Server error: {$response['status']}");
                }
                
                return $response;
                
            } catch (Exception $e) {
                $attempt++;
                $lastException = $e;
                
                if ($attempt < $this->maxRetries) {
                    $this->logger->warning("Service call failed, retrying", [
                        'attempt' => $attempt,
                        'max_retries' => $this->maxRetries,
                        'url' => $url,
                        'error' => $e->getMessage()
                    ]);
                    
                    usleep($this->retryDelay * 1000 * $attempt); // Exponential backoff
                }
            }
        }
        
        $this->logger->error("Service call failed after all retries", [
            'url' => $url,
            'attempts' => $attempt,
            'error' => $lastException->getMessage()
        ]);
        
        throw $lastException;
    }
}

// Message Bus for asynchronous communication
interface MessageBusInterface {
    public function publish(string $topic, array $message): void;
    public function subscribe(string $topic, callable $handler): void;
    public function unsubscribe(string $topic, callable $handler): void;
}

class InMemoryMessageBus implements MessageBusInterface {
    private array $subscribers = [];
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function publish(string $topic, array $message): void {
        $this->logger->info("Publishing message", [
            'topic' => $topic,
            'message' => $message
        ]);
        
        if (!isset($this->subscribers[$topic])) {
            return;
        }
        
        foreach ($this->subscribers[$topic] as $handler) {
            try {
                $handler($message);
            } catch (Exception $e) {
                $this->logger->error("Message handler failed", [
                    'topic' => $topic,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    public function subscribe(string $topic, callable $handler): void {
        if (!isset($this->subscribers[$topic])) {
            $this->subscribers[$topic] = [];
        }
        
        $this->subscribers[$topic][] = $handler;
        
        $this->logger->info("Subscribed to topic", ['topic' => $topic]);
    }
    
    public function unsubscribe(string $topic, callable $handler): void {
        if (isset($this->subscribers[$topic])) {
            $this->subscribers[$topic] = array_filter(
                $this->subscribers[$topic],
                fn($h) => $h !== $handler
            );
        }
        
        $this->logger->info("Unsubscribed from topic", ['topic' => $topic]);
    }
}

// Event-driven communication
class EventDispatcher implements EventDispatcherInterface {
    private array $listeners = [];
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function dispatch(EventInterface $event): void {
        $eventName = get_class($event);
        
        $this->logger->info("Dispatching event", [
            'event' => $eventName,
            'data' => $event->getData()
        ]);
        
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        
        foreach ($this->listeners[$eventName] as $listener) {
            try {
                $listener($event);
            } catch (Exception $e) {
                $this->logger->error("Event listener failed", [
                    'event' => $eventName,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    public function addListener(string $eventName, callable $listener): void {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        
        $this->listeners[$eventName][] = $listener;
        
        $this->logger->info("Added event listener", ['event' => $eventName]);
    }
    
    public function removeListener(string $eventName, callable $listener): void {
        if (isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = array_filter(
                $this->listeners[$eventName],
                fn($l) => $l !== $listener
            );
        }
        
        $this->logger->info("Removed event listener", ['event' => $eventName]);
    }
}

// Service-specific clients
class UserServiceClient {
    private ServiceClient $serviceClient;
    private string $baseUrl;
    
    public function __construct(ServiceClient $serviceClient, string $baseUrl) {
        $this->serviceClient = $serviceClient;
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    public function getUser(string $id): ?array {
        try {
            $response = $this->serviceClient->call('get', "{$this->baseUrl}/users/$id");
            return $response['data'];
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function createUser(array $data): ?array {
        try {
            $response = $this->serviceClient->call('post', $this->baseUrl . '/users', $data);
            return $response['data'];
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function updateUser(string $id, array $data): ?array {
        try {
            $response = $this->serviceClient->call('put', "{$this->baseUrl}/users/$id", $data);
            return $response['data'];
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function deleteUser(string $id): bool {
        try {
            $response = $this->serviceClient->call('delete', "{$this->baseUrl}/users/$id");
            return $response['status'] === 204;
        } catch (Exception $e) {
            return false;
        }
    }
}

class ProductServiceClient {
    private ServiceClient $serviceClient;
    private string $baseUrl;
    
    public function __construct(ServiceClient $serviceClient, string $baseUrl) {
        $this->serviceClient = $serviceClient;
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    public function getProduct(string $id): ?array {
        try {
            $response = $this->serviceClient->call('get', "{$this->baseUrl}/products/$id");
            return $response['data'];
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function updateStock(string $productId, int $quantity): bool {
        try {
            $response = $this->serviceClient->call('put', "{$this->baseUrl}/products/$productId/stock", [
                'quantity' => $quantity
            ]);
            return $response['status'] === 200;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function reserveStock(string $productId, int $quantity): bool {
        try {
            $response = $this->serviceClient->call('post', "{$this->baseUrl}/products/$productId/reserve", [
                'quantity' => $quantity
            ]);
            return $response['status'] === 200;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function releaseStock(string $productId, int $quantity): bool {
        try {
            $response = $this->serviceClient->call('post', "{$this->baseUrl}/products/$productId/release", [
                'quantity' => $quantity
            ]);
            return $response['status'] === 200;
        } catch (Exception $e) {
            return false;
        }
    }
}

class PaymentServiceClient {
    private ServiceClient $serviceClient;
    private string $baseUrl;
    
    public function __construct(ServiceClient $serviceClient, string $baseUrl) {
        $this->serviceClient = $serviceClient;
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    public function processPayment(array $paymentData): array {
        try {
            $response = $this->serviceClient->call('post', $this->baseUrl . '/payments', $paymentData);
            return $response['data'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function refundPayment(string $paymentId, float $amount): array {
        try {
            $response = $this->serviceClient->call('post', $this->baseUrl . "/payments/$paymentId/refund", [
                'amount' => $amount
            ]);
            return $response['data'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

// Usage examples
$logger = new SimpleLogger();
$circuitBreaker = new CircuitBreaker();
$httpClient = new ServiceHttpClient(['timeout' => 30], $logger, $circuitBreaker);
$serviceClient = new ServiceClient($httpClient, $logger);

// Create service clients
$userServiceClient = new UserServiceClient($serviceClient, 'http://localhost:8001');
$productServiceClient = new ProductServiceClient($serviceClient, 'http://localhost:8003');
$paymentServiceClient = new PaymentServiceClient($serviceClient, 'http://localhost:8004');

// Test service communication
try {
    $user = $userServiceClient->getUser('123');
    echo "User: " . json_encode($user) . "\n";
    
    $product = $productClient->getProduct('456');
    echo "Product: " . json_encode($product) . "\n";
    
    $paymentResult = $paymentServiceClient->processPayment([
        'order_id' => '789',
        'amount' => 99.99,
        'payment_method' => 'credit_card'
    ]);
    echo "Payment result: " . json_encode($paymentResult) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check circuit breaker stats
$stats = $circuitBreaker->getStats();
echo "Circuit breaker stats:\n";
print_r($stats);
?>
```

## Service Discovery

### Dynamic Service Registration and Discovery
```php
<?php
// Service Discovery Interface
interface ServiceDiscoveryInterface {
    public function register(ServiceInstance $instance): void;
    public function unregister(string $serviceId): void;
    public function discover(string $serviceName): array;
    public function getInstances(string $serviceName): array;
    public function watch(string $serviceName, callable $callback): void;
}

class ServiceInstance {
    private string $id;
    private string $serviceName;
    private string $host;
    private int $port;
    private array $metadata;
    private int $registeredAt;
    private array $healthCheck;
    
    public function __construct(
        string $serviceName,
        string $host,
        int $port,
        array $metadata = [],
        array $healthCheck = []
    ) {
        $this->id = uniqid();
        $this->serviceName = $serviceName;
        $this->host = $host;
        $this->port = $port;
        $this->metadata = $metadata;
        $this->registeredAt = time();
        $this->healthCheck = array_merge([
            'path' => '/health',
            'interval' => 30,
            'timeout' => 5,
            'healthy_threshold' => 2,
            'unhealthy_threshold' => 3
        ], $healthCheck);
    }
    
    public function getId(): string {
        return $this->id;
    }
    
    public function getServiceName(): string {
        return $this->serviceName;
    }
    
    public function getHost(): string {
        return $this->host;
    }
    
    public function getPort(): int {
        return $this->port;
    }
    
    public function getAddress(): string {
        return "{$this->host}:{$this->port}";
    }
    
    public function getMetadata(): array {
        return $this->metadata;
    }
    
    public function getRegisteredAt(): int {
        return $this->registeredAt;
    }
    
    public function getHealthCheck(): array {
        return $this->healthCheck;
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'service_name' => $this->serviceName,
            'host' => $this->host,
            'port' => $this->port,
            'address' => $this->getAddress(),
            'metadata' => $this->metadata,
            'registered_at' => $this->registeredAt,
            'health_check' => $this->healthCheck
        ];
    }
}

// Consul-based Service Discovery
class ConsulServiceDiscovery implements ServiceDiscoveryInterface {
    private string $consulUrl;
    private LoggerInterface $logger;
    private array $watchers = [];
    
    public function __construct(string $consulUrl, LoggerInterface $logger) {
        $this->consulUrl = rtrim($consulUrl, '/');
        $this->logger = $logger;
    }
    
    public function register(ServiceInstance $instance): void {
        $url = "{$this->consulUrl}/v1/agent/service/register";
        
        $serviceData = [
            'ID' => $instance->getId(),
            'Name' => $instance->getServiceName(),
            'Address' => $instance->getHost(),
            'Port' => $instance->getPort(),
            'Meta' => $instance->getMetadata(),
            'Check' => [
                'HTTP' => "http://{$instance->getAddress()}{$instance->getHealthCheck()['path']}",
                'Interval' => "{$instance->getHealthCheck()['interval']}s",
                'Timeout' => "{$instance->getHealthCheck()['timeout']}s",
                'DeregisterCriticalServiceAfter' => '30s'
            ]
        ];
        
        $context = stream_context_create([
            'http' => [
                'method' => 'PUT',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($serviceData)
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new ServiceDiscoveryException("Failed to register service with Consul");
        }
        
        $this->logger->info("Service registered with Consul", [
            'service_id' => $instance->getId(),
            'service_name' => $instance->getServiceName(),
            'address' => $instance->getAddress()
        ]);
    }
    
    public function unregister(string $serviceId): void {
        $url = "{$this->consulUrl}/v1/agent/service/deregister/$serviceId";
        
        $context = stream_context_create([
            'http' => [
                'method' => 'PUT'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new ServiceDiscoveryException("Failed to unregister service from Consul");
        }
        
        $this->logger->info("Service unregistered from Consul", [
            'service_id' => $serviceId
        ]);
    }
    
    public function discover(string $serviceName): array {
        $url = "{$this->consulUrl}/v1/health/service/{$serviceName}?passing";
        
        $response = @file_get_contents($url);
        
        if ($response === false) {
            throw new ServiceDiscoveryException("Failed to discover services from Consul");
        }
        
        $services = json_decode($response, true);
        $instances = [];
        
        foreach ($services as $service) {
            $instances[] = [
                'id' => $service['Service']['ID'],
                'service_name' => $service['Service']['Service'],
                'host' => $service['Service']['Address'],
                'port' => $service['Service']['Port'],
                'address' => $service['Service']['Address'] . ':' . $service['Service']['Port'],
                'metadata' => $service['Service']['Meta'] ?? [],
                'health' => 'passing'
            ];
        }
        
        return $instances;
    }
    
    public function getInstances(string $serviceName): array {
        return $this->discover($serviceName);
    }
    
    public function watch(string $serviceName, callable $callback): void {
        $this->watchers[$serviceName] = $callback;
        
        // Start watching in background (simplified)
        $this->startWatching($serviceName, $callback);
    }
    
    private function startWatching(string $serviceName, callable $callback): void {
        $index = 0;
        
        while (true) {
            try {
                $url = "{$this->consulUrl}/v1/health/service/{$serviceName}?index={$index}&wait=30s&passing";
                $response = @file_get_contents($url);
                
                if ($response !== false) {
                    $data = json_decode($response, true);
                    $newIndex = $data[0]['Service']['CreateIndex'] ?? $index;
                    
                    if ($newIndex > $index) {
                        $callback($this->discover($serviceName));
                        $index = $newIndex;
                    }
                }
            } catch (Exception $e) {
                $this->logger->error("Error watching service", [
                    'service' => $serviceName,
                    'error' => $e->getMessage()
                ]);
            }
            
            sleep(5);
        }
    }
}

// In-memory Service Discovery (for testing)
class InMemoryServiceDiscovery implements ServiceDiscoveryInterface {
    private array $services = [];
    private array $watchers = [];
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function register(ServiceInstance $instance): void {
        $this->services[$instance->getServiceName()][$instance->getId()] = $instance;
        
        $this->logger->info("Service registered", [
            'service_id' => $instance->getId(),
            'service_name' => $instance->getServiceName(),
            'address' => $instance->getAddress()
        ]);
        
        $this->notifyWatchers($instance->getServiceName());
    }
    
    public function unregister(string $serviceId): void {
        foreach ($this->services as $serviceName => $instances) {
            if (isset($instances[$serviceId])) {
                unset($this->services[$serviceName][$serviceId]);
                
                $this->logger->info("Service unregistered", [
                    'service_id' => $serviceId,
                    'service_name' => $serviceName
                ]);
                
                $this->notifyWatchers($serviceName);
                break;
            }
        }
    }
    
    public function discover(string $serviceName): array {
        if (!isset($this->services[$serviceName])) {
            return [];
        }
        
        return array_map(fn($instance) => $instance->toArray(), $this->services[$serviceName]);
    }
    
    public function getInstances(string $serviceName): array {
        return $this->discover($serviceName);
    }
    
    public function watch(string $serviceName, callable $callback): void {
        if (!isset($this->watchers[$serviceName])) {
            $this->watchers[$serviceName] = [];
        }
        
        $this->watchers[$serviceName][] = $callback;
    }
    
    private function notifyWatchers(string $serviceName): void {
        if (!isset($this->watchers[$serviceName])) {
            return;
        }
        
        $instances = $this->discover($serviceName);
        
        foreach ($this->watchers[$serviceName] as $callback) {
            try {
                $callback($instances);
            } catch (Exception $e) {
                $this->logger->error("Watcher callback failed", [
                    'service' => $serviceName,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}

// Load Balancer
class LoadBalancer {
    private ServiceDiscoveryInterface $discovery;
    private LoggerInterface $logger;
    private array $strategies = [
        'round_robin' => 'roundRobinStrategy',
        'random' => 'randomStrategy',
        'least_connections' => 'leastConnectionsStrategy'
    ];
    
    public function __construct(ServiceDiscoveryInterface $discovery, LoggerInterface $logger) {
        $this->discovery = $discovery;
        $this->logger = $logger;
    }
    
    public function selectInstance(string $serviceName, string $strategy = 'round_robin'): ?array {
        $instances = $this->discovery->getInstances($serviceName);
        
        if (empty($instances)) {
            return null;
        }
        
        $healthyInstances = array_filter($instances, fn($instance) => $this->isHealthy($instance));
        
        if (empty($healthyInstances)) {
            return null;
        }
        
        $strategyMethod = $this->strategies[$strategy] ?? 'roundRobinStrategy';
        
        return $this->{$strategyMethod}($healthyInstances);
    }
    
    private function roundRobinStrategy(array $instances): array {
        static $indexes = [];
        
        $serviceName = $instances[0]['service_name'];
        
        if (!isset($indexes[$serviceName])) {
            $indexes[$serviceName] = 0;
        }
        
        $index = $indexes[$serviceName] % count($instances);
        $indexes[$serviceName]++;
        
        return $instances[$index];
    }
    
    private function randomStrategy(array $instances): array {
        return $instances[array_rand($instances)];
    }
    
    private function leastConnectionsStrategy(array $instances): array {
        // Simplified - would need actual connection tracking
        return $this->randomStrategy($instances);
    }
    
    private function isHealthy(array $instance): bool {
        $healthCheckUrl = "http://{$instance['address']}/health";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 2,
                'method' => 'GET'
            ]
        ]);
        
        $response = @file_get_contents($healthCheckUrl, false, $context);
        
        return $response !== false && strpos($response, 'healthy') !== false;
    }
}

// Service Registry with TTL
class ServiceRegistry {
    private ServiceDiscoveryInterface $discovery;
    private LoggerInterface $logger;
    private array $registeredServices = [];
    private int $ttl = 60; // seconds
    
    public function __construct(ServiceDiscoveryInterface $discovery, LoggerInterface $logger, int $ttl = 60) {
        $this->discovery = $discovery;
        $this->logger = $logger;
        $this->ttl = $ttl;
    }
    
    public function registerService(ServiceInstance $instance): void {
        $this->discovery->register($instance);
        $this->registeredServices[$instance->getId()] = [
            'instance' => $instance,
            'last_heartbeat' => time()
        ];
        
        $this->logger->info("Service registered in registry", [
            'service_id' => $instance->getId(),
            'service_name' => $instance->getServiceName()
        ]);
    }
    
    public function unregisterService(string $serviceId): void {
        $this->discovery->unregister($serviceId);
        unset($this->registeredServices[$serviceId]);
        
        $this->logger->info("Service unregistered from registry", [
            'service_id' => $serviceId
        ]);
    }
    
    public function sendHeartbeat(string $serviceId): void {
        if (isset($this->registeredServices[$serviceId])) {
            $this->registeredServices[$serviceId]['last_heartbeat'] = time();
        }
    }
    
    public function cleanupExpiredServices(): void {
        $now = time();
        $expired = [];
        
        foreach ($this->registeredServices as $serviceId => $service) {
            if ($now - $service['last_heartbeat'] > $this->ttl) {
                $expired[] = $serviceId;
            }
        }
        
        foreach ($expired as $serviceId) {
            $this->unregisterService($serviceId);
            $this->logger->info("Service expired and removed", [
                'service_id' => $serviceId
            ]);
        }
    }
    
    public function getRegisteredServices(): array {
        return $this->registeredServices;
    }
    
    public function startHeartbeatCheck(): void {
        // Start background process to check heartbeats
        register_shutdown_function(function() {
            $this->cleanupExpiredServices();
        });
    }
}

// Auto-registration for services
class ServiceAutoRegistrar {
    private ServiceRegistry $registry;
    private LoggerInterface $logger;
    private ServiceInstance $instance;
    private bool $running = false;
    
    public function __construct(
        ServiceRegistry $registry,
        LoggerInterface $logger,
        string $serviceName,
        string $host,
        int $port,
        array $metadata = []
    ) {
        $this->registry = $registry;
        $this->logger = $logger;
        $this->instance = new ServiceInstance($serviceName, $host, $port, $metadata);
    }
    
    public function start(): void {
        $this->registry->registerService($this->instance);
        $this->running = true;
        
        // Start heartbeat sender
        $this->startHeartbeat();
        
        $this->logger->info("Service auto-registration started", [
            'service_id' => $this->instance->getId(),
            'service_name' => $this->instance->getServiceName()
        ]);
    }
    
    public function stop(): void {
        $this->running = false;
        $this->registry->unregisterService($this->instance->getId());
        
        $this->logger->info("Service auto-registration stopped", [
            'service_id' => $this->instance->getId()
        ]);
    }
    
    private function startHeartbeat(): void {
        // Simplified heartbeat mechanism
        register_shutdown_function(function() {
            if ($this->running) {
                $this->registry->sendHeartbeat($this->instance->getId());
            }
        });
    }
}

// Usage examples
$logger = new SimpleLogger();

// Create service discovery
$discovery = new InMemoryServiceDiscovery($logger);

// Create service registry
$registry = new ServiceRegistry($discovery, $logger, 60);

// Create load balancer
$loadBalancer = new LoadBalancer($discovery, $logger);

// Register services
$userService = new ServiceInstance('user-service', 'localhost', 8001, ['version' => '1.0.0']);
$orderService = new ServiceInstance('order-service', 'localhost', 8002, ['version' => '1.0.0']);
$productService = new ServiceInstance('product-service', 'localhost', 8003, ['version' => '1.0.0']);

$registry->registerService($userService);
$registry->registerService($orderService);
$registry->registerService($productService);

// Discover services
$userServices = $discovery->discover('user-service');
echo "User services: " . json_encode($userServices) . "\n";

// Load balancing
$selectedUserService = $loadBalancer->selectInstance('user-service', 'round_robin');
echo "Selected user service: " . json_encode($selectedUserService) . "\n";

$selectedOrderService = $loadBalancer->selectInstance('order-service', 'random');
echo "Selected order service: " . json_encode($selectedOrderService) . "\n";

// Watch for service changes
$discovery->watch('user-service', function($instances) {
    echo "User services updated: " . count($instances) . " instances\n";
});

// Auto-registration
$autoRegistrar = new ServiceAutoRegistrar(
    $registry,
    $logger,
    'notification-service',
    'localhost',
    8004,
    ['version' => '1.0.0', 'type' => 'microservice']
);

$autoRegistrar->start();

// Simulate some work
sleep(2);

// Cleanup
$autoRegistrar->stop();
?>
```

## Container Orchestration

### Docker and Kubernetes Integration
```php
<?php
// Docker Manager
class DockerManager {
    private string $dockerHost;
    private LoggerInterface $logger;
    
    public function __construct(string $dockerHost, LoggerInterface $logger) {
        $this->dockerHost = $dockerHost;
        $this->logger = $logger;
    }
    
    public function buildImage(string $imageName, string $dockerfilePath, string $contextPath): bool {
        $command = "docker build -t $imageName $contextPath";
        
        $this->logger->info("Building Docker image", [
            'image' => $imageName,
            'dockerfile' => $dockerfilePath,
            'context' => $contextPath
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Docker build failed", [
                'image' => $imageName,
                'output' => implode("\n", $output)
            ]);
            return false;
        }
        
        $this->logger->info("Docker image built successfully", [
            'image' => $imageName
        ]);
        
        return true;
    }
    
    public function runContainer(string $imageName, array $options = []): ?string {
        $command = "docker run -d";
        
        // Add options
        if (isset($options['name'])) {
            $command .= " --name {$options['name']}";
        }
        
        if (isset($options['ports'])) {
            foreach ($options['ports'] as $hostPort => $containerPort) {
                $command .= " -p $hostPort:$containerPort";
            }
        }
        
        if (isset($options['environment'])) {
            foreach ($options['environment'] as $key => $value) {
                $command .= " -e $key=$value";
            }
        }
        
        if (isset($options['volumes'])) {
            foreach ($options['volumes'] as $hostPath => $containerPath) {
                $command .= " -v $hostPath:$containerPath";
            }
        }
        
        if (isset($options['network'])) {
            $command .= " --network {$options['network']}";
        }
        
        $command .= " $imageName";
        
        $this->logger->info("Running Docker container", [
            'image' => $imageName,
            'options' => $options
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Docker run failed", [
                'image' => $imageName,
                'output' => implode("\n", $output)
            ]);
            return null;
        }
        
        $containerId = trim($output[0]);
        
        $this->logger->info("Docker container started", [
            'container_id' => $containerId,
            'image' => $imageName
        ]);
        
        return $containerId;
    }
    
    public function stopContainer(string $containerId): bool {
        $command = "docker stop $containerId";
        
        $this->logger->info("Stopping Docker container", [
            'container_id' => $containerId
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Docker stop failed", [
                'container_id' => $containerId,
                'output' => implode("\n", $output)
            ]);
            return false;
        }
        
        $this->logger->info("Docker container stopped", [
            'container_id' => $containerId
        ]);
        
        return true;
    }
    
    public function removeContainer(string $containerId): bool {
        $command = "docker rm $containerId";
        
        $this->logger->info("Removing Docker container", [
            'container_id' => $containerId
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Docker remove failed", [
                'container_id' => $containerId,
                'output' => implode("\n", $output)
            ]);
            return false;
        }
        
        $this->logger->info("Docker container removed", [
            'container_id' => $containerId
        ]);
        
        return true;
    }
    
    public function getContainerInfo(string $containerId): ?array {
        $command = "docker inspect $containerId";
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            return null;
        }
        
        $data = json_decode(implode("\n", $output), true);
        
        if (empty($data)) {
            return null;
        }
        
        $container = $data[0];
        
        return [
            'id' => $container['Id'],
            'name' => $container['Name'],
            'status' => $container['State']['Status'],
            'image' => $container['Config']['Image'],
            'created' => $container['Created'],
            'ports' => $container['NetworkSettings']['Ports'] ?? [],
            'environment' => $container['Config']['Env'] ?? []
        ];
    }
    
    public function listContainers(bool $all = false): array {
        $command = $all ? "docker ps -a" : "docker ps";
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            return [];
        }
        
        $containers = [];
        $headers = [];
        
        foreach ($output as $line) {
            $parts = preg_split('/\s{2,}/', $line);
            
            if (empty($headers)) {
                $headers = $parts;
                continue;
            }
            
            $container = array_combine($headers, $parts);
            $containers[] = $container;
        }
        
        return $containers;
    }
    
    public function pushImage(string $imageName): bool {
        $command = "docker push $imageName";
        
        $this->logger->info("Pushing Docker image", [
            'image' => $imageName
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Docker push failed", [
                'image' => $imageName,
                'output' => implode("\n", $output)
            ]);
            return false;
        }
        
        $this->logger->info("Docker image pushed successfully", [
            'image' => $imageName
        ]);
        
        return true;
    }
}

// Kubernetes Manager
class KubernetesManager {
    private string $kubeconfig;
    private LoggerInterface $logger;
    
    public function __construct(string $kubeconfig, LoggerInterface $logger) {
        $this->kubeconfig = $kubeconfig;
        $this->logger = $logger;
    }
    
    public function applyYaml(string $yamlFile): bool {
        $command = "kubectl apply -f $yamlFile --kubeconfig {$this->kubeconfig}";
        
        $this->logger->info("Applying Kubernetes YAML", [
            'file' => $yamlFile
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Kubernetes apply failed", [
                'file' => $yamlFile,
                'output' => implode("\n", $output)
            ]);
            return false;
        }
        
        $this->logger->info("Kubernetes YAML applied successfully", [
            'file' => $yamlFile
        ]);
        
        return true;
    }
    
    public function deleteYaml(string $yamlFile): bool {
        $command = "kubectl delete -f $yamlFile --kubeconfig {$this->kubeconfig}";
        
        $this->logger->info("Deleting Kubernetes resources", [
            'file' => $yamlFile
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Kubernetes delete failed", [
                'file' => $yamlFile,
                'output' => implode("\n", $output)
            ]);
            return false;
        }
        
        $this->logger->info("Kubernetes resources deleted successfully", [
            'file' => $yamlFile
        ]);
        
        return true;
    }
    
    public function getPods(string $namespace = 'default'): array {
        $command = "kubectl get pods -n $namespace -o json --kubeconfig {$this->kubeconfig}";
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            return [];
        }
        
        $data = json_decode(implode("\n", $output), true);
        
        $pods = [];
        
        foreach ($data['items'] as $pod) {
            $pods[] = [
                'name' => $pod['metadata']['name'],
                'namespace' => $pod['metadata']['namespace'],
                'status' => $pod['status']['phase'],
                'ip' => $pod['status']['podIP'] ?? null,
                'node' => $pod['spec']['nodeName'] ?? null,
                'created' => $pod['metadata']['creationTimestamp'],
                'labels' => $pod['metadata']['labels'] ?? []
            ];
        }
        
        return $pods;
    }
    
    public function getServices(string $namespace = 'default'): array {
        $command = "kubectl get services -n $namespace -o json --kubeconfig {$this->kubeconfig}";
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            return [];
        }
        
        $data = json_decode(implode("\n", $output), true);
        
        $services = [];
        
        foreach ($data['items'] as $service) {
            $services[] = [
                'name' => $service['metadata']['name'],
                'namespace' => $service['metadata']['namespace'],
                'type' => $service['spec']['type'],
                'cluster_ip' => $service['spec']['clusterIP'],
                'external_ip' => $service['spec']['externalIPs'][0] ?? null,
                'ports' => $service['spec']['ports'] ?? [],
                'selector' => $service['spec']['selector'] ?? []
            ];
        }
        
        return $services;
    }
    
    public function getDeployments(string $namespace = 'default'): array {
        $command = "kubectl get deployments -n $namespace -o json --kubeconfig {$this->kubeconfig}";
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            return [];
        }
        
        $data = json_decode(implode("\n", $output), true);
        
        $deployments = [];
        
        foreach ($data['items'] as $deployment) {
            $deployments[] = [
                'name' => $deployment['metadata']['name'],
                'namespace' => $deployment['metadata']['namespace'],
                'replicas' => $deployment['spec']['replicas'] ?? 0,
                'available_replicas' => $deployment['status']['availableReplicas'] ?? 0,
                'created' => $deployment['metadata']['creationTimestamp'],
                'labels' => $deployment['metadata']['labels'] ?? []
            ];
        }
        
        return $deployments;
    }
    
    public function scaleDeployment(string $deploymentName, int $replicas, string $namespace = 'default'): bool {
        $command = "kubectl scale deployment $deploymentName --replicas=$replicas -n $namespace --kubeconfig {$this->kubeconfig}";
        
        $this->logger->info("Scaling Kubernetes deployment", [
            'deployment' => $deploymentName,
            'replicas' => $replicas,
            'namespace' => $namespace
        ]);
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->logger->error("Kubernetes scale failed", [
                'deployment' => $deploymentName,
                'output' => implode("\n", $output)
            ]);
            return false;
        }
        
        $this->logger->info("Kubernetes deployment scaled successfully", [
            'deployment' => $deploymentName,
            'replicas' => $replicas
        ]);
        
        return true;
    }
    
    public function getLogs(string $podName, string $namespace = 'default', int $lines = 100): string {
        $command = "kubectl logs $podName -n $namespace --tail=$lines --kubeconfig {$this->kubeconfig}";
        
        $output = [];
        $returnCode = 0;
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            return '';
        }
        
        return implode("\n", $output);
    }
}

// Microservice Deployment Manager
class MicroserviceDeploymentManager {
    private DockerManager $dockerManager;
    private KubernetesManager $kubernetesManager;
    private LoggerInterface $logger;
    private string $registryUrl;
    
    public function __construct(
        DockerManager $dockerManager,
        KubernetesManager $kubernetesManager,
        LoggerInterface $logger,
        string $registryUrl = 'localhost:5000'
    ) {
        $this->dockerManager = $dockerManager;
        $this->kubernetesManager = $kubernetesManager;
        $this->logger = $logger;
        $this->registryUrl = $registryUrl;
    }
    
    public function deployMicroservice(MicroserviceConfig $config): bool {
        $this->logger->info("Starting microservice deployment", [
            'service' => $config->getName(),
            'version' => $config->getVersion()
        ]);
        
        try {
            // Build Docker image
            $imageName = "{$this->registryUrl}/{$config->getName()}:{$config->getVersion()}";
            
            if (!$this->dockerManager->buildImage($imageName, $config->getDockerfilePath(), $config->getContextPath())) {
                throw new DeploymentException("Failed to build Docker image");
            }
            
            // Push image to registry
            if (!$this->dockerManager->pushImage($imageName)) {
                throw new DeploymentException("Failed to push Docker image");
            }
            
            // Generate Kubernetes manifests
            $manifests = $this->generateKubernetesManifests($config, $imageName);
            
            // Apply manifests
            foreach ($manifests as $manifest) {
                $tempFile = tempnam(sys_get_temp_dir(), 'k8s-');
                file_put_contents($tempFile, $manifest);
                
                if (!$this->kubernetesManager->applyYaml($tempFile)) {
                    unlink($tempFile);
                    throw new DeploymentException("Failed to apply Kubernetes manifest");
                }
                
                unlink($tempFile);
            }
            
            $this->logger->info("Microservice deployed successfully", [
                'service' => $config->getName(),
                'version' => $config->getVersion(),
                'image' => $imageName
            ]);
            
            return true;
            
        } catch (Exception $e) {
            $this->logger->error("Microservice deployment failed", [
                'service' => $config->getName(),
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    public function rollbackMicroservice(string $serviceName, string $previousVersion): bool {
        $this->logger->info("Starting microservice rollback", [
            'service' => $serviceName,
            'previous_version' => $previousVersion
        ]);
        
        try {
            // Scale down current deployment
            if (!$this->kubernetesManager->scaleDeployment($serviceName, 0)) {
                throw new DeploymentException("Failed to scale down current deployment");
            }
            
            // Deploy previous version
            $imageName = "{$this->registryUrl}/$serviceName:$previousVersion";
            
            // This would need the previous config - simplified for example
            $config = new MicroserviceConfig($serviceName, $previousVersion);
            $config->setImage($imageName);
            
            return $this->deployMicroservice($config);
            
        } catch (Exception $e) {
            $this->logger->error("Microservice rollback failed", [
                'service' => $serviceName,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    public function scaleMicroservice(string $serviceName, int $replicas): bool {
        return $this->kubernetesManager->scaleDeployment($serviceName, $replicas);
    }
    
    public function getMicroserviceStatus(string $serviceName): array {
        $deployments = $this->kubernetesManager->getDeployments();
        $pods = $this->kubernetesManager->getPods();
        $services = $this->kubernetesManager->getServices();
        
        $deployment = null;
        foreach ($deployments as $dep) {
            if ($dep['name'] === $serviceName) {
                $deployment = $dep;
                break;
            }
        }
        
        $servicePods = array_filter($pods, fn($pod) => 
            isset($pod['labels']['app']) && $pod['labels']['app'] === $serviceName
        );
        
        $serviceServices = array_filter($services, fn($service) => $service['name'] === $serviceName);
        
        return [
            'deployment' => $deployment,
            'pods' => array_values($servicePods),
            'services' => array_values($serviceServices),
            'healthy_pods' => count(array_filter($servicePods, fn($pod) => $pod['status'] === 'Running')),
            'total_pods' => count($servicePods)
        ];
    }
    
    private function generateKubernetesManifests(MicroserviceConfig $config, string $imageName): array {
        $manifests = [];
        
        // Deployment manifest
        $deployment = [
            'apiVersion' => 'apps/v1',
            'kind' => 'Deployment',
            'metadata' => [
                'name' => $config->getName(),
                'labels' => [
                    'app' => $config->getName(),
                    'version' => $config->getVersion()
                ]
            ],
            'spec' => [
                'replicas' => $config->getReplicas(),
                'selector' => [
                    'matchLabels' => [
                        'app' => $config->getName()
                    ]
                ],
                'template' => [
                    'metadata' => [
                        'labels' => [
                            'app' => $config->getName(),
                            'version' => $config->getVersion()
                        ]
                    ],
                    'spec' => [
                        'containers' => [
                            [
                                'name' => $config->getName(),
                                'image' => $imageName,
                                'ports' => array_map(fn($port) => ['containerPort' => $port], $config->getPorts()),
                                'env' => array_map(fn($key, $value) => ['name' => $key, 'value' => $value], 
                                    array_keys($config->getEnvironment()), 
                                    $config->getEnvironment()),
                                'resources' => [
                                    'requests' => [
                                        'memory' => $config->getMemoryRequest(),
                                        'cpu' => $config->getCpuRequest()
                                    ],
                                    'limits' => [
                                        'memory' => $config->getMemoryLimit(),
                                        'cpu' => $config->getCpuLimit()
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        $manifests[] = yaml_emit($deployment);
        
        // Service manifest
        if (!empty($config->getPorts())) {
            $service = [
                'apiVersion' => 'v1',
                'kind' => 'Service',
                'metadata' => [
                    'name' => $config->getName(),
                    'labels' => [
                        'app' => $config->getName()
                    ]
                ],
                'spec' => [
                    'selector' => [
                        'app' => $config->getName()
                    ],
                    'ports' => array_map(fn($port) => [
                        'port' => $port,
                        'targetPort' => $port
                    ], $config->getPorts()),
                    'type' => $config->getServiceType()
                ]
            ];
            
            $manifests[] = yaml_emit($service);
        }
        
        return $manifests;
    }
}

// Microservice Configuration
class MicroserviceConfig {
    private string $name;
    private string $version;
    private string $dockerfilePath;
    private string $contextPath;
    private int $replicas;
    private array $ports;
    private array $environment;
    private string $memoryRequest;
    private string $memoryLimit;
    private string $cpuRequest;
    private string $cpuLimit;
    private string $serviceType;
    
    public function __construct(
        string $name,
        string $version,
        string $dockerfilePath = 'Dockerfile',
        string $contextPath = '.'
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->dockerfilePath = $dockerfilePath;
        $this->contextPath = $contextPath;
        $this->replicas = 1;
        $this->ports = [];
        $this->environment = [];
        $this->memoryRequest = '128Mi';
        $this->memoryLimit = '512Mi';
        $this->cpuRequest = '100m';
        $this->cpuLimit = '500m';
        $this->serviceType = 'ClusterIP';
    }
    
    // Getters and setters
    public function getName(): string {
        return $this->name;
    }
    
    public function getVersion(): string {
        return $this->version;
    }
    
    public function getDockerfilePath(): string {
        return $this->dockerfilePath;
    }
    
    public function getContextPath(): string {
        return $this->contextPath;
    }
    
    public function getReplicas(): int {
        return $this->replicas;
    }
    
    public function setReplicas(int $replicas): self {
        $this->replicas = $replicas;
        return $this;
    }
    
    public function getPorts(): array {
        return $this->ports;
    }
    
    public function addPort(int $port): self {
        $this->ports[] = $port;
        return $this;
    }
    
    public function getEnvironment(): array {
        return $this->environment;
    }
    
    public function addEnvironment(string $key, string $value): self {
        $this->environment[$key] = $value;
        return $this;
    }
    
    public function getMemoryRequest(): string {
        return $this->memoryRequest;
    }
    
    public function setMemoryRequest(string $memoryRequest): self {
        $this->memoryRequest = $memoryRequest;
        return $this;
    }
    
    public function getMemoryLimit(): string {
        return $this->memoryLimit;
    }
    
    public function setMemoryLimit(string $memoryLimit): self {
        $this->memoryLimit = $memoryLimit;
        return $this;
    }
    
    public function getCpuRequest(): string {
        return $this->cpuRequest;
    }
    
    public function setCpuRequest(string $cpuRequest): self {
        $this->cpuRequest = $cpuRequest;
        return $this;
    }
    
    public function getCpuLimit(): string {
        return $this->cpuLimit;
    }
    
    public function setCpuLimit(string $cpuLimit): self {
        $this->cpuLimit = $cpuLimit;
        return $this;
    }
    
    public function getServiceType(): string {
        return $this->serviceType;
    }
    
    public function setServiceType(string $serviceType): self {
        $this->serviceType = $serviceType;
        return $this;
    }
    
    public function setImage(string $image): self {
        $this->image = $image;
        return $this;
    }
}

// Usage examples
$logger = new SimpleLogger();

// Create managers
$dockerManager = new DockerManager('unix:///var/run/docker.sock', $logger);
$kubernetesManager = new KubernetesManager('/path/to/kubeconfig', $logger);
$deploymentManager = new MicroserviceDeploymentManager($dockerManager, $kubernetesManager, $logger);

// Create microservice configuration
$userServiceConfig = new MicroserviceConfig('user-service', '1.0.0');
$userServiceConfig
    ->setReplicas(3)
    ->addPort(8001)
    ->addEnvironment('DATABASE_URL', 'mysql://localhost:3306/users')
    ->addEnvironment('REDIS_URL', 'redis://localhost:6379')
    ->setMemoryLimit('256Mi')
    ->setCpuLimit('200m')
    ->setServiceType('LoadBalancer');

// Deploy microservice
$success = $deploymentManager->deployMicroservice($userServiceConfig);

if ($success) {
    echo "Microservice deployed successfully\n";
    
    // Get status
    $status = $deploymentManager->getMicroserviceStatus('user-service');
    echo "Service status: " . json_encode($status, JSON_PRETTY_PRINT) . "\n";
    
    // Scale service
    $deploymentManager->scaleMicroservice('user-service', 5);
    echo "Service scaled to 5 replicas\n";
} else {
    echo "Microservice deployment failed\n";
}
?>
```

## Summary

PHP Microservices provides:

**Microservice Architecture:**
- Service design principles and patterns
- Base microservice implementation
- Service registry and discovery
- API gateway implementation

**Inter-Service Communication:**
- HTTP client with retry logic
- Circuit breaker pattern
- Service clients for specific services
- Message bus for asynchronous communication
- Event-driven architecture

**Service Discovery:**
- Dynamic service registration
- Health checking and monitoring
- Load balancing strategies
- Auto-registration mechanisms
- TTL-based service management

**Container Orchestration:**
- Docker container management
- Kubernetes deployment automation
- Microservice deployment manager
- Configuration management
- Scaling and rollback capabilities

**Key Benefits:**
- Scalability and flexibility
- Independent deployment
- Technology diversity
- Fault isolation
- Team autonomy

**Implementation Considerations:**
- Service boundaries and responsibilities
- Communication patterns
- Data consistency
- Monitoring and logging
- Security and authentication

Microservices architecture enables building scalable, maintainable, and resilient applications by breaking down complex systems into smaller, independent services that can be developed, deployed, and scaled independently.
