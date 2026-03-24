using System;

namespace BankAccountDemo
{
    public class BankAccount
    {
        private decimal balance;
        private static int accountCounter = 1000;
        
        public string AccountNumber { get; }
        public string OwnerName { get; set; }
        public DateTime CreatedDate { get; }
        
        public decimal Balance
        {
            get { return balance; }
            private set { balance = value; }
        }
        
        public static int TotalAccounts { get; private set; }
        
        public BankAccount(string ownerName, decimal initialBalance = 0)
        {
            AccountNumber = GenerateAccountNumber();
            OwnerName = ownerName;
            CreatedDate = DateTime.Now;
            
            if (initialBalance >= 0)
                Balance = initialBalance;
            else
                throw new ArgumentException("Initial balance cannot be negative");
                
            TotalAccounts++;
        }
        
        private static string GenerateAccountNumber()
        {
            return $"ACC{accountCounter++:D6}";
        }
        
        public void Deposit(decimal amount)
        {
            if (amount <= 0)
                throw new ArgumentException("Deposit amount must be positive");
                
            Balance += amount;
            Console.WriteLine($"Deposited: {amount:C}. New balance: {Balance:C}");
        }
        
        public bool Withdraw(decimal amount)
        {
            if (amount <= 0)
                throw new ArgumentException("Withdrawal amount must be positive");
                
            if (amount > Balance)
            {
                Console.WriteLine("Insufficient funds");
                return false;
            }
            
            Balance -= amount;
            Console.WriteLine($"Withdrew: {amount:C}. New balance: {Balance:C}");
            return true;
        }
        
        public void DisplayInfo()
        {
            Console.WriteLine($"Account Number: {AccountNumber}");
            Console.WriteLine($"Owner: {OwnerName}");
            Console.WriteLine($"Balance: {Balance:C}");
            Console.WriteLine($"Created: {CreatedDate.ToShortDateString()}");
        }
        
        public static void DisplayBankStatistics()
        {
            Console.WriteLine($"Total bank accounts: {TotalAccounts}");
        }
    }
    
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("=== Bank Account Demo ===\n");
            
            try
            {
                // Create accounts
                BankAccount account1 = new BankAccount("John Doe", 1000);
                BankAccount account2 = new BankAccount("Jane Smith", 500);
                
                // Display account information
                account1.DisplayInfo();
                Console.WriteLine();
                account2.DisplayInfo();
                Console.WriteLine();
                
                // Perform transactions
                Console.WriteLine("=== Transactions ===");
                account1.Deposit(250);
                account1.Withdraw(100);
                account2.Withdraw(600); // Should fail
                account2.Withdraw(200);
                Console.WriteLine();
                
                // Display bank statistics
                BankAccount.DisplayBankStatistics();
                
            }
            catch (ArgumentException ex)
            {
                Console.WriteLine($"Error: {ex.Message}");
            }
        }
    }
}
