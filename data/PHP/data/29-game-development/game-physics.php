<?php
/**
 * Game Physics in PHP
 * 
 * Physics simulation, collision detection, and physics-based gameplay mechanics.
 */

// Physics Engine
class PhysicsEngine
{
    private Vector2 $gravity;
    private array $bodies;
    private array $colliders;
    private array $joints;
    private float $timeStep;
    private int $velocityIterations;
    private int $positionIterations;
    private bool $allowSleep;
    
    public function __construct(Vector2 $gravity = null, float $timeStep = 1/60)
    {
        $this->gravity = $gravity ?? new Vector2(0, 9.8);
        $this->timeStep = $timeStep;
        $this->velocityIterations = 6;
        $this->positionIterations = 2;
        $this->allowSleep = true;
        
        $this->bodies = [];
        $this->colliders = [];
        $this->joints = [];
    }
    
    public function addBody(PhysicsBody $body): void
    {
        $this->bodies[$body->getId()] = $body;
        echo "Added physics body: {$body->getId()}\n";
    }
    
    public function removeBody(string $bodyId): void
    {
        if (isset($this->bodies[$bodyId])) {
            unset($this->bodies[$bodyId]);
            echo "Removed physics body: $bodyId\n";
        }
    }
    
    public function addCollider(Collider $collider): void
    {
        $this->colliders[$collider->getId()] = $collider;
        echo "Added collider: {$collider->getId()}\n";
    }
    
    public function removeCollider(string $colliderId): void
    {
        if (isset($this->colliders[$colliderId])) {
            unset($this->colliders[$colliderId]);
            echo "Removed collider: $colliderId\n";
        }
    }
    
    public function addJoint(Joint $joint): void
    {
        $this->joints[$joint->getId()] = $joint;
        echo "Added joint: {$joint->getId()}\n";
    }
    
    public function removeJoint(string $jointId): void
    {
        if (isset($this->joints[$jointId])) {
            unset($this->joints[$jointId]);
            echo "Removed joint: $jointId\n";
        }
    }
    
    public function step(float $deltaTime): void
    {
        // Update physics simulation
        $this->integrateForces($deltaTime);
        $this->integrateVelocities($deltaTime);
        $this->detectCollisions();
        $this->resolveCollisions();
        $this->solveJoints();
        $this->integratePositions($deltaTime);
    }
    
    private function integrateForces(float $deltaTime): void
    {
        foreach ($this->bodies as $body) {
            if ($body->isDynamic()) {
                // Apply gravity
                if ($body->isAffectedByGravity()) {
                    $force = $this->gravity->multiply($body->getMass());
                    $body->applyForce($force);
                }
                
                // Update velocity from forces
                $acceleration = $body->getForce()->divide($body->getMass());
                $velocity = $body->getVelocity()->add($acceleration->multiply($deltaTime));
                $body->setVelocity($velocity);
                
                // Clear forces
                $body->clearForces();
            }
        }
    }
    
    private function integrateVelocities(float $deltaTime): void
    {
        foreach ($this->bodies as $body) {
            if ($body->isDynamic()) {
                // Apply damping
                $velocity = $body->getVelocity()->multiply($body->getDamping());
                $body->setVelocity($velocity);
                
                // Update position
                $position = $body->getPosition()->add($body->getVelocity()->multiply($deltaTime));
                $body->setPosition($position);
            }
        }
    }
    
    private function detectCollisions(): void
    {
        foreach ($this->colliders as $collider) {
            $collider->clearCollisions();
        }
        
        $colliderArray = array_values($this->colliders);
        
        for ($i = 0; $i < count($colliderArray); $i++) {
            for ($j = $i + 1; $j < count($colliderArray); $j++) {
                $colliderA = $colliderArray[$i];
                $colliderB = $colliderArray[$j];
                
                if ($this->checkCollision($colliderA, $colliderB)) {
                    $collision = new Collision($colliderA, $colliderB);
                    $colliderA->addCollision($collision);
                    $colliderB->addCollision($collision);
                }
            }
        }
    }
    
    private function checkCollision(Collider $a, Collider $b): bool
    {
        // Simple AABB collision
        if ($a instanceof BoxCollider && $b instanceof BoxCollider) {
            return $this->checkBoxCollision($a, $b);
        }
        
        // Circle collision
        if ($a instanceof CircleCollider && $b instanceof CircleCollider) {
            return $this->checkCircleCollision($a, $b);
        }
        
        // Box-Circle collision
        if ($a instanceof BoxCollider && $b instanceof CircleCollider) {
            return $this->checkBoxCircleCollision($a, $b);
        }
        
        if ($a instanceof CircleCollider && $b instanceof BoxCollider) {
            return $this->checkBoxCircleCollision($b, $a);
        }
        
        return false;
    }
    
    private function checkBoxCollision(BoxCollider $a, BoxCollider $b): bool
    {
        $posA = $a->getPosition();
        $sizeA = $a->getSize();
        $posB = $b->getPosition();
        $sizeB = $b->getSize();
        
        return $posA->x < $posB->x + $sizeB->x &&
               $posA->x + $sizeA->x > $posB->x &&
               $posA->y < $posB->y + $sizeB->y &&
               $posA->y + $sizeA->y > $posB->y;
    }
    
    private function checkCircleCollision(CircleCollider $a, CircleCollider $b): bool
    {
        $distance = $a->getPosition()->distance($b->getPosition());
        $radiusSum = $a->getRadius() + $b->getRadius();
        
        return $distance < $radiusSum;
    }
    
    private function checkBoxCircleCollision(BoxCollider $box, CircleCollider $circle): bool
    {
        $circlePos = $circle->getPosition();
        $boxPos = $box->getPosition();
        $boxSize = $box->getSize();
        
        // Find closest point on box to circle center
        $closestX = max($boxPos->x, min($circlePos->x, $boxPos->x + $boxSize->x));
        $closestY = max($boxPos->y, min($circlePos->y, $boxPos->y + $boxSize->y));
        
        // Check if closest point is inside circle
        $closestPoint = new Vector2($closestX, $closestY);
        $distance = $circlePos->distance($closestPoint);
        
        return $distance < $circle->getRadius();
    }
    
    private function resolveCollisions(): void
    {
        foreach ($this->colliders as $collider) {
            $collisions = $collider->getCollisions();
            
            foreach ($collisions as $collision) {
                $this->resolveCollision($collision);
            }
        }
    }
    
    private function resolveCollision(Collision $collision): void
    {
        $colliderA = $collision->getColliderA();
        $colliderB = $collision->getColliderB();
        
        $bodyA = $colliderA->getBody();
        $bodyB = $colliderB->getBody();
        
        if (!$bodyA->isDynamic() && !$bodyB->isDynamic()) {
            return; // Both are static, no resolution needed
        }
        
        // Calculate collision normal
        $normal = $colliderB->getPosition()->subtract($colliderA->getPosition())->normalize();
        
        // Calculate relative velocity
        $relativeVelocity = $bodyB->getVelocity()->subtract($bodyA->getVelocity());
        
        // Calculate relative velocity along collision normal
        $velocityAlongNormal = $relativeVelocity->dot($normal);
        
        // Don't resolve if velocities are separating
        if ($velocityAlongNormal > 0) {
            return;
        }
        
        // Calculate restitution (bounciness)
        $restitution = min($bodyA->getRestitution(), $bodyB->getRestitution());
        
        // Calculate impulse scalar
        $impulseScalar = -(1 + $restitution) * $velocityAlongNormal;
        $impulseScalar /= 1 / $bodyA->getMass() + 1 / $bodyB->getMass();
        
        // Apply impulse
        $impulse = $normal->multiply($impulseScalar);
        
        if ($bodyA->isDynamic()) {
            $velocityA = $bodyA->getVelocity()->subtract($impulse->divide($bodyA->getMass()));
            $bodyA->setVelocity($velocityA);
        }
        
        if ($bodyB->isDynamic()) {
            $velocityB = $bodyB->getVelocity()->add($impulse->divide($bodyB->getMass()));
            $bodyB->setVelocity($velocityB);
        }
        
        // Position correction to prevent objects from sinking
        $penetrationDepth = $this->calculatePenetrationDepth($colliderA, $colliderB);
        if ($penetrationDepth > 0) {
            $correction = $normal->multiply($penetrationDepth * 0.5);
            
            if ($bodyA->isDynamic()) {
                $positionA = $bodyA->getPosition()->subtract($correction);
                $bodyA->setPosition($positionA);
            }
            
            if ($bodyB->isDynamic()) {
                $positionB = $bodyB->getPosition()->add($correction);
                $bodyB->setPosition($positionB);
            }
        }
    }
    
    private function calculatePenetrationDepth(Collider $a, Collider $b): float
    {
        // Simplified penetration depth calculation
        $distance = $a->getPosition()->distance($b->getPosition());
        
        if ($a instanceof CircleCollider && $b instanceof CircleCollider) {
            $radiusSum = $a->getRadius() + $b->getRadius();
            return max(0, $radiusSum - $distance);
        }
        
        return 0;
    }
    
    private function solveJoints(): void
    {
        foreach ($this->joints as $joint) {
            $joint->solve();
        }
    }
    
    private function integratePositions(float $deltaTime): void
    {
        foreach ($this->bodies as $body) {
            if ($body->isDynamic()) {
                $position = $body->getPosition()->add($body->getVelocity()->multiply($deltaTime));
                $body->setPosition($position);
            }
        }
    }
    
    public function raycast(Vector2 $origin, Vector2 $direction, float $maxDistance): ?RaycastHit
    {
        $closestHit = null;
        $closestDistance = $maxDistance;
        
        foreach ($this->colliders as $collider) {
            $hit = $this->raycastCollider($origin, $direction, $maxDistance, $collider);
            
            if ($hit && $hit->distance < $closestDistance) {
                $closestHit = $hit;
                $closestDistance = $hit->distance;
            }
        }
        
        return $closestHit;
    }
    
    private function raycastCollider(Vector2 $origin, Vector2 $direction, float $maxDistance, Collider $collider): ?RaycastHit
    {
        // Simplified raycasting
        if ($collider instanceof BoxCollider) {
            return $this->raycastBox($origin, $direction, $maxDistance, $collider);
        } elseif ($collider instanceof CircleCollider) {
            return $this->raycastCircle($origin, $direction, $maxDistance, $collider);
        }
        
        return null;
    }
    
    private function raycastBox(Vector2 $origin, Vector2 $direction, float $maxDistance, BoxCollider $box): ?RaycastHit
    {
        // Simplified box raycasting
        $boxPos = $box->getPosition();
        $boxSize = $box->getSize();
        
        // Check if ray starts inside box
        if ($origin->x >= $boxPos->x && $origin->x <= $boxPos->x + $boxSize->x &&
            $origin->y >= $boxPos->y && $origin->y <= $boxPos->y + $boxSize->y) {
            return new RaycastHit($box, 0, $origin, $direction);
        }
        
        // Simplified: just check if ray intersects box bounds
        $endPoint = $origin->add($direction->multiply($maxDistance));
        
        if ($this->lineIntersectsBox($origin, $endPoint, $box)) {
            return new RaycastHit($box, $maxDistance / 2, $origin, $direction);
        }
        
        return null;
    }
    
    private function raycastCircle(Vector2 $origin, Vector2 $direction, float $maxDistance, CircleCollider $circle): ?RaycastHit
    {
        $circlePos = $circle->getPosition();
        $radius = $circle->getRadius();
        
        // Check if ray starts inside circle
        $distanceToCenter = $origin->distance($circlePos);
        if ($distanceToCenter < $radius) {
            return new RaycastHit($circle, 0, $origin, $direction);
        }
        
        // Ray-circle intersection
        $toCircle = $circlePos->subtract($origin);
        $projectedDistance = $toCircle->dot($direction);
        
        if ($projectedDistance < 0 || $projectedDistance > $maxDistance) {
            return null;
        }
        
        $closestPoint = $origin->add($direction->multiply($projectedDistance));
        $distanceToClosest = $closestPoint->distance($circlePos);
        
        if ($distanceToClosest <= $radius) {
            return new RaycastHit($circle, $projectedDistance, $origin, $direction);
        }
        
        return null;
    }
    
    private function lineIntersectsBox(Vector2 $start, Vector2 $end, BoxCollider $box): bool
    {
        $boxPos = $box->getPosition();
        $boxSize = $box->getSize();
        
        // Simplified line-box intersection
        return $start->x <= $boxPos->x + $boxSize->x && $end->x >= $boxPos->x &&
               $start->y <= $boxPos->y + $boxSize->y && $end->y >= $boxPos->y;
    }
    
    public function getGravity(): Vector2
    {
        return $this->gravity;
    }
    
    public function setGravity(Vector2 $gravity): void
    {
        $this->gravity = $gravity;
    }
    
    public function getTimeStep(): float
    {
        return $this->timeStep;
    }
    
    public function setTimeStep(float $timeStep): void
    {
        $this->timeStep = $timeStep;
    }
    
    public function getBodies(): array
    {
        return $this->bodies;
    }
    
    public function getColliders(): array
    {
        return $this->colliders;
    }
    
    public function getJoints(): array
    {
        return $this->joints;
    }
}

// Physics Body
class PhysicsBody
{
    private string $id;
    private Vector2 $position;
    private Vector2 $velocity;
    private Vector2 $force;
    private float $mass;
    private float $inverseMass;
    private float $damping;
    private float $restitution;
    private bool $dynamic;
    private bool $affectedByGravity;
    private bool $sleeping;
    
    public function __construct(string $id, Vector2 $position, float $mass = 1.0)
    {
        $this->id = $id;
        $this->position = $position;
        $this->velocity = new Vector2();
        $this->force = new Vector2();
        $this->mass = max(0.001, $mass);
        $this->inverseMass = 1.0 / $this->mass;
        $this->damping = 0.98;
        $this->restitution = 0.8;
        $this->dynamic = true;
        $this->affectedByGravity = true;
        $this->sleeping = false;
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
    
    public function getForce(): Vector2
    {
        return $this->force;
    }
    
    public function applyForce(Vector2 $force): void
    {
        $this->force = $this->force->add($force);
    }
    
    public function applyImpulse(Vector2 $impulse): void
    {
        $this->velocity = $this->velocity->add($impulse->multiply($this->inverseMass));
    }
    
    public function clearForces(): void
    {
        $this->force = new Vector2();
    }
    
    public function getMass(): float
    {
        return $this->mass;
    }
    
    public function setMass(float $mass): void
    {
        $this->mass = max(0.001, $mass);
        $this->inverseMass = 1.0 / $this->mass;
    }
    
    public function getInverseMass(): float
    {
        return $this->inverseMass;
    }
    
    public function getDamping(): float
    {
        return $this->damping;
    }
    
    public function setDamping(float $damping): void
    {
        $this->damping = max(0, min(1, $damping));
    }
    
    public function getRestitution(): float
    {
        return $this->restitution;
    }
    
    public function setRestitution(float $restitution): void
    {
        $this->restitution = max(0, min(1, $restitution));
    }
    
    public function isDynamic(): bool
    {
        return $this->dynamic;
    }
    
    public function setDynamic(bool $dynamic): void
    {
        $this->dynamic = $dynamic;
    }
    
    public function isStatic(): bool
    {
        return !$this->dynamic;
    }
    
    public function setStatic(bool $static): void
    {
        $this->dynamic = !$static;
    }
    
    public function isAffectedByGravity(): bool
    {
        return $this->affectedByGravity;
    }
    
    public function setAffectedByGravity(bool $affected): void
    {
        $this->affectedByGravity = $affected;
    }
    
    public function isSleeping(): bool
    {
        return $this->sleeping;
    }
    
    public function setSleeping(bool $sleeping): void
    {
        $this->sleeping = $sleeping;
    }
    
    public function getKineticEnergy(): float
    {
        $speed = $this->velocity->magnitude();
        return 0.5 * $this->mass * $speed * $speed;
    }
    
    public function __toString(): string
    {
        return "PhysicsBody(id: {$this->id}, pos: {$this->position}, mass: {$this->mass})";
    }
}

// Collider Base Class
abstract class Collider
{
    protected string $id;
    protected PhysicsBody $body;
    protected Vector2 $position;
    protected bool $trigger;
    protected array $collisions;
    
    public function __construct(string $id, PhysicsBody $body, bool $trigger = false)
    {
        $this->id = $id;
        $this->body = $body;
        $this->position = $body->getPosition();
        $this->trigger = $trigger;
        $this->collisions = [];
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getBody(): PhysicsBody
    {
        return $this->body;
    }
    
    public function getPosition(): Vector2
    {
        return $this->position;
    }
    
    public function setPosition(Vector2 $position): void
    {
        $this->position = $position;
        $this->body->setPosition($position);
    }
    
    public function isTrigger(): bool
    {
        return $this->trigger;
    }
    
    public function setTrigger(bool $trigger): void
    {
        $this->trigger = $trigger;
    }
    
    public function getCollisions(): array
    {
        return $this->collisions;
    }
    
    public function addCollision(Collision $collision): void
    {
        $this->collisions[] = $collision;
    }
    
    public function clearCollisions(): void
    {
        $this->collisions = [];
    }
    
    abstract public function getBounds(): Rectangle;
}

// Box Collider
class BoxCollider extends Collider
{
    private Vector2 $size;
    
    public function __construct(string $id, PhysicsBody $body, Vector2 $size, bool $trigger = false)
    {
        parent::__construct($id, $body, $trigger);
        $this->size = $size;
    }
    
    public function getSize(): Vector2
    {
        return $this->size;
    }
    
    public function setSize(Vector2 $size): void
    {
        $this->size = $size;
    }
    
    public function getBounds(): Rectangle
    {
        return new Rectangle(
            $this->position->x - $this->size->x / 2,
            $this->position->y - $this->size->y / 2,
            $this->size->x,
            $this->size->y
        );
    }
    
    public function getWidth(): float
    {
        return $this->size->x;
    }
    
    public function getHeight(): float
    {
        return $this->size->y;
    }
    
    public function getLeft(): float
    {
        return $this->position->x - $this->size->x / 2;
    }
    
    public function getRight(): float
    {
        return $this->position->x + $this->size->x / 2;
    }
    
    public function getTop(): float
    {
        return $this->position->y - $this->size->y / 2;
    }
    
    public function getBottom(): float
    {
        return $this->position->y + $this->size->y / 2;
    }
}

// Circle Collider
class CircleCollider extends Collider
{
    private float $radius;
    
    public function __construct(string $id, PhysicsBody $body, float $radius, bool $trigger = false)
    {
        parent::__construct($id, $body, $trigger);
        $this->radius = $radius;
    }
    
    public function getRadius(): float
    {
        return $this->radius;
    }
    
    public function setRadius(float $radius): void
    {
        $this->radius = $radius;
    }
    
    public function getBounds(): Rectangle
    {
        return new Rectangle(
            $this->position->x - $this->radius,
            $this->position->y - $this->radius,
            $this->radius * 2,
            $this->radius * 2
        );
    }
}

// Collision
class Collision
{
    private Collider $colliderA;
    private Collider $colliderB;
    private Vector2 $normal;
    private float $penetrationDepth;
    
    public function __construct(Collider $colliderA, Collider $colliderB)
    {
        $this->colliderA = $colliderA;
        $this->colliderB = $colliderB;
        $this->normal = $colliderB->getPosition()->subtract($colliderA->getPosition())->normalize();
        $this->penetrationDepth = 0;
    }
    
    public function getColliderA(): Collider
    {
        return $this->colliderA;
    }
    
    public function getColliderB(): Collider
    {
        return $this->colliderB;
    }
    
    public function getNormal(): Vector2
    {
        return $this->normal;
    }
    
    public function setNormal(Vector2 $normal): void
    {
        $this->normal = $normal;
    }
    
    public function getPenetrationDepth(): float
    {
        return $this->penetrationDepth;
    }
    
    public function setPenetrationDepth(float $depth): void
    {
        $this->penetrationDepth = $depth;
    }
}

// Joint Base Class
abstract class Joint
{
    protected string $id;
    protected PhysicsBody $bodyA;
    protected PhysicsBody $bodyB;
    protected bool $enabled;
    
    public function __construct(string $id, PhysicsBody $bodyA, PhysicsBody $bodyB)
    {
        $this->id = $id;
        $this->bodyA = $bodyA;
        $this->bodyB = $bodyB;
        $this->enabled = true;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getBodyA(): PhysicsBody
    {
        return $this->bodyA;
    }
    
    public function getBodyB(): PhysicsBody
    {
        return $this->bodyB;
    }
    
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
    
    abstract public function solve(): void;
}

// Distance Joint
class DistanceJoint extends Joint
{
    private float $targetDistance;
    private float $stiffness;
    
    public function __construct(string $id, PhysicsBody $bodyA, PhysicsBody $bodyB, float $targetDistance, float $stiffness = 1.0)
    {
        parent::__construct($id, $bodyA, $bodyB);
        $this->targetDistance = $targetDistance;
        $this->stiffness = $stiffness;
    }
    
    public function solve(): void
    {
        if (!$this->enabled) {
            return;
        }
        
        $posA = $this->bodyA->getPosition();
        $posB = $this->bodyB->getPosition();
        
        $currentDistance = $posA->distance($posB);
        $distanceError = $currentDistance - $this->targetDistance;
        
        if (abs($distanceError) < 0.01) {
            return;
        }
        
        $direction = $posB->subtract($posA)->normalize();
        $correction = $direction->multiply($distanceError * $this->stiffness * 0.5);
        
        if ($this->bodyA->isDynamic()) {
            $newPosA = $posA->add($correction);
            $this->bodyA->setPosition($newPosA);
        }
        
        if ($this->bodyB->isDynamic()) {
            $newPosB = $posB->subtract($correction);
            $this->bodyB->setPosition($newPosB);
        }
    }
    
    public function getTargetDistance(): float
    {
        return $this->targetDistance;
    }
    
    public function setTargetDistance(float $distance): void
    {
        $this->targetDistance = $distance;
    }
    
    public function getStiffness(): float
    {
        return $this->stiffness;
    }
    
    public function setStiffness(float $stiffness): void
    {
        $this->stiffness = $stiffness;
    }
}

// Spring Joint
class SpringJoint extends Joint
{
    private float $restLength;
    private float $stiffness;
    private float $damping;
    
    public function __construct(string $id, PhysicsBody $bodyA, PhysicsBody $bodyB, float $restLength, float $stiffness = 1.0, float $damping = 0.1)
    {
        parent::__construct($id, $bodyA, $bodyB);
        $this->restLength = $restLength;
        $this->stiffness = $stiffness;
        $this->damping = $damping;
    }
    
    public function solve(): void
    {
        if (!$this->enabled) {
            return;
        }
        
        $posA = $this->bodyA->getPosition();
        $posB = $this->bodyB->getPosition();
        
        $currentLength = $posA->distance($posB);
        $extension = $currentLength - $this->restLength;
        
        if (abs($extension) < 0.01) {
            return;
        }
        
        $direction = $posB->subtract($posA)->normalize();
        
        // Spring force
        $springForce = $direction->multiply($extension * $this->stiffness);
        
        // Damping force
        $relativeVelocity = $this->bodyB->getVelocity()->subtract($this->bodyA->getVelocity());
        $dampingForce = $relativeVelocity->multiply($this->damping);
        
        $totalForce = $springForce->add($dampingForce);
        
        if ($this->bodyA->isDynamic()) {
            $this->bodyA->applyForce($totalForce);
        }
        
        if ($this->bodyB->isDynamic()) {
            $this->bodyB->applyForce($totalForce->multiply(-1));
        }
    }
    
    public function getRestLength(): float
    {
        return $this->restLength;
    }
    
    public function setRestLength(float $length): void
    {
        $this->restLength = $length;
    }
    
    public function getStiffness(): float
    {
        return $this->stiffness;
    }
    
    public function setStiffness(float $stiffness): void
    {
        $this->stiffness = $stiffness;
    }
    
    public function getDamping(): float
    {
        return $this->damping;
    }
    
    public function setDamping(float $damping): void
    {
        $this->damping = $damping;
    }
}

// Ray Cast Hit
class RaycastHit
{
    private Collider $collider;
    private float $distance;
    private Vector2 $point;
    private Vector2 $normal;
    
    public function __construct(Collider $collider, float $distance, Vector2 $point, Vector2 $normal)
    {
        $this->collider = $collider;
        $this->distance = $distance;
        $this->point = $point;
        $this->normal = $normal;
    }
    
    public function getCollider(): Collider
    {
        return $this->collider;
    }
    
    public function getDistance(): float
    {
        return $this->distance;
    }
    
    public function getPoint(): Vector2
    {
        return $this->point;
    }
    
    public function getNormal(): Vector2
    {
        return $this->normal;
    }
}

// Physics Examples
class PhysicsExamples
{
    public function demonstrateBasicPhysics(): void
    {
        echo "Basic Physics Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $physics = new PhysicsEngine(new Vector2(0, 9.8));
        
        // Create physics bodies
        $ballBody = new PhysicsBody('ball', new Vector2(400, 100), 1.0);
        $ballBody->setRestitution(0.8);
        $ballBody->setDamping(0.99);
        
        $groundBody = new PhysicsBody('ground', new Vector2(400, 550), 1000.0);
        $groundBody->setStatic(true);
        
        $physics->addBody($ballBody);
        $physics->addBody($groundBody);
        
        // Create colliders
        $ballCollider = new CircleCollider('ball_collider', $ballBody, 20);
        $groundCollider = new BoxCollider('ground_collider', $groundBody, new Vector2(800, 20));
        
        $physics->addCollider($ballCollider);
        $physics->addCollider($groundCollider);
        
        echo "Created physics bodies and colliders:\n";
        echo "  Ball: mass={$ballBody->getMass()}, restitution={$ballBody->getRestitution()}\n";
        echo "  Ground: mass={$groundBody->getMass()}, static=" . ($groundBody->isStatic() ? 'Yes' : 'No') . "\n";
        
        // Simulate physics
        echo "\nSimulating physics (5 seconds):\n";
        
        for ($i = 0; $i < 50; $i++) {
            $physics->step(0.1);
            
            if ($i % 10 === 0) {
                $pos = $ballBody->getPosition();
                $vel = $ballBody->getVelocity();
                echo "  Time " . ($i * 0.1) . "s: pos=$pos, vel=$vel\n";
            }
        }
        
        // Show final state
        echo "\nFinal state:\n";
        echo "  Ball position: {$ballBody->getPosition()}\n";
        echo "  Ball velocity: {$ballBody->getVelocity()}\n";
        echo "  Kinetic energy: " . round($ballBody->getKineticEnergy(), 2) . "\n";
    }
    
    public function demonstrateCollisions(): void
    {
        echo "\nCollision Detection Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $physics = new PhysicsEngine(new Vector2(0, 0));
        
        // Create colliding objects
        $box1Body = new PhysicsBody('box1', new Vector2(200, 300), 2.0);
        $box2Body = new PhysicsBody('box2', new Vector2(400, 300), 2.0);
        $circle1Body = new PhysicsBody('circle1', new Vector2(300, 200), 1.0);
        $circle2Body = new PhysicsBody('circle2', new Vector2(350, 250), 1.0);
        
        // Set initial velocities
        $box1Body->setVelocity(new Vector2(50, 0));
        $box2Body->setVelocity(new Vector2(-50, 0));
        $circle1Body->setVelocity(new Vector2(25, 25));
        $circle2Body->setVelocity(new Vector2(-25, -25));
        
        $physics->addBody($box1Body);
        $physics->addBody($box2Body);
        $physics->addBody($circle1Body);
        $physics->addBody($circle2Body);
        
        // Create colliders
        $box1Collider = new BoxCollider('box1_collider', $box1Body, new Vector2(40, 40));
        $box2Collider = new BoxCollider('box2_collider', $box2Body, new Vector2(40, 40));
        $circle1Collider = new CircleCollider('circle1_collider', $circle1Body, 20);
        $circle2Collider = new CircleCollider('circle2_collider', $circle2Body, 20);
        
        $physics->addCollider($box1Collider);
        $physics->addCollider($box2Collider);
        $physics->addCollider($circle1Collider);
        $physics->addCollider($circle2Collider);
        
        echo "Created colliding objects:\n";
        echo "  Box 1: {$box1Body->getPosition()}, velocity={$box1Body->getVelocity()}\n";
        echo "  Box 2: {$box2Body->getPosition()}, velocity={$box2Body->getVelocity()}\n";
        echo "  Circle 1: {$circle1Body->getPosition()}, velocity={$circle1Body->getVelocity()}\n";
        echo "  Circle 2: {$circle2Body->getPosition()}, velocity={$circle2Body->getVelocity()}\n";
        
        // Simulate collisions
        echo "\nSimulating collisions:\n";
        
        for ($i = 0; $i < 30; $i++) {
            $physics->step(0.1);
            
            if ($i % 5 === 0) {
                echo "  Time " . ($i * 0.1) . "s:\n";
                echo "    Box 1: pos={$box1Body->getPosition()}, vel={$box1Body->getVelocity()}\n";
                echo "    Box 2: pos={$box2Body->getPosition()}, vel={$box2Body->getVelocity()}\n";
                echo "    Circle 1: pos={$circle1Body->getPosition()}, vel={$circle1Body->getVelocity()}\n";
                echo "    Circle 2: pos={$circle2Body->getPosition()}, vel={$circle2Body->getVelocity()}\n";
            }
        }
        
        // Check for collisions
        echo "\nCollision detection:\n";
        $collisions = [];
        
        foreach ($physics->getColliders() as $collider) {
            foreach ($collider->getCollisions() as $collision) {
                $collisions[] = $collision;
            }
        }
        
        echo "Active collisions: " . count($collisions) . "\n";
        
        foreach ($collisions as $i => $collision) {
            echo "  Collision " . ($i + 1) . ": {$collision->getColliderA()->getId()} <-> {$collision->getColliderB()->getId()}\n";
        }
    }
    
    public function demonstrateJoints(): void
    {
        echo "\nJoints Demo\n";
        echo str_repeat("-", 15) . "\n";
        
        $physics = new PhysicsEngine(new Vector2(0, 9.8));
        
        // Create bodies for joint
        $body1 = new PhysicsBody('body1', new Vector2(300, 200), 1.0);
        $body2 = new PhysicsBody('body2', new Vector2(500, 200), 1.0);
        $body3 = new PhysicsBody('body3', new Vector2(400, 300), 1.0);
        
        $physics->addBody($body1);
        $physics->addBody($body2);
        $physics->addBody($body3);
        
        // Create distance joints
        $distanceJoint1 = new DistanceJoint('joint1', $body1, $body2, 200, 2.0);
        $distanceJoint2 = new DistanceJoint('joint2', $body1, $body3, 150, 1.5);
        $distanceJoint3 = new DistanceJoint('joint3', $body2, $body3, 150, 1.5);
        
        $physics->addJoint($distanceJoint1);
        $physics->addJoint($distanceJoint2);
        $physics->addJoint($distanceJoint3);
        
        // Create spring joint
        $springJoint = new SpringJoint('spring1', $body3, new PhysicsBody('anchor', new Vector2(400, 400), 1000.0), 100, 0.5, 0.1);
        $physics->addBody($springJoint->getBodyB());
        $physics->addJoint($springJoint);
        
        echo "Created joints:\n";
        echo "  Distance Joint 1: {$distanceJoint1->getTargetDistance()} units, stiffness={$distanceJoint1->getStiffness()}\n";
        echo "  Distance Joint 2: {$distanceJoint2->getTargetDistance()} units, stiffness={$distanceJoint2->getStiffness()}\n";
        echo "  Distance Joint 3: {$distanceJoint3->getTargetDistance()} units, stiffness={$distanceJoint3->getStiffness()}\n";
        echo "  Spring Joint: {$springJoint->getRestLength()} units, stiffness={$springJoint->getStiffness()}, damping={$springJoint->getDamping()}\n";
        
        // Set initial velocities
        $body1->setVelocity(new Vector2(30, 20));
        $body2->setVelocity(new Vector2(-20, 30));
        $body3->setVelocity(new Vector2(10, -40));
        
        echo "\nInitial velocities:\n";
        echo "  Body 1: {$body1->getVelocity()}\n";
        echo "  Body 2: {$body2->getVelocity()}\n";
        echo "  Body 3: {$body3->getVelocity()}\n";
        
        // Simulate jointed physics
        echo "\nSimulating jointed physics:\n";
        
        for ($i = 0; $i < 40; $i++) {
            $physics->step(0.1);
            
            if ($i % 8 === 0) {
                echo "  Time " . ($i * 0.1) . "s:\n";
                echo "    Body 1: pos={$body1->getPosition()}\n";
                echo "    Body 2: pos={$body2->getPosition()}\n";
                echo "    Body 3: pos={$body3->getPosition()}\n";
                
                // Check joint distances
                $dist1 = $body1->getPosition()->distance($body2->getPosition());
                $dist2 = $body1->getPosition()->distance($body3->getPosition());
                $dist3 = $body2->getPosition()->distance($body3->getPosition());
                
                echo "    Joint distances: $dist1, $dist2, $dist3\n";
            }
        }
        
        // Show joint constraints
        echo "\nJoint constraints:\n";
        echo "  Target distances: {$distanceJoint1->getTargetDistance()}, {$distanceJoint2->getTargetDistance()}, {$distanceJoint3->getTargetDistance()}\n";
        echo "  Actual distances: " . $body1->getPosition()->distance($body2->getPosition()) . ", " . 
             $body1->getPosition()->distance($body3->getPosition()) . ", " . 
             $body2->getPosition()->distance($body3->getPosition()) . "\n";
    }
    
    public function demonstrateRaycasting(): void
    {
        echo "\nRaycasting Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $physics = new PhysicsEngine();
        
        // Create obstacles
        $obstacles = [
            ['pos' => new Vector2(200, 300), 'size' => new Vector2(60, 60)],
            ['pos' => new Vector2(400, 250), 'size' => new Vector2(40, 40)],
            ['pos' => new Vector2(600, 350), 'size' => new Vector2(80, 80)]
        ];
        
        foreach ($obstacles as $i => $obstacle) {
            $body = new PhysicsBody("obstacle_$i", $obstacle['pos'], 1.0);
            $body->setStatic(true);
            
            $collider = new BoxCollider("obstacle_collider_$i", $body, $obstacle['size']);
            
            $physics->addBody($body);
            $physics->addCollider($collider);
        }
        
        echo "Created obstacles:\n";
        foreach ($obstacles as $i => $obstacle) {
            echo "  Obstacle $i: {$obstacle['pos']}, size={$obstacle['size']}\n";
        }
        
        // Test raycasting
        echo "\nTesting raycasting:\n";
        
        $rayOrigins = [
            new Vector2(100, 300),
            new Vector2(300, 200),
            new Vector2(500, 400)
        ];
        
        $rayDirections = [
            new Vector2(1, 0),   // Right
            new Vector2(0, -1),  // Up
            new Vector2(-1, 1)   // Left-Down
        ];
        
        foreach ($rayOrigins as $i => $origin) {
            foreach ($rayDirections as $j => $direction) {
                $hit = $physics->raycast($origin, $direction, 500);
                
                echo "  Ray " . ($i + 1) . "-" . ($j + 1) . ": origin=$origin, direction=$direction\n";
                
                if ($hit) {
                    echo "    Hit: {$hit->getCollider()->getId()} at distance {$hit->getDistance()}\n";
                    echo "    Point: {$hit->getPoint()}\n";
                } else {
                    echo "    No hit\n";
                }
                echo "\n";
            }
        }
        
        // Test raycast with maximum distance
        echo "Testing raycast with different distances:\n";
        
        $origin = new Vector2(100, 300);
        $direction = new Vector2(1, 0);
        
        $distances = [100, 200, 300, 400, 500];
        
        foreach ($distances as $distance) {
            $hit = $physics->raycast($origin, $direction, $distance);
            echo "  Distance $distance: " . ($hit ? "Hit at {$hit->getDistance()}" : "No hit") . "\n";
        }
    }
    
    public function demonstrateAdvancedPhysics(): void
    {
        echo "\nAdvanced Physics Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $physics = new PhysicsEngine(new Vector2(0, 9.8));
        
        // Create a chain of connected bodies
        $chainBodies = [];
        $chainJoints = [];
        
        for ($i = 0; $i < 5; $i++) {
            $body = new PhysicsBody("chain_$i", new Vector2(200 + $i * 60, 200), 0.5);
            $body->setDamping(0.95);
            
            $physics->addBody($body);
            $chainBodies[] = $body;
            
            if ($i > 0) {
                $joint = new DistanceJoint("chain_joint_$i", $chainBodies[$i-1], $body, 50, 5.0);
                $physics->addJoint($joint);
                $chainJoints[] = $joint;
            }
        }
        
        // Create pendulum
        $pendulumBob = new PhysicsBody('pendulum', new Vector2(400, 100), 2.0);
        $pendulumBob->setDamping(0.98);
        
        $anchor = new PhysicsBody('anchor', new Vector2(400, 50), 1000.0);
        $anchor->setStatic(true);
        
        $physics->addBody($pendulumBob);
        $physics->addBody($anchor);
        
        $pendulumJoint = new DistanceJoint('pendulum_joint', $anchor, $pendulumBob, 100, 10.0);
        $physics->addJoint($pendulumJoint);
        
        // Add initial velocities
        $chainBodies[0]->setVelocity(new Vector2(100, 0));
        $pendulumBob->setVelocity(new Vector2(50, 0));
        
        echo "Created advanced physics setup:\n";
        echo "  Chain: " . count($chainBodies) . " bodies, " . count($chainJoints) . " joints\n";
        echo "  Pendulum: {$pendulumJoint->getTargetDistance()} units\n";
        
        // Simulate advanced physics
        echo "\nSimulating advanced physics:\n";
        
        for ($i = 0; $i < 60; $i++) {
            $physics->step(0.05);
            
            if ($i % 10 === 0) {
                echo "  Time " . ($i * 0.05) . "s:\n";
                
                // Show chain state
                echo "    Chain:\n";
                foreach ($chainBodies as $j => $body) {
                    echo "      Link $j: {$body->getPosition()}\n";
                }
                
                // Show pendulum state
                echo "    Pendulum: {$pendulumBob->getPosition()}\n";
                
                // Calculate system energy
                $totalEnergy = 0;
                foreach ($chainBodies as $body) {
                    $totalEnergy += $body->getKineticEnergy();
                }
                $totalEnergy += $pendulumBob->getKineticEnergy();
                
                echo "    Total energy: " . round($totalEnergy, 2) . "\n";
            }
        }
        
        // Show damping effects
        echo "\nDamping effects:\n";
        echo "  Initial energy: ~1000 (estimated)\n";
        echo "  Final energy: " . round($pendulumBob->getKineticEnergy(), 2) . "\n";
        echo "  Energy dissipation: " . round((1000 - $pendulumBob->getKineticEnergy()) / 1000 * 100, 1) . "%\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nPhysics Best Practices\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "1. Physics Engine Design:\n";
        echo "   • Use fixed time steps for stability\n";
        echo "   • Separate collision detection from resolution\n";
        echo "   • Use spatial partitioning for performance\n";
        echo "   • Implement proper mass/inverse mass handling\n";
        echo "   • Use iterative constraint solving\n\n";
        
        echo "2. Collision Detection:\n";
        echo "   • Use broad-phase culling first\n";
        echo "   • Implement multiple collision shapes\n";
        echo "   • Use continuous collision detection\n";
        echo "   • Implement collision filtering\n";
        echo "   • Use trigger colliders for non-physical interactions\n\n";
        
        echo "3. Collision Resolution:\n";
        echo "   • Use impulse-based resolution\n";
        echo "   • Implement proper restitution\n";
        echo "   • Use position correction for penetration\n";
        echo "   • Stack multiple collision passes\n";
        echo "   • Handle resting contacts properly\n\n";
        
        echo "4. Constraints and Joints:\n";
        echo "   • Use iterative solvers for constraints\n";
        echo "   • Implement proper joint limits\n";
        echo "   • Use soft constraints for stability\n";
        echo "   • Implement joint breaking\n";
        echo "   • Use constraint relaxation\n\n";
        
        echo "5. Performance Optimization:\n";
        echo "   • Use object pooling for bodies/colliders\n";
        echo "   • Implement sleeping for inactive objects\n";
        echo "   • Use multi-threading where possible\n";
        echo "   • Profile and optimize bottlenecks\n";
        echo "   • Use level-of-detail for complex shapes";
    }
    
    public function runAllExamples(): void
    {
        echo "Physics Examples\n";
        echo str_repeat("=", 15) . "\n";
        
        $this->demonstrateBasicPhysics();
        $this->demonstrateCollisions();
        $this->demonstrateJoints();
        $this->demonstrateRaycasting();
        $this->demonstrateAdvancedPhysics();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runPhysicsDemo(): void
{
    $examples = new PhysicsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runPhysicsDemo();
}
?>
