#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <dlfcn.h> // For dynamic loading (Unix-like systems)
#include <dirent.h> // For directory operations

// =============================================================================
// PLUGIN ARCHITECTURE FUNDAMENTALS
// =============================================================================

#define MAX_PLUGIN_NAME_LENGTH 64
#define MAX_PLUGIN_VERSION_LENGTH 16
#define MAX_PLUGIN_DESCRIPTION_LENGTH 256
#define MAX_PLUGINS 100
#define MAX_PLUGIN_FUNCTIONS 50

// Plugin API version
#define PLUGIN_API_VERSION_MAJOR 1
#define PLUGIN_API_VERSION_MINOR 0

// Plugin status
typedef enum {
    PLUGIN_STATUS_UNLOADED = 0,
    PLUGIN_STATUS_LOADED = 1,
    PLUGIN_STATUS_ACTIVE = 2,
    PLUGIN_STATUS_ERROR = 3
} PluginStatus;

// Plugin type
typedef enum {
    PLUGIN_TYPE_UNKNOWN = 0,
    PLUGIN_TYPE_FILTER = 1,
    PLUGIN_TYPE_PROCESSOR = 2,
    PLUGIN_TYPE_OUTPUT = 3,
    PLUGIN_TYPE_INPUT = 4,
    PLUGIN_TYPE_UTILITY = 5
} PluginType;

// =============================================================================
// PLUGIN API STRUCTURES
// =============================================================================

// Plugin information structure
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

// Plugin function descriptor
typedef struct {
    char name[64];
    void* function_ptr;
    char description[256];
    char parameters[256];
} PluginFunction;

// Plugin interface (what plugins must implement)
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

// Plugin handle structure
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

// Plugin manager
typedef struct {
    PluginHandle plugins[MAX_PLUGINS];
    int plugin_count;
    char plugin_directory[512];
    int auto_discover;
    PluginInterface* active_plugins[MAX_PLUGINS];
    int active_count;
} PluginManager;

// =============================================================================
// PLUGIN MANAGER IMPLEMENTATION
// =============================================================================

static PluginManager g_plugin_manager = {0};

// Initialize plugin manager
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

// Cleanup plugin manager
void cleanupPluginManager() {
    // Unload all plugins
    for (int i = 0; i < g_plugin_manager.plugin_count; i++) {
        unloadPlugin(&g_plugin_manager.plugins[i]);
    }
    
    memset(&g_plugin_manager, 0, sizeof(PluginManager));
}

// =============================================================================
// PLUGIN LOADING AND UNLOADING
// =============================================================================

// Load a plugin
int loadPlugin(PluginHandle* plugin, const char* filename) {
    if (!plugin || !filename) {
        return -1; // Invalid parameters
    }
    
    // Initialize plugin handle
    memset(plugin, 0, sizeof(PluginHandle));
    strncpy(plugin->filename, filename, sizeof(plugin->filename) - 1);
    plugin->filename[sizeof(plugin->filename) - 1] = '\0';
    
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
    } else {
        strcpy(plugin->info.name, "Unknown");
        strcpy(plugin->info.version, "1.0.0");
        plugin->info.type = PLUGIN_TYPE_UNKNOWN;
    }
    
    // Check API compatibility
    if (plugin->info.api_version_major != PLUGIN_API_VERSION_MAJOR) {
        fprintf(stderr, "Plugin %s API version mismatch (expected %d.x, got %d.%d)\n",
                filename, PLUGIN_API_VERSION_MAJOR, 
                plugin->info.api_version_major, plugin->info.api_version_minor);
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

// Unload a plugin
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

// =============================================================================
// PLUGIN DISCOVERY
// =============================================================================

// Discover plugins in directory
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

// =============================================================================
// PLUGIN MANAGEMENT
// =============================================================================

// Find plugin by name
PluginHandle* findPlugin(const char* name) {
    for (int i = 0; i < g_plugin_manager.plugin_count; i++) {
        if (strcmp(g_plugin_manager.plugins[i].info.name, name) == 0) {
            return &g_plugin_manager.plugins[i];
        }
    }
    return NULL;
}

// Activate a plugin
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

// Deactivate a plugin
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

// =============================================================================
// PLUGIN EXECUTION
// =============================================================================

// Execute plugin function
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

// Process data through all active plugins
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

// =============================================================================
// PLUGIN INFORMATION AND UTILITIES
// =============================================================================

// Get plugin type name
const char* getPluginTypeName(PluginType type) {
    switch (type) {
        case PLUGIN_TYPE_FILTER: return "Filter";
        case PLUGIN_TYPE_PROCESSOR: return "Processor";
        case PLUGIN_TYPE_OUTPUT: return "Output";
        case PLUGIN_TYPE_INPUT: return "Input";
        case PLUGIN_TYPE_UTILITY: return "Utility";
        default: return "Unknown";
    }
}

// Get plugin status name
const char* getPluginStatusName(PluginStatus status) {
    switch (status) {
        case PLUGIN_STATUS_UNLOADED: return "Unloaded";
        case PLUGIN_STATUS_LOADED: return "Loaded";
        case PLUGIN_STATUS_ACTIVE: return "Active";
        case PLUGIN_STATUS_ERROR: return "Error";
        default: return "Unknown";
    }
}

// Print plugin information
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

// List all plugins
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

// =============================================================================
// SAMPLE PLUGIN IMPLEMENTATIONS
// =============================================================================

// Sample Filter Plugin
static PluginInfo filter_plugin_info = {
    .name = "Uppercase Filter",
    .version = "1.0.0",
    .author = "Plugin Developer",
    .description = "Converts text to uppercase",
    .type = PLUGIN_TYPE_FILTER,
    .api_version_major = 1,
    .api_version_minor = 0,
    .build_time = 0
};

static int filter_initialized = 0;

int filter_initialize() {
    printf("Uppercase Filter: Initialized\n");
    filter_initialized = 1;
    return 0;
}

int filter_cleanup() {
    printf("Uppercase Filter: Cleaned up\n");
    filter_initialized = 0;
    return 0;
}

int filter_get_info(PluginInfo* info) {
    if (!info) return -1;
    *info = filter_plugin_info;
    return 0;
}

int filter_process(void* data, size_t size) {
    if (!data || !filter_initialized) return -1;
    
    char* text = (char*)data;
    for (size_t i = 0; i < size && text[i] != '\0'; i++) {
        if (text[i] >= 'a' && text[i] <= 'z') {
            text[i] = text[i] - 'a' + 'A';
        }
    }
    
    printf("Uppercase Filter: Processed %zu bytes\n", size);
    return 0;
}

int filter_configure(const char* config) {
    printf("Uppercase Filter: Configured with: %s\n", config ? config : "none");
    return 0;
}

int filter_get_status() {
    return filter_initialized ? 1 : 0;
}

PluginFunction filter_functions[] = {
    {"process", filter_process, "Process text data", "data: char*, size: size_t"},
    {"configure", filter_configure, "Configure filter", "config: const char*"},
    {"get_status", filter_get_status, "Get filter status", "void"}
};

PluginFunction* filter_get_functions(int* count) {
    if (count) *count = sizeof(filter_functions) / sizeof(filter_functions[0]);
    return filter_functions;
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

// Sample Processor Plugin
static PluginInfo processor_plugin_info = {
    .name = "Word Counter",
    .version = "1.0.0",
    .author = "Plugin Developer",
    .description = "Counts words in text",
    .type = PLUGIN_TYPE_PROCESSOR,
    .api_version_major = 1,
    .api_version_minor = 0,
    .build_time = 0
};

static int processor_initialized = 0;
static int word_count = 0;

int processor_initialize() {
    printf("Word Counter: Initialized\n");
    processor_initialized = 1;
    word_count = 0;
    return 0;
}

int processor_cleanup() {
    printf("Word Counter: Cleaned up (total words counted: %d)\n", word_count);
    processor_initialized = 0;
    return 0;
}

int processor_get_info(PluginInfo* info) {
    if (!info) return -1;
    *info = processor_plugin_info;
    return 0;
}

int processor_process(void* data, size_t size) {
    if (!data || !processor_initialized) return -1;
    
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
    printf("Word Counter: Found %d words in this text (total: %d)\n", words, word_count);
    
    return 0;
}

int processor_configure(const char* config) {
    printf("Word Counter: Configured with: %s\n", config ? config : "none");
    return 0;
}

int processor_get_status() {
    return processor_initialized ? 1 : 0;
}

PluginFunction processor_functions[] = {
    {"process", processor_process, "Process text and count words", "data: char*, size: size_t"},
    {"configure", processor_configure, "Configure processor", "config: const char*"},
    {"get_status", processor_get_status, "Get processor status", "void"}
};

PluginFunction* processor_get_functions(int* count) {
    if (count) *count = sizeof(processor_functions) / sizeof(processor_functions[0]);
    return processor_functions;
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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstratePluginLoading() {
    printf("=== PLUGIN LOADING DEMO ===\n");
    
    // Initialize plugin manager
    initPluginManager("./plugins", 0); // Don't auto-discover for this demo
    
    // Load sample plugins (in real scenario, these would be separate .so files)
    printf("Loading sample plugins...\n");
    
    // Create plugin handles for our sample plugins
    PluginHandle filter_plugin = {0};
    PluginHandle processor_plugin = {0};
    
    // Simulate loading (in real scenario, would load from .so files)
    filter_plugin.handle = (void*)0x1234; // Fake handle
    filter_plugin.interface = &filter_plugin_interface;
    filter_plugin.info = filter_plugin_info;
    filter_plugin.status = PLUGIN_STATUS_LOADED;
    filter_plugin.load_time = time(NULL);
    filter_plugin.function_count = sizeof(filter_functions) / sizeof(filter_functions[0]);
    memcpy(filter_plugin.functions, filter_functions, sizeof(filter_functions));
    
    processor_plugin.handle = (void*)0x5678; // Fake handle
    processor_plugin.interface = &processor_plugin_interface;
    processor_plugin.info = processor_plugin_info;
    processor_plugin.status = PLUGIN_STATUS_LOADED;
    processor_plugin.load_time = time(NULL);
    processor_plugin.function_count = sizeof(processor_functions) / sizeof(processor_functions[0]);
    memcpy(processor_plugin.functions, processor_functions, sizeof(processor_functions));
    
    // Add to manager
    g_plugin_manager.plugins[g_plugin_manager.plugin_count++] = filter_plugin;
    g_plugin_manager.plugins[g_plugin_manager.plugin_count++] = processor_plugin;
    
    printf("Loaded %d plugins\n", g_plugin_manager.plugin_count);
}

void demonstratePluginActivation() {
    printf("\n=== PLUGIN ACTIVATION DEMO ===\n");
    
    // Activate plugins
    activatePlugin("Uppercase Filter");
    activatePlugin("Word Counter");
    
    // List plugins
    listPlugins();
}

void demonstratePluginProcessing() {
    printf("\n=== PLUGIN PROCESSING DEMO ===\n");
    
    char test_data[] = "Hello world! This is a test message for our plugins.";
    
    printf("Original text: %s\n", test_data);
    
    // Process through active plugins
    printf("Processing through plugins...\n");
    
    // First, uppercase filter
    PluginHandle* filter = findPlugin("Uppercase Filter");
    if (filter && filter->status == PLUGIN_STATUS_ACTIVE) {
        filter_process(test_data, strlen(test_data));
        printf("After filter: %s\n", test_data);
    }
    
    // Then, word counter
    PluginHandle* processor = findPlugin("Word Counter");
    if (processor && processor->status == PLUGIN_STATUS_ACTIVE) {
        processor_process(test_data, strlen(test_data));
    }
    
    // Process through all active plugins
    printf("\nProcessing through all active plugins:\n");
    char test_data2[] = "Another test message with multiple words.";
    printf("Original: %s\n", test_data2);
    
    processThroughPlugins(test_data2, strlen(test_data2));
    printf("Final: %s\n", test_data2);
}

void demonstratePluginConfiguration() {
    printf("\n=== PLUGIN CONFIGURATION DEMO ===\n");
    
    PluginHandle* filter = findPlugin("Uppercase Filter");
    if (filter) {
        if (filter->interface->configure) {
            filter->interface->configure("case=upper, preserve_numbers=true");
        }
    }
    
    PluginHandle* processor = findPlugin("Word Counter");
    if (processor) {
        if (processor->interface->configure) {
            processor->interface->configure("min_word_length=1, ignore_numbers=false");
        }
    }
}

void demonstratePluginInformation() {
    printf("\n=== PLUGIN INFORMATION DEMO ===\n");
    
    for (int i = 0; i < g_plugin_manager.plugin_count; i++) {
        PluginHandle* plugin = &g_plugin_manager.plugins[i];
        printPluginInfo(plugin);
    }
}

void demonstratePluginFunctions() {
    printf("\n=== PLUGIN FUNCTIONS DEMO ===\n");
    
    PluginHandle* filter = findPlugin("Uppercase Filter");
    if (filter) {
        printf("Functions available in %s:\n", filter->info.name);
        for (int i = 0; i < filter->function_count; i++) {
            PluginFunction* func = &filter->functions[i];
            printf("  %s: %s\n", func->name, func->description);
            printf("    Parameters: %s\n", func->parameters);
        }
    }
}

void demonstratePluginDeactivation() {
    printf("\n=== PLUGIN DEACTIVATION DEMO ===\n");
    
    // Deactivate one plugin
    deactivatePlugin("Uppercase Filter");
    
    // List plugins again
    listPlugins();
    
    // Try to process data
    char test_data[] = "Test message after deactivation";
    printf("Processing: %s\n", test_data);
    processThroughPlugins(test_data, strlen(test_data));
}

void demonstratePluginUnloading() {
    printf("\n=== PLUGIN UNLOADING DEMO ===\n");
    
    // Unload all plugins
    for (int i = g_plugin_manager.plugin_count - 1; i >= 0; i--) {
        PluginHandle* plugin = &g_plugin_manager.plugins[i];
        printf("Unloading plugin: %s\n", plugin->info.name);
        
        // Simulate cleanup
        if (plugin->interface && plugin->interface->cleanup) {
            plugin->interface->cleanup();
        }
        
        plugin->status = PLUGIN_STATUS_UNLOADED;
    }
    
    g_plugin_manager.plugin_count = 0;
    g_plugin_manager.active_count = 0;
    
    printf("All plugins unloaded\n");
}

void demonstratePluginErrorHandling() {
    printf("\n=== PLUGIN ERROR HANDLING DEMO ===\n");
    
    // Try to find non-existent plugin
    PluginHandle* non_existent = findPlugin("Non-existent Plugin");
    if (!non_existent) {
        printf("Correctly handled non-existent plugin\n");
    }
    
    // Try to activate non-existent plugin
    int result = activatePlugin("Non-existent Plugin");
    if (result != 0) {
        printf("Correctly handled activation error: %d\n", result);
    }
    
    // Try to execute function on non-existent plugin
    result = executePluginFunction("Non-existent Plugin", "process", NULL, 0);
    if (result != 0) {
        printf("Correctly handled execution error: %d\n", result);
    }
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Plugin Architecture Examples\n");
    printf("===========================\n\n");
    
    // Run all demonstrations
    demonstratePluginLoading();
    demonstratePluginActivation();
    demonstratePluginProcessing();
    demonstratePluginConfiguration();
    demonstratePluginInformation();
    demonstratePluginFunctions();
    demonstratePluginDeactivation();
    demonstratePluginUnloading();
    demonstratePluginErrorHandling();
    
    // Cleanup
    cleanupPluginManager();
    
    printf("\nAll plugin architecture examples demonstrated!\n");
    printf("Key takeaways:\n");
    printf("- Dynamic plugin loading and unloading\n");
    printf("- Plugin interface standardization\n");
    printf("- Plugin discovery and management\n");
    printf("- Plugin activation and deactivation\n");
    printf("- Plugin configuration and status monitoring\n");
    printf("- Error handling for plugin operations\n");
    printf("- Function discovery and execution\n");
    
    return 0;
}
