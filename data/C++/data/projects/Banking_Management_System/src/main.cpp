#include <iostream>
#include <memory>
#include <vector>
#include <iomanip>
#include <limits>
#include "bank.h"
#include "account.h"
#include "transaction.h"
#include "user.h"
#include "database.h"

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
    std::cout << std::setw(25) << " " << "BANKING MANAGEMENT SYSTEM" << std::setw(25) << " " << std::endl;
    std::cout << std::string(60, '=') << std::endl;
    std::cout << "1. Account Management" << std::endl;
    std::cout << "2. Transactions" << std::endl;
    std::cout << "3. Reports" << std::endl;
    std::cout << "4. User Management" << std::endl;
    std::cout << "5. System Maintenance" << std::endl;
    std::cout << "6. Exit" << std::endl;
    std::cout << std::string(60, '-') << std::endl;
    std::cout << "Enter your choice (1-6): ";
}

void displayAccountMenu() {
    std::cout << "\n" << std::string(40, '-') << std::endl;
    std::cout << "ACCOUNT MANAGEMENT" << std::endl;
    std::cout << std::string(40, '-') << std::endl;
    std::cout << "1. Create New Account" << std::endl;
    std::cout << "2. View Account Details" << std::endl;
    std::cout << "3. Update Account" << std::endl;
    std::cout << "4. Close Account" << std::endl;
    std::cout << "5. List All Accounts" << std::endl;
    std::cout << "6. Back to Main Menu" << std::endl;
    std::cout << "Enter your choice (1-6): ";
}

void displayTransactionMenu() {
    std::cout << "\n" << std::string(40, '-') << std::endl;
    std::cout << "TRANSACTIONS" << std::endl;
    std::cout << std::string(40, '-') << std::endl;
    std::cout << "1. Deposit" << std::endl;
    std::cout << "2. Withdraw" << std::endl;
    std::cout << "3. Transfer" << std::endl;
    std::cout << "4. View Transaction History" << std::endl;
    std::cout << "5. Back to Main Menu" << std::endl;
    std::cout << "Enter your choice (1-5): ";
}

void displayReportsMenu() {
    std::cout << "\n" << std::string(40, '-') << std::endl;
    std::cout << "REPORTS" << std::endl;
    std::cout << std::string(40, '-') << std::endl;
    std::cout << "1. Account Summary" << std::endl;
    std::cout << "2. Transaction Report" << std::endl;
    std::cout << "3. Daily Summary" << std::endl;
    std::cout << "4. Monthly Report" << std::endl;
    std::cout << "5. Back to Main Menu" << std::endl;
    std::cout << "Enter your choice (1-5): ";
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
void handleAccountManagement(std::unique_ptr<Bank>& bank) {
    int choice;
    do {
        displayAccountMenu();
        choice = getValidIntegerInput("", 1, 6);
        
        switch (choice) {
            case 1: {
                // Create New Account
                clearScreen();
                std::cout << "\n=== CREATE NEW ACCOUNT ===" << std::endl;
                
                std::string name = getStringInput("Enter account holder name: ");
                std::string type = getStringInput("Enter account type (CHECKING/SAVINGS): ");
                std::transform(type.begin(), type.end(), type.begin(), ::toupper);
                
                double initialDeposit = getValidDoubleInput("Enter initial deposit amount: $", 0.0);
                
                try {
                    std::string accountNumber = bank->createAccount(name, type, initialDeposit);
                    std::cout << "\nAccount created successfully!" << std::endl;
                    std::cout << "Account Number: " << accountNumber << std::endl;
                    std::cout << "Initial Balance: $" << std::fixed << std::setprecision(2) << initialDeposit << std::endl;
                } catch (const std::exception& e) {
                    std::cout << "Error creating account: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 2: {
                // View Account Details
                clearScreen();
                std::cout << "\n=== VIEW ACCOUNT DETAILS ===" << std::endl;
                
                std::string accountNumber = getStringInput("Enter account number: ");
                
                try {
                    auto account = bank->getAccount(accountNumber);
                    if (account) {
                        account->displayDetails();
                    } else {
                        std::cout << "Account not found." << std::endl;
                    }
                } catch (const std::exception& e) {
                    std::cout << "Error retrieving account: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 3: {
                // Update Account
                clearScreen();
                std::cout << "\n=== UPDATE ACCOUNT ===" << std::endl;
                
                std::string accountNumber = getStringInput("Enter account number: ");
                
                try {
                    auto account = bank->getAccount(accountNumber);
                    if (account) {
                        std::string newName = getStringInput("Enter new name (or press Enter to keep current): ");
                        if (!newName.empty()) {
                            account->setAccountHolderName(newName);
                            std::cout << "Account name updated successfully." << std::endl;
                        } else {
                            std::cout << "No changes made." << std::endl;
                        }
                    } else {
                        std::cout << "Account not found." << std::endl;
                    }
                } catch (const std::exception& e) {
                    std::cout << "Error updating account: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 4: {
                // Close Account
                clearScreen();
                std::cout << "\n=== CLOSE ACCOUNT ===" << std::endl;
                
                std::string accountNumber = getStringInput("Enter account number: ");
                std::string confirmation = getStringInput("Are you sure you want to close this account? (yes/no): ");
                
                if (confirmation == "yes" || confirmation == "YES") {
                    try {
                        if (bank->closeAccount(accountNumber)) {
                            std::cout << "Account closed successfully." << std::endl;
                        } else {
                            std::cout << "Failed to close account." << std::endl;
                        }
                    } catch (const std::exception& e) {
                        std::cout << "Error closing account: " << e.what() << std::endl;
                    }
                } else {
                    std::cout << "Account closure cancelled." << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 5: {
                // List All Accounts
                clearScreen();
                std::cout << "\n=== ALL ACCOUNTS ===" << std::endl;
                bank->displayAllAccounts();
                pauseScreen();
                break;
            }
        }
    } while (choice != 6);
}

void handleTransactions(std::unique_ptr<Bank>& bank) {
    int choice;
    do {
        displayTransactionMenu();
        choice = getValidIntegerInput("", 1, 5);
        
        switch (choice) {
            case 1: {
                // Deposit
                clearScreen();
                std::cout << "\n=== DEPOSIT ===" << std::endl;
                
                std::string accountNumber = getStringInput("Enter account number: ");
                double amount = getValidDoubleInput("Enter deposit amount: $", 0.01);
                
                try {
                    if (bank->deposit(accountNumber, amount)) {
                        std::cout << "Deposit successful!" << std::endl;
                        std::cout << "Amount: $" << std::fixed << std::setprecision(2) << amount << std::endl;
                    } else {
                        std::cout << "Deposit failed. Account not found." << std::endl;
                    }
                } catch (const std::exception& e) {
                    std::cout << "Error processing deposit: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 2: {
                // Withdraw
                clearScreen();
                std::cout << "\n=== WITHDRAW ===" << std::endl;
                
                std::string accountNumber = getStringInput("Enter account number: ");
                double amount = getValidDoubleInput("Enter withdrawal amount: $", 0.01);
                
                try {
                    if (bank->withdraw(accountNumber, amount)) {
                        std::cout << "Withdrawal successful!" << std::endl;
                        std::cout << "Amount: $" << std::fixed << std::setprecision(2) << amount << std::endl;
                    } else {
                        std::cout << "Withdrawal failed. Insufficient funds or account not found." << std::endl;
                    }
                } catch (const std::exception& e) {
                    std::cout << "Error processing withdrawal: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 3: {
                // Transfer
                clearScreen();
                std::cout << "\n=== TRANSFER ===" << std::endl;
                
                std::string fromAccount = getStringInput("Enter source account number: ");
                std::string toAccount = getStringInput("Enter destination account number: ");
                double amount = getValidDoubleInput("Enter transfer amount: $", 0.01);
                
                try {
                    if (bank->transfer(fromAccount, toAccount, amount)) {
                        std::cout << "Transfer successful!" << std::endl;
                        std::cout << "Amount: $" << std::fixed << std::setprecision(2) << amount << std::endl;
                        std::cout << "From: " << fromAccount << std::endl;
                        std::cout << "To: " << toAccount << std::endl;
                    } else {
                        std::cout << "Transfer failed. Check account numbers and balance." << std::endl;
                    }
                } catch (const std::exception& e) {
                    std::cout << "Error processing transfer: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
            
            case 4: {
                // View Transaction History
                clearScreen();
                std::cout << "\n=== TRANSACTION HISTORY ===" << std::endl;
                
                std::string accountNumber = getStringInput("Enter account number: ");
                
                try {
                    auto transactions = bank->getTransactionHistory(accountNumber);
                    if (transactions.empty()) {
                        std::cout << "No transactions found for this account." << std::endl;
                    } else {
                        std::cout << "\nTransaction History for Account " << accountNumber << ":" << std::endl;
                        std::cout << std::string(80, '-') << std::endl;
                        std::cout << std::left << std::setw(15) << "Date/Time" 
                                  << std::setw(12) << "Type" 
                                  << std::setw(12) << "Amount" 
                                  << std::setw(20) << "Description" 
                                  << std::setw(15) << "Balance" << std::endl;
                        std::cout << std::string(80, '-') << std::endl;
                        
                        for (const auto& transaction : transactions) {
                            transaction.display();
                        }
                    }
                } catch (const std::exception& e) {
                    std::cout << "Error retrieving transaction history: " << e.what() << std::endl;
                }
                
                pauseScreen();
                break;
            }
        }
    } while (choice != 5);
}

void handleReports(std::unique_ptr<Bank>& bank) {
    int choice;
    do {
        displayReportsMenu();
        choice = getValidIntegerInput("", 1, 5);
        
        switch (choice) {
            case 1: {
                // Account Summary
                clearScreen();
                std::cout << "\n=== ACCOUNT SUMMARY ===" << std::endl;
                bank->generateAccountSummary();
                pauseScreen();
                break;
            }
            
            case 2: {
                // Transaction Report
                clearScreen();
                std::cout << "\n=== TRANSACTION REPORT ===" << std::endl;
                bank->generateTransactionReport();
                pauseScreen();
                break;
            }
            
            case 3: {
                // Daily Summary
                clearScreen();
                std::cout << "\n=== DAILY SUMMARY ===" << std::endl;
                bank->generateDailySummary();
                pauseScreen();
                break;
            }
            
            case 4: {
                // Monthly Report
                clearScreen();
                std::cout << "\n=== MONTHLY REPORT ===" << std::endl;
                bank->generateMonthlyReport();
                pauseScreen();
                break;
            }
        }
    } while (choice != 5);
}

void handleUserManagement(std::unique_ptr<Bank>& bank) {
    clearScreen();
    std::cout << "\n=== USER MANAGEMENT ===" << std::endl;
    std::cout << "User management features coming soon..." << std::endl;
    std::cout << "Current functionality includes basic account access." << std::endl;
    pauseScreen();
}

void handleSystemMaintenance(std::unique_ptr<Bank>& bank) {
    clearScreen();
    std::cout << "\n=== SYSTEM MAINTENANCE ===" << std::endl;
    
    int choice;
    std::cout << "1. Backup Data" << std::endl;
    std::cout << "2. Restore Data" << std::endl;
    std::cout << "3. System Statistics" << std::endl;
    std::cout << "4. Back to Main Menu" << std::endl;
    std::cout << "Enter your choice (1-4): ";
    
    choice = getValidIntegerInput("", 1, 4);
    
    switch (choice) {
        case 1: {
            try {
                bank->backupData();
                std::cout << "Data backup completed successfully!" << std::endl;
            } catch (const std::exception& e) {
                std::cout << "Backup failed: " << e.what() << std::endl;
            }
            break;
        }
        case 2: {
            try {
                bank->restoreData();
                std::cout << "Data restore completed successfully!" << std::endl;
            } catch (const std::exception& e) {
                std::cout << "Restore failed: " << e.what() << std::endl;
            }
            break;
        }
        case 3: {
            bank->displaySystemStatistics();
            break;
        }
    }
    
    pauseScreen();
}

int main() {
    // Initialize the banking system
    std::unique_ptr<Bank> bank;
    
    try {
        bank = std::make_unique<Bank>("MyBank");
        std::cout << "Banking Management System initialized successfully!" << std::endl;
    } catch (const std::exception& e) {
        std::cerr << "Failed to initialize banking system: " << e.what() << std::endl;
        return 1;
    }
    
    // Main application loop
    int choice;
    do {
        clearScreen();
        displayMainMenu();
        choice = getValidIntegerInput("", 1, 6);
        
        switch (choice) {
            case 1:
                handleAccountManagement(bank);
                break;
            case 2:
                handleTransactions(bank);
                break;
            case 3:
                handleReports(bank);
                break;
            case 4:
                handleUserManagement(bank);
                break;
            case 5:
                handleSystemMaintenance(bank);
                break;
            case 6:
                std::cout << "\nThank you for using the Banking Management System!" << std::endl;
                std::cout << "Goodbye!" << std::endl;
                break;
        }
    } while (choice != 6);
    
    return 0;
}