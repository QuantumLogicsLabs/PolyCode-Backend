/*
 * File: ping_utility.c
 * Description: Simple ping utility implementation using raw sockets
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netinet/ip.h>
#include <netinet/ip_icmp.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <time.h>
#include <signal.h>

#define PACKET_SIZE 64
#define MAX_WAIT_TIME 5
#define PING_COUNT 4

// ICMP packet structure
typedef struct {
    struct icmphdr hdr;
    char data[PACKET_SIZE - sizeof(struct icmphdr)];
} icmp_packet_t;

// Ping statistics
typedef struct {
    int packets_sent;
    int packets_received;
    double min_rtt;
    double max_rtt;
    double total_rtt;
} ping_stats_t;

// Calculate checksum
unsigned short calculate_checksum(unsigned short *addr, int len) {
    int nleft = len;
    int sum = 0;
    unsigned short *w = addr;
    unsigned short answer = 0;
    
    while (nleft > 1) {
        sum += *w++;
        nleft -= 2;
    }
    
    if (nleft == 1) {
        *(unsigned char*)(&answer) = *(unsigned char*)w;
        sum += answer;
    }
    
    sum = (sum >> 16) + (sum & 0xffff);
    sum += (sum >> 16);
    answer = ~sum;
    
    return answer;
}

// Create ICMP packet
void create_icmp_packet(icmp_packet_t *packet, int seq) {
    // Fill ICMP header
    packet->hdr.type = ICMP_ECHO;
    packet->hdr.code = 0;
    packet->hdr.un.echo.id = htons(getpid() & 0xffff);
    packet->hdr.un.echo.sequence = htons(seq);
    packet->hdr.checksum = 0;
    
    // Fill data
    memset(packet->data, 0, sizeof(packet->data));
    strcpy(packet->data, "Ping from C program");
    
    // Calculate checksum
    packet->hdr.checksum = calculate_checksum((unsigned short*)packet, sizeof(icmp_packet_t));
}

// Parse ICMP reply
int parse_icmp_reply(char *buffer, int size, int expected_seq) {
    struct iphdr *ip_hdr = (struct iphdr*)buffer;
    struct icmphdr *icmp_hdr = (struct icmphdr*)(buffer + (ip_hdr->ihl * 4));
    
    // Check if it's an echo reply
    if (icmp_hdr->type == ICMP_ECHOREPLY) {
        // Check if it's our packet
        if (ntohs(icmp_hdr->un.echo.id) == (getpid() & 0xffff) &&
            ntohs(icmp_hdr->un.echo.sequence) == expected_seq) {
            return 1;
        }
    }
    
    return 0;
}

// Send ping packet
int send_ping(int sock, struct sockaddr_in *dest_addr, int seq) {
    icmp_packet_t packet;
    create_icmp_packet(&packet, seq);
    
    int bytes_sent = sendto(sock, &packet, sizeof(packet), 0,
                           (struct sockaddr*)dest_addr, sizeof(*dest_addr));
    
    return bytes_sent;
}

// Receive ping reply
int receive_ping(int sock, int seq, double *rtt) {
    char buffer[1024];
    struct sockaddr_in from_addr;
    socklen_t from_len = sizeof(from_addr);
    
    struct timeval start_time, end_time;
    gettimeofday(&start_time, NULL);
    
    // Wait for reply with timeout
    fd_set read_fds;
    FD_ZERO(&read_fds);
    FD_SET(sock, &read_fds);
    
    struct timeval timeout;
    timeout.tv_sec = MAX_WAIT_TIME;
    timeout.tv_usec = 0;
    
    int select_result = select(sock + 1, &read_fds, NULL, NULL, &timeout);
    
    if (select_result <= 0) {
        return 0; // Timeout or error
    }
    
    int bytes_received = recvfrom(sock, buffer, sizeof(buffer), 0,
                                 (struct sockaddr*)&from_addr, &from_len);
    
    gettimeofday(&end_time, NULL);
    
    // Calculate round-trip time
    *rtt = (end_time.tv_sec - start_time.tv_sec) * 1000.0 +
           (end_time.tv_usec - start_time.tv_usec) / 1000.0;
    
    if (bytes_received > 0) {
        return parse_icmp_reply(buffer, bytes_received, seq);
    }
    
    return 0;
}

// Resolve hostname
int resolve_hostname(const char *hostname, struct in_addr *addr) {
    struct hostent *host = gethostbyname(hostname);
    
    if (host == NULL) {
        return -1;
    }
    
    *addr = *((struct in_addr*)host->h_addr);
    return 0;
}

// Ping function
int ping_host(const char *hostname, ping_stats_t *stats) {
    int sock;
    struct sockaddr_in dest_addr;
    struct in_addr addr;
    
    // Resolve hostname
    if (resolve_hostname(hostname, &addr) < 0) {
        printf("ping: %s: Name or service not known\n", hostname);
        return -1;
    }
    
    // Create raw socket (requires root privileges)
    sock = socket(AF_INET, SOCK_RAW, IPPROTO_ICMP);
    if (sock < 0) {
        perror("socket");
        printf("ping: %s: Permission denied (try running as root)\n", hostname);
        return -1;
    }
    
    // Set up destination address
    memset(&dest_addr, 0, sizeof(dest_addr));
    dest_addr.sin_family = AF_INET;
    dest_addr.sin_addr = addr;
    
    // Initialize statistics
    stats->packets_sent = 0;
    stats->packets_received = 0;
    stats->min_rtt = 999999.0;
    stats->max_rtt = 0.0;
    stats->total_rtt = 0.0;
    
    printf("PING %s (%s): %d data bytes\n", 
           hostname, inet_ntoa(addr), PACKET_SIZE - sizeof(struct icmphdr));
    
    // Send and receive ping packets
    for (int i = 1; i <= PING_COUNT; i++) {
        // Send ping
        int bytes_sent = send_ping(sock, &dest_addr, i);
        if (bytes_sent > 0) {
            stats->packets_sent++;
        } else {
            perror("send_ping");
            continue;
        }
        
        // Receive reply
        double rtt;
        if (receive_ping(sock, i, &rtt)) {
            stats->packets_received++;
            stats->total_rtt += rtt;
            
            if (rtt < stats->min_rtt) stats->min_rtt = rtt;
            if (rtt > stats->max_rtt) stats->max_rtt = rtt;
            
            printf("%d bytes from %s: icmp_seq=%d ttl=%d time=%.2f ms\n",
                   bytes_sent, hostname, i, 64, rtt);
        } else {
            printf("Request timeout for icmp_seq %d\n", i);
        }
        
        // Wait between pings
        if (i < PING_COUNT) {
            sleep(1);
        }
    }
    
    close(sock);
    return 0;
}

// Print ping statistics
void print_ping_stats(const char *hostname, ping_stats_t *stats) {
    printf("\n--- %s ping statistics ---\n", hostname);
    printf("%d packets transmitted, %d received, %.1f%% packet loss\n",
           stats->packets_sent, stats->packets_received,
           (1.0 - (double)stats->packets_received / stats->packets_sent) * 100);
    
    if (stats->packets_received > 0) {
        double avg_rtt = stats->total_rtt / stats->packets_received;
        printf("rtt min/avg/max = %.2f/%.2f/%.2f ms\n",
               stats->min_rtt, avg_rtt, stats->max_rtt);
    }
}

// Signal handler for Ctrl+C
volatile sig_atomic_t stop_ping = 0;

void signal_handler(int signum) {
    stop_ping = 1;
    printf("\n--- Ping interrupted ---\n");
}

// Continuous ping
int continuous_ping(const char *hostname) {
    ping_stats_t stats;
    int seq = 1;
    
    signal(SIGINT, signal_handler);
    
    int sock;
    struct sockaddr_in dest_addr;
    struct in_addr addr;
    
    if (resolve_hostname(hostname, &addr) < 0) {
        printf("ping: %s: Name or service not known\n", hostname);
        return -1;
    }
    
    sock = socket(AF_INET, SOCK_RAW, IPPROTO_ICMP);
    if (sock < 0) {
        perror("socket");
        printf("ping: %s: Permission denied (try running as root)\n", hostname);
        return -1;
    }
    
    memset(&dest_addr, 0, sizeof(dest_addr));
    dest_addr.sin_family = AF_INET;
    dest_addr.sin_addr = addr;
    
    printf("PING %s (%s): %d data bytes\n", 
           hostname, inet_ntoa(addr), PACKET_SIZE - sizeof(struct icmphdr));
    
    // Initialize statistics
    stats.packets_sent = 0;
    stats.packets_received = 0;
    stats.min_rtt = 999999.0;
    stats.max_rtt = 0.0;
    stats.total_rtt = 0.0;
    
    while (!stop_ping) {
        int bytes_sent = send_ping(sock, &dest_addr, seq);
        if (bytes_sent > 0) {
            stats.packets_sent++;
        } else {
            perror("send_ping");
            continue;
        }
        
        double rtt;
        if (receive_ping(sock, seq, &rtt)) {
            stats.packets_received++;
            stats.total_rtt += rtt;
            
            if (rtt < stats.min_rtt) stats.min_rtt = rtt;
            if (rtt > stats.max_rtt) stats.max_rtt = rtt;
            
            printf("%d bytes from %s: icmp_seq=%d ttl=%d time=%.2f ms\n",
                   bytes_sent, hostname, seq, 64, rtt);
        } else {
            printf("Request timeout for icmp_seq %d\n", seq);
        }
        
        seq++;
        sleep(1);
    }
    
    close(sock);
    print_ping_stats(hostname, &stats);
    
    return 0;
}

int main(int argc, char *argv[]) {
    if (argc < 2) {
        printf("Usage: %s <hostname> [count]\n", argv[0]);
        printf("Example: %s google.com 4\n", argv[0]);
        printf("Example: %s localhost (continuous, Ctrl+C to stop)\n", argv[0]);
        return 1;
    }
    
    const char *hostname = argv[1];
    
    if (argc == 3) {
        // Ping with specified count
        int count = atoi(argv[2]);
        if (count <= 0) {
            printf("Invalid count: %s\n", argv[2]);
            return 1;
        }
        
        ping_stats_t stats;
        if (ping_host(hostname, &stats) == 0) {
            print_ping_stats(hostname, &stats);
        }
    } else {
        // Continuous ping
        continuous_ping(hostname);
    }
    
    return 0;
}
