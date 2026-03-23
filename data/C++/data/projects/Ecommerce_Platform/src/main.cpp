#include <iostream>
#include <memory>
#include <vector>
#include <iomanip>
#include <limits>
#include <thread>
#include <chrono>
#include "product_catalog.h"
#include "shopping_cart.h"
#include "order_processor.h"
#include "payment_gateway.h"
#include "user_manager.h"
#include "web_server.h"

// Utility functions
void clearScreen() {
#ifdef _WIN32
    system("cls");
#else
    system("clear");
#endif
}

void pauseScreen() {
    std::cout << "\nPress Enter to continue...";
    std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
    std::cin.get();
}

// Menu display functions
void displayMainMenu() {
    std::cout << "\n" << std::string(60, '=') << std::endl;
    std::cout << std::setw(20) << " " << "E-COMMERCE PLATFORM" << std::setw(20) << " " << std::endl;
    std::cout << std::string(60, '=') << std::endl;
    std::cout << "1. Browse Products" << std::endl;
    std::cout << "2. Shopping Cart" << std::endl;
    std::cout << "3. My Account" << std::endl;
    std::cout << "4. Checkout" << std::endl;
    std::cout << "5. Order History" << std::endl;
    std::cout << "6. Admin Panel" << std::endl;
    std::cout << "7. Start Web Server" << std::endl;
    std::cout << "8. Exit" << std::endl;
    std::cout << std::string(60, '-') << std::endl;
    std::cout << "Enter your choice (1-8): ";
}

void displayProductMenu() {
    std::cout << "\n" << std::string(40, '-') << std::endl;
    std::cout << "PRODUCT BROWSING" << std::endl;
    std::cout << std::string(40, '-') << std::endl;
    std::cout << "1. View All Products" << std::endl;
    std::cout << "2. Search Products" << std::endl;
    std::cout << "3. Browse by Category" << std::endl;
    std::cout << "4. View Product Details" << std::endl;
    std::cout << "5. Add to Cart" << std::endl;
    std::cout << "6. Back to Main Menu" << std::endl;
    std::cout << "Enter your choice (1-6): ";
}

void displayCartMenu() {
    std::cout << "\n" << std::string(40, '-') << std::endl;
    std::cout << "SHOPPING CART" << std::endl;
    std::cout << std::string(40, '-') << std::endl;
    std::cout << "1. View Cart" << std::endl;
    std::cout << "2. Update Quantity" << std::endl;
    std::cout << "3. Remove Item" << std::endl;
    std::cout << "4. Clear Cart" << std::endl;
    std::cout << "5. Apply Coupon" << std::endl;
    std::cout << "6. Back to Main Menu" << std::endl;
    std::cout << "Enter your choice (1-6): ";
}

void displayAdminMenu() {
    std::cout << "\n" << std::string(40, '-') << std::endl;
    std::cout << "ADMIN PANEL" << std::endl;
    std::cout << std::string(40, '-') << std::endl;
    std::cout << "1. Add Product" << std::endl;
    std::cout << "2. Update Product" << std::endl;
    std::cout << "3. Remove Product" << std::endl;
    std::cout << "4. View Orders" << std::endl;
    std::cout << "5. Inventory Management" << std::endl;
    std::cout << "6. Sales Report" << std::endl;
    std::cout << "7. Customer Management" << std::endl;
    std::cout << "8. Back to Main Menu" << std::endl;
    std::cout << "Enter your choice (1-8): ";
}

// Input validation functions
int getValidIntegerInput(const std::string& prompt, int min, int max) {
    int value;
    while (true) {
        std::cout << prompt;
        if (std::cin >> value && value >= min && value <= max) {
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
            return value;
        } else {
            std::cout << "Invalid input. Please enter a number between " << min << " and " << max << "." << std::endl;
            std::cin.clear();
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
        }
    }
}

double getValidDoubleInput(const std::string& prompt, double min = 0.0) {
    double value;
    while (true) {
        std::cout << prompt;
        if (std::cin >> value && value >= min) {
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
            return value;
        } else {
            std::cout << "Invalid input. Please enter a valid number >= " << min << "." << std::endl;
            std::cin.clear();
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
        }
    }
}

std::string getStringInput(const std::string& prompt) {
    std::string value;
    std::cout << prompt;
    std::getline(std::cin, value);
    return value;
}

// Menu handlers
void handleProductBrowsing(std::unique_ptr<ProductCatalog>& catalog, std::unique_ptr<ShoppingCart>& cart) {
    int choice;
    do {
        displayProductMenu();
        choice = getValidIntegerInput("", 1, 6);
        
        switch (choice) {
            case 1: {
                // View All Products
                clearScreen();
                std::cout << "\n=== ALL PRODUCTS ===" << std::endl;
                catalog->displayAllProducts();
                pauseScreen();
                break;
            }
            
            case 2: {
                // Search Products
                clearScreen();
                std::cout << "\n=== SEARCH PRODUCTS ===" << std::endl;
                std::string searchTerm = getStringInput("Enter search term: ");
                auto results = catalog->searchProducts(searchTerm);
                
                if (results.empty()) {
                    std::cout << "No products found matching '" << searchTerm << "'" << std::endl;
                } else {
                    std::cout << "Found " << results.size() << " products:" << std::endl;
                    for (const auto& product : results) {
                        product.displaySummary();
                        std::cout << std::string(50, '-') << std::endl;
                    }
                }
                
                pauseScreen();
                break;
            }
            
            case 3: {
                // Browse by Category
                clearScreen();
                std::cout << "\n=== BROWSE BY CATEGORY ===" << std::endl;
                auto categories = catalog->getCategories();
                
                std::cout << "Available categories:" << std::endl;
                for (size_t i = 0; i < categories.size(); ++i) {
                    std::cout << (i + 1) << ". " << categories[i] << std::endl;
                }
                
                int categoryChoice = getValidIntegerInput("Select category number: ", 1, categories.size());
                std::string selectedCategory = categories[categoryChoice - 1];
                
                auto products = catalog->getProductsByCategory(selectedCategory);
                std::cout << "\nProducts in " << selectedCategory << ":" << std::endl;
                for (const auto& product : products) {
                    product.displaySummary();
                    std::cout << std::string(50, '-') << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 4: {
                // View Product Details
                clearScreen();
                std::cout << "\n=== PRODUCT DETAILS ===" << std::endl;
                std::string productId = getStringInput("Enter product ID: ");
                
                auto product = catalog->getProduct(productId);
                if (product) {
                    product->displayDetails();
                } else {
                    std::cout << "Product not found." << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 5: {
                // Add to Cart
                clearScreen();
                std::cout << "\n=== ADD TO CART ===" << std::endl;
                std::string productId = getStringInput("Enter product ID: ");
                int quantity = getValidIntegerInput("Enter quantity: ", 1, 100);
                
                auto product = catalog->getProduct(productId);
                if (product) {
                    if (cart->addItem(productId, quantity)) {
                        std::cout << "Added " << quantity << " x " << product->getName() << " to cart." << std::endl;
                        std::cout << "Subtotal: $" << std::fixed << std::setprecision(2) << cart->getSubtotal() << std::endl;
                    } else {
                        std::cout << "Failed to add item to cart. Check stock availability." << std::endl;
                    }
                } else {
                    std::cout << "Product not found." << std::endl;
                }
                
                pauseScreen();
                break;
            }
        }
    } while (choice != 6);
}

void handleShoppingCart(std::unique_ptr<ShoppingCart>& cart, std::unique_ptr<ProductCatalog>& catalog) {
    int choice;
    do {
        displayCartMenu();
        choice = getValidIntegerInput("", 1, 6);
        
        switch (choice) {
            case 1: {
                // View Cart
                clearScreen();
                std::cout << "\n=== SHOPPING CART ===" << std::endl;
                cart->displayDetails();
                pauseScreen();
                break;
            }
            
            case 2: {
                // Update Quantity
                clearScreen();
                std::cout << "\n=== UPDATE QUANTITY ===" << std::endl;
                std::string productId = getStringInput("Enter product ID: ");
                int quantity = getValidIntegerInput("Enter new quantity: ", 0, 100);
                
                if (cart->updateItemQuantity(productId, quantity)) {
                    std::cout << "Cart updated successfully." << std::endl;
                } else {
                    std::cout << "Failed to update cart." << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 3: {
                // Remove Item
                clearScreen();
                std::cout << "\n=== REMOVE ITEM ===" << std::endl;
                std::string productId = getStringInput("Enter product ID to remove: ");
                
                if (cart->removeItem(productId)) {
                    std::cout << "Item removed from cart." << std::endl;
                } else {
                    std::cout << "Failed to remove item." << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 4: {
                // Clear Cart
                clearScreen();
                std::cout << "\n=== CLEAR CART ===" << std::endl;
                std::string confirmation = getStringInput("Are you sure you want to clear your cart? (yes/no): ");
                
                if (confirmation == "yes" || confirmation == "YES") {
                    cart->clear();
                    std::cout << "Cart cleared successfully." << std::endl;
                } else {
                    std::cout << "Operation cancelled." << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 5: {
                // Apply Coupon
                clearScreen();
                std::cout << "\n=== APPLY COUPON ===" << std::endl;
                std::string couponCode = getStringInput("Enter coupon code: ");
                
                if (cart->applyCoupon(couponCode)) {
                    std::cout << "Coupon applied successfully!" << std::endl;
                    std::cout << "Discount: $" << std::fixed << std::setprecision(2) << cart->getDiscount() << std::endl;
                    std::cout << "New Total: $" << cart->getTotal() << std::endl;
                } else {
                    std::cout << "Invalid or expired coupon code." << std::endl;
                }
                
                pauseScreen();
                break;
            }
        }
    } while (choice != 6);
}

void handleCheckout(std::unique_ptr<ShoppingCart>& cart, std::unique_ptr<OrderProcessor>& orderProcessor, 
                   std::unique_ptr<PaymentGateway>& paymentGateway) {
    clearScreen();
    std::cout << "\n=== CHECKOUT ===" << std::endl;
    
    if (cart->isEmpty()) {
        std::cout << "Your cart is empty. Add some products before checking out." << std::endl;
        pauseScreen();
        return;
    }
    
    // Display cart summary
    std::cout << "\nOrder Summary:" << std::endl;
    cart->displayDetails();
    
    // Get customer information
    std::cout << "\nCustomer Information:" << std::endl;
    std::string name = getStringInput("Full Name: ");
    std::string email = getStringInput("Email: ");
    std::string phone = getStringInput("Phone: ");
    std::string address = getStringInput("Shipping Address: ");
    
    // Create customer
    Customer customer(name, email, phone, address);
    
    // Create order
    try {
        std::string orderId = orderProcessor->createOrder(customer, cart->getItems());
        std::cout << "\nOrder created: " << orderId << std::endl;
        
        // Process payment
        std::cout << "\nProcessing payment..." << std::endl;
        PaymentInfo paymentInfo("credit_card", "1234-5678-9012-3456", "12/25", "123");
        
        if (paymentGateway->processPayment(orderId, cart->getTotal(), paymentInfo)) {
            std::cout << "Payment successful!" << std::endl;
            
            // Complete order
            orderProcessor->completeOrder(orderId);
            std::cout << "Order completed successfully!" << std::endl;
            
            // Clear cart
            cart->clear();
            
            std::cout << "\nThank you for your purchase!" << std::endl;
            std::cout << "Order ID: " << orderId << std::endl;
            std::cout << "You will receive a confirmation email shortly." << std::endl;
        } else {
            std::cout << "Payment failed. Please try again." << std::endl;
            orderProcessor->cancelOrder(orderId);
        }
    } catch (const std::exception& e) {
        std::cout << "Error during checkout: " << e.what() << std::endl;
    }
    
    pauseScreen();
}

void handleAdminPanel(std::unique_ptr<ProductCatalog>& catalog, std::unique_ptr<OrderProcessor>& orderProcessor) {
    int choice;
    do {
        displayAdminMenu();
        choice = getValidIntegerInput("", 1, 8);
        
        switch (choice) {
            case 1: {
                // Add Product
                clearScreen();
                std::cout << "\n=== ADD PRODUCT ===" << std::endl;
                
                std::string name = getStringInput("Product name: ");
                std::string description = getStringInput("Description: ");
                std::string category = getStringInput("Category: ");
                double price = getValidDoubleInput("Price: $", 0.01);
                int stock = getValidIntegerInput("Stock quantity: ", 0);
                
                try {
                    std::string productId = catalog->addProduct(name, description, category, price, stock);
                    std::cout << "Product added successfully! ID: " << productId << std::endl;
                } catch (const std::exception& e) {
                    std::cout << "Error adding product: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 2: {
                // Update Product
                clearScreen();
                std::cout << "\n=== UPDATE PRODUCT ===" << std::endl;
                
                std::string productId = getStringInput("Enter product ID: ");
                auto product = catalog->getProduct(productId);
                
                if (product) {
                    std::string newName = getStringInput("New name (or press Enter to keep current): ");
                    std::string newPrice = getStringInput("New price (or press Enter to keep current): ");
                    
                    if (!newName.empty()) {
                        product->setName(newName);
                    }
                    if (!newPrice.empty()) {
                        product->setPrice(std::stod(newPrice));
                    }
                    
                    std::cout << "Product updated successfully!" << std::endl;
                } else {
                    std::cout << "Product not found." << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 3: {
                // Remove Product
                clearScreen();
                std::cout << "\n=== REMOVE PRODUCT ===" << std::endl;
                
                std::string productId = getStringInput("Enter product ID: ");
                std::string confirmation = getStringInput("Are you sure? (yes/no): ");
                
                if (confirmation == "yes" || confirmation == "YES") {
                    if (catalog->removeProduct(productId)) {
                        std::cout << "Product removed successfully!" << std::endl;
                    } else {
                        std::cout << "Failed to remove product." << std::endl;
                    }
                } else {
                    std::cout << "Operation cancelled." << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 4: {
                // View Orders
                clearScreen();
                std::cout << "\n=== ORDERS ===" << std::endl;
                orderProcessor->displayAllOrders();
                pauseScreen();
                break;
            }
            
            case 5: {
                // Inventory Management
                clearScreen();
                std::cout << "\n=== INVENTORY MANAGEMENT ===" << std::endl;
                catalog->displayInventoryReport();
                pauseScreen();
                break;
            }
            
            case 6: {
                // Sales Report
                clearScreen();
                std::cout << "\n=== SALES REPORT ===" << std::endl;
                orderProcessor->generateSalesReport();
                pauseScreen();
                break;
            }
            
            case 7: {
                // Customer Management
                clearScreen();
                std::cout << "\n=== CUSTOMER MANAGEMENT ===" << std::endl;
                std::cout << "Customer management features coming soon..." << std::endl;
                pauseScreen();
                break;
            }
        }
    } while (choice != 8);
}

void startWebServer(std::unique_ptr<ProductCatalog>& catalog, std::unique_ptr<OrderProcessor>& orderProcessor) {
    clearScreen();
    std::cout << "\n=== WEB SERVER ===" << std::endl;
    
    int port = getValidIntegerInput("Enter port number (default 8080): ", 1024, 65535);
    
    std::cout << "Starting web server on port " << port << "..." << std::endl;
    std::cout << "Server running at http://localhost:" << port << std::endl;
    std::cout << "Press Ctrl+C to stop the server" << std::endl;
    
    try {
        WebServer server(port, catalog, orderProcessor);
        server.start();
        
        // Keep server running
        while (true) {
            std::this_thread::sleep_for(std::chrono::seconds(1));
        }
    } catch (const std::exception& e) {
        std::cout << "Server error: " << e.what() << std::endl;
    }
}

int main() {
    // Initialize the e-commerce platform
    std::unique_ptr<ProductCatalog> catalog;
    std::unique_ptr<ShoppingCart> cart;
    std::unique_ptr<OrderProcessor> orderProcessor;
    std::unique_ptr<PaymentGateway> paymentGateway;
    std::unique_ptr<UserManager> userManager;
    
    try {
        catalog = std::make_unique<ProductCatalog>();
        cart = std::make_unique<ShoppingCart>();
        orderProcessor = std::make_unique<OrderProcessor>();
        paymentGateway = std::make_unique<PaymentGateway>();
        userManager = std::make_unique<UserManager>();
        
        // Initialize with sample data
        catalog->initializeSampleData();
        
        std::cout << "E-commerce Platform initialized successfully!" << std::endl;
    } catch (const std::exception& e) {
        std::cerr << "Failed to initialize e-commerce platform: " << e.what() << std::endl;
        return 1;
    }
    
    // Main application loop
    int choice;
    do {
        clearScreen();
        displayMainMenu();
        choice = getValidIntegerInput("", 1, 8);
        
        switch (choice) {
            case 1:
                handleProductBrowsing(catalog, cart);
                break;
            case 2:
                handleShoppingCart(cart, catalog);
                break;
            case 3:
                std::cout << "\nUser account management coming soon..." << std::endl;
                pauseScreen();
                break;
            case 4:
                handleCheckout(cart, orderProcessor, paymentGateway);
                break;
            case 5:
                std::cout << "\nOrder history coming soon..." << std::endl;
                pauseScreen();
                break;
            case 6:
                handleAdminPanel(catalog, orderProcessor);
                break;
            case 7:
                startWebServer(catalog, orderProcessor);
                break;
            case 8:
                std::cout << "\nThank you for using the E-commerce Platform!" << std::endl;
                std::cout << "Goodbye!" << std::endl;
                break;
        }
    } while (choice != 8);
    
    return 0;
}