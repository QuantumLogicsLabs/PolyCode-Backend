# Collections Framework Example

## File Overview
Comprehensive demonstration of Java Collections Framework including Lists, Sets, Maps, Queues, utility classes, custom comparators, and performance comparisons.

## Key Concepts

### Collection Interfaces
- **Collection**: Root interface for collections
- **List**: Ordered collection with duplicates
- **Set**: Unordered collection without duplicates
- **Queue**: FIFO ordering
- **Map**: Key-value pairs (extends Collection indirectly)

### Implementation Classes
- **ArrayList**: Dynamic array, fast random access
- **LinkedList**: Doubly-linked, fast insert/delete
- **HashSet**: Hash-based, O(1) operations
- **TreeSet**: Sorted set, O(log n) operations
- **HashMap**: Hash-based map, O(1) operations
- **TreeMap**: Sorted map, O(log n) operations

### Performance Characteristics
- **ArrayList**: O(1) get, O(n) insert/delete
- **LinkedList**: O(n) get, O(1) insert/delete
- **HashSet**: O(1) add/remove/contains
- **HashMap**: O(1) get/put/remove

### Utility Methods
- **Collections.sort()**: Sorting collections
- **Collections.binarySearch()**: Binary search
- **Collections.shuffle()**: Random ordering
- **Collections.max/min()**: Find extremes

### Customization
- **Comparable**: Natural ordering
- **Comparator**: Custom ordering
- **Custom collections**: Specialized implementations
