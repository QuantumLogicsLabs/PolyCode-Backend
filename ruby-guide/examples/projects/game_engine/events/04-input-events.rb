# Input Events System for Game Engine
# This file implements a comprehensive input event system for the game engine,
# handling keyboard, mouse, touch, and game controller input events.

require_relative 'event_manager'

module GameEngine
  module Events
    class InputEvents
      def initialize(event_manager)
        @event_manager = event_manager
        @keyboard_state = {}
        @mouse_state = {}
        @touch_state = {}
        @controller_state = {}
        @input_mappings = {}
        @action_mappings = {}
        @dead_zones = {}
        @sensitivity = {
          mouse: 1.0,
          controller: 1.0
        }
        @enabled = true
        
        setup_event_listeners
        initialize_input_state
      end

      def enable
        @enabled = true
        @event_manager.dispatch_event(:input_enabled, { timestamp: Time.now })
      end

      def disable
        @enabled = false
        @event_manager.dispatch_event(:input_disabled, { timestamp: Time.now })
      end

      def enabled?
        @enabled
      end

      def set_mouse_sensitivity(sensitivity)
        @sensitivity[:mouse] = sensitivity
        @event_manager.dispatch_event(:mouse_sensitivity_changed, {
          sensitivity: sensitivity,
          timestamp: Time.now
        })
      end

      def set_controller_sensitivity(sensitivity)
        @sensitivity[:controller] = sensitivity
        @event_manager.dispatch_event(:controller_sensitivity_changed, {
          sensitivity: sensitivity,
          timestamp: Time.now
        })
      end

      def set_dead_zone(controller_axis, dead_zone)
        @dead_zones[controller_axis] = dead_zone
        @event_manager.dispatch_event(:dead_zone_changed, {
          axis: controller_axis,
          dead_zone: dead_zone,
          timestamp: Time.now
        })
      end

      def map_input(input_code, action_name)
        @input_mappings[input_code] = action_name
        @event_manager.dispatch_event(:input_mapped, {
          input_code: input_code,
          action_name: action_name,
          timestamp: Time.now
        })
      end

      def unmap_input(input_code)
        action_name = @input_mappings.delete(input_code)
        if action_name
          @event_manager.dispatch_event(:input_unmapped, {
            input_code: input_code,
            action_name: action_name,
            timestamp: Time.now
          })
        end
      end

      def map_action(action_name, &block)
        @action_mappings[action_name] = block
      end

      def unmap_action(action_name)
        @action_mappings.delete(action_name)
      end

      # Keyboard events
      def on_key_down(key_code, modifiers = {})
        return unless @enabled
        
        key = normalize_key_code(key_code)
        
        # Update keyboard state
        @keyboard_state[key] = {
          pressed: true,
          timestamp: Time.now,
          modifiers: modifiers
        }
        
        # Get mapped action
        action_name = @input_mappings[key]
        
        # Dispatch events
        @event_manager.dispatch_event(:key_down, {
          key_code: key,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
        
        if action_name
          @event_manager.dispatch_event(:action_triggered, {
            action_name: action_name,
            input_type: :keyboard,
            input_code: key,
            timestamp: Time.now
          })
          
          # Execute action block if mapped
          if @action_mappings[action_name]
            @action_mappings[action_name].call(:key_down, key, modifiers)
          end
        end
      end

      def on_key_up(key_code, modifiers = {})
        return unless @enabled
        
        key = normalize_key_code(key_code)
        
        # Update keyboard state
        @keyboard_state[key] = {
          pressed: false,
          timestamp: Time.now,
          modifiers: modifiers
        }
        
        # Get mapped action
        action_name = @input_mappings[key]
        
        # Dispatch events
        @event_manager.dispatch_event(:key_up, {
          key_code: key,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
        
        if action_name
          @event_manager.dispatch_event(:action_released, {
            action_name: action_name,
            input_type: :keyboard,
            input_code: key,
            timestamp: Time.now
          })
          
          # Execute action block if mapped
          if @action_mappings[action_name]
            @action_mappings[action_name].call(:key_up, key, modifiers)
          end
        end
      end

      def on_key_pressed(key_code, duration, modifiers = {})
        return unless @enabled
        
        key = normalize_key_code(key_code)
        action_name = @input_mappings[key]
        
        @event_manager.dispatch_event(:key_pressed, {
          key_code: key,
          duration: duration,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
      end

      # Mouse events
      def on_mouse_button_down(button, position, modifiers = {})
        return unless @enabled
        
        # Update mouse state
        @mouse_state[button] = {
          pressed: true,
          position: position,
          timestamp: Time.now,
          modifiers: modifiers
        }
        
        # Get mapped action
        action_name = @input_mappings["mouse_#{button}"]
        
        # Dispatch events
        @event_manager.dispatch_event(:mouse_button_down, {
          button: button,
          position: position,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
        
        if action_name
          @event_manager.dispatch_event(:action_triggered, {
            action_name: action_name,
            input_type: :mouse,
            input_code: "mouse_#{button}",
            position: position,
            timestamp: Time.now
          })
          
          # Execute action block if mapped
          if @action_mappings[action_name]
            @action_mappings[action_name].call(:mouse_down, button, position, modifiers)
          end
        end
      end

      def on_mouse_button_up(button, position, modifiers = {})
        return unless @enabled
        
        # Update mouse state
        @mouse_state[button] = {
          pressed: false,
          position: position,
          timestamp: Time.now,
          modifiers: modifiers
        }
        
        # Get mapped action
        action_name = @input_mappings["mouse_#{button}"]
        
        # Dispatch events
        @event_manager.dispatch_event(:mouse_button_up, {
          button: button,
          position: position,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
        
        if action_name
          @event_manager.dispatch_event(:action_released, {
            action_name: action_name,
            input_type: :mouse,
            input_code: "mouse_#{button}",
            position: position,
            timestamp: Time.now
          })
          
          # Execute action block if mapped
          if @action_mappings[action_name]
            @action_mappings[action_name].call(:mouse_up, button, position, modifiers)
          end
        end
      end

      def on_mouse_button_clicked(button, position, modifiers = {})
        return unless @enabled
        
        action_name = @input_mappings["mouse_#{button}"]
        
        @event_manager.dispatch_event(:mouse_button_clicked, {
          button: button,
          position: position,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
      end

      def on_mouse_button_double_clicked(button, position, modifiers = {})
        return unless @enabled
        
        action_name = @input_mappings["mouse_#{button}_double"]
        
        @event_manager.dispatch_event(:mouse_button_double_clicked, {
          button: button,
          position: position,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
      end

      def on_mouse_moved(position, delta, modifiers = {})
        return unless @enabled
        
        # Update mouse position
        @mouse_state[:position] = position
        @mouse_state[:delta] = delta
        
        # Apply sensitivity
        scaled_delta = delta * @sensitivity[:mouse]
        
        @event_manager.dispatch_event(:mouse_moved, {
          position: position,
          delta: scaled_delta,
          modifiers: modifiers,
          timestamp: Time.now
        })
      end

      def on_mouse_wheel_scrolled(delta, position, modifiers = {})
        return unless @enabled
        
        action_name = @input_mappings["mouse_wheel"]
        
        @event_manager.dispatch_event(:mouse_wheel_scrolled, {
          delta: delta,
          position: position,
          modifiers: modifiers,
          action_name: action_name,
          timestamp: Time.now
        })
        
        if action_name
          @event_manager.dispatch_event(:action_triggered, {
            action_name: action_name,
            input_type: :mouse,
            input_code: "mouse_wheel",
            delta: delta,
            position: position,
            timestamp: Time.now
          })
          
          # Execute action block if mapped
          if @action_mappings[action_name]
            @action_mappings[action_name].call(:mouse_wheel, delta, position, modifiers)
          end
        end
      end

      # Touch events
      def on_touch_start(touch_id, position, pressure = 1.0)
        return unless @enabled
        
        # Update touch state
        @touch_state[touch_id] = {
          active: true,
          position: position,
          pressure: pressure,
          timestamp: Time.now
        }
        
        @event_manager.dispatch_event(:touch_start, {
          touch_id: touch_id,
          position: position,
          pressure: pressure,
          timestamp: Time.now
        })
      end

      def on_touch_moved(touch_id, position, pressure = 1.0)
        return unless @enabled
        
        if @touch_state[touch_id]
          @touch_state[touch_id][:position] = position
          @touch_state[touch_id][:pressure] = pressure
          @touch_state[touch_id][:timestamp] = Time.now
        end
        
        @event_manager.dispatch_event(:touch_moved, {
          touch_id: touch_id,
          position: position,
          pressure: pressure,
          timestamp: Time.now
        })
      end

      def on_touch_end(touch_id, position)
        return unless @enabled
        
        if @touch_state[touch_id]
          @touch_state[touch_id][:active] = false
          @touch_state[touch_id][:position] = position
          @touch_state[touch_id][:timestamp] = Time.now
        end
        
        @event_manager.dispatch_event(:touch_end, {
          touch_id: touch_id,
          position: position,
          timestamp: Time.now
        })
      end

      def on_touch_cancelled(touch_id)
        return unless @enabled
        
        if @touch_state[touch_id]
          @touch_state[touch_id][:active] = false
          @touch_state[touch_id][:timestamp] = Time.now
        end
        
        @event_manager.dispatch_event(:touch_cancelled, {
          touch_id: touch_id,
          timestamp: Time.now
        })
      end

      # Game controller events
      def on_controller_connected(controller_id, controller_name)
        @controller_state[controller_id] = {
          name: controller_name,
          connected: true,
          buttons: {},
          axes: {},
          timestamp: Time.now
        }
        
        @event_manager.dispatch_event(:controller_connected, {
          controller_id: controller_id,
          controller_name: controller_name,
          timestamp: Time.now
        })
      end

      def on_controller_disconnected(controller_id)
        if @controller_state[controller_id]
          @controller_state[controller_id][:connected] = false
          @controller_state[controller_id][:timestamp] = Time.now
        end
        
        @event_manager.dispatch_event(:controller_disconnected, {
          controller_id: controller_id,
          timestamp: Time.now
        })
      end

      def on_controller_button_down(controller_id, button_code)
        return unless @enabled
        
        controller = @controller_state[controller_id]
        return unless controller && controller[:connected]
        
        # Update controller state
        controller[:buttons][button_code] = {
          pressed: true,
          timestamp: Time.now
        }
        
        # Get mapped action
        action_name = @input_mappings["controller_#{button_code}"]
        
        # Dispatch events
        @event_manager.dispatch_event(:controller_button_down, {
          controller_id: controller_id,
          button_code: button_code,
          action_name: action_name,
          timestamp: Time.now
        })
        
        if action_name
          @event_manager.dispatch_event(:action_triggered, {
            action_name: action_name,
            input_type: :controller,
            input_code: "controller_#{button_code}",
            controller_id: controller_id,
            timestamp: Time.now
          })
          
          # Execute action block if mapped
          if @action_mappings[action_name]
            @action_mappings[action_name].call(:controller_down, controller_id, button_code)
          end
        end
      end

      def on_controller_button_up(controller_id, button_code)
        return unless @enabled
        
        controller = @controller_state[controller_id]
        return unless controller && controller[:connected]
        
        # Update controller state
        controller[:buttons][button_code] = {
          pressed: false,
          timestamp: Time.now
        }
        
        # Get mapped action
        action_name = @input_mappings["controller_#{button_code}"]
        
        # Dispatch events
        @event_manager.dispatch_event(:controller_button_up, {
          controller_id: controller_id,
          button_code: button_code,
          action_name: action_name,
          timestamp: Time.now
        })
        
        if action_name
          @event_manager.dispatch_event(:action_released, {
            action_name: action_name,
            input_type: :controller,
            input_code: "controller_#{button_code}",
            controller_id: controller_id,
            timestamp: Time.now
          })
          
          # Execute action block if mapped
          if @action_mappings[action_name]
            @action_mappings[action_name].call(:controller_up, controller_id, button_code)
          end
        end
      end

      def on_controller_axis_moved(controller_id, axis_code, value)
        return unless @enabled
        
        controller = @controller_state[controller_id]
        return unless controller && controller[:connected]
        
        # Apply dead zone
        dead_zone = @dead_zones[axis_code] || 0.1
        adjusted_value = apply_dead_zone(value, dead_zone)
        
        # Apply sensitivity
        adjusted_value *= @sensitivity[:controller]
        
        # Update controller state
        controller[:axes][axis_code] = {
          value: adjusted_value,
          raw_value: value,
          timestamp: Time.now
        }
        
        @event_manager.dispatch_event(:controller_axis_moved, {
          controller_id: controller_id,
          axis_code: axis_code,
          value: adjusted_value,
          raw_value: value,
          timestamp: Time.now
        })
      end

      # Input state queries
      def is_key_pressed?(key_code)
        key = normalize_key_code(key_code)
        state = @keyboard_state[key]
        state ? state[:pressed] : false
      end

      def is_mouse_button_pressed?(button)
        state = @mouse_state[button]
        state ? state[:pressed] : false
      end

      def is_controller_button_pressed?(controller_id, button_code)
        controller = @controller_state[controller_id]
        return false unless controller && controller[:connected]
        
        button_state = controller[:buttons][button_code]
        button_state ? button_state[:pressed] : false
      end

      def get_mouse_position
        @mouse_state[:position] || Vector2.new(0, 0)
      end

      def get_mouse_delta
        @mouse_state[:delta] || Vector2.new(0, 0)
      end

      def get_controller_axis_value(controller_id, axis_code)
        controller = @controller_state[controller_id]
        return 0.0 unless controller && controller[:connected]
        
        axis_state = controller[:axes][axis_code]
        axis_state ? axis_state[:value] : 0.0
      end

      def get_active_touches
        @touch_state.select { |id, state| state[:active] }
      end

      def get_connected_controllers
        @controller_state.select { |id, state| state[:connected] }
      end

      # Input mapping utilities
      def load_input_mappings(file_path)
        return unless File.exist?(file_path)
        
        mappings = YAML.load_file(file_path)
        
        mappings.each do |input_code, action_name|
          map_input(input_code, action_name)
        end
        
        @event_manager.dispatch_event(:input_mappings_loaded, {
          file_path: file_path,
          mappings_loaded: mappings.length,
          timestamp: Time.now
        })
      end

      def save_input_mappings(file_path)
        mappings = {}
        
        @input_mappings.each do |input_code, action_name|
          mappings[input_code] = action_name
        end
        
        File.write(file_path, YAML.dump(mappings))
        
        @event_manager.dispatch_event(:input_mappings_saved, {
          file_path: file_path,
          mappings_saved: mappings.length,
          timestamp: Time.now
        })
      end

      def reset_input_mappings
        @input_mappings.clear
        @action_mappings.clear
        
        @event_manager.dispatch_event(:input_mappings_reset, {
          timestamp: Time.now
        })
      end

      private

      def setup_event_listeners
        @event_manager.add_listener(:game_paused, method(:on_game_paused))
        @event_manager.add_listener(:game_resumed, method(:on_game_resumed))
        @event_manager.add_listener(:focus_lost, method(:on_focus_lost))
        @event_manager.add_listener(:focus_gained, method(:on_focus_gained))
      end

      def initialize_input_state
        # Initialize keyboard state
        (0..255).each do |code|
          @keyboard_state[code] = { pressed: false, timestamp: Time.now }
        end
        
        # Initialize mouse state
        @mouse_state = {
          position: Vector2.new(0, 0),
          delta: Vector2.new(0, 0)
        }
        
        (0..10).each do |button|
          @mouse_state[button] = { pressed: false, timestamp: Time.now }
        end
      end

      def normalize_key_code(key_code)
        case key_code
        when Symbol then key_code
        when String then key_code.downcase.to_sym
        when Integer then key_code
        else key_code.to_sym
        end
      end

      def apply_dead_zone(value, dead_zone)
        return 0.0 if value.abs < dead_zone
        
        # Normalize value outside dead zone
        if value > 0
          (value - dead_zone) / (1.0 - dead_zone)
        else
          (value + dead_zone) / (1.0 - dead_zone)
        end
      end

      def on_game_paused(event_data)
        # Disable input when game is paused
        @enabled = false
      end

      def on_game_resumed(event_data)
        # Re-enable input when game is resumed
        @enabled = true
      end

      def on_focus_lost(event_data)
        # Disable input when focus is lost
        @enabled = false
        
        # Clear input state
        clear_input_state
      end

      def on_focus_gained(event_data)
        # Re-enable input when focus is gained
        @enabled = true
      end

      def clear_input_state
        # Clear keyboard state
        @keyboard_state.each do |key, state|
          state[:pressed] = false
        end
        
        # Clear mouse state
        @mouse_state.each do |key, state|
          if key == :position || key == :delta
            next
          end
          state[:pressed] = false
        end
        
        # Clear touch state
        @touch_state.clear
      end
    end

    # Input event types
    module InputEventTypes
      KEY_DOWN = :key_down
      KEY_UP = :key_up
      KEY_PRESSED = :key_pressed
      MOUSE_BUTTON_DOWN = :mouse_button_down
      MOUSE_BUTTON_UP = :mouse_button_up
      MOUSE_BUTTON_CLICKED = :mouse_button_clicked
      MOUSE_BUTTON_DOUBLE_CLICKED = :mouse_button_double_clicked
      MOUSE_MOVED = :mouse_moved
      MOUSE_WHEEL_SCROLLED = :mouse_wheel_scrolled
      TOUCH_START = :touch_start
      TOUCH_MOVED = :touch_moved
      TOUCH_END = :touch_end
      TOUCH_CANCELLED = :touch_cancelled
      CONTROLLER_CONNECTED = :controller_connected
      CONTROLLER_DISCONNECTED = :controller_disconnected
      CONTROLLER_BUTTON_DOWN = :controller_button_down
      CONTROLLER_BUTTON_UP = :controller_button_up
      CONTROLLER_AXIS_MOVED = :controller_axis_moved
      ACTION_TRIGGERED = :action_triggered
      ACTION_RELEASED = :action_released
      INPUT_ENABLED = :input_enabled
      INPUT_DISABLED = :input_disabled
      INPUT_MAPPINGS_LOADED = :input_mappings_loaded
      INPUT_MAPPINGS_SAVED = :input_mappings_saved
      INPUT_MAPPINGS_RESET = :input_mappings_reset
    end

    # Input event listener interface
    module InputEventListener
      def on_input_event(event_type, event_data)
        case event_type
        when InputEventTypes::KEY_DOWN
          handle_key_down(event_data)
        when InputEventTypes::KEY_UP
          handle_key_up(event_data)
        when InputEventTypes::ACTION_TRIGGERED
          handle_action_triggered(event_data)
        when InputEventTypes::ACTION_RELEASED
          handle_action_released(event_data)
        end
      end

      def handle_key_down(event_data)
        # Override in implementing classes
      end

      def handle_key_up(event_data)
        # Override in implementing classes
      end

      def handle_action_triggered(event_data)
        # Override in implementing classes
      end

      def handle_action_released(event_data)
        # Override in implementing classes
      end
    end
  end
end
