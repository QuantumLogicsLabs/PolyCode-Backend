# Callbacks

Functions passed as arguments.

## Example
```javascript
function greet(name, callback) {
    console.log("Hello " + name);
    callback();
}

greet("Alice", function() {
    console.log("Callback executed");
});
Practice

Write a function that takes a callback and executes it.
