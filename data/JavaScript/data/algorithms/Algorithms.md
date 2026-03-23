# JavaScript Algorithms

Core algorithms implemented in modern JavaScript (ES2022+) with practical examples and complexity analysis.

---

## Big-O Quick Reference

| Algorithm | Time | Space |
|---|---|---|
| Binary Search | O(log n) | O(1) |
| Merge Sort | O(n log n) | O(n) |
| Quick Sort | O(n log n) avg | O(log n) |
| BFS / DFS | O(V + E) | O(V) |
| Dynamic Programming | problem-dependent | problem-dependent |

---

## 1. Searching

### Linear Search
```js
function linearSearch(arr, target) {
    for (let i = 0; i < arr.length; i++)
        if (arr[i] === target) return i;
    return -1;
}
```

### Binary Search
```js
function binarySearch(arr, target) {
    let left = 0, right = arr.length - 1;
    while (left <= right) {
        const mid = Math.floor((left + right) / 2);
        if (arr[mid] === target) return mid;
        else if (arr[mid] < target) left = mid + 1;
        else right = mid - 1;
    }
    return -1;
}
```

---

## 2. Sorting

### Merge Sort — O(n log n), stable
```js
function mergeSort(arr) {
    if (arr.length <= 1) return arr;
    const mid = Math.floor(arr.length / 2);
    const left  = mergeSort(arr.slice(0, mid));
    const right = mergeSort(arr.slice(mid));
    return merge(left, right);
}

function merge(left, right) {
    const result = [];
    let i = 0, j = 0;
    while (i < left.length && j < right.length)
        result.push(left[i] <= right[j] ? left[i++] : right[j++]);
    return result.concat(left.slice(i), right.slice(j));
}
```

### Quick Sort — O(n log n) average
```js
function quickSort(arr) {
    if (arr.length <= 1) return arr;
    const pivot = arr[Math.floor(arr.length / 2)];
    const left   = arr.filter(x => x < pivot);
    const middle = arr.filter(x => x === pivot);
    const right  = arr.filter(x => x > pivot);
    return [...quickSort(left), ...middle, ...quickSort(right)];
}
```

---

## 3. Graph Algorithms

### BFS — Shortest Path (Unweighted)
```js
function bfs(graph, start) {
    const visited = new Set([start]);
    const queue   = [start];
    const order   = [];
    while (queue.length) {
        const node = queue.shift();
        order.push(node);
        for (const neighbor of graph[node] || [])
            if (!visited.has(neighbor)) { visited.add(neighbor); queue.push(neighbor); }
    }
    return order;
}
```

### DFS — Recursive
```js
function dfs(graph, node, visited = new Set()) {
    visited.add(node);
    const result = [node];
    for (const neighbor of graph[node] || [])
        if (!visited.has(neighbor)) result.push(...dfs(graph, neighbor, visited));
    return result;
}
```

---

## 4. Dynamic Programming

### Fibonacci (Memoized)
```js
function fib(n, memo = {}) {
    if (n <= 1) return n;
    if (memo[n]) return memo[n];
    return memo[n] = fib(n - 1, memo) + fib(n - 2, memo);
}
```

### Longest Common Subsequence
```js
function lcs(a, b) {
    const m = a.length, n = b.length;
    const dp = Array.from({ length: m + 1 }, () => Array(n + 1).fill(0));
    for (let i = 1; i <= m; i++)
        for (let j = 1; j <= n; j++)
            dp[i][j] = a[i-1] === b[j-1] ? dp[i-1][j-1] + 1
                                           : Math.max(dp[i-1][j], dp[i][j-1]);
    return dp[m][n];
}
```

### Coin Change — Minimum Coins
```js
function coinChange(coins, amount) {
    const dp = Array(amount + 1).fill(Infinity);
    dp[0] = 0;
    for (let i = 1; i <= amount; i++)
        for (const coin of coins)
            if (coin <= i) dp[i] = Math.min(dp[i], dp[i - coin] + 1);
    return dp[amount] === Infinity ? -1 : dp[amount];
}
```

---

## 5. String Algorithms

### Palindrome Check
```js
const isPalindrome = s => s === [...s].reverse().join('');
```

### Anagram Check
```js
function isAnagram(a, b) {
    if (a.length !== b.length) return false;
    const freq = {};
    for (const c of a) freq[c] = (freq[c] || 0) + 1;
    for (const c of b) {
        if (!freq[c]) return false;
        freq[c]--;
    }
    return true;
}
```

> 💡 In JavaScript, prefer `Array.prototype` methods over manual loops for sorting/filtering — they're highly optimized in V8 and produce cleaner code.
