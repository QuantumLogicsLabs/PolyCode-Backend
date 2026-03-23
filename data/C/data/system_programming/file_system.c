/*
 * File: file_system.c
 * Description: File system operations and utilities
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <dirent.h>
#include <time.h>
#include <pwd.h>
#include <grp.h>

// File information structure
typedef struct {
    char name[256];
    char path[512];
    off_t size;
    time_t mtime;
    time_t atime;
    time_t ctime;
    mode_t mode;
    uid_t uid;
    gid_t gid;
    nlink_t nlink;
} FileInfo;

// Get file information
FileInfo get_file_info(const char* filepath) {
    FileInfo info;
    struct stat statbuf;
    
    strncpy(info.name, filepath, sizeof(info.name) - 1);
    strncpy(info.path, filepath, sizeof(info.path) - 1);
    
    if (stat(filepath, &statbuf) == 0) {
        info.size = statbuf.st_size;
        info.mtime = statbuf.st_mtime;
        info.atime = statbuf.st_atime;
        info.ctime = statbuf.st_ctime;
        info.mode = statbuf.st_mode;
        info.uid = statbuf.st_uid;
        info.gid = statbuf.st_gid;
        info.nlink = statbuf.st_nlink;
    }
    
    return info;
}

// Print file permissions
void print_permissions(mode_t mode) {
    printf((S_ISDIR(mode)) ? "d" : "-");
    printf((mode & S_IRUSR) ? "r" : "-");
    printf((mode & S_IWUSR) ? "w" : "-");
    printf((mode & S_IXUSR) ? "x" : "-");
    printf((mode & S_IRGRP) ? "r" : "-");
    printf((mode & S_IWGRP) ? "w" : "-");
    printf((mode & S_IXGRP) ? "x" : "-");
    printf((mode & S_IROTH) ? "r" : "-");
    printf((mode & S_IWOTH) ? "w" : "-");
    printf((mode & S_IXOTH) ? "x" : "-");
}

// Get file type string
const char* get_file_type(mode_t mode) {
    if (S_ISREG(mode)) return "Regular File";
    if (S_ISDIR(mode)) return "Directory";
    if (S_ISLNK(mode)) return "Symbolic Link";
    if (S_ISBLK(mode)) return "Block Device";
    if (S_ISCHR(mode)) return "Character Device";
    if (S_ISFIFO(mode)) return "FIFO/Named Pipe";
    if (S_ISSOCK(mode)) return "Socket";
    return "Unknown";
}

// Print detailed file information
void print_file_info(const char* filepath) {
    FileInfo info = get_file_info(filepath);
    struct stat statbuf;
    
    if (stat(filepath, &statbuf) != 0) {
        printf("Cannot get info for %s\n", filepath);
        return;
    }
    
    printf("=== File Information ===\n");
    printf("Path: %s\n", info.path);
    printf("Type: %s\n", get_file_type(statbuf.st_mode));
    printf("Size: %ld bytes\n", info.size);
    printf("Links: %ld\n", info.nlink);
    printf("Permissions: ");
    print_permissions(statbuf.st_mode);
    printf("\n");
    
    // Owner and group
    struct passwd* pw = getpwuid(info.uid);
    struct group* gr = getgrgid(info.gid);
    printf("Owner: %s (UID: %d)\n", pw ? pw->pw_name : "Unknown", info.uid);
    printf("Group: %s (GID: %d)\n", gr ? gr->gr_name : "Unknown", info.gid);
    
    // Timestamps
    printf("Last modified: %s", ctime(&info.mtime));
    printf("Last accessed: %s", ctime(&info.atime));
    printf("Status change: %s", ctime(&info.ctime));
}

// List directory contents
void list_directory(const char* dirpath) {
    DIR* dir;
    struct dirent* entry;
    
    printf("\n=== Directory Contents ===\n");
    printf("Directory: %s\n", dirpath);
    printf("Permissions Size    Owner     Group     Name\n");
    printf("------------------------------------------------\n");
    
    dir = opendir(dirpath);
    if (dir == NULL) {
        printf("Cannot open directory %s\n", dirpath);
        return;
    }
    
    while ((entry = readdir(dir)) != NULL) {
        char fullpath[512];
        snprintf(fullpath, sizeof(fullpath), "%s/%s", dirpath, entry->d_name);
        
        struct stat statbuf;
        if (stat(fullpath, &statbuf) == 0) {
            print_permissions(statbuf.st_mode);
            
            // Owner and group
            struct passwd* pw = getpwuid(statbuf.st_uid);
            struct group* gr = getgrgid(statbuf.st_gid);
            
            printf(" %8ld %-8s %-8s %s\n", 
                   statbuf.st_size,
                   pw ? pw->pw_name : "unknown",
                   gr ? gr->gr_name : "unknown",
                   entry->d_name);
        }
    }
    
    closedir(dir);
}

// Create directory with permissions
int create_directory(const char* dirpath, mode_t mode) {
    if (mkdir(dirpath, mode) == 0) {
        printf("Directory created: %s\n", dirpath);
        return 0;
    } else {
        perror("mkdir failed");
        return -1;
    }
}

// Remove directory
int remove_directory(const char* dirpath) {
    if (rmdir(dirpath) == 0) {
        printf("Directory removed: %s\n", dirpath);
        return 0;
    } else {
        perror("rmdir failed");
        return -1;
    }
}

// Copy file
int copy_file(const char* src, const char* dest) {
    int src_fd, dest_fd;
    char buffer[4096];
    ssize_t bytes_read;
    
    src_fd = open(src, O_RDONLY);
    if (src_fd == -1) {
        perror("Cannot open source file");
        return -1;
    }
    
    dest_fd = open(dest, O_WRONLY | O_CREAT | O_TRUNC, 0644);
    if (dest_fd == -1) {
        perror("Cannot create destination file");
        close(src_fd);
        return -1;
    }
    
    while ((bytes_read = read(src_fd, buffer, sizeof(buffer))) > 0) {
        if (write(dest_fd, buffer, bytes_read) != bytes_read) {
            perror("Write error");
            close(src_fd);
            close(dest_fd);
            return -1;
        }
    }
    
    close(src_fd);
    close(dest_fd);
    
    printf("File copied: %s -> %s\n", src, dest);
    return 0;
}

// Move/rename file
int move_file(const char* src, const char* dest) {
    if (rename(src, dest) == 0) {
        printf("File moved: %s -> %s\n", src, dest);
        return 0;
    } else {
        perror("rename failed");
        return -1;
    }
}

// Remove file
int remove_file(const char* filepath) {
    if (unlink(filepath) == 0) {
        printf("File removed: %s\n", filepath);
        return 0;
    } else {
        perror("unlink failed");
        return -1;
    }
}

// Create symbolic link
int create_symlink(const char* target, const char* linkpath) {
    if (symlink(target, linkpath) == 0) {
        printf("Symlink created: %s -> %s\n", linkpath, target);
        return 0;
    } else {
        perror("symlink failed");
        return -1;
    }
}

// Read symbolic link
void read_symlink(const char* linkpath) {
    char target[512];
    ssize_t len = readlink(linkpath, target, sizeof(target) - 1);
    
    if (len != -1) {
        target[len] = '\0';
        printf("Symlink %s points to: %s\n", linkpath, target);
    } else {
        perror("readlink failed");
    }
}

// Get disk usage information
void get_disk_usage(const char* path) {
    struct statvfs vfs;
    
    if (statvfs(path, &vfs) == 0) {
        unsigned long total = vfs.f_blocks * vfs.f_frsize;
        unsigned long free = vfs.f_bfree * vfs.f_frsize;
        unsigned long available = vfs.f_bavail * vfs.f_frsize;
        
        printf("\n=== Disk Usage ===\n");
        printf("Path: %s\n", path);
        printf("Total space: %lu bytes (%.2f MB)\n", total, total / (1024.0 * 1024.0));
        printf("Free space: %lu bytes (%.2f MB)\n", free, free / (1024.0 * 1024.0));
        printf("Available: %lu bytes (%.2f MB)\n", available, available / (1024.0 * 1024.0));
        printf("Used: %.2f%%\n", (double)(total - available) / total * 100);
    } else {
        perror("statvfs failed");
    }
}

// Find files by pattern
void find_files(const char* dirpath, const char* pattern) {
    DIR* dir;
    struct dirent* entry;
    
    printf("\n=== Find Files ===\n");
    printf("Pattern: %s\n", pattern);
    printf("Directory: %s\n", dirpath);
    printf("Matches:\n");
    
    dir = opendir(dirpath);
    if (dir == NULL) {
        printf("Cannot open directory %s\n", dirpath);
        return;
    }
    
    int found = 0;
    while ((entry = readdir(dir)) != NULL) {
        if (strstr(entry->d_name, pattern) != NULL) {
            printf("  %s\n", entry->d_name);
            found++;
        }
    }
    
    if (found == 0) {
        printf("  No matches found\n");
    } else {
        printf("  Found %d matches\n", found);
    }
    
    closedir(dir);
}

// File system test function
void test_file_system() {
    printf("=== File System Testing ===\n");
    
    // Test file information
    printf("\n1. File Information Test:\n");
    print_file_info(".");
    
    // Test directory listing
    printf("\n2. Directory Listing Test:\n");
    list_directory(".");
    
    // Test file operations
    printf("\n3. File Operations Test:\n");
    
    // Create test file
    FILE* test_file = fopen("test_file.txt", "w");
    if (test_file) {
        fprintf(test_file, "This is a test file for file system operations.\n");
        fclose(test_file);
        
        // Copy file
        copy_file("test_file.txt", "test_copy.txt");
        
        // Move file
        move_file("test_copy.txt", "test_moved.txt");
        
        // Create symlink
        create_symlink("test_file.txt", "test_symlink.txt");
        
        // Read symlink
        read_symlink("test_symlink.txt");
        
        // Get info on symlink
        print_file_info("test_symlink.txt");
        
        // Cleanup
        remove_file("test_file.txt");
        remove_file("test_moved.txt");
        remove_file("test_symlink.txt");
    }
    
    // Test disk usage
    printf("\n4. Disk Usage Test:\n");
    get_disk_usage(".");
    
    // Test find files
    printf("\n5. Find Files Test:\n");
    find_files(".", ".c");
    
    printf("\n=== File system testing completed ===\n");
}

int main() {
    test_file_system();
    
    return 0;
}
