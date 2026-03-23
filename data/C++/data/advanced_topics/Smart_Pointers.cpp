#include <iostream>
#include <memory>

/**
 * Smart Pointers in C++
 * This tutorial covers unique_ptr and shared_ptr for automatic memory management.
 */

class Resource {
public:
    Resource() { std::cout << "Resource Acquired" << std::endl; }
    ~Resource() { std::cout << "Resource Released" << std::endl; }
    void sayHello() { std::cout << "Hello from Resource!" << std::endl; }
};

int main() {
    std::cout << "--- unique_ptr ---" << std::endl;
    {
        // unique_ptr: exclusive ownership
        std::unique_ptr<Resource> res1 = std::make_unique<Resource>();
        res1->sayHello();
        // res1 is automatically destroyed at the end of this block
    }

    std::cout << "\n--- shared_ptr ---" << std::endl;
    {
        // shared_ptr: shared ownership via reference counting
        std::shared_ptr<Resource> res2 = std::make_shared<Resource>();
        {
            std::shared_ptr<Resource> res3 = res2; // res3 and res2 share ownership
            std::cout << "Reference Count: " << res2.use_count() << std::endl;
            res3->sayHello();
        } // res3 goes out of scope, but the resource remains because res2 still exists
        std::cout << "Reference Count after res3 scope: " << res2.use_count() << std::endl;
    } // res2 goes out of scope, and the resource is finally destroyed

    return 0;
}