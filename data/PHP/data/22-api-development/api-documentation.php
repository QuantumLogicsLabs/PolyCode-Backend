<?php
/**
 * API Documentation and Versioning
 * 
 * Implementation of API documentation generation and versioning strategies.
 */

// OpenAPI Specification Generator
class OpenApiGenerator
{
    private array $spec = [];
    private array $paths = [];
    private array $components = [];
    
    public function __construct()
    {
        $this->initializeSpec();
    }
    
    /**
     * Initialize OpenAPI specification
     */
    private function initializeSpec(): void
    {
        $this->spec = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'PHP Learning Guide API',
                'description' => 'A comprehensive API for demonstrating PHP development concepts',
                'version' => '1.0.0',
                'contact' => [
                    'name' => 'API Support',
                    'email' => 'support@example.com',
                    'url' => 'https://example.com/support'
                ],
                'license' => [
                    'name' => 'MIT',
                    'url' => 'https://opensource.org/licenses/MIT'
                ]
            ],
            'servers' => [
                [
                    'url' => 'https://api.example.com/v1',
                    'description' => 'Production server'
                ],
                [
                    'url' => 'https://staging-api.example.com/v1',
                    'description' => 'Staging server'
                ],
                [
                    'url' => 'http://localhost:8000/v1',
                    'description' => 'Development server'
                ]
            ]
        ];
    }
    
    /**
     * Add path to specification
     */
    public function addPath(string $path, array $operations): void
    {
        $this->paths[$path] = $operations;
    }
    
    /**
     * Add component schema
     */
    public function addSchema(string $name, array $schema): void
    {
        $this->components['schemas'][$name] = $schema;
    }
    
    /**
     * Add security scheme
     */
    public function addSecurityScheme(string $name, array $scheme): void
    {
        $this->components['securitySchemes'][$name] = $scheme;
    }
    
    /**
     * Generate complete specification
     */
    public function generate(): array
    {
        $spec = $this->spec;
        $spec['paths'] = $this->paths;
        
        if (!empty($this->components)) {
            $spec['components'] = $this->components;
        }
        
        return $spec;
    }
    
    /**
     * Generate JSON specification
     */
    public function generateJson(): string
    {
        return json_encode($this->generate(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Generate YAML specification
     */
    public function generateYaml(): string
    {
        // Simplified YAML generation
        $array = $this->generate();
        return $this->arrayToYaml($array);
    }
    
    /**
     * Convert array to YAML
     */
    private function arrayToYaml(array $array, int $level = 0): string
    {
        $yaml = '';
        $indent = str_repeat('  ', $level);
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $yaml .= "$indent$key:\n";
                $yaml .= $this->arrayToYaml($value, $level + 1);
            } else {
                $yaml .= "$indent$key: $value\n";
            }
        }
        
        return $yaml;
    }
}

// API Documentation Builder
class ApiDocumentationBuilder
{
    private OpenApiGenerator $openApi;
    private array $endpoints = [];
    
    public function __construct()
    {
        $this->openApi = new OpenApiGenerator();
        $this->initializeSchemas();
        $this->initializeSecurity();
    }
    
    /**
     * Initialize component schemas
     */
    private function initializeSchemas(): void
    {
        // User schema
        $this->openApi->addSchema('User', [
            'type' => 'object',
            'required' => ['id', 'name', 'email'],
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'description' => 'Unique identifier for the user'
                ],
                'name' => [
                    'type' => 'string',
                    'minLength' => 2,
                    'maxLength' => 50,
                    'description' => 'User name'
                ],
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'description' => 'User email address'
                ],
                'createdAt' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'description' => 'User creation timestamp'
                ],
                'updatedAt' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'description' => 'Last update timestamp'
                ]
            ]
        ]);
        
        // Post schema
        $this->openApi->addSchema('Post', [
            'type' => 'object',
            'required' => ['id', 'title', 'authorId'],
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'description' => 'Unique identifier for the post'
                ],
                'title' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 200,
                    'description' => 'Post title'
                ],
                'content' => [
                    'type' => 'string',
                    'maxLength' => 10000,
                    'description' => 'Post content'
                ],
                'authorId' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'description' => 'ID of the post author'
                ],
                'publishedAt' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'description' => 'Publication timestamp'
                ]
            ]
        ]);
        
        // Error schema
        $this->openApi->addSchema('Error', [
            'type' => 'object',
            'required' => ['message', 'code'],
            'properties' => [
                'message' => [
                    'type' => 'string',
                    'description' => 'Error message'
                ],
                'code' => [
                    'type' => 'integer',
                    'description' => 'Error code'
                ],
                'details' => [
                    'type' => 'object',
                    'description' => 'Additional error details'
                ]
            ]
        ]);
        
        // Pagination schema
        $this->openApi->addSchema('Pagination', [
            'type' => 'object',
            'properties' => [
                'total' => [
                    'type' => 'integer',
                    'description' => 'Total number of items'
                ],
                'page' => [
                    'type' => 'integer',
                    'description' => 'Current page number'
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Items per page'
                ],
                'pages' => [
                    'type' => 'integer',
                    'description' => 'Total number of pages'
                ]
            ]
        ]);
    }
    
    /**
     * Initialize security schemes
     */
    private function initializeSecurity(): void
    {
        // JWT authentication
        $this->openApi->addSecurityScheme('BearerAuth', [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
            'description' => 'JWT authentication token'
        ]);
        
        // API key authentication
        $this->openApi->addSecurityScheme('ApiKeyAuth', [
            'type' => 'apiKey',
            'in' => 'header',
            'name' => 'X-API-Key',
            'description' => 'API key for authentication'
        ]);
    }
    
    /**
     * Add GET endpoint
     */
    public function addGetEndpoint(string $path, string $summary, array $parameters = [], array $responses = []): void
    {
        $operation = [
            'summary' => $summary,
            'description' => $summary,
            'operationId' => lcfirst(str_replace(' ', '', $summary)),
            'tags' => $this->extractTags($path),
            'parameters' => $parameters,
            'responses' => array_merge([
                '200' => [
                    'description' => 'Successful response'
                ]
            ], $responses)
        ];
        
        $this->openApi->addPath($path, ['get' => $operation]);
    }
    
    /**
     * Add POST endpoint
     */
    public function addPostEndpoint(string $path, string $summary, array $requestBody = [], array $responses = []): void
    {
        $operation = [
            'summary' => $summary,
            'description' => $summary,
            'operationId' => lcfirst(str_replace(' ', '', $summary)),
            'tags' => $this->extractTags($path),
            'requestBody' => $requestBody,
            'responses' => array_merge([
                '201' => [
                    'description' => 'Resource created successfully'
                ]
            ], $responses)
        ];
        
        $this->openApi->addPath($path, ['post' => $operation]);
    }
    
    /**
     * Add PUT endpoint
     */
    public function addPutEndpoint(string $path, string $summary, array $requestBody = [], array $responses = []): void
    {
        $operation = [
            'summary' => $summary,
            'description' => $summary,
            'operationId' => lcfirst(str_replace(' ', '', $summary)),
            'tags' => $this->extractTags($path),
            'requestBody' => $requestBody,
            'responses' => array_merge([
                '200' => [
                    'description' => 'Resource updated successfully'
                ]
            ], $responses)
        ];
        
        $this->openApi->addPath($path, ['put' => $operation]);
    }
    
    /**
     * Add DELETE endpoint
     */
    public function addDeleteEndpoint(string $path, string $summary, array $responses = []): void
    {
        $operation = [
            'summary' => $summary,
            'description' => $summary,
            'operationId' => lcfirst(str_replace(' ', '', $summary)),
            'tags' => $this->extractTags($path),
            'responses' => array_merge([
                '204' => [
                    'description' => 'Resource deleted successfully'
                ]
            ], $responses)
        ];
        
        $this->openApi->addPath($path, ['delete' => $operation]);
    }
    
    /**
     * Extract tags from path
     */
    private function extractTags(string $path): array
    {
        $parts = explode('/', trim($path, '/'));
        return [ucfirst($parts[0] ?? 'Default')];
    }
    
    /**
     * Build complete API documentation
     */
    public function build(): void
    {
        // User endpoints
        $this->addGetEndpoint('/users', 'Get all users', [
            [
                'name' => 'limit',
                'in' => 'query',
                'description' => 'Maximum number of users to return',
                'required' => false,
                'schema' => ['type' => 'integer', 'default' => 10]
            ],
            [
                'name' => 'offset',
                'in' => 'query',
                'description' => 'Number of users to skip',
                'required' => false,
                'schema' => ['type' => 'integer', 'default' => 0]
            ]
        ], [
            '200' => [
                'description' => 'List of users',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'data' => [
                                    'type' => 'array',
                                    'items' => ['$ref' => '#/components/schemas/User']
                                ],
                                'pagination' => ['$ref' => '#/components/schemas/Pagination']
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        
        $this->addGetEndpoint('/users/{id}', 'Get user by ID', [
            [
                'name' => 'id',
                'in' => 'path',
                'description' => 'User ID',
                'required' => true,
                'schema' => ['type' => 'integer']
            ]
        ], [
            '200' => [
                'description' => 'User details',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/User']
                    ]
                ]
            ],
            '404' => [
                'description' => 'User not found',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ]
        ]);
        
        $this->addPostEndpoint('/users', 'Create user', [
            'description' => 'Create a new user',
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'required' => ['name', 'email'],
                        'properties' => [
                            'name' => [
                                'type' => 'string',
                                'minLength' => 2,
                                'maxLength' => 50,
                                'description' => 'User name'
                            ],
                            'email' => [
                                'type' => 'string',
                                'format' => 'email',
                                'description' => 'User email address'
                            ]
                        ]
                    ]
                ]
            ]
        ], [
            '201' => [
                'description' => 'User created successfully',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/User']
                    ]
                ]
            ],
            '400' => [
                'description' => 'Validation error',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ]
        ]);
        
        $this->addPutEndpoint('/users/{id}', 'Update user', [
            'description' => 'Update user information',
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => [
                                'type' => 'string',
                                'minLength' => 2,
                                'maxLength' => 50,
                                'description' => 'User name'
                            ],
                            'email' => [
                                'type' => 'string',
                                'format' => 'email',
                                'description' => 'User email address'
                            ]
                        ]
                    ]
                ]
            ]
        ], [
            '200' => [
                'description' => 'User updated successfully',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/User']
                    ]
                ]
            ],
            '404' => [
                'description' => 'User not found',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ]
        ]);
        
        $this->addDeleteEndpoint('/users/{id}', 'Delete user', [
            '404' => [
                'description' => 'User not found',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ]
        ]);
        
        // Post endpoints
        $this->addGetEndpoint('/posts', 'Get all posts', [
            [
                'name' => 'limit',
                'in' => 'query',
                'description' => 'Maximum number of posts to return',
                'required' => false,
                'schema' => ['type' => 'integer', 'default' => 10]
            ],
            [
                'name' => 'authorId',
                'in' => 'query',
                'description' => 'Filter by author ID',
                'required' => false,
                'schema' => ['type' => 'integer']
            ]
        ]);
        
        $this->addPostEndpoint('/posts', 'Create post', [
            'description' => 'Create a new post',
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'required' => ['title', 'authorId'],
                        'properties' => [
                            'title' => [
                                'type' => 'string',
                                'minLength' => 1,
                                'maxLength' => 200,
                                'description' => 'Post title'
                            ],
                            'content' => [
                                'type' => 'string',
                                'maxLength' => 10000,
                                'description' => 'Post content'
                            ],
                            'authorId' => [
                                'type' => 'integer',
                                'description' => 'ID of the post author'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Get OpenAPI specification
     */
    public function getSpecification(): array
    {
        return $this->openApi->generate();
    }
    
    /**
     * Get JSON specification
     */
    public function getJson(): string
    {
        return $this->openApi->generateJson();
    }
    
    /**
     * Get YAML specification
     */
    public function getYaml(): string
    {
        return $this->openApi->generateYaml();
    }
}

// API Version Manager
class ApiVersionManager
{
    private array $versions = [];
    private string $currentVersion;
    private array $deprecationPolicies = [];
    
    public function __construct()
    {
        $this->initializeVersions();
        $this->currentVersion = 'v1';
    }
    
    /**
     * Initialize API versions
     */
    private function initializeVersions(): void
    {
        $this->versions = [
            'v1' => [
                'version' => '1.0.0',
                'released' => '2024-01-01',
                'deprecated' => false,
                'sunset' => null,
                'endpoints' => [
                    'GET /users',
                    'POST /users',
                    'GET /users/{id}',
                    'PUT /users/{id}',
                    'DELETE /users/{id}',
                    'GET /posts',
                    'POST /posts',
                    'GET /posts/{id}',
                    'PUT /posts/{id}',
                    'DELETE /posts/{id}'
                ],
                'changes' => [
                    'Initial release with user and post management'
                ]
            ],
            'v2' => [
                'version' => '2.0.0',
                'released' => '2024-06-01',
                'deprecated' => false,
                'sunset' => null,
                'endpoints' => [
                    'GET /users',
                    'POST /users',
                    'GET /users/{id}',
                    'PATCH /users/{id}',
                    'DELETE /users/{id}',
                    'GET /posts',
                    'POST /posts',
                    'GET /posts/{id}',
                    'PATCH /posts/{id}',
                    'DELETE /posts/{id}',
                    'GET /users/{id}/posts',
                    'POST /users/{id}/posts'
                ],
                'changes' => [
                    'Replaced PUT with PATCH for partial updates',
                    'Added user posts relationship endpoints',
                    'Improved error response format',
                    'Added pagination metadata'
                ]
            ]
        ];
        
        $this->deprecationPolicies = [
            'notice_period' => 90, // days
            'support_period' => 180, // days after deprecation
            'sunset_period' => 365 // // days after deprecation
        ];
    }
    
    /**
     * Get version information
     */
    public function getVersion(string $version): ?array
    {
        return $this->versions[$version] ?? null;
    }
    
    /**
     * Get all versions
     */
    public function getAllVersions(): array
    {
        return $this->versions;
    }
    
    /**
     * Get current version
     */
    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }
    
    /**
     * Set current version
     */
    public function setCurrentVersion(string $version): bool
    {
        if (isset($this->versions[$version])) {
            $this->currentVersion = $version;
            return true;
        }
        
        return false;
    }
    
    /**
     * Add new version
     */
    public function addVersion(string $version, array $config): void
    {
        $this->versions[$version] = array_merge([
            'version' => $version,
            'released' => date('Y-m-d'),
            'deprecated' => false,
            'sunset' => null,
            'endpoints' => [],
            'changes' => []
        ], $config);
    }
    
    /**
     * Deprecate version
     */
    public function deprecateVersion(string $version, string $sunsetDate = null): void
    {
        if (isset($this->versions[$version])) {
            $this->versions[$version]['deprecated'] = true;
            $this->versions[$version]['sunset'] = $sunsetDate ?: date('Y-m-d', strtotime("+{$this->deprecationPolicies['sunset_period']} days"));
        }
    }
    
    /**
     * Check if version is deprecated
     */
    public function isDeprecated(string $version): bool
    {
        return $this->versions[$version]['deprecated'] ?? false;
    }
    
    /**
     * Check if version is sunset
     */
    public function isSunset(string $version): bool
    {
        $sunset = $this->versions[$version]['sunset'] ?? null;
        return $sunset && strtotime($sunset) < time();
    }
    
    /**
     * Get version compatibility matrix
     */
    public function getCompatibilityMatrix(): array
    {
        $matrix = [];
        
        foreach ($this->versions as $version => $info) {
            $matrix[$version] = [
                'compatible' => [],
                'breaking_changes' => [],
                'deprecated_features' => []
            ];
            
            // Determine compatibility with other versions
            foreach ($this->versions as $otherVersion => $otherInfo) {
                if ($version !== $otherVersion) {
                    $compatible = $this->checkCompatibility($version, $otherVersion);
                    $matrix[$version]['compatible'][$otherVersion] = $compatible;
                }
            }
        }
        
        return $matrix;
    }
    
    /**
     * Check compatibility between versions
     */
    private function checkCompatibility(string $version1, string $version2): bool
    {
        // Simplified compatibility check
        $v1 = $this->versions[$version1];
        $v2 = $this->versions[$version2];
        
        // Major version differences are breaking
        $major1 = (int) explode('.', $v1['version'])[0];
        $major2 = (int) explode('.', $v2['version'])[0];
        
        return $major1 === $major2;
    }
    
    /**
     * Get migration guide
     */
    public function getMigrationGuide(string $from, string $to): array
    {
        $fromVersion = $this->versions[$from] ?? null;
        $toVersion = $this->versions[$to] ?? null;
        
        if (!$fromVersion || !$toVersion) {
            return [];
        }
        
        return [
            'from' => $from,
            'to' => $to,
            'breaking_changes' => $this->getBreakingChanges($from, $to),
            'deprecated_features' => $this->getDeprecatedFeatures($from, $to),
            'new_features' => $toVersion['changes'] ?? [],
            'migration_steps' => $this->generateMigrationSteps($from, $to)
        ];
    }
    
    /**
     * Get breaking changes between versions
     */
    private function getBreakingChanges(string $from, string $to): array
    {
        // Simplified breaking changes detection
        $fromEndpoints = $this->versions[$from]['endpoints'] ?? [];
        $toEndpoints = $this->versions[$to]['endpoints'] ?? [];
        
        $breaking = [];
        
        // Check for removed endpoints
        foreach ($fromEndpoints as $endpoint) {
            if (!in_array($endpoint, $toEndpoints)) {
                $breaking[] = [
                    'type' => 'removed_endpoint',
                    'endpoint' => $endpoint,
                    'message' => "Endpoint $endpoint has been removed"
                ];
            }
        }
        
        // Check for changed methods (PUT -> PATCH)
        foreach ($fromEndpoints as $endpoint) {
            if (strpos($endpoint, 'PUT') !== false) {
                $patchEndpoint = str_replace('PUT', 'PATCH', $endpoint);
                if (in_array($patchEndpoint, $toEndpoints)) {
                    $breaking[] = [
                        'type' => 'changed_method',
                        'endpoint' => $endpoint,
                        'new_endpoint' => $patchEndpoint,
                        'message' => "PUT method changed to PATCH for $endpoint"
                    ];
                }
            }
        }
        
        return $breaking;
    }
    
    /**
     * Get deprecated features
     */
    private function getDeprecatedFeatures(string $from, string $to): array
    {
        return [];
    }
    
    /**
     * Generate migration steps
     */
    private function generateMigrationSteps(string $from, string $to): array
    {
        return [
            'Review breaking changes',
            'Update API base URLs',
            'Modify request methods',
            'Update response handling',
            'Test new endpoints',
            'Deploy changes gradually'
        ];
    }
}

// Interactive Documentation Generator
class InteractiveDocumentation
{
    private array $spec;
    private string $template;
    
    public function __construct(array $spec)
    {
        $this->spec = $spec;
        $this->template = $this->loadTemplate();
    }
    
    /**
     * Load HTML template
     */
    private function loadTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <title>{{title}} - API Documentation</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@3.52.5/swagger-ui.css">
    <style>
        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .version-info { background: #f0f0f0; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .endpoint-list { margin-bottom: 30px; }
        .endpoint { border: 1px solid #ddd; margin-bottom: 10px; padding: 15px; border-radius: 5px; }
        .endpoint h4 { margin: 0 0 10px 0; color: #333; }
        .method { display: inline-block; padding: 3px 8px; border-radius: 3px; color: white; font-weight: bold; margin-right: 10px; }
        .get { background: #61affe; }
        .post { background: #49cc90; }
        .put { background: #fca130; }
        .delete { background: #f93e3e; }
        .patch { background: #50e3c2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{title}}</h1>
        <p>{{description}}</p>
        <div class="version-info">
            <strong>Version:</strong> {{version}} | 
            <strong>Released:</strong> {{released}} |
            <strong>Deprecated:</strong> {{deprecated}}
        </div>
    </div>
    
    <div class="endpoint-list">
        <h2>API Endpoints</h2>
        {{endpoints}}
    </div>
    
    <div id="swagger-ui"></div>
    
    <script src="https://unpkg.com/swagger-ui-dist@3.52.5/swagger-ui-bundle.js"></script>
    <script>
        SwaggerUIBundle({
            url: "{{spec_url}}",
            dom_id: "#swagger-ui",
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ]
        });
    </script>
</body>
</html>';
    }
    
    /**
     * Generate interactive documentation
     */
    public function generate(): string
    {
        $html = $this->template;
        
        // Replace placeholders
        $html = str_replace('{{title}}', $this->spec['info']['title'], $html);
        $html = str_replace('{{description}}', $this->spec['info']['description'], $html);
        $html = str_replace('{{version}}', $this->spec['info']['version'], $html);
        $html = str_replace('{{released}}', date('Y-m-d'), $html);
        $html = str_replace('{{deprecated}}', 'false', $html);
        $html = str_replace('{{spec_url}}', '/api/spec.json', $html);
        
        // Generate endpoints list
        $endpointsHtml = '';
        foreach ($this->spec['paths'] as $path => $operations) {
            foreach ($operations as $method => $operation) {
                $endpointsHtml .= $this->generateEndpointHtml($path, $method, $operation);
            }
        }
        
        $html = str_replace('{{endpoints}}', $endpointsHtml, $html);
        
        return $html;
    }
    
    /**
     * Generate endpoint HTML
     */
    private function generateEndpointHtml(string $path, string $method, array $operation): string
    {
        $methodClass = strtolower($method);
        $summary = $operation['summary'] ?? '';
        $description = $operation['description'] ?? '';
        $parameters = $operation['parameters'] ?? [];
        
        $html = '<div class="endpoint">';
        $html .= '<span class="method ' . $methodClass . '">' . strtoupper($method) . '</span>';
        $html .= '<h4>' . $path . '</h4>';
        $html .= '<p><strong>' . $summary . '</strong></p>';
        $html .= '<p>' . $description . '</p>';
        
        if (!empty($parameters)) {
            $html .= '<h5>Parameters:</h5><ul>';
            foreach ($parameters as $param) {
                $required = $param['required'] ?? false ? ' (required)' : '';
                $html .= '<li><strong>' . $param['name'] . '</strong>' . $required . ' - ' . ($param['description'] ?? '') . '</li>';
            }
            $html .= '</ul>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Save documentation to file
     */
    public function save(string $filename): void
    {
        file_put_contents($filename, $this->generate());
    }
}

// API Documentation Examples
class ApiDocumentationExamples
{
    private ApiDocumentationBuilder $builder;
    private ApiVersionManager $versionManager;
    
    public function __construct()
    {
        $this->builder = new ApiDocumentationBuilder();
        $this->versionManager = new ApiVersionManager();
    }
    
    public function demonstrateOpenApiGeneration(): void
    {
        echo "OpenAPI Specification Generation Demo\n";
        echo str_repeat("-", 45) . "\n";
        
        // Build documentation
        $this->builder->build();
        
        // Get JSON specification
        $jsonSpec = $this->builder->getJson();
        
        echo "Generated OpenAPI Specification (JSON):\n";
        echo substr($jsonSpec, 0, 1000) . "...\n\n";
        
        // Get YAML specification
        $yamlSpec = $this->builder->getYaml();
        
        echo "Generated OpenAPI Specification (YAML):\n";
        echo substr($yamlSpec, 0, 500) . "...\n\n";
        
        // Show specification structure
        $spec = $this->builder->getSpecification();
        echo "Specification Structure:\n";
        echo "OpenAPI Version: {$spec['openapi']}\n";
        echo "Title: {$spec['info']['title']}\n";
        echo "Version: {$spec['info']['version']}\n";
        echo "Servers: " . count($spec['servers']) . "\n";
        echo "Paths: " . count($spec['paths']) . "\n";
        echo "Components: " . count($spec['components']) . "\n";
    }
    
    public function demonstrateVersioning(): void
    {
        echo "\nAPI Versioning Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Show all versions
        echo "Available Versions:\n";
        foreach ($this->versionManager->getAllVersions() as $version => $info) {
            echo "$version ({$info['version']}):\n";
            echo "  Released: {$info['released']}\n";
            echo "  Deprecated: " . ($info['deprecated'] ? 'Yes' : 'No') . "\n";
            echo "  Endpoints: " . count($info['endpoints']) . "\n";
            echo "  Changes: " . implode(', ', $info['changes']) . "\n\n";
        }
        
        // Show current version
        echo "Current Version: " . $this->versionManager->getCurrentVersion() . "\n\n";
        
        // Show compatibility matrix
        $matrix = $this->versionManager->getCompatibilityMatrix();
        echo "Compatibility Matrix:\n";
        foreach ($matrix as $version => $compat) {
            echo "$version:\n";
            foreach ($compat['compatible'] as $otherVersion => $isCompatible) {
                echo "  $otherVersion: " . ($isCompatible ? 'Compatible' : 'Incompatible') . "\n";
            }
            echo "\n";
        }
        
        // Show migration guide
        $migration = $this->versionManager->getMigrationGuide('v1', 'v2');
        echo "Migration Guide (v1 to v2):\n";
        echo "Breaking Changes:\n";
        foreach ($migration['breaking_changes'] as $change) {
            echo "  • {$change['message']}\n";
        }
        echo "\nMigration Steps:\n";
        foreach ($migration['migration_steps'] as $step) {
            echo "  • $step\n";
        }
    }
    
    public function demonstrateInteractiveDocs(): void
    {
        echo "\nInteractive Documentation Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Build specification
        $this->builder->build();
        $spec = $this->builder->getSpecification();
        
        // Generate interactive documentation
        $interactive = new InteractiveDocumentation($spec);
        $html = $interactive->generate();
        
        echo "Generated Interactive Documentation:\n";
        echo substr($html, 0, 1000) . "...\n\n";
        
        // Show key features
        echo "Interactive Documentation Features:\n";
        echo "• Swagger UI integration\n";
        echo "• Endpoint exploration\n";
        echo "• Interactive API testing\n";
        echo "• Parameter validation\n";
        echo "• Response examples\n";
        echo "• Schema visualization\n";
        echo "• Code generation\n";
        echo "• Downloadable specifications\n";
    }
    
    public function demonstrateDocumentationStrategies(): void
    {
        echo "\nDocumentation Strategies Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. OpenAPI Specification:\n";
        echo "   • Standardized API description format\n";
        echo "   • Machine-readable and human-readable\n";
        echo "   • Supports JSON and YAML formats\n";
        echo "   • Extensive tooling ecosystem\n";
        echo "   • Version control friendly\n\n";
        
        echo "2. Versioning Strategies:\n";
        echo "   • URL path versioning (/v1, /v2)\n";
        echo "   • Header versioning (Accept: application/vnd.api+json;version=1)\n";
        echo "   • Query parameter versioning (?version=1)\n";
        echo "   • Semantic versioning (major.minor.patch)\n";
        echo "   • Deprecation and sunset policies\n\n";
        
        echo "3. Documentation Types:\n";
        echo "   • Reference documentation (API specs)\n";
        echo "   • Getting started guides\n";
        echo "   • Tutorials and examples\n";
        echo "   • SDK and client libraries\n";
        echo "   • Interactive playgrounds\n\n";
        
        echo "4. Best Practices:\n";
        echo "   • Keep documentation up to date\n";
        echo "   • Use clear and consistent language\n";
        echo "   • Provide real examples\n";
        echo "   • Include error scenarios\n";
        echo "   • Make it discoverable\n\n";
        
        echo "5. Tooling:\n";
        echo "   • Swagger UI/OpenAPI Generator\n";
        echo "   • Redoc for documentation rendering\n";
        echo "   • Postman for API testing\n";
        echo "   • Insomnia for API development\n";
        echo "   • Custom documentation generators";
    }
    
    public function runAllExamples(): void
    {
        echo "API Documentation and Versioning Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateOpenApiGeneration();
        $this->demonstrateVersioning();
        $this->demonstrateInteractiveDocs();
        $this->demonstrateDocumentationStrategies();
    }
}

// Main execution
function runApiDocumentationDemo(): void
{
    $examples = new ApiDocumentationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runApiDocumentationDemo();
}
?>
