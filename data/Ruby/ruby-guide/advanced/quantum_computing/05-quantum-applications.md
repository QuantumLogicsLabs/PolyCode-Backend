# Quantum Applications in Ruby

## Overview

Quantum computing promises revolutionary applications across various domains. While Ruby is not typically used for production quantum computing, it serves as an excellent platform for understanding, prototyping, and demonstrating quantum applications and their potential impact.

## Quantum Cryptography

### Quantum Key Distribution (QKD) Implementation
```ruby
class QuantumKeyDistributionSystem
  def initialize
    @alice_bases = []
    @alice_bits = []
    @bob_bases = []
    @bob_bits = []
    @shared_key = []
    @eavesdropper_detected = false
  end

  def bb84_protocol(key_length = 256)
    puts "Running BB84 Quantum Key Distribution Protocol"
    puts "Target key length: #{key_length} bits"
    
    # Step 1: Alice generates random bits and bases
    generate_alice_data(key_length * 2)  # Generate extra for sifting
    
    # Step 2: Alice prepares and sends qubits
    qubits = prepare_quantum_states
    
    # Step 3: Bob measures received qubits
    bob_measures_qubits(qubits)
    
    # Step 4: Basis reconciliation
    sifted_key = sift_key
    
    # Step 5: Error rate estimation
    error_rate = estimate_error_rate(sifted_key)
    
    # Step 6: Privacy amplification
    final_key = privacy_amplification(sifted_key, error_rate)
    
    {
      final_key: final_key,
      key_length: final_key.length,
      error_rate: error_rate,
      eavesdropper_detected: @eavesdropper_detected
    }
  end

  def simulate_eavesdropping(intercept_rate = 0.1)
    puts "Simulating eavesdropping with #{(intercept_rate * 100).round(1)}% interception rate"
    
    # Eve intercepts and measures some qubits
    intercepted_positions = []
    @alice_bits.each_with_index do |bit, i|
      if rand < intercept_rate
        intercepted_positions << i
        # Eve's measurement disturbs the state
        if @alice_bases[i] != [:rectilinear, :diagonal].sample
          @eavesdropper_detected = true
        end
      end
    end
    
    intercepted_positions.length
  end

  def analyze_security(key_length = 1024)
    puts "Analyzing QKD security for #{key_length}-bit key"
    
    security_analysis = {}
    
    # Test different eavesdropping rates
    [0.0, 0.05, 0.1, 0.2, 0.3].each do |eve_rate|
      puts "Testing eavesdropping rate: #{(eve_rate * 100).round(1)}%"
      
      results = []
      10.times do  # Multiple trials
        result = bb84_protocol(key_length)
        simulate_eavesdropping(eve_rate)
        results << result[:error_rate]
      end
      
      avg_error_rate = results.sum / results.length
      security_analysis[eve_rate] = avg_error_rate
      
      puts "  Average error rate: #{(avg_error_rate * 100).round(2)}%"
    end
    
    security_analysis
  end

  private

  def generate_alice_data(length)
    @alice_bits = Array.new(length) { rand(2) }
    @alice_bases = Array.new(length) { [:rectilinear, :diagonal].sample }
  end

  def prepare_quantum_states
    @alice_bits.map.with_index do |bit, i|
      basis = @alice_bases[i]
      prepare_single_qubit(bit, basis)
    end
  end

  def prepare_single_qubit(bit, basis)
    # Prepare quantum state based on bit and basis
    if basis == :rectilinear
      bit == 0 ? { alpha: 1, beta: 0 } : { alpha: 0, beta: 1 }  # |0⟩ or |1⟩
    else
      bit == 0 ? { alpha: 1/Math.sqrt(2), beta: 1/Math.sqrt(2) } : 
                 { alpha: 1/Math.sqrt(2), beta: -1/Math.sqrt(2) }  # |+⟩ or |−⟩
    end
  end

  def bob_measures_qubits(qubits)
    @bob_bases = Array.new(qubits.length) { [:rectilinear, :diagonal].sample }
    @bob_bits = []
    
    qubits.each_with_index do |qubit, i|
      @bob_bits << measure_qubit(qubit, @bob_bases[i])
    end
  end

  def measure_qubit(qubit, basis)
    # Simulate quantum measurement
    if basis == :rectilinear
      # Measure in computational basis
      prob_0 = qubit[:alpha].abs2
      rand < prob_0 ? 0 : 1
    else
      # Measure in diagonal basis
      # Transform to diagonal basis, then measure
      plus_amplitude = (qubit[:alpha] + qubit[:beta]) / Math.sqrt(2)
      minus_amplitude = (qubit[:alpha] - qubit[:beta]) / Math.sqrt(2)
      
      prob_plus = plus_amplitude.abs2
      rand < prob_plus ? 0 : 1  # 0 for |+⟩, 1 for |−⟩
    end
  end

  def sift_key
    @shared_key = []
    
    @alice_bits.each_with_index do |bit, i|
      if @alice_bases[i] == @bob_bases[i]
        @shared_key << bit
      end
    end
    
    @shared_key
  end

  def estimate_error_rate(sifted_key)
    # Compare a subset of the key to estimate error rate
    sample_size = [sifted_key.length / 4, 50].min
    sample_indices = sifted_key.length.times.to_a.sample(sample_size)
    
    errors = 0
    sample_indices.each do |i|
      # In a real system, Alice and Bob would compare these bits
      # Here we simulate potential errors from eavesdropping
      if @eavesdropper_detected && rand < 0.25  # 25% error rate if eavesdropped
        errors += 1
      end
    end
    
    errors.to_f / sample_size
  end

  def privacy_amplification(sifted_key, error_rate)
    # Simple privacy amplification: hash the key
    if error_rate > 0.1
      puts "High error rate detected! Possible eavesdropping."
      return []
    end
    
    # Use a simple hash function for privacy amplification
    key_hash = sifted_key.hash
    binary_hash = key_hash.to_s(2)
    
    # Take only the required number of bits
    final_length = [sifted_key.length / 2, 128].min
    binary_hash[-final_length..-1].chars.map(&:to_i)
  end
end
```

### Quantum Digital Signatures
```ruby
class QuantumDigitalSignature
  def initialize
    @private_keys = {}
    @public_keys = {}
    @signatures = {}
  end

  def generate_key_pair(user_id, key_size = 256)
    puts "Generating quantum key pair for #{user_id}"
    
    # Generate quantum private key (simplified)
    private_key = Array.new(key_size) { rand(2) }
    @private_keys[user_id] = private_key
    
    # Generate corresponding public key
    public_key = generate_quantum_public_key(private_key)
    @public_keys[user_id] = public_key
    
    { private_key: private_key, public_key: public_key }
  end

  def sign_message(user_id, message)
    private_key = @private_keys[user_id]
    return nil unless private_key
    
    puts "Signing message for #{user_id}"
    
    # Generate quantum signature
    message_hash = message.hash
    signature = create_quantum_signature(private_key, message_hash)
    
    @signatures[message_hash] = {
      user_id: user_id,
      signature: signature,
      timestamp: Time.now
    }
    
    signature
  end

  def verify_signature(message, signature, user_id)
    public_key = @public_keys[user_id]
    return false unless public_key
    
    message_hash = message.hash
    stored_signature = @signatures[message_hash]
    
    return false unless stored_signature
    return false unless stored_signature[:user_id] == user_id
    
    # Verify quantum signature
    verify_quantum_signature(signature, public_key, message_hash)
  end

  def analyze_forgery_resistance(num_attempts = 1000)
    puts "Analyzing forgery resistance with #{num_attempts} attempts"
    
    successful_forgeries = 0
    
    num_attempts.times do |i|
      # Generate legitimate signature
      user_id = "user_#{i % 10}"
      generate_key_pair(user_id) unless @public_keys[user_id]
      
      message = "Test message #{i}"
      signature = sign_message(user_id, message)
      
      # Attempt forgery
      forged_signature = attempt_forgery(signature)
      
      if verify_signature(message, forged_signature, user_id)
        successful_forgeries += 1
      end
      
      if (i + 1) % 100 == 0
        puts "  Attempt #{i + 1}/#{num_attempts}, Successful forgeries: #{successful_forgeries}"
      end
    end
    
    forgery_rate = successful_forgeries.to_f / num_attempts
    puts "Forgery rate: #{(forgery_rate * 100).round(4)}%"
    
    forgery_rate
  end

  private

  def generate_quantum_public_key(private_key)
    # Generate quantum public key from private key
    # In reality, this would involve quantum measurements
    private_key.map do |bit|
      {
        basis: [:rectilinear, :diagonal].sample,
        measurement: bit
      }
    end
  end

  def create_quantum_signature(private_key, message_hash)
    # Create quantum signature using private key and message
    signature = []
    
    private_key.each_with_index do |bit, i|
      # Use message hash to determine signature parameters
      seed = (message_hash + i).hash
      basis = seed % 2 == 0 ? :rectilinear : :diagonal
      
      signature << {
        basis: basis,
        measurement: bit,
        verification: generate_verification_bit(bit, basis, message_hash)
      }
    end
    
    signature
  end

  def generate_verification_bit(bit, basis, message_hash)
    # Generate verification bit for signature
    seed = (message_hash + bit + basis.hash).hash
    seed % 2
  end

  def verify_quantum_signature(signature, public_key, message_hash)
    # Verify quantum signature against public key
    return false if signature.length != public_key.length
    
    verified_bits = 0
    
    signature.each_with_index do |sig_bit, i|
      pub_bit = public_key[i]
      
      # Check if signature matches public key
      if sig_bit[:basis] == pub_bit[:basis]
        if sig_bit[:measurement] == pub_bit[:measurement]
          verified_bits += 1
        end
      else
        # Different basis - check verification bit
        expected_verification = generate_verification_bit(
          pub_bit[:measurement], sig_bit[:basis], message_hash
        )
        
        if sig_bit[:verification] == expected_verification
          verified_bits += 1
        end
      end
    end
    
    # Accept if enough bits are verified
    verification_rate = verified_bits.to_f / signature.length
    verification_rate > 0.75  # 75% threshold
  end

  def attempt_forgery(original_signature)
    # Attempt to forge a signature
    forged_signature = []
    
    original_signature.each do |sig_bit|
      # Random guess at signature parameters
      forged_signature << {
        basis: [:rectilinear, :diagonal].sample,
        measurement: rand(2),
        verification: rand(2)
      }
    end
    
    forged_signature
  end
end
```

## Quantum Machine Learning Applications

### Quantum Neural Network for Classification
```ruby
class QuantumMLClassifier
  def initialize(num_features, num_classes)
    @num_features = num_features
    @num_classes = num_classes
    @num_qubits = num_features + num_classes
    @circuit = nil
    @parameters = nil
  end

  def train(training_data, labels, epochs = 100)
    puts "Training Quantum ML Classifier"
    puts "Training samples: #{training_data.length}"
    puts "Features: #{@num_features}, Classes: #{@num_classes}"
    
    # Initialize quantum circuit
    initialize_quantum_circuit
    
    # Training loop
    loss_history = []
    
    epochs.times do |epoch|
      total_loss = 0
      
      training_data.each_with_index do |sample, i|
        # Forward pass
        prediction = classify_sample(sample)
        
        # Calculate loss
        loss = calculate_loss(prediction, labels[i])
        total_loss += loss
        
        # Backward pass (simplified gradient descent)
        update_parameters(sample, labels[i], prediction, 0.01)
      end
      
      avg_loss = total_loss / training_data.length
      loss_history << avg_loss
      
      if (epoch + 1) % 10 == 0
        puts "Epoch #{epoch + 1}/#{epochs}, Loss: #{avg_loss.round(6)}"
      end
    end
    
    loss_history
  end

  def classify_sample(sample)
    # Encode classical data into quantum state
    quantum_state = encode_data(sample)
    
    # Apply quantum circuit
    apply_quantum_circuit(quantum_state)
    
    # Measure and decode results
    decode_measurement(quantum_state)
  end

  def evaluate(test_data, test_labels)
    puts "Evaluating classifier on #{test_data.length} test samples"
    
    correct_predictions = 0
    predictions = []
    
    test_data.each_with_index do |sample, i|
      prediction = classify_sample(sample)
      predicted_class = prediction.index(prediction.max)
      actual_class = test_labels[i]
      
      predictions << predicted_class
      
      if predicted_class == actual_class
        correct_predictions += 1
      end
    end
    
    accuracy = correct_predictions.to_f / test_data.length
    puts "Accuracy: #{(accuracy * 100).round(2)}%"
    
    {
      accuracy: accuracy,
      predictions: predictions,
      confusion_matrix: calculate_confusion_matrix(predictions, test_labels)
    }
  end

  def quantum_advantage_analysis(dataset_size)
    puts "Analyzing quantum advantage for dataset size: #{dataset_size}"
    
    # Simulate classical vs quantum performance
    classical_times = []
    quantum_times = []
    
    [100, 500, 1000, 5000, 10000].each do |size|
      # Generate synthetic data
      data = generate_synthetic_data(size, @num_features)
      labels = generate_synthetic_labels(size, @num_classes)
      
      # Classical training time (simulated)
      classical_time = simulate_classical_training_time(size, @num_features)
      classical_times << classical_time
      
      # Quantum training time (simulated)
      quantum_time = simulate_quantum_training_time(size, @num_features)
      quantum_times << quantum_time
      
      puts "  Size #{size}: Classical #{classical_time.round(3)}s, Quantum #{quantum_time.round(3)}s"
    end
    
    {
      classical_times: classical_times,
      quantum_times: quantum_times,
      speedup: classical_times.zip(quantum_times).map { |c, q| c / q }
    }
  end

  private

  def initialize_quantum_circuit
    @circuit = QuantumCircuit.new(@num_qubits)
    @parameters = Array.new(20) { rand(-Math::PI..Math::PI) }  # 20 trainable parameters
    
    # Build variational quantum circuit
    build_variational_circuit
  end

  def build_variational_circuit
    # Feature encoding
    @num_features.times do |i|
      @circuit.add_gate(:rotation_y, i, { angle: @parameters[i] })
    end
    
    # Entanglement layer
    (@num_qubits - 1).times do |i|
      @circuit.add_two_qubit_gate(:cnot, i, i + 1)
    end
    
    # Classification layer
    @num_classes.times do |i|
      qubit = @num_features + i
      @circuit.add_gate(:rotation_z, qubit, { angle: @parameters[@num_features + i] })
    end
  end

  def encode_data(sample)
    # Encode classical data into quantum state
    state = QuantumState.new(@num_qubits)
    
    sample.each_with_index do |feature, i|
      # Encode feature as rotation angle
      angle = feature * Math::PI  # Normalize to [0, π]
      rotation = [[Math.cos(angle/2), -Math.sin(angle/2)],
                  [Math.sin(angle/2), Math.cos(angle/2)]]
      state.apply_gate(rotation, [i])
    end
    
    state
  end

  def apply_quantum_circuit(state)
    # Apply the trained quantum circuit
    @circuit.operations.each do |op|
      case op[:type]
      when :rotation_y
        angle = @parameters[op[:qubit]]
        gate = [[Math.cos(angle/2), -Math.sin(angle/2)],
                [Math.sin(angle/2), Math.cos(angle/2)]]
        state.apply_gate(gate, [op[:qubit]])
      when :rotation_z
        angle = @parameters[op[:qubit]]
        gate = [[Math.exp(-1i * angle/2), 0],
                [0, Math.exp(1i * angle/2)]]
        state.apply_gate(gate, [op[:qubit]])
      when :cnot
        # Simplified CNOT implementation
        apply_cnot(state, op[:control], op[:target])
      end
    end
    
    state
  end

  def apply_cnot(state, control, target)
    # Simplified CNOT implementation
    if state.measure_qubit(control) == 1
      gate = [[0, 1], [1, 0]]  # Pauli-X
      state.apply_gate(gate, [target])
    end
  end

  def decode_measurement(state)
    # Measure class qubits and decode probabilities
    probabilities = []
    
    @num_classes.times do |i|
      qubit = @num_features + i
      # Measure probability of |1⟩ state
      prob_1 = state.get_probabilities[qubit * 2 + 1]
      probabilities << prob_1
    end
    
    probabilities
  end

  def calculate_loss(prediction, target)
    # Cross-entropy loss
    target_prob = Array.new(@num_classes, 0.01)  # Small probability for all classes
    target_prob[target] = 0.99  # High probability for target class
    
    loss = 0
    prediction.each_with_index do |pred, i|
      loss -= target_prob[i] * Math.log(pred + 1e-10)
    end
    
    loss
  end

  def update_parameters(sample, target, prediction, learning_rate)
    # Simplified parameter update (gradient descent)
    @parameters.each_with_index do |param, i|
      # Calculate gradient (simplified)
      gradient = calculate_gradient(sample, target, prediction, i)
      
      # Update parameter
      @parameters[i] -= learning_rate * gradient
    end
  end

  def calculate_gradient(sample, target, prediction, param_index)
    # Simplified gradient calculation
    # In practice, this would involve parameter-shift rule
    epsilon = 0.01
    
    # Forward pass with parameter + epsilon
    @parameters[param_index] += epsilon
    pred_plus = classify_sample(sample)
    loss_plus = calculate_loss(pred_plus, target)
    
    # Forward pass with parameter - epsilon
    @parameters[param_index] -= 2 * epsilon
    pred_minus = classify_sample(sample)
    loss_minus = calculate_loss(pred_minus, target)
    
    # Reset parameter
    @parameters[param_index] += epsilon
    
    # Gradient
    (loss_plus - loss_minus) / (2 * epsilon)
  end

  def calculate_confusion_matrix(predictions, actual_labels)
    matrix = Array.new(@num_classes) { Array.new(@num_classes, 0) }
    
    predictions.each_with_index do |pred, i|
      actual = actual_labels[i]
      matrix[actual][pred] += 1
    end
    
    matrix
  end

  def generate_synthetic_data(size, num_features)
    Array.new(size) { Array.new(num_features) { rand(-1.0..1.0) } }
  end

  def generate_synthetic_labels(size, num_classes)
    Array.new(size) { rand(num_classes) }
  end

  def simulate_classical_training_time(size, num_features)
    # Simulate classical ML training time
    # O(n * d) complexity
    base_time = 0.001
    base_time * size * num_features * 0.0001
  end

  def simulate_quantum_training_time(size, num_features)
    # Simulate quantum ML training time
    # O(√n * d) complexity (theoretical advantage)
    base_time = 0.01  # Higher overhead but better scaling
    base_time * Math.sqrt(size) * num_features * 0.0001
  end
end
```

## Quantum Optimization

### Quantum Approximate Optimization Algorithm (QAOA)
```ruby
class QuantumOptimizer
  def initialize(problem_type, problem_size)
    @problem_type = problem_type
    @problem_size = problem_size
    @num_qubits = problem_size
    @circuit = nil
    @optimal_solution = nil
    @optimal_value = nil
  end

  def solve_max_cut(graph, p = 2)
    puts "Solving Max-Cut problem using QAOA"
    puts "Graph: #{graph.length} vertices, p = #{p}"
    
    @circuit = QuantumCircuit.new(@num_qubits)
    
    # Initialize parameters
    gamma_params = Array.new(p) { rand(0..Math::PI) }
    beta_params = Array.new(p) { rand(0..Math::PI) }
    
    best_solution = nil
    best_value = -Float::INFINITY
    
    # Optimization loop
    100.times do |iteration|
      # Build QAOA circuit
      build_qaoa_circuit(graph, gamma_params, beta_params, p)
      
      # Execute circuit and get solution
      solution = execute_qaoa_circuit
      value = calculate_max_cut_value(solution, graph)
      
      # Update best solution
      if value > best_value
        best_value = value
        best_solution = solution
      end
      
      # Update parameters (simplified gradient ascent)
      gamma_params, beta_params = update_parameters(
        graph, gamma_params, beta_params, p, solution, value
      )
      
      if (iteration + 1) % 20 == 0
        puts "Iteration #{iteration + 1}: Best value = #{best_value}"
      end
    end
    
    @optimal_solution = best_solution
    @optimal_value = best_value
    
    {
      solution: best_solution,
      value: best_value,
      cut_size: best_value
    }
  end

  def solve_traveling_salesman(distances, p = 3)
    puts "Solving TSP using QAOA"
    puts "Cities: #{distances.length}, p = #{p}"
    
    @num_qubits = distances.length * distances.length  # Encoding
    @circuit = QuantumCircuit.new(@num_qubits)
    
    # Initialize parameters
    gamma_params = Array.new(p) { rand(0..Math::PI) }
    beta_params = Array.new(p) { rand(0..Math::PI) }
    
    best_route = nil
    best_distance = Float::INFINITY
    
    # Optimization loop
    50.times do |iteration|
      # Build QAOA circuit for TSP
      build_tsp_qaoa_circuit(distances, gamma_params, beta_params, p)
      
      # Execute circuit
      solution = execute_qaoa_circuit
      route = decode_tsp_solution(solution, distances.length)
      distance = calculate_tour_distance(route, distances)
      
      # Update best solution
      if distance < best_distance
        best_distance = distance
        best_route = route
      end
      
      # Update parameters
      gamma_params, beta_params = update_tsp_parameters(
        distances, gamma_params, beta_params, p, route, distance
      )
      
      if (iteration + 1) % 10 == 1
        puts "Iteration #{iteration + 1}: Best distance = #{best_distance.round(2)}"
      end
    end
    
    {
      route: best_route,
      distance: best_distance
    }
  end

  def compare_with_classical(graph)
    puts "Comparing quantum vs classical optimization"
    
    # Quantum solution
    quantum_result = solve_max_cut(graph)
    
    # Classical approximation (greedy algorithm)
    classical_result = solve_max_cut_classical(graph)
    
    puts "Quantum solution: #{quantum_result[:value]}"
    puts "Classical solution: #{classical_result[:value]}"
    
    improvement = (quantum_result[:value] - classical_result[:value]).to_f / classical_result[:value]
    puts "Quantum improvement: #{(improvement * 100).round(2)}%"
    
    {
      quantum: quantum_result,
      classical: classical_result,
      improvement: improvement
    }
  end

  private

  def build_qaoa_circuit(graph, gamma_params, beta_params, p)
    @circuit = QuantumCircuit.new(@num_qubits)
    
    # Initialize in superposition
    @num_qubits.times { |i| @circuit.add_gate(:hadamard, i) }
    
    p.times do |layer|
      # Problem unitary
      apply_problem_unitary(graph, gamma_params[layer])
      
      # Mixer unitary
      apply_mixer_unitary(beta_params[layer])
    end
  end

  def apply_problem_unitary(graph, gamma)
    # Apply problem Hamiltonian exp(-iγH)
    graph.each_with_index do |row, i|
      row.each_with_index do |weight, j|
        next if i >= j || weight == 0
        
        # Apply controlled-Z rotation for edge (i,j)
        angle = gamma * weight
        apply_controlled_z_rotation(i, j, angle)
      end
    end
  end

  def apply_mixer_unitary(beta)
    # Apply mixer Hamiltonian exp(-iβX)
    @num_qubits.times do |i|
      @circuit.add_gate(:rotation_x, i, { angle: 2 * beta })
    end
  end

  def apply_controlled_z_rotation(control, target, angle)
    # Apply controlled-Z rotation gate
    gate_matrix = [
      [1, 0, 0, 0],
      [0, 1, 0, 0],
      [0, 0, 1, 0],
      [0, 0, 0, Math.exp(1i * angle)]
    ]
    
    @circuit.add_two_qubit_gate(:controlled_phase, control, target, { angle: angle })
  end

  def execute_qaoa_circuit
    # Execute circuit and get measurement result
    state = QuantumState.new(@num_qubits)
    
    @circuit.operations.each do |op|
      case op[:type]
      when :hadamard
        state.apply_gate(QuantumGates::HADAMARD, [op[:qubit]])
      when :rotation_x
        angle = op[:params][:angle]
        gate = [[Math.cos(angle/2), -1i * Math.sin(angle/2)],
                [-1i * Math.sin(angle/2), Math.cos(angle/2)]]
        state.apply_gate(gate, [op[:qubit]])
      when :controlled_phase
        angle = op[:params][:angle]
        apply_controlled_phase_to_state(state, op[:control], op[:target], angle)
      end
    end
    
    # Measure all qubits
    measurement = state.measure_all
    measurement
  end

  def apply_controlled_phase_to_state(state, control, target, angle)
    # Simplified controlled phase application
    if state.measure_qubit(control) == 1
      gate = [[1, 0], [0, Math.exp(1i * angle)]]
      state.apply_gate(gate, [target])
    end
  end

  def calculate_max_cut_value(solution, graph)
    cut_value = 0
    
    graph.each_with_index do |row, i|
      row.each_with_index do |weight, j|
        next if i >= j || weight == 0
        
        # Check if vertices are in different partitions
        if solution[i] != solution[j]
          cut_value += weight
        end
      end
    end
    
    cut_value
  end

  def solve_max_cut_classical(graph)
    # Greedy classical algorithm for Max-Cut
    n = graph.length
    best_partition = Array.new(n, 0)
    best_value = 0
    
    # Try different initializations
    10.times do
      partition = Array.new(n) { rand(2) }
      current_value = calculate_max_cut_value(partition, graph)
      
      if current_value > best_value
        best_value = current_value
        best_partition = partition.dup
      end
    end
    
    { solution: best_partition, value: best_value }
  end

  def update_parameters(graph, gamma_params, beta_params, p, solution, value)
    # Simplified parameter update (gradient-free optimization)
    new_gamma = gamma_params.map { |g| g + 0.1 * (rand - 0.5) }
    new_beta = beta_params.map { |b| b + 0.1 * (rand - 0.5) }
    
    # Keep parameters in valid range
    new_gamma = new_gamma.map { |g| [[0, g].max, Math::PI].min }
    new_beta = new_beta.map { |b| [[0, b].max, Math::PI].min }
    
    [new_gamma, new_beta]
  end

  def build_tsp_qaoa_circuit(distances, gamma_params, beta_params, p)
    # Build QAOA circuit for TSP (simplified)
    build_qaoa_circuit([], gamma_params, beta_params, p)
  end

  def decode_tsp_solution(solution, num_cities)
    # Decode binary solution to TSP route
    # Simplified: just return a random valid route
    (0...num_cities).to_a.shuffle
  end

  def calculate_tour_distance(route, distances)
    total_distance = 0
    
    route.each_with_index do |city, i|
      next_city = route[(i + 1) % route.length]
      total_distance += distances[city][next_city]
    end
    
    total_distance
  end

  def update_tsp_parameters(distances, gamma_params, beta_params, p, route, distance)
    # Simplified parameter update for TSP
    update_parameters([], gamma_params, beta_params, p, route, distance)
  end
end
```

## Quantum Finance Applications

### Quantum Portfolio Optimization
```ruby
class QuantumPortfolioOptimizer
  def initialize(expected_returns, covariances, risk_tolerance = 0.1)
    @expected_returns = expected_returns
    @covariances = covariances
    @risk_tolerance = risk_tolerance
    @num_assets = expected_returns.length
    @num_qubits = @num_assets
  end

  def optimize_portfolio(p = 3)
    puts "Optimizing portfolio using quantum algorithm"
    puts "Assets: #{@num_assets}, Risk tolerance: #{@risk_tolerance}"
    
    # Formulate as quadratic optimization problem
    qubo_matrix = formulate_portfolio_qubo()
    
    # Solve using quantum algorithm
    solution = solve_qubo(qubo_matrix, p)
    
    # Decode solution to portfolio weights
    weights = decode_portfolio_weights(solution)
    
    # Calculate portfolio metrics
    expected_return = calculate_portfolio_return(weights)
    portfolio_risk = calculate_portfolio_risk(weights)
    sharpe_ratio = expected_return / portfolio_risk
    
    puts "Expected return: #{(expected_return * 100).round(2)}%"
    puts "Portfolio risk: #{(portfolio_risk * 100).round(2)}%"
    puts "Sharpe ratio: #{sharpe_ratio.round(4)}"
    
    {
      weights: weights,
      expected_return: expected_return,
      portfolio_risk: portfolio_risk,
      sharpe_ratio: sharpe_ratio
    }
  end

  def quantum_risk_analysis(scenarios = 1000)
    puts "Performing quantum risk analysis with #{scenarios} scenarios"
    
    # Generate quantum scenarios
    quantum_scenarios = generate_quantum_scenarios(scenarios)
    
    # Analyze portfolio performance under scenarios
    portfolio_returns = []
    portfolio_risks = []
    
    quantum_scenarios.each do |scenario|
      returns = scenario[:returns]
      risk = scenario[:risk]
      
      portfolio_returns << calculate_portfolio_return(@optimal_weights || [])
      portfolio_risks << calculate_portfolio_risk(@optimal_weights || [])
    end
    
    # Calculate risk metrics
    var_95 = calculate_value_at_risk(portfolio_returns, 0.95)
    cvar_95 = calculate_conditional_var(portfolio_returns, 0.95)
    max_drawdown = calculate_max_drawdown(portfolio_returns)
    
    {
      var_95: var_95,
      cvar_95: cvar_95,
      max_drawdown: max_drawdown,
      scenario_analysis: {
        return_distribution: portfolio_returns,
        risk_distribution: portfolio_risks
      }
    }
  end

  def compare_with_classical()
    puts "Comparing quantum vs classical portfolio optimization"
    
    # Quantum optimization
    quantum_result = optimize_portfolio()
    @optimal_weights = quantum_result[:weights]
    
    # Classical optimization (Markowitz)
    classical_result = optimize_portfolio_classical()
    
    puts "\nQuantum Portfolio:"
    puts "  Return: #{(quantum_result[:expected_return] * 100).round(2)}%"
    puts "  Risk: #{(quantum_result[:portfolio_risk] * 100).round(2)}%"
    puts "  Sharpe: #{quantum_result[:sharpe_ratio].round(4)}"
    
    puts "\nClassical Portfolio:"
    puts "  Return: #{(classical_result[:expected_return] * 100).round(2)}%"
    puts "  Risk: #{(classical_result[:portfolio_risk] * 100).round(2)}%"
    puts "  Sharpe: #{classical_result[:sharpe_ratio].round(4)}"
    
    improvement = quantum_result[:sharpe_ratio] - classical_result[:sharpe_ratio]
    puts "\nSharpe ratio improvement: #{improvement.round(4)}"
    
    {
      quantum: quantum_result,
      classical: classical_result,
      sharpe_improvement: improvement
    }
  end

  private

  def formulate_portfolio_qubo()
    # Formulate portfolio optimization as QUBO
    # Objective: maximize return - λ * risk
    n = @num_assets
    qubo = Array.new(n) { Array.new(n, 0) }
    
    (0...n).each do |i|
      # Return term (negative because we minimize)
      qubo[i][i] -= @expected_returns[i]
      
      # Risk term
      (0...n).each do |j|
        qubo[i][j] += @risk_tolerance * @covariances[i][j]
      end
    end
    
    qubo
  end

  def solve_qubo(qubo_matrix, p)
    # Solve QUBO using quantum algorithm (simplified QAOA)
    circuit = QuantumCircuit.new(@num_qubits)
    
    # Initialize parameters
    gamma = rand(0..Math::PI)
    beta = rand(0..Math::PI)
    
    # Build QAOA circuit
    @num_qubits.times { |i| circuit.add_gate(:hadamard, i) }
    
    # Apply problem unitary
    apply_qubo_unitary(circuit, qubo_matrix, gamma)
    
    # Apply mixer unitary
    @num_qubits.times { |i| circuit.add_gate(:rotation_x, i, { angle: 2 * beta }) }
    
    # Execute circuit
    state = QuantumState.new(@num_qubits)
    
    circuit.operations.each do |op|
      case op[:type]
      when :hadamard
        state.apply_gate(QuantumGates::HADAMARD, [op[:qubit]])
      when :rotation_x
        angle = op[:params][:angle]
        gate = [[Math.cos(angle/2), -1i * Math.sin(angle/2)],
                [-1i * Math.sin(angle/2), Math.cos(angle/2)]]
        state.apply_gate(gate, [op[:qubit]])
      end
    end
    
    # Measure and return result
    state.measure_all
  end

  def apply_qubo_unitary(circuit, qubo_matrix, gamma)
    # Apply QUBO Hamiltonian
    @num_qubits.times do |i|
      # Diagonal terms
      if qubo_matrix[i][i] != 0
        angle = gamma * qubo_matrix[i][i]
        circuit.add_gate(:rotation_z, i, { angle: angle })
      end
    end
    
    # Off-diagonal terms (simplified)
    (0...@num_qubits).each do |i|
      (i+1...@num_qubits).each do |j|
        if qubo_matrix[i][j] != 0
          angle = gamma * qubo_matrix[i][j]
          circuit.add_two_qubit_gate(:controlled_phase, i, j, { angle: angle })
        end
      end
    end
  end

  def decode_portfolio_weights(solution)
    # Decode binary solution to portfolio weights
    # Simplified: normalize to sum to 1
    selected_assets = solution.each_with_index.select { |bit, i| bit == 1 }.map(&:last)
    
    if selected_assets.empty?
      # Equal weight portfolio
      Array.new(@num_assets) { 1.0 / @num_assets }
    else
      weights = Array.new(@num_assets, 0)
      weight_per_asset = 1.0 / selected_assets.length
      
      selected_assets.each { |i| weights[i] = weight_per_asset }
      weights
    end
  end

  def calculate_portfolio_return(weights)
    weights.zip(@expected_returns).sum { |w, r| w * r }
  end

  def calculate_portfolio_risk(weights)
    risk = 0
    
    (0...@num_assets).each do |i|
      (0...@num_assets).each do |j|
        risk += weights[i] * weights[j] * @covariances[i][j]
      end
    end
    
    Math.sqrt(risk)
  end

  def optimize_portfolio_classical()
    # Classical Markowitz optimization (simplified)
    n = @num_assets
    
    # Equal weight portfolio as baseline
    weights = Array.new(n, 1.0 / n)
    
    expected_return = calculate_portfolio_return(weights)
    portfolio_risk = calculate_portfolio_risk(weights)
    sharpe_ratio = expected_return / portfolio_risk
    
    {
      weights: weights,
      expected_return: expected_return,
      portfolio_risk: portfolio_risk,
      sharpe_ratio: sharpe_ratio
    }
  end

  def generate_quantum_scenarios(num_scenarios)
    # Generate quantum-inspired market scenarios
    scenarios = []
    
    num_scenarios.times do
      # Quantum superposition of market states
      scenario_returns = @expected_returns.map do |ret|
        # Add quantum uncertainty
        noise = rand(-0.1..0.1)
        ret + noise
      end
      
      scenario_risk = rand(0.05..0.25)
      
      scenarios << {
        returns: scenario_returns,
        risk: scenario_risk
      }
    end
    
    scenarios
  end

  def calculate_value_at_risk(returns, confidence_level)
    sorted_returns = returns.sort
    index = ((1 - confidence_level) * returns.length).to_i
    sorted_returns[index]
  end

  def calculate_conditional_var(returns, confidence_level)
    sorted_returns = returns.sort
    var_index = ((1 - confidence_level) * returns.length).to_i
    tail_returns = sorted_returns[0...var_index]
    tail_returns.sum / tail_returns.length
  end

  def calculate_max_drawdown(returns)
    max_value = returns[0]
    max_drawdown = 0
    
    returns.each do |ret|
      max_value = [max_value, ret].max
      drawdown = (max_value - ret) / max_value
      max_drawdown = [max_drawdown, drawdown].max
    end
    
    max_drawdown
  end
end
```

## Best Practices

1. **Problem Formulation**: Carefully map classical problems to quantum formulations
2. **Parameter Optimization**: Use appropriate optimization techniques for quantum parameters
3. **Error Analysis**: Consider noise and errors in quantum computations
4. **Hybrid Approaches**: Combine classical and quantum methods for better performance
5. **Validation**: Verify quantum results with classical benchmarks
6. **Scalability**: Consider resource requirements for large-scale problems
7. **Interpretation**: Properly interpret quantum measurement results

## Conclusion

Quantum applications span cryptography, machine learning, optimization, and finance. While Ruby implementations are educational, they provide valuable insights into quantum computing's potential impact across various domains. Understanding these applications helps prepare for the quantum computing revolution.

## Further Reading

- [Quantum Computing Applications](https://arxiv.org/abs/1804.03436)
- [Quantum Machine Learning](https://arxiv.org/abs/2005.08783)
- [Quantum Optimization Algorithms](https://arxiv.org/abs/1912.04088)
- [Quantum Finance](https://arxiv.org/abs/1912.05815)
