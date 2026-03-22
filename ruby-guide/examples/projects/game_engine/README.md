# Game Engine

A simple 2D game engine implemented in Ruby that demonstrates game loop architecture, entity-component system, state management, and event handling.

## Features

- Entity-Component-System (ECS) architecture
- Game loop with fixed timestep
- Component-based game objects
- Event system for communication
- Basic physics simulation
- Input handling
- State management
- Simple rendering system

## Concepts Demonstrated

- Game loop implementation
- Entity-Component-System pattern
- Event-driven architecture
- State management
- Component-based design
- Physics simulation basics
- Input handling patterns
- Modular game architecture

## How to Run

```bash
ruby main.rb
```

## Usage Examples

```
Game Engine Controls:
==================
Arrow Keys: Move player
Space: Jump
R: Reset game
Q: Quit game

Game Loop:
- Updates at 60 FPS
- Fixed timestep for physics
- Entity system updates
- Event processing
- Rendering

Components:
- Position: (x, y) coordinates
- Velocity: movement speed
- Render: visual representation
- Physics: collision detection
- Input: player control
```

## Project Structure

```
game_engine/
├── main.rb              # Main game entry point
├── game_engine.rb       # Core engine class
├── game_loop.rb         # Game loop implementation
├── entity_manager.rb    # Entity management system
├── components/          # Component definitions
│   ├── position.rb
│   ├── velocity.rb
│   ├── render.rb
│   ├── physics.rb
│   └── input.rb
├── systems/             # System implementations
│   ├── movement_system.rb
│   ├── render_system.rb
│   ├── physics_system.rb
│   └── input_system.rb
├── events/              # Event system
│   ├── event_manager.rb
│   └── events.rb
└── README.md            # This file
```

## Code Overview

### GameEngine Class
Main engine class that:
- Manages the game loop
- Coordinates all systems
- Handles game state
- Provides main API

### Entity Manager
Manages entities with:
- Entity creation and destruction
- Component management
- Entity queries
- Component relationships

### Components
Data components for:
- Position (x, y coordinates)
- Velocity (movement vectors)
- Render (visual properties)
- Physics (collision properties)
- Input (player control)

### Systems
Processing systems for:
- Movement (position updates)
- Rendering (visual output)
- Physics (collision detection)
- Input (user interaction)

## Game Architecture

### Entity-Component-System
```
Entity (ID) + Components (Data) = Game Object
Systems process entities with required components
```

### Game Loop
```
while game_running:
  handle_input()
  update_physics(fixed_timestep)
  update_game_state()
  render()
```

### Event System
```
Events flow: Input → Game Logic → Systems → Rendering
Decoupled communication between components
```

## Example Game Objects

### Player
```ruby
player = entity_manager.create_entity
entity_manager.add_component(player, Position.new(100, 100))
entity_manager.add_component(player, Velocity.new(0, 0))
entity_manager.add_component(player, Render.new('@', 'white'))
entity_manager.add_component(player, Input.new(:player))
```

### Obstacle
```ruby
obstacle = entity_manager.create_entity
entity_manager.add_component(obstacle, Position.new(200, 150))
entity_manager.add_component(obstacle, Render.new('#', 'red'))
entity_manager.add_component(obstacle, Physics.new(true))
```

## Extensions to Try

1. **Advanced Rendering**: Add sprite support, animations, layers
2. **Audio System**: Sound effects, background music
3. **Save/Load**: Game state persistence
4. **Level System**: Multiple levels, level transitions
5. **AI Components**: Enemy behavior, pathfinding
6. **Networking**: Multiplayer support
7. **Physics**: Advanced collision, forces, gravity
8. **UI System**: Menus, HUD, dialog boxes

## Performance Considerations

- Fixed timestep for stable physics
- Component pooling for memory efficiency
- Spatial partitioning for collision detection
- Event batching for performance
- Efficient rendering with dirty flags

## Best Practices

- Separate data from logic (ECS pattern)
- Use events for loose coupling
- Keep systems focused and single-purpose
- Profile and optimize bottlenecks
- Modular design for extensibility

---

**This game engine demonstrates advanced Ruby patterns and game development concepts! 🎮**
