# Quantum Circuits in Ruby

## Overview

Quantum circuits are the fundamental building blocks of quantum algorithms. They consist of quantum gates applied to qubits in a specific sequence to perform quantum computations. This guide explores how to design, implement, and simulate quantum circuits in Ruby.

## Quantum Circuit Architecture

### Circuit Components
```ruby
class QuantumCircuit
  attr_reader :num_qubits, :operations, :depth

  def initialize(num_qubits)
    @num_qubits = num_qubits
    @operations = []
    @qubits = Array.new(num_qubits) { Qubit.new }
    @depth = 0
  end

  def add_gate(gate_type, qubit_index, params = {})
    operation = {
      type: gate_type,
      qubit: qubit_index,
      params: params,
      position: @operations.length
    }
    
    @operations << operation
    @depth = calculate_depth
    operation
  end

  def add_two_qubit_gate(gate_type, control_qubit, target_qubit, params = {})
    operation = {
      type: gate_type,
      control: control_qubit,
      target: target_qubit,
      params: params,
      position: @operations.length
    }
    
    @operations << operation
    @depth = calculate_depth
    operation
  end

  def execute
    puts "Executing quantum circuit with #{@num_qubits} qubits"
    puts "Circuit depth: #{@depth}"
    puts "Operations: #{@operations.length}"
    
    @operations.each do |op|
      execute_operation(op)
    end
    
    get_final_state
  end

  def measure_all
    @qubits.map(&:measure)
  end

  def measure_qubit(qubit_index)
    @qubits[qubit_index].measure
  end

  def get_amplitudes
    @qubits.map { |qubit| [qubit.alpha, qubit.beta] }
  end

  def print_circuit_diagram
    puts "\nQuantum Circuit Diagram:"
    puts "=" * 50
    
    @num_qubits.times do |i|
      line = "q#{i}: |"
      
      @operations.each do |op|
        if affects_qubit?(op, i)
          line += " #{gate_symbol(op)} |"
        else
          line += "     |"
        end
      end
      
      puts line
    end
    
    puts "=" * 50
  end

  private

  def execute_operation(operation)
    case operation[:type]
    when :hadamard
      @qubits[operation[:qubit]].apply_gate(QuantumGates::HADAMARD)
    when :pauli_x
      @qubits[operation[:qubit]].apply_gate(QuantumGates::PAULI_X)
    when :pauli_y
      @qubits[operation[:qubit]].apply_gate(QuantumGates::PAULI_Y)
    when :pauli_z
      @qubits[operation[:qubit]].apply_gate(QuantumGates::PAULI_Z)
    when :phase
      angle = operation[:params][:angle] || Math::PI / 4
      gate = [[1, 0], [0, Math.exp(1i * angle)]]
      @qubits[operation[:qubit]].apply_gate(gate)
    when :cnot
      apply_cnot(operation[:control], operation[:target])
    when :swap
      apply_swap(operation[:control], operation[:target])
    when :controlled_phase
      angle = operation[:params][:angle] || Math::PI / 4
      apply_controlled_phase(operation[:control], operation[:target], angle)
    end
  end

  def apply_cnot(control, target)
    if @qubits[control].measure == 1
      @qubits[target].apply_gate(QuantumGates::PAULI_X)
    end
  end

  def apply_swap(qubit1, qubit2)
    # SWAP = CNOT(q2,q1) CNOT(q1,q2) CNOT(q2,q1)
    apply_cnot(qubit2, qubit1)
    apply_cnot(qubit1, qubit2)
    apply_cnot(qubit2, qubit1)
  end

  def apply_controlled_phase(control, target, angle)
    if @qubits[control].measure == 1
      gate = [[1, 0], [0, Math.exp(1i * angle)]]
      @qubits[target].apply_gate(gate)
    end
  end

  def affects_qubit?(operation, qubit_index)
    case operation[:type]
    when :cnot, :swap, :controlled_phase
      operation[:control] == qubit_index || operation[:target] == qubit_index
    else
      operation[:qubit] == qubit_index
    end
  end

  def gate_symbol(operation)
    case operation[:type]
    when :hadamard then 'H'
    when :pauli_x then 'X'
    when :pauli_y then 'Y'
    when :pauli_z then 'Z'
    when :phase then 'P'
    when :cnot then '⊕'
    when :swap then '⇄'
    when :controlled_phase then '⊗'
    else '?'
    end
  end

  def calculate_depth
    # Simplified depth calculation - assumes all operations can be parallelized
    # unless they share qubits
    max_parallel_depth = 0
    qubit_depths = Array.new(@num_qubits, 0)
    
    @operations.each do |op|
      affected_qubits = get_affected_qubits(op)
      max_current_depth = affected_qubits.map { |q| qubit_depths[q] }.max
      new_depth = max_current_depth + 1
      
      affected_qubits.each { |q| qubit_depths[q] = new_depth }
      max_parallel_depth = [max_parallel_depth, new_depth].max
    end
    
    max_parallel_depth
  end

  def get_affected_qubits(operation)
    case operation[:type]
    when :cnot, :swap, :controlled_phase
      [operation[:control], operation[:target]]
    else
      [operation[:qubit]]
    end
  end

  def get_final_state
    {
      amplitudes: get_amplitudes,
      probabilities: get_probabilities,
      entanglement: calculate_entanglement
    }
  end

  def get_probabilities
    @qubits.map { |qubit| qubit.alpha.abs2 }
  end

  def calculate_entanglement
    # Simplified entanglement measure
    # In reality, this would require full density matrix calculation
    0.0
  end
end
```

## Advanced Quantum Gates

### Universal Gate Set
```ruby
class UniversalQuantumGates
  def self.t_gate
    [[1, 0], [0, Math.exp(1i * Math::PI / 4)]]
  end

  def self.s_gate
    [[1, 0], [0, 1i]]
  end

  def self.rotation_x(angle)
    [[Math.cos(angle/2), -1i * Math.sin(angle/2)],
     [-1i * Math.sin(angle/2), Math.cos(angle/2)]]
  end

  def self.rotation_y(angle)
    [[Math.cos(angle/2), -Math.sin(angle/2)],
     [Math.sin(angle/2), Math.cos(angle/2)]]
  end

  def self.rotation_z(angle)
    [[Math.exp(-1i * angle/2), 0],
     [0, Math.exp(1i * angle/2)]]
  end

  def self.u_gate(theta, phi, lambda)
    [[Math.cos(theta/2), -Math.exp(1i * lambda) * Math.sin(theta/2)],
     [Math.exp(1i * phi) * Math.sin(theta/2), 
      Math.exp(1i * (phi + lambda)) * Math.cos(theta/2)]]
  end

  def self.cu_gate(theta, phi, lambda, control, target)
    # Controlled-U gate
    [
      [1, 0, 0, 0],
      [0, 1, 0, 0],
      [0, 0, Math.cos(theta/2), -Math.exp(1i * lambda) * Math.sin(theta/2)],
      [0, 0, Math.exp(1i * phi) * Math.sin(theta/2), 
       Math.exp(1i * (phi + lambda)) * Math.cos(theta/2)]
    ]
  end
end

# Gate Decomposition
class GateDecomposer
  def self.decompose_u_gate(theta, phi, lambda)
    # U(θ, φ, λ) = Rz(φ) Ry(θ) Rz(λ)
    [
      { type: :rotation_z, params: { angle: phi } },
      { type: :rotation_y, params: { angle: theta } },
      { type: :rotation_z, params: { angle: lambda } }
    ]
  end

  def self.decompose_cu_gate(theta, phi, lambda)
    # Controlled-U decomposition using CNOTs and single-qubit gates
    [
      { type: :rotation_z, params: { angle: lambda / 2 } },
      { type: :cnot, control: 0, target: 1 },
      { type: :rotation_y, params: { angle: theta / 2 } },
      { type: :cnot, control: 0, target: 1 },
      { type: :rotation_y, params: { angle: -theta / 2 } },
      { type: :rotation_z, params: { angle: -(phi + lambda) / 2 } },
      { type: :cnot, control: 0, target: 1 }
    ]
  end
end
```

## Circuit Optimization

### Circuit Optimizer
```ruby
class CircuitOptimizer
  def initialize(circuit)
    @circuit = circuit
    @optimized_operations = []
  end

  def optimize
    puts "Optimizing quantum circuit..."
    
    # Remove identity gates
    remove_identity_gates
    
    # Cancel adjacent inverse gates
    cancel_inverse_gates
    
    # Merge consecutive rotations
    merge_rotations
    
    # Reduce CNOT chains
    optimize_cnot_chains
    
    # Create optimized circuit
    create_optimized_circuit
    
    puts "Optimization complete"
    puts "Original operations: #{@circuit.operations.length}"
    puts "Optimized operations: #{@optimized_operations.length}"
    puts "Reduction: #{((1 - @optimized_operations.length.to_f / @circuit.operations.length) * 100).round(1)}%"
    
    @optimized_circuit
  end

  private

  def remove_identity_gates
    @circuit.operations.each do |op|
      next if is_identity_gate?(op)
      @optimized_operations << op
    end
  end

  def cancel_inverse_gates
    cancelled = []
    
    @optimized_operations.each_with_index do |op, i|
      next if cancelled.include?(i)
      
      # Check next operation for cancellation
      if i + 1 < @optimized_operations.length
        next_op = @optimized_operations[i + 1]
        if are_inverse_gates?(op, next_op) && same_qubits?(op, next_op)
          cancelled << i
          cancelled << i + 1
        end
      end
    end
    
    @optimized_operations = @optimized_operations.reject.with_index { |_, i| cancelled.include?(i) }
  end

  def merge_rotations
    merged = []
    
    @optimized_operations.each_with_index do |op, i|
      next if merged.include?(i)
      
      if op[:type] == :rotation_z && i + 1 < @optimized_operations.length
        next_op = @optimized_operations[i + 1]
        
        if next_op[:type] == :rotation_z && op[:qubit] == next_op[:qubit]
          # Merge rotations
          total_angle = op[:params][:angle] + next_op[:params][:angle]
          merged_op = op.dup
          merged_op[:params][:angle] = total_angle
          @optimized_operations[i] = merged_op
          merged << i + 1
        end
      end
    end
    
    @optimized_operations = @optimized_operations.reject.with_index { |_, i| merged.include?(i) }
  end

  def optimize_cnot_chains
    # Simplified CNOT optimization
    # In practice, this would involve more complex gate identities
  end

  def create_optimized_circuit
    @optimized_circuit = QuantumCircuit.new(@circuit.num_qubits)
    @optimized_operations.each do |op|
      if op[:control] && op[:target]
        @optimized_circuit.add_two_qubit_gate(op[:type], op[:control], op[:target], op[:params])
      else
        @optimized_circuit.add_gate(op[:type], op[:qubit], op[:params])
      end
    end
  end

  def is_identity_gate?(operation)
    case operation[:type]
    when :rotation_x, :rotation_y, :rotation_z
      angle = operation[:params][:angle] || 0
      angle % (2 * Math::PI) == 0
    when :phase
      angle = operation[:params][:angle] || 0
      angle % (2 * Math::PI) == 0
    else
      false
    end
  end

  def are_inverse_gates?(op1, op2)
    return false unless op1[:type] == op2[:type]
    
    case op1[:type]
    when :pauli_x, :pauli_y, :pauli_z
      true  # Pauli gates are their own inverses
    when :rotation_x, :rotation_y, :rotation_z, :phase
      angle1 = op1[:params][:angle] || 0
      angle2 = op2[:params][:angle] || 0
      (angle1 + angle2) % (2 * Math::PI) == 0
    else
      false
    end
  end

  def same_qubits?(op1, op2)
    if op1[:control] && op1[:target]
      op1[:control] == op2[:control] && op1[:target] == op2[:target]
    else
      op1[:qubit] == op2[:qubit]
    end
  end
end
```

## Circuit Templates

### Common Quantum Circuits
```ruby
class QuantumCircuitTemplates
  # Bell State Circuit
  def self.create_bell_state_circuit
    circuit = QuantumCircuit.new(2)
    circuit.add_gate(:hadamard, 0)
    circuit.add_two_qubit_gate(:cnot, 0, 1)
    circuit
  end

  # GHZ State Circuit
  def self.create_ghz_state_circuit(num_qubits)
    circuit = QuantumCircuit.new(num_qubits)
    circuit.add_gate(:hadamard, 0)
    
    (1...num_qubits).each do |i|
      circuit.add_two_qubit_gate(:cnot, 0, i)
    end
    
    circuit
  end

  # Quantum Fourier Transform Circuit
  def self.create_qft_circuit(num_qubits)
    circuit = QuantumCircuit.new(num_qubits)
    
    num_qubits.times do |j|
      circuit.add_gate(:hadamard, j)
      
      (j + 1...num_qubits).each do |k|
        angle = Math::PI / (2 ** (k - j))
        circuit.add_two_qubit_gate(:controlled_phase, k, j, { angle: angle })
      end
    end
    
    # Swap qubits for correct order
    (num_qubits / 2).times do |i|
      circuit.add_two_qubit_gate(:swap, i, num_qubits - 1 - i)
    end
    
    circuit
  end

  # Variational Quantum Eigensolver (VQE) Circuit
  def self.create_vqe_circuit(num_qubits, depth)
    circuit = QuantumCircuit.new(num_qubits)
    
    depth.times do |layer|
      # Parameterized rotations
      num_qubits.times do |i|
        theta = rand(0..2 * Math::PI)
        circuit.add_gate(:rotation_y, i, { angle: theta })
      end
      
      # Entanglement layer
      (num_qubits - 1).times do |i|
        circuit.add_two_qubit_gate(:cnot, i, i + 1)
      end
    end
    
    circuit
  end

  # Quantum Approximate Optimization Algorithm (QAOA) Circuit
  def self.create_qaoa_circuit(num_qubits, p, gamma, beta)
    circuit = QuantumCircuit.new(num_qubits)
    
    p.times do |layer|
      # Problem unitary (simplified - depends on specific problem)
      num_qubits.times do |i|
        circuit.add_gate(:rotation_z, i, { angle: gamma[layer] })
      end
      
      # Mixer unitary
      num_qubits.times do |i|
        circuit.add_gate(:rotation_x, i, { angle: beta[layer] })
      end
    end
    
    circuit
  end

  # Quantum Phase Estimation Circuit
  def self.create_qpe_circuit(num_counting_qubits, unitary_circuit)
    total_qubits = num_counting_qubits + unitary_circuit.num_qubits
    circuit = QuantumCircuit.new(total_qubits)
    
    # Initialize counting qubits in superposition
    num_counting_qubits.times do |i|
      circuit.add_gate(:hadamard, i)
    end
    
    # Apply controlled unitary operations
    num_counting_qubits.times do |i|
      repetitions = 2 ** i
      repetitions.times do
        # Apply controlled version of unitary
        apply_controlled_unitary(circuit, unitary_circuit, i, num_counting_qubits)
      end
    end
    
    # Apply inverse QFT to counting qubits
    qft_circuit = create_qft_circuit(num_counting_qubits)
    # Apply inverse operations (simplified)
    
    circuit
  end

  private

  def self.apply_controlled_unitary(circuit, unitary_circuit, control, target_start)
    # Simplified controlled unitary application
    # In practice, this would decompose the unitary into basic gates
    unitary_circuit.operations.each do |op|
      case op[:type]
      when :hadamard
        circuit.add_two_qubit_gate(:controlled_hadamard, control, target_start + op[:qubit])
      when :pauli_x
        circuit.add_two_qubit_gate(:cnot, control, target_start + op[:qubit])
      # Add more gate types as needed
      end
    end
  end
end
```

## Circuit Simulation

### Advanced Circuit Simulator
```ruby
class AdvancedQuantumSimulator
  def initialize(circuit)
    @circuit = circuit
    @state_vector = initialize_state_vector
    @measurement_history = []
  end

  def simulate(shots = 1000)
    puts "Simulating quantum circuit with #{shots} shots"
    
    # Execute circuit
    execute_circuit
    
    # Perform measurements
    results = Array.new(shots) { measure_all_qubits }
    
    # Analyze results
    analyze_results(results)
  end

  def get_state_vector
    @state_vector
  end

  def get_density_matrix
    calculate_density_matrix
  end

  def calculate_fidelity(target_state)
    # Calculate fidelity between current state and target state
    overlap = 0
    
    @state_vector.each_with_index do |amplitude, i|
      overlap += amplitude * target_state[i].conjugate
    end
    
    overlap.abs2
  end

  private

  def initialize_state_vector
    # Initialize |00...0⟩ state
    size = 2 ** @circuit.num_qubits
    state_vector = Array.new(size, 0)
    state_vector[0] = 1
    state_vector
  end

  def execute_circuit
    @circuit.operations.each do |op|
      apply_operation_to_state_vector(op)
    end
  end

  def apply_operation_to_state_vector(operation)
    case operation[:type]
    when :hadamard, :pauli_x, :pauli_y, :pauli_z, :phase, :rotation_x, :rotation_y, :rotation_z
      apply_single_qubit_gate(operation)
    when :cnot, :swap, :controlled_phase
      apply_two_qubit_gate(operation)
    end
  end

  def apply_single_qubit_gate(operation)
    gate = get_gate_matrix(operation)
    qubit = operation[:qubit]
    
    new_state_vector = Array.new(@state_vector.length, 0)
    
    @state_vector.each_with_index do |amplitude, index|
      # Find which basis state this amplitude corresponds to
      basis_state = index.to_s(2).rjust(@circuit.num_qubits, '0').reverse
      
      # Apply gate to the specified qubit
      (0..1).each do |target_bit|
        new_basis_state = basis_state.dup
        new_basis_state[qubit] = target_bit.to_s
        
        new_index = new_basis_state.reverse.to_i(2)
        new_state_vector[new_index] += gate[target_bit][basis_state[qubit].to_i] * amplitude
      end
    end
    
    @state_vector = new_state_vector
  end

  def apply_two_qubit_gate(operation)
    gate = get_two_qubit_gate_matrix(operation)
    control = operation[:control]
    target = operation[:target]
    
    new_state_vector = Array.new(@state_vector.length, 0)
    
    @state_vector.each_with_index do |amplitude, index|
      basis_state = index.to_s(2).rjust(@circuit.num_qubits, '0').reverse
      
      # Only apply if control qubit is |1⟩ (for CNOT)
      if operation[:type] == :cnot
        if basis_state[control] == '1'
          # Flip target qubit
          new_basis_state = basis_state.dup
          new_basis_state[target] = (basis_state[target] == '0' ? '1' : '0')
          new_index = new_basis_state.reverse.to_i(2)
          new_state_vector[new_index] += amplitude
        else
          # No change
          new_state_vector[index] += amplitude
        end
      else
        # For other two-qubit gates, apply full 4x4 matrix
        (0..3).each do |target_state|
          new_basis_state = basis_state.dup
          new_basis_state[control] = (target_state / 2).to_s
          new_basis_state[target] = (target_state % 2).to_s
          
          new_index = new_basis_state.reverse.to_i(2)
          current_state = (basis_state[control] + basis_state[target]).to_i(2)
          new_state_vector[new_index] += gate[target_state][current_state] * amplitude
        end
      end
    end
    
    @state_vector = new_state_vector
  end

  def get_gate_matrix(operation)
    case operation[:type]
    when :hadamard
      QuantumGates::HADAMARD
    when :pauli_x
      QuantumGates::PAULI_X
    when :pauli_y
      QuantumGates::PAULI_Y
    when :pauli_z
      QuantumGates::PAULI_Z
    when :phase
      angle = operation[:params][:angle] || Math::PI / 4
      [[1, 0], [0, Math.exp(1i * angle)]]
    when :rotation_x
      angle = operation[:params][:angle] || 0
      UniversalQuantumGates.rotation_x(angle)
    when :rotation_y
      angle = operation[:params][:angle] || 0
      UniversalQuantumGates.rotation_y(angle)
    when :rotation_z
      angle = operation[:params][:angle] || 0
      UniversalQuantumGates.rotation_z(angle)
    else
      [[1, 0], [0, 1]]  # Identity
    end
  end

  def get_two_qubit_gate_matrix(operation)
    case operation[:type]
    when :cnot
      [[1, 0, 0, 0],
       [0, 1, 0, 0],
       [0, 0, 0, 1],
       [0, 0, 1, 0]]
    when :swap
      [[1, 0, 0, 0],
       [0, 0, 1, 0],
       [0, 1, 0, 0],
       [0, 0, 0, 1]]
    when :controlled_phase
      angle = operation[:params][:angle] || Math::PI / 4
      [[1, 0, 0, 0],
       [0, 1, 0, 0],
       [0, 0, 1, 0],
       [0, 0, 0, Math.exp(1i * angle)]]
    else
      [[1, 0, 0, 0],
       [0, 1, 0, 0],
       [0, 0, 1, 0],
       [0, 0, 0, 1]]  # Identity
    end
  end

  def measure_all_qubits
    probabilities = @state_vector.map { |amp| amp.abs2 }
    cumulative = probabilities.each_with_index.map { |prob, i| [i, probabilities[0...i].sum] }
    
    random = rand
    selected_index = cumulative.find { |_, cum| random <= cum + probabilities[_] }&.first || 0
    
    result = selected_index.to_s(2).rjust(@circuit.num_qubits, '0')
    @measurement_history << result
    result
  end

  def analyze_results(results)
    # Count measurement outcomes
    counts = Hash.new(0)
    results.each { |result| counts[result] += 1 }
    
    # Calculate probabilities
    probabilities = counts.transform_values { |count| count.to_f / results.length }
    
    # Find most likely outcome
    most_likely = probabilities.max_by { |_, prob| prob }
    
    {
      counts: counts,
      probabilities: probabilities,
      most_likely_outcome: most_likely[0],
      confidence: most_likely[1],
      shots: results.length
    }
  end

  def calculate_density_matrix
    size = @state_vector.length
    density_matrix = Array.new(size) { Array.new(size, 0) }
    
    @state_vector.each_with_index do |amp_i, i|
      @state_vector.each_with_index do |amp_j, j|
        density_matrix[i][j] = amp_i * amp_j.conjugate
      end
    end
    
    density_matrix
  end
end
```

## Visualization

### Circuit Visualizer
```ruby
class CircuitVisualizer
  def initialize(circuit)
    @circuit = circuit
  end

  def generate_ascii_diagram
    diagram = []
    diagram << "Quantum Circuit (#{@circuit.num_qubits} qubits, #{@circuit.depth} depth)"
    diagram << "=" * 80
    
    @circuit.num_qubits.times do |i|
      line = "q#{i.to_s.rjust(2)}: |"
      
      @circuit.operations.each do |op|
        if affects_qubit?(op, i)
          line += " #{format_gate(op).ljust(6)} |"
        else
          line += "       |"
        end
      end
      
      diagram << line
    end
    
    diagram << "=" * 80
    diagram.join("\n")
  end

  def generate_latex_diagram
    latex = []
    latex << "\\begin{quantikz}"
    latex << "  \\lstick{|0\\rangle} & "
    
    @circuit.operations.each_with_index do |op, i|
      latex += latex_gate(op)
      latex += " & " if i < @circuit.operations.length - 1
    end
    
    latex << " \\qw;"
    latex << "\\end{quantikz}"
    
    latex.join("\n")
  end

  def generate_statistics
    stats = {
      total_gates: @circuit.operations.length,
      gate_types: count_gate_types,
      depth: @circuit.depth,
      qubit_utilization: calculate_qubit_utilization,
      entanglement: estimate_entanglement
    }
    
    format_statistics(stats)
  end

  private

  def affects_qubit?(operation, qubit_index)
    case operation[:type]
    when :cnot, :swap, :controlled_phase
      operation[:control] == qubit_index || operation[:target] == qubit_index
    else
      operation[:qubit] == qubit_index
    end
  end

  def format_gate(operation)
    case operation[:type]
    when :hadamard then 'H'
    when :pauli_x then 'X'
    when :pauli_y then 'Y'
    when :pauli_z then 'Z'
    when :phase then 'P'
    when :rotation_x then 'Rx'
    when :rotation_y then 'Ry'
    when :rotation_z then 'Rz'
    when :cnot then '⊕'
    when :swap then '⇄'
    when :controlled_phase then '⊗'
    else '?'
    end
  end

  def latex_gate(operation)
    case operation[:type]
    when :hadamard
      " \\gate{H} "
    when :pauli_x
      " \\gate{X} "
    when :pauli_z
      " \\gate{Z} "
    when :cnot
      " \\ctrl{1} \\qw & \\gate{X} \\qw "
    when :swap
      " \\qswap \\qw & \\qswap \\qw "
    else
      " \\gate{?} "
    end
  end

  def count_gate_types
    gate_counts = Hash.new(0)
    @circuit.operations.each { |op| gate_counts[op[:type]] += 1 }
    gate_counts
  end

  def calculate_qubit_utilization
    utilization = Array.new(@circuit.num_qubits, 0)
    
    @circuit.operations.each do |op|
      case op[:type]
      when :cnot, :swap, :controlled_phase
        utilization[op[:control]] += 1
        utilization[op[:target]] += 1
      else
        utilization[op[:qubit]] += 1
      end
    end
    
    utilization
  end

  def estimate_entanglement
    # Simplified entanglement estimation based on CNOT count
    cnot_count = @circuit.operations.count { |op| op[:type] == :cnot }
    cnot_count.to_f / @circuit.operations.length
  end

  def format_statistics(stats)
    output = []
    output << "Circuit Statistics:"
    output << "  Total gates: #{stats[:total_gates]}"
    output << "  Circuit depth: #{stats[:depth]}"
    output << "  Gate distribution:"
    
    stats[:gate_types].each do |type, count|
      output << "    #{type}: #{count}"
    end
    
    output << "  Qubit utilization:"
    stats[:qubit_utilization].each_with_index do |util, i|
      output << "    q#{i}: #{util} operations"
    end
    
    output << "  Entanglement estimate: #{(stats[:entanglement] * 100).round(1)}%"
    
    output.join("\n")
  end
end
```

## Best Practices

1. **Gate Optimization**: Minimize gate count and circuit depth
2. **Error Analysis**: Consider noise and decoherence effects
3. **Resource Estimation**: Calculate required qubits and operations
4. **Modular Design**: Build circuits from reusable components
5. **Testing**: Verify circuit behavior with small examples
6. **Documentation**: Document circuit purpose and parameters
7. **Performance**: Optimize state vector simulation for large circuits

## Conclusion

Quantum circuits are the foundation of quantum computing. While Ruby simulations are educational, they provide valuable insights into quantum algorithm design and optimization. Understanding circuit construction, optimization, and simulation prepares you for real quantum programming.

## Further Reading

- [Quantum Circuit Design Principles](https://arxiv.org/abs/quant-ph/9503016)
- [IBM Quantum Circuit Composer](https://quantum-computing.ibm.com/composer)
- [Qiskit Circuit Library](https://qiskit.org/documentation/apidoc/circuit_library.html)
- [Quantum Compiler Optimization](https://www.nature.com/articles/s41534-019-0205-7)
