# Advanced Game Development

This file contains comprehensive advanced game development examples in C, including Entity Component System (ECS), rendering system, physics engine, input management, audio system, particle system, animation system, scene management, and game loop implementation.

## 📚 Advanced Game Development Fundamentals

### 🎮 Game Development Concepts
- **Entity Component System**: Flexible architecture for game objects
- **Rendering Pipeline**: 2D/3D graphics rendering with camera system
- **Physics Engine**: Collision detection, physics simulation, and response
- **Input Management**: Keyboard, mouse, and controller input handling
- **Audio System**: Sound loading, playback, and mixing

### 🎯 Game Engine Architecture
- **Component-based**: Modular design with reusable components
- **Data-oriented**: Efficient memory layout for performance
- **Multi-threading**: Parallel processing for performance
- **Scene Management**: Level loading and unloading
- **Game Loop**: Fixed timestep with frame rate control

## 🏗️ Entity Component System (ECS)

### Component Types
```c
// Component types
typedef enum {
    COMPONENT_TRANSFORM = 0,
    COMPONENT_SPRITE = 1,
    COMPONENT_PHYSICS = 2,
    COMPONENT_INPUT = 3,
    COMPONENT_SCRIPT = 4,
    COMPONENT_AUDIO = 5,
    COMPONENT_PARTICLE = 6,
    COMPONENT_CAMERA = 7,
    COMPONENT_LIGHT = 8,
    COMPONENT_UI = 9
} ComponentType;
```

### Entity Structure
```c
// Entity structure
typedef struct {
    int id;
    int component_mask; // Bitmask of active components
    void* components[MAX_COMPONENTS];
    int active;
} Entity;
```

### Transform Component
```c
// Transform component
typedef struct {
    float x, y, z;
    float rotation_x, rotation_y, rotation_z;
    float scale_x, scale_y, scale_z;
    float velocity_x, velocity_y, velocity_z;
    float acceleration_x, acceleration_y, acceleration_z;
} TransformComponent;
```

### Sprite Component
```c
// Sprite component
typedef struct {
    int texture_id;
    int width, height;
    float u, v; // Texture coordinates
    float u_scale, v_scale;
    int visible;
    int layer;
    float opacity;
} SpriteComponent;
```

### Physics Component
```c
// Physics component
typedef struct {
    float mass;
    float friction;
    float restitution; // Bounciness
    float gravity_scale;
    int is_static;
    int collision_layer;
    int collision_mask;
    float bounding_box[4]; // x, y, width, height
} PhysicsComponent;
```

### ECS Implementation
```c
// Initialize game engine
GameEngine* initGameEngine() {
    GameEngine* engine = malloc(sizeof(GameEngine));
    if (!engine) return NULL;
    
    memset(engine, 0, sizeof(GameEngine));
    engine->next_entity_id = 1;
    engine->delta_time = 1.0f / FPS;
    
    return engine;
}

// Create entity
int createEntity(GameEngine* engine) {
    if (!engine || engine->entity_count >= MAX_ENTITIES) {
        return -1;
    }
    
    int entity_id = engine->next_entity_id++;
    
    Entity* entity = &engine->entities[engine->entity_count];
    entity->id = entity_id;
    entity->component_mask = 0;
    entity->active = 1;
    
    // Initialize component pointers to NULL
    for (int i = 0; i < MAX_COMPONENTS; i++) {
        entity->components[i] = NULL;
    }
    
    engine->entity_count++;
    return entity_id;
}

// Add component to entity
int addComponent(GameEngine* engine, int entity_id, ComponentType type, void* data) {
    if (!engine || !data) {
        return -1;
    }
    
    // Find entity
    Entity* entity = NULL;
    for (int i = 0; i < engine->entity_count; i++) {
        if (engine->entities[i].id == entity_id) {
            entity = &engine->entities[i];
            break;
        }
    }
    
    if (!entity) {
        return -1; // Entity not found
    }
    
    // Check if component already exists
    if (entity->component_mask & (1 << type)) {
        return -1; // Component already exists
    }
    
    // Add component
    entity->components[type] = data;
    entity->component_mask |= (1 << type);
    
    return 0;
}

// Get component from entity
void* getComponent(GameEngine* engine, int entity_id, ComponentType type) {
    if (!engine) {
        return NULL;
    }
    
    // Find entity
    Entity* entity = NULL;
    for (int i = 0; i < engine->entity_count; i++) {
        if (engine->entities[i].id == entity_id) {
            entity = &engine->entities[i];
            break;
        }
    }
    
    if (!entity || !(entity->component_mask & (1 << type))) {
        return NULL; // Entity not found or component doesn't exist
    }
    
    return entity->components[type];
}
```

**ECS Benefits**:
- **Flexibility**: Easy to add/remove components from entities
- **Performance**: Data-oriented design for cache efficiency
- **Modularity**: Components can be reused across different entities
- **Scalability**: Supports thousands of entities efficiently

## 🎨 Rendering System

### Color Structure
```c
// Color structure
typedef struct {
    float r, g, b, a;
} Color;
```

### Vertex Structure
```c
// Vertex structure
typedef struct {
    float x, y, z;
    float u, v; // Texture coordinates
    Color color;
} Vertex;
```

### Mesh Structure
```c
// Mesh structure
typedef struct {
    Vertex* vertices;
    int vertex_count;
    int* indices;
    int index_count;
    int texture_id;
} Mesh;
```

### Camera Structure
```c
// Camera structure
typedef struct {
    float x, y, zoom;
    float rotation;
    float viewport_width, viewport_height;
    float near_plane, far_plane;
    float projection_matrix[16];
    float view_matrix[16];
} Camera;
```

### Renderer Implementation
```c
// Initialize renderer
Renderer* initRenderer() {
    Renderer* renderer = malloc(sizeof(Renderer));
    if (!renderer) return NULL;
    
    memset(renderer, 0, sizeof(Renderer));
    
    renderer->camera.x = 0.0f;
    renderer->camera.y = 0.0f;
    renderer->camera.zoom = 1.0f;
    renderer->camera.viewport_width = SCREEN_WIDTH;
    renderer->camera.viewport_height = SCREEN_HEIGHT;
    renderer->background_color = (Color){0.1f, 0.1f, 0.1f, 1.0f};
    
    return renderer;
}

// Create quad mesh
Mesh* createQuadMesh(float x, float y, float width, float height, Color color) {
    Mesh* mesh = createMesh(4, 6);
    if (!mesh) return NULL;
    
    // Create vertices
    mesh->vertices[0] = (Vertex){x, y, 0, 0, 0, color};
    mesh->vertices[1] = (Vertex){x + width, y, 0, 1, 0, color};
    mesh->vertices[2] = (Vertex){x + width, y + height, 0, 1, 1, color};
    mesh->vertices[3] = (Vertex){x, y + height, 0, 0, 1, color};
    
    // Create indices
    mesh->indices[0] = 0;
    mesh->indices[1] = 1;
    mesh->indices[2] = 2;
    mesh->indices[3] = 0;
    mesh->indices[4] = 2;
    mesh->indices[5] = 3;
    
    return mesh;
}

// Render mesh
void renderMesh(Renderer* renderer, Mesh* mesh) {
    if (!renderer || !mesh) return;
    
    // In a real implementation, this would use OpenGL or DirectX
    // For demonstration, we'll simulate rendering
    
    printf("Rendering mesh with %d vertices, %d indices\n", 
           mesh->vertex_count, mesh->index_count);
    
    // Apply camera transform
    for (int i = 0; i < mesh->vertex_count; i++) {
        Vertex* vertex = &mesh->vertices[i];
        
        // Apply camera transform
        vertex->x = (vertex->x - renderer->camera.x) * renderer->camera.zoom;
        vertex->y = (vertex->y - renderer->camera.y) * renderer->camera.zoom;
        
        // Convert to screen coordinates
        vertex->x = vertex->x + SCREEN_WIDTH / 2;
        vertex->y = vertex->y + SCREEN_HEIGHT / 2;
    }
    
    // Add to render queue
    renderer->meshes[renderer->mesh_count++] = mesh;
}
```

**Rendering Benefits**:
- **Hardware Acceleration**: GPU-based rendering for performance
- **Camera System**: Flexible camera with zoom and rotation
- **Batch Rendering**: Efficient rendering of multiple objects
- **Layer Support**: Depth sorting for proper rendering order

## ⚛️ Physics Engine

### Vector2D Structure
```c
// Vector2D structure
typedef struct {
    float x, y;
} Vector2D;
```

### Vector Operations
```c
// Vector operations
Vector2D vec2Create(float x, float y) {
    Vector2D v = {x, y};
    return v;
}

Vector2D vec2Add(Vector2D a, Vector2D b) {
    return vec2Create(a.x + b.x, a.y + b.y);
}

Vector2D vec2Subtract(Vector2D a, Vector2D b) {
    return vec2Create(a.x - b.x, a.y - b.y);
}

Vector2D vec2Multiply(Vector2D v, float scalar) {
    return vec2Create(v.x * scalar, v.y * scalar);
}

float vec2Dot(Vector2D a, Vector2D b) {
    return a.x * b.x + a.y * b.y;
}

float vec2Length(Vector2D v) {
    return sqrtf(v.x * v.x + v.y * v.y);
}

Vector2D vec2Normalize(Vector2D v) {
    float length = vec2Length(v);
    if (length > 0.0f) {
        return vec2Multiply(v, 1.0f / length);
    }
    return v;
}
```

### Collision Detection
```c
// Check AABB collision
int checkAABBCollision(TransformComponent* a, TransformComponent* b, PhysicsComponent* a_physics, PhysicsComponent* b_physics) {
    if (!a || !b || !a_physics || !b_physics) return 0;
    
    float a_left = a->x - a_physics->bounding_box[2] / 2;
    float a_right = a->x + a_physics->bounding_box[2] / 2;
    float a_top = a->y + a_physics->bounding_box[3] / 2;
    float a_bottom = a->y - a_physics->bounding_box[3] / 2;
    
    float b_left = b->x - b_physics->bounding_box[2] / 2;
    float b_right = b->x + b_physics->bounding_box[2] / 2;
    float b_top = b->y + b_physics->bounding_box[3] / 2;
    float b_bottom = b->y - b_physics->bounding_box[3] / 2;
    
    return !(a_left > b_right || a_right < b_left || a_top < b_bottom || a_bottom > b_top);
}
```

### Physics World
```c
// Physics world
typedef struct {
    Vector2D gravity;
    float time_step;
    int velocity_iterations;
    int position_iterations;
    Collision collisions[MAX_ENTITIES];
    int collision_count;
} PhysicsWorld;

// Initialize physics world
PhysicsWorld* initPhysicsWorld() {
    PhysicsWorld* world = malloc(sizeof(PhysicsWorld));
    if (!world) return NULL;
    
    memset(world, 0, sizeof(PhysicsWorld));
    world->gravity = vec2Create(0.0f, -9.81f);
    world->time_step = 1.0f / 60.0f;
    world->velocity_iterations = 8;
    world->position_iterations = 3;
    
    return world;
}
```

### Physics Update
```c
// Update physics
void updatePhysics(GameEngine* engine, float delta_time) {
    if (!engine) return;
    
    PhysicsWorld* world = getComponent(engine, 0, COMPONENT_PHYSICS); // Simplified
    if (!world) return;
    
    // Update positions and velocities
    for (int i = 0; i < engine->entity_count; i++) {
        Entity* entity = &engine->entities[i];
        if (!entity->active) continue;
        
        TransformComponent* transform = getComponent(engine, entity->id, COMPONENT_TRANSFORM);
        PhysicsComponent* physics = getComponent(engine, entity->id, COMPONENT_PHYSICS);
        
        if (!transform || !physics || physics->is_static) continue;
        
        // Apply gravity
        transform->velocity_y += world->gravity.y * physics->gravity_scale * delta_time;
        
        // Apply acceleration to velocity
        transform->velocity_x += transform->acceleration_x * delta_time;
        transform->velocity_y += transform->acceleration_y * delta_time;
        
        // Apply friction
        transform->velocity_x *= (1.0f - physics->friction * delta_time);
        transform->velocity_y *= (1.0f - physics->friction * delta_time);
        
        // Update position
        transform->x += transform->velocity_x * delta_time;
        transform->y += transform->velocity_y * delta_time;
    }
    
    // Collision detection and response
    world->collision_count = 0;
    
    for (int i = 0; i < engine->entity_count; i++) {
        Entity* entity_a = &engine->entities[i];
        if (!entity_a->active) continue;
        
        TransformComponent* transform_a = getComponent(engine, entity_a->id, COMPONENT_TRANSFORM);
        PhysicsComponent* physics_a = getComponent(engine, entity_a->id, COMPONENT_PHYSICS);
        
        if (!transform_a || !physics_a) continue;
        
        for (int j = i + 1; j < engine->entity_count; j++) {
            Entity* entity_b = &engine->entities[j];
            if (!entity_b->active) continue;
            
            TransformComponent* transform_b = getComponent(engine, entity_b->id, COMPONENT_TRANSFORM);
            PhysicsComponent* physics_b = getComponent(engine, entity_b->id, COMPONENT_PHYSICS);
            
            if (!transform_b || !physics_b) continue;
            
            // Check collision layers
            if (!(physics_a->collision_mask & physics_b->collision_layer)) continue;
            
            // Check AABB collision
            if (checkAABBCollision(transform_a, transform_b, physics_a, physics_b)) {
                // Record collision
                Collision* collision = &world->collisions[world->collision_count++];
                collision->entity_a = entity_a->id;
                collision->entity_b = entity_b->id;
                
                // Resolve collision
                resolveCollision(transform_a, transform_b, physics_a, physics_b);
            }
        }
    }
}
```

**Physics Benefits**:
- **Realistic Simulation**: Accurate physics calculations
- **Collision Detection**: Efficient AABB and other collision algorithms
- **Impulse Resolution**: Realistic collision response
- **Performance**: Optimized for real-time applications

## 🎮 Input Management

### Input Manager
```c
// Input manager
typedef struct {
    int keys[256];
    int keys_previous[256];
    int mouse_buttons[3];
    int mouse_buttons_previous[3];
    float mouse_x, mouse_y;
    float mouse_x_previous, mouse_y_previous;
    float mouse_wheel_delta;
    int text_input_enabled;
    char text_input_buffer[256];
    int text_input_length;
} InputManager;
```

### Input Implementation
```c
// Initialize input manager
InputManager* initInputManager() {
    InputManager* input = malloc(sizeof(InputManager));
    if (!input) return NULL;
    
    memset(input, 0, sizeof(InputManager));
    
    return input;
}

// Update input
void updateInput(InputManager* input) {
    if (!input) return;
    
    // Store previous state
    memcpy(input->keys_previous, input->keys, sizeof(input->keys));
    memcpy(input->mouse_buttons_previous, input->mouse_buttons, sizeof(input->mouse_buttons));
    input->mouse_x_previous = input->mouse_x;
    input->mouse_y_previous = input->mouse_y;
    
    // Reset mouse wheel delta
    input->mouse_wheel_delta = 0;
    
    // In a real implementation, this would poll the actual input devices
    // For demonstration, we'll simulate some input
}

// Check if key is pressed
int isKeyPressed(InputManager* input, int key) {
    if (!input) return 0;
    return input->keys[key];
}

// Check if key was just pressed
int isKeyJustPressed(InputManager* input, int key) {
    if (!input) return 0;
    return input->keys[key] && !input->keys_previous[key];
}

// Check if key was just released
int isKeyJustReleased(InputManager* input, int key) {
    if (!input) return 0;
    return !input->keys[key] && input->keys_previous[key];
}
```

**Input Benefits**:
- **State Tracking**: Previous and current input states
- **Event Detection**: Just pressed/released detection
- **Multi-input**: Keyboard, mouse, and controller support
- **Text Input**: Text input handling for UI elements

## 🔊 Audio System

### Sound Structure
```c
// Sound structure
typedef struct {
    int id;
    char filename[256];
    float volume;
    float pitch;
    int loop;
    int playing;
    float position; // Current position in seconds
} Sound;
```

### Audio Engine
```c
// Audio engine
typedef struct {
    Sound sounds[MAX_ENTITIES];
    int sound_count;
    float master_volume;
    float sfx_volume;
    float music_volume;
    int channels;
} AudioEngine;

// Initialize audio engine
AudioEngine* initAudioEngine() {
    AudioEngine* audio = malloc(sizeof(AudioEngine));
    if (!audio) return NULL;
    
    memset(audio, 0, sizeof(AudioEngine));
    audio->master_volume = 1.0f;
    audio->sfx_volume = 1.0f;
    audio->music_volume = 1.0f;
    audio->channels = 32;
    
    return audio;
}

// Load sound
int loadSound(AudioEngine* audio, const char* filename) {
    if (!audio || !filename) return -1;
    
    if (audio->sound_count >= MAX_ENTITIES) {
        return -1; // Maximum sounds reached
    }
    
    Sound* sound = &audio->sounds[audio->sound_count];
    sound->id = audio->sound_count;
    strncpy(sound->filename, filename, sizeof(sound->filename) - 1);
    sound->volume = 1.0f;
    sound->pitch = 1.0f;
    sound->loop = 0;
    sound->playing = 0;
    sound->position = 0.0f;
    
    audio->sound_count++;
    return sound->id;
}

// Play sound
int playSound(AudioEngine* audio, int sound_id, float volume, float pitch, int loop) {
    if (!audio) return -1;
    
    for (int i = 0; i < audio->sound_count; i++) {
        Sound* sound = &audio->sounds[i];
        if (sound->id == sound_id) {
            sound->volume = volume;
            sound->pitch = pitch;
            sound->loop = loop;
            sound->playing = 1;
            sound->position = 0.0f;
            
            printf("Playing sound: %s (volume: %.2f, pitch: %.2f, loop: %d)\n",
                   sound->filename, volume, pitch, loop);
            
            return 0;
        }
    }
    
    return -1; // Sound not found
}
```

**Audio Benefits**:
- **Multi-channel**: Multiple sounds playing simultaneously
- **Volume Control**: Master, SFX, and music volume control
- **Loop Support**: Looping for background music
- **Pitch Control**: Pitch shifting for effects

## ✨ Particle System

### Particle Structure
```c
// Particle structure
typedef struct {
    Vector2D position;
    Vector2D velocity;
    Vector2D acceleration;
    Color color;
    float size;
    float lifetime;
    float max_lifetime;
    int active;
} Particle;
```

### Particle Emitter
```c
// Particle emitter
typedef struct {
    Vector2D position;
    Vector2D direction;
    float spread_angle;
    float emission_rate;
    float emission_timer;
    float particle_lifetime;
    float particle_speed;
    Color start_color;
    Color end_color;
    float start_size;
    float end_size;
    Particle particles[MAX_ENTITIES];
    int particle_count;
    int active;
} ParticleEmitter;
```

### Particle System Implementation
```c
// Initialize particle emitter
ParticleEmitter* initParticleEmitter() {
    ParticleEmitter* emitter = malloc(sizeof(ParticleEmitter));
    if (!emitter) return NULL;
    
    memset(emitter, 0, sizeof(ParticleEmitter));
    emitter->emission_rate = 60.0f;
    emitter->particle_lifetime = 2.0f;
    emitter->particle_speed = 100.0f;
    emitter->start_color = (Color){1.0f, 1.0f, 1.0f, 1.0f};
    emitter->end_color = (Color){1.0f, 1.0f, 1.0f, 0.0f};
    emitter->start_size = 10.0f;
    emitter->end_size = 5.0f;
    emitter->active = 1;
    
    return emitter;
}

// Emit particle
void emitParticle(ParticleEmitter* emitter) {
    if (!emitter || emitter->particle_count >= MAX_ENTITIES) return;
    
    Particle* particle = &emitter->particles[emitter->particle_count];
    
    // Random direction within spread angle
    float angle = atan2f(emitter->direction.y, emitter->direction.x);
    float random_angle = angle + ((float)rand() / RAND_MAX - 0.5f) * emitter->spread_angle;
    
    particle->position = emitter->position;
    particle->velocity = vec2Create(
        cosf(random_angle) * emitter->particle_speed,
        sinf(random_angle) * emitter->particle_speed
    );
    particle->acceleration = vec2Create(0.0f, 0.0f);
    particle->color = emitter->start_color;
    particle->size = emitter->start_size;
    particle->lifetime = 0.0f;
    particle->max_lifetime = emitter->particle_lifetime;
    particle->active = 1;
    
    emitter->particle_count++;
}

// Update particle emitter
void updateParticleEmitter(ParticleEmitter* emitter, float delta_time) {
    if (!emitter || !emitter->active) return;
    
    // Emit new particles
    emitter->emission_timer += delta_time;
    float emission_interval = 1.0f / emitter->emission_rate;
    
    while (emitter->emission_timer >= emission_interval) {
        emitParticle(emitter);
        emitter->emission_timer -= emission_interval;
    }
    
    // Update existing particles
    for (int i = 0; i < emitter->particle_count; i++) {
        Particle* particle = &emitter->particles[i];
        
        if (!particle->active) continue;
        
        // Update lifetime
        particle->lifetime += delta_time;
        
        // Remove dead particles
        if (particle->lifetime >= particle->max_lifetime) {
            particle->active = 0;
            continue;
        }
        
        // Update position
        particle->velocity = vec2Add(particle->velocity, vec2Multiply(particle->acceleration, delta_time));
        particle->position = vec2Add(particle->position, vec2Multiply(particle->velocity, delta_time));
        
        // Update color (interpolate between start and end colors)
        float t = particle->lifetime / particle->max_lifetime;
        particle->color.r = emitter->start_color.r * (1.0f - t) + emitter->end_color.r * t;
        particle->color.g = emitter->start_color.g * (1.0f - t) + emitter->end_color.g * t;
        particle->color.b = emitter->start_color.b * (1.0f - t) + emitter->end_color.b * t;
        particle->color.a = emitter->start_color.a * (1.0f - t) + emitter->end_color.a * t;
        
        // Update size (interpolate between start and end sizes)
        particle->size = emitter->start_size * (1.0f - t) + emitter->end_size * t;
    }
    
    // Remove inactive particles
    int active_count = 0;
    for (int i = 0; i < emitter->particle_count; i++) {
        if (emitter->particles[i].active) {
            emitter->particles[active_count++] = emitter->particles[i];
        }
    }
    emitter->particle_count = active_count;
}
```

**Particle System Benefits**:
- **Visual Effects**: Explosions, smoke, fire, water effects
- **Performance**: Efficient particle pooling and management
- **Customizable**: Configurable emission patterns and properties
- **Interpolation**: Smooth transitions for color and size

## 🎬 Animation System

### Animation Keyframe
```c
// Animation keyframe
typedef struct {
    float time;
    float value;
    int interpolation_type; // 0=linear, 1=ease_in, 2=ease_out, 3=ease_in_out
} AnimationKeyframe;
```

### Animation Track
```c
// Animation track
typedef struct {
    AnimationKeyframe* keyframes;
    int keyframe_count;
    float duration;
    int loop;
    float current_time;
    float current_value;
    int playing;
} AnimationTrack;
```

### Animation Implementation
```c
// Create animation track
AnimationTrack* createAnimationTrack(float duration, int loop) {
    AnimationTrack* track = malloc(sizeof(AnimationTrack));
    if (!track) return NULL;
    
    memset(track, 0, sizeof(AnimationTrack));
    track->duration = duration;
    track->loop = loop;
    track->playing = 0;
    
    return track;
}

// Add keyframe to animation track
void addKeyframe(AnimationTrack* track, float time, float value, int interpolation_type) {
    if (!track) return;
    
    // In a real implementation, this would add the keyframe to the track
    // For demonstration, we'll just print the keyframe
    printf("Added keyframe: time=%.2f, value=%.2f, interpolation=%d\n", 
           time, value, interpolation_type);
}

// Update animation
void updateAnimation(AnimationTrack* track, float delta_time) {
    if (!track || !track->playing) return;
    
    track->current_time += delta_time;
    
    // Check if animation has finished
    if (track->current_time >= track->duration) {
        if (track->loop) {
            track->current_time = fmodf(track->current_time, track->duration);
        } else {
            track->current_time = track->duration;
            track->playing = 0;
        }
    }
    
    // Calculate current value based on keyframes
    // In a real implementation, this would interpolate between keyframes
    track->current_value = sinf(track->current_time * 2.0f * M_PI); // Simple sine wave animation
}
```

**Animation Benefits**:
- **Keyframe Animation**: Precise control over animation timing
- **Interpolation**: Smooth transitions between keyframes
- **Loop Support**: Looping animations for continuous effects
- **Multiple Tracks**: Multiple simultaneous animations

## 🎭 Scene Management

### Scene Structure
```c
// Scene structure
typedef struct {
    char name[256];
    Entity* entities[MAX_ENTITIES];
    int entity_count;
    void (*update)(void* scene, float delta_time);
    void (*render)(void* scene);
    void (*load)(void* scene);
    void (*unload)(void* scene);
    int loaded;
} Scene;
```

### Scene Manager
```c
// Scene manager
typedef struct {
    Scene* scenes[MAX_COMPONENTS];
    int scene_count;
    Scene* current_scene;
    char next_scene[256];
    int transition_active;
} SceneManager;
```

### Scene Management Implementation
```c
// Initialize scene manager
SceneManager* initSceneManager() {
    SceneManager* manager = malloc(sizeof(SceneManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(SceneManager));
    
    return manager;
}

// Load scene
int loadScene(SceneManager* manager, const char* scene_name) {
    if (!manager || !scene_name) return -1;
    
    // Find scene
    Scene* scene = NULL;
    for (int i = 0; i < manager->scene_count; i++) {
        if (strcmp(manager->scenes[i]->name, scene_name) == 0) {
            scene = manager->scenes[i];
            break;
        }
    }
    
    if (!scene) return -1; // Scene not found
    
    // Unload current scene
    if (manager->current_scene && manager->current_scene->unload) {
        manager->current_scene->unload(manager->current_scene);
    }
    
    // Load new scene
    manager->current_scene = scene;
    if (scene->load) {
        scene->load(scene);
    }
    scene->loaded = 1;
    
    printf("Loaded scene: %s\n", scene_name);
    
    return 0;
}

// Update current scene
void updateScene(SceneManager* manager, float delta_time) {
    if (!manager || !manager->current_scene) return;
    
    if (manager->current_scene->update) {
        manager->current_scene->update(manager->current_scene, delta_time);
    }
}

// Render current scene
void renderScene(SceneManager* manager) {
    if (!manager || !manager->current_scene) return;
    
    if (manager->current_scene->render) {
        manager->current_scene->render(manager->current_scene);
    }
}
```

**Scene Management Benefits**:
- **Level Management**: Easy loading and unloading of game levels
- **Memory Management**: Proper cleanup when switching scenes
- **Transitions**: Smooth transitions between scenes
- **State Management**: Scene-specific state handling

## 🔄 Game Loop

### Game Loop Structure
```c
// Game loop
typedef struct {
    int running;
    float target_fps;
    float delta_time;
    float accumulator;
    struct timeval last_time;
    struct timeval current_time;
    int frame_count;
    float fps;
    float fps_timer;
    int fps_frame_count;
} GameLoop;
```

### Game Loop Implementation
```c
// Initialize game loop
GameLoop* initGameLoop(float target_fps) {
    GameLoop* loop = malloc(sizeof(GameLoop));
    if (!loop) return NULL;
    
    memset(loop, 0, sizeof(GameLoop));
    loop->target_fps = target_fps;
    loop->delta_time = 1.0f / target_fps;
    loop->accumulator = 0.0f;
    
    gettimeofday(&loop->last_time, NULL);
    
    return loop;
}

// Update game loop
int updateGameLoop(GameLoop* loop) {
    if (!loop || !loop->running) return 0;
    
    gettimeofday(&loop->current_time, NULL);
    
    // Calculate delta time
    long seconds = loop->current_time.tv_sec - loop->last_time.tv_sec;
    long microseconds = loop->current_time.tv_usec - loop->last_time.tv_usec;
    double delta_time = seconds + microseconds / 1000000.0;
    
    loop->last_time = loop->current_time;
    
    // Fixed timestep with accumulator
    loop->accumulator += delta_time;
    
    int steps = 0;
    while (loop->accumulator >= loop->delta_time && steps < 5) {
        loop->accumulator -= loop->delta_time;
        steps++;
    }
    
    // Update FPS counter
    loop->fps_timer += delta_time;
    loop->fps_frame_count++;
    
    if (loop->fps_timer >= 1.0f) {
        loop->fps = loop->fps_frame_count / loop->fps_timer;
        loop->fps_frame_count = 0;
        loop->fps_timer = 0.0f;
    }
    
    loop->frame_count++;
    
    return steps;
}
```

**Game Loop Benefits**:
- **Fixed Timestep**: Consistent physics simulation regardless of frame rate
- **Frame Rate Control**: Target FPS with delta time calculation
- **FPS Counter**: Performance monitoring
- **Accumulator**: Smooth frame rate handling

## 🔧 Best Practices

### 1. Memory Management
```c
// Good: Component pooling for performance
typedef struct {
    TransformComponent pool[MAX_ENTITIES];
    int used[MAX_ENTITIES];
    int available_count;
} TransformPool;

TransformComponent* getTransformFromPool(TransformPool* pool) {
    if (pool->available_count == 0) return NULL;
    
    int index = pool->used[0];
    // Remove from available list
    for (int i = 0; i < pool->available_count - 1; i++) {
        pool->used[i] = pool->used[i + 1];
    }
    pool->available_count--;
    
    return &pool->pool[index];
}

// Bad: Frequent malloc/free
TransformComponent* createTransform() {
    return malloc(sizeof(TransformComponent));
}

void destroyTransform(TransformComponent* transform) {
    free(transform);
}
```

### 2. Data Locality
```c
// Good: Structure of Arrays for cache efficiency
typedef struct {
    float positions_x[MAX_ENTITIES];
    float positions_y[MAX_ENTITIES];
    float velocities_x[MAX_ENTITIES];
    float velocities_y[MAX_ENTITIES];
    int active[MAX_ENTITIES];
} TransformSystem;

// Bad: Array of Structures (poor cache locality)
typedef struct {
    float x, y;
    float velocity_x, velocity_y;
    int active;
} TransformComponent;

TransformComponent components[MAX_ENTITIES];
```

### 3. Component Design
```c
// Good: Minimal, focused components
typedef struct {
    float x, y;
    float velocity_x, velocity_y;
} PositionComponent;

// Bad: Large, monolithic components
typedef struct {
    float x, y, z;
    float rotation_x, rotation_y, rotation_z;
    float scale_x, scale_y, scale_z;
    float velocity_x, velocity_y, velocity_z;
    float acceleration_x, acceleration_y, acceleration_z;
    float mass, friction, restitution;
    int collision_layer, collision_mask;
    float bounding_box[4];
    int is_static;
    // ... many more fields
} PhysicsComponent;
```

### 4. System Updates
```c
// Good: Batch processing of components
void updateTransformSystem(TransformSystem* system, float delta_time) {
    for (int i = 0; i < MAX_ENTITIES; i++) {
        if (!system->active[i]) continue;
        
        // Update position
        system->positions_x[i] += system->velocities_x[i] * delta_time;
        system->positions_y[i] += system->velocities_y[i] * delta_time;
    }
}

// Bad: Individual entity updates
void updateEntity(Entity* entity, float delta_time) {
    TransformComponent* transform = getComponent(entity, COMPONENT_TRANSFORM);
    if (transform) {
        transform->x += transform->velocity_x * delta_time;
        transform->y += transform->velocity_y * delta_time;
    }
}
```

### 5. Resource Management
```c
// Good: Resource manager with reference counting
typedef struct {
    void* data;
    int ref_count;
    char filename[256];
} Resource;

Resource* loadResource(const char* filename) {
    // Check if already loaded
    Resource* resource = findResource(filename);
    if (resource) {
        resource->ref_count++;
        return resource;
    }
    
    // Load new resource
    resource = malloc(sizeof(Resource));
    resource->data = loadFromFile(filename);
    resource->ref_count = 1;
    strncpy(resource->filename, filename, sizeof(resource->filename) - 1);
    
    addResource(resource);
    return resource;
}

// Bad: Loading resources every time
Texture* loadTexture(const char* filename) {
    return loadTextureFromFile(filename); // Loads from disk every time
}
```

## ⚠️ Common Pitfalls

### 1. Component Coupling
```c
// Wrong: Components referencing each other
typedef struct {
    TransformComponent* transform;
    PhysicsComponent* physics;
} Entity;

// Right: Components are independent
typedef struct {
    int entity_id;
    float x, y;
    float velocity_x, velocity_y;
} TransformComponent;

typedef struct {
    int entity_id;
    float mass, friction;
    float bounding_box[4];
} PhysicsComponent;
```

### 2. System Dependencies
```c
// Wrong: Systems calling each other directly
void updatePhysicsSystem() {
    updateRenderSystem(); // Direct call creates dependency
}

// Right: Systems communicate through events
void updatePhysicsSystem() {
    // Emit collision events
    emitEvent(EVENT_COLLISION, collision_data);
}

void handleCollisionEvent(void* data) {
    // Handle collision
}
```

### 3. Performance Issues
```c
// Wrong: O(n²) collision detection
for (int i = 0; i < entity_count; i++) {
    for (int j = 0; j < entity_count; j++) {
        checkCollision(entities[i], entities[j]);
    }
}

// Right: Spatial partitioning for O(n) collision detection
void updateSpatialGrid() {
    // Partition entities into grid cells
    for (int i = 0; i < entity_count; i++) {
        addToGrid(entities[i]);
    }
    
    // Only check collisions within same cell
    for (int cell = 0; cell < grid_cell_count; cell++) {
        checkCollisionsInCell(cell);
    }
}
```

### 4. Memory Fragmentation
```c
// Wrong: Frequent small allocations
void createParticles() {
    for (int i = 0; i < 1000; i++) {
        Particle* particle = malloc(sizeof(Particle)); // Fragmentation
        particles[i] = particle;
    }
}

// Right: Object pooling
void createParticles() {
    for (int i = 0; i < 1000; i++) {
        Particle* particle = getParticleFromPool(); // No fragmentation
        particles[i] = particle;
    }
}
```

## 🔧 Real-World Applications

### 1. 2D Platformer
```c
// Platformer game with physics
void updatePlatformer(GameEngine* engine, float delta_time) {
    // Update player physics
    Entity* player = findPlayer(engine);
    TransformComponent* transform = getComponent(engine, player->id, COMPONENT_TRANSFORM);
    PhysicsComponent* physics = getComponent(engine, player->id, COMPONENT_PHYSICS);
    
    // Apply gravity
    transform->velocity_y += GRAVITY * delta_time;
    
    // Handle input
    if (isKeyPressed(input, 'A')) {
        transform->velocity_x = -PLAYER_SPEED;
    } else if (isKeyPressed(input, 'D')) {
        transform->velocity_x = PLAYER_SPEED;
    }
    
    // Check platform collisions
    checkPlatformCollisions(player);
    
    // Update position
    transform->x += transform->velocity_x * delta_time;
    transform->y += transform->velocity_y * delta_time;
}
```

### 2. Tower Defense
```c
// Tower defense game
void updateTowerDefense(GameEngine* engine, float delta_time) {
    // Update enemies
    for (int i = 0; i < enemy_count; i++) {
        Enemy* enemy = enemies[i];
        updateEnemyMovement(enemy, delta_time);
        
        // Check if enemy reached end
        if (enemy->position >= path_end) {
            player_lives--;
            removeEnemy(enemy);
        }
    }
    
    // Update towers
    for (int i = 0; i < tower_count; i++) {
        Tower* tower = towers[i];
        updateTowerTargeting(tower, enemies);
        updateTowerShooting(tower, delta_time);
    }
    
    // Update projectiles
    updateProjectiles(delta_time);
}
```

### 3. RPG Game
```c
// RPG game with inventory and combat
void updateRPG(GameEngine* engine, float delta_time) {
    // Update player
    Player* player = getPlayer(engine);
    updatePlayerMovement(player, input, delta_time);
    updatePlayerAnimation(player, delta_time);
    
    // Update NPCs
    for (int i = 0; i < npc_count; i++) {
        NPC* npc = npcs[i];
        updateNPCAI(npc, player, delta_time);
        updateNPCAnimation(npc, delta_time);
    }
    
    // Update combat
    updateCombatSystem(delta_time);
    
    // Update inventory
    updateInventory(player, input);
    
    // Update UI
    updateUI(player, game_state);
}
```

## 📚 Further Reading

### Books
- "Game Programming Patterns" by Robert Nystrom
- "Game Engine Architecture" by Jason Gregory
- "Real-Time Rendering" by Tomas Akenine-Möller

### Topics
- OpenGL and DirectX graphics programming
- Advanced physics simulations
- Artificial intelligence in games
- Multiplayer networking
- Mobile game development

Advanced game development in C provides the foundation for building high-performance, engaging, and scalable games. Master these techniques to create professional-quality game engines and games that run smoothly on a variety of platforms!
