# Borrowing

Borrowing allows references without taking ownership.

## Example
```rust
fn print_str(s: &String) {
    println!("{}", s);
}
Practice

Pass a reference to a function.
