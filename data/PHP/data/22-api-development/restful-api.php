<?php
/**
 * RESTful API Development
 * 
 * Comprehensive implementation of RESTful API patterns and best practices.
 */

// RESTful API Router
class RestfulRouter
{
    private array $routes = [];
    private array $middleware = [];
    private array $params = [];
    
    public function __construct()
    {
        $this->initializeRoutes();
        $this->setupMiddleware();
    }
    
    /**
     * Initialize RESTful routes
     */
    private function initializeRoutes(): void
    {
        $this->routes = [
            'GET' => [
                '/users' => 'UserController@index',
                '/users/{id}' => 'UserController@show',
                '/posts' => 'PostController@index',
                '/posts/{id}' => 'PostController@show',
                '/posts/{id}/comments' => 'CommentController@index',
                '/categories' => 'CategoryController@index',
                '/categories/{id}/posts' => 'PostController@byCategory'
            ],
            'POST' => [
                '/users' => 'UserController@store',
                '/posts' => 'PostController@store',
                '/posts/{id}/comments' => 'CommentController@store',
                '/login' => 'AuthController@login',
                '/register' => 'AuthController@register'
            ],
            'PUT' => [
                '/users/{id}' => 'UserController@update',
                '/posts/{id}' => 'PostController@update',
                '/posts/{id}/comments/{comment_id}' => 'CommentController@update'
            ],
            'PATCH' => [
                '/users/{id}' => 'UserController@update',
                '/posts/{id}/publish' => 'PostController@publish',
                '/posts/{id}/like' => 'PostController@like'
            ],
            'DELETE' => [
                '/users/{id}' => 'UserController@delete',
                '/posts/{id}' => 'PostController@delete',
                '/posts/{id}/comments/{comment_id}' => 'CommentController@delete'
            ]
        ];
    }
    
    /**
     * Setup middleware
     */
    private function setupMiddleware(): void
    {
        $this->middleware = [
            'auth' => 'AuthMiddleware@handle',
            'cors' => 'CorsMiddleware@handle',
            'rate_limit' => 'RateLimitMiddleware@handle',
            'validation' => 'ValidationMiddleware@handle'
        ];
    }
    
    /**
     * Add route
     */
    public function addRoute(string $method, string $path, string $handler, array $middleware = []): void
    {
        $this->routes[strtoupper($method)][$path] = $handler;
        $this->middleware[$path] = $middleware;
    }
    
    /**
     * Route request
     */
    public function route(string $method, string $uri): array
    {
        $method = strtoupper($method);
        $this->params = [];
        
        // Find matching route
        foreach ($this->routes[$method] as $route => $handler) {
            if ($this->matchRoute($route, $uri)) {
                return [
                    'handler' => $handler,
                    'params' => $this->params,
                    'middleware' => $this->middleware[$route] ?? []
                ];
            }
        }
        
        return [
            'handler' => null,
            'params' => [],
            'middleware' => []
        ];
    }
    
    /**
     * Match route pattern
     */
    private function matchRoute(string $pattern, string $uri): bool
    {
        $pattern = preg_replace('/{([^}]+)}/', '([^/]+)', $pattern);
        $pattern = "#^$pattern$#";
        
        if (preg_match($pattern, $uri, $matches)) {
            // Extract parameter names
            preg_match_all('/{([^}]+)}/', $pattern, $paramNames);
            
            // Map parameters
            for ($i = 1; $i < count($matches); $i++) {
                $paramName = $paramNames[1][$i - 1] ?? 'param' . $i;
                $this->params[$paramName] = $matches[$i];
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get all routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}

// RESTful Response Builder
class RestfulResponse
{
    private array $data;
    private int $statusCode;
    private array $headers;
    private array $meta;
    
    public function __construct()
    {
        $this->statusCode = 200;
        $this->headers = [
            'Content-Type' => 'application/json',
            'X-API-Version' => '1.0.0'
        ];
        $this->meta = [];
    }
    
    /**
     * Set response data
     */
    public function data(array $data): self
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Set status code
     */
    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }
    
    /**
     * Add header
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * Add meta information
     */
    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }
    
    /**
     * Success response
     */
    public function success(array $data = [], string $message = 'Success'): self
    {
        return $this->data($data)
            ->status(200)
            ->meta(['message' => $message, 'success' => true]);
    }
    
    /**
     * Created response
     */
    public function created(array $data = [], string $message = 'Resource created'): self
    {
        return $this->data($data)
            ->status(201)
            ->meta(['message' => $message, 'success' => true]);
    }
    
    /**
     * Error response
     */
    public function error(string $message, int $code = 400, array $errors = []): self
    {
        return $this->data(['errors' => $errors])
            ->status($code)
            ->meta(['message' => $message, 'success' => false]);
    }
    
    /**
     * Not found response
     */
    public function notFound(string $message = 'Resource not found'): self
    {
        return $this->error($message, 404);
    }
    
    /**
     * Unauthorized response
     */
    public function unauthorized(string $message = 'Unauthorized'): self
    {
        return $this->error($message, 401);
    }
    
    /**
     * Forbidden response
     */
    public function forbidden(string $message = 'Forbidden'): self
    {
        return $this->error($message, 403);
    }
    
    /**
     * Validation error response
     */
    public function validationError(array $errors, string $message = 'Validation failed'): self
    {
        return $this->error($message, 422, $errors);
    }
    
    /**
     * Paginated response
     */
    public function paginated(array $data, array $pagination): self
    {
        return $this->data($data)
            ->status(200)
            ->meta(array_merge([
                'success' => true,
                'pagination' => $pagination
            ], $this->meta));
    }
    
    /**
     * Build response array
     */
    public function build(): array
    {
        $response = [
            'status' => $this->statusCode,
            'headers' => $this->headers
        ];
        
        if (!empty($this->data) || !empty($this->meta)) {
            $response['body'] = array_merge($this->data, $this->meta);
        }
        
        return $response;
    }
    
    /**
     * Send JSON response
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        $body = array_merge($this->data, $this->meta);
        echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

// RESTful Controller Base
class RestfulController
{
    protected RestfulResponse $response;
    protected array $request;
    
    public function __construct()
    {
        $this->response = new RestfulResponse();
        $this->request = $this->getRequestData();
    }
    
    /**
     * Get request data
     */
    protected function getRequestData(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        switch ($method) {
            case 'GET':
                return $_GET;
            case 'POST':
                return json_decode(file_get_contents('php://input'), true) ?? $_POST;
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                return json_decode(file_get_contents('php://input'), true) ?? [];
            default:
                return [];
        }
    }
    
    /**
     * Validate required fields
     */
    protected function validate(array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->request[$field] ?? null;
            
            if ($rule === 'required' && empty($value)) {
                $errors[$field] = "Field $field is required";
            } elseif (is_string($rule) && strpos($rule, 'min:') === 0) {
                $minLength = (int) substr($rule, 4);
                if (strlen($value) < $minLength) {
                    $errors[$field] = "Field $field must be at least $minLength characters";
                }
            } elseif (is_string($rule) && strpos($rule, 'max:') === 0) {
                $maxLength = (int) substr($rule, 4);
                if (strlen($value) > $maxLength) {
                    $errors[$field] = "Field $field must not exceed $maxLength characters";
                }
            } elseif ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Field $field must be a valid email";
            }
        }
        
        return $errors;
    }
    
    /**
     * Get pagination parameters
     */
    protected function getPagination(): array
    {
        $page = max(1, (int) ($this->request['page'] ?? 1));
        $limit = min(100, max(1, (int) ($this->request['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset
        ];
    }
}

// User Controller Example
class UserController extends RestfulController
{
    private array $users = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->initializeUsers();
    }
    
    /**
     * Initialize sample users
     */
    private function initializeUsers(): void
    {
        $this->users = [
            1 => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            2 => ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            3 => ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com']
        ];
    }
    
    /**
     * Get all users
     */
    public function index(): void
    {
        $pagination = $this->getPagination();
        $total = count($this->users);
        $users = array_slice($this->users, $pagination['offset'], $pagination['limit'], true);
        
        $paginationInfo = [
            'total' => $total,
            'page' => $pagination['page'],
            'limit' => $pagination['limit'],
            'pages' => ceil($total / $pagination['limit'])
        ];
        
        $this->response->paginated(array_values($users), $paginationInfo)->send();
    }
    
    /**
     * Get single user
     */
    public function show(array $params): void
    {
        $id = (int) $params['id'];
        
        if (!isset($this->users[$id])) {
            $this->response->notFound('User not found')->send();
            return;
        }
        
        $this->response->success($this->users[$id])->send();
    }
    
    /**
     * Create user
     */
    public function store(): void
    {
        $rules = [
            'name' => 'required|min:2',
            'email' => 'required|email'
        ];
        
        $errors = $this->validate($rules);
        
        if (!empty($errors)) {
            $this->response->validationError($errors)->send();
            return;
        }
        
        $id = max(array_keys($this->users)) + 1;
        $user = [
            'id' => $id,
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->users[$id] = $user;
        
        $this->response->created($user, 'User created successfully')->send();
    }
    
    /**
     * Update user
     */
    public function update(array $params): void
    {
        $id = (int) $params['id'];
        
        if (!isset($this->users[$id])) {
            $this->response->notFound('User not found')->send();
            return;
        }
        
        $rules = [
            'name' => 'min:2',
            'email' => 'email'
        ];
        
        $errors = $this->validate($rules);
        
        if (!empty($errors)) {
            $this->response->validationError($errors)->send();
            return;
        }
        
        if (isset($this->request['name'])) {
            $this->users[$id]['name'] = $this->request['name'];
        }
        
        if (isset($this->request['email'])) {
            $this->users[$id]['email'] = $this->request['email'];
        }
        
        $this->users[$id]['updated_at'] = date('Y-m-d H:i:s');
        
        $this->response->success($this->users[$id], 'User updated successfully')->send();
    }
    
    /**
     * Delete user
     */
    public function delete(array $params): void
    {
        $id = (int) $params['id'];
        
        if (!isset($this->users[$id])) {
            $this->response->notFound('User not found')->send();
            return;
        }
        
        unset($this->users[$id]);
        
        $this->response->success([], 'User deleted successfully')->send();
    }
}

// RESTful API Examples
class RestfulApiExamples
{
    private RestfulRouter $router;
    private RestfulResponse $response;
    
    public function __construct()
    {
        $this->router = new RestfulRouter();
        $this->response = new RestfulResponse();
    }
    
    public function demonstrateRouting(): void
    {
        echo "RESTful API Routing Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test different routes
        $testRoutes = [
            ['method' => 'GET', 'uri' => '/users'],
            ['method' => 'GET', 'uri' => '/users/123'],
            ['method' => 'POST', 'uri' => '/users'],
            ['method' => 'PUT', 'uri' => '/users/123'],
            ['method' => 'DELETE', 'uri' => '/users/123'],
            ['method' => 'GET', 'uri' => '/posts/45/comments'],
            ['method' => 'POST', 'uri' => '/posts/45/comments']
        ];
        
        foreach ($testRoutes as $route) {
            echo "Testing: {$route['method']} {$route['uri']}\n";
            
            $result = $this->router->route($route['method'], $route['uri']);
            
            if ($result['handler']) {
                echo "  Handler: {$result['handler']}\n";
                if (!empty($result['params'])) {
                    echo "  Params: " . json_encode($result['params']) . "\n";
                }
                if (!empty($result['middleware'])) {
                    echo "  Middleware: " . implode(', ', $result['middleware']) . "\n";
                }
            } else {
                echo "  No route found\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateResponses(): void
    {
        echo "RESTful API Response Demo\n";
        echo str_repeat("-", 32) . "\n";
        
        // Success response
        echo "1. Success Response:\n";
        $success = $this->response->success(['id' => 1, 'name' => 'John'], 'User found');
        echo json_encode($success->build(), JSON_PRETTY_PRINT) . "\n\n";
        
        // Created response
        echo "2. Created Response:\n";
        $created = $this->response->created(['id' => 2, 'name' => 'Jane'], 'User created');
        echo json_encode($created->build(), JSON_PRETTY_PRINT) . "\n\n";
        
        // Error response
        echo "3. Error Response:\n";
        $error = $this->response->error('Invalid input', 400, ['name' => 'Name is required']);
        echo json_encode($error->build(), JSON_PRETTY_PRINT) . "\n\n";
        
        // Not found response
        echo "4. Not Found Response:\n";
        $notFound = $this->response->notFound('User not found');
        echo json_encode($notFound->build(), JSON_PRETTY_PRINT) . "\n\n";
        
        // Paginated response
        echo "5. Paginated Response:\n";
        $users = [['id' => 1, 'name' => 'John'], ['id' => 2, 'name' => 'Jane']];
        $pagination = ['total' => 50, 'page' => 1, 'limit' => 10, 'pages' => 5];
        $paginated = $this->response->paginated($users, $pagination);
        echo json_encode($paginated->build(), JSON_PRETTY_PRINT) . "\n";
    }
    
    public function demonstrateController(): void
    {
        echo "\nRESTful Controller Demo\n";
        echo str_repeat("-", 28) . "\n";
        
        $controller = new UserController();
        
        // Simulate different requests
        echo "1. GET /users (Index):\n";
        $_GET['page'] = 1;
        $_GET['limit'] = 2;
        $controller->index();
        echo "\n";
        
        echo "2. GET /users/1 (Show):\n";
        $controller->show(['id' => '1']);
        echo "\n";
        
        echo "3. POST /users (Store):\n";
        $_POST = ['name' => 'Alice', 'email' => 'alice@example.com'];
        $controller->store();
        echo "\n";
        
        echo "4. PUT /users/1 (Update):\n";
        $_POST = ['name' => 'John Updated'];
        $controller->update(['id' => '1']);
        echo "\n";
        
        echo "5. DELETE /users/999 (Delete - Not Found):\n";
        $controller->delete(['id' => '999']);
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nRESTful API Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. HTTP Methods:\n";
        echo "   • GET: Retrieve resources\n";
        echo "   • POST: Create new resources\n";
        echo "   • PUT/PATCH: Update resources\n";
        echo "   • DELETE: Remove resources\n\n";
        
        echo "2. Status Codes:\n";
        echo "   • 200: Success\n";
        echo "   • 201: Created\n";
        echo "   • 400: Bad Request\n";
        echo "   • 401: Unauthorized\n";
        echo "   • 403: Forbidden\n";
        echo "   • 404: Not Found\n";
        echo "   • 422: Validation Error\n";
        echo "   • 500: Server Error\n\n";
        
        echo "3. URL Design:\n";
        echo "   • Use nouns, not verbs\n";
        echo "   • Use plural nouns for collections\n";
        echo "   • Use hierarchical structure\n";
        echo "   • Keep URLs consistent\n\n";
        
        echo "4. Response Format:\n";
        echo "   • Use consistent JSON structure\n";
        echo "   • Include success/error indicators\n";
        echo "   • Provide meaningful messages\n";
        echo "   • Include metadata for pagination\n\n";
        
        echo "5. Security:\n";
        echo "   • Implement authentication\n";
        echo "   • Use HTTPS\n";
        echo "   • Validate all inputs\n";
        echo "   • Implement rate limiting\n";
        echo "   • Use proper CORS headers";
    }
    
    public function runAllExamples(): void
    {
        echo "RESTful API Development Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateRouting();
        $this->demonstrateResponses();
        $this->demonstrateController();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runRestfulApiDemo(): void
{
    $examples = new RestfulApiExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runRestfulApiDemo();
}
?>
