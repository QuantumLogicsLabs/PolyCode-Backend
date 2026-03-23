# Quantum Algorithms in Ruby

## Overview

Quantum algorithms leverage quantum mechanical phenomena like superposition and entanglement to solve computational problems more efficiently than classical algorithms. While Ruby is not typically used for quantum computing, we can simulate quantum concepts and implement quantum-inspired algorithms.

## Quantum Computing Basics

### Qubit Representation
```ruby
class Qubit
  attr_accessor :alpha, :beta

  def initialize(alpha = 1.0, beta = 0.0)
    @alpha = alpha
    @beta = beta
    normalize
  end

  def normalize
    norm = Math.sqrt(@alpha.abs2 + @beta.abs2)
    @alpha /= norm
    @beta /= norm
  end

  def measure
    prob_zero = @alpha.abs2
    rand < prob_zero ? 0 : 1
  end

  def apply_gate(gate)
    new_alpha = gate[0][0] * @alpha + gate[0][1] * @beta
    new_beta = gate[1][0] * @alpha + gate[1][1] * @beta
    
    @alpha = new_alpha
    @beta = new_beta
    normalize
  end

  def to_s
    "#{@alpha.round(3)}|0⟩ + #{@beta.round(3)}|1⟩"
  end
end

# Quantum Gates
module QuantumGates
  PAULI_X = [[0, 1], [1, 0]]  # NOT gate
  PAULI_Y = [[0, -1i], [1i, 0]]
  PAULI_Z = [[1, 0], [0, -1]]
  HADAMARD = [[1/Math.sqrt(2), 1/Math.sqrt(2)], 
              [1/Math.sqrt(2), -1/Math.sqrt(2)]]
  IDENTITY = [[1, 0], [0, 1]]
end
```

## Quantum Circuit Simulator

### Quantum Circuit Class
```ruby
class QuantumCircuit
  def initialize(num_qubits)
    @num_qubits = num_qubits
    @qubits = Array.new(num_qubits) { Qubit.new }
    @operations = []
  end

  def apply_hadamard(qubit_index)
    @operations << { type: :hadamard, qubit: qubit_index }
    @qubits[qubit_index].apply_gate(QuantumGates::HADAMARD)
  end

  def apply_pauli_x(qubit_index)
    @operations << { type: :pauli_x, qubit: qubit_index }
    @qubits[qubit_index].apply_gate(QuantumGates::PAULI_X)
  end

  def apply_pauli_z(qubit_index)
    @operations << { type: :pauli_z, qubit: qubit_index }
    @qubits[qubit_index].apply_gate(QuantumGates::PAULI_Z)
  end

  def apply_cnot(control_qubit, target_qubit)
    @operations << { type: :cnot, control: control_qubit, target: target_qubit }
    
    # Simplified CNOT implementation
    if @qubits[control_qubit].measure == 1
      @qubits[target_qubit].apply_gate(QuantumGates::PAULI_X)
    end
  end

  def measure_all
    @operations << { type: :measure_all }
    @qubits.map(&:measure)
  end

  def measure_qubit(qubit_index)
    @operations << { type: :measure, qubit: qubit_index }
    @qubits[qubit_index].measure
  end

  def get_state
    @qubits.map(&:to_s)
  end

  def print_circuit
    puts "Quantum Circuit (#{@num_qubits} qubits):"
    @operations.each_with_index do |op, i|
      case op[:type]
      when :hadamard
        puts "  #{i}: H(q#{op[:qubit]})"
      when :pauli_x
        puts "  #{i}: X(q#{op[:qubit]})"
      when :pauli_z
        puts "  #{i}: Z(q#{op[:qubit]})"
      when :cnot
        puts "  #{i}: CNOT(q#{op[:control]} → q#{op[:target]})"
      when :measure
        puts "  #{i}: MEASURE(q#{op[:qubit]})"
      when :measure_all
        puts "  #{i}: MEASURE_ALL"
      end
    end
  end
end
```

## Quantum Algorithms

### Grover's Search Algorithm
```ruby
class GroverSearch
  def initialize(database_size)
    @n = Math.log2(database_size).ceil
    @oracle = nil
    @iterations = Math.sqrt(database_size).to_i
  end

  def set_oracle(&block)
    @oracle = block
  end

  def search
    circuit = QuantumCircuit.new(@n)
    
    # Initialize superposition
    @n.times { |i| circuit.apply_hadamard(i) }
    
    # Grover iterations
    @iterations.times do
      # Oracle phase
      apply_oracle(circuit)
      
      # Diffusion operator
      apply_diffusion(circuit)
    end
    
    # Measure result
    result = circuit.measure_all
    binary_result = result.reverse.join('')
    binary_result.to_i(2)
  end

  private

  def apply_oracle(circuit)
    # Simplified oracle - mark the target state
    @n.times do |i|
      if @oracle.call(i)
        circuit.apply_pauli_z(i)
      end
    end
  end

  def apply_diffusion(circuit)
    # Simplified diffusion operator
    @n.times do |i|
      circuit.apply_hadamard(i)
      circuit.apply_pauli_z(i)
      circuit.apply_hadamard(i)
    end
  end
end

# Usage example
def demo_grover_search
  puts "Grover's Search Algorithm Demo"
  puts "=" * 40
  
  # Search for number 7 in database of 16 items
  grover = GroverSearch.new(16)
  grover.set_oracle { |index| index == 7 }
  
  found = grover.search
  puts "Found item: #{found}"
  puts "Expected: 7"
  puts "Success: #{found == 7 ? 'Yes' : 'No'}"
end
```

### Quantum Fourier Transform
```ruby
class QuantumFourierTransform
  def initialize(num_qubits)
    @n = num_qubits
  end

  def apply_qft(circuit)
    @n.times do |j|
      circuit.apply_hadamard(j)
      
      # Controlled phase rotations
      (j + 1...@n).each do |k|
        angle = Math::PI / (2 ** (k - j))
        apply_controlled_phase(circuit, k, j, angle)
      end
    end
    
    # Swap qubits (simplified)
    swap_qubits(circuit)
  end

  def inverse_qft(circuit)
    # Apply QFT with negative angles
    @n.times do |j|
      # Reverse controlled phase rotations
      (@n - 1 - j).times do |k|
        angle = -Math::PI / (2 ** (k + 1))
        apply_controlled_phase(circuit, j, @n - 2 - k, angle)
      end
      
      circuit.apply_hadamard(@n - 1 - j)
    end
  end

  def period_finding(function, n)
    circuit = QuantumCircuit.new(2 * n)
    
    # Initialize first register in superposition
    n.times { |i| circuit.apply_hadamard(i) }
    
    # Apply function (simplified)
    apply_function(circuit, function, n)
    
    # Apply inverse QFT to first register
    qft = QuantumFourierTransform.new(n)
    qft.inverse_qft(circuit)
    
    # Measure first register
    first_register = circuit.measure_all[0...n]
    binary_to_fraction(first_register)
  end

  private

  def apply_controlled_phase(circuit, control, target, angle)
    # Simplified controlled phase gate
    gate = [[1, 0], [0, Math.exp(1i * angle)]]
    
    # This is a simplified implementation
    circuit.apply_pauli_z(target) if rand < 0.5
  end

  def swap_qubits(circuit)
    # Simplified swap operation
    (@n / 2).times do |i|
      circuit.apply_cnot(i, @n - 1 - i)
      circuit.apply_cnot(@n - 1 - i, i)
      circuit.apply_cnot(i, @n - 1 - i)
    end
  end

  def apply_function(circuit, function, n)
    # Simplified function application
    n.times do |i|
      result = function.call(i)
      if result == 1
        circuit.apply_pauli_x(n + i)
      end
    end
  end

  def binary_to_fraction(binary_bits)
    numerator = binary_bits.reverse.join('').to_i(2)
    denominator = 2 ** binary_bits.length
    Rational(numerator, denominator)
  end
end
```

### Shor's Algorithm (Simplified)
```ruby
class ShorAlgorithm
  def initialize(number_to_factor)
    @n = number_to_factor
    @qubits = Math.log2(@n).ceil * 2
  end

  def factor
    puts "Attempting to factor #{@n}..."
    
    # Step 1: Pick random a < n
    a = rand(2...@n)
    puts "Chosen a = #{a}"
    
    # Step 2: Check if a and n are coprime
    gcd = gcd(a, @n)
    if gcd > 1
      puts "Found factor: #{gcd}"
      return [gcd, @n / gcd]
    end
    
    # Step 3: Find period of a^x mod n
    period = find_period(a)
    puts "Found period: #{period}"
    
    # Step 4: Use period to find factors
    if period.odd?
      puts "Period is odd, trying again..."
      return factor
    end
    
    factor1 = gcd(a**(period/2) - 1, @n)
    factor2 = gcd(a**(period/2) + 1, @n)
    
    if factor1 == 1 || factor1 == @n
      puts "Failed to find non-trivial factors"
      return nil
    end
    
    puts "Found factors: #{factor1} and #{factor2}"
    [factor1, factor2]
  end

  private

  def gcd(a, b)
    while b != 0
      a, b = b, a % b
    end
    a
  end

  def find_period(a)
    # Simplified period finding (classical simulation)
    current = 1
    period = 0
    
    loop do
      period += 1
      current = (current * a) % @n
      break if current == 1
    end
    
    period
  end
end

# Usage example
def demo_shor_algorithm
  puts "Shor's Algorithm Demo (Classical Simulation)"
  puts "=" * 50
  
  # Factor 15 (should find 3 and 5)
  shor = ShorAlgorithm.new(15)
  factors = shor.factor
  
  if factors
    puts "Success! Factors: #{factors[0]} × #{factors[1]} = #{factors[0] * factors[1]}"
  else
    puts "Failed to find factors"
  end
end
```

## Quantum Error Correction

### Simple Error Correction Code
```ruby
class QuantumErrorCorrection
  def initialize
    @error_rates = {
      bit_flip: 0.01,
      phase_flip: 0.01,
      depolarizing: 0.005
    }
  end

  def apply_three_qubit_bit_flip_code(qubit)
    # Encode logical qubit into three physical qubits
    encoded = [
      Qubit.new(qubit.alpha, qubit.beta),
      Qubit.new(qubit.alpha, qubit.beta),
      Qubit.new(qubit.alpha, qubit.beta)
    ]
    
    # Simulate errors
    encoded = simulate_errors(encoded)
    
    # Error detection and correction
    corrected = detect_and_correct_bit_flip(encoded)
    corrected
  end

  def apply_shor_code(qubit)
    # Shor's 9-qubit code (simplified)
    encoded = Array.new(9) { Qubit.new(qubit.alpha, qubit.beta) }
    
    # Apply encoding circuit (simplified)
    encoded = encode_shor_code(encoded)
    
    # Simulate errors
    encoded = simulate_errors(encoded)
    
    # Decode and correct
    decoded = decode_shor_code(encoded)
    decoded
  end

  private

  def simulate_errors(qubits)
    qubits.map do |qubit|
      # Bit flip error
      if rand < @error_rates[:bit_flip]
        qubit.apply_gate(QuantumGates::PAULI_X)
      end
      
      # Phase flip error
      if rand < @error_rates[:phase_flip]
        qubit.apply_gate(QuantumGates::PAULI_Z)
      end
      
      qubit
    end
  end

  def detect_and_correct_bit_flip(qubits)
    # Majority vote for bit flip correction
    measurements = qubits.map(&:measure)
    
    if measurements[0] == measurements[1]
      target_state = measurements[0]
    elsif measurements[1] == measurements[2]
      target_state = measurements[1]
    else
      target_state = measurements[0]
    end
    
    # Correct qubits
    qubits.each_with_index do |qubit, i|
      if measurements[i] != target_state
        qubit.apply_gate(QuantumGates::PAULI_X)
      end
    end
    
    qubits
  end

  def encode_shor_code(qubits)
    # Simplified Shor encoding
    # In reality, this would involve a complex quantum circuit
    qubits
  end

  def decode_shor_code(qubits)
    # Simplified Shor decoding
    # In reality, this would involve syndrome measurement and correction
    qubits[0]  # Return the logical qubit
  end
end
```

## Quantum Machine Learning

### Quantum-Inspired Neural Network
```ruby
class QuantumNeuralNetwork
  def initialize(input_size, hidden_size, output_size)
    @input_size = input_size
    @hidden_size = hidden_size
    @output_size = output_size
    
    # Initialize quantum-inspired weights
    @weights_input_hidden = initialize_quantum_weights(input_size, hidden_size)
    @weights_hidden_output = initialize_quantum_weights(hidden_size, output_size)
    
    @biases_hidden = Array.new(hidden_size) { rand(-1.0..1.0) }
    @biases_output = Array.new(output_size) { rand(-1.0..1.0) }
  end

  def forward(input)
    # Quantum-inspired activation function
    hidden = quantum_activation(input, @weights_input_hidden, @biases_hidden)
    output = quantum_activation(hidden, @weights_hidden_output, @biases_output)
    output
  end

  def train(inputs, targets, epochs = 1000, learning_rate = 0.01)
    epochs.times do |epoch|
      total_error = 0
      
      inputs.each_with_index do |input, i|
        # Forward pass
        hidden = quantum_activation(input, @weights_input_hidden, @biases_hidden)
        output = quantum_activation(hidden, @weights_hidden_output, @biases_output)
        
        # Calculate error
        error = calculate_error(output, targets[i])
        total_error += error
        
        # Backward pass (simplified)
        update_weights(input, hidden, output, targets[i], learning_rate)
      end
      
      puts "Epoch #{epoch + 1}, Error: #{total_error.round(6)}" if (epoch + 1) % 100 == 0
    end
  end

  private

  def initialize_quantum_weights(rows, cols)
    Array.new(rows) do
      Array.new(cols) do
        # Quantum-inspired random initialization
        angle = rand(0..2 * Math::PI)
        [Math.cos(angle), Math.sin(angle)]
      end
    end
  end

  def quantum_activation(input, weights, biases)
    hidden = []
    
    weights[0].length.times do |j|
      # Quantum-inspired computation
      real_part = biases[j]
      imag_part = 0
      
      input.each_with_index do |input_val, i|
        weight_real, weight_imag = weights[i][j]
        real_part += input_val * weight_real
        imag_part += input_val * weight_imag
      end
      
      # Quantum measurement
      amplitude = Math.sqrt(real_part**2 + imag_part**2)
      phase = Math.atan2(imag_part, real_part)
      
      # Quantum-inspired activation
      hidden << amplitude * Math.tanh(phase)
    end
    
    hidden
  end

  def calculate_error(output, target)
    error = 0
    output.each_with_index do |val, i|
      error += (val - target[i])**2
    end
    error / output.length
  end

  def update_weights(input, hidden, output, target, learning_rate)
    # Simplified weight update (gradient descent)
    # In a real quantum neural network, this would involve quantum gradients
    
    output.each_with_index do |output_val, k|
      error = output_val - target[k]
      
      hidden.each_with_index do |hidden_val, j|
        weight_update = error * hidden_val * learning_rate
        
        # Update quantum weights
        @weights_hidden_output[j][k][0] -= weight_update
        @weights_hidden_output[j][k][1] *= 0.99  # Decay imaginary part
      end
    end
  end
end

# Usage example
def demo_quantum_neural_network
  puts "Quantum-Inspired Neural Network Demo"
  puts "=" * 40
  
  # XOR problem
  inputs = [[0, 0], [0, 1], [1, 0], [1, 1]]
  targets = [[0], [1], [1], [0]]
  
  qnn = QuantumNeuralNetwork.new(2, 4, 1)
  qnn.train(inputs, targets, 1000)
  
  puts "\nPredictions:"
  inputs.each_with_index do |input, i|
    prediction = qnn.forward(input)[0]
    puts "Input: #{input} → Output: #{prediction.round(3)} (Target: #{targets[i][0]})"
  end
end
```

## Quantum Cryptography

### Quantum Key Distribution (BB84 Protocol)
```ruby
class QuantumKeyDistribution
  def initialize
    @bases = { rectilinear: ['+', 'x'], diagonal: ['/', '\\'] }
    @states = {
      '+' => [[1, 0], [0, 0]],    # |0⟩
      'x' => [[0, 0], [0, 1]],    # |1⟩
      '/' => [[1/Math.sqrt(2), 0], [1/Math.sqrt(2), 0]],  # |+⟩
      '\\' => [[1/Math.sqrt(2), 0], [-1/Math.sqrt(2), 0]] # |−⟩
    }
  end

  def generate_quantum_key(length)
    alice_bits = Array.new(length) { rand(2) }
    alice_bases = Array.new(length) { [:rectilinear, :diagonal].sample }
    
    # Alice prepares qubits
    qubits = alice_bits.map.with_index do |bit, i|
      basis = alice_bases[i]
      state = bit == 0 ? @bases[basis][0] : @bases[basis][1]
      prepare_qubit(state)
    end
    
    # Bob measures qubits
    bob_bases = Array.new(length) { [:rectilinear, :diagonal].sample }
    bob_bits = qubits.map.with_index do |qubit, i|
      measure_qubit(qubit, bob_bases[i])
    end
    
    # Sift key (keep only matching bases)
    sifted_key = []
    alice_bits.each_with_index do |bit, i|
      if alice_bases[i] == bob_bases[i]
        sifted_key << bit
      end
    end
    
    # Error checking (simplified)
    sample_size = [sifted_key.length / 4, 10].min
    sample_indices = sifted_key.length.times.to_a.sample(sample_size)
    
    puts "Generated quantum key of length #{sifted_key.length}"
    puts "Sample check: #{sample_indices.map { |i| sifted_key[i] }.join('')}"
    
    sifted_key
  end

  private

  def prepare_qubit(state_symbol)
    state = @states[state_symbol]
    Qubit.new(state[0][0], state[1][0])
  end

  def measure_qubit(qubit, basis)
    # Simplified measurement
    if basis == :rectilinear
      qubit.measure
    else
      # Diagonal basis measurement (simplified)
      qubit.apply_gate(QuantumGates::HADAMARD)
      qubit.measure
    end
  end
end
```

## Performance Analysis

### Quantum vs Classical Performance
```ruby
class QuantumPerformanceAnalyzer
  def initialize
    @results = {}
  end

  def benchmark_search_algorithms(size)
    puts "Benchmarking Search Algorithms (Size: #{size})"
    puts "=" * 50
    
    # Classical linear search
    classical_time = benchmark_classical_search(size)
    
    # Quantum Grover's search (simulated)
    quantum_time = benchmark_quantum_search(size)
    
    @results[:search] = {
      classical: classical_time,
      quantum: quantum_time,
      speedup: classical_time / quantum_time
    }
    
    puts "Classical search: #{classical_time.round(6)}s"
    puts "Quantum search: #{quantum_time.round(6)}s"
    puts "Speedup: #{@results[:search][:speedup].round(2)}x"
  end

  def benchmark_factoring_algorithms(number)
    puts "Benchmarking Factoring Algorithms (Number: #{number})"
    puts "=" * 50
    
    # Classical trial division
    classical_time = benchmark_classical_factoring(number)
    
    # Quantum Shor's algorithm (simulated)
    quantum_time = benchmark_quantum_factoring(number)
    
    @results[:factoring] = {
      classical: classical_time,
      quantum: quantum_time,
      speedup: classical_time / quantum_time
    }
    
    puts "Classical factoring: #{classical_time.round(6)}s"
    puts "Quantum factoring: #{quantum_time.round(6)}s"
    puts "Speedup: #{@results[:factoring][:speedup].round(2)}x"
  end

  def generate_report
    puts "\n" + "=" * 60
    puts "QUANTUM ALGORITHM PERFORMANCE REPORT"
    puts "=" * 60
    
    @results.each do |algorithm, data|
      puts "\n#{algorithm.to_s.capitalize}:"
      puts "  Classical: #{data[:classical].round(6)}s"
      puts "  Quantum: #{data[:quantum].round(6)}s"
      puts "  Speedup: #{data[:speedup].round(2)}x"
    end
  end

  private

  def benchmark_classical_search(size)
    start_time = Time.now
    
    # Linear search
    data = Array.new(size) { rand(size) }
    target = data.sample
    data.index(target)
    
    Time.now - start_time
  end

  def benchmark_quantum_search(size)
    start_time = Time.now
    
    # Simulated Grover's search
    grover = GroverSearch.new(size)
    grover.set_oracle { |index| index == target }
    grover.search
    
    Time.now - start_time
  end

  def benchmark_classical_factoring(number)
    start_time = Time.now
    
    # Trial division
    factors = []
    (2..Math.sqrt(number).to_i).each do |i|
      if number % i == 0
        factors << i
        factors << number / i
      end
    end
    
    Time.now - start_time
  end

  def benchmark_quantum_factoring(number)
    start_time = Time.now
    
    # Simulated Shor's algorithm
    shor = ShorAlgorithm.new(number)
    shor.factor
    
    Time.now - start_time
  end
end
```

## Best Practices

1. **Simulation Limitations**: Ruby simulations are educational, not true quantum computing
2. **Complex Numbers**: Use complex arithmetic for quantum states
3. **Normalization**: Always normalize quantum states after operations
4. **Measurement**: Understand probabilistic nature of quantum measurement
5. **Entanglement**: Model quantum correlations carefully
6. **Error Correction**: Implement quantum error detection and correction
7. **Performance**: Optimize quantum circuit simulations for large systems

## Conclusion

While Ruby is not the primary language for quantum computing, it provides excellent tools for simulating and understanding quantum concepts. These implementations help in learning quantum algorithms and preparing for real quantum programming with specialized languages like Q# or Qiskit.

## Further Reading

- [Quantum Computing for Computer Scientists](https://arxiv.org/abs/0908.3403)
- [IBM Quantum Experience](https://quantum-computing.ibm.com/)
- [Microsoft Quantum Development Kit](https://docs.microsoft.com/en-us/azure/quantum/)
- [Qiskit Documentation](https://qiskit.org/documentation/)
