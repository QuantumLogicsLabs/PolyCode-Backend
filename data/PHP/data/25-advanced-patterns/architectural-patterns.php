<?php
/**
 * Advanced Architectural Design Patterns
 * 
 * Implementation of complex architectural patterns for large-scale applications.
 */

// MVC Pattern Implementation
interface Model
{
    public function getData(): array;
    public function setData(array $data): void;
    public function validate(): bool;
    public function save(): bool;
    public function delete(): bool;
}

interface View
{
    public function render(array $data): string;
    public function setTemplate(string $template): void;
    public function addData(string $key, $value): void;
}

interface Controller
{
    public function handleRequest(array $request): string;
    public function setModel(Model $model): void;
    public function setView(View $view): void;
}

class UserModel implements Model
{
    private array $data = [];
    private array $rules = [
        'name' => 'required|string|min:3|max:50',
        'email' => 'required|email',
        'age' => 'integer|min:18|max:120'
    ];
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function setData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }
    
    public function validate(): bool
    {
        foreach ($this->rules as $field => $rule) {
            if (!$this->validateField($field, $rule)) {
                return false;
            }
        }
        return true;
    }
    
    private function validateField(string $field, string $rule): bool
    {
        $value = $this->data[$field] ?? null;
        
        if (strpos($rule, 'required') !== false && empty($value)) {
            echo "Validation failed: $field is required\n";
            return false;
        }
        
        if ($field === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            echo "Validation failed: $field must be a valid email\n";
            return false;
        }
        
        if ($field === 'age' && $value && (!is_numeric($value) || $value < 18 || $value > 120)) {
            echo "Validation failed: $field must be between 18 and 120\n";
            return false;
        }
        
        if ($field === 'name' && $value && strlen($value) < 3) {
            echo "Validation failed: $field must be at least 3 characters\n";
            return false;
        }
        
        return true;
    }
    
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        
        // Simulate database save
        $this->data['id'] = $this->data['id'] ?? uniqid('user_');
        echo "User saved with ID: {$this->data['id']}\n";
        return true;
    }
    
    public function delete(): bool
    {
        if (!isset($this->data['id'])) {
            echo "Cannot delete user: no ID specified\n";
            return false;
        }
        
        // Simulate database delete
        echo "User deleted: {$this->data['id']}\n";
        return true;
    }
}

class UserView implements View
{
    private string $template = 'default';
    private array $templateData = [];
    
    public function render(array $data): string
    {
        $this->templateData = array_merge($this->templateData, $data);
        
        switch ($this->template) {
            case 'list':
                return $this->renderList();
            case 'form':
                return $this->renderForm();
            case 'detail':
                return $this->renderDetail();
            default:
                return $this->renderDefault();
        }
    }
    
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }
    
    public function addData(string $key, $value): void
    {
        $this->templateData[$key] = $value;
    }
    
    private function renderList(): string
    {
        $html = "<h2>User List</h2>\n";
        $html .= "<table border='1'>\n";
        $html .= "<tr><th>ID</th><th>Name</th><th>Email</th><th>Age</th><th>Actions</th></tr>\n";
        
        foreach ($this->templateData['users'] ?? [] as $user) {
            $html .= "<tr>";
            $html .= "<td>{$user['id']}</td>";
            $html .= "<td>{$user['name']}</td>";
            $html .= "<td>{$user['email']}</td>";
            $html .= "<td>{$user['age']}</td>";
            $html .= "<td><a href='/edit/{$user['id']}'>Edit</a> | <a href='/delete/{$user['id']}'>Delete</a></td>";
            $html .= "</tr>\n";
        }
        
        $html .= "</table>\n";
        return $html;
    }
    
    private function renderForm(): string
    {
        $user = $this->templateData['user'] ?? [];
        $html = "<h2>" . (isset($user['id']) ? 'Edit' : 'Create') . " User</h2>\n";
        $html .= "<form method='post'>\n";
        $html .= "<div>\n";
        $html .= "<label for='name'>Name:</label>\n";
        $html .= "<input type='text' id='name' name='name' value='{$user['name'] ?? ''}' required>\n";
        $html .= "</div>\n";
        $html .= "<div>\n";
        $html .= "<label for='email'>Email:</label>\n";
        $html .= "<input type='email' id='email' name='email' value='{$user['email'] ?? ''}' required>\n";
        $html .= "</div>\n";
        $html .= "<div>\n";
        $html .= "<label for='age'>Age:</label>\n";
        $html .= "<input type='number' id='age' name='age' value='{$user['age'] ?? ''}' min='18' max='120'>\n";
        $html .= "</div>\n";
        $html .= "<div>\n";
        $html .= "<button type='submit'>" . (isset($user['id']) ? 'Update' : 'Create') . "</button>\n";
        $html .= "</div>\n";
        $html .= "</form>\n";
        
        return $html;
    }
    
    private function renderDetail(): string
    {
        $user = $this->templateData['user'] ?? [];
        $html = "<h2>User Details</h2>\n";
        $html .= "<div>\n";
        $html .= "<p><strong>ID:</strong> {$user['id']}</p>\n";
        $html .= "<p><strong>Name:</strong> {$user['name']}</p>\n";
        $html .= "<p><strong>Email:</strong> {$user['email']}</p>\n";
        $html .= "<p><strong>Age:</strong> {$user['age']}</p>\n";
        $html .= "</div>\n";
        $html .= "<a href='/list'>Back to List</a> | <a href='/edit/{$user['id']}'>Edit</a>\n";
        
        return $html;
    }
    
    private function renderDefault(): string
    {
        return "<p>No template specified</p>";
    }
}

class UserController implements Controller
{
    private Model $model;
    private View $view;
    private array $users = [];
    
    public function __construct()
    {
        // Simulate database users
        $this->users = [
            ['id' => 'user_1', 'name' => 'John Doe', 'email' => 'john@example.com', 'age' => 30],
            ['id' => 'user_2', 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'age' => 25]
        ];
    }
    
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }
    
    public function setView(View $view): void
    {
        $this->view = $view;
    }
    
    public function handleRequest(array $request): string
    {
        $action = $request['action'] ?? 'list';
        
        switch ($action) {
            case 'list':
                return $this->listUsers();
            case 'create':
                return $this->createUser($request);
            case 'edit':
                return $this->editUser($request);
            case 'update':
                return $this->updateUser($request);
            case 'delete':
                return $this->deleteUser($request);
            default:
                return "<h1>404 - Action not found</h1>";
        }
    }
    
    private function listUsers(): string
    {
        $this->view->setTemplate('list');
        $this->view->addData('users', $this->users);
        return $this->view->render([]);
    }
    
    private function createUser(array $request): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->setData($request);
            
            if ($this->model->save()) {
                // Add to users list
                $newUser = $this->model->getData();
                $this->users[] = $newUser;
                
                return "<h2>User created successfully!</h2><a href='/list'>Back to List</a>";
            }
        }
        
        $this->view->setTemplate('form');
        return $this->view->render([]);
    }
    
    private function editUser(array $request): string
    {
        $userId = $request['id'] ?? '';
        $user = $this->findUser($userId);
        
        if (!$user) {
            return "<h1>User not found</h1>";
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->setData($request);
            $this->model->setData(['id' => $userId]);
            
            if ($this->model->save()) {
                // Update users list
                $updatedUser = $this->model->getData();
                $this->updateUserInList($userId, $updatedUser);
                
                return "<h2>User updated successfully!</h2><a href='/list'>Back to List</a>";
            }
        }
        
        $this->view->setTemplate('form');
        $this->view->addData('user', $user);
        return $this->view->render([]);
    }
    
    private function updateUser(array $request): string
    {
        return $this->editUser($request);
    }
    
    private function deleteUser(array $request): string
    {
        $userId = $request['id'] ?? '';
        $user = $this->findUser($userId);
        
        if (!$user) {
            return "<h1>User not found</h1>";
        }
        
        $this->model->setData(['id' => $userId]);
        
        if ($this->model->delete()) {
            $this->removeUserFromList($userId);
            return "<h2>User deleted successfully!</h2><a href='/list'>Back to List</a>";
        }
        
        return "<h2>Error deleting user</h2>";
    }
    
    private function findUser(string $id): ?array
    {
        foreach ($this->users as $user) {
            if ($user['id'] === $id) {
                return $user;
            }
        }
        return null;
    }
    
    private function updateUserInList(string $id, array $updatedUser): void
    {
        foreach ($this->users as &$user) {
            if ($user['id'] === $id) {
                $user = $updatedUser;
                break;
            }
        }
    }
    
    private function removeUserFromList(string $id): void
    {
        $this->users = array_filter($this->users, fn($user) => $user['id'] !== $id);
    }
}

// Repository Pattern
interface Repository
{
    public function findById($id): ?object;
    public function findAll(): array;
    public function save(object $entity): bool;
    public function delete(object $entity): bool;
    public function findBy(array $criteria): array;
}

interface Entity
{
    public function getId();
    public function setId($id): void;
    public function toArray(): array;
    public function fromArray(array $data): void;
}

class UserRepository implements Repository
{
    private array $storage = [];
    private string $entityClass;
    
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }
    
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
            $id = uniqid('entity_');
            $entity->setId($id);
        }
        
        $this->storage[$id] = $entity;
        echo "Entity saved with ID: $id\n";
        return true;
    }
    
    public function delete(object $entity): bool
    {
        $id = $entity->getId();
        
        if (!isset($this->storage[$id])) {
            echo "Entity not found: $id\n";
            return false;
        }
        
        unset($this->storage[$id]);
        echo "Entity deleted: $id\n";
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

class User implements Entity
{
    private string $id;
    private string $name;
    private string $email;
    private int $age;
    
    public function __construct(string $name = '', string $email = '', int $age = 0)
    {
        $this->name = $name;
        $this->email = $email;
        $this->age = $age;
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
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    public function getAge(): int
    {
        return $this->age;
    }
    
    public function setAge(int $age): void
    {
        $this->age = $age;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age
        ];
    }
    
    public function fromArray(array $data): void
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->age = $data['age'] ?? 0;
    }
}

// Service Layer Pattern
class UserService
{
    private Repository $repository;
    private array $validators = [];
    
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->initializeValidators();
    }
    
    private function initializeValidators(): void
    {
        $this->validators = [
            'name' => function($value) {
                return is_string($value) && strlen($value) >= 3;
            },
            'email' => function($value) {
                return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL);
            },
            'age' => function($value) {
                return is_int($value) && $value >= 18 && $value <= 120;
            }
        ];
    }
    
    public function createUser(array $data): ?User
    {
        if (!$this->validateData($data)) {
            return null;
        }
        
        $user = new User($data['name'], $data['email'], $data['age']);
        
        if ($this->repository->save($user)) {
            return $user;
        }
        
        return null;
    }
    
    public function updateUser(string $id, array $data): ?User
    {
        $user = $this->repository->findById($id);
        
        if (!$user) {
            return null;
        }
        
        // Update only provided fields
        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['age'])) {
            $user->setAge($data['age']);
        }
        
        if ($this->repository->save($user)) {
            return $user;
        }
        
        return null;
    }
    
    public function deleteUser(string $id): bool
    {
        $user = $this->repository->findById($id);
        
        if (!$user) {
            return false;
        }
        
        return $this->repository->delete($user);
    }
    
    public function getUser(string $id): ?User
    {
        return $this->repository->findById($id);
    }
    
    public function getAllUsers(): array
    {
        return $this->repository->findAll();
    }
    
    public function searchUsers(array $criteria): array
    {
        return $this->repository->findBy($criteria);
    }
    
    private function validateData(array $data): bool
    {
        foreach ($this->validators as $field => $validator) {
            if (isset($data[$field]) && !$validator($data[$field])) {
                echo "Validation failed for field: $field\n";
                return false;
            }
        }
        
        return true;
    }
}

// Dependency Injection Container
class DIContainer
{
    private array $bindings = [];
    private array $instances = [];
    private array $singletons = [];
    
    public function bind(string $interface, string $class, bool $singleton = false): void
    {
        $this->bindings[$interface] = [
            'class' => $class,
            'singleton' => $singleton
        ];
    }
    
    public function instance(string $interface, $instance): void
    {
        $this->instances[$interface] = $instance;
    }
    
    public function resolve(string $interface)
    {
        // Check if instance already exists
        if (isset($this->instances[$interface])) {
            return $this->instances[$interface];
        }
        
        // Check if singleton instance exists
        if (isset($this->singletons[$interface])) {
            return $this->singletons[$interface];
        }
        
        // Check binding
        if (!isset($this->bindings[$interface])) {
            throw new Exception("No binding found for: $interface");
        }
        
        $binding = $this->bindings[$interface];
        $class = $binding['class'];
        
        // Create instance with dependency injection
        $instance = $this->createInstance($class);
        
        if ($binding['singleton']) {
            $this->singletons[$interface] = $instance;
        }
        
        return $instance;
    }
    
    private function createInstance(string $class)
    {
        // Simple reflection-based dependency injection
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            return new $class();
        }
        
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if ($type && $this->isBound($type->getName())) {
                $dependencies[] = $this->resolve($type->getName());
            } else {
                throw new Exception("Unable to resolve dependency: {$type->getName()}");
            }
        }
        
        return $reflection->newInstanceArgs($dependencies);
    }
    
    private function isBound(string $interface): bool
    {
        return isset($this->bindings[$interface]) || isset($this->instances[$interface]);
    }
    
    public function hasBinding(string $interface): bool
    {
        return isset($this->bindings[$interface]);
    }
    
    public function getBindings(): array
    {
        return array_keys($this->bindings);
    }
}

// Architectural Patterns Examples
class ArchitecturalPatternsExamples
{
    public function demonstrateMVC(): void
    {
        echo "MVC Pattern Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Setup MVC components
        $model = new UserModel();
        $view = new UserView();
        $controller = new UserController();
        
        $controller->setModel($model);
        $controller->setView($view);
        
        // Simulate requests
        echo "1. List Users:\n";
        $listResponse = $controller->handleRequest(['action' => 'list']);
        echo substr($listResponse, 0, 200) . "...\n\n";
        
        echo "2. Create User Form:\n";
        $createFormResponse = $controller->handleRequest(['action' => 'create']);
        echo substr($createFormResponse, 0, 200) . "...\n\n";
        
        echo "3. Create User (POST):\n";
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $createResponse = $controller->handleRequest([
            'action' => 'create',
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'age' => 28
        ]);
        echo $createResponse . "\n\n";
        
        echo "4. List Users After Creation:\n";
        $listResponse2 = $controller->handleRequest(['action' => 'list']);
        echo substr($listResponse2, 0, 200) . "...\n\n";
        
        echo "5. Edit User:\n";
        $editResponse = $controller->handleRequest([
            'action' => 'edit',
            'id' => 'user_1'
        ]);
        echo substr($editResponse, 0, 200) . "...\n\n";
        
        echo "6. Delete User:\n";
        $deleteResponse = $controller->handleRequest([
            'action' => 'delete',
            'id' => 'user_2'
        ]);
        echo $deleteResponse . "\n";
    }
    
    public function demonstrateRepository(): void
    {
        echo "\nRepository Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $repository = new UserRepository(User::class);
        
        // Create users
        echo "Creating users:\n";
        $user1 = new User('John Doe', 'john@example.com', 30);
        $user2 = new User('Jane Smith', 'jane@example.com', 25);
        $user3 = new User('Bob Wilson', 'bob@example.com', 35);
        
        $repository->save($user1);
        $repository->save($user2);
        $repository->save($user3);
        
        // Find by ID
        echo "\nFinding user by ID:\n";
        $foundUser = $repository->findById($user1->getId());
        echo "Found: " . $foundUser->getName() . " (" . $foundUser->getEmail() . ")\n";
        
        // Find all
        echo "\nFinding all users:\n";
        $allUsers = $repository->findAll();
        foreach ($allUsers as $user) {
            echo "- " . $user->getName() . " (" . $user->getEmail() . ", age " . $user->getAge() . ")\n";
        }
        
        // Find by criteria
        echo "\nFinding users by criteria (age > 25):\n";
        $criteriaUsers = $repository->findBy(['age' => 30]);
        foreach ($criteriaUsers as $user) {
            echo "- " . $user->getName() . " (age " . $user->getAge() . ")\n";
        }
        
        // Update user
        echo "\nUpdating user:\n";
        $user1->setAge(31);
        $repository->save($user1);
        echo "Updated: " . $user1->getName() . " (age " . $user1->getAge() . ")\n";
        
        // Delete user
        echo "\nDeleting user:\n";
        $repository->delete($user2);
        echo "Deleted: " . $user2->getName() . "\n";
        
        echo "\nFinal user count: " . count($repository->findAll()) . "\n";
    }
    
    public function demonstrateServiceLayer(): void
    {
        echo "\nService Layer Pattern Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $repository = new UserRepository(User::class);
        $userService = new UserService($repository);
        
        // Create users through service
        echo "Creating users through service:\n";
        $user1 = $userService->createUser([
            'name' => 'Alice Brown',
            'email' => 'alice@example.com',
            'age' => 28
        ]);
        
        $user2 = $userService->createUser([
            'name' => 'Charlie Davis',
            'email' => 'charlie@example.com',
            'age' => 32
        ]);
        
        if ($user1) {
            echo "Created: " . $user1->getName() . " (ID: " . $user1->getId() . ")\n";
        }
        
        if ($user2) {
            echo "Created: " . $user2->getName() . " (ID: " . $user2->getId() . ")\n";
        }
        
        // Get all users
        echo "\nGetting all users:\n";
        $allUsers = $userService->getAllUsers();
        foreach ($allUsers as $user) {
            echo "- " . $user->getName() . " (" . $user->getEmail() . ", age " . $user->getAge() . ")\n";
        }
        
        // Search users
        echo "\nSearching users (age >= 30):\n";
        $searchResults = $userService->searchUsers(['age' => 30]);
        foreach ($searchResults as $user) {
            echo "- " . $user->getName() . " (age " . $user->getAge() . ")\n";
        }
        
        // Update user
        echo "\nUpdating user through service:\n";
        $updatedUser = $userService->updateUser($user1->getId(), ['age' => 29]);
        if ($updatedUser) {
            echo "Updated: " . $updatedUser->getName() . " (new age: " . $updatedUser->getAge() . ")\n";
        }
        
        // Delete user
        echo "\nDeleting user through service:\n";
        if ($userService->deleteUser($user2->getId())) {
            echo "Deleted user successfully\n";
        }
        
        echo "\nFinal user count: " . count($userService->getAllUsers()) . "\n";
    }
    
    public function demonstrateDependencyInjection(): void
    {
        echo "\nDependency Injection Container Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        $container = new DIContainer();
        
        // Bind interfaces to implementations
        echo "Binding interfaces:\n";
        $container->bind(Repository::class, UserRepository::class);
        $container->bind(UserService::class, UserService::class);
        
        echo "Bound: " . implode(', ', $container->getBindings()) . "\n";
        
        // Resolve dependencies
        echo "\nResolving dependencies:\n";
        
        // This will automatically inject Repository into UserService
        $userService = $container->resolve(UserService::class);
        echo "Resolved UserService with Repository dependency\n";
        
        // Use the service
        $user = $userService->createUser([
            'name' => 'David Miller',
            'email' => 'david@example.com',
            'age' => 40
        ]);
        
        if ($user) {
            echo "Created user through DI: " . $user->getName() . "\n";
        }
        
        // Singleton binding
        echo "\nTesting singleton binding:\n";
        $container->bind('Logger', 'FileLogger', true);
        
        $logger1 = $container->resolve('Logger');
        $logger2 = $container->resolve('Logger');
        
        echo "Logger 1 ID: " . spl_object_id($logger1) . "\n";
        echo "Logger 2 ID: " . spl_object_id($logger2) . "\n";
        echo "Same instance: " . ($logger1 === $logger2 ? 'Yes' : 'No') . "\n";
        
        // Instance binding
        echo "\nTesting instance binding:\n";
        $config = ['debug' => true, 'log_file' => 'app.log'];
        $container->instance('Config', $config);
        
        $resolvedConfig = $container->resolve('Config');
        echo "Resolved config: " . json_encode($resolvedConfig) . "\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nArchitectural Patterns Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. MVC Pattern:\n";
        echo "   • Keep models focused on business logic\n";
        echo "   • Views should handle presentation only\n";
        echo "   • Controllers coordinate between models and views\n";
        echo "   • Use dependency injection for controllers\n";
        echo "   • Implement proper routing and request handling\n\n";
        
        echo "2. Repository Pattern:\n";
        echo "   • Abstract data access from business logic\n";
        echo "   • Use interfaces for repository contracts\n";
        echo "   • Implement proper error handling\n";
        echo "   • Consider pagination for large datasets\n";
        echo "   • Use unit of work pattern for transactions\n\n";
        
        echo "3. Service Layer:\n";
        echo "   • Encapsulate business logic in services\n";
        echo "   • Use repositories for data access\n";
        echo "   • Implement proper validation\n";
        echo "   • Handle business rules and workflows\n";
        echo "   • Use DTOs for data transfer\n\n";
        
        echo "4. Dependency Injection:\n";
        echo "   • Use constructor injection for dependencies\n";
        echo "   • Bind interfaces to implementations\n";
        echo "   • Use container for object creation\n";
        echo "   • Implement proper lifecycle management\n";
        echo "   • Avoid service locator anti-pattern\n\n";
        
        echo "5. General Guidelines:\n";
        echo "   • Keep layers loosely coupled\n";
        echo "   • Use SOLID principles\n";
        echo "   • Implement proper error handling\n";
        echo "   • Write comprehensive tests\n";
        echo "   • Document architecture decisions";
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Architectural Design Patterns Examples\n";
        echo str_repeat("=", 50) . "\n";
        
        $this->demonstrateMVC();
        $this->demonstrateRepository();
        $this->demonstrateServiceLayer();
        $this->demonstrateDependencyInjection();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runArchitecturalPatternsDemo(): void
{
    $examples = new ArchitecturalPatternsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runArchitecturalPatternsDemo();
}
?>
