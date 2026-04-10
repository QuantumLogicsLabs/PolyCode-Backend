# Recursion

Method calling itself.

## Example
```ruby
def factorial(n)
  return 1 if n == 0
  n * factorial(n - 1)
end
Practice

Write recursive sum function.
