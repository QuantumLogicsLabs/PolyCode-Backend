/*
 * File: data_structure_problems.c
 * Description: Collection of data structure implementation exercises
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <limits.h>

// ============================================================================
// EXERCISE 1: Linked List Implementation
// ============================================================================

typedef struct Node {
    int data;
    struct Node* next;
} Node;

typedef struct {
    Node* head;
    int size;
} LinkedList;

LinkedList* createLinkedList() {
    LinkedList* list = (LinkedList*)malloc(sizeof(LinkedList));
    if (list != NULL) {
        list->head = NULL;
        list->size = 0;
    }
    return list;
}

void insertAtBeginning(LinkedList* list, int data) {
    Node* newNode = (Node*)malloc(sizeof(Node));
    if (newNode == NULL) return;
    
    newNode->data = data;
    newNode->next = list->head;
    list->head = newNode;
    list->size++;
}

void insertAtEnd(LinkedList* list, int data) {
    Node* newNode = (Node*)malloc(sizeof(Node));
    if (newNode == NULL) return;
    
    newNode->data = data;
    newNode->next = NULL;
    
    if (list->head == NULL) {
        list->head = newNode;
    } else {
        Node* current = list->head;
        while (current->next != NULL) {
            current = current->next;
        }
        current->next = newNode;
    }
    list->size++;
}

void deleteNode(LinkedList* list, int data) {
    if (list->head == NULL) return;
    
    // Delete head if it matches
    if (list->head->data == data) {
        Node* temp = list->head;
        list->head = list->head->next;
        free(temp);
        list->size--;
        return;
    }
    
    // Find and delete node
    Node* current = list->head;
    while (current->next != NULL && current->next->data != data) {
        current = current->next;
    }
    
    if (current->next != NULL) {
        Node* temp = current->next;
        current->next = current->next->next;
        free(temp);
        list->size--;
    }
}

void printLinkedList(LinkedList* list) {
    Node* current = list->head;
    printf("LinkedList (size %d): [", list->size);
    while (current != NULL) {
        printf("%d", current->data);
        if (current->next != NULL) printf(", ");
        current = current->next;
    }
    printf("]\n");
}

void freeLinkedList(LinkedList* list) {
    Node* current = list->head;
    while (current != NULL) {
        Node* temp = current;
        current = current->next;
        free(temp);
    }
    free(list);
}

// ============================================================================
// EXERCISE 2: Stack Implementation using Array
// ============================================================================

#define MAX_STACK_SIZE 100

typedef struct {
    int data[MAX_STACK_SIZE];
    int top;
} ArrayStack;

void initStack(ArrayStack* stack) {
    stack->top = -1;
}

int isStackEmpty(ArrayStack* stack) {
    return stack->top == -1;
}

int isStackFull(ArrayStack* stack) {
    return stack->top == MAX_STACK_SIZE - 1;
}

void push(ArrayStack* stack, int value) {
    if (isStackFull(stack)) {
        printf("Stack overflow!\n");
        return;
    }
    stack->data[++stack->top] = value;
}

int pop(ArrayStack* stack) {
    if (isStackEmpty(stack)) {
        printf("Stack underflow!\n");
        return -1;
    }
    return stack->data[stack->top--];
}

int peek(ArrayStack* stack) {
    if (isStackEmpty(stack)) {
        printf("Stack is empty!\n");
        return -1;
    }
    return stack->data[stack->top];
}

void printStack(ArrayStack* stack) {
    printf("Stack: [");
    for (int i = 0; i <= stack->top; i++) {
        printf("%d", stack->data[i]);
        if (i < stack->top) printf(", ");
    }
    printf("]\n");
}

// ============================================================================
// EXERCISE 3: Queue Implementation using Array
// ============================================================================

#define MAX_QUEUE_SIZE 100

typedef struct {
    int data[MAX_QUEUE_SIZE];
    int front, rear;
} ArrayQueue;

void initQueue(ArrayQueue* queue) {
    queue->front = -1;
    queue->rear = -1;
}

int isQueueEmpty(ArrayQueue* queue) {
    return queue->front == -1;
}

int isQueueFull(ArrayQueue* queue) {
    return (queue->rear + 1) % MAX_QUEUE_SIZE == queue->front;
}

void enqueue(ArrayQueue* queue, int value) {
    if (isQueueFull(queue)) {
        printf("Queue overflow!\n");
        return;
    }
    
    if (isQueueEmpty(queue)) {
        queue->front = 0;
    }
    
    queue->rear = (queue->rear + 1) % MAX_QUEUE_SIZE;
    queue->data[queue->rear] = value;
}

int dequeue(ArrayQueue* queue) {
    if (isQueueEmpty(queue)) {
        printf("Queue underflow!\n");
        return -1;
    }
    
    int value = queue->data[queue->front];
    
    if (queue->front == queue->rear) {
        queue->front = queue->rear = -1;
    } else {
        queue->front = (queue->front + 1) % MAX_QUEUE_SIZE;
    }
    
    return value;
}

int peekQueue(ArrayQueue* queue) {
    if (isQueueEmpty(queue)) {
        printf("Queue is empty!\n");
        return -1;
    }
    return queue->data[queue->front];
}

void printQueue(ArrayQueue* queue) {
    if (isQueueEmpty(queue)) {
        printf("Queue: []\n");
        return;
    }
    
    printf("Queue: [");
    int i = queue->front;
    while (1) {
        printf("%d", queue->data[i]);
        if (i == queue->rear) break;
        printf(", ");
        i = (i + 1) % MAX_QUEUE_SIZE;
    }
    printf("]\n");
}

// ============================================================================
// EXERCISE 4: Binary Tree Implementation
// ============================================================================

typedef struct TreeNode {
    int data;
    struct TreeNode* left;
    struct TreeNode* right;
} TreeNode;

TreeNode* createTreeNode(int data) {
    TreeNode* node = (TreeNode*)malloc(sizeof(TreeNode));
    if (node != NULL) {
        node->data = data;
        node->left = NULL;
        node->right = NULL;
    }
    return node;
}

TreeNode* insertBST(TreeNode* root, int data) {
    if (root == NULL) {
        return createTreeNode(data);
    }
    
    if (data < root->data) {
        root->left = insertBST(root->left, data);
    } else if (data > root->data) {
        root->right = insertBST(root->right, data);
    }
    
    return root;
}

void inorderTraversal(TreeNode* root) {
    if (root != NULL) {
        inorderTraversal(root->left);
        printf("%d ", root->data);
        inorderTraversal(root->right);
    }
}

void preorderTraversal(TreeNode* root) {
    if (root != NULL) {
        printf("%d ", root->data);
        preorderTraversal(root->left);
        preorderTraversal(root->right);
    }
}

void postorderTraversal(TreeNode* root) {
    if (root != NULL) {
        postorderTraversal(root->left);
        postorderTraversal(root->right);
        printf("%d ", root->data);
    }
}

int searchBST(TreeNode* root, int data) {
    if (root == NULL) return 0;
    
    if (root->data == data) return 1;
    if (data < root->data) return searchBST(root->left, data);
    return searchBST(root->right, data);
}

int findMin(TreeNode* root) {
    while (root->left != NULL) {
        root = root->left;
    }
    return root->data;
}

int findMax(TreeNode* root) {
    while (root->right != NULL) {
        root = root->right;
    }
    return root->data;
}

void freeTree(TreeNode* root) {
    if (root != NULL) {
        freeTree(root->left);
        freeTree(root->right);
        free(root);
    }
}

// ============================================================================
// EXERCISE 5: Hash Table Implementation
// ============================================================================

#define HASH_TABLE_SIZE 10

typedef struct HashNode {
    int key;
    int value;
    struct HashNode* next;
} HashNode;

typedef struct {
    HashNode* buckets[HASH_TABLE_SIZE];
} HashTable;

int hashFunction(int key) {
    return key % HASH_TABLE_SIZE;
}

HashTable* createHashTable() {
    HashTable* table = (HashTable*)malloc(sizeof(HashTable));
    if (table != NULL) {
        for (int i = 0; i < HASH_TABLE_SIZE; i++) {
            table->buckets[i] = NULL;
        }
    }
    return table;
}

void insertHash(HashTable* table, int key, int value) {
    int index = hashFunction(key);
    
    HashNode* newNode = (HashNode*)malloc(sizeof(HashNode));
    if (newNode == NULL) return;
    
    newNode->key = key;
    newNode->value = value;
    newNode->next = table->buckets[index];
    table->buckets[index] = newNode;
}

int* getHash(HashTable* table, int key) {
    int index = hashFunction(key);
    
    HashNode* current = table->buckets[index];
    while (current != NULL) {
        if (current->key == key) {
            return &current->value;
        }
        current = current->next;
    }
    
    return NULL;
}

void removeHash(HashTable* table, int key) {
    int index = hashFunction(key);
    
    HashNode* current = table->buckets[index];
    HashNode* prev = NULL;
    
    while (current != NULL && current->key != key) {
        prev = current;
        current = current->next;
    }
    
    if (current == NULL) return; // Key not found
    
    if (prev == NULL) {
        table->buckets[index] = current->next;
    } else {
        prev->next = current->next;
    }
    
    free(current);
}

void printHashTable(HashTable* table) {
    printf("Hash Table:\n");
    for (int i = 0; i < HASH_TABLE_SIZE; i++) {
        printf("Bucket %d: ", i);
        HashNode* current = table->buckets[i];
        while (current != NULL) {
            printf("[%d:%d]", current->key, current->value);
            current = current->next;
            if (current != NULL) printf(" -> ");
        }
        printf("\n");
    }
}

void freeHashTable(HashTable* table) {
    for (int i = 0; i < HASH_TABLE_SIZE; i++) {
        HashNode* current = table->buckets[i];
        while (current != NULL) {
            HashNode* temp = current;
            current = current->next;
            free(temp);
        }
    }
    free(table);
}

// ============================================================================
// TEST FUNCTION
// ============================================================================

void testDataStructures() {
    printf("=== Data Structure Exercises ===\n\n");
    
    // Exercise 1: Linked List
    printf("1. Linked List:\n");
    LinkedList* list = createLinkedList();
    insertAtEnd(list, 10);
    insertAtEnd(list, 20);
    insertAtEnd(list, 30);
    insertAtBeginning(list, 5);
    printLinkedList(list);
    deleteNode(list, 20);
    printLinkedList(list);
    freeLinkedList(list);
    
    // Exercise 2: Stack
    printf("\n2. Stack:\n");
    ArrayStack stack;
    initStack(&stack);
    push(&stack, 10);
    push(&stack, 20);
    push(&stack, 30);
    printStack(&stack);
    printf("Popped: %d\n", pop(&stack));
    printf("Peek: %d\n", peek(&stack));
    printStack(&stack);
    
    // Exercise 3: Queue
    printf("\n3. Queue:\n");
    ArrayQueue queue;
    initQueue(&queue);
    enqueue(&queue, 10);
    enqueue(&queue, 20);
    enqueue(&queue, 30);
    printQueue(&queue);
    printf("Dequeued: %d\n", dequeue(&queue));
    printf("Peek: %d\n", peekQueue(&queue));
    printQueue(&queue);
    
    // Exercise 4: Binary Tree
    printf("\n4. Binary Search Tree:\n");
    TreeNode* root = NULL;
    root = insertBST(root, 50);
    root = insertBST(root, 30);
    root = insertBST(root, 70);
    root = insertBST(root, 20);
    root = insertBST(root, 40);
    root = insertBST(root, 60);
    root = insertBST(root, 80);
    
    printf("Inorder: ");
    inorderTraversal(root);
    printf("\n");
    
    printf("Preorder: ");
    preorderTraversal(root);
    printf("\n");
    
    printf("Postorder: ");
    postorderTraversal(root);
    printf("\n");
    
    printf("Search 40: %s\n", searchBST(root, 40) ? "Found" : "Not found");
    printf("Search 90: %s\n", searchBST(root, 90) ? "Found" : "Not found");
    printf("Min: %d\n", findMin(root));
    printf("Max: %d\n", findMax(root));
    
    freeTree(root);
    
    // Exercise 5: Hash Table
    printf("\n5. Hash Table:\n");
    HashTable* table = createHashTable();
    insertHash(table, 10, 100);
    insertHash(table, 20, 200);
    insertHash(table, 30, 300);
    insertHash(table, 15, 150);
    insertHash(table, 25, 250);
    
    printHashTable(table);
    
    int* value = getHash(table, 20);
    if (value != NULL) {
        printf("Value for key 20: %d\n", *value);
    }
    
    removeHash(table, 15);
    printf("After removing key 15:\n");
    printHashTable(table);
    
    freeHashTable(table);
    
    printf("\n=== All data structure exercises completed ===\n");
}

int main() {
    testDataStructures();
    return 0;
}
