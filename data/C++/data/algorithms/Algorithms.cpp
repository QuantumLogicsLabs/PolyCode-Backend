// ============================================================
//  C++ Algorithms — Complete Implementations
// ============================================================

#include <iostream>
#include <vector>
#include <queue>
#include <unordered_map>
#include <algorithm>
#include <climits>
#include <string>

// ─────────────────────────────────────────────
// SECTION 1: Searching
// ─────────────────────────────────────────────

int linearSearch(const std::vector<int>& arr, int target) {
    for (int i = 0; i < (int)arr.size(); i++)
        if (arr[i] == target) return i;
    return -1;
}

int binarySearch(const std::vector<int>& arr, int target) {
    int left = 0, right = (int)arr.size() - 1;
    while (left <= right) {
        int mid = left + (right - left) / 2;
        if (arr[mid] == target) return mid;
        else if (arr[mid] < target) left = mid + 1;
        else right = mid - 1;
    }
    return -1;
}

// ─────────────────────────────────────────────
// SECTION 2: Sorting
// ─────────────────────────────────────────────

void bubbleSort(std::vector<int>& arr) {
    int n = arr.size();
    for (int i = 0; i < n - 1; i++) {
        bool swapped = false;
        for (int j = 0; j < n - i - 1; j++) {
            if (arr[j] > arr[j + 1]) { std::swap(arr[j], arr[j + 1]); swapped = true; }
        }
        if (!swapped) break;
    }
}

void merge(std::vector<int>& arr, int l, int m, int r) {
    std::vector<int> L(arr.begin() + l, arr.begin() + m + 1);
    std::vector<int> R(arr.begin() + m + 1, arr.begin() + r + 1);
    int i = 0, j = 0, k = l;
    while (i < (int)L.size() && j < (int)R.size())
        arr[k++] = (L[i] <= R[j]) ? L[i++] : R[j++];
    while (i < (int)L.size()) arr[k++] = L[i++];
    while (j < (int)R.size()) arr[k++] = R[j++];
}

void mergeSort(std::vector<int>& arr, int l, int r) {
    if (l >= r) return;
    int m = l + (r - l) / 2;
    mergeSort(arr, l, m);
    mergeSort(arr, m + 1, r);
    merge(arr, l, m, r);
}

int partition(std::vector<int>& arr, int low, int high) {
    int pivot = arr[high], i = low - 1;
    for (int j = low; j < high; j++)
        if (arr[j] <= pivot) std::swap(arr[++i], arr[j]);
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

// ─────────────────────────────────────────────
// SECTION 3: Graph Algorithms
// ─────────────────────────────────────────────

void bfs(const std::vector<std::vector<int>>& adj, int start, int n) {
    std::vector<bool> visited(n, false);
    std::queue<int> q;
    visited[start] = true;
    q.push(start);
    std::cout << "BFS: ";
    while (!q.empty()) {
        int v = q.front(); q.pop();
        std::cout << v << " ";
        for (int u : adj[v])
            if (!visited[u]) { visited[u] = true; q.push(u); }
    }
    std::cout << "\n";
}

void dfs(const std::vector<std::vector<int>>& adj, int v, std::vector<bool>& visited) {
    visited[v] = true;
    std::cout << v << " ";
    for (int u : adj[v])
        if (!visited[u]) dfs(adj, u, visited);
}

std::vector<int> dijkstra(const std::vector<std::vector<std::pair<int,int>>>& adj, int src, int n) {
    std::vector<int> dist(n, INT_MAX);
    std::priority_queue<std::pair<int,int>, std::vector<std::pair<int,int>>, std::greater<>> pq;
    dist[src] = 0;
    pq.push({0, src});
    while (!pq.empty()) {
        auto [d, u] = pq.top(); pq.pop();
        if (d > dist[u]) continue;
        for (auto [w, v] : adj[u]) {
            if (dist[u] + w < dist[v]) { dist[v] = dist[u] + w; pq.push({dist[v], v}); }
        }
    }
    return dist;
}

// ─────────────────────────────────────────────
// SECTION 4: Dynamic Programming
// ─────────────────────────────────────────────

std::unordered_map<int, long long> memo;
long long fib(int n) {
    if (n <= 1) return n;
    if (memo.count(n)) return memo[n];
    return memo[n] = fib(n - 1) + fib(n - 2);
}

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

int lcs(const std::string& a, const std::string& b) {
    int m = a.size(), n = b.size();
    std::vector<std::vector<int>> dp(m + 1, std::vector<int>(n + 1, 0));
    for (int i = 1; i <= m; i++)
        for (int j = 1; j <= n; j++)
            dp[i][j] = (a[i-1] == b[j-1]) ? dp[i-1][j-1] + 1
                                            : std::max(dp[i-1][j], dp[i][j-1]);
    return dp[m][n];
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

void printVec(const std::vector<int>& v) {
    for (int x : v) std::cout << x << " ";
    std::cout << "\n";
}

int main() {
    std::cout << "===== C++ Algorithms Demo =====\n\n";

    // --- Searching ---
    std::cout << "--- Searching ---\n";
    std::vector<int> arr = {2, 5, 8, 12, 16, 23, 38, 56, 72, 91};
    std::cout << "Array: "; printVec(arr);
    std::cout << "Linear search for 23: index " << linearSearch(arr, 23) << "\n";
    std::cout << "Binary search for 56: index " << binarySearch(arr, 56) << "\n";
    std::cout << "Binary search for 99: index " << binarySearch(arr, 99) << "\n";

    // --- Sorting ---
    std::cout << "\n--- Sorting ---\n";
    std::vector<int> data = {64, 34, 25, 12, 22, 11, 90};
    std::cout << "Original: "; printVec(data);

    std::vector<int> b1 = data;
    bubbleSort(b1); std::cout << "Bubble Sort: "; printVec(b1);

    std::vector<int> b2 = data;
    mergeSort(b2, 0, b2.size() - 1); std::cout << "Merge Sort:  "; printVec(b2);

    std::vector<int> b3 = data;
    quickSort(b3, 0, b3.size() - 1); std::cout << "Quick Sort:  "; printVec(b3);

    // --- Graph ---
    std::cout << "\n--- Graph BFS & DFS ---\n";
    int n = 6;
    std::vector<std::vector<int>> adj(n);
    auto addEdge = [&](int u, int v){ adj[u].push_back(v); adj[v].push_back(u); };
    addEdge(0,1); addEdge(0,2); addEdge(1,3); addEdge(2,4); addEdge(3,5);
    bfs(adj, 0, n);
    std::vector<bool> vis(n, false);
    std::cout << "DFS: ";
    dfs(adj, 0, vis);
    std::cout << "\n";

    // --- Dijkstra ---
    std::cout << "\n--- Dijkstra Shortest Path ---\n";
    int nodes = 5;
    std::vector<std::vector<std::pair<int,int>>> wadjc(nodes);
    auto addW = [&](int u, int v, int w){ wadjc[u].push_back({w,v}); wadjc[v].push_back({w,u}); };
    addW(0,1,10); addW(0,2,3); addW(1,3,2); addW(2,1,4); addW(2,3,8); addW(2,4,2); addW(3,4,5);
    auto dist = dijkstra(wadjc, 0, nodes);
    std::cout << "Distances from node 0: ";
    for (int d : dist) std::cout << d << " "; std::cout << "\n";

    // --- Dynamic Programming ---
    std::cout << "\n--- Dynamic Programming ---\n";
    for (int i = 0; i <= 10; i++) std::cout << "fib(" << i << ")=" << fib(i) << "  ";
    std::cout << "\n";

    std::vector<int> wt = {1, 3, 4, 5};
    std::vector<int> val = {1, 4, 5, 7};
    std::cout << "Knapsack (W=7): max value = " << knapsack(7, wt, val, 4) << "\n";

    std::cout << "LCS(\"ABCBDAB\", \"BDCAB\") = " << lcs("ABCBDAB", "BDCAB") << "\n";

    std::cout << "\n✅ All algorithm demos complete!\n";
    return 0;
}
