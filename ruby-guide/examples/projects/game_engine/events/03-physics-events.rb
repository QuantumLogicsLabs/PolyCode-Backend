# Physics Events System for Game Engine
# This file implements a comprehensive physics event system for the game engine,
# handling collision detection, physics calculations, and event dispatching.

require_relative 'event_manager'
require_relative '../components/physics_component'

module GameEngine
  module Events
    class PhysicsEvents
      def initialize(event_manager)
        @event_manager = event_manager
        @physics_components = []
        @collision_pairs = Set.new
        @gravity = Vector2.new(0, 9.81)
        @time_scale = 1.0
        @max_velocity = 1000.0
        @restitution = 0.8
        @friction = 0.95
        @air_resistance = 0.99
        
        setup_event_listeners
      end

      def register_physics_component(component)
        @physics_components << component
        component.add_physics_listener(self)
        
        @event_manager.dispatch_event(:physics_component_registered, {
          component: component,
          timestamp: Time.now
        })
      end

      def unregister_physics_component(component)
        @physics_components.delete(component)
        component.remove_physics_listener(self)
        
        @event_manager.dispatch_event(:physics_component_unregistered, {
          component: component,
          timestamp: Time.now
        })
      end

      def update(delta_time)
        scaled_delta_time = delta_time * @time_scale
        
        # Update physics for all components
        @physics_components.each do |component|
          next unless component.active?
          
          update_component_physics(component, scaled_delta_time)
        end
        
        # Check for collisions
        detect_collisions
        
        # Resolve collisions
        resolve_collisions
        
        # Clear collision pairs for next frame
        @collision_pairs.clear
      end

      def set_gravity(gravity)
        @gravity = gravity
        
        @event_manager.dispatch_event(:gravity_changed, {
          old_gravity: @gravity,
          new_gravity: gravity,
          timestamp: Time.now
        })
      end

      def set_time_scale(scale)
        @time_scale = scale
        
        @event_manager.dispatch_event(:time_scale_changed, {
          old_scale: @time_scale,
          new_scale: scale,
          timestamp: Time.now
        })
      end

      def set_restitution(restitution)
        @restitution = [[0.0, restitution].max, 1.0].min
        
        @event_manager.dispatch_event(:restitution_changed, {
          old_restitution: @restitution,
          new_restitution: restitution,
          timestamp: Time.now
        })
      end

      def set_friction(friction)
        @friction = [[0.0, friction].max, 1.0].min
        
        @event_manager.dispatch_event(:friction_changed, {
          old_friction: @friction,
          new_friction: friction,
          timestamp: Time.now
        })
      end

      def apply_force(component, force)
        return unless component.active?
        
        component.add_force(force)
        
        @event_manager.dispatch_event(:force_applied, {
          component: component,
          force: force,
          timestamp: Time.now
        })
      end

      def apply_impulse(component, impulse)
        return unless component.active?
        
        component.add_impulse(impulse)
        
        @event_manager.dispatch_event(:impulse_applied, {
          component: component,
          impulse: impulse,
          timestamp: Time.now
        })
      end

      def raycast(origin, direction, max_distance)
        hits = []
        
        @physics_components.each do |component|
          next unless component.active?
          next unless component.has_collider?
          
          hit = raycast_component(component, origin, direction, max_distance)
          hits << hit if hit
        end
        
        # Sort hits by distance
        hits.sort_by! { |hit| hit[:distance] }
        
        @event_manager.dispatch_event(:raycast_completed, {
          origin: origin,
          direction: direction,
          max_distance: max_distance,
          hits: hits,
          timestamp: Time.now
        })
        
        hits
      end

      def sphere_cast(center, radius)
        hits = []
        
        @physics_components.each do |component|
          next unless component.active?
          next unless component.has_collider?
          
          hit = sphere_cast_component(component, center, radius)
          hits << hit if hit
        end
        
        @event_manager.dispatch_event(:sphere_cast_completed, {
          center: center,
          radius: radius,
          hits: hits,
          timestamp: Time.now
        })
        
        hits
      end

      def box_cast(center, size)
        hits = []
        
        @physics_components.each do |component|
          next unless component.active?
          next unless component.has_collider?
          
          hit = box_cast_component(component, center, size)
          hits << hit if hit
        end
        
        @event_manager.dispatch_event(:box_cast_completed, {
          center: center,
          size: size,
          hits: hits,
          timestamp: Time.now
        })
        
        hits
      end

      def on_collision_start(component_a, component_b)
        collision_data = {
          component_a: component_a,
          component_b: component_b,
          contact_point: calculate_contact_point(component_a, component_b),
          contact_normal: calculate_contact_normal(component_a, component_b),
          penetration_depth: calculate_penetration_depth(component_a, component_b),
          relative_velocity: calculate_relative_velocity(component_a, component_b)
        }
        
        @event_manager.dispatch_event(:collision_start, collision_data)
        
        # Apply collision response
        apply_collision_response(component_a, component_b, collision_data)
      end

      def on_collision_end(component_a, component_b)
        collision_data = {
          component_a: component_a,
          component_b: component_b,
          timestamp: Time.now
        }
        
        @event_manager.dispatch_event(:collision_end, collision_data)
      end

      def on_trigger_enter(component_a, component_b)
        trigger_data = {
          trigger_component: component_a,
          other_component: component_b,
          timestamp: Time.now
        }
        
        @event_manager.dispatch_event(:trigger_enter, trigger_data)
      end

      def on_trigger_exit(component_a, component_b)
        trigger_data = {
          trigger_component: component_a,
          other_component: component_b,
          timestamp: Time.now
        }
        
        @event_manager.dispatch_event(:trigger_exit, trigger_data)
      end

      def on_physics_update(component)
        update_data = {
          component: component,
          position: component.position,
          velocity: component.velocity,
          acceleration: component.acceleration,
          timestamp: Time.now
        }
        
        @event_manager.dispatch_event(:physics_update, update_data)
      end

      private

      def setup_event_listeners
        @event_manager.add_listener(:component_transform_changed, method(:on_transform_changed))
        @event_manager.add_listener(:component_activated, method(:on_component_activated))
        @event_manager.add_listener(:component_deactivated, method(:on_component_deactivated))
      end

      def on_transform_changed(event_data)
        component = event_data[:component]
        
        # Update physics component if it exists
        physics_component = component.get_component(PhysicsComponent)
        return unless physics_component
        
        update_physics_transform(physics_component, event_data[:old_transform], event_data[:new_transform])
      end

      def on_component_activated(event_data)
        component = event_data[:component]
        physics_component = component.get_component(PhysicsComponent)
        
        if physics_component
          physics_component.active = true
        end
      end

      def on_component_deactivated(event_data)
        component = event_data[:component]
        physics_component = component.get_component(PhysicsComponent)
        
        if physics_component
          physics_component.active = false
        end
      end

      def update_component_physics(component, delta_time)
        # Apply gravity
        if component.affected_by_gravity?
          gravity_force = @gravity * component.mass
          apply_force(component, gravity_force)
        end
        
        # Apply forces
        total_force = component.total_force
        
        # Calculate acceleration (F = ma)
        acceleration = total_force / component.mass
        
        # Update velocity
        component.velocity += acceleration * delta_time
        
        # Apply air resistance
        component.velocity *= @air_resistance
        
        # Clamp velocity
        component.velocity = component.velocity.clamp_magnitude(@max_velocity)
        
        # Update position
        old_position = component.position
        component.position += component.velocity * delta_time
        
        # Clear forces for next frame
        component.clear_forces
        
        # Dispatch physics update event
        on_physics_update(component)
        
        # Check if position changed significantly
        if (component.position - old_position).magnitude > 0.01
          @event_manager.dispatch_event(:component_moved, {
            component: component,
            old_position: old_position,
            new_position: component.position,
            timestamp: Time.now
          })
        end
      end

      def update_physics_transform(physics_component, old_transform, new_transform)
        # Update position from transform
        physics_component.position = new_transform.position
        
        # Update velocity based on position change
        if old_transform
          position_change = new_transform.position - old_transform.position
          physics_component.velocity = position_change / (1.0 / 60.0) # Assuming 60 FPS
        end
      end

      def detect_collisions
        @physics_components.each_with_index do |component_a, i|
          next unless component_a.active?
          next unless component_a.has_collider?
          
          @physics_components[(i + 1)..-1].each do |component_b|
            next unless component_b.active?
            next unless component_b.has_collider?
            
            # Check if already colliding
            pair_key = [component_a.object_id, component_b.object_id].sort
            next if @collision_pairs.include?(pair_key)
            
            # Check for collision
            if check_collision(component_a, component_b)
              @collision_pairs.add(pair_key)
              
              if component_a.is_trigger? || component_b.is_trigger?
                # Trigger collision
                trigger_component = component_a.is_trigger? ? component_a : component_b
                other_component = component_a.is_trigger? ? component_b : component_a
                on_trigger_enter(trigger_component, other_component)
              else
                # Physics collision
                on_collision_start(component_a, component_b)
              end
            end
          end
        end
      end

      def resolve_collisions
        # This would be implemented with more sophisticated collision resolution
        # For now, we'll just handle basic collision response
        
        @collision_pairs.each do |pair|
          component_a = find_component_by_id(pair[0])
          component_b = find_component_by_id(pair[1])
          
          next unless component_a && component_b
          
          # Check if still colliding
          unless check_collision(component_a, component_b)
            @collision_pairs.delete(pair)
            
            if component_a.is_trigger? || component_b.is_trigger?
              trigger_component = component_a.is_trigger? ? component_a : component_b
              other_component = component_a.is_trigger? ? component_b : component_a
              on_trigger_exit(trigger_component, other_component)
            else
              on_collision_end(component_a, component_b)
            end
          end
        end
      end

      def check_collision(component_a, component_b)
        # Simple bounding box collision check
        # In a real implementation, this would use more sophisticated collision detection
        
        box_a = component_a.bounding_box
        box_b = component_b.bounding_box
        
        return false unless box_a && box_b
        
        # AABB collision check
        box_a.min.x <= box_b.max.x &&
        box_a.max.x >= box_b.min.x &&
        box_a.min.y <= box_b.max.y &&
        box_a.max.y >= box_b.min.y
      end

      def apply_collision_response(component_a, component_b, collision_data)
        # Calculate relative velocity
        relative_velocity = collision_data[:relative_velocity]
        contact_normal = collision_data[:contact_normal]
        
        # Calculate relative velocity along contact normal
        velocity_along_normal = relative_velocity.dot(contact_normal)
        
        # Don't resolve if velocities are separating
        return if velocity_along_normal > 0
        
        # Calculate restitution
        e = [component_a.restitution, component_b.restitution, @restitution].min
        
        # Calculate impulse scalar
        j = -(1 + e) * velocity_along_normal
        j /= 1 / component_a.mass + 1 / component_b.mass
        
        # Apply impulse
        impulse = contact_normal * j
        component_a.velocity -= impulse / component_a.mass
        component_b.velocity += impulse / component_b.mass
        
        # Position correction to prevent objects from sinking
        correction_magnitude = collision_data[:penetration_depth] * 0.2
        correction_direction = contact_normal
        correction = correction_direction * correction_magnitude
        
        component_a.position -= correction * (component_b.mass / (component_a.mass + component_b.mass))
        component_b.position += correction * (component_a.mass / (component_a.mass + component_b.mass))
      end

      def calculate_contact_point(component_a, component_b)
        # Simplified contact point calculation
        # In a real implementation, this would use more sophisticated algorithms
        
        center_a = component_a.position
        center_b = component_b.position
        
        (center_a + center_b) / 2
      end

      def calculate_contact_normal(component_a, component_b)
        # Calculate contact normal from component_a to component_b
        direction = component_b.position - component_a.position
        direction.normalize
      end

      def calculate_penetration_depth(component_a, component_b)
        # Simplified penetration depth calculation
        # In a real implementation, this would use more precise calculations
        
        distance = (component_b.position - component_a.position).magnitude
        combined_radius = component_a.radius + component_b.radius
        
        [combined_radius - distance, 0].max
      end

      def calculate_relative_velocity(component_a, component_b)
        component_b.velocity - component_a.velocity
      end

      def raycast_component(component, origin, direction, max_distance)
        # Simplified raycasting
        # In a real implementation, this would use more sophisticated ray-casting algorithms
        
        return nil unless component.has_collider?
        
        # Check if ray intersects with component's bounding box
        box = component.bounding_box
        return nil unless box
        
        # Simple ray-box intersection
        if ray_intersects_box(origin, direction, box, max_distance)
          distance = (component.position - origin).magnitude
          
          {
            component: component,
            distance: distance,
            point: origin + direction * distance,
            normal: (component.position - origin).normalize
          }
        else
          nil
        end
      end

      def sphere_cast_component(component, center, radius)
        return nil unless component.has_collider?
        
        # Check if sphere intersects with component
        distance = (component.position - center).magnitude
        combined_radius = radius + component.radius
        
        if distance <= combined_radius
          {
            component: component,
            distance: distance,
            point: component.position,
            normal: (component.position - center).normalize
          }
        else
          nil
        end
      end

      def box_cast_component(component, center, size)
        return nil unless component.has_collider?
        
        # Check if box intersects with component
        box_a = BoundingBox.new(center - size / 2, center + size / 2)
        box_b = component.bounding_box
        
        if box_a && box_b && boxes_intersect(box_a, box_b)
          {
            component: component,
            distance: (component.position - center).magnitude,
            point: component.position,
            normal: (component.position - center).normalize
          }
        else
          nil
        end
      end

      def ray_intersects_box(origin, direction, box, max_distance)
        # Simplified ray-box intersection
        # In a real implementation, this would use proper ray-box intersection algorithms
        
        # For now, just check if the ray passes through the box
        box_center = (box.min + box.max) / 2
        box_size = box.max - box.min
        
        # Check if ray direction points towards box
        to_box = box_center - origin
        projection = to_box.dot(direction)
        
        return false if projection < 0
        return false if projection > max_distance
        
        # Check if ray passes close enough to box
        closest_point = origin + direction * projection
        distance_to_box = (closest_point - box_center).magnitude
        
        distance_to_box <= box_size.magnitude / 2
      end

      def boxes_intersect(box_a, box_b)
        box_a.min.x <= box_b.max.x &&
        box_a.max.x >= box_b.min.x &&
        box_a.min.y <= box_b.max.y &&
        box_a.max.y >= box_b.min.y
      end

      def find_component_by_id(id)
        @physics_components.find { |component| component.object_id == id }
      end
    end

    # Physics event types
    module PhysicsEventTypes
      PHYSICS_COMPONENT_REGISTERED = :physics_component_registered
      PHYSICS_COMPONENT_UNREGISTERED = :physics_component_unregistered
      GRAVITY_CHANGED = :gravity_changed
      TIME_SCALE_CHANGED = :time_scale_changed
      RESTITUTION_CHANGED = :restitution_changed
      FRICTION_CHANGED = :friction_changed
      FORCE_APPLIED = :force_applied
      IMPULSE_APPLIED = :impulse_applied
      COLLISION_START = :collision_start
      COLLISION_END = :collision_end
      TRIGGER_ENTER = :trigger_enter
      TRIGGER_EXIT = :trigger_exit
      PHYSICS_UPDATE = :physics_update
      RAYCAST_COMPLETED = :raycast_completed
      SPHERE_CAST_COMPLETED = :sphere_cast_completed
      BOX_CAST_COMPLETED = :box_cast_completed
      COMPONENT_MOVED = :component_moved
    end

    # Physics event listener interface
    module PhysicsEventListener
      def on_physics_event(event_type, event_data)
        case event_type
        when PhysicsEventTypes::COLLISION_START
          handle_collision_start(event_data)
        when PhysicsEventTypes::COLLISION_END
          handle_collision_end(event_data)
        when PhysicsEventTypes::TRIGGER_ENTER
          handle_trigger_enter(event_data)
        when PhysicsEventTypes::TRIGGER_EXIT
          handle_trigger_exit(event_data)
        when PhysicsEventTypes::FORCE_APPLIED
          handle_force_applied(event_data)
        when PhysicsEventTypes::IMPULSE_APPLIED
          handle_impulse_applied(event_data)
        end
      end

      def handle_collision_start(event_data)
        # Override in implementing classes
      end

      def handle_collision_end(event_data)
        # Override in implementing classes
      end

      def handle_trigger_enter(event_data)
        # Override in implementing classes
      end

      def handle_trigger_exit(event_data)
        # Override in implementing classes
      end

      def handle_force_applied(event_data)
        # Override in implementing classes
      end

      def handle_impulse_applied(event_data)
        # Override in implementing classes
      end
    end
  end
end
