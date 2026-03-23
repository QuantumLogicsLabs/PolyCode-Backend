#include <iostream>
#include <string>

/**
 * Templates in C++
 * This tutorial covers Function Templates and Class Templates for generic programming.
 */

// --- Function Template ---
template <typename T>
T add(T a, T b) {
    return a + b;
}

// --- Class Template ---
template <typename T>
class Box {
private:
    T value;

public:
    Box(T val) : value(val) {}
    T getValue() const { return value; }
    void setValue(T val) { value = val; }
};

int main() {
    std::cout << "--- Function Templates ---" << std::endl;
    // The compiler deduces the type automatically
    std::cout << "Integer Addition: " << add(10, 20) << std::endl;
    std::cout << "Double Addition: " << add(10.5, 20.3) << std::endl;
    std::cout << "String Addition: " << add(std::string("Hello "), std::string("World")) << std::endl;

    std::cout << "\n--- Class Templates ---" << std::endl;
    // Specify the type when creating a Class Template object
    Box<int> intBox(42);
    Box<std::string> strBox("Generic Programming");

    std::cout << "Int Box Value: " << intBox.getValue() << std::endl;
    std::cout << "String Box Value: " << strBox.getValue() << std::endl;

    return 0;
}