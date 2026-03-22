# Introduction to Ruby

## What is Ruby?

Ruby is a dynamic, object-oriented programming language created by Yukihiro "Matz" Matsumoto in 1995. It was designed to be simple, elegant, and enjoyable to use.

## Philosophy

Ruby follows several key principles:

### 1. "Optimized for programmer happiness"
- Code should be natural to read and write
- Minimize confusion and surprise
- Make programming fun!

### 2. "Principle of Least Surprise" (POLS)
- The language should behave as you expect
- Consistent behavior across different contexts

### 3. "There's more than one way to do it" (TMTOWTDI)
- Multiple approaches to solve problems
- Flexibility in coding style

## Why Learn Ruby?

### Advantages
- **Simple Syntax**: Easy to read and write
- **Object-Oriented**: Everything is an object
- **Dynamic**: Flexible and adaptable
- **Rich Standard Library**: Built-in functionality
- **Strong Community**: Active support and resources
- **Popular Frameworks**: Ruby on Rails, Sinatra

### Common Uses
- Web development (Ruby on Rails)
- Scripting and automation
- Data processing
- API development
- DevOps tools

## Ruby vs Other Languages

| Feature | Ruby | Python | JavaScript | Java |
|---------|------|--------|------------|------|
| Typing | Dynamic | Dynamic | Dynamic | Static |
| Paradigm | OO | Multi | Multi | OO |
| Syntax | Very clean | Clean | Flexible | Verbose |
| Performance | Moderate | Good | Good | Fast |
| Web Frameworks | Rails, Sinatra | Django, Flask | Express, React | Spring |

## Your First Ruby Program

Let's create your first Ruby program:

```ruby
# hello.rb
puts "Hello, Ruby World!"
```

### Running Ruby Code

There are several ways to run Ruby code:

#### 1. Interactive Ruby (IRB)
```bash
irb
> puts "Hello, Ruby!"
Hello, Ruby!
=> nil
```

#### 2. Ruby Files
```bash
ruby hello.rb
```

#### 3. One-liners
```bash
ruby -e "puts 'Hello, Ruby!'"
```

## Basic Ruby Concepts

### 1. Everything is an Object
```ruby
5.class        # => Integer
"hello".class  # => String
[1,2,3].class  # => Array
```

### 2. Dynamic Typing
```ruby
x = 5          # x is an Integer
x = "hello"    # x is now a String
x = [1,2,3]    # x is now an Array
```

### 3. Duck Typing
"If it walks like a duck and quacks like a duck, it's a duck"

```ruby
def make_sound(animal)
  animal.speak
end

class Dog
  def speak
    "Woof!"
  end
end

class Cat
  def speak
    "Meow!"
  end
end

make_sound(Dog.new)  # => "Woof!"
make_sound(Cat.new)  # => "Meow!"
```

## Ruby Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Variables | snake_case | `my_variable` |
| Classes | PascalCase | `MyClass` |
| Constants | UPPER_SNAKE_CASE | `MY_CONSTANT` |
| Methods | snake_case | `my_method` |
| Files | snake_case.rb | `my_file.rb` |

## Development Environment Setup

### Recommended Tools

#### Text Editors/IDEs
- **VS Code** with Ruby extension
- **RubyMine** (JetBrains)
- **Sublime Text**
- **Vim/Neovim**

#### Essential Gems
```bash
gem install bundler    # Dependency management
gem install rails      # Web framework
gem install pry        # Enhanced REPL
gem install rubocop    # Code linter
```

### Project Structure
```
my_project/
├── Gemfile           # Dependencies
├── README.md         # Documentation
├── lib/              # Source code
├── spec/             # Tests
└── bin/              # Executables
```

## Next Steps

Now that you understand the basics of Ruby, let's move on to:

1. **[Basic Syntax](02-basic-syntax.md)** - Learn Ruby's syntax rules
2. **[Data Types](03-data-types.md)** - Explore Ruby's data types
3. **[Control Flow](04-control-flow.md)** - Master control structures

## Practice Exercise

Create a simple Ruby program that:
1. Prints your name
2. Calculates the sum of two numbers
3. Defines a simple class with one method

```ruby
# exercise.rb
# Your code here
```

## Resources

### Official Documentation
- [Ruby Documentation](https://ruby-doc.org/)
- [Ruby-Lang.org](https://www.ruby-lang.org/)

### Learning Resources
- [RubyMonk](https://rubymonk.com/)
- [Learn Ruby the Hard Way](https://learnrubythehardway.org/)
- [Ruby Koans](https://github.com/skmetz/ruby_koans)

### Community
- [Ruby Forum](https://discuss.ruby-lang.org/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/ruby)
- [Reddit r/ruby](https://www.reddit.com/r/ruby/)

---

**Ready to dive deeper into Ruby syntax? Let's continue! 🚀**
