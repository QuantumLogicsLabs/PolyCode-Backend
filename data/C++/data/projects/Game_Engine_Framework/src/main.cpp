#include <iostream>
#include <memory>
#include <chrono>
#include <thread>
#include "engine/engine.h"
#include "graphics/opengl_renderer.h"
#include "physics/physics_world.h"
#include "audio/audio_engine.h"
#include "input/input_manager.h"
#include "resources/resource_manager.h"
#include "scene/scene_manager.h"
#include "game_object/game_object.h"
#include "components/transform.h"
#include "components/camera.h"
#include "components/sprite_renderer.h"
#include "components/rigid_body.h"
#include "components/audio_source.h"

// Simple game components
class PlayerController : public Component {
private:
    float speed = 5.0f;
    float rotationSpeed = 2.0f;
    
public:
    void update(float deltaTime) override {
        auto transform = getGameObject()->getComponent<Transform>();
        if (!transform) return;
        
        // Movement
        Vector3 movement;
        if (Input::isKeyDown(KeyCode::W)) movement.z += 1.0f;
        if (Input::isKeyDown(KeyCode::S)) movement.z -= 1.0f;
        if (Input::isKeyDown(KeyCode::A)) movement.x -= 1.0f;
        if (Input::isKeyDown(KeyCode::D)) movement.x += 1.0f;
        
        if (movement.length() > 0) {
            movement.normalize();
            transform->translate(movement * speed * deltaTime);
        }
        
        // Rotation
        if (Input::isKeyDown(KeyCode::Q)) {
            transform->rotate(Vector3::UP, rotationSpeed * deltaTime);
        }
        if (Input::isKeyDown(KeyCode::E)) {
            transform->rotate(Vector3::UP, -rotationSpeed * deltaTime);
        }
        
        // Jump
        if (Input::isKeyPressed(KeyCode::Space)) {
            auto rigidBody = getGameObject()->getComponent<RigidBody>();
            if (rigidBody) {
                rigidBody->addForce(Vector3::UP * 500.0f);
            }
        }
    }
};

class CameraController : public Component {
private:
    float sensitivity = 0.1f;
    float speed = 10.0f;
    Vector2 lastMousePosition;
    
public:
    void update(float deltaTime) override {
        auto transform = getGameObject()->getComponent<Transform>();
        auto camera = getGameObject()->getComponent<Camera>();
        if (!transform || !camera) return;
        
        // Mouse look
        Vector2 currentMousePos = Input::getMousePosition();
        Vector2 mouseDelta = currentMousePos - lastMousePosition;
        
        if (Input::isMouseButtonDown(MouseButton::RIGHT)) {
            // Yaw
            transform->rotate(Vector3::UP, -mouseDelta.x * sensitivity);
            
            // Pitch
            transform->rotate(Vector3::RIGHT, -mouseDelta.y * sensitivity);
        }
        
        lastMousePosition = currentMousePos;
        
        // Movement
        Vector3 movement;
        if (Input::isKeyDown(KeyCode::W)) movement += transform->getForward();
        if (Input::isKeyDown(KeyCode::S)) movement -= transform->getForward();
        if (Input::isKeyDown(KeyCode::A)) movement -= transform->getRight();
        if (Input::isKeyDown(KeyCode::D)) movement += transform->getRight();
        
        if (movement.length() > 0) {
            movement.normalize();
            transform->translate(movement * speed * deltaTime);
        }
    }
};

class RotatingCube : public Component {
private:
    float rotationSpeed = 1.0f;
    
public:
    void update(float deltaTime) override {
        auto transform = getGameObject()->getComponent<Transform>();
        if (!transform) return;
        
        transform->rotate(Vector3::UP, rotationSpeed * deltaTime);
        transform->rotate(Vector3::RIGHT, rotationSpeed * deltaTime * 0.7f);
        transform->rotate(Vector3::FORWARD, rotationSpeed * deltaTime * 0.3f);
    }
};

class BouncingBall : public Component {
private:
    Vector3 velocity;
    float gravity = -9.81f;
    float bounceDamping = 0.8f;
    float minBounceVelocity = 0.5f;
    
public:
    void start() override {
        velocity = Vector3(
            (rand() % 10 - 5) * 0.5f,
            10.0f,
            (rand() % 10 - 5) * 0.5f
        );
    }
    
    void update(float deltaTime) override {
        auto transform = getGameObject()->getComponent<Transform>();
        if (!transform) return;
        
        // Apply gravity
        velocity.y += gravity * deltaTime;
        
        // Update position
        Vector3 newPosition = transform->getPosition() + velocity * deltaTime;
        transform->setPosition(newPosition);
        
        // Bounce when hitting ground
        if (newPosition.y <= 0.5f) {
            newPosition.y = 0.5f;
            velocity.y = -velocity.y * bounceDamping;
            
            // Stop tiny bounces
            if (abs(velocity.y) < minBounceVelocity) {
                velocity.y = 0;
            }
        }
        
        // Bounce off walls
        if (abs(newPosition.x) > 10.0f) {
            newPosition.x = (newPosition.x > 0) ? 10.0f : -10.0f;
            velocity.x = -velocity.x * bounceDamping;
        }
        
        if (abs(newPosition.z) > 10.0f) {
            newPosition.z = (newPosition.z > 0) ? 10.0f : -10.0f;
            velocity.z = -velocity.z * bounceDamping;
        }
        
        transform->setPosition(newPosition);
    }
};

class AudioController : public Component {
private:
    float playInterval = 3.0f;
    float timer = 0.0f;
    
public:
    void update(float deltaTime) override {
        timer += deltaTime;
        
        if (timer >= playInterval) {
            timer = 0.0f;
            
            auto audioSource = getGameObject()->getComponent<AudioSource>();
            if (audioSource) {
                audioSource->play();
            }
        }
    }
};

// Scene setup functions
void setupDemoScene(std::shared_ptr<Scene> scene) {
    // Create camera
    auto camera = scene->createGameObject("Main Camera");
    camera->addComponent<Transform>(Vector3(0.0f, 5.0f, 10.0f));
    camera->addComponent<Camera>(70.0f, 0.1f, 100.0f);
    camera->addComponent<CameraController>();
    
    // Create player
    auto player = scene->createGameObject("Player");
    player->addComponent<Transform>(Vector3(0.0f, 1.0f, 0.0f));
    player->addComponent<RigidBody>(1.0f);
    player->addComponent<PlayerController>();
    
    // Create ground
    auto ground = scene->createGameObject("Ground");
    ground->addComponent<Transform>(Vector3(0.0f, 0.0f, 0.0f));
    ground->addComponent<RigidBody>(0.0f); // Static body
    
    // Create rotating cubes
    for (int i = 0; i < 5; ++i) {
        auto cube = scene->createGameObject("Cube " + std::to_string(i));
        cube->addComponent<Transform>(
            Vector3(i * 3.0f - 6.0f, 2.0f, 0.0f),
            Vector3::ONE * 0.5f
        );
        cube->addComponent<RigidBody>(1.0f);
        cube->addComponent<RotatingCube>();
    }
    
    // Create bouncing balls
    for (int i = 0; i < 10; ++i) {
        auto ball = scene->createGameObject("Ball " + std::to_string(i));
        ball->addComponent<Transform>(
            Vector3(
                (rand() % 20 - 10) * 0.5f,
                5.0f + i * 2.0f,
                (rand() % 20 - 10) * 0.5f
            ),
            Vector3::ONE * 0.3f
        );
        ball->addComponent<RigidBody>(0.5f);
        ball->addComponent<BouncingBall>();
        ball->addComponent<AudioSource>("bounce.wav");
        ball->addComponent<AudioController>();
    }
    
    // Create lights
    auto directionalLight = scene->createGameObject("Directional Light");
    directionalLight->addComponent<Transform>(Vector3(5.0f, 10.0f, 5.0f));
    directionalLight->addComponent<DirectionalLight>(Vector3(1.0f, 1.0f, 1.0f), 0.8f);
    
    auto pointLight = scene->createGameObject("Point Light");
    pointLight->addComponent<Transform>(Vector3(0.0f, 5.0f, 0.0f));
    pointLight->addComponent<PointLight>(Vector3(1.0f, 0.5f, 0.5f), 2.0f, 10.0f);
}

void setupPhysicsDemo(std::shared_ptr<Scene> scene) {
    // Create camera
    auto camera = scene->createGameObject("Camera");
    camera->addComponent<Transform>(Vector3(0.0f, 10.0f, 20.0f));
    camera->addComponent<Camera>(70.0f, 0.1f, 100.0f);
    camera->addComponent<CameraController>();
    
    // Create physics playground
    // Ground
    auto ground = scene->createGameObject("Ground");
    ground->addComponent<Transform>(Vector3(0.0f, 0.0f, 0.0f), Vector3(20.0f, 1.0f, 20.0f));
    ground->addComponent<RigidBody>(0.0f);
    
    // Walls
    std::vector<Vector3> wallPositions = {
        Vector3(0.0f, 5.0f, -10.0f),  // Front
        Vector3(0.0f, 5.0f, 10.0f),   // Back
        Vector3(-10.0f, 5.0f, 0.0f),  // Left
        Vector3(10.0f, 5.0f, 0.0f)    // Right
    };
    
    std::vector<Vector3> wallScales = {
        Vector3(20.0f, 10.0f, 1.0f),   // Front/Back
        Vector3(20.0f, 10.0f, 1.0f),   // Front/Back
        Vector3(1.0f, 10.0f, 20.0f),   // Left/Right
        Vector3(1.0f, 10.0f, 20.0f)    // Left/Right
    };
    
    for (size_t i = 0; i < wallPositions.size(); ++i) {
        auto wall = scene->createGameObject("Wall " + std::to_string(i));
        wall->addComponent<Transform>(wallPositions[i], wallScales[i]);
        wall->addComponent<RigidBody>(0.0f);
    }
    
    // Create various physics objects
    // Spheres
    for (int i = 0; i < 5; ++i) {
        auto sphere = scene->createGameObject("Sphere " + std::to_string(i));
        sphere->addComponent<Transform>(
            Vector3((i - 2) * 2.0f, 8.0f + i, 0.0f),
            Vector3::ONE * 0.5f
        );
        sphere->addComponent<RigidBody>(1.0f);
    }
    
    // Boxes
    for (int i = 0; i < 5; ++i) {
        auto box = scene->createGameObject("Box " + std::to_string(i));
        box->addComponent<Transform>(
            Vector3((i - 2) * 2.0f, 6.0f + i, 2.0f),
            Vector3(1.0f, 1.0f, 1.0f)
        );
        box->addComponent<RigidBody>(2.0f);
    }
    
    // Create pendulum
    auto pendulumAnchor = scene->createGameObject("Pendulum Anchor");
    pendulumAnchor->addComponent<Transform>(Vector3(0.0f, 15.0f, 0.0f));
    
    auto pendulumBob = scene->createGameObject("Pendulum Bob");
    pendulumBob->addComponent<Transform>(Vector3(0.0f, 10.0f, 0.0f));
    pendulumBob->addComponent<RigidBody>(1.0f);
    
    // Create joint (would need actual joint implementation)
    // auto joint = scene->createJoint<HingeJoint>(pendulumAnchor, pendulumBob, Vector3::UP);
    
    // Create dominoes
    for (int i = 0; i < 10; ++i) {
        auto domino = scene->createGameObject("Domino " + std::to_string(i));
        domino->addComponent<Transform>(
            Vector3(-8.0f + i * 0.8f, 1.0f, -5.0f),
            Vector3(0.2f, 2.0f, 1.0f)
        );
        domino->addComponent<RigidBody>(0.5f);
    }
    
    // Create ball to knock over dominoes
    auto ball = scene->createGameObject("Ball");
    ball->addComponent<Transform>(Vector3(-12.0f, 2.0f, -5.0f));
    ball->addComponent<RigidBody>(1.0f);
    // ball->getComponent<RigidBody>()->setVelocity(Vector3(10.0f, 0.0f, 0.0f));
}

void setupAudioDemo(std::shared_ptr<Scene> scene) {
    // Create camera
    auto camera = scene->createGameObject("Camera");
    camera->addComponent<Transform>(Vector3(0.0f, 2.0f, 10.0f));
    camera->addComponent<Camera>(70.0f, 0.1f, 100.0f);
    camera->addComponent<CameraController>();
    
    // Create audio sources
    std::vector<std::string> soundFiles = {
        "music.wav",
        "explosion.wav",
        "footsteps.wav",
        "ambient.wav"
    };
    
    for (size_t i = 0; i < soundFiles.size(); ++i) {
        auto audioObject = scene->createGameObject("Audio Source " + std::to_string(i));
        audioObject->addComponent<Transform>(
            Vector3((i - 1.5f) * 3.0f, 0.0f, 0.0f)
        );
        audioObject->addComponent<AudioSource>(soundFiles[i]);
        audioObject->addComponent<AudioController>();
    }
    
    // Create visual representations for audio sources
    for (size_t i = 0; i < soundFiles.size(); ++i) {
        auto visual = scene->createGameObject("Visual " + std::to_string(i));
        visual->addComponent<Transform>(
            Vector3((i - 1.5f) * 3.0f, 0.5f, 0.0f),
            Vector3::ONE * 0.3f
        );
        visual->addComponent<RotatingCube>();
    }
    
    // Create background music
    auto musicPlayer = scene->createGameObject("Music Player");
    musicPlayer->addComponent<AudioSource>("background_music.wav");
    auto audioSource = musicPlayer->getComponent<AudioSource>();
    if (audioSource) {
        audioSource->setLoop(true);
        audioSource->setVolume(0.5f);
        audioSource->play();
    }
}

int main(int argc, char* argv[]) {
    try {
        // Initialize engine
        Engine engine;
        engine.initialize("Game Engine Demo", 1920, 1080);
        
        // Create scene manager
        auto sceneManager = engine.getSceneManager();
        
        // Create different scenes
        auto demoScene = sceneManager->createScene("Demo Scene");
        auto physicsScene = sceneManager->createScene("Physics Demo");
        auto audioScene = sceneManager->createScene("Audio Demo");
        
        // Setup scenes
        setupDemoScene(demoScene);
        setupPhysicsDemo(physicsScene);
        setupAudioDemo(audioScene);
        
        // Set active scene
        sceneManager->setActiveScene("Demo Scene");
        
        // Main game loop
        bool running = true;
        auto lastTime = std::chrono::high_resolution_clock::now();
        
        while (running) {
            auto currentTime = std::chrono::high_resolution_clock::now();
            float deltaTime = std::chrono::duration<float>(currentTime - lastTime).count();
            lastTime = currentTime;
            
            // Handle input
            if (Input::isKeyPressed(KeyCode::ESCAPE)) {
                running = false;
            }
            
            // Scene switching
            if (Input::isKeyPressed(KeyCode::KEY_1)) {
                sceneManager->setActiveScene("Demo Scene");
            }
            if (Input::isKeyPressed(KeyCode::KEY_2)) {
                sceneManager->setActiveScene("Physics Demo");
            }
            if (Input::isKeyPressed(KeyCode::KEY_3)) {
                sceneManager->setActiveScene("Audio Demo");
            }
            
            // Update engine
            engine.update(deltaTime);
            
            // Render
            engine.render();
            
            // Display controls
            if (Input::isKeyPressed(KeyCode::F1)) {
                std::cout << "\n=== CONTROLS ===" << std::endl;
                std::cout << "ESC - Exit" << std::endl;
                std::cout << "1 - Demo Scene" << std::endl;
                std::cout << "2 - Physics Demo" << std::endl;
                std::cout << "3 - Audio Demo" << std::endl;
                std::cout << "WASD - Move (Player/Camera)" << std::endl;
                std::cout << "Mouse - Look (Right click)" << std::endl;
                std::cout << "Space - Jump" << std::endl;
                std::cout << "Q/E - Rotate" << std::endl;
                std::cout << "F1 - Show controls" << std::endl;
                std::cout << "==================" << std::endl;
            }
        }
        
        // Cleanup
        engine.shutdown();
        
    } catch (const std::exception& e) {
        std::cerr << "Error: " << e.what() << std::endl;
        return 1;
    }
    
    return 0;
}