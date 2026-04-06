# JavaScript Testing

## Testing Fundamentals

### Unit Testing Basics
```javascript
// Simple test runner
class TestRunner {
    constructor() {
        this.tests = [];
        this.results = [];
    }
    
    test(name, testFunction) {
        this.tests.push({ name, testFunction });
    }
    
    async run() {
        this.results = [];
        
        for (const test of this.tests) {
            try {
                const startTime = performance.now();
                await test.testFunction();
                const endTime = performance.now();
                
                this.results.push({
                    name: test.name,
                    status: 'passed',
                    duration: endTime - startTime,
                    error: null
                });
            } catch (error) {
                this.results.push({
                    name: test.name,
                    status: 'failed',
                    duration: 0,
                    error: error.message
                });
            }
        }
        
        return this.results;
    }
    
    assert(condition, message = 'Assertion failed') {
        if (!condition) {
            throw new Error(message);
        }
    }
    
    assertEqual(actual, expected, message = 'Values not equal') {
        if (actual !== expected) {
            throw new Error(`${message}: expected ${expected}, got ${actual}`);
        }
    }
    
    assertNotEqual(actual, expected, message = 'Values should not be equal') {
        if (actual === expected) {
            throw new Error(`${message}: expected not ${expected}, got ${actual}`);
        }
    }
    
    assertThrows(fn, expectedErrorType = null, message = 'Expected function to throw') {
        try {
            fn();
            throw new Error(message);
        } catch (error) {
            if (expectedErrorType && !(error instanceof expectedErrorType)) {
                throw new Error(`${message}: expected ${expectedErrorType.name}, got ${error.constructor.name}`);
            }
        }
    }
    
    generateReport() {
        const passed = this.results.filter(r => r.status === 'passed').length;
        const failed = this.results.filter(r => r.status === 'failed').length;
        const total = this.results.length;
        
        console.log(`\nTest Results:`);
        console.log(`Total: ${total}`);
        console.log(`Passed: ${passed}`);
        console.log(`Failed: ${failed}`);
        console.log(`Success Rate: ${((passed / total) * 100).toFixed(2)}%`);
        
        console.log(`\nDetailed Results:`);
        this.results.forEach(result => {
            const icon = result.status === 'passed' ? '✅' : '❌';
            console.log(`${icon} ${result.name} (${result.duration.toFixed(2)}ms)`);
            if (result.error) {
                console.log(`   Error: ${result.error}`);
            }
        });
    }
}

// Example usage
const runner = new TestRunner();

// Test basic arithmetic
runner.test('Addition', () => {
    runner.assertEqual(2 + 2, 4);
    runner.assertEqual(5 + 3, 8);
});

runner.test('Subtraction', () => {
    runner.assertEqual(10 - 5, 5);
    runner.assertEqual(7 - 3, 4);
});

// Test string operations
runner.test('String concatenation', () => {
    runner.assertEqual('Hello' + ' ' + 'World', 'Hello World');
});

runner.test('String length', () => {
    runner.assertEqual('test'.length, 4);
});

// Test array operations
runner.test('Array push', () => {
    const arr = [1, 2, 3];
    arr.push(4);
    runner.assertEqual(arr.length, 4);
    runner.assertEqual(arr[3], 4);
});

// Test error handling
runner.test('Error throwing', () => {
    runner.assertThrows(() => {
        throw new Error('Test error');
    }, Error);
});

// Run tests
runner.run().then(() => {
    runner.generateReport();
});
```

### Test Doubles and Mocks
```javascript
// Mock objects for testing
class Mock {
    constructor() {
        this.calls = [];
        this.returnValue = null;
        this.throwError = null;
    }
    
    // Set return value for method calls
    returns(value) {
        this.returnValue = value;
        return this;
    }
    
    // Set error to throw
    throws(error) {
        this.throwError = error;
        return this;
    }
    
    // Create mock method
    createMock() {
        const self = this;
        return function(...args) {
            self.calls.push({
                args,
                timestamp: Date.now()
            });
            
            if (self.throwError) {
                throw self.throwError;
            }
            
            return self.returnValue;
        };
    }
    
    // Get call history
    getCalls() {
        return this.calls;
    }
    
    // Get number of calls
    callCount() {
        return this.calls.length;
    }
    
    // Check if method was called
    wasCalled() {
        return this.calls.length > 0;
    }
    
    // Check if method was called with specific arguments
    wasCalledWith(...args) {
        return this.calls.some(call => 
            JSON.stringify(call.args) === JSON.stringify(args)
        );
    }
    
    // Reset call history
    reset() {
        this.calls = [];
    }
}

// Stub objects for testing
class Stub {
    constructor() {
        this.methods = new Map();
    }
    
    // Add stub method
    method(name, implementation) {
        this.methods.set(name, implementation);
        return this;
    }
    
    // Get stub method
    getMethod(name) {
        return this.methods.get(name) || (() => {});
    }
}

// Spy objects for testing
class Spy {
    constructor(object) {
        this.object = object;
        this.originalMethods = new Map();
        this.calls = new Map();
    }
    
    // Spy on a method
    on(methodName) {
        const originalMethod = this.object[methodName];
        this.originalMethods.set(methodName, originalMethod);
        
        const spy = this;
        this.object[methodName] = function(...args) {
            if (!spy.calls.has(methodName)) {
                spy.calls.set(methodName, []);
            }
            
            spy.calls.get(methodName).push({
                args,
                timestamp: Date.now(),
                result: null
            });
            
            const result = originalMethod.apply(this, args);
            spy.calls.get(methodName)[spy.calls.get(methodName).length - 1].result = result;
            
            return result;
        };
        
        return this;
    }
    
    // Get call history for a method
    getCalls(methodName) {
        return this.calls.get(methodName) || [];
    }
    
    // Get number of calls for a method
    callCount(methodName) {
        return this.getCalls(methodName).length;
    }
    
    // Check if method was called
    wasCalled(methodName) {
        return this.callCount(methodName) > 0;
    }
    
    // Restore original methods
    restore() {
        for (const [methodName, originalMethod] of this.originalMethods) {
            this.object[methodName] = originalMethod;
        }
    }
}

// Example usage
function demonstrateTestDoubles() {
    // Mock example
    const mockAPI = new Mock();
    mockAPI.returns({ id: 1, name: 'John' });
    
    const mockFetch = mockAPI.createMock();
    const result = mockFetch('/api/users/1');
    
    console.log('Mock result:', result);
    console.log('Call count:', mockAPI.callCount());
    console.log('Was called:', mockAPI.wasCalled());
    
    // Stub example
    const stubDB = new Stub();
    stubDB.method('getUser', (id) => ({ id, name: 'User ' + id }));
    stubDB.method('saveUser', (user) => ({ ...user, id: Date.now() }));
    
    const user = stubDB.getMethod('getUser')(1);
    console.log('Stub user:', user);
    
    // Spy example
    const calculator = {
        add: (a, b) => a + b,
        multiply: (a, b) => a * b
    };
    
    const spy = new Spy(calculator);
    spy.on('add');
    spy.on('multiply');
    
    calculator.add(2, 3);
    calculator.multiply(4, 5);
    
    console.log('Add calls:', spy.getCalls('add'));
    console.log('Multiply call count:', spy.callCount('multiply'));
    
    spy.restore();
}
```

## Testing Frameworks

### Jest-like Framework
```javascript
// Simple Jest-like testing framework
class JestLikeFramework {
    constructor() {
        this.tests = [];
        this.beforeEachCallbacks = [];
        this.afterEachCallbacks = [];
        this.describeStack = [];
        this.currentDescribe = null;
    }
    
    describe(description, callback) {
        this.describeStack.push(this.currentDescribe);
        this.currentDescribe = description;
        
        callback();
        
        this.currentDescribe = this.describeStack.pop();
    }
    
    it(description, testFunction) {
        const fullName = this.describeStack.concat(this.currentDescribe, description).join(' ');
        
        this.tests.push({
            name: fullName,
            testFunction,
            beforeEachCallbacks: [...this.beforeEachCallbacks],
            afterEachCallbacks: [...this.afterEachCallbacks]
        });
    }
    
    beforeEach(callback) {
        this.beforeEachCallbacks.push(callback);
    }
    
    afterEach(callback) {
        this.afterEachCallbacks.push(callback);
    }
    
    expect(actual) {
        return new Expectation(actual);
    }
    
    async run() {
        const results = [];
        
        for (const test of this.tests) {
            try {
                // Run beforeEach callbacks
                for (const callback of test.beforeEachCallbacks) {
                    await callback();
                }
                
                // Run test
                const startTime = performance.now();
                await test.testFunction();
                const endTime = performance.now();
                
                // Run afterEach callbacks
                for (const callback of test.afterEachCallbacks) {
                    await callback();
                }
                
                results.push({
                    name: test.name,
                    status: 'passed',
                    duration: endTime - startTime,
                    error: null
                });
            } catch (error) {
                results.push({
                    name: test.name,
                    status: 'failed',
                    duration: 0,
                    error: error.message
                });
            }
        }
        
        return results;
    }
}

class Expectation {
    constructor(actual) {
        this.actual = actual;
    }
    
    toEqual(expected) {
        if (this.actual !== expected) {
            throw new Error(`Expected ${expected}, but got ${this.actual}`);
        }
    }
    
    toBe(expected) {
        if (this.actual !== expected) {
            throw new Error(`Expected ${expected}, but got ${this.actual}`);
        }
    }
    
    toBeNull() {
        if (this.actual !== null) {
            throw new Error(`Expected null, but got ${this.actual}`);
        }
    }
    
    toBeUndefined() {
        if (this.actual !== undefined) {
            throw new Error(`Expected undefined, but got ${this.actual}`);
        }
    }
    
    toBeTruthy() {
        if (!this.actual) {
            throw new Error(`Expected truthy value, but got ${this.actual}`);
        }
    }
    
    toBeFalsy() {
        if (this.actual) {
            throw new Error(`Expected falsy value, but got ${this.actual}`);
        }
    }
    
    toContain(item) {
        if (Array.isArray(this.actual)) {
            if (!this.actual.includes(item)) {
                throw new Error(`Expected array to contain ${item}`);
            }
        } else if (typeof this.actual === 'string') {
            if (!this.actual.includes(item)) {
                throw new Error(`Expected string to contain ${item}`);
            }
        } else {
            throw new Error('toContain only works with arrays and strings');
        }
    }
    
    toThrow(expectedError = null) {
        let threw = false;
        let actualError = null;
        
        try {
            if (typeof this.actual === 'function') {
                this.actual();
            }
        } catch (error) {
            threw = true;
            actualError = error;
        }
        
        if (!threw) {
            throw new Error('Expected function to throw');
        }
        
        if (expectedError && !(actualError instanceof expectedError)) {
            throw new Error(`Expected ${expectedError.name}, but got ${actualError.constructor.name}`);
        }
    }
    
    not() {
        return new NotExpectation(this.actual);
    }
}

class NotExpectation {
    constructor(actual) {
        this.actual = actual;
    }
    
    toEqual(expected) {
        if (this.actual === expected) {
            throw new Error(`Expected not ${expected}, but got ${this.actual}`);
        }
    }
    
    toContain(item) {
        if (Array.isArray(this.actual)) {
            if (this.actual.includes(item)) {
                throw new Error(`Expected array not to contain ${item}`);
            }
        } else if (typeof this.actual === 'string') {
            if (this.actual.includes(item)) {
                throw new Error(`Expected string not to contain ${item}`);
            }
        }
    }
}

// Example usage
const framework = new JestLikeFramework();

framework.describe('Math operations', () => {
    let calculator;
    
    framework.beforeEach(() => {
        calculator = {
            add: (a, b) => a + b,
            subtract: (a, b) => a - b
        };
    });
    
    framework.describe('Addition', () => {
        framework.it('should add two numbers correctly', () => {
            framework.expect(calculator.add(2, 3)).toEqual(5);
            framework.expect(calculator.add(-1, 1)).toEqual(0);
        });
        
        framework.it('should handle zero', () => {
            framework.expect(calculator.add(0, 5)).toEqual(5);
            framework.expect(calculator.add(0, 0)).toEqual(0);
        });
    });
    
    framework.describe('Subtraction', () => {
        framework.it('should subtract two numbers correctly', () => {
            framework.expect(calculator.subtract(10, 5)).toEqual(5);
            framework.expect(calculator.subtract(5, 3)).toEqual(2);
        });
        
        framework.it('should handle negative results', () => {
            framework.expect(calculator.subtract(3, 5)).toEqual(-2);
        });
    });
});

framework.describe('Array operations', () => {
    framework.it('should push items to array', () => {
        const arr = [1, 2, 3];
        arr.push(4);
        framework.expect(arr).toContain(4);
        framework.expect(arr.length).toEqual(4);
    });
    
    framework.it('should filter array items', () => {
        const arr = [1, 2, 3, 4, 5];
        const filtered = arr.filter(x => x > 3);
        framework.expect(filtered).toEqual([4, 5]);
        framework.expect(filtered).not.toContain(1);
    });
});

// Run tests
framework.run().then(results => {
    const passed = results.filter(r => r.status === 'passed').length;
    const failed = results.filter(r => r.status === 'failed').length;
    
    console.log(`\nTest Results: ${passed} passed, ${failed} failed`);
    
    results.forEach(result => {
        const icon = result.status === 'passed' ? '✅' : '❌';
        console.log(`${icon} ${result.name}`);
        if (result.error) {
            console.log(`   ${result.error}`);
        }
    });
});
```

### Mocha-like Framework
```javascript
// Mocha-like testing framework
class MochaLikeFramework {
    constructor() {
        this.suites = [];
        this.currentSuite = null;
        this.suiteStack = [];
        this.hooks = {
            before: [],
            beforeEach: [],
            after: [],
            afterEach: []
        };
    }
    
    describe(description, callback) {
        const suite = {
            description,
            tests: [],
            hooks: {
                before: [],
                beforeEach: [],
                after: [],
                afterEach: []
            },
            suites: []
        };
        
        if (this.currentSuite) {
            this.currentSuite.suites.push(suite);
        } else {
            this.suites.push(suite);
        }
        
        this.suiteStack.push(this.currentSuite);
        this.currentSuite = suite;
        
        callback();
        
        this.currentSuite = this.suiteStack.pop();
    }
    
    it(description, testFunction) {
        if (!this.currentSuite) {
            throw new Error('Test must be inside a describe block');
        }
        
        this.currentSuite.tests.push({
            description,
            testFunction,
            fullDescription: this.getFullDescription(description)
        });
    }
    
    getFullDescription(description) {
        return this.suiteStack
            .filter(suite => suite)
            .map(suite => suite.description)
            .concat(description)
            .join(' ');
    }
    
    before(callback) {
        this.registerHook('before', callback);
    }
    
    beforeEach(callback) {
        this.registerHook('beforeEach', callback);
    }
    
    after(callback) {
        this.registerHook('after', callback);
    }
    
    afterEach(callback) {
        this.registerHook('afterEach', callback);
    }
    
    registerHook(type, callback) {
        if (this.currentSuite) {
            this.currentSuite.hooks[type].push(callback);
        } else {
            this.hooks[type].push(callback);
        }
    }
    
    async run() {
        const results = [];
        
        // Run global before hooks
        for (const hook of this.hooks.before) {
            await hook();
        }
        
        // Run suites
        for (const suite of this.suites) {
            const suiteResults = await this.runSuite(suite);
            results.push(...suiteResults);
        }
        
        // Run global after hooks
        for (const hook of this.hooks.after) {
            await hook();
        }
        
        return results;
    }
    
    async runSuite(suite) {
        const results = [];
        
        // Run suite before hooks
        for (const hook of suite.hooks.before) {
            await hook();
        }
        
        // Run tests
        for (const test of suite.tests) {
            const result = await this.runTest(test, suite);
            results.push(result);
        }
        
        // Run suite after hooks
        for (const hook of suite.hooks.after) {
            await hook();
        }
        
        return results;
    }
    
    async runTest(test, suite) {
        try {
            // Run beforeEach hooks
            for (const hook of suite.hooks.beforeEach) {
                await hook();
            }
            
            // Run global beforeEach hooks
            for (const hook of this.hooks.beforeEach) {
                await hook();
            }
            
            // Run test
            const startTime = performance.now();
            await test.testFunction();
            const endTime = performance.now();
            
            // Run afterEach hooks
            for (const hook of this.hooks.afterEach) {
                await hook();
            }
            
            // Run suite afterEach hooks
            for (const hook of suite.hooks.afterEach) {
                await hook();
            }
            
            return {
                description: test.fullDescription,
                status: 'passed',
                duration: endTime - startTime,
                error: null
            };
        } catch (error) {
            return {
                description: test.fullDescription,
                status: 'failed',
                duration: 0,
                error: error.message
            };
        }
    }
}

// Example usage
const mocha = new MochaLikeFramework();

mocha.describe('User management', () => {
    let user;
    
    mocha.beforeEach(() => {
        user = {
            name: 'John Doe',
            email: 'john@example.com',
            age: 30
        };
    });
    
    mocha.describe('User creation', () => {
        mocha.it('should create a user with valid data', () => {
            if (!user.name || !user.email || !user.age) {
                throw new Error('User missing required fields');
            }
        });
        
        mocha.it('should validate email format', () => {
            if (!user.email.includes('@')) {
                throw new Error('Invalid email format');
            }
        });
        
        mocha.it('should validate age is positive', () => {
            if (user.age <= 0) {
                throw new Error('Age must be positive');
            }
        });
    });
    
    mocha.describe('User updates', () => {
        mocha.it('should allow name updates', () => {
            user.name = 'Jane Doe';
            if (user.name !== 'Jane Doe') {
                throw new Error('Name update failed');
            }
        });
        
        mocha.it('should allow age updates', () => {
            const originalAge = user.age;
            user.age = originalAge + 1;
            
            if (user.age !== originalAge + 1) {
                throw new Error('Age update failed');
            }
        });
    });
});

mocha.describe('Database operations', () => {
    let db;
    
    mocha.before(() => {
        db = {
            users: [],
            save: function(user) {
                this.users.push(user);
                return user;
            },
            find: function(id) {
                return this.users.find(u => u.id === id);
            }
        };
    });
    
    mocha.it('should save user to database', () => {
        const user = { id: 1, name: 'Test User' };
        const saved = db.save(user);
        
        if (!saved || saved.id !== 1) {
            throw new Error('Save operation failed');
        }
    });
    
    mocha.it('should find user by id', () => {
        const user = { id: 1, name: 'Test User' };
        db.save(user);
        
        const found = db.find(1);
        if (!found || found.name !== 'Test User') {
            throw new Error('Find operation failed');
        }
    });
});

// Run tests
mocha.run().then(results => {
    console.log('\nMocha-like Test Results:');
    
    results.forEach(result => {
        const icon = result.status === 'passed' ? '✅' : '❌';
        console.log(`${icon} ${result.description}`);
        if (result.error) {
            console.log(`   ${result.error}`);
        }
    });
    
    const passed = results.filter(r => r.status === 'passed').length;
    const failed = results.filter(r => r.status === 'failed').length;
    
    console.log(`\nTotal: ${results.length}, Passed: ${passed}, Failed: ${failed}`);
});
```

## Test-Driven Development

### TDD Workflow Example
```javascript
// Test-Driven Development example
class Calculator {
    constructor() {
        this.history = [];
    }
    
    add(a, b) {
        const result = a + b;
        this.history.push({ operation: 'add', a, b, result });
        return result;
    }
    
    subtract(a, b) {
        const result = a - b;
        this.history.push({ operation: 'subtract', a, b, result });
        return result;
    }
    
    multiply(a, b) {
        const result = a * b;
        this.history.push({ operation: 'multiply', a, b, result });
        return result;
    }
    
    divide(a, b) {
        if (b === 0) {
            throw new Error('Division by zero');
        }
        
        const result = a / b;
        this.history.push({ operation: 'divide', a, b, result });
        return result;
    }
    
    getHistory() {
        return this.history;
    }
    
    clearHistory() {
        this.history = [];
    }
}

// TDD Test Suite
class CalculatorTDD {
    constructor() {
        this.calculator = new Calculator();
        this.testRunner = new TestRunner();
    }
    
    runAllTests() {
        this.testAddition();
        this.testSubtraction();
        this.testMultiplication();
        this.testDivision();
        this.testHistory();
        
        return this.testRunner.run().then(results => {
            this.testRunner.generateReport();
            return results;
        });
    }
    
    testAddition() {
        // Test 1: Basic addition
        this.testRunner.test('should add two positive numbers', () => {
            this.testRunner.assertEqual(this.calculator.add(2, 3), 5);
        });
        
        // Test 2: Addition with negative numbers
        this.testRunner.test('should handle negative numbers', () => {
            this.testRunner.assertEqual(this.calculator.add(-2, 3), 1);
            this.testRunner.assertEqual(this.calculator.add(-2, -3), -5);
        });
        
        // Test 3: Addition with zero
        this.testRunner.test('should handle zero correctly', () => {
            this.testRunner.assertEqual(this.calculator.add(0, 5), 5);
            this.testRunner.assertEqual(this.calculator.add(5, 0), 5);
        });
        
        // Test 4: Addition with decimals
        this.testRunner.test('should handle decimal numbers', () => {
            this.testRunner.assertEqual(this.calculator.add(1.5, 2.5), 4.0);
        });
    }
    
    testSubtraction() {
        // Test 1: Basic subtraction
        this.testRunner.test('should subtract two numbers', () => {
            this.testRunner.assertEqual(this.calculator.subtract(10, 3), 7);
        });
        
        // Test 2: Subtraction resulting in negative
        this.testRunner.test('should handle negative results', () => {
            this.testRunner.assertEqual(this.calculator.subtract(3, 10), -7);
        });
        
        // Test 3: Subtraction with zero
        this.testRunner.test('should handle zero correctly', () => {
            this.testRunner.assertEqual(this.calculator.subtract(5, 0), 5);
            this.testRunner.assertEqual(this.calculator.subtract(0, 5), -5);
        });
    }
    
    testMultiplication() {
        // Test 1: Basic multiplication
        this.testRunner.test('should multiply two numbers', () => {
            this.testRunner.assertEqual(this.calculator.multiply(3, 4), 12);
        });
        
        // Test 2: Multiplication with zero
        this.testRunner.test('should handle zero multiplication', () => {
            this.testRunner.assertEqual(this.calculator.multiply(5, 0), 0);
            this.testRunner.assertEqual(this.calculator.multiply(0, 5), 0);
        });
        
        // Test 3: Multiplication with negative numbers
        this.testRunner.test('should handle negative numbers', () => {
            this.testRunner.assertEqual(this.calculator.multiply(-2, 3), -6);
            this.testRunner.assertEqual(this.calculator.multiply(-2, -3), 6);
        });
    }
    
    testDivision() {
        // Test 1: Basic division
        this.testRunner.test('should divide two numbers', () => {
            this.testRunner.assertEqual(this.calculator.divide(12, 3), 4);
        });
        
        // Test 2: Division with decimals
        this.testRunner.test('should handle decimal division', () => {
            this.testRunner.assertEqual(this.calculator.divide(5, 2), 2.5);
        });
        
        // Test 3: Division by zero should throw error
        this.testRunner.test('should throw error on division by zero', () => {
            this.testRunner.assertThrows(() => {
                this.calculator.divide(5, 0);
            }, Error);
        });
    }
    
    testHistory() {
        // Test 1: History should record operations
        this.testRunner.test('should record operation in history', () => {
            this.calculator.clearHistory();
            this.calculator.add(2, 3);
            
            const history = this.calculator.getHistory();
            this.testRunner.assertEqual(history.length, 1);
            this.testRunner.assertEqual(history[0].operation, 'add');
            this.testRunner.assertEqual(history[0].result, 5);
        });
        
        // Test 2: Multiple operations should be recorded
        this.testRunner.test('should record multiple operations', () => {
            this.calculator.clearHistory();
            this.calculator.add(2, 3);
            this.calculator.subtract(5, 2);
            this.calculator.multiply(3, 4);
            
            const history = this.calculator.getHistory();
            this.testRunner.assertEqual(history.length, 3);
            this.testRunner.assertEqual(history[0].operation, 'add');
            this.testRunner.assertEqual(history[1].operation, 'subtract');
            this.testRunner.assertEqual(history[2].operation, 'multiply');
        });
        
        // Test 3: Clear history should work
        this.testRunner.test('should clear history', () => {
            this.calculator.add(2, 3);
            this.calculator.clearHistory();
            
            const history = this.calculator.getHistory();
            this.testRunner.assertEqual(history.length, 0);
        });
    }
}

// Run TDD tests
const tdd = new CalculatorTDD();
tdd.runAllTests();
```

### Red-Green-Refactor Cycle
```javascript
// Red-Green-Refactor demonstration
class ShoppingCart {
    constructor() {
        this.items = [];
        this.discount = 0;
    }
    
    addItem(item) {
        this.items.push(item);
    }
    
    removeItem(itemId) {
        this.items = this.items.filter(item => item.id !== itemId);
    }
    
    getItems() {
        return this.items;
    }
    
    getTotal() {
        const subtotal = this.items.reduce((total, item) => {
            return total + (item.price * item.quantity);
        }, 0);
        
        return subtotal * (1 - this.discount / 100);
    }
    
    setDiscount(percentage) {
        if (percentage < 0 || percentage > 100) {
            throw new Error('Discount must be between 0 and 100');
        }
        
        this.discount = percentage;
    }
    
    clear() {
        this.items = [];
        this.discount = 0;
    }
}

// TDD Test Suite for ShoppingCart
class ShoppingCartTDD {
    constructor() {
        this.cart = new ShoppingCart();
        this.testRunner = new TestRunner();
    }
    
    runTDDCycle() {
        console.log('=== RED: Write failing tests ===');
        this.redPhase();
        
        console.log('\n=== GREEN: Make tests pass ===');
        this.greenPhase();
        
        console.log('\n=== REFACTOR: Improve code ===');
        this.refactorPhase();
        
        return this.testRunner.run().then(results => {
            this.testRunner.generateReport();
            return results;
        });
    }
    
    redPhase() {
        // RED: Write tests that fail (implementation doesn't exist yet)
        
        // Test 1: Add item to cart
        this.testRunner.test('should add item to cart', () => {
            const item = { id: 1, name: 'Product 1', price: 10, quantity: 1 };
            this.cart.addItem(item);
            
            const items = this.cart.getItems();
            this.testRunner.assertEqual(items.length, 1);
            this.testRunner.assertEqual(items[0].name, 'Product 1');
        });
        
        // Test 2: Calculate total
        this.testRunner.test('should calculate total correctly', () => {
            this.cart.clear();
            this.cart.addItem({ id: 1, name: 'Product 1', price: 10, quantity: 2 });
            this.cart.addItem({ id: 2, name: 'Product 2', price: 5, quantity: 1 });
            
            const total = this.cart.getTotal();
            this.testRunner.assertEqual(total, 25); // 10*2 + 5*1 = 25
        });
        
        // Test 3: Apply discount
        this.testRunner.test('should apply discount correctly', () => {
            this.cart.clear();
            this.cart.addItem({ id: 1, name: 'Product 1', price: 100, quantity: 1 });
            this.cart.setDiscount(20);
            
            const total = this.cart.getTotal();
            this.testRunner.assertEqual(total, 80); // 100 * (1 - 0.2) = 80
        });
        
        // Test 4: Remove item
        this.testRunner.test('should remove item from cart', () => {
            this.cart.clear();
            this.cart.addItem({ id: 1, name: 'Product 1', price: 10, quantity: 1 });
            this.cart.addItem({ id: 2, name: 'Product 2', price: 5, quantity: 1 });
            
            this.cart.removeItem(1);
            
            const items = this.cart.getItems();
            this.testRunner.assertEqual(items.length, 1);
            this.testRunner.assertEqual(items[0].id, 2);
        });
        
        // Test 5: Invalid discount should throw error
        this.testRunner.test('should throw error for invalid discount', () => {
            this.testRunner.assertThrows(() => {
                this.cart.setDiscount(-10);
            }, Error);
            
            this.testRunner.assertThrows(() => {
                this.cart.setDiscount(150);
            }, Error);
        });
    }
    
    greenPhase() {
        // GREEN: Implement minimal code to make tests pass
        // (Implementation already exists in ShoppingCart class)
        
        console.log('Implementation already exists - tests should pass');
    }
    
    refactorPhase() {
        // REFACTOR: Improve code while keeping tests passing
        
        // Add method to get item count
        this.testRunner.test('should return item count', () => {
            this.cart.clear();
            this.cart.addItem({ id: 1, name: 'Product 1', price: 10, quantity: 1 });
            this.cart.addItem({ id: 2, name: 'Product 2', price: 5, quantity: 1 });
            
            this.testRunner.assertEqual(this.cart.getItemCount(), 2);
        });
        
        // Add method to check if cart is empty
        this.testRunner.test('should check if cart is empty', () => {
            this.cart.clear();
            this.testRunner.assertTrue(this.cart.isEmpty());
            
            this.cart.addItem({ id: 1, name: 'Product 1', price: 10, quantity: 1 });
            this.testRunner.assertFalse(this.cart.isEmpty());
        });
        
        // Add method to get item by id
        this.testRunner.test('should get item by id', () => {
            this.cart.clear();
            const item = { id: 1, name: 'Product 1', price: 10, quantity: 1 };
            this.cart.addItem(item);
            
            const foundItem = this.cart.getItemById(1);
            this.testRunner.assertEqual(foundItem.name, 'Product 1');
            
            const notFoundItem = this.cart.getItemById(999);
            this.testRunner.assertEqual(notFoundItem, null);
        });
        
        // Add method to update item quantity
        this.testRunner.test('should update item quantity', () => {
            this.cart.clear();
            this.cart.addItem({ id: 1, name: 'Product 1', price: 10, quantity: 1 });
            
            this.cart.updateItemQuantity(1, 3);
            
            const item = this.cart.getItemById(1);
            this.testRunner.assertEqual(item.quantity, 3);
        });
    }
    
    // Additional methods for refactoring phase
    getItemCount() {
        return this.items.length;
    }
    
    isEmpty() {
        return this.items.length === 0;
    }
    
    getItemById(id) {
        return this.items.find(item => item.id === id) || null;
    }
    
    updateItemQuantity(id, quantity) {
        const item = this.getItemById(id);
        if (item) {
            item.quantity = quantity;
        }
    }
}

// Run TDD cycle
const shoppingCartTDD = new ShoppingCartTDD();
shoppingCartTDD.runTDDCycle();
```

## Integration Testing

### API Integration Testing
```javascript
// API Integration Testing
class APIIntegrationTester {
    constructor(baseURL) {
        this.baseURL = baseURL;
        this.testRunner = new TestRunner();
        this.authToken = null;
    }
    
    async runAllTests() {
        await this.testAuthentication();
        await this.testUserCRUD();
        await this.testDataValidation();
        await this.testErrorHandling();
        
        return this.testRunner.run();
    }
    
    async testAuthentication() {
        // Test login
        this.testRunner.test('should authenticate user', async () => {
            const response = await this.makeRequest('/auth/login', {
                method: 'POST',
                body: JSON.stringify({
                    email: 'test@example.com',
                    password: 'password123'
                })
            });
            
            this.testRunner.assertEqual(response.status, 200);
            this.testRunner.assertTrue(response.data.token);
            
            this.authToken = response.data.token;
        });
        
        // Test invalid credentials
        this.testRunner.test('should reject invalid credentials', async () => {
            const response = await this.makeRequest('/auth/login', {
                method: 'POST',
                body: JSON.stringify({
                    email: 'invalid@example.com',
                    password: 'wrongpassword'
                })
            });
            
            this.testRunner.assertEqual(response.status, 401);
        });
        
        // Test protected endpoint without token
        this.testRunner.test('should reject request without token', async () => {
            const response = await this.makeRequest('/api/users/profile');
            
            this.testRunner.assertEqual(response.status, 401);
        });
    }
    
    async testUserCRUD() {
        // Test create user
        this.testRunner.test('should create user', async () => {
            const userData = {
                name: 'Test User',
                email: 'testuser@example.com',
                age: 25
            };
            
            const response = await this.makeRequest('/api/users', {
                method: 'POST',
                body: JSON.stringify(userData)
            });
            
            this.testRunner.assertEqual(response.status, 201);
            this.testRunner.assertTrue(response.data.id);
            this.testRunner.assertEqual(response.data.name, userData.name);
        });
        
        // Test get user
        this.testRunner.test('should get user by ID', async () => {
            const response = await this.makeRequest('/api/users/1');
            
            this.testRunner.assertEqual(response.status, 200);
            this.testRunner.assertTrue(response.data.id);
        });
        
        // Test update user
        this.testRunner.test('should update user', async () => {
            const updateData = {
                name: 'Updated User',
                age: 26
            };
            
            const response = await this.makeRequest('/api/users/1', {
                method: 'PUT',
                body: JSON.stringify(updateData)
            });
            
            this.testRunner.assertEqual(response.status, 200);
            this.testRunner.assertEqual(response.data.name, 'Updated User');
        });
        
        // Test delete user
        this.testRunner.test('should delete user', async () => {
            const response = await this.makeRequest('/api/users/1', {
                method: 'DELETE'
            });
            
            this.testRunner.assertEqual(response.status, 204);
        });
    }
    
    async testDataValidation() {
        // Test required fields
        this.testRunner.test('should validate required fields', async () => {
            const response = await this.makeRequest('/api/users', {
                method: 'POST',
                body: JSON.stringify({
                    email: 'test@example.com'
                    // Missing required fields
                })
            });
            
            this.testRunner.assertEqual(response.status, 400);
            this.testRunner.assertTrue(response.data.errors);
        });
        
        // Test email format
        this.testRunner.test('should validate email format', async () => {
            const response = await this.makeRequest('/api/users', {
                method: 'POST',
                body: JSON.stringify({
                    name: 'Test User',
                    email: 'invalid-email',
                    age: 25
                })
            });
            
            this.testRunner.assertEqual(response.status, 400);
        });
        
        // Test data types
        this.testRunner.test('should validate data types', async () => {
            const response = await this.makeRequest('/api/users', {
                method: 'POST',
                body: JSON.stringify({
                    name: 'Test User',
                    email: 'test@example.com',
                    age: 'invalid-age' // Should be number
                })
            });
            
            this.testRunner.assertEqual(response.status, 400);
        });
    }
    
    async testErrorHandling() {
        // Test not found
        this.testRunner.test('should return 404 for non-existent user', async () => {
            const response = await this.makeRequest('/api/users/99999');
            
            this.testRunner.assertEqual(response.status, 404);
        });
        
        // Test malformed JSON
        this.testRunner.test('should handle malformed JSON', async () => {
            try {
                const response = await fetch(`${this.baseURL}/api/users`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.authToken}`
                    },
                    body: 'invalid-json'
                });
                
                this.testRunner.assertEqual(response.status, 400);
            } catch (error) {
                this.testRunner.assertTrue(true); // Expected to fail
            }
        });
        
        // Test rate limiting
        this.testRunner.test('should handle rate limiting', async () => {
            const promises = [];
            
            // Make multiple rapid requests
            for (let i = 0; i < 10; i++) {
                promises.push(this.makeRequest('/api/users'));
            }
            
            const responses = await Promise.allSettled(promises);
            const rateLimited = responses.some(r => 
                r.status === 429 || (r.value && r.value.status === 429)
            );
            
            this.testRunner.assertTrue(rateLimited);
        });
    }
    
    async makeRequest(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json'
            }
        };
        
        const config = { ...defaultOptions, ...options };
        
        if (this.authToken) {
            config.headers['Authorization'] = `Bearer ${this.authToken}`;
        }
        
        const response = await fetch(url, config);
        
        return {
            status: response.status,
            data: await response.json().catch(() => null)
        };
    }
}

// Usage example
const apiTester = new APIIntegrationTester('https://api.example.com');
apiTester.runAllTests().then(results => {
    console.log('API Integration Test Results:');
    apiTester.testRunner.generateReport();
});
```

### Database Integration Testing
```javascript
// Database Integration Testing
class DatabaseIntegrationTester {
    constructor() {
        this.testRunner = new TestRunner();
        this.db = null;
    }
    
    async setupDatabase() {
        // Mock database for testing
        this.db = {
            users: new Map(),
            posts: new Map(),
            
            async create(table, data) {
                const id = Date.now().toString();
                const record = { id, ...data, createdAt: new Date() };
                
                if (table === 'users') {
                    this.db.users.set(id, record);
                } else if (table === 'posts') {
                    this.db.posts.set(id, record);
                }
                
                return record;
            },
            
            async findById(table, id) {
                if (table === 'users') {
                    return this.db.users.get(id) || null;
                } else if (table === 'posts') {
                    return this.db.posts.get(id) || null;
                }
                return null;
            },
            
            async findAll(table) {
                if (table === 'users') {
                    return Array.from(this.db.users.values());
                } else if (table === 'posts') {
                    return Array.from(this.db.posts.values());
                }
                return [];
            },
            
            async update(table, id, data) {
                if (table === 'users') {
                    const record = this.db.users.get(id);
                    if (record) {
                        const updated = { ...record, ...data, updatedAt: new Date() };
                        this.db.users.set(id, updated);
                        return updated;
                    }
                } else if (table === 'posts') {
                    const record = this.db.posts.get(id);
                    if (record) {
                        const updated = { ...record, ...data, updatedAt: new Date() };
                        this.db.posts.set(id, updated);
                        return updated;
                    }
                }
                return null;
            },
            
            async delete(table, id) {
                if (table === 'users') {
                    return this.db.users.delete(id);
                } else if (table === 'posts') {
                    return this.db.posts.delete(id);
                }
                return false;
            }
        };
    }
    
    async runAllTests() {
        await this.setupDatabase();
        
        await this.testUserOperations();
        await this.testPostOperations();
        await this.testRelationships();
        await this.testTransactions();
        
        return this.testRunner.run();
    }
    
    async testUserOperations() {
        // Test create user
        this.testRunner.test('should create user in database', async () => {
            const userData = {
                name: 'John Doe',
                email: 'john@example.com',
                age: 30
            };
            
            const user = await this.db.create('users', userData);
            
            this.testRunner.assertTrue(user.id);
            this.testRunner.assertEqual(user.name, userData.name);
            this.testRunner.assertEqual(user.email, userData.email);
            this.testRunner.assertTrue(user.createdAt);
        });
        
        // Test find user by ID
        this.testRunner.test('should find user by ID', async () => {
            const user = await this.db.create('users', {
                name: 'Jane Doe',
                email: 'jane@example.com',
                age: 25
            });
            
            const found = await this.db.findById('users', user.id);
            
            this.testRunner.assertNotNull(found);
            this.testRunner.assertEqual(found.id, user.id);
            this.testRunner.assertEqual(found.name, 'Jane Doe');
        });
        
        // Test update user
        this.testRunner.test('should update user in database', async () => {
            const user = await this.db.create('users', {
                name: 'Bob Smith',
                email: 'bob@example.com',
                age: 35
            });
            
            const updated = await this.db.update('users', user.id, {
                age: 36,
                status: 'active'
            });
            
            this.testRunner.assertNotNull(updated);
            this.testRunner.assertEqual(updated.age, 36);
            this.testRunner.assertEqual(updated.status, 'active');
            this.testRunner.assertTrue(updated.updatedAt);
        });
        
        // Test delete user
        this.testRunner.test('should delete user from database', async () => {
            const user = await this.db.create('users', {
                name: 'Alice Johnson',
                email: 'alice@example.com',
                age: 28
            });
            
            const deleted = await this.db.delete('users', user.id);
            
            this.testRunner.assertTrue(deleted);
            
            const found = await this.db.findById('users', user.id);
            this.testRunner.assertNull(found);
        });
    }
    
    async testPostOperations() {
        // Test create post with user relationship
        this.testRunner.test('should create post with user relationship', async () => {
            const user = await this.db.create('users', {
                name: 'Test User',
                email: 'test@example.com',
                age: 30
            });
            
            const post = await this.db.create('posts', {
                title: 'Test Post',
                content: 'This is a test post',
                userId: user.id
            });
            
            this.testRunner.assertEqual(post.userId, user.id);
            this.testRunner.assertTrue(post.createdAt);
        });
        
        // Test find posts by user
        this.testRunner.test('should find posts by user', async () => {
            const user = await this.db.create('users', {
                name: 'Content Creator',
                email: 'creator@example.com',
                age: 25
            });
            
            await this.db.create('posts', {
                title: 'Post 1',
                content: 'Content 1',
                userId: user.id
            });
            
            await this.db.create('posts', {
                title: 'Post 2',
                content: 'Content 2',
                userId: user.id
            });
            
            const allPosts = await this.db.findAll('posts');
            const userPosts = allPosts.filter(post => post.userId === user.id);
            
            this.testRunner.assertEqual(userPosts.length, 2);
        });
    }
    
    async testRelationships() {
        // Test user-posts relationship integrity
        this.testRunner.test('should maintain user-posts relationship integrity', async () => {
            const user = await this.db.create('users', {
                name: 'Relation Test',
                email: 'relation@example.com',
                age: 30
            });
            
            const post = await this.db.create('posts', {
                title: 'Relation Post',
                content: 'Testing relationships',
                userId: user.id
            });
            
            // Verify relationship
            const foundUser = await this.db.findById('users', post.userId);
            this.testRunner.assertNotNull(foundUser);
            this.testRunner.assertEqual(foundUser.id, user.id);
            
            // Verify user has posts
            const allPosts = await this.db.findAll('posts');
            const userPosts = allPosts.filter(p => p.userId === user.id);
            this.testRunner.assertTrue(userPosts.some(p => p.id === post.id));
        });
        
        // Test cascade delete
        this.testRunner.test('should handle cascade delete', async () => {
            const user = await this.db.create('users', {
                name: 'Cascade Test',
                email: 'cascade@example.com',
                age: 25
            });
            
            await this.db.create('posts', {
                title: 'Cascade Post',
                content: 'Testing cascade delete',
                userId: user.id
            });
            
            // Delete user
            await this.db.delete('users', user.id);
            
            // Verify post still exists (or was deleted based on requirements)
            const post = await this.db.findById('posts', user.id);
            // In a real database, this might be null due to cascade delete
            // For this mock, we'll just verify the user is gone
            const deletedUser = await this.db.findById('users', user.id);
            this.testRunner.assertNull(deletedUser);
        });
    }
    
    async testTransactions() {
        // Test transaction rollback
        this.testRunner.test('should handle transaction rollback', async () => {
            const initialUserCount = (await this.db.findAll('users')).length;
            
            try {
                // Simulate transaction
                const user = await this.db.create('users', {
                    name: 'Transaction User',
                    email: 'transaction@example.com',
                    age: 30
                });
                
                // Simulate error
                throw new Error('Transaction failed');
                
            } catch (error) {
                // Rollback - delete created user
                const users = await this.db.findAll('users');
                const foundUser = users.find(u => u.email === 'transaction@example.com');
                
                if (foundUser) {
                    await this.db.delete('users', foundUser.id);
                }
            }
            
            const finalUserCount = (await this.db.findAll('users')).length;
            this.testRunner.assertEqual(initialUserCount, finalUserCount);
        });
    }
    
    assertNotNull(value) {
        if (value === null || value === undefined) {
            throw new Error('Expected value not to be null');
        }
    }
    
    assertNull(value) {
        if (value !== null) {
            throw new Error('Expected value to be null');
        }
    }
    
    assertTrue(value) {
        if (!value) {
            throw new Error('Expected value to be true');
        }
    }
    
    assertFalse(value) {
        if (value) {
            throw new Error('Expected value to be false');
        }
    }
}

// Usage example
const dbTester = new DatabaseIntegrationTester();
dbTester.runAllTests().then(results => {
    console.log('Database Integration Test Results:');
    dbTester.testRunner.generateReport();
});
```

## End-to-End Testing

### E2E Testing with Puppeteer
```javascript
// End-to-End Testing with Puppeteer-like API
class E2ETester {
    constructor() {
        this.testRunner = new TestRunner();
        this.page = null;
        this.browser = null;
    }
    
    async setup() {
        // Mock browser API for demonstration
        this.page = {
            goto: async (url) => {
                console.log(`Navigating to: ${url}`);
                return { status: 'ok' };
            },
            
            click: async (selector) => {
                console.log(`Clicking: ${selector}`);
                return true;
            },
            
            type: async (selector, text) => {
                console.log(`Typing "${text}" into: ${selector}`);
                return true;
            },
            
            waitForSelector: async (selector) => {
                console.log(`Waiting for: ${selector}`);
                return true;
            },
            
            evaluate: async (fn) => {
                return fn();
            },
            
            screenshot: async (filename) => {
                console.log(`Taking screenshot: ${filename}`);
                return true;
            }
        };
    }
    
    async runAllTests() {
        await this.setup();
        
        await this.testUserRegistration();
        await this.testUserLogin();
        await this.testDashboard();
        await this.testUserLogout();
        
        return this.testRunner.run();
    }
    
    async testUserRegistration() {
        // Test navigation to registration page
        this.testRunner.test('should navigate to registration page', async () => {
            const response = await this.page.goto('/register');
            this.testRunner.assertEqual(response.status, 'ok');
        });
        
        // Test form validation
        this.testRunner.test('should validate registration form', async () => {
            await this.page.click('#register-button');
            
            // Check for validation errors
            const hasErrors = await this.page.evaluate(() => {
                const errors = document.querySelectorAll('.error-message');
                return errors.length > 0;
            });
            
            this.testRunner.assertTrue(hasErrors);
        });
        
        // Test successful registration
        this.testRunner.test('should register new user successfully', async () => {
            await this.page.type('#name', 'John Doe');
            await this.page.type('#email', 'john@example.com');
            await this.page.type('#password', 'password123');
            await this.page.type('#confirm-password', 'password123');
            
            await this.page.click('#register-button');
            
            // Wait for success message
            await this.page.waitForSelector('.success-message');
            
            const isSuccess = await this.page.evaluate(() => {
                const message = document.querySelector('.success-message');
                return message && message.textContent.includes('Registration successful');
            });
            
            this.testRunner.assertTrue(isSuccess);
        });
    }
    
    async testUserLogin() {
        // Test navigation to login page
        this.testRunner.test('should navigate to login page', async () => {
            const response = await this.page.goto('/login');
            this.testRunner.assertEqual(response.status, 'ok');
        });
        
        // Test successful login
        this.testRunner.test('should login successfully', async () => {
            await this.page.type('#email', 'john@example.com');
            await this.page.type('#password', 'password123');
            
            await this.page.click('#login-button');
            
            // Wait for redirect to dashboard
            await this.page.waitForSelector('.dashboard');
            
            const isLoggedIn = await this.page.evaluate(() => {
                return window.location.pathname === '/dashboard';
            });
            
            this.testRunner.assertTrue(isLoggedIn);
        });
        
        // Test login with invalid credentials
        this.testRunner.test('should reject invalid credentials', async () => {
            await this.page.goto('/login');
            await this.page.type('#email', 'invalid@example.com');
            await this.page.type('#password', 'wrongpassword');
            
            await this.page.click('#login-button');
            
            // Wait for error message
            await this.page.waitForSelector('.error-message');
            
            const hasError = await this.page.evaluate(() => {
                const error = document.querySelector('.error-message');
                return error && error.textContent.includes('Invalid credentials');
            });
            
            this.testRunner.assertTrue(hasError);
        });
    }
    
    async testDashboard() {
        // Test dashboard loads
        this.testRunner.test('should load dashboard components', async () => {
            await this.page.goto('/dashboard');
            
            const components = await this.page.evaluate(() => {
                return {
                    hasHeader: !!document.querySelector('.header'),
                    hasSidebar: !!document.querySelector('.sidebar'),
                    hasMainContent: !!document.querySelector('.main-content'),
                    hasUserMenu: !!document.querySelector('.user-menu')
                };
            });
            
            this.testRunner.assertTrue(components.hasHeader);
            this.testRunner.assertTrue(components.hasSidebar);
            this.testRunner.assertTrue(components.hasMainContent);
            this.testRunner.assertTrue(components.hasUserMenu);
        });
        
        // Test user profile display
        this.testRunner.test('should display user profile', async () => {
            const profile = await this.page.evaluate(() => {
                const profileElement = document.querySelector('.user-profile');
                if (!profileElement) return null;
                
                return {
                    name: profileElement.querySelector('.name')?.textContent,
                    email: profileElement.querySelector('.email')?.textContent
                };
            });
            
            this.testRunner.assertNotNull(profile);
            this.testRunner.assertEqual(profile.name, 'John Doe');
            this.testRunner.assertEqual(profile.email, 'john@example.com');
        });
        
        // Test navigation
        this.testRunner.test('should navigate between sections', async () => {
            await this.page.click('#nav-profile');
            await this.page.waitForSelector('.profile-section');
            
            const isProfileVisible = await this.page.evaluate(() => {
                return document.querySelector('.profile-section').style.display !== 'none';
            });
            
            this.testRunner.assertTrue(isProfileVisible);
            
            await this.page.click('#nav-settings');
            await this.page.waitForSelector('.settings-section');
            
            const isSettingsVisible = await this.page.evaluate(() => {
                return document.querySelector('.settings-section').style.display !== 'none';
            });
            
            this.testRunner.assertTrue(isSettingsVisible);
        });
    }
    
    async testUserLogout() {
        // Test logout functionality
        this.testRunner.test('should logout successfully', async () => {
            await this.page.click('#logout-button');
            
            // Wait for redirect to login page
            await this.page.waitForSelector('#login-form');
            
            const isLoggedIn = await this.page.evaluate(() => {
                return window.location.pathname === '/dashboard';
            });
            
            this.testRunner.assertFalse(isLoggedIn);
            
            const isOnLoginPage = await this.page.evaluate(() => {
                return window.location.pathname === '/login';
            });
            
            this.testRunner.assertTrue(isOnLoginPage);
        });
        
        // Test session invalidation
        this.testRunner.test('should invalidate session after logout', async () => {
            // Try to access protected page after logout
            const response = await this.page.goto('/dashboard');
            
            // Should be redirected to login
            const isRedirected = await this.page.evaluate(() => {
                return window.location.pathname === '/login';
            });
            
            this.testRunner.assertTrue(isRedirected);
        });
    }
    
    async takeScreenshot(testName) {
        const filename = `screenshots/${testName}-${Date.now()}.png`;
        await this.page.screenshot(filename);
        console.log(`Screenshot saved: ${filename}`);
    }
    
    async generateReport() {
        const results = await this.testRunner.run();
        
        console.log('\nE2E Test Results:');
        
        results.forEach(result => {
            const icon = result.status === 'passed' ? '✅' : '❌';
            console.log(`${icon} ${result.name}`);
            if (result.error) {
                console.log(`   ${result.error}`);
            }
        });
        
        const passed = results.filter(r => r.status === 'passed').length;
        const failed = results.filter(r => r.status === 'failed').length;
        
        console.log(`\nTotal: ${results.length}, Passed: ${passed}, Failed: ${failed}`);
        
        return results;
    }
}

// Usage example
const e2eTester = new E2ETester();
e2eTester.runAllTests().then(results => {
    console.log('E2E Testing completed');
});
```

## Testing Best Practices

### Testing Best Practices
```javascript
// Testing Best Practices Guide
class TestingBestPractices {
    constructor() {
        this.guidelines = [
            'Write tests first (TDD)',
            'Write descriptive test names',
            'Test one thing per test',
            'Use AAA pattern (Arrange, Act, Assert)',
            'Use meaningful test data',
            'Test edge cases and error conditions',
            'Keep tests independent',
            'Use test doubles appropriately',
            'Maintain test code quality',
            'Run tests frequently'
        ];
    }
    
    demonstrateAAA() {
        // ARRANGE: Set up test data and conditions
        const calculator = {
            add: (a, b) => a + b
        };
        
        const a = 5;
        const b = 3;
        const expected = 8;
        
        // ACT: Execute the code being tested
        const result = calculator.add(a, b);
        
        // ASSERT: Verify the outcome
        if (result !== expected) {
            throw new Error(`Expected ${expected}, got ${result}`);
        }
    }
    
    demonstrateDescriptiveNaming() {
        // Good: Descriptive test name
        function testAdditionWithPositiveNumbers() {
            // Test implementation
        }
        
        // Bad: Vague test name
        function testAdd() {
            // Test implementation
        }
        
        // Good: Descriptive test name with context
        function testCalculatorAddShouldReturnCorrectResultForPositiveNumbers() {
            // Test implementation
        }
    }
    
    demonstrateSingleResponsibility() {
        // Good: One assertion per test
        function testAdditionShouldReturnCorrectResult() {
            const result = calculator.add(2, 3);
            if (result !== 5) {
                throw new Error('Expected 5, got ' + result);
            }
        }
        
        function testAdditionShouldHandleNegativeNumbers() {
            const result = calculator.add(-2, 3);
            if (result !== 1) {
                throw new Error('Expected 1, got ' + result);
            }
        }
        
        // Bad: Multiple assertions in one test
        function testAdditionMultipleAssertions() {
            const result1 = calculator.add(2, 3);
            const result2 = calculator.add(-2, 3);
            
            if (result1 !== 5) {
                throw new Error('First assertion failed');
            }
            
            if (result2 !== 1) {
                throw new Error('Second assertion failed');
            }
        }
    }
    
    demonstrateTestIndependence() {
        // Good: Tests don't depend on each other
        function testUserCreation() {
            const user = new User({ name: 'John', email: 'john@example.com' });
            if (user.name !== 'John') {
                throw new Error('User creation failed');
            }
        }
        
        function testUserUpdate() {
            const user = new User({ name: 'Jane', email: 'jane@example.com' });
            user.name = 'Jane Updated';
            if (user.name !== 'Jane Updated') {
                throw new Error('User update failed');
            }
        }
        
        // Bad: Tests depend on each other
        let globalUser;
        
        function testUserCreationWithDependency() {
            globalUser = new User({ name: 'John', email: 'john@example.com' });
        }
        
        function testUserUpdateWithDependency() {
            globalUser.name = 'John Updated';
            if (globalUser.name !== 'John Updated') {
                throw new Error('User update failed');
            }
        }
    }
    
    demonstrateTestDataManagement() {
        // Good: Use test data factories
        function createTestUser(overrides = {}) {
            const defaults = {
                name: 'Test User',
                email: 'test@example.com',
                age: 30
            };
            
            return { ...defaults, ...overrides };
        }
        
        function testUserWithDefaults() {
            const user = createTestUser();
            if (user.name !== 'Test User') {
                throw new Error('Default user creation failed');
            }
        }
        
        function testUserWithOverrides() {
            const user = createTestUser({ name: 'Custom User' });
            if (user.name !== 'Custom User') {
                throw new Error('Custom user creation failed');
            }
        }
        
        // Good: Use builders for complex objects
        class UserBuilder {
            constructor() {
                this.user = {
                    name: 'Default User',
                    email: 'default@example.com',
                    age: 25,
                    active: true
                };
            }
            
            withName(name) {
                this.user.name = name;
                return this;
            }
            
            withEmail(email) {
                this.user.email = email;
                return this;
            }
            
            withAge(age) {
                this.user.age = age;
                return this;
            }
            
            inactive() {
                this.user.active = false;
                return this;
            }
            
            build() {
                return new User(this.user);
            }
        }
        
        function testUserBuilder() {
            const user = new UserBuilder()
                .withName('Builder User')
                .withEmail('builder@example.com')
                .withAge(35)
                .inactive()
                .build();
            
            if (user.name !== 'Builder User') {
                throw new Error('Builder user creation failed');
            }
        }
    }
    
    demonstrateMockingBestPractices() {
        // Good: Use mocks for external dependencies
        class UserService {
            constructor(apiClient) {
                this.apiClient = apiClient;
            }
            
            async getUser(id) {
                const response = await this.apiClient.get(`/users/${id}`);
                return response.data;
            }
        }
        
        function testUserServiceWithMock() {
            const mockApiClient = new Mock();
            mockApiClient.returns({ data: { id: 1, name: 'Test User' } });
            
            const userService = new UserService(mockApiClient);
            
            // Test would use the mock instead of real API
        }
        
        // Good: Use stubs for partial functionality
        function testWithStub() {
            const realService = new UserService();
            const stubbedService = new Stub(realService);
            
            stubbedService.method('getUser', (id) => {
                if (id === 1) {
                    return { id: 1, name: 'Stubbed User' };
                }
                return realService.getUser(id);
            });
        }
    }
    
    demonstrateErrorTesting() {
        // Good: Test error conditions
        function testDivisionByZero() {
            try {
                const result = 10 / 0;
                throw new Error('Expected division by zero error');
            } catch (error) {
                if (error.message !== 'Division by zero') {
                    throw new Error('Unexpected error message');
                }
            }
        }
        
        // Good: Test with invalid inputs
        function testInvalidEmailFormat() {
            const validator = new EmailValidator();
            
            const invalidEmails = [
                'invalid-email',
                '@domain.com',
                'user@',
                'user@domain',
                'user name@domain.com'
            ];
            
            invalidEmails.forEach(email => {
                if (validator.isValid(email)) {
                    throw new Error(`Email "${email}" should be invalid`);
                }
            });
        }
        
        // Good: Test boundary conditions
        function testArrayBoundaryConditions() {
            const arr = [1, 2, 3];
            
            // Test empty array
            if (arr.slice(0, 0).length !== 0) {
                throw new Error('Empty array slice failed');
            }
            
            // Test full array
            if (arr.slice(0, 3).length !== 3) {
                throw new Error('Full array slice failed');
            }
            
            // Test beyond array bounds
            if (arr.slice(0, 10).length !== 3) {
                throw new Error('Beyond bounds slice failed');
            }
        }
    }
    
    generateGuidelines() {
        console.log('Testing Best Practices:');
        this.guidelines.forEach((guideline, index) => {
            console.log(`${index + 1}. ${guideline}`);
        });
    }
}

// Usage example
const bestPractices = new TestingBestPractices();
bestPractices.generateGuidelines();
```

## Summary

JavaScript testing encompasses comprehensive approaches:

**Testing Fundamentals:**
- Unit testing basics
- Test runners and frameworks
- Assertions and expectations
- Test organization and structure

**Test Doubles:**
- Mock objects for isolation
- Stubs for partial functionality
- Spies for interaction verification
- Fakes for lightweight alternatives

**Testing Frameworks:**
- Jest-like framework features
- Mocha-like structure
- Custom framework creation
- Assertion libraries

**Test-Driven Development:**
- Red-Green-Refactor cycle
- Writing failing tests first
- Minimal implementation
- Continuous refactoring

**Integration Testing:**
- API integration testing
- Database integration testing
- Service layer testing
- End-to-end scenarios

**E2E Testing:**
- Browser automation
- User flow testing
- UI component testing
- Visual regression testing

**Best Practices:**
- AAA pattern (Arrange, Act, Assert)
- Descriptive test naming
- Test independence
- Meaningful test data
- Error condition testing
- Proper mocking strategies

**Testing Tools:**
- Unit testing frameworks
- Integration testing tools
- E2E testing solutions
- Test automation
- Continuous integration

**Testing Strategies:**
- Test pyramid approach
- Test coverage analysis
- Test data management
- Environment setup
- CI/CD integration

Effective testing ensures code quality, prevents regressions, and provides confidence in system reliability through comprehensive validation at all levels.
