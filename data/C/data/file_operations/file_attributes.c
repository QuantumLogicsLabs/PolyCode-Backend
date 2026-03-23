#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <unistd.h>

#ifdef _WIN32
#include <windows.h>
#else
#include <pwd.h>
#include <grp.h>
#endif

// File information structure
typedef struct {
    char name[256];
    char path[512];
    long size;
    time_t creation_time;
    time_t modification_time;
    time_t access_time;
    int is_directory;
    int is_regular_file;
    int permissions;
    uid_t owner_id;
    gid_t group_id;
} FileInfo;

// Function to get file information
FileInfo getFileInfo(const char* filepath) {
    FileInfo info;
    struct stat statbuf;
    
    // Initialize with default values
    memset(&info, 0, sizeof(info));
    strncpy(info.name, filepath, sizeof(info.name) - 1);
    strncpy(info.path, filepath, sizeof(info.path) - 1);
    
    if (stat(filepath, &statbuf) != 0) {
        printf("Error: Cannot get file information for %s\n", filepath);
        return info;
    }
    
    // Fill file information
    info.size = statbuf.st_size;
    info.creation_time = statbuf.st_ctime;
    info.modification_time = statbuf.st_mtime;
    info.access_time = statbuf.st_atime;
    info.is_directory = S_ISDIR(statbuf.st_mode);
    info.is_regular_file = S_ISREG(statbuf.st_mode);
    info.permissions = statbuf.st_mode;
    info.owner_id = statbuf.st_uid;
    info.group_id = statbuf.st_gid;
    
    return info;
}

// Function to format time as string
void formatTime(time_t time_val, char* buffer, size_t buffer_size) {
    struct tm* time_info = localtime(&time_val);
    strftime(buffer, buffer_size, "%Y-%m-%d %H:%M:%S", time_info);
}

// Function to get permission string
void getPermissionString(int permissions, char* buffer, size_t buffer_size) {
    snprintf(buffer, buffer_size, "%c%c%c%c%c%c%c%c%c",
             (permissions & S_IRUSR) ? 'r' : '-',
             (permissions & S_IWUSR) ? 'w' : '-',
             (permissions & S_IXUSR) ? 'x' : '-',
             (permissions & S_IRGRP) ? 'r' : '-',
             (permissions & S_IWGRP) ? 'w' : '-',
             (permissions & S_IXGRP) ? 'x' : '-',
             (permissions & S_IROTH) ? 'r' : '-',
             (permissions & S_IWOTH) ? 'w' : '-',
             (permissions & S_IXOTH) ? 'x' : '-');
}

// Function to get owner name
void getOwnerName(uid_t uid, char* buffer, size_t buffer_size) {
#ifdef _WIN32
    snprintf(buffer, buffer_size, "Unknown (Windows)");
#else
    struct passwd* pw = getpwuid(uid);
    if (pw != NULL) {
        strncpy(buffer, pw->pw_name, buffer_size - 1);
        buffer[buffer_size - 1] = '\0';
    } else {
        snprintf(buffer, buffer_size, "Unknown");
    }
#endif
}

// Function to get group name
void getGroupName(gid_t gid, char* buffer, size_t buffer_size) {
#ifdef _WIN32
    snprintf(buffer, buffer_size, "Unknown (Windows)");
#else
    struct group* gr = getgrgid(gid);
    if (gr != NULL) {
        strncpy(buffer, gr->gr_name, buffer_size - 1);
        buffer[buffer_size - 1] = '\0';
    } else {
        snprintf(buffer, buffer_size, "Unknown");
    }
#endif
}

// Function to print file information
void printFileInfo(const FileInfo* info) {
    char time_buffer[32];
    char perm_buffer[10];
    char owner_buffer[64];
    char group_buffer[64];
    
    printf("File Information:\n");
    printf("================\n");
    printf("Name: %s\n", info->name);
    printf("Path: %s\n", info->path);
    printf("Type: %s\n", info->is_directory ? "Directory" : 
                       (info->is_regular_file ? "Regular File" : "Special File"));
    printf("Size: %ld bytes\n", info->size);
    
    formatTime(info->creation_time, time_buffer, sizeof(time_buffer));
    printf("Created: %s\n", time_buffer);
    
    formatTime(info->modification_time, time_buffer, sizeof(time_buffer));
    printf("Modified: %s\n", time_buffer);
    
    formatTime(info->access_time, time_buffer, sizeof(time_buffer));
    printf("Accessed: %s\n", time_buffer);
    
    getPermissionString(info->permissions, perm_buffer, sizeof(perm_buffer));
    printf("Permissions: %s\n", perm_buffer);
    
    getOwnerName(info->owner_id, owner_buffer, sizeof(owner_buffer));
    printf("Owner: %s (ID: %d)\n", owner_buffer, info->owner_id);
    
    getGroupName(info->group_id, group_buffer, sizeof(group_buffer));
    printf("Group: %s (ID: %d)\n", group_buffer, info->group_id);
    
    printf("\n");
}

// Function to compare file modification times
int compareFileTimes(const char* file1, const char* file2) {
    FileInfo info1 = getFileInfo(file1);
    FileInfo info2 = getFileInfo(file2);
    
    if (info1.modification_time < info2.modification_time) {
        return -1; // file1 is older
    } else if (info1.modification_time > info2.modification_time) {
        return 1;  // file1 is newer
    } else {
        return 0;  // same time
    }
}

// Function to check if file is executable
int isExecutable(const char* filepath) {
    FileInfo info = getFileInfo(filepath);
    return (info.permissions & S_IXUSR) || 
           (info.permissions & S_IXGRP) || 
           (info.permissions & S_IXOTH);
}

// Function to check if file is readable
int isReadable(const char* filepath) {
    FileInfo info = getFileInfo(filepath);
    return (info.permissions & S_IRUSR) || 
           (info.permissions & S_IRGRP) || 
           (info.permissions & S_IROTH);
}

// Function to check if file is writable
int isWritable(const char* filepath) {
    FileInfo info = getFileInfo(filepath);
    return (info.permissions & S_IWUSR) || 
           (info.permissions & S_IWGRP) || 
           (info.permissions & S_IWOTH);
}

// Function to format file size
void formatFileSize(long size, char* buffer, size_t buffer_size) {
    const char* units[] = {"B", "KB", "MB", "GB", "TB"};
    int unit_index = 0;
    double file_size = (double)size;
    
    while (file_size >= 1024.0 && unit_index < 4) {
        file_size /= 1024.0;
        unit_index++;
    }
    
    if (unit_index == 0) {
        snprintf(buffer, buffer_size, "%ld %s", size, units[unit_index]);
    } else {
        snprintf(buffer, buffer_size, "%.2f %s", file_size, units[unit_index]);
    }
}

// Function to create a sample file for testing
void createSampleFile(const char* filename, const char* content) {
    FILE* file = fopen(filename, "w");
    if (file == NULL) {
        printf("Error: Cannot create file %s\n", filename);
        return;
    }
    
    fprintf(file, "%s", content);
    fclose(file);
    printf("Created sample file: %s\n", filename);
}

int main() {
    // Create sample files for demonstration
    createSampleFile("sample1.txt", "This is a sample text file for testing file attributes.");
    createSampleFile("sample2.dat", "Binary data simulation: \x01\x02\x03\x04\x05");
    
    printf("=== File Attributes Demo ===\n\n");
    
    // Get and display information for sample1.txt
    FileInfo info1 = getFileInfo("sample1.txt");
    printFileInfo(&info1);
    
    // Get and display information for sample2.dat
    FileInfo info2 = getFileInfo("sample2.dat");
    printFileInfo(&info2);
    
    // Compare file times
    printf("File Time Comparison:\n");
    int comparison = compareFileTimes("sample1.txt", "sample2.dat");
    if (comparison < 0) {
        printf("sample1.txt is older than sample2.dat\n");
    } else if (comparison > 0) {
        printf("sample1.txt is newer than sample2.dat\n");
    } else {
        printf("sample1.txt and sample2.dat have the same modification time\n");
    }
    
    // Check file permissions
    printf("\nFile Permission Checks:\n");
    printf("sample1.txt:\n");
    printf("  Readable: %s\n", isReadable("sample1.txt") ? "Yes" : "No");
    printf("  Writable: %s\n", isWritable("sample1.txt") ? "Yes" : "No");
    printf("  Executable: %s\n", isExecutable("sample1.txt") ? "Yes" : "No");
    
    // Format file sizes
    char size_buffer[32];
    printf("\nFile Size Formatting:\n");
    formatFileSize(info1.size, size_buffer, sizeof(size_buffer));
    printf("sample1.txt: %s\n", size_buffer);
    formatFileSize(info2.size, size_buffer, sizeof(size_buffer));
    printf("sample2.dat: %s\n", size_buffer);
    
    // Get information about current directory
    printf("\nCurrent Directory Information:\n");
    FileInfo dir_info = getFileInfo(".");
    printFileInfo(&dir_info);
    
    // Clean up sample files
    remove("sample1.txt");
    remove("sample2.dat");
    printf("Cleaned up sample files\n");
    
    printf("\n=== File attributes demo completed ===\n");
    
    return 0;
}
