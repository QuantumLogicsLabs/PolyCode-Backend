# C++ Algorithms

A practical guide to the most important algorithms implemented in C++, with real-world applications and complexity analysis.

---

## Big-O Complexity Cheat Sheet

| Algorithm | Best | Average | Worst | Space |
|---|---|---|---|---|
| Binary Search | O(1) | O(log n) | O(log n) | O(1) |
| Bubble Sort | O(n) | O(n²) | O(n²) | O(1) |
| Selection Sort | O(n²) | O(n²) | O(n²) | O(1) |
| Insertion Sort | O(n) | O(n²) | O(n²) | O(1) |
| Merge Sort | O(n log n) | O(n log n) | O(n log n) | O(n) |
| Quick Sort | O(n log n) | O(n log n) | O(n²) | O(log n) |
| BFS / DFS | O(V+E) | O(V+E) | O(V+E) | O(V) |
| Dijkstra | O(V²) | O(V²) | O(V²) | O(V) |

---

## 1. Searching Algorithms

### Linear Search
```cpp
int linearSearch(const std::vector<int>& arr, int target) {
    for (int i = 0; i < arr.size(); i++) {
        if (arr[i] == target) return i;
    }
    return -1;
}
```

### Binary Search
Requires a **sorted** array. Halves the search space each step — O(log n).

```cpp
int binarySearch(const std::vector<int>& arr, int target) {
    int left = 0, right = arr.size() - 1;
    while (left <= right) {
        int mid = left + (right - left) / 2;
        if (arr[mid] == target) return mid;
        else if (arr[mid] < target) left = mid + 1;
        else right = mid - 1;
    }
    return -1;
}
```

---

## 2. Sorting Algorithms

### Bubble Sort — O(n²)
```cpp
void bubbleSort(std::vector<int>& arr) {
    int n = arr.size();
    for (int i = 0; i < n - 1; i++) {
        bool swapped = false;
        for (int j = 0; j < n - i - 1; j++) {
            if (arr[j] > arr[j + 1]) {
                std::swap(arr[j], arr[j + 1]);
                swapped = true;
            }
        }
        if (!swapped) break; // already sorted
    }
}
```

### Merge Sort — O(n log n)
```cpp
void merge(std::vector<int>& arr, int l, int m, int r) {
    std::vector<int> left(arr.begin() + l, arr.begin() + m + 1);
    std::vector<int> right(arr.begin() + m + 1, arr.begin() + r + 1);
    int i = 0, j = 0, k = l;
    while (i < left.size() && j < right.size())
        arr[k++] = (left[i] <= right[j]) ? left[i++] : right[j++];
    while (i < left.size()) arr[k++] = left[i++];
    while (j < right.size()) arr[k++] = right[j++];
}

void mergeSort(std::vector<int>& arr, int l, int r) {
    if (l >= r) return;
    int m = l + (r - l) / 2;
    mergeSort(arr, l, m);
    mergeSort(arr, m + 1, r);
    merge(arr, l, m, r);
}
```

### Quick Sort — O(n log n) average
```cpp
int partition(std::vector<int>& arr, int low, int high) {
    int pivot = arr[high];
    int i = low - 1;
    for (int j = low; j < high; j++) {
        if (arr[j] <= pivot) std::swap(arr[++i], arr[j]);
    }
    std::swap(arr[i + 1], arr[high]);
    return i + 1;
}

void quickSort(std::vector<int>& arr, int low, int high) {
    if (low < high) {
        int pi = partition(arr, low, high);
        quickSort(arr, low, pi - 1);
        quickSort(arr, pi + 1, high);
    }
}
```

---

## 3. Graph Algorithms

### Breadth-First Search (BFS)
```cpp
void bfs(const std::vector<std::vector<int>>& adj, int start, int n) {
    std::vector<bool> visited(n, false);
    std::queue<int> q;
    visited[start] = true;
    q.push(start);
    while (!q.empty()) {
        int v = q.front(); q.pop();
        std::cout << v << " ";
        for (int u : adj[v]) {
            if (!visited[u]) { visited[u] = true; q.push(u); }
        }
    }
}
```

### Depth-First Search (DFS)
```cpp
void dfs(const std::vector<std::vector<int>>& adj, int v, std::vector<bool>& visited) {
    visited[v] = true;
    std::cout << v << " ";
    for (int u : adj[v])
        if (!visited[u]) dfs(adj, u, visited);
}
```

### Dijkstra's Shortest Path
```cpp
std::vector<int> dijkstra(const std::vector<std::vector<std::pair<int,int>>>& adj, int src, int n) {
    std::vector<int> dist(n, INT_MAX);
    std::priority_queue<std::pair<int,int>, std::vector<std::pair<int,int>>, std::greater<>> pq;
    dist[src] = 0;
    pq.push({0, src});
    while (!pq.empty()) {
        auto [d, u] = pq.top(); pq.pop();
        if (d > dist[u]) continue;
        for (auto [w, v] : adj[u]) {
            if (dist[u] + w < dist[v]) {
                dist[v] = dist[u] + w;
                pq.push({dist[v], v});
            }
        }
    }
    return dist;
}
```

---

## 4. Dynamic Programming

### Fibonacci (Memoization)
```cpp
std::unordered_map<int, long long> memo;
long long fib(int n) {
    if (n <= 1) return n;
    if (memo.count(n)) return memo[n];
    return memo[n] = fib(n - 1) + fib(n - 2);
}
```

### 0/1 Knapsack
```cpp
int knapsack(int W, const std::vector<int>& wt, const std::vector<int>& val, int n) {
    std::vector<std::vector<int>> dp(n + 1, std::vector<int>(W + 1, 0));
    for (int i = 1; i <= n; i++)
        for (int w = 0; w <= W; w++) {
            dp[i][w] = dp[i-1][w];
            if (wt[i-1] <= w)
                dp[i][w] = std::max(dp[i][w], dp[i-1][w - wt[i-1]] + val[i-1]);
        }
    return dp[n][W];
}
```

### Longest Common Subsequence (LCS)
```cpp
int lcs(const std::string& a, const std::string& b) {
    int m = a.size(), n = b.size();
    std::vector<std::vector<int>> dp(m + 1, std::vector<int>(n + 1, 0));
    for (int i = 1; i <= m; i++)
        for (int j = 1; j <= n; j++)
            dp[i][j] = (a[i-1] == b[j-1]) ? dp[i-1][j-1] + 1
                                            : std::max(dp[i-1][j], dp[i][j-1]);
    return dp[m][n];
}
```

---

## Summary

| Category | Use When |
|---|---|
| **Binary Search** | Sorted array, need fast lookup |
| **Merge Sort** | Need stable, guaranteed O(n log n) |
| **Quick Sort** | General purpose, good cache performance |
| **BFS** | Shortest path in unweighted graph |
| **Dijkstra** | Shortest path with positive weights |
| **DP** | Overlapping subproblems, optimal substructure |

> 💡 **Rule of thumb**: If n ≤ 10⁶ use O(n log n). If n ≤ 10⁴ you can use O(n²). Never use O(n²) on n > 10⁵.
