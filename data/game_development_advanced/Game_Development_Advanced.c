#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <time.h>
#include <unistd.h>
#include <pthread.h>
#include <sys/time.h>

// =============================================================================
// ADVANCED GAME DEVELOPMENT
// =============================================================================

#define MAX_ENTITIES 1000
#define MAX_COMPONENTS 10
#define MAX_SYSTEMS 20
#define SCREEN_WIDTH 800
#define SCREEN_HEIGHT 600
#define FPS 60
#define FRAME_TIME (1000 / FPS)

// =============================================================================
// ENTITY COMPONENT SYSTEM (ECS)
// =============================================================================

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

// Entity structure
typedef struct {
    int id;
    int component_mask; // Bitmask of active components
    void* components[MAX_COMPONENTS];
    int active;
} Entity;

// Component base structure
typedef struct {
    ComponentType type;
    int entity_id;
    void* data;
} Component;

// Transform component
typedef struct {
    float x, y, z;
    float rotation_x, rotation_y, rotation_z;
    float scale_x, scale_y, scale_z;
    float velocity_x, velocity_y, velocity_z;
    float acceleration_x, acceleration_y, acceleration_z;
} TransformComponent;

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

// Input component
typedef struct {
    int keys[256];
    int mouse_buttons[3];
    float mouse_x, mouse_y;
    float mouse_delta_x, mouse_delta_y;
    int key_pressed[256];
    int key_released[256];
} InputComponent;

// =============================================================================
// GAME ENGINE CORE
// =============================================================================

// Game engine structure
typedef struct {
    Entity entities[MAX_ENTITIES];
    int entity_count;
    int next_entity_id;
    void* systems[MAX_SYSTEMS];
    int system_count;
    int running;
    float delta_time;
    float total_time;
    int frame_count;
} GameEngine;

// System base structure
typedef struct {
    void (*update)(GameEngine* engine, float delta_time);
    void (*render)(GameEngine* engine);
    int priority;
    int active;
} System;

// =============================================================================
// RENDERING SYSTEM
// =============================================================================

// Color structure
typedef struct {
    float r, g, b, a;
} Color;

// Vertex structure
typedef struct {
    float x, y, z;
    float u, v; // Texture coordinates
    Color color;
} Vertex;

// Mesh structure
typedef struct {
    Vertex* vertices;
    int vertex_count;
    int* indices;
    int index_count;
    int texture_id;
} Mesh;

// Camera structure
typedef struct {
    float x, y, zoom;
    float rotation;
    float viewport_width, viewport_height;
    float near_plane, far_plane;
    float projection_matrix[16];
    float view_matrix[16];
} Camera;

// Renderer structure
typedef struct {
    Camera camera;
    Mesh* meshes[MAX_ENTITIES];
    int mesh_count;
    Color background_color;
    int vsync_enabled;
    int fullscreen;
} Renderer;

// =============================================================================
// PHYSICS ENGINE
// =============================================================================

// Vector2D structure
typedef struct {
    float x, y;
} Vector2D;

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

// Collision detection
typedef struct {
    int entity_a;
    int entity_b;
    Vector2D normal;
    float penetration;
    int is_trigger;
} Collision;

// Physics world
typedef struct {
    Vector2D gravity;
    float time_step;
    int velocity_iterations;
    int position_iterations;
    Collision collisions[MAX_ENTITIES];
    int collision_count;
} PhysicsWorld;

// =============================================================================
// INPUT SYSTEM
// =============================================================================

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

// =============================================================================
// AUDIO SYSTEM
// =============================================================================

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

// Audio engine
typedef struct {
    Sound sounds[MAX_ENTITIES];
    int sound_count;
    float master_volume;
    float sfx_volume;
    float music_volume;
    int channels;
} AudioEngine;

// =============================================================================
// PARTICLE SYSTEM
// =============================================================================

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

// =============================================================================
// ANIMATION SYSTEM
// =============================================================================

// Animation keyframe
typedef struct {
    float time;
    float value;
    int interpolation_type; // 0=linear, 1=ease_in, 2=ease_out, 3=ease_in_out
} AnimationKeyframe;

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

// Animation system
typedef struct {
    AnimationTrack tracks[MAX_COMPONENTS];
    int track_count;
    float global_time;
} AnimationSystem;

// =============================================================================
// SCENE MANAGEMENT
// =============================================================================

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

// Scene manager
typedef struct {
    Scene* scenes[MAX_COMPONENTS];
    int scene_count;
    Scene* current_scene;
    char next_scene[256];
    int transition_active;
} SceneManager;

// =============================================================================
// GAME LOOP
// =============================================================================

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

// =============================================================================
// ECS IMPLEMENTATION
// =============================================================================

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

// Remove component from entity
int removeComponent(GameEngine* engine, int entity_id, ComponentType type) {
    if (!engine) {
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
    
    // Remove component
    if (entity->components[type]) {
        free(entity->components[type]);
        entity->components[type] = NULL;
    }
    
    entity->component_mask &= ~(1 << type);
    
    return 0;
}

// =============================================================================
// RENDERING SYSTEM IMPLEMENTATION
// =============================================================================

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

// Create mesh
Mesh* createMesh(int vertex_count, int index_count) {
    Mesh* mesh = malloc(sizeof(Mesh));
    if (!mesh) return NULL;
    
    mesh->vertices = malloc(vertex_count * sizeof(Vertex));
    mesh->indices = malloc(index_count * sizeof(int));
    mesh->vertex_count = vertex_count;
    mesh->index_count = index_count;
    mesh->texture_id = -1;
    
    return mesh;
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

// Clear screen
void clearScreen(Renderer* renderer) {
    if (!renderer) return;
    
    // In a real implementation, this would clear the framebuffer
    printf("Clearing screen with color (%.2f, %.2f, %.2f, %.2f)\n",
           renderer->background_color.r, renderer->background_color.g,
           renderer->background_color.b, renderer->background_color.a);
}

// Present frame
void presentFrame(Renderer* renderer) {
    if (!renderer) return;
    
    // In a real implementation, this would swap buffers
    printf("Presenting frame with %d meshes\n", renderer->mesh_count);
    
    renderer->mesh_count = 0; // Clear render queue
}

// =============================================================================
// PHYSICS ENGINE IMPLEMENTATION
// =============================================================================

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

// Resolve collision
void resolveCollision(TransformComponent* a, TransformComponent* b, PhysicsComponent* a_physics, PhysicsComponent* b_physics) {
    if (!a || !b || !a_physics || !b_physics) return;
    
    // Calculate collision normal
    Vector2D center_a = vec2Create(a->x, a->y);
    Vector2D center_b = vec2Create(b->x, b->y);
    Vector2D normal = vec2Normalize(vec2Subtract(center_b, center_a));
    
    // Calculate relative velocity
    Vector2D relative_velocity = vec2Subtract(
        vec2Create(b->velocity_x, b->velocity_y),
        vec2Create(a->velocity_x, a->velocity_y)
    );
    
    // Calculate relative velocity along collision normal
    float velocity_along_normal = vec2Dot(relative_velocity, normal);
    
    // Don't resolve if velocities are separating
    if (velocity_along_normal > 0) return;
    
    // Calculate restitution
    float restitution = fminf(a_physics->restitution, b_physics->restitution);
    
    // Calculate impulse scalar
    float impulse_scalar = -(1 + restitution) * velocity_along_normal;
    impulse_scalar /= (1 / a_physics->mass + 1 / b_physics->mass);
    
    // Apply impulse
    Vector2D impulse = vec2Multiply(normal, impulse_scalar);
    
    a->velocity_x -= impulse.x / a_physics->mass;
    a->velocity_y -= impulse.y / a_physics->mass;
    b->velocity_x += impulse.x / b_physics->mass;
    b->velocity_y += impulse.y / b_physics->mass;
}

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

// =============================================================================
// INPUT SYSTEM IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// AUDIO SYSTEM IMPLEMENTATION
// =============================================================================

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

// Stop sound
int stopSound(AudioEngine* audio, int sound_id) {
    if (!audio) return -1;
    
    for (int i = 0; i < audio->sound_count; i++) {
        Sound* sound = &audio->sounds[i];
        if (sound->id == sound_id) {
            sound->playing = 0;
            sound->position = 0.0f;
            
            printf("Stopped sound: %s\n", sound->filename);
            return 0;
        }
    }
    
    return -1; // Sound not found
}

// =============================================================================
// PARTICLE SYSTEM IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// ANIMATION SYSTEM IMPLEMENTATION
// =============================================================================

// Initialize animation system
AnimationSystem* initAnimationSystem() {
    AnimationSystem* system = malloc(sizeof(AnimationSystem));
    if (!system) return NULL;
    
    memset(system, 0, sizeof(AnimationSystem));
    
    return system;
}

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

// =============================================================================
// SCENE MANAGEMENT IMPLEMENTATION
// =============================================================================

// Initialize scene manager
SceneManager* initSceneManager() {
    SceneManager* manager = malloc(sizeof(SceneManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(SceneManager));
    
    return manager;
}

// Add scene
int addScene(SceneManager* manager, Scene* scene) {
    if (!manager || !scene || manager->scene_count >= MAX_COMPONENTS) {
        return -1;
    }
    
    manager->scenes[manager->scene_count++] = scene;
    
    return 0;
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

// =============================================================================
// GAME LOOP IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateECS() {
    printf("=== ECS DEMO ===\n");
    
    // Initialize game engine
    GameEngine* engine = initGameEngine();
    if (!engine) {
        printf("Failed to initialize game engine\n");
        return;
    }
    
    printf("Game engine initialized\n");
    
    // Create entities
    int player_id = createEntity(engine);
    int enemy_id = createEntity(engine);
    int bullet_id = createEntity(engine);
    
    printf("Created entities: Player=%d, Enemy=%d, Bullet=%d\n", player_id, enemy_id, bullet_id);
    
    // Create components
    TransformComponent* player_transform = malloc(sizeof(TransformComponent));
    player_transform->x = 100.0f;
    player_transform->y = 100.0f;
    player_transform->velocity_x = 0.0f;
    player_transform->velocity_y = 0.0f;
    
    SpriteComponent* player_sprite = malloc(sizeof(SpriteComponent));
    player_sprite->texture_id = 0;
    player_sprite->width = 32;
    player_sprite->height = 32;
    player_sprite->visible = 1;
    
    PhysicsComponent* player_physics = malloc(sizeof(PhysicsComponent));
    player_physics->mass = 1.0f;
    player_physics->friction = 0.1f;
    player_physics->restitution = 0.5f;
    player_physics->bounding_box[0] = 32.0f;
    player_physics->bounding_box[1] = 32.0f;
    
    // Add components to player
    addComponent(engine, player_id, COMPONENT_TRANSFORM, player_transform);
    addComponent(engine, player_id, COMPONENT_SPRITE, player_sprite);
    addComponent(engine, player_id, COMPONENT_PHYSICS, player_physics);
    
    printf("Added components to player entity\n");
    
    // Get component
    TransformComponent* retrieved_transform = getComponent(engine, player_id, COMPONENT_TRANSFORM);
    if (retrieved_transform) {
        printf("Retrieved player position: (%.2f, %.2f)\n", 
               retrieved_transform->x, retrieved_transform->y);
    }
    
    // Update position
    retrieved_transform->x = 150.0f;
    retrieved_transform->y = 150.0f;
    
    printf("Updated player position: (%.2f, %.2f)\n", 
           retrieved_transform->x, retrieved_transform->y);
    
    printf("Total entities: %d\n", engine->entity_count);
    
    // Clean up
    free(engine);
}

void demonstrateRendering() {
    printf("\n=== RENDERING DEMO ===\n");
    
    // Initialize renderer
    Renderer* renderer = initRenderer();
    if (!renderer) {
        printf("Failed to initialize renderer\n");
        return;
    }
    
    printf("Renderer initialized\n");
    printf("Camera: (%.2f, %.2f, %.2f)\n", 
           renderer->camera.x, renderer->camera.y, renderer->camera.zoom);
    
    // Create meshes
    Color red = {1.0f, 0.0f, 0.0f, 1.0f};
    Color green = {0.0f, 1.0f, 0.0f, 1.0f};
    Color blue = {0.0f, 0.0f, 1.0f, 1.0f};
    
    Mesh* quad1 = createQuadMesh(0, 0, 100, 100, red);
    Mesh* quad2 = createQuadMesh(150, 0, 100, 100, green);
    Mesh* quad3 = createQuadMesh(75, 150, 100, 100, blue);
    
    printf("Created %d meshes\n", 3);
    
    // Render meshes
    clearScreen(renderer);
    renderMesh(renderer, quad1);
    renderMesh(renderer, quad2);
    renderMesh(renderer, quad3);
    presentFrame(renderer);
    
    // Update camera
    renderer->camera.x = 50.0f;
    renderer->camera.y = 50.0f;
    renderer->camera.zoom = 1.5f;
    
    printf("Updated camera: (%.2f, %.2f, %.2f)\n", 
           renderer->camera.x, renderer->camera.y, renderer->camera.zoom);
    
    // Render again with new camera
    clearScreen(renderer);
    renderMesh(renderer, quad1);
    renderMesh(renderer, quad2);
    renderMesh(renderer, quad3);
    presentFrame(renderer);
    
    // Clean up
    free(quad1);
    free(quad2);
    free(quad3);
    free(renderer);
}

void demonstratePhysics() {
    printf("\n=== PHYSICS DEMO ===\n");
    
    // Initialize physics world
    PhysicsWorld* world = initPhysicsWorld();
    if (!world) {
        printf("Failed to initialize physics world\n");
        return;
    }
    
    printf("Physics world initialized\n");
    printf("Gravity: (%.2f, %.2f)\n", world->gravity.x, world->gravity.y);
    printf("Time step: %.3f seconds\n", world->time_step);
    
    // Create physics objects
    TransformComponent box1 = {100.0f, 100.0f, 0.0f, 0.0f, 0.0f, 0.0f, 1.0f, 1.0f, 1.0f, 0.0f, 0.0f, 0.0f, 0.0f, 0.0f};
    TransformComponent box2 = {200.0f, 100.0f, 0.0f, 0.0f, 0.0f, 0.0f, 1.0f, 1.0f, 1.0f, 50.0f, 0.0f, 0.0f, 0.0f, 0.0f};
    
    PhysicsComponent physics1 = {1.0f, 0.1f, 0.5f, 1.0f, 0, 1, 1, {32.0f, 32.0f}};
    PhysicsComponent physics2 = {2.0f, 0.1f, 0.5f, 1.0f, 1, 2, 1, {32.0f, 32.0f}};
    
    printf("Created physics objects:\n");
    printf("Box 1: position=(%.2f, %.2f), mass=%.2f\n", box1.x, box1.y, physics1.mass);
    printf("Box 2: position=(%.2f, %.2f), mass=%.2f\n", box2.x, box2.y, physics2.mass);
    
    // Simulate physics
    float delta_time = 0.016f; // 60 FPS
    
    printf("\nSimulating physics for %.3f seconds:\n", delta_time);
    
    // Apply gravity
    box1.velocity_y += world->gravity.y * physics1.gravity_scale * delta_time;
    box2.velocity_y += world->gravity.y * physics2.gravity_scale * delta_time;
    
    // Update positions
    box1.x += box1.velocity_x * delta_time;
    box1.y += box1.velocity_y * delta_time;
    box2.x += box2.velocity_x * delta_time;
    box2.y += box2.velocity_y * delta_time;
    
    printf("After update:\n");
    printf("Box 1: position=(%.2f, %.2f), velocity=(%.2f, %.2f)\n", 
           box1.x, box1.y, box1.velocity_x, box1.velocity_y);
    printf("Box 2: position=(%.2f, %.2f), velocity=(%.2f, %.2f)\n", 
           box2.x, box2.y, box2.velocity_x, box2.velocity_y);
    
    // Check collision
    int collision = checkAABBCollision(&box1, &box2, &physics1, &physics2);
    printf("Collision detected: %s\n", collision ? "Yes" : "No");
    
    if (collision) {
        resolveCollision(&box1, &box2, &physics1, &physics2);
        printf("After collision resolution:\n");
        printf("Box 1: velocity=(%.2f, %.2f)\n", box1.velocity_x, box1.velocity_y);
        printf("Box 2: velocity=(%.2f, %.2f)\n", box2.velocity_x, box2.velocity_y);
    }
    
    free(world);
}

void demonstrateInput() {
    printf("\n=== INPUT DEMO ===\n");
    
    // Initialize input manager
    InputManager* input = initInputManager();
    if (!input) {
        printf("Failed to initialize input manager\n");
        return;
    }
    
    printf("Input manager initialized\n");
    
    // Simulate input
    input->keys['W'] = 1;
    input->keys['A'] = 1;
    input->keys['S'] = 0;
    input->keys['D'] = 0;
    input->mouse_buttons[0] = 1;
    input->mouse_x = 400.0f;
    input->mouse_y = 300.0f;
    
    printf("Simulated input state:\n");
    printf("Keys: W=%s, A=%s, S=%s, D=%s\n",
           input->keys['W'] ? "Pressed" : "Released",
           input->keys['A'] ? "Pressed" : "Released",
           input->keys['S'] ? "Pressed" : "Released",
           input->keys['D'] ? "Pressed" : "Released");
    printf("Mouse: Left=%s, Position=(%.0f, %.0f)\n",
           input->mouse_buttons[0] ? "Pressed" : "Released",
           input->mouse_x, input->mouse_y);
    
    // Update input (simulate state change)
    updateInput(input);
    
    input->keys['W'] = 0;
    input->keys['S'] = 1;
    input->mouse_buttons[0] = 0;
    input->mouse_x = 410.0f;
    input->mouse_y = 310.0f;
    
    printf("\nAfter update:\n");
    printf("Keys: W=%s, A=%s, S=%s, D=%s\n",
           input->keys['W'] ? "Pressed" : "Released",
           input->keys['A'] ? "Pressed" : "Released",
           input->keys['S'] ? "Pressed" : "Released",
           input->keys['D'] ? "Pressed" : "Released");
    printf("Mouse: Left=%s, Position=(%.0f, %.0f)\n",
           input->mouse_buttons[0] ? "Pressed" : "Released",
           input->mouse_x, input->mouse_y);
    
    // Check input states
    printf("\nInput states:\n");
    printf("W is %s\n", isKeyPressed(input, 'W') ? "Pressed" : "Released");
    printf("W was just %s\n", isKeyJustPressed(input, 'W') ? "Pressed" : "Released");
    printf("W was just %s\n", isKeyJustReleased(input, 'W') ? "Released" : "Pressed");
    printf("S was just %s\n", isKeyJustPressed(input, 'S') ? "Pressed" : "Released");
    
    free(input);
}

void demonstrateAudio() {
    printf("\n=== AUDIO DEMO ===\n");
    
    // Initialize audio engine
    AudioEngine* audio = initAudioEngine();
    if (!audio) {
        printf("Failed to initialize audio engine\n");
        return;
    }
    
    printf("Audio engine initialized\n");
    printf("Channels: %d\n", audio->channels);
    printf("Master volume: %.2f\n", audio->master_volume);
    printf("SFX volume: %.2f\n", audio->sfx_volume);
    printf("Music volume: %.2f\n", audio->music_volume);
    
    // Load sounds
    int laser_id = loadSound(audio, "laser.wav");
    int explosion_id = loadSound(audio, "explosion.wav");
    int music_id = loadSound(audio, "background_music.ogg");
    
    printf("Loaded sounds:\n");
    printf("Laser: ID %d\n", laser_id);
    printf("Explosion: ID %d\n", explosion_id);
    printf("Background Music: ID %d\n", music_id);
    
    // Play sounds
    playSound(audio, laser_id, 0.8f, 1.0f, 0);
    playSound(audio, music_id, 0.5f, 1.0f, 1);
    
    printf("Playing laser sound (volume: 0.8, pitch: 1.0, loop: no)\n");
    printf("Playing background music (volume: 0.5, pitch: 1.0, loop: yes)\n");
    
    // Update volumes
    audio->master_volume = 0.7f;
    audio->sfx_volume = 0.9f;
    audio->music_volume = 0.6f;
    
    printf("Updated volumes:\n");
    printf("Master: %.2f, SFX: %.2f, Music: %.2f\n",
           audio->master_volume, audio->sfx_volume, audio->music_volume);
    
    // Stop laser sound
    stopSound(audio, laser_id);
    printf("Stopped laser sound\n");
    
    free(audio);
}

void demonstrateParticles() {
    printf("\n=== PARTICLE DEMO ===\n");
    
    // Initialize particle emitter
    ParticleEmitter* emitter = initParticleEmitter();
    if (!emitter) {
        printf("Failed to initialize particle emitter\n");
        return;
    }
    
    printf("Particle emitter initialized\n");
    printf("Emission rate: %.1f particles/second\n", emitter->emission_rate);
    printf("Particle lifetime: %.1f seconds\n", emitter->particle_lifetime);
    printf("Particle speed: %.1f units/second\n", emitter->particle_speed);
    
    // Set emitter properties
    emitter->position = vec2Create(400.0f, 300.0f);
    emitter->direction = vec2Create(0.0f, 1.0f); // Upward
    emitter->spread_angle = M_PI / 4; // 45 degrees
    emitter->start_color = (Color){1.0f, 0.5f, 0.0f, 1.0f}; // Orange
    emitter->end_color = (Color){1.0f, 0.0f, 0.0f, 0.0f}; // Red, transparent
    emitter->start_size = 20.0f;
    emitter->end_size = 5.0f;
    
    printf("Emitter position: (%.1f, %.1f)\n", emitter->position.x, emitter->position.y);
    printf("Emitter direction: (%.1f, %.1f)\n", emitter->direction.x, emitter->direction.y);
    printf("Spread angle: %.1f radians\n", emitter->spread_angle);
    
    // Update particle system
    float delta_time = 0.016f; // 60 FPS
    
    printf("\nUpdating particle system for %.3f seconds:\n", delta_time);
    updateParticleEmitter(emitter, delta_time);
    
    printf("Active particles: %d\n", emitter->particle_count);
    
    // Update a few more times
    for (int i = 0; i < 5; i++) {
        updateParticleEmitter(emitter, delta_time);
        printf("Frame %d: Active particles: %d\n", i + 1, emitter->particle_count);
    }
    
    // Display particle information
    if (emitter->particle_count > 0) {
        Particle* particle = &emitter->particles[0];
        printf("\nFirst particle:\n");
        printf("Position: (%.2f, %.2f)\n", particle->position.x, particle->position.y);
        printf("Velocity: (%.2f, %.2f)\n", particle->velocity.x, particle->velocity.y);
        printf("Color: (%.2f, %.2f, %.2f, %.2f)\n", 
               particle->color.r, particle->color.g, particle->color.b, particle->color.a);
        printf("Size: %.2f\n", particle->size);
        printf("Lifetime: %.2f / %.2f\n", particle->lifetime, particle->max_lifetime);
    }
    
    free(emitter);
}

void demonstrateAnimation() {
    printf("\n=== ANIMATION DEMO ===\n");
    
    // Initialize animation system
    AnimationSystem* system = initAnimationSystem();
    if (!system) {
        printf("Failed to initialize animation system\n");
        return;
    }
    
    printf("Animation system initialized\n");
    
    // Create animation track
    AnimationTrack* track = createAnimationTrack(2.0f, 1); // 2 seconds, looping
    if (!track) {
        printf("Failed to create animation track\n");
        free(system);
        return;
    }
    
    printf("Animation track created:\n");
    printf("Duration: %.1f seconds\n", track->duration);
    printf("Loop: %s\n", track->loop ? "Yes" : "No");
    
    // Add keyframes
    addKeyframe(track, 0.0f, 0.0f, 0); // Start at 0
    addKeyframe(track, 0.5f, 1.0f, 1); // Ease in to 1
    addKeyframe(track, 1.0f, 0.5f, 2); // Ease out to 0.5
    addKeyframe(track, 1.5f, 1.5f, 3); // Ease in-out to 1.5
    addKeyframe(track, 2.0f, 0.0f, 0); // End at 0
    
    // Start animation
    track->playing = 1;
    printf("Animation started\n");
    
    // Update animation
    float delta_time = 0.016f; // 60 FPS
    
    for (int i = 0; i < 10; i++) {
        updateAnimation(track, delta_time);
        printf("Frame %d: Time=%.2f, Value=%.3f\n", 
               i + 1, track->current_time, track->current_value);
    }
    
    // Check if animation is still playing
    printf("Animation playing: %s\n", track->playing ? "Yes" : "No");
    
    free(track);
    free(system);
}

void demonstrateSceneManagement() {
    printf("\n=== SCENE MANAGEMENT DEMO ===\n");
    
    // Initialize scene manager
    SceneManager* manager = initSceneManager();
    if (!manager) {
        printf("Failed to initialize scene manager\n");
        return;
    }
    
    printf("Scene manager initialized\n");
    
    // Create scenes
    Scene main_menu = {
        .name = "MainMenu",
        .entity_count = 0,
        .update = NULL,
        .render = NULL,
        .load = NULL,
        .unload = NULL,
        .loaded = 0
    };
    
    Scene game_play = {
        .name = "GamePlay",
        .entity_count = 0,
        .update = NULL,
        .render = NULL,
        .load = NULL,
        .unload = NULL,
        .loaded = 0
    };
    
    Scene options = {
        .name = "Options",
        .entity_count = 0,
        .update = NULL,
        .render = NULL,
        .load = NULL,
        .unload = NULL,
        .loaded = 0
    };
    
    // Add scenes
    addScene(manager, &main_menu);
    addScene(manager, &game_play);
    addScene(manager, &options);
    
    printf("Added %d scenes\n", manager->scene_count);
    
    // Load main menu scene
    loadScene(manager, "MainMenu");
    printf("Current scene: %s\n", manager->current_scene->name);
    printf("Scene loaded: %s\n", manager->current_scene->loaded ? "Yes" : "No");
    
    // Load game play scene
    loadScene(manager, "GamePlay");
    printf("Current scene: %s\n", manager->current_scene->name);
    printf("Scene loaded: %s\n", manager->current_scene->loaded ? "Yes" : "No");
    
    // Update current scene
    float delta_time = 0.016f;
    updateScene(manager, delta_time);
    printf("Updated scene with delta time: %.3f\n", delta_time);
    
    // Render current scene
    renderScene(manager);
    printf("Rendered scene\n");
    
    free(manager);
}

void demonstrateGameLoop() {
    printf("\n=== GAME LOOP DEMO ===\n");
    
    // Initialize game loop
    GameLoop* loop = initGameLoop(60.0f);
    if (!loop) {
        printf("Failed to initialize game loop\n");
        return;
    }
    
    printf("Game loop initialized\n");
    printf("Target FPS: %.1f\n", loop->target_fps);
    printf("Delta time: %.3f seconds\n", loop->delta_time);
    
    // Start game loop
    loop->running = 1;
    printf("Game loop started\n");
    
    // Simulate a few frames
    for (int i = 0; i < 5; i++) {
        int steps = updateGameLoop(loop);
        printf("Frame %d: %d update steps, FPS: %.1f\n", 
               loop->frame_count, steps, loop->fps);
        
        // Simulate some delay
        usleep(1000); // 1ms
    }
    
    // Stop game loop
    loop->running = 0;
    printf("Game loop stopped\n");
    
    printf("Total frames: %d\n", loop->frame_count);
    
    free(loop);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Game Development Examples\n");
    printf("=================================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // Run all demonstrations
    demonstrateECS();
    demonstrateRendering();
    demonstratePhysics();
    demonstrateInput();
    demonstrateAudio();
    demonstrateParticles();
    demonstrateAnimation();
    demonstrateSceneManagement();
    demonstrateGameLoop();
    
    printf("\nAll advanced game development examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- Entity Component System (ECS) architecture\n");
    printf("- Rendering system with camera and meshes\n");
    printf("- Physics engine with collision detection\n");
    printf("- Input management for keyboard and mouse\n");
    printf("- Audio system with sound loading and playback\n");
    printf("- Particle system with emitters and effects\n");
    printf("- Animation system with keyframes and interpolation\n");
    printf("- Scene management with loading/unloading\n");
    printf("- Game loop with fixed timestep and FPS control\n");
    printf("- Vector mathematics for 2D operations\n");
    printf("- Component-based architecture for flexibility\n");
    
    return 0;
}
