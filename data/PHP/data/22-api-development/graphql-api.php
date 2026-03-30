<?php
/**
 * GraphQL API Development
 * 
 * Implementation of GraphQL schema, resolvers, and query execution.
 */

// GraphQL Type System
class GraphQLType
{
    public string $name;
    public string $kind;
    public array $fields;
    public array $args;
    public ?GraphQLType $ofType;
    
    public function __construct(string $name, string $kind, array $fields = [], array $args = [])
    {
        $this->name = $name;
        $this->kind = $kind;
        $this->fields = $fields;
        $this->args = $args;
        $this->ofType = null;
    }
    
    /**
     * Create scalar type
     */
    public static function scalar(string $name): self
    {
        return new self($name, 'SCALAR');
    }
    
    /**
     * Create object type
     */
    public static function object(string $name, array $fields): self
    {
        return new self($name, 'OBJECT', $fields);
    }
    
    /**
     * Create list type
     */
    public static function list(GraphQLType $type): self
    {
        $listType = new self('', 'LIST');
        $listType->ofType = $type;
        return $listType;
    }
    
    /**
     * Create non-null type
     */
    public static function nonNull(GraphQLType $type): self
    {
        $nonNullType = new self('', 'NON_NULL');
        $nonNullType->ofType = $type;
        return $nonNullType;
    }
    
    /**
     * Get type string representation
     */
    public function toString(): string
    {
        switch ($this->kind) {
            case 'NON_NULL':
                return $this->ofType->toString() . '!';
            case 'LIST':
                return '[' . $this->ofType->toString() . ']';
            default:
                return $this->name;
        }
    }
}

// GraphQL Schema
class GraphQLSchema
{
    private array $types = [];
    private array $queries = [];
    private array $mutations = [];
    private array $subscriptions = [];
    
    public function __construct()
    {
        $this->initializeScalarTypes();
        $this->defineCustomTypes();
    }
    
    /**
     * Initialize built-in scalar types
     */
    private function initializeScalarTypes(): void
    {
        $this->types = [
            'String' => GraphQLType::scalar('String'),
            'Int' => GraphQLType::scalar('Int'),
            'Float' => GraphQLType::scalar('Float'),
            'Boolean' => GraphQLType::scalar('Boolean'),
            'ID' => GraphQLType::scalar('ID')
        ];
    }
    
    /**
     * Define custom types
     */
    private function defineCustomTypes(): void
    {
        // User type
        $this->types['User'] = GraphQLType::object('User', [
            'id' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))],
            'name' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))],
            'email' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))],
            'posts' => ['type' => GraphQLType::list(GraphQLType::object('Post'))],
            'createdAt' => ['type' => GraphQLType::scalar('String')]
        ]);
        
        // Post type
        $this->types['Post'] = GraphQLType::object('Post', [
            'id' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))],
            'title' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))],
            'content' => ['type' => GraphQLType::scalar('String')],
            'author' => ['type' => GraphQLType::object('User')],
            'comments' => ['type' => GraphQLType::list(GraphQLType::object('Comment'))],
            'publishedAt' => ['type' => GraphQLType::scalar('String')]
        ]);
        
        // Comment type
        $this->types['Comment'] = GraphQLType::object('Comment', [
            'id' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))],
            'content' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))],
            'author' => ['type' => GraphQLType::object('User')],
            'post' => ['type' => GraphQLType::object('Post')],
            'createdAt' => ['type' => GraphQLType::scalar('String')]
        ]);
        
        // Define queries
        $this->queries = [
            'user' => [
                'type' => GraphQLType::object('User'),
                'args' => [
                    'id' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))]
                ]
            ],
            'users' => [
                'type' => GraphQLType::list(GraphQLType::object('User')),
                'args' => [
                    'limit' => ['type' => GraphQLType::scalar('Int')],
                    'offset' => ['type' => GraphQLType::scalar('Int')]
                ]
            ],
            'post' => [
                'type' => GraphQLType::object('Post'),
                'args' => [
                    'id' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))]
                ]
            ],
            'posts' => [
                'type' => GraphQLType::list(GraphQLType::object('Post')),
                'args' => [
                    'limit' => ['type' => GraphQLType::scalar('Int')],
                    'offset' => ['type' => GraphQLType::scalar('Int')],
                    'authorId' => ['type' => GraphQLType::scalar('ID')]
                ]
            ]
        ];
        
        // Define mutations
        $this->mutations = [
            'createUser' => [
                'type' => GraphQLType::object('User'),
                'args' => [
                    'name' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))],
                    'email' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))]
                ]
            ],
            'createPost' => [
                'type' => GraphQLType::object('Post'),
                'args' => [
                    'title' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))],
                    'content' => ['type' => GraphQLType::scalar('String')],
                    'authorId' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))]
                ]
            ],
            'createComment' => [
                'type' => GraphQLType::object('Comment'),
                'args' => [
                    'content' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('String'))],
                    'postId' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))],
                    'authorId' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))]
                ]
            ]
        ];
    }
    
    /**
     * Get type by name
     */
    public function getType(string $name): ?GraphQLType
    {
        return $this->types[$name] ?? null;
    }
    
    /**
     * Get query fields
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
    
    /**
     * Get mutation fields
     */
    public function getMutations(): array
    {
        return $this->mutations;
    }
    
    /**
     * Get all types
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}

// GraphQL Resolver
class GraphQLResolver
{
    private array $data = [];
    
    public function __construct()
    {
        $this->initializeData();
    }
    
    /**
     * Initialize sample data
     */
    private function initializeData(): void
    {
        $this->data = [
            'users' => [
                1 => ['id' => '1', 'name' => 'John Doe', 'email' => 'john@example.com', 'createdAt' => '2024-01-01'],
                2 => ['id' => '2', 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'createdAt' => '2024-01-02'],
                3 => ['id' => '3', 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'createdAt' => '2024-01-03']
            ],
            'posts' => [
                1 => ['id' => '1', 'title' => 'First Post', 'content' => 'This is my first post', 'authorId' => '1', 'publishedAt' => '2024-01-10'],
                2 => ['id' => '2', 'title' => 'Second Post', 'content' => 'This is my second post', 'authorId' => '2', 'publishedAt' => '2024-01-11'],
                3 => ['id' => '3', 'title' => 'Third Post', 'content' => 'This is my third post', 'authorId' => '1', 'publishedAt' => '2024-01-12']
            ],
            'comments' => [
                1 => ['id' => '1', 'content' => 'Great post!', 'postId' => '1', 'authorId' => '2', 'createdAt' => '2024-01-10'],
                2 => ['id' => '2', 'content' => 'Thanks for sharing', 'postId' => '1', 'authorId' => '3', 'createdAt' => '2024-01-11'],
                3 => ['id' => '3', 'content' => 'Interesting read', 'postId' => '2', 'authorId' => '1', 'createdAt' => '2024-01-12']
            ]
        ];
    }
    
    /**
     * Resolve user query
     */
    public function resolveUser(array $args): ?array
    {
        $id = $args['id'];
        return $this->data['users'][$id] ?? null;
    }
    
    /**
     * Resolve users query
     */
    public function resolveUsers(array $args): array
    {
        $limit = $args['limit'] ?? 10;
        $offset = $args['offset'] ?? 0;
        
        return array_slice($this->data['users'], $offset, $limit, true);
    }
    
    /**
     * Resolve post query
     */
    public function resolvePost(array $args): ?array
    {
        $id = $args['id'];
        return $this->data['posts'][$id] ?? null;
    }
    
    /**
     * Resolve posts query
     */
    public function resolvePosts(array $args): array
    {
        $limit = $args['limit'] ?? 10;
        $offset = $args['offset'] ?? 0;
        $authorId = $args['authorId'] ?? null;
        
        $posts = $this->data['posts'];
        
        if ($authorId) {
            $posts = array_filter($posts, function($post) use ($authorId) {
                return $post['authorId'] === $authorId;
            });
        }
        
        return array_slice($posts, $offset, $limit, true);
    }
    
    /**
     * Resolve user field
     */
    public function resolveUserField(string $fieldName, array $parent): mixed
    {
        switch ($fieldName) {
            case 'posts':
                return array_filter($this->data['posts'], function($post) use ($parent) {
                    return $post['authorId'] === $parent['id'];
                });
            default:
                return $parent[$fieldName] ?? null;
        }
    }
    
    /**
     * Resolve post field
     */
    public function resolvePostField(string $fieldName, array $parent): mixed
    {
        switch ($fieldName) {
            case 'author':
                return $this->data['users'][$parent['authorId']] ?? null;
            case 'comments':
                return array_filter($this->data['comments'], function($comment) use ($parent) {
                    return $comment['postId'] === $parent['id'];
                });
            default:
                return $parent[$fieldName] ?? null;
        }
    }
    
    /**
     * Resolve comment field
     */
    public function resolveCommentField(string $fieldName, array $parent): mixed
    {
        switch ($fieldName) {
            case 'author':
                return $this->data['users'][$parent['authorId']] ?? null;
            case 'post':
                return $this->data['posts'][$parent['postId']] ?? null;
            default:
                return $parent[$fieldName] ?? null;
        }
    }
    
    /**
     * Resolve create user mutation
     */
    public function resolveCreateUser(array $args): array
    {
        $id = (string) (max(array_keys($this->data['users'])) + 1);
        $user = [
            'id' => $id,
            'name' => $args['name'],
            'email' => $args['email'],
            'createdAt' => date('Y-m-d')
        ];
        
        $this->data['users'][$id] = $user;
        
        return $user;
    }
    
    /**
     * Resolve create post mutation
     */
    public function resolveCreatePost(array $args): array
    {
        $id = (string) (max(array_keys($this->data['posts'])) + 1);
        $post = [
            'id' => $id,
            'title' => $args['title'],
            'content' => $args['content'] ?? '',
            'authorId' => $args['authorId'],
            'publishedAt' => date('Y-m-d')
        ];
        
        $this->data['posts'][$id] = $post;
        
        return $post;
    }
    
    /**
     * Resolve create comment mutation
     */
    public function resolveCreateComment(array $args): array
    {
        $id = (string) (max(array_keys($this->data['comments'])) + 1);
        $comment = [
            'id' => $id,
            'content' => $args['content'],
            'postId' => $args['postId'],
            'authorId' => $args['authorId'],
            'createdAt' => date('Y-m-d')
        ];
        
        $this->data['comments'][$id] = $comment;
        
        return $comment;
    }
}

// GraphQL Executor
class GraphQLExecutor
{
    private GraphQLSchema $schema;
    private GraphQLResolver $resolver;
    
    public function __construct()
    {
        $this->schema = new GraphQLSchema();
        $this->resolver = new GraphQLResolver();
    }
    
    /**
     * Execute GraphQL query
     */
    public function execute(string $query, array $variables = []): array
    {
        $parsed = $this->parseQuery($query);
        
        if (!$parsed) {
            return [
                'errors' => [['message' => 'Invalid query syntax']]
            ];
        }
        
        try {
            $result = $this->executeQuery($parsed, $variables);
            return ['data' => $result];
        } catch (Exception $e) {
            return [
                'errors' => [['message' => $e->getMessage()]]
            ];
        }
    }
    
    /**
     * Parse GraphQL query (simplified)
     */
    private function parseQuery(string $query): array
    {
        // Simplified query parsing - in real implementation, use proper parser
        $query = trim($query);
        
        if (strpos($query, '{') === 0) {
            // Remove outer braces
            $inner = substr($query, 1, -1);
            $inner = trim($inner);
            
            // Parse fields
            $fields = [];
            $parts = $this->splitQuery($inner);
            
            foreach ($parts as $part) {
                $part = trim($part);
                if (strpos($part, '{') !== false) {
                    // Nested field
                    $fieldName = substr($part, 0, strpos($part, '{'));
                    $fieldName = trim($fieldName);
                    $nested = substr($part, strpos($part, '{') + 1, -1);
                    $fields[$fieldName] = $this->parseQuery('{' . $nested . '}');
                } else {
                    // Simple field
                    $fields[$part] = null;
                }
            }
            
            return $fields;
        }
        
        return [];
    }
    
    /**
     * Split query into parts
     */
    private function splitQuery(string $query): array
    {
        $parts = [];
        $current = '';
        $depth = 0;
        
        for ($i = 0; $i < strlen($query); $i++) {
            $char = $query[$i];
            
            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
            } elseif ($char === ' ' && $depth === 0) {
                if (!empty(trim($current))) {
                    $parts[] = trim($current);
                    $current = '';
                }
                continue;
            }
            
            $current .= $char;
        }
        
        if (!empty(trim($current))) {
            $parts[] = trim($current);
        }
        
        return $parts;
    }
    
    /**
     * Execute parsed query
     */
    private function executeQuery(array $parsed, array $variables = []): array
    {
        $result = [];
        
        foreach ($parsed as $field => $nested) {
            if ($nested !== null) {
                // Nested field
                $parentData = $this->resolveField($field, []);
                if ($parentData) {
                    $result[$field] = $this->executeNested($nested, $parentData, $field);
                }
            } else {
                // Simple field
                $result[$field] = $this->resolveField($field, $variables);
            }
        }
        
        return $result;
    }
    
    /**
     * Execute nested query
     */
    private function executeNested(array $parsed, array $parentData, string $parentType): array
    {
        $result = [];
        
        foreach ($parsed as $field => $nested) {
            if ($nested !== null) {
                // Nested field
                $fieldData = $this->resolveNestedField($field, $parentData, $parentType);
                if ($fieldData) {
                    $result[$field] = $this->executeNested($nested, $fieldData, $field);
                }
            } else {
                // Simple field
                $result[$field] = $this->resolveNestedField($field, $parentData, $parentType);
            }
        }
        
        return $result;
    }
    
    /**
     * Resolve field
     */
    private function resolveField(string $field, array $args): mixed
    {
        $method = 'resolve' . ucfirst($field);
        
        if (method_exists($this->resolver, $method)) {
            return $this->resolver->$method($args);
        }
        
        return null;
    }
    
    /**
     * Resolve nested field
     */
    private function resolveNestedField(string $field, array $parent, string $parentType): mixed
    {
        $method = 'resolve' . ucfirst($parentType) . 'Field';
        
        if (method_exists($this->resolver, $method)) {
            return $this->resolver->$method($field, $parent);
        }
        
        return $parent[$field] ?? null;
    }
}

// GraphQL Examples
class GraphQLExamples
{
    private GraphQLExecutor $executor;
    
    public function __construct()
    {
        $this->executor = new GraphQLExecutor();
    }
    
    public function demonstrateSchema(): void
    {
        echo "GraphQL Schema Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $schema = new GraphQLSchema();
        
        echo "Available Types:\n";
        foreach ($schema->getTypes() as $name => $type) {
            echo "$name: {$type->kind}\n";
            if (!empty($type->fields)) {
                echo "  Fields: " . implode(', ', array_keys($type->fields)) . "\n";
            }
            echo "\n";
        }
        
        echo "Query Fields:\n";
        foreach ($schema->getQueries() as $name => $field) {
            echo "$name: {$field['type']->toString()}\n";
            if (!empty($field['args'])) {
                echo "  Args: " . implode(', ', array_keys($field['args'])) . "\n";
            }
            echo "\n";
        }
        
        echo "Mutation Fields:\n";
        foreach ($schema->getMutations() as $name => $field) {
            echo "$name: {$field['type']->toString()}\n";
            if (!empty($field['args'])) {
                echo "  Args: " . implode(', ', array_keys($field['args'])) . "\n";
            }
            echo "\n";
        }
    }
    
    public function demonstrateQueries(): void
    {
        echo "\nGraphQL Queries Demo\n";
        echo str_repeat("-", 28) . "\n";
        
        $queries = [
            // Simple user query
            [
                'query' => '{ user(id: "1") { id name email } }',
                'description' => 'Get user by ID'
            ],
            // Users list query
            [
                'query' => '{ users(limit: 2) { id name email } }',
                'description' => 'Get list of users'
            ],
            // Nested query
            [
                'query' => '{ user(id: "1") { id name posts { id title } } }',
                'description' => 'Get user with posts'
            ],
            // Complex nested query
            [
                'query' => '{ posts(limit: 2) { id title author { id name } comments { content } } }',
                'description' => 'Get posts with author and comments'
            ]
        ];
        
        foreach ($queries as $example) {
            echo "Query: {$example['description']}\n";
            echo "GraphQL: {$example['query']}\n";
            
            $result = $this->executor->execute($example['query']);
            echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        }
    }
    
    public function demonstrateMutations(): void
    {
        echo "\nGraphQL Mutations Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $mutations = [
            // Create user
            [
                'mutation' => 'mutation { createUser(name: "Alice", email: "alice@example.com") { id name email } }',
                'description' => 'Create new user'
            ],
            // Create post
            [
                'mutation' => 'mutation { createPost(title: "New Post", content: "Content here", authorId: "1") { id title author { name } } }',
                'description' => 'Create new post'
            ]
        ];
        
        foreach ($mutations as $example) {
            echo "Mutation: {$example['description']}\n";
            echo "GraphQL: {$example['mutation']}\n";
            
            // Note: This is a simplified executor - mutations would need proper handling
            $result = $this->executor->execute(str_replace('mutation', '', $example['mutation']));
            echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        }
    }
    
    public function demonstrateTypeSystem(): void
    {
        echo "\nGraphQL Type System Demo\n";
        echo str_repeat("-", 32) . "\n";
        
        // Demonstrate different types
        $stringType = GraphQLType::scalar('String');
        $userType = GraphQLType::object('User', [
            'id' => ['type' => GraphQLType::nonNull(GraphQLType::scalar('ID'))],
            'name' => ['type' => GraphQLType::scalar('String')],
            'posts' => ['type' => GraphQLType::list(GraphQLType::object('Post'))]
        ]);
        
        $listType = GraphQLType::list(GraphQLType::scalar('String'));
        $nonNullType = GraphQLType::nonNull(GraphQLType::scalar('String'));
        $listOfNonNull = GraphQLType::list(GraphQLType::nonNull(GraphQLType::scalar('String')));
        
        echo "Type Examples:\n";
        echo "String: {$stringType->toString()}\n";
        echo "User: {$userType->toString()}\n";
        echo "List of Strings: {$listType->toString()}\n";
        echo "Non-null String: {$nonNullType->toString()}\n";
        echo "List of Non-null Strings: {$listOfNonNull->toString()}\n\n";
        
        // Demonstrate type introspection
        echo "Type Introspection:\n";
        echo "User Type Fields:\n";
        foreach ($userType->fields as $name => $field) {
            echo "  $name: {$field['type']->toString()}\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nGraphQL Best Practices\n";
        echo str_repeat("-", 28) . "\n";
        
        echo "1. Schema Design:\n";
        echo "   • Design types around your domain\n";
        echo "   • Use consistent naming conventions\n";
        echo "   • Keep mutations focused and single-purpose\n";
        echo "   • Use proper type relationships\n";
        echo "   • Implement pagination for list fields\n\n";
        
        echo "2. Resolver Implementation:\n";
        echo "   • Keep resolvers simple and focused\n";
        echo "   • Implement proper error handling\n";
        echo "   • Use data loaders to prevent N+1 queries\n";
        echo "   • Cache frequently accessed data\n";
        echo "   • Validate input arguments\n\n";
        
        echo "3. Performance:\n";
        echo "   • Implement query complexity analysis\n";
        echo "   • Use query depth limiting\n";
        echo "   • Implement proper caching strategies\n";
        echo "   • Monitor resolver performance\n";
        echo "   • Use persisted queries for frequently used queries\n\n";
        
        echo "4. Security:\n";
        echo "   • Implement authentication and authorization\n";
        echo "   • Validate all input data\n";
        echo "   • Implement rate limiting\n";
        echo "   • Use query whitelisting for public APIs\n";
        echo "   • Log and monitor suspicious queries\n\n";
        
        echo "5. API Design:\n";
        echo "   • Design queries around use cases\n";
        echo "   • Provide clear error messages\n";
        echo "   • Use consistent field naming\n";
        echo "   • Implement proper versioning\n";
        echo "   • Document your schema thoroughly";
    }
    
    public function runAllExamples(): void
    {
        echo "GraphQL API Development Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateSchema();
        $this->demonstrateQueries();
        $this->demonstrateMutations();
        $this->demonstrateTypeSystem();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runGraphQLApiDemo(): void
{
    $examples = new GraphQLExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runGraphQLApiDemo();
}
?>
