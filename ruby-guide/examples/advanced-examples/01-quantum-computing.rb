# Quantum Computing Examples in Ruby
# Demonstrating quantum computing concepts and simulations

require 'matrix'
require 'json'

class QuantumComputingExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "⚛️ Quantum Computing Examples in Ruby"
    puts "===================================="
    puts "Explore quantum computing concepts and simulations!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Quantum Computing Menu:"
      puts "1. Quantum Bits (Qubits)"
      puts "2. Quantum Gates"
      puts "3. Quantum Circuits"
      puts "4. Quantum Algorithms"
      puts "5. Quantum Entanglement"
      puts "6. Quantum Teleportation"
      puts "7. Quantum Error Correction"
      puts "8. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-8): "
      choice = gets.chomp.to_i
      case choice
      when 1
        quantum_bits
      when 2
        quantum_gates
      when 3
        quantum_circuits
      when 4
        quantum_algorithms
      when 5
        quantum_entanglement
      when 6
        quantum_teleportation
      when 7
        quantum_error_correction
      when 8
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def quantum_bits
    puts "\n⚛️ Example 1: Quantum Bits (Qubits)"
    puts "=" * 55
    puts "Understanding quantum bits and superposition."
    puts ""
    
    # Qubit class
    puts "🔮 Qubit Implementation:"
    
    class Qubit
      attr_reader :alpha, :beta
      
      def initialize(alpha = 1.0, beta = 0.0)
        @alpha = alpha
        @beta = beta
        normalize!
      end
      
      def |0⟩
        Qubit.new(1.0, 0.0)
      end
      
      def |1⟩
        Qubit.new(0.0, 1.0)
      end
      
      def |+⟩
        Qubit.new(1.0 / Math.sqrt(2), 1.0 / Math.sqrt(2))
      end
      
      def |-⟩
        Qubit.new(1.0 / Math.sqrt(2), -1.0 / Math.sqrt(2))
      end
      
      def measure
        prob_zero = (@alpha ** 2).abs
        if rand < prob_zero
          0
        else
          1
        end
      end
      
      def probabilities
        {
          zero: (@alpha ** 2).abs,
          one: (@beta ** 2).abs
        }
      end
      
      def to_s
        "#{@alpha.round(3)}|0⟩ + #{@beta.round(3)}|1⟩"
      end
      
      private
      
      def normalize!
        norm = Math.sqrt((@alpha ** 2).abs + (@beta ** 2).abs)
        @alpha /= norm
        @beta /= norm
      end
    end
    
    # Qubit demonstrations
    puts "\nQubit States:"
    
    # Classical states
    zero_qubit = Qubit.new(1.0, 0.0)
    one_qubit = Qubit.new(0.0, 1.0)
    
    puts "  |0⟩ = #{zero_qubit}"
    puts "  |1⟩ = #{one_qubit}"
    
    # Superposition states
    plus_qubit = Qubit.new(1.0 / Math.sqrt(2), 1.0 / Math.sqrt(2))
    minus_qubit = Qubit.new(1.0 / Math.sqrt(2), -1.0 / Math.sqrt(2))
    
    puts "  |+⟩ = #{plus_qubit}"
    puts "  |-⟩ = #{minus_qubit}"
    
    # Measurement demonstration
    puts "\nMeasurement Results:"
    
    puts "  |0⟩ measurement: #{zero_qubit.measure}"
    puts "  |1⟩ measurement: #{one_qubit.measure}"
    
    # Superposition measurement (multiple trials)
    puts "  |+⟩ measurement probabilities: #{plus_qubit.probabilities}"
    
    plus_results = 1000.times.map { plus_qubit.measure }
    plus_counts = plus_results.tally
    puts "  |+⟩ measurement results (1000 trials): #{plus_counts}"
    
    # Random superposition
    puts "\nRandom Superposition:"
    random_qubit = Qubit.new(0.6, 0.8)
    puts "  Random qubit: #{random_qubit}"
    puts "  Probabilities: #{random_qubit.probabilities}"
    
    @examples << {
      title: "Quantum Bits",
      description: "Qubit implementation and superposition",
      code: <<~RUBY
        class Qubit
          def initialize(alpha = 1.0, beta = 0.0)
            @alpha = alpha
            @beta = beta
            normalize!
          end
          
          def measure
            prob_zero = (@alpha ** 2).abs
            rand < prob_zero ? 0 : 1
          end
        end
      RUBY
    }
    
    puts "\n✅ Quantum Bits example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def quantum_gates
    puts "\n🚪 Example 2: Quantum Gates"
    puts "=" * 50
    puts "Implementing quantum gates and operations."
    puts ""
    
    # Quantum gate implementation
    puts "🚪 Quantum Gate Implementation:"
    
    class QuantumGate
      def self.pauli_x
        Matrix[[0, 1], [1, 0]]
      end
      
      def self.pauli_y
        Matrix[[0, -Complex(0, 1)], [Complex(0, 1), 0]]
      end
      
      def self.pauli_z
        Matrix[[1, 0], [0, -1]]
      end
      
      def self.hadamard
        Matrix[[1.0 / Math.sqrt(2), 1.0 / Math.sqrt(2)], 
               [1.0 / Math.sqrt(2), -1.0 / Math.sqrt(2)]]
      end
      
      def self.phase(phi)
        Matrix[[1, 0], [0, Complex(Math.cos(phi), Math.sin(phi))]]
      end
      
      def self.cnot
        Matrix[[1, 0, 0, 0],
               [0, 1, 0, 0],
               [0, 0, 0, 1],
               [0, 0, 1, 0]]
      end
    end
    
    # Enhanced Qubit with gate operations
    class QuantumQubit
      attr_reader :state
      
      def initialize(alpha = 1.0, beta = 0.0)
        @state = Matrix[[alpha], [beta]]
        normalize!
      end
      
      def apply_gate(gate)
        @state = gate * @state
        normalize!
        self
      end
      
      def apply_x
        apply_gate(QuantumGate.pauli_x)
      end
      
      def apply_y
        apply_gate(QuantumGate.pauli_y)
      end
      
      def apply_z
        apply_gate(QuantumGate.pauli_z)
      end
      
      def apply_h
        apply_gate(QuantumGate.hadamard)
      end
      
      def apply_phase(phi)
        apply_gate(QuantumGate.phase(phi))
      end
      
      def measure
        prob_zero = (@state[0, 0] ** 2).abs
        rand < prob_zero ? 0 : 1
      end
      
      def probabilities
        {
          zero: (@state[0, 0] ** 2).abs,
          one: (@state[1, 0] ** 2).abs
        }
      end
      
      def to_s
        alpha = @state[0, 0]
        beta = @state[1, 0]
        "#{alpha.round(3)}|0⟩ + #{beta.round(3)}|1⟩"
      end
      
      private
      
      def normalize!
        norm = Math.sqrt((@state[0, 0] ** 2).abs + (@state[1, 0] ** 2).abs)
        @state = @state / norm
      end
    end
    
    # Gate demonstrations
    puts "\nQuantum Gate Operations:"
    
    # Start with |0⟩
    qubit = QuantumQubit.new(1.0, 0.0)
    puts "  Initial state: #{qubit}"
    
    # Apply Hadamard gate
    qubit.apply_h
    puts "  After H gate: #{qubit}"
    puts "  Probabilities: #{qubit.probabilities}"
    
    # Apply Pauli-X gate
    qubit.apply_x
    puts "  After X gate: #{qubit}"
    puts "  Probabilities: #{qubit.probabilities}"
    
    # Apply Phase gate
    qubit.apply_phase(Math::PI / 4)
    puts "  After Phase(π/4): #{qubit}"
    
    # Multiple gate sequence
    puts "\nGate Sequence Demonstration:"
    
    sequence_qubit = QuantumQubit.new(1.0, 0.0)
    puts "  Start: #{sequence_qubit}"
    
    sequence_qubit.apply_h.apply_x.apply_h
    puts "  H-X-H sequence: #{sequence_qubit}"
    
    # Bell state preparation
    puts "\nBell State Preparation:"
    
    bell_qubit = QuantumQubit.new(1.0, 0.0)
    bell_qubit.apply_h
    puts "  Bell state |+⟩: #{bell_qubit}"
    
    @examples << {
      title: "Quantum Gates",
      description: "Quantum gate implementation and operations",
      code: <<~RUBY
        class QuantumGate
          def self.hadamard
            Matrix[[1/Math.sqrt(2), 1/Math.sqrt(2)], 
                   [1/Math.sqrt(2), -1/Math.sqrt(2)]]
          end
          
          def self.pauli_x
            Matrix[[0, 1], [1, 0]]
          end
        end
      RUBY
    }
    
    puts "\n✅ Quantum Gates example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def quantum_circuits
    puts "\n⚡ Example 3: Quantum Circuits"
    puts "=" * 50
    puts "Building and simulating quantum circuits."
    puts ""
    
    # Quantum circuit implementation
    puts "⚡ Quantum Circuit Implementation:"
    
    class QuantumCircuit
      def initialize(num_qubits)
        @num_qubits = num_qubits
        @gates = []
        @qubits = Array.new(num_qubits) { |i| QuantumQubit.new(1.0, 0.0) }
      end
      
      def add_gate(gate_type, target_qubit, params = {})
        @gates << {
          type: gate_type,
          target: target_qubit,
          params: params
        }
        self
      end
      
      def hadamard(qubit)
        add_gate(:hadamard, qubit)
      end
      
      def pauli_x(qubit)
        add_gate(:pauli_x, qubit)
      end
      
      def pauli_y(qubit)
        add_gate(:pauli_y, qubit)
      end
      
      def pauli_z(qubit)
        add_gate(:pauli_z, qubit)
      end
      
      def phase(qubit, phi)
        add_gate(:phase, qubit, phi: phi)
      end
      
      def cnot(control, target)
        add_gate(:cnot, [control, target])
      end
      
      def run
        @gates.each do |gate|
          case gate[:type]
          when :hadamard
            @qubits[gate[:target]].apply_h
          when :pauli_x
            @qubits[gate[:target]].apply_x
          when :pauli_y
            @qubits[gate[:target]].apply_y
          when :pauli_z
            @qubits[gate[:target]].apply_z
          when :phase
            @qubits[gate[:target]].apply_phase(gate[:params][:phi])
          when :cnot
            control, target = gate[:target]
            apply_cnot(control, target)
          end
        end
        self
      end
      
      def measure
        @qubits.map(&:measure)
      end
      
      def probabilities
        @qubits.map(&:probabilities)
      end
      
      def to_s
        @qubits.map.with_index { |q, i| "Qubit #{i}: #{q}" }.join("\n")
      end
      
      private
      
      def apply_cnot(control, target)
        # Simplified CNOT implementation
        if @qubits[control].measure == 1
          @qubits[target].apply_x
        end
      end
    end
    
    # Circuit demonstrations
    puts "\nQuantum Circuit Examples:"
    
    # Simple single-qubit circuit
    puts "\nSingle-Qubit Circuit:"
    single_circuit = QuantumCircuit.new(1)
    single_circuit.hadamard(0).pauli_x(0).hadamard(0)
    
    puts "  Circuit: H-X-H"
    single_circuit.run
    puts "  Result: #{single_circuit}"
    puts "  Measurement: #{single_circuit.measure}"
    
    # Bell state circuit
    puts "\nBell State Circuit:"
    bell_circuit = QuantumCircuit.new(2)
    bell_circuit.hadamard(0).cnot(0, 1)
    
    puts "  Circuit: H(q0) - CNOT(q0,q1)"
    bell_circuit.run
    puts "  Result: #{bell_circuit}"
    puts "  Probabilities: #{bell_circuit.probabilities}"
    
    # GHZ state circuit
    puts "\nGHZ State Circuit:"
    ghz_circuit = QuantumCircuit.new(3)
    ghz_circuit.hadamard(0).cnot(0, 1).cnot(1, 2)
    
    puts "  Circuit: H(q0) - CNOT(q0,q1) - CNOT(q1,q2)"
    ghz_circuit.run
    puts "  Result: #{ghz_circuit}"
    
    # Multiple measurements
    puts "\nMultiple Measurements:"
    measurements = 1000.times.map { bell_circuit.measure }
    measurement_counts = measurements.tally
    puts "  Bell state measurement results: #{measurement_counts}"
    
    @examples << {
      title: "Quantum Circuits",
      description: "Building and simulating quantum circuits",
      code: <<~RUBY
        class QuantumCircuit
          def initialize(num_qubits)
            @num_qubits = num_qubits
            @gates = []
          end
          
          def hadamard(qubit)
            add_gate(:hadamard, qubit)
          end
          
          def run
            @gates.each { |gate| apply_gate(gate) }
          end
        end
      RUBY
    }
    
    puts "\n✅ Quantum Circuits example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Quantum Computing Examples"
    puts "=" * 55
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate quantum computing concepts!"
  end
end

if __FILE__ == $0
  examples = QuantumComputingExamples.new
  examples.start_examples
end
