#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>

#define MAX_VERTICES 100

// Graph structure using adjacency matrix
typedef struct {
    int numVertices;
    bool adjMatrix[MAX_VERTICES][MAX_VERTICES];
} Graph;

// Initialize graph
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

// Add edge to graph
void addEdge(Graph* graph, int src, int dest) {
    // Add edge from src to dest
    graph->adjMatrix[src][dest] = true;
    // Add edge from dest to src (for undirected graph)
    graph->adjMatrix[dest][src] = true;
}

// DFS utility function
void DFSUtil(Graph* graph, int vertex, bool visited[]) {
    visited[vertex] = true;
    printf("%d ", vertex);
    
    // Recur for all adjacent vertices
    for (int i = 0; i < graph->numVertices; i++) {
        if (graph->adjMatrix[vertex][i] && !visited[i]) {
            DFSUtil(graph, i, visited);
        }
    }
}

// Depth First Search traversal
void DFS(Graph* graph, int startVertex) {
    bool visited[MAX_VERTICES] = {false};
    
    printf("DFS traversal starting from vertex %d: ", startVertex);
    DFSUtil(graph, startVertex, visited);
    printf("\n");
}

// Check if graph is connected using DFS
bool isConnected(Graph* graph) {
    bool visited[MAX_VERTICES] = {false};
    
    // Find a vertex with non-zero degree
    int startVertex = 0;
    while (startVertex < graph->numVertices) {
        bool hasEdge = false;
        for (int i = 0; i < graph->numVertices; i++) {
            if (graph->adjMatrix[startVertex][i]) {
                hasEdge = true;
                break;
            }
        }
        if (hasEdge) break;
        startVertex++;
    }
    
    // If no edges found, graph is connected (trivially)
    if (startVertex >= graph->numVertices) {
        return true;
    }
    
    // Perform DFS from startVertex
    DFSUtil(graph, startVertex, visited);
    
    // Check if all vertices with non-zero degree are visited
    for (int i = 0; i < graph->numVertices; i++) {
        bool hasEdge = false;
        for (int j = 0; j < graph->numVertices; j++) {
            if (graph->adjMatrix[i][j]) {
                hasEdge = true;
                break;
            }
        }
        if (hasEdge && !visited[i]) {
            return false;
        }
    }
    
    return true;
}

// Count connected components
int countConnectedComponents(Graph* graph) {
    bool visited[MAX_VERTICES] = {false};
    int count = 0;
    
    for (int i = 0; i < graph->numVertices; i++) {
        if (!visited[i]) {
            count++;
            printf("Component %d: ", count);
            DFSUtil(graph, i, visited);
            printf("\n");
        }
    }
    
    return count;
}

// Free graph memory
void freeGraph(Graph* graph) {
    free(graph);
}

int main() {
    // Create a sample graph
    Graph* graph = createGraph(7);
    
    // Add edges
    addEdge(graph, 0, 1);
    addEdge(graph, 0, 2);
    addEdge(graph, 1, 3);
    addEdge(graph, 2, 4);
    addEdge(graph, 3, 5);
    addEdge(graph, 4, 5);
    addEdge(graph, 5, 6);
    
    printf("Graph created with 7 vertices\n");
    
    // Perform DFS from different starting vertices
    DFS(graph, 0);
    DFS(graph, 3);
    DFS(graph, 6);
    
    // Check if graph is connected
    if (isConnected(graph)) {
        printf("\nThe graph is connected\n");
    } else {
        printf("\nThe graph is not connected\n");
    }
    
    // Count connected components
    printf("\nConnected components:\n");
    int components = countConnectedComponents(graph);
    printf("Total connected components: %d\n", components);
    
    freeGraph(graph);
    
    return 0;
}
