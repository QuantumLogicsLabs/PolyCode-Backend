/*
 * File: process_info.c
 * Description: System process information utilities
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <time.h>
#include <string.h>

// Get current process ID
void print_process_info() {
    printf("=== Process Information ===\n");
    printf("Process ID (PID): %d\n", getpid());
    printf("Parent Process ID (PPID): %d\n", getppid());
    printf("User ID (UID): %d\n", getuid());
    printf("Group ID (GID): %d\n", getgid());
    printf("Effective User ID (EUID): %d\n", geteuid());
    printf("Effective Group ID (EGID): %d\n", getegid());
}

// Fork process example
void fork_example() {
    printf("\n=== Fork Example ===\n");
    
    pid_t pid = fork();
    
    if (pid < 0) {
        perror("Fork failed");
        exit(EXIT_FAILURE);
    } else if (pid == 0) {
        // Child process
        printf("Child process: PID = %d, Parent PID = %d\n", getpid(), getppid());
        sleep(2);
        printf("Child process: Exiting\n");
        exit(EXIT_SUCCESS);
    } else {
        // Parent process
        printf("Parent process: PID = %d, Child PID = %d\n", getpid(), pid);
        
        int status;
        wait(&status);
        printf("Parent process: Child exited with status %d\n", WEXITSTATUS(status));
    }
}

// Multiple fork example (process tree)
void process_tree_example() {
    printf("\n=== Process Tree Example ===\n");
    
    for (int i = 0; i < 3; i++) {
        pid_t pid = fork();
        
        if (pid == 0) {
            // Child process
            printf("Child %d: PID = %d, Parent PID = %d\n", i + 1, getpid(), getppid());
            sleep(1);
            exit(EXIT_SUCCESS);
        } else if (pid < 0) {
            perror("Fork failed");
        }
    }
    
    // Parent waits for all children
    for (int i = 0; i < 3; i++) {
        int status;
        wait(&status);
    }
    
    printf("Parent: All children have exited\n");
}

// Exec example
void exec_example() {
    printf("\n=== Exec Example ===\n");
    
    pid_t pid = fork();
    
    if (pid == 0) {
        // Child process executes 'ls -l'
        printf("Child: Executing 'ls -l'\n");
        char* args[] = {"ls", "-l", NULL};
        execvp("ls", args);
        
        // If exec returns, an error occurred
        perror("Exec failed");
        exit(EXIT_FAILURE);
    } else if (pid > 0) {
        // Parent waits for child
        int status;
        wait(&status);
        printf("Parent: Child completed\n");
    }
}

// Pipe example (inter-process communication)
void pipe_example() {
    printf("\n=== Pipe Example ===\n");
    
    int pipefd[2];
    char buffer[256];
    
    if (pipe(pipefd) == -1) {
        perror("Pipe failed");
        return;
    }
    
    pid_t pid = fork();
    
    if (pid == 0) {
        // Child process - write to pipe
        close(pipefd[0]); // Close read end
        
        const char* message = "Hello from child process!";
        write(pipefd[1], message, strlen(message));
        printf("Child: Sent message: %s\n", message);
        
        close(pipefd[1]);
        exit(EXIT_SUCCESS);
    } else if (pid > 0) {
        // Parent process - read from pipe
        close(pipefd[1]); // Close write end
        
        ssize_t bytes_read = read(pipefd[0], buffer, sizeof(buffer) - 1);
        buffer[bytes_read] = '\0';
        
        printf("Parent: Received message: %s\n", buffer);
        
        close(pipefd[0]);
        wait(NULL);
    }
}

// Environment variables
void environment_example() {
    printf("\n=== Environment Variables ===\n");
    
    // Print all environment variables
    extern char** environ;
    for (char** env = environ; *env != NULL; env++) {
        printf("%s\n", *env);
    }
    
    // Get specific environment variable
    char* path = getenv("PATH");
    if (path) {
        printf("\nPATH: %s\n", path);
    }
    
    // Set environment variable
    setenv("CUSTOM_VAR", "Hello from C", 1);
    char* custom = getenv("CUSTOM_VAR");
    printf("CUSTOM_VAR: %s\n", custom);
    
    // Unset environment variable
    unsetenv("CUSTOM_VAR");
    custom = getenv("CUSTOM_VAR");
    printf("CUSTOM_VAR after unset: %s\n", custom ? custom : "NULL");
}

// System information
void system_info() {
    printf("\n=== System Information ===\n");
    
    // Get system limits
    long max_open_files = sysconf(_SC_OPEN_MAX);
    printf("Maximum open files: %ld\n", max_open_files);
    
    long clock_ticks = sysconf(_SC_CLK_TCK);
    printf("Clock ticks per second: %ld\n", clock_ticks);
    
    long max_path = pathconf(".", _PC_PATH_MAX);
    printf("Maximum path length: %ld\n", max_path);
    
    // Get hostname
    char hostname[256];
    if (gethostname(hostname, sizeof(hostname)) == 0) {
        printf("Hostname: %s\n", hostname);
    }
    
    // Get current working directory
    char cwd[1024];
    if (getcwd(cwd, sizeof(cwd)) != NULL) {
        printf("Current working directory: %s\n", cwd);
    }
}

// Process timing
void process_timing() {
    printf("\n=== Process Timing ===\n");
    
    clock_t start = clock();
    
    // Do some work
    volatile long sum = 0;
    for (long i = 0; i < 1000000; i++) {
        sum += i;
    }
    
    clock_t end = clock();
    double cpu_time = ((double)(end - start)) / CLOCKS_PER_SEC;
    
    printf("CPU time used: %.6f seconds\n", cpu_time);
    printf("Sum result: %ld\n", sum);
    
    // Real time using time()
    time_t real_start = time(NULL);
    sleep(2);
    time_t real_end = time(NULL);
    
    printf("Real time elapsed: %ld seconds\n", real_end - real_start);
}

// Signal handling example
#include <signal.h>

volatile sig_atomic_t signal_received = 0;

void signal_handler(int signum) {
    signal_received = signum;
    printf("Received signal %d\n", signum);
}

void signal_example() {
    printf("\n=== Signal Handling Example ===\n");
    printf("Send SIGINT (Ctrl+C) to test signal handling...\n");
    
    // Set up signal handler
    signal(SIGINT, signal_handler);
    signal(SIGTERM, signal_handler);
    
    // Wait for signal
    while (!signal_received) {
        printf("Waiting for signal... (PID: %d)\n", getpid());
        sleep(1);
        
        if (signal_received) {
            break;
        }
    }
    
    printf("Signal handling completed\n");
}

int main() {
    print_process_info();
    fork_example();
    process_tree_example();
    exec_example();
    pipe_example();
    environment_example();
    system_info();
    process_timing();
    
    // Uncomment to test signal handling
    // signal_example();
    
    printf("\n=== System programming examples completed ===\n");
    
    return 0;
}
