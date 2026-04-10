"""
Linear Search Algorithm
A simple search algorithm that checks each element in sequence.
Time Complexity: O(n)
Space Complexity: O(1)
"""

def linear_search(arr, target):
    """
    Perform linear search on an array.
    
    Args:
        arr (list): List of elements to search through
        target: Element to search for
        
    Returns:
        int: Index of target if found, -1 otherwise
    """
    for i in range(len(arr)):
        if arr[i] == target:
            return i
    return -1

def binary_search(arr, target):
    """
    Perform binary search on a sorted array.
    
    Args:
        arr (list): Sorted list of elements
        target: Element to search for
        
    Returns:
        int: Index of target if found, -1 otherwise
    """
    left, right = 0, len(arr) - 1
    
    while left <= right:
        mid = (left + right) // 2
        
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1
    
    return -1

def jump_search(arr, target):
    """
    Perform jump search on a sorted array.
    
    Args:
        arr (list): Sorted list of elements
        target: Element to search for
        
    Returns:
        int: Index of target if found, -1 otherwise
    """
    import math
    
    n = len(arr)
    step = int(math.sqrt(n))
    prev = 0
    
    # Find the block where element could be present
    while prev < n and arr[min(step, n) - 1] < target:
        prev = step
        step += int(math.sqrt(n))
        if prev >= n:
            return -1
    
    # Linear search in the identified block
    while prev < min(step, n):
        if arr[prev] == target:
            return prev
        prev += 1
    
    return -1

def main():
    """Demonstrate search algorithms."""
    # Test data
    data = [2, 5, 8, 12, 16, 23, 38, 56, 72, 91]
    target = 23
    
    print("Search Algorithms Demonstration")
    print(f"Array: {data}")
    print(f"Target: {target}")
    print()
    
    # Linear Search
    result = linear_search(data, target)
    print(f"Linear Search: Target found at index {result}")
    
    # Binary Search
    result = binary_search(data, target)
    print(f"Binary Search: Target found at index {result}")
    
    # Jump Search
    result = jump_search(data, target)
    print(f"Jump Search: Target found at index {result}")
    
    # Test with non-existent element
    print("\nTesting with non-existent element (45):")
    result = linear_search(data, 45)
    print(f"Linear Search: Target found at index {result}")

if __name__ == "__main__":
    main()
