# Understanding Blocks and Procs in Ruby

In Ruby, a block is a way to pass a chunk of code to a method. It is one of the most "Ruby-ish" features and is used everywhere (like in `.each`).

### Key Takeaways
* **Implicit vs. Explicit:** Blocks are implicit; Procs are objects that store code.
* **Yield:** The `yield` keyword is used inside a method to execute the block passed to it.
* **Flexibility:** Blocks allow you to write generic methods that can perform different actions depending on the code provided.