// ============================================================
//  JavaScript Algorithms — Complete Examples
// ============================================================

// ─────────────────────────────────────────────
// SECTION 1: Searching
// ─────────────────────────────────────────────

function linearSearch(arr, target) {
    for (let i = 0; i < arr.length; i++)
        if (arr[i] === target) return i;
    return -1;
}

function binarySearch(arr, target) {
    let left = 0, right = arr.length - 1;
    while (left <= right) {
        const mid = left + Math.floor((right - left) / 2);
        if (arr[mid] === target) return mid;
        else if (arr[mid] < target) left = mid + 1;
        else right = mid - 1;
    }
    return -1;
}

// ─────────────────────────────────────────────
// SECTION 2: Sorting
// ─────────────────────────────────────────────

function merge(left, right) {
    const result = [];
    let i = 0, j = 0;
    while (i < left.length && j < right.length)
        result.push(left[i] <= right[j] ? left[i++] : right[j++]);
    return result.concat(left.slice(i), right.slice(j));
}

function mergeSort(arr) {
    if (arr.length <= 1) return arr;
    const mid = Math.floor(arr.length / 2);
    return merge(mergeSort(arr.slice(0, mid)), mergeSort(arr.slice(mid)));
}

function quickSort(arr) {
    if (arr.length <= 1) return arr;
    const pivot  = arr[Math.floor(arr.length / 2)];
    const left   = arr.filter(x => x < pivot);
    const middle = arr.filter(x => x === pivot);
    const right  = arr.filter(x => x > pivot);
    return [...quickSort(left), ...middle, ...quickSort(right)];
}

function bubbleSort(arr) {
    const a = [...arr];
    for (let i = 0; i < a.length - 1; i++) {
        let swapped = false;
        for (let j = 0; j < a.length - i - 1; j++) {
            if (a[j] > a[j + 1]) { [a[j], a[j+1]] = [a[j+1], a[j]]; swapped = true; }
        }
        if (!swapped) break;
    }
    return a;
}

// ─────────────────────────────────────────────
// SECTION 3: Graph Algorithms
// ─────────────────────────────────────────────

function bfs(graph, start) {
    const visited = new Set([start]);
    const queue   = [start];
    const order   = [];
    while (queue.length) {
        const node = queue.shift();
        order.push(node);
        for (const n of graph[node] || [])
            if (!visited.has(n)) { visited.add(n); queue.push(n); }
    }
    return order;
}

function dfs(graph, node, visited = new Set()) {
    visited.add(node);
    const result = [node];
    for (const n of graph[node] || [])
        if (!visited.has(n)) result.push(...dfs(graph, n, visited));
    return result;
}

// BFS shortest path with distances
function shortestPath(graph, start, end) {
    if (start === end) return { path: [start], distance: 0 };
    const queue    = [[start, [start]]];
    const visited  = new Set([start]);
    while (queue.length) {
        const [node, path] = queue.shift();
        for (const n of graph[node] || []) {
            if (!visited.has(n)) {
                const newPath = [...path, n];
                if (n === end) return { path: newPath, distance: newPath.length - 1 };
                visited.add(n);
                queue.push([n, newPath]);
            }
        }
    }
    return { path: [], distance: -1 };
}

// ─────────────────────────────────────────────
// SECTION 4: Dynamic Programming
// ─────────────────────────────────────────────

function fib(n, memo = {}) {
    if (n <= 1) return n;
    if (n in memo) return memo[n];
    return memo[n] = fib(n - 1, memo) + fib(n - 2, memo);
}

function lcs(a, b) {
    const m = a.length, n = b.length;
    const dp = Array.from({ length: m + 1 }, () => Array(n + 1).fill(0));
    for (let i = 1; i <= m; i++)
        for (let j = 1; j <= n; j++)
            dp[i][j] = a[i-1] === b[j-1] ? dp[i-1][j-1] + 1 : Math.max(dp[i-1][j], dp[i][j-1]);
    return dp[m][n];
}

function coinChange(coins, amount) {
    const dp = Array(amount + 1).fill(Infinity);
    dp[0] = 0;
    for (let i = 1; i <= amount; i++)
        for (const c of coins)
            if (c <= i) dp[i] = Math.min(dp[i], dp[i - c] + 1);
    return dp[amount] === Infinity ? -1 : dp[amount];
}

function knapsack(weights, values, capacity) {
    const n = weights.length;
    const dp = Array.from({ length: n + 1 }, () => Array(capacity + 1).fill(0));
    for (let i = 1; i <= n; i++)
        for (let w = 0; w <= capacity; w++) {
            dp[i][w] = dp[i-1][w];
            if (weights[i-1] <= w)
                dp[i][w] = Math.max(dp[i][w], dp[i-1][w - weights[i-1]] + values[i-1]);
        }
    return dp[n][capacity];
}

// ─────────────────────────────────────────────
// SECTION 5: String Algorithms
// ─────────────────────────────────────────────

const isPalindrome = s => {
    const clean = s.toLowerCase().replace(/[^a-z0-9]/g, '');
    return clean === [...clean].reverse().join('');
};

function isAnagram(a, b) {
    if (a.length !== b.length) return false;
    const freq = {};
    for (const c of a.toLowerCase()) freq[c] = (freq[c] || 0) + 1;
    for (const c of b.toLowerCase()) {
        if (!freq[c]) return false;
        freq[c]--;
    }
    return true;
}

function longestSubstringNoRepeat(s) {
    const seen = new Map();
    let start = 0, maxLen = 0;
    for (let end = 0; end < s.length; end++) {
        if (seen.has(s[end]) && seen.get(s[end]) >= start)
            start = seen.get(s[end]) + 1;
        seen.set(s[end], end);
        maxLen = Math.max(maxLen, end - start + 1);
    }
    return maxLen;
}

// ─────────────────────────────────────────────
// DEMOS
// ─────────────────────────────────────────────

console.log('===== JavaScript Algorithms Demo =====\n');

// Searching
console.log('--- Searching ---');
const sorted = [2, 5, 8, 12, 16, 23, 38, 56, 72, 91];
console.log('Array:', sorted.join(', '));
console.log('Linear search 23:', linearSearch(sorted, 23));
console.log('Binary search 56:', binarySearch(sorted, 56));
console.log('Binary search 99:', binarySearch(sorted, 99));

// Sorting
console.log('\n--- Sorting ---');
const data = [64, 34, 25, 12, 22, 11, 90];
console.log('Original:   ', data.join(', '));
console.log('Bubble Sort:', bubbleSort(data).join(', '));
console.log('Merge Sort: ', mergeSort(data).join(', '));
console.log('Quick Sort: ', quickSort(data).join(', '));

// Graph
console.log('\n--- Graph BFS & DFS ---');
const graph = { A: ['B','C'], B: ['D','E'], C: ['F'], D: [], E: [], F: [] };
console.log('BFS from A:', bfs(graph, 'A').join(' → '));
console.log('DFS from A:', dfs(graph, 'A').join(' → '));
const path = shortestPath(graph, 'A', 'E');
console.log('Shortest path A→E:', path.path.join('→'), '(distance:', path.distance + ')');

// Dynamic Programming
console.log('\n--- Dynamic Programming ---');
console.log('Fibonacci:', Array.from({length: 11}, (_, i) => fib(i)).join(', '));
console.log('LCS("ABCBDAB","BDCAB"):', lcs('ABCBDAB', 'BDCAB'));
console.log('Coin change(amount=11, coins=[1,5,6,9]):', coinChange([1,5,6,9], 11));
console.log('Knapsack(cap=7):', knapsack([1,3,4,5],[1,4,5,7], 7));

// Strings
console.log('\n--- String Algorithms ---');
console.log('isPalindrome("racecar"):', isPalindrome('racecar'));
console.log('isPalindrome("A man a plan a canal Panama"):', isPalindrome('A man a plan a canal Panama'));
console.log('isAnagram("listen","silent"):', isAnagram('listen', 'silent'));
console.log('longestSubstring("abcabcbb"):', longestSubstringNoRepeat('abcabcbb'));

console.log('\n✅ All algorithm demos complete!');
