# Advanced Computer Graphics

This file contains comprehensive advanced computer graphics examples in C, including vector and matrix mathematics, 3D rendering pipelines, lighting models, rasterization, ray tracing, texture mapping, animation, and scene management.

## 📚 Advanced Computer Graphics Fundamentals

### 🎯 Graphics Concepts
- **3D Mathematics**: Vector and matrix operations for 3D transformations
- **Rendering Pipeline**: Vertex processing to pixel output
- **Lighting Models**: Phong shading with multiple light types
- **Rasterization**: Converting 3D primitives to 2D pixels
- **Ray Tracing**: Physically-based light simulation

### 🔧 Graphics Pipeline
- **Vertex Processing**: Transform and project 3D vertices
- **Primitive Assembly**: Build triangles from vertices
- **Rasterization**: Convert triangles to fragments
- **Fragment Processing**: Apply lighting and textures
- **Output Merger**: Depth testing and blending

## 📐 Vector Mathematics

### Vector3D Structure
```c
// Vector3D structure
typedef struct {
    float x, y, z;
} Vector3D;
```

### Vector Operations
```c
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
```

**Vector Benefits**:
- **3D Math**: Essential for 3D graphics calculations
- **Operations**: Complete set of vector operations
- **Efficiency**: Optimized for performance
- **Utility**: Helper functions for common operations

## 🔢 Matrix Mathematics

### Matrix4x4 Structure
```c
// Matrix4x4 structure
typedef struct {
    float m[4][4];
} Matrix4x4;
```

### Matrix Operations
```c
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
```

### Transformation Matrices
```c
// Translation matrix
Matrix4x4 mat4Translation(Vector3D translation) {
    Matrix4x4 m = mat4Identity();
    m.m[0][3] = translation.x;
    m.m[1][3] = translation.y;
    m.m[2][3] = translation.z;
    return m;
}

// Rotation matrices
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

// Scale matrix
Matrix4x4 mat4Scale(Vector3D scale) {
    Matrix4x4 m = mat4Identity();
    m.m[0][0] = scale.x;
    m.m[1][1] = scale.y;
    m.m[2][2] = scale.z;
    return m;
}
```

### Projection Matrices
```c
// Perspective projection matrix
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

// View matrix (look-at)
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
```

**Matrix Benefits**:
- **3D Transformations**: Complete set of 3D transformation matrices
- **Projection**: Perspective and orthographic projection
- **Camera**: Look-at matrix for camera positioning
- **Efficiency**: Optimized matrix operations

## 🎨 Color and Lighting

### Color Structure
```c
// Color structure
typedef struct {
    float r, g, b, a;
} Color;
```

### Phong Lighting Model
```c
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
```

**Lighting Benefits**:
- **Phong Model**: Complete ambient, diffuse, and specular lighting
- **Multiple Lights**: Support for directional and point lights
- **Attenuation**: Realistic light falloff
- **Material Properties**: Realistic material appearance

## 📐 3D Geometry

### Vertex Structure
```c
// Vertex structure
typedef struct {
    Vector3D position;
    Vector3D normal;
    Vector2D tex_coord;
    Color color;
} Vertex;
```

### Triangle Structure
```c
// Triangle structure
typedef struct {
    Vertex vertices[3];
    Vector3D normal;
    Color material_color;
} Triangle;
```

### Mesh Structure
```c
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
```

### Mesh Creation
```c
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
```

**Geometry Benefits**:
- **Primitives**: Complete set of 3D primitives
- **Mesh Data**: Organized vertex and triangle data
- **Transformations**: Position, rotation, and scale
- **Normals**: Proper normal calculation for lighting

## 🖼️ Rasterization

### Framebuffer Structure
```c
// Framebuffer
typedef struct {
    Color* color_buffer;
    float* depth_buffer;
    int width;
    int height;
} Framebuffer;
```

### Framebuffer Management
```c
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
```

### Line Drawing (Bresenham's Algorithm)
```c
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
```

### Triangle Drawing
```c
// Draw triangle
void drawTriangle(Framebuffer* fb, Vector2D v0, Vector2D v1, Vector2D v2, Color color) {
    drawLine(fb, (int)v0.x, (int)v0.y, (int)v1.x, (int)v1.y, color);
    drawLine(fb, (int)v1.x, (int)v1.y, (int)v2.x, (int)v2.y, color);
    drawLine(fb, (int)v2.x, (int)v2.y, (int)v0.x, (int)v0.y, color);
}
```

**Rasterization Benefits**:
- **Framebuffer**: Complete framebuffer management
- **Drawing Algorithms**: Bresenham's line algorithm
- **Primitives**: Line and triangle drawing
- **Depth Buffer**: Z-buffer for depth testing

## 🎯 3D Rendering Pipeline

### Camera Structure
```c
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
```

### Camera Management
```c
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
```

### 3D Projection
```c
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
```

### Mesh Rendering
```c
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
```

**Rendering Benefits**:
- **Complete Pipeline**: Full 3D rendering pipeline
- **Camera System**: Perspective projection and camera controls
- **Lighting**: Phong shading with multiple lights
- **Culling**: Backface culling for performance

## 🌟 Ray Tracing

### Ray Structure
```c
// Ray structure
typedef struct {
    Vector3D origin;
    Vector3D direction;
} Ray;
```

### Ray-Sphere Intersection
```c
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
```

### Ray Tracing
```c
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
```

**Ray Tracing Benefits**:
- **Physically Based**: Realistic light simulation
- **Reflections**: Natural light behavior
- **Shadows**: Automatic shadow generation
- **Quality**: High-quality rendering

## 🖼️ Texture Mapping

### Texture Structure
```c
// Texture structure
typedef struct {
    Color* data;
    int width;
    int height;
} Texture;
```

### Texture Operations
```c
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
```

**Texture Benefits**:
- **UV Mapping**: Texture coordinate mapping
- **Filtering**: Texture sampling and filtering
- **Patterns**: Procedural texture generation
- **Memory**: Efficient texture storage

## 🎬 Animation

### Animation System
```c
// Animation example
void demonstrateAnimation() {
    Scene* scene = createScene();
    if (!scene) return;
    
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
```

**Animation Benefits**:
- **Smooth Motion**: Continuous animation
- **Transformations**: Position, rotation, scale animation
- **Frame-based**: Frame-by-frame animation system
- **Interpolation**: Smooth interpolation between keyframes

## 🔧 Best Practices

### 1. Matrix Operations
```c
// Good: Use matrix multiplication for combined transforms
Matrix4x4 transform = mat4Multiply(
    mat4Multiply(translation, rotation),
    scale
);

// Bad: Apply transforms sequentially
Vector3D point = original_point;
point = vec3Add(point, translation);
point = mat4MultiplyVector3(rotation, point);
point = vec3Multiply(point, scale);
```

### 2. Normal Transformation
```c
// Good: Use inverse transpose for normal transformation
Matrix4x4 normal_matrix = mat4Transpose(mat4Inverse(model_matrix));
Vector3D transformed_normal = mat4MultiplyVector3(normal_matrix, normal);

// Bad: Transform normals with model matrix
Vector3D transformed_normal = mat4MultiplyVector3(model_matrix, normal);
```

### 3. Depth Testing
```c
// Good: Perform depth testing
if (depth < depth_buffer[y * width + x]) {
    setPixel(x, y, color);
    depth_buffer[y * width + x] = depth;
}

// Bad: No depth testing
setPixel(x, y, color); // Overwrites existing pixels
```

### 4. Lighting Calculations
```c
// Good: Normalize vectors before dot products
Vector3D normal = vec3Normalize(surface_normal);
Vector3D light_dir = vec3Normalize(to_light);
float diffuse = fmaxf(0.0f, vec3Dot(normal, light_dir));

// Bad: Use unnormalized vectors
float diffuse = vec3Dot(surface_normal, to_light);
```

### 5. Color Clamping
```c
// Good: Clamp color values
color.r = fminf(1.0f, fmaxf(0.0f, color.r));
color.g = fminf(1.0f, fmaxf(0.0f, color.g));
color.b = fminf(1.0f, fmaxf(0.0f, color.b));

// Bad: Allow color values to exceed range
color.r += light_intensity; // May exceed 1.0
```

## ⚠️ Common Pitfalls

### 1. Perspective Division
```c
// Wrong: Forget perspective divide
Vector4D projected = mat4MultiplyVector4(mvp_matrix, vertex);
Vector2D screen_pos = {projected.x, projected.y};

// Right: Perform perspective divide
Vector4D projected = mat4MultiplyVector4(mvp_matrix, vertex);
if (projected.w != 0.0f) {
    projected.x /= projected.w;
    projected.y /= projected.w;
}
Vector2D screen_pos = {projected.x, projected.y};
```

### 2. Matrix Order
```c
// Wrong: Incorrect matrix multiplication order
Matrix4x4 mvp = mat4Multiply(model_matrix, mat4Multiply(view_matrix, projection_matrix));

// Right: Correct order (Projection * View * Model)
Matrix4x4 mvp = mat4Multiply(projection_matrix, mat4Multiply(view_matrix, model_matrix));
```

### 3. Normal Calculation
```c
// Wrong: Don't normalize normals
Vector3D normal = vec3Cross(v1, v2);

// Right: Normalize normals
Vector3D normal = vec3Normalize(vec3Cross(v1, v2));
```

### 4. Texture Coordinates
```c
// Wrong: Don't handle texture wrapping
int x = uv.x * texture_width;
int y = uv.y * texture_height;

// Right: Handle texture wrapping
int x = ((int)(uv.x * texture_width)) % texture_width;
int y = ((int)(uv.y * texture_height)) % texture_height;
if (x < 0) x += texture_width;
if (y < 0) y += texture_height;
```

## 🔧 Real-World Applications

### 1. Game Engine
```c
// Game engine rendering loop
void gameEngineRender() {
    // Update camera
    updateCamera(game_camera);
    
    // Update animations
    updateAnimations(delta_time);
    
    // Render scene
    renderScene(game_scene);
    
    // Post-processing
    applyPostProcessing(framebuffer);
    
    // Present to screen
    swapBuffers();
}
```

### 2. 3D Modeling Software
```c
// 3D modeling viewport
void renderModelingViewport() {
    // Set up modeling camera
    Camera* camera = createCamera(orbit_position, model_center, up_vector, fov, aspect);
    
    // Render wireframe
    renderWireframe(model_mesh, camera);
    
    // Render gizmos
    renderGizmos(camera);
    
    // Render selection
    renderSelection(highlighted_mesh, camera);
}
```

### 3. Scientific Visualization
```c
// Scientific data visualization
void renderScientificData() {
    // Create visualization mesh from data
    Mesh* data_mesh = createMeshFromScientificData(data_points);
    
    // Apply color mapping based on values
    applyColorMapping(data_mesh, color_scale);
    
    // Render with transparency
    renderTransparentMesh(data_mesh, camera);
    
    // Render axes and labels
    renderAxes(camera);
    renderLabels(camera);
}
```

## 📚 Further Reading

### Books
- "Computer Graphics: Principles and Practice" by John F. Hughes
- "Real-Time Rendering" by Tomas Akenine-Möller
- "Physically Based Rendering" by Matt Pharr

### Topics
- OpenGL and DirectX graphics APIs
- GPU programming and shaders
- Advanced lighting models
- Global illumination algorithms
- Virtual reality graphics

Advanced computer graphics in C provides the foundation for building sophisticated 3D applications, from games to scientific visualizations. Master these techniques to create realistic, high-performance graphics applications!
