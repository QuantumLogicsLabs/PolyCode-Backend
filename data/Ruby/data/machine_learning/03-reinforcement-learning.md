# Reinforcement Learning in Ruby
# Comprehensive guide to RL algorithms and applications

## 🎮 Reinforcement Learning Fundamentals

### 1. RL Concepts

Core reinforcement learning principles:

```ruby
class ReinforcementLearningFundamentals
  def self.explain_rl_concepts
    puts "Reinforcement Learning Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Reinforcement Learning",
        description: "Learning through interaction with environment",
        elements: ["Agent", "Environment", "State", "Action", "Reward", "Policy"],
        characteristics: ["Trial and error", "Delayed rewards", "Exploration", "Exploitation"],
        applications: ["Games", "Robotics", "Finance", "Recommendation systems"]
      },
      {
        concept: "Markov Decision Process",
        description: "Mathematical framework for RL",
        components: ["States (S)", "Actions (A)", "Transition probability (P)", "Reward function (R)", "Discount factor (γ)"],
        properties: ["Markov property", "Stationary", "Finite/infinite", "Episodic/continuing"],
        types: ["Finite MDP", "Infinite MDP", "Episodic MDP", "Continuing MDP"]
      },
      {
        concept: "Value Functions",
        description: "Expected future rewards",
        types: ["State value V(s)", "Action value Q(s,a)", "Advantage function A(s,a)"],
        properties: ["Bellman equation", "Optimality", "Convergence", "Approximation"],
        estimation: ["Monte Carlo", "Temporal difference", "Bootstrapping", "Function approximation"]
      },
      {
        concept: "Policy",
        description: "Strategy for selecting actions",
        types: ["Deterministic", "Stochastic", "ε-greedy", "Softmax"],
        optimization: ["Policy gradient", "Actor-critic", "Q-learning", "SARSA"],
        representation: ["Tabular", "Neural network", "Linear", "Non-linear"]
      },
      {
        concept: "Exploration vs Exploitation",
        description: "Balance between trying new actions and using known good actions",
        strategies: ["ε-greedy", "UCB", "Thompson sampling", "Optimism in uncertainty"],
        challenges: ["Credit assignment", "Sparse rewards", "Non-stationarity", "Safety"],
        solutions: ["Intrinsic motivation", "Curiosity", "Count-based exploration", "Parameter exploration"]
      },
      {
        concept: "Temporal Difference Learning",
        description: "Learning from incomplete sequences",
        methods: ["TD(0)", "TD(λ)", "Q-learning", "SARSA"],
        advantages: ["Online learning", "Model-free", "Bootstrapping", "Sample efficiency"],
        algorithms: ["Q-learning", "SARSA", "Actor-critic", "DDPG"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Elements: #{concept[:elements].join(', ')}" if concept[:elements]
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Applications: #{concept[:applications].join(', ')}" if concept[:applications]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Properties: #{concept[:properties].join(', ')}" if concept[:properties]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Estimation: #{concept[:estimation].join(', ')}" if concept[:estimation]
      puts "  Strategies: #{concept[:strategies].join(', ')}" if concept[:strategies]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Solutions: #{concept[:solutions].join(', ')}" if concept[:solutions]
      puts "  Optimization: #{concept[:optimization].join(', ')}" if concept[:optimization]
      puts "  Representation: #{concept[:representation].join(', ')}" if concept[:representation]
      puts "  Methods: #{concept[:methods].join(', ')}" if concept[:methods]
      puts "  Advantages: #{concept[:advantages].join(', ')}" if concept[:advantages]
      puts "  Algorithms: #{concept[:algorithms].join(', ')}" if concept[:algorithms]
      puts
    end
  end
  
  def self.rl_algorithms
    puts "\nReinforcement Learning Algorithms:"
    puts "=" * 50
    
    algorithms = [
      {
        name: "Q-Learning",
        type: "Value-based",
        description: "Learn action-value function",
        updates: "Q(s,a) ← Q(s,a) + α[r + γ max Q(s',a') - Q(s,a)]",
        properties: ["Off-policy", "Model-free", "Convergence proof", "Bootstrapping"],
        variants: ["Deep Q-Network", "Double Q-Learning", "Dueling Q-Network"]
      },
      {
        name: "SARSA",
        type: "Value-based",
        description: "On-policy TD control",
        updates: "Q(s,a) ← Q(s,a) + α[r + γ Q(s',a') - Q(s,a)]",
        properties: ["On-policy", "Model-free", "Stochastic", "Convergence"],
        variants: ["Expected SARSA", "SARSA(λ)", "Multi-step SARSA"]
      },
      {
        name: "Policy Gradient",
        type: "Policy-based",
        description: "Directly optimize policy",
        updates: "θ ← θ + α ∇θ log π(a|s) Q(s,a)",
        properties: ["On-policy", "Stochastic", "Continuous actions", "Convergence"],
        variants: ["REINFORCE", "Actor-Critic", "A2C", "PPO"]
      },
      {
        name: "Actor-Critic",
        type: "Hybrid",
        description: "Combine value and policy methods",
        components: ["Actor (policy)", "Critic (value)", "Two networks", "Joint training"],
        advantages: ["Stability", "Sample efficiency", "Continuous actions", "Convergence"],
        variants: ["A2C", "A3C", "DDPG", "TD3"]
      },
      {
        name: "Deep Q-Network (DQN)",
        type: "Deep Learning",
        description: "Q-learning with neural networks",
        innovations: ["Experience replay", "Target network", "Convolutional layers", "Frame stacking"],
        applications: ["Atari games", "Robotics", "Finance", "NLP"],
        improvements: ["Double DQN", "Dueling DQN", "Noisy DQN", "Distributional DQN"]
      },
      {
        name: "Proximal Policy Optimization (PPO)",
        type: "Policy Gradient",
        description: "Clipped policy optimization",
        features: ["Clipped surrogate objective", "Multiple epochs", "Advantage estimation", "Trust region"],
        benefits: ["Stability", "Sample efficiency", "Implementation simplicity", "Performance"],
        variants: ["PPO-Penalty", "PPO-Clip", "PPO-KL"]
      }
    ]
    
    algorithms.each do |algorithm|
      puts "#{algorithm[:name]}:"
      puts "  Type: #{algorithm[:type]}"
      puts "  Description: #{algorithm[:description]}"
      puts "  Updates: #{algorithm[:updates]}" if algorithm[:updates]
      puts "  Properties: #{algorithm[:properties].join(', ')}" if algorithm[:properties]
      puts "  Variants: #{algorithm[:variants].join(', ')}" if algorithm[:variants]
      puts "  Components: #{algorithm[:components].join(', ')}" if algorithm[:components]
      puts "  Advantages: #{algorithm[:advantages].join(', ')}" if algorithm[:advantages]
      puts "  Applications: #{algorithm[:applications].join(', ')}" if algorithm[:applications]
      puts "  Innovations: #{algorithm[:innovations].join(', ')}" if algorithm[:innovations]
      puts "  Benefits: #{algorithm[:benefits].join(', ')}" if algorithm[:benefits]
      puts "  Features: #{algorithm[:features].join(', ')}" if algorithm[:features]
      puts
    end
  end
  
  def self.environments
    puts "\nReinforcement Learning Environments:"
    puts "=" * 50
    
    environments = [
      {
        name: "OpenAI Gym",
        type: "General Purpose",
        description: "Standardized RL environment suite",
        categories: ["Classic control", "Atari games", "Robotics", "MuJoCo", "Box2D"],
        features: ["Standard API", "Benchmarking", "Visualization", "Wrappers"],
        examples: ["CartPole", "MountainCar", "Pendulum", "FrozenLake"]
      },
      {
        name: "Atari Games",
        type: "Games",
        description: "Classic Atari 2600 games",
        challenges: ["High-dimensional", "Sparse rewards", "Partial observability", "Long episodes"],
        preprocessing: ["Frame stacking", "Grayscale", "Resizing", "Frame skipping"],
        algorithms: ["DQN", "A3C", "IMPALA", "Rainbow"]
      },
      {
        name: "MuJoCo",
        type: "Robotics",
        description: "Continuous control tasks",
        domains: ["Manipulation", "Locomotion", "Hand manipulation", "Locomotion"],
        challenges: ["Continuous actions", "Complex dynamics", "High-dimensional", "Contact"],
        algorithms: ["DDPG", "TD3", "SAC", "PPO"]
      },
      {
        name: "RoboDesk",
        type: "Robotics",
        description: "Desktop manipulation tasks",
        features: ["Realistic physics", "Visual feedback", "Diverse tasks", "Benchmarking"],
        challenges: ["Visual perception", "Fine motor control", "Generalization", "Sample efficiency"],
        algorithms: ["PPO", "SAC", "HER", "CURL"]
      },
      {
        name: "Procgen",
        type: "Procedural Content Generation",
        description: "Procedurally generated games",
        domains: ["Platform games", "Dungeon games", "3D games", "Strategy games"],
        challenges: ["Generalization", "Complexity", "Long-term rewards", "Evaluation"],
        algorithms: ["PPO", "IMPALA", "Agent57", "MuZero"]
      },
      {
        name: "DeepMind Lab",
        type: "Research",
        description: "3D environments for research",
        domains: ["Navigation", "Physics", "Memory", "Social interaction"],
        features: ["Photorealistic", "First-person", "Multi-agent", "Rich tasks"],
        algorithms: ["A3C", "IMPALA", "MuZero", "Agent57"]
      }
    ]
    
    environments.each do |env|
      puts "#{env[:name]}:"
      puts "  Type: #{env[:type]}"
      puts "  Description: #{env[:description]}"
      puts "  Categories: #{env[:categories].join(', ')}" if env[:categories]
      puts "  Features: #{env[:features].join(', ')}" if env[:features]
      puts "  Examples: #{env[:examples].join(', ')}" if env[:examples]
      puts "  Challenges: #{env[:challenges].join(', ')}" if env[:challenges]
      puts "  Preprocessing: #{env[:preprocessing].join(', ')}" if env[:preprocessing]
      puts "  Algorithms: #{env[:algorithms].join(', ')}" if env[:algorithms]
      puts "  Domains: #{env[:domains].join(', ')}" if env[:domains]
      puts
    end
  end
  
  def self.rl_applications
    puts "\nReinforcement Learning Applications:"
    puts "=" * 50
    
    applications = [
      {
        domain: "Games",
        applications: ["Game playing", "Strategy games", "Board games", "Video games"],
        successes: ["AlphaGo", "AlphaZero", "OpenAI Five", "Agent57"],
        challenges: ["Long-term planning", "Multi-agent", "Real-time", "Partial observability"],
        impact: ["AI breakthroughs", "Human-level performance", "New strategies", "Entertainment"]
      },
      {
        domain: "Robotics",
        applications: ["Manipulation", "Locomotion", "Navigation", "Assembly"],
        successes: ["Robot hand manipulation", "Quadruped locomotion", "Industrial robots"],
        challenges: ["Sim-to-real transfer", "Sample efficiency", "Safety", "Generalization"],
        impact: ["Automation", "Manufacturing", "Healthcare", "Exploration"]
      },
      {
        domain: "Finance",
        applications: ["Trading", "Portfolio management", "Risk management", "Fraud detection"],
        successes: ["Algorithmic trading", "Portfolio optimization", "Risk assessment"],
        challenges: ["Non-stationarity", "Risk management", "Regulation", "Market impact"],
        impact: ["Automated trading", "Better decisions", "Risk reduction", "Efficiency"]
      },
      {
        domain: "Healthcare",
        applications: ["Treatment planning", "Drug discovery", "Diagnosis", "Personalized medicine"],
        successes: ["Radiotherapy planning", "Drug discovery", "Clinical trials"],
        challenges: ["Safety", "Interpretability", "Regulation", "Data privacy"],
        impact: ["Better outcomes", "Cost reduction", "Personalization", "Efficiency"]
      },
      {
        domain: "Recommendation Systems",
        applications: ["Content recommendation", "Ad placement", "Personalization", "Cold start"],
        successes: ["YouTube recommendations", "Netflix recommendations", "E-commerce"],
        challenges: ["Scalability", "Cold start", "Fairness", "Privacy"],
        impact: ["User satisfaction", "Revenue increase", "Personalization", "Discovery"]
      },
      {
        domain: "Autonomous Systems",
        applications: ["Self-driving cars", "Drones", "Autonomous agents", "Smart grids"],
        successes: ["Tesla Autopilot", "Delivery drones", "Autonomous navigation"],
        challenges: ["Safety", "Reliability", "Regulation", "Real-world deployment"],
        impact: ["Transportation", "Delivery", "Efficiency", "Automation"]
      }
    ]
    
    applications.each do |app|
      puts "#{app[:domain]}:"
      puts "  Applications: #{app[:applications].join(', ')}"
      puts "  Successes: #{app[:successes].join(', ')}"
      puts "  Challenges: #{app[:challenges].join(', ')}"
      puts "  Impact: #{app[:impact].join(', ')}"
      puts
    end
  end
  
  # Run RL fundamentals
  explain_rl_concepts
  rl_algorithms
  environments
  rl_applications
end
```

### 2. Q-Learning Implementation

Q-learning algorithm implementation:

```ruby
class QLearningAgent
  def initialize(state_space, action_space, learning_rate = 0.1, discount_factor = 0.95, epsilon = 0.1)
    @state_space = state_space
    @action_space = action_space
    @learning_rate = learning_rate
    @discount_factor = discount_factor
    @epsilon = epsilon
    
    # Initialize Q-table
    @q_table = Hash.new do |state|
      Hash.new { 0.0 }
    end
    
    # Statistics
    @episode_count = 0
    @step_count = 0
    @rewards = []
  end
  
  attr_reader :q_table, :episode_count, :step_count
  
  def select_action(state, training = true)
    actions = @action_space
    
    if training && rand < @epsilon
      # Explore: random action
      actions.sample
    else
      # Exploit: best action
      q_values = @q_table[state]
      if q_values.empty?
        actions.sample
      else
        max_q = q_values.values.max
        best_actions = q_values.select { |_, v| v == max_q }.keys
        best_actions.sample
      end
    end
  end
  
  def update_q_table(state, action, reward, next_state, done)
    # Q-learning update rule
    current_q = @q_table[state][action]
    
    if done
      max_next_q = 0
    else
      max_next_q = @q_table[next_state].values.max || 0
    end
    
    new_q = current_q + @learning_rate * (reward + @discount_factor * max_next_q - current_q)
    @q_table[state][action] = new_q
    
    @step_count += 1
    @rewards << reward
  end
  
  def train_episode(environment, max_steps = 1000)
    state = environment.reset
    total_reward = 0
    steps = 0
    
    until done || steps >= max_steps
      action = select_action(state)
      next_state, reward, done = environment.step(action)
      
      update_q_table(state, action, reward, next_state, done)
      
      state = next_state
      total_reward += reward
      steps += 1
    end
    
    @episode_count += 1
    total_reward
  end
  
  def evaluate(environment, num_episodes = 100, max_steps = 1000)
    total_rewards = []
    
    num_episodes.times do
      state = environment.reset
      total_reward = 0
      steps = 0
      
      until done || steps >= max_steps
        action = select_action(state, training = false)
        next_state, reward, done = environment.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      total_rewards << total_reward
    end
    
    {
      average_reward: total_rewards.sum / total_rewards.length,
      min_reward: total_rewards.min,
      max_reward: total_rewards.max,
      std_reward: calculate_std(total_rewards)
    }
  end
  
  def decay_epsilon(decay_rate = 0.995)
    @epsilon *= decay_rate
    @epsilon = [@epsilon, 0.01].max
  end
  
  def save_model(filepath)
    model_data = {
      q_table: @q_table,
      learning_rate: @learning_rate,
      discount_factor: @discount_factor,
      epsilon: @epsilon,
      episode_count: @episode_count,
      step_count: @step_count,
      rewards: @rewards
    }
    
    File.write(filepath, model_data.to_json)
  end
  
  def load_model(filepath)
    model_data = JSON.parse(File.read(filepath))
    
    @q_table = model_data['q_table'].transform_values { |v| Hash[v] }
    @learning_rate = model_data['learning_rate']
    @discount_factor = model_data['discount_factor']
    @epsilon = model_data['epsilon']
    @episode_count = model_data['episode_count']
    @step_count = model_data['step_count']
    @rewards = model_data['rewards']
  end
  
  def get_learning_stats
    return if @rewards.empty?
    
    {
      average_reward: @rewards.sum / @rewards.length,
      min_reward: @rewards.min,
      max_reward: @rewards.max,
      std_reward: calculate_std(@rewards),
      episodes: @episode_count,
      steps: @step_count,
      epsilon: @epsilon
    }
  end
  
  def self.demonstrate_q_learning
    puts "Q-Learning Demonstration:"
    puts "=" * 50
    
    # Create environment
    env = GridWorldEnvironment.new(4, 4)
    
    # Create Q-learning agent
    agent = QLearningAgent.new(
      env.state_space,
      env.action_space,
      learning_rate: 0.1,
      discount_factor: 0.95,
      epsilon: 0.1
    )
    
    puts "Environment: #{env.class.name}"
    puts "State space: #{env.state_space.length} states"
    puts "Action space: #{env.action_space.length} actions"
    puts "Learning rate: #{agent.learning_rate}"
    puts "Discount factor: #{agent.discount_factor}"
    puts "Epsilon: #{agent.epsilon}"
    
    # Train agent
    puts "\nTraining agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      reward = agent.train_episode(env, max_steps = 50)
      training_rewards << reward
      
      # Decay epsilon
      agent.decay_epsilon if episode % 10 == 0
      
      puts "Episode #{episode + 1}: Reward = #{reward}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating agent for 20 episodes:"
    
    eval_results = agent.evaluate(env, num_episodes = 20, max_steps = 50)
    
    puts "Average reward: #{eval_results[:average_reward].round(4)}"
    puts "Min reward: #{eval_results[:min_reward]}"
    puts "Max reward: #{eval_results[:max_reward]}"
    puts "Std reward: #{eval_results[:std_reward].round(4)}"
    
    # Show final Q-table (sample)
    puts "\nSample Q-table values:"
    @q_table.each do |state, actions|
      puts "  State #{state}: #{actions}"
      break if @q_table.keys.index(state) >= 3
    end
    
    # Test greedy policy
    puts "\nTesting greedy policy:"
    state = env.reset
    total_reward = 0
    
    10.times do
      action = agent.select_action(state, training = false)
      next_state, reward, done = env.step(action)
      
      puts "  State #{state}: Action #{action} -> Reward #{reward}"
      
      state = next_state
      total_reward += reward
      break if done
    end
    
    puts "Total reward: #{total_reward}"
    
    puts "\nQ-Learning Features:"
    puts "- Tabular Q-learning"
    puts "- Epsilon-greedy exploration"
    "- Experience replay (in extended version)"
    "- Model saving and loading"
    "- Learning statistics"
    "- Policy evaluation"
  end
  
  private
  
  def calculate_std(values)
    mean = values.sum.to_f / values.length
    variance = values.map { |x| (x - mean)**2 }.sum / values.length
    Math.sqrt(variance)
  end
end

class GridWorldEnvironment
  def initialize(width, height)
    @width = width
    @height = height
    @start_state = [0, 0]
    @goal_state = [width - 1, height - 1]
    @obstacles = generate_obstacles
    @current_state = @start_state
    @done = false
    @step_count = 0
  end
  
  attr_reader :state_space, :action_space, :current_state, :done, :step_count
  
  def reset
    @current_state = @start_state
    @done = false
    @step_count = 0
    @current_state
  end
  
  def step(action)
    return @current_state, 0, true if @done
    
    x, y = @current_state
    new_x, new_y = x, y
    
    # Execute action
    case action
    when :up
      new_y = [y - 1, 0].max
    when :down
      new_y = [y + 1, @height - 1].min
    when :left
      new_x = [x - 1, 0].max
    when :right
      new_x = [x + 1, @width - 1].min
    end
    
    # Check boundaries and obstacles
    if @obstacles.include?([new_x, new_y])
      # Hit obstacle
      reward = -10
      new_x, new_y = x, y
    else
      @current_state = [new_x, new_y]
      @step_count += 1
      
      # Calculate reward
      if @current_state == @goal_state
        reward = 100
        @done = true
      else
        # Distance-based reward
        distance_to_goal = Math.sqrt((@goal_state[0] - new_x)**2 + (@goal_state[1] - new_y)**2)
        max_distance = Math.sqrt((@goal_state[0] - @start_state[0])**2 + (@goal_state[1] - @start_state[1])**2)
        reward = -1.0 + (1.0 - distance_to_goal / max_distance)
      end
    end
    
    [@current_state, reward, @done]
  end
  
  def render
    puts "\nEnvironment state:"
    puts "  Legend: S=Start, G=Goal, X=Obstacle, .=Empty, A=Agent"
    
    (0...@height).each do |y|
      row = (0...@width).map do |x|
        if [x, y] == @current_state
          'A'
        elsif [x, y] == @start_state
          'S'
        elsif [x, y] == @goal_state
          'G'
        elsif @obstacles.include?([x, y])
          'X'
        else
          '.'
        end
      end
      puts "  #{row.join(' ')}"
    end
    
    puts "  Agent at: #{@current_state}, Steps: #{@step_count}, Done: #{@done}"
  end
  
  def state_space
    (0...@width).to_a.product((0...@height).to_a)
  end
  
  def action_space
    [:up, :down, :left, :right]
  end
  
  private
  
  def generate_obstacles
    # Generate some random obstacles
    obstacles = []
    
    # Add obstacles (avoid start and goal)
    5.times do
      x = rand(1...@width - 1)
      y = rand(1...@height - 1)
      
      unless [x, y] == @start_state || [x, y] == @goal_state
        obstacles << [x, y]
      end
    end
    
    obstacles
  end
end

class DeepQNetwork
  def initialize(state_size, action_size, hidden_sizes: [64, 64], learning_rate: 0.001)
    @state_size = state_size
    @action_size = action_size
    @hidden_sizes = hidden_sizes
    @learning_rate = learning_rate
    
    # Build neural network
    @network = build_network
    
    # Experience replay buffer
    @replay_buffer = ReplayBuffer.new(capacity: 10000)
    
    # Target network
    @target_network = build_network
    
    # Training parameters
    @batch_size = 32
    @update_frequency = 4
    @update_count = 0
  end
  
  def predict(state)
    @network.forward(state)
  end
  
  def store_experience(state, action, reward, next_state, done)
    @replay_buffer.add(state, action, reward, next_state, done)
  end
  
  def train_step
    return if @replay_buffer.size < @batch_size
    
    # Sample batch from replay buffer
    batch = @replay_buffer.sample(@batch_size)
    
    # Extract components
    states = batch.map { |exp| exp[:state] }
    actions = batch.map { |exp| exp[:action] }
    rewards = batch.map { |exp| exp[:reward] }
    next_states = batch.map { |exp| exp[:next_state] }
    dones = batch.map { |exp| exp[:done] }
    
    # Get current and target Q-values
    current_q_values = @network.forward_batch(states)
    next_q_values = @target_network.forward_batch(next_states)
    
    # Compute target Q-values
    target_q_values = current_q_values.dup
    
    batch.each_with_index do |exp, i|
      action_idx = action_to_index(exp[:action])
      
      if dones[i]
        target_q_values[i][action_idx] = rewards[i]
      else
        target_q_values[i][action_idx] = rewards[i] + 0.99 * next_q_values[i].max
      end
    end
    
    # Train network
    loss = @network.train_batch(states, actions, target_q_values)
    
    # Update target network
    @update_count += 1
    if @update_count % @update_frequency == 0
      @target_network.copy_weights(@network)
    end
    
    loss
  end
  
  def copy_weights(source_network)
    @network.copy_weights(source_network)
  end
  
  def build_network
    layers = []
    
    # Hidden layers
    input_size = @state_size
    @hidden_sizes.each do |hidden_size|
      layers << DenseLayer.new(input_size, hidden_size, activation: :relu)
      input_size = hidden_size
    end
    
    # Output layer
    layers << DenseLayer.new(input_size, @action_size, activation: :linear)
    
    NeuralNetwork.new(layers)
  end
  
  def action_to_index(action)
    @action_space ||= [:up, :down, :left, :right]
    @action_space.index(action)
  end
  
  def self.demonstrate_deep_q_network
    puts "Deep Q-Network Demonstration:"
    puts "=" * 50
    
    # Create environment
    env = CartPoleEnvironment.new
    
    # Create DQN agent
    agent = DeepQNetworkAgent.new(
      env.state_space_size,
      env.action_space_size,
      hidden_sizes: [128, 128],
      learning_rate: 0.001
    )
    
    puts "Environment: #{env.class.name}"
    puts "State size: #{env.state_space_size}"
    puts "Action size: #{env.action_space_size}"
    puts "Hidden layers: #{agent.hidden_sizes}"
    
    # Train agent
    puts "\nTraining DQN agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state)
        next_state, reward, done = env.step(action)
        
        agent.store_experience(state, action, reward, next_state, done)
        agent.train_step if agent.replay_buffer.size >= 32
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      training_rewards << total_reward
      puts "Episode #{episode + 1}: Reward = #{total_reward.round(2)}, Steps = #{steps}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating agent for 20 episodes:"
    
    eval_rewards = []
    
    20.times do |episode|
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state, training = false)
        next_state, reward, done = env.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length}"
    puts "Max reward: #{eval_rewards.max}"
    puts "Min reward: #{eval_rewards.min}"
    
    puts "\nDeep Q-Network Features:"
    puts "- Neural network approximation"
    puts "- Experience replay buffer"
    "- Target network"
    "- Batch training"
    "- Epsilon-greedy exploration"
    "- Continuous control support"
  end
end

class ReplayBuffer
  def initialize(capacity)
    @capacity = capacity
    @buffer = []
    @position = 0
  end
  
  def add(state, action, reward, next_state, done)
    experience = {
      state: state,
      action: action,
      reward: reward,
      next_state: next_state,
      done: done
    }
    
    if @buffer.length < @capacity
      @buffer << experience
    else
      @buffer[@position] = experience
      @position = (@position + 1) % @capacity
    end
  end
  
  def sample(batch_size)
    @buffer.sample(batch_size)
  end
  
  def size
    @buffer.length
  end
end

class DeepQNetworkAgent
  def initialize(state_size, action_size, hidden_sizes: [64, 64], learning_rate: 0.001, epsilon: 0.1)
    @state_size = state_size
    @action_size = action_size
    @epsilon = epsilon
    
    @q_network = DeepQNetwork.new(state_size, action_size, hidden_sizes, learning_rate)
    @target_network = DeepQNetwork.new(state_size, action_size, hidden_sizes, learning_rate)
    
    @episode_count = 0
    @step_count = 0
  end
  
  def select_action(state, training = true)
    q_values = @q_network.predict(state)
    
    if training && rand < @epsilon
      # Explore: random action
      rand(@action_size)
    else
      # Exploit: best action
      q_values.index(q_values.max)
    end
  end
  
  def store_experience(state, action, reward, next_state, done)
    @q_network.store_experience(state, action, reward, next_state, done)
  end
  
  def train_step
    @q_network.train_step
  end
  
  def decay_epsilon(decay_rate = 0.995)
    @epsilon *= decay_rate
    @epsilon = [@epsilon, 0.01].max
  end
  
  def evaluate(environment, num_episodes = 100, max_steps = 200)
    total_rewards = []
    
    num_episodes.times do
      state = environment.reset
      total_reward = 0
      steps = 0
      
      until environment.done? || steps >= max_steps
        action = select_action(state, training = false)
        next_state, reward, done = environment.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      total_rewards << total_reward
    end
    
    {
      average_reward: total_rewards.sum / total_rewards.length,
      min_reward: total_rewards.min,
      max_reward: total_rewards.max
    }
  end
end

class CartPoleEnvironment
  def initialize
    @gravity = 9.8
    @mass_cart = 1.0
    @mass_pole = 0.1
    @length = 1.0
    @force_mag = 10.0
    @tau = 0.02
    
    reset
  end
  
  attr_reader :state_space_size, :action_space_size, :current_state, :done, :step_count
  
  def reset
    @angle = 0.0
    @angular_velocity = 0.0
    @position = 0.0
    @velocity = 0.0
    @step_count = 0
    @done = false
    
    @current_state = [@position, @velocity, @angle, @angular_velocity]
    @current_state
  end
  
  def step(action)
    # Apply action
    force = action == 0 ? @force_mag : -@force_mag
    torque = force * @length * Math.cos(@angle)
    
    # Physics simulation
    angular_acc = (torque - @mass_pole * @length * @gravity * Math.sin(@angle)) / (@mass_pole * @length**2 + @mass_cart * @length)
    
    linear_acc = (force - @mass_pole * @length * @angular_velocity**2 * Math.sin(@angle)) / (@mass_cart + @mass_pole)
    
    # Update state
    @angular_velocity += angular_acc * @tau
    @angle += @angular_velocity * @tau
    @velocity += linear_acc * @tau
    @position += @velocity * @tau
    
    @step_count += 1
    
    # Check termination
    @done = @angle.abs > 0.2 || @position.abs > 2.4
    
    # Calculate reward
    reward = 1.0 - (@angle.abs * 10 + @position.abs * 0.5 + @angular_velocity.abs * 0.1 + @velocity.abs * 0.1)
    
    [@current_state, reward, @done]
  end
  
  def state_space_size
    4 # [position, velocity, angle, angular_velocity]
  end
  
  def action_space_size
    2 # [left, right]
  end
end

class NeuralNetwork
  def initialize(layers)
    @layers = layers
    @loss_function = MSELoss.new
    @optimizer = SGD.new(0.001)
  end
  
  def forward(state)
    input = state.is_a?(Array) ? Matrix.column_vector(state) : state
    
    @layers.each do |layer|
      input = layer.forward(input)
    end
    
    input
  end
  
  def forward_batch(states)
    states.map { |state| forward(state) }
  end
  
  def train_batch(states, actions, target_q_values)
    # Forward pass
    outputs = forward_batch(states)
    
    # Compute loss
    losses = []
    outputs.each_with_index do |output, i|
      action_idx = actions[i]
      target = target_q_values[i]
      predicted = output[action_idx]
      
      losses << (predicted - target)**2
    end
    
    # Backward pass
    grad = losses.map { |l| -2.0 * l }
    
    @layers.reverse.each do |layer|
      grad = layer.backward(grad)
    end
    
    # Update parameters
    @layers.each { |layer| layer.update_parameters(@optimizer) }
    
    losses.sum / losses.length
  end
  
  def copy_weights(source_network)
    @layers.each_with_index do |layer, i|
      layer.copy_weights(source_network.layers[i])
    end
  end
  
  attr_reader :layers
end

class DenseLayer
  def initialize(input_size, output_size, activation: :linear)
    @input_size = input_size
    @output_size = output_size
    @activation = activation
    
    # Initialize weights and biases
    @weights = Matrix.random(output_size, input_size) { rand(-0.1..0.1) }
    @biases = Matrix.random(output_size, 1) { rand(-0.1..0.1) }
    
    # For gradient computation
    @last_input = nil
    @last_output = nil
  end
  
  attr_reader :input_size, :output_size, :activation, :weights, :biases
  
  def forward(input)
    @last_input = input
    
    # Linear transformation
    z = @weights * input + @biases
    
    # Apply activation
    output = apply_activation(z)
    @last_output = output
    
    output
  end
  
  def backward(grad_output)
    # Compute gradients
    activation_grad = activation_derivative(@last_output)
    grad_z = elementwise_multiply(grad_output, activation_grad)
    
    # Gradient for weights and biases
    grad_weights = grad_z * @last_input.transpose
    grad_biases = grad_z
    
    # Gradient for input
    grad_input = @weights.transpose * grad_z
    
    # Store gradients for parameter update
    @grad_weights = grad_weights
    @grad_biases = grad_biases
    
    grad_input
  end
  
  def update_parameters(optimizer)
    @weights, @biases = optimizer.update(@weights, @biases, @grad_weights, @grad_biases)
  end
  
  def copy_weights(source_layer)
    @weights = source_layer.weights.dup
    @biases = source_layer.biases.dup
  end
  
  private
  
  def apply_activation(z)
    case @activation
    when :relu
      relu(z)
    when :sigmoid
      sigmoid(z)
    when :tanh
      tanh(z)
    when :linear
      z
    else
      z
    end
  end
  
  def activation_derivative(output)
    case @activation
    when :relu
      relu_derivative(output)
    when :sigmoid
      sigmoid_derivative(output)
    when :tanh
      tanh_derivative(output)
    when :linear
      Matrix.ones(output.row_count, output.column_count)
    else
      Matrix.ones(output.row_count, output.column_count)
    end
  end
  
  def relu(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| [x, 0].max } })
  end
  
  def relu_derivative(output)
    Matrix.rows(output.to_a.map { |row| row.map { |x| x > 0 ? 1 : 0 } })
  end
  
  def sigmoid(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| 1 / (1 + Math.exp(-x)) } })
  end
  
  def sigmoid_derivative(output)
    Matrix.rows(output.to_a.map { |row| row.map { |x| x * (1 - x) } })
  end
  
  def tanh(z)
    Matrix.rows(z.to_a.map { |row| row.map { |x| Math.tanh(x) } })
  end
  
  def tanh_derivative(output)
    Matrix.rows(output.to_a.map { |row| row.map { |x| 1 - x * x } })
  end
  
  def elementwise_multiply(a, b)
    Matrix.rows(a.to_a.zip(b.to_a).map { |row_a, row_b| row_a.zip(row_b).map { |x, y| x * y } })
  end
end

class MSELoss
  def compute(predictions, targets)
    predictions = predictions.is_a?(Array) ? Matrix.column_vector(predictions) : predictions
    targets = targets.is_a?(Array) ? Matrix.column_vector(targets) : targets
    
    diff = predictions - targets
    (diff.map { |x| x * x }).sum / predictions.row_count
  end
end

class SGD
  def initialize(learning_rate = 0.001)
    @learning_rate = learning_rate
  end
  
  def update(weights, biases, grad_weights, grad_biases)
    new_weights = weights - grad_weights * @learning_rate
    new_biases = biases - grad_biases * @learning_rate
    [new_weights, new_biases]
  end
end
```

## 🎮 Policy Gradient Methods

### 3. Policy Gradient Algorithms

Policy gradient implementation:

```ruby
class PolicyGradientMethods
  def self.demonstrate_policy_gradient
    puts "Policy Gradient Methods Demonstration:"
    puts "=" * 50
    
    # 1. REINFORCE Algorithm
    demonstrate_reinforce
    
    # 2. Actor-Critic Methods
    demonstrate_actor_critic
    
    # 3. PPO Algorithm
    demonstrate_ppo
    
    # 4. A2C Algorithm
    demonstrate_a2c
    
    # 5. DDPG Algorithm
    demonstrate_ddpg
    
    # 6. SAC Algorithm
    demonstrate_sac
  end
  
  def self.demonstrate_reinforce
    puts "\n1. REINFORCE Algorithm:"
    puts "=" * 30
    
    # Create environment
    env = CartPoleEnvironment.new
    agent = REINFORCEAgent.new(env.state_space_size, env.action_space_size)
    
    puts "Training REINFORCE agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(env, max_steps = 200)
      training_rewards << total_reward
      
      puts "Episode #{episode + 1}: Reward = #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating REINFORCE agent:"
    
    eval_rewards = []
    
    20.times do
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state)
        next_state, reward, done = env.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length.round(2)}"
    
    puts "\nREINFORCE Features:"
    puts "- Policy gradient optimization"
    "- Monte Carlo policy gradient"
    "- Baseline subtraction"
    "- Advantage estimation"
    "- Episodic tasks"
  end
  
  def self.demonstrate_actor_critic
    puts "\n2. Actor-Critic Methods:"
    puts "=" * 30
    
    # Create environment
    env = ContinuousControlEnvironment.new
    agent = ActorCriticAgent.new(
      env.state_space_size,
      env.action_space_size,
      actor_hidden_sizes: [128, 64],
      critic_hidden_sizes: [128, 64],
      learning_rate: 0.001
    )
    
    puts "Training Actor-Critic agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(env, max_steps = 200)
      training_rewards << total_reward
      
      puts "Episode #{episode + 1}: Reward = #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating Actor-Critic agent:"
    
    eval_rewards = []
    
    20.times do
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state)
        next_state, reward, done = env.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length.round(2)}"
    
    puts "\nActor-Critic Features:"
    puts "- Actor network for policy"
    "- Critic network for value"
    "- Temporal difference learning"
    "- Continuous action spaces"
    "- Advantage estimation"
  end
  
  def self.demonstrate_ppo
    puts "\n3. PPO Algorithm:"
    puts "=" * 30
    
    # Create environment
    env = ContinuousControlEnvironment.new
    agent = PPOAgent.new(
      env.state_space_size,
      env.action_space_size,
      hidden_sizes: [128, 64],
      learning_rate: 0.0003,
      clip_epsilon: 0.2
    )
    
    puts "Training PPO agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(env, max_steps = 200)
      training_rewards << total_reward
      
      puts "Episode #{episode + 1}: Reward = #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating PPO agent:"
    
    eval_rewards = []
    
    20.times do
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state)
        next_state, reward, done = env.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length.round(2)}"
    
    puts "\nPPO Features:"
    puts "- Clipped surrogate objective"
    "- Multiple epochs per update"
    "- Advantage estimation"
    "- Trust region optimization"
    "- Stable training"
  end
  
  def self.demonstrate_a2c
    puts "\n4. A2C Algorithm:"
    puts "=" * 30
    
    # Create environment
    env = ContinuousControlEnvironment.new
    agent = A2CAgent.new(
      env.state_space_size,
      env.action_space_size,
      actor_hidden_sizes: [128, 64],
      critic_hidden_sizes: [128, 64],
      learning_rate: 0.0007,
      entropy_coefficient: 0.01
    )
    
    puts "Training A2C agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(env, max_steps = 200)
      training_rewards << total_reward
      
      puts "    Episode #{episode + 1}: Reward = #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating A2C agent:"
    
    eval_rewards = []
    
    20.times do
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state)
        next_state, reward, done = env.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length.round(2)}"
    
    puts "\nA2C Features:"
    puts "- Advantage Actor-Critic"
    "- Synchronous parallel training"
    "- Entropy regularization"
    "- Vectorized environments"
    "- Stable learning"
  end
  
  def self.demonstrate_ddpg
    puts "\n5. DDPG Algorithm:"
    puts "=" * 30
    
    # Create environment
    env = ContinuousControlEnvironment.new
    agent = DDPGAgent.new(
      env.state_space_size,
      env.action_space_size,
      actor_hidden_sizes: [400, 300, 200],
      critic_hidden_sizes: [400, 300, 200],
      actor_learning_rate: 0.0001,
      critic_learning_rate: 0.001,
      tau = 0.005
    )
    
    puts "Training DDPG agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(env, max_steps = 200)
      training_rewards << total_reward
      
      puts "    Episode #{episode + 1}: Reward = #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating DDPG agent:"
    
    eval_rewards = []
    
    20.times do
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state)
        next_state, reward, done = env.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length.round(2)}"
    
    puts "\nDDPG Features:"
    puts "- Deterministic policy gradient"
    "- Target network"
    "- Soft target updates"
    "- Continuous action spaces"
    "- Off-policy learning"
  end
  
  def self.demonstrate_sac
    puts "\n6. SAC Algorithm:"
    puts "=" * 30
    
    # Create environment
    env = ContinuousControlEnvironment.new
    agent = SACAgent.new(
      env.state_space_size,
      env.action_space_size,
      actor_hidden_sizes: [256, 256],
      critic_hidden_sizes: [256, 256],
      learning_rate: 0.0003,
      tau = 0.02,
      alpha = 0.2,
      beta = 0.003
    )
    
    puts "Training SAC agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(env, max_steps = 200)
      training_rewards << total_reward
      
      puts "    Episode #{episode + 1}: Reward = #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate agent
    puts "\nEvaluating SAC agent:"
    
    eval_rewards = []
    
    20.times do
      state = env.reset
      total_reward = 0
      steps = 0
      
      until env.done? || steps >= 200
        action = agent.select_action(state)
        next_state, reward, done = env.step(action)
        
        state = next_state
        total_reward += reward
        steps += 1
      end
      
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length.round(2)}"
    
    puts "\nSAC Features:"
    puts "- Soft Actor-Critic"
    "- Entropy regularization"
    "- Twin Q-networks"
    "- Experience replay"
    "- Sample efficiency"
  end
  
  private
  
  def self.simulate_delay(seconds)
    sleep(seconds)
  end
end

class REINFORCEAgent
  def initialize(state_size, action_size, learning_rate = 0.01, gamma = 0.99)
    @state_size = state_size
    @action_size = action_size
    @learning_rate = learning_rate
    @gamma = gamma
    
    # Policy network
    @policy_network = PolicyNetwork.new(state_size, action_size, learning_rate)
    
    # Experience buffer
    @experience_buffer = []
    
    @episode_count = 0
    @step_count = 0
  end
  
  def select_action(state)
    # Sample action from policy
    action_probs = @policy_network.forward(state)
    cumulative_probs = action_probs.cumsum
    random_val = rand
    action = cumulative_probs.find_index { |_, p| p >= random_val }
    
    action
  end
  
  def train_episode(environment, max_steps = 100)
    state = environment.reset
    total_reward = 0
    log_probs = []
    
    (max_steps).times do |step|
      action = select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Store experience
      @experience_buffer << {
        state: state,
        action: action,
        reward: reward,
        next_state: next_state,
        done: done
      }
      
      log_probs << @policy_network.forward(state)[action]
      total_reward += reward * (@gamma ** step)
      
      state = next_state
      break if done
    end
    
    # Update policy
    update_policy(log_probs, @experience_buffer)
    
    @episode_count += 1
    total_reward
  end
  
  private
  
  def update_policy(log_probs, experiences)
    # Calculate returns
    returns = []
    returns << 0
    
    experiences[1..-1].each_with_index do |exp, i|
      discount_factor = @gamma ** (experiences.length - i - 1)
      returns << exp[:reward] + discount_factor * returns.last
    end
    
    # Update policy network
    @policy_network.train(@experience_buffer, returns, log_probs)
  end
end

class PolicyNetwork
  def initialize(state_size, action_size, learning_rate)
    @state_size = state_size
    @action_size = action_size
    @learning_rate = learning_rate
    
    # Build policy network
    @network = build_policy_network
    @optimizer = SGD.new(learning_rate)
  end
  
  def forward(state)
    @network.forward(state)
  end
  
  def train(experiences, returns, log_probs)
    # Calculate policy gradient
    policy_gradients = []
    
    experiences.each_with_index do |exp, i|
      action = exp[:action]
      log_prob = log_probs[i]
      
      # Policy gradient: ∇θ log π(a|s)
      policy_grad = Array.new(@action_size, 0)
      policy_grad[action] = 1.0
      policy_grad[action] -= 1.0 if log_prob > 0
      
      # Multiply by return
      weighted_grad = policy_grad.map { |g| g * returns[i] }
      policy_gradients << weighted_grad
    end
    
    # Average gradients
    avg_grad = policy_gradients.transpose.map { |col| col.sum / col.length }
    
    # Update network
    @network.train_batch(experiences.map { |exp| exp[:state] }, avg_grad)
  end
  
  def build_policy_network
    layers = []
    
    # Hidden layers
    input_size = @state_size
    [64, 64].each do |hidden_size|
      layers << DenseLayer.new(input_size, hidden_size, activation: :tanh)
      input_size = hidden_size
    end
    
    # Output layer (softmax)
    layers << DenseLayer.new(input_size, @action_size, activation: :softmax)
    
    NeuralNetwork.new(layers)
  end
end

class ActorCriticAgent
  def initialize(state_size, action_size, actor_hidden_sizes, critic_hidden_sizes, learning_rate = 0.001)
    @state_size = state_size
    @action_size = action_size
    @actor_hidden_sizes = actor_hidden_sizes
    @critic_hidden_sizes = critic_hidden_sizes
    @learning_rate = learning_rate
    
    # Actor network (policy)
    @actor_network = PolicyNetwork.new(state_size, action_size, learning_rate, actor_hidden_sizes)
    
    # Critic network (value)
    @critic_network = ValueNetwork.new(state_size, critic_hidden_sizes, learning_rate)
    
    # Experience buffer
    @experience_buffer = ReplayBuffer.new(capacity: 10000)
    
    @episode_count = 0
    @step_count = 0
  end
  
  def select_action(state)
    # Sample action from policy
    action_probs = @actor_network.forward(state)
    cumulative_probs = action_probs.cumsum
    random_val = rand
    action = cumulative_probs.find_index { |_, p| p >= random_val }
    
    action
  end
  
  def train_episode(environment, max_steps = 200)
    state = environment.reset
    total_reward = 0
    log_probs = []
    
    (max_steps).times do |step|
      action = select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Store experience
      @experience_buffer.add(state, action, reward, next_state, done)
      
      # Get log probability
      log_probs << @actor_network.forward(state)[action]
      
      # Calculate advantage
      advantage = calculate_advantage(state, reward, next_state, done)
      
      # Update actor and critic
      update_actor_critic(state, action, advantage, log_probs)
      
      state = next_state
      total_reward += reward
      break if done
    end
    
    @episode_count += 1
    total_reward
  end
  
  private
  
  def calculate_advantage(state, reward, next_state, done)
    # Get value estimates
    value = @critic_network.predict(state)[0]
    next_value = done ? 0 : @critic_network.predict(next_state)[0]
    
    # TD error
    advantage = reward + 0.99 * next_value - value
  end
  
  def update_actor_critic(state, action, advantage, log_probs)
    # Sample batch from experience buffer
    batch = @experience_buffer.sample(32)
    
    # Extract components
    states = batch.map { |exp| exp[:state] }
    actions = batch.map { |exp| exp[:action] }
    rewards = batch.map { |exp| exp[:reward] }
    next_states = batch.map { |exp| exp[:next_state] }
    dones = batch.map { |exp| exp[:done] }
    
    # Get current and next values
    values = @critic_network.predict_batch(states)
    next_values = @critic_network.predict_batch(next_states)
    
    # Calculate advantages
    advantages = []
    rewards.each_with_index do |reward, i|
      if dones[i]
        advantages << reward - values[i][0]
      else
        advantages << reward + 0.99 * next_values[i][0] - values[i][0]
      end
    end
    
    # Update critic
    targets = rewards + 0.99 * next_values.map { |v| v[0] }
    critic_loss = @critic_network.train_batch(states, targets)
    
    # Update actor
    log_probs_batch = batch.map { |exp| @actor_network.forward(exp[:state]) }
    actor_loss = @actor_network.train_batch(states, actions, advantages, log_probs_batch)
    
    critic_loss + actor_loss
  end
end

class ValueNetwork
  def initialize(state_size, hidden_sizes, learning_rate)
    @state_size = state_size
    @hidden_sizes = hidden_sizes
    @learning_rate = learning_rate
    
    # Build value network
    @network = build_value_network
    @optimizer = SGD.new(learning_rate)
  end
  
  def predict(state)
    @network.forward(state)
  end
  
  def predict_batch(states)
    states.map { |state| predict(state) }
  end
  
  def train_batch(states, targets)
    # Forward pass
    outputs = predict_batch(states)
    
    # Compute loss
    losses = outputs.zip(targets).map { |output, target| (output[0] - target)**2 }
    
    # Backward pass
    grad = losses.map { |l| -2.0 * l }
    
    @network.train_batch(states, grad)
    
    losses.sum / losses.length
  end
  
  private
  
  def build_value_network
    layers = []
    
    # Hidden layers
    input_size = @state_size
    @hidden_sizes.each do |hidden_size|
      layers << DenseLayer.new(input_size, hidden_size, activation: :relu)
      input_size = hidden_size
    end
    
    # Output layer
    layers << DenseLayer.new(input_size, 1, activation: :linear)
    
    NeuralNetwork.new(layers)
  end
end

class PPOAgent
  def initialize(state_size, action_size, hidden_sizes, learning_rate = 0.0003, clip_epsilon = 0.2)
    @state_size = state_size
    @action_size = action_size
    @hidden_sizes = hidden_sizes
    @learning_rate = learning_rate
    @clip_epsilon = clip_epsilon
    
    # Actor and critic networks
    @actor_network = PolicyNetwork.new(state_size, action_size, learning_rate, hidden_sizes)
    @critic_network = ValueNetwork.new(state_size, hidden_sizes, learning_rate)
    
    # Experience buffer
    @experience_buffer = @experience_buffer = ReplayBuffer.new(capacity: 10000)
    
    @episode_count = 0
    @step_count = 0
  end
  
  def select_action(state)
    # Sample action from policy
    action_probs = @actor_network.forward(state)
    cumulative_probs = action_probs.cumsum
    random_val = rand
    action = cumulative_probs.find_index { |_, p| p >= random_val }
    
    action
  end
  
  def train_episode(environment, max_steps = 200)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Store experience
      @experience_buffer.add(state, action, reward, next_state, done)
      
      # Update networks
      if @experience_buffer.size >= 64
        update_networks
      end
      
      state = next_state
      total_reward += reward
      break if done
    end
    
    @episode_count += 1
    total_reward
  end
  
  private
  
  def update_networks
    # Sample batch from experience buffer
    batch = @experience_buffer.sample(64)
    
    # Extract components
    states = batch.map { |exp| exp[:state] }
    actions = batch.map { |exp| exp[:action] }
    rewards = batch.map { |exp| exp[:reward] }
    next_states = batch.map { |exp| exp[:next_state] }
    dones = batch.map { |exp| exp[:done] }
    
    # Calculate advantages
    values = @critic_network.predict_batch(states)
    next_values = @critic_network.predict_batch(next_states)
    
    advantages = []
    rewards.each_with_index do |reward, i|
      if dones[i]
        advantages << reward - values[i][0]
      else
        advantages << reward + 0.99 * next_values[i][0] - values[i][0]
      end
    end
    
    # Update critic
    targets = rewards + 0.99 * next_values.map { |v| v[0] }
    critic_loss = @critic_network.train_batch(states, targets)
    
    # Update actor with PPO
    log_probs_batch = batch.map { |exp| @actor_network.forward(exp[:state]) }
    actor_losses = []
    
    advantages.each_with_index do |advantage, i|
      action_idx = actions[i]
      
      # Clipped surrogate objective
      ratio = Math.exp(advantage - log_probs_batch[i][action_idx])
      clipped_ratio = [[ratio, 100].min, [ratio / 100, 100].max, [ratio / 100, 100].min].min
      clipped_ratio = [clipped_ratio, 1.0 - @clip_epsilon].max].min
      
      actor_loss = -clipped_ratio * log_probs_batch[i][action_idx]
      actor_losses << actor_loss
    end
    
    actor_loss = actor_losses.sum / actor_losses.length
    total_loss = critic_loss + actor_loss
    
    # Update networks
    @actor_network.train_batch(states, actions, actor_losses)
  end
end

class A2CAgent
  def initialize(state_size, action_size, actor_hidden_sizes, critic_hidden_sizes, learning_rate = 0.0007, entropy_coefficient = 0.01)
    @state_size = state_size
    @action_size = action_size
    @actor_hidden_sizes = actor_hidden_sizes
    @critic_hidden_sizes = critic_hidden_sizes
    @learning_rate = learning_rate
    @entropy_coefficient = entropy_coefficient
    
    # Actor and critic networks
    @actor_network = PolicyNetwork.new(state_size, action_size, learning_rate, actor_hidden_sizes)
    @critic_network = ValueNetwork.new(state_size, critic_hidden_sizes, learning_rate)
    
    # Experience buffer
    @experience_buffer = @experience_buffer = ReplayBuffer.new(capacity: 10000)
    
    @episode_count = 0
    @step_count = 0
  end
  
  def select_action(state)
    # Sample action from policy
    action_probs = @actor_network.forward(state)
    cumulative_probs = action_probs.cumsum
    random_val = rand
    action = cumulative_probs.find_index { |_, p| p >= random_val }
    
    action
  end
  
  def train_episode(environment, max_steps = 200)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Store experience
      @experience_buffer.add(state, action, reward, next_state, done)
      
      # Update networks
      if @experience_buffer.size >= 64
        update_networks
      end
      
      state = next_state
      total_reward += reward
      break if done
    end
    
    @episode_count += 1
    total_reward
  end
  
  private
  
  def update_networks
    # Sample batch from experience buffer
    batch = @experience_buffer.sample(64)
    
    # Extract components
    states = batch.map { |exp| exp[:state] }
    actions = batch.map { |exp| exp[:action] }
    rewards = batch.map { |exp| exp[:reward] }
    next_states = batch.map { |exp| exp[:next_state] }
    dones = batch.map { |exp| exp[:done] }
    
    # Calculate advantages
    values = @critic_network.predict_batch(states)
    next_values = @critic_network.predict_batch(next_states)
    
    advantages = []
    rewards.each_with_index do |reward, i|
      if dones[i]
        advantages << reward - values[i][0]
      else
        advantages << reward + 0.99 * next_values[i][0] - values[i][0]
      end
    end
    
    # Update critic
    targets = rewards + 0.99 * next_values.map { |v| v[0] }
    critic_loss = @critic_network.train_batch(states, targets)
    
    # Update actor
    log_probs_batch = batch.map { |exp| @actor_network.forward(exp[:state]) }
    actor_losses = []
    entropy_losses = []
    
    advantages.each_with_index do |advantage, i|
      action_idx = actions[i]
      
      # Policy gradient with entropy
      policy_grad = log_probs_batch[i].map { |log_p| -log_p }
      weighted_grad = policy_grad.map { |g| g * advantage }
      
      actor_loss = weighted_grad.sum - @entropy_coefficient * log_probs_batch[i].sum
      entropy_losses << -log_probs_batch[i].sum
      
      actor_losses << actor_loss
    end
    
    actor_loss = actor_losses.sum / actor_losses.length
    entropy_loss = entropy_losses.sum / entropy_losses.length
    total_loss = critic_loss + actor_loss - entropy_loss
    
    # Update networks
    @actor_network.train_batch(states, actions, actor_losses)
  end
end

class DDPGAgent
  def initialize(state_size, action_size, actor_hidden_sizes, critic_hidden_sizes, actor_learning_rate = 0.0001, critic_learning_rate = 0.001, tau = 0.005)
    @state_size = state_size
    @action_size = action_size
    @actor_hidden_sizes = actor_hidden_sizes
    @critic_hidden_sizes = critic_hidden_sizes
    @actor_learning_rate = actor_learning_rate
    @critic_learning_rate = critic_learning_rate
    @tau = tau
    
    # Actor and critic networks
    @actor_network = DeterministicPolicyNetwork.new(state_size, action_size, actor_hidden_sizes, actor_learning_rate)
    @critic_network = ValueNetwork.new(state_size, critic_hidden_sizes, critic_learning_rate)
    
    # Target networks
    @target_actor_network = DeterministicPolicyNetwork.new(state_size, action_size, actor_hidden_sizes, actor_learning_rate)
    @target_critic_network = ValueNetwork.new(state_size, critic_hidden_sizes, critic_learning_rate)
    
    # Experience buffer
    @experience_buffer = ReplayBuffer.new(capacity: 10000)
    
    # Soft update parameters
    @actor_soft_update = 0.005
    @critic_soft_update = 0.005
    
    @episode_count = 0
    @step_count = 0
  end
  
  def select_action(state)
    # Deterministic policy
    action = @actor_network.forward(state)
    
    # Add noise for exploration
    noise = rand(-0.1..0.1)
    action = action + noise
    
    # Clip action to valid range
    action = [action, -2.0, 2.0].sort[1]
    
    action.round
  end
  
  def train_episode(environment, max_steps = 200)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Store experience
      @experience_buffer.add(state, action, reward, next_state, done)
      
      # Update networks
      if @experience_buffer.size >= 64
        update_networks
      end
      
      state = next_state
      total_reward += reward
      break if done
    end
    
    @episode_count += 1
    total_reward
  end
  
  private
  
  def update_networks
    # Sample batch from experience buffer
    batch = @experience_buffer.sample(64)
    
    # Extract components
    states = batch.map { |exp| exp[:state] }
    actions = batch.map { |exp| exp[:action] }
    rewards = batch.map { |exp| exp[:reward] }
    next_states = batch.map { |exp| exp[:next_state] }
    dones = batch.map { |exp| exp[:done] }
    
    # Get target values
    target_actions = @target_actor_network.forward_batch(states)
    target_values = @target_critic_network.forward_batch(next_states)
    
    # Calculate target Q-values
    target_q_values = []
    target_actions.each_with_index do |target_action, i|
      target_q_values << target_values[i][0]
    end
    
    # Update critic
    targets = rewards + 0.99 * target_values
    critic_loss = @critic_network.train_batch(states, targets)
    
    # Update actor
    actor_loss = (target_actions - actions.map { |a| a[0] }).map { |x| x**2 }.sum / @action_size
    
    @actor_network.train_batch(states, actor_loss)
    
    # Soft update target networks
    @target_actor_network.soft_update(@actor_network, @actor_soft_update)
    @target_critic_network.soft_update(@critic_network, @critic_soft_update)
  end
end

class SACAgent
  def initialize(state_size, action_size, hidden_sizes, learning_rate = 0.0003, tau = 0.02, alpha = 0.2, beta = 0.003)
    @state_size = state_size
    @action_size = action_size
    @hidden_sizes = hidden_sizes
    @learning_rate = learning_rate
    @tau = tau
    @alpha = alpha
    @beta = beta
    
    # Actor and critic networks
    @actor_network = PolicyNetwork.new(state_size, action_size, learning_rate, hidden_sizes)
    @critic_network = ValueNetwork.new(state_size, hidden_sizes, learning_rate)
    
    # Target networks
    @target_actor_network = PolicyNetwork.new(state_size, action_size, learning_rate, hidden_sizes)
    @target_critic_network = ValueNetwork(state_size, hidden_sizes, learning_rate)
    
    # Experience buffer
    @experience_buffer = ReplayBuffer.new(capacity: 10000)
    
    # Soft update parameters
    @actor_soft_update = 0.005
    @critic_soft_update = 0.005
    
    @episode_count = 0
    @step_count = 0
  end
  
  def select_action(state)
    # Sample action from policy
    action_probs = @actor_network.forward(state)
    cumulative_probs = action_probs.cumsum
    random_val = rand
    action = cumulative_probs.find_index { |_, p| p >= random_val }
    
    action
  end
  
  def train_episode(environment, max_steps = 200)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Store experience
      @experience_buffer.add(state, action, reward, next_state, done)
      
      # Update networks
      if @experience_buffer.size >= 64
        update_networks
      end
      
      state = state
      total_reward += reward
      break if done
    end
    
    @episode_count += 1
    total_reward
  end
  
  private
  
  def update_networks
    # Sample batch from experience buffer
    batch = @experience_buffer.sample(64)
    
    # Extract components
    states = batch.map { |exp| exp[:state] }
    actions = batch.map { |exp| exp[:action] }
    rewards = batch.map { |exp| exp[:reward] }
    next_states = batch.map { |exp| exp[:next_state] }
    dones = batch.map { |exp| exp[:done] }
    
    # Get target values
    target_actions = @target_actor_network.forward_batch(states)
    target_values = @target_critic_network.forward_batch(next_states)
    
    # Calculate target Q-values
    target_q_values = []
    target_actions.each_with_index do |target_action, i|
      target_q_values << target_values[i][0]
    end
    
    # Calculate Q-values
    q_values = @critic_network.predict_batch(states)
    
    # Update critic
    targets = rewards + (1 - @alpha) * target_values + @alpha * q_values.map { |v| v[0] }
    critic_loss = @critic_network.train_batch(states, targets)
    
    # Update actor
    log_probs_batch = batch.map { |exp| @actor_network.forward(exp[:state]) }
    actor_losses = []
    
    advantages = []
    
    q_values.each_with_index do |q_value, i|
      if dones[i]
        advantages << target_q_values[i] - q_value
      else
        advantages << rewards[i] + 0.99 * target_q_values[i] - q_value
      end
    end
    
    advantages.each_with_index do |advantage, i|
      action_idx = actions[i]
      
      # Soft actor-critic
      policy_grad = log_probs_batch[i].map { |log_p| -log_p }
      weighted_grad = policy_grad.map { |g| g * advantage }
      
      actor_loss = weighted_grad.sum - @beta * log_probs_batch[i].sum
      actor_losses << actor_loss
    end
    
    actor_loss = actor_losses.sum / actor_losses.length
    total_loss = critic_loss + actor_loss
    
    # Update actor and critic
    @actor_network.train_batch(states, actor_loss)
    @critic_network.train_batch(states, targets)
    
    # Soft update target networks
    @target_actor_network.soft_update(@actor_network, @actor_soft_update)
    @target_critic_network.soft_update(@critic_network, @critic_soft_update)
  end
end

class DeterministicPolicyNetwork
  def initialize(state_size, action_size, hidden_sizes, learning_rate)
    @state_size = state_size
    @action_size = action_size
    @hidden_sizes = hidden_sizes
    @learning_rate = learning_rate
    
    # Build policy network
    @network = build_policy_network
    @optimizer = SGD.new(learning_rate)
  end
  
  def forward(state)
    @network.forward(state)
  end
  
  def train_batch(states, losses)
    @network.train_batch(states, losses)
  end
  
  def soft_update(source_network, soft_update_rate)
    # Soft update: θ ← τ * θ_target + (1 - τ) * θ_current
    @network.weights.each_with_index do |layer, i|
      source_layer = source_network.layers[i]
      layer.weights = layer.weights.map.with_index do |row, i|
        row.map.with_index do |cell, j|
          source_row = source_layer.weights[i][j]
          cell * soft_update_rate + row * (1 - soft_update_rate)
        end
      end
    end
    
    @network.biases.each_with_index do |bias, i|
      source_bias = source_network.biases[i]
      @network.biases[i] = bias * soft_update_rate + bias * (1 - soft_update_rate)
    end
  end
  
  private
  
  def build_policy_network
    layers = []
    
    # Hidden layers
    input_size = @state_size
    @hidden_sizes.each do |hidden_size|
      layers << DenseLayer.new(input_size, hidden_size, activation: :tanh)
      input_size = hidden_size
    end
    
    # Output layer (tanh for continuous actions)
    layers << DenseLayer.new(input_size, @action_size, activation: :tanh)
    
    NeuralNetwork.new(layers)
  end
end

class ReplayBuffer
  def initialize(capacity)
    @capacity = capacity
    @buffer = []
    @position = 0
  end
  
  def add(state, action, reward, next_state, done)
    experience = {
      state: state,
      action: action,
      reward: reward,
      next_state: next_state,
      done: done
    }
    
    if @buffer.length < @capacity
      @buffer << experience
    else
      @buffer[@position] = experience
      @position = (@position + 1) % @capacity
    end
  end
  
  def sample(batch_size)
    @buffer.sample(batch_size)
  end
  
  def size
    @buffer.length
  end
end
```

## 🎮 Advanced RL Applications

### 4. Advanced RL Applications

Complex reinforcement learning applications:

```ruby
class AdvancedRLApplications
  def self.demonstrate_advanced_applications
    puts "Advanced RL Applications:"
    puts "=" * 50
    
    # 1. Multi-Agent RL
    demonstrate_multi_agent_rl
    
    # 2. Hierarchical RL
    demonstrate_hierarchical_rl
    
    # 3. Meta-Learning
    demonstrate_meta_learning
    
    # 4. Inverse RL
    demonstrate_inverse_rl
    
    # 5. Curriculum Learning
    demonstrate_curriculum_learning
    
    # 6. Transfer Learning
    demonstrate_transfer_learning
  end
  
  def self.demonstrate_multi_agent
    puts "\n1. Multi-Agent RL:"
    puts "=" * 30
    
    # Create multi-agent environment
    env = MultiAgentEnvironment.new(2, 4, 4) # 2 agents, 4x4 grid
    agents = MultiAgentAgent.new(env, num_agents: 2)
    
    puts "Training multi-agent system for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agents.train_episode(env, max_steps = 100)
      training_rewards << total_reward
      
      puts "Episode #{episode + 1}: Total reward: #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Evaluate
    puts "\nEvaluating multi-agent system:"
    
    eval_rewards = []
    
    20.times do
      total_reward = agents.evaluate_episode(env, max_steps = 100)
      eval_rewards << total_reward
    end
    
    puts "Average reward: #{eval_rewards.sum / eval_rewards.length.round(2)}"
    
    puts "\nMulti-Agent RL Features:"
    "- Multiple learning agents"
    "- Cooperative and competitive"
    "- Shared environment"
    "- Communication protocols"
    "- Centralized training"
    "- Distributed learning"
  end
  
  def self.demonstrate_hierarchical_rl
    manager = HierarchicalRLManager.new
    puts "\n2. Hierarchical RL:"
    puts "=" * 30
    
    # Create hierarchical environment
    env = HierarchicalEnvironment.new(8, 8)
    manager = HierarchicalRLManager.new
    
    # Define hierarchy
    manager.add_level(0, 'high_level', env.state_space_size, 4)
    manager.add_level(1, 'mid_level', 16, 6)
    manager.add_level(2, 'low_level', 64, 4)
    
    puts "Training hierarchical agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = manager.train_episode(env, max_steps = 100)
      training_rewards << total_reward
      
      puts "Episode #{episode + 1}: Total reward: #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    puts "\nHierarchical RL Features:"
    "- Hierarchical state space"
    "- Multiple abstraction levels"
    "- Temporal abstraction"
    "- Sub-goal decomposition"
    "- Curriculum learning"
    "- Scalable to complex tasks"
  end
  
  def self.demonstrate_meta_learning
    puts "\n3. Meta-Learning:"
    puts "=" * 30
    
    # Create meta-learning environment
    env = MetaLearningEnvironment.new
    manager = MAMLAgent.new(env)
    
    puts "Training meta-learning agent for 1000 episodes:"
    
    training_rewards = []
    
    1000.times do |episode|
      total_reward = manager.train_episode(env)
      training_rewards << total_reward
      
      puts "Episode #{episode + 1}: Reward: #{total_reward.round(2)}" if (episode + 1) % 100 == 0
    end
    
    # Test adaptation
    puts "\nTesting meta-learning adaptation:"
    
    test_rewards = []
    
    20.times do |episode|
      total_reward = manager.test_episode(env)
      test_rewards << total_reward
    end
    
    puts "Average test reward: #{test_rewards.sum / test_rewards.length.round(2)}"
    
    puts "\nMeta-Learning Features:"
    "- Fast adaptation"
    "- Few-shot learning"
    "- Task generalization"
    "- Automatic curriculum"
    "- Transfer learning"
    "- Skill acquisition"
  end
  
  def self.demonstrate_inverse_rl
    puts "\n4. Inverse RL:"
    puts "=" * 30
    
    # Create inverse RL environment
    env = InverseEnvironment.new
    agent = InverseAgent.new(env)
    
    puts "Training inverse agent for 100 episodes:"
    
    training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(env, max_steps = 50)
      training_rewards << total_reward
      
      puts "Episode #{episode + 1}: Reward: #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    # Test inverse model
    puts "\nTesting inverse model:"
    
    test_rewards = []
    
    20.times do |episode|
      total_reward = agent.test_inverse_model(env)
      test_rewards << total_reward
    end
    
    puts "Average test reward: #{test_rewards.sum / test_rewards.length.round(2)}"
    
    puts "\nInverse RL Features:"
    "- Reward function learning"
    "- Inverse model learning"
    "- Goal-conditioned RL"
    "- Preference learning"
    "- Demonstrations"
    "- Human preference modeling"
  end
  
  def self.demonstrate_curriculum_learning
    puts "\n5. Curriculum Learning:"
    puts "=" * 30
    
    # Create curriculum learning system
    curriculum = CurriculumLearning.new
    
    # Define curriculum
    curriculum.add_task('level1', 'easy', 10, 10, 0.9)
    curriculum.add_task('level2', 'medium', 20, 20, 0.7)
    curriculum.add_task('level3', 'hard', 30, 30, 0.5)
    
    # Create agent that can adapt
    agent = CurriculumAgent.new
    
    puts "Training with curriculum learning:"
    
    curriculum.train_agent(agent, total_episodes: 300)
    
    puts "\nCurriculum Learning Features:"
    "- Progressive difficulty"
    "- Automatic task generation"
    "- Adaptive scheduling"
    "- Performance tracking"
    "- Skill acquisition"
    "- Learning efficiency"
  end
  
  def self.demonstrate_transfer_learning
    puts "\n6. Transfer Learning:"
    puts "=" * 30
    
    # Create source and target environments
    source_env = SourceEnvironment.new
    target_env = TargetEnvironment.new
    
    # Create agent
    agent = TransferLearningAgent.new
    
    # Pre-train on source environment
    puts "Pre-training on source environment:"
    
    pre_training_rewards = []
    
    100.times do |episode|
      total_reward = agent.train_episode(source_env, max_steps = 100)
      pre_training_rewards << total_reward
    end
    
    puts "Pre-training completed"
    
    # Fine-tune on target environment
    puts "\nFine-tuning on target environment:"
    
    fine_tuning_rewards = []
    
    20.times do |episode|
      total_reward = agent.train_episode(target_env, max_steps = 100)
      fine_tuning_rewards << total_reward
    end
    
    puts "Fine-tuning completed"
    
    # Test transfer
    puts "\nTesting transfer performance:"
    
    test_rewards = []
    
    20.times do |episode|
      total_reward = agent.test_episode(target_env, max_steps = 100)
      test_rewards << total_reward
    end
    
    puts "Transfer performance: #{test_rewards.sum / test_rewards.length.round(2)}"
    
    puts "\nTransfer Learning Features:"
    "- Source pre-training"
    "- Target fine-tuning"
    "- Knowledge transfer"
    "- Faster convergence"
    "- Sample efficiency"
    "- Domain adaptation"
  end
  
  private
  
  def self.simulate_delay(seconds)
    sleep(seconds)
  end
end

class MultiAgentEnvironment
  def initialize(num_agents, width, height)
    @num_agents = num_agents
    @width = width
    @height = height
    @agents = []
    @current_states = Array.new(num_agents) { [0, 0] }
    @done = false
    @step_count = 0
    
    # Create agents
    @num_agents.times do |i|
      @agents[i] = Agent.new(i, @width, @height)
    end
    
    # Place agents
    @agents.each_with_index do |agent, i|
      @current_states[i] = [rand(@width), rand(@height)]
    end
  end
  
  attr_reader :num_agents, :width, :height, :agents, :current_states, :done, :step_count
  
  def reset
    @current_states = Array.new(@num_agents) { [0, 0] }
    @done = false
    @step_count = 0
    
    # Place agents randomly
    @agents.each_with_index do |agent, i|
      @current_states[i] = [rand(@width), rand(@height)]
    end
    @current_states
  end
  
  def step(actions)
    rewards = []
    
    @agents.each_with_index do |agent, i|
      action = actions[i]
      state = @current_states[i]
      next_state, reward, done = agent.step(state, action)
      
      @current_states[i] = next_state
      rewards << reward
      @done = true if @done && @agents.all? { |agent| agent.done? }
    end
    
    @step_count += 1
    [rewards, @done]
  end
  
  def render
    puts "\nMulti-Agent Environment State:"
    puts "  Agents: #{@num_agents}"
    puts "  Grid: #{@width}x#{@height}"
    
    grid = Array.new(@height) { Array.new(@width, '.') }
    
    @agents.each_with_index do |agent, i|
      x, y = @current_states[i]
      grid[y][x] = agent.id.to_s
    end
    
    grid.each_with_index do |row, i|
      puts "  #{row.join(' ')}"
    end
  end
end

class Agent
  def initialize(id, width, height)
    @id = id
    @width = width
    @height = height
    @position = [0, 0]
    @velocity = [0, 0]
    @done = false
  end
  
  attr_reader :id, :position, :velocity, :done
  
  def step(state, action)
    @position = state
    @velocity = @velocity
    
    # Simple movement
    case action
    when :up
      @position[1] = [@position[1], [@position[1] + 1].min(@height - 1)].min
    when :down
      @position[1] = [@position[1], [@position[1] - 1].max(0)]
    when :left
      @position[0] = [@position[0] - 1].max(0)]
    when :right
      @position[0] = [@position[0] + 1].min(@width - 1)]
    end
    
    # Check boundaries
    @done = @position[0] < 0 || @position[0] >= @width || @position[1] < 0 || @position[1] >= @height]
    
    [@position, @done]
  end
end

class HierarchicalEnvironment
  def initialize(width, height)
    @width = width
    @height = height
    @current_level = 0
    @sub_environments = []
    @goals = [
      [width - 1, height - 1], # High level goal
      [width / 2, height / 2]    # Mid level goal
    ]
    @current_state = [0, 0]
    @done = false
    @step_count = 0
  end
  
  attr_reader :width, :height, :current_level, :sub_environments, :goals, :current_state, :done, :step_count
  
  def add_level(level, name, state_size, num_goals, success_threshold)
    sub_env = SubEnvironment.new(name, state_size, num_goals, success_threshold)
    @sub_environments << sub_env
  end
  
  def reset
    @current_level = 0
    @current_state = [0, 0]
    @done = false
    @step_count = 0
    
    @sub_environments.each { |env| env.reset }
    
    @current_state
  end
  
  def step(action)
    if @current_level == 0
      # High level
      # Simple movement towards goal
      goal = @goals[@current_level]
      dx = goal[0] - @current_state[0]
      dy = goal[1] - @current_state[1]
      
      # Move towards goal
      @current_state[0] += dx * 0.1
      @current_state[1] += dy * 0.1
      
      # Check if reached goal
      distance = Math.sqrt(dx**2 + dy**2)
      @done = distance < 0.5
    else
      # Sub-environment
      sub_env = @sub_environments[@current_level]
      state = @current_state
      
      # Map state to sub-environment
      sub_state = map_to_sub_environment(state)
      next_state, reward, done = sub_env.step(action)
      
      @current_state = map_from_sub_environment(next_state)
      @done = done
    end
    
    @step_count += 1
    [@current_state, @done]
  end
  
  def state_space_size
    if @current_level == 0
      2
    else
      @sub_environments[@current_level].state_space_size
    end
  end
  
  def map_to_sub_environment(state)
    case @current_level
    when 0
      # Map to 2D state
      state
    when 1
      # Map to 4D state
      [state[0], state[1], state[2], state[3]]
    when 2
      # Map to 8D state
      [state[0], state[1], state[2], state[3], state[4], state[5], state[6], state[7]]
    else
      state
    end
  end
  
  def map_from_sub_environment(sub_state)
    case @current_level
    when 0
      # Map from 2D to 2D
      [sub_state[0], sub_state[1]]
    when 1
      # Map from 4D to 2D
      sub_state[0], sub_state[1]
    when 2
      # Map from 8D to 2D
      sub_state[0], sub_state[1]]
    else
      state
    end
  end
end

class SubEnvironment
  def initialize(name, state_size, num_goals, success_threshold)
    @name = name
    @state_size = state_size
    @num_goals = num_goals
    @success_threshold = success_threshold
    @goals = generate_goals
    @current_state = [0] * state_size
    @done = false
    @step_count = 0
  end
  
  attr_reader :name, :state_size, :num_goals, :success_threshold, :goals, :current_state, :done, :step_count
  
  def reset
    @current_state = [0] * @state_size
    @done = false
    @step_count = 0
  end
  
  def step(action)
    # Simple movement in state space
    @current_state = @current_state.map { |x| x + action }
    
    # Check if any goal is reached
    goals_reached = @goals.any? { |goal| (@current_state - goal).abs.all? { |d| d < 0.5 } }
    @done = goals_reached
    
    [@current_state, @done]
  end
  
  def state_space_size
    @state_size
  end
  
  private
  
  def generate_goals
    @num_goals.times.map { |i| Array.new(@state_size) { |x| x * (i + 1) } }
  end
end

class MAMLAgent
  def initialize(environment)
    @environment = environment
    @meta_policy = MetaPolicy.new(environment)
    @meta_optimizer = MetaOptimizer.new(environment)
    @episode_count = 0
    @step_count = 0
  end
  
  def train_episode(environment, max_steps = 100)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = @meta_policy.select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Update meta-policy
      @meta_policy.update(state, action, reward)
      
      state = next_state
      total_reward += reward
      
      break if done
    end
    
    @episode_count += 1
    total_reward
  end
  
  def test_episode(environment, max_steps = 100)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = @meta_policy.select_action(state)
      next_state, reward, done = environment.step(action)
      
      state = next_state
      total_reward += reward
      
      break if done
    end
    
    total_reward
  end
end

class MetaPolicy
  def initialize(environment)
    @environment = environment
    @policy_table = Hash.new { |h| Hash.new(0) }
    @optimizer = MetaOptimizer.new(environment)
    @learning_rate = 0.01
    @epsilon = 0.1
    @episode_count = 0
  end
  
  def select_action(state)
    if rand < @epsilon
      # Explore
      actions = @environment.action_space
      action = actions.sample
    else
      # Exploit
      q_values = @policy_table[state]
      action = q_values.max_by { |_, v| v }.first
    end
    
    action
  end
  
  def update(state, action, reward)
    # Update Q-value
    current_q = @policy_table[state][action] || 0
    @policy_table[state][action] = current_q + @learning_rate * (reward - current_q)
    
    @episode_count += 1
  end
  
  def decay_epsilon(decay_rate = 0.995)
    @epsilon *= decay_rate
    @epsilon = [@epsilon, 0.01].max
  end
end

class MetaOptimizer
  def initialize(environment)
    @environment = environment
    @learning_rate = 0.01
    @meta_optimizer = GradientDescent.new(learning_rate: 0.001)
  end
  
  def update(policy, optimizer)
    # Simplified meta-optimization
    @meta_optimizer.optimize(policy)
  end
end

class GradientDescent
  def initialize(learning_rate = 0.001)
    @learning_rate = learning_rate
  end
  
  def optimize(policy)
    # Simplified gradient descent
    policy.each do |state, actions|
      actions.each do |action, q_value|
      grad = policy[state][action] if policy[state]
      policy[state][action] -= @learning_rate * grad
    end
    end
  end
end

class InverseEnvironment
  def initialize
    @goal = [5, 5]
    @current_state = [0, 0]
    @done = false
    @step_count = 0
  end
  
  def state_space_size
    2
  end
  
  def reset
    @current_state = [0, 0]
    @done = false
    @step_count = 0
    @current_state
  end
  
  def step(action)
    # Inverse: learn reward function from demonstrations
    case action
    when 0 # Up
      @current_state[1] = [@current_state[1] + 1].min(@goal[1] - 1).max(0)
    when 1 # Down
      @current_state[1] = [@current_state[1] - 1].max(0).min(@goal[1] + 1)
    when 2 # Left
      @current_state[0] = [@current_state[0] - 1].max(0).min(@goal[0] + 1)
    when 3 # Right
      @current_state[0] = [@current_state[0] + 1].min(@goal[0] - 1).max(@goal[0] + 1)
    end
    
    # Check if reached goal
    distance = Math.sqrt((@current_state[0] - @goal[0])**2 + (@current_state[1] - @goal[1])**2)
    @done = distance < 0.5
    
    [@current_state, @done]
  end
  
  def reward_function(state)
    # Inverse reward function
    distance = Math.sqrt((state[0] - @goal[0])**2 + (state[1] - @goal[1])**2)
    -distance
  end
end

class InverseAgent
  def initialize(environment)
    @environment = environment
    @reward_function = environment.reward_function
    @inverse_model = InverseModel.new(environment)
    @optimizer = InverseOptimizer.new
    @episode_count = 0
  end
  
  def train_episode(environment, max_steps = 50)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = @inverse_model.select_action(state)
      next_state, reward, done = environment.step(action)
      
      # Update inverse model
      @inverse_model.update(state, action, reward)
      
      state = next_state
      total_reward += reward
      
      break if done
    end
    
    @episode_count += 1
    total_reward
  end
  
  def test_inverse_model(environment)
    state = environment.reset
    total_reward = 0
    
    10.times do
      action = @inverse_model.select_action(state)
      next_state, reward, done = environment.step(action)
      total_reward += reward
      state = next_state
    end
    
    total_reward / 10
  end
end

class InverseModel
  def initialize(environment)
    @environment = environment
    @reward_function = environment.reward_function
    @model = InverseModelNetwork.new(environment.state_space_size, environment.action_space)
    @optimizer = InverseOptimizer.new
    @training_data = []
  end
  
  def select_action(state)
    # Sample from inverse model
    action_probs = @model.predict(state)
    cumulative_probs = action_probs.cumsum
    random_val = rand
    action = cumulative_probs.find_index { |_, p| p >= random_val }
    
    action
  end
  
  def update(state, action, reward)
    # Store training data
    @training_data << {
      state: state,
      action: action,
      reward: reward
    }
    
    # Train inverse model
    if @training_data.length > 100
      @model.train(@training_data)
      @training_data.clear
    end
  end
  
  def train(training_data)
    # Simplified training for inverse model
    @model.train(training_data)
  end

class InverseModel
  def initialize(state_size, action_size, hidden_sizes = [64, 32], learning_rate = 0.001)
    @state_size = state_size
    @action_size = action_size
    @hidden_sizes = hidden_sizes
    @learning_rate = learning_rate
    
    # Build neural network
    @model = build_inverse_model
    @optimizer = SGD.new(learning_rate)
  end
  
  def predict(state)
    @model.forward(state)
  end
  
  def train(training_data)
    # Simplified training
    inputs = training_data.map { |data| data[:state] }
    targets = training_data.map { |data| [data[:action]] }
    
    # Train model
    100.times do
      indices = (0...training_data.length).to_a.sample(training_data.length)
      batch_inputs = inputs[indices]
      batch_targets = targets[indices]
      
      outputs = @model.forward(batch_inputs)
      targets_tensor = batch_targets.map { |t| [t] }
      
      # Calculate loss
      loss = outputs.zip(targets).map { |output, target| (output - target)**2 }.sum / outputs.length
      
      # Backward pass
      grad = outputs.map { |output, target| 2 * (target - output) / outputs.length }
      
      # Update weights
      @model.train_batch(batch_inputs, grad)
    end
  end
  
  def build_inverse_model
    layers = []
    
    # Input layer
    layers << DenseLayer.new(@state_size, @hidden_sizes[0], activation: :relu)
    input_size = @hidden_sizes[0]
    
    # Hidden layers
    @hidden_sizes[1..-1].each do |hidden_size|
      layers << DenseLayer.new(input_size, hidden_size, activation: :relu)
      input_size = hidden_size
    end
    
    # Output layer
    layers << DenseLayer.new(input_size, @action_size, activation: :linear)
    
    NeuralNetwork.new(layers)
  end
  
  def copy_weights(source_model)
    @model.copy_weights(source_model)
  end
end

class InverseOptimizer
  def initialize(learning_rate = 0.001)
    @learning_rate = learning_rate
  end
  
  def optimize(model)
    # Simplified gradient descent
    @model.learning_rate = @learning_rate
  end
end

class CurriculumLearning
  def initialize
    @tasks = []
    @current_task = 0
    @task_difficulties = []
    @success_rates = []
  end
  
  def add_task(name, difficulty, num_goals, success_threshold)
    @tasks << {
      name: name,
      difficulty: difficulty,
      num_goals: num_goals,
      success_threshold: success_threshold,
      success_rate: 0.0
    }
    @task_difficulties << difficulty
  end
  
  def train_agent(agent, total_episodes = 300)
    @current_task = 0
    @success_rates = Array.new(@tasks.length, 0.0)
    
    total_episodes.times do |episode|
      task = @tasks[@current_task]
      
      # Adjust task difficulty based on performance
      if @success_rates[@current_task] > 0.8
        @current_task = [(@current_task + 1) % @tasks.length]
      elsif @success_rates[@current_task] < 0.2
        @current_task = [@current_task - 1, @tasks.length].max(0)
      end
      
      # Train agent on current task
      task_reward = agent.train_episode(@tasks[@current_task], max_steps: 100)
      @success_rates[@current_task] = 0.8 * @success_rates[@current_task] + 0.2
      
      puts "Task: #{@tasks[@current_task][:name]}, Difficulty: #{@tasks[@current_task][:difficulty]}, Success Rate: #{@success_rates[@current_task].round(3)}"
      
      @current_task = (@current_task + 1) % @tasks.length if episode % 50 == 49
    end
    
    puts "Curriculum completed"
    puts "Final success rates: #{@success_rates.map(&:round(3)}"
  end
  
  def get_task_difficulty_progression
    @task_difficulties.map.with_index { |d, i| [i, d] }
  end
end

class TransferLearningAgent
  def initialize(source_env, target_env)
    @source_env = source_env
    @target_env = target_env
    @agent = TransferAgent.new(source_env, target_env)
    @pre_trained = false
  end
  
  def pre_train(episodes = 100)
    puts "Pre-training on source environment..."
    
    episodes.times do |episode|
      total_reward = @agent.train_episode(@source_env, max_steps: 100)
      puts "  Episode #{episode + 1}: Reward: #{total_reward.round(2)}" if (episode + 1) % 20 == 0
    end
    
    @pre_trained = true
  end
  
  def fine_tune(episodes = 20)
    puts "Fine-tuning on target environment..."
    
    episodes.times do |episode|
      total_reward = @agent.train_episode(@target_env, max_steps: 100)
      puts "  Episode #{episode + 1}: Reward: #{total_reward.round(2)}" if (episode + 1) % 5 == 0
    end
    
    @agent.fine_tuned = true
  end
  
  def evaluate(episodes = 20)
    total_reward = 0
    
    episodes.times do |episode|
      total_reward += @agent.test_episode(@target_env, max_steps: 100)
    end
    
    total_reward / episodes
  end
  
  def test_episode(environment, max_steps = 100)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = @agent.select_action(state)
      state, reward, done = environment.step(action)
      total_reward += reward
      break if done
    end
    
    total_reward
  end
end

class TransferAgent
  def initialize(source_env, target_env)
    @source_env = source_env
    @target_env = target_env
    @agent = DQNAgent.new(
      source_env.state_space_size,
      source_env.action_space_size,
      learning_rate: 0.001,
      gamma: 0.99
    )
  end
  
  def train_episode(environment, max_steps = 100)
    total_reward = 0
    
    (max_steps).times do |step|
      state = environment.reset if step == 0
      
      action = @agent.select_action(state)
      next_state, reward, done = environment.step(action)
      
      state = next_state
      total_reward += reward
      break if done
    end
    
    total_reward
  end
  
  def test_episode(environment, max_steps = 100)
    state = environment.reset
    total_reward = 0
    
    (max_steps).times do |step|
      action = @agent.select_action(state)
      state, reward, done = environment.step(action)
      total_reward += reward
      break if done
    end
    
    total_reward
  end
end
```

## 🧠 Exercises

### Beginner Exercises

1. **Basic Q-Learning**: Implement simple Q-learning algorithm
2. **Policy Gradient**: Implement REINFORCE algorithm
3. **Environment Creation**: Create custom RL environment
4. **Training Loop**: Implement training loop

### Intermediate Exercises

1. **Deep Q-Network**: Implement DQN with experience replay
2. **Actor-Critic**: Implement A2C or PPO
3. **Multi-Agent**: Create multi-agent system
4. **Meta-Learning**: Implement MAML algorithm

### Advanced Exercises

1. **Hierarchical RL**: Create hierarchical RL system
2. **Inverse RL**: Implement inverse reinforcement learning
3. **Transfer Learning**: Implement transfer learning
4. **Production RL**: Deploy RL system

---

## 🎯 Summary

Reinforcement Learning in Ruby provides:

- **RL Fundamentals** - Core concepts and principles
- **Q-Learning** - Value-based methods implementation
- **Policy Gradient** - Policy gradient methods
- **Advanced Applications** - Complex RL applications
- **Comprehensive Examples** - Real-world implementations

Master these reinforcement learning techniques for advanced AI systems!
