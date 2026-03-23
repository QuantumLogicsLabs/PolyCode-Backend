#ifndef ACCOUNT_H
#define ACCOUNT_H

#include <string>
#include <vector>
#include <memory>
#include <chrono>
#include <iomanip>

enum class AccountType {
    CHECKING,
    SAVINGS,
    LOAN
};

class Transaction;

class Account {
protected:
    std::string accountNumber;
    std::string accountHolderName;
    AccountType accountType;
    double balance;
    double interestRate;
    std::vector<Transaction> transactionHistory;
    std::chrono::system_clock::time_point createdAt;
    bool isActive;
    
    void addTransaction(const Transaction& transaction);
    
public:
    Account(const std::string& number, const std::string& holderName, 
           AccountType type, double initialBalance, double rate = 0.0);
    virtual ~Account() = default;
    
    // Core operations
    virtual bool deposit(double amount);
    virtual bool withdraw(double amount);
    virtual bool transfer(const std::string& toAccount, double amount, Bank* bank);
    
    // Information methods
    virtual void displayDetails() const;
    virtual void displayTransactionHistory() const;
    
    // Getters and setters
    std::string getAccountNumber() const { return accountNumber; }
    std::string getAccountHolderName() const { return accountHolderName; }
    void setAccountHolderName(const std::string& name) { accountHolderName = name; }
    AccountType getAccountType() const { return accountType; }
    double getBalance() const { return balance; }
    double getInterestRate() const { return interestRate; }
    bool getIsActive() const { return isActive; }
    void setActive(bool active) { isActive = active; }
    std::chrono::system_clock::time_point getCreatedAt() const { return createdAt; }
    
    // Interest calculation
    virtual void calculateInterest();
    
    // Validation
    virtual bool isValidForTransfer(double amount) const;
    
    // Static methods
    static std::string accountTypeToString(AccountType type);
    static AccountType stringToAccountType(const std::string& type);
};

class CheckingAccount : public Account {
private:
    double overdraftLimit;
    double monthlyFee;
    
public:
    CheckingAccount(const std::string& number, const std::string& holderName, 
                   double initialBalance, double overdraft = 500.0, double fee = 10.0);
    
    bool withdraw(double amount) override;
    void displayDetails() const override;
    void applyMonthlyFee();
    
    double getOverdraftLimit() const { return overdraftLimit; }
    double getMonthlyFee() const { return monthlyFee; }
};

class SavingsAccount : public Account {
private:
    int withdrawalCount;
    int maxWithdrawalsPerMonth;
    double minimumBalance;
    
public:
    SavingsAccount(const std::string& number, const std::string& holderName, 
                  double initialBalance, double interestRate = 0.02, 
                  int maxWithdrawals = 6, double minBalance = 100.0);
    
    bool withdraw(double amount) override;
    void calculateInterest() override;
    void displayDetails() const override;
    void resetMonthlyWithdrawals();
    
    int getWithdrawalCount() const { return withdrawalCount; }
    int getMaxWithdrawalsPerMonth() const { return maxWithdrawalsPerMonth; }
    double getMinimumBalance() const { return minimumBalance; }
};

class LoanAccount : public Account {
private:
    double principalAmount;
    double interestRate;
    int loanTermMonths;
    double monthlyPayment;
    std::chrono::system_clock::time_point nextPaymentDue;
    
public:
    LoanAccount(const std::string& number, const std::string& holderName, 
               double principal, double rate, int termMonths);
    
    bool withdraw(double amount) override;
    void displayDetails() const override;
    bool makePayment(double amount);
    void calculateNextPaymentDue();
    
    double getPrincipalAmount() const { return principalAmount; }
    double getMonthlyPayment() const { return monthlyPayment; }
    int getLoanTermMonths() const { return loanTermMonths; }
    std::chrono::system_clock::time_point getNextPaymentDue() const { return nextPaymentDue; }
};

#endif // ACCOUNT_H