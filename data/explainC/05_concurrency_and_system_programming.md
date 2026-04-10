# Concurrency and Systems Programming in C

## POSIX Threads (pthreads)

`pthreads` is the standard threading API on POSIX systems (Linux, macOS).

```bash
gcc -o program program.c -lpthread
```

### Creating and Joining Threads

```c
#include <pthread.h>
#include <stdio.h>
#include <stdlib.h>

typedef struct {
    int thread_id;
    int start;
    int end;
    long result;
} WorkArgs;

void *sum_range(void *arg) {
    WorkArgs *a = (WorkArgs *)arg;
    a->result = 0;
    for (int i = a->start; i <= a->end; i++)
        a->result += i;
    return NULL;
}

int main(void) {
    pthread_t t1, t2;
    WorkArgs a1 = {0, 1, 500000, 0};
    WorkArgs a2 = {1, 500001, 1000000, 0};

    pthread_create(&t1, NULL, sum_range, &a1);
    pthread_create(&t2, NULL, sum_range, &a2);

    pthread_join(t1, NULL);
    pthread_join(t2, NULL);

    printf("Total: %ld\n", a1.result + a2.result);
    return 0;
}
```

---

## Mutexes — Protecting Shared State

A **race condition** occurs when multiple threads access shared data without synchronization.

```c
#include <pthread.h>

typedef struct {
    long count;
    pthread_mutex_t lock;
} Counter;

void counter_init(Counter *c) {
    c->count = 0;
    pthread_mutex_init(&c->lock, NULL);
}

void counter_inc(Counter *c) {
    pthread_mutex_lock(&c->lock);
    c->count++;                         // critical section
    pthread_mutex_unlock(&c->lock);
}

long counter_get(Counter *c) {
    pthread_mutex_lock(&c->lock);
    long val = c->count;
    pthread_mutex_unlock(&c->lock);
    return val;
}

void counter_destroy(Counter *c) {
    pthread_mutex_destroy(&c->lock);
}
```

### Deadlock

Deadlock happens when two threads each hold a lock the other needs.

```c
// DEADLOCK SCENARIO:
// Thread 1: locks A, then tries to lock B
// Thread 2: locks B, then tries to lock A

// Prevention: always lock in the same order across all threads
// Or use pthread_mutex_trylock to back off on failure
```

---

## Condition Variables

Condition variables let threads sleep until a condition becomes true.

```c
// Producer-Consumer Queue
#define QUEUE_SIZE 16

typedef struct {
    int buf[QUEUE_SIZE];
    int head, tail, count;
    pthread_mutex_t lock;
    pthread_cond_t  not_empty;
    pthread_cond_t  not_full;
} BoundedQueue;

void bq_init(BoundedQueue *q) {
    q->head = q->tail = q->count = 0;
    pthread_mutex_init(&q->lock, NULL);
    pthread_cond_init(&q->not_empty, NULL);
    pthread_cond_init(&q->not_full, NULL);
}

void bq_push(BoundedQueue *q, int val) {
    pthread_mutex_lock(&q->lock);
    while (q->count == QUEUE_SIZE)
        pthread_cond_wait(&q->not_full, &q->lock);
    q->buf[q->tail] = val;
    q->tail = (q->tail + 1) % QUEUE_SIZE;
    q->count++;
    pthread_cond_signal(&q->not_empty);
    pthread_mutex_unlock(&q->lock);
}

int bq_pop(BoundedQueue *q) {
    pthread_mutex_lock(&q->lock);
    while (q->count == 0)
        pthread_cond_wait(&q->not_empty, &q->lock);
    int val = q->buf[q->head];
    q->head = (q->head + 1) % QUEUE_SIZE;
    q->count--;
    pthread_cond_signal(&q->not_full);
    pthread_mutex_unlock(&q->lock);
    return val;
}
```

---

## File I/O at the System Level

### POSIX File Descriptors vs `FILE *`

| Feature | POSIX (`open`/`read`/`write`) | C Standard (`fopen`/`fread`/`fwrite`) |
|---|---|---|
| Buffering | None (manual) | Automatic (stdio buffer) |
| Portability | POSIX only | Standard C |
| Control | Full (flags, permissions) | Limited |
| Performance | Depends on use case | Usually better for text |

### POSIX I/O

```c
#include <fcntl.h>
#include <unistd.h>

int fd = open("data.bin", O_RDWR | O_CREAT | O_TRUNC, 0644);
if (fd < 0) { perror("open"); exit(1); }

char buf[] = "Hello, systems!";
ssize_t written = write(fd, buf, sizeof(buf) - 1);

lseek(fd, 0, SEEK_SET);  // rewind

char rbuf[64] = {0};
ssize_t bytes_read = read(fd, rbuf, sizeof(rbuf) - 1);
printf("Read: %s\n", rbuf);

close(fd);
```

### Memory-Mapped Files (`mmap`)

`mmap` maps a file directly into the process's address space — ideal for large files.

```c
#include <sys/mman.h>
#include <sys/stat.h>
#include <fcntl.h>

int fd = open("large_file.dat", O_RDONLY);
struct stat st;
fstat(fd, &st);

char *data = mmap(NULL, st.st_size, PROT_READ, MAP_PRIVATE, fd, 0);
if (data == MAP_FAILED) { perror("mmap"); exit(1); }

// Access file contents like a regular array
printf("First byte: %c\n", data[0]);
printf("Last byte: %c\n", data[st.st_size - 1]);

munmap(data, st.st_size);
close(fd);
```

---

## Signals

Signals are asynchronous notifications sent to a process.

```c
#include <signal.h>
#include <stdio.h>
#include <unistd.h>

volatile sig_atomic_t running = 1;

void handle_sigint(int sig) {
    (void)sig;
    running = 0;  // safe to set in signal handler
}

int main(void) {
    struct sigaction sa = {0};
    sa.sa_handler = handle_sigint;
    sigemptyset(&sa.sa_mask);
    sigaction(SIGINT, &sa, NULL);  // prefer sigaction over signal()

    printf("Running... press Ctrl+C to stop\n");
    while (running) {
        sleep(1);
    }
    printf("Shutting down cleanly.\n");
    return 0;
}
```

Signal-safe functions (the only ones safe to call from handlers):
`write()`, `_exit()`, `sigaction()`, `sem_post()` — avoid `printf`, `malloc`, `free`.

---

## Atomic Operations (C11)

C11 provides `_Atomic` types for lock-free programming:

```c
#include <stdatomic.h>
#include <pthread.h>

atomic_int counter = ATOMIC_VAR_INIT(0);

void *increment(void *arg) {
    for (int i = 0; i < 1000000; i++)
        atomic_fetch_add(&counter, 1);
    return NULL;
}

// Spinlock using compare-and-swap
typedef struct { atomic_flag flag; } Spinlock;

void spin_lock(Spinlock *s) {
    while (atomic_flag_test_and_set_explicit(
               &s->flag, memory_order_acquire));
}

void spin_unlock(Spinlock *s) {
    atomic_flag_clear_explicit(&s->flag, memory_order_release);
}
```

---

## Process Creation: `fork` and `exec`

```c
#include <unistd.h>
#include <sys/wait.h>

int main(void) {
    pid_t pid = fork();

    if (pid < 0) {
        perror("fork");
        return 1;
    } else if (pid == 0) {
        // Child process
        execlp("ls", "ls", "-l", NULL);
        perror("exec");  // only reached if exec fails
        _exit(1);
    } else {
        // Parent process
        int status;
        waitpid(pid, &status, 0);
        if (WIFEXITED(status))
            printf("Child exited with code %d\n", WEXITSTATUS(status));
    }
    return 0;
}
```

`fork()` creates a copy of the current process. `exec()` replaces the process image with a new program. Together they form the Unix process model.

---

## Socket Programming (TCP Echo Server)

```c
#include <sys/socket.h>
#include <netinet/in.h>
#include <string.h>

int main(void) {
    int server_fd = socket(AF_INET, SOCK_STREAM, 0);

    int opt = 1;
    setsockopt(server_fd, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));

    struct sockaddr_in addr = {
        .sin_family = AF_INET,
        .sin_addr.s_addr = INADDR_ANY,
        .sin_port = htons(8080)
    };

    bind(server_fd, (struct sockaddr *)&addr, sizeof(addr));
    listen(server_fd, 10);

    printf("Listening on port 8080\n");

    while (1) {
        int client_fd = accept(server_fd, NULL, NULL);
        char buf[1024];
        ssize_t n;
        while ((n = recv(client_fd, buf, sizeof(buf), 0)) > 0)
            send(client_fd, buf, n, 0);  // echo back
        close(client_fd);
    }

    close(server_fd);
    return 0;
}
```
