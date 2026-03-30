<?php
/**
 * 2D Graphics in PHP
 * 
 * 2D rendering, sprites, animations, and visual effects.
 */

// 2D Graphics Engine
class Graphics2DEngine
{
    private int $width;
    private int $height;
    private array $layers;
    private array $sprites;
    private array $animations;
    private array $effects;
    private Camera2D $camera;
    private Color $backgroundColor;
    
    public function __construct(int $width = 800, int $height = 600)
    {
        $this->width = $width;
        $this->height = $height;
        $this->layers = [];
        $this->sprites = [];
        $this->animations = [];
        $this->effects = [];
        $this->camera = new Camera2D($width, $height);
        $this->backgroundColor = new Color(0, 0, 0);
        
        $this->initializeLayers();
    }
    
    private function initializeLayers(): void
    {
        $this->layers = [
            'background' => new Layer('background', 0),
            'terrain' => new Layer('terrain', 1),
            'objects' => new Layer('objects', 2),
            'characters' => new Layer('characters', 3),
            'effects' => new Layer('effects', 4),
            'ui' => new Layer('ui', 5)
        ];
    }
    
    public function getWidth(): int
    {
        return $this->width;
    }
    
    public function getHeight(): int
    {
        return $this->height;
    }
    
    public function setBackgroundColor(Color $color): void
    {
        $this->backgroundColor = $color;
    }
    
    public function getBackgroundColor(): Color
    {
        return $this->backgroundColor;
    }
    
    public function getCamera(): Camera2D
    {
        return $this->camera;
    }
    
    public function addSprite(Sprite $sprite, string $layerName = 'objects'): void
    {
        if (!isset($this->layers[$layerName])) {
            throw new Exception("Layer not found: $layerName");
        }
        
        $this->layers[$layerName]->addSprite($sprite);
        $this->sprites[$sprite->getId()] = $sprite;
        
        echo "Added sprite '{$sprite->getId()}' to layer '$layerName'\n";
    }
    
    public function removeSprite(string $spriteId): void
    {
        if (!isset($this->sprites[$spriteId])) {
            return;
        }
        
        $sprite = $this->sprites[$spriteId];
        
        foreach ($this->layers as $layer) {
            $layer->removeSprite($spriteId);
        }
        
        unset($this->sprites[$spriteId]);
        echo "Removed sprite '$spriteId'\n";
    }
    
    public function getSprite(string $id): ?Sprite
    {
        return $this->sprites[$id] ?? null;
    }
    
    public function addAnimation(Animation $animation): void
    {
        $this->animations[$animation->getId()] = $animation;
        echo "Added animation '{$animation->getId()}'\n";
    }
    
    public function getAnimation(string $id): ?Animation
    {
        return $this->animations[$id] ?? null;
    }
    
    public function addEffect(VisualEffect $effect): void
    {
        $this->effects[$effect->getId()] = $effect;
        $this->layers['effects']->addSprite($effect->getSprite());
        echo "Added effect '{$effect->getId()}'\n";
    }
    
    public function removeEffect(string $effectId): void
    {
        if (!isset($this->effects[$effectId])) {
            return;
        }
        
        $effect = $this->effects[$effectId];
        $this->layers['effects']->removeSprite($effect->getSprite()->getId());
        unset($this->effects[$effectId]);
        echo "Removed effect '$effectId'\n";
    }
    
    public function render(): void
    {
        echo "=== 2D Graphics Render ===\n";
        echo "Screen: {$this->width}x{$this->height}\n";
        echo "Background: {$this->backgroundColor}\n";
        echo "Camera: {$this->camera->getPosition()}\n\n";
        
        // Render layers in order
        ksort($this->layers);
        
        foreach ($this->layers as $layer) {
            if ($layer->isVisible()) {
                echo "--- Layer: {$layer->getName()} ---\n";
                $layer->render($this->camera);
                echo "\n";
            }
        }
        
        echo "=== End Render ===\n\n";
    }
    
    public function update(float $deltaTime): void
    {
        // Update animations
        foreach ($this->animations as $animation) {
            $animation->update($deltaTime);
        }
        
        // Update effects
        foreach ($this->effects as $effect) {
            $effect->update($deltaTime);
            
            // Remove completed effects
            if ($effect->isComplete()) {
                $this->removeEffect($effect->getId());
            }
        }
        
        // Update sprites
        foreach ($this->sprites as $sprite) {
            $sprite->update($deltaTime);
        }
    }
    
    public function getLayer(string $name): ?Layer
    {
        return $this->layers[$name] ?? null;
    }
    
    public function getLayers(): array
    {
        return $this->layers;
    }
    
    public function getSpriteCount(): int
    {
        return count($this->sprites);
    }
    
    public function getEffectCount(): int
    {
        return count($this->effects);
    }
}

// Camera2D Class
class Camera2D
{
    private Vector2 $position;
    private Vector2 $size;
    private float $zoom;
    private float $rotation;
    
    public function __construct(int $width, int $height)
    {
        $this->position = new Vector2($width / 2, $height / 2);
        $this->size = new Vector2($width, $height);
        $this->zoom = 1.0;
        $this->rotation = 0;
    }
    
    public function getPosition(): Vector2
    {
        return $this->position;
    }
    
    public function setPosition(Vector2 $position): void
    {
        $this->position = $position;
    }
    
    public function getSize(): Vector2
    {
        return $this->size;
    }
    
    public function setSize(Vector2 $size): void
    {
        $this->size = $size;
    }
    
    public function getZoom(): float
    {
        return $this->zoom;
    }
    
    public function setZoom(float $zoom): void
    {
        $this->zoom = max(0.1, $zoom);
    }
    
    public function getRotation(): float
    {
        return $this->rotation;
    }
    
    public function setRotation(float $rotation): void
    {
        $this->rotation = $rotation;
    }
    
    public function worldToScreen(Vector2 $worldPos): Vector2
    {
        // Apply camera transformations
        $relativePos = $worldPos->subtract($this->position);
        $scaledPos = $relativePos->multiply($this->zoom);
        
        // Apply rotation
        if ($this->rotation !== 0) {
            $cos = cos($this->rotation);
            $sin = sin($this->rotation);
            $rotatedX = $scaledPos->x * $cos - $scaledPos->y * $sin;
            $rotatedY = $scaledPos->x * $sin + $scaledPos->y * $cos;
            $scaledPos = new Vector2($rotatedX, $rotatedY);
        }
        
        // Convert to screen coordinates
        $screenPos = $scaledPos->add(new Vector2($this->size->x / 2, $this->size->y / 2));
        
        return $screenPos;
    }
    
    public function screenToWorld(Vector2 $screenPos): Vector2
    {
        // Convert from screen coordinates
        $relativePos = $screenPos->subtract(new Vector2($this->size->x / 2, $this->size->y / 2));
        
        // Apply inverse rotation
        if ($this->rotation !== 0) {
            $cos = cos(-$this->rotation);
            $sin = sin(-$this->rotation);
            $rotatedX = $relativePos->x * $cos - $relativePos->y * $sin;
            $rotatedY = $relativePos->x * $sin + $relativePos->y * $cos;
            $relativePos = new Vector2($rotatedX, $rotatedY);
        }
        
        // Apply inverse zoom
        $scaledPos = $relativePos->divide($this->zoom);
        
        // Convert to world coordinates
        $worldPos = $scaledPos->add($this->position);
        
        return $worldPos;
    }
    
    public function isVisible(Vector2 $worldPos, Vector2 $size): bool
    {
        $screenPos = $this->worldToScreen($worldPos);
        $screenSize = $size->multiply($this->zoom);
        
        return $screenPos->x + $screenSize->x >= 0 &&
               $screenPos->x <= $this->size->x &&
               $screenPos->y + $screenSize->y >= 0 &&
               $screenPos->y <= $this->size->y;
    }
    
    public function follow(Vector2 $target, float $smoothSpeed = 0.1): void
    {
        $desiredPosition = $target;
        $currentPosition = $this->position;
        
        $newPosition = $currentPosition->add(
            $desiredPosition->subtract($currentPosition)->multiply($smoothSpeed)
        );
        
        $this->setPosition($newPosition);
    }
    
    public function __toString(): string
    {
        return "Camera2D(Position: {$this->position}, Zoom: {$this->zoom}, Rotation: {$this->rotation})";
    }
}

// Layer Class
class Layer
{
    private string $name;
    private int $order;
    private array $sprites;
    private bool $visible;
    private bool $parallax;
    private Vector2 $parallaxFactor;
    
    public function __construct(string $name, int $order, bool $parallax = false)
    {
        $this->name = $name;
        $this->order = $order;
        $this->sprites = [];
        $this->visible = true;
        $this->parallax = $parallax;
        $this->parallaxFactor = new Vector2(1, 1);
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getOrder(): int
    {
        return $this->order;
    }
    
    public function isVisible(): bool
    {
        return $this->visible;
    }
    
    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }
    
    public function isParallax(): bool
    {
        return $this->parallax;
    }
    
    public function setParallax(bool $parallax): void
    {
        $this->parallax = $parallax;
    }
    
    public function getParallaxFactor(): Vector2
    {
        return $this->parallaxFactor;
    }
    
    public function setParallaxFactor(Vector2 $factor): void
    {
        $this->parallaxFactor = $factor;
    }
    
    public function addSprite(Sprite $sprite): void
    {
        $this->sprites[$sprite->getId()] = $sprite;
    }
    
    public function removeSprite(string $spriteId): void
    {
        if (isset($this->sprites[$spriteId])) {
            unset($this->sprites[$spriteId]);
        }
    }
    
    public function getSprites(): array
    {
        return $this->sprites;
    }
    
    public function render(Camera2D $camera): void
    {
        foreach ($this->sprites as $sprite) {
            if ($sprite->isVisible()) {
                $this->renderSprite($sprite, $camera);
            }
        }
    }
    
    private function renderSprite(Sprite $sprite, Camera2D $camera): void
    {
        $position = $sprite->getPosition();
        
        // Apply parallax effect
        if ($this->parallax) {
            $cameraPos = $camera->getPosition();
            $parallaxOffset = $cameraPos->multiply(
                new Vector2(1 - $this->parallaxFactor->x, 1 - $this->parallaxFactor->y)
            );
            $position = $position->add($parallaxOffset);
        }
        
        // Check if sprite is visible
        if (!$camera->isVisible($position, $sprite->getSize())) {
            return;
        }
        
        // Convert to screen coordinates
        $screenPos = $camera->worldToScreen($position);
        
        echo "Sprite: {$sprite->getId()}\n";
        echo "  Position: $screenPos\n";
        echo "  Size: {$sprite->getSize()}\n";
        echo "  Color: {$sprite->getColor()}\n";
        echo "  Rotation: {$sprite->getRotation()}°\n";
        echo "  Scale: {$sprite->getScale()}\n";
        echo "  Texture: {$sprite->getTexture()}\n";
        echo "  Visible: " . ($sprite->isVisible() ? 'Yes' : 'No') . "\n\n";
    }
}

// Sprite Class
class Sprite
{
    private string $id;
    private Vector2 $position;
    private Vector2 $size;
    private Color $color;
    private float $rotation;
    private Vector2 $scale;
    private string $texture;
    private bool $visible;
    private int $layer;
    private Rectangle $bounds;
    
    public function __construct(string $id, Vector2 $position, Vector2 $size, string $texture = '')
    {
        $this->id = $id;
        $this->position = $position;
        $this->size = $size;
        $this->color = new Color(255, 255, 255);
        $this->rotation = 0;
        $this->scale = new Vector2(1, 1);
        $this->texture = $texture;
        $this->visible = true;
        $this->layer = 0;
        $this->updateBounds();
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
        $this->updateBounds();
    }
    
    public function getSize(): Vector2
    {
        return $this->size->multiply($this->scale);
    }
    
    public function getOriginalSize(): Vector2
    {
        return $this->size;
    }
    
    public function setSize(Vector2 $size): void
    {
        $this->size = $size;
        $this->updateBounds();
    }
    
    public function getColor(): Color
    {
        return $this->color;
    }
    
    public function setColor(Color $color): void
    {
        $this->color = $color;
    }
    
    public function getRotation(): float
    {
        return $this->rotation;
    }
    
    public function setRotation(float $rotation): void
    {
        $this->rotation = $rotation;
    }
    
    public function getScale(): Vector2
    {
        return $this->scale;
    }
    
    public function setScale(Vector2 $scale): void
    {
        $this->scale = $scale;
        $this->updateBounds();
    }
    
    public function getTexture(): string
    {
        return $this->texture;
    }
    
    public function setTexture(string $texture): void
    {
        $this->texture = $texture;
    }
    
    public function isVisible(): bool
    {
        return $this->visible;
    }
    
    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }
    
    public function getLayer(): int
    {
        return $this->layer;
    }
    
    public function setLayer(int $layer): void
    {
        $this->layer = $layer;
    }
    
    public function getBounds(): Rectangle
    {
        return $this->bounds;
    }
    
    private function updateBounds(): void
    {
        $size = $this->getSize();
        $this->bounds = new Rectangle(
            $this->position->x - $size->x / 2,
            $this->position->y - $size->y / 2,
            $size->x,
            $size->y
        );
    }
    
    public function intersects(Sprite $other): bool
    {
        return $this->bounds->intersects($other->getBounds());
    }
    
    public function containsPoint(Vector2 $point): bool
    {
        return $this->bounds->contains($point);
    }
    
    public function update(float $deltaTime): void
    {
        // Override in subclasses for animation
    }
    
    public function __toString(): string
    {
        return "Sprite(id: {$this->id}, pos: {$this->position}, size: {$this->getSize()})";
    }
}

// Color Class
class Color
{
    public int $r;
    public int $g;
    public int $b;
    public int $a;
    
    public function __construct(int $r = 255, int $g = 255, int $b = 255, int $a = 255)
    {
        $this->r = max(0, min(255, $r));
        $this->g = max(0, min(255, $g));
        $this->b = max(0, min(255, $b));
        $this->a = max(0, min(255, $a));
    }
    
    public static function fromHex(string $hex): Color
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $r = hexdec($hex[0] . $hex[0]);
            $g = hexdec($hex[1] . $hex[1]);
            $b = hexdec($hex[2] . $hex[2]);
        } elseif (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            throw new Exception("Invalid hex color: $hex");
        }
        
        return new Color($r, $g, $b);
    }
    
    public function toHex(): string
    {
        return sprintf('#%02x%02x%02x', $this->r, $this->g, $this->b);
    }
    
    public function toRGBA(): string
    {
        return "rgba({$this->r}, {$this->g}, {$this->b}, {$this->a / 255})";
    }
    
    public function lerp(Color $other, float $t): Color
    {
        $r = (int) ($this->r + ($other->r - $this->r) * $t);
        $g = (int) ($this->g + ($other->g - $this->g) * $t);
        $b = (int) ($this->b + ($other->b - $this->b) * $t);
        $a = (int) ($this->a + ($other->a - $this->a) * $t);
        
        return new Color($r, $g, $b, $a);
    }
    
    public function __toString(): string
    {
        return "Color(r: {$this->r}, g: {$this->g}, b: {$this->b}, a: {$this->a})";
    }
}

// Rectangle Class
class Rectangle
{
    public float $x;
    public float $y;
    public float $width;
    public float $height;
    
    public function __construct(float $x = 0, float $y = 0, float $width = 0, float $height = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
    }
    
    public function intersects(Rectangle $other): bool
    {
        return $this->x < $other->x + $other->width &&
               $this->x + $this->width > $other->x &&
               $this->y < $other->y + $other->height &&
               $this->y + $this->height > $other->y;
    }
    
    public function contains(Vector2 $point): bool
    {
        return $point->x >= $this->x &&
               $point->x <= $this->x + $this->width &&
               $point->y >= $this->y &&
               $point->y <= $this->y + $this->height;
    }
    
    public function getCenter(): Vector2
    {
        return new Vector2($this->x + $this->width / 2, $this->y + $this->height / 2);
    }
    
    public function __toString(): string
    {
        return "Rectangle(x: {$this->x}, y: {$this->y}, w: {$this->width}, h: {$this->height})";
    }
}

// Animation Class
class Animation
{
    private string $id;
    private array $frames;
    private float $frameDuration;
    private bool $looping;
    private int $currentFrame;
    private float $currentTime;
    private bool $playing;
    
    public function __construct(string $id, array $frames, float $frameDuration, bool $looping = true)
    {
        $this->id = $id;
        $this->frames = $frames;
        $this->frameDuration = $frameDuration;
        $this->looping = $looping;
        $this->currentFrame = 0;
        $this->currentTime = 0;
        $this->playing = false;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function play(): void
    {
        $this->playing = true;
        echo "Playing animation: {$this->id}\n";
    }
    
    public function pause(): void
    {
        $this->playing = false;
        echo "Paused animation: {$this->id}\n";
    }
    
    public function stop(): void
    {
        $this->playing = false;
        $this->currentFrame = 0;
        $this->currentTime = 0;
        echo "Stopped animation: {$this->id}\n";
    }
    
    public function update(float $deltaTime): void
    {
        if (!$this->playing) {
            return;
        }
        
        $this->currentTime += $deltaTime;
        
        while ($this->currentTime >= $this->frameDuration) {
            $this->currentTime -= $this->frameDuration;
            $this->currentFrame++;
            
            if ($this->currentFrame >= count($this->frames)) {
                if ($this->looping) {
                    $this->currentFrame = 0;
                } else {
                    $this->currentFrame = count($this->frames) - 1;
                    $this->playing = false;
                }
            }
        }
    }
    
    public function getCurrentFrame(): array
    {
        return $this->frames[$this->currentFrame] ?? [];
    }
    
    public function getCurrentFrameIndex(): int
    {
        return $this->currentFrame;
    }
    
    public function getFrameCount(): int
    {
        return count($this->frames);
    }
    
    public function isPlaying(): bool
    {
        return $this->playing;
    }
    
    public function isComplete(): bool
    {
        return !$this->looping && $this->currentFrame >= count($this->frames) - 1 && !$this->playing;
    }
    
    public function setFrame(int $frame): void
    {
        $this->currentFrame = max(0, min(count($this->frames) - 1, $frame));
    }
    
    public function setFrameDuration(float $duration): void
    {
        $this->frameDuration = $duration;
    }
    
    public function setLooping(bool $looping): void
    {
        $this->looping = $looping;
    }
}

// Animated Sprite Class
class AnimatedSprite extends Sprite
{
    private Animation $animation;
    private array $animations;
    private string $currentAnimation;
    
    public function __construct(string $id, Vector2 $position, Vector2 $size, array $animations = [])
    {
        parent::__construct($id, $position, $size);
        $this->animations = $animations;
        $this->currentAnimation = '';
        
        if (!empty($animations)) {
            $firstAnimation = key($animations);
            $this->setAnimation($firstAnimation);
        }
    }
    
    public function addAnimation(string $name, Animation $animation): void
    {
        $this->animations[$name] = $animation;
        echo "Added animation '$name' to sprite '{$this->id}'\n";
    }
    
    public function setAnimation(string $name): void
    {
        if (!isset($this->animations[$name])) {
            throw new Exception("Animation not found: $name");
        }
        
        // Stop current animation
        if (!empty($this->currentAnimation)) {
            $this->animations[$this->currentAnimation]->stop();
        }
        
        $this->currentAnimation = $name;
        $this->animation = $this->animations[$name];
        $this->animation->play();
        
        echo "Set animation '$name' for sprite '{$this->id}'\n";
    }
    
    public function getCurrentAnimation(): string
    {
        return $this->currentAnimation;
    }
    
    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);
        
        if (isset($this->animation)) {
            $this->animation->update($deltaTime);
            
            // Update sprite texture based on current frame
            $frame = $this->animation->getCurrentFrame();
            if (isset($frame['texture'])) {
                $this->setTexture($frame['texture']);
            }
            
            if (isset($frame['color'])) {
                $this->setColor($frame['color']);
            }
        }
    }
    
    public function isAnimationComplete(): bool
    {
        return isset($this->animation) ? $this->animation->isComplete() : true;
    }
    
    public function getAnimationProgress(): float
    {
        if (!isset($this->animation)) {
            return 0;
        }
        
        return $this->animation->getCurrentFrameIndex() / $this->animation->getFrameCount();
    }
}

// Visual Effect Class
class VisualEffect
{
    private string $id;
    private Sprite $sprite;
    private float $duration;
    private float $currentTime;
    private bool $playing;
    private array $keyframes;
    
    public function __construct(string $id, Sprite $sprite, float $duration)
    {
        $this->id = $id;
        $this->sprite = $sprite;
        $this->duration = $duration;
        $this->currentTime = 0;
        $this->playing = false;
        $this->keyframes = [];
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getSprite(): Sprite
    {
        return $this->sprite;
    }
    
    public function play(): void
    {
        $this->playing = true;
        $this->currentTime = 0;
        echo "Playing effect: {$this->id}\n";
    }
    
    public function stop(): void
    {
        $this->playing = false;
        $this->currentTime = 0;
        echo "Stopped effect: {$this->id}\n";
    }
    
    public function update(float $deltaTime): void
    {
        if (!$this->playing) {
            return;
        }
        
        $this->currentTime += $deltaTime;
        
        if ($this->currentTime >= $this->duration) {
            $this->playing = false;
            $this->currentTime = $this->duration;
        }
        
        $this->updateEffect();
    }
    
    private function updateEffect(): void
    {
        $progress = $this->currentTime / $this->duration;
        
        // Apply keyframe animations
        foreach ($this->keyframes as $property => $keyframes) {
            $value = $this->interpolateKeyframes($keyframes, $progress);
            $this->applyProperty($property, $value);
        }
        
        // Default fade out effect
        if (!isset($this->keyframes['alpha'])) {
            $alpha = 1 - $progress;
            $color = $this->sprite->getColor();
            $color->a = (int) ($alpha * 255);
            $this->sprite->setColor($color);
        }
    }
    
    private function interpolateKeyframes(array $keyframes, float $progress): float
    {
        if (empty($keyframes)) {
            return 0;
        }
        
        // Simple linear interpolation between keyframes
        $time = $progress * (count($keyframes) - 1);
        $index = floor($time);
        $fraction = $time - $index;
        
        if ($index >= count($keyframes) - 1) {
            return $keyframes[count($keyframes) - 1];
        }
        
        $startValue = $keyframes[$index];
        $endValue = $keyframes[$index + 1];
        
        return $startValue + ($endValue - $startValue) * $fraction;
    }
    
    private function applyProperty(string $property, float $value): void
    {
        switch ($property) {
            case 'alpha':
                $color = $this->sprite->getColor();
                $color->a = (int) ($value * 255);
                $this->sprite->setColor($color);
                break;
                
            case 'scale':
                $this->sprite->setScale(new Vector2($value, $value));
                break;
                
            case 'rotation':
                $this->sprite->setRotation($value);
                break;
                
            case 'x':
                $pos = $this->sprite->getPosition();
                $this->sprite->setPosition(new Vector2($value, $pos->y));
                break;
                
            case 'y':
                $pos = $this->sprite->getPosition();
                $this->sprite->setPosition(new Vector2($pos->x, $value));
                break;
        }
    }
    
    public function addKeyframe(string $property, array $values): void
    {
        $this->keyframes[$property] = $values;
    }
    
    public function isPlaying(): bool
    {
        return $this->playing;
    }
    
    public function isComplete(): bool
    {
        return $this->currentTime >= $this->duration;
    }
    
    public function getProgress(): float
    {
        return $this->currentTime / $this->duration;
    }
}

// 2D Graphics Examples
class Graphics2DExamples
{
    public function demonstrateBasicRendering(): void
    {
        echo "Basic 2D Rendering Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $graphics = new Graphics2DEngine(800, 600);
        
        // Set background color
        $graphics->setBackgroundColor(new Color(135, 206, 235)); // Sky blue
        
        // Create sprites
        $player = new Sprite('player', new Vector2(400, 300), new Vector2(50, 50), 'player.png');
        $player->setColor(new Color(255, 255, 255));
        
        $enemy = new Sprite('enemy', new Vector2(200, 200), new Vector2(40, 40), 'enemy.png');
        $enemy->setColor(new Color(255, 0, 0));
        
        $platform = new Sprite('platform', new Vector2(400, 500), new Vector2(200, 20), 'platform.png');
        $platform->setColor(new Color(139, 69, 19)); // Brown
        
        $background = new Sprite('background', new Vector2(400, 300), new Vector2(800, 600), 'background.png');
        $background->setColor(new Color(255, 255, 255));
        
        // Add sprites to layers
        $graphics->addSprite($background, 'background');
        $graphics->addSprite($platform, 'terrain');
        $graphics->addSprite($player, 'characters');
        $graphics->addSprite($enemy, 'characters');
        
        // Configure camera
        $camera = $graphics->getCamera();
        $camera->setPosition(new Vector2(400, 300));
        $camera->setZoom(1.0);
        
        echo "\nRendering scene:\n";
        $graphics->render();
        
        // Move camera
        echo "\nMoving camera:\n";
        $camera->setPosition(new Vector2(500, 350));
        $camera->setZoom(1.2);
        
        $graphics->render();
        
        // Show statistics
        echo "\nGraphics Statistics:\n";
        echo "  Screen size: {$graphics->getWidth()}x{$graphics->getHeight()}\n";
        echo "  Sprites: {$graphics->getSpriteCount()}\n";
        echo "  Effects: {$graphics->getEffectCount()}\n";
        echo "  Layers: " . count($graphics->getLayers()) . "\n";
    }
    
    public function demonstrateCamera(): void
    {
        echo "\nCamera Demo\n";
        echo str_repeat("-", 15) . "\n";
        
        $graphics = new Graphics2DEngine(800, 600);
        
        // Create sprites in different positions
        $sprites = [];
        for ($i = 0; $i < 5; $i++) {
            $sprite = new Sprite("sprite_$i", new Vector2($i * 150, $i * 100), new Vector2(50, 50));
            $sprite->setColor(new Color(255, $i * 50, 0));
            $graphics->addSprite($sprite);
            $sprites[] = $sprite;
        }
        
        $camera = $graphics->getCamera();
        
        echo "Camera operations:\n";
        
        // Test world to screen conversion
        $worldPos = new Vector2(300, 200);
        $screenPos = $camera->worldToScreen($worldPos);
        echo "World to screen: $worldPos -> $screenPos\n";
        
        // Test screen to world conversion
        $screenPos2 = new Vector2(400, 300);
        $worldPos2 = $camera->screenToWorld($screenPos2);
        echo "Screen to world: $screenPos2 -> $worldPos2\n";
        
        // Test visibility
        $visibleSprite = $sprites[2];
        $isVisible = $camera->isVisible($visibleSprite->getPosition(), $visibleSprite->getSize());
        echo "Sprite '{$visibleSprite->getId()}' visible: " . ($isVisible ? 'Yes' : 'No') . "\n";
        
        // Camera movement
        echo "\nCamera movement:\n";
        echo "Initial position: {$camera->getPosition()}\n";
        
        $camera->setPosition(new Vector2(200, 150));
        echo "After move: {$camera->getPosition()}\n";
        
        // Camera zoom
        echo "\nCamera zoom:\n";
        echo "Initial zoom: {$camera->getZoom()}\n";
        
        $camera->setZoom(1.5);
        echo "After zoom: {$camera->getZoom()}\n";
        
        // Camera rotation
        echo "\nCamera rotation:\n";
        echo "Initial rotation: {$camera->getRotation()}°\n";
        
        $camera->setRotation(45);
        echo "After rotation: {$camera->getRotation()}°\n";
        
        // Camera follow
        echo "\nCamera follow:\n";
        $targetPos = new Vector2(600, 400);
        echo "Following target: $targetPos\n";
        
        for ($i = 0; $i < 5; $i++) {
            $camera->follow($targetPos, 0.3);
            echo "  Step " . ($i + 1) . ": {$camera->getPosition()}\n";
        }
    }
    
    public function demonstrateAnimation(): void
    {
        echo "\nAnimation Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $graphics = new Graphics2DEngine(800, 600);
        
        // Create animation frames
        $walkFrames = [
            ['texture' => 'walk_1.png', 'duration' => 0.1],
            ['texture' => 'walk_2.png', 'duration' => 0.1],
            ['texture' => 'walk_3.png', 'duration' => 0.1],
            ['texture' => 'walk_4.png', 'duration' => 0.1]
        ];
        
        $jumpFrames = [
            ['texture' => 'jump_1.png', 'duration' => 0.2],
            ['texture' => 'jump_2.png', 'duration' => 0.3],
            ['texture' => 'jump_3.png', 'duration' => 0.2]
        ];
        
        // Create animations
        $walkAnimation = new Animation('walk', $walkFrames, 0.1, true);
        $jumpAnimation = new Animation('jump', $jumpFrames, 0.2, false);
        
        $graphics->addAnimation($walkAnimation);
        $graphics->addAnimation($jumpAnimation);
        
        // Create animated sprite
        $player = new AnimatedSprite('player', new Vector2(400, 300), new Vector2(50, 50));
        $player->addAnimation('walk', $walkAnimation);
        $player->addAnimation('jump', $jumpAnimation);
        
        $graphics->addSprite($player);
        
        echo "Created animated sprite with animations:\n";
        echo "  Walk animation: {$walkAnimation->getFrameCount()} frames\n";
        echo "  Jump animation: {$jumpAnimation->getFrameCount()} frames\n";
        
        // Test animation playback
        echo "\nTesting animations:\n";
        
        // Play walk animation
        echo "Playing walk animation...\n";
        $player->setAnimation('walk');
        
        for ($i = 0; $i < 10; $i++) {
            $player->update(0.1);
            echo "  Frame " . ($i + 1) . ": {$player->getCurrentAnimation()} (frame {$player->getAnimation()->getCurrentFrameIndex()})\n";
        }
        
        // Switch to jump animation
        echo "\nSwitching to jump animation...\n";
        $player->setAnimation('jump');
        
        for ($i = 0; $i < 8; $i++) {
            $player->update(0.1);
            echo "  Frame " . ($i + 1) . ": {$player->getCurrentAnimation()} (frame {$player->getAnimation()->getCurrentFrameIndex()})\n";
            
            if ($player->isAnimationComplete()) {
                echo "  Animation complete!\n";
            }
        }
        
        // Show animation statistics
        echo "\nAnimation Statistics:\n";
        echo "  Current animation: {$player->getCurrentAnimation()}\n";
        echo "  Is playing: " . ($player->getAnimation()->isPlaying() ? 'Yes' : 'No') . "\n";
        echo "  Is complete: " . ($player->isAnimationComplete() ? 'Yes' : 'No') . "\n";
        echo "  Progress: " . round($player->getAnimationProgress() * 100, 1) . "%\n";
    }
    
    public function demonstrateVisualEffects(): void
    {
        echo "\nVisual Effects Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $graphics = new Graphics2DEngine(800, 600);
        
        // Create effect sprites
        $explosionSprite = new Sprite('explosion', new Vector2(400, 300), new Vector2(100, 100));
        $explosionSprite->setColor(new Color(255, 200, 0));
        
        $fadeSprite = new Sprite('fade', new Vector2(200, 300), new Vector2(50, 50));
        $fadeSprite->setColor(new Color(255, 255, 255));
        
        $scaleSprite = new Sprite('scale', new Vector2(600, 300), new Vector2(30, 30));
        $scaleSprite->setColor(new Color(0, 255, 0));
        
        // Create effects
        $explosionEffect = new VisualEffect('explosion', $explosionSprite, 2.0);
        $explosionEffect->addKeyframe('scale', [1, 2, 1.5]);
        $explosionEffect->addKeyframe('rotation', [0, 360, 180]);
        
        $fadeEffect = new VisualEffect('fade', $fadeSprite, 3.0);
        $fadeEffect->addKeyframe('alpha', [1, 0.5, 0]);
        
        $scaleEffect = new VisualEffect('scale', $scaleSprite, 2.5);
        $scaleEffect->addKeyframe('scale', [0.5, 2, 1]);
        $scaleEffect->addKeyframe('y', [300, 250, 300]);
        
        // Add effects to graphics engine
        $graphics->addEffect($explosionEffect);
        $graphics->addEffect($fadeEffect);
        $graphics->addEffect($scaleEffect);
        
        echo "Created visual effects:\n";
        echo "  Explosion effect (2.0s)\n";
        echo "  Fade effect (3.0s)\n";
        echo "  Scale effect (2.5s)\n";
        
        // Play effects
        echo "\nPlaying effects...\n";
        $explosionEffect->play();
        $fadeEffect->play();
        $scaleEffect->play();
        
        // Update effects
        echo "\nUpdating effects:\n";
        
        for ($i = 0; $i < 30; $i++) {
            $graphics->update(0.1);
            
            if ($i % 5 === 0) {
                echo "  Time: " . ($i * 0.1) . "s\n";
                echo "    Explosion: " . ($explosionEffect->isPlaying() ? 'Playing' : 'Stopped') . 
                     " (Progress: " . round($explosionEffect->getProgress() * 100, 1) . "%)\n";
                echo "    Fade: " . ($fadeEffect->isPlaying() ? 'Playing' : 'Stopped') . 
                     " (Progress: " . round($fadeEffect->getProgress() * 100, 1) . "%)\n";
                echo "    Scale: " . ($scaleEffect->isPlaying() ? 'Playing' : 'Stopped') . 
                     " (Progress: " . round($scaleEffect->getProgress() * 100, 1) . "%)\n";
            }
            
            if ($explosionEffect->isComplete()) {
                echo "    Explosion effect complete!\n";
            }
        }
        
        // Show final state
        echo "\nFinal effect state:\n";
        echo "  Active effects: {$graphics->getEffectCount()}\n";
        echo "  Explosion complete: " . ($explosionEffect->isComplete() ? 'Yes' : 'No') . "\n";
        echo "  Fade complete: " . ($fadeEffect->isComplete() ? 'Yes' : 'No') . "\n";
        echo "  Scale complete: " . ($scaleEffect->isComplete() ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateParallax(): void
    {
        echo "\nParallax Scrolling Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $graphics = new Graphics2DEngine(800, 600);
        
        // Create background layers
        $farBackground = new Sprite('far_bg', new Vector2(400, 300), new Vector2(1600, 600), 'far_bg.png');
        $farBackground->setColor(new Color(100, 100, 100));
        
        $nearBackground = new Sprite('near_bg', new Vector2(400, 300), new Vector2(1200, 600), 'near_bg.png');
        $nearBackground->setColor(new Color(150, 150, 150));
        
        $foreground = new Sprite('fg', new Vector2(400, 300), new Vector2(800, 600), 'fg.png');
        $foreground->setColor(new Color(200, 200, 200));
        
        // Add to parallax layers
        $graphics->addSprite($farBackground, 'background');
        $graphics->addSprite($nearBackground, 'terrain');
        $graphics->addSprite($foreground, 'objects');
        
        // Configure parallax
        $backgroundLayer = $graphics->getLayer('background');
        $backgroundLayer->setParallax(true);
        $backgroundLayer->setParallaxFactor(new Vector2(0.2, 0.2));
        
        $terrainLayer = $graphics->getLayer('terrain');
        $terrainLayer->setParallax(true);
        $terrainLayer->setParallaxFactor(new Vector2(0.5, 0.5));
        
        echo "Parallax configuration:\n";
        echo "  Background layer: 0.2x parallax\n";
        echo "  Terrain layer: 0.5x parallax\n";
        echo "  Objects layer: No parallax\n";
        
        // Test camera movement with parallax
        $camera = $graphics->getCamera();
        
        echo "\nTesting camera movement with parallax:\n";
        
        $positions = [
            new Vector2(400, 300),
            new Vector2(500, 300),
            new Vector2(600, 300),
            new Vector2(700, 300),
            new Vector2(800, 300)
        ];
        
        foreach ($positions as $i => $pos) {
            echo "  Position " . ($i + 1) . ": $pos\n";
            $camera->setPosition($pos);
            $graphics->render();
        }
        
        // Show layer information
        echo "\nLayer information:\n";
        foreach ($graphics->getLayers() as $name => $layer) {
            echo "  $name:\n";
            echo "    Order: {$layer->getOrder()}\n";
            echo "    Visible: " . ($layer->isVisible() ? 'Yes' : 'No') . "\n";
            echo "    Parallax: " . ($layer->isParallax() ? 'Yes' : 'No') . "\n";
            if ($layer->isParallax()) {
                echo "    Factor: {$layer->getParallaxFactor()}\n";
            }
            echo "    Sprites: " . count($layer->getSprites()) . "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\n2D Graphics Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Rendering Pipeline:\n";
        echo "   • Use layers for proper depth sorting\n";
        echo "   • Implement culling for off-screen objects\n";
        echo "   • Batch similar rendering operations\n";
        echo "   • Use sprite atlases to reduce draw calls\n";
        echo "   • Implement proper z-ordering\n\n";
        
        echo "2. Camera System:\n";
        echo "   • Implement smooth camera following\n";
        echo "   • Use camera bounds to prevent going out of bounds\n";
        echo "   • Implement camera shake for effects\n";
        echo "   • Use parallax scrolling for depth\n";
        echo "   • Implement camera zoom and rotation\n\n";
        
        echo "3. Animation System:\n";
        echo "   • Use keyframe-based animations\n";
        echo "   • Implement animation blending\n";
        echo "   • Use animation states for complex behaviors\n";
        echo "   • Implement animation events\n";
        echo "   • Use animation pooling for performance\n\n";
        
        echo "4. Visual Effects:\n";
        echo "   • Use particle systems for complex effects\n";
        echo "   • Implement effect pooling\n";
        echo "   • Use shader effects for advanced visuals\n";
        echo "   • Implement effect chaining\n";
        echo "   • Use time-based effect updates\n\n";
        
        echo "5. Performance Optimization:\n";
        echo "   • Use object pooling for sprites\n";
        echo "   • Implement dirty flagging\n";
        echo "   • Use spatial partitioning for visibility\n";
        echo "   • Implement level-of-detail systems\n";
        echo "   • Profile rendering bottlenecks";
    }
    
    public function runAllExamples(): void
    {
        echo "2D Graphics Examples\n";
        echo str_repeat("=", 20) . "\n";
        
        $this->demonstrateBasicRendering();
        $this->demonstrateCamera();
        $this->demonstrateAnimation();
        $this->demonstrateVisualEffects();
        $this->demonstrateParallax();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runGraphics2DDemo(): void
{
    $examples = new Graphics2DExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runGraphics2DDemo();
}
?>
