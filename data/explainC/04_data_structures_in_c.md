# Implementing Data Structures in C

## Linked List

A singly linked list with full insert, delete, and search operations.

```c
#include <stdio.h>
#include <stdlib.h>

typedef struct Node {
    int data;
    struct Node *next;
} Node;

typedef struct {
    Node *head;
    size_t size;
} LinkedList;

void list_init(LinkedList *list) {
    list->head = NULL;
    list->size = 0;
}

// Prepend — O(1)
void list_prepend(LinkedList *list, int val) {
    Node *n = malloc(sizeof(Node));
    if (!n) return;
    n->data = val;
    n->next = list->head;
    list->head = n;
    list->size++;
}

// Append — O(n)
void list_append(LinkedList *list, int val) {
    Node *n = malloc(sizeof(Node));
    if (!n) return;
    n->data = val;
    n->next = NULL;
    if (!list->head) { list->head = n; }
    else {
        Node *cur = list->head;
        while (cur->next) cur = cur->next;
        cur->next = n;
    }
    list->size++;
}

// Delete by value — O(n)
int list_delete(LinkedList *list, int val) {
    Node **cur = &list->head;
    while (*cur) {
        if ((*cur)->data == val) {
            Node *tmp = *cur;
            *cur = (*cur)->next;
            free(tmp);
            list->size--;
            return 1;
        }
        cur = &(*cur)->next;
    }
    return 0;
}

void list_free(LinkedList *list) {
    Node *cur = list->head;
    while (cur) {
        Node *next = cur->next;
        free(cur);
        cur = next;
    }
    list->head = NULL;
    list->size = 0;
}
```

---

## Generic Stack (void pointer)

```c
typedef struct {
    void **data;
    size_t top;
    size_t capacity;
} Stack;

Stack *stack_create(size_t capacity) {
    Stack *s = malloc(sizeof(Stack));
    s->data = malloc(capacity * sizeof(void *));
    s->top = 0;
    s->capacity = capacity;
    return s;
}

int stack_push(Stack *s, void *item) {
    if (s->top == s->capacity) {
        s->capacity *= 2;
        void **tmp = realloc(s->data, s->capacity * sizeof(void *));
        if (!tmp) return 0;
        s->data = tmp;
    }
    s->data[s->top++] = item;
    return 1;
}

void *stack_pop(Stack *s) {
    if (s->top == 0) return NULL;
    return s->data[--s->top];
}

void *stack_peek(const Stack *s) {
    if (s->top == 0) return NULL;
    return s->data[s->top - 1];
}

void stack_destroy(Stack *s) {
    free(s->data);
    free(s);
}
```

---

## Hash Map (Open Addressing)

```c
#define HM_CAPACITY 64
#define HM_LOAD     0.75

typedef struct {
    char *key;
    int   value;
    int   occupied;
} HMEntry;

typedef struct {
    HMEntry *entries;
    size_t   capacity;
    size_t   count;
} HashMap;

static size_t hash(const char *key, size_t cap) {
    size_t h = 2166136261u;
    while (*key) {
        h ^= (unsigned char)*key++;
        h *= 16777619;
    }
    return h % cap;
}

HashMap *hm_create(void) {
    HashMap *hm = malloc(sizeof(HashMap));
    hm->capacity = HM_CAPACITY;
    hm->count = 0;
    hm->entries = calloc(hm->capacity, sizeof(HMEntry));
    return hm;
}

void hm_set(HashMap *hm, const char *key, int value) {
    size_t idx = hash(key, hm->capacity);
    // Linear probing
    while (hm->entries[idx].occupied &&
           strcmp(hm->entries[idx].key, key) != 0) {
        idx = (idx + 1) % hm->capacity;
    }
    if (!hm->entries[idx].occupied) {
        hm->entries[idx].key = strdup(key);
        hm->count++;
    }
    hm->entries[idx].value = value;
    hm->entries[idx].occupied = 1;
}

int hm_get(HashMap *hm, const char *key, int *out) {
    size_t idx = hash(key, hm->capacity);
    while (hm->entries[idx].occupied) {
        if (strcmp(hm->entries[idx].key, key) == 0) {
            *out = hm->entries[idx].value;
            return 1;
        }
        idx = (idx + 1) % hm->capacity;
    }
    return 0;
}

void hm_destroy(HashMap *hm) {
    for (size_t i = 0; i < hm->capacity; i++)
        if (hm->entries[i].occupied) free(hm->entries[i].key);
    free(hm->entries);
    free(hm);
}
```

---

## Binary Search Tree

```c
typedef struct BSTNode {
    int data;
    struct BSTNode *left, *right;
} BSTNode;

BSTNode *bst_insert(BSTNode *root, int val) {
    if (!root) {
        BSTNode *n = malloc(sizeof(BSTNode));
        n->data = val;
        n->left = n->right = NULL;
        return n;
    }
    if (val < root->data)
        root->left  = bst_insert(root->left, val);
    else if (val > root->data)
        root->right = bst_insert(root->right, val);
    return root;
}

int bst_search(BSTNode *root, int val) {
    if (!root) return 0;
    if (val == root->data) return 1;
    return val < root->data
        ? bst_search(root->left, val)
        : bst_search(root->right, val);
}

// In-order traversal — produces sorted output
void bst_inorder(BSTNode *root, void (*visit)(int)) {
    if (!root) return;
    bst_inorder(root->left, visit);
    visit(root->data);
    bst_inorder(root->right, visit);
}

void bst_free(BSTNode *root) {
    if (!root) return;
    bst_free(root->left);
    bst_free(root->right);
    free(root);
}
```

---

## Dynamic Array (like std::vector)

```c
typedef struct {
    int    *data;
    size_t  size;
    size_t  capacity;
} DynArray;

void dyn_init(DynArray *a) {
    a->data = malloc(4 * sizeof(int));
    a->size = 0;
    a->capacity = 4;
}

void dyn_push(DynArray *a, int val) {
    if (a->size == a->capacity) {
        a->capacity *= 2;
        a->data = realloc(a->data, a->capacity * sizeof(int));
    }
    a->data[a->size++] = val;
}

int dyn_get(DynArray *a, size_t i) {
    assert(i < a->size);
    return a->data[i];
}

void dyn_remove(DynArray *a, size_t i) {
    assert(i < a->size);
    memmove(a->data + i, a->data + i + 1,
            (a->size - i - 1) * sizeof(int));
    a->size--;
}

void dyn_free(DynArray *a) {
    free(a->data);
    a->data = NULL;
    a->size = a->capacity = 0;
}
```

---

## Complexity Summary

| Structure | Access | Search | Insert | Delete |
|---|---|---|---|---|
| Dynamic Array | O(1) | O(n) | O(1)* | O(n) |
| Linked List | O(n) | O(n) | O(1) | O(n) |
| Hash Map | — | O(1)* | O(1)* | O(1)* |
| BST (balanced) | — | O(log n) | O(log n) | O(log n) |
| Stack | — | — | O(1) | O(1) |

\* Amortized
