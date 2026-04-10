# Control Statements Example

## File Overview
This file demonstrates all control flow statements available in Java for controlling program execution.

## Key Concepts Explained

### 1. Conditional Statements

#### If-Else Statement
- Executes code based on a boolean condition
- Can have optional else block
- Can be chained with else-if

#### Switch Statement
- Multi-way branch statement
- Works with byte, short, int, char, enum, String (Java 7+)
- Requires break to prevent fall-through
- Default case handles unmatched values

### 2. Loop Statements

#### For Loop
- Traditional for loop with initialization, condition, and increment
- Best when number of iterations is known
- Syntax: `for (initialization; condition; increment)`

#### Enhanced For Loop (For-Each)
- Simplified iteration over arrays and collections
- Syntax: `for (element : collection)`
- Read-only access to elements

#### While Loop
- Pre-test loop (checks condition before execution)
- Executes zero or more times
- Best when number of iterations is unknown

#### Do-While Loop
- Post-test loop (checks condition after execution)
- Executes at least once
- Best when loop must execute at least once

### 3. Jump Statements

#### Break Statement
- Exits from loop or switch statement
- Can use labels to exit nested loops
- Terminates the current flow

#### Continue Statement
- Skips current iteration and continues with next
- Can use labels in nested loops
- Only works within loops

#### Return Statement
- Exits from method
- Returns optional value to caller

### 4. Advanced Features

#### Labeled Break/Continue
- Used with nested loops
- Label syntax: `labelName:`
- Allows breaking/continuing outer loops

#### Enhanced Switch (Java 14+)
- Arrow syntax (->) instead of break
- Multiple values per case
- More concise and safer

## Best Practices
1. Use switch when comparing against multiple constant values
2. Prefer enhanced for-loop for simple iteration
3. Use descriptive variable names in loop conditions
4. Avoid deeply nested control structures
5. Use break and continue judiciously
6. Consider extracting complex conditions to variables
7. Use meaningful labels when using labeled break/continue

## Common Pitfalls
1. Forgetting break in switch statements
2. Infinite loops (wrong loop conditions)
3. Off-by-one errors in loop bounds
4. Using = instead of == in conditions
5. Not handling all cases in switch statements
