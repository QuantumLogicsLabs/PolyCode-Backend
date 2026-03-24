# Plugin Architecture

This file contains comprehensive plugin architecture examples in C, including dynamic plugin loading, interface standardization, plugin management, discovery, activation, configuration, and error handling.

## 📚 Plugin Architecture Fundamentals

### 🎯 Plugin Concepts
- **Dynamic Loading**: Load plugins at runtime from shared libraries
- **Interface Standardization**: Common plugin interface for compatibility
- **Plugin Management**: Discover, load, activate, and manage plugins
- **Error Handling**: Robust error handling for plugin operations

### 🔧 Plugin Components
- **Plugin Interface**: Standard interface that all plugins must implement
- **Plugin Manager**: Central system for managing plugins
- **Plugin Discovery**: Automatic plugin discovery in directories
- **Plugin Lifecycle**: Load, activate, configure, deactivate, unload

## 🏗️ Plugin Interface Design

### Plugin API Version
```c
#define PLUGIN_API_VERSION_MAJOR 1
#define PLUGIN_API_VERSION_MINOR 0
```

### Plugin Types
```c
typedef enum {
    PLUGIN_TYPE_UNKNOWN = 0,
    PLUGIN_TYPE_FILTER = 1,    // Data transformation plugins
    PLUGIN_TYPE_PROCESSOR = 2,  // Data processing plugins
    PLUGIN_TYPE_OUTPUT = 3,     // Output generation plugins
    PLUGIN_TYPE_INPUT = 4,      // Input handling plugins
    PLUGIN_TYPE_UTILITY = 5     // Utility plugins
} PluginType;
```

### Plugin Status
```c
typedef enum {
    PLUGIN_STATUS_UNLOADED = 0,
    PLUGIN_STATUS_LOADED = 1,
    PLUGIN_STATUS_ACTIVE = 2,
    PLUGIN_STATUS_ERROR = 3
} PluginStatus;
```

### Plugin Information
```c
typedef struct {
    char name[MAX_PLUGIN_NAME_LENGTH];
    char version[MAX_PLUGIN_VERSION_LENGTH];
    char author[MAX_PLUGIN_NAME_LENGTH];
    char description[MAX_PLUGIN_DESCRIPTION_LENGTH];
    PluginType type;
    int api_version_major;
    int api_version_minor;
    time_t build_time;
} PluginInfo;
```

### Plugin Function Descriptor
```c
typedef struct {
    char name[64];
    void* function_ptr;
    char description[256];
    char parameters[256];
} PluginFunction;
```

## 🔌 Plugin Interface

### Standard Plugin Interface
```c
typedef struct {
    // Required functions
    int (*initialize)(void);
    int (*cleanup)(void);
    int (*get_info)(PluginInfo* info);
    
    // Optional functions
    int (*process)(void* data, size_t size);
    int (*configure)(const char* config);
    int (*get_status)(void);
    PluginFunction* (*get_functions)(int* count);
} PluginInterface;
```

### Required Functions

#### Initialize
```c
int plugin_initialize(void) {
    // Perform plugin initialization
    // Allocate resources, setup state, etc.
    printf("Plugin initialized\n");
    return 0; // Success
}
```

#### Cleanup
```c
int plugin_cleanup(void) {
    // Perform plugin cleanup
    // Free resources, cleanup state, etc.
    printf("Plugin cleaned up\n");
    return 0; // Success
}
```

#### Get Info
```c
int plugin_get_info(PluginInfo* info) {
    if (!info) return -1;
    
    // Fill plugin information
    strcpy(info->name, "My Plugin");
    strcpy(info->version, "1.0.0");
    strcpy(info->author, "Plugin Developer");
    strcpy(info->description, "A sample plugin");
    info->type = PLUGIN_TYPE_PROCESSOR;
    info->api_version_major = 1;
    info->api_version_minor = 0;
    
    return 0; // Success
}
```

### Optional Functions

#### Process
```c
int plugin_process(void* data, size_t size) {
    if (!data) return -1;
    
    // Process the data
    char* text = (char*)data;
    for (size_t i = 0; i < size && text[i] != '\0'; i++) {
        // Example: Convert to uppercase
        if (text[i] >= 'a' && text[i] <= 'z') {
            text[i] = text[i] - 'a' + 'A';
        }
    }
    
    return 0; // Success
}
```

#### Configure
```c
int plugin_configure(const char* config) {
    if (!config) return -1;
    
    // Parse configuration
    printf("Plugin configured with: %s\n", config);
    
    return 0; // Success
}
```

#### Get Status
```c
int plugin_get_status(void) {
    // Return plugin status
    return 1; // 1 = active, 0 = inactive
}
```

#### Get Functions
```c
PluginFunction plugin_functions[] = {
    {"process", plugin_process, "Process data", "data: void*, size: size_t"},
    {"configure", plugin_configure, "Configure plugin", "config: const char*"},
    {"get_status", plugin_get_status, "Get plugin status", "void"}
};

PluginFunction* plugin_get_functions(int* count) {
    if (count) *count = sizeof(plugin_functions) / sizeof(plugin_functions[0]);
    return plugin_functions;
}
```

**Interface Benefits**:
- **Standardization**: Common interface across all plugins
- **Compatibility**: Version checking for API compatibility
- **Flexibility**: Optional functions for different plugin types
- **Discovery**: Function discovery and introspection

## 🏛️ Plugin Manager

### Plugin Manager Structure
```c
typedef struct {
    PluginHandle plugins[MAX_PLUGINS];
    int plugin_count;
    char plugin_directory[512];
    int auto_discover;
    PluginInterface* active_plugins[MAX_PLUGINS];
    int active_count;
} PluginManager;
```

### Plugin Handle
```c
typedef struct {
    char filename[512];
    void* handle; // Dynamic library handle
    PluginInterface* interface;
    PluginInfo info;
    PluginStatus status;
    time_t load_time;
    int function_count;
    PluginFunction functions[MAX_PLUGIN_FUNCTIONS];
} PluginHandle;
```

### Manager Initialization
```c
int initPluginManager(const char* plugin_directory, int auto_discover) {
    memset(&g_plugin_manager, 0, sizeof(PluginManager));
    
    if (plugin_directory) {
        strncpy(g_plugin_manager.plugin_directory, plugin_directory, 
                sizeof(g_plugin_manager.plugin_directory) - 1);
        g_plugin_manager.plugin_directory[sizeof(g_plugin_manager.plugin_directory) - 1] = '\0';
    } else {
        strcpy(g_plugin_manager.plugin_directory, "./plugins");
    }
    
    g_plugin_manager.auto_discover = auto_discover;
    
    if (auto_discover) {
        discoverPlugins();
    }
    
    return 0; // Success
}
```

**Manager Benefits**:
- **Centralized Control**: Single point for plugin management
- **Resource Tracking**: Track all loaded plugins
- **Configuration**: Centralized plugin configuration
- **Lifecycle Management**: Complete plugin lifecycle control

## 📦 Plugin Loading

### Dynamic Library Loading
```c
int loadPlugin(PluginHandle* plugin, const char* filename) {
    if (!plugin || !filename) {
        return -1; // Invalid parameters
    }
    
    // Initialize plugin handle
    memset(plugin, 0, sizeof(PluginHandle));
    strncpy(plugin->filename, filename, sizeof(plugin->filename) - 1);
    
    // Load the dynamic library
    plugin->handle = dlopen(filename, RTLD_LAZY);
    if (!plugin->handle) {
        fprintf(stderr, "Failed to load plugin %s: %s\n", filename, dlerror());
        plugin->status = PLUGIN_STATUS_ERROR;
        return -2; // Load failed
    }
    
    // Get the plugin interface
    plugin->interface = (PluginInterface*)dlsym(plugin->handle, "plugin_interface");
    if (!plugin->interface) {
        fprintf(stderr, "Plugin %s does not export required interface\n", filename);
        dlclose(plugin->handle);
        plugin->status = PLUGIN_STATUS_ERROR;
        return -3; // Interface not found
    }
    
    // Get plugin information
    if (plugin->interface->get_info) {
        if (plugin->interface->get_info(&plugin->info) != 0) {
            fprintf(stderr, "Failed to get plugin information from %s\n", filename);
            dlclose(plugin->handle);
            plugin->status = PLUGIN_STATUS_ERROR;
            return -4; // Info failed
        }
    }
    
    // Check API compatibility
    if (plugin->info.api_version_major != PLUGIN_API_VERSION_MAJOR) {
        fprintf(stderr, "Plugin %s API version mismatch\n", filename);
        dlclose(plugin->handle);
        plugin->status = PLUGIN_STATUS_ERROR;
        return -5; // API version mismatch
    }
    
    // Initialize the plugin
    if (plugin->interface->initialize) {
        if (plugin->interface->initialize() != 0) {
            fprintf(stderr, "Failed to initialize plugin %s\n", filename);
            dlclose(plugin->handle);
            plugin->status = PLUGIN_STATUS_ERROR;
            return -6; // Initialization failed
        }
    }
    
    // Get plugin functions
    if (plugin->interface->get_functions) {
        int count = 0;
        PluginFunction* functions = plugin->interface->get_functions(&count);
        if (functions && count > 0) {
            plugin->function_count = (count < MAX_PLUGIN_FUNCTIONS) ? count : MAX_PLUGIN_FUNCTIONS;
            memcpy(plugin->functions, functions, 
                   plugin->function_count * sizeof(PluginFunction));
        }
    }
    
    plugin->status = PLUGIN_STATUS_LOADED;
    plugin->load_time = time(NULL);
    
    printf("Successfully loaded plugin: %s v%s\n", plugin->info.name, plugin->info.version);
    return 0; // Success
}
```

### Plugin Unloading
```c
int unloadPlugin(PluginHandle* plugin) {
    if (!plugin || plugin->status == PLUGIN_STATUS_UNLOADED) {
        return -1; // Invalid parameters or already unloaded
    }
    
    // Cleanup the plugin
    if (plugin->interface && plugin->interface->cleanup) {
        plugin->interface->cleanup();
    }
    
    // Close the dynamic library
    if (plugin->handle) {
        dlclose(plugin->handle);
    }
    
    // Remove from active plugins
    for (int i = 0; i < g_plugin_manager.active_count; i++) {
        if (g_plugin_manager.active_plugins[i] == plugin->interface) {
            // Remove from active list
            for (int j = i; j < g_plugin_manager.active_count - 1; j++) {
                g_plugin_manager.active_plugins[j] = g_plugin_manager.active_plugins[j + 1];
            }
            g_plugin_manager.active_count--;
            break;
        }
    }
    
    plugin->status = PLUGIN_STATUS_UNLOADED;
    memset(plugin, 0, sizeof(PluginHandle));
    
    return 0; // Success
}
```

**Loading Benefits**:
- **Dynamic Loading**: Load plugins at runtime
- **Version Checking**: Ensure API compatibility
- **Error Handling**: Comprehensive error handling
- **Resource Management**: Proper resource cleanup

## 🔍 Plugin Discovery

### Directory Scanning
```c
int discoverPlugins() {
    DIR* dir = opendir(g_plugin_manager.plugin_directory);
    if (!dir) {
        fprintf(stderr, "Cannot open plugin directory: %s\n", g_plugin_manager.plugin_directory);
        return -1;
    }
    
    struct dirent* entry;
    int discovered = 0;
    
    while ((entry = readdir(dir)) != NULL && g_plugin_manager.plugin_count < MAX_PLUGINS) {
        // Check for .so files (Unix shared libraries)
        int name_len = strlen(entry->d_name);
        if (name_len > 3 && strcmp(entry->d_name + name_len - 3, ".so") == 0) {
            char full_path[1024];
            snprintf(full_path, sizeof(full_path), "%s/%s", 
                     g_plugin_manager.plugin_directory, entry->d_name);
            
            PluginHandle* plugin = &g_plugin_manager.plugins[g_plugin_manager.plugin_count];
            if (loadPlugin(plugin, full_path) == 0) {
                g_plugin_manager.plugin_count++;
                discovered++;
            }
        }
    }
    
    closedir(dir);
    printf("Discovered %d plugins\n", discovered);
    return discovered;
}
```

### Plugin Finding
```c
PluginHandle* findPlugin(const char* name) {
    for (int i = 0; i < g_plugin_manager.plugin_count; i++) {
        if (strcmp(g_plugin_manager.plugins[i].info.name, name) == 0) {
            return &g_plugin_manager.plugins[i];
        }
    }
    return NULL;
}
```

**Discovery Benefits**:
- **Automatic Loading**: Discover plugins automatically
- **Directory Support**: Scan multiple directories
- **Filtering**: Filter by file extensions
- **Error Resilience**: Handle missing/broken plugins

## ⚡ Plugin Activation

### Plugin Activation
```c
int activatePlugin(const char* name) {
    PluginHandle* plugin = findPlugin(name);
    if (!plugin) {
        return -1; // Plugin not found
    }
    
    if (plugin->status != PLUGIN_STATUS_LOADED) {
        return -2; // Plugin not in loadable state
    }
    
    // Add to active plugins
    if (g_plugin_manager.active_count < MAX_PLUGINS) {
        g_plugin_manager.active_plugins[g_plugin_manager.active_count] = plugin->interface;
        g_plugin_manager.active_count++;
        plugin->status = PLUGIN_STATUS_ACTIVE;
        
        printf("Activated plugin: %s\n", name);
        return 0; // Success
    }
    
    return -3; // Too many active plugins
}
```

### Plugin Deactivation
```c
int deactivatePlugin(const char* name) {
    PluginHandle* plugin = findPlugin(name);
    if (!plugin) {
        return -1; // Plugin not found
    }
    
    if (plugin->status != PLUGIN_STATUS_ACTIVE) {
        return -2; // Plugin not active
    }
    
    // Remove from active plugins
    for (int i = 0; i < g_plugin_manager.active_count; i++) {
        if (g_plugin_manager.active_plugins[i] == plugin->interface) {
            // Remove from active list
            for (int j = i; j < g_plugin_manager.active_count - 1; j++) {
                g_plugin_manager.active_plugins[j] = g_plugin_manager.active_plugins[j + 1];
            }
            g_plugin_manager.active_count--;
            break;
        }
    }
    
    plugin->status = PLUGIN_STATUS_LOADED;
    printf("Deactivated plugin: %s\n", name);
    return 0; // Success
}
```

**Activation Benefits**:
- **Selective Processing**: Only active plugins process data
- **Resource Management**: Control resource usage
- **Plugin Orchestration**: Manage plugin execution order
- **State Management**: Track plugin states

## 🔄 Plugin Execution

### Function Execution
```c
int executePluginFunction(const char* plugin_name, const char* function_name, 
                         void* data, size_t size) {
    PluginHandle* plugin = findPlugin(plugin_name);
    if (!plugin) {
        return -1; // Plugin not found
    }
    
    if (plugin->status != PLUGIN_STATUS_ACTIVE) {
        return -2; // Plugin not active
    }
    
    // Find the function
    for (int i = 0; i < plugin->function_count; i++) {
        if (strcmp(plugin->functions[i].name, function_name) == 0) {
            // Execute the function (simplified)
            if (plugin->interface->process) {
                return plugin->interface->process(data, size);
            }
            return -3; // Function not executable
        }
    }
    
    return -4; // Function not found
}
```

### Pipeline Processing
```c
int processThroughPlugins(void* data, size_t size) {
    int result = 0;
    
    for (int i = 0; i < g_plugin_manager.active_count; i++) {
        PluginInterface* interface = g_plugin_manager.active_plugins[i];
        
        if (interface->process) {
            int plugin_result = interface->process(data, size);
            if (plugin_result != 0) {
                printf("Plugin %d returned error: %d\n", i, plugin_result);
                result = plugin_result;
            }
        }
    }
    
    return result;
}
```

**Execution Benefits**:
- **Pipeline Processing**: Process data through multiple plugins
- **Function Discovery**: Discover and execute plugin functions
- **Error Propagation**: Handle plugin execution errors
- **Data Flow**: Control data flow through plugins

## ⚙️ Plugin Configuration

### Plugin Configuration
```c
int configurePlugin(const char* plugin_name, const char* config) {
    PluginHandle* plugin = findPlugin(plugin_name);
    if (!plugin) {
        return -1; // Plugin not found
    }
    
    if (!plugin->interface->configure) {
        return -2; // Plugin doesn't support configuration
    }
    
    return plugin->interface->configure(config);
}
```

### Configuration Examples
```c
// Example: Configure a filter plugin
configurePlugin("Uppercase Filter", "case=upper, preserve_numbers=true");

// Example: Configure a processor plugin
configurePlugin("Word Counter", "min_word_length=1, ignore_numbers=false");

// Example: Configure an output plugin
configurePlugin("File Output", "filename=output.txt, mode=append");
```

**Configuration Benefits**:
- **Runtime Configuration**: Configure plugins at runtime
- **Flexibility**: Adapt plugin behavior without recompilation
- **Parameter Passing**: Pass configuration parameters to plugins
- **Validation**: Validate configuration parameters

## 📊 Plugin Information

### Plugin Information Display
```c
void printPluginInfo(const PluginHandle* plugin) {
    if (!plugin) {
        printf("Invalid plugin\n");
        return;
    }
    
    printf("Plugin Information:\n");
    printf("  Name: %s\n", plugin->info.name);
    printf("  Version: %s\n", plugin->info.version);
    printf("  Author: %s\n", plugin->info.author);
    printf("  Type: %s\n", getPluginTypeName(plugin->info.type));
    printf("  Description: %s\n", plugin->info.description);
    printf("  API Version: %d.%d\n", plugin->info.api_version_major, plugin->info.api_version_minor);
    printf("  Status: %s\n", getPluginStatusName(plugin->status));
    printf("  Load Time: %s", ctime(&plugin->load_time));
    printf("  Functions: %d\n", plugin->function_count);
    
    if (plugin->function_count > 0) {
        printf("  Available Functions:\n");
        for (int i = 0; i < plugin->function_count; i++) {
            printf("    %s: %s\n", plugin->functions[i].name, plugin->functions[i].description);
        }
    }
    printf("\n");
}
```

### Plugin Listing
```c
void listPlugins() {
    printf("=== Plugin List ===\n");
    printf("Total plugins: %d\n", g_plugin_manager.plugin_count);
    printf("Active plugins: %d\n\n", g_plugin_manager.active_count);
    
    for (int i = 0; i < g_plugin_manager.plugin_count; i++) {
        PluginHandle* plugin = &g_plugin_manager.plugins[i];
        printf("%d. %s (%s) - %s\n", i + 1, plugin->info.name, 
               plugin->info.version, getPluginStatusName(plugin->status));
    }
    printf("\n");
}
```

**Information Benefits**:
- **Introspection**: Discover plugin capabilities
- **Documentation**: Self-documenting plugins
- **Debugging**: Plugin state information
- **Management**: Plugin inventory and status

## 🔧 Sample Plugin Implementations

### Filter Plugin Example
```c
// Uppercase Filter Plugin
static PluginInfo filter_plugin_info = {
    .name = "Uppercase Filter",
    .version = "1.0.0",
    .author = "Plugin Developer",
    .description = "Converts text to uppercase",
    .type = PLUGIN_TYPE_FILTER,
    .api_version_major = 1,
    .api_version_minor = 0
};

int filter_process(void* data, size_t size) {
    if (!data) return -1;
    
    char* text = (char*)data;
    for (size_t i = 0; i < size && text[i] != '\0'; i++) {
        if (text[i] >= 'a' && text[i] <= 'z') {
            text[i] = text[i] - 'a' + 'A';
        }
    }
    
    return 0;
}

PluginInterface filter_plugin_interface = {
    .initialize = filter_initialize,
    .cleanup = filter_cleanup,
    .get_info = filter_get_info,
    .process = filter_process,
    .configure = filter_configure,
    .get_status = filter_get_status,
    .get_functions = filter_get_functions
};

// Export the plugin interface
PluginInterface* plugin_interface = &filter_plugin_interface;
```

### Processor Plugin Example
```c
// Word Counter Plugin
static PluginInfo processor_plugin_info = {
    .name = "Word Counter",
    .version = "1.0.0",
    .author = "Plugin Developer",
    .description = "Counts words in text",
    .type = PLUGIN_TYPE_PROCESSOR,
    .api_version_major = 1,
    .api_version_minor = 0
};

static int word_count = 0;

int processor_process(void* data, size_t size) {
    if (!data) return -1;
    
    char* text = (char*)data;
    int words = 0;
    int in_word = 0;
    
    for (size_t i = 0; i < size && text[i] != '\0'; i++) {
        if (text[i] == ' ' || text[i] == '\t' || text[i] == '\n') {
            if (in_word) {
                words++;
                in_word = 0;
            }
        } else {
            in_word = 1;
        }
    }
    
    if (in_word) words++;
    
    word_count += words;
    printf("Word Counter: Found %d words (total: %d)\n", words, word_count);
    
    return 0;
}

PluginInterface processor_plugin_interface = {
    .initialize = processor_initialize,
    .cleanup = processor_cleanup,
    .get_info = processor_get_info,
    .process = processor_process,
    .configure = processor_configure,
    .get_status = processor_get_status,
    .get_functions = processor_get_functions
};
```

**Sample Plugin Benefits**:
- **Reference Implementation**: Show how to implement plugins
- **Best Practices**: Demonstrate plugin development patterns
- **Testing**: Provide test plugins for the system
- **Documentation**: Self-documenting examples

## ⚠️ Error Handling

### Comprehensive Error Handling
```c
int loadPlugin(PluginHandle* plugin, const char* filename) {
    if (!plugin || !filename) {
        return -1; // Invalid parameters
    }
    
    // Load the dynamic library
    plugin->handle = dlopen(filename, RTLD_LAZY);
    if (!plugin->handle) {
        fprintf(stderr, "Failed to load plugin %s: %s\n", filename, dlerror());
        plugin->status = PLUGIN_STATUS_ERROR;
        return -2; // Load failed
    }
    
    // Get the plugin interface
    plugin->interface = (PluginInterface*)dlsym(plugin->handle, "plugin_interface");
    if (!plugin->interface) {
        fprintf(stderr, "Plugin %s does not export required interface\n", filename);
        dlclose(plugin->handle);
        plugin->status = PLUGIN_STATUS_ERROR;
        return -3; // Interface not found
    }
    
    // Check API compatibility
    if (plugin->info.api_version_major != PLUGIN_API_VERSION_MAJOR) {
        fprintf(stderr, "Plugin %s API version mismatch\n", filename);
        dlclose(plugin->handle);
        plugin->status = PLUGIN_STATUS_ERROR;
        return -5; // API version mismatch
    }
    
    // ... more error checking
    
    return 0; // Success
}
```

### Error Recovery
```c
int handlePluginError(const char* plugin_name, int error_code) {
    switch (error_code) {
        case -1:
            printf("Plugin %s: Invalid parameters\n", plugin_name);
            break;
        case -2:
            printf("Plugin %s: Load failed\n", plugin_name);
            break;
        case -3:
            printf("Plugin %s: Interface not found\n", plugin_name);
            break;
        case -4:
            printf("Plugin %s: Info failed\n", plugin_name);
            break;
        case -5:
            printf("Plugin %s: API version mismatch\n", plugin_name);
            break;
        case -6:
            printf("Plugin %s: Initialization failed\n", plugin_name);
            break;
        default:
            printf("Plugin %s: Unknown error %d\n", plugin_name, error_code);
            break;
    }
    
    return error_code;
}
```

**Error Handling Benefits**:
- **Robustness**: Handle all error conditions gracefully
- **Debugging**: Clear error messages for debugging
- **Recovery**: Attempt error recovery when possible
- **Logging**: Log errors for troubleshooting

## 🔧 Best Practices

### 1. Plugin Interface Design
```c
// Good: Clear, minimal interface
typedef struct {
    int (*initialize)(void);
    int (*cleanup)(void);
    int (*get_info)(PluginInfo* info);
    int (*process)(void* data, size_t size);
} PluginInterface;

// Bad: Too many required functions
typedef struct {
    int (*initialize)(void);
    int (*cleanup)(void);
    int (*get_info)(PluginInfo* info);
    int (*process)(void* data, size_t size);
    int (*configure)(const char* config);
    int (*get_status)(void);
    int (*validate)(void* data);
    int (*reset)(void);
    // ... too many functions
} PluginInterface;
```

### 2. Version Compatibility
```c
// Good: Semantic versioning
typedef struct {
    int api_version_major;
    int api_version_minor;
    int api_version_patch;
} PluginVersion;

// Check compatibility
if (plugin->info.api_version_major != PLUGIN_API_VERSION_MAJOR) {
    return -1; // Incompatible
}

// Bad: No version checking
// Load any plugin without version verification
```

### 3. Resource Management
```c
// Good: Proper resource cleanup
int unloadPlugin(PluginHandle* plugin) {
    if (plugin->interface && plugin->interface->cleanup) {
        plugin->interface->cleanup();
    }
    
    if (plugin->handle) {
        dlclose(plugin->handle);
    }
    
    memset(plugin, 0, sizeof(PluginHandle));
    return 0;
}

// Bad: Resource leaks
int unloadPlugin(PluginHandle* plugin) {
    // Forget to call cleanup
    // Forget to close handle
    return 0;
}
```

### 4. Error Handling
```c
// Good: Comprehensive error checking
int loadPlugin(PluginHandle* plugin, const char* filename) {
    if (!plugin || !filename) return -1;
    
    plugin->handle = dlopen(filename, RTLD_LAZY);
    if (!plugin->handle) {
        fprintf(stderr, "Failed to load: %s\n", dlerror());
        return -2;
    }
    
    plugin->interface = dlsym(plugin->handle, "plugin_interface");
    if (!plugin->interface) {
        dlclose(plugin->handle);
        return -3;
    }
    
    return 0;
}

// Bad: No error checking
int loadPlugin(PluginHandle* plugin, const char* filename) {
    plugin->handle = dlopen(filename, RTLD_LAZY);
    plugin->interface = dlsym(plugin->handle, "plugin_interface");
    return 0; // Assume success
}
```

### 5. Thread Safety
```c
// Good: Thread-safe operations
pthread_mutex_t g_plugin_mutex = PTHREAD_MUTEX_INITIALIZER;

int activatePlugin(const char* name) {
    pthread_mutex_lock(&g_plugin_mutex);
    
    // ... activation logic
    
    pthread_mutex_unlock(&g_plugin_mutex);
    return 0;
}

// Bad: Not thread-safe
int activatePlugin(const char* name) {
    // ... activation logic without synchronization
    return 0;
}
```

## ⚠️ Common Pitfalls

### 1. Memory Management
```c
// Wrong: Memory leaks in plugins
int plugin_process(void* data, size_t size) {
    char* buffer = malloc(size * 2); // Allocate memory
    strcpy(buffer, (char*)data);
    strcat(buffer, " processed");
    // Forget to free buffer
    return 0;
}

// Right: Proper memory management
int plugin_process(void* data, size_t size) {
    char* buffer = malloc(size * 2);
    if (!buffer) return -1;
    
    strcpy(buffer, (char*)data);
    strcat(buffer, " processed");
    
    // Use buffer...
    
    free(buffer); // Clean up
    return 0;
}
```

### 2. API Violations
```c
// Wrong: Plugin doesn't implement required interface
// Plugin exports wrong symbol name
void* get_plugin_interface(void) { // Wrong name
    return &my_interface;
}

// Right: Correct interface export
PluginInterface* plugin_interface = &my_interface; // Correct name
```

### 3. Version Mismatches
```c
// Wrong: No version checking
// Load any plugin regardless of API version

// Right: Check API compatibility
if (plugin->info.api_version_major != PLUGIN_API_VERSION_MAJOR) {
    fprintf(stderr, "API version mismatch\n");
    return -1;
}
```

### 4. Error Propagation
```c
// Wrong: Ignore errors from plugins
int processThroughPlugins(void* data, size_t size) {
    for (int i = 0; i < active_count; i++) {
        active_plugins[i]->process(data, size); // Ignore return value
    }
    return 0;
}

// Right: Handle plugin errors
int processThroughPlugins(void* data, size_t size) {
    for (int i = 0; i < active_count; i++) {
        int result = active_plugins[i]->process(data, size);
        if (result != 0) {
            printf("Plugin %d failed: %d\n", i, result);
            return result;
        }
    }
    return 0;
}
```

## 🔧 Real-World Applications

### 1. Web Server Plugin System
```c
// HTTP handler plugin
typedef struct {
    int (*handle_request)(HttpRequest* request, HttpResponse* response);
    int (*get_supported_methods)(char* methods, size_t size);
} HttpPluginInterface;

// Load HTTP handler plugins
int loadHttpHandlers(const char* plugin_dir) {
    // Discover and load HTTP handler plugins
    // Register handlers for different URL patterns
    return 0;
}
```

### 2. Image Processing Pipeline
```c
// Image filter plugin
typedef struct {
    int (*apply_filter)(Image* image, FilterParams* params);
    int (*get_filter_info)(FilterInfo* info);
} ImageFilterInterface;

// Process image through filter pipeline
int processImagePipeline(Image* image, const char** filter_names) {
    for (int i = 0; filter_names[i]; i++) {
        ImageFilterInterface* filter = findFilter(filter_names[i]);
        if (filter) {
            filter->apply_filter(image, NULL);
        }
    }
    return 0;
}
```

### 3. Database Driver System
```c
// Database driver plugin
typedef struct {
    int (*connect)(DatabaseConnection* conn, const char* conn_str);
    int (*execute_query)(DatabaseConnection* conn, const char* query, ResultSet* result);
    int (*disconnect)(DatabaseConnection* conn);
} DatabaseDriverInterface;

// Load database drivers
int loadDatabaseDrivers(const char* driver_dir) {
    // Discover and load database drivers
    // MySQL, PostgreSQL, SQLite, etc.
    return 0;
}
```

## 📚 Further Reading

### Books
- "Plugin Architecture" by Martin Fowler
- "Design Patterns" by Gang of Four
- "Linkers and Loaders" by John Levine

### Topics
- Dynamic linking and loading
- Shared library development
- Component-based architecture
- Extensible system design
- Plugin security considerations

Plugin architecture in C enables extensible, modular systems that can grow and adapt without recompilation. Master these techniques to build flexible, maintainable applications that can be extended through plugins!
