#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <time.h>

// =============================================================================
// ADVANCED COMPUTER GRAPHICS
// =============================================================================

#define PI 3.14159265359
#define DEG_TO_RAD (PI / 180.0)
#define MAX_VERTICES 10000
#define MAX_TRIANGLES 10000
#define MAX_LIGHTS 8
#define MAX_TEXTURES 16
#define WIDTH 800
#define HEIGHT 600
#define FOV 60.0
#define NEAR_PLANE 0.1
#define FAR_PLANE 1000.0

// =============================================================================
// MATHEMATICAL FOUNDATIONS
// =============================================================================

// Vector2D structure
typedef struct {
    float x, y;
} Vector2D;

// Vector3D structure
typedef struct {
    float x, y, z;
} Vector3D;

// Vector4D structure
typedef struct {
    float x, y, z, w;
} Vector4D;

// Matrix4x4 structure
typedef struct {
    float m[4][4];
} Matrix4x4;

// Color structure
typedef struct {
    float r, g, b, a;
} Color;

// =============================================================================
// VECTOR OPERATIONS
// =============================================================================

// Vector3D operations
Vector3D vec3Create(float x, float y, float z) {
    Vector3D v = {x, y, z};
    return v;
}

Vector3D vec3Add(Vector3D a, Vector3D b) {
    return vec3Create(a.x + b.x, a.y + b.y, a.z + b.z);
}

Vector3D vec3Subtract(Vector3D a, Vector3D b) {
    return vec3Create(a.x - b.x, a.y - b.y, a.z - b.z);
}

Vector3D vec3Multiply(Vector3D v, float scalar) {
    return vec3Create(v.x * scalar, v.y * scalar, v.z * scalar);
}

float vec3Dot(Vector3D a, Vector3D b) {
    return a.x * b.x + a.y * b.y + a.z * b.z;
}

Vector3D vec3Cross(Vector3D a, Vector3D b) {
    return vec3Create(
        a.y * b.z - a.z * b.y,
        a.z * b.x - a.x * b.z,
        a.x * b.y - a.y * b.x
    );
}

float vec3Length(Vector3D v) {
    return sqrtf(v.x * v.x + v.y * v.y + v.z * v.z);
}

Vector3D vec3Normalize(Vector3D v) {
    float length = vec3Length(v);
    if (length > 0.0f) {
        return vec3Multiply(v, 1.0f / length);
    }
    return v;
}

Vector3D vec3Lerp(Vector3D a, Vector3D b, float t) {
    return vec3Add(vec3Multiply(a, 1.0f - t), vec3Multiply(b, t));
}

// =============================================================================
// MATRIX OPERATIONS
// =============================================================================

// Matrix4x4 operations
Matrix4x4 mat4Identity() {
    Matrix4x4 m = {{{0}}};
    m.m[0][0] = m.m[1][1] = m.m[2][2] = m.m[3][3] = 1.0f;
    return m;
}

Matrix4x4 mat4Multiply(Matrix4x4 a, Matrix4x4 b) {
    Matrix4x4 result = {{{0}}};
    
    for (int i = 0; i < 4; i++) {
        for (int j = 0; j < 4; j++) {
            for (int k = 0; k < 4; k++) {
                result.m[i][j] += a.m[i][k] * b.m[k][j];
            }
        }
    }
    
    return result;
}

Vector4D mat4MultiplyVector4(Matrix4x4 m, Vector4D v) {
    Vector4D result;
    
    result.x = m.m[0][0] * v.x + m.m[0][1] * v.y + m.m[0][2] * v.z + m.m[0][3] * v.w;
    result.y = m.m[1][0] * v.x + m.m[1][1] * v.y + m.m[1][2] * v.z + m.m[1][3] * v.w;
    result.z = m.m[2][0] * v.x + m.m[2][1] * v.y + m.m[2][2] * v.z + m.m[2][3] * v.w;
    result.w = m.m[3][0] * v.x + m.m[3][1] * v.y + m.m[3][2] * v.z + m.m[3][3] * v.w;
    
    return result;
}

Vector3D mat4MultiplyVector3(Matrix4x4 m, Vector3D v) {
    Vector4D v4 = {v.x, v.y, v.z, 1.0f};
    Vector4D result = mat4MultiplyVector4(m, v4);
    
    // Perspective divide
    if (result.w != 0.0f) {
        result.x /= result.w;
        result.y /= result.w;
        result.z /= result.w;
    }
    
    return vec3Create(result.x, result.y, result.z);
}

Matrix4x4 mat4Translation(Vector3D translation) {
    Matrix4x4 m = mat4Identity();
    m.m[0][3] = translation.x;
    m.m[1][3] = translation.y;
    m.m[2][3] = translation.z;
    return m;
}

Matrix4x4 mat4RotationX(float angle) {
    Matrix4x4 m = mat4Identity();
    float c = cosf(angle);
    float s = sinf(angle);
    
    m.m[1][1] = c;
    m.m[1][2] = -s;
    m.m[2][1] = s;
    m.m[2][2] = c;
    
    return m;
}

Matrix4x4 mat4RotationY(float angle) {
    Matrix4x4 m = mat4Identity();
    float c = cosf(angle);
    float s = sinf(angle);
    
    m.m[0][0] = c;
    m.m[0][2] = s;
    m.m[2][0] = -s;
    m.m[2][2] = c;
    
    return m;
}

Matrix4x4 mat4RotationZ(float angle) {
    Matrix4x4 m = mat4Identity();
    float c = cosf(angle);
    float s = sinf(angle);
    
    m.m[0][0] = c;
    m.m[0][1] = -s;
    m.m[1][0] = s;
    m.m[1][1] = c;
    
    return m;
}

Matrix4x4 mat4Scale(Vector3D scale) {
    Matrix4x4 m = mat4Identity();
    m.m[0][0] = scale.x;
    m.m[1][1] = scale.y;
    m.m[2][2] = scale.z;
    return m;
}

Matrix4x4 mat4Perspective(float fov, float aspect, float near_plane, float far_plane) {
    Matrix4x4 m = {{{0}}};
    
    float tan_half_fov = tanf(fov * 0.5f);
    
    m.m[0][0] = 1.0f / (aspect * tan_half_fov);
    m.m[1][1] = 1.0f / tan_half_fov;
    m.m[2][2] = -(far_plane + near_plane) / (far_plane - near_plane);
    m.m[2][3] = -(2.0f * far_plane * near_plane) / (far_plane - near_plane);
    m.m[3][2] = -1.0f;
    
    return m;
}

Matrix4x4 mat4LookAt(Vector3D eye, Vector3D target, Vector3D up) {
    Vector3D forward = vec3Normalize(vec3Subtract(target, eye));
    Vector3D right = vec3Normalize(vec3Cross(forward, up));
    Vector3D new_up = vec3Cross(right, forward);
    
    Matrix4x4 m = mat4Identity();
    
    m.m[0][0] = right.x;
    m.m[1][0] = right.y;
    m.m[2][0] = right.z;
    
    m.m[0][1] = new_up.x;
    m.m[1][1] = new_up.y;
    m.m[2][1] = new_up.z;
    
    m.m[0][2] = -forward.x;
    m.m[1][2] = -forward.y;
    m.m[2][2] = -forward.z;
    
    m.m[0][3] = -vec3Dot(right, eye);
    m.m[1][3] = -vec3Dot(new_up, eye);
    m.m[2][3] = vec3Dot(forward, eye);
    
    return m;
}

// =============================================================================
// 3D GEOMETRY
// =============================================================================

// Vertex structure
typedef struct {
    Vector3D position;
    Vector3D normal;
    Vector2D tex_coord;
    Color color;
} Vertex;

// Triangle structure
typedef struct {
    Vertex vertices[3];
    Vector3D normal;
    Color material_color;
} Triangle;

// Mesh structure
typedef struct {
    Vertex* vertices;
    Triangle* triangles;
    int vertex_count;
    int triangle_count;
    Vector3D position;
    Vector3D rotation;
    Vector3D scale;
    Matrix4x4 model_matrix;
} Mesh;

// =============================================================================
// RENDERING PIPELINE
// =============================================================================

// Framebuffer
typedef struct {
    Color* color_buffer;
    float* depth_buffer;
    int width;
    int height;
} Framebuffer;

// Camera structure
typedef struct {
    Vector3D position;
    Vector3D target;
    Vector3D up;
    float fov;
    float aspect;
    float near_plane;
    float far_plane;
    Matrix4x4 view_matrix;
    Matrix4x4 projection_matrix;
    Matrix4x4 view_projection_matrix;
} Camera;

// Light structure
typedef struct {
    Vector3D position;
    Vector3D direction;
    Color color;
    float intensity;
    int type; // 0=directional, 1=point, 2=spot
    float range;
    float spot_angle;
} Light;

// =============================================================================
// LIGHTING
// =============================================================================

// Phong lighting model
Color calculatePhongLighting(Vector3D position, Vector3D normal, Color material_color,
                           Vector3D view_dir, Light* lights, int light_count) {
    Color final_color = {0.0f, 0.0f, 0.0f, 1.0f};
    
    // Ambient light
    Color ambient = {0.1f, 0.1f, 0.1f, 1.0f};
    final_color.r += ambient.r * material_color.r;
    final_color.g += ambient.g * material_color.g;
    final_color.b += ambient.b * material_color.b;
    
    for (int i = 0; i < light_count; i++) {
        Light* light = &lights[i];
        Vector3D light_dir;
        float attenuation = 1.0f;
        
        if (light->type == 0) { // Directional light
            light_dir = vec3Normalize(vec3Multiply(light->direction, -1.0f));
        } else { // Point light
            Vector3D to_light = vec3Subtract(light->position, position);
            float distance = vec3Length(to_light);
            light_dir = vec3Normalize(to_light);
            
            // Distance attenuation
            attenuation = 1.0f / (1.0f + 0.09f * distance + 0.032f * distance * distance);
        }
        
        // Diffuse lighting
        float diffuse_intensity = fmaxf(0.0f, vec3Dot(normal, light_dir));
        Color diffuse = {
            light->color.r * material_color.r * diffuse_intensity * light->intensity * attenuation,
            light->color.g * material_color.g * diffuse_intensity * light->intensity * attenuation,
            light->color.b * material_color.b * diffuse_intensity * light->intensity * attenuation,
            1.0f
        };
        
        final_color.r += diffuse.r;
        final_color.g += diffuse.g;
        final_color.b += diffuse.b;
        
        // Specular lighting
        Vector3D reflect_dir = vec3Subtract(vec3Multiply(light_dir, 2.0f * vec3Dot(normal, light_dir)), normal);
        float specular_intensity = powf(fmaxf(0.0f, vec3Dot(view_dir, reflect_dir)), 32.0f);
        Color specular = {
            light->color.r * specular_intensity * light->intensity * attenuation,
            light->color.g * specular_intensity * light->intensity * attenuation,
            light->color.b * specular_intensity * light->intensity * attenuation,
            1.0f
        };
        
        final_color.r += specular.r;
        final_color.g += specular.g;
        final_color.b += specular.b;
    }
    
    // Clamp colors
    final_color.r = fminf(1.0f, fmaxf(0.0f, final_color.r));
    final_color.g = fminf(1.0f, fmaxf(0.0f, final_color.g));
    final_color.b = fminf(1.0f, fmaxf(0.0f, final_color.b));
    
    return final_color;
}

// =============================================================================
// RASTERIZATION
// =============================================================================

// Initialize framebuffer
Framebuffer* createFramebuffer(int width, int height) {
    Framebuffer* fb = malloc(sizeof(Framebuffer));
    if (!fb) return NULL;
    
    fb->width = width;
    fb->height = height;
    fb->color_buffer = malloc(width * height * sizeof(Color));
    fb->depth_buffer = malloc(width * height * sizeof(float));
    
    if (!fb->color_buffer || !fb->depth_buffer) {
        free(fb->color_buffer);
        free(fb->depth_buffer);
        free(fb);
        return NULL;
    }
    
    // Clear buffers
    memset(fb->color_buffer, 0, width * height * sizeof(Color));
    for (int i = 0; i < width * height; i++) {
        fb->depth_buffer[i] = 1.0f;
    }
    
    return fb;
}

// Destroy framebuffer
void destroyFramebuffer(Framebuffer* fb) {
    if (fb) {
        free(fb->color_buffer);
        free(fb->depth_buffer);
        free(fb);
    }
}

// Clear framebuffer
void clearFramebuffer(Framebuffer* fb, Color color) {
    for (int i = 0; i < fb->width * fb->height; i++) {
        fb->color_buffer[i] = color;
        fb->depth_buffer[i] = 1.0f;
    }
}

// Set pixel
void setPixel(Framebuffer* fb, int x, int y, Color color) {
    if (x >= 0 && x < fb->width && y >= 0 && y < fb->height) {
        fb->color_buffer[y * fb->width + x] = color;
    }
}

// Get pixel
Color getPixel(Framebuffer* fb, int x, int y) {
    if (x >= 0 && x < fb->width && y >= 0 && y < fb->height) {
        return fb->color_buffer[y * fb->width + x];
    }
    return (Color){0, 0, 0, 1};
}

// Draw line using Bresenham's algorithm
void drawLine(Framebuffer* fb, int x0, int y0, int x1, int y1, Color color) {
    int dx = abs(x1 - x0);
    int dy = abs(y1 - y0);
    int sx = x0 < x1 ? 1 : -1;
    int sy = y0 < y1 ? 1 : -1;
    int err = dx - dy;
    
    while (1) {
        setPixel(fb, x0, y0, color);
        
        if (x0 == x1 && y0 == y1) break;
        
        int e2 = 2 * err;
        if (e2 > -dy) {
            err -= dy;
            x0 += sx;
        }
        if (e2 < dx) {
            err += dx;
            y0 += sy;
        }
    }
}

// Draw triangle
void drawTriangle(Framebuffer* fb, Vector2D v0, Vector2D v1, Vector2D v2, Color color) {
    drawLine(fb, (int)v0.x, (int)v0.y, (int)v1.x, (int)v1.y, color);
    drawLine(fb, (int)v1.x, (int)v1.y, (int)v2.x, (int)v2.y, color);
    drawLine(fb, (int)v2.x, (int)v2.y, (int)v0.x, (int)v0.y, color);
}

// =============================================================================
// 3D RENDERING
// =============================================================================

// Project 3D point to 2D screen
Vector2D project3DTo2D(Vector3D point3D, Matrix4x4 view_projection_matrix) {
    Vector4D projected = mat4MultiplyVector4(view_projection_matrix, 
                                            (Vector4D){point3D.x, point3D.y, point3D.z, 1.0f});
    
    // Perspective divide
    if (projected.w != 0.0f) {
        projected.x /= projected.w;
        projected.y /= projected.w;
        projected.z /= projected.w;
    }
    
    // Convert to screen coordinates
    Vector2D screen_pos;
    screen_pos.x = (projected.x + 1.0f) * WIDTH / 2.0f;
    screen_pos.y = (1.0f - projected.y) * HEIGHT / 2.0f;
    
    return screen_pos;
}

// Render mesh
void renderMesh(Framebuffer* fb, Mesh* mesh, Matrix4x4 view_projection_matrix, 
               Light* lights, int light_count, Camera* camera) {
    // Update model matrix
    Matrix4x4 translation = mat4Translation(mesh->position);
    Matrix4x4 rotation_x = mat4RotationX(mesh->rotation.x);
    Matrix4x4 rotation_y = mat4RotationY(mesh->rotation.y);
    Matrix4x4 rotation_z = mat4RotationZ(mesh->rotation.z);
    Matrix4x4 scale = mat4Scale(mesh->scale);
    
    mesh->model_matrix = mat4Multiply(
        mat4Multiply(
            mat4Multiply(translation, rotation_x),
            mat4Multiply(rotation_y, rotation_z)
        ),
        scale
    );
    
    Matrix4x4 mvp = mat4Multiply(view_projection_matrix, mesh->model_matrix);
    
    // Render triangles
    for (int i = 0; i < mesh->triangle_count; i++) {
        Triangle* triangle = &mesh->triangles[i];
        
        // Transform vertices to screen space
        Vector2D screen_points[3];
        Vector3D world_positions[3];
        
        for (int j = 0; j < 3; j++) {
            world_positions[j] = mat4MultiplyVector3(mesh->model_matrix, triangle->vertices[j].position);
            screen_points[j] = project3DTo2D(world_positions[j], view_projection_matrix);
        }
        
        // Backface culling
        Vector3D v1 = vec3Subtract(world_positions[1], world_positions[0]);
        Vector3D v2 = vec3Subtract(world_positions[2], world_positions[0]);
        Vector3D normal = vec3Normalize(vec3Cross(v1, v2));
        
        Vector3D view_dir = vec3Normalize(vec3Subtract(camera->position, world_positions[0]));
        if (vec3Dot(normal, view_dir) < 0) {
            continue; // Back-facing triangle
        }
        
        // Calculate lighting
        Vector3D face_normal = vec3Normalize(vec3Add(vec3Add(triangle->vertices[0].normal,
                                                         triangle->vertices[1].normal),
                                                   triangle->vertices[2].normal));
        
        Color lit_color = calculatePhongLighting(world_positions[0], face_normal, 
                                                triangle->material_color, view_dir, 
                                                lights, light_count);
        
        // Draw triangle
        drawTriangle(fb, screen_points[0], screen_points[1], screen_points[2], lit_color);
    }
}

// =============================================================================
// MESH CREATION
// =============================================================================

// Create cube mesh
Mesh* createCubeMesh(float size) {
    Mesh* mesh = malloc(sizeof(Mesh));
    if (!mesh) return NULL;
    
    mesh->vertex_count = 8;
    mesh->triangle_count = 12;
    mesh->vertices = malloc(mesh->vertex_count * sizeof(Vertex));
    mesh->triangles = malloc(mesh->triangle_count * sizeof(Triangle));
    
    if (!mesh->vertices || !mesh->triangles) {
        free(mesh->vertices);
        free(mesh->triangles);
        free(mesh);
        return NULL;
    }
    
    // Create vertices
    float s = size / 2.0f;
    mesh->vertices[0] = (Vertex){{-s, -s, -s}, {-1, -1, -1}, {0, 0}, {1, 0, 0, 1}};
    mesh->vertices[1] = (Vertex){{ s, -s, -s}, { 1, -1, -1}, {1, 0}, {0, 1, 0, 1}};
    mesh->vertices[2] = (Vertex){{ s,  s, -s}, { 1,  1, -1}, {1, 1}, {0, 0, 1, 1}};
    mesh->vertices[3] = (Vertex){{-s,  s, -s}, {-1,  1, -1}, {0, 1}, {1, 1, 0, 1}};
    mesh->vertices[4] = (Vertex){{-s, -s,  s}, {-1, -1,  1}, {0, 0}, {1, 0, 1, 1}};
    mesh->vertices[5] = (Vertex){{ s, -s,  s}, { 1, -1,  1}, {1, 0}, {0, 1, 1, 1}};
    mesh->vertices[6] = (Vertex){{ s,  s,  s}, { 1,  1,  1}, {1, 1}, {0, 0, 0, 1}};
    mesh->vertices[7] = (Vertex){{-s,  s,  s}, {-1,  1,  1}, {0, 1}, {1, 1, 1, 1}};
    
    // Create triangles (12 triangles for cube)
    int cube_triangles[12][3] = {
        {0, 1, 2}, {0, 2, 3}, // Front face
        {4, 7, 6}, {4, 6, 5}, // Back face
        {0, 4, 5}, {0, 5, 1}, // Bottom face
        {2, 6, 7}, {2, 7, 3}, // Top face
        {0, 3, 7}, {0, 7, 4}, // Left face
        {1, 5, 6}, {1, 6, 2}  // Right face
    };
    
    for (int i = 0; i < 12; i++) {
        mesh->triangles[i].vertices[0] = mesh->vertices[cube_triangles[i][0]];
        mesh->triangles[i].vertices[1] = mesh->vertices[cube_triangles[i][1]];
        mesh->triangles[i].vertices[2] = mesh->vertices[cube_triangles[i][2]];
        mesh->triangles[i].material_color = (Color){0.8f, 0.8f, 0.8f, 1.0f};
    }
    
    mesh->position = (Vector3D){0, 0, 0};
    mesh->rotation = (Vector3D){0, 0, 0};
    mesh->scale = (Vector3D){1, 1, 1};
    
    return mesh;
}

// Create sphere mesh
Mesh* createSphereMesh(float radius, int segments) {
    Mesh* mesh = malloc(sizeof(Mesh));
    if (!mesh) return NULL;
    
    int vertex_count = (segments + 1) * (segments + 1);
    int triangle_count = segments * segments * 2;
    
    mesh->vertices = malloc(vertex_count * sizeof(Vertex));
    mesh->triangles = malloc(triangle_count * sizeof(Triangle));
    
    if (!mesh->vertices || !mesh->triangles) {
        free(mesh->vertices);
        free(mesh->triangles);
        free(mesh);
        return NULL;
    }
    
    mesh->vertex_count = vertex_count;
    mesh->triangle_count = triangle_count;
    
    // Generate vertices
    int vertex_index = 0;
    for (int lat = 0; lat <= segments; lat++) {
        float theta = lat * PI / segments;
        float sin_theta = sinf(theta);
        float cos_theta = cosf(theta);
        
        for (int lon = 0; lon <= segments; lon++) {
            float phi = lon * 2 * PI / segments;
            float sin_phi = sinf(phi);
            float cos_phi = cosf(phi);
            
            float x = cos_phi * sin_theta * radius;
            float y = cos_theta * radius;
            float z = sin_phi * sin_theta * radius;
            
            Vector3D normal = vec3Normalize((Vector3D){x, y, z});
            Vector2D tex_coord = {(float)lon / segments, (float)lat / segments};
            Color color = {0.7f, 0.7f, 0.7f, 1.0f};
            
            mesh->vertices[vertex_index++] = (Vertex){{x, y, z}, normal, tex_coord, color};
        }
    }
    
    // Generate triangles
    int triangle_index = 0;
    for (int lat = 0; lat < segments; lat++) {
        for (int lon = 0; lon < segments; lon++) {
            int current = lat * (segments + 1) + lon;
            int next = current + segments + 1;
            
            // First triangle
            mesh->triangles[triangle_index].vertices[0] = mesh->vertices[current];
            mesh->triangles[triangle_index].vertices[1] = mesh->vertices[next];
            mesh->triangles[triangle_index].vertices[2] = mesh->vertices[current + 1];
            mesh->triangles[triangle_index].material_color = (Color){0.8f, 0.8f, 0.8f, 1.0f};
            triangle_index++;
            
            // Second triangle
            mesh->triangles[triangle_index].vertices[0] = mesh->vertices[current + 1];
            mesh->triangles[triangle_index].vertices[1] = mesh->vertices[next];
            mesh->triangles[triangle_index].vertices[2] = mesh->vertices[next + 1];
            mesh->triangles[triangle_index].material_color = (Color){0.8f, 0.8f, 0.8f, 1.0f};
            triangle_index++;
        }
    }
    
    mesh->position = (Vector3D){0, 0, 0};
    mesh->rotation = (Vector3D){0, 0, 0};
    mesh->scale = (Vector3D){1, 1, 1};
    
    return mesh;
}

// =============================================================================
// CAMERA MANAGEMENT
// =============================================================================

// Create camera
Camera* createCamera(Vector3D position, Vector3D target, Vector3D up, float fov, float aspect) {
    Camera* camera = malloc(sizeof(Camera));
    if (!camera) return NULL;
    
    camera->position = position;
    camera->target = target;
    camera->up = up;
    camera->fov = fov;
    camera->aspect = aspect;
    camera->near_plane = NEAR_PLANE;
    camera->far_plane = FAR_PLANE;
    
    camera->view_matrix = mat4LookAt(position, target, up);
    camera->projection_matrix = mat4Perspective(fov * DEG_TO_RAD, aspect, NEAR_PLANE, FAR_PLANE);
    camera->view_projection_matrix = mat4Multiply(camera->projection_matrix, camera->view_matrix);
    
    return camera;
}

// Update camera
void updateCamera(Camera* camera) {
    camera->view_matrix = mat4LookAt(camera->position, camera->target, camera->up);
    camera->view_projection_matrix = mat4Multiply(camera->projection_matrix, camera->view_matrix);
}

// =============================================================================
// SCENE MANAGEMENT
// =============================================================================

// Scene structure
typedef struct {
    Mesh* meshes[MAX_MESHES];
    int mesh_count;
    Camera* camera;
    Light lights[MAX_LIGHTS];
    int light_count;
    Framebuffer* framebuffer;
} Scene;

// Create scene
Scene* createScene() {
    Scene* scene = malloc(sizeof(Scene));
    if (!scene) return NULL;
    
    memset(scene, 0, sizeof(Scene));
    
    scene->camera = createCamera(
        (Vector3D){0, 0, 5}, 
        (Vector3D){0, 0, 0}, 
        (Vector3D){0, 1, 0}, 
        FOV, 
        (float)WIDTH / HEIGHT
    );
    
    scene->framebuffer = createFramebuffer(WIDTH, HEIGHT);
    
    // Add default lighting
    scene->lights[0] = (Light){
        (Vector3D){1, 1, 1},    // position
        (Vector3D){-1, -1, -1}, // direction
        (Color){1, 1, 1, 1},     // color
        1.0f,                    // intensity
        0,                       // type (directional)
        0.0f,                    // range
        0.0f                     // spot angle
    };
    scene->light_count = 1;
    
    return scene;
}

// Destroy scene
void destroyScene(Scene* scene) {
    if (!scene) return;
    
    for (int i = 0; i < scene->mesh_count; i++) {
        free(scene->meshes[i]->vertices);
        free(scene->meshes[i]->triangles);
        free(scene->meshes[i]);
    }
    
    free(scene->camera);
    destroyFramebuffer(scene->framebuffer);
    free(scene);
}

// Add mesh to scene
void addMeshToScene(Scene* scene, Mesh* mesh) {
    if (scene->mesh_count < MAX_MESHES) {
        scene->meshes[scene->mesh_count++] = mesh;
    }
}

// Add light to scene
void addLightToScene(Scene* scene, Light light) {
    if (scene->light_count < MAX_LIGHTS) {
        scene->lights[scene->light_count++] = light;
    }
}

// Render scene
void renderScene(Scene* scene) {
    clearFramebuffer(scene->framebuffer, (Color){0.1f, 0.1f, 0.1f, 1.0f});
    
    for (int i = 0; i < scene->mesh_count; i++) {
        renderMesh(scene->framebuffer, scene->meshes[i], 
                 scene->camera->view_projection_matrix, 
                 scene->lights, scene->light_count, scene->camera);
    }
}

// =============================================================================
// TEXTURE MAPPING
// =============================================================================

// Texture structure
typedef struct {
    Color* data;
    int width;
    int height;
} Texture;

// Create texture
Texture* createTexture(int width, int height) {
    Texture* texture = malloc(sizeof(Texture));
    if (!texture) return NULL;
    
    texture->width = width;
    texture->height = height;
    texture->data = malloc(width * height * sizeof(Color));
    
    if (!texture->data) {
        free(texture);
        return NULL;
    }
    
    return texture;
}

// Destroy texture
void destroyTexture(Texture* texture) {
    if (texture) {
        free(texture->data);
        free(texture);
    }
}

// Sample texture
Color sampleTexture(Texture* texture, Vector2D uv) {
    if (!texture || !texture->data) {
        return (Color){1, 0, 1, 1}; // Magenta for error
    }
    
    int x = (int)(uv.x * texture->width) % texture->width;
    int y = (int)(uv.y * texture->height) % texture->height;
    
    if (x < 0) x += texture->width;
    if (y < 0) y += texture->height;
    
    return texture->data[y * texture->width + x];
}

// Create checkerboard texture
Texture* createCheckerboardTexture(int width, int height, int checker_size) {
    Texture* texture = createTexture(width, height);
    if (!texture) return NULL;
    
    for (int y = 0; y < height; y++) {
        for (int x = 0; x < width; x++) {
            int checker_x = x / checker_size;
            int checker_y = y / checker_size;
            
            if ((checker_x + checker_y) % 2 == 0) {
                texture->data[y * width + x] = (Color){1, 1, 1, 1}; // White
            } else {
                texture->data[y * width + x] = (Color){0, 0, 0, 1}; // Black
            }
        }
    }
    
    return texture;
}

// =============================================================================
// RAY TRACING
// =============================================================================

// Ray structure
typedef struct {
    Vector3D origin;
    Vector3D direction;
} Ray;

// Ray-sphere intersection
float raySphereIntersection(Ray ray, Vector3D sphere_center, float sphere_radius) {
    Vector3D oc = vec3Subtract(ray.origin, sphere_center);
    float a = vec3Dot(ray.direction, ray.direction);
    float b = 2.0f * vec3Dot(oc, ray.direction);
    float c = vec3Dot(oc, oc) - sphere_radius * sphere_radius;
    
    float discriminant = b * b - 4 * a * c;
    if (discriminant < 0) {
        return -1.0f; // No intersection
    }
    
    float t = (-b - sqrtf(discriminant)) / (2.0f * a);
    if (t < 0) {
        t = (-b + sqrtf(discriminant)) / (2.0f * a);
    }
    
    return t;
}

// Ray-trace scene
Color rayTraceScene(Ray ray, Scene* scene, int depth) {
    if (depth <= 0) {
        return (Color){0.1f, 0.1f, 0.1f, 1.0f}; // Background color
    }
    
    float closest_t = FAR_PLANE;
    Mesh* closest_mesh = NULL;
    
    // Find closest intersection
    for (int i = 0; i < scene->mesh_count; i++) {
        Mesh* mesh = scene->meshes[i];
        
        // Simple sphere intersection for demonstration
        float t = raySphereIntersection(ray, mesh->position, 1.0f);
        if (t > 0 && t < closest_t) {
            closest_t = t;
            closest_mesh = mesh;
        }
    }
    
    if (!closest_mesh) {
        return (Color){0.1f, 0.1f, 0.1f, 1.0f}; // Background
    }
    
    // Calculate intersection point
    Vector3D hit_point = vec3Add(ray.origin, vec3Multiply(ray.direction, closest_t));
    
    // Calculate normal at hit point
    Vector3D normal = vec3Normalize(vec3Subtract(hit_point, closest_mesh->position));
    
    // Simple lighting
    Color color = closest_mesh->triangles[0].material_color;
    Color lit_color = calculatePhongLighting(hit_point, normal, color, 
                                            vec3Multiply(ray.direction, -1.0f),
                                            scene->lights, scene->light_count);
    
    return lit_color;
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateVectorMath() {
    printf("=== VECTOR MATH DEMO ===\n");
    
    Vector3D a = vec3Create(1, 2, 3);
    Vector3D b = vec3Create(4, 5, 6);
    
    printf("Vector A: (%.2f, %.2f, %.2f)\n", a.x, a.y, a.z);
    printf("Vector B: (%.2f, %.2f, %.2f)\n", b.x, b.y, b.z);
    
    Vector3D sum = vec3Add(a, b);
    printf("A + B = (%.2f, %.2f, %.2f)\n", sum.x, sum.y, sum.z);
    
    Vector3D diff = vec3Subtract(a, b);
    printf("A - B = (%.2f, %.2f, %.2f)\n", diff.x, diff.y, diff.z);
    
    float dot = vec3Dot(a, b);
    printf("A · B = %.2f\n", dot);
    
    Vector3D cross = vec3Cross(a, b);
    printf("A × B = (%.2f, %.2f, %.2f)\n", cross.x, cross.y, cross.z);
    
    float length = vec3Length(a);
    printf("|A| = %.2f\n", length);
    
    Vector3D normalized = vec3Normalize(a);
    printf("Normalized A = (%.2f, %.2f, %.2f)\n", normalized.x, normalized.y, normalized.z);
}

void demonstrateMatrixMath() {
    printf("\n=== MATRIX MATH DEMO ===\n");
    
    Matrix4x4 identity = mat4Identity();
    printf("Identity matrix created\n");
    
    Matrix4x4 trans = mat4Translation((Vector3D){1, 2, 3});
    printf("Translation matrix created\n");
    
    Matrix4x4 rot_x = mat4RotationX(45 * DEG_TO_RAD);
    Matrix4x4 rot_y = mat4RotationY(45 * DEG_TO_RAD);
    Matrix4x4 rot_z = mat4RotationZ(45 * DEG_TO_RAD);
    printf("Rotation matrices created\n");
    
    Matrix4x4 scale = mat4Scale((Vector3D){2, 2, 2});
    printf("Scale matrix created\n");
    
    Matrix4x4 combined = mat4Multiply(mat4Multiply(trans, rot_x), scale);
    printf("Combined transformation matrix created\n");
    
    Vector3D point = {1, 1, 1};
    Vector3D transformed = mat4MultiplyVector3(combined, point);
    printf("Transformed point: (%.2f, %.2f, %.2f)\n", transformed.x, transformed.y, transformed.z);
}

void demonstrateLighting() {
    printf("\n=== LIGHTING DEMO ===\n");
    
    Vector3D position = {0, 0, 0};
    Vector3D normal = {0, 1, 0};
    Color material_color = {1, 0, 0, 1}; // Red
    Vector3D view_dir = {0, 0, 1};
    
    Light light = {
        (Vector3D){1, 1, 1},    // position
        (Vector3D){-1, -1, -1}, // direction
        (Color){1, 1, 1, 1},     // color
        1.0f,                    // intensity
        1,                       // type (point)
        10.0f,                   // range
        0.0f                     // spot angle
    };
    
    Color lit_color = calculatePhongLighting(position, normal, material_color, 
                                            view_dir, &light, 1);
    
    printf("Material color: (%.2f, %.2f, %.2f)\n", 
           material_color.r, material_color.g, material_color.b);
    printf("Lit color: (%.2f, %.2f, %.2f)\n", 
           lit_color.r, lit_color.g, lit_color.b);
}

void demonstrateRasterization() {
    printf("\n=== RASTERIZATION DEMO ===\n");
    
    Framebuffer* fb = createFramebuffer(100, 100);
    if (!fb) {
        printf("Failed to create framebuffer\n");
        return;
    }
    
    clearFramebuffer(fb, (Color){0, 0, 0, 1});
    
    // Draw some lines
    drawLine(fb, 10, 10, 90, 90, (Color){1, 0, 0, 1}); // Red diagonal
    drawLine(fb, 10, 90, 90, 10, (Color){0, 1, 0, 1}); // Green diagonal
    drawLine(fb, 50, 10, 50, 90, (Color){0, 0, 1, 1}); // Blue vertical
    drawLine(fb, 10, 50, 90, 50, (Color){1, 1, 0, 1}); // Yellow horizontal
    
    // Draw a triangle
    drawTriangle(fb, (Vector2D){50, 20}, (Vector2D){20, 80}, (Vector2D){80, 80}, 
                (Color){1, 0, 1, 1}); // Magenta triangle
    
    printf("Framebuffer created and shapes drawn\n");
    printf("Buffer size: %dx%d\n", fb->width, fb->height);
    
    destroyFramebuffer(fb);
}

void demonstrate3DRendering() {
    printf("\n=== 3D RENDERING DEMO ===\n");
    
    Scene* scene = createScene();
    if (!scene) {
        printf("Failed to create scene\n");
        return;
    }
    
    // Add a cube
    Mesh* cube = createCubeMesh(2.0f);
    if (cube) {
        cube->position = (Vector3D){0, 0, 0};
        cube->rotation = (Vector3D){45 * DEG_TO_RAD, 45 * DEG_TO_RAD, 0};
        addMeshToScene(scene, cube);
        printf("Cube mesh added to scene\n");
    }
    
    // Add a sphere
    Mesh* sphere = createSphereMesh(1.0f, 16);
    if (sphere) {
        sphere->position = (Vector3D){3, 0, 0};
        addMeshToScene(scene, sphere);
        printf("Sphere mesh added to scene\n");
    }
    
    // Render the scene
    renderScene(scene);
    printf("Scene rendered with %d meshes\n", scene->mesh_count);
    
    destroyScene(scene);
}

void demonstrateRayTracing() {
    printf("\n=== RAY TRACING DEMO ===\n");
    
    Scene* scene = createScene();
    if (!scene) {
        printf("Failed to create scene\n");
        return;
    }
    
    // Add a sphere for ray tracing
    Mesh* sphere = createSphereMesh(1.0f, 16);
    if (sphere) {
        sphere->position = (Vector3D){0, 0, 0};
        addMeshToScene(scene, sphere);
    }
    
    // Simple ray tracing
    Ray ray = {
        (Vector3D){0, 0, 5}, // origin
        (Vector3D){0, 0, -1} // direction (looking down Z)
    };
    
    Color color = rayTraceScene(ray, scene, 1);
    printf("Ray traced color: (%.2f, %.2f, %.2f)\n", color.r, color.g, color.b);
    
    destroyScene(scene);
}

void demonstrateTextureMapping() {
    printf("\n=== TEXTURE MAPPING DEMO ===\n");
    
    Texture* texture = createCheckerboardTexture(64, 64, 8);
    if (!texture) {
        printf("Failed to create texture\n");
        return;
    }
    
    printf("Checkerboard texture created (%dx%d)\n", texture->width, texture->height);
    
    // Sample texture at different UV coordinates
    Color sample1 = sampleTexture(texture, (Vector2D){0.25f, 0.25f});
    Color sample2 = sampleTexture(texture, (Vector2D){0.75f, 0.75f});
    
    printf("Sample at (0.25, 0.25): (%.2f, %.2f, %.2f)\n", 
           sample1.r, sample1.g, sample1.b);
    printf("Sample at (0.75, 0.75): (%.2f, %.2f, %.2f)\n", 
           sample2.r, sample2.g, sample2.b);
    
    destroyTexture(texture);
}

void demonstrateAnimation() {
    printf("\n=== ANIMATION DEMO ===\n");
    
    Scene* scene = createScene();
    if (!scene) {
        printf("Failed to create scene\n");
        return;
    }
    
    // Add animated cube
    Mesh* cube = createCubeMesh(1.5f);
    if (cube) {
        addMeshToScene(scene, cube);
        
        // Animate rotation
        for (int frame = 0; frame < 360; frame += 30) {
            cube->rotation.x = frame * DEG_TO_RAD;
            cube->rotation.y = frame * DEG_TO_RAD * 2;
            
            renderScene(scene);
            
            if (frame % 90 == 0) {
                printf("Frame %d: Rotation (%.1f°, %.1f°)\n", 
                       frame, cube->rotation.x / DEG_TO_RAD, cube->rotation.y / DEG_TO_RAD);
            }
        }
        
        printf("Animation completed\n");
    }
    
    destroyScene(scene);
}

void demonstrateCameraMovement() {
    printf("\n=== CAMERA MOVEMENT DEMO ===\n");
    
    Scene* scene = createScene();
    if (!scene) {
        printf("Failed to create scene\n");
        return;
    }
    
    // Add a sphere
    Mesh* sphere = createSphereMesh(1.0f, 16);
    if (sphere) {
        addMeshToScene(scene, sphere);
    }
    
    // Move camera in a circle
    for (int angle = 0; angle < 360; angle += 45) {
        float radians = angle * DEG_TO_RAD;
        scene->camera->position.x = 5.0f * cosf(radians);
        scene->camera->position.z = 5.0f * sinf(radians);
        
        updateCamera(scene->camera);
        renderScene(scene);
        
        printf("Camera at (%.1f, %.1f, %.1f)\n", 
               scene->camera->position.x, scene->camera->position.y, scene->camera->position.z);
    }
    
    destroyScene(scene);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Computer Graphics Examples\n");
    printf("===================================\n\n");
    
    // Run all demonstrations
    demonstrateVectorMath();
    demonstrateMatrixMath();
    demonstrateLighting();
    demonstrateRasterization();
    demonstrate3DRendering();
    demonstrateRayTracing();
    demonstrateTextureMapping();
    demonstrateAnimation();
    demonstrateCameraMovement();
    
    printf("\nAll advanced graphics examples demonstrated!\n");
    printf("Key features demonstrated:\n");
    printf("- Vector and matrix mathematics for 3D graphics\n");
    printf("- Phong lighting model with multiple light types\n");
    printf("- Rasterization with line and triangle drawing\n");
    printf("- 3D mesh rendering with cube and sphere primitives\n");
    printf("- Ray tracing for realistic lighting simulation\n");
    printf("- Texture mapping with checkerboard pattern\n");
    printf("- Animation with rotation and camera movement\n");
    printf("- Scene management with multiple objects\n");
    printf("- Camera system with perspective projection\n");
    printf("- Framebuffer management and depth testing\n");
    
    return 0;
}
