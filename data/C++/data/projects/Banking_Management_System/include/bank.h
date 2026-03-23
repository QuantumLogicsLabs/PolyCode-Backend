#ifndef BANK_H
#define BANK_H

#include <string>
#include <vector>
#include <memory>
#include <map>
#include "account.h"
#include "transaction.h"
#include "database.h"

class Bank {
private:
    std::string bankName;
    std::map<std::string, std::unique_ptr<Account>> accounts;
    std::vector<Transaction> transactions;
    std::unique_ptr<Database> database;
    int nextAccountNumber;
    
    std::string generateAccountNumber();
    void loadAccounts();
    void saveAccounts();
    
public:
    explicit Bank(const std::string& name);
    ~Bank();
    
    // Account management
    std::string createAccount(const std::string& accountHolderName, 
                            const std::string& accountType, 
                            double initialBalance);
    std::shared_ptr<Account> getAccount(const std::string& accountNumber);
    bool closeAccount(const std::string& accountNumber);
    void displayAllAccounts() const;
    
    // Transaction operations
    bool deposit(const std::string& accountNumber, double amount);
    bool withdraw(const std::string& accountNumber, double amount);
    bool transfer(const std::string& fromAccount, const std::string& toAccount, double amount);
    std::vector<Transaction> getTransactionHistory(const std::string& accountNumber);
    
    // Reporting
    void generateAccountSummary() const;
    void generateTransactionReport() const;
    void generateDailySummary() const;
    void generateMonthlyReport() const;
    void displaySystemStatistics() const;
    
    // Data management
    void backupData();
    void restoreData();
    
    // Getters
    std::string getBankName() const { return bankName; }
    size_t getAccountCount() const { return accounts.size(); }
    size_t getTransactionCount() const { return transactions.size(); }
};

#endif // BANK_H