package main

import (
	"fmt"
	"math"
	"sort"
)

// Strategy interface
type PaymentStrategy interface {
	Pay(amount float64) string
}

// Concrete strategies
type CreditCardPayment struct {
	cardNumber string
	cvv        string
}

func NewCreditCardPayment(cardNumber, cvv string) *CreditCardPayment {
	return &CreditCardPayment{
		cardNumber: cardNumber,
		cvv:        cvv,
	}
}

func (cc *CreditCardPayment) Pay(amount float64) string {
	return fmt.Sprintf("Paid $%.2f using Credit Card ending in %s", amount, cc.cardNumber[len(cc.cardNumber)-4:])
}

type PayPalPayment struct {
	email string
}

func NewPayPalPayment(email string) *PayPalPayment {
	return &PayPalPayment{email: email}
}

func (pp *PayPalPayment) Pay(amount float64) string {
	return fmt.Sprintf("Paid $%.2f using PayPal account %s", amount, pp.email)
}

type BankTransferPayment struct {
	accountNumber string
	routingNumber string
}

func NewBankTransferPayment(accountNumber, routingNumber string) *BankTransferPayment {
	return &BankTransferPayment{
		accountNumber: accountNumber,
		routingNumber: routingNumber,
	}
}

func (bt *BankTransferPayment) Pay(amount float64) string {
	return fmt.Sprintf("Paid $%.2f via Bank Transfer from account %s", amount, bt.accountNumber)
}

// Context
type ShoppingCart struct {
	items      []string
	prices     []float64
	payment    PaymentStrategy
}

func NewShoppingCart() *ShoppingCart {
	return &ShoppingCart{
		items:  make([]string, 0),
		prices: make([]float64, 0),
	}
}

func (sc *ShoppingCart) AddItem(item string, price float64) {
	sc.items = append(sc.items, item)
	sc.prices = append(sc.prices, price)
}

func (sc *ShoppingCart) SetPaymentStrategy(payment PaymentStrategy) {
	sc.payment = payment
}

func (sc *ShoppingCart) Checkout() string {
	total := 0.0
	for _, price := range sc.prices {
		total += price
	}
	
	fmt.Printf("Items: %v\n", sc.items)
	fmt.Printf("Total: $%.2f\n", total)
	
	if sc.payment == nil {
		return "No payment method selected"
	}
	
	return sc.payment.Pay(total)
}

// Another strategy example: Sorting strategies
type SortingStrategy interface {
	Sort(data []int) []int
}

type BubbleSortStrategy struct{}

func (bs *BubbleSortStrategy) Sort(data []int) []int {
	n := len(data)
	result := make([]int, len(data))
	copy(result, data)

	for i := 0; i < n-1; i++ {
		for j := 0; j < n-i-1; j++ {
			if result[j] > result[j+1] {
				result[j], result[j+1] = result[j+1], result[j]
			}
		}
	}
	return result
}

type QuickSortStrategy struct{}

func (qs *QuickSortStrategy) Sort(data []int) []int {
	if len(data) <= 1 {
		return data
	}

	pivot := data[len(data)/2]
	left := []int{}
	right := []int{}
	middle := []int{}

	for _, v := range data {
		if v < pivot {
			left = append(left, v)
		} else if v > pivot {
			right = append(right, v)
		} else {
			middle = append(middle, v)
		}
	}

	result := append(qs.Sort(left), middle...)
	result = append(result, qs.Sort(right)...)
	return result
}

type BuiltInSortStrategy struct{}

func (bis *BuiltInSortStrategy) Sort(data []int) []int {
	result := make([]int, len(data))
	copy(result, data)
	sort.Ints(result)
	return result
}

// Sorting context
type Sorter struct {
	strategy SortingStrategy
}

func NewSorter(strategy SortingStrategy) *Sorter {
	return &Sorter{strategy: strategy}
}

func (s *Sorter) SetStrategy(strategy SortingStrategy) {
	s.strategy = strategy
}

func (s *Sorter) Sort(data []int) []int {
	return s.strategy.Sort(data)
}

func main() {
	fmt.Println("=== Strategy Pattern Examples ===")

	// Payment strategy example
	fmt.Println("\n--- Payment Strategy Example ---")
	cart := NewShoppingCart()
	cart.AddItem("Laptop", 999.99)
	cart.AddItem("Mouse", 29.99)
	cart.AddItem("Keyboard", 79.99)

	// Try different payment strategies
	fmt.Println("\nUsing Credit Card:")
	cart.SetPaymentStrategy(NewCreditCardPayment("1234567890123456", "123"))
	fmt.Println(cart.Checkout())

	fmt.Println("\nUsing PayPal:")
	cart.SetPaymentStrategy(NewPayPalPayment("user@example.com"))
	fmt.Println(cart.Checkout())

	fmt.Println("\nUsing Bank Transfer:")
	cart.SetPaymentStrategy(NewBankTransferPayment("987654321", "111000025"))
	fmt.Println(cart.Checkout())

	// Sorting strategy example
	fmt.Println("\n--- Sorting Strategy Example ---")
	data := []int{64, 34, 25, 12, 22, 11, 90, 88, 76, 50, 42}

	sorter := NewSorter(&BubbleSortStrategy{})
	fmt.Printf("Original: %v\n", data)
	fmt.Printf("Bubble Sort: %v\n", sorter.Sort(data))

	sorter.SetStrategy(&QuickSortStrategy{})
	fmt.Printf("Quick Sort: %v\n", sorter.Sort(data))

	sorter.SetStrategy(&BuiltInSortStrategy{})
	fmt.Printf("Built-in Sort: %v\n", sorter.Sort(data))
}
