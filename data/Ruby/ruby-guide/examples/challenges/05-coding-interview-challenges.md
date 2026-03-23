# Coding Interview Challenges in Ruby

## Overview

Coding interview challenges are designed to test your problem-solving skills, algorithmic thinking, and Ruby programming abilities. This guide covers common interview questions, problem-solving strategies, and best practices for technical interviews.

## Array and String Problems

### Two Sum Problem
```ruby
class TwoSum
  # O(n^2) brute force solution
  def self.brute_force(nums, target)
    (0...nums.length).each do |i|
      (i + 1...nums.length).each do |j|
        return [i, j] if nums[i] + nums[j] == target
      end
    end
    nil
  end

  # O(n) hash table solution
  def self.hash_table(nums, target)
    hash_map = {}
    
    nums.each_with_index do |num, i|
      complement = target - num
      
      return [hash_map[complement], i] if hash_map.key?(complement)
      
      hash_map[num] = i
    end
    
    nil
  end

  # O(n log n) two-pointer solution (requires sorted input)
  def self.two_pointer(nums, target)
    nums_with_index = nums.each_with_index.to_a.sort_by(&:first)
    
    left = 0
    right = nums_with_index.length - 1
    
    while left < right
      sum = nums_with_index[left][0] + nums_with_index[right][0]
      
      if sum == target
        return [nums_with_index[left][1], nums_with_index[right][1]].sort
      elsif sum < target
        left += 1
      else
        right -= 1
      end
    end
    
    nil
  end

  # Handle multiple solutions
  def self.all_pairs(nums, target)
    pairs = []
    seen = {}
    
    nums.each_with_index do |num, i|
      complement = target - num
      
      if seen.key?(complement)
        pairs << [seen[complement], i]
      end
      
      seen[num] = i
    end
    
    pairs
  end

  # Three Sum problem (variation)
  def self.three_sum(nums, target)
    nums.sort!
    result = []
    
    (0...nums.length - 2).each do |i|
      next if i > 0 && nums[i] == nums[i - 1]
      
      left = i + 1
      right = nums.length - 1
      
      while left < right
        sum = nums[i] + nums[left] + nums[right]
        
        if sum == target
          result << [nums[i], nums[left], nums[right]]
          left += 1
          right -= 1
          
          # Skip duplicates
          left += 1 while left < right && nums[left] == nums[left - 1]
          right -= 1 while left < right && nums[right] == nums[right + 1]
        elsif sum < target
          left += 1
        else
          right -= 1
        end
      end
    end
    
    result
  end
end

# Usage examples
nums = [2, 7, 11, 15]
target = 9

puts "Brute force: #{TwoSum.brute_force(nums, target)}"
puts "Hash table: #{TwoSum.hash_table(nums, target)}"

sorted_nums = nums.sort
puts "Two pointer: #{TwoSum.two_pointer(sorted_nums, target)}"

puts "All pairs for target 9: #{TwoSum.all_pairs(nums, 9)}"

three_nums = [1, 2, 3, 4, 5, 6, 7, 8, 9]
target_three = 12
puts "Three sum for target #{target_three}: #{TwoSum.three_sum(three_nums, target_three)}"
```

### Valid Parentheses
```ruby
class ValidParentheses
  def self.is_valid?(s)
    stack = []
    pairs = { ')' => '(', '}' => '{', ']' => '[' }
    
    s.each_char do |char|
      if char == '(' || char == '{' || char == '['
        stack.push(char)
      elsif char == ')' || char == '}' || char == ']'
        return false if stack.empty? || stack.pop != pairs[char]
      end
    end
    
    stack.empty?
  end

  def self.is_valid_with_counting?(s)
    counts = { '(' => 0, '{' => 0, '[' => 0 }
    
    s.each_char do |char|
      case char
      when '(', '{', '['
        counts[char] += 1
      when ')'
        counts['('] -= 1
        return false if counts['('] < 0
      when '}'
        counts['{'] -= 1
        return false if counts['{'] < 0
      when ']'
        counts['['] -= 1
        return false if counts['['] < 0
      end
    end
    
    counts.values.all?(&:zero?)
  end

  def self.minimum_add_to_make_valid(s)
    stack = []
    additions = 0
    
    s.each_char do |char|
      if char == '(' || char == '{' || char == '['
        stack.push(char)
      elsif char == ')' || char == '}' || char == ']'
        if stack.empty?
          additions += 1
        else
          stack.pop
        end
      end
    end
    
    additions + stack.length
  end

  def self.longest_valid_parentheses(s)
    max_length = 0
    stack = [-1]  # Base index
    
    s.each_char.with_index do |char, i|
      if char == '('
        stack.push(i)
      else
        stack.pop
        if stack.empty?
          stack.push(i)
        else
          length = i - stack.last
          max_length = [max_length, length].max
        end
      end
    end
    
    max_length
  end
end

# Usage examples
test_cases = ["()", "()[]{}", "(]", "([{}])", "([)]", "(()", "(()())"]

test_cases.each do |test|
  puts "#{test}: #{ValidParentheses.is_valid?(test)}"
end

puts "Minimum additions to make '())' valid: #{ValidParentheses.minimum_add_to_make_valid('())')}"
puts "Minimum additions to make '([)]' valid: #{ValidParentheses.minimum_add_to_make_valid('([)]')}"

puts "Longest valid parentheses in '(()())': #{ValidParentheses.longest_valid_parentheses('(()())')}"
puts "Longest valid parentheses in '())()': #{ValidParentheses.longest_valid_parentheses('())()')}"
```

### String Reversal
```ruby
class StringReversal
  def self.reverse_string(s)
    s.reverse
  end

  def self.reverse_string_manual(s)
    chars = s.chars
    left = 0
    right = chars.length - 1
    
    while left < right
      chars[left], chars[right] = chars[right], chars[left]
      left += 1
      right -= 1
    end
    
    chars.join
  end

  def self.reverse_words_in_string(s)
    words = s.split(' ')
    words.reverse.join(' ')
  end

  def self.reverse_words_in_string_manual(s)
    chars = s.chars
    n = chars.length
    
    # Reverse the entire string
    reverse_chars_manual(chars)
    
    # Reverse each word
    start = 0
    (0...n).each do |i|
      if i == n - 1 || chars[i + 1] == ' '
        end_idx = i
        reverse_chars_range(chars, start, end_idx)
        start = i + 2
      end
    end
    
    chars.join
  end

  def self.reverse_vowels(s)
    vowels = Set.new(['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'])
    chars = s.chars
    left = 0
    right = chars.length - 1
    
    while left < right
      # Find next vowel from left
      while left < right && !vowels.include?(chars[left])
        left += 1
      end
      
      # Find next vowel from right
      while left < right && !vowels.include?(chars[right])
        right -= 1
      end
      
      break if left >= right
      
      # Swap vowels
      chars[left], chars[right] = chars[right], chars[left]
      left += 1
      right -= 1
    end
    
    chars.join
  end

  private

  def self.reverse_chars_manual(chars)
    left = 0
    right = chars.length - 1
    
    while left < right
      chars[left], chars[right] = chars[right], chars[left]
      left += 1
      right -= 1
    end
  end

  def self.reverse_chars_range(chars, start, finish)
    while start < finish
      chars[start], chars[finish] = chars[finish], chars[start]
      start += 1
      finish -= 1
    end
  end
end

# Usage examples
puts "Reverse 'hello': #{StringReversal.reverse_string('hello')}"
puts "Reverse manual 'world': #{StringReversal.reverse_string_manual('world')}"

sentence = "Let's take coding interviews to a new level"
puts "Reverse words: #{StringReversal.reverse_words_in_string(sentence)}"

puts "Reverse words manual: #{StringReversal.reverse_words_in_string_manual(sentence)}"

puts "Reverse vowels in 'hello': #{StringReversal.reverse_vowels('hello')}"
puts "Reverse vowels in 'leetcode': #{StringReversal.reverse_vowels('leetcode')}"
```

## Tree and Graph Problems

### Binary Tree Traversal
```ruby
class TreeNode
  attr_accessor :val, :left, :right

  def initialize(val = 0, left = nil, right = nil)
    @val = val
    @left = left
    @right = right
  end
end

class BinaryTreeTraversal
  def self.inorder_traversal(root)
    return [] unless root
    
    result = []
    stack = []
    current = root
    
    while current || stack.any?
      while current
        stack.push(current)
        current = current.left
      end
      
      current = stack.pop
      result << current.val
      current = current.right
    end
    
    result
  end

  def self.inorder_traversal_recursive(root)
    return [] unless root
    
    result = []
    inorder_recursive_helper(root, result)
    result
  end

  def self.preorder_traversal(root)
    return [] unless root
    
    result = []
    stack = [root]
    
    while stack.any?
      node = stack.pop
      result << node.val
      
      # Push right first, then left (stack is LIFO)
      stack.push(node.right) if node.right
      stack.push(node.left) if node.left
    end
    
    result
  end

  def self.postorder_traversal(root)
    return [] unless root
    
    result = []
    stack = []
    last_visited = nil
    current = root
    
    while current || stack.any?
      if current
        stack.push(current)
        current = current.left
      else
        peek_node = stack.last
        
        if peek_node.right && peek_node.right != last_visited
          current = peek_node.right
        else
          node = stack.pop
          result << node.val
          last_visited = node
        end
      end
    end
    
    result
  end

  def self.level_order_traversal(root)
    return [] unless root
    
    result = []
    queue = [root]
    
    while queue.any?
      node = queue.shift
      result << node.val
      
      queue << node.left if node.left
      queue << node.right if node.right
    end
    
    result
  end

  def self.zigzag_traversal(root)
    return [] unless root
    
    result = []
    queue = [root]
    left_to_right = true
    
    while queue.any?
      level_size = queue.length
      level_nodes = []
      
      level_size.times do
        node = queue.shift
        level_nodes << node.val
        
        queue << node.left if node.left
        queue << node.right if node.right
      end
      
      result.concat(left_to_right ? level_nodes : level_nodes.reverse)
      left_to_right = !left_to_right
    end
    
    result
  end

  def self.maximum_depth(root)
    return 0 unless root
    
    max_depth = 0
    queue = [root]
    
    while queue.any?
      level_size = queue.length
      max_depth += 1
      
      level_size.times do
        node = queue.shift
        queue << node.left if node.left
        queue << node.right if node.right
      end
    end
    
    max_depth
  end

  def self.is_balanced(root)
    return true unless root
    
    balanced_recursive(root) != -1
  end

  def self.is_symmetric(root)
    return true unless root
    
    is_symmetric_recursive(root, root)
  end

  def self.lowest_common_ancestor(root, p, q)
    return nil unless root
    
    lca_recursive(root, p, q)
  end

  private

  def self.inorder_recursive_helper(node, result)
    return unless node
    
    inorder_recursive_helper(node.left, result)
    result << node.val
    inorder_recursive_helper(node.right, result)
  end

  def self.balanced_recursive(node)
    return 0 unless node
    
    left_height = balanced_recursive(node.left)
    return -1 if left_height == -1
    
    right_height = balanced_recursive(node.right)
    return -1 if right_height == -1
    
    return -1 if (left_height - right_height).abs > 1
    
    [left_height, right_height].max + 1
  end

  def self.is_symmetric_recursive(left, right)
    return true unless left && right
    
    left.val == right.val &&
      is_symmetric_recursive(left.left, right.right) &&
      is_symmetric_recursive(left.right, right.left)
  end

  def self.lca_recursive(node, p, q)
    return node if node == p || node == q
    
    left_lca = lca_recursive(node.left, p, q)
    return left_lca if left_lca
    
    right_lca = lca_recursive(node.right, p, q)
    return right_lca if right_lca
    
    node
  end
end

# Usage example
# Build a sample tree
#       1
#      / \
#     2   3
#    / \
#   4   5

root = TreeNode.new(1)
root.left = TreeNode.new(2)
root.right = TreeNode.new(3)
root.left.left = TreeNode.new(4)
root.left.right = TreeNode.new(5)

puts "In-order traversal: #{BinaryTreeTraversal.inorder_traversal(root)}"
puts "Pre-order traversal: #{BinaryTreeTraversal.preorder_traversal(root)}"
puts "Post-order traversal: #{BinaryTreeTraversal.postorder_traversal(root)}"
puts "Level-order traversal: #{BinaryTreeTraversal.level_order_traversal(root)}"
puts "Zigzag traversal: #{BinaryTreeTraversal.zigzag_traversal(root)}"
puts "Maximum depth: #{BinaryTreeTraversal.maximum_depth(root)}"
puts "Is balanced: #{BinaryTreeTraversal.is_balanced(root)}"
puts "Is symmetric: #{BinaryTreeTraversal.is_symmetric(root)}"

puts "LCA of 4 and 5: #{BinaryTreeTraversal.lowest_common_ancestor(root, 4, 5).val}"
```

### Graph Problems
```ruby
class GraphProblems
  def self.has_path_dfs(graph, start, target)
    visited = Set.new
    dfs_helper(graph, start, target, visited)
  end

  def self.has_path_bfs(graph, start, target)
    visited = Set.new
    queue = [start]
    
    while queue.any?
      current = queue.shift
      return true if current == target
      
      visited.add(current)
      
      graph[current]&.each do |neighbor|
        queue << neighbor unless visited.include?(neighbor)
      end
    end
    
    false
  end

  def self.shortest_path_bfs(graph, start, target)
    return [] unless graph[start] && graph[target]
    
    visited = Set.new([start])
    queue = [[start]]
    parent = { start => nil }
    
    while queue.any?
      path = queue.shift
      current = path.last
      
      return path if current == target
      
      graph[current]&.each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          parent[neighbor] = current
          queue << path + [neighbor]
        end
      end
    end
    
    []
  end

  def self.clone_graph(node)
    return nil unless node
    
    old_to_new = {}
    cloned_nodes = {}
    
    # First pass: clone all nodes
    queue = [node]
    while queue.any?
      current = queue.shift
      
      cloned_node = Node.new(current.val)
      old_to_new[current] = cloned_node
      cloned_nodes[current] = cloned_node
      
      current.neighbors&.each do |neighbor|
        queue << neighbor unless old_to_new.key?(neighbor)
      end
    end
    
    # Second pass: connect cloned nodes
    old_to_new.each do |old_node, new_node|
      old_node.neighbors&.each do |neighbor|
        new_node.neighbors << old_to_new[neighbor]
      end
    end
    
    old_to_new[node]
  end

  def self.course_schedule(num_courses, prerequisites)
    # Build graph
    graph = Array.new(num_courses) { [] }
    in_degree = Array.new(num_courses, 0)
    
    prerequisites.each do |course, prereq|
      graph[prereq] << course
      in_degree[course] += 1
    end
    
    # Find courses with no prerequisites
    queue = []
    num_courses.times do |i|
      queue << i if in_degree[i] == 0
    end
    
    result = []
    processed = 0
    
    while queue.any?
      course = queue.shift
      result << course
      processed += 1
      
      graph[course].each do |neighbor|
        in_degree[neighbor] -= 1
        queue << neighbor if in_degree[neighbor] == 0
      end
    end
    
    processed == num_courses ? result : []
  end

  def self.num_islands(grid)
    return 0 if grid.empty?
    
    rows = grid.length
    cols = grid[0].length
    visited = Array.new(rows) { Array.new(cols, false) }
    islands = 0
    
    (0...rows).each do |row|
      (0...cols).each do |col|
        if grid[row][col] == '1' && !visited[row][col]
          islands += 1
          dfs_islands(grid, row, col, visited)
        end
      end
    end
    
    islands
  end

  def self.word_ladder(begin_word, end_word, word_list)
    word_set = Set.new(word_list)
    return [] unless word_set.include?(begin_word) && word_set.include?(end_word)
    
    queue = [[begin_word]]
    visited = Set.new([begin_word])
    
    while queue.any?
      current_path = queue.shift
      current_word = current_path.last
      
      if current_word == end_word
        return current_path
      end
      
      neighbors = find_word_neighbors(current_word, word_set, visited)
      
      neighbors.each do |neighbor|
        new_path = current_path + [neighbor]
        queue << new_path
        visited.add(neighbor)
      end
    end
    
    []
  end

  private

  def self.dfs_helper(graph, current, target, visited)
    return true if current == target
    return false if visited.include?(current)
    
    visited.add(current)
    
    graph[current]&.each do |neighbor|
      return true if dfs_helper(graph, neighbor, target, visited)
    end
    
    false
  end

  def self.dfs_islands(grid, row, col, visited)
    return if row < 0 || row >= grid.length || col < 0 || col >= grid[0].length
    return if visited[row][col] || grid[row][col] == '0'
    
    visited[row][col] = true
    
    # Explore neighbors (up, down, left, right)
    dfs_islands(grid, row - 1, col, visited)
    dfs_islands(grid, row + 1, col, visited)
    dfs_islands(grid, row, col - 1, visited)
    dfs_islands(grid, row, col + 1, visited)
  end

  def self.find_word_neighbors(word, word_set, visited)
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

# Simple Node class for graph problems
class Node
  attr_accessor :val, :neighbors

  def initialize(val = 0)
    @val = val
    @neighbors = []
  end
end

# Usage examples
# Sample graph for path finding
graph = {
  'A' => ['B', 'C'],
  'B' => ['D'],
  'C' => ['D', 'E'],
  'D' => ['F'],
  'E' => ['F'],
  'F' => []
}

puts "Has path A to F (DFS): #{GraphProblems.has_path_dfs(graph, 'A', 'F')}"
puts "Has path A to F (BFS): #{GraphProblems.has_path_bfs(graph, 'A', 'F')}"
puts "Shortest path A to F: #{GraphProblems.shortest_path_bfs(graph, 'A', 'F')}"

# Clone graph example
node1 = Node.new(1)
node2 = Node.new(2)
node3 = Node.new(3)
node1.neighbors = [node2, node3]
node2.neighbors = [node3]

cloned = GraphProblems.clone_graph(node1)
puts "Cloned node value: #{cloned.val}"
puts "Cloned node neighbors: #{cloned.neighbors.map(&:val)}"

# Course schedule example
num_courses = 4
prerequisites = [[1, 0], [2, 0], [3, 1], [3, 2]]
puts "Course schedule: #{GraphProblems.course_schedule(num_courses, prerequisites)}"

# Number of islands example
grid = [
  ['1', '1', '0', '0', '0'],
  ['1', '1', '0', '0', '0'],
  ['0', '0', '1', '0', '0'],
  ['0', '0', '0', '1', '1'],
  ['0', '0', '0', '0', '0']
]
puts "Number of islands: #{GraphProblems.num_islands(grid)}"

# Word ladder example
word_list = ["hot", "dot", "dog", "lot", "log", "cog"]
begin_word = "hit"
end_word = "cog"
puts "Word ladder: #{GraphProblems.word_ladder(begin_word, end_word, word_list)}"
```

## Dynamic Programming

### Climbing Stairs
```ruby
class ClimbingStairs
  def self.climb_stairs(n)
    return 1 if n <= 1
    
    dp = Array.new(n + 1, 0)
    dp[0] = 1
    dp[1] = 1
    
    (2..n).each do |i|
      dp[i] = dp[i - 1] + dp[i - 2]
    end
    
    dp[n]
  end

  def self.climb_stairs_recursive(n)
    return 1 if n <= 1
    climb_stairs_recursive(n - 1) + climb_stairs_recursive(n - 2)
  end

  def self.climb_stairs_memoization(n)
    return 1 if n <= 1
    
    @memo ||= {}
    return @memo[n] if @memo[n]
    
    @memo[n] = climb_stairs_memoization(n - 1) + climb_stairs_memoization(n - 2)
  end

  def self.climb_stairs_with_steps(n)
    return [[1]] if n == 1
    
    dp = Array.new(n + 1) { [] }
    dp[1] = [[1]]
    
    (2..n).each do |i|
      dp[i] = dp[i - 1].map { |path| path + [1] }
      
      if i >= 2
        dp[i - 2].each do |path|
          dp[i] << path + [2]
        end
      end
    end
    
    dp[n]
  end

  def self.min_cost_climbing_stairs(cost)
    return 0 if cost.empty?
    
    n = cost.length
    dp = Array.new(n + 1, Float::INFINITY)
    dp[0] = 0
    
    (1...n).each do |i|
      (0...i).each do |j|
        if j + 1 == i
          dp[i] = [dp[i], dp[j] + cost[j]].min
        elsif j + 2 == i
          dp[i] = [dp[i], dp[j] + cost[j]].min
        end
      end
    end
    
    dp[n]
  end

  def self.count_ways_with_jumps(jumps)
    return 0 if jumps.empty?
    
    n = jumps.length
    dp = Array.new(n, 0)
    dp[0] = 1
    
    (1...n).each do |i|
      (0...i).each do |j|
        break if j + jumps[j] >= i
        dp[i] += dp[j]
      end
    end
    
    dp[n - 1]
  end

  def self.can_reach_end(positions)
    return true if positions.length <= 1
    
    n = positions.length
    max_position = positions.max
    dp = Array.new(max_position + 1, false)
    dp[positions.first] = true
    
    (positions.first + 1..max_position).each do |pos|
      (0...pos).each do |prev_pos|
        if dp[prev_pos] && positions.include?(prev_pos)
          dp[pos] = true
          break
        end
      end
    end
    
    dp[positions.last]
  end
end

# Usage examples
puts "Ways to climb 5 stairs: #{ClimbingStairs.climb_stairs(5)}"
puts "Ways to climb 10 stairs: #{ClimbingStairs.climb_stairs(10)}"

puts "Recursive (memoized) ways to climb 5: #{ClimbingStairs.climb_stairs_memoization(5)}"

puts "All ways to climb 4 stairs:"
ClimbingStairs.climb_stairs_with_steps(4).each_with_index do |way, i|
  puts "#{i + 1}: #{way}"
end

cost = [10, 15, 20]
puts "Min cost to climb stairs: #{ClimbingStairs.min_cost_climbing_stairs(cost)}"

jumps = [2, 3, 1, 1, 4]
puts "Ways to reach end: #{ClimbingStairs.count_ways_with_jumps(jumps)}"

positions = [2, 3, 1, 1, 4]
puts "Can reach end position: #{ClimbingStairs.can_reach_end(positions)}"
```

### Coin Change Problem
```ruby
class CoinChange
  def self.coin_change(coins, amount)
    return 1 if amount == 0
    
    dp = Array.new(amount + 1, 0)
    dp[0] = 1
    
    (1..amount).each do |i|
      coins.each do |coin|
        if coin <= i
          dp[i] += dp[i - coin]
        end
      end
    end
    
    dp[amount]
  end

  def self.coin_change_recursive(coins, amount)
    return 1 if amount == 0
    return 0 if amount < 0
    
    ways = 0
    coins.each do |coin|
      ways += coin_change_recursive(coins, amount - coin)
    end
    
    ways
  end

  def self.coin_change_min_coins(coins, amount)
    return 0 if amount == 0
    
    dp = Array.new(amount + 1, Float::INFINITY)
    dp[0] = 0
    
    (1..amount).each do |i|
      coins.each do |coin|
        if coin <= i
          dp[i] = [dp[i], dp[i - coin] + 1].min
        end
      end
    end
    
    dp[amount] == Float::INFINITY ? -1 : dp[amount]
  end

  def self.coin_change_min_coins_optimized(coins, amount)
    return 0 if amount == 0
    
    coins.sort!
    dp = Array.new(amount + 1, Float::INFINITY)
    dp[0] = 0
    
    (1..amount).each do |i|
      coins.each do |coin|
        break if coin > i
        dp[i] = [dp[i], dp[i - coin] + 1].min
      end
    end
    
    dp[amount] == Float::INFINITY ? -1 : dp[amount]
  end

  def self.coin_change_with_combinations(coins, amount)
    return [[]] if amount == 0
    
    dp = Array.new(amount + 1) { [] }
    dp[0] = [[]]
    
    (1..amount).each do |i|
      coins.each do |coin|
        if coin <= i
          dp[i - coin].each do |combination|
            dp[i] << combination + [coin]
          end
        end
      end
    end
    
    dp[amount]
  end

  def self.coin_change_ways(coins, amount)
    return 0 if amount == 0
    
    n = coins.length
    dp = Array.new(n + 1) { Array.new(amount + 1, 0) }
    
    (0...n).each do |i|
      dp[i][0] = 1
    end
    
    (1..amount).each do |j|
      (0...n).each do |i|
        if i > 0 && j >= coins[i]
          dp[i][j] += dp[i - 1][j]
        end
        
        if j >= coins[i]
          dp[i][j] += dp[i][j - coins[i]]
        end
      end
    end
    
    dp[n - 1][amount]
  end

  def self.possible_denominations(amount)
    return [] if amount == 0
    
    max_coin = amount
    dp = Array.new(amount + 1, 0)
    dp[0] = 1
    
    (1..max_coin).each do |coin|
      (coin..amount).each do |i|
        dp[i] += dp[i - coin]
      end
    end
    
    denominations = []
    
    (1..amount).each do |i|
      if dp[i] > 0
        denominations << i
      end
    end
    
    denominations
  end

  def self.limited_coin_change(coins, amount, limit)
    return 0 if amount == 0
    
    n = coins.length
    dp = Array.new(n + 1) { Array.new(amount + 1, 0) }
    
    (0...n).each do |i|
      dp[i][0] = 1
    end
    
    (1..amount).each do |j|
      (0...n).each do |i|
        if i > 0 && j >= coins[i]
          dp[i][j] += dp[i - 1][j]
        end
        
        if j >= coins[i]
          dp[i][j] += dp[i][j - coins[i]]
        end
      end
    end
    
    # Count ways with limit
    count = 0
    count_ways_recursive(dp, n, amount, limit, 0, 0, count)
    count
  end

  private

  def self.count_ways_recursive(dp, n, amount, limit, index, coins_used, count)
    return count + 1 if index == n && amount == 0
    return count if index == n || amount < 0 || coins_used > limit
    
    (index...n).each do |i|
      if amount >= coins[i]
        count_ways_recursive(dp, n, amount - coins[i], limit, i, coins_used + 1, count)
      end
    end
    
    count
  end
end

# Usage examples
coins = [1, 2, 5]
amount = 5

puts "Ways to make #{amount} with #{coins}: #{CoinChange.coin_change(coins, amount)}"
puts "Ways to make 10 with [1, 2, 5]: #{CoinChange.coin_change([1, 2, 5], 10)}"

puts "Min coins for 11: #{CoinChange.coin_change_min_coins(coins, 11)}"
puts "Min coins for 6: #{CoinChange.coin_change_min_coins_optimized(coins, 6)}"

puts "Combinations for 4:"
CoinChange.coin_change_with_combinations(coins, 4).each_with_index do |combo, i|
  puts "#{i + 1}: #{combo}"
end

puts "Ways with DP table for [1,2,5] amount 5: #{CoinChange.coin_change_ways(coins, 5)}"

puts "Possible denominations up to 10: #{CoinChange.possible_denominations(10)}"

puts "Limited coin change (3 coins max): #{CoinChange.limited_coin_change([1, 2, 5], 5, 3)}"
```

## System Design Questions

### LRU Cache Implementation
```ruby
class LRUCache
  def initialize(capacity)
    @capacity = capacity
    @cache = {}
    @order = []
  end

  def get(key)
    return -1 unless @cache.key?(key)
    
    # Move to end (most recently used)
    @order.delete(key)
    @order << key
    
    @cache[key]
  end

  def put(key, value)
    if @cache.key?(key)
      # Update existing key
      @cache[key] = value
      @order.delete(key)
      @order << key
    else
      # Add new key
      if @order.length >= @capacity
        # Remove least recently used
        lru_key = @order.shift
        @cache.delete(lru_key)
      end
      
      @cache[key] = value
      @order << key
    end
  end

  def size
    @cache.length
  end

  def to_array
    @order.map { |key| [key, @cache[key]] }
  end

  def clear
    @cache.clear
    @order.clear
  end
end

class LRUCacheOptimized
  def initialize(capacity)
    @capacity = capacity
    @cache = {}
    @head = Node.new(nil, nil)  # Dummy head
    @tail = Node.new(nil, nil)  # Dummy tail
    @head.next = @tail
    @tail.prev = @head
    @size = 0
  end

  def get(key)
    return -1 unless @cache[key]
    
    node = @cache[key]
    
    # Move to front (most recently used)
    remove_node(node)
    add_to_front(node)
    
    node.value
  end

  def put(key, value)
    if @cache[key]
      # Update existing node
      node = @cache[key]
      node.value = value
      remove_node(node)
      add_to_front(node)
    else
      # Add new node
      if @size >= @capacity
        # Remove least recently used
        lru_node = @tail.prev
        remove_node(lru_node)
        @cache.delete(lru_node.key)
        
        @size -= 1
      end
      
      new_node = Node.new(key, value)
      @cache[key] = new_node
      add_to_front(new_node)
      @size += 1
    end
  end

  def size
    @size
  end

  private

  def add_to_front(node)
    node.next = @head.next
    node.prev = @head
    @head.next.prev = node
    @head.next = node
  end

  def remove_node(node)
    node.prev.next = node.next if node.next
    node.next.prev = node.prev if node.prev
    @size -= 1 if node != @head && node != @tail
  end

  class Node
    attr_accessor :key, :value, :prev, :next

    def initialize(key = nil, value = nil)
      @key = key
      @value = value
      @prev = nil
      @next = nil
    end
  end
end
```

### Design a HashSet
```ruby
class MyHashSet
  def initialize
    @capacity = 16
    @size = 0
    @buckets = Array.new(@capacity) { [] }
    @load_factor = 0.75
  end

  def add(key)
    if contains?(key)
      return false
    end
    
    resize if @size >= @capacity * @load_factor
    
    bucket_index = get_bucket_index(key)
    bucket = @buckets[bucket_index]
    
    bucket << key
    @size += 1
    
    true
  end

  def remove(key)
    bucket_index = get_bucket_index(key)
    bucket = @buckets[bucket_index]
    
    bucket.each_with_index do |item, index|
      if item == key
        bucket.delete_at(index)
        @size -= 1
        return true
      end
    end
    
    false
  end

  def contains?(key)
    bucket_index = get_bucket_index(key)
    bucket = @buckets[bucket_index]
    
    bucket.include?(key)
  end

  def size
    @size
  end

  def empty?
    @size == 0
  end

  def to_array
    @buckets.flatten
  end

  private

  def get_bucket_index(key)
    key.hash % @capacity
  end

  def resize
    old_buckets = @buckets
    @capacity *= 2
    @buckets = Array.new(@capacity) { [] }
    
    old_buckets.each do |bucket|
      bucket.each { |item| add(item) }
    end
  end
end
```

## Interview Strategy Tips

### Problem-Solving Framework
```ruby
class InterviewStrategy
  def self.solve_problem(problem_description)
    puts "=== Problem: #{problem_description} ==="
    
    # Step 1: Understand the problem
    puts "\n1. Understanding the problem:"
    understanding = understand_problem(problem_description)
    puts understanding
    
    # Step 2: Identify constraints
    puts "\n2. Constraints:"
    constraints = identify_constraints(problem_description)
    puts constraints
    
    # Step 3: Choose approach
    puts "\n3. Approach selection:"
    approach = choose_approach(problem_description, constraints)
    puts approach
    
    # Step 4: Implement solution
    puts "\n4. Implementation:"
    implementation = implement_solution(problem_description, approach)
    puts implementation
    
    # Step 5: Test and optimize
    puts "\n5. Testing and optimization:"
    testing = test_and_optimize(problem_description, approach, implementation)
    puts testing
  end

  private

  def self.understand_problem(problem)
    "Analyze requirements: input format, expected output, edge cases"
  end

  def self.identify_constraints(problem)
    "Identify constraints: time/space complexity, input size, special cases"
  end

  def self.choose_approach(problem, constraints)
    "Choose approach: brute force, DP, greedy, two pointers, etc."
  end

  def self.implement_solution(problem, approach)
    "Implement solution with clear, readable code and comments"
  end

  def self.test_and_optimize(problem, approach, solution)
    "Test with examples, analyze complexity, consider optimizations"
  end
end

# Communication tips
class CommunicationTips
  def self.ask_clarifying_questions
    [
      "What are the input and output formats?",
      "What are the constraints on input size?",
      "Are there any special cases I should consider?",
      "What time and space complexity are expected?",
      "Can you provide an example to walk through?",
      "Should I handle edge cases like empty input?",
      "Are there multiple valid solutions?",
      "Should I optimize for time or space?"
    ]
  end

  def self.explain_solution(solution)
    [
      "Start with high-level approach",
      "Explain the algorithm step by step",
      "Discuss time and space complexity",
      "Mention edge cases",
      "Talk about optimizations",
      "Provide code structure"
    ]
  end

  def self.handle_follow_up_questions
    [
      "Alternative approaches",
      "Trade-offs between solutions",
      "How to handle different constraints",
      "Testing strategies",
      "Real-world applications"
    ]
  end
end
```

## Practice Problems

### Array Problems
```ruby
class ArrayProblems
  def self.remove_duplicates(nums)
    return [] if nums.empty?
    
    nums.uniq
  end

  def self.remove_duplicates_in_place(nums)
    return nums if nums.length <= 1
    
    write = 1
    
    (1...nums.length).each do |read|
      if nums[read] != nums[write - 1]
        nums[write] = nums[read]
        write += 1
      end
    end
    
    nums[0...write]
  end

  def self.rotate_array(nums, k)
    return nums if nums.empty?
    
    k = k % nums.length
    nums[-k..-1] + nums[0...-k]
  end

  def self.contains_duplicate(nums)
    seen = Set.new
    
    nums.each do |num|
      return true if seen.include?(num)
      seen.add(num)
    end
    
    false
  end

  def self.contains_duplicate_optimized(nums)
    nums.sort!
    
    (1...nums.length).each do |i|
      return true if nums[i] == nums[i - 1]
    end
    
    false
  end

  def self.max_subarray_sum(nums)
    return 0 if nums.empty?
    
    max_sum = current_sum = nums[0]
    
    (1...nums.length).each do |i|
      current_sum = [current_sum + nums[i], nums[i]].max
      max_sum = [max_sum, current_sum].max
    end
    
    max_sum
  end

  def self.max_product_subarray(nums)
    return 0 if nums.empty?
    
    max_product = current_product = nums[0]
    
    (1...nums.length).each do |i|
      current_product = [
        current_product * nums[i],
        nums[i],
        current_product * nums[i] * (nums[i + 1] || 1)
      ].max
      max_product = [max_product, current_product].max
    end
    
    max_product
  end

  def self.three_sum_closest(nums, target)
    return [] if nums.length < 3
    
    nums.sort!
    left = 0
    right = nums.length - 1
    
    while left < right
      sum = nums[left] + nums[right]
      
      if sum == target
        return [nums[left], nums[(left + right) / 2], nums[right]]
      elsif sum < target
        left += 1
      else
        right -= 1
      end
    end
    
    []
  end
end

# Usage examples
nums = [1, 2, 3, 2, 1]
puts "Remove duplicates: #{ArrayProblems.remove_duplicates(nums)}"
puts "Remove duplicates in place: #{ArrayProblems.remove_duplicates_in_place(nums.dup)}"

nums2 = [4, 5, 6, 7, 0, 1, 2, 3]
puts "Rotate array [4,5,6,7,0,1,2,3] by 3: #{ArrayProblems.rotate_array(nums2, 3)}"

nums3 = [1, 2, 3, 1]
puts "Contains duplicate: #{ArrayProblems.contains_duplicate(nums3)}"
puts "Contains duplicate optimized: #{ArrayProblems.contains_duplicate_optimized(nums3.dup)}"

nums4 = [-2, 1, -3, 4, -1, 2, 1, -5, 4]
puts "Max subarray sum: #{ArrayProblems.max_subarray_sum(nums4)}"
puts "Max product subarray: #{ArrayProblems.max_product_subarray(nums4)}"

nums5 = [-1, 2, 1, -4]
target5 = 1
puts "Three sum closest to #{target5}: #{ArrayProblems.three_sum_closest(nums5, target5)}"
```

## Best Practices

### Before the Interview
```ruby
class InterviewPreparation
  def self.prepare_topics
    [
      "Data Structures: Arrays, Linked Lists, Stacks, Queues, Trees, Graphs, Hash Tables",
      "Algorithms: Sorting, Searching, Recursion, Dynamic Programming, Greedy",
      "System Design: Scalability, Caching, Load Balancing, Databases",
      "Ruby Specific: Ruby idioms, gems, Rails, performance optimization",
      "Problem Solving: Breaking down problems, time/space complexity"
    ]
  end

  def self.practice_problems
    [
      "Array problems (Two Sum, Three Sum, Maximum Subarray)",
      "String problems (Reverse, Anagrams, Palindromes)",
      "Tree problems (Traversal, LCA, Validation)",
      "Graph problems (BFS, DFS, Shortest Path)",
      "Dynamic Programming (Fibonacci, Coin Change, Knapsack)",
      "System Design (LRU Cache, URL Shortener, Twitter)",
      "Math problems (Prime Numbers, GCD, LCM)"
    ]
  end

  def self.mock_interview
    [
      "Practice with a timer",
      "Explain your thought process out loud",
      "Use a whiteboard or paper",
      "Record yourself and review",
      "Practice with different problem types"
    ]
  end
end
```

### During the Interview
```ruby
class InterviewEtiquette
  def self.communication_tips
    [
      "Listen carefully to the entire question",
      "Ask clarifying questions if needed",
      "Think out loud before coding",
      "Explain your approach clearly",
      "Write clean, readable code",
      "Test your solution with examples",
      "Discuss time and space complexity",
      "Be open to feedback and suggestions"
    ]
  end

  def self.problem_solving_approach
    [
      "Understand the problem thoroughly",
      "Identify constraints and edge cases",
      "Start with a simple solution",
      "Consider multiple approaches",
      "Choose the optimal approach",
      "Implement step by step",
      "Test thoroughly",
      "Analyze complexity",
      "Discuss trade-offs"
    ]
  end
end
```

## Conclusion

Coding interview challenges are essential for technical interview preparation. By practicing these problems in Ruby and following good problem-solving strategies, you'll be well-prepared for technical interviews and demonstrate your programming abilities effectively.

## Further Reading

- [Cracking the Coding Interview](https://www.careercup.com/book/)
- [LeetCode](https://leetcode.com/)
- [HackerRank](https://www.hackerrank.com/)
- [Interview Cake](https://www.interviewcake.com/)
- [System Design Interview](https://www.systemdesigninterview.com/)
