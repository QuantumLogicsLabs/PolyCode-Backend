import java.sql.*;
import java.util.*;
import java.util.concurrent.*;

public class JDBCExample {
    // Database connection details
    private static final String DB_URL = "jdbc:h2:mem:testdb";
    private static final String DB_USER = "sa";
    private static final String DB_PASSWORD = "";
    
    public static void main(String[] args) {
        // Basic JDBC Operations
        System.out.println("=== Basic JDBC Operations ===");
        demonstrateBasicOperations();
        
        // Prepared Statements
        System.out.println("\n=== Prepared Statements ===");
        demonstratePreparedStatements();
        
        // Transactions
        System.out.println("\n=== Transactions ===");
        demonstrateTransactions();
        
        // Batch Processing
        System.out.println("\n=== Batch Processing ===");
        demonstrateBatchProcessing();
        
        // Connection Pooling
        System.out.println("\n=== Connection Pooling ===");
        demonstrateConnectionPooling();
        
        // Metadata
        System.out.println("\n=== Database Metadata ===");
        demonstrateMetadata();
        
        // Performance Considerations
        System.out.println("\n=== Performance Considerations ===");
        performanceComparison();
    }
    
    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
    }
    
    public static void createTables(Connection conn) throws SQLException {
        try (Statement stmt = conn.createStatement()) {
            // Create employees table
            String createEmployeesTable = "CREATE TABLE employees (" +
                    "id INT PRIMARY KEY AUTO_INCREMENT, " +
                    "name VARCHAR(100) NOT NULL, " +
                    "email VARCHAR(100) UNIQUE NOT NULL, " +
                    "department VARCHAR(50), " +
                    "salary DECIMAL(10,2), " +
                    "hire_date DATE, " +
                    "is_active BOOLEAN DEFAULT TRUE)";
            
            stmt.execute(createEmployeesTable);
            
            // Create departments table
            String createDepartmentsTable = "CREATE TABLE departments (" +
                    "id INT PRIMARY KEY AUTO_INCREMENT, " +
                    "name VARCHAR(50) UNIQUE NOT NULL, " +
                    "manager_id INT, " +
                    "budget DECIMAL(12,2))";
            
            stmt.execute(createDepartmentsTable);
            
            System.out.println("Tables created successfully");
        }
    }
    
    public static void demonstrateBasicOperations() {
        try (Connection conn = getConnection()) {
            createTables(conn);
            
            // Insert data using Statement
            try (Statement stmt = conn.createStatement()) {
                // Insert departments
                stmt.executeUpdate("INSERT INTO departments (name, budget) VALUES " +
                        "('Engineering', 500000.00), " +
                        "('Marketing', 300000.00), " +
                        "('Sales', 400000.00)");
                
                // Insert employees
                stmt.executeUpdate("INSERT INTO employees (name, email, department, salary, hire_date) VALUES " +
                        "('John Doe', 'john@company.com', 'Engineering', 75000.00, '2020-01-15'), " +
                        "('Jane Smith', 'jane@company.com', 'Marketing', 65000.00, '2020-03-20'), " +
                        "('Bob Johnson', 'bob@company.com', 'Sales', 55000.00, '2020-02-10')");
                
                System.out.println("Sample data inserted");
                
                // Query data
                ResultSet rs = stmt.executeQuery("SELECT e.name, e.email, e.salary, d.name as department " +
                        "FROM employees e JOIN departments d ON e.department = d.name " +
                        "WHERE e.salary > 60000");
                
                System.out.println("High-earning employees:");
                while (rs.next()) {
                    System.out.printf("  %s (%s) - $%.2f - %s%n",
                            rs.getString("name"),
                            rs.getString("email"),
                            rs.getDouble("salary"),
                            rs.getString("department"));
                }
                
                // Update data
                int updateCount = stmt.executeUpdate("UPDATE employees SET salary = salary * 1.10 " +
                        "WHERE department = 'Engineering'");
                System.out.println("Updated " + updateCount + " engineering salaries");
                
                // Delete data
                int deleteCount = stmt.executeUpdate("DELETE FROM employees WHERE is_active = FALSE");
                System.out.println("Deleted " + deleteCount + " inactive employees");
            }
            
        } catch (SQLException e) {
            System.err.println("JDBC error: " + e.getMessage());
        }
    }
    
    public static void demonstratePreparedStatements() {
        try (Connection conn = getConnection()) {
            // Insert with prepared statement
            String insertSQL = "INSERT INTO employees (name, email, department, salary, hire_date) VALUES (?, ?, ?, ?, ?)";
            
            try (PreparedStatement pstmt = conn.prepareStatement(insertSQL, Statement.RETURN_GENERATED_KEYS)) {
                // Insert multiple employees
                Object[][] employees = {
                    {"Alice Wilson", "alice@company.com", "Engineering", 80000.00, "2021-01-05"},
                    {"Charlie Brown", "charlie@company.com", "Engineering", 70000.00, "2021-02-15"},
                    {"Diana Prince", "diana@company.com", "Marketing", 68000.00, "2021-03-10"}
                };
                
                for (Object[] employee : employees) {
                    pstmt.setString(1, (String) employee[0]);
                    pstmt.setString(2, (String) employee[1]);
                    pstmt.setString(3, (String) employee[2]);
                    pstmt.setDouble(4, (Double) employee[3]);
                    pstmt.setDate(5, java.sql.Date.valueOf((String) employee[4]));
                    pstmt.addBatch();
                }
                
                int[] results = pstmt.executeBatch();
                System.out.println("Inserted " + Arrays.stream(results).sum() + " employees using prepared statement");
                
                // Get generated keys
                ResultSet generatedKeys = pstmt.getGeneratedKeys();
                while (generatedKeys.next()) {
                    System.out.println("Generated ID: " + generatedKeys.getInt(1));
                }
            }
            
            // Query with prepared statement
            String querySQL = "SELECT * FROM employees WHERE department = ? AND salary > ?";
            
            try (PreparedStatement pstmt = conn.prepareStatement(querySQL)) {
                pstmt.setString(1, "Engineering");
                pstmt.setDouble(2, 65000.00);
                
                ResultSet rs = pstmt.executeQuery();
                System.out.println("Engineering employees earning > $65,000:");
                while (rs.next()) {
                    System.out.printf("  %s - $%.2f%n",
                            rs.getString("name"), rs.getDouble("salary"));
                }
            }
            
        } catch (SQLException e) {
            System.err.println("Prepared statement error: " + e.getMessage());
        }
    }
    
    public static void demonstrateTransactions() {
        try (Connection conn = getConnection()) {
            conn.setAutoCommit(false); // Start transaction
            
            try {
                try (Statement stmt = conn.createStatement()) {
                    // Transfer budget from Marketing to Sales
                    stmt.executeUpdate("UPDATE departments SET budget = budget - 50000 WHERE name = 'Marketing'");
                    System.out.println("Reduced Marketing budget by $50,000");
                    
                    // Simulate potential error (comment out for success)
                    // if (Math.random() > 0.5) {
                    //     throw new SQLException("Simulated error during transaction");
                    // }
                    
                    stmt.executeUpdate("UPDATE departments SET budget = budget + 50000 WHERE name = 'Sales'");
                    System.out.println("Increased Sales budget by $50,000");
                }
                
                conn.commit();
                System.out.println("Transaction committed successfully");
                
                // Verify results
                try (Statement stmt = conn.createStatement();
                     ResultSet rs = stmt.executeQuery("SELECT name, budget FROM departments WHERE name IN ('Marketing', 'Sales')")) {
                    
                    System.out.println("Department budgets after transaction:");
                    while (rs.next()) {
                        System.out.printf("  %s: $%.2f%n",
                                rs.getString("name"), rs.getDouble("budget"));
                    }
                }
                
            } catch (SQLException e) {
                conn.rollback();
                System.err.println("Transaction rolled back: " + e.getMessage());
            }
            
        } catch (SQLException e) {
            System.err.println("Transaction error: " + e.getMessage());
        }
    }
    
    public static void demonstrateBatchProcessing() {
        try (Connection conn = getConnection()) {
            // Disable auto-commit for batch
            conn.setAutoCommit(false);
            
            try (PreparedStatement pstmt = conn.prepareStatement(
                    "INSERT INTO employees (name, email, department, salary, hire_date) VALUES (?, ?, ?, ?, ?)")) {
                
                // Add batch operations
                for (int i = 1; i <= 100; i++) {
                    pstmt.setString(1, "Employee " + i);
                    pstmt.setString(2, "employee" + i + "@company.com");
                    pstmt.setString(3, "Engineering");
                    pstmt.setDouble(4, 50000.00 + (i * 100));
                    pstmt.setDate(5, java.sql.Date.valueOf("2021-01-" + String.format("%02d", i % 28 + 1)));
                    pstmt.addBatch();
                }
                
                long startTime = System.nanoTime();
                int[] results = pstmt.executeBatch();
                long endTime = System.nanoTime();
                
                conn.commit();
                
                System.out.println("Batch insert completed:");
                System.out.println("  Records inserted: " + Arrays.stream(results).sum());
                System.out.println("  Time taken: " + (endTime - startTime) / 1000000.0 + " ms");
                
            } catch (SQLException e) {
                conn.rollback();
                System.err.println("Batch processing error: " + e.getMessage());
            }
            
        } catch (SQLException e) {
            System.err.println("Batch error: " + e.getMessage());
        }
    }
    
    public static void demonstrateConnectionPooling() {
        // Simple connection pool implementation
        ConnectionPool pool = new ConnectionPool(5, DB_URL, DB_USER, DB_PASSWORD);
        
        // Simulate multiple threads using connections
        ExecutorService executor = Executors.newFixedThreadPool(10);
        CountDownLatch latch = new CountDownLatch(10);
        
        for (int i = 0; i < 10; i++) {
            final int threadId = i;
            executor.submit(() -> {
                try (Connection conn = pool.getConnection()) {
                    // Use connection for database operations
                    try (Statement stmt = conn.createStatement();
                         ResultSet rs = stmt.executeQuery("SELECT COUNT(*) as count FROM employees")) {
                        
                        if (rs.next()) {
                            System.out.println("Thread " + threadId + " - Employee count: " + rs.getInt("count"));
                        }
                    }
                    
                    // Simulate work
                    Thread.sleep(100);
                    
                } catch (SQLException | InterruptedException e) {
                    System.err.println("Thread " + threadId + " error: " + e.getMessage());
                } finally {
                    latch.countDown();
                }
            });
        }
        
        try {
            latch.await(10, TimeUnit.SECONDS);
        } catch (InterruptedException e) {
            System.err.println("Pool test interrupted: " + e.getMessage());
        }
        
        executor.shutdown();
        pool.closeAll();
        System.out.println("Connection pool test completed");
    }
    
    public static void demonstrateMetadata() {
        try (Connection conn = getConnection()) {
            DatabaseMetaData dbMetaData = conn.getMetaData();
            
            // Database information
            System.out.println("Database Information:");
            System.out.println("  Product Name: " + dbMetaData.getDatabaseProductName());
            System.out.println("  Product Version: " + dbMetaData.getDatabaseProductVersion());
            System.out.println("  Driver Name: " + dbMetaData.getDriverName());
            System.out.println("  Driver Version: " + dbMetaData.getDriverVersion());
            
            // Table information
            System.out.println("\nTables:");
            try (ResultSet tables = dbMetaData.getTables(null, null, "%", new String[]{"TABLE"})) {
                while (tables.next()) {
                    System.out.println("  " + tables.getString("TABLE_NAME"));
                }
            }
            
            // Column information for employees table
            System.out.println("\nEmployees Table Columns:");
            try (ResultSet columns = dbMetaData.getColumns(null, null, "EMPLOYEES", null)) {
                while (columns.next()) {
                    System.out.printf("  %s - %s(%d) - %s%n",
                            columns.getString("COLUMN_NAME"),
                            columns.getString("TYPE_NAME"),
                            columns.getInt("COLUMN_SIZE"),
                            columns.getString("IS_NULLABLE"));
                }
            }
            
        } catch (SQLException e) {
            System.err.println("Metadata error: " + e.getMessage());
        }
    }
    
    public static void performanceComparison() {
        final int OPERATIONS = 1000;
        
        // Test individual inserts
        long startTime = System.nanoTime();
        
        try (Connection conn = getConnection()) {
            for (int i = 0; i < OPERATIONS; i++) {
                try (PreparedStatement pstmt = conn.prepareStatement(
                        "INSERT INTO employees (name, email, department, salary, hire_date) VALUES (?, ?, ?, ?, ?)")) {
                    
                    pstmt.setString(1, "Perf Test " + i);
                    pstmt.setString(2, "perf" + i + "@test.com");
                    pstmt.setString(3, "Test");
                    pstmt.setDouble(4, 50000.00);
                    pstmt.setDate(5, java.sql.Date.valueOf("2021-01-01"));
                    pstmt.executeUpdate();
                }
            }
        } catch (SQLException e) {
            System.err.println("Individual insert error: " + e.getMessage());
        }
        
        long endTime = System.nanoTime();
        System.out.println("Individual inserts (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // Test batch inserts
        startTime = System.nanoTime();
        
        try (Connection conn = getConnection()) {
            conn.setAutoCommit(false);
            
            try (PreparedStatement pstmt = conn.prepareStatement(
                    "INSERT INTO employees (name, email, department, salary, hire_date) VALUES (?, ?, ?, ?, ?)")) {
                
                for (int i = 0; i < OPERATIONS; i++) {
                    pstmt.setString(1, "Batch Test " + i);
                    pstmt.setString(2, "batch" + i + "@test.com");
                    pstmt.setString(3, "Test");
                    pstmt.setDouble(4, 50000.00);
                    pstmt.setDate(5, java.sql.Date.valueOf("2021-01-01"));
                    pstmt.addBatch();
                }
                
                pstmt.executeBatch();
                conn.commit();
            }
            
        } catch (SQLException e) {
            System.err.println("Batch insert error: " + e.getMessage());
        }
        
        endTime = System.nanoTime();
        System.out.println("Batch inserts (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        System.out.println("\nPerformance tip: Batch operations are significantly faster than individual operations");
    }
}

// Simple Connection Pool Implementation
class ConnectionPool {
    private final java.util.concurrent.BlockingQueue<Connection> pool;
    private final String url;
    private final String user;
    private final String password;
    private volatile boolean isShutdown;
    
    public ConnectionPool(int size, String url, String user, String password) {
        this.pool = new java.util.concurrent.LinkedBlockingQueue<>(size);
        this.url = url;
        this.user = user;
        this.password = password;
        this.isShutdown = false;
        
        // Initialize pool with connections
        for (int i = 0; i < size; i++) {
            try {
                pool.add(DriverManager.getConnection(url, user, password));
            } catch (SQLException e) {
                System.err.println("Failed to create connection: " + e.getMessage());
            }
        }
    }
    
    public Connection getConnection() throws SQLException {
        if (isShutdown) {
            throw new SQLException("Connection pool is shutdown");
        }
        
        try {
            return pool.take();
        } catch (InterruptedException e) {
            Thread.currentThread().interrupt();
            throw new SQLException("Interrupted while waiting for connection", e);
        }
    }
    
    public void releaseConnection(Connection conn) {
        if (conn != null && !isShutdown) {
            pool.offer(conn);
        }
    }
    
    public void closeAll() {
        isShutdown = true;
        for (Connection conn : pool) {
            try {
                conn.close();
            } catch (SQLException e) {
                System.err.println("Error closing connection: " + e.getMessage());
            }
        }
        pool.clear();
    }
    
    // Getters for debugging/monitoring
    public String getUrl() { return url; }
    public String getUser() { return user; }
    public String getPassword() { return password; }
    public int getPoolSize() { return pool.size(); }
    public boolean isShutdown() { return isShutdown; }
}
