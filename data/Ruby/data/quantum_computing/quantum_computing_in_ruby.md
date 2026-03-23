# Quantum Computing in Ruby

## Overview

This guide explores quantum computing concepts and their implementation in Ruby. While Ruby may not be the traditional choice for quantum computing, it provides an excellent platform for understanding quantum concepts through simulation and visualization.

## Quantum Computing Fundamentals

### Qubit Representation

```ruby
class Qubit
  attr_reader :alpha, :beta, :amplitudes
  
  def initialize(alpha = 1.0, beta = 0.0)
    @alpha = Complex(alpha)
    @beta = Complex(beta)
    normalize!
    @amplitudes = [@alpha, @beta]
  end
  
  def self.zero
    new(1.0, 0.0)
  end
  
  def self.one
    new(0.0, 1.0)
  end
  
  def self.plus
    new(1.0 / Math.sqrt(2), 1.0 / Math.sqrt(2))
  end
  
  def self.minus
    new(1.0 / Math.sqrt(2), -1.0 / Math.sqrt(2))
  end
  
  def measure
    prob_zero = (@alpha.abs2).real
    prob_one = (@beta.abs2).real
    
    result = rand < prob_zero ? 0 : 1
    
    # Collapse to measured state
    case result
    when 0
      @alpha = Complex(1.0, 0.0)
      @beta = Complex(0.0, 0.0)
    when 1
      @alpha = Complex(0.0, 0.0)
      @beta = Complex(1.0, 0.0)
    end
    
    result
  end
  
  def probability_of_zero
    (@alpha.abs2).real
  end
  
  def probability_of_one
    (@beta.abs2).real
  end
  
  def apply_gate(gate_matrix)
    new_alpha = gate_matrix[0][0] * @alpha + gate_matrix[0][1] * @beta
    new_beta = gate_matrix[1][0] * @alpha + gate_matrix[1][1] * @beta
    
    Qubit.new(new_alpha.real, new_beta.real)
  end
  
  def to_s
    "#{@alpha.round(3)}|0⟩ + #{@beta.round(3)}|1⟩"
  end
  
  def to_bloch_sphere
    theta = 2 * Math.acos(@alpha.real)
    phi = Math.atan2(@beta.imag, @beta.real)
    
    {
      theta: theta,
      phi: phi,
      x: Math.sin(theta) * Math.cos(phi),
      y: Math.sin(theta) * Math.sin(phi),
      z: Math.cos(theta)
    }
  end
  
  private
  
  def normalize!
    norm = Math.sqrt((@alpha.abs2 + @beta.abs2).real)
    if norm > 0
      @alpha /= norm
      @beta /= norm
    end
  end
end
```

### Quantum Gates

```ruby
module QuantumGates
  # Pauli-X gate (NOT gate)
  X = [
    [0, 1],
    [1, 0]
  ].freeze
  
  # Pauli-Y gate
  Y = [
    [0, Complex(0, -1)],
    [Complex(0, 1), 0]
  ].freeze
  
  # Pauli-Z gate
  Z = [
    [1, 0],
    [0, -1]
  ].freeze
  
  # Hadamard gate
  H = [
    [1 / Math.sqrt(2), 1 / Math.sqrt(2)],
    [1 / Math.sqrt(2), -1 / Math.sqrt(2)]
  ].freeze
  
  # Identity gate
  I = [
    [1, 0],
    [0, 1]
  ].freeze
  
  # Phase gate
  def self.phase(angle)
    [
      [1, 0],
      [0, Complex(Math.cos(angle), Math.sin(angle))]
    ]
  end
  
  # Rotation gate around X axis
  def self.rx(angle)
    half_angle = angle / 2
    cos_half = Math.cos(half_angle)
    sin_half = Math.sin(half_angle)
    
    [
      [cos_half, Complex(0, -sin_half)],
      [Complex(0, -sin_half), cos_half]
    ]
  end
  
  # Rotation gate around Y axis
  def self.ry(angle)
    half_angle = angle / 2
    cos_half = Math.cos(half_angle)
    sin_half = Math.sin(half_angle)
    
    [
      [cos_half, -sin_half],
      [sin_half, cos_half]
    ]
  end
  
  # Rotation gate around Z axis
  def self.rz(angle)
    [
      [Complex(0, -angle/2), 0],
      [0, Complex(0, angle/2)]
    ]
  end
  
  # CNOT gate (Controlled-NOT)
  CNOT = [
    [1, 0, 0, 0],
    [0, 1, 0, 0],
    [0, 0, 0, 1],
    [0, 0, 1, 0]
  ].freeze
  
  # SWAP gate
  SWAP = [
    [1, 0, 0, 0],
    [0, 0, 1, 0],
    [0, 1, 0, 0],
    [0, 0, 0, 1]
  ].freeze
end
```

### Multi-Qubit System

```ruby
class QuantumRegister
  attr_reader :qubits, :size
  
  def initialize(*qubits)
    @qubits = qubits.flatten
    @size = @qubits.length
  end
  
  def self.zeros(n)
    new(*Array.new(n) { Qubit.zero })
  end
  
  def self.from_state(state_vector)
    n = Math.log2(state_vector.length).to_i
    qubits = []
    
    (2**n).times do |i|
      amplitude = state_vector[i]
      qubits << Qubit.new(amplitude.real, 0) if amplitude != 0
    end
    
    new(qubits)
  end
  
  def apply_gate(gate, target_qubit)
    raise ArgumentError, "Target qubit out of range" if target_qubit >= @size
    
    @qubits[target_qubit] = @qubits[target_qubit].apply_gate(gate)
  end
  
  def apply_controlled_gate(gate, control_qubit, target_qubit)
    raise ArgumentError, "Control qubit out of range" if control_qubit >= @size
    raise ArgumentError, "Target qubit out of range" if target_qubit >= @size
    
    if @qubits[control_qubit].measure == 1
      @qubits[target_qubit] = @qubits[target_qubit].apply_gate(gate)
    end
  end
  
  def apply_two_qubit_gate(gate, qubit1, qubit2)
    raise ArgumentError, "Qubit indices out of range" if qubit1 >= @size || qubit2 >= @size
    
    # Simplified two-qubit gate application
    # In practice, this would require tensor product calculations
    if qubit1 == 0 && qubit2 == 1
      @qubits[1] = @qubits[1].apply_gate(gate)
    end
  end
  
  def measure_all
    results = []
    @qubits.each { |qubit| results << qubit.measure }
    results
  end
  
  def measure_qubit(index)
    @qubits[index].measure
  end
  
  def entangle!(qubit1, qubit2)
    # Create Bell state
    @qubits[qubit1] = Qubit.new(1.0 / Math.sqrt(2), 0.0)
    @qubits[qubit2] = Qubit.new(1.0 / Math.sqrt(2), 0.0)
    
    # Apply H gate to first qubit
    @qubits[qubit1] = @qubits[qubit1].apply_gate(QuantumGates::H)
    
    # Apply CNOT gate
    apply_controlled_gate(QuantumGates::X, qubit1, qubit2)
  end
  
  def to_state_vector
    # Simplified state vector calculation
    # In practice, this would involve tensor products
    state_vector = Array.new(2**@size, 0)
    
    # For demonstration, return a simple state vector
    state_vector[0] = @qubits.reduce(1) { |acc, q| acc * q.alpha.real }
    state_vector[2**@size - 1] = @qubits.reduce(1) { |acc, q| acc * q.beta.real }
    
    state_vector
  end
  
  def to_s
    @qubits.map.with_index { |q, i| "Qubit #{i}: #{q}" }.join("\n")
  end
  
  def density_matrix
    # Simplified density matrix calculation
    state_vector = to_state_vector
    n = state_vector.length
    
    Array.new(n) do |i|
      Array.new(n) do |j|
        state_vector[i] * state_vector[j].conjugate
      end
    end
  end
end
```

### Quantum Algorithms

```ruby
class QuantumAlgorithms
  def self.deutsch_jozsa(oracle)
    # Deutsch-Jozsa algorithm for 2-bit function
    # Returns true if function is constant, false if balanced
    
    # Prepare qubits
    register = QuantumRegister.zeros(2)
    
    # Apply Hadamard gates
    register.apply_gate(QuantumGates::H, 0)
    register.apply_gate(QuantumGates::H, 1)
    
    # Apply oracle
    oracle.call(register)
    
    # Apply Hadamard gates again
    register.apply_gate(QuantumGates::H, 0)
    register.apply_gate(QuantumGates::H, 1)
    
    # Measure first qubit
    result = register.measure_qubit(0)
    
    result == 0  # 0 indicates constant function
  end
  
  def self.grover_search(items, target_index)
    n = items.length
    num_qubits = Math.log2(n).ceil
    
    # Initialize quantum register
    register = QuantumRegister.zeros(num_qubits)
    
    # Apply Hadamard gates to create superposition
    num_qubits.times { |i| register.apply_gate(QuantumGates::H, i) }
    
    # Grover iteration (simplified)
    iterations = Math.sqrt(n).to_i
    
    iterations.times do
      # Oracle function (marks target)
      oracle_function(register, target_index)
      
      # Diffusion operator
      diffusion_operator(register)
    end
    
    # Measure result
    measurement = register.measure_all
    measurement.join.to_i(2)
  end
  
  def self.quantum_fourier_transform(register)
    n = register.size
    
    # Apply Hadamard gates
    n.times { |i| register.apply_gate(QuantumGates::H, i) }
    
    # Apply controlled phase rotations
    (n-1).times do |i|
      (n-i-1).times do |j|
        angle = 2 * Math::PI / (2**(j+2))
        phase_gate = QuantumGates.phase(angle)
        register.apply_controlled_gate(phase_gate, i+j+1, i)
      end
    end
    
    # Apply Hadamard gates again
    n.times { |i| register.apply_gate(QuantumGates::H, i) }
  end
  
  def self.shors_algorithm(n)
    # Simplified Shor's algorithm for period finding
    # Returns the period of a function modulo n
    
    # Choose random a
    a = rand(2..n-1)
    return nil if a.gcd(n) != 1
    
    # Create quantum register for period finding
    register = QuantumRegister.zeros(Math.log2(n*n).ceil)
    
    # Apply quantum Fourier transform
    quantum_fourier_transform(register)
    
    # Measure result
    measurement = register.measure_all
    result = measurement.join.to_i(2)
    
    # Classical post-processing (simplified)
    find_period(a, n, result)
  end
  
  private
  
  def self.oracle_function(register, target_index)
    # Simplified oracle that marks the target state
    state = register.to_state_vector
    target_state = target_index
    
    # Flip phase of target state
    state[target_state] = -state[target_state]
  end
  
  def self.diffusion_operator(register)
    n = register.size
    
    # Apply Hadamard gates
    n.times { |i| register.apply_gate(QuantumGates::H, i) }
    
    # Apply phase flip
    n.times { |i| register.apply_gate(QuantumGates::Z, i) }
    
    # Apply Hadamard gates again
    n.times { |i| register.apply_gate(QuantumGates::H, i) }
  end
  
  def self.find_period(a, n, measurement)
    # Simplified period finding (classical)
    # In real Shor's algorithm, this would use continued fractions
    period = 1
    
    while (a**period) % n != 1
      period += 1
      break if period > n
    end
    
    period
  end
end
```

### Quantum Simulator

```ruby
class QuantumSimulator
  def initialize
    @registers = {}
    @history = []
  end
  
  def create_register(name, *qubits)
    register = QuantumRegister.new(*qubits)
    @registers[name] = register
    @history << "Created register '#{name}' with #{register.size} qubits"
    register
  end
  
  def apply_gate(register_name, gate, target_qubit)
    register = @registers[register_name]
    raise ArgumentError, "Register '#{register_name}' not found" unless register
    
    register.apply_gate(gate, target_qubit)
    @history << "Applied #{gate_name} to qubit #{target_qubit} in register '#{register_name}'"
  end
  
  def measure(register_name, qubit = nil)
    register = @registers[register_name]
    raise ArgumentError, "Register '#{register_name}' not found" unless register
    
    result = qubit ? register.measure_qubit(qubit) : register.measure_all
    @history << "Measured register '#{register_name}': #{result}"
    result
  end
  
  def entangle(register_name, qubit1, qubit2)
    register = @registers[register_name]
    raise ArgumentError, "Register '#{register_name}' not found" unless register
    
    register.entangle!(qubit1, qubit2)
    @history << "Entangled qubits #{qubit1} and #{qubit2} in register '#{register_name}'"
  end
  
  def run_deutsch_jozsa(oracle)
    result = QuantumAlgorithms.deutsch_jozsa(oracle)
    @history << "Deutsch-Jozsa algorithm result: #{result ? 'constant' : 'balanced'}"
    result
  end
  
  def run_grover_search(items, target)
    result = QuantumAlgorithms.grover_search(items, target)
    @history << "Grover's search result: #{result}"
    result
  end
  
  def run_shors(n)
    result = QuantumAlgorithms.shors_algorithm(n)
    @history << "Shor's algorithm result: #{result}"
    result
  end
  
  def visualize_register(register_name)
    register = @registers[register_name]
    return "Register '#{register_name}' not found" unless register
    
    visualization = []
    register.qubits.each_with_index do |qubit, i|
      bloch = qubit.to_bloch_sphere
      visualization << {
        qubit: i,
        state: qubit.to_s,
        probabilities: {
          zero: qubit.probability_of_zero,
          one: qubit.probability_of_one
        },
        bloch_sphere: bloch
      }
    end
    
    visualization
  end
  
  def history
    @history.join("\n")
  end
  
  def reset!
    @registers.clear
    @history.clear
  end
  
  def state_summary
    summary = []
    
    @registers.each do |name, register|
      summary << {
        name: name,
        size: register.size,
        state_vector: register.to_state_vector,
        entanglement: calculate_entanglement(register)
      }
    end
    
    summary
  end
  
  private
  
  def calculate_entanglement(register)
    # Simplified entanglement measure
    # In practice, this would use von Neumann entropy
    density_matrix = register.density_matrix
    trace = density_matrix.map.with_index { |row, i| row[i].real }.sum
    
    # Return a simple entanglement measure
    (trace - 1).abs
  end
end
```

### Quantum Error Correction

```ruby
class QuantumErrorCorrection
  def initialize
    @syndrome_table = {}
    @correction_table = {}
  end
  
  def encode_logical_qubit(physical_qubits)
    # Three-qubit bit-flip code
    encoded = []
    
    # Encode logical |0⟩ as |000⟩
    encoded << Qubit.zero
    encoded << Qubit.zero
    encoded << Qubit.zero
    
    encoded
  end
  
  def encode_logical_one
    # Encode logical |1⟩ as |111⟩
    encoded = []
    
    encoded << Qubit.one
    encoded << Qubit.one
    encoded << Qubit.one
    
    encoded
  end
  
  def detect_error(physical_qubits)
    # Simplified error detection using parity checks
    syndrome = []
    
    # Check parity between qubits 0 and 1
    parity_01 = (physical_qubits[0].measure + physical_qubits[1].measure) % 2
    syndrome << parity_01
    
    # Check parity between qubits 1 and 2
    parity_12 = (physical_qubits[1].measure + physical_qubits[2].measure) % 2
    syndrome << parity_12
    
    syndrome
  end
  
  def correct_error(physical_qubits, syndrome)
    corrected = physical_qubits.dup
    
    case syndrome
    when [1, 1]
      # Error in qubit 1
      corrected[1] = flip_qubit(corrected[1])
    when [0, 1]
      # Error in qubit 2
      corrected[2] = flip_qubit(corrected[2])
    when [1, 0]
      # Error in qubit 0
      corrected[0] = flip_qubit(corrected[0])
    end
    
    corrected
  end
  
  def decode_logical_qubit(physical_qubits)
    # Decode by majority vote
    measurements = physical_qubits.map(&:measure)
    majority = measurements.group_by(&:itself).max_by { |k, v| v.length }.first
    
    majority == 0 ? Qubit.zero : Qubit.one
  end
  
  def apply_error(qubits, error_rate = 0.1)
    qubits.map do |qubit|
      if rand < error_rate
        flip_qubit(qubit)
      else
        qubit
      end
    end
  end
  
  def simulate_error_correction(logical_state, error_rate = 0.1)
    # Encode logical qubit
    if logical_state == 0
      physical_qubits = encode_logical_qubit
    else
      physical_qubits = encode_logical_one
    end
    
    # Apply errors
    physical_qubits = apply_error(physical_qubits, error_rate)
    
    # Detect errors
    syndrome = detect_error(physical_qubits)
    
    # Correct errors
    corrected_qubits = correct_error(physical_qubits, syndrome)
    
    # Decode logical qubit
    decoded = decode_logical_qubit(corrected_qubits)
    
    {
      original: logical_state,
      decoded: decoded.measure,
      syndrome: syndrome,
      success: decoded.measure == logical_state
    }
  end
  
  private
  
  def flip_qubit(qubit)
    if qubit.measure == 0
      Qubit.new(0, 1)
    else
      Qubit.new(1, 0)
    end
  end
end
```

### Quantum Cryptography

```ruby
class QuantumCryptography
  def self.bb84_key_exchange(alice_bits, bob_bits, eve_present = false)
    # Simplified BB84 protocol
    alice_keys = []
    bob_keys = []
    eve_keys = [] if eve_present
    
    alice_bits.each_with_index do |bit, i|
      # Alice prepares qubit in random basis
      alice_basis = rand < 0.5 ? :rectilinear : :diagonal
      alice_qubit = prepare_qubit(bit, alice_basis)
      
      # Bob chooses random basis for measurement
      bob_basis = rand < 0.5 ? :rectilinear : :diagonal
      bob_bit = measure_qubit(alice_qubit, bob_basis)
      
      # Eve intercepts (if present)
      if eve_present
        eve_basis = rand < 0.5 ? :rectilinear : :diagonal
        eve_bit = measure_qubit(alice_qubit, eve_basis)
        eve_keys << eve_bit
        
        # Eve resends qubit
        alice_qubit = prepare_qubit(eve_bit, eve_basis)
        bob_bit = measure_qubit(alice_qubit, bob_basis)
      end
      
      # Bob and Alice compare bases (in real protocol)
      if alice_basis == bob_basis
        alice_keys << bit
        bob_keys << bob_bit
      end
    end
    
    {
      alice_key: alice_keys,
      bob_key: bob_keys,
      eve_key: eve_keys,
      error_rate: calculate_error_rate(alice_keys, bob_keys)
    }
  end
  
  def self.quantum_teleportation(state_to_teleport)
    # Simplified quantum teleportation
    # Uses entanglement and classical communication
    
    # Create entangled pair
    entangled_pair = create_entangled_pair
    
    # Bell state measurement (simplified)
    bell_measurement = bell_state_measurement(state_to_teleport, entangled_pair[0])
    
    # Classical communication of measurement results
    classical_info = {
      measurement: bell_measurement,
      entangled_state: entangled_pair[1]
    }
    
    # Bob applies corrections based on classical info
    teleported_state = apply_corrections(classical_info)
    
    teleported_state
  end
  
  def self.superdense_coding(message_bits)
    # Superdense coding: send 2 classical bits using 1 quantum bit
    encoded_states = []
    
    message_bits.each_slice(2) do |bit1, bit2|
      # Encode 2 classical bits into 1 quantum bit
      case [bit1, bit2]
      when [0, 0]
        encoded_states << Qubit.zero
      when [0, 1]
        encoded_states << Qubit.plus
      when [1, 0]
        encoded_states << Qubit.minus
      when [1, 1]
        encoded_states << Qubit.one
      end
    end
    
    encoded_states
  end
  
  private
  
  def self.prepare_qubit(bit, basis)
    case basis
    when :rectilinear
      bit == 0 ? Qubit.zero : Qubit.one
    when :diagonal
      bit == 0 ? Qubit.plus : Qubit.minus
    end
  end
  
  def self.measure_qubit(qubit, basis)
    case basis
    when :rectilinear
      qubit.measure
    when :diagonal
      # Convert diagonal basis to computational basis
      h_gate = QuantumGates::H
      measured = qubit.apply_gate(h_gate).measure
      measured
    end
  end
  
  def self.create_entangled_pair
    # Create Bell state |Φ+⟩ = (|00⟩ + |11⟩)/√2
    qubit1 = Qubit.zero
    qubit2 = Qubit.zero
    
    # Apply H gate to first qubit
    qubit1 = qubit1.apply_gate(QuantumGates::H)
    
    # Apply CNOT gate
    # Simplified - in practice this would be more complex
    qubit2 = qubit2.apply_gate(QuantumGates::X)
    
    [qubit1, qubit2]
  end
  
  def self.bell_state_measurement(qubit1, qubit2)
    # Simplified Bell state measurement
    # In practice, this would require proper quantum operations
    
    measurement = rand(4)  # 00, 01, 10, or 11
    measurement
  end
  
  def self.apply_corrections(classical_info)
    # Apply Pauli corrections based on Bell measurement
    # Simplified implementation
    
    corrections = {
      0 => QuantumGates::I,
      1 => QuantumGates::X,
      2 => QuantumGates::Z,
      3 => QuantumGates::Y
    }
    
    gate = corrections[classical_info[:measurement]]
    classical_info[:entangled_state].apply_gate(gate)
  end
  
  def self.calculate_error_rate(alice_keys, bob_keys)
    return 0.0 if alice_keys.empty?
    
    errors = alice_keys.zip(bob_keys).count { |a, b| a != b }
    errors.to_f / alice_keys.length
  end
end
```

### Quantum Machine Learning

```ruby
class QuantumMachineLearning
  def initialize
    @quantum_features = []
    @classical_labels = []
  end
  
  def quantum_feature_map(data_points)
    # Map classical data to quantum feature space
    n = data_points.first.length
    num_qubits = Math.log2(n).ceil
    
    @quantum_features = data_points.map do |point|
      # Normalize data point
      normalized = normalize_data(point)
      
      # Create quantum state
      state_vector = Array.new(2**num_qubits, 0)
      normalized.each_with_index do |value, i|
        state_vector[i] = value
      end
      
      QuantumRegister.from_state(state_vector)
    end
  end
  
  def quantum_kernel_matrix
    n = @quantum_features.length
    kernel = Array.new(n) { Array.new(n, 0) }
    
    n.times do |i|
      n.times do |j|
        # Calculate inner product of quantum states
        state_i = @quantum_features[i].to_state_vector
        state_j = @quantum_features[j].to_state_vector
        
        kernel[i][j] = inner_product(state_i, state_j)
      end
    end
    
    kernel
  end
  
  def quantum_support_vector_machine(training_data, labels)
    # Quantum SVM using quantum kernel
    kernel = quantum_kernel_matrix
    
    # Simplified SVM training (would use proper optimization in practice)
    alpha = Array.new(training_data.length, 1.0)
    bias = 0.0
    
    {
      kernel: kernel,
      alpha: alpha,
      bias: bias,
      support_vectors: training_data
    }
  end
  
  def quantum_neural_network(input_size, hidden_size, output_size)
    # Quantum neural network with quantum layers
    QuantumNeuralNetwork.new(input_size, hidden_size, output_size)
  end
  
  def variational_quantum_circuit(circuit_params)
    # Variational quantum circuit for machine learning
    circuit = VariationalCircuit.new
    
    # Add parameterized gates
    circuit_params.each_with_index do |param, i|
      circuit.add_parameterized_gate(:rx, param, i)
      circuit.add_parameterized_gate(:ry, param, i + 1)
      circuit.add_parameterized_gate(:rz, param, i + 2)
    end
    
    circuit
  end
  
  def quantum_classification(data_points, labels)
    # Quantum classification using variational circuits
    num_qubits = Math.log2(data_points.first.length).ceil
    circuit = variational_quantum_circuit(Array.new(num_qubits * 3) { rand * 2 * Math::PI })
    
    predictions = data_points.map do |point|
      # Prepare quantum state
      state = prepare_quantum_state(point)
      
      # Apply variational circuit
      output_state = circuit.execute(state)
      
      # Classify based on measurement
      classify_measurement(output_state)
    end
    
    predictions
  end
  
  private
  
  def normalize_data(point)
    max_val = point.max
    min_val = point.min
    range = max_val - min_val
    
    point.map do |value|
      if range > 0
        (value - min_val) / range
      else
        0.0
      end
    end
  end
  
  def inner_product(state1, state2)
    state1.zip(state2).sum { |a, b| a * b.conjugate }.real
  end
  
  def prepare_quantum_state(data_point)
    state_vector = Array.new(2**data_point.length, 0)
    data_point.each_with_index do |value, i|
      state_vector[i] = value
    end
    
    QuantumRegister.from_state(state_vector)
  end
  
  def classify_measurement(state)
    measurement = state.measure
    measurement % 2  # Binary classification
  end
end

class QuantumNeuralNetwork
  def initialize(input_size, hidden_size, output_size)
    @input_size = input_size
    @hidden_size = hidden_size
    @output_size = output_size
    
    @input_weights = initialize_weights(input_size, hidden_size)
    @output_weights = initialize_weights(hidden_size, output_size)
    
    @quantum_layer = QuantumLayer.new(hidden_size)
  end
  
  def forward(input_data)
    # Classical to quantum input
    quantum_input = classical_to_quantum(input_data)
    
    # Quantum layer processing
    quantum_hidden = @quantum_layer.forward(quantum_input)
    
    # Quantum to classical output
    classical_hidden = quantum_to_classical(quantum_hidden)
    
    # Classical output layer
    output = matrix_multiply(classical_hidden, @output_weights)
    
    output
  end
  
  def train(training_data, labels, epochs = 100)
    epochs.times do |epoch|
      training_data.each_with_index do |input, i|
        output = forward(input)
        error = calculate_error(output, labels[i])
        
        # Backpropagation (simplified)
        update_weights(error)
        
        puts "Epoch #{epoch + 1}, Sample #{i + 1}: Error = #{error}" if (epoch + 1) % 10 == 0
      end
    end
  end
  
  private
  
  def initialize_weights(rows, cols)
    Array.new(rows) { Array.new(cols) { rand(-1.0..1.0) } }
  end
  
  def classical_to_quantum(classical_data)
    # Convert classical data to quantum state
    state_vector = Array.new(2**@input_size, 0)
    classical_data.each_with_index do |value, i|
      state_vector[i] = value
    end
    
    QuantumRegister.from_state(state_vector)
  end
  
  def quantum_to_classical(quantum_state)
    # Convert quantum state to classical data
    state_vector = quantum_state.to_state_vector
    state_vector[0...@hidden_size]
  end
  
  def matrix_multiply(vector, matrix)
    Array.new(matrix.first.length) do |j|
      vector.zip(matrix).sum { |v, row| v * row[j] }
    end
  end
  
  def calculate_error(output, target)
    mean_squared_error(output, target)
  end
  
  def mean_squared_error(predicted, actual)
    predicted.zip(actual).sum { |p, a| (p - a)**2 } / predicted.length
  end
  
  def update_weights(error)
    # Simplified weight update
    learning_rate = 0.01
    
    @input_weights = update_weight_matrix(@input_weights, learning_rate, error)
    @output_weights = update_weight_matrix(@output_weights, learning_rate, error)
  end
  
  def update_weight_matrix(weights, learning_rate, error)
    weights.map do |row|
      row.map { |weight| weight - learning_rate * error * weight }
    end
  end
end

class QuantumLayer
  def initialize(size)
    @size = size
    @weights = Array.new(size) { rand(-1.0..1.0) }
  end
  
  def forward(quantum_input)
    # Apply quantum operations
    apply_quantum_gates(quantum_input)
  end
  
  private
  
  def apply_quantum_gates(quantum_register)
    @weights.each_with_index do |weight, i|
      angle = weight * Math::PI
      gate = QuantumGates.rz(angle)
      quantum_register.apply_gate(gate, i)
    end
    
    quantum_register
  end
end

class VariationalCircuit
  def initialize
    @gates = []
    @parameters = []
  end
  
  def add_parameterized_gate(gate_type, parameter, qubit)
    @gates << { type: gate_type, param: parameter, qubit: qubit }
    @parameters << parameter
  end
  
  def execute(initial_state)
    state = initial_state
    
    @gates.each do |gate|
      case gate[:type]
      when :rx
        gate_matrix = QuantumGates.rx(gate[:param])
        state.apply_gate(gate_matrix, gate[:qubit])
      when :ry
        gate_matrix = QuantumGates.ry(gate[:param])
        state.apply_gate(gate_matrix, gate[:qubit])
      when :rz
        gate_matrix = QuantumGates.rz(gate[:param])
        state.apply_gate(gate_matrix, gate[:qubit])
      end
    end
    
    state
  end
end
```

## Practice Exercises

### Exercise 1: Quantum Circuit Simulator
Build a complete quantum circuit simulator with:
- Visual interface for circuit design
- Real-time state visualization
- Support for common quantum gates
- Measurement and analysis tools

### Exercise 2: Quantum Algorithm Implementation
Implement advanced quantum algorithms:
- Full Shor's algorithm implementation
- Quantum error correction codes
- Quantum cryptography protocols
- Performance optimization

### Exercise 3: Quantum Machine Learning
Create a quantum machine learning framework:
- Quantum neural networks
- Quantum support vector machines
- Variational quantum circuits
- Hybrid classical-quantum models

### Exercise 4: Quantum Cryptography System
Build a complete quantum cryptography system:
- BB84 key exchange
- Quantum teleportation
- Superdense coding
- Security analysis

---

**Ready to explore the cutting edge of computing? Let's dive into the quantum realm! ⚛**
