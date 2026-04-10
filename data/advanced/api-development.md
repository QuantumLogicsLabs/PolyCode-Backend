# PHP API Development

## RESTful API Design

### REST Principles and Implementation
```php
<?php
// API Router class
class ApiRouter {
    private array $routes = [];
    private array $middleware = [];
    private string $basePath = '';
    
    public function setBasePath(string $path): void {
        $this->basePath = rtrim($path, '/');
    }
    
    public function get(string $path, callable $handler, array $middleware = []): void {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    public function post(string $path, callable $handler, array $middleware = []): void {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    public function put(string $path, callable $handler, array $middleware = []): void {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }
    
    public function patch(string $path, callable $handler, array $middleware = []): void {
        $this->addRoute('PATCH', $path, $handler, $middleware);
    }
    
    public function delete(string $path, callable $handler, array $middleware = []): void {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }
    
    public function options(string $path, callable $handler, array $middleware = []): void {
        $this->addRoute('OPTIONS', $path, $handler, $middleware);
    }
    
    private function addRoute(string $method, string $path, callable $handler, array $middleware): void {
        $pattern = $this->convertPathToRegex($path);
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => array_merge($this->middleware, $middleware)
        ];
    }
    
    private function convertPathToRegex(string $path): string {
        // Convert path parameters like {id} to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $this->basePath . $pattern . '$#';
    }
    
    public function addMiddleware(callable $middleware): void {
        $this->middleware[] = $middleware;
    }
    
    public function dispatch(Request $request): Response {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches); // Remove full match
                $request->setRouteParams($matches);
                
                return $this->executeHandler($route['handler'], $route['middleware'], $request);
            }
        }
        
        return new Response(404, ['Content-Type' => 'application/json'], json_encode(['error' => 'Not found']));
    }
    
    private function executeHandler(callable $handler, array $middleware, Request $request): Response {
        $next = function($request) use ($handler) {
            return $handler($request);
        };
        
        // Execute middleware in reverse order
        for ($i = count($middleware) - 1; $i >= 0; $i--) {
            $next = function($request) use ($middleware, $next, $i) {
                return $middleware[$i]($request, $next);
            };
        }
        
        return $next($request);
    }
}

// Request class
class Request {
    private string $method;
    private string $path;
    private array $headers;
    private array $queryParams;
    private array $body;
    private array $routeParams;
    
    public function __construct(string $method, string $path, array $headers = [], array $queryParams = [], array $body = []) {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->headers = $headers;
        $this->queryParams = $queryParams;
        $this->body = $body;
        $this->routeParams = [];
    }
    
    public function getMethod(): string {
        return $this->method;
    }
    
    public function getPath(): string {
        return $this->path;
    }
    
    public function getHeader(string $name): ?string {
        return $this->headers[strtolower($name)] ?? null;
    }
    
    public function getHeaders(): array {
        return $this->headers;
    }
    
    public function getQueryParam(string $name, $default = null) {
        return $this->queryParams[$name] ?? $default;
    }
    
    public function getQueryParams(): array {
        return $this->queryParams;
    }
    
    public function getBody(): array {
        return $this->body;
    }
    
    public function getJson(): array {
        return $this->body;
    }
    
    public function getRouteParam(string $name, $default = null) {
        return $this->routeParams[$name] ?? $default;
    }
    
    public function getRouteParams(): array {
        return $this->routeParams;
    }
    
    public function setRouteParams(array $params): void {
        $this->routeParams = $params;
    }
    
    public static function fromGlobals(): self {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = $path ?: '/';
        
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[strtolower($headerName)] = $value;
            }
        }
        
        $queryParams = $_GET;
        
        $body = [];
        $contentType = $headers['content-type'] ?? '';
        
        if ($method !== 'GET' && $method !== 'HEAD') {
            if (strpos($contentType, 'application/json') !== false) {
                $input = file_get_contents('php://input');
                $body = json_decode($input, true) ?: [];
            } elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                $body = $_POST;
            }
        }
        
        return new self($method, $path, $headers, $queryParams, $body);
    }
}

// Response class
class Response {
    private int $statusCode;
    private array $headers;
    private string $body;
    
    public function __construct(int $statusCode = 200, array $headers = [], string $body = '') {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }
    
    public function getStatusCode(): int {
        return $this->statusCode;
    }
    
    public function getHeaders(): array {
        return $this->headers;
    }
    
    public function getBody(): string {
        return $this->body;
    }
    
    public function send(): void {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        echo $this->body;
    }
    
    public static function json(array $data, int $statusCode = 200): self {
        return new self($statusCode, ['Content-Type' => 'application/json'], json_encode($data));
    }
    
    public static function error(string $message, int $statusCode = 400, array $details = []): self {
        $error = [
            'error' => true,
            'message' => $message,
            'status' => $statusCode
        ];
        
        if (!empty($details)) {
            $error['details'] = $details;
        }
        
        return new self($statusCode, ['Content-Type' => 'application/json'], json_encode($error));
    }
    
    public static function redirect(string $url, int $statusCode = 302): self {
        return new self($statusCode, ['Location' => $url], '');
    }
}

// Resource controller base class
abstract class ResourceController {
    protected Request $request;
    
    public function setRequest(Request $request): void {
        $this->request = $request;
    }
    
    protected function validate(array $rules, array $data): array {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (!$this->validateField($value, $rule)) {
                $errors[$field] = "Field $field is invalid";
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return $data;
    }
    
    private function validateField($value, string $rule): bool {
        $rules = explode('|', $rule);
        
        foreach ($rules as $singleRule) {
            if (!$this->applyRule($value, $singleRule)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function applyRule($value, string $rule): bool {
        if ($rule === 'required') {
            return !empty($value);
        }
        
        if ($rule === 'email' && !empty($value)) {
            return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        if (strpos($rule, 'min:') === 0 && !empty($value)) {
            $min = substr($rule, 4);
            return strlen($value) >= (int)$min;
        }
        
        if (strpos($rule, 'max:') === 0 && !empty($value)) {
            $max = substr($rule, 4);
            return strlen($value) <= (int)$max;
        }
        
        return true;
    }
    
    protected function paginate(callable $callback, int $defaultLimit = 20): array {
        $page = max(1, (int)($this->request->getQueryParam('page', 1)));
        $limit = min(100, max(1, (int)($this->request->getQueryParam('limit', $defaultLimit))));
        $offset = ($page - 1) * $limit;
        
        $result = $callback($limit, $offset);
        
        return [
            'data' => $result['data'] ?? [],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $result['total'] ?? 0,
                'pages' => ceil(($result['total'] ?? 0) / $limit)
            ]
        ];
    }
}

// User resource controller
class UserController extends ResourceController {
    private UserService $userService;
    
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    
    public function index(): Response {
        $result = $this->paginate(function($limit, $offset) {
            return $this->userService->findAll($limit, $offset);
        });
        
        return Response::json($result);
    }
    
    public function show(): Response {
        $id = $this->request->getRouteParam('id');
        
        $user = $this->userService->findById($id);
        
        if (!$user) {
            return Response::error('User not found', 404);
        }
        
        return Response::json($user);
    }
    
    public function store(): Response {
        $data = $this->request->getJson();
        
        try {
            $this->validate([
                'name' => 'required|min:2',
                'email' => 'required|email'
            ], $data);
            
            $user = $this->userService->create($data);
            
            return Response::json($user, 201);
            
        } catch (ValidationException $e) {
            return Response::error('Validation failed', 400, $e->getErrors());
        }
    }
    
    public function update(): Response {
        $id = $this->request->getRouteParam('id');
        $data = $this->request->getJson();
        
        try {
            $this->validate([
                'name' => 'min:2',
                'email' => 'email'
            ], $data);
            
            $user = $this->userService->update($id, $data);
            
            if (!$user) {
                return Response::error('User not found', 404);
            }
            
            return Response::json($user);
            
        } catch (ValidationException $e) {
            return Response::error('Validation failed', 400, $e->getErrors());
        }
    }
    
    public function destroy(): Response {
        $id = $this->request->getRouteParam('id');
        
        $success = $this->userService->delete($id);
        
        if (!$success) {
            return Response::error('User not found', 404);
        }
        
        return new Response(204);
    }
}

// API Middleware
class ApiMiddleware {
    public static function cors(callable $next): Response {
        return $next();
    }
    
    public static function auth(callable $next): Response {
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (empty($token) || !self::validateToken($token)) {
            return Response::error('Unauthorized', 401);
        }
        
        return $next();
    }
    
    public static function rateLimit(callable $next): Response {
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = "rate_limit:$clientIp";
        
        $current = apcu_fetch($key) ?: 0;
        
        if ($current >= 100) { // 100 requests per hour
            return Response::error('Rate limit exceeded', 429);
        }
        
        apcu_store($key, $current + 1, 3600);
        
        return $next();
    }
    
    public static function logging(callable $next): Response {
        $start = microtime(true);
        
        $response = $next();
        
        $duration = microtime(true) - $start;
        
        error_log(sprintf(
            "[%s] %s %s - %d - %.3fs",
            date('Y-m-d H:i:s'),
            $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            $_SERVER['REQUEST_URI'] ?? '/',
            $response->getStatusCode(),
            $duration
        ));
        
        return $response;
    }
    
    private static function validateToken(string $token): bool {
        // Simplified token validation
        return strlen($token) > 20;
    }
}

// API Application
class ApiApplication {
    private ApiRouter $router;
    private array $middleware = [];
    
    public function __construct() {
        $this->router = new ApiRouter();
        $this->setupDefaultMiddleware();
    }
    
    private function setupDefaultMiddleware(): void {
        $this->middleware = [
            [ApiMiddleware::class, 'logging'],
            [ApiMiddleware::class, 'cors'],
            [ApiMiddleware::class, 'rateLimit']
        ];
    }
    
    public function addRoute(string $method, string $path, callable $handler, array $middleware = []): void {
        $this->router->{$method}($path, $handler, array_merge($this->middleware, $middleware));
    }
    
    public function resource(string $name, ResourceController $controller): void {
        $this->router->get("/$name", [$controller, 'index']);
        $this->router->get("/$name/{id}", [$controller, 'show']);
        $this->router->post("/$name", [$controller, 'store']);
        $this->router->put("/$name/{id}", [$controller, 'update']);
        $this->router->patch("/$name/{id}", [$controller, 'update']);
        $this->router->delete("/$name/{id}", [$controller, 'destroy']);
    }
    
    public function run(): void {
        $request = Request::fromGlobals();
        $response = $this->router->dispatch($request);
        $response->send();
    }
}

// Usage example
$app = new ApiApplication();

// Add user resource
$userService = new UserService();
$userController = new UserController($userService);
$app->resource('users', $userController);

// Add custom routes with authentication
$app->addRoute('get', '/profile', function($request) use ($userService) {
    $userId = $request->getRouteParam('id');
    $user = $userService->findById($userId);
    return Response::json($user);
}, [[ApiMiddleware::class, 'auth']]);

$app->run();
?>
```

## API Documentation

### OpenAPI/Swagger Integration
```php
<?php
// OpenAPI documentation generator
class OpenApiGenerator {
    private array $spec = [
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'API Documentation',
            'version' => '1.0.0',
            'description' => 'API documentation generated automatically'
        ],
        'servers' => [
            ['url' => 'http://localhost:8000/api', 'description' => 'Development server']
        ],
        'paths' => [],
        'components' => [
            'schemas' => [],
            'responses' => [],
            'securitySchemes' => []
        ]
    ];
    
    public function setTitle(string $title): self {
        $this->spec['info']['title'] = $title;
        return $this;
    }
    
    public function setVersion(string $version): self {
        $this->spec['info']['version'] = $version;
        return $this;
    }
    
    public function setDescription(string $description): self {
        $this->spec['info']['description'] = $description;
        return $this;
    }
    
    public function addServer(string $url, string $description = ''): self {
        $server = ['url' => $url];
        
        if (!empty($description)) {
            $server['description'] = $description;
        }
        
        $this->spec['servers'][] = $server;
        return $this;
    }
    
    public function addPath(string $path, array $operations): self {
        $this->spec['paths'][$path] = $operations;
        return $this;
    }
    
    public function addGet(string $path, array $operation): self {
        $this->spec['paths'][$path]['get'] = $operation;
        return $this;
    }
    
    public function addPost(string $path, array $operation): self {
        $this->spec['paths'][$path]['post'] = $operation;
        return $this;
    }
    
    public function addPut(string $path, array $operation): self {
        $this->spec['paths'][$path]['put'] = $operation;
        return $this;
    }
    
    public function addDelete(string $path, array $operation): self {
        $this->spec['paths'][$path]['delete'] = $operation;
        return $this;
    }
    
    public function addSchema(string $name, array $schema): self {
        $this->spec['components']['schemas'][$name] = $schema;
        return $this;
    }
    
    public function addResponse(string $name, array $response): self {
        $this->spec['components']['responses'][$name] = $response;
        return $this;
    }
    
    public function addSecurityScheme(string $name, array $scheme): self {
        $this->spec['components']['securitySchemes'][$name] = $scheme;
        return $this;
    }
    
    public function generate(): array {
        return $this->spec;
    }
    
    public function toJson(): string {
        return json_encode($this->spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    public function toYaml(): string {
        return yaml_emit($this->spec);
    }
}

// API documentation decorator
class ApiDocumentation {
    private OpenApiGenerator $generator;
    private array $controllers = [];
    
    public function __construct(OpenApiGenerator $generator = null) {
        $this->generator = $generator ?: new OpenApiGenerator();
    }
    
    public function addController(string $name, ResourceController $controller): self {
        $this->controllers[$name] = $controller;
        return $this;
    }
    
    public function generateFromControllers(): self {
        foreach ($this->controllers as $name => $controller) {
            $this->generateControllerDocumentation($name, $controller);
        }
        
        return $this;
    }
    
    private function generateControllerDocumentation(string $name, ResourceController $controller): void {
        $reflection = new ReflectionClass($controller);
        $resourceName = strtolower($name);
        
        // Generate schema for the resource
        $this->generator->addSchema(ucfirst($resourceName), [
            'type' => 'object',
            'properties' => $this->generateSchemaProperties($controller),
            'required' => ['id']
        ]);
        
        // Generate paths
        $this->generator->addGet("/$resourceName", [
            'summary' => "List $name resources",
            'description' => "Retrieve a paginated list of $name resources",
            'responses' => [
                '200' => [
                    'description' => 'Successful response',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'data' => [
                                        'type' => 'array',
                                        'items' => [
                                            '$ref' => "#/components/schemas/" . ucfirst($resourceName)
                                        ]
                                    ],
                                    'pagination' => [
                                        '$ref' => '#/components/schemas/Pagination'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        
        $this->generator->addGet("/$resourceName/{id}", [
            'summary' => "Get $name resource",
            'description' => "Retrieve a specific $name resource by ID",
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => 'integer'],
                    'description' => "$name resource ID"
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Successful response',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/" . ucfirst($resourceName)
                            ]
                        ]
                    ]
                ],
                '404' => [
                    '$ref' => '#/components/responses/NotFound'
                ]
            ]
        ]);
        
        $this->generator->addPost("/$resourceName", [
            'summary' => "Create $name resource",
            'description' => "Create a new $name resource",
            'requestBody' => [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => "#/components/schemas/" . ucfirst($resourceName) . "Create"
                        ]
                    ]
                ]
            ],
            'responses' => [
                '201' => [
                    'description' => 'Resource created successfully',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/" . ucfirst($resourceName)
                            ]
                        ]
                    ]
                ],
                '400' => [
                    '$ref' => '#/components/responses/ValidationError'
                ]
            ]
        ]);
        
        $this->generator->addPut("/$resourceName/{id}", [
            'summary' => "Update $name resource",
            'description' => "Update a specific $name resource",
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => 'integer'],
                    'description' => "$name resource ID"
                ]
            ],
            'requestBody' => [
                'required' => true,
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => "#/components/schemas/" . ucfirst($resourceName) . "Update"
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => 'Resource updated successfully',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/" . ucfirst($resourceName)
                            ]
                        ]
                    ]
                ],
                '404' => [
                    '$ref' => '#/components/responses/NotFound'
                ],
                '400' => [
                    '$ref' => '#/components/responses/ValidationError'
                ]
            ]
        ]);
        
        $this->generator->addDelete("/$resourceName/{id}", [
            'summary' => "Delete $name resource",
            'description' => "Delete a specific $name resource",
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => ['type' => 'integer'],
                    'description' => "$name resource ID"
                ]
            ],
            'responses' => [
                '204' => [
                    'description' => 'Resource deleted successfully'
                ],
                '404' => [
                    '$ref' => '#/components/responses/NotFound'
                ]
            ]
        ]);
    }
    
    private function generateSchemaProperties(ResourceController $controller): array {
        // This would analyze the controller to determine properties
        // For now, return a basic structure
        return [
            'id' => [
                'type' => 'integer',
                'description' => 'Resource ID',
                'example' => 1
            ],
            'name' => [
                'type' => 'string',
                'description' => 'Resource name',
                'example' => 'Example Resource'
            ],
            'created_at' => [
                'type' => 'string',
                'format' => 'date-time',
                'description' => 'Creation timestamp'
            ],
            'updated_at' => [
                'type' => 'string',
                'format' => 'date-time',
                'description' => 'Last update timestamp'
            ]
        ];
    }
    
    public function addCommonSchemas(): self {
        $this->generator->addSchema('Pagination', [
            'type' => 'object',
            'properties' => [
                'page' => [
                    'type' => 'integer',
                    'description' => 'Current page number',
                    'example' => 1
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Number of items per page',
                    'example' => 20
                ],
                'total' => [
                    'type' => 'integer',
                    'description' => 'Total number of items',
                    'example' => 100
                ],
                'pages' => [
                    'type' => 'integer',
                    'description' => 'Total number of pages',
                    'example' => 5
                ]
            ]
        ]);
        
        $this->generator->addSchema('Error', [
            'type' => 'object',
            'properties' => [
                'error' => [
                    'type' => 'boolean',
                    'description' => 'Error flag',
                    'example' => true
                ],
                'message' => [
                    'type' => 'string',
                    'description' => 'Error message',
                    'example' => 'An error occurred'
                ],
                'status' => [
                    'type' => 'integer',
                    'description' => 'HTTP status code',
                    'example' => 400
                ]
            ]
        ]);
        
        $this->generator->addResponse('NotFound', [
            'description' => 'Resource not found',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/Error'
                    ]
                ]
            ]
        ]);
        
        $this->generator->addResponse('ValidationError', [
            'description' => 'Validation error',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'error' => ['type' => 'boolean'],
                            'message' => ['type' => 'string'],
                            'status' => ['type' => 'integer'],
                            'details' => [
                                'type' => 'object',
                                'description' => 'Validation error details'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        
        return $this;
    }
    
    public function addSecurityScheme(string $name, string $type, array $options = []): self {
        $scheme = ['type' => $type] + $options;
        $this->generator->addSecurityScheme($name, $scheme);
        return $this;
    }
    
    public function getGenerator(): OpenApiGenerator {
        return $this->generator;
    }
}

// API documentation endpoint
class DocumentationController {
    private OpenApiGenerator $generator;
    
    public function __construct(OpenApiGenerator $generator) {
        $this->generator = $generator;
    }
    
    public function openApi(): Response {
        return Response::json($this->generator->generate());
    }
    
    public function openApiJson(): Response {
        return new Response(200, ['Content-Type' => 'application/json'], $this->generator->toJson());
    }
    
    public function openApiYaml(): Response {
        return new Response(200, ['Content-Type' => 'application/x-yaml'], $this->generator->toYaml());
    }
    
    public function swaggerUi(): Response {
        $html = $this->generateSwaggerUiHtml();
        return new Response(200, ['Content-Type' => 'text/html'], $html);
    }
    
    private function generateSwaggerUiHtml(): string {
        $specUrl = '/api/docs/openapi.json';
        
        return '<!DOCTYPE html>
<html>
<head>
    <title>API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3.52.5/swagger-ui.css" />
    <style>
        html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin:0; background: #fafafa; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@3.52.5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@3.52.5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "' . $specUrl . '",
                dom_id: "#swagger-ui",
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout"
            });
        };
    </script>
</body>
</html>';
    }
}

// Annotation-based documentation
#[Attribute(Attribute::TARGET_CLASS)]
class ApiResource {
    public function __construct(public string $name) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class ApiOperation {
    public function __construct(
        public string $summary,
        public string $description = '',
        public array $tags = []
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class ApiParameter {
    public function __construct(
        public string $name,
        public string $in,
        public bool $required = false,
        public string $type = 'string',
        public string $description = ''
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class ApiResponse {
    public function __construct(
        public int $code,
        public string $description,
        public string $schema = null
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class ApiRequestBody {
    public function __construct(
        public string $description = '',
        public bool $required = true,
        public string $schema = null
    ) {}
}

// Example annotated controller
#[ApiResource('users')]
class AnnotatedUserController extends ResourceController {
    #[ApiOperation('List users', 'Retrieve a paginated list of users', ['users'])]
    #[ApiResponse(200, 'Successful response')]
    public function index(): Response {
        // Implementation
    }
    
    #[ApiOperation('Get user', 'Retrieve a specific user by ID', ['users'])]
    #[ApiParameter('id', 'path', true, 'integer', 'User ID')]
    #[ApiResponse(200, 'Successful response')]
    #[ApiResponse(404, 'User not found')]
    public function show(): Response {
        // Implementation
    }
    
    #[ApiOperation('Create user', 'Create a new user', ['users'])]
    #[ApiRequestBody('User data', true, 'UserCreate')]
    #[ApiResponse(201, 'User created successfully')]
    #[ApiResponse(400, 'Validation error')]
    public function store(): Response {
        // Implementation
    }
}

// Annotation parser
class AnnotationParser {
    public function parseController(object $controller): array {
        $reflection = new ReflectionClass($controller);
        $resourceAttribute = $reflection->getAttributes(ApiResource::class)[0] ?? null;
        
        if (!$resourceAttribute) {
            return [];
        }
        
        $resourceName = $resourceAttribute->name;
        $paths = [];
        
        foreach ($reflection->getMethods() as $method) {
            $operation = $method->getAttributes(ApiOperation::class)[0] ?? null;
            
            if (!$operation) {
                continue;
            }
            
            $pathData = $this->parseMethod($method, $resourceName, $operation);
            $paths[$pathData['path']][$pathData['method']] = $pathData['operation'];
        }
        
        return ['paths' => $paths];
    }
    
    private function parseMethod(ReflectionMethod $method, string $resourceName, ApiOperation $operation): array {
        $methodName = $method->getName();
        $httpMethod = $this->mapMethodToHttp($methodName);
        $path = $this->mapMethodToPath($resourceName, $methodName);
        
        $operationData = [
            'summary' => $operation->summary,
            'description' => $operation->description,
            'tags' => $operation->tags,
            'parameters' => [],
            'responses' => []
        ];
        
        // Parse parameters
        foreach ($method->getAttributes(ApiParameter::class) as $param) {
            $operationData['parameters'][] = [
                'name' => $param->name,
                'in' => $param->in,
                'required' => $param->required,
                'schema' => ['type' => $param->type],
                'description' => $param->description
            ];
        }
        
        // Parse request body
        $requestBody = $method->getAttributes(ApiRequestBody::class)[0] ?? null;
        if ($requestBody) {
            $operationData['requestBody'] = [
                'description' => $requestBody->description,
                'required' => $requestBody->required,
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => "#/components/schemas/{$requestBody->schema}"]
                    ]
                ]
            ];
        }
        
        // Parse responses
        foreach ($method->getAttributes(ApiResponse::class) as $response) {
            $responseData = [
                'description' => $response->description
            ];
            
            if ($response->schema) {
                $responseData['content'] = [
                    'application/json' => [
                        'schema' => ['$ref' => "#/components/schemas/{$response->schema}"]
                    ]
                ];
            }
            
            $operationData['responses'][$response->code] = $responseData;
        }
        
        return [
            'path' => $path,
            'method' => $httpMethod,
            'operation' => $operationData
        ];
    }
    
    private function mapMethodToHttp(string $methodName): string {
        $mapping = [
            'index' => 'get',
            'show' => 'get',
            'store' => 'post',
            'update' => 'put',
            'destroy' => 'delete'
        ];
        
        return $mapping[$methodName] ?? strtolower($methodName);
    }
    
    private function mapMethodToPath(string $resourceName, string $methodName): string {
        switch ($methodName) {
            case 'index':
                return "/$resourceName";
            case 'show':
            case 'update':
            case 'destroy':
                return "/$resourceName/{id}";
            case 'store':
                return "/$resourceName";
            default:
                return "/$resourceName/$methodName";
        }
    }
}

// Usage example
$generator = new OpenApiGenerator();
$generator
    ->setTitle('My API')
    ->setVersion('1.0.0')
    ->setDescription('A comprehensive API example')
    ->addServer('https://api.example.com/v1', 'Production')
    ->addServer('http://localhost:8000/api', 'Development')
    ->addCommonSchemas()
    ->addSecurityScheme('bearerAuth', 'http', [
        'scheme' => 'bearer',
        'bearerFormat' => 'JWT'
    ]);

// Add controller documentation
$doc = new ApiDocumentation($generator);
$doc
    ->addController('users', new UserController(new UserService()))
    ->generateFromControllers();

// Add custom routes
$generator->addPost('/auth/login', [
    'summary' => 'User login',
    'description' => 'Authenticate user and return JWT token',
    'requestBody' => [
        'required' => true,
        'content' => [
            'application/json' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'email' => ['type' => 'string', 'format' => 'email'],
                        'password' => ['type' => 'string', 'minLength' => 8]
                    ],
                    'required' => ['email', 'password']
                ]
            ]
        ]
    ],
    'responses' => [
        '200' => [
            'description' => 'Login successful',
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'token' => ['type' => 'string'],
                            'user' => ['$ref' => '#/components/schemas/User']
                        ]
                    ]
                ]
            ]
        ],
        '401' => ['$ref' => '#/components/responses/Unauthorized']
    ]
]);

// Generate documentation
$spec = $generator->generate();
echo $generator->toJson();
?>
```

## API Versioning

### Version Management Strategies
```php
<?php
// API Version Manager
class ApiVersionManager {
    private array $versions = [];
    private string $defaultVersion = 'v1';
    private string $versionHeader = 'API-Version';
    private string $versionParam = 'version';
    
    public function addVersion(string $version, callable $handler): void {
        $this->versions[$version] = $handler;
    }
    
    public function setDefaultVersion(string $version): void {
        $this->defaultVersion = $version;
    }
    
    public function setVersionHeader(string $header): void {
        $this->versionHeader = $header;
    }
    
    public function setVersionParam(string $param): void {
        $this->versionParam = $param;
    }
    
    public function getVersion(Request $request): string {
        // Try header first
        $version = $request->getHeader($this->versionHeader);
        
        // Try query parameter
        if (!$version) {
            $version = $request->getQueryParam($this->versionParam);
        }
        
        // Try URL path
        if (!$version) {
            $path = $request->getPath();
            if (preg_match('/^\/(v\d+)\//', $path, $matches)) {
                $version = $matches[1];
            }
        }
        
        return $version ?: $this->defaultVersion;
    }
    
    public function handle(Request $request): Response {
        $version = $this->getVersion($request);
        
        if (!isset($this->versions[$version])) {
            return Response::error("API version '$version' is not supported", 400);
        }
        
        $handler = $this->versions[$version];
        return $handler($request);
    }
    
    public function getSupportedVersions(): array {
        return array_keys($this->versions);
    }
}

// Versioned Router
class VersionedRouter {
    private array $routers = [];
    private ApiVersionManager $versionManager;
    
    public function __construct(ApiVersionManager $versionManager) {
        $this->versionManager = $versionManager;
    }
    
    public function addRouter(string $version, ApiRouter $router): void {
        $this->routers[$version] = $router;
        $this->versionManager->addVersion($version, function(Request $request) use ($router) {
            return $router->dispatch($request);
        });
    }
    
    public function dispatch(Request $request): Response {
        return $this->versionManager->handle($request);
    }
    
    public function getRouter(string $version): ?ApiRouter {
        return $this->routers[$version] ?? null;
    }
}

// Base API Controller
abstract class BaseApiController {
    protected string $version;
    
    public function setVersion(string $version): void {
        $this->version = $version;
    }
    
    protected function transformData(array $data): array {
        // Transform data based on version
        switch ($this->version) {
            case 'v1':
                return $this->transformToV1($data);
            case 'v2':
                return $this->transformToV2($data);
            default:
                return $data;
        }
    }
    
    protected function transformToV1(array $data): array {
        // V1 transformation logic
        return array_map(function($item) {
            if (isset($item['created_at'])) {
                $item['created'] = $item['created_at'];
                unset($item['created_at']);
            }
            return $item;
        }, $data);
    }
    
    protected function transformToV2(array $data): array {
        // V2 transformation logic
        return array_map(function($item) {
            if (isset($item['created_at'])) {
                $item['metadata']['created_at'] = $item['created_at'];
                unset($item['created_at']);
            }
            return $item;
        }, $data);
    }
}

// Versioned User Controller
class VersionedUserController extends BaseApiController {
    private UserService $userService;
    
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    
    public function index(): Response {
        $users = $this->userService->findAll();
        $transformedUsers = $this->transformData($users);
        
        return Response::json($transformedUsers);
    }
    
    public function show(): Response {
        $id = $this->request->getRouteParam('id');
        $user = $this->userService->findById($id);
        
        if (!$user) {
            return Response::error('User not found', 404);
        }
        
        $transformedUser = $this->transformData([$user])[0];
        
        return Response::json($transformedUser);
    }
    
    public function store(): Response {
        $data = $this->request->getJson();
        
        // Validate based on version
        $this->validateForVersion($data);
        
        $user = $this->userService->create($data);
        $transformedUser = $this->transformData([$user])[0];
        
        return Response::json($transformedUser, 201);
    }
    
    private function validateForVersion(array $data): void {
        switch ($this->version) {
            case 'v1':
                $this->validateV1($data);
                break;
            case 'v2':
                $this->validateV2($data);
                break;
        }
    }
    
    private function validateV1(array $data): void {
        $required = ['name', 'email'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new ValidationException([$field => "Field $field is required"]);
            }
        }
    }
    
    private function validateV2(array $data): void {
        $required = ['name', 'email', 'password'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new ValidationException([$field => "Field $field is required"]);
            }
        }
        
        // Additional V2 validations
        if (strlen($data['password']) < 8) {
            throw new ValidationException(['password' => 'Password must be at least 8 characters']);
        }
    }
}

// API Version Middleware
class ApiVersionMiddleware {
    private ApiVersionManager $versionManager;
    
    public function __construct(ApiVersionManager $versionManager) {
        $this->versionManager = $versionManager;
    }
    
    public function __invoke(Request $request, callable $next): Response {
        $version = $this->versionManager->getVersion($request);
        
        // Add version to response headers
        $response = $next($request);
        
        $headers = $response->getHeaders();
        $headers['API-Version'] = $version;
        $headers['API-Versions'] = implode(', ', $this->versionManager->getSupportedVersions());
        
        return new Response($response->getStatusCode(), $headers, $response->getBody());
    }
}

// Version Compatibility Checker
class VersionCompatibilityChecker {
    private array $compatibilityMatrix = [];
    
    public function addCompatibility(string $fromVersion, string $toVersion, bool $compatible): void {
        $this->compatibilityMatrix[$fromVersion][$toVersion] = $compatible;
    }
    
    public function isCompatible(string $fromVersion, string $toVersion): bool {
        return $this->compatibilityMatrix[$fromVersion][$toVersion] ?? false;
    }
    
    public function getCompatibleVersions(string $version): array {
        return array_keys(array_filter($this->compatibilityMatrix[$version] ?? []));
    }
    
    public function getBreakingChanges(string $fromVersion, string $toVersion): array {
        // This would analyze the differences between versions
        // For now, return a placeholder
        return [
            'field_removed' => 'old_field',
            'field_type_changed' => 'field_name',
            'endpoint_removed' => '/deprecated-endpoint'
        ];
    }
}

// Version Migration Helper
class VersionMigration {
    private string $fromVersion;
    private string $toVersion;
    private array $migrations = [];
    
    public function __construct(string $fromVersion, string $toVersion) {
        $this->fromVersion = $fromVersion;
        $this->toVersion = $toVersion;
    }
    
    public function addFieldMigration(string $field, callable $transformer): void {
        $this->migrations['fields'][$field] = $transformer;
    }
    
    public function addEndpointMigration(string $endpoint, callable $transformer): void {
        $this->migrations['endpoints'][$endpoint] = $transformer;
    }
    
    public function migrateData(array $data): array {
        $migratedData = $data;
        
        // Apply field migrations
        foreach ($this->migrations['fields'] ?? [] as $field => $transformer) {
            if (isset($migratedData[$field])) {
                $migratedData[$field] = $transformer($migratedData[$field]);
            }
        }
        
        return $migratedData;
    }
    
    public function migrateResponse(array $response): array {
        $migratedResponse = $response;
        
        // Apply endpoint-specific migrations
        foreach ($this->migrations['endpoints'] ?? [] as $endpoint => $transformer) {
            if (isset($migratedResponse[$endpoint])) {
                $migratedResponse[$endpoint] = $transformer($migratedResponse[$endpoint]);
            }
        }
        
        return $migratedResponse;
    }
}

// Version Deprecation Manager
class VersionDeprecationManager {
    private array $deprecatedVersions = [];
    private array $deprecationWarnings = [];
    
    public function deprecateVersion(string $version, string $message, string $removalDate): void {
        $this->deprecatedVersions[$version] = [
            'message' => $message,
            'removal_date' => $removalDate,
            'deprecated_at' => date('Y-m-d')
        ];
    }
    
    public function isDeprecated(string $version): bool {
        return isset($this->deprecatedVersions[$version]);
    }
    
    public function getDeprecationInfo(string $version): ?array {
        return $this->deprecatedVersions[$version] ?? null;
    }
    
    public function addWarning(string $version, string $warning): void {
        $this->deprecationWarnings[$version][] = $warning;
    }
    
    public function getWarnings(string $version): array {
        return $this->deprecationWarnings[$version] ?? [];
    }
}

// Versioned API Application
class VersionedApiApplication {
    private VersionedRouter $router;
    private ApiVersionManager $versionManager;
    private VersionDeprecationManager $deprecationManager;
    
    public function __construct() {
        $this->versionManager = new ApiVersionManager();
        $this->router = new VersionedRouter($this->versionManager);
        $this->deprecationManager = new VersionDeprecationManager();
        
        $this->setupVersioning();
    }
    
    private function setupVersioning(): void {
        // Set up version detection
        $this->versionManager->setVersionHeader('API-Version');
        $this->versionManager->setVersionParam('v');
        $this->versionManager->setDefaultVersion('v1');
        
        // Set up version routers
        $v1Router = new ApiRouter();
        $v2Router = new ApiRouter();
        
        // V1 routes
        $v1Router->get('/users', function($request) {
            $controller = new VersionedUserController(new UserService());
            $controller->setVersion('v1');
            return $controller->index();
        });
        
        // V2 routes
        $v2Router->get('/users', function($request) {
            $controller = new VersionedUserController(new UserService());
            $controller->setVersion('v2');
            return $controller->index();
        });
        
        $v2Router->get('/users/{id}/profile', function($request) {
            // New endpoint in V2
            $id = $request->getRouteParam('id');
            return Response::json(['id' => $id, 'profile' => 'User profile data']);
        });
        
        $this->router->addRouter('v1', $v1Router);
        $this->router->addRouter('v2', $v2Router);
        
        // Add version handlers
        $this->versionManager->addVersion('v1', function($request) use ($v1Router) {
            return $v1Router->dispatch($request);
        });
        
        $this->versionManager->addVersion('v2', function($request) use ($v2Router) {
            return $v2Router->dispatch($request);
        });
        
        // Set up deprecation
        $this->deprecationManager->deprecateVersion('v1', 
            'Version 1 is deprecated. Please migrate to version 2.', 
            '2024-12-31'
        );
    }
    
    public function run(): void {
        $request = Request::fromGlobals();
        
        // Check for deprecation warnings
        $version = $this->versionManager->getVersion($request);
        
        if ($this->deprecationManager->isDeprecated($version)) {
            $deprecationInfo = $this->deprecationManager->getDeprecationInfo($version);
            $warnings = $this->deprecationManager->getWarnings($version);
            
            // Add deprecation headers
            header('X-API-Deprecated: true');
            header('X-API-Deprecation-Message: ' . $deprecationInfo['message']);
            header('X-API-Removal-Date: ' . $deprecationInfo['removal_date']);
            
            if (!empty($warnings)) {
                header('X-API-Warnings: ' . implode('; ', $warnings));
            }
        }
        
        // Add version middleware
        $middleware = new ApiVersionMiddleware($this->versionManager);
        $response = $middleware($request, function($request) {
            return $this->router->dispatch($request);
        });
        
        $response->send();
    }
    
    public function getVersionManager(): ApiVersionManager {
        return $this->versionManager;
    }
    
    public function getDeprecationManager(): VersionDeprecationManager {
        return $this->deprecationManager;
    }
}

// Usage example
$app = new VersionedApiApplication();

// Add custom version migration
$migration = new VersionMigration('v1', 'v2');
$migration->addFieldMigration('created_at', function($value) {
    return [
        'timestamp' => strtotime($value),
        'formatted' => date('c', strtotime($value))
    ];
});

$app->run();
?>
```

## API Security

### Authentication and Authorization
```php
<?php
// JWT Authentication
class JwtAuthenticator {
    private string $secretKey;
    private string $algorithm;
    private int $expirationTime;
    
    public function __construct(string $secretKey, string $algorithm = 'HS256', int $expirationTime = 3600) {
        $this->secretKey = $secretKey;
        $this->algorithm = $algorithm;
        $this->expirationTime = $expirationTime;
    }
    
    public function generateToken(array $payload): string {
        $header = $this->base64UrlEncode(json_encode([
            'typ' => 'JWT',
            'alg' => $this->algorithm
        ]));
        
        $payload['exp'] = time() + $this->expirationTime;
        $payload['iat'] = time();
        
        $payload = $this->base64UrlEncode(json_encode($payload));
        
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $this->secretKey, true)
        );
        
        return "$header.$payload.$signature";
    }
    
    public function validateToken(string $token): ?array {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Verify signature
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $this->secretKey, true)
        );
        
        if (!hash_equals($signature, $expectedSignature)) {
            return null;
        }
        
        // Decode payload
        $payloadData = json_decode(base64_decode($payload), true);
        
        if (!$payloadData) {
            return null;
        }
        
        // Check expiration
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return null;
        }
        
        return $payloadData;
    }
    
    public function refreshToken(string $token): ?string {
        $payload = $this->validateToken($token);
        
        if (!$payload) {
            return null;
        }
        
        // Remove time-sensitive claims
        unset($payload['exp'], $payload['iat']);
        
        return $this->generateToken($payload);
    }
    
    private function base64UrlEncode(string $data): string {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}

// API Key Authentication
class ApiKeyAuthenticator {
    private array $apiKeys = [];
    private array $keyPermissions = [];
    
    public function addKey(string $key, array $permissions = []): void {
        $this->apiKeys[$key] = [
            'key' => $key,
            'created_at' => time(),
            'last_used' => null
        ];
        
        $this->keyPermissions[$key] = $permissions;
    }
    
    public function removeKey(string $key): void {
        unset($this->apiKeys[$key]);
        unset($this->keyPermissions[$key]);
    }
    
    public function validateKey(string $key): bool {
        return isset($this->apiKeys[$key]);
    }
    
    public function hasPermission(string $key, string $permission): bool {
        return in_array($permission, $this->keyPermissions[$key] ?? []);
    }
    
    public function updateLastUsed(string $key): void {
        if (isset($this->apiKeys[$key])) {
            $this->apiKeys[$key]['last_used'] = time();
        }
    }
    
    public function getKeyInfo(string $key): ?array {
        return $this->apiKeys[$key] ?? null;
    }
}

// OAuth2 Authentication (simplified)
class OAuth2Authenticator {
    private array $clients = [];
    private array $accessTokens = [];
    private array $refreshTokens = [];
    
    public function registerClient(string $clientId, string $clientSecret, array $redirectUris): void {
        $this->clients[$clientId] = [
            'client_id' => $clientId,
            'client_secret' => password_hash($clientSecret, PASSWORD_DEFAULT),
            'redirect_uris' => $redirectUris,
            'created_at' => time()
        ];
    }
    
    public function authenticateClient(string $clientId, string $clientSecret): bool {
        if (!isset($this->clients[$clientId])) {
            return false;
        }
        
        return password_verify($clientSecret, $this->clients[$clientId]['client_secret']);
    }
    
    public function authorizeClient(string $clientId, string $redirectUri, array $scopes = []): ?string {
        if (!isset($this->clients[$clientId])) {
            return null;
        }
        
        $client = $this->clients[$clientId];
        
        if (!in_array($redirectUri, $client['redirect_uris'])) {
            return null;
        }
        
        // Generate authorization code
        $authCode = bin2hex(random_bytes(16));
        
        // Store authorization code with expiry
        $_SESSION['auth_codes'][$authCode] = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scopes' => $scopes,
            'expires_at' => time() + 600, // 10 minutes
            'user_id' => $_SESSION['user_id'] ?? null
        ];
        
        return $authCode;
    }
    
    public function exchangeCodeForToken(string $code, string $clientId, string $clientSecret): ?array {
        if (!$this->authenticateClient($clientId, $clientSecret)) {
            return null;
        }
        
        $authCodeData = $_SESSION['auth_codes'][$code] ?? null;
        
        if (!$authCodeData || $authCodeData['expires_at'] < time()) {
            return null;
        }
        
        if ($authCodeData['client_id'] !== $clientId) {
            return null;
        }
        
        // Generate tokens
        $accessToken = $this->generateAccessToken($authCodeData['user_id'], $authCodeData['scopes']);
        $refreshToken = $this->generateRefreshToken($authCodeData['user_id']);
        
        // Remove authorization code
        unset($_SESSION['auth_codes'][$code]);
        
        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'scope' => implode(' ', $authCodeData['scopes'])
        ];
    }
    
    public function refreshToken(string $refreshToken): ?array {
        $tokenData = $this->refreshTokens[$refreshToken] ?? null;
        
        if (!$tokenData || $tokenData['expires_at'] < time()) {
            return null;
        }
        
        $newAccessToken = $this->generateAccessToken($tokenData['user_id'], $tokenData['scopes']);
        
        return [
            'access_token' => $newAccessToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'scope' => implode(' ', $tokenData['scopes'])
        ];
    }
    
    private function generateAccessToken(int $userId, array $scopes): string {
        $token = bin2hex(random_bytes(32));
        
        $this->accessTokens[$token] = [
            'user_id' => $userId,
            'scopes' => $scopes,
            'expires_at' => time() + 3600,
            'created_at' => time()
        ];
        
        return $token;
    }
    
    private function generateRefreshToken(int $userId): string {
        $token = bin2hex(random_bytes(32));
        
        $this->refreshTokens[$token] = [
            'user_id' => $userId,
            'scopes' => ['read', 'write'],
            'expires_at' => time() + (30 * 24 * 3600), // 30 days
            'created_at' => time()
        ];
        
        return $token;
    }
    
    public function validateAccessToken(string $token): ?array {
        $tokenData = $this->accessTokens[$token] ?? null;
        
        if (!$tokenData || $tokenData['expires_at'] < time()) {
            return null;
        }
        
        return $tokenData;
    }
}

// Authorization Manager
class AuthorizationManager {
    private array $roles = [];
    private array $permissions = [];
    private array $userRoles = [];
    private array $rolePermissions = [];
    
    public function addRole(string $role, array $permissions = []): void {
        $this->roles[$role] = $permissions;
    }
    
    public function addPermission(string $permission): void {
        $this->permissions[] = $permission;
    }
    
    public function assignRoleToUser(int $userId, string $role): void {
        if (!isset($this->userRoles[$userId])) {
            $this->userRoles[$userId] = [];
        }
        
        $this->userRoles[$userId][] = $role;
    }
    
    public function assignPermissionToRole(string $role, string $permission): void {
        if (!isset($this->rolePermissions[$role])) {
            $this->rolePermissions[$role] = [];
        }
        
        $this->rolePermissions[$role][] = $permission;
    }
    
    public function hasPermission(int $userId, string $permission): bool {
        $userRoles = $this->userRoles[$userId] ?? [];
        
        foreach ($userRoles as $role) {
            if (in_array($permission, $this->rolePermissions[$role] ?? [])) {
                return true;
            }
        }
        
        return false;
    }
    
    public function hasRole(int $userId, string $role): bool {
        return in_array($role, $this->userRoles[$userId] ?? []);
    }
    
    public function getUserPermissions(int $userId): array {
        $permissions = [];
        $userRoles = $this->userRoles[$userId] ?? [];
        
        foreach ($userRoles as $role) {
            $permissions = array_merge($permissions, $this->rolePermissions[$role] ?? []);
        }
        
        return array_unique($permissions);
    }
    
    public function getUserRoles(int $userId): array {
        return $this->userRoles[$userId] ?? [];
    }
}

// Security Middleware
class SecurityMiddleware {
    private JwtAuthenticator $jwtAuth;
    private ApiKeyAuthenticator $apiKeyAuth;
    private OAuth2Authenticator $oauth2Auth;
    private AuthorizationManager $authz;
    
    public function __construct(
        JwtAuthenticator $jwtAuth,
        ApiKeyAuthenticator $apiKeyAuth,
        OAuth2Authenticator $oauth2Auth,
        AuthorizationManager $authz
    ) {
        $this->jwtAuth = $jwtAuth;
        $this->apiKeyAuth = $apiKeyAuth;
        $this->oauth2Auth = $oauth2Auth;
        $this->authz = $authz;
    }
    
    public function authenticate(callable $next): Response {
        $request = func_get_arg(0);
        
        // Try JWT authentication first
        $token = $request->getHeader('Authorization');
        
        if ($token && strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
            $payload = $this->jwtAuth->validateToken($token);
            
            if ($payload) {
                $request->setUser($payload['user_id']);
                $request->setAuthType('jwt');
                return $next($request);
            }
        }
        
        // Try API key authentication
        $apiKey = $request->getHeader('X-API-Key');
        
        if ($apiKey && $this->apiKeyAuth->validateKey($apiKey)) {
            $request->setUser($this->getUserIdFromApiKey($apiKey));
            $request->setAuthType('api_key');
            $request->setApiKey($apiKey);
            $this->apiKeyAuth->updateLastUsed($apiKey);
            return $next($request);
        }
        
        // Try OAuth2 authentication
        $oauthToken = $request->getHeader('Authorization');
        
        if ($oauthToken && strpos($oauthToken, 'Bearer ') === 0) {
            $oauthToken = substr($oauthToken, 7);
            $tokenData = $this->oauth2Auth->validateAccessToken($oauthToken);
            
            if ($tokenData) {
                $request->setUser($tokenData['user_id']);
                $request->setAuthType('oauth2');
                $request->setScopes($tokenData['scopes']);
                return $next($request);
            }
        }
        
        return Response::error('Authentication required', 401);
    }
    
    public function authorize(string $permission): callable {
        return function($request) use ($permission) {
            $userId = $request->getUser();
            
            if (!$userId) {
                return Response::error('Authentication required', 401);
            }
            
            if (!$this->authz->hasPermission($userId, $permission)) {
                return Response::error('Insufficient permissions', 403);
            }
            
            return $this->next($request);
        };
    }
    
    public function requireRole(string $role): callable {
        return function($request) use ($role) {
            $userId = $request->getUser();
            
            if (!$userId) {
                return Response::error('Authentication required', 401);
            }
            
            if (!$this->authz->hasRole($userId, $role)) {
                return Response::error('Insufficient permissions', 403);
            }
            
            return $this->next($request);
        };
    }
    
    public function rateLimit(int $requests, int $window): callable {
        return function($request) use ($requests, $window) {
            $clientId = $this->getClientId($request);
            $key = "rate_limit:$clientId";
            
            $current = apcu_fetch($key) ?: ['count' => 0, 'reset_time' => time() + $window];
            
            if (time() > $current['reset_time']) {
                $current = ['count' => 0, 'reset_time' => time() + $window];
            }
            
            if ($current['count'] >= $requests) {
                return Response::error('Rate limit exceeded', 429, [
                    'retry_after' => $current['reset_time'] - time()
                ]);
            }
            
            $current['count']++;
            apcu_store($key, $current, $window);
            
            return $this->next($request);
        };
    }
    
    public function cors(array $allowedOrigins = ['*']): callable {
        return function($request) use ($allowedOrigins) {
            $origin = $request->getHeader('Origin') ?? '*';
            
            if ($allowedOrigins !== ['*'] && !in_array($origin, $allowedOrigins)) {
                return Response::error('CORS policy violation', 403);
            }
            
            $response = $this->next($request);
            
            $headers = $response->getHeaders();
            $headers['Access-Control-Allow-Origin'] = $origin;
            $headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, DELETE, OPTIONS';
            $headers['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, X-API-Key';
            $headers['Access-Control-Allow-Credentials'] = 'true';
            
            return new Response($response->getStatusCode(), $headers, $response->getBody());
        };
    }
    
    private function getClientId(Request $request): string {
        // Try to get a unique client identifier
        return $request->getHeader('X-Forwarded-For') ?:
               $request->getHeader('X-Real-IP') ?:
               $request->getHeader('Remote-Addr') ?:
               $_SERVER['REMOTE_ADDR'] ?:
               'unknown';
    }
    
    private function getUserIdFromApiKey(string $apiKey): int {
        // This would typically look up the API key in a database
        // For now, return a placeholder
        return 1;
    }
    
    private function next($request) {
        return func_get_arg(1)($request);
    }
}

// Request extension for authentication
class Request {
    // ... existing properties ...
    private ?int $user = null;
    private ?string $authType = null;
    private ?string $apiKey = null;
    private array $scopes = [];
    
    public function getUser(): ?int {
        return $this->user;
    }
    
    public function setUser(int $user): void {
        $this->user = $user;
    }
    
    public function getAuthType(): ?string {
        return $this->authType;
    }
    
    public function setAuthType(string $type): void {
        $this->authType = $type;
    }
    
    public function getApiKey(): ?string {
        return $this->apiKey;
    }
    
    public function setApiKey(string $apiKey): void {
        $this->apiKey = $apiKey;
    }
    
    public function getScopes(): array {
        return $this->scopes;
    }
    
    public function setScopes(array $scopes): void {
        $this->scopes = $scopes;
    }
}

// Security Headers Middleware
class SecurityHeadersMiddleware {
    public function __invoke(callable $next): Response {
        $response = $next();
        
        $headers = $response->getHeaders();
        
        // Security headers
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'DENY';
        $headers['X-XSS-Protection'] = '1; mode=block';
        $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        $headers['Content-Security-Policy'] = "default-src 'self'";
        
        return new Response($response->getStatusCode(), $headers, $response->getBody());
    }
}

// Input Validation and Sanitization
class SecurityValidator {
    public function validateInput(array $data, array $rules): array {
        $validated = [];
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            try {
                $validated[$field] = $this->validateField($value, $rule);
            } catch (ValidationException $e) {
                $errors[$field] = $e->getMessage();
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return $validated;
    }
    
    private function validateField($value, string $rule) {
        $rules = explode('|', $rule);
        
        foreach ($rules as $singleRule) {
            $value = $this->applyValidationRule($value, $singleRule);
        }
        
        return $value;
    }
    
    private function applyValidationRule($value, string $rule) {
        if ($rule === 'required' && ($value === null || $value === '')) {
            throw new ValidationException('Field is required');
        }
        
        if ($value === null || $value === '') {
            return $value;
        }
        
        if ($rule === 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('Invalid email format');
            }
        }
        
        if ($rule === 'url') {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                throw new ValidationException('Invalid URL format');
            }
        }
        
        if (strpos($rule, 'min:') === 0) {
            $min = substr($rule, 4);
            if (strlen($value) < (int)$min) {
                throw new ValidationException("Minimum length is $min");
            }
        }
        
        if (strpos($rule, 'max:') === 0) {
            $max = substr($rule, 4);
            if (strlen($value) > (int)$max) {
                throw new ValidationException("Maximum length is $max");
            }
        }
        
        if ($rule === 'sanitize') {
            $value = $this->sanitize($value);
        }
        
        return $value;
    }
    
    public function sanitize(string $input): string {
        // Remove potentially dangerous characters
        $input = preg_replace('/[<>"\']/', '', $input);
        
        // Remove SQL injection attempts
        $input = preg_replace('/\b(union|select|insert|update|delete|drop|exec|script)\b/i', '', $input);
        
        return trim($input);
    }
    
    public function sanitizeArray(array $data): array {
        return array_map([$this, 'sanitize'], $data);
    }
}

// Usage example
$jwtAuth = new JwtAuthenticator('your-secret-key');
$apiKeyAuth = new ApiKeyAuthenticator();
$oauth2Auth = new OAuth2Authenticator();
$authz = new AuthorizationManager();

// Set up roles and permissions
$authz->addRole('admin', ['read', 'write', 'delete', 'admin']);
$authz->addRole('user', ['read', 'write']);
$authz->addRole('guest', ['read']);

$authz->assignRoleToUser(1, 'admin');
$authz->assignRoleToUser(2, 'user');

$security = new SecurityMiddleware($jwtAuth, $apiKeyAuth, $oauth2Auth, $authz);

// Add middleware to router
$router->addMiddleware([$security, 'cors']);
$router->addMiddleware([$security, 'authenticate']);
$router->addMiddleware([$security, 'rateLimit', 100, 3600]);

// Protected routes
$router->get('/admin/users', function($request) {
    return Response::json(['users' => 'admin data']);
}, [$security, 'authorize', 'admin']);

$router->post('/users', function($request) {
    return Response::json(['message' => 'User created']);
}, [$security, 'authorize', 'write']);
?>
```

## Summary

PHP API Development provides:

**RESTful API Design:**
- Complete routing system with middleware
- Resource controllers with validation
- HTTP method handling
- Request/response management
- Error handling and status codes

**API Documentation:**
- OpenAPI/Swagger integration
- Automatic documentation generation
- Annotation-based documentation
- Swagger UI integration
- Schema definitions

**API Versioning:**
- Multiple version support
- Version detection strategies
- Data transformation between versions
- Deprecation management
- Migration helpers

**API Security:**
- JWT authentication
- API key authentication
- OAuth2 implementation
- Role-based authorization
- Security headers and CORS

**Key Features:**
- Flexible routing system
- Middleware pipeline
- Input validation and sanitization
- Rate limiting
- Comprehensive error handling

**Best Practices:**
- RESTful design principles
- Proper HTTP status codes
- Consistent API responses
- Security-first approach
- Comprehensive documentation

**Implementation Considerations:**
- Performance optimization
- Caching strategies
- Logging and monitoring
- Testing strategies
- Deployment considerations

PHP API Development provides a complete foundation for building robust, secure, and well-documented RESTful APIs with modern authentication, versioning, and security features.
