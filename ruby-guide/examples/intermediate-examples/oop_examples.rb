# Object-Oriented Programming Examples
# Demonstrating OOP concepts in Ruby

puts "=== CLASSES AND OBJECTS ==="

# Basic class definition
class Person
  def initialize(name, age)
    @name = name
    @age = age
  end
  
  def name
    @name
  end
  
  def age
    @age
  end
  
  def introduce
    "Hi, I'm #{@name} and I'm #{@age} years old."
  end
end

person1 = Person.new("Alice", 25)
person2 = Person.new("Bob", 30)

puts person1.introduce
puts person2.introduce

puts "\n=== ACCESSOR METHODS ==="

class Student
  attr_accessor :name, :grade
  attr_reader :student_id
  
  def initialize(student_id, name, grade)
    @student_id = student_id
    @name = name
    @grade = grade
  end
  
  def promote
    @grade += 1
    puts "#{@name} promoted to grade #{@grade}"
  end
end

student = Student.new(1001, "Charlie", 9)
puts "Student ID: #{student.student_id}"
puts "Name: #{student.name}"
student.name = "Charlie Brown"
puts "Updated name: #{student.name}"
student.grade = 10
puts "Grade: #{student.grade}"
student.promote

puts "\n=== CLASS VARIABLES AND METHODS ==="

class Employee
  @@company_name = "Tech Corp"
  @@total_employees = 0
  
  def initialize(name, salary)
    @name = name
    @salary = salary
    @@total_employees += 1
  end
  
  def self.company_name
    @@company_name
  end
  
  def self.total_employees
    @@total_employees
  end
  
  def employee_info
    "#{@name} works at #{@@company_name} with salary $#{@salary}"
  end
end

emp1 = Employee.new("Alice", 75000)
emp2 = Employee.new("Bob", 80000)
emp3 = Employee.new("Charlie", 65000)

puts emp1.employee_info
puts emp2.employee_info
puts "Company: #{Employee.company_name}"
puts "Total employees: #{Employee.total_employees}"

puts "\n=== INHERITANCE ==="

class Animal
  def initialize(name)
    @name = name
  end
  
  def speak
    "#{@name} makes a sound"
  end
  
  def eat
    "#{@name} is eating"
  end
end

class Dog < Animal
  def speak
    "#{@name} barks: Woof!"
  end
  
  def fetch
    "#{@name} is fetching the ball"
  end
end

class Cat < Animal
  def speak
    "#{@name} meows: Meow!"
  end
  
  def purr
    "#{@name} is purring contentedly"
  end
end

dog = Dog.new("Buddy")
cat = Cat.new("Whiskers")

puts dog.speak
puts dog.eat
puts dog.fetch

puts cat.speak
puts cat.eat
puts cat.purr

puts "\n=== METHOD OVERRIDING AND SUPER ==="

class Vehicle
  def initialize(make, model)
    @make = make
    @model = model
  end
  
  def info
    "#{@make} #{@model}"
  end
  
  def start
    "Starting #{@make} #{@model}"
  end
end

class Car < Vehicle
  def initialize(make, model, year, doors)
    super(make, model)
    @year = year
    @doors = doors
  end
  
  def info
    "#{super} (#{@year}, #{@doors} doors)"
  end
  
  def start
    "#{super} - Engine roaring to life!"
  end
end

class Motorcycle < Vehicle
  def initialize(make, model, year, type)
    super(make, model)
    @year = year
    @type = type
  end
  
  def info
    "#{super} (#{@year} #{@type})"
  end
  
  def start
    "#{super} - Vroom vroom!"
  end
end

car = Car.new("Toyota", "Camry", 2020, 4)
motorcycle = Motorcycle.new("Harley", "Sportster", 2019, "Cruiser")

puts car.info
puts car.start

puts motorcycle.info
puts motorcycle.start

puts "\n=== POLYMORPHISM ==="

class Shape
  def area
    "Base shape area calculation"
  end
  
  def perimeter
    "Base shape perimeter calculation"
  end
end

class Rectangle < Shape
  def initialize(width, height)
    @width = width
    @height = height
  end
  
  def area
    @width * @height
  end
  
  def perimeter
    2 * (@width + @height)
  end
end

class Circle < Shape
  def initialize(radius)
    @radius = radius
  end
  
  def area
    Math::PI * @radius ** 2
  end
  
  def perimeter
    2 * Math::PI * @radius
  end
end

class Triangle < Shape
  def initialize(base, height)
    @base = base
    @height = height
  end
  
  def area
    0.5 * @base * @height
  end
  
  def perimeter
    # Assuming equilateral triangle for simplicity
    3 * @base
  end
end

shapes = [
  Rectangle.new(5, 3),
  Circle.new(4),
  Triangle.new(6, 4)
]

shapes.each do |shape|
  puts "#{shape.class.name}: Area = #{'%.2f' % shape.area}, Perimeter = #{'%.2f' % shape.perimeter}"
end

puts "\n=== ENCAPSULATION ==="

class BankAccount
  def initialize(account_number, initial_balance = 0)
    @account_number = account_number
    @balance = initial_balance
    @transaction_count = 0
  end
  
  def deposit(amount)
    validate_amount(amount)
    @balance += amount
    @transaction_count += 1
    log_transaction("Deposit", amount)
  end
  
  def withdraw(amount)
    validate_amount(amount)
    return "Insufficient funds" if amount > @balance
    
    @balance -= amount
    @transaction_count += 1
    log_transaction("Withdrawal", amount)
  end
  
  def balance
    @balance
  end
  
  def account_summary
    "Account #{@account_number}: Balance $#{@balance}, Transactions: #{@transaction_count}"
  end
  
  private
  
  def validate_amount(amount)
    raise ArgumentError, "Amount must be positive" if amount <= 0
  end
  
  def log_transaction(type, amount)
    puts "#{type}: $#{amount} - New balance: $#{@balance}"
  end
end

account = BankAccount.new("12345", 1000)
puts account.deposit(500)
puts account.withdraw(200)
puts account.withdraw(2000)  # Insufficient funds
puts account.account_summary

puts "\n=== COMPOSITION ==="

class Engine
  def start
    "Engine started"
  end
  
  def stop
    "Engine stopped"
  end
  
  def accelerate
    "Engine accelerating"
  end
end

class Wheels
  def rotate
    "Wheels rotating"
  end
  
  def stop_rotation
    "Wheels stopped"
  end
  
  def turn(direction)
    "Wheels turning #{direction}"
  end
end

class Steering
  def turn_left
    "Steering left"
  end
  
  def turn_right
    "Steering right"
  end
end

class Car
  def initialize
    @engine = Engine.new
    @wheels = Wheels.new
    @steering = Steering.new
    @speed = 0
  end
  
  def start
    @engine.start
    @wheels.rotate
    "Car started and ready to go"
  end
  
  def accelerate
    @engine.accelerate
    @speed += 10
    "Car accelerating. Speed: #{@speed} mph"
  end
  
  def turn(direction)
    @steering.send("turn_#{direction}")
    @wheels.turn(direction)
    "Car turning #{direction}"
  end
  
  def stop
    @engine.stop
    @wheels.stop_rotation
    @speed = 0
    "Car stopped"
  end
end

my_car = Car.new
puts my_car.start
puts my_car.accelerate
puts my_car.turn("left")
puts my_car.accelerate
puts my_car.stop

puts "\n=== DYNAMIC METHODS ==="

class DynamicCalculator
  def self.create_operation(name, operation)
    define_method(name) do |a, b|
      result = a.send(operation, b)
      "#{a} #{operation} #{b} = #{result}"
    end
  end
  
  # Create methods dynamically
  create_operation :add, :+
  create_operation :subtract, :-
  create_operation :multiply, :*
  create_operation :divide, :/
end

calc = DynamicCalculator.new
puts calc.add(5, 3)
puts calc.subtract(10, 4)
puts calc.multiply(6, 7)
puts calc.divide(15, 3)

puts "\n=== METHOD MISSING ==="

class FlexibleFormatter
  def method_missing(method_name, *args)
    if method_name.to_s.start_with?("format_")
      format_type = method_name.to_s.sub("format_", "")
      case format_type
      when "uppercase"
        args.map(&:upcase).join(" ")
      when "lowercase"
        args.map(&:downcase).join(" ")
      when "title"
        args.map(&:capitalize).join(" ")
      when "reverse"
        args.map(&:reverse).join(" ")
      else
        super
      end
    else
      super
    end
  end
  
  def respond_to_missing?(method_name, include_private = false)
    method_name.to_s.start_with?("format_") || super
  end
end

formatter = FlexibleFormatter.new
puts formatter.format_uppercase("hello", "world")
puts formatter.format_lowercase("HELLO", "WORLD")
puts formatter.format_title("hello", "ruby", "world")
puts formatter.format_reverse("hello", "world")

puts "\n=== PRACTICAL EXAMPLE: BANKING SYSTEM ==="

class Account
  attr_reader :account_number, :balance, :owner
  
  def initialize(account_number, owner, initial_balance = 0)
    @account_number = account_number
    @owner = owner
    @balance = initial_balance
    @transactions = []
  end
  
  def deposit(amount)
    raise ArgumentError, "Amount must be positive" if amount <= 0
    
    @balance += amount
    @transactions << { type: :deposit, amount: amount, timestamp: Time.now }
    "Deposited $#{amount}. New balance: $#{@balance}"
  end
  
  def withdraw(amount)
    raise ArgumentError, "Amount must be positive" if amount <= 0
    raise "Insufficient funds" if amount > @balance
    
    @balance -= amount
    @transactions << { type: :withdrawal, amount: amount, timestamp: Time.now }
    "Withdrew $#{amount}. New balance: $#{@balance}"
  end
  
  def transfer(target_account, amount)
    withdraw(amount)
    target_account.deposit(amount)
    "Transferred $#{amount} to account #{target_account.account_number}"
  end
  
  def account_summary
    {
      account_number: @account_number,
      owner: @owner,
      balance: @balance,
      transaction_count: @transactions.length
    }
  end
end

# Create accounts
account1 = Account.new("001", "Alice", 1000)
account2 = Account.new("002", "Bob", 500)

# Perform operations
puts account1.deposit(500)
puts account1.withdraw(200)
puts account1.transfer(account2, 300)
puts account2.deposit(100)

# Show summaries
puts "\nAccount Summaries:"
puts account1.account_summary
puts account2.account_summary
