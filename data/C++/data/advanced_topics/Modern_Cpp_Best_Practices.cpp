// Module 15: Modern C++ and Best Practices - Real-Life Examples
// This file demonstrates practical applications of modern C++ features and best practices

#include <iostream>
#include <string>
#include <vector>
#include <memory>
#include <optional>
#include <variant>
#include <tuple>
#include <algorithm>
#include <ranges>
#include <concepts>
#include <format>
#include <chrono>
#include <functional>
#include <future>
#include <thread>
#include <mutex>
#include <atomic>

// Example 1: Modern C++ Concepts and Templates
template<typename T>
concept Numeric = std::is_arithmetic_v<T>;

template<Numeric T>
class Calculator {
private:
    T value;
    
public:
    constexpr Calculator(T initialValue) : value(initialValue) {}
    
    constexpr Calculator& add(T other) {
        value += other;
        return *this;
    }
    
    constexpr Calculator& subtract(T other) {
        value -= other;
        return *this;
    }
    
    constexpr Calculator& multiply(T other) {
        value *= other;
        return *this;
    }
    
    constexpr Calculator& divide(T other) {
        if (other != 0) {
            value /= other;
        }
        return *this;
    }
    
    [[nodiscard]] constexpr T getValue() const {
        return value;
    }
    
    // C++20 spaceship operator
    auto operator<=>(const Calculator& other) const {
        return value <=> other.value;
    }
};

// Example 2: Modern C++ File System Operations
class ModernFileProcessor {
private:
    std::string filename;
    std::optional<std::string> content;
    
public:
    explicit ModernFileProcessor(std::string_view name) : filename(name) {}
    
    [[nodiscard]] std::optional<std::string> readContent() {
        if (content.has_value()) {
            return content;
        }
        
        std::ifstream file(filename);
        if (!file.is_open()) {
            return std::nullopt;
        }
        
        std::string result((std::istreambuf_iterator<char>(file)),
                          std::istreambuf_iterator<char>());
        
        content = result;
        return content;
    }
    
    [[nodiscard]] bool writeContent(std::string_view newContent) {
        std::ofstream file(filename);
        if (!file.is_open()) {
            return false;
        }
        
        file << newContent;
        content = std::string(newContent);
        return true;
    }
    
    [[nodiscard]] std::string getFilename() const {
        return filename;
    }
};

// Example 3: Modern C++ Event System
class EventSystem {
private:
    using EventCallback = std::function<void(const std::string&)>;
    std::vector<EventCallback> listeners;
    std::mutex listenersMutex;
    
public:
    void addListener(EventCallback callback) {
        std::lock_guard<std::mutex> lock(listenersMutex);
        listeners.push_back(std::move(callback));
    }
    
    void emitEvent(std::string_view eventName) {
        std::lock_guard<std::mutex> lock(listenersMutex);
        for (const auto& listener : listeners) {
            listener(std::string(eventName));
        }
    }
    
    void emitEventAsync(std::string_view eventName) {
        std::thread([this, eventName]() {
            emitEvent(eventName);
        }).detach();
    }
};

// Example 4: Modern C++ Configuration Manager
class ConfigurationManager {
private:
    using ConfigValue = std::variant<int, double, std::string, bool>;
    std::map<std::string, ConfigValue> config;
    std::string configFilename;
    mutable std::shared_mutex configMutex;
    
public:
    explicit ConfigurationManager(std::string_view filename) : configFilename(filename) {
        loadConfig();
    }
    
    template<typename T>
    [[nodiscard]] T getValue(std::string_view key, const T& defaultValue = T{}) const {
        std::shared_lock<std::shared_mutex> lock(configMutex);
        
        auto it = config.find(std::string(key));
        if (it == config.end()) {
            return defaultValue;
        }
        
        return std::get<T>(it->second);
    }
    
    template<typename T>
    void setValue(std::string_view key, const T& value) {
        std::unique_lock<std::shared_mutex> lock(configMutex);
        config[std::string(key)] = value;
        saveConfig();
    }
    
    [[nodiscard]] bool hasKey(std::string_view key) const {
        std::shared_lock<std::shared_mutex> lock(configMutex);
        return config.contains(std::string(key));
    }
    
    void removeKey(std::string_view key) {
        std::unique_lock<std::shared_mutex> lock(configMutex);
        config.erase(std::string(key));
        saveConfig();
    }
    
private:
    void loadConfig() {
        std::ifstream file(configFilename);
        if (!file.is_open()) {
            return;
        }
        
        std::string line;
        while (std::getline(file, line)) {
            size_t pos = line.find('=');
            if (pos != std::string::npos) {
                std::string key = line.substr(0, pos);
                std::string value = line.substr(pos + 1);
                
                // Try to parse different types
                if (value == "true" || value == "false") {
                    config[key] = (value == "true");
                } else if (value.find('.') != std::string::npos) {
                    try {
                        config[key] = std::stod(value);
                    } catch (...) {
                        config[key] = value;
                    }
                } else {
                    try {
                        config[key] = std::stoi(value);
                    } catch (...) {
                        config[key] = value;
                    }
                }
            }
        }
    }
    
    void saveConfig() {
        std::ofstream file(configFilename);
        if (!file.is_open()) {
            return;
        }
        
        for (const auto& [key, value] : config) {
            file << key << "=";
            std::visit([&file](const auto& v) {
                if constexpr (std::is_same_v<decltype(v), bool>) {
                    file << (v ? "true" : "false");
                } else {
                    file << v;
                }
            }, value);
            file << "\n";
        }
    }
};

// Example 5: Modern C++ Logger with Structured Binding
enum class LogLevel {
    DEBUG,
    INFO,
    WARNING,
    ERROR
};

class ModernLogger {
private:
    std::ofstream logFile;
    LogLevel minLogLevel;
    mutable std::mutex logMutex;
    
    static std::string_view levelToString(LogLevel level) {
        switch (level) {
            case LogLevel::DEBUG: return "DEBUG";
            case LogLevel::INFO: return "INFO";
            case LogLevel::WARNING: return "WARNING";
            case LogLevel::ERROR: return "ERROR";
        }
        return "UNKNOWN";
    }
    
    static std::string getCurrentTimestamp() {
        auto now = std::chrono::system_clock::now();
        auto timeT = std::chrono::system_clock::to_time_t(now);
        auto ms = std::chrono::duration_cast<std::chrono::milliseconds>(
            now.time_since_epoch()) % 1000;
        
        return std::format("{:%Y-%m-%d %H:%M:%S}.{:03d}", 
                          *std::localtime(&timeT), ms.count());
    }
    
public:
    ModernLogger(std::string_view filename, LogLevel minLevel = LogLevel::INFO)
        : logFile(filename.data()), minLogLevel(minLevel) {}
    
    void log(LogLevel level, std::string_view message) {
        if (level < minLogLevel) {
            return;
        }
        
        std::lock_guard<std::mutex> lock(logMutex);
        
        auto timestamp = getCurrentTimestamp();
        auto levelStr = levelToString(level);
        
        std::string logEntry = std::format("[{}] [{}] {}", timestamp, levelStr, message);
        
        std::cout << logEntry << std::endl;
        if (logFile.is_open()) {
            logFile << logEntry << std::endl;
        }
    }
    
    template<typename... Args>
    void logFormat(LogLevel level, std::string_view formatStr, Args&&... args) {
        if (level < minLogLevel) {
            return;
        }
        
        try {
            auto message = std::format(formatStr, std::forward<Args>(args)...);
            log(level, message);
        } catch (const std::exception& e) {
            log(level, std::format("Format error: {}", e.what()));
        }
    }
    
    void debug(std::string_view message) { log(LogLevel::DEBUG, message); }
    void info(std::string_view message) { log(LogLevel::INFO, message); }
    void warning(std::string_view message) { log(LogLevel::WARNING, message); }
    void error(std::string_view message) { log(LogLevel::ERROR, message); }
};

// Example 6: Modern C++ Task Scheduler
class TaskScheduler {
private:
    struct Task {
        std::string name;
        std::function<void()> function;
        std::chrono::system_clock::time_point scheduledTime;
        bool isRecurring;
        std::chrono::seconds interval;
        
        Task(std::string_view n, std::function<void()> func, 
             std::chrono::system_clock::time_point time, bool recurring = false,
             std::chrono::seconds interval = std::chrono::seconds(0))
            : name(n), function(std::move(func)), scheduledTime(time), 
              isRecurring(recurring), interval(interval) {}
    };
    
    std::vector<Task> tasks;
    std::mutex tasksMutex;
    std::atomic<bool> running;
    std::thread schedulerThread;
    ModernLogger logger;
    
public:
    TaskScheduler() : running(false), logger("scheduler.log") {}
    
    ~TaskScheduler() {
        stop();
    }
    
    void start() {
        running = true;
        schedulerThread = std::thread([this]() { runScheduler(); });
        logger.info("Task scheduler started");
    }
    
    void stop() {
        if (running) {
            running = false;
            if (schedulerThread.joinable()) {
                schedulerThread.join();
            }
            logger.info("Task scheduler stopped");
        }
    }
    
    void scheduleTask(std::string_view name, std::function<void()> task,
                     std::chrono::system_clock::time_point when) {
        std::lock_guard<std::mutex> lock(tasksMutex);
        tasks.emplace_back(name, std::move(task), when);
        logger.info("Scheduled task: {}", name);
    }
    
    void scheduleRecurringTask(std::string_view name, std::function<void()> task,
                              std::chrono::seconds interval) {
        auto firstRun = std::chrono::system_clock::now() + interval;
        std::lock_guard<std::mutex> lock(tasksMutex);
        tasks.emplace_back(name, std::move(task), firstRun, true, interval);
        logger.info("Scheduled recurring task: {} with interval {}s", name, interval.count());
    }
    
private:
    void runScheduler() {
        while (running) {
            auto now = std::chrono::system_clock::now();
            std::vector<std::function<void()>> tasksToRun;
            
            {
                std::lock_guard<std::mutex> lock(tasksMutex);
                
                for (auto it = tasks.begin(); it != tasks.end();) {
                    if (it->scheduledTime <= now) {
                        tasksToRun.push_back(it->function);
                        
                        if (it->isRecurring) {
                            it->scheduledTime = now + it->interval;
                            ++it;
                        } else {
                            it = tasks.erase(it);
                        }
                    } else {
                        ++it;
                    }
                }
            }
            
            // Execute tasks outside of lock
            for (const auto& task : tasksToRun) {
                try {
                    task();
                } catch (const std::exception& e) {
                    logger.error("Task execution failed: {}", e.what());
                }
            }
            
            std::this_thread::sleep_for(std::chrono::milliseconds(100));
        }
    }
};

// Example 7: Modern C++ Data Processing Pipeline
class DataProcessor {
private:
    ModernLogger logger;
    
public:
    DataProcessor() : logger("data_processor.log") {}
    
    auto processData(std::vector<int> data) {
        logger.info("Starting data processing with {} items", data.size());
        
        // Modern C++ ranges
        auto filtered = data | std::views::filter([](int x) { return x > 0; });
        auto transformed = filtered | std::views::transform([](int x) { return x * 2; });
        
        std::vector<int> result;
        for (int value : transformed) {
            result.push_back(value);
        }
        
        logger.info("Data processing completed. {} items processed", result.size());
        return result;
    }
    
    auto processDataWithStructuredBindings(const std::vector<std::pair<int, std::string>>& data) {
        std::vector<std::tuple<int, std::string, double>> result;
        
        for (const auto& [id, name] : data) {
            double score = calculateScore(id, name);
            result.emplace_back(id, name, score);
        }
        
        // Sort using ranges
        std::ranges::sort(result, [](const auto& a, const auto& b) {
            return std::get<2>(a) > std::get<2>(b);
        });
        
        return result;
    }
    
private:
    double calculateScore(int id, const std::string& name) {
        // Simple scoring algorithm
        return static_cast<double>(id) + name.length() * 10.0;
    }
};

// Example 8: Modern C++ Observer Pattern
template<typename... EventTypes>
class ModernObserver {
public:
    virtual ~ModernObserver() = default;
    virtual void onEvent(const std::variant<EventTypes...>& event) = 0;
};

template<typename... EventTypes>
class ModernSubject {
private:
    std::vector<std::shared_ptr<ModernObserver<EventTypes...>>> observers;
    std::mutex observersMutex;
    
public:
    void addObserver(std::shared_ptr<ModernObserver<EventTypes...>> observer) {
        std::lock_guard<std::mutex> lock(observersMutex);
        observers.push_back(observer);
    }
    
    void removeObserver(std::shared_ptr<ModernObserver<EventTypes...>> observer) {
        std::lock_guard<std::mutex> lock(observersMutex);
        observers.erase(
            std::remove(observers.begin(), observers.end(), observer),
            observers.end()
        );
    }
    
    void notify(const std::variant<EventTypes...>& event) {
        std::lock_guard<std::mutex> lock(observersMutex);
        for (const auto& observer : observers) {
            observer->onEvent(event);
        }
    }
};

// Example event types
struct UserLoginEvent {
    std::string username;
    std::chrono::system_clock::time_point timestamp;
};

struct UserLogoutEvent {
    std::string username;
    std::chrono::system_clock::time_point timestamp;
};

// Example observer
class SecurityObserver : public ModernObserver<UserLoginEvent, UserLogoutEvent> {
private:
    ModernLogger logger;
    
public:
    SecurityObserver() : logger("security.log") {}
    
    void onEvent(const std::variant<UserLoginEvent, UserLogoutEvent>& event) override {
        std::visit([this](const auto& e) {
            if constexpr (std::is_same_v<decltype(e), UserLoginEvent>) {
                logger.info("User logged in: {}", e.username);
            } else if constexpr (std::is_same_v<decltype(e), UserLogoutEvent>) {
                logger.info("User logged out: {}", e.username);
            }
        }, event);
    }
};

int main() {
    std::cout << "=== Modern C++ and Best Practices - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of modern C++ features\n" << std::endl;
    
    // Example 1: Modern Calculator with Concepts
    std::cout << "=== MODERN CALCULATOR ===" << std::endl;
    Calculator calc(10.0);
    calc.add(5.0).multiply(2.0).subtract(3.0);
    std::cout << "Calculator result: " << calc.getValue() << std::endl;
    
    // Example 2: Modern File Processor
    std::cout << "\n=== MODERN FILE PROCESSOR ===" << std::endl;
    ModernFileProcessor processor("example.txt");
    processor.writeContent("Hello, Modern C++!");
    
    if (auto content = processor.readContent()) {
        std::cout << "File content: " << *content << std::endl;
    }
    
    // Example 3: Event System
    std::cout << "\n=== EVENT SYSTEM ===" << std::endl;
    EventSystem eventSystem;
    
    eventSystem.addListener([](const std::string& event) {
        std::cout << "Received event: " << event << std::endl;
    });
    
    eventSystem.emitEvent("user_logged_in");
    eventSystem.emitEventAsync("file_uploaded");
    
    // Example 4: Configuration Manager
    std::cout << "\n=== CONFIGURATION MANAGER ===" << std::endl;
    ConfigurationManager config("app_config.txt");
    
    config.setValue("app_name", "Modern C++ App");
    config.setValue("version", 1.0);
    config.setValue("debug_mode", true);
    config.setValue("max_connections", 100);
    
    std::cout << "App name: " << config.getValue<std::string>("app_name") << std::endl;
    std::cout << "Version: " << config.getValue<double>("version") << std::endl;
    std::cout << "Debug mode: " << (config.getValue<bool>("debug_mode") ? "true" : "false") << std::endl;
    std::cout << "Max connections: " << config.getValue<int>("max_connections") << std::endl;
    
    // Example 5: Modern Logger
    std::cout << "\n=== MODERN LOGGER ===" << std::endl;
    ModernLogger logger("app.log", LogLevel::DEBUG);
    
    logger.debug("This is a debug message");
    logger.info("Application started successfully");
    logger.warning("This is a warning message");
    logger.error("This is an error message");
    
    logger.logFormat(LogLevel::INFO, "User {} logged in at {}", "john", std::chrono::system_clock::now());
    
    // Example 6: Task Scheduler
    std::cout << "\n=== TASK SCHEDULER ===" << std::endl;
    TaskScheduler scheduler;
    scheduler.start();
    
    scheduler.scheduleTask("cleanup", []() {
        std::cout << "Performing cleanup task" << std::endl;
    }, std::chrono::system_clock::now() + std::chrono::seconds(1));
    
    scheduler.scheduleRecurringTask("backup", []() {
        std::cout << "Performing backup task" << std::endl;
    }, std::chrono::seconds(2));
    
    std::this_thread::sleep_for(std::chrono::seconds(3));
    scheduler.stop();
    
    // Example 7: Data Processing Pipeline
    std::cout << "\n=== DATA PROCESSING PIPELINE ===" << std::endl;
    DataProcessor processor;
    
    std::vector<int> data = {1, -2, 3, -4, 5, -6, 7, -8, 9, -10};
    auto processedData = processor.processData(data);
    
    std::cout << "Processed data: ";
    for (int value : processedData) {
        std::cout << value << " ";
    }
    std::cout << std::endl;
    
    // Example 8: Observer Pattern
    std::cout << "\n=== OBSERVER PATTERN ===" << std::endl;
    ModernSubject<UserLoginEvent, UserLogoutEvent> subject;
    auto securityObserver = std::make_shared<SecurityObserver>();
    
    subject.addObserver(securityObserver);
    
    UserLoginEvent loginEvent{"alice", std::chrono::system_clock::now()};
    UserLogoutEvent logoutEvent{"bob", std::chrono::system_clock::now()};
    
    subject.notify(loginEvent);
    subject.notify(logoutEvent);
    
    std::cout << "\n\n=== MODERN C++ FEATURES SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various modern C++ features:" << std::endl;
    std::cout << "• C++20 concepts for template constraints" << std::endl;
    std::cout << "• std::optional for nullable values" << std::endl;
    std::cout << "• std::variant for type-safe unions" << std::endl;
    std::cout << "• std::format for safe string formatting" << std::endl;
    std::cout << "• Ranges for functional-style programming" << std::endl;
    std::cout << "• Structured bindings for clean code" << std::endl;
    std::cout << "• Spaceship operator for comparisons" << std::endl;
    std::cout << "• Smart pointers for memory management" << std::endl;
    std::cout << "• Thread-safe containers and algorithms" << std::endl;
    std::cout << "• Modern RAII and resource management" << std::endl;
    std::cout << "• Template metaprogramming improvements" << std::endl;
    
    std::cout << "\n=== BEST PRACTICES ===" << std::endl;
    std::cout << "Key best practices demonstrated:" << std::endl;
    std::cout << "• Use RAII for automatic resource management" << std::endl;
    std::cout << "• Prefer smart pointers over raw pointers" << std::endl;
    std::cout << "• Use constexpr for compile-time computations" << std::endl;
    std::cout << "• Apply const-correctness and [[nodiscard]]" << std::endl;
    std::cout << "• Use structured bindings for clean code" << std::endl;
    std::cout << "• Leverage concepts for template constraints" << std::endl;
    std::cout << "• Use std::optional for nullable return values" << std::endl;
    std::cout << "• Apply modern threading and synchronization" << std::endl;
    std::cout << "• Use std::format for type-safe string formatting" << std::endl;
    std::cout << "• Implement proper exception handling" << std::endl;
    std::cout << "• Use ranges for functional-style algorithms" << std::endl;
    
    std::cout << "\nModern C++ provides powerful tools for writing safe, efficient, and maintainable code!" << std::endl;
    
    return 0;
}