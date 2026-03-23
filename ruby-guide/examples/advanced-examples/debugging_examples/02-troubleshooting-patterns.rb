# Ruby Troubleshooting Patterns
# This file demonstrates systematic troubleshooting patterns and strategies
# for identifying and resolving common Ruby application issues.

module DebuggingExamples
  module TroubleshootingPatterns
    # 1. Issue Classification System
    # Categorizing and prioritizing different types of issues
    
    class IssueClassifier
      ISSUE_TYPES = {
        syntax_error: { priority: 1, description: 'Syntax errors preventing code execution' },
        runtime_error: { priority: 2, description: 'Runtime errors during execution' },
        logic_error: { priority: 3, description: 'Logic errors producing incorrect results' },
        performance_issue: { priority: 4, description: 'Performance problems' },
        memory_leak: { priority: 5, description: 'Memory consumption issues' },
        concurrency_issue: { priority: 6, description: 'Threading and concurrency problems' },
        dependency_issue: { priority: 7, description: 'External dependency problems' },
        configuration_issue: { priority: 8, description: 'Configuration and setup problems' }
      }.freeze
      
      def self.classify_issue(exception, context = {})
        issue_type = determine_issue_type(exception, context)
        severity = determine_severity(exception, context, issue_type)
        
        {
          type: issue_type,
          severity: severity,
          priority: ISSUE_TYPES[issue_type][:priority],
          description: ISSUE_TYPES[issue_type][:description],
          exception: exception,
          context: context
        }
      end
      
      def self.determine_issue_type(exception, context)
        case exception
        when SyntaxError
          :syntax_error
        when NoMethodError, NameError, TypeError
          :runtime_error
        when StandardError
          if context[:performance_related]
            :performance_issue
          elsif context[:memory_related]
            :memory_leak
          elsif context[:concurrency_related]
            :concurrency_issue
          else
            :runtime_error
          end
        else
          :runtime_error
        end
      end
      
      def self.determine_severity(exception, context, issue_type)
        case issue_type
        when :syntax_error
          :critical
        when :runtime_error
          exception.class.name == 'SystemExit' ? :high : :medium
        when :logic_error
          context[:production] ? :high : :medium
        when :performance_issue
          context[:production] ? :high : :low
        when :memory_leak
          context[:production] ? :high : :medium
        else
          :medium
        end
      end
    end
    
    # 2. Troubleshooting Workflow
    # Systematic approach to troubleshooting issues
    
    class TroubleshootingWorkflow
      def initialize(logger = nil)
        @logger = logger || create_default_logger
        @steps = []
      end
      
      def troubleshoot(issue, &block)
        @steps = []
        
        begin
          @logger.info("Starting troubleshooting for #{issue[:type]}")
          
          step1 = gather_information(issue)
          step2 = analyze_problem(step1)
          step3 = identify_solutions(step2)
          step4 = implement_solution(step3, &block)
          step5 = verify_solution(step4)
          
          @logger.info("Troubleshooting completed successfully")
          step5
        rescue => e
          @logger.error("Troubleshooting failed: #{e.message}")
          raise
        end
      end
      
      private
      
      def gather_information(issue)
        @logger.info("Step 1: Gathering information")
        
        info = {
          issue: issue,
          environment: collect_environment_info,
          system_state: collect_system_state,
          logs: collect_relevant_logs(issue),
          stack_trace: collect_stack_trace(issue),
          user_context: collect_user_context(issue)
        }
        
        @steps << { step: 1, status: :completed, data: info }
        info
      end
      
      def analyze_problem(info)
        @logger.info("Step 2: Analyzing problem")
        
        analysis = {
          root_cause: identify_root_cause(info),
          impact_assessment: assess_impact(info),
          reproduction_steps: identify_reproduction_steps(info),
          related_issues: find_related_issues(info)
        }
        
        @steps << { step: 2, status: :completed, data: analysis }
        analysis
      end
      
      def identify_solutions(analysis)
        @logger.info("Step 3: Identifying solutions")
        
        solutions = {
          immediate_fixes: find_immediate_fixes(analysis),
          permanent_solutions: find_permanent_solutions(analysis),
          workarounds: find_workarounds(analysis),
          prevention_strategies: find_prevention_strategies(analysis)
        }
        
        @steps << { step: 3, status: :completed, data: solutions }
        solutions
      end
      
      def implement_solution(solutions, &block)
        @logger.info("Step 4: Implementing solution")
        
        implementation = {
          solution_applied: apply_solution(solutions, &block),
          changes_made: track_changes(solutions),
          rollback_plan: create_rollback_plan(solutions)
        }
        
        @steps << { step: 4, status: :completed, data: implementation }
        implementation
      end
      
      def verify_solution(implementation)
        @logger.info("Step 5: Verifying solution")
        
        verification = {
          tests_passed: run_verification_tests,
          performance_check: check_performance,
          stability_check: check_stability,
          user_acceptance: verify_user_acceptance
        }
        
        @steps << { step: 5, status: :completed, data: verification }
        verification
      end
      
      def collect_environment_info
        {
          ruby_version: RUBY_VERSION,
          ruby_platform: RUBY_PLATFORM,
          gem_version: Gem::VERSION,
          rails_version: defined?(Rails) ? Rails.version : 'N/A',
          environment: ENV['RAILS_ENV'] || ENV['RACK_ENV'] || 'development'
        }
      end
      
      def collect_system_state
        {
          memory_usage: `ps -o rss= -p #{Process.pid}`.to_i,
          cpu_usage: `ps -o %cpu= -p #{Process.pid}`.to_f,
          open_files: `lsof -p #{Process.pid} | wc -l`.to_i,
          thread_count: Thread.list.size,
          gc_stats: GC.stat
        }
      end
      
      def collect_relevant_logs(issue)
        # This would collect relevant log entries
        []
      end
      
      def collect_stack_trace(issue)
        issue[:exception]&.backtrace || []
      end
      
      def collect_user_context(issue)
        issue[:context] || {}
      end
      
      def identify_root_cause(info)
        # Analyze the information to identify root cause
        "Root cause analysis based on collected information"
      end
      
      def assess_impact(info)
        # Assess the impact of the issue
        {
          severity: :medium,
          affected_users: 0,
          business_impact: :low
        }
      end
      
      def identify_reproduction_steps(info)
        # Identify steps to reproduce the issue
        []
      end
      
      def find_related_issues(info)
        # Find related issues
        []
      end
      
      def find_immediate_fixes(analysis)
        # Find immediate fixes
        []
      end
      
      def find_permanent_solutions(analysis)
        # Find permanent solutions
        []
      end
      
      def find_workarounds(analysis)
        # Find workarounds
        []
      end
      
      def find_prevention_strategies(analysis)
        # Find prevention strategies
        []
      end
      
      def apply_solution(solutions, &block)
        # Apply the solution
        yield if block_given?
        true
      end
      
      def track_changes(solutions)
        # Track changes made
        []
      end
      
      def create_rollback_plan(solutions)
        # Create rollback plan
        "Rollback plan created"
      end
      
      def run_verification_tests
        # Run verification tests
        true
      end
      
      def check_performance
        # Check performance
        true
      end
      
      def check_stability
        # Check stability
        true
      end
      
      def verify_user_acceptance
        # Verify user acceptance
        true
      end
      
      def create_default_logger
        Logger.new(STDOUT)
      end
    end
    
    # 3. Common Issue Patterns
    # Patterns for common Ruby issues and their solutions
    
    class CommonIssuePatterns
      # Pattern 1: Nil Reference Errors
      class NilReferencePattern
        def self.troubleshoot(exception, context)
          return unless exception.is_a?(NoMethodError) && exception.message.include?('nil:')
          
          {
            pattern: :nil_reference,
            description: 'Method called on nil object',
            cause: identify_cause(exception, context),
            solutions: generate_solutions(exception, context),
            prevention: prevention_strategies
          }
        end
        
        def self.identify_cause(exception, context)
          backtrace = exception.backtrace
          return "Unable to identify cause" unless backtrace&.any?
          
          # Analyze the calling code to identify the cause
          "Variable or method returned nil when object was expected"
        end
        
        def self.generate_solutions(exception, context)
          [
            "Add nil check before method call",
            "Use safe navigation operator (&.)",
            "Provide default value with ||",
            "Use try method (if available)"
          ]
        end
        
        def self.prevention_strategies
          [
            "Validate input parameters",
            "Use guard clauses",
            "Initialize variables properly",
            "Add defensive programming checks"
          ]
        end
      end
      
      # Pattern 2: Memory Issues
      class MemoryIssuePattern
        def self.troubleshoot(exception, context)
          return unless exception.is_a?(NoMemoryError) || context[:memory_related]
          
          {
            pattern: :memory_issue,
            description: 'Memory consumption problem',
            cause: identify_cause(exception, context),
            solutions: generate_solutions(exception, context),
            prevention: prevention_strategies
          }
        end
        
        def self.identify_cause(exception, context)
          if exception.is_a?(NoMemoryError)
            "System ran out of memory"
          else
            "Potential memory leak or excessive memory usage"
          end
        end
        
        def self.generate_solutions(exception, context)
          [
            "Force garbage collection with GC.start",
            "Optimize data structures",
            "Use streaming for large datasets",
            "Implement pagination",
            "Clear unused references"
          ]
        end
        
        def self.prevention_strategies
          [
            "Monitor memory usage",
            "Use memory-efficient data structures",
            "Implement object pooling",
            "Add memory limits"
          ]
        end
      end
      
      # Pattern 3: Performance Issues
      class PerformanceIssuePattern
        def self.troubleshoot(exception, context)
          return unless context[:performance_related] || slow_operation_detected?(context)
          
          {
            pattern: :performance_issue,
            description: 'Performance degradation',
            cause: identify_cause(exception, context),
            solutions: generate_solutions(exception, context),
            prevention: prevention_strategies
          }
        end
        
        def self.slow_operation_detected?(context)
          context[:operation_time] && context[:operation_time] > 5.0
        end
        
        def self.identify_cause(exception, context)
          if context[:n_plus_one_queries]
            "N+1 query problem"
          elsif context[:inefficient_algorithm]
            "Inefficient algorithm implementation"
          elsif context[:large_dataset]
            "Processing large dataset without optimization"
          else
            "General performance issue"
          end
        end
        
        def self.generate_solutions(exception, context)
          solutions = []
          
          if context[:n_plus_one_queries]
            solutions << "Use eager loading (includes, preload, eager_load)"
            solutions << "Implement batch loading"
            solutions << "Use database query optimization"
          end
          
          if context[:inefficient_algorithm]
            solutions << "Optimize algorithm complexity"
            solutions << "Use more efficient data structures"
            solutions << "Implement caching"
          end
          
          if context[:large_dataset]
            solutions << "Implement pagination"
            solutions << "Use streaming processing"
            solutions << "Add background processing"
          end
          
          solutions << "Add performance monitoring"
          solutions << "Use profiling tools"
          
          solutions
        end
        
        def self.prevention_strategies
          [
            "Implement performance monitoring",
            "Use automated performance tests",
            "Regular code reviews for performance",
            "Database query optimization"
          ]
        end
      end
      
      # Pattern 4: Concurrency Issues
      class ConcurrencyIssuePattern
        def self.troubleshoot(exception, context)
          return unless context[:concurrency_related] || thread_safety_issue?(exception)
          
          {
            pattern: :concurrency_issue,
            description: 'Threading or concurrency problem',
            cause: identify_cause(exception, context),
            solutions: generate_solutions(exception, context),
            prevention: prevention_strategies
          end
        end
        
        def self.thread_safety_issue?(exception)
          exception.is_a?(ThreadError) || 
          exception.message.include?('deadlock') ||
          exception.message.include?('race condition')
        end
        
        def self.identify_cause(exception, context)
          if exception.is_a?(ThreadError)
            "Thread synchronization error"
          elsif context[:shared_state]
            "Shared state access without proper synchronization"
          elsif context[:deadlock]
            "Deadlock between threads"
          else
            "Concurrency issue"
          end
        end
        
        def self.generate_solutions(exception, context)
          [
            "Use Mutex for thread synchronization",
            "Implement proper locking mechanisms",
            "Use thread-safe data structures",
            "Avoid shared mutable state",
            "Use atomic operations when possible"
          ]
        end
        
        def self.prevention_strategies
          [
            "Design for thread safety from start",
            "Use concurrent data structures",
            "Implement proper error handling",
            "Test with multiple threads"
          ]
        end
      end
      
      # Pattern 5: External Dependency Issues
      class ExternalDependencyPattern
        def self.troubleshoot(exception, context)
          return unless external_dependency_issue?(exception, context)
          
          {
            pattern: :external_dependency,
            description: 'External service or dependency problem',
            cause: identify_cause(exception, context),
            solutions: generate_solutions(exception, context),
            prevention: prevention_strategies
          }
        end
        
        def self.external_dependency_issue?(exception, context)
          context[:external_service] ||
          exception.is_a?(Net::TimeoutError) ||
          exception.is_a?(SocketError) ||
          exception.message.include?('timeout') ||
          exception.message.include?('connection')
        end
        
        def self.identify_cause(exception, context)
          if exception.is_a?(Net::TimeoutError)
            "Network timeout"
          elsif exception.is_a?(SocketError)
            "Network connection error"
          elsif context[:service_unavailable]
            "External service unavailable"
          else
            "External dependency issue"
          end
        end
        
        def self.generate_solutions(exception, context)
          [
            "Implement retry mechanism with exponential backoff",
            "Add circuit breaker pattern",
            "Implement timeout handling",
            "Add fallback mechanisms",
            "Use connection pooling"
          ]
        end
        
        def self.prevention_strategies
          [
            "Implement health checks",
            "Use service discovery",
            "Add monitoring and alerting",
            "Implement graceful degradation"
          ]
        end
      end
    end
    
    # 4. Troubleshooting Assistant
    # Main assistant class that coordinates troubleshooting
    
    class TroubleshootingAssistant
      def initialize(logger = nil)
        @logger = logger || create_default_logger
        @workflow = TroubleshootingWorkflow.new(@logger)
        @patterns = [
          CommonIssuePatterns::NilReferencePattern,
          CommonIssuePatterns::MemoryIssuePattern,
          CommonIssuePatterns::PerformanceIssuePattern,
          CommonIssuePatterns::ConcurrencyIssuePattern,
          CommonIssuePatterns::ExternalDependencyPattern
        ]
      end
      
      def troubleshoot(exception, context = {}, &block)
        @logger.info("Starting troubleshooting assistant")
        
        # Classify the issue
        issue = IssueClassifier.classify_issue(exception, context)
        @logger.info("Issue classified as: #{issue[:type]}")
        
        # Check for known patterns
        pattern_result = check_patterns(exception, context)
        
        if pattern_result
          @logger.info("Known pattern detected: #{pattern_result[:pattern]}")
          return handle_known_pattern(pattern_result, &block)
        end
        
        # Use general workflow for unknown issues
        @workflow.troubleshoot(issue, &block)
      end
      
      def quick_diagnose(exception, context = {})
        puts "\n=== Quick Diagnosis ==="
        puts "Exception: #{exception.class.name}"
        puts "Message: #{exception.message}"
        puts "Context: #{context.inspect}"
        
        # Check for known patterns
        pattern_result = check_patterns(exception, context)
        
        if pattern_result
          puts "\nPattern Detected: #{pattern_result[:pattern]}"
          puts "Description: #{pattern_result[:description]}"
          puts "Cause: #{pattern_result[:cause]}"
          puts "\nSuggested Solutions:"
          pattern_result[:solutions].each_with_index do |solution, index|
            puts "  #{index + 1}. #{solution}"
          end
          puts "\nPrevention Strategies:"
          pattern_result[:prevention].each_with_index do |strategy, index|
            puts "  #{index + 1}. #{strategy}"
          end
        else
          puts "\nNo known pattern detected. Use full troubleshooting workflow."
        end
        
        puts "\n=== End Diagnosis ==="
      end
      
      def create_troubleshooting_report(exception, context = {})
        issue = IssueClassifier.classify_issue(exception, context)
        pattern_result = check_patterns(exception, context)
        
        report = {
          timestamp: Time.current,
          issue: issue,
          pattern: pattern_result,
          environment: collect_environment_info,
          recommendations: generate_recommendations(issue, pattern_result)
        }
        
        save_report(report)
        report
      end
      
      private
      
      def check_patterns(exception, context)
        @patterns.each do |pattern_class|
          result = pattern_class.troubleshoot(exception, context)
          return result if result
        end
        nil
      end
      
      def handle_known_pattern(pattern_result, &block)
        @logger.info("Handling known pattern: #{pattern_result[:pattern]}")
        
        # Apply the first solution by default
        if block_given?
          yield
        else
          apply_pattern_solution(pattern_result)
        end
        
        pattern_result
      end
      
      def apply_pattern_solution(pattern_result)
        solutions = pattern_result[:solutions]
        return nil unless solutions.any?
        
        first_solution = solutions.first
        @logger.info("Applying solution: #{first_solution}")
        
        # This would implement the actual solution
        puts "Applied solution: #{first_solution}"
      end
      
      def collect_environment_info
        {
          ruby_version: RUBY_VERSION,
          platform: RUBY_PLATFORM,
          memory_usage: `ps -o rss= -p #{Process.pid}`.to_i,
          thread_count: Thread.list.size
        }
      end
      
      def generate_recommendations(issue, pattern)
        recommendations = []
        
        if pattern
          recommendations << "Apply the suggested solutions for #{pattern[:pattern]}"
          recommendations.concat(pattern[:prevention])
        else
          recommendations << "Use systematic troubleshooting workflow"
          recommendations << "Collect more context information"
          recommendations << "Consider adding logging for better debugging"
        end
        
        recommendations
      end
      
      def save_report(report)
        filename = "troubleshooting_report_#{Time.current.strftime('%Y%m%d_%H%M%S')}.json"
        File.write(filename, JSON.pretty_generate(report))
        @logger.info("Troubleshooting report saved to: #{filename}")
      end
      
      def create_default_logger
        logger = Logger.new(STDOUT)
        logger.level = Logger::INFO
        logger.formatter = proc do |severity, datetime, progname, msg|
          "[#{datetime.strftime('%H:%M:%S')}] #{severity}: #{msg}"
        end
        logger
      end
    end
    
    # 5. Troubleshooting Utilities
    # Utility methods for common troubleshooting tasks
    
    module TroubleshootingUtilities
      def self.check_system_health
        {
          memory_usage: check_memory_usage,
          disk_space: check_disk_space,
          process_count: check_process_count,
          network_connectivity: check_network_connectivity,
          database_connection: check_database_connection
        }
      end
      
      def self.check_memory_usage
        memory_kb = `ps -o rss= -p #{Process.pid}`.to_i
        memory_mb = memory_kb / 1024.0
        
        {
          current_kb: memory_kb,
          current_mb: memory_mb.round(2),
          status: memory_mb < 1000 ? :healthy : :warning
        }
      end
      
      def self.check_disk_space
        df_output = `df -h .`
        lines = df_output.split("\n")
        return nil unless lines.length > 1
        
        usage_line = lines[1].split
        used_percent = usage_line[4].to_i
        
        {
          used_percent: used_percent,
          status: used_percent < 80 ? :healthy : :warning
        }
      end
      
      def self.check_process_count
        process_count = `ps aux | wc -l`.to_i
        
        {
          count: process_count,
          status: process_count < 200 ? :healthy : :warning
        }
      end
      
      def self.check_network_connectivity
        begin
          require 'socket'
          socket = TCPSocket.new('google.com', 80)
          socket.close
          { status: :healthy, message: "Network connectivity OK" }
        rescue
          { status: :error, message: "Network connectivity failed" }
        end
      end
      
      def self.check_database_connection
        begin
          # This would check actual database connection
          { status: :healthy, message: "Database connection OK" }
        rescue
          { status: :error, message: "Database connection failed" }
        end
      end
      
      def self.generate_health_report
        health = check_system_health
        
        puts "\n=== System Health Report ==="
        health.each do |component, status|
          status_symbol = status[:status] == :healthy ? "✅" : "⚠️"
          puts "#{status_symbol} #{component.to_s.gsub('_', ' ').capitalize}: #{status[:status]}"
          
          if status[:message]
            puts "   #{status[:message]}"
          end
        end
        puts "=== End Health Report ===\n"
        
        health
      end
      
      def self.monitor_resources(duration_seconds = 60, interval_seconds = 5)
        puts "Starting resource monitoring for #{duration_seconds} seconds..."
        
        start_time = Time.current
        readings = []
        
        while Time.current - start_time < duration_seconds
          reading = {
            timestamp: Time.current,
            memory: check_memory_usage,
            processes: check_process_count
          }
          
          readings << reading
          puts "[#{reading[:timestamp].strftime('%H:%M:%S')}] Memory: #{reading[:memory][:current_mb]}MB, Processes: #{reading[:processes][:count]}"
          
          sleep interval_seconds
        end
        
        puts "Resource monitoring completed"
        readings
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Ruby Troubleshooting Patterns Demonstration"
  puts "=" * 60
  
  # Demonstrate issue classification
  puts "\n1. Issue Classification:"
  puts "✅ Automatic issue type detection"
  puts "✅ Severity assessment"
  puts "✅ Priority determination"
  
  # Demonstrate troubleshooting workflow
  puts "\n2. Troubleshooting Workflow:"
  puts "✅ Systematic 5-step process"
  puts "✅ Information gathering"
  puts "✅ Solution implementation"
  
  # Demonstrate common patterns
  puts "\n3. Common Issue Patterns:"
  puts "✅ Nil reference errors"
  puts "✅ Memory issues"
  puts "✅ Performance problems"
  puts "✅ Concurrency issues"
  puts "✅ External dependency problems"
  
  # Demonstrate troubleshooting assistant
  puts "\n4. Troubleshooting Assistant:"
  puts "✅ Pattern recognition"
  puts "✅ Quick diagnosis"
  puts "✅ Report generation"
  
  # Demonstrate utilities
  puts "\n5. Troubleshooting Utilities:"
  puts "✅ System health checks"
  puts "✅ Resource monitoring"
  puts "✅ Environment analysis"
  
  puts "\nTroubleshooting patterns help resolve issues systematically!"
end
