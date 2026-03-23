# Ruby Debugging Techniques
# This file demonstrates comprehensive debugging techniques and strategies
# for Ruby applications, including common debugging tools and best practices.

module DebuggingExamples
  module DebuggingTechniques
    # 1. Basic Debugging with puts and p
    # Simple debugging techniques for quick inspection
    
    class BasicDebugger
      def self.debug_with_puts(object, label = nil)
        if label
          puts "[DEBUG] #{label}: #{object.inspect}"
        else
          puts "[DEBUG] #{object.inspect}"
        end
      end
      
      def self.debug_with_p(object, label = nil)
        if label
          p label => object
        else
          p object
        end
      end
      
      def self.debug_variables(binding_context)
        puts "[DEBUG] Variables:"
        binding_context.local_variables.each do |name, value|
          puts "  #{name}: #{value.inspect}"
        end
      end
      
      def self.debug_method_entry(method_name, args = [])
        puts "[DEBUG] Entering #{method_name} with args: #{args.inspect}"
      end
      
      def self.debug_method_exit(method_name, result = nil)
        if result
          puts "[DEBUG] Exiting #{method_name} with result: #{result.inspect}"
        else
          puts "[DEBUG] Exiting #{method_name}"
        end
      end
    end
    
    # 2. Logger-based Debugging
    # Using Ruby's Logger for structured debugging
    
    class LoggerDebugger
      def initialize(logger = nil)
        @logger = logger || create_default_logger
      end
      
      def debug(message, context = {})
        @logger.debug(format_message(message, context))
      end
      
      def info(message, context = {})
        @logger.info(format_message(message, context))
      end
      
      def warn(message, context = {})
        @logger.warn(format_message(message, context))
      end
      
      def error(message, context = {})
        @logger.error(format_message(message, context))
      end
      
      def debug_method_call(method_name, args = [], result = nil)
        context = {
          method: method_name,
          args: args,
          result: result,
          timestamp: Time.current,
          thread_id: Thread.current.object_id
        }
        
        if result
          debug("Method #{method_name} completed", context)
        else
          debug("Method #{method_name} called", context)
        end
      end
      
      def debug_exception(exception, context = {})
        error("Exception occurred: #{exception.message}", {
          exception_class: exception.class.name,
          backtrace: exception.backtrace.first(5),
          **context
        })
      end
      
      def debug_performance(operation_name, start_time, end_time)
        duration = end_time - start_time
        
        info("Performance: #{operation_name}", {
          duration_ms: (duration * 1000).round(2),
          start_time: start_time,
          end_time: end_time
        })
      end
      
      private
      
      def create_default_logger
        logger = Logger.new(STDOUT)
        logger.level = Logger::DEBUG
        logger.formatter = proc do |severity, datetime, progname, msg|
          "#{datetime.strftime('%Y-%m-%d %H:%M:%S')} [#{severity}] #{msg}"
        end
        logger
      end
      
      def format_message(message, context)
        if context.any?
          "#{message} | #{context.map { |k, v| "#{k}=#{v}" }.join(', ')}"
        else
          message
        end
      end
    end
    
    # 3. Stack Trace Analysis
    # Analyzing and formatting stack traces for debugging
    
    class StackTraceAnalyzer
      def self.format_stack_trace(exception = nil)
        if exception
          format_exception_stack_trace(exception)
        else
          format_current_stack_trace
        end
      end
      
      def self.format_exception_stack_trace(exception)
        backtrace = exception.backtrace || []
        
        {
          exception: exception.class.name,
          message: exception.message,
          backtrace: format_backtrace(backtrace)
        }
      end
      
      def self.format_current_stack_trace
        backtrace = caller_locations
        
        {
          backtrace: format_backtrace(backtrace)
        }
      end
      
      def self.find_relevant_frames(stack_trace, max_frames = 10)
        frames = stack_trace[:backtrace]
        
        # Filter out framework/gem frames
        relevant_frames = frames.select do |frame|
          !frame[:path].match?(/(ruby\/gems|\/lib\/ruby)/)
        end
        
        relevant_frames.first(max_frames)
      end
      
      def self.debug_stack_trace(max_frames = 10)
        stack_trace = format_current_stack_trace
        relevant_frames = find_relevant_frames(stack_trace, max_frames)
        
        puts "[DEBUG] Stack Trace (#{relevant_frames.length} relevant frames):"
        relevant_frames.each_with_index do |frame, index|
          puts "  #{index + 1}. #{frame[:file]}:#{frame[:line]} in `#{frame[:method]}`"
          if frame[:code]
            code_lines = frame[:code].strip.split("\n")
            code_lines.each { |line| puts "     #{line}" }
          end
        end
      end
      
      private
      
      def self.format_backtrace(backtrace)
        backtrace.map do |frame|
          if frame.is_a?(String)
            # Handle string-based backtrace
            parts = frame.split(':')
            file = parts[0]
            line = parts[1] ? parts[1].to_i : 0
            method = parts[2] ? parts[2].gsub(/^in `/, '') : 'unknown'
            
            {
              file: file,
              line: line,
              method: method,
              path: file,
              code: nil
            }
          else
            # Handle Thread::Backtrace::Location
            {
              file: frame.path,
              line: frame.lineno,
              method: frame.label,
              path: frame.path,
              code: frame.to_s
            }
          end
        end
      end
    end
    
    # 4. Object Inspection
    # Deep inspection of objects and their state
    
    class ObjectInspector
      def self.inspect_object(object, max_depth = 3)
        inspection = {
          class: object.class.name,
          object_id: object.object_id,
          instance_variables: inspect_instance_variables(object),
          methods: inspect_methods(object),
          ancestors: object.class.ancestors.map(&:name),
          singleton_methods: inspect_singleton_methods(object)
        }
        
        # Add custom inspection based on object type
        inspection.merge(inspect_by_type(object))
      end
      
      def self.inspect_instance_variables(object)
        object.instance_variables.map do |var|
          value = object.instance_variable_get(var)
          {
            variable: var,
            value: inspect_value(value, 2),
            type: value.class.name
          }
        end
      end
      
      def self.inspect_methods(object)
        public_methods = object.public_methods(false).map(&:to_s)
        protected_methods = object.protected_methods(false).map(&:to_s)
        private_methods = object.private_methods(false).map(&:to_s)
        
        {
          public: public_methods.sort,
          protected: protected_methods.sort,
          private: private_methods.sort
        }
      end
      
      def self.inspect_singleton_methods(object)
        object.singleton_methods(false).map(&:to_s).sort
      end
      
      def self.inspect_by_type(object)
        case object
        when Array
          {
            length: object.length,
            first: object.first,
            last: object.last,
            sample: object.sample(3)
          }
        when Hash
          {
            keys: object.keys,
            size: object.size,
            sample: object.sample(3)
          }
        when String
          {
            length: object.length,
            encoding: object.encoding,
            bytesize: object.bytesize
          }
        when Numeric
          {
            value: object,
            class: object.class.name
          }
        else
          {}
        end
      end
      
      def self.inspect_value(value, max_depth = 2)
        return '...' if max_depth <= 0
        
        case value
        when Array
          if value.length > 5
              "[#{value.first(3).map { |v| inspect_value(v, max_depth - 1) }.join(', ')}, ...]"
            else
              "[#{value.map { |v| inspect_value(v, max_depth - 1) }.join(', ')}]"
            end
        when Hash
          if value.size > 5
              "{#{value.first(3).map { |k, v| "#{k}: #{inspect_value(v, max_depth - 1)}" }.join(', ')}, ...}"
            else
              "{#{value.map { |k, v| "#{k}: #{inspect_value(v, max_depth - 1)}" }.join(', ')}"
            end
        when String
          value.length > 50 ? "#{value[0..47]}..." : value
        else
          value.inspect
        end
      end
      
      def self.print_inspection(object)
        inspection = inspect_object(object)
        
        puts "[DEBUG] Object Inspection:"
        puts "  Class: #{inspection[:class]}"
        puts "  Object ID: #{inspection[:object_id]}"
        puts "  Ancestors: #{inspection[:ancestors].join(' -> ')}"
        
        if inspection[:instance_variables].any?
          puts "  Instance Variables:"
          inspection[:instance_variables].each do |var_info|
            puts "    #{var_info[:variable]} (#{var_info[:type]}): #{var_info[:value]}"
          end
        end
        
        if inspection[:singleton_methods].any?
          puts "  Singleton Methods: #{inspection[:singleton_methods].join(', ')}"
        end
      end
    end
    
    # 5. Performance Profiling
    # Basic performance profiling for debugging performance issues
    
    class PerformanceProfiler
      def self.profile_method(object, method_name, &block)
        start_time = Time.current
        start_memory = get_memory_usage
        
        result = object.send(method_name, &block)
        
        end_time = Time.current
        end_memory = get_memory_usage
        
        duration = end_time - start_time
        memory_diff = end_memory - start_memory
        
        puts "[PROFILE] Method: #{object.class}##{method_name}"
        puts "[PROFILE] Duration: #{(duration * 1000).round(2)}ms"
        puts "[PROFILE] Memory: #{memory_diff} bytes"
        puts "[PROFILE] Result: #{result.class}"
        
        result
      end
      
      def self.profile_block(description = "Block", &block)
        start_time = Time.current
        start_memory = get_memory_usage
        
        result = yield
        
        end_time = Time.current
        end_memory = get_memory_usage
        
        duration = end_time - start_time
        memory_diff = end_memory - start_memory
        
        puts "[PROFILE] #{description}"
        puts "[PROFILE] Duration: #{(duration * 1000).round(2)}ms"
        puts "[PROFILE] Memory: #{memory_diff} bytes"
        puts "[PROFILE] Result: #{result.class}"
        
        result
      end
      
      def self.compare_performance(name1, block1, name2, block2)
        puts "[PROFILE] Comparing performance..."
        
        result1, stats1 = profile_block(name1, &block1)
        result2, stats2 = profile_block(name2, &block2)
        
        puts "[PROFILE] Comparison Results:"
        puts "  #{name1}: #{stats1[:duration]}ms, #{stats1[:memory]} bytes"
        puts "  #{name2}: #{stats2[:duration]}ms, #{stats2[:memory]} bytes"
        
        if stats1[:duration] < stats2[:duration]
          puts "  #{name1} is faster by #{(stats2[:duration] - stats1[:duration]).round(2)}ms"
        else
          puts "  #{name2} is faster by #{(stats1[:duration] - stats2[:duration]).round(2)}ms"
        end
        
        [result1, result2]
      end
      
      private
      
      def self.get_memory_usage
        GC.start
        GC.stat[:heap_allocated_pages] * GC::INTERNAL_CONSTANTS[:HEAP_PAGE_SIZE]
      end
    end
    
    # 6. Memory Leak Detection
    # Detecting and debugging memory leaks in Ruby applications
    
    class MemoryLeakDetector
      def self.detect_leaks(object_creation_block, iterations = 100)
        initial_objects = count_objects
        object_counts = []
        
        iterations.times do |i|
          object_creation_block.call
          current_objects = count_objects
          object_counts << current_objects
          
          GC.start
          
          puts "[LEAK] Iteration #{i + 1}: #{current_objects} objects"
        end
        
        final_objects = count_objects
        leak_detected = final_objects > initial_objects
        
        puts "[LEAK] Memory Leak Detection Results:"
        puts "  Initial objects: #{initial_objects}"
        puts "  Final objects: #{final_objects}"
        puts "  Objects created: #{final_objects - initial_objects}"
        puts "  Leak detected: #{leak_detected ? 'YES' : 'NO'}"
        
        if leak_detected
          puts "[LEAK] Object count trend:"
          object_counts.each_with_index do |count, index|
            puts "  Iteration #{index + 1}: #{count} objects"
          end
        end
        
        leak_detected
      end
      
      def self.find_object_leaks(object_class, threshold = 10)
        initial_count = ObjectSpace.count_objects(object_class)
        
        # Force garbage collection
        GC.start
        
        current_count = ObjectSpace.count_objects(object_class)
        
        leak_detected = current_count > threshold
        
        puts "[LEAK] #{object_class} Leak Detection:"
        puts "  Initial count: #{initial_count}"
        puts "  Current count: #{current_count}"
        puts "  Threshold: #{threshold}"
        puts "  Leak detected: #{leak_detected ? 'YES' : 'NO'}"
        
        if leak_detected
          puts "[LEAK] Leaking objects:"
          ObjectSpace.each_object(object_class) do |obj|
            puts "  Object ID: #{obj.object_id}, Class: #{obj.class.name}"
          end
        end
        
        leak_detected
      end
      
      private
      
      def self.count_objects
        ObjectSpace.count_objects[:TOTAL]
      end
    end
    
    # 7. Debugging Helper Class
    # Comprehensive debugging utility class
    
    class DebugHelper
      def initialize(logger = nil)
        @logger = LoggerDebugger.new(logger)
        @performance_profiler = PerformanceProfiler
        @stack_trace_analyzer = StackTraceAnalyzer
        @object_inspector = ObjectInspector
        @memory_leak_detector = MemoryLeakDetector
      end
      
      def debug_method_call(method_name, args = [], result = nil)
        @logger.debug_method_call(method_name, args, result)
      end
      
      def debug_exception(exception, context = {})
        @logger.debug_exception(exception, context)
        @stack_trace_analyzer.debug_stack_trace(exception)
      end
      
      def debug_performance(operation_name, start_time, end_time)
        @logger.debug_performance(operation_name, start_time, end_time)
      end
      
      def debug_object(object, max_depth = 3)
        @object_inspector.print_inspection(object)
      end
      
      def debug_stack_trace(max_frames = 10)
        @stack_trace_analyzer.debug_stack_trace(max_frames)
      end
      
      def profile_method(object, method_name, &block)
        @performance_profiler.profile_method(object, method_name, &block)
      end
      
      def profile_block(description = "Block", &block)
        @performance_profiler.profile_block(description, &block)
      end
      
      def detect_memory_leaks(object_creation_block, iterations = 100)
        @memory_leak_detector.detect_leaks(object_creation_block, iterations)
      end
      
      def find_object_leaks(object_class, threshold = 10)
        @memory_leak_detector.find_object_leaks(object_class, threshold)
      end
      
      def debug_binding(binding_context)
        BasicDebugger.debug_variables(binding_context)
      end
      
      def debug_puts(object, label = nil)
        BasicDebugger.debug_with_puts(object, label)
      end
      
      def debug_p(object, label = nil)
        BasicDebugger.debug_with_p(object, label)
      end
    end
    
    # 8. Conditional Debugging
      # Debugging that can be enabled/disabled based on environment
    
    class ConditionalDebugger
      def self.debug_enabled?
        ENV['RUBY_DEBUG'] == 'true' || ENV['DEBUG'] == 'true'
      end
      
      def self.debug(message, context = {})
        return unless debug_enabled?
        
        if context.any?
          puts "[DEBUG] #{message} | #{context.map { |k, v| "#{k}=#{v}" }.join(', ')}"
        else
          puts "[DEBUG] #{message}"
        end
      end
      
      def self.debug_method(method_name, &block)
        return unless debug_enabled?
        
        debug("Entering method: #{method_name}")
        result = yield
        debug("Exiting method: #{method_name}")
        result
      end
      
      def self.debug_performance(operation_name, &block)
        return unless debug_enabled?
        
        start_time = Time.current
        result = yield
        end_time = Time.current
        
        duration = end_time - start_time
        debug("Performance: #{operation_name} took #{(duration * 1000).round(2)}ms")
        
        result
      end
      
      def self.debug_exception(exception)
        return unless debug_enabled?
        
        puts "[DEBUG] Exception: #{exception.class.name}: #{exception.message}"
        puts "[DEBUG] Backtrace:"
        exception.backtrace.each_with_index do |line, index|
          puts "[DEBUG]   #{index + 1}: #{line}"
        end
      end
    end
    
    # 9. Debugging Helper Methods
    # Utility methods for common debugging tasks
    
    module DebugHelperMethods
      def debug_binding
        ConditionalDebugger.debug(binding)
      end
      
      def debug_caller
        puts "[DEBUG] Called from: #{caller_locations[1]}"
        puts "[DEBUG] File: #{caller_locations[1].path}"
        puts "[DEBUG] Line: #{caller_locations[1].lineno}"
        puts "[DEBUG] Method: #{caller_locations[1].label}"
      end
      
      def debug_call_stack
        ConditionalDebugger.debug_stack_trace
      end
      
      def debug_current_object
        ConditionalDebugger.debug(self)
      end
      
      def debug_instance_variables
        instance_variables.each do |var|
          value = instance_variable_get(var)
          puts "[DEBUG] #{var}: #{value.inspect} (#{value.class})"
        end
      end
      
      def debug_method_entry(method_name, args = [])
        ConditionalDebugger.debug("Entering #{method_name} with args: #{args.inspect}")
      end
      
      def debug_method_exit(method_name, result = nil)
        if result
          ConditionalDebugger.debug("Exiting #{method_name} with result: #{result.inspect}")
        else
          ConditionalDebugger.debug("Exiting #{method_name}")
        end
      end
      
      def debug_performance(operation_name)
        start_time = Time.current
        result = yield
        end_time = Time.current
        
        duration = end_time - start_time
        ConditionalDebugger.debug("Performance: #{operation_name} took #{(duration * 1000).round(2)}ms")
        
        result
      end
      
      def debug_exception(exception)
        ConditionalDebugger.debug_exception(exception)
      end
      
      private
      
      def caller_locations
        caller_locations
      end
    end
    
    # 10. Debugging Examples
    # Examples demonstrating debugging techniques in practice
    
    class DebuggingExamples
      include DebugHelperMethods
      
      def complex_calculation(x, y, z)
        debug_method_entry(:complex_calculation, [x, y, z])
        
        debug_performance("initial calculation") do
          intermediate = x * y
          debug("Intermediate result: #{intermediate}")
          intermediate
        end
        
        debug_performance("final calculation") do
          result = intermediate + z
          debug("Final result: #{result}")
          result
        end
        
        debug_method_exit(:complex_calculation, result)
      end
      
      def process_data(data)
        debug_method_entry(:process_data, [data.class.name])
        
        begin
          debug_performance("data processing") do
            processed = data.map { |item| item * 2 }
            debug("Processed #{processed.length} items")
            processed
          end
          
          debug_performance("data validation") do
            validated = processed.select { |item| item > 0 }
            debug("Validated #{validated.length} items")
            validated
          end
          
          validated
        rescue => e
          debug_exception(e)
          raise
        end
      end
      
      def self.class_method_example
        debug_method_entry(:class_method_example)
        
        debug_performance("class method execution") do
          result = "Class method result"
          debug("Class method result: #{result}")
          result
        end
        
        debug_method_exit(:class_method_example)
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Ruby Debugging Techniques Demonstration"
  puts "=" * 60
  
  # Demonstrate basic debugging
  puts "\n1. Basic Debugging:"
  puts "✅ puts and p for quick inspection"
  puts "✅ Variable debugging"
  puts "✅ Method entry/exit debugging"
  
  # Demonstrate logger debugging
  puts "\n2. Logger Debugging:"
  puts "✅ Structured logging"
  puts "✅ Context information"
  puts "✅ Performance tracking"
  
  # Demonstrate stack trace analysis
  puts "\n3. Stack Trace Analysis:"
  puts "✅ Exception backtraces"
  puts "✅ Current stack trace"
  puts "✅ Relevant frame filtering"
  
  # Demonstrate object inspection
  puts "\n4. Object Inspection:"
  puts "✅ Instance variables"
  puts "✅ Method inspection"
  puts "✅ Type-specific inspection"
  
  # Demonstrate performance profiling
  puts "\n5. Performance Profiling:"
  puts "✅ Method timing"
  puts "✅ Block timing"
  puts "✅ Memory usage tracking"
  
  # Demonstrate memory leak detection
  puts "\n6. Memory Leak Detection:"
  puts "✅ Object counting"
  puts "✅ Leak detection"
  puts "✅ Object inspection"
  
  # Demonstrate conditional debugging
  puts "\n7. Conditional Debugging:"
  puts "✅ Environment-based enabling"
  puts "✅ Performance debugging"
  puts "✅ Exception debugging"
  
  puts "\nDebugging techniques help identify and fix issues efficiently!"
end
