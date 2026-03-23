# Puzzle Challenges in Ruby

## Overview

Puzzle challenges are excellent for developing logical thinking, problem-solving skills, and algorithmic creativity. This guide covers various classic puzzles and brain teasers implemented in Ruby, from mathematical puzzles to logic problems.

## Mathematical Puzzles

### FizzBuzz Challenge
```ruby
class FizzBuzz
  def self.basic_solution(n)
    result = []
    
    (1..n).each do |i|
      if i % 15 == 0
        result << "FizzBuzz"
      elsif i % 3 == 0
        result << "Fizz"
      elsif i % 5 == 0
        result << "Buzz"
      else
        result << i.to_s
      end
    end
    
    result
  end

  def self.ternary_solution(n)
    result = []
    
    (1..n).each do |i|
      output = ""
      output += "Fizz" if i % 3 == 0
      output += "Buzz" if i % 5 == 0
      output = i.to_s if output.empty?
      
      result << output
    end
    
    result
  end

  def self.functional_solution(n)
    (1..n).map do |i|
      fizz = i % 3 == 0 ? "Fizz" : ""
      buzz = i % 5 == 0 ? "Buzz" : ""
      output = fizz + buzz
      output.empty? ? i.to_s : output
    end
  end

  def self.configurable_solution(n, rules = {})
    default_rules = { 3 => "Fizz", 5 => "Buzz" }
    merged_rules = default_rules.merge(rules)
    
    (1..n).map do |i|
      output = ""
      
      merged_rules.each do |divisor, word|
        output += word if i % divisor == 0
      end
      
      output.empty? ? i.to_s : output
    end
  end

  def self.performance_test(n = 100000)
    puts "Performance test with #{n} numbers"
    
    Benchmark.bm(15) do |x|
      x.report("Basic:") { basic_solution(n) }
      x.report("Ternary:") { ternary_solution(n) }
      x.report("Functional:") { functional_solution(n) }
    end
  end
end

# Usage examples
puts FizzBuzz.basic_solution(15)
puts FizzBuzz.ternary_solution(15)
puts FizzBuzz.functional_solution(15)

# Custom rules
custom_rules = { 2 => "Foo", 7 => "Bar", 3 => "Fizz", 5 => "Buzz" }
puts FizzBuzz.configurable_solution(21, custom_rules)

FizzBuzz.performance_test(10000)
```

### Prime Number Spiral
```ruby
class PrimeSpiral
  def self.generate_spiral(size)
    return [] if size <= 0
    
    # Create spiral matrix
    matrix = create_spiral_matrix(size)
    
    # Highlight primes
    matrix.map do |row|
      row.map { |num| prime?(num) ? num : " " }
    end
  end

  def self.print_spiral(size)
    spiral = generate_spiral(size)
    
    max_num = size * size
    cell_width = max_num.to_s.length
    
    spiral.each do |row|
      row.each do |cell|
        if cell == " "
          print " " * cell_width + " "
        else
          print cell.to_s.rjust(cell_width) + " "
        end
      end
      puts
    end
  end

  def self.analyze_diagonal_primes(size)
    spiral = create_spiral_matrix(size)
    
    # Diagonal from top-left to bottom-right
    main_diagonal = []
    size.times { |i| main_diagonal << spiral[i][i] }
    
    # Diagonal from top-right to bottom-left
    anti_diagonal = []
    size.times { |i| anti_diagonal << spiral[i][size - 1 - i] }
    
    main_primes = main_diagonal.select { |num| prime?(num) }
    anti_primes = anti_diagonal.select { |num| prime?(num) }
    
    {
      size: size,
      main_diagonal: main_diagonal,
      anti_diagonal: anti_diagonal,
      main_diagonal_primes: main_primes,
      anti_diagonal_primes: anti_primes,
      main_diagonal_prime_count: main_primes.length,
      anti_diagonal_prime_count: anti_primes.length
    }
  end

  def self.prime_ratio_spiral(size)
    spiral = create_spiral_matrix(size)
    
    # Calculate prime ratio for each position
    ratio_spiral = spiral.map.with_index do |row, i|
      row.map.with_index do |num, j|
        {
          position: [i, j],
          number: num,
          is_prime: prime?(num),
          prime_ratio: calculate_prime_ratio(num, size * size)
        }
      end
    end
    
    ratio_spiral
  end

  private

  def self.create_spiral_matrix(size)
    matrix = Array.new(size) { Array.new(size) }
    
    # Fill with numbers from 1 to size^2
    num = 1
    (0...size).each do |i|
      (0...size).each do |j|
        matrix[i][j] = num
        num += 1
      end
    end
    
    matrix
  end

  def self.prime?(n)
    return false if n <= 1
    return true if n <= 3
    return false if n % 2 == 0 || n % 3 == 0
    
    i = 5
    w = 2
    while i * i <= n
      return false if n % i == 0
      i += w
      w = 6 - w
    end
    
    true
  end

  def self.calculate_prime_ratio(num, total)
    # Simplified prime ratio calculation
    prime_count = count_primes_up_to(num)
    prime_count.to_f / total
  end

  def self.count_primes_up_to(n)
    count = 0
    (2..n).each { |i| count += 1 if prime?(i) }
    count
  end
end

# Usage examples
puts "5x5 Prime Spiral:"
PrimeSpiral.print_spiral(5)

analysis = PrimeSpiral.analyze_diagonal_primes(7)
puts "\nDiagonal Analysis (7x7):"
puts "Main diagonal primes: #{analysis[:main_diagonal_prime_count]}"
puts "Anti-diagonal primes: #{analysis[:anti_diagonal_prime_count]}"
puts "Main diagonal: #{analysis[:main_diagonal]}"
puts "Anti-diagonal: #{analysis[:anti_diagonal]}"

# 10x10 spiral would be too large to print, but we can analyze it
large_analysis = PrimeSpiral.analyze_diagonal_primes(10)
puts "\n10x10 Diagonal Analysis:"
puts "Main diagonal primes: #{large_analysis[:main_diagonal_prime_count]}"
puts "Anti-diagonal primes: #{large_analysis[:anti_diagonal_prime_count]}"
```

### Collatz Conjecture
```ruby
class CollatzConjecture
  def self.collatz_sequence(start)
    return [1] if start <= 0
    
    sequence = [start]
    current = start
    
    while current != 1
      if current.even?
        current = current / 2
      else
        current = 3 * current + 1
      end
      sequence << current
    end
    
    sequence
  end

  def self.max_sequence_in_range(start, finish)
    max_sequence = []
    max_start = start
    
    (start..finish).each do |num|
      sequence = collatz_sequence(num)
      if sequence.length > max_sequence.length
        max_sequence = sequence
        max_start = num
      end
    end
    
    {
      start: max_start,
      sequence: max_sequence,
      length: max_sequence.length
    }
  end

  def self.analyze_range(start, finish)
    sequences = {}
    max_length = 0
    max_sequence_start = start
    total_steps = 0
    
    (start..finish).each do |num|
      sequence = collatz_sequence(num)
      sequences[num] = {
        sequence: sequence,
        length: sequence.length,
        steps: sequence.length - 1,
        peak_value: sequence.max
      }
      
      total_steps += sequence.length - 1
      
      if sequence.length > max_length
        max_length = sequence.length
        max_sequence_start = num
      end
    end
    
    {
      range: "#{start}-#{finish}",
      sequences: sequences,
      max_length: max_length,
      max_sequence_start: max_sequence_start,
      average_steps: total_steps.to_f / (finish - start + 1),
      total_steps: total_steps
    }
  end

  def self.visualize_sequence(start)
    sequence = collatz_sequence(start)
    
    puts "Collatz sequence for #{start}:"
    puts "Length: #{sequence.length}"
    puts "Peak value: #{sequence.max}"
    puts "Sequence: #{sequence.first(10).join(', ')}#{'...' if sequence.length > 10}"
    
    # Simple ASCII visualization
    max_val = sequence.max
    height = 10
    
    sequence.each_with_index do |num, i|
      bar_length = (num.to_f / max_val * height).round
      bar = "█" * bar_length
      puts "#{i.to_s.rjust(3)}: #{bar} #{num}"
    end
  end

  def self.find_cycles(max_iterations = 1000)
    cycles = {}
    
    (1..max_iterations).each do |start|
      sequence = collatz_sequence(start)
      
      # Look for cycles (other than the trivial 4-2-1 cycle)
      if sequence.length > 10
        last_10 = sequence.last(10)
        if last_10 == [4, 2, 1, 4, 2, 1, 4, 2, 1, 4]
          cycles[start] = "Trivial cycle reached"
        else
          # Check for other repeating patterns
          (5..sequence.length / 2).each do |cycle_length|
            pattern = sequence.last(cycle_length)
            previous = sequence[-(2 * cycle_length)..-cycle_length - 1]
            
            if pattern == previous
              cycles[start] = "Cycle of length #{cycle_length}: #{pattern}"
              break
            end
          end
        end
      end
    end
    
    cycles
  end

  private

  def self.even?(n)
    n % 2 == 0
  end
end

# Usage examples
puts "Collatz sequence for 7:"
puts CollatzConjecture.collatz_sequence(7)

puts "\nCollatz sequence for 27 (notable for its length):"
sequence_27 = CollatzConjecture.collatz_sequence(27)
puts "Length: #{sequence_27.length}"
puts "First 20: #{sequence_27.first(20)}"
puts "Last 10: #{sequence_27.last(10)}"

puts "\nMax sequence in range 1-100:"
max_result = CollatzConjecture.max_sequence_in_range(1, 100)
puts "Start: #{max_result[:start]}"
puts "Length: #{max_result[:length]}"

puts "\nAnalysis of range 1-20:"
analysis = CollatzConjecture.analyze_range(1, 20)
puts "Max length: #{analysis[:max_length]} (#{analysis[:max_sequence_start]})"
puts "Average steps: #{analysis[:average_steps].round(2)}"

CollatzConjecture.visualize_sequence(13)

puts "\nChecking for cycles up to 1000:"
cycles = CollatzConjecture.find_cycles(1000)
if cycles.empty?
  puts "No unusual cycles found (all reach the trivial 4-2-1 cycle)"
else
  cycles.each { |start, cycle| puts "#{start}: #{cycle}" }
end
```

## Logic Puzzles

### Tower of Hanoi
```ruby
class TowerOfHanoi
  def initialize(num_disks)
    @num_disks = num_disks
    @moves = []
    @towers = {
      'A' => (1..num_disks).to_a.reverse,
      'B' => [],
      'C' => []
    }
  end

  def solve
    puts "Solving Tower of Hanoi with #{@num_disks} disks"
    move_disks(@num_disks, 'A', 'C', 'B')
    @moves
  end

  def print_solution
    puts "Tower of Hanoi Solution (#{@num_disks} disks):"
    puts "Moves required: #{@moves.length}"
    puts "Minimum moves: #{2**@num_disks - 1}"
    puts ""
    
    @moves.each_with_index do |move, i|
      puts "#{i + 1}. Move disk from #{move[:from]} to #{move[:to]}"
    end
  end

  def visualize_moves
    puts "Initial state:"
    print_towers
    
    @moves.each_with_index do |move, i|
      puts "\nMove #{i + 1}: #{move[:from]} → #{move[:to]}"
      
      # Perform the move
      disk = @towers[move[:from]].pop
      @towers[move[:to]].push(disk)
      
      print_towers
    end
  end

  def solve_with_animation(delay = 0.5)
    puts "Solving Tower of Hanoi with #{@num_disks} disks (animated)"
    print_towers
    
    move_disks_animated(@num_disks, 'A', 'C', 'B', delay)
    
    puts "\nSolved! Final state:"
    print_towers
  end

  private

  def move_disks(n, from, to, aux)
    return if n == 0
    
    # Move n-1 disks from source to auxiliary
    move_disks(n - 1, from, aux, to)
    
    # Move the nth disk from source to destination
    @moves << { from: from, to: to }
    
    # Move n-1 disks from auxiliary to destination
    move_disks(n - 1, aux, to, from)
  end

  def move_disks_animated(n, from, to, aux, delay)
    return if n == 0
    
    # Move n-1 disks from source to auxiliary
    move_disks_animated(n - 1, from, aux, to, delay)
    
    # Move the nth disk from source to destination
    disk = @towers[from].pop
    @towers[to].push(disk)
    
    puts "\nMove disk #{disk} from #{from} to #{to}"
    print_towers
    sleep(delay)
    
    # Move n-1 disks from auxiliary to destination
    move_disks_animated(n - 1, aux, to, from, delay)
  end

  def print_towers
    max_height = [@towers['A'].length, @towers['B'].length, @towers['C'].length].max
    
    (max_height - 1).downto(0) do |level|
      line = ""
      
      ['A', 'B', 'C'].each do |tower|
        if level < @towers[tower].length
          disk = @towers[tower][level]
          line += " #{disk.to_s.ljust(2)} "
        else
          line += "    "
        end
      end
      
      puts line
    end
    
    puts "A   B   C"
    puts "--- --- ---"
  end
end

# Usage example
hanoi = TowerOfHanoi.new(3)
moves = hanoi.solve
hanoi.print_solution

puts "\nAnimated solution:"
hanoi2 = TowerOfHanoi.new(4)
hanoi2.solve_with_animation(0.1)
```

### N-Queens Problem
```ruby
class NQueens
  def initialize(n)
    @n = n
    @solutions = []
  end

  def solve
    @solutions = []
    board = Array.new(@n) { Array.new(@n, false) }
    solve_recursive(board, 0)
    @solutions
  end

  def print_solutions(limit = 5)
    solutions_to_print = @solutions.first(limit)
    
    puts "N-Queens Solutions for #{@n}x#{@n} board:"
    puts "Total solutions: #{@solutions.length}"
    puts "Showing first #{solutions_to_print.length} solutions:"
    puts ""
    
    solutions_to_print.each_with_index do |solution, i|
      puts "Solution #{i + 1}:"
      print_board(solution)
      puts
    end
  end

  def count_solutions
    @solutions = []
    board = Array.new(@n) { Array.new(@n, false) }
    solve_recursive(board, 0)
    @solutions.length
  end

  def find_first_solution
    @solutions = []
    board = Array.new(@n) { Array.new(@n, false) }
    solve_recursive(board, 0)
    @solutions.first
  end

  def solve_with_backtracking
    @solutions = []
    board = Array.new(@n) { Array.new(@n, false) }
    solve_with_backtracking_recursive(board, 0)
    @solutions
  end

  def solve_with_bitmask
    @solutions = []
    solve_bitmask_recursive(0, 0, 0, 0)
    @solutions
  end

  def analyze_solutions
    solve if @solutions.empty?
    
    analysis = {
      total_solutions: @solutions.length,
      board_size: @n,
      symmetry_reduced_solutions: count_symmetry_reduced_solutions,
      unique_solutions: count_unique_solutions
    }
    
    # Analyze queen positions
    all_positions = []
    @solutions.each do |solution|
      positions = []
      solution.each_with_index do |row, col|
        row.each_with_index do |is_queen, j|
          positions << [row, j] if is_queen
        end
      end
      all_positions << positions
    end
    
    analysis[:position_analysis] = analyze_positions(all_positions)
    analysis
  end

  private

  def solve_recursive(board, row)
    return @solutions << board.map(&:dup) if row == @n
    
    (0...@n).each do |col|
      if is_safe_position(board, row, col)
        board[row][col] = true
        solve_recursive(board, row + 1)
        board[row][col] = false
      end
    end
  end

  def solve_with_backtracking_recursive(board, row)
    return @solutions << board.map(&:dup) if row == @n
    
    (0...@n).each do |col|
      if is_safe_position(board, row, col)
        board[row][col] = true
        solve_with_backtracking_recursive(board, row + 1)
        board[row][col] = false
      end
    end
  end

  def solve_bitmask_recursive(row, cols, diag1, diag2)
    return @solutions << reconstruct_solution(cols, diag1, diag2) if row == @n
    
    (0...@n).each do |col|
      mask = 1 << col
      d1_mask = 1 << (row + col)
      d2_mask = 1 << (row - col + @n - 1)
      
      if (cols & mask).zero? && (diag1 & d1_mask).zero? && (diag2 & d2_mask).zero?
        solve_bitmask_recursive(row + 1, cols | mask, diag1 | d1_mask, diag2 | d2_mask)
      end
    end
  end

  def reconstruct_solution(cols, diag1, diag2)
    solution = Array.new(@n) { Array.new(@n, false) }
    
    cols.to_s(2).each_char.with_index do |char, row|
      col = char.to_i
      solution[row][col] = true
    end
    
    solution
  end

  def is_safe_position(board, row, col)
    return false unless board[row][col] == false
    
    # Check column
    (0...row).each do |i|
      return false if board[i][col]
    end
    
    # Check diagonal
    (0...row).each do |i|
      if board[i][col - (row - i)] || board[i][col + (row - i)]
        return false
      end
    end
    
    true
  end

  def print_board(board)
    board.each do |row|
      row_line = ""
      row.each do |cell|
        row_line += cell ? "Q " : ". "  "
      end
      puts row_line
    end
  end

  def count_symmetry_reduced_solutions
    # Simplified symmetry reduction count
    (@solutions.length / 8.0).ceil
  end

  def count_unique_solutions
    # Simplified unique solution count
    (@solutions.length / 2.0).ceil
  end

  def analyze_positions(all_positions)
    position_counts = Hash.new(0)
    
    all_positions.each do |positions|
      positions.each do |row, col|
        key = "#{row},#{col}"
        position_counts[key] += 1
      end
    end
    
    {
      most_common_positions: position_counts.sort_by { |_, count| -count }.first(5),
      position_distribution: position_counts
    }
  end
end

# Usage examples
n_queens = NQueens.new(4)
solutions = n_queens.solve
n_queens.print_solutions

puts "\n8-Queens problem:"
n_queens8 = NQueens.new(8)
count = n_queens8.count_solutions
puts "Total solutions: #{count}"

puts "\nFirst solution for 8-Queens:"
first_solution = n_queens8.find_first_solution
n_queens8.print_board(first_solution)

puts "\nAnalysis for 8-Queens:"
analysis = n_queens8.analyze_solutions
puts "Total solutions: #{analysis[:total_solutions]}"
puts "Symmetry reduced: #{analysis[:symmetry_reduced_solutions]}"
puts "Unique solutions: #{analysis[:unique_solutions]}"
```

## String Puzzles

### Word Ladder
```ruby
class WordLadder
  def self.find_ladder(begin_word, end_word, word_list)
    return [] unless word_list.include?(begin_word) && word_list.include?(end_word)
    
    word_set = Set.new(word_list)
    queue = [[begin_word]]
    visited = Set.new([begin_word])
    
    while queue.any?
      current_path = queue.shift
      current_word = current_path.last
      
      if current_word == end_word
        return current_path
      end
      
      # Find all one-letter transformations
      neighbors = find_neighbors(current_word, word_set, visited)
      
      neighbors.each do |neighbor|
        new_path = current_path + [neighbor]
        queue << new_path
        visited.add(neighbor)
      end
    end
    
    []
  end

  def self.find_shortest_ladder(begin_word, end_word, word_list)
    ladder = find_ladder(begin_word, end_word, word_list)
    ladder.empty? ? [] : ladder
  end

  def self.find_all_ladders(begin_word, end_word, word_list)
    return [] unless word_list.include?(begin_word) && word_list.include?(end_word)
    
    word_set = Set.new(word_list)
    queue = [[begin_word]]
    visited = Set.new([begin_word])
    all_ladders = []
    found = false
    
    while queue.any?
      current_path = queue.shift
      current_word = current_path.last
      
      if current_word == end_word
        all_ladders << current_path
        found = true
      end
      
      # Only continue if we haven't found the end word yet
      next if found
      
      neighbors = find_neighbors(current_word, word_set, visited)
      
      neighbors.each do |neighbor|
        new_path = current_path + [neighbor]
        queue << new_path
        visited.add(neighbor)
      end
    end
    
    all_ladders
  end

  def self.word_ladder_length(begin_word, end_word, word_list)
    ladder = find_ladder(begin_word, end_word, word_list)
    ladder.empty? ? 0 : ladder.length - 1
  end

  def self.generate_word_graph(word_list)
    graph = {}
    
    word_list.each do |word|
      graph[word] = find_neighbors(word, Set.new(word_list), Set.new)
    end
    
    graph
  end

  def self.find_ladder_with_graph(begin_word, end_word, graph)
    return [] unless graph[begin_word] && graph[end_word]
    
    queue = [[begin_word]]
    visited = Set.new([begin_word])
    
    while queue.any?
      current_path = queue.shift
      current_word = current_path.last
      
      if current_word == end_word
        return current_path
      end
      
      graph[current_word].each do |neighbor|
        unless visited.include?(neighbor)
          new_path = current_path + [neighbor]
          queue << new_path
          visited.add(neighbor)
        end
      end
    end
    
    []
  end

  private

  def self.find_neighbors(word, word_set, visited)
    neighbors = []
    alphabet = ('a'..'z').to_a
    
    word.length.times do |i|
      original_char = word[i]
      
      alphabet.each do |char|
        next if char == original_char
        
        new_word = word[0...i] + char + word[i + 1..-1]
        
        if word_set.include?(new_word) && !visited.include?(new_word)
          neighbors << new_word
        end
      end
    end
    
    neighbors
  end
end

# Usage example
word_list = ["hot", "dot", "dog", "lot", "log", "cog"]
begin_word = "hit"
end_word = "cog"

puts "Word ladder from '#{begin_word}' to '#{end_word}':"
ladder = WordLadder.find_ladder(begin_word, end_word, word_list)
puts ladder.empty? ? "No ladder found" : ladder.join(" → ")

puts "\nShortest ladder length: #{WordLadder.word_ladder_length(begin_word, end_word, word_list)}"

puts "\nAll possible ladders:"
all_ladders = WordLadder.find_all_ladders(begin_word, end_word, word_list)
all_ladders.each_with_index do |ladder, i|
  puts "#{i + 1}: #{ladder.join(" → ")}"
end

# Larger example
word_list2 = ["aah", "aahed", "aahing", "aahi", "aal", "aalii", "aaliis", "aals"]
begin_word2 = "aah"
end_word2 = "aals"

puts "\nLadder from '#{begin_word2}' to '#{end_word2}':"
ladder2 = WordLadder.find_ladder(begin_word2, end_word2, word_list2)
puts ladder2.empty? ? "No ladder found" : ladder2.join(" → ")
```

### Palindrome Partitioning
```ruby
class PalindromePartitioner
  def self.partition_palindromes(s)
    return [] if s.empty?
    
    all_partitions = []
    current_partition = []
    
    partition_recursive(s, 0, current_partition, all_partitions)
    all_partitions
  end

  def self.min_cuts(s)
    return 0 if s.empty?
    
    n = s.length
    dp = Array.new(n + 1, Float::INFINITY)
    dp[0] = 0
    
    (1..n).each do |i|
      (0...i).each do |j|
        if palindrome?(s[j...i])
          dp[i] = [dp[i], dp[j] + 1].min
        end
      end
    end
    
    dp[n] - 1
  end

  def self.palindrome_partitions(s)
    min_cuts(s)
  end

  def self.all_palindromic_partitions(s)
    partition_palindromes(s)
  end

  def self.count_palindromic_partitions(s)
    n = s.length
    dp = Array.new(n + 1, 0)
    dp[0] = 1
    
    (1..n).each do |i|
      (0...i).each do |j|
        if palindrome?(s[j...i])
          dp[i] += dp[j]
        end
      end
    end
    
    dp[n]
  end

  def self.find_longest_palindrome(s)
    return "" if s.empty?
    
    longest = ""
    
    (0...s.length).each do |i|
      (i...s.length).each do |j|
        substring = s[i...j]
        if palindrome?(substring) && substring.length > longest.length
          longest = substring
        end
      end
    end
    
    longest
  end

  def self.expand_around_center(s)
    return "" if s.empty?
    
    longest = ""
    
    s.length.times do |i|
      # Odd length palindromes
      odd_palindrome = expand_from_center(s, i, i)
      if odd_palindrome.length > longest.length
        longest = odd_palindrome
      end
      
      # Even length palindromes
      even_palindrome = expand_from_center(s, i, i + 1)
      if even_palindrome.length > longest.length
        longest = even_palindrome
      end
    end
    
    longest
  end

  private

  def self.partition_recursive(s, start, current_partition, all_partitions)
    if start == s.length
      all_partitions << current_partition.dup
      return
    end
    
    (start...s.length).each do |end_pos|
      substring = s[start...end_pos]
      
      if palindrome?(substring)
        current_partition << substring
        partition_recursive(s, end_pos, current_partition, all_partitions)
        current_partition.pop
      end
    end
  end

  def self.palindrome?(s)
    return true if s.length <= 1
    
    left = 0
    right = s.length - 1
    
    while left < right
      return false if s[left] != s[right]
      left += 1
      right -= 1
    end
    
    true
  end

  def self.expand_from_center(s, left, right)
    while left >= 0 && right < s.length && s[left] == s[right]
      left -= 1
      right += 1
    end
    
    s[(left + 1)...right]
  end
end

# Usage examples
s = "aab"
puts "Palindromic partitions of '#{s}':"
partitions = PalindromePartitioner.partition_palindromes(s)
partitions.each_with_index do |partition, i|
  puts "#{i + 1}: #{partition.map { |p| "'#{p}'" }.join(' + ')}"
end

puts "\nMinimum cuts for '#{s}': #{PalindromePartitioner.min_cuts(s)}"

s2 = "racecar"
puts "\nLongest palindrome in '#{s2}': #{PalindromePartitioner.find_longest_palindrome(s2)}"
puts "Longest palindrome (expand around center): #{PalindromePartitioner.expand_around_center(s2)}"

s3 = "banana"
puts "\nCount palindromic partitions of '#{s3}': #{PalindromePartitioner.count_palindromic_partitions(s3)}"
```

## Pattern Puzzles

### Pascal's Triangle
```ruby
class PascalsTriangle
  def self.generate(num_rows)
    return [] if num_rows <= 0
    
    triangle = []
    
    (0...num_rows).each do |row|
      current_row = []
      
      (0..row).each do |col|
        if col == 0 || col == row
          current_row << 1
        else
          current_row << triangle[row - 1][col - 1] + triangle[row - 1][col]
        end
      end
      
      triangle << current_row
    end
    
    triangle
  end

  def self.generate_recursive(num_rows)
    return [] if num_rows <= 0
    
    triangle = []
    generate_row_recursive(num_rows, 0, triangle)
    triangle
  end

  def self.print_triangle(triangle)
    max_width = triangle.last.map { |num| num.to_s.length }.max
    
    triangle.each_with_index do |row, i|
      # Center the row
      padding = " " * ((max_width * (triangle.length - i - 1)) / 2)
      puts padding + row.map { |num| num.to_s.center(max_width) }.join(" ")
    end
  end

  def self.sum_row(row_number)
    return 0 if row_number <= 0
    
    triangle = generate(row_number)
    triangle[row_number - 1].sum
  end

  def self.get_element(row, col)
    return nil if row <= 0 || col < 0 || col > row
    
    triangle = generate(row)
    triangle[row - 1][col]
  end

  def self.is_palindromic_row(row_number)
    return false if row_number <= 0
    
    triangle = generate(row_number)
    row = triangle[row_number - 1]
    row == row.reverse
  end

  def self.find_palindromic_rows(limit)
    palindromic_rows = []
    
    (1..limit).each do |row|
      palindromic_rows << row if is_palindromic_row(row)
    end
    
    palindromic_rows
  end

  def self.binomial_coefficients(n)
    return [1] if n == 0
    
    triangle = generate(n)
    triangle.last
  end

  def self.sierpinski_triangle(num_rows)
    triangle = generate(num_rows)
    
    triangle.map do |row|
      row.map do |num|
        num.odd? ? 1 : 0
      end
    end
  end

  def self.print_sierpinski_triangle(num_rows)
    sierpinski = sierpinski_triangle(num_rows)
    
    sierpinski.each do |row|
      puts row.map { |num| num == 1 ? "█" : " " }.join(" ")
    end
  end

  private

  def self.generate_row_recursive(num_rows, current_row, triangle)
    return if current_row >= num_rows
    
    new_row = []
    
    (0..current_row).each do |col|
      if col == 0 || col == current_row
        new_row << 1
      else
        new_row << triangle[current_row - 1][col - 1] + triangle[current_row - 1][col]
      end
    end
    
    triangle << new_row
    generate_row_recursive(num_rows, current_row + 1, triangle)
  end
end

# Usage examples
puts "Pascal's Triangle (10 rows):"
triangle = PascalsTriangle.generate(10)
PascalsTriangle.print_triangle(triangle)

puts "\nRecursive Pascal's Triangle (7 rows):"
recursive_triangle = PascalsTriangle.generate_recursive(7)
PascalsTriangle.print_triangle(recursive_triangle)

puts "\nRow 5 sum: #{PascalsTriangle.sum_row(5)}"
puts "Element at (4, 2): #{PascalsTriangle.get_element(4, 2)}"
puts "Is row 6 palindromic? #{PascalsTriangle.is_palindromic_row(6)}"

puts "\nPalindromic rows up to 10:"
palindromic_rows = PascalsTriangle.find_palindromic_rows(10)
puts palindromic_rows.empty? ? "None found" : palindromic_rows.join(", ")

puts "\nBinomial coefficients for n=8:"
coefficients = PascalsTriangle.binomial_coefficients(8)
puts coefficients.join(", ")

puts "\nSierpinski Triangle (8 rows):"
PascalsTriangle.print_sierpinski_triangle(8)
```

## Challenge Problems

### Sudoku Solver
```ruby
class SudokuSolver
  def initialize(board)
    @board = board.map(&:dup)
    @size = 9
    @empty_cells = find_empty_cells
  end

  def solve
    solve_recursive(0)
  end

  def print_board
    @board.each_with_index do |row, i|
      row.each_with_index do |cell, j|
        print cell
        print "|" if j == 2 || j == 5
        print "-" if j == 8
      end
      puts if i == 2 || i == 5
      puts "---------------------"
    end
  end

  def is_valid?
    # Check rows
    (0...@size).each do |row|
      return false unless valid_row?(row)
    end
    
    # Check columns
    (0...@size).each do |col|
      return false unless valid_column?(col)
    end
    
    # Check 3x3 boxes
    (0...@size).step(3) do |box_row|
      (0...@size).step(3) do |box_col|
        return false unless valid_box?(box_row, box_col)
      end
    end
    
    true
  end

  def find_empty_cells
    empty_cells = []
    
    (0...@size).each do |row|
      (0...@size).each do |col|
        empty_cells << [row, col] if @board[row][col] == 0
      end
    end
    
    empty_cells
  end

  def count_solutions
    @solutions = []
    solve_all_recursive(0)
    @solutions.length
  end

  private

  def solve_recursive(cell_index)
    return true if cell_index >= @empty_cells.length
    
    row, col = @empty_cells[cell_index]
    
    (1..9).each do |num|
      if valid_move?(row, col, num)
        @board[row][col] = num
        
        if solve_recursive(cell_index + 1)
          return true
        end
        
        @board[row][col] = 0
      end
    end
    
    false
  end

  def solve_all_recursive(cell_index)
    return @solutions << @board.map(&:dup) if cell_index >= @empty_cells.length
    
    row, col = @empty_cells[cell_index]
    
    (1..9).each do |num|
      if valid_move?(row, col, num)
        @board[row][col] = num
        solve_all_recursive(cell_index + 1)
        @board[row][col] = 0
      end
    end
  end

  def valid_move?(row, col, num)
    # Check row
    (0...@size).each do |j|
      return false if @board[row][j] == num
    end
    
    # Check column
    (0...@size).each do |i|
      return false if @board[i][col] == num
    end
    
    # Check 3x3 box
    box_row = (row / 3) * 3
    box_col = (col / 3) * 3
    
    (box_row...box_row + 3).each do |i|
      (box_col...box_col + 3).each do |j|
        return false if @board[i][j] == num
      end
    end
    
    true
  end

  def valid_row?(row)
    numbers = @board[row].reject { |num| num == 0 }
    numbers == numbers.uniq
  end

  def valid_column?(col)
    numbers = @board.map { |row| row[col] }.reject { |num| num == 0 }
    numbers == numbers.uniq
  end

  def valid_box?(box_row, box_col)
    numbers = []
    
    (box_row...box_row + 3).each do |i|
      (box_col...box_col + 3).each do |j|
        numbers << @board[i][j] if @board[i][j] != 0
      end
    end
    
    numbers == numbers.uniq
  end
end

# Usage example
# Easy Sudoku puzzle
board = [
  [5, 3, 0, 0, 7, 0, 0, 0, 0],
  [6, 0, 0, 1, 9, 5, 0, 0, 0],
  [0, 9, 8, 0, 0, 0, 0, 6, 0],
  [8, 0, 0, 0, 6, 0, 0, 0, 3],
  [4, 0, 0, 8, 0, 3, 0, 0, 1],
  [7, 0, 0, 0, 2, 0, 0, 0, 6],
  [0, 6, 0, 0, 0, 0, 2, 8, 0],
  [0, 0, 0, 4, 1, 9, 0, 0, 5],
  [0, 0, 0, 0, 8, 0, 0, 7, 9]
]

solver = SudokuSolver.new(board)
puts "Initial Sudoku:"
solver.print_board

puts "\nSolving..."
if solver.solve
  puts "\nSolved Sudoku:"
  solver.print_board
else
  puts "No solution found"
end
```

## Best Practices

1. **Understand the Problem**: Read the problem statement carefully
2. **Start Simple**: Begin with a basic solution before optimizing
3. **Test Edge Cases**: Consider empty inputs, single elements, etc.
4. **Time Complexity**: Analyze the efficiency of your solution
5. **Space Complexity**: Consider memory usage
6. **Code Clarity**: Write readable and maintainable code
7. **Validation**: Test your solution with known examples

## Conclusion

Puzzle challenges are excellent for developing problem-solving skills and algorithmic thinking. By working through these challenges in Ruby, you'll improve your understanding of data structures, algorithms, and Ruby's capabilities while having fun solving interesting problems.

## Further Reading

- [Project Euler](https://projecteuler.net/)
- [Codewars](https://www.codewars.com/)
- [HackerRank](https://www.hackerrank.com/)
- [LeetCode](https://leetcode.com/)
- [Advent of Code](https://adventofcode.com/)
