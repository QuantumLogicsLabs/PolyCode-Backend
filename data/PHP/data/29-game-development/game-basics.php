<?php
/**
 * Game Development Basics in PHP
 * 
 * Fundamental game concepts, game loop, and basic mechanics.
 */

// Game Engine Base Class
class GameEngine
{
    protected bool $running = false;
    protected int $fps = 60;
    protected int $frameTime;
    protected array $gameObjects = [];
    protected array $systems = [];
    protected float $deltaTime = 0;
    protected int $lastFrameTime = 0;
    
    public function __construct(int $fps = 60)
    {
        $this->fps = $fps;
        $this->frameTime = 1000000 / $fps; // Microseconds per frame
        $this->lastFrameTime = microtime(true);
    }
    
    public function start(): void
    {
        if ($this->running) {
            return;
        }
        
        $this->running = true;
        echo "Game engine started at {$this->fps} FPS\n";
        $this->initialize();
        $this->gameLoop();
    }
    
    public function stop(): void
    {
        $this->running = false;
        echo "Game engine stopped\n";
    }
    
    protected function initialize(): void
    {
        echo "Initializing game engine...\n";
        $this->addSystem(new InputSystem());
        $this->addSystem(new RenderSystem());
        $this->addSystem(new PhysicsSystem());
        $this->addSystem(new AudioSystem());
    }
    
    protected function gameLoop(): void
    {
        while ($this->running) {
            $currentTime = microtime(true);
            $this->deltaTime = $currentTime - $this->lastFrameTime;
            $this->lastFrameTime = $currentTime;
            
            $this->update($this->deltaTime);
            $this->render();
            
            // Frame rate limiting
            $frameEndTime = microtime(true);
            $frameDuration = ($frameEndTime - $currentTime) * 1000000;
            
            if ($frameDuration < $this->frameTime) {
                usleep($this->frameTime - $frameDuration);
            }
        }
    }
    
    protected function update(float $deltaTime): void
    {
        foreach ($this->systems as $system) {
            $system->update($this->gameObjects, $deltaTime);
        }
    }
    
    protected function render(): void
    {
        $renderSystem = $this->getSystem('RenderSystem');
        if ($renderSystem) {
            $renderSystem->render($this->gameObjects);
        }
    }
    
    public function addGameObject(GameObject $gameObject): void
    {
        $this->gameObjects[$gameObject->getId()] = $gameObject;
        echo "Added game object: {$gameObject->getId()}\n";
    }
    
    public function removeGameObject(string $objectId): void
    {
        if (isset($this->gameObjects[$objectId])) {
            unset($this->gameObjects[$objectId]);
            echo "Removed game object: $objectId\n";
        }
    }
    
    public function addSystem(GameSystem $system): void
    {
        $this->systems[$system->getName()] = $system;
        echo "Added system: {$system->getName()}\n";
    }
    
    public function getSystem(string $name): ?GameSystem
    {
        return $this->systems[$name] ?? null;
    }
    
    public function getGameObject(string $id): ?GameObject
    {
        return $this->gameObjects[$id] ?? null;
    }
    
    public function getGameObjects(): array
    {
        return $this->gameObjects;
    }
    
    public function isRunning(): bool
    {
        return $this->running;
    }
    
    public function getFPS(): int
    {
        return $this->fps;
    }
    
    public function setFPS(int $fps): void
    {
        $this->fps = $fps;
        $this->frameTime = 1000000 / $fps;
    }
}

// Game Object Base Class
class GameObject
{
    protected string $id;
    protected Vector2 $position;
    protected Vector2 $velocity;
    protected Vector2 $scale;
    protected float $rotation;
    protected bool $active;
    protected array $components;
    
    public function __construct(string $id, float $x = 0, float $y = 0)
    {
        $this->id = $id;
        $this->position = new Vector2($x, $y);
        $this->velocity = new Vector2(0, 0);
        $this->scale = new Vector2(1, 1);
        $this->rotation = 0;
        $this->active = true;
        $this->components = [];
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getPosition(): Vector2
    {
        return $this->position;
    }
    
    public function setPosition(Vector2 $position): void
    {
        $this->position = $position;
    }
    
    public function getVelocity(): Vector2
    {
        return $this->velocity;
    }
    
    public function setVelocity(Vector2 $velocity): void
    {
        $this->velocity = $velocity;
    }
    
    public function getScale(): Vector2
    {
        return $this->scale;
    }
    
    public function setScale(Vector2 $scale): void
    {
        $this->scale = $scale;
    }
    
    public function getRotation(): float
    {
        return $this->rotation;
    }
    
    public function setRotation(float $rotation): void
    {
        $this->rotation = $rotation;
    }
    
    public function isActive(): bool
    {
        return $this->active;
    }
    
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
    
    public function addComponent(Component $component): void
    {
        $this->components[$component->getName()] = $component;
        $component->setGameObject($this);
    }
    
    public function getComponent(string $name): ?Component
    {
        return $this->components[$name] ?? null;
    }
    
    public function hasComponent(string $name): bool
    {
        return isset($this->components[$name]);
    }
    
    public function removeComponent(string $name): void
    {
        if (isset($this->components[$name])) {
            unset($this->components[$name]);
        }
    }
    
    public function update(float $deltaTime): void
    {
        foreach ($this->components as $component) {
            $component->update($deltaTime);
        }
    }
    
    public function render(): void
    {
        foreach ($this->components as $component) {
            if ($component instanceof Renderable) {
                $component->render();
            }
        }
    }
    
    public function getTransform(): array
    {
        return [
            'position' => $this->position,
            'velocity' => $this->velocity,
            'scale' => $this->scale,
            'rotation' => $this->rotation
        ];
    }
}

// Component Base Class
abstract class Component
{
    protected GameObject $gameObject;
    protected string $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setGameObject(GameObject $gameObject): void
    {
        $this->gameObject = $gameObject;
    }
    
    public function getGameObject(): GameObject
    {
        return $this->gameObject;
    }
    
    abstract public function update(float $deltaTime): void;
}

// Renderable Interface
interface Renderable
{
    public function render(): void;
}

// Vector2 Class
class Vector2
{
    public float $x;
    public float $y;
    
    public function __construct(float $x = 0, float $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }
    
    public function add(Vector2 $other): Vector2
    {
        return new Vector2($this->x + $other->x, $this->y + $other->y);
    }
    
    public function subtract(Vector2 $other): Vector2
    {
        return new Vector2($this->x - $other->x, $this->y - $other->y);
    }
    
    public function multiply(float $scalar): Vector2
    {
        return new Vector2($this->x * $scalar, $this->y * $scalar);
    }
    
    public function divide(float $scalar): Vector2
    {
        return new Vector2($this->x / $scalar, $this->y / $scalar);
    }
    
    public function magnitude(): float
    {
        return sqrt($this->x * $this->x + $this->y * $this->y);
    }
    
    public function normalize(): Vector2
    {
        $mag = $this->magnitude();
        if ($mag > 0) {
            return $this->divide($mag);
        }
        return new Vector2();
    }
    
    public function dot(Vector2 $other): float
    {
        return $this->x * $other->x + $this->y * $other->y;
    }
    
    public function distance(Vector2 $other): float
    {
        return $this->subtract($other)->magnitude();
    }
    
    public function __toString(): string
    {
        return "({$this->x}, {$this->y})";
    }
}

// Game System Base Class
abstract class GameSystem
{
    protected string $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    abstract public function update(array $gameObjects, float $deltaTime): void;
}

// Input System
class InputSystem extends GameSystem
{
    private array $keys = [];
    private array $mouseButtons = [];
    private Vector2 $mousePosition;
    
    public function __construct()
    {
        parent::__construct('InputSystem');
        $this->mousePosition = new Vector2();
    }
    
    public function update(array $gameObjects, float $deltaTime): void
    {
        // Simulate input handling
        $this->handleKeyboardInput();
        $this->handleMouseInput();
        $this->processInputForGameObjects($gameObjects);
    }
    
    private function handleKeyboardInput(): void
    {
        // Simulate keyboard state
        $this->keys = [
            'w' => rand(0, 1) === 1,
            'a' => rand(0, 1) === 1,
            's' => rand(0, 1) === 1,
            'd' => rand(0, 1) === 1,
            'space' => rand(0, 1) === 1
        ];
    }
    
    private function handleMouseInput(): void
    {
        // Simulate mouse position and buttons
        $this->mousePosition = new Vector2(rand(0, 800), rand(0, 600));
        $this->mouseButtons = [
            'left' => rand(0, 1) === 1,
            'right' => rand(0, 1) === 1,
            'middle' => rand(0, 1) === 1
        ];
    }
    
    private function processInputForGameObjects(array $gameObjects): void
    {
        foreach ($gameObjects as $gameObject) {
            $inputComponent = $gameObject->getComponent('InputComponent');
            if ($inputComponent) {
                $inputComponent->processInput($this->keys, $this->mouseButtons, $this->mousePosition);
            }
        }
    }
    
    public function isKeyPressed(string $key): bool
    {
        return $this->keys[$key] ?? false;
    }
    
    public function isMouseButtonPressed(string $button): bool
    {
        return $this->mouseButtons[$button] ?? false;
    }
    
    public function getMousePosition(): Vector2
    {
        return $this->mousePosition;
    }
}

// Render System
class RenderSystem extends GameSystem
{
    private int $screenWidth;
    private int $screenHeight;
    private array $renderQueue;
    
    public function __construct(int $width = 800, int $height = 600)
    {
        parent::__construct('RenderSystem');
        $this->screenWidth = $width;
        $this->screenHeight = $height;
        $this->renderQueue = [];
    }
    
    public function update(array $gameObjects, float $deltaTime): void
    {
        $this->buildRenderQueue($gameObjects);
        $this->sortRenderQueue();
    }
    
    public function render(array $gameObjects): void
    {
        echo "=== Rendering Frame ===\n";
        echo "Screen: {$this->screenWidth}x{$this->screenHeight}\n";
        
        foreach ($this->renderQueue as $renderable) {
            $renderable->render();
        }
        
        echo "=== End Frame ===\n\n";
    }
    
    private function buildRenderQueue(array $gameObjects): void
    {
        $this->renderQueue = [];
        
        foreach ($gameObjects as $gameObject) {
            if ($gameObject->isActive()) {
                $renderable = $gameObject->getComponent('RenderComponent');
                if ($renderable) {
                    $this->renderQueue[] = $renderable;
                }
            }
        }
    }
    
    private function sortRenderQueue(): void
    {
        // Sort by render layer (depth)
        usort($this->renderQueue, function($a, $b) {
            return $a->getLayer() - $b->getLayer();
        });
    }
    
    public function getScreenSize(): array
    {
        return ['width' => $this->screenWidth, 'height' => $this->screenHeight];
    }
}

// Physics System
class PhysicsSystem extends GameSystem
{
    private Vector2 $gravity;
    private float $friction;
    
    public function __construct(Vector2 $gravity = null, float $friction = 0.98)
    {
        parent::__construct('PhysicsSystem');
        $this->gravity = $gravity ?? new Vector2(0, 9.8);
        $this->friction = $friction;
    }
    
    public function update(array $gameObjects, float $deltaTime): void
    {
        foreach ($gameObjects as $gameObject) {
            $physicsComponent = $gameObject->getComponent('PhysicsComponent');
            if ($physicsComponent) {
                $this->updatePhysics($gameObject, $physicsComponent, $deltaTime);
            }
        }
        
        $this->checkCollisions($gameObjects);
    }
    
    private function updatePhysics(GameObject $gameObject, PhysicsComponent $physics, float $deltaTime): void
    {
        if (!$physics->isStatic()) {
            // Apply gravity
            if ($physics->isAffectedByGravity()) {
                $velocity = $gameObject->getVelocity();
                $gameObject->setVelocity($velocity->add($this->gravity->multiply($deltaTime)));
            }
            
            // Apply friction
            $velocity = $gameObject->getVelocity();
            $gameObject->setVelocity($velocity->multiply($this->friction));
            
            // Update position
            $position = $gameObject->getPosition();
            $velocity = $gameObject->getVelocity();
            $gameObject->setPosition($position->add($velocity->multiply($deltaTime)));
        }
    }
    
    private function checkCollisions(array $gameObjects): void
    {
        $physicsObjects = [];
        
        foreach ($gameObjects as $gameObject) {
            $physicsComponent = $gameObject->getComponent('PhysicsComponent');
            if ($physicsComponent) {
                $physicsObjects[] = $gameObject;
            }
        }
        
        for ($i = 0; $i < count($physicsObjects); $i++) {
            for ($j = $i + 1; $j < count($physicsObjects); $j++) {
                $obj1 = $physicsObjects[$i];
                $obj2 = $physicsObjects[$j];
                
                if ($this->checkCollision($obj1, $obj2)) {
                    $this->resolveCollision($obj1, $obj2);
                }
            }
        }
    }
    
    private function checkCollision(GameObject $obj1, GameObject $obj2): bool
    {
        $pos1 = $obj1->getPosition();
        $pos2 = $obj2->getPosition();
        
        $distance = $pos1->distance($pos2);
        
        // Simple circle collision
        $radius1 = $this->getCollisionRadius($obj1);
        $radius2 = $this->getCollisionRadius($obj2);
        
        return $distance < ($radius1 + $radius2);
    }
    
    private function getCollisionRadius(GameObject $gameObject): float
    {
        $renderComponent = $gameObject->getComponent('RenderComponent');
        if ($renderComponent) {
            return $renderComponent->getRadius();
        }
        
        return 10; // Default radius
    }
    
    private function resolveCollision(GameObject $obj1, GameObject $obj2): void
    {
        $physics1 = $obj1->getComponent('PhysicsComponent');
        $physics2 = $obj2->getComponent('PhysicsComponent');
        
        if ($physics1 && $physics2) {
            // Simple elastic collision
            $vel1 = $obj1->getVelocity();
            $vel2 = $obj2->getVelocity();
            
            $obj1->setVelocity($vel2);
            $obj2->setVelocity($vel1);
            
            echo "Collision resolved between {$obj1->getId()} and {$obj2->getId()}\n";
        }
    }
    
    public function setGravity(Vector2 $gravity): void
    {
        $this->gravity = $gravity;
    }
    
    public function setFriction(float $friction): void
    {
        $this->friction = $friction;
    }
}

// Audio System
class AudioSystem extends GameSystem
{
    private array $sounds = [];
    private float $volume;
    
    public function __construct(float $volume = 1.0)
    {
        parent::__construct('AudioSystem');
        $this->volume = $volume;
    }
    
    public function update(array $gameObjects, float $deltaTime): void
    {
        foreach ($gameObjects as $gameObject) {
            $audioComponent = $gameObject->getComponent('AudioComponent');
            if ($audioComponent) {
                $audioComponent->update($deltaTime);
            }
        }
    }
    
    public function playSound(string $soundName, float $volume = 1.0): void
    {
        echo "Playing sound: $soundName at volume " . ($volume * $this->volume) . "\n";
        $this->sounds[] = ['name' => $soundName, 'volume' => $volume * $this->volume, 'timestamp' => time()];
    }
    
    public function stopSound(string $soundName): void
    {
        echo "Stopping sound: $soundName\n";
    }
    
    public function setVolume(float $volume): void
    {
        $this->volume = max(0, min(1, $volume));
    }
    
    public function getVolume(): float
    {
        return $this->volume;
    }
    
    public function getPlayingSounds(): array
    {
        return $this->sounds;
    }
}

// Input Component
class InputComponent extends Component
{
    public function __construct()
    {
        parent::__construct('InputComponent');
    }
    
    public function update(float $deltaTime): void
    {
        // Input is handled by the InputSystem
    }
    
    public function processInput(array $keys, array $mouseButtons, Vector2 $mousePosition): void
    {
        $gameObject = $this->getGameObject();
        
        // Handle movement
        $velocity = $gameObject->getVelocity();
        $speed = 100;
        
        if ($keys['w']) {
            $velocity->y -= $speed;
        }
        if ($keys['s']) {
            $velocity->y += $speed;
        }
        if ($keys['a']) {
            $velocity->x -= $speed;
        }
        if ($keys['d']) {
            $velocity->x += $speed;
        }
        
        $gameObject->setVelocity($velocity);
        
        // Handle actions
        if ($keys['space']) {
            $this->onAction();
        }
    }
    
    private function onAction(): void
    {
        echo "Action triggered for {$this->getGameObject()->getId()}\n";
    }
}

// Render Component
class RenderComponent extends Component implements Renderable
{
    private string $color;
    private float $radius;
    private int $layer;
    
    public function __construct(string $color = 'white', float $radius = 10, int $layer = 0)
    {
        parent::__construct('RenderComponent');
        $this->color = $color;
        $this->radius = $radius;
        $this->layer = $layer;
    }
    
    public function update(float $deltaTime): void
    {
        // Rendering is handled by the RenderSystem
    }
    
    public function render(): void
    {
        $gameObject = $this->getGameObject();
        $position = $gameObject->getPosition();
        $scale = $gameObject->getScale();
        $rotation = $gameObject->getRotation();
        
        echo "Rendering {$gameObject->getId()}: ";
        echo "Position: {$position}, ";
        echo "Scale: {$scale}, ";
        echo "Rotation: {$rotation}°, ";
        echo "Color: {$this->color}, ";
        echo "Radius: {$this->radius}\n";
    }
    
    public function getColor(): string
    {
        return $this->color;
    }
    
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
    
    public function getRadius(): float
    {
        return $this->radius;
    }
    
    public function setRadius(float $radius): void
    {
        $this->radius = $radius;
    }
    
    public function getLayer(): int
    {
        return $this->layer;
    }
    
    public function setLayer(int $layer): void
    {
        $this->layer = $layer;
    }
}

// Physics Component
class PhysicsComponent extends Component
{
    private bool $static;
    private bool $affectedByGravity;
    private float $mass;
    private bool $collidable;
    
    public function __construct(bool $static = false, bool $affectedByGravity = true, float $mass = 1.0, bool $collidable = true)
    {
        parent::__construct('PhysicsComponent');
        $this->static = $static;
        $this->affectedByGravity = $affectedByGravity;
        $this->mass = $mass;
        $this->collidable = $collidable;
    }
    
    public function update(float $deltaTime): void
    {
        // Physics is handled by the PhysicsSystem
    }
    
    public function isStatic(): bool
    {
        return $this->static;
    }
    
    public function setStatic(bool $static): void
    {
        $this->static = $static;
    }
    
    public function isAffectedByGravity(): bool
    {
        return $this->affectedByGravity;
    }
    
    public function setAffectedByGravity(bool $affectedByGravity): void
    {
        $this->affectedByGravity = $affectedByGravity;
    }
    
    public function getMass(): float
    {
        return $this->mass;
    }
    
    public function setMass(float $mass): void
    {
        $this->mass = $mass;
    }
    
    public function isCollidable(): bool
    {
        return $this->collidable;
    }
    
    public function setCollidable(bool $collidable): void
    {
        $this->collidable = $collidable;
    }
}

// Audio Component
class AudioComponent extends Component
{
    private array $sounds;
    private bool $looping;
    private float $volume;
    
    public function __construct(array $sounds = [], bool $looping = false, float $volume = 1.0)
    {
        parent::__construct('AudioComponent');
        $this->sounds = $sounds;
        $this->looping = $looping;
        $this->volume = $volume;
    }
    
    public function update(float $deltaTime): void
    {
        if ($this->looping && !empty($this->sounds)) {
            // Play looping sound
            $this->playSound($this->sounds[0]);
        }
    }
    
    public function playSound(string $soundName): void
    {
        if (in_array($soundName, $this->sounds)) {
            echo "Playing sound for {$this->getGameObject()->getId()}: $soundName\n";
        }
    }
    
    public function stopSound(): void
    {
        echo "Stopping sound for {$this->getGameObject()->getId()}\n";
    }
    
    public function setLooping(bool $looping): void
    {
        $this->looping = $looping;
    }
    
    public function isLooping(): bool
    {
        return $this->looping;
    }
    
    public function setVolume(float $volume): void
    {
        $this->volume = max(0, min(1, $volume));
    }
    
    public function getVolume(): float
    {
        return $this->volume;
    }
    
    public function addSound(string $soundName): void
    {
        if (!in_array($soundName, $this->sounds)) {
            $this->sounds[] = $soundName;
        }
    }
}

// Game Development Examples
class GameDevelopmentExamples
{
    public function demonstrateBasicGameEngine(): void
    {
        echo "Basic Game Engine Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $game = new GameEngine(60);
        
        // Create game objects
        $player = new GameObject('player', 400, 300);
        $player->addComponent(new InputComponent());
        $player->addComponent(new RenderComponent('blue', 20, 1));
        $player->addComponent(new PhysicsComponent(false, true, 1.0, true));
        $player->addComponent(new AudioComponent(['jump', 'land'], false, 0.8));
        
        $enemy = new GameObject('enemy', 100, 100);
        $enemy->addComponent(new RenderComponent('red', 15, 1));
        $enemy->addComponent(new PhysicsComponent(false, true, 0.5, true));
        
        $platform = new GameObject('platform', 400, 500);
        $platform->addComponent(new RenderComponent('gray', 100, 0));
        $platform->addComponent(new PhysicsComponent(true, false, 0, false));
        
        // Add game objects to engine
        $game->addGameObject($player);
        $game->addGameObject($enemy);
        $game->addGameObject($platform);
        
        echo "\nGame objects added:\n";
        foreach ($game->getGameObjects() as $obj) {
            echo "  {$obj->getId()} at {$obj->getPosition()}\n";
        }
        
        // Simulate a few frames
        echo "\nSimulating game frames...\n";
        
        for ($i = 0; $i < 3; $i++) {
            echo "\nFrame " . ($i + 1) . ":\n";
            $game->update(0.016); // 60 FPS = 0.016 seconds per frame
            $game->render();
        }
        
        // Show game statistics
        echo "\nGame Statistics:\n";
        echo "  FPS: {$game->getFPS()}\n";
        echo "  Running: " . ($game->isRunning() ? 'Yes' : 'No') . "\n";
        echo "  Game Objects: " . count($game->getGameObjects()) . "\n";
        echo "  Systems: " . count(['InputSystem', 'RenderSystem', 'PhysicsSystem', 'AudioSystem']) . "\n";
        
        $game->stop();
    }
    
    public function demonstrateVectorMath(): void
    {
        echo "\nVector Math Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        // Create vectors
        $v1 = new Vector2(3, 4);
        $v2 = new Vector2(1, 2);
        
        echo "Vector 1: $v1\n";
        echo "Vector 2: $v2\n";
        
        // Vector operations
        echo "\nVector Operations:\n";
        
        $add = $v1->add($v2);
        echo "Add: $add\n";
        
        $subtract = $v1->subtract($v2);
        echo "Subtract: $subtract\n";
        
        $multiply = $v1->multiply(2);
        echo "Multiply by 2: $multiply\n";
        
        $divide = $v1->divide(2);
        echo "Divide by 2: $divide\n";
        
        // Vector properties
        echo "\nVector Properties:\n";
        echo "Magnitude: {$v1->magnitude()}\n";
        echo "Normalized: {$v1->normalize()}\n";
        echo "Dot product: {$v1->dot($v2)}\n";
        echo "Distance: {$v1->distance($v2)}\n";
        
        // Vector in game context
        echo "\nGame Context:\n";
        $playerPos = new Vector2(100, 100);
        $enemyPos = new Vector2(200, 150);
        $direction = $enemyPos->subtract($playerPos)->normalize();
        
        echo "Player position: $playerPos\n";
        echo "Enemy position: $enemyPos\n";
        echo "Direction to enemy: $direction\n";
        echo "Distance to enemy: " . $playerPos->distance($enemyPos) . "\n";
    }
    
    public function demonstrateComponents(): void
    {
        echo "\nComponent System Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create game object with multiple components
        $gameObject = new GameObject('hero', 50, 50);
        
        // Add components
        $gameObject->addComponent(new InputComponent());
        $gameObject->addComponent(new RenderComponent('green', 25, 2));
        $gameObject->addComponent(new PhysicsComponent(false, true, 1.5, true));
        $gameObject->addComponent(new AudioComponent(['walk', 'attack', 'hurt'], false, 0.9));
        
        echo "Game Object: {$gameObject->getId()}\n";
        echo "Components: " . implode(', ', array_keys($gameObject->getComponents())) . "\n";
        
        // Test component interactions
        echo "\nComponent Interactions:\n";
        
        // Update game object
        $gameObject->update(0.016);
        
        // Check specific components
        $renderComponent = $gameObject->getComponent('RenderComponent');
        if ($renderComponent) {
            echo "Render component found\n";
            echo "  Color: {$renderComponent->getColor()}\n";
            echo "  Radius: {$renderComponent->getRadius()}\n";
        }
        
        $physicsComponent = $gameObject->getComponent('PhysicsComponent');
        if ($physicsComponent) {
            echo "Physics component found\n";
            echo "  Static: " . ($physicsComponent->isStatic() ? 'Yes' : 'No') . "\n";
            echo "  Mass: {$physicsComponent->getMass()}\n";
            echo "  Affected by gravity: " . ($physicsComponent->isAffectedByGravity() ? 'Yes' : 'No') . "\n";
        }
        
        // Component removal
        echo "\nRemoving AudioComponent...\n";
        $gameObject->removeComponent('AudioComponent');
        echo "Components after removal: " . implode(', ', array_keys($gameObject->getComponents())) . "\n";
        
        // Component existence check
        echo "\nComponent Checks:\n";
        echo "Has RenderComponent: " . ($gameObject->hasComponent('RenderComponent') ? 'Yes' : 'No') . "\n";
        echo "Has AudioComponent: " . ($gameObject->hasComponent('AudioComponent') ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateGameSystems(): void
    {
        echo "\nGame Systems Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $game = new GameEngine(60);
        
        // Create test objects
        $objects = [];
        for ($i = 0; $i < 5; $i++) {
            $obj = new GameObject("object_$i", rand(0, 800), rand(0, 600));
            $obj->addComponent(new RenderComponent(['red', 'green', 'blue', 'yellow', 'purple'][$i], 10 + $i * 5, $i));
            $obj->addComponent(new PhysicsComponent(false, true, 1.0, true));
            
            if ($i % 2 === 0) {
                $obj->addComponent(new AudioComponent(['sound_$i'], false, 0.8));
            }
            
            $objects[] = $obj;
            $game->addGameObject($obj);
        }
        
        echo "Created " . count($objects) . " game objects\n";
        
        // Test individual systems
        echo "\nTesting Systems:\n";
        
        // Input System
        $inputSystem = $game->getSystem('InputSystem');
        if ($inputSystem) {
            echo "Input System:\n";
            echo "  W key pressed: " . ($inputSystem->isKeyPressed('w') ? 'Yes' : 'No') . "\n";
            echo "  Mouse position: {$inputSystem->getMousePosition()}\n";
            echo "  Left mouse button: " . ($inputSystem->isMouseButtonPressed('left') ? 'Yes' : 'No') . "\n";
        }
        
        // Render System
        $renderSystem = $game->getSystem('RenderSystem');
        if ($renderSystem) {
            echo "\nRender System:\n";
            $screenSize = $renderSystem->getScreenSize();
            echo "  Screen size: {$screenSize['width']}x{$screenSize['height']}\n";
        }
        
        // Physics System
        $physicsSystem = $game->getSystem('PhysicsSystem');
        if ($physicsSystem) {
            echo "\nPhysics System:\n";
            echo "  Gravity applied to objects with gravity enabled\n";
            echo "  Friction applied to all moving objects\n";
        }
        
        // Audio System
        $audioSystem = $game->getSystem('AudioSystem');
        if ($audioSystem) {
            echo "\nAudio System:\n";
            echo "  Volume: {$audioSystem->getVolume()}\n";
            echo "  Playing sounds: " . count($audioSystem->getPlayingSounds()) . "\n";
        }
        
        // Simulate game loop
        echo "\nSimulating game loop...\n";
        
        for ($frame = 0; $frame < 2; $frame++) {
            echo "\nFrame " . ($frame + 1) . ":\n";
            $game->update(0.016);
            $game->render();
        }
        
        $game->stop();
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nGame Development Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Game Engine Architecture:\n";
        echo "   • Use component-based architecture\n";
        echo "   • Separate concerns with systems\n";
        echo "   • Implement proper game loop\n";
        echo "   • Use delta time for frame-independent updates\n";
        echo "   • Optimize for performance\n\n";
        
        echo "2. Component Design:\n";
        echo "   • Keep components focused and single-purpose\n";
        echo "   • Use interfaces for common functionality\n";
        echo "   • Implement proper component communication\n";
        echo "   • Avoid circular dependencies\n";
        echo "   • Use composition over inheritance\n\n";
        
        echo "3. System Implementation:\n";
        echo "   • Update systems in proper order\n";
        echo "   • Use efficient data structures\n";
        echo "   • Implement proper error handling\n";
        echo "   • Cache frequently accessed data\n";
        echo "   • Profile and optimize bottlenecks\n\n";
        
        echo "4. Performance Optimization:\n";
        echo "   • Use object pooling for frequently created objects\n";
        echo "   • Implement spatial partitioning for collision detection\n";
        echo "   • Use efficient rendering techniques\n";
        echo "   • Minimize memory allocations\n";
        echo "   • Profile code regularly\n\n";
        
        echo "5. Code Organization:\n";
        echo "   • Use clear naming conventions\n";
        echo "   • Document complex algorithms\n";
        echo "   • Implement proper error handling\n";
        echo "   • Use version control\n";
        echo "   • Write unit tests for critical components";
    }
    
    public function runAllExamples(): void
    {
        echo "Game Development Basics Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateBasicGameEngine();
        $this->demonstrateVectorMath();
        $this->demonstrateComponents();
        $this->demonstrateGameSystems();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runGameBasicsDemo(): void
{
    $examples = new GameDevelopmentExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runGameBasicsDemo();
}
?>
