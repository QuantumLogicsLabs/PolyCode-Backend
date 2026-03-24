#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

// =============================================================================
// API DESIGN AND IMPLEMENTATION FUNDAMENTALS
// =============================================================================

#define MAX_API_NAME_LENGTH 64
#define MAX_API_VERSION_LENGTH 16
#define MAX_API_DESCRIPTION_LENGTH 256
#define MAX_API_FUNCTIONS 50
#define MAX_API_PARAMETERS 10

// API versioning
typedef struct {
    int major;
    int minor;
    int patch;
} ApiVersion;

// API status codes
typedef enum {
    API_SUCCESS = 0,
    API_ERROR_INVALID_PARAMETER = -1,
    API_ERROR_OUT_OF_MEMORY = -2,
    API_ERROR_NOT_FOUND = -3,
    API_ERROR_PERMISSION_DENIED = -4,
    API_ERROR_TIMEOUT = -5,
    API_ERROR_INTERNAL = -6
} ApiStatusCode;

// API error information
typedef struct {
    ApiStatusCode code;
    char message[256];
    char details[512];
} ApiError;

// =============================================================================
// API CORE STRUCTURES
// =============================================================================

// API function signature
typedef struct {
    char name[MAX_API_NAME_LENGTH];
    void* function_ptr;
    int parameter_count;
    char parameter_types[MAX_API_PARAMETERS][32];
    char return_type[32];
    char description[MAX_API_DESCRIPTION_LENGTH];
} ApiFunction;

// API context
typedef struct {
    char name[MAX_API_NAME_LENGTH];
    ApiVersion version;
    ApiFunction functions[MAX_API_FUNCTIONS];
    int function_count;
    void* user_data;
    int is_initialized;
    time_t creation_time;
    ApiError last_error;
} ApiContext;

// API parameter
typedef struct {
    char name[64];
    char type[32];
    void* value;
    int is_optional;
    char default_value[64];
} ApiParameter;

// =============================================================================
// ERROR HANDLING
// =============================================================================

// Set API error
void setApiError(ApiContext* context, ApiStatusCode code, const char* message, const char* details) {
    if (context) {
        context->last_error.code = code;
        if (message) {
            strncpy(context->last_error.message, message, sizeof(context->last_error.message) - 1);
            context->last_error.message[sizeof(context->last_error.message) - 1] = '\0';
        }
        if (details) {
            strncpy(context->last_error.details, details, sizeof(context->last_error.details) - 1);
            context->last_error.details[sizeof(context->last_error.details) - 1] = '\0';
        }
    }
}

// Get API error message
const char* getApiErrorMessage(ApiStatusCode code) {
    switch (code) {
        case API_SUCCESS: return "Success";
        case API_ERROR_INVALID_PARAMETER: return "Invalid parameter";
        case API_ERROR_OUT_OF_MEMORY: return "Out of memory";
        case API_ERROR_NOT_FOUND: return "Not found";
        case API_ERROR_PERMISSION_DENIED: return "Permission denied";
        case API_ERROR_TIMEOUT: return "Timeout";
        case API_ERROR_INTERNAL: return "Internal error";
        default: return "Unknown error";
    }
}

// =============================================================================
// API INITIALIZATION AND CLEANUP
// =============================================================================

// Initialize API context
ApiStatusCode initializeApi(ApiContext* context, const char* name, const char* version) {
    if (!context || !name || !version) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    // Clear context
    memset(context, 0, sizeof(ApiContext));
    
    // Set basic information
    strncpy(context->name, name, sizeof(context->name) - 1);
    
    // Parse version string (expected format: "major.minor.patch")
    int major, minor, patch;
    if (sscanf(version, "%d.%d.%d", &major, &minor, &patch) == 3) {
        context->version.major = major;
        context->version.minor = minor;
        context->version.patch = patch;
    } else {
        setApiError(context, API_ERROR_INVALID_PARAMETER, 
                    "Invalid version format", "Expected format: major.minor.patch");
        return API_ERROR_INVALID_PARAMETER;
    }
    
    context->creation_time = time(NULL);
    context->is_initialized = 1;
    
    return API_SUCCESS;
}

// Cleanup API context
void cleanupApi(ApiContext* context) {
    if (context && context->is_initialized) {
        // Free any allocated resources
        for (int i = 0; i < context->function_count; i++) {
            // Free function-specific resources if needed
        }
        
        memset(context, 0, sizeof(ApiContext));
    }
}

// =============================================================================
// API FUNCTION REGISTRATION
// =============================================================================

// Register API function
ApiStatusCode registerApiFunction(ApiContext* context, const char* name, void* function_ptr,
                                 const char* return_type, const char* description) {
    if (!context || !name || !function_ptr || !context->is_initialized) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    if (context->function_count >= MAX_API_FUNCTIONS) {
        setApiError(context, API_ERROR_INTERNAL, 
                    "Maximum functions reached", "Cannot register more functions");
        return API_ERROR_INTERNAL;
    }
    
    ApiFunction* func = &context->functions[context->function_count];
    
    strncpy(func->name, name, sizeof(func->name) - 1);
    func->function_ptr = function_ptr;
    func->parameter_count = 0;
    
    if (return_type) {
        strncpy(func->return_type, return_type, sizeof(func->return_type) - 1);
    }
    
    if (description) {
        strncpy(func->description, description, sizeof(func->description) - 1);
    }
    
    context->function_count++;
    
    return API_SUCCESS;
}

// Add parameter to function
ApiStatusCode addFunctionParameter(ApiContext* context, const char* function_name,
                                    const char* param_name, const char* param_type,
                                    int is_optional, const char* default_value) {
    if (!context || !function_name || !param_name || !param_type) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    // Find the function
    int func_index = -1;
    for (int i = 0; i < context->function_count; i++) {
        if (strcmp(context->functions[i].name, function_name) == 0) {
            func_index = i;
            break;
        }
    }
    
    if (func_index == -1) {
        setApiError(context, API_ERROR_NOT_FOUND, 
                    "Function not found", function_name);
        return API_ERROR_NOT_FOUND;
    }
    
    ApiFunction* func = &context->functions[func_index];
    
    if (func->parameter_count >= MAX_API_PARAMETERS) {
        setApiError(context, API_ERROR_INTERNAL, 
                    "Maximum parameters reached", "Cannot add more parameters");
        return API_ERROR_INTERNAL;
    }
    
    int param_index = func->parameter_count;
    strncpy(func->parameter_types[param_index], param_type, sizeof(func->parameter_types[param_index]) - 1);
    func->parameter_count++;
    
    return API_SUCCESS;
}

// =============================================================================
// API FUNCTION CALLING
// =============================================================================

// Call API function
ApiStatusCode callApiFunction(ApiContext* context, const char* function_name,
                             ApiParameter* parameters, int parameter_count, void* result) {
    if (!context || !function_name || !context->is_initialized) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    // Find the function
    int func_index = -1;
    for (int i = 0; i < context->function_count; i++) {
        if (strcmp(context->functions[i].name, function_name) == 0) {
            func_index = i;
            break;
        }
    }
    
    if (func_index == -1) {
        setApiError(context, API_ERROR_NOT_FOUND, 
                    "Function not found", function_name);
        return API_ERROR_NOT_FOUND;
    }
    
    ApiFunction* func = &context->functions[func_index];
    
    // Check parameter count
    if (parameter_count != func->parameter_count) {
        setApiError(context, API_ERROR_INVALID_PARAMETER, 
                    "Parameter count mismatch", "Expected different number of parameters");
        return API_ERROR_INVALID_PARAMETER;
    }
    
    // Call the function (simplified - in real implementation would use function pointers with proper signatures)
    // This is a placeholder for actual function calling logic
    printf("Calling function: %s\n", func->name);
    
    return API_SUCCESS;
}

// =============================================================================
// API VERSION MANAGEMENT
// =============================================================================

// Compare API versions
int compareApiVersion(const ApiVersion* v1, const ApiVersion* v2) {
    if (v1->major != v2->major) {
        return v1->major - v2->major;
    }
    if (v1->minor != v2->minor) {
        return v1->minor - v2->minor;
    }
    return v1->patch - v2->patch;
}

// Check API compatibility
int isApiCompatible(const ApiVersion* required, const ApiVersion* available) {
    // Major version must match
    if (required->major != available->major) {
        return 0;
    }
    
    // Available minor version must be >= required
    if (available->minor < required->minor) {
        return 0;
    }
    
    // If minor versions match, patch must be >= required
    if (available->minor == required->minor && available->patch < required->patch) {
        return 0;
    }
    
    return 1;
}

// Get API version string
void getApiVersionString(const ApiVersion* version, char* buffer, size_t buffer_size) {
    snprintf(buffer, buffer_size, "%d.%d.%d", version->major, version->minor, version->patch);
}

// =============================================================================
// API DOCUMENTATION
// =============================================================================

// Print API documentation
void printApiDocumentation(ApiContext* context) {
    if (!context || !context->is_initialized) {
        printf("API context not initialized\n");
        return;
    }
    
    printf("=== API Documentation ===\n");
    printf("Name: %s\n", context->name);
    
    char version_str[32];
    getApiVersionString(&context->version, version_str, sizeof(version_str));
    printf("Version: %s\n", version_str);
    
    printf("Functions: %d\n", context->function_count);
    printf("Created: %s", ctime(&context->creation_time));
    
    printf("\n=== Functions ===\n");
    for (int i = 0; i < context->function_count; i++) {
        ApiFunction* func = &context->functions[i];
        printf("\n%s:\n", func->name);
        printf("  Return: %s\n", func->return_type);
        printf("  Parameters: %d\n", func->parameter_count);
        printf("  Description: %s\n", func->description);
        
        if (func->parameter_count > 0) {
            printf("  Parameter types:\n");
            for (int j = 0; j < func->parameter_count; j++) {
                printf("    [%d] %s\n", j + 1, func->parameter_types[j]);
            }
        }
    }
}

// =============================================================================
// API VALIDATION
// =============================================================================

// Validate API context
ApiStatusCode validateApiContext(ApiContext* context) {
    if (!context) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    if (!context->is_initialized) {
        setApiError(context, API_ERROR_INTERNAL, 
                    "API not initialized", "Call initializeApi() first");
        return API_ERROR_INTERNAL;
    }
    
    if (strlen(context->name) == 0) {
        setApiError(context, API_ERROR_INTERNAL, 
                    "Invalid API name", "API name cannot be empty");
        return API_ERROR_INTERNAL;
    }
    
    // Validate version
    if (context->version.major < 0 || context->version.minor < 0 || context->version.patch < 0) {
        setApiError(context, API_ERROR_INTERNAL, 
                    "Invalid version", "Version numbers must be non-negative");
        return API_ERROR_INTERNAL;
    }
    
    return API_SUCCESS;
}

// =============================================================================
// SAMPLE API IMPLEMENTATION: STRING PROCESSING API
// =============================================================================

// String processing API functions
typedef struct {
    char* buffer;
    size_t capacity;
    size_t length;
} StringBuffer;

// Initialize string buffer
ApiStatusCode stringBufferInit(StringBuffer* buffer, size_t capacity) {
    if (!buffer || capacity == 0) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    buffer->buffer = malloc(capacity);
    if (!buffer->buffer) {
        return API_ERROR_OUT_OF_MEMORY;
    }
    
    buffer->capacity = capacity;
    buffer->length = 0;
    buffer->buffer[0] = '\0';
    
    return API_SUCCESS;
}

// Append to string buffer
ApiStatusCode stringBufferAppend(StringBuffer* buffer, const char* text) {
    if (!buffer || !text || !buffer->buffer) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    size_t text_len = strlen(text);
    
    if (buffer->length + text_len >= buffer->capacity) {
        return API_ERROR_OUT_OF_MEMORY;
    }
    
    strcat(buffer->buffer + buffer->length, text);
    buffer->length += text_len;
    
    return API_SUCCESS;
}

// Clear string buffer
ApiStatusCode stringBufferClear(StringBuffer* buffer) {
    if (!buffer || !buffer->buffer) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    buffer->length = 0;
    buffer->buffer[0] = '\0';
    
    return API_SUCCESS;
}

// Free string buffer
void stringBufferFree(StringBuffer* buffer) {
    if (buffer && buffer->buffer) {
        free(buffer->buffer);
        buffer->buffer = NULL;
        buffer->capacity = 0;
        buffer->length = 0;
    }
}

// Get string buffer content
const char* stringBufferGetContent(StringBuffer* buffer) {
    if (!buffer || !buffer->buffer) {
        return NULL;
    }
    
    return buffer->buffer;
}

// Get string buffer length
size_t stringBufferGetLength(StringBuffer* buffer) {
    if (!buffer) {
        return 0;
    }
    
    return buffer->length;
}

// =============================================================================
// SAMPLE API IMPLEMENTATION: MATH API
// =============================================================================

// Math API functions
ApiStatusCode mathAdd(int a, int b, int* result) {
    if (!result) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    *result = a + b;
    return API_SUCCESS;
}

ApiStatusCode mathMultiply(int a, int b, int* result) {
    if (!result) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    *result = a * b;
    return API_SUCCESS;
}

ApiStatusCode mathDivide(int a, int b, int* result) {
    if (!result || b == 0) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    *result = a / b;
    return API_SUCCESS;
}

ApiStatusCode mathPower(int base, int exponent, int* result) {
    if (!result || exponent < 0) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    *result = 1;
    for (int i = 0; i < exponent; i++) {
        *result *= base;
    }
    
    return API_SUCCESS;
}

ApiStatusCode mathFactorial(int n, int* result) {
    if (!result || n < 0) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    if (n > 12) { // Prevent overflow for int
        return API_ERROR_INVALID_PARAMETER;
    }
    
    *result = 1;
    for (int i = 2; i <= n; i++) {
        *result *= i;
    }
    
    return API_SUCCESS;
}

// =============================================================================
// SAMPLE API IMPLEMENTATION: FILE API
// =============================================================================

// File API functions
typedef struct {
    FILE* file;
    char filename[256];
    char mode[16];
    int is_open;
} FileHandle;

// Open file
ApiStatusCode fileOpen(FileHandle* handle, const char* filename, const char* mode) {
    if (!handle || !filename || !mode) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    handle->file = fopen(filename, mode);
    if (!handle->file) {
        setApiError(NULL, API_ERROR_NOT_FOUND, 
                    "File not found", filename);
        return API_ERROR_NOT_FOUND;
    }
    
    strncpy(handle->filename, filename, sizeof(handle->filename) - 1);
    strncpy(handle->mode, mode, sizeof(handle->mode) - 1);
    handle->is_open = 1;
    
    return API_SUCCESS;
}

// Close file
ApiStatusCode fileClose(FileHandle* handle) {
    if (!handle || !handle->is_open || !handle->file) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    fclose(handle->file);
    handle->file = NULL;
    handle->is_open = 0;
    
    return API_SUCCESS;
}

// Read file
ApiStatusCode fileRead(FileHandle* handle, char* buffer, size_t size, size_t* bytes_read) {
    if (!handle || !handle->is_open || !handle->file || !buffer) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    size_t read = fread(buffer, 1, size, handle->file);
    
    if (bytes_read) {
        *bytes_read = read;
    }
    
    return (ferror(handle->file) == 0) ? API_SUCCESS : API_ERROR_INTERNAL;
}

// Write file
ApiStatusCode fileWrite(FileHandle* handle, const char* buffer, size_t size, size_t* bytes_written) {
    if (!handle || !handle->is_open || !handle->file || !buffer) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    size_t written = fwrite(buffer, 1, size, handle->file);
    
    if (bytes_written) {
        *bytes_written = written;
    }
    
    return (ferror(handle->file) == 0) ? API_SUCCESS : API_ERROR_INTERNAL;
}

// Get file size
ApiStatusCode fileSize(FileHandle* handle, long* size) {
    if (!handle || !handle->is_open || !handle->file || !size) {
        return API_ERROR_INVALID_PARAMETER;
    }
    
    long current_pos = ftell(handle->file);
    fseek(handle->file, 0, SEEK_END);
    *size = ftell(handle->file);
    fseek(handle->file, current_pos, SEEK_SET);
    
    return API_SUCCESS;
}

// =============================================================================
// API USAGE EXAMPLES
// =============================================================================

// Demonstrate string processing API
void demonstrateStringApi() {
    printf("=== STRING PROCESSING API DEMO ===\n");
    
    StringBuffer buffer;
    
    // Initialize buffer
    ApiStatusCode status = stringBufferInit(&buffer, 1024);
    if (status != API_SUCCESS) {
        printf("Failed to initialize string buffer: %s\n", getApiErrorMessage(status));
        return;
    }
    
    // Append strings
    stringBufferAppend(&buffer, "Hello, ");
    stringBufferAppend(&buffer, "World! ");
    stringBufferAppend(&buffer, "This is a ");
    stringBufferAppend(&buffer, "string processing API.");
    
    // Get content and length
    printf("Content: %s\n", stringBufferGetContent(&buffer));
    printf("Length: %zu\n", stringBufferGetLength(&buffer));
    
    // Clear and reuse
    stringBufferClear(&buffer);
    stringBufferAppend(&buffer, "Buffer cleared and reused!");
    printf("After clear: %s\n", stringBufferGetContent(&buffer));
    
    // Cleanup
    stringBufferFree(&buffer);
}

// Demonstrate math API
void demonstrateMathApi() {
    printf("\n=== MATH API DEMO ===\n");
    
    int result;
    
    // Addition
    if (mathAdd(10, 5, &result) == API_SUCCESS) {
        printf("10 + 5 = %d\n", result);
    }
    
    // Multiplication
    if (mathMultiply(6, 7, &result) == API_SUCCESS) {
        printf("6 * 7 = %d\n", result);
    }
    
    // Division
    if (mathDivide(20, 4, &result) == API_SUCCESS) {
        printf("20 / 4 = %d\n", result);
    }
    
    // Power
    if (mathPower(2, 8, &result) == API_SUCCESS) {
        printf("2^8 = %d\n", result);
    }
    
    // Factorial
    if (mathFactorial(5, &result) == API_SUCCESS) {
        printf("5! = %d\n", result);
    }
}

// Demonstrate file API
void demonstrateFileApi() {
    printf("\n=== FILE API DEMO ===\n");
    
    FileHandle file;
    
    // Write to file
    if (fileOpen(&file, "test_api.txt", "w") == API_SUCCESS) {
        const char* content = "This is a test file for the File API.\nSecond line of content.\n";
        size_t bytes_written;
        
        if (fileWrite(&file, content, strlen(content), &bytes_written) == API_SUCCESS) {
            printf("Wrote %zu bytes to file\n", bytes_written);
        }
        
        fileClose(&file);
    }
    
    // Read from file
    if (fileOpen(&file, "test_api.txt", "r") == API_SUCCESS) {
        long size;
        if (fileSize(&file, &size) == API_SUCCESS) {
            printf("File size: %ld bytes\n", size);
        }
        
        char buffer[256];
        size_t bytes_read;
        
        if (fileRead(&file, buffer, sizeof(buffer) - 1, &bytes_read) == API_SUCCESS) {
            buffer[bytes_read] = '\0';
            printf("Read %zu bytes: %s\n", bytes_read, buffer);
        }
        
        fileClose(&file);
    }
    
    // Clean up
    remove("test_api.txt");
}

// Demonstrate API context management
void demonstrateApiContext() {
    printf("\n=== API CONTEXT DEMO ===\n");
    
    ApiContext context;
    
    // Initialize API
    ApiStatusCode status = initializeApi(&context, "SampleAPI", "1.2.3");
    if (status != API_SUCCESS) {
        printf("Failed to initialize API: %s\n", getApiErrorMessage(status));
        return;
    }
    
    printf("API initialized successfully!\n");
    
    // Register some functions
    registerApiFunction(&context, "stringBufferInit", (void*)stringBufferInit, "ApiStatusCode", "Initialize string buffer");
    registerApiFunction(&context, "mathAdd", (void*)mathAdd, "ApiStatusCode", "Add two integers");
    registerApiFunction(&context, "fileOpen", (void*)fileOpen, "ApiStatusCode", "Open a file");
    
    // Add parameters
    addFunctionParameter(&context, "stringBufferInit", "buffer", "StringBuffer*", 0, NULL);
    addFunctionParameter(&context, "stringBufferInit", "capacity", "size_t", 0, NULL);
    
    addFunctionParameter(&context, "mathAdd", "a", "int", 0, NULL);
    addFunctionParameter(&context, "mathAdd", "b", "int", 0, NULL);
    addFunctionParameter(&context, "mathAdd", "result", "int*", 0, NULL);
    
    // Print documentation
    printApiDocumentation(&context);
    
    // Validate API
    status = validateApiContext(&context);
    if (status == API_SUCCESS) {
        printf("API validation passed!\n");
    } else {
        printf("API validation failed: %s\n", getApiErrorMessage(status));
    }
    
    // Cleanup
    cleanupApi(&context);
}

// Demonstrate version management
void demonstrateVersionManagement() {
    printf("\n=== VERSION MANAGEMENT DEMO ===\n");
    
    ApiVersion v1 = {1, 2, 3};
    ApiVersion v2 = {1, 3, 0};
    ApiVersion v3 = {2, 0, 0};
    
    // Compare versions
    printf("Comparing 1.2.3 and 1.3.0: %d\n", compareApiVersion(&v1, &v2));
    printf("Comparing 1.3.0 and 2.0.0: %d\n", compareApiVersion(&v2, &v3));
    
    // Check compatibility
    printf("1.2.3 compatible with 1.3.0: %s\n", 
           isApiCompatible(&v1, &v2) ? "Yes" : "No");
    printf("1.2.3 compatible with 2.0.0: %s\n", 
           isApiCompatible(&v1, &v3) ? "Yes" : "No");
    
    // Version strings
    char version_str[32];
    getApiVersionString(&v1, version_str, sizeof(version_str));
    printf("Version string: %s\n", version_str);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("API Design and Implementation Examples\n");
    printf("====================================\n\n");
    
    // Run all demonstrations
    demonstrateStringApi();
    demonstrateMathApi();
    demonstrateFileApi();
    demonstrateApiContext();
    demonstrateVersionManagement();
    
    printf("\nAll API design and implementation examples demonstrated!\n");
    printf("Key takeaways:\n");
    printf("- Consistent error handling across all API functions\n");
    printf("- Clear parameter validation and type checking\n");
    printf("- Comprehensive documentation and version management\n");
    printf("- Modular design with separate contexts for different APIs\n");
    printf("- Proper resource cleanup and memory management\n");
    
    return 0;
}
