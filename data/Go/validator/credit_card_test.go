package validator

import (
	"fmt"
	"testing"
)

func TestCreditCard(t *testing.T) {
	// Validator initialize kar rahe hain
	v := NewCreditCardValidator()

	// Ek dummy Visa card number check kar rahe hain
	testCard := "411111111111a111"

	fmt.Println("--- Credit Card Test Result ---")
	fmt.Println("Card Number:", testCard)

	// Validate function ko call kiya
	isValid, cardType, err := v.Validate(testCard)

	fmt.Println("Is Valid:", isValid)
	fmt.Println("Card Type:", cardType)

	if err != nil {
		fmt.Println("Error:", err)
	}
	fmt.Println("-------------------------------")
}
