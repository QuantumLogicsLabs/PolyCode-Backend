# STL Deep Dive

## Containers

- vector
- map
- set
- unordered_map

---

## Iterators

```cpp
vector<int> v = {1,2,3};
for(auto it = v.begin(); it != v.end(); ++it) {}
```

---

## Algorithms

```cpp
sort(v.begin(), v.end());
```

---

## Complexity

| Container | Insert | Access |
|-----------|--------|--------|
| vector | O(1) | O(1) |
| map | O(log n) | O(log n) |

---

## Key Takeaways

- Prefer STL over manual structures
