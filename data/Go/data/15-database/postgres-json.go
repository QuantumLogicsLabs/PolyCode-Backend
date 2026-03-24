package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"time"

	_ "github.com/lib/pq"
)

type Order struct {
	ID          int                    `json:"id"`
	CustomerID  int                    `json:"customer_id"`
	Items       []OrderItem            `json:"items"`
	Shipping    ShippingInfo           `json:"shipping"`
	Payment     PaymentInfo            `json:"payment"`
	Status      string                 `json:"status"`
	Total       float64                `json:"total"`
	CreatedAt   time.Time              `json:"created_at"`
	UpdatedAt   time.Time              `json:"updated_at"`
	Metadata    map[string]interface{} `json:"metadata"`
}

type OrderItem struct {
	ProductID int     `json:"product_id"`
	Name      string  `json:"name"`
	Quantity  int     `json:"quantity"`
	Price     float64 `json:"price"`
	Subtotal  float64 `json:"subtotal"`
}

type ShippingInfo struct {
	Address    string `json:"address"`
	City       string `json:"city"`
	State      string `json:"state"`
	PostalCode string `json:"postal_code"`
	Country    string `json:"country"`
	Method     string `json:"method"`
	Tracking   string `json:"tracking,omitempty"`
}

type PaymentInfo struct {
	Method      string    `json:"method"`
	Status      string    `json:"status"`
	Transaction string    `json:"transaction"`
	ProcessedAt time.Time `json:"processed_at"`
}

type DatabaseConfig struct {
	Host     string
	Port     int
	User     string
	Password string
	Database string
	SSLMode  string
}

func main() {
	fmt.Println("=== PostgreSQL with JSON Operations ===")

	config := DatabaseConfig{
		Host:     "localhost",
		Port:     5432,
		User:     "postgres",
		Password: "password", // Change in production
		Database: "testdb",
		SSLMode:  "disable",
	}

	db, err := connectToPostgreSQL(config)
	if err != nil {
		log.Fatal("Failed to connect to database:", err)
	}
	defer db.Close()

	if err := createOrdersTable(db); err != nil {
		log.Fatal("Failed to create table:", err)
	}

	if err := demonstrateJSONOperations(db); err != nil {
		log.Fatal("JSON operations failed:", err)
	}

	if err := demonstrateJSONQueries(db); err != nil {
		log.Fatal("JSON queries failed:", err)
	}

	if err := demonstrateJSONAggregation(db); err != nil {
		log.Fatal("JSON aggregation failed:", err)
	}
}

func connectToPostgreSQL(config DatabaseConfig) (*sql.DB, error) {
	connStr := fmt.Sprintf("host=%s port=%d user=%s password=%s dbname=%s sslmode=%s",
		config.Host, config.Port, config.User, config.Password, config.Database, config.SSLMode)

	db, err := sql.Open("postgres", connStr)
	if err != nil {
		return nil, fmt.Errorf("failed to open database connection: %w", err)
	}

	if err := db.Ping(); err != nil {
		return nil, fmt.Errorf("failed to ping database: %w", err)
	}

	fmt.Println("✓ Connected to PostgreSQL database")
	return db, nil
}

func createOrdersTable(db *sql.DB) error {
	query := `
	CREATE TABLE IF NOT EXISTS orders (
		id SERIAL PRIMARY KEY,
		customer_id INTEGER NOT NULL,
		items JSONB NOT NULL,
		shipping JSONB NOT NULL,
		payment JSONB NOT NULL,
		status VARCHAR(50) NOT NULL DEFAULT 'pending',
		total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		metadata JSONB,
		CONSTRAINT valid_status CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled'))
	);

	-- Create indexes for JSONB fields
	CREATE INDEX IF NOT EXISTS idx_orders_items ON orders USING GIN (items);
	CREATE INDEX IF NOT EXISTS idx_orders_shipping ON orders USING GIN (shipping);
	CREATE INDEX IF NOT EXISTS idx_orders_metadata ON orders USING GIN (metadata);
	CREATE INDEX IF NOT EXISTS idx_orders_customer_id ON orders (customer_id);
	CREATE INDEX IF NOT EXISTS idx_orders_status ON orders (status);
	CREATE INDEX IF NOT EXISTS idx_orders_created_at ON orders (created_at);

	-- Create a trigger to update updated_at timestamp
	CREATE OR REPLACE FUNCTION update_updated_at_column()
	RETURNS TRIGGER AS $$
	BEGIN
		NEW.updated_at = CURRENT_TIMESTAMP;
		RETURN NEW;
	END;
	$$ language 'plpgsql';

	CREATE TRIGGER update_orders_updated_at 
		BEFORE UPDATE ON orders 
		FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
	`

	_, err := db.Exec(query)
	if err != nil {
		return fmt.Errorf("failed to create orders table: %w", err)
	}

	fmt.Println("✓ Orders table created successfully")
	return nil
}

func demonstrateJSONOperations(db *sql.DB) error {
	fmt.Println("\n--- JSON Operations ---")

	// Create sample order
	order := Order{
		CustomerID: 1001,
		Items: []OrderItem{
			{ProductID: 1, Name: "Laptop", Quantity: 1, Price: 1299.99, Subtotal: 1299.99},
			{ProductID: 2, Name: "Mouse", Quantity: 2, Price: 29.99, Subtotal: 59.98},
			{ProductID: 3, Name: "Keyboard", Quantity: 1, Price: 79.99, Subtotal: 79.99},
		},
		Shipping: ShippingInfo{
			Address:    "123 Main St",
			City:       "New York",
			State:      "NY",
			PostalCode: "10001",
			Country:    "USA",
			Method:     "Standard",
		},
		Payment: PaymentInfo{
			Method:      "Credit Card",
			Status:      "Completed",
			Transaction: "TXN123456",
			ProcessedAt: time.Now(),
		},
		Status: "pending",
		Total:  1439.96,
		Metadata: map[string]interface{}{
			"source":     "web",
			"campaign":   "summer_sale",
			"device":     "mobile",
			"ip_address": "192.168.1.100",
		},
	}

	// Insert order with JSON data
	orderID, err := insertOrder(db, order)
	if err != nil {
		return fmt.Errorf("failed to insert order: %w", err)
	}
	fmt.Printf("✓ Inserted order with ID: %d\n", orderID)

	// Retrieve and verify order
	retrieved, err := getOrder(db, orderID)
	if err != nil {
		return fmt.Errorf("failed to retrieve order: %w", err)
	}
	fmt.Printf("✓ Retrieved order: %s (Customer: %d, Total: $%.2f)\n",
		retrieved.Status, retrieved.CustomerID, retrieved.Total)

	// Update JSON field
	if err := updateOrderStatus(db, orderID, "processing"); err != nil {
		return fmt.Errorf("failed to update order status: %w", err)
	}

	// Add tracking info to shipping JSON
	if err := addTrackingInfo(db, orderID, "TRACK123456789"); err != nil {
		return fmt.Errorf("failed to add tracking info: %w", err)
	}

	return nil
}

func insertOrder(db *sql.DB, order Order) (int64, error) {
	itemsJSON, err := json.Marshal(order.Items)
	if err != nil {
		return 0, fmt.Errorf("failed to marshal items: %w", err)
	}

	shippingJSON, err := json.Marshal(order.Shipping)
	if err != nil {
		return 0, fmt.Errorf("failed to marshal shipping: %w", err)
	}

	paymentJSON, err := json.Marshal(order.Payment)
	if err != nil {
		return 0, fmt.Errorf("failed to marshal payment: %w", err)
	}

	metadataJSON, err := json.Marshal(order.Metadata)
	if err != nil {
		return 0, fmt.Errorf("failed to marshal metadata: %w", err)
	}

	query := `
	INSERT INTO orders (customer_id, items, shipping, payment, status, total, metadata)
	VALUES ($1, $2, $3, $4, $5, $6, $7)
	RETURNING id`

	var id int64
	err = db.QueryRow(query, order.CustomerID, itemsJSON, shippingJSON, paymentJSON,
		order.Status, order.Total, metadataJSON).Scan(&id)
	if err != nil {
		return 0, fmt.Errorf("failed to insert order: %w", err)
	}

	return id, nil
}

func getOrder(db *sql.DB, id int64) (*Order, error) {
	query := `
	SELECT id, customer_id, items, shipping, payment, status, total, created_at, updated_at, metadata
	FROM orders WHERE id = $1`

	var order Order
	var itemsJSON, shippingJSON, paymentJSON, metadataJSON string

	err := db.QueryRow(query, id).Scan(
		&order.ID, &order.CustomerID, &itemsJSON, &shippingJSON, &paymentJSON,
		&order.Status, &order.Total, &order.CreatedAt, &order.UpdatedAt, &metadataJSON)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, fmt.Errorf("order with ID %d not found", id)
		}
		return nil, fmt.Errorf("failed to scan order: %w", err)
	}

	// Unmarshal JSON fields
	if err := json.Unmarshal([]byte(itemsJSON), &order.Items); err != nil {
		return nil, fmt.Errorf("failed to unmarshal items: %w", err)
	}

	if err := json.Unmarshal([]byte(shippingJSON), &order.Shipping); err != nil {
		return nil, fmt.Errorf("failed to unmarshal shipping: %w", err)
	}

	if err := json.Unmarshal([]byte(paymentJSON), &order.Payment); err != nil {
		return nil, fmt.Errorf("failed to unmarshal payment: %w", err)
	}

	if err := json.Unmarshal([]byte(metadataJSON), &order.Metadata); err != nil {
		return nil, fmt.Errorf("failed to unmarshal metadata: %w", err)
	}

	return &order, nil
}

func updateOrderStatus(db *sql.DB, id int64, status string) error {
	query := `UPDATE orders SET status = $1 WHERE id = $2`
	result, err := db.Exec(query, status, id)
	if err != nil {
		return fmt.Errorf("failed to update order status: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("no order found with ID %d", id)
	}

	fmt.Printf("✓ Updated order %d status to '%s'\n", id, status)
	return nil
}

func addTrackingInfo(db *sql.DB, id int64, trackingNumber string) error {
	// Update JSON field using PostgreSQL JSON operators
	query := `
	UPDATE orders 
	SET shipping = jsonb_set(shipping, '{tracking}', to_jsonb($1::text))
	WHERE id = $2`

	result, err := db.Exec(query, trackingNumber, id)
	if err != nil {
		return fmt.Errorf("failed to add tracking info: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("no order found with ID %d", id)
	}

	fmt.Printf("✓ Added tracking number '%s' to order %d\n", trackingNumber, id)
	return nil
}

func demonstrateJSONQueries(db *sql.DB) error {
	fmt.Println("\n--- JSON Queries ---")

	// Insert more sample orders for querying
	if err := insertSampleOrders(db); err != nil {
		return fmt.Errorf("failed to insert sample orders: %w", err)
	}

	// Query orders by item name (JSON contains)
	if err := queryOrdersByItemName(db, "Laptop"); err != nil {
		return fmt.Errorf("failed to query by item name: %w", err)
	}

	// Query orders by shipping state (JSON path)
	if err := queryOrdersByShippingState(db, "CA"); err != nil {
		return fmt.Errorf("failed to query by shipping state: %w", err)
	}

	// Query orders by payment method (JSON field)
	if err := queryOrdersByPaymentMethod(db, "Credit Card"); err != nil {
		return fmt.Errorf("failed to query by payment method: %w", err)
	}

	// Extract specific JSON fields
	if err := extractJSONFields(db); err != nil {
		return fmt.Errorf("failed to extract JSON fields: %w", err)
	}

	return nil
}

func insertSampleOrders(db *sql.DB) error {
	orders := []Order{
		{
			CustomerID: 1002,
			Items: []OrderItem{
				{ProductID: 4, Name: "Tablet", Quantity: 1, Price: 699.99, Subtotal: 699.99},
			},
			Shipping: ShippingInfo{
				Address:    "456 Oak Ave", City: "Los Angeles", State: "CA",
				PostalCode: "90001", Country: "USA", Method: "Express",
			},
			Payment: PaymentInfo{
				Method: "PayPal", Status: "Completed", Transaction: "TXN789012",
				ProcessedAt: time.Now(),
			},
			Status: "shipped",
			Total:  699.99,
			Metadata: map[string]interface{}{"source": "mobile", "campaign": "flash_sale"},
		},
		{
			CustomerID: 1003,
			Items: []OrderItem{
				{ProductID: 5, Name: "Monitor", Quantity: 2, Price: 399.99, Subtotal: 799.98},
			},
			Shipping: ShippingInfo{
				Address:    "789 Pine St", City: "Chicago", State: "IL",
				PostalCode: "60007", Country: "USA", Method: "Standard",
			},
			Payment: PaymentInfo{
				Method: "Credit Card", Status: "Completed", Transaction: "TXN345678",
				ProcessedAt: time.Now(),
			},
			Status: "delivered",
			Total:  799.98,
			Metadata: map[string]interface{}{"source": "web", "campaign": "new_year"},
		},
	}

	for _, order := range orders {
		_, err := insertOrder(db, order)
		if err != nil {
			return fmt.Errorf("failed to insert sample order: %w", err)
		}
	}

	fmt.Println("✓ Sample orders inserted for JSON queries")
	return nil
}

func queryOrdersByItemName(db *sql.DB, itemName string) error {
	query := `
	SELECT id, customer_id, status, total
	FROM orders 
	WHERE items::jsonb @> '[{"name": $1}]'::jsonb`

	rows, err := db.Query(query, itemName)
	if err != nil {
		return fmt.Errorf("failed to execute query: %w", err)
	}
	defer rows.Close()

	fmt.Printf("Orders containing item '%s':\n", itemName)
	for rows.Next() {
		var id, customerID int
		var status string
		var total float64

		err := rows.Scan(&id, &customerID, &status, &total)
		if err != nil {
			return fmt.Errorf("failed to scan row: %w", err)
		}

		fmt.Printf("  - Order %d (Customer %d): %s, $%.2f\n", id, customerID, status, total)
	}

	return nil
}

func queryOrdersByShippingState(db *sql.DB, state string) error {
	query := `
	SELECT id, customer_id, shipping->>'city' as city, shipping->>'method' as method
	FROM orders 
	WHERE shipping->>'state' = $1`

	rows, err := db.Query(query, state)
	if err != nil {
		return fmt.Errorf("failed to execute query: %w", err)
	}
	defer rows.Close()

	fmt.Printf("Orders shipping to state '%s':\n", state)
	for rows.Next() {
		var id, customerID int
		var city, method string

		err := rows.Scan(&id, &customerID, &city, &method)
		if err != nil {
			return fmt.Errorf("failed to scan row: %w", err)
		}

		fmt.Printf("  - Order %d (Customer %d): %s, %s shipping\n", id, customerID, city, method)
	}

	return nil
}

func queryOrdersByPaymentMethod(db *sql.DB, method string) error {
	query := `
	SELECT id, customer_id, status, payment->>'transaction' as transaction
	FROM orders 
	WHERE payment->>'method' = $1`

	rows, err := db.Query(query, method)
	if err != nil {
		return fmt.Errorf("failed to execute query: %w", err)
	}
	defer rows.Close()

	fmt.Printf("Orders paid with '%s':\n", method)
	for rows.Next() {
		var id, customerID int
		var status, transaction string

		err := rows.Scan(&id, &customerID, &status, &transaction)
		if err != nil {
			return fmt.Errorf("failed to scan row: %w", err)
		}

		fmt.Printf("  - Order %d (Customer %d): %s, TXN: %s\n", id, customerID, status, transaction)
	}

	return nil
}

func extractJSONFields(db *sql.DB) error {
	fmt.Println("\n--- Extracted JSON Fields ---")

	// Extract item names and quantities
	query := `
	SELECT id, 
		   jsonb_array_elements(items)->>'name' as item_name,
		   (jsonb_array_elements(items)->>'quantity')::int as quantity
	FROM orders
	ORDER BY id, item_name`

	rows, err := db.Query(query)
	if err != nil {
		return fmt.Errorf("failed to extract items: %w", err)
	}
	defer rows.Close()

	fmt.Println("All items in all orders:")
	for rows.Next() {
		var id, quantity int
		var itemName string

		err := rows.Scan(&id, &itemName, &quantity)
		if err != nil {
			return fmt.Errorf("failed to scan extracted item: %w", err)
		}

		fmt.Printf("  - Order %d: %s (Qty: %d)\n", id, itemName, quantity)
	}

	return nil
}

func demonstrateJSONAggregation(db *sql.DB) error {
	fmt.Println("\n--- JSON Aggregation ---")

	// Aggregate orders by status with item counts
	query := `
	SELECT status,
		   COUNT(*) as order_count,
		   SUM(total) as total_sales,
		   AVG(jsonb_array_length(items)) as avg_items_per_order
	FROM orders
	GROUP BY status
	ORDER BY status`

	rows, err := db.Query(query)
	if err != nil {
		return fmt.Errorf("failed to execute aggregation: %w", err)
	}
	defer rows.Close()

	fmt.Println("Order statistics by status:")
	for rows.Next() {
		var status string
		var orderCount int
		var totalSales, avgItems float64

		err := rows.Scan(&status, &orderCount, &totalSales, &avgItems)
		if err != nil {
			return fmt.Errorf("failed to scan aggregation: %w", err)
		}

		fmt.Printf("  - %s: %d orders, $%.2f total, %.1f avg items\n",
			status, orderCount, totalSales, avgItems)
	}

	// Create a JSON summary of all orders
	summaryQuery := `
	SELECT json_agg(
		json_build_object(
			'id', id,
			'customer_id', customer_id,
			'status', status,
			'total', total,
			'item_count', jsonb_array_length(items),
			'shipping_city', shipping->>'city'
		)
	) as orders_summary
	FROM orders`

	var summaryJSON string
	err = db.QueryRow(summaryQuery).Scan(&summaryJSON)
	if err != nil {
		return fmt.Errorf("failed to create JSON summary: %w", err)
	}

	fmt.Printf("\nOrders summary JSON length: %d characters\n", len(summaryJSON))
	fmt.Printf("First 100 characters: %s...\n", summaryJSON[:100])

	return nil
}
