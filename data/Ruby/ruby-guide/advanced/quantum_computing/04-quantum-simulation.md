# Quantum Simulation in Ruby

## Overview

Quantum simulation involves modeling quantum systems using classical computers. While Ruby is not typically used for high-performance quantum simulations, it provides excellent tools for educational purposes, prototyping, and understanding quantum mechanics concepts.

## Quantum State Representation

### State Vector Simulation
```ruby
class QuantumState
  attr_reader :num_qubits, :amplitudes, :dimension

  def initialize(num_qubits)
    @num_qubits = num_qubits
    @dimension = 2 ** num_qubits
    @amplitudes = Array.new(@dimension, 0)
    @amplitudes[0] = 1  # Initialize to |00...0⟩
  end

  def initialize_from_amplitudes(amplitudes)
    @amplitudes = amplitudes.dup
    normalize
  end

  def apply_gate(gate_matrix, qubit_indices)
    # Apply gate to specified qubits
    full_gate = build_full_gate_matrix(gate_matrix, qubit_indices)
    @amplitudes = multiply_matrix_vector(full_gate, @amplitudes)
    normalize
  end

  def measure(qubit_index = nil)
    if qubit_index
      measure_single_qubit(qubit_index)
    else
      measure_all_qubits
    end
  end

  def get_probabilities
    @amplitudes.map { |amp| amp.abs2 }
  end

  def get_density_matrix
    calculate_density_matrix
  end

  def calculate_entropy
    probs = get_probabilities
    probs.reject { |p| p == 0 }.sum { |p| -p * Math.log2(p) }
  end

  def calculate_purity
    density_matrix = get_density_matrix
    trace = 0
    
    density_matrix.each_with_index do |row, i|
      trace += (row[i] * row[i].conjugate).real
    end
    
    trace
  end

  def clone
    new_state = QuantumState.new(@num_qubits)
    new_state.initialize_from_amplitudes(@amplitudes)
    new_state
  end

  def to_s
    state_str = ""
    @amplitudes.each_with_index do |amp, i|
      if amp.abs > 1e-10
        basis_state = i.to_s(2).rjust(@num_qubits, '0')
        coefficient = amp.round(6)
        sign = coefficient >= 0 ? "+" : "-"
        magnitude = coefficient.abs
        
        if magnitude == 1 && amp.imag == 0
          state_str += " #{sign} |#{basis_state}⟩"
        else
          state_str += " #{sign} #{magnitude}|#{basis_state}⟩"
        end
      end
    end
    
    state_str.sub(/^\+ /, "")
  end

  private

  def normalize
    norm = Math.sqrt(@amplitudes.sum { |amp| amp.abs2 })
    @amplitudes.map! { |amp| amp / norm }
  end

  def build_full_gate_matrix(gate_matrix, qubit_indices)
    # Build the full matrix for the specified qubits
    size = @dimension
    full_matrix = Array.new(size) { Array.new(size, 0) }
    
    (0...size).each do |i|
      (0...size).each do |j|
        # Check if transition i -> j is allowed by the gate
        if transition_allowed?(i, j, qubit_indices, gate_matrix)
          full_matrix[i][j] = calculate_matrix_element(i, j, qubit_indices, gate_matrix)
        end
      end
    end
    
    full_matrix
  end

  def transition_allowed?(i, j, qubit_indices, gate_matrix)
    qubit_indices.each do |qubit|
      i_bit = (i >> qubit) & 1
      j_bit = (j >> qubit) & 1
      
      # Check if this bit transition is allowed by the gate
      gate_i = 0
      gate_j = 0
      
      qubit_indices.each_with_index do |q, idx|
        gate_i = (gate_i << 1) | ((i >> q) & 1)
        gate_j = (gate_j << 1) | ((j >> q) & 1)
      end
      
      return false if gate_matrix[gate_j][gate_i] == 0
    end
    
    true
  end

  def calculate_matrix_element(i, j, qubit_indices, gate_matrix)
    element = 1
    
    # Calculate the matrix element for this transition
    gate_i = 0
    gate_j = 0
    
    qubit_indices.each_with_index do |q, idx|
      gate_i = (gate_i << 1) | ((i >> q) & 1)
      gate_j = (gate_j << 1) | ((j >> q) & 1)
    end
    
    gate_matrix[gate_j][gate_i]
  end

  def multiply_matrix_vector(matrix, vector)
    matrix.map do |row|
      row.zip(vector).sum { |elem, vec| elem * vec }
    end
  end

  def measure_single_qubit(qubit_index)
    probs = calculate_measurement_probabilities(qubit_index)
    outcome = rand < probs[0] ? 0 : 1
    
    # Collapse the state
    collapse_state(qubit_index, outcome)
    outcome
  end

  def measure_all_qubits
    probs = get_probabilities
    cumulative = probs.each_with_index.map { |prob, i| [i, probs[0...i].sum] }
    
    random = rand
    selected_index = cumulative.find { |_, cum| random <= cum + probs[_] }&.first || 0
    
    collapse_to_basis_state(selected_index)
    selected_index
  end

  def calculate_measurement_probabilities(qubit_index)
    prob_0 = 0
    prob_1 = 0
    
    @amplitudes.each_with_index do |amp, i|
      bit = (i >> qubit_index) & 1
      if bit == 0
        prob_0 += amp.abs2
      else
        prob_1 += amp.abs2
      end
    end
    
    [prob_0, prob_1]
  end

  def collapse_state(qubit_index, outcome)
    new_amplitudes = Array.new(@dimension, 0)
    
    @amplitudes.each_with_index do |amp, i|
      bit = (i >> qubit_index) & 1
      if bit == outcome
        new_amplitudes[i] = amp
      end
    end
    
    @amplitudes = new_amplitudes
    normalize
  end

  def collapse_to_basis_state(index)
    @amplitudes = Array.new(@dimension, 0)
    @amplitudes[index] = 1
  end

  def calculate_density_matrix
    size = @dimension
    density_matrix = Array.new(size) { Array.new(size, Complex(0, 0)) }
    
    @amplitudes.each_with_index do |amp_i, i|
      @amplitudes.each_with_index do |amp_j, j|
        density_matrix[i][j] = amp_i * amp_j.conjugate
      end
    end
    
    density_matrix
  end
end
```

## Quantum System Simulation

### Hamiltonian Simulation
```ruby
class HamiltonianSimulator
  def initialize(hamiltonian_matrix, time_step = 0.01)
    @hamiltonian = hamiltonian_matrix
    @time_step = time_step
    @dimension = hamiltonian_matrix.length
  end

  def evolve_state(initial_state, total_time)
    current_state = initial_state.clone
    steps = (total_time / @time_step).to_i
    
    puts "Evolving quantum system for #{total_time} time units"
    puts "Time step: #{@time_step}, Steps: #{steps}"
    
    evolution_history = [current_state.clone]
    
    steps.times do |step|
      current_state = time_evolution_step(current_state)
      evolution_history << current_state.clone
      
      if (step + 1) % 100 == 0
        puts "Step #{step + 1}/#{steps} completed"
      end
    end
    
    evolution_history
  end

  def calculate_energies(state)
    # Calculate expectation value of Hamiltonian
    energy = 0
    
    state.amplitudes.each_with_index do |amp_i, i|
      state.amplitudes.each_with_index do |amp_j, j|
        energy += amp_i.conjugate * @hamiltonian[i][j] * amp_j
      end
    end
    
    energy.real
  end

  def get_eigenvalues
    # Simplified eigenvalue calculation (for small matrices)
    calculate_matrix_eigenvalues(@hamiltonian)
  end

  def simulate_thermal_state(temperature)
    # Simulate thermal state using Boltzmann distribution
    eigenvalues = get_eigenvalues
    beta = 1.0 / temperature
    
    # Calculate Boltzmann weights
    weights = eigenvalues.map { |E| Math.exp(-beta * E) }
    total_weight = weights.sum
    probabilities = weights.map { |w| w / total_weight }
    
    # Create mixed state
    density_matrix = Array.new(@dimension) { Array.new(@dimension, Complex(0, 0)) }
    
    eigenvalues.each_with_index do |E, i|
      density_matrix[i][i] = probabilities[i]
    end
    
    density_matrix
  end

  private

  def time_evolution_step(state)
    # U = exp(-iHt/ℏ) ≈ I - iHt/ℏ for small t
    evolution_operator = calculate_evolution_operator(@time_step)
    new_amplitudes = multiply_matrix_vector(evolution_operator, state.amplitudes)
    
    new_state = QuantumState.new(state.num_qubits)
    new_state.initialize_from_amplitudes(new_amplitudes)
    new_state
  end

  def calculate_evolution_operator(time)
    # Simplified evolution operator using matrix exponential
    identity = Array.new(@dimension) { Array.new(@dimension, 0) }
    (0...@dimension).each { |i| identity[i][i] = 1 }
    
    # U ≈ I - iHt (first-order approximation)
    evolution_operator = Array.new(@dimension) do |i|
      Array.new(@dimension) do |j|
        identity[i][j] - 1i * @hamiltonian[i][j] * time
      end
    end
    
    evolution_operator
  end

  def multiply_matrix_vector(matrix, vector)
    matrix.map do |row|
      row.zip(vector).sum { |elem, vec| elem * vec }
    end
  end

  def calculate_matrix_eigenvalues(matrix)
    # Simplified eigenvalue calculation for small matrices
    # In practice, you'd use a proper linear algebra library
    case matrix.length
    when 1
      [matrix[0][0]]
    when 2
      calculate_2x2_eigenvalues(matrix)
    else
      # Placeholder for larger matrices
      Array.new(matrix.length) { |i| matrix[i][i] }
    end
  end

  def calculate_2x2_eigenvalues(matrix)
    a, b = matrix[0]
    c, d = matrix[1]
    
    trace = a + d
    determinant = a * d - b * c
    discriminant = Math.sqrt(trace**2 - 4 * determinant)
    
    [(trace + discriminant) / 2, (trace - discriminant) / 2]
  end
end
```

### Many-Body Quantum System
```ruby
class ManyBodyQuantumSystem
  def initialize(num_particles, interaction_strength = 1.0)
    @num_particles = num_particles
    @interaction_strength = interaction_strength
    @num_qubits = num_particles
    @state = QuantumState.new(@num_qubits)
  end

  def apply_heisenberg_interaction(j, k, time)
    # Apply Heisenberg interaction between particles j and k
    # H = J(Sx_j Sx_k + Sy_j Sy_k + Sz_j Sz_k)
    
    # Simplified implementation using Trotterization
    apply_xx_interaction(j, k, time / 3)
    apply_yy_interaction(j, k, time / 3)
    apply_zz_interaction(j, k, time / 3)
  end

  def apply_transverse_field(time)
    # Apply transverse magnetic field
    # H = h Σ_i Sx_i
    
    @num_qubits.times do |i|
      apply_x_rotation(i, time)
    end
  end

  def simulate_ising_model(total_time, dt = 0.01)
    steps = (total_time / dt).to_i
    
    puts "Simulating Ising model with #{@num_particles} particles"
    puts "Total time: #{total_time}, Time step: #{dt}"
    
    magnetization_history = []
    energy_history = []
    
    steps.times do |step|
      # Apply interactions
      (0...@num_particles - 1).each do |i|
        apply_zz_interaction(i, i + 1, dt)
      end
      
      # Apply transverse field
      apply_transverse_field(dt)
      
      # Measure observables
      if step % 10 == 0
        magnetization = calculate_magnetization
        energy = calculate_ising_energy
        
        magnetization_history << magnetization
        energy_history << energy
        
        puts "Step #{step}: M = #{magnetization.round(4)}, E = #{energy.round(4)}"
      end
    end
    
    { magnetization: magnetization_history, energy: energy_history }
  end

  def simulate_quantum_phase_transition(field_range, dt = 0.01)
    puts "Simulating quantum phase transition"
    
    phase_diagram = {}
    
    field_range.each do |field_strength|
      puts "Field strength: #{field_strength}"
      
      # Reset to initial state
      @state = QuantumState.new(@num_qubits)
      
      # Evolve with given field strength
      evolution_time = 10.0
      steps = (evolution_time / dt).to_i
      
      steps.times do |step|
        # Apply interactions
        (0...@num_particles - 1).each do |i|
          apply_zz_interaction(i, i + 1, dt)
        end
        
        # Apply transverse field with given strength
        @num_qubits.times do |i|
          apply_x_rotation(i, field_strength * dt)
        end
      end
      
      # Measure final state properties
      magnetization = calculate_magnetization
      correlation = calculate_correlation_function
      
      phase_diagram[field_strength] = {
        magnetization: magnetization,
        correlation: correlation
      }
    end
    
    phase_diagram
  end

  private

  def apply_xx_interaction(j, k, time)
    # XX interaction: exp(-iJt Sx_j Sx_k)
    # Implement using CNOT and single-qubit rotations
    
    # Decompose into basic gates
    @state.apply_gate(QuantumGates::HADAMARD, [j])
    @state.apply_gate(QuantumGates::HADAMARD, [k])
    apply_zz_interaction(j, k, time)
    @state.apply_gate(QuantumGates::HADAMARD, [j])
    @state.apply_gate(QuantumGates::HADAMARD, [k])
  end

  def apply_yy_interaction(j, k, time)
    # YY interaction: exp(-iJt Sy_j Sy_k)
    
    # Decompose using basis change
    apply_s_dagger(j)
    apply_s_dagger(k)
    apply_xx_interaction(j, k, time)
    apply_s(j)
    apply_s(k)
  end

  def apply_zz_interaction(j, k, time)
    # ZZ interaction: exp(-iJt Sz_j Sz_k)
    angle = @interaction_strength * time
    
    # Implement using controlled phase gate
    gate_matrix = [
      [Math.exp(-1i * angle / 4), 0, 0, 0],
      [0, Math.exp(1i * angle / 4), 0, 0],
      [0, 0, Math.exp(1i * angle / 4), 0],
      [0, 0, 0, Math.exp(-1i * angle / 4)]
    ]
    
    @state.apply_gate(gate_matrix, [j, k])
  end

  def apply_x_rotation(qubit, angle)
    rotation_matrix = [
      [Math.cos(angle/2), -1i * Math.sin(angle/2)],
      [-1i * Math.sin(angle/2), Math.cos(angle/2)]
    ]
    
    @state.apply_gate(rotation_matrix, [qubit])
  end

  def apply_s(qubit)
    s_gate = [[1, 0], [0, 1i]]
    @state.apply_gate(s_gate, [qubit])
  end

  def apply_s_dagger(qubit)
    s_dagger_gate = [[1, 0], [0, -1i]]
    @state.apply_gate(s_dagger_gate, [qubit])
  end

  def calculate_magnetization
    # Calculate expectation value of Σ_i Sz_i
    magnetization = 0
    
    @num_qubits.times do |i|
      # Measure Sz expectation value
      sz_expectation = calculate_sz_expectation(i)
      magnetization += sz_expectation
    end
    
    magnetization / @num_qubits
  end

  def calculate_sz_expectation(qubit)
    expectation = 0
    
    @state.amplitudes.each_with_index do |amp, i|
      bit = (i >> qubit) & 1
      value = bit == 0 ? 0.5 : -0.5
      expectation += amp.abs2 * value
    end
    
    expectation
  end

  def calculate_ising_energy
    # Calculate Ising Hamiltonian expectation value
    energy = 0
    
    # Interaction terms
    (0...@num_qubits - 1).each do |i|
      correlation = calculate_zz_correlation(i, i + 1)
      energy += @interaction_strength * correlation
    end
    
    energy
  end

  def calculate_zz_correlation(qubit1, qubit2)
    correlation = 0
    
    @state.amplitudes.each_with_index do |amp, i|
      bit1 = (i >> qubit1) & 1
      bit2 = (i >> qubit2) & 1
      
      value = bit1 == bit2 ? 0.25 : -0.25
      correlation += amp.abs2 * value
    end
    
    correlation
  end

  def calculate_correlation_function
    # Calculate two-point correlation function
    correlations = []
    
    distance = 1
    while distance < @num_qubits
      total_correlation = 0
      
      (0...@num_qubits - distance).each do |i|
        correlation = calculate_zz_correlation(i, i + distance)
        total_correlation += correlation
      end
      
      avg_correlation = total_correlation / (@num_qubits - distance)
      correlations << [distance, avg_correlation]
      
      distance += 1
    end
    
    correlations
  end
end
```

## Quantum Monte Carlo

### Quantum Monte Carlo Simulator
```ruby
class QuantumMonteCarlo
  def initialize(hamiltonian, beta = 1.0)
    @hamiltonian = hamiltonian
    @beta = beta  # Inverse temperature
    @dimension = hamiltonian.length
    @samples = []
  end

  def metropolis_hastings_sampling(num_samples = 10000)
    puts "Running Quantum Monte Carlo with #{num_samples} samples"
    
    # Initialize random state
    current_state = rand(@dimension)
    current_energy = @hamiltonian[current_state][current_state].real
    
    @samples = []
    accepted = 0
    
    num_samples.times do |i|
      # Propose new state
      proposed_state = propose_state(current_state)
      proposed_energy = @hamiltonian[proposed_state][proposed_state].real
      
      # Calculate acceptance probability
      delta_energy = proposed_energy - current_energy
      acceptance_prob = Math.exp(-@beta * delta_energy)
      
      # Accept or reject
      if rand < acceptance_prob
        current_state = proposed_state
        current_energy = proposed_energy
        accepted += 1
      end
      
      @samples << current_state
      
      if (i + 1) % 1000 == 0
        puts "Sample #{i + 1}/#{num_samples}, Acceptance rate: #{(accepted.to_f / (i + 1) * 100).round(1)}%"
      end
    end
    
    puts "Final acceptance rate: #{(accepted.to_f / num_samples * 100).round(1)}%"
    @samples
  end

  def calculate_observables
    return [] if @samples.empty?
    
    # Calculate energy distribution
    energies = @samples.map { |state| @hamiltonian[state][state].real }
    
    # Calculate heat capacity
    avg_energy = energies.sum / energies.length
    avg_energy_squared = energies.map { |e| e**2 }.sum / energies.length
    heat_capacity = @beta**2 * (avg_energy_squared - avg_energy**2)
    
    # Calculate entropy
    state_probabilities = calculate_state_probabilities
    entropy = state_probabilities.sum { |p| -p * Math.log2(p + 1e-10) }
    
    {
      avg_energy: avg_energy,
      heat_capacity: heat_capacity,
      entropy: entropy,
      energy_distribution: energies.group_by(&:itself).transform_values(&:count)
    }
  end

  def simulate_phase_transition(beta_range)
    puts "Simulating quantum phase transition"
    
    phase_data = {}
    
    beta_range.each do |beta|
      puts "Beta = #{beta}"
      @beta = beta
      metropolis_hastings_sampling(5000)
      observables = calculate_observables
      
      phase_data[beta] = observables
    end
    
    phase_data
  end

  private

  def propose_state(current_state)
    # Simple proposal: flip one random bit
    new_state = current_state
    num_qubits = Math.log2(@dimension).to_i
    
    # Flip one random qubit
    qubit = rand(num_qubits)
    new_state ^= (1 << qubit)
    
    new_state
  end

  def calculate_state_probabilities
    state_counts = Hash.new(0)
    @samples.each { |state| state_counts[state] += 1 }
    
    total_samples = @samples.length
    state_counts.transform_values { |count| count.to_f / total_samples }
  end
end
```

## Quantum Chemistry Simulation

### Molecular Quantum System
```ruby
class MolecularQuantumSystem
  def initialize(num_electrons, num_orbitals)
    @num_electrons = num_electrons
    @num_orbitals = num_orbitals
    @num_qubits = num_orbitals * 2  # Spin orbitals
    @state = QuantumState.new(@num_qubits)
  end

  def apply_hartree_fock_hamiltonian(integrals)
    # Apply Hartree-Fock Hamiltonian
    # H = Σ_i h_i + Σ_i<j J_ij - K_ij
    
    # One-electron terms
    integrals[:one_electron].each_with_index do |h_ij, i|
      if h_ij != 0
        apply_one_electron_operator(i, h_ij)
      end
    end
    
    # Two-electron terms (simplified)
    integrals[:two_electron].each_with_index do |g_ijkl, i|
      if g_ijkl != 0
        apply_two_electron_operator(i, g_ijkl)
      end
    end
  end

  def simulate_uccsd(amplitudes, iterations = 100)
    # Simulate Unitary Coupled Cluster with Singles and Doubles
    puts "Running UCCSD simulation with #{iterations} iterations"
    
    energy_history = []
    
    iterations.times do |iter|
      # Apply UCCSD operator
      apply_uccsd_operator(amplitudes)
      
      # Calculate energy
      energy = calculate_molecular_energy
      energy_history << energy
      
      if (iter + 1) % 10 == 0
        puts "Iteration #{iter + 1}: Energy = #{energy.round(6)}"
      end
    end
    
    energy_history
  end

  def calculate_molecular_energy
    # Simplified molecular energy calculation
    energy = 0
    
    # One-electron contribution
    @num_orbitals.times do |i|
      occupation = calculate_orbital_occupation(i)
      energy += occupation * get_one_electron_integral(i)
    end
    
    # Two-electron contribution (simplified)
    (@num_orbitals).times do |i|
      (@num_orbitals).times do |j|
        next if i == j
        
        occ_i = calculate_orbital_occupation(i)
        occ_j = calculate_orbital_occupation(j)
        
        if occ_i > 0 && occ_j > 0
          energy += occ_i * occ_j * get_two_electron_integral(i, j)
        end
      end
    end
    
    energy
  end

  private

  def apply_one_electron_operator(index, coefficient)
    # Apply one-electron operator
    # Simplified implementation
    gate_matrix = [
      [1, 0],
      [0, Math.exp(1i * coefficient)]
    ]
    
    @state.apply_gate(gate_matrix, [index])
  end

  def apply_two_electron_operator(index, coefficient)
    # Apply two-electron operator
    # Simplified implementation
    orbital_i = index / @num_orbitals
    orbital_j = index % @num_orbitals
    
    if orbital_i != orbital_j
      apply_excitation(orbital_i, orbital_j, coefficient)
    end
  end

  def apply_excitation(from_orbital, to_orbital, amplitude)
    # Apply single excitation operator
    # |i⟩ → cos(θ)|i⟩ + sin(θ)|j⟩
    
    theta = amplitude
    rotation_matrix = [
      [Math.cos(theta), Math.sin(theta)],
      [-Math.sin(theta), Math.cos(theta)]
    ]
    
    @state.apply_gate(rotation_matrix, [from_orbital, to_orbital])
  end

  def apply_uccsd_operator(amplitudes)
    # Apply UCCSD operator with given amplitudes
    # Singles
    amplitudes[:singles].each do |(i, a), t|
      apply_excitation(i, a, t)
    end
    
    # Doubles (simplified)
    amplitudes[:doubles].each do |(i, j, a, b), t|
      apply_double_excitation(i, j, a, b, t)
    end
  end

  def apply_double_excitation(i, j, a, b, amplitude)
    # Apply double excitation operator
    # Simplified implementation using two single excitations
    apply_excitation(i, a, amplitude / 2)
    apply_excitation(j, b, amplitude / 2)
  end

  def calculate_orbital_occupation(orbital)
    # Calculate orbital occupation number
    occupation = 0
    
    @state.amplitudes.each_with_index do |amp, state|
      if orbital_occupied?(state, orbital)
        occupation += amp.abs2
      end
    end
    
    occupation
  end

  def orbital_occupied?(state, orbital)
    # Check if orbital is occupied in given basis state
    # Simplified: check spin-up orbital
    (state >> (2 * orbital)) & 1 == 1
  end

  def get_one_electron_integral(i)
    # Placeholder for one-electron integral
    -1.0  # Simplified
  end

  def get_two_electron_integral(i, j)
    # Placeholder for two-electron integral
    0.5  # Simplified
  end
end
```

## Performance Analysis

### Simulation Benchmark
```ruby
class QuantumSimulationBenchmark
  def initialize
    @results = {}
  end

  def benchmark_state_vector_simulation(num_qubits, operations = 1000)
    puts "Benchmarking state vector simulation (#{num_qubits} qubits)"
    
    state = QuantumState.new(num_qubits)
    hadamard = QuantumGates::HADAMARD
    
    start_time = Time.now
    
    operations.times do |i|
      qubit = i % num_qubits
      state.apply_gate(hadamard, [qubit])
      
      if (i + 1) % 100 == 0
        puts "  Operation #{i + 1}/#{operations}"
      end
    end
    
    end_time = Time.now
    elapsed_time = end_time - start_time
    
    @results[:state_vector] = {
      num_qubits: num_qubits,
      operations: operations,
      time: elapsed_time,
      ops_per_second: operations / elapsed_time,
      memory_usage: calculate_memory_usage(num_qubits)
    }
    
    puts "  Time: #{elapsed_time.round(4)}s"
    puts "  Operations/second: #{(operations / elapsed_time).round(0)}"
    puts "  Memory usage: #{calculate_memory_usage(num_qubits)} MB"
  end

  def benchmark_hamiltonian_evolution(num_qubits, time_steps = 100)
    puts "Benchmarking Hamiltonian evolution (#{num_qubits} qubits)"
    
    # Create simple Heisenberg Hamiltonian
    hamiltonian = create_heisenberg_hamiltonian(num_qubits)
    simulator = HamiltonianSimulator.new(hamiltonian)
    
    initial_state = QuantumState.new(num_qubits)
    
    start_time = Time.now
    evolution_history = simulator.evolve_state(initial_state, time_steps * 0.01)
    end_time = Time.now
    
    elapsed_time = end_time - start_time
    
    @results[:hamiltonian_evolution] = {
      num_qubits: num_qubits,
      time_steps: time_steps,
      time: elapsed_time,
      steps_per_second: time_steps / elapsed_time
    }
    
    puts "  Time: #{elapsed_time.round(4)}s"
    puts "  Steps/second: #{(time_steps / elapsed_time).round(0)}"
  end

  def benchmark_monte_carlo(beta = 1.0, samples = 10000)
    puts "Benchmarking Quantum Monte Carlo (β = #{beta})"
    
    # Create simple Hamiltonian
    dimension = 8  # 3 qubits
    hamiltonian = create_random_hamiltonian(dimension)
    
    mc = QuantumMonteCarlo.new(hamiltonian, beta)
    
    start_time = Time.now
    mc.metropolis_hastings_sampling(samples)
    end_time = Time.now
    
    elapsed_time = end_time - start_time
    
    @results[:monte_carlo] = {
      beta: beta,
      samples: samples,
      time: elapsed_time,
      samples_per_second: samples / elapsed_time
    }
    
    puts "  Time: #{elapsed_time.round(4)}s"
    puts "  Samples/second: #{(samples / elapsed_time).round(0)}"
  end

  def generate_report
    puts "\n" + "=" * 60
    puts "QUANTUM SIMULATION BENCHMARK REPORT"
    puts "=" * 60
    
    @results.each do |method, data|
      puts "\n#{method.to_s.gsub('_', ' ').capitalize}:"
      data.each do |key, value|
        puts "  #{key}: #{value}"
      end
    end
    
    puts "\nRecommendations:"
    puts "- State vector simulation scales as O(2^n)"
    puts "- Hamiltonian evolution benefits from larger time steps"
    puts "- Monte Carlo is efficient for large systems"
  end

  private

  def create_heisenberg_hamiltonian(num_qubits)
    dimension = 2 ** num_qubits
    hamiltonian = Array.new(dimension) { Array.new(dimension, 0) }
    
    # Add nearest-neighbor interactions
    (num_qubits - 1).times do |i|
      # Simplified Heisenberg interaction
      interaction_strength = 1.0
      
      (0...dimension).each do |state|
        if state & (1 << i) != state & (1 << (i + 1))
          hamiltonian[state][state] += interaction_strength
        end
      end
    end
    
    hamiltonian
  end

  def create_random_hamiltonian(dimension)
    hamiltonian = Array.new(dimension) { Array.new(dimension, 0) }
    
    (0...dimension).each do |i|
      (0...dimension).each do |j|
        hamiltonian[i][j] = rand(-1.0..1.0)
      end
    end
    
    # Make it Hermitian
    (0...dimension).each do |i|
      (i...dimension).each do |j|
        hamiltonian[j][i] = hamiltonian[i][j].conjugate
      end
    end
    
    hamiltonian
  end

  def calculate_memory_usage(num_qubits)
    # Estimate memory usage in MB
    dimension = 2 ** num_qubits
    # Each amplitude is a complex number (16 bytes)
    (dimension * 16) / (1024 * 1024)
  end
end
```

## Best Practices

1. **Memory Management**: Be aware of exponential memory growth
2. **Numerical Stability**: Handle floating-point precision carefully
3. **Performance**: Optimize matrix operations for large systems
4. **Validation**: Verify results with known analytical solutions
5. **Visualization**: Use appropriate visualization for quantum states
6. **Error Analysis**: Consider numerical errors and approximations
7. **Parallelization**: Use parallel processing for large simulations

## Conclusion

Quantum simulation in Ruby provides valuable educational insights into quantum mechanics and quantum computing. While not suitable for large-scale production simulations, Ruby implementations help in understanding quantum algorithms, testing concepts, and prototyping quantum systems.

## Further Reading

- [Quantum Simulation Methods](https://arxiv.org/abs/quant-ph/9708022)
- [Density Matrix Renormalization Group](https://arxiv.org/abs/cond-mat/0409315)
- [Quantum Monte Carlo Methods](https://arxiv.org/abs/cond-mat/0409314)
- [Variational Quantum Eigensolver](https://arxiv.org/abs/1304.3061)
