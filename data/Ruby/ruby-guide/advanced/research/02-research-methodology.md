# Research Methodology in Ruby

## Overview

Research methodology encompasses the systematic approaches, techniques, and tools used to conduct scientific and technical research. Ruby provides excellent capabilities for data analysis, experimentation, and research automation, making it a valuable tool for researchers across various domains.

## Research Design Framework

### Research Process Management
```ruby
class ResearchProject
  attr_reader :title, :objectives, :methodology, :timeline, :budget

  def initialize(title, researcher)
    @title = title
    @researcher = researcher
    @objectives = []
    @methodology = []
    @timeline = {}
    @budget = {}
    @data = {}
    @results = {}
    @publications = []
    @collaborators = []
    @status = :planning
    @created_at = Time.now
  end

  def add_objective(objective, priority = :medium)
    @objectives << {
      description: objective,
      priority: priority,
      status: :pending,
      created_at: Time.now
    }
    puts "Added objective: #{objective}"
  end

  def define_methodology(method, description, tools = [])
    @methodology << {
      method: method,
      description: description,
      tools: tools,
      status: :planned
    }
    puts "Defined methodology: #{method}"
  end

  def set_timeline(phase, duration, dependencies = [])
    @timeline[phase] = {
      duration: duration,
      dependencies: dependencies,
      status: :planned,
      start_date: nil,
      end_date: nil
    }
    puts "Set timeline for #{phase}: #{duration} days"
  end

  def allocate_budget(category, amount, justification)
    @budget[category] = {
      amount: amount,
      justification: justification,
      spent: 0,
      transactions: []
    }
    puts "Allocated $#{amount} to #{category}"
  end

  def add_collaborator(name, institution, role)
    @collaborators << {
      name: name,
      institution: institution,
      role: role,
      joined_at: Time.now
    }
    puts "Added collaborator: #{name} from #{institution}"
  end

  def start_research
    @status = :active
    @timeline[:start_date] = Time.now
    puts "Research project '#{@title}' started"
  end

  def track_progress(objective_index, progress_notes)
    return false unless @objectives[objective_index]
    
    @objectives[objective_index][:status] = :in_progress
    @objectives[objective_index][:progress_notes] = progress_notes
    @objectives[objective_index][:last_updated] = Time.now
    
    puts "Updated progress for objective #{objective_index + 1}"
  end

  def complete_objective(objective_index, results)
    return false unless @objectives[objective_index]
    
    @objectives[objective_index][:status] = :completed
    @objectives[objective_index][:results] = results
    @objectives[objective_index][:completed_at] = Time.now
    
    @results[objective_index] = results
    puts "Completed objective #{objective_index + 1}"
  end

  def generate_progress_report
    report = []
    report << "Research Progress Report"
    report << "Project: #{@title}"
    report << "Researcher: #{@researcher}"
    report << "Status: #{@status}"
    report << "Created: #{@created_at.strftime('%Y-%m-%d')}"
    report << ""
    
    report << "Objectives (#{@objectives.length}):"
    @objectives.each_with_index do |obj, i|
      status_icon = case obj[:status]
                   when :pending then "⏳"
                   when :in_progress then "🔄"
                   when :completed then "✅"
                   else "❓"
                   end
      
      report << "  #{i + 1}. #{status_icon} #{obj[:description]} (#{obj[:priority]})"
    end
    
    report << ""
    report << "Collaborators (#{@collaborators.length}):"
    @collaborators.each do |collab|
      report << "  • #{collab[:name]} (#{collab[:institution]}) - #{collab[:role]}"
    end
    
    report << ""
    report << "Budget Summary:"
    @budget.each do |category, details|
      spent = details[:spent]
      remaining = details[:amount] - spent
      report << "  #{category}: $#{remaining}/$#{details[:amount]}"
    end
    
    report.join("\n")
  end

  def export_to_json
    {
      title: @title,
      researcher: @researcher,
      objectives: @objectives,
      methodology: @methodology,
      timeline: @timeline,
      budget: @budget,
      collaborators: @collaborators,
      status: @status,
      created_at: @created_at,
      results: @results
    }.to_json
  end
end
```

### Experimental Design
```ruby
class ExperimentalDesign
  def initialize(name, hypothesis)
    @name = name
    @hypothesis = hypothesis
    @variables = {}
    @treatments = []
    @control_groups = []
    @sample_size = 0
    @randomization_method = :simple
    @blinding_method = :none
    @data_collection_plan = {}
    @statistical_analysis = {}
  end

  def define_independent_variable(name, values, type = :categorical)
    @variables[name] = {
      type: type,
      values: values,
      role: :independent
    }
    puts "Defined independent variable: #{name}"
  end

  def define_dependent_variable(name, measurement_method, units = nil)
    @variables[name] = {
      type: :continuous,
      measurement_method: measurement_method,
      units: units,
      role: :dependent
    }
    puts "Defined dependent variable: #{name}"
  end

  def create_treatment(name, variable_values)
    treatment = {
      name: name,
      variables: variable_values,
      sample_size: 0,
      results: []
    }
    
    @treatments << treatment
    puts "Created treatment: #{name}"
  end

  def create_control_group(name, baseline_values = {})
    control = {
      name: name,
      variables: baseline_values,
      sample_size: 0,
      results: []
    }
    
    @control_groups << control
    puts "Created control group: #{name}"
  end

  def calculate_sample_size(effect_size, alpha = 0.05, power = 0.8)
    # Simplified power analysis
    z_alpha = 1.96  # For alpha = 0.05
    z_beta = 0.84   # For power = 0.8
    
    n = 2 * ((z_alpha + z_beta) / effect_size) ** 2
    @sample_size = n.ceil
    
    puts "Calculated sample size: #{@sample_size} per group"
    @sample_size
  end

  def randomize_subjects(subjects)
    case @randomization_method
    when :simple
      simple_randomization(subjects)
    when :block
      block_randomization(subjects)
    when :stratified
      stratified_randomization(subjects)
    else
      simple_randomization(subjects)
    end
  end

  def assign_blinding(level = :double)
    @blinding_method = level
    puts "Assigned blinding level: #{level}"
  end

  def plan_data_collection(variables, frequency, method)
    @data_collection_plan = {
      variables: variables,
      frequency: frequency,
      method: method,
      schedule: generate_collection_schedule(frequency)
    }
    puts "Planned data collection: #{variables.join(', ')} at #{frequency}"
  end

  def define_statistical_analysis(tests, significance_level = 0.05)
    @statistical_analysis = {
      tests: tests,
      significance_level: significance_level,
      assumptions: [],
      software: []
    }
    puts "Defined statistical analysis: #{tests.join(', ')}"
  end

  def generate_protocol
    protocol = []
    protocol << "Experimental Protocol: #{@name}"
    protocol << "Hypothesis: #{@hypothesis}"
    protocol << ""
    
    protocol << "Variables:"
    @variables.each do |name, details|
      role = details[:role] == :independent ? "Independent" : "Dependent"
      protocol << "  #{name}: #{role} (#{details[:type]})"
    end
    
    protocol << ""
    protocol << "Treatments (#{@treatments.length}):"
    @treatments.each_with_index do |treatment, i|
      protocol << "  #{i + 1}. #{treatment[:name]}"
      treatment[:variables].each do |var, val|
        protocol << "     #{var}: #{val}"
      end
    end
    
    protocol << ""
    protocol << "Control Groups (#{@control_groups.length}):"
    @control_groups.each_with_index do |control, i|
      protocol << "  #{i + 1}. #{control[:name]}"
    end
    
    protocol << ""
    protocol << "Sample Size: #{@sample_size} per group"
    protocol << "Randomization: #{@randomization_method}"
    protocol << "Blinding: #{@blinding_method}"
    
    protocol.join("\n")
  end

  private

  def simple_randomization(subjects)
    subjects.shuffle
  end

  def block_randomization(subjects, block_size = 4)
    blocks = []
    subjects.each_slice(block_size) do |block|
      blocks << block.shuffle
    end
    blocks.flatten
  end

  def stratified_randomization(subjects, strata_variable)
    # Simplified stratified randomization
    groups = subjects.group_by { |s| s[strata_variable] }
    groups.values.flat_map(&:shuffle)
  end

  def generate_collection_schedule(frequency)
    case frequency
    when :daily
      (Date.today..(Date.today + 30)).to_a
    when :weekly
      (Date.today..(Date.today + 12 * 7)).select { |d| d.wday == 1 }
    when :monthly
      (Date.today..(Date.today + 365)).select { |d| d.day == 1 }
    else
      [Date.today]
    end
  end
end
```

## Data Collection and Analysis

### Survey Research
```ruby
class SurveyResearch
  def initialize(title, target_population)
    @title = title
    @target_population = target_population
    @questions = []
    @responses = []
    @sampling_method = :random
    @sample_size = 100
    @response_rate = 0
  end

  def add_question(text, type, options = {})
    question = {
      id: @questions.length + 1,
      text: text,
      type: type,  # :multiple_choice, :likert, :open_ended, :rating
      options: options[:options] || [],
      required: options[:required] || false,
      validation: options[:validation] || {}
    }
    
    @questions << question
    puts "Added question #{question[:id]}: #{text}"
  end

  def set_sampling_method(method, sample_size = 100)
    @sampling_method = method
    @sample_size = sample_size
    puts "Set sampling method: #{method}, sample size: #{sample_size}"
  end

  def generate_sample(population)
    case @sampling_method
    when :random
      random_sample(population)
    when :stratified
      stratified_sample(population)
    when :systematic
      systematic_sample(population)
    when :convenience
      convenience_sample(population)
    else
      random_sample(population)
    end
  end

  def collect_response(respondent_id, answers)
    response = {
      respondent_id: respondent_id,
      timestamp: Time.now,
      answers: answers,
      completion_status: :complete
    }
    
    # Validate response
    if validate_response(answers)
      @responses << response
      puts "Collected response from respondent #{respondent_id}"
      true
    else
      puts "Invalid response from respondent #{respondent_id}"
      false
    end
  end

  def calculate_response_rate
    @response_rate = (@responses.length.to_f / @sample_size) * 100
    puts "Response rate: #{@response_rate.round(2)}%"
    @response_rate
  end

  def analyze_responses
    return {} if @responses.empty?
    
    analysis = {
      total_responses: @responses.length,
      response_rate: calculate_response_rate,
      question_analysis: {},
      demographics: analyze_demographics(),
      completion_times: analyze_completion_times()
    }
    
    @questions.each do |question|
      analysis[:question_analysis][question[:id]] = analyze_question(question)
    end
    
    analysis
  end

  def generate_report
    analysis = analyze_responses
    
    report = []
    report << "Survey Report: #{@title}"
    report << "Target Population: #{@target_population}"
    report << "Total Responses: #{analysis[:total_responses]}"
    report << "Response Rate: #{analysis[:response_rate].round(2)}%"
    report << ""
    
    report << "Question Analysis:"
    analysis[:question_analysis].each do |question_id, results|
      question = @questions.find { |q| q[:id] == question_id }
      report << "  Question #{question_id}: #{question[:text]}"
      
      case question[:type]
      when :multiple_choice
        results[:distribution].each do |option, count|
          percentage = (count.to_f / analysis[:total_responses] * 100).round(1)
          report << "    #{option}: #{count} (#{percentage}%)"
        end
      when :likert, :rating
        report << "    Mean: #{results[:mean].round(2)}"
        report << "    Std Dev: #{results[:std_dev].round(2)}"
      when :open_ended
        report << "    Response count: #{results[:response_count]}"
        report << "    Average length: #{results[:avg_length].round(0)} characters"
      end
      report << ""
    end
    
    report.join("\n")
  end

  def export_responses(format = :csv)
    case format
    when :csv
      export_to_csv
    when :json
      export_to_json
    else
      export_to_csv
    end
  end

  private

  def random_sample(population)
    population.sample(@sample_size)
  end

  def stratified_sample(population)
    # Simplified stratified sampling
    strata = population.group_by { |p| p[:stratum] }
    sample = []
    
    strata.each do |stratum, members|
      stratum_size = (@sample_size * members.length.to_f / population.length).ceil
      sample += members.sample(stratum_size)
    end
    
    sample
  end

  def systematic_sample(population)
    interval = (population.length / @sample_size).ceil
    start_index = rand(interval)
    
    (start_index...population.length).step(interval).map { |i| population[i] }
  end

  def convenience_sample(population)
    population.first(@sample_size)
  end

  def validate_response(answers)
    @questions.each do |question|
      answer = answers[question[:id]]
      
      # Check required questions
      if question[:required] && answer.nil?
        return false
      end
      
      # Check answer format
      next if answer.nil?
      
      case question[:type]
      when :multiple_choice
        unless question[:options].include?(answer)
          return false
        end
      when :likert, :rating
        unless answer.is_a?(Numeric) && answer.between?(1, question[:options].length)
          return false
        end
      end
    end
    
    true
  end

  def analyze_question(question)
    answers = @responses.map { |r| r[:answers][question[:id]] }.compact
    
    case question[:type]
    when :multiple_choice
      distribution = Hash.new(0)
      answers.each { |answer| distribution[answer] += 1 }
      { distribution: distribution }
    when :likert, :rating
      {
        mean: answers.sum.to_f / answers.length,
        std_dev: calculate_std_dev(answers),
        min: answers.min,
        max: answers.max
      }
    when :open_ended
      {
        response_count: answers.length,
        avg_length: answers.map(&:length).sum.to_f / answers.length
      }
    else
      {}
    end
  end

  def analyze_demographics
    # Simplified demographic analysis
    {}
  end

  def analyze_completion_times
    # Analyze time taken to complete survey
    {}
  end

  def calculate_std_dev(values)
    mean = values.sum.to_f / values.length
    variance = values.sum { |v| (v - mean) ** 2 } / values.length
    Math.sqrt(variance)
  end

  def export_to_csv
    CSV.generate do |csv|
      # Header
      header = ['Response ID', 'Timestamp'] + @questions.map { |q| "Q#{q[:id]}" }
      csv << header
      
      # Data
      @responses.each do |response|
        row = [
          response[:respondent_id],
          response[:timestamp]
        ] + @questions.map { |q| response[:answers][q[:id]] }
        csv << row
      end
    end
  end

  def export_to_json
    {
      survey: {
        title: @title,
        target_population: @target_population,
        questions: @questions
      },
      responses: @responses
    }.to_json
  end
end
```

### Statistical Analysis
```ruby
class StatisticalAnalyzer
  def initialize(data = [])
    @data = data
    @results = {}
  end

  def load_data(file_path, format = :csv)
    case format
    when :csv
      load_csv_data(file_path)
    when :json
      load_json_data(file_path)
    else
      load_csv_data(file_path)
    end
  end

  def descriptive_statistics(variable)
    values = extract_variable_values(variable)
    return {} if values.empty?
    
    stats = {
      count: values.length,
      mean: calculate_mean(values),
      median: calculate_median(values),
      mode: calculate_mode(values),
      std_dev: calculate_std_dev(values),
      variance: calculate_variance(values),
      min: values.min,
      max: values.max,
      range: values.max - values.min,
      quartiles: calculate_quartiles(values)
    }
    
    @results["descriptive_#{variable}"] = stats
    stats
  end

  def correlation_test(var1, var2, method = :pearson)
    values1 = extract_variable_values(var1)
    values2 = extract_variable_values(var2)
    
    return {} if values1.empty? || values2.empty? || values1.length != values2.length
    
    case method
    when :pearson
      pearson_correlation(values1, values2)
    when :spearman
      spearman_correlation(values1, values2)
    else
      pearson_correlation(values1, values2)
    end
  end

  def t_test(group1, group2, type = :independent)
    case type
    when :independent
      independent_t_test(group1, group2)
    when :paired
      paired_t_test(group1, group2)
    else
      independent_t_test(group1, group2)
    end
  end

  def anova(groups)
    return {} if groups.empty? || groups.any?(&:empty?)
    
    # One-way ANOVA
    all_values = groups.flatten
    grand_mean = calculate_mean(all_values)
    
    # Calculate between-group variance
    group_means = groups.map { |group| calculate_mean(group) }
    group_sizes = groups.map(&:length)
    
    ss_between = group_sizes.zip(group_means).sum { |size, mean| size * (mean - grand_mean) ** 2 }
    df_between = groups.length - 1
    ms_between = ss_between / df_between
    
    # Calculate within-group variance
    ss_within = groups.sum { |group| group.sum { |value| (value - calculate_mean(group)) ** 2 } }
    df_within = all_values.length - groups.length
    ms_within = ss_within / df_within
    
    # Calculate F-statistic
    f_statistic = ms_between / ms_within
    p_value = calculate_f_p_value(f_statistic, df_between, df_within)
    
    result = {
      f_statistic: f_statistic,
      p_value: p_value,
      df_between: df_between,
      df_within: df_within,
      significant: p_value < 0.05
    }
    
    @results[:anova] = result
    result
  end

  def chi_square_test(observed, expected = nil)
    return {} if observed.empty?
    
    if expected.nil?
      # Goodness of fit test
      expected = Array.new(observed.length, observed.sum.to_f / observed.length)
    end
    
    # Calculate chi-square statistic
    chi_square = observed.zip(expected).sum do |obs, exp|
      ((obs - exp) ** 2) / exp
    end
    
    df = observed.length - 1
    p_value = calculate_chi_square_p_value(chi_square, df)
    
    result = {
      chi_square: chi_square,
      p_value: p_value,
      df: df,
      significant: p_value < 0.05
    }
    
    @results[:chi_square] = result
    result
  end

  def regression_analysis(dependent_var, independent_vars)
    y = extract_variable_values(dependent_var)
    x_matrix = independent_vars.map { |var| extract_variable_values(var) }
    
    return {} if y.empty? || x_matrix.any?(&:empty?)
    
    # Multiple linear regression (simplified)
    n = y.length
    k = independent_vars.length
    
    # Add intercept term
    x_with_intercept = x_matrix.transpose.map { |row| [1] + row }
    
    # Calculate regression coefficients using normal equations
    xtx = matrix_multiply(matrix_transpose(x_with_intercept), x_with_intercept)
    xty = matrix_multiply(matrix_transpose(x_with_intercept), y)
    
    coefficients = solve_linear_system(xtx, xty)
    
    # Calculate R-squared
    y_pred = x_with_intercept.map { |row| row.zip(coefficients).sum { |x, c| x * c } }
    ss_total = y.sum { |yi| (yi - calculate_mean(y)) ** 2 }
    ss_residual = y.zip(y_pred).sum { |yi, ypi| (yi - ypi) ** 2 }
    r_squared = 1 - (ss_residual / ss_total)
    
    result = {
      coefficients: coefficients,
      r_squared: r_squared,
      adjusted_r_squared: calculate_adjusted_r_squared(r_squared, n, k),
      residuals: y.zip(y_pred).map { |yi, ypi| yi - ypi }
    }
    
    @results[:regression] = result
    result
  end

  def generate_summary_report
    report = []
    report << "Statistical Analysis Summary"
    report << "=" * 40
    report << ""
    
    @results.each do |test_name, results|
      report << "#{test_name.to_s.gsub('_', ' ').capitalize}:"
      
      case test_name.to_s
      when /^descriptive_/
        results.each { |stat, value| report << "  #{stat}: #{value.round(4)}" }
      when :correlation
        report << "  Correlation coefficient: #{results[:correlation].round(4)}"
        report << "  P-value: #{results[:p_value].round(4)}"
        report << "  Significant: #{results[:significant] ? 'Yes' : 'No'}"
      when :t_test
        report << "  T-statistic: #{results[:t_statistic].round(4)}"
        report << "  P-value: #{results[:p_value].round(4)}"
        report << "  Significant: #{results[:significant] ? 'Yes' : 'No'}"
      when :anova
        report << "  F-statistic: #{results[:f_statistic].round(4)}"
        report << "  P-value: #{results[:p_value].round(4)}"
        report << "  Significant: #{results[:significant] ? 'Yes' : 'No'}"
      when :regression
        report << "  R-squared: #{results[:r_squared].round(4)}"
        report << "  Adjusted R-squared: #{results[:adjusted_r_squared].round(4)}"
      end
      
      report << ""
    end
    
    report.join("\n")
  end

  private

  def load_csv_data(file_path)
    # Simplified CSV loading
    @data = []
    puts "Loaded CSV data from #{file_path}"
  end

  def load_json_data(file_path)
    # Simplified JSON loading
    @data = []
    puts "Loaded JSON data from #{file_path}"
  end

  def extract_variable_values(variable)
    # Extract values for a specific variable from the dataset
    # This is a simplified implementation
    case variable
    when Symbol
      @data.map { |row| row[variable] }.compact
    when String
      @data.map { |row| row[variable.to_sym] }.compact
    else
      []
    end
  end

  def calculate_mean(values)
    values.sum.to_f / values.length
  end

  def calculate_median(values)
    sorted = values.sort
    mid = sorted.length / 2
    
    if sorted.length.odd?
      sorted[mid]
    else
      (sorted[mid - 1] + sorted[mid]) / 2.0
    end
  end

  def calculate_mode(values)
    frequency = values.group_by(&:itself).transform_values(&:count)
    frequency.max_by { |_, count| count }.first
  end

  def calculate_std_dev(values)
    Math.sqrt(calculate_variance(values))
  end

  def calculate_variance(values)
    mean = calculate_mean(values)
    values.sum { |v| (v - mean) ** 2 } / values.length
  end

  def calculate_quartiles(values)
    sorted = values.sort
    n = sorted.length
    
    q1_index = (n * 0.25).ceil - 1
    q2_index = (n * 0.5).ceil - 1
    q3_index = (n * 0.75).ceil - 1
    
    {
      q1: sorted[q1_index],
      q2: sorted[q2_index],  # Median
      q3: sorted[q3_index]
    }
  end

  def pearson_correlation(x, y)
    n = x.length
    sum_x = x.sum
    sum_y = y.sum
    sum_xy = x.zip(y).sum { |xi, yi| xi * yi }
    sum_x2 = x.sum { |xi| xi ** 2 }
    sum_y2 = y.sum { |yi| yi ** 2 }
    
    numerator = n * sum_xy - sum_x * sum_y
    denominator = Math.sqrt((n * sum_x2 - sum_x ** 2) * (n * sum_y2 - sum_y ** 2))
    
    correlation = numerator / denominator
    p_value = calculate_correlation_p_value(correlation, n)
    
    {
      correlation: correlation,
      p_value: p_value,
      significant: p_value < 0.05
    }
  end

  def spearman_correlation(x, y)
    # Convert to ranks
    x_ranks = x.sort.map.with_index.to_h
    y_ranks = y.sort.map.with_index.to_h
    
    x_ranked = x.map { |xi| x_ranks[xi] }
    y_ranked = y.map { |yi| y_ranks[yi] }
    
    pearson_correlation(x_ranked, y_ranked)
  end

  def independent_t_test(group1, group2)
    n1 = group1.length
    n2 = group2.length
    mean1 = calculate_mean(group1)
    mean2 = calculate_mean(group2)
    var1 = calculate_variance(group1)
    var2 = calculate_variance(group2)
    
    # Pooled variance
    pooled_var = ((n1 - 1) * var1 + (n2 - 1) * var2) / (n1 + n2 - 2)
    
    # T-statistic
    t_statistic = (mean1 - mean2) / Math.sqrt(pooled_var * (1.0/n1 + 1.0/n2))
    
    # Degrees of freedom
    df = n1 + n2 - 2
    
    # P-value (simplified)
    p_value = calculate_t_p_value(t_statistic, df)
    
    {
      t_statistic: t_statistic,
      p_value: p_value,
      df: df,
      mean1: mean1,
      mean2: mean2,
      significant: p_value < 0.05
    }
  end

  def paired_t_test(group1, group2)
    differences = group1.zip(group2).map { |x, y| x - y }
    
    mean_diff = calculate_mean(differences)
    std_diff = calculate_std_dev(differences)
    n = differences.length
    
    t_statistic = mean_diff / (std_diff / Math.sqrt(n))
    df = n - 1
    p_value = calculate_t_p_value(t_statistic, df)
    
    {
      t_statistic: t_statistic,
      p_value: p_value,
      df: df,
      mean_difference: mean_diff,
      significant: p_value < 0.05
    }
  end

  def calculate_p_value(statistic, df, test_type = :two_tailed)
    # Simplified p-value calculation
    # In practice, you'd use a proper statistical library
    case test_type
    when :two_tailed
      2 * (1 - normal_cdf(Math.abs(statistic)))
    else
      1 - normal_cdf(statistic)
    end
  end

  def normal_cdf(x)
    # Simplified normal CDF approximation
    0.5 * (1 + Math.erf(x / Math.sqrt(2)))
  end

  def calculate_t_p_value(t, df)
    # Simplified t-distribution p-value
    calculate_p_value(t, df)
  end

  def calculate_f_p_value(f, df1, df2)
    # Simplified F-distribution p-value
    calculate_p_value(f, df1 + df2)
  end

  def calculate_chi_square_p_value(chi_square, df)
    # Simplified chi-square p-value
    calculate_p_value(chi_square, df)
  end

  def calculate_correlation_p_value(correlation, n)
    # Simplified correlation p-value
    t = correlation * Math.sqrt((n - 2) / (1 - correlation ** 2))
    calculate_t_p_value(t, n - 2)
  end

  def matrix_multiply(a, b)
    a.map do |row|
      b.transpose.map do |col|
        row.zip(col).sum { |x, y| x * y }
      end
    end
  end

  def matrix_transpose(matrix)
    matrix.transpose
  end

  def solve_linear_system(a, b)
    # Simplified linear system solver
    # In practice, you'd use a proper linear algebra library
    n = a.length
    
    # Gaussian elimination (simplified)
    augmented = a.zip(b).map { |row, bi| row + [bi] }
    
    n.times do |i|
      # Pivot
      max_row = (i...n).max_by { |j| augmented[j][i].abs }
      augmented[i], augmented[max_row] = augmented[max_row], augmented[i]
      
      # Eliminate
      (i + 1...n).each do |j|
        factor = augmented[j][i] / augmented[i][i]
        (i...n + 1).each do |k|
          augmented[j][k] -= factor * augmented[i][k]
        end
      end
    end
    
    # Back substitution
    solution = Array.new(n, 0)
    (n - 1).downto(0) do |i|
      solution[i] = augmented[i][n]
      (i + 1...n).each do |j|
        solution[i] -= augmented[i][j] * solution[j]
      end
      solution[i] /= augmented[i][i]
    end
    
    solution
  end

  def calculate_adjusted_r_squared(r_squared, n, k)
    1 - ((1 - r_squared) * (n - 1)) / (n - k - 1)
  end
end
```

## Literature Review Management

### Bibliographic Database
```ruby
class BibliographicDatabase
  def initialize
    @papers = []
    @categories = {}
    @keywords = {}
    @authors = {}
    @venues = {}
  end

  def add_paper(title, authors, year, venue, abstract = nil, doi = nil)
    paper = {
      id: generate_id,
      title: title,
      authors: Array(authors),
      year: year,
      venue: venue,
      abstract: abstract,
      doi: doi,
      keywords: [],
      categories: [],
      citations: [],
      references: [],
      added_at: Time.now
    }
    
    @papers << paper
    index_paper(paper)
    
    puts "Added paper: #{title} (#{year})"
    paper[:id]
  end

  def add_keywords(paper_id, keywords)
    paper = find_paper(paper_id)
    return false unless paper
    
    paper[:keywords] = Array(keywords)
    keywords.each { |keyword| index_keyword(keyword, paper_id) }
    
    puts "Added keywords to paper #{paper_id}: #{keywords.join(', ')}"
  end

  def categorize_paper(paper_id, categories)
    paper = find_paper(paper_id)
    return false unless paper
    
    paper[:categories] = Array(categories)
    categories.each { |category| index_category(category, paper_id) }
    
    puts "Categorized paper #{paper_id}: #{categories.join(', ')}"
  end

  def search_by_title(query)
    results = @papers.select do |paper|
      paper[:title].downcase.include?(query.downcase)
    end
    
    puts "Found #{results.length} papers matching '#{query}' in title"
    results
  end

  def search_by_author(author)
    results = @papers.select do |paper|
      paper[:authors].any? { |a| a.downcase.include?(author.downcase) }
    end
    
    puts "Found #{results.length} papers by author '#{author}'"
    results
  end

  def search_by_keyword(keyword)
    paper_ids = @keywords[keyword.downcase] || []
    results = paper_ids.map { |id| find_paper(id) }.compact
    
    puts "Found #{results.length} papers with keyword '#{keyword}'"
    results
  end

  def search_by_year(year_range)
    start_year, end_year = year_range.is_a?(Range) ? [year_range.begin, year_range.end] : [year_range, year_range]
    
    results = @papers.select do |paper|
      paper[:year] >= start_year && paper[:year] <= end_year
    end
    
    puts "Found #{results.length} papers from #{start_year}-#{end_year}"
    results
  end

  def find_related_papers(paper_id, limit = 10)
    paper = find_paper(paper_id)
    return [] unless paper
    
    # Find papers with similar keywords
    keyword_matches = []
    paper[:keywords].each do |keyword|
      keyword_matches += search_by_keyword(keyword)
    end
    
    # Find papers by same authors
    author_matches = []
    paper[:authors].each do |author|
      author_matches += search_by_author(author)
    end
    
    # Combine and deduplicate
    all_matches = (keyword_matches + author_matches).uniq
    all_matches.reject { |p| p[:id] == paper_id }
    
    # Sort by relevance (simplified - by number of matching keywords)
    scored_matches = all_matches.map do |match|
      shared_keywords = paper[:keywords] & match[:keywords]
      shared_authors = paper[:authors] & match[:authors]
      
      score = shared_keywords.length * 2 + shared_authors.length * 3
      [match, score]
    end
    
    related = scored_matches.sort_by { |_, score| -score }.first(limit).map(&:first)
    
    puts "Found #{related.length} related papers to #{paper_id}"
    related
  end

  def generate_bibliography(style = :apa)
    bibliography = []
    
    @papers.each do |paper|
      case style
      when :apa
        entry = format_apa_citation(paper)
      when :mla
        entry = format_mla_citation(paper)
      when :chicago
        entry = format_chicago_citation(paper)
      else
        entry = format_apa_citation(paper)
      end
      
      bibliography << entry
    end
    
    bibliography.sort.join("\n")
  end

  def analyze_research_trends(year_range = 5)
    current_year = Time.now.year
    start_year = current_year - year_range
    
    recent_papers = search_by_year(start_year..current_year)
    
    # Analyze keyword trends
    keyword_trends = Hash.new(0)
    recent_papers.each do |paper|
      paper[:keywords].each { |keyword| keyword_trends[keyword] += 1 }
    end
    
    # Analyze venue trends
    venue_trends = Hash.new(0)
    recent_papers.each { |paper| venue_trends[paper[:venue]] += 1 }
    
    # Analyze author collaboration
    collaboration_network = build_collaboration_network(recent_papers)
    
    {
      total_papers: recent_papers.length,
      year_range: "#{start_year}-#{current_year}",
      top_keywords: keyword_trends.sort_by { |_, count| -count }.first(10),
      top_venues: venue_trends.sort_by { |_, count| -count }.first(10),
      collaboration_network: collaboration_network
    }
  end

  def export_to_bibtex
    bibtex = []
    
    @papers.each do |paper|
      key = generate_bibtex_key(paper)
      entry = []
      
      entry << "@{#{key},"
      entry << "  title = {#{paper[:title]}},"
      entry << "  author = {#{paper[:authors].join(' and ')}}," if paper[:authors].any?
      entry << "  year = {#{paper[:year]}},"
      entry << "  venue = {#{paper[:venue]}},"
      entry << "  abstract = {#{paper[:abstract]}," if paper[:abstract]
      entry << "  doi = {#{paper[:doi]}," if paper[:doi]
      entry << "}"
      
      bibtex << entry.join("\n")
    end
    
    bibtex.join("\n\n")
  end

  private

  def generate_id
    "paper_#{@papers.length + 1}"
  end

  def find_paper(id)
    @papers.find { |paper| paper[:id] == id }
  end

  def index_paper(paper)
    # Index authors
    paper[:authors].each { |author| index_author(author, paper[:id]) }
    
    # Index venue
    index_venue(paper[:venue], paper[:id])
  end

  def index_author(author, paper_id)
    @authors[author.downcase] ||= []
    @authors[author.downcase] << paper_id
  end

  def index_keyword(keyword, paper_id)
    @keywords[keyword.downcase] ||= []
    @keywords[keyword.downcase] << paper_id
  end

  def index_category(category, paper_id)
    @categories[category.downcase] ||= []
    @categories[category.downcase] << paper_id
  end

  def index_venue(venue, paper_id)
    @venues[venue.downcase] ||= []
    @venues[venue.downcase] << paper_id
  end

  def format_apa_citation(paper)
    authors = paper[:authors].map { |author| 
      author.split(', ').reverse.join(' ') 
    }.join(', ')
    
    if paper[:authors].length > 2
      authors = paper[:authors].first + ", et al."
    end
    
    "#{authors} (#{paper[:year]}). #{paper[:title]}. *#{paper[:venue]}*.#{paper[:doi] ? " https://doi.org/#{paper[:doi]}" : ""}"
  end

  def format_mla_citation(paper)
    authors = paper[:authors].join(', ')
    
    if paper[:authors].length > 2
      authors = paper[:authors].first + ", et al."
    end
    
    "#{authors}. \"#{paper[:title].downcase.capitalize}.\" *#{paper[:venue]}*, #{paper[:year]}.#{paper[:doi] ? " doi:#{paper[:doi]}" : ""}"
  end

  def format_chicago_citation(paper)
    authors = paper[:authors].join(', ')
    
    "#{authors}. \"#{paper[:title].downcase.capitalize}.\" #{paper[:venue]} (#{paper[:year]}).#{paper[:doi] ? " https://doi.org/#{paper[:doi]}" : ""}"
  end

  def generate_bibtex_key(paper)
    first_author = paper[:authors].first.split(',').first.downcase.gsub(/[^a-z]/, '')
    year = paper[:year]
    title_word = paper[:title].split(' ').first.downcase.gsub(/[^a-z]/, '')
    
    "#{first_author}#{year}#{title_word}"
  end

  def build_collaboration_network(papers)
    network = Hash.new(0)
    
    papers.each do |paper|
      paper[:authors].combination(2) do |author1, author2|
        pair = [author1, author2].sort
        network[pair] += 1
      end
    end
    
    network.sort_by { |_, count| -count }.first(20).to_h
  end
end
```

## Best Practices

1. **Research Ethics**: Follow ethical guidelines for human and animal research
2. **Data Integrity**: Ensure data accuracy and reproducibility
3. **Statistical Validity**: Use appropriate statistical methods and sample sizes
4. **Documentation**: Maintain thorough records of methods and results
5. **Peer Review**: Seek feedback from colleagues and experts
6. **Open Science**: Consider sharing data and code for transparency
7. **Literature Review**: Stay current with relevant research

## Conclusion

Ruby provides powerful tools for conducting research, from experimental design to data analysis and literature management. While not as specialized as dedicated statistical software, Ruby's flexibility and extensive libraries make it an excellent choice for custom research workflows and automation.

## Further Reading

- [Research Methods Knowledge Base](https://conjointly.com/knowledge/)
- [Ruby for Data Science](https://github.com/sciruby)
- [Statistical Analysis with Ruby](https://github.com/SciRuby/statsample)
- [Research Ethics Guidelines](https://www.nih.gov/research-training/nih-policy-research-conduct)
