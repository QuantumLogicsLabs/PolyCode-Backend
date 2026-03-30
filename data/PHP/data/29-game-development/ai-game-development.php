<?php
/**
 * AI in Game Development in PHP
 * 
 * AI behaviors, pathfinding, decision trees, and game AI systems.
 */

// AI Manager
class AIManager
{
    private array $agents;
    private array $behaviors;
    private array $states;
    private Pathfinding $pathfinding;
    private DecisionTree $decisionTree;
    
    public function __construct()
    {
        $this->agents = [];
        $this->behaviors = [];
        $this->states = [];
        $this->pathfinding = new Pathfinding();
        $this->decisionTree = new DecisionTree();
        
        $this->initializeDefaultBehaviors();
        $this->initializeDefaultStates();
    }
    
    private function initializeDefaultBehaviors(): void
    {
        $this->behaviors = [
            'seek' => SeekBehavior::class,
            'flee' => FleeBehavior::class,
            'arrive' => ArriveBehavior::class,
            'wander' => WanderBehavior::class,
            'pursue' => PursueBehavior::class,
            'evade' => EvadeBehavior::class,
            'follow_path' => FollowPathBehavior::class,
            'attack' => AttackBehavior::class,
            'defend' => DefendBehavior::class,
            'patrol' => PatrolBehavior::class
        ];
    }
    
    private function initializeDefaultStates(): void
    {
        $this->states = [
            'idle' => IdleState::class,
            'patrol' => PatrolState::class,
            'chase' => ChaseState::class,
            'attack' => AttackState::class,
            'flee' => FleeState::class,
            'dead' => DeadState::class,
            'search' => SearchState::class,
            'guard' => GuardState::class
        ];
    }
    
    public function addAgent(AIAgent $agent): void
    {
        $this->agents[$agent->getId()] = $agent;
        echo "Added AI agent: {$agent->getId()}\n";
    }
    
    public function removeAgent(string $agentId): void
    {
        if (isset($this->agents[$agentId])) {
            unset($this->agents[$agentId]);
            echo "Removed AI agent: $agentId\n";
        }
    }
    
    public function getAgent(string $id): ?AIAgent
    {
        return $this->agents[$id] ?? null;
    }
    
    public function update(float $deltaTime): void
    {
        foreach ($this->agents as $agent) {
            $agent->update($deltaTime);
        }
    }
    
    public function createBehavior(string $type, array $params = []): Behavior
    {
        if (!isset($this->behaviors[$type])) {
            throw new Exception("Unknown behavior type: $type");
        }
        
        $behaviorClass = $this->behaviors[$type];
        return new $behaviorClass($params);
    }
    
    public function createState(string $type, array $params = []): State
    {
        if (!isset($this->states[$type])) {
            throw new Exception("Unknown state type: $type");
        }
        
        $stateClass = $this->states[$type];
        return new $stateClass($params);
    }
    
    public function getPathfinding(): Pathfinding
    {
        return $this->pathfinding;
    }
    
    public function getDecisionTree(): DecisionTree
    {
        return $this->decisionTree;
    }
    
    public function getAgents(): array
    {
        return $this->agents;
    }
    
    public function getAgentCount(): int
    {
        return count($this->agents);
    }
}

// AI Agent
class AIAgent
{
    private string $id;
    private Vector2 $position;
    private Vector2 $velocity;
    private Vector2 $acceleration;
    private float $maxSpeed;
    private float $maxForce;
    private array $behaviors;
    private StateMachine $stateMachine;
    private array $memory;
    private float $health;
    private array $sensors;
    
    public function __construct(string $id, Vector2 $position, float $maxSpeed = 100, float $maxForce = 50)
    {
        $this->id = $id;
        $this->position = $position;
        $this->velocity = new Vector2();
        $this->acceleration = new Vector2();
        $this->maxSpeed = $maxSpeed;
        $this->maxForce = $maxForce;
        $this->behaviors = [];
        $this->stateMachine = new StateMachine();
        $this->memory = [];
        $this->health = 100;
        $this->sensors = [];
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
    
    public function getAcceleration(): Vector2
    {
        return $this->acceleration;
    }
    
    public function getMaxSpeed(): float
    {
        return $this->maxSpeed;
    }
    
    public function setMaxSpeed(float $maxSpeed): void
    {
        $this->maxSpeed = $maxSpeed;
    }
    
    public function getMaxForce(): float
    {
        return $this->maxForce;
    }
    
    public function setMaxForce(float $maxForce): void
    {
        $this->maxForce = $maxForce;
    }
    
    public function getHealth(): float
    {
        return $this->health;
    }
    
    public function setHealth(float $health): void
    {
        $this->health = max(0, min(100, $health));
    }
    
    public function isAlive(): bool
    {
        return $this->health > 0;
    }
    
    public function addBehavior(Behavior $behavior): void
    {
        $this->behaviors[$behavior->getName()] = $behavior;
        echo "Added behavior '{$behavior->getName()}' to agent '{$this->id}'\n";
    }
    
    public function removeBehavior(string $behaviorName): void
    {
        if (isset($this->behaviors[$behaviorName])) {
            unset($this->behaviors[$behaviorName]);
            echo "Removed behavior '$behaviorName' from agent '{$this->id}'\n";
        }
    }
    
    public function getBehavior(string $name): ?Behavior
    {
        return $this->behaviors[$name] ?? null;
    }
    
    public function getStateMachine(): StateMachine
    {
        return $this->stateMachine;
    }
    
    public function setMemory(string $key, $value): void
    {
        $this->memory[$key] = $value;
    }
    
    public function getMemory(string $key)
    {
        return $this->memory[$key] ?? null;
    }
    
    public function hasMemory(string $key): bool
    {
        return isset($this->memory[$key]);
    }
    
    public function addSensor(Sensor $sensor): void
    {
        $this->sensors[$sensor->getName()] = $sensor;
        echo "Added sensor '{$sensor->getName()}' to agent '{$this->id}'\n";
    }
    
    public function getSensor(string $name): ?Sensor
    {
        return $this->sensors[$name] ?? null;
    }
    
    public function getSensors(): array
    {
        return $this->sensors;
    }
    
    public function update(float $deltaTime): void
    {
        if (!$this->isAlive()) {
            return;
        }
        
        // Update sensors
        foreach ($this->sensors as $sensor) {
            $sensor->update($deltaTime);
        }
        
        // Update state machine
        $this->stateMachine->update($this, $deltaTime);
        
        // Calculate steering forces from behaviors
        $steeringForce = new Vector2();
        
        foreach ($this->behaviors as $behavior) {
            if ($behavior->isActive()) {
                $force = $behavior->calculate($this, $deltaTime);
                $steeringForce = $steeringForce->add($force);
            }
        }
        
        // Apply steering force
        $this->applyForce($steeringForce);
        
        // Update physics
        $this->updatePhysics($deltaTime);
    }
    
    private function applyForce(Vector2 $force): void
    {
        // Limit force to max force
        if ($force->magnitude() > $this->maxForce) {
            $force = $force->normalize()->multiply($this->maxForce);
        }
        
        $this->acceleration = $this->acceleration->add($force);
    }
    
    private function updatePhysics(float $deltaTime): void
    {
        // Update velocity
        $this->velocity = $this->velocity->add($this->acceleration->multiply($deltaTime));
        
        // Limit velocity to max speed
        if ($this->velocity->magnitude() > $this->maxSpeed) {
            $this->velocity = $this->velocity->normalize()->multiply($this->maxSpeed);
        }
        
        // Update position
        $this->position = $this->position->add($this->velocity->multiply($deltaTime));
        
        // Reset acceleration
        $this->acceleration = new Vector2();
    }
    
    public function seek(Vector2 $target): Vector2
    {
        $desiredVelocity = $target->subtract($this->position)->normalize()->multiply($this->maxSpeed);
        $steeringForce = $desiredVelocity->subtract($this->velocity);
        
        return $steeringForce;
    }
    
    public function flee(Vector2 $target): Vector2
    {
        return $this->seek($target)->multiply(-1);
    }
    
    public function arrive(Vector2 $target, float $slowingRadius = 100): Vector2
    {
        $desiredVelocity = $target->subtract($this->position);
        $distance = $desiredVelocity->magnitude();
        
        if ($distance < $slowingRadius) {
            $desiredVelocity = $desiredVelocity->normalize()->multiply($this->maxSpeed * ($distance / $slowingRadius));
        } else {
            $desiredVelocity = $desiredVelocity->normalize()->multiply($this->maxSpeed);
        }
        
        return $desiredVelocity->subtract($this->velocity);
    }
    
    public function wander(float $wanderRadius = 50, float $wanderDistance = 100, float $wanderJitter = 30): Vector2
    {
        $wanderPoint = $this->position->add($this->velocity->normalize()->multiply($wanderDistance));
        
        $randomAngle = (rand(0, 360) - 180) * M_PI / 180;
        $jitterOffset = new Vector2(cos($randomAngle), sin($randomAngle))->multiply($wanderJitter);
        
        $target = $wanderPoint->add($jitterOffset);
        
        return $this->seek($target);
    }
    
    public function canSee(Vector2 $target, float $visionRange = 200, float $visionAngle = 45): bool
    {
        $distance = $this->position->distance($target);
        
        if ($distance > $visionRange) {
            return false;
        }
        
        $toTarget = $target->subtract($this->position);
        $angle = atan2($toTarget->y, $toTarget->x);
        $velocityAngle = atan2($this->velocity->y, $this->velocity->x);
        
        $angleDiff = abs($angle - $velocityAngle);
        
        return $angleDiff <= ($visionAngle * M_PI / 180);
    }
    
    public function takeDamage(float $damage): void
    {
        $this->health -= $damage;
        echo "Agent {$this->id} took $damage damage, health: {$this->health}\n";
        
        if ($this->health <= 0) {
            echo "Agent {$this->id} died\n";
        }
    }
    
    public function heal(float $amount): void
    {
        $this->health = min(100, $this->health + $amount);
        echo "Agent {$this->id} healed $amount, health: {$this->health}\n";
    }
    
    public function __toString(): string
    {
        return "AIAgent(id: {$this->id}, pos: {$this->position}, health: {$this->health})";
    }
}

// Behavior Base Class
abstract class Behavior
{
    protected string $name;
    protected bool $active;
    protected array $params;
    
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->active = true;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function isActive(): bool
    {
        return $this->active;
    }
    
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
    
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
    
    public function setParam(string $key, $value): void
    {
        $this->params[$key] = $value;
    }
    
    abstract public function calculate(AIAgent $agent, float $deltaTime): Vector2;
}

// Seek Behavior
class SeekBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'seek';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $target = $this->getParam('target');
        
        if (!$target) {
            return new Vector2();
        }
        
        return $agent->seek($target);
    }
}

// Flee Behavior
class FleeBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'flee';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $target = $this->getParam('target');
        
        if (!$target) {
            return new Vector2();
        }
        
        return $agent->flee($target);
    }
}

// Arrive Behavior
class ArriveBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'arrive';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $target = $this->getParam('target');
        $slowingRadius = $this->getParam('slowing_radius', 100);
        
        if (!$target) {
            return new Vector2();
        }
        
        return $agent->arrive($target, $slowingRadius);
    }
}

// Wander Behavior
class WanderBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'wander';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $wanderRadius = $this->getParam('wander_radius', 50);
        $wanderDistance = $this->getParam('wander_distance', 100);
        $wanderJitter = $this->getParam('wander_jitter', 30);
        
        return $agent->wander($wanderRadius, $wanderDistance, $wanderJitter);
    }
}

// Pursue Behavior
class PursueBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'pursue';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $target = $this->getParam('target');
        
        if (!$target) {
            return new Vector2();
        }
        
        // Predict target's future position
        $targetVelocity = $target->getVelocity();
        $distance = $agent->getPosition()->distance($target->getPosition());
        $predictionTime = $distance / $agent->getMaxSpeed();
        
        $futurePosition = $target->getPosition()->add($targetVelocity->multiply($predictionTime));
        
        return $agent->seek($futurePosition);
    }
}

// Evade Behavior
class EvadeBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'evade';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $target = $this->getParam('target');
        
        if (!$target) {
            return new Vector2();
        }
        
        // Predict target's future position
        $targetVelocity = $target->getVelocity();
        $distance = $agent->getPosition()->distance($target->getPosition());
        $predictionTime = $distance / $agent->getMaxSpeed();
        
        $futurePosition = $target->getPosition()->add($targetVelocity->multiply($predictionTime));
        
        return $agent->flee($futurePosition);
    }
}

// Follow Path Behavior
class FollowPathBehavior extends Behavior
{
    private array $path;
    private int $currentWaypoint;
    private float $waypointRadius;
    
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'follow_path';
        $this->path = $params['path'] ?? [];
        $this->currentWaypoint = 0;
        $this->waypointRadius = $params['waypoint_radius'] ?? 20;
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        if (empty($this->path)) {
            return new Vector2();
        }
        
        $target = $this->path[$this->currentWaypoint];
        $distance = $agent->getPosition()->distance($target);
        
        // Check if reached current waypoint
        if ($distance < $this->waypointRadius) {
            $this->currentWaypoint = ($this->currentWaypoint + 1) % count($this->path);
        }
        
        return $agent->seek($target);
    }
    
    public function setPath(array $path): void
    {
        $this->path = $path;
        $this->currentWaypoint = 0;
    }
    
    public function getPath(): array
    {
        return $this->path;
    }
}

// Attack Behavior
class AttackBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'attack';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $target = $this->getParam('target');
        $attackRange = $this->getParam('attack_range', 50);
        
        if (!$target) {
            return new Vector2();
        }
        
        $distance = $agent->getPosition()->distance($target->getPosition());
        
        if ($distance > $attackRange) {
            // Move towards target
            return $agent->seek($target->getPosition());
        } else {
            // In attack range, stop and attack
            $this->performAttack($agent, $target);
            return new Vector2();
        }
    }
    
    private function performAttack(AIAgent $agent, AIAgent $target): void
    {
        $damage = $this->getParam('damage', 10);
        $attackCooldown = $this->getParam('attack_cooldown', 1.0);
        
        $lastAttackTime = $agent->getMemory('last_attack_time') ?? 0;
        $currentTime = microtime(true);
        
        if ($currentTime - $lastAttackTime >= $attackCooldown) {
            $target->takeDamage($damage);
            $agent->setMemory('last_attack_time', $currentTime);
            echo "Agent {$agent->getId()} attacked {$target->getId()} for $damage damage\n";
        }
    }
}

// Defend Behavior
class DefendBehavior extends Behavior
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'defend';
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        $defendPoint = $this->getParam('defend_point');
        $defendRadius = $this->getParam('defend_radius', 100);
        $threats = $this->getParam('threats', []);
        
        if (!$defendPoint) {
            return new Vector2();
        }
        
        // Find nearest threat
        $nearestThreat = null;
        $nearestDistance = PHP_FLOAT_MAX;
        
        foreach ($threats as $threat) {
            $distance = $agent->getPosition()->distance($threat->getPosition());
            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestThreat = $threat;
            }
        }
        
        if ($nearestThreat && $nearestDistance < $defendRadius) {
            // Attack the threat
            $this->setParam('target', $nearestThreat);
            return $agent->seek($nearestThreat->getPosition());
        } else {
            // Return to defend point
            return $agent->arrive($defendPoint, 50);
        }
    }
}

// Patrol Behavior
class PatrolBehavior extends Behavior
{
    private array $patrolPoints;
    private int $currentPoint;
    private float $waitTime;
    private float $currentWaitTime;
    
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->name = 'patrol';
        $this->patrolPoints = $params['patrol_points'] ?? [];
        $this->currentPoint = 0;
        $this->waitTime = $params['wait_time'] ?? 2.0;
        $this->currentWaitTime = 0;
    }
    
    public function calculate(AIAgent $agent, float $deltaTime): Vector2
    {
        if (empty($this->patrolPoints)) {
            return new Vector2();
        }
        
        $target = $this->patrolPoints[$this->currentPoint];
        $distance = $agent->getPosition()->distance($target);
        $arrivalRadius = $this->getParam('arrival_radius', 20);
        
        if ($distance < $arrivalRadius) {
            // Wait at patrol point
            $this->currentWaitTime += $deltaTime;
            
            if ($this->currentWaitTime >= $this->waitTime) {
                $this->currentWaitTime = 0;
                $this->currentPoint = ($this->currentPoint + 1) % count($this->patrolPoints);
            }
            
            return new Vector2();
        } else {
            // Move to patrol point
            return $agent->seek($target);
        }
    }
    
    public function setPatrolPoints(array $points): void
    {
        $this->patrolPoints = $points;
        $this->currentPoint = 0;
    }
}

// State Machine
class StateMachine
{
    private State $currentState;
    private State $globalState;
    private array $states;
    
    public function __construct()
    {
        $this->states = [];
    }
    
    public function addState(string $name, State $state): void
    {
        $this->states[$name] = $state;
        echo "Added state: $name\n";
    }
    
    public function setCurrentState(string $stateName): void
    {
        if (!isset($this->states[$stateName])) {
            throw new Exception("State not found: $stateName");
        }
        
        if (isset($this->currentState)) {
            $this->currentState->exit();
        }
        
        $this->currentState = $this->states[$stateName];
        $this->currentState->enter();
        
        echo "Changed state to: $stateName\n";
    }
    
    public function setGlobalState(string $stateName): void
    {
        if (!isset($this->states[$stateName])) {
            throw new Exception("State not found: $stateName");
        }
        
        $this->globalState = $this->states[$stateName];
        echo "Set global state to: $stateName\n";
    }
    
    public function update(AIAgent $agent, float $deltaTime): void
    {
        // Update global state
        if (isset($this->globalState)) {
            $this->globalState->execute($agent, $deltaTime);
        }
        
        // Update current state
        if (isset($this->currentState)) {
            $this->currentState->execute($agent, $deltaTime);
        }
    }
    
    public function getCurrentState(): ?State
    {
        return $this->currentState ?? null;
    }
    
    public function getState(string $name): ?State
    {
        return $this->states[$name] ?? null;
    }
}

// State Base Class
abstract class State
{
    protected string $name;
    
    public function __construct()
    {
        $this->name = static::class;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function enter(AIAgent $agent): void
    {
        // Called when entering the state
    }
    
    public function execute(AIAgent $agent, float $deltaTime): void
    {
        // Called every frame while in the state
    }
    
    public function exit(AIAgent $agent): void
    {
        // Called when exiting the state
    }
}

// Idle State
class IdleState extends State
{
    private float $idleTime;
    private float $maxIdleTime;
    
    public function __construct(array $params = [])
    {
        parent::__construct();
        $this->maxIdleTime = $params['max_idle_time'] ?? 5.0;
        $this->idleTime = 0;
    }
    
    public function enter(AIAgent $agent): void
    {
        $this->idleTime = 0;
        echo "Agent {$agent->getId()} entered idle state\n";
    }
    
    public function execute(AIAgent $agent, float $deltaTime): void
    {
        $this->idleTime += $deltaTime;
        
        // Look around occasionally
        if (rand(0, 100) < 1) {
            $angle = rand(0, 360) * M_PI / 180;
            $lookDirection = new Vector2(cos($angle), sin($angle));
            $agent->setMemory('look_direction', $lookDirection);
        }
        
        // Transition to patrol after idle time
        if ($this->idleTime >= $this->maxIdleTime) {
            $agent->getStateMachine()->setCurrentState('patrol');
        }
    }
    
    public function exit(AIAgent $agent): void
    {
        echo "Agent {$agent->getId()} exited idle state\n";
    }
}

// Patrol State
class PatrolState extends State
{
    private array $patrolPoints;
    private int $currentPoint;
    private float $waitTime;
    private float $currentWaitTime;
    
    public function __construct(array $params = [])
    {
        parent::__construct();
        $this->patrolPoints = $params['patrol_points'] ?? [
            new Vector2(100, 100),
            new Vector2(700, 100),
            new Vector2(700, 500),
            new Vector2(100, 500)
        ];
        $this->currentPoint = 0;
        $this->waitTime = $params['wait_time'] ?? 2.0;
        $this->currentWaitTime = 0;
    }
    
    public function enter(AIAgent $agent): void
    {
        $this->currentPoint = 0;
        $this->currentWaitTime = 0;
        echo "Agent {$agent->getId()} entered patrol state\n";
    }
    
    public function execute(AIAgent $agent, float $deltaTime): void
    {
        $target = $this->patrolPoints[$this->currentPoint];
        $distance = $agent->getPosition()->distance($target);
        $arrivalRadius = 30;
        
        if ($distance < $arrivalRadius) {
            // Wait at patrol point
            $this->currentWaitTime += $deltaTime;
            
            if ($this->currentWaitTime >= $this->waitTime) {
                $this->currentWaitTime = 0;
                $this->currentPoint = ($this->currentPoint + 1) % count($this->patrolPoints);
            }
        } else {
            // Move to patrol point
            $steering = $agent->seek($target);
            $agent->applyForce($steering);
        }
        
        // Check for threats
        $threats = $agent->getMemory('threats') ?? [];
        foreach ($threats as $threat) {
            if ($agent->canSee($threat->getPosition(), 150)) {
                $agent->setMemory('last_seen_threat', $threat);
                $agent->getStateMachine()->setCurrentState('chase');
                break;
            }
        }
    }
    
    public function exit(AIAgent $agent): void
    {
        echo "Agent {$agent->getId()} exited patrol state\n";
    }
}

// Chase State
class ChaseState extends State
{
    private float $chaseTime;
    private float $maxChaseTime;
    
    public function __construct(array $params = [])
    {
        parent::__construct();
        $this->maxChaseTime = $params['max_chase_time'] ?? 10.0;
        $this->chaseTime = 0;
    }
    
    public function enter(AIAgent $agent): void
    {
        $this->chaseTime = 0;
        echo "Agent {$agent->getId()} entered chase state\n";
    }
    
    public function execute(AIAgent $agent, float $deltaTime): void
    {
        $this->chaseTime += $deltaTime;
        
        $threat = $agent->getMemory('last_seen_threat');
        if ($threat && $agent->canSee($threat->getPosition(), 200)) {
            // Chase the threat
            $steering = $agent->pursue($threat);
            $agent->applyForce($steering);
            
            // Attack if close enough
            $distance = $agent->getPosition()->distance($threat->getPosition());
            if ($distance < 50) {
                $agent->getStateMachine()->setCurrentState('attack');
            }
        } else {
            // Lost sight of threat
            $agent->getStateMachine()->setCurrentState('search');
        }
        
        // Give up chase after max time
        if ($this->chaseTime >= $this->maxChaseTime) {
            $agent->getStateMachine()->setCurrentState('patrol');
        }
    }
    
    public function exit(AIAgent $agent): void
    {
        echo "Agent {$agent->getId()} exited chase state\n";
    }
}

// Attack State
class State
{
    // This is a placeholder - the actual AttackState class should be implemented here
    // For brevity, I'm including a simple version
}

// Search State
class SearchState extends State
{
    private array $searchPoints;
    private int $currentPoint;
    private float $searchTime;
    private float $maxSearchTime;
    
    public function __construct(array $params = [])
    {
        parent::__construct();
        $this->maxSearchTime = $params['max_search_time'] ?? 15.0;
        $this->searchTime = 0;
        $this->currentPoint = 0;
    }
    
    public function enter(AIAgent $agent): void
    {
        $this->searchTime = 0;
        $lastSeenPosition = $agent->getMemory('last_seen_position');
        
        if ($lastSeenPosition) {
            // Generate search points around last seen position
            $this->searchPoints = $this->generateSearchPoints($lastSeenPosition);
        }
        
        echo "Agent {$agent->getId()} entered search state\n";
    }
    
    public function execute(AIAgent $agent, float $deltaTime): void
    {
        $this->searchTime += $deltaTime;
        
        if (!empty($this->searchPoints)) {
            $target = $this->searchPoints[$this->currentPoint];
            $distance = $agent->getPosition()->distance($target);
            
            if ($distance < 30) {
                $this->currentPoint = ($this->currentPoint + 1) % count($this->searchPoints);
            } else {
                $steering = $agent->seek($target);
                $agent->applyForce($steering);
            }
        } else {
            // Wander if no search points
            $steering = $agent->wander();
            $agent->applyForce($steering);
        }
        
        // Check for threats again
        $threats = $agent->getMemory('threats') ?? [];
        foreach ($threats as $threat) {
            if ($agent->canSee($threat->getPosition(), 150)) {
                $agent->setMemory('last_seen_threat', $threat);
                $agent->getStateMachine()->setCurrentState('chase');
                break;
            }
        }
        
        // Give up search after max time
        if ($this->searchTime >= $this->maxSearchTime) {
            $agent->getStateMachine()->setCurrentState('patrol');
        }
    }
    
    private function generateSearchPoints(Vector2 $center): array
    {
        $points = [];
        $radius = 100;
        $numPoints = 8;
        
        for ($i = 0; $i < $numPoints; $i++) {
            $angle = ($i / $numPoints) * 2 * M_PI;
            $x = $center->x + cos($angle) * $radius;
            $y = $center->y + sin($angle) * $radius;
            $points[] = new Vector2($x, $y);
        }
        
        return $points;
    }
    
    public function exit(AIAgent $agent): void
    {
        echo "Agent {$agent->getId()} exited search state\n";
    }
}

// Pathfinding
class Pathfinding
{
    private array $grid;
    private int $width;
    private int $height;
    private array $nodes;
    
    public function __construct(int $width = 50, int $height = 50)
    {
        $this->width = $width;
        $this->height = $height;
        $this->initializeGrid();
    }
    
    private function initializeGrid(): void
    {
        $this->grid = [];
        $this->nodes = [];
        
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $this->grid[$y][$x] = 1; // 1 = walkable, 0 = obstacle
                $this->nodes["$x,$y"] = new PathNode($x, $y);
            }
        }
    }
    
    public function setObstacle(int $x, int $y, bool $isObstacle = true): void
    {
        if ($x >= 0 && $x < $this->width && $y >= 0 && $y < $this->height) {
            $this->grid[$y][$x] = $isObstacle ? 0 : 1;
        }
    }
    
    public function findPath(Vector2 $start, Vector2 $end): array
    {
        $startNode = $this->worldToGrid($start);
        $endNode = $this->worldToGrid($end);
        
        if (!$this->isValidNode($startNode) || !$this->isValidNode($endNode)) {
            return [];
        }
        
        return $this->aStar($startNode, $endNode);
    }
    
    private function worldToGrid(Vector2 $worldPos): Vector2
    {
        return new Vector2(
            floor($worldPos->x / 20), // Assuming 20 pixels per grid cell
            floor($worldPos->y / 20)
        );
    }
    
    private function gridToWorld(Vector2 $gridPos): Vector2
    {
        return new Vector2(
            $gridPos->x * 20 + 10,
            $gridPos->y * 20 + 10
        );
    }
    
    private function isValidNode(Vector2 $node): bool
    {
        $x = floor($node->x);
        $y = floor($node->y);
        
        return $x >= 0 && $x < $this->width && 
               $y >= 0 && $y < $this->height && 
               $this->grid[$y][$x] === 1;
    }
    
    private function aStar(Vector2 $start, Vector2 $end): array
    {
        $openSet = [];
        $closedSet = [];
        $startKey = $this->nodeKey($start);
        $endKey = $this->nodeKey($end);
        
        $startNode = $this->nodes[$startKey];
        $startNode->g = 0;
        $startNode->h = $this->heuristic($start, $end);
        $startNode->f = $startNode->g + $startNode->h;
        
        $openSet[$startKey] = $startNode;
        
        while (!empty($openSet)) {
            // Find node with lowest f score
            $currentKey = $this->getLowestFScore($openSet);
            $current = $openSet[$currentKey];
            
            if ($currentKey === $endKey) {
                return $this->reconstructPath($current);
            }
            
            unset($openSet[$currentKey]);
            $closedSet[$currentKey] = $current;
            
            // Check neighbors
            $neighbors = $this->getNeighbors($current);
            
            foreach ($neighbors as $neighbor) {
                $neighborKey = $this->nodeKey($neighbor);
                
                if (isset($closedSet[$neighborKey])) {
                    continue;
                }
                
                $tentativeG = $current->g + $this->distance($current, $neighbor);
                
                if (!isset($openSet[$neighborKey])) {
                    $openSet[$neighborKey] = $neighbor;
                } elseif ($tentativeG >= $neighbor->g) {
                    continue;
                }
                
                $neighbor->parent = $current;
                $neighbor->g = $tentativeG;
                $neighbor->h = $this->heuristic($neighbor, $end);
                $neighbor->f = $neighbor->g + $neighbor->h;
            }
        }
        
        return []; // No path found
    }
    
    private function nodeKey(Vector2 $node): string
    {
        return floor($node->x) . ',' . floor($node->y);
    }
    
    private function heuristic(Vector2 $a, Vector2 $b): float
    {
        // Manhattan distance
        return abs($a->x - $b->x) + abs($a->y - $b->y);
    }
    
    private function distance(Vector2 $a, Vector2 $b): float
    {
        return sqrt(pow($a->x - $b->x, 2) + pow($a->y - $b->y, 2));
    }
    
    private function getLowestFScore(array $openSet): string
    {
        $lowestKey = null;
        $lowestScore = PHP_FLOAT_MAX;
        
        foreach ($openSet as $key => $node) {
            if ($node->f < $lowestScore) {
                $lowestScore = $node->f;
                $lowestKey = $key;
            }
        }
        
        return $lowestKey;
    }
    
    private function getNeighbors(PathNode $node): array
    {
        $neighbors = [];
        $directions = [
            new Vector2(0, -1), // Up
            new Vector2(1, 0),  // Right
            new Vector2(0, 1),  // Down
            new Vector2(-1, 0)  // Left
        ];
        
        foreach ($directions as $direction) {
            $neighborPos = new Vector2($node->x + $direction->x, $node->y + $direction->y);
            
            if ($this->isValidNode($neighborPos)) {
                $neighborKey = $this->nodeKey($neighborPos);
                $neighbors[] = $this->nodes[$neighborKey];
            }
        }
        
        return $neighbors;
    }
    
    private function reconstructPath(PathNode $endNode): array
    {
        $path = [];
        $current = $endNode;
        
        while ($current !== null) {
            $path[] = $this->gridToWorld(new Vector2($current->x, $current->y));
            $current = $current->parent;
        }
        
        return array_reverse($path);
    }
}

// Path Node
class PathNode
{
    public int $x;
    public int $y;
    public float $g;
    public float $h;
    public float $f;
    public ?PathNode $parent;
    
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->g = 0;
        $this->h = 0;
        $this->f = 0;
        $this->parent = null;
    }
}

// Decision Tree
class DecisionTree
{
    private DecisionNode $root;
    
    public function __construct()
    {
        $this->root = $this->createDefaultTree();
    }
    
    private function createDefaultTree(): DecisionNode
    {
        // Create a simple decision tree for AI behavior
        $root = new DecisionNode('Is enemy visible?', function($agent) {
            $enemies = $agent->getMemory('enemies') ?? [];
            foreach ($enemies as $enemy) {
                if ($agent->canSee($enemy->getPosition(), 200)) {
                    return true;
                }
            }
            return false;
        });
        
        $attackNode = new DecisionNode('Is enemy in attack range?', function($agent) {
            $enemies = $agent->getMemory('enemies') ?? [];
            foreach ($enemies as $enemy) {
                if ($agent->getPosition()->distance($enemy->getPosition()) < 50) {
                    return true;
                }
            }
            return false;
        });
        
        $healthNode = new DecisionNode('Is health low?', function($agent) {
            return $agent->getHealth() < 30;
        });
        
        $root->setTrueChild($attackNode);
        $root->setFalseChild(new ActionNode('Patrol', function($agent) {
            $agent->getStateMachine()->setCurrentState('patrol');
        }));
        
        $attackNode->setTrueChild(new ActionNode('Attack', function($agent) {
            $agent->getStateMachine()->setCurrentState('attack');
        }));
        $attackNode->setFalseChild(new ActionNode('Chase', function($agent) {
            $agent->getStateMachine()->setCurrentState('chase');
        }));
        
        return $root;
    }
    
    public function makeDecision(AIAgent $agent): string
    {
        return $this->root->evaluate($agent);
    }
    
    public function setRoot(DecisionNode $root): void
    {
        $this->root = $root;
    }
}

// Decision Node
class DecisionNode
{
    private string $question;
    private $condition;
    private ?DecisionNode $trueChild;
    private ?DecisionNode $falseChild;
    
    public function __construct(string $question, callable $condition)
    {
        $this->question = $question;
        $this->condition = $condition;
        $this->trueChild = null;
        $this->falseChild = null;
    }
    
    public function setTrueChild(DecisionNode $child): void
    {
        $this->trueChild = $child;
    }
    
    public function setFalseChild(DecisionNode $child): void
    {
        $this->falseChild = $child;
    }
    
    public function evaluate(AIAgent $agent): string
    {
        $result = ($this->condition)($agent);
        
        if ($result && $this->trueChild) {
            return $this->trueChild->evaluate($agent);
        } elseif (!$result && $this->falseChild) {
            return $this->falseChild->evaluate($agent);
        }
        
        return $this->question;
    }
}

// Action Node
class ActionNode extends DecisionNode
{
    private string $action;
    private $callback;
    
    public function __construct(string $action, callable $callback)
    {
        parent::__construct($action, function() { return true; });
        $this->action = $action;
        $this->callback = $callback;
    }
    
    public function evaluate(AIAgent $agent): string
    {
        ($this->callback)($agent);
        return $this->action;
    }
}

// Sensor
class Sensor
{
    protected string $name;
    protected float $range;
    protected float $angle;
    
    public function __construct(string $name, float $range, float $angle = 360)
    {
        $this->name = $name;
        $this->range = $range;
        $this->angle = $angle;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getRange(): float
    {
        return $this->range;
    }
    
    public function getAngle(): float
    {
        return $this->angle;
    }
    
    public function update(float $deltaTime): void
    {
        // Override in subclasses
    }
    
    public function detect(AIAgent $agent, array $objects): array
    {
        $detected = [];
        
        foreach ($objects as $object) {
            if ($this->canDetect($agent, $object)) {
                $detected[] = $object;
            }
        }
        
        return $detected;
    }
    
    protected function canDetect(AIAgent $agent, $object): bool
    {
        // Basic detection logic
        $distance = $agent->getPosition()->distance($object->getPosition());
        
        if ($distance > $this->range) {
            return false;
        }
        
        if ($this->angle < 360) {
            $toObject = $object->getPosition()->subtract($agent->getPosition());
            $angle = atan2($toObject->y, $toObject->x);
            $velocityAngle = atan2($agent->getVelocity()->y, $agent->getVelocity()->x);
            
            $angleDiff = abs($angle - $velocityAngle);
            
            if ($angleDiff > ($this->angle * M_PI / 180)) {
                return false;
            }
        }
        
        return true;
    }
}

// Vision Sensor
class VisionSensor extends Sensor
{
    public function __construct(float $range = 200, float $angle = 45)
    {
        parent::__construct('vision', $range, $angle);
    }
    
    public function update(float $deltaTime): void
    {
        // Vision sensor update logic
    }
}

// Hearing Sensor
class HearingSensor extends Sensor
{
    public function __construct(float $range = 150)
    {
        parent::__construct('hearing', $range, 360);
    }
    
    public function update(float $deltaTime): void
    {
        // Hearing sensor update logic
    }
}

// AI Game Development Examples
class AIGameDevelopmentExamples
{
    public function demonstrateBasicAI(): void
    {
        echo "Basic AI Demo\n";
        echo str_repeat("-", 15) . "\n";
        
        $aiManager = new AIManager();
        
        // Create AI agents
        $player = new AIAgent('player', new Vector2(400, 300), 80, 40);
        $enemy = new AIAgent('enemy', new Vector2(100, 100), 60, 30);
        $npc = new AIAgent('npc', new Vector2(600, 400), 50, 25);
        
        $aiManager->addAgent($player);
        $aiManager->addAgent($enemy);
        $aiManager->addAgent($npc);
        
        // Add behaviors
        $enemy->addBehavior($aiManager->createBehavior('seek', ['target' => $player->getPosition()]));
        $npc->addBehavior($aiManager->createBehavior('wander'));
        
        // Add sensors
        $enemy->addSensor(new VisionSensor(200, 45));
        $npc->addSensor(new VisionSensor(150, 90));
        
        echo "Created AI agents:\n";
        echo "  Player: {$player->getPosition()}, speed={$player->getMaxSpeed()}\n";
        echo "  Enemy: {$enemy->getPosition()}, speed={$enemy->getMaxSpeed()}\n";
        echo "  NPC: {$npc->getPosition()}, speed={$npc->getMaxSpeed()}\n";
        
        // Simulate AI behavior
        echo "\nSimulating AI behavior:\n";
        
        for ($i = 0; $i < 20; $i++) {
            $aiManager->update(0.1);
            
            if ($i % 5 === 0) {
                echo "  Time " . ($i * 0.1) . "s:\n";
                echo "    Player: {$player->getPosition()}\n";
                echo "    Enemy: {$enemy->getPosition()}\n";
                echo "    NPC: {$npc->getPosition()}\n";
            }
        }
    }
    
    public function demonstrateStateMachine(): void
    {
        echo "\nState Machine Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $aiManager = new AIManager();
        
        // Create guard agent
        $guard = new AIAgent('guard', new Vector2(400, 300), 70, 35);
        $aiManager->addAgent($guard);
        
        // Create state machine
        $stateMachine = $guard->getStateMachine();
        
        // Add states
        $stateMachine->addState('idle', new IdleState(['max_idle_time' => 3.0]));
        $stateMachine->addState('patrol', new PatrolState([
            'patrol_points' => [
                new Vector2(100, 100),
                new Vector2(700, 100),
                new Vector2(700, 500),
                new Vector2(100, 500)
            ],
            'wait_time' => 1.5
        ]));
        $stateMachine->addState('chase', new ChaseState(['max_chase_time' => 8.0]));
        $stateMachine->addState('search', new SearchState(['max_search_time' => 12.0]));
        
        // Set initial state
        $stateMachine->setCurrentState('idle');
        
        // Add vision sensor
        $guard->addSensor(new VisionSensor(200, 60));
        
        // Create intruder
        $intruder = new AIAgent('intruder', new Vector2(50, 50), 80, 40);
        $aiManager->addAgent($intruder);
        
        // Set up memory
        $guard->setMemory('threats', [$intruder]);
        
        echo "Created guard with state machine:\n";
        echo "  Initial state: idle\n";
        echo "  Patrol points: 4\n";
        echo "  Vision range: 200\n";
        echo "  Intruder at: {$intruder->getPosition()}\n";
        
        // Simulate state machine behavior
        echo "\nSimulating state machine:\n";
        
        for ($i = 0; $i < 30; $i++) {
            $aiManager->update(0.1);
            
            if ($i % 6 === 0) {
                echo "  Time " . ($i * 0.1) . "s:\n";
                echo "    Guard: {$guard->getPosition()}, state: " . ($stateMachine->getCurrentState()?->getName() ?? 'None') . "\n";
                echo "    Intruder: {$intruder->getPosition()}\n";
                
                // Move intruder occasionally
                if ($i % 12 === 0) {
                    $newPos = new Vector2(rand(50, 750), rand(50, 550));
                    $intruder->setPosition($newPos);
                    echo "    Intruder moved to: $newPos\n";
                }
            }
        }
    }
    
    public function demonstratePathfinding(): void
    {
        echo "\nPathfinding Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $aiManager = new AIManager();
        
        // Create pathfinding system
        $pathfinding = $aiManager->getPathfinding();
        
        // Set up obstacles
        $pathfinding->setObstacle(10, 10, true);
        $pathfinding->setObstacle(11, 10, true);
        $pathfinding->setObstacle(12, 10, true);
        $pathfinding->setObstacle(10, 11, true);
        $pathfinding->setObstacle(10, 12, true);
        
        $pathfinding->setObstacle(20, 5, true);
        $pathfinding->setObstacle(20, 6, true);
        $pathfinding->setObstacle(20, 7, true);
        
        // Create agent
        $agent = new AIAgent('pathfinder', new Vector2(50, 50), 60, 30);
        $aiManager->addAgent($agent);
        
        // Find path
        $start = new Vector2(50, 50);
        $end = new Vector2(750, 550);
        
        $path = $pathfinding->findPath($start, $end);
        
        echo "Pathfinding from $start to $end:\n";
        echo "  Path length: " . count($path) . " waypoints\n";
        echo "  First waypoint: " . ($path[0] ?? 'None') . "\n";
        echo "  Last waypoint: " . (end($path) ?? 'None') . "\n";
        
        if (!empty($path)) {
            // Add follow path behavior
            $followPathBehavior = $aiManager->createBehavior('follow_path', [
                'path' => $path,
                'waypoint_radius' => 25
            ]);
            $agent->addBehavior($followPathBehavior);
            
            // Simulate path following
            echo "\nSimulating path following:\n";
            
            for ($i = 0; $i < 40; $i++) {
                $aiManager->update(0.1);
                
                if ($i % 8 === 0) {
                    echo "  Time " . ($i * 0.1) . "s: {$agent->getPosition()}\n";
                }
            }
            
            echo "\nFinal position: {$agent->getPosition()}\n";
            echo "Distance to target: " . $agent->getPosition()->distance($end) . "\n";
        }
    }
    
    public function demonstrateDecisionTree(): void
    {
        echo "\nDecision Tree Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $aiManager = new AIManager();
        
        // Create agent
        $agent = new AIAgent('decision_agent', new Vector2(400, 300), 80, 40);
        $aiManager->addAgent($agent);
        
        // Create enemies
        $enemy1 = new AIAgent('enemy1', new Vector2(300, 200), 60, 30);
        $enemy2 = new AIAgent('enemy2', new Vector2(500, 400), 60, 30);
        $aiManager->addAgent($enemy1);
        $aiManager->addAgent($enemy2);
        
        // Set up agent memory
        $agent->setMemory('enemies', [$enemy1, $enemy2]);
        
        // Create decision tree
        $decisionTree = $aiManager->getDecisionTree();
        
        // Test decision making
        echo "Testing decision tree:\n";
        
        $scenarios = [
            ['health' => 80, 'enemy_distance' => 150],
            ['health' => 80, 'enemy_distance' => 30],
            ['health' => 20, 'enemy_distance' => 150],
            ['health' => 20, 'enemy_distance' => 30]
        ];
        
        foreach ($scenarios as $i => $scenario) {
            $agent->setHealth($scenario['health']);
            
            // Position enemies based on distance
            $angle = rand(0, 360) * M_PI / 180;
            $enemyPos = $agent->getPosition()->add(
                new Vector2(cos($angle), sin($angle))->multiply($scenario['enemy_distance'])
            );
            $enemy1->setPosition($enemyPos);
            
            echo "\nScenario " . ($i + 1) . ":\n";
            echo "  Health: {$agent->getHealth()}\n";
            echo "  Enemy distance: {$scenario['enemy_distance']}\n";
            
            $decision = $decisionTree->makeDecision($agent);
            echo "  Decision: $decision\n";
        }
    }
    
    public function demonstrateAdvancedAI(): void
    {
        echo "\nAdvanced AI Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $aiManager = new AIManager();
        
        // Create squad of AI agents
        $squad = [];
        $positions = [
            new Vector2(300, 300),
            new Vector2(320, 280),
            new Vector2(320, 320),
            new Vector2(340, 300)
        ];
        
        foreach ($positions as $i => $pos) {
            $soldier = new AIAgent("soldier_$i", $pos, 70, 35);
            $soldier->addSensor(new VisionSensor(180, 90));
            $soldier->addSensor(new HearingSensor(120));
            
            // Add behaviors
            $soldier->addBehavior($aiManager->createBehavior('defend', [
                'defend_point' => new Vector2(400, 300),
                'defend_radius' => 200,
                'attack_range' => 60,
                'damage' => 15
            ]));
            
            // Set up state machine
            $stateMachine = $soldier->getStateMachine();
            $stateMachine->addState('idle', new IdleState(['max_idle_time' => 2.0]));
            $stateMachine->addState('patrol', new PatrolState([
                'patrol_points' => [
                    new Vector2(200, 200),
                    new Vector2(600, 200),
                    new Vector2(600, 400),
                    new Vector2(200, 400)
                ]
            ]));
            $stateMachine->setCurrentState('patrol');
            
            $aiManager->addAgent($soldier);
            $squad[] = $soldier;
        }
        
        // Create enemies
        $enemies = [];
        for ($i = 0; $i < 3; $i++) {
            $enemy = new AIAgent("enemy_$i", new Vector2(rand(100, 700), rand(100, 500)), 50, 25);
            $enemy->addBehavior($aiManager->createBehavior('attack', [
                'attack_range' => 50,
                'damage' => 10,
                'attack_cooldown' => 1.5
            ]));
            
            $aiManager->addAgent($enemy);
            $enemies[] = $enemy;
        }
        
        echo "Created AI squad:\n";
        echo "  Soldiers: " . count($squad) . "\n";
        echo "  Enemies: " . count($enemies) . "\n";
        
        // Set up squad coordination
        foreach ($squad as $soldier) {
            $soldier->setMemory('squad', $squad);
            $soldier->setMemory('enemies', $enemies);
        }
        
        // Simulate advanced AI
        echo "\nSimulating advanced AI:\n";
        
        for ($i = 0; $i < 50; $i++) {
            $aiManager->update(0.1);
            
            if ($i % 10 === 0) {
                echo "  Time " . ($i * 0.1) . "s:\n";
                
                foreach ($squad as $j => $soldier) {
                    echo "    Soldier $j: {$soldier->getPosition()}, health={$soldier->getHealth()}, state=" . ($soldier->getStateMachine()->getCurrentState()?->getName() ?? 'None') . "\n";
                }
                
                // Show squad cohesion
                $centerOfMass = new Vector2();
                foreach ($squad as $soldier) {
                    $centerOfMass = $centerOfMass->add($soldier->getPosition());
                }
                $centerOfMass = $centerOfMass->divide(count($squad));
                
                echo "    Squad center: $centerOfMass\n";
            }
            
            // Random enemy movement
            if ($i % 15 === 0) {
                foreach ($enemies as $enemy) {
                    $newPos = $enemy->getPosition()->add(
                        new Vector2(rand(-50, 50), rand(-50, 50))
                    );
                    $enemy->setPosition($newPos);
                }
            }
        }
        
        // Show final statistics
        echo "\nFinal statistics:\n";
        $aliveSoldiers = array_filter($squad, fn($s) => $s->isAlive());
        $aliveEnemies = array_filter($enemies, fn($e) => $e->isAlive());
        
        echo "  Alive soldiers: " . count($aliveSoldiers) . "/" . count($squad) . "\n";
        echo "  Alive enemies: " . count($aliveEnemies) . "/" . count($enemies) . "\n";
        
        if (!empty($aliveSoldiers)) {
            $avgHealth = array_sum(array_map(fn($s) => $s->getHealth(), $aliveSoldiers)) / count($aliveSoldiers);
            echo "  Average soldier health: " . round($avgHealth, 1) . "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nAI Development Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. AI Architecture:\n";
        echo "   • Use modular AI components\n";
        echo "   • Separate decision making from execution\n";
        echo "   • Implement proper state management\n";
        echo "   • Use behavior trees for complex logic\n";
        echo "   • Implement AI debugging tools\n\n";
        
        echo "2. Pathfinding:\n";
        echo "   • Use A* for optimal pathfinding\n";
        echo "   • Implement path smoothing\n";
        echo "   • Use hierarchical pathfinding\n";
        echo "   • Cache frequently used paths\n";
        echo "   • Handle dynamic obstacles\n\n";
        
        echo "3. State Machines:\n";
        echo "   • Keep states simple and focused\n";
        echo "   • Use global states for common behaviors\n";
        echo "   • Implement proper state transitions\n";
        echo "   • Use state history for debugging\n";
        echo "   • Avoid deep state hierarchies\n\n";
        
        echo "4. Behavior Systems:\n";
        echo "   • Use weighted steering behaviors\n";
        echo "   • Implement behavior prioritization\n";
        echo "   • Use context-aware behaviors\n";
        echo "   • Implement behavior blending\n";
        echo "   • Use behavior trees for complex logic\n\n";
        
        echo "5. Performance:\n";
        echo "   • Use spatial partitioning for perception\n";
        echo "   • Implement AI level of detail\n";
        echo "   • Use AI update frequency control\n";
        echo "   • Cache AI calculations\n";
        echo "   • Profile AI performance bottlenecks";
    }
    
    public function runAllExamples(): void
    {
        echo "AI Game Development Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateBasicAI();
        $this->demonstrateStateMachine();
        $this->demonstratePathfinding();
        $this->demonstrateDecisionTree();
        $this->demonstrateAdvancedAI();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runAIGameDevelopmentDemo(): void
{
    $examples = new AIGameDevelopmentExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runAIGameDevelopmentDemo();
}
?>
