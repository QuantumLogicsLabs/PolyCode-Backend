#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <dirent.h>
#include <sys/stat.h>
#include <unistd.h>

// Cross-platform directory operations
#ifdef _WIN32
#include <windows.h>
#define PATH_SEPARATOR "\\"
#else
#define PATH_SEPARATOR "/"
#endif

// Function to check if a path is a directory
int isDirectory(const char* path) {
    struct stat statbuf;
    if (stat(path, &statbuf) != 0) {
        return 0;
    }
    return S_ISDIR(statbuf.st_mode);
}

// Function to check if a path exists
int pathExists(const char* path) {
    struct stat statbuf;
    return (stat(path, &statbuf) == 0);
}

// Function to create directory
int createDirectory(const char* path) {
#ifdef _WIN32
    return _mkdir(path);
#else
    return mkdir(path, 0777);
#endif
}

// Function to remove directory
int removeDirectory(const char* path) {
#ifdef _WIN32
    return _rmdir(path);
#else
    return rmdir(path);
#endif
}

// Function to list directory contents
void listDirectory(const char* path) {
    DIR* dir;
    struct dirent* entry;
    
    printf("Contents of directory '%s':\n", path);
    printf("--------------------------------\n");
    
    dir = opendir(path);
    if (dir == NULL) {
        printf("Error: Cannot open directory %s\n", path);
        return;
    }
    
    while ((entry = readdir(dir)) != NULL) {
        char full_path[1024];
        snprintf(full_path, sizeof(full_path), "%s%s%s", 
                path, PATH_SEPARATOR, entry->d_name);
        
        printf("%-20s", entry->d_name);
        
        if (isDirectory(full_path)) {
            printf("(Directory)");
        } else {
            struct stat statbuf;
            if (stat(full_path, &statbuf) == 0) {
                printf("(%ld bytes)", statbuf.st_size);
            }
        }
        printf("\n");
    }
    
    closedir(dir);
}

// Function to count files in directory
int countFiles(const char* path) {
    DIR* dir;
    struct dirent* entry;
    int count = 0;
    
    dir = opendir(path);
    if (dir == NULL) {
        return -1;
    }
    
    while ((entry = readdir(dir)) != NULL) {
        if (strcmp(entry->d_name, ".") != 0 && strcmp(entry->d_name, "..") != 0) {
            count++;
        }
    }
    
    closedir(dir);
    return count;
}

// Function to get file size
long getFileSize(const char* path) {
    struct stat statbuf;
    if (stat(path, &statbuf) != 0) {
        return -1;
    }
    return statbuf.st_size;
}

// Function to get file permissions
void getFilePermissions(const char* path) {
    struct stat statbuf;
    if (stat(path, &statbuf) != 0) {
        printf("Cannot get permissions for %s\n", path);
        return;
    }
    
    printf("Permissions for %s:\n", path);
    printf("  Owner: ");
    printf((statbuf.st_mode & S_IRUSR) ? "r" : "-");
    printf((statbuf.st_mode & S_IWUSR) ? "w" : "-");
    printf((statbuf.st_mode & S_IXUSR) ? "x" : "-");
    
    printf("  Group: ");
    printf((statbuf.st_mode & S_IRGRP) ? "r" : "-");
    printf((statbuf.st_mode & S_IWGRP) ? "w" : "-");
    printf((statbuf.st_mode & S_IXGRP) ? "x" : "-");
    
    printf("  Other: ");
    printf((statbuf.st_mode & S_IROTH) ? "r" : "-");
    printf((statbuf.st_mode & S_IWOTH) ? "w" : "-");
    printf((statbuf.st_mode & S_IXOTH) ? "x" : "-");
    printf("\n");
}

// Function to create a file with content
void createSampleFile(const char* path, const char* content) {
    FILE* file = fopen(path, "w");
    if (file == NULL) {
        printf("Error: Cannot create file %s\n", path);
        return;
    }
    
    fprintf(file, "%s", content);
    fclose(file);
    printf("Created file: %s\n", path);
}

// Function to copy a file
int copyFile(const char* src, const char* dest) {
    FILE* source = fopen(src, "rb");
    FILE* destination = fopen(dest, "wb");
    
    if (source == NULL || destination == NULL) {
        if (source) fclose(source);
        if (destination) fclose(destination);
        return 0;
    }
    
    char buffer[1024];
    size_t bytes;
    
    while ((bytes = fread(buffer, 1, sizeof(buffer), source)) > 0) {
        fwrite(buffer, 1, bytes, destination);
    }
    
    fclose(source);
    fclose(destination);
    return 1;
}

// Function to move a file
int moveFile(const char* src, const char* dest) {
    if (copyFile(src, dest)) {
        remove(src);
        return 1;
    }
    return 0;
}

// Function to search for files with specific extension
void searchByExtension(const char* path, const char* extension) {
    DIR* dir;
    struct dirent* entry;
    
    printf("Searching for *.%s files in %s:\n", extension, path);
    
    dir = opendir(path);
    if (dir == NULL) {
        printf("Error: Cannot open directory %s\n", path);
        return;
    }
    
    int found = 0;
    while ((entry = readdir(dir)) != NULL) {
        if (strstr(entry->d_name, extension) != NULL) {
            printf("  %s\n", entry->d_name);
            found++;
        }
    }
    
    if (found == 0) {
        printf("  No .%s files found\n", extension);
    } else {
        printf("  Found %d .%s files\n", found, extension);
    }
    
    closedir(dir);
}

// Function to create directory tree
void createDirectoryTree(const char* base_path) {
    char path1[1024], path2[1024], path3[1024];
    
    snprintf(path1, sizeof(path1), "%s%ssubdir1", base_path, PATH_SEPARATOR);
    snprintf(path2, sizeof(path2), "%s%ssubdir2", base_path, PATH_SEPARATOR);
    snprintf(path3, sizeof(path3), "%s%ssubdir2%snested", base_path, PATH_SEPARATOR, PATH_SEPARATOR);
    
    createDirectory(path1);
    createDirectory(path2);
    createDirectory(path3);
    
    // Create some sample files
    char file1[1024], file2[1024], file3[1024];
    snprintf(file1, sizeof(file1), "%s%sfile1.txt", path1, PATH_SEPARATOR);
    snprintf(file2, sizeof(file2), "%s%sfile2.dat", path2, PATH_SEPARATOR);
    snprintf(file3, sizeof(file3), "%s%sfile3.log", path3, PATH_SEPARATOR);
    
    createSampleFile(file1, "This is file1 in subdir1");
    createSampleFile(file2, "This is file2 in subdir2");
    createSampleFile(file3, "This is file3 in nested directory");
    
    printf("Created directory tree structure\n");
}

int main() {
    const char* test_dir = "test_directory";
    
    printf("=== Directory Operations Demo ===\n\n");
    
    // Create test directory
    if (!pathExists(test_dir)) {
        if (createDirectory(test_dir) == 0) {
            printf("Created directory: %s\n", test_dir);
        } else {
            printf("Error creating directory\n");
            return 1;
        }
    }
    
    // Create directory tree
    createDirectoryTree(test_dir);
    
    // List directory contents
    printf("\n");
    listDirectory(test_dir);
    
    // Count files
    int file_count = countFiles(test_dir);
    printf("\nTotal items in directory: %d\n", file_count);
    
    // Search for specific file types
    printf("\n");
    searchByExtension(test_dir, "txt");
    searchByExtension(test_dir, "dat");
    
    // Get file information
    char file_path[1024];
    snprintf(file_path, sizeof(file_path), "%s%ssubdir1%sfile1.txt", 
             test_dir, PATH_SEPARATOR, PATH_SEPARATOR);
    
    printf("\nFile information:\n");
    printf("  Path: %s\n", file_path);
    printf("  Size: %ld bytes\n", getFileSize(file_path));
    getFilePermissions(file_path);
    
    // Copy file
    char copy_path[1024];
    snprintf(copy_path, sizeof(copy_path), "%s%scopy_of_file1.txt", 
             test_dir, PATH_SEPARATOR);
    
    printf("\nCopying file...\n");
    if (copyFile(file_path, copy_path)) {
        printf("File copied successfully to: %s\n", copy_path);
    } else {
        printf("File copy failed\n");
    }
    
    // List directory again to show copied file
    printf("\n");
    listDirectory(test_dir);
    
    // Clean up (optional - comment out if you want to keep the files)
    printf("\nCleaning up...\n");
    // removeDirectory(test_dir); // Uncomment to remove test directory
    
    printf("\n=== Directory operations demo completed ===\n");
    
    return 0;
}
