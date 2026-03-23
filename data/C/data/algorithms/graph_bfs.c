#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>

#define MAX_VERTICES 100
#define MAX_QUEUE_SIZE 100

// Queue structure for BFS
typedef struct {
    int items[MAX_QUEUE_SIZE];
    int front;
    int rear;
} Queue;

// Graph structure using adjacency matrix
typedef struct {
    int numVertices;
    bool adjMatrix[MAX_VERTICES][MAX_VERTICES];
} Graph;

// Queue operations
Queue* createQueue() {
    Queue* q = (Queue*)malloc(sizeof(Queue));
    q->front = -1;
    q->rear = -1;
    return q;
}

bool isEmpty(Queue* q) {
    if (q->rear == -1)
        return true;
    else
        return false;
}

void enqueue(Queue* q, int value) {
    if (q->rear == MAX_QUEUE_SIZE - 1)
        printf("\nQueue is Full!!");
    else {
        if (q->front == -1)
            q->front = 0;
        q->rear++;
        q->items[q->rear] = value;
    }
}

int dequeue(Queue* q) {
    int item;
    if (isEmpty(q)) {
        printf("Queue is empty");
        item = -1;
    } else {
        item = q->items[q->front];
        q->front++;
        if (q->front > q->rear) {
            q->front = q->rear = -1;
        }
    }
    return item;
}

// Graph operations
Graph* createGraph(int vertices) {
    Graph* graph = (Graph*)malloc(sizeof(Graph));
    graph->numVertices = vertices;
    
    for (int i = 0; i < vertices; i++) {
        for (int j = 0; j < vertices; j++) {
            graph->adjMatrix[i][j] = false;
        }
    }
    
    return graph;
}

void addEdge(Graph* graph, int src, int dest) {
    // Add edge from src to dest
    graph->adjMatrix[src][dest] = true;
    // Add edge from dest to src (for undirected graph)
    graph->adjMatrix[dest][src] = true;
}

// BFS traversal
void BFS(Graph* graph, int startVertex) {
    Queue* q = createQueue();
    
    bool visited[MAX_VERTICES] = {false};
    
    visited[startVertex] = true;
    enqueue(q, startVertex);
    
    printf("BFS traversal starting from vertex %d: ", startVertex);
    
    while (!isEmpty(q)) {
        int currentVertex = dequeue(q);
        printf("%d ", currentVertex);
        
        // Visit all adjacent vertices
        for (int i = 0; i < graph->numVertices; i++) {
            if (graph->adjMatrix[currentVertex][i] && !visited[i]) {
                visited[i] = true;
                enqueue(q, i);
            }
        }
    }
    printf("\n");
    
    free(q);
}

// Find shortest path using BFS
void findShortestPath(Graph* graph, int start, int end) {
    Queue* q = createQueue();
    
    bool visited[MAX_VERTICES] = {false};
    int distance[MAX_VERTICES] = {0};
    int parent[MAX_VERTICES];
    
    for (int i = 0; i < graph->numVertices; i++) {
        parent[i] = -1;
    }
    
    visited[start] = true;
    enqueue(q, start);
    
    while (!isEmpty(q)) {
        int current = dequeue(q);
        
        if (current == end) {
            break;
        }
        
        for (int i = 0; i < graph->numVertices; i++) {
            if (graph->adjMatrix[current][i] && !visited[i]) {
                visited[i] = true;
                distance[i] = distance[current] + 1;
                parent[i] = current;
                enqueue(q, i);
            }
        }
    }
    
    if (visited[end]) {
        printf("Shortest path from %d to %d: ", start, end);
        
        // Reconstruct path
        int path[MAX_VERTICES];
        int pathLength = 0;
        int current = end;
        
        while (current != -1) {
            path[pathLength++] = current;
            current = parent[current];
        }
        
        // Print path in reverse order
        for (int i = pathLength - 1; i >= 0; i--) {
            printf("%d", path[i]);
            if (i > 0) printf(" -> ");
        }
        
        printf("\nPath length: %d\n", distance[end]);
    } else {
        printf("No path found from %d to %d\n", start, end);
    }
    
    free(q);
}

// Check if graph is bipartite using BFS
bool isBipartite(Graph* graph) {
    int colors[MAX_VERTICES];
    for (int i = 0; i < graph->numVertices; i++) {
        colors[i] = -1; // -1 means no color assigned
    }
    
    Queue* q = createQueue();
    
    for (int i = 0; i < graph->numVertices; i++) {
        if (colors[i] == -1) {
            colors[i] = 0;
            enqueue(q, i);
            
            while (!isEmpty(q)) {
                int current = dequeue(q);
                
                for (int neighbor = 0; neighbor < graph->numVertices; neighbor++) {
                    if (graph->adjMatrix[current][neighbor]) {
                        if (colors[neighbor] == -1) {
                            colors[neighbor] = 1 - colors[current];
                            enqueue(q, neighbor);
                        } else if (colors[neighbor] == colors[current]) {
                            free(q);
                            return false;
                        }
                    }
                }
            }
        }
    }
    
    free(q);
    return true;
}

void freeGraph(Graph* graph) {
    free(graph);
}

int main() {
    // Create a sample graph
    Graph* graph = createGraph(8);
    
    // Add edges to create a connected graph
    addEdge(graph, 0, 1);
    addEdge(graph, 0, 2);
    addEdge(graph, 1, 3);
    addEdge(graph, 1, 4);
    addEdge(graph, 2, 5);
    addEdge(graph, 2, 6);
    addEdge(graph, 3, 7);
    addEdge(graph, 4, 7);
    addEdge(graph, 5, 7);
    addEdge(graph, 6, 7);
    
    printf("Graph created with 8 vertices\n\n");
    
    // Perform BFS from different starting vertices
    BFS(graph, 0);
    BFS(graph, 3);
    BFS(graph, 7);
    
    // Find shortest paths
    printf("\nShortest paths:\n");
    findShortestPath(graph, 0, 7);
    findShortestPath(graph, 1, 6);
    findShortestPath(graph, 2, 4);
    
    // Check if graph is bipartite
    printf("\nGraph bipartite check:\n");
    if (isBipartite(graph)) {
        printf("The graph is bipartite\n");
    } else {
        printf("The graph is not bipartite\n");
    }
    
    freeGraph(graph);
    
    return 0;
}
