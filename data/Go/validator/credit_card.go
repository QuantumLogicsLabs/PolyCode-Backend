package validator

import (
	"fmt"
	"regexp"
	"strconv"
	"strings"
)

// CreditCardValidator validates credit card numbers
type CreditCardValidator struct {
	cardTypes map[string]*CardType
}

// CardType represents a credit card type with validation rules
type CardType struct {
	Name           string
	Patterns       []string
	Lengths        []int
	LuhnRequired   bool
	ExampleNumbers []string
}

// NewCreditCardValidator creates a new credit card validator
func NewCreditCardValidator() *CreditCardValidator {
	return &CreditCardValidator{
		cardTypes: map[string]*CardType{
			"visa": {
				Name:         "Visa",
				Patterns:     []string{"^4"},
				Lengths:      []int{13, 16, 19},
				LuhnRequired: true,
				ExampleNumbers: []string{
					"4111111111111111",
					"4012888888881881",
					"4222222222222",
				},
			},
			"mastercard": {
				Name:         "MasterCard",
				Patterns:     []string{"^5[1-5]", "^2[2-7]"},
				Lengths:      []int{16},
				LuhnRequired: true,
				ExampleNumbers: []string{
					"5555555555554444",
					"5105105105105100",
					"2223000048400011",
				},
			},
			"amex": {
				Name:         "American Express",
				Patterns:     []string{"^3[47]"},
				Lengths:      []int{15},
				LuhnRequired: true,
				ExampleNumbers: []string{
					"378282246310005",
					"371449635398431",
					"348282246310005",
				},
			},
			"discover": {
				Name:         "Discover",
				Patterns:     []string{"^6011", "^65", "^64[4-9]", "^622"},
				Lengths:      []int{16, 19},
				LuhnRequired: true,
				ExampleNumbers: []string{
					"6011111111111117",
					"6511111111111116",
					"6220000000000005",
				},
			},
			"diners": {
				Name:         "Diners Club",
				Patterns:     []string{"^3[0689]", "^30[0-5]"},
				Lengths:      []int{14, 16},
				LuhnRequired: true,
				ExampleNumbers: []string{
					"30569309025904",
					"38520000023237",
				},
			},
			"jcb": {
				Name:         "JCB",
				Patterns:     []string{"^35"},
				Lengths:      []int{16},
				LuhnRequired: true,
				ExampleNumbers: []string{
					"3530111333300000",
					"3566002020360505",
				},
			},
		},
	}
}

// Validate validates a credit card number and returns the card type
func (v *CreditCardValidator) Validate(cardNumber string) (bool, string, error) {
	// Clean the card number
	cleaned := v.cleanCardNumber(cardNumber)
	
	if cleaned == "" {
		return false, "", fmt.Errorf("empty card number")
	}

	// Check length
	if len(cleaned) < 13 || len(cleaned) > 19 {
		return false, "", fmt.Errorf("invalid card length: %d", len(cleaned))
	}

	// Find matching card type
	var matchedType *CardType
	var cardTypeName string

	for name, cardType := range v.cardTypes {
		if v.matchesCardType(cleaned, cardType) {
			matchedType = cardType
			cardTypeName = name
			break
		}
	}

	if matchedType == nil {
		return false, "", fmt.Errorf("unknown card type")
	}

	// Perform Luhn check if required
	if matchedType.LuhnRequired && !v.luhnCheck(cleaned) {
		return false, cardTypeName, fmt.Errorf("failed Luhn validation")
	}

	return true, cardTypeName, nil
}

// cleanCardNumber removes spaces and hyphens from card number
func (v *CreditCardValidator) cleanCardNumber(cardNumber string) string {
	return strings.ReplaceAll(strings.ReplaceAll(cardNumber, " ", "-"), "-", "")
}

// matchesCardType checks if card number matches a specific card type
func (v *CreditCardValidator) matchesCardType(cardNumber string, cardType *CardType) bool {
	// Check length
	lengthMatch := false
	for _, length := range cardType.Lengths {
		if len(cardNumber) == length {
			lengthMatch = true
			break
		}
	}
	if !lengthMatch {
		return false
	}

	// Check pattern
	for _, pattern := range cardType.Patterns {
		matched, _ := regexp.MatchString(pattern, cardNumber)
		if matched {
			return true
		}
	}

	return false
}

// luhnCheck performs the Luhn algorithm validation
func (v *CreditCardValidator) luhnCheck(cardNumber string) bool {
	sum := 0
	doubleDigit := false

	// Process from right to left
	for i := len(cardNumber) - 1; i >= 0; i-- {
		digit, err := strconv.Atoi(string(cardNumber[i]))
		if err != nil {
			return false
		}

		if doubleDigit {
			digit *= 2
			if digit > 9 {
				digit = (digit % 10) + 1
			}
		}

		sum += digit
		doubleDigit = !doubleDigit
	}

	return sum%10 == 0
}

// GetCardInfo returns information about a specific card type
func (v *CreditCardValidator) GetCardInfo(cardType string) (*CardType, error) {
	if ct, exists := v.cardTypes[cardType]; exists {
		return ct, nil
	}
	return nil, fmt.Errorf("unknown card type: %s", cardType)
}

// ListSupportedCards returns all supported card types
func (v *CreditCardValidator) ListSupportedCards() []string {
	var cards []string
	for name := range v.cardTypes {
		cards = append(cards, name)
	}
	return cards
}

// ValidateWithDetails provides detailed validation information
func (v *CreditCardValidator) ValidateWithDetails(cardNumber string) *CreditCardValidationResult {
	result := &CreditCardValidationResult{
		CardNumber: cardNumber,
		IsValid:    false,
	}

	// Clean the card number
	cleaned := v.cleanCardNumber(cardNumber)
	result.CleanedNumber = cleaned

	if cleaned == "" {
		result.ErrorMessage = "empty card number"
		return result
	}

	result.Length = len(cleaned)

	// Check basic length
	if len(cleaned) < 13 || len(cleaned) > 19 {
		result.ErrorMessage = fmt.Sprintf("invalid card length: %d", len(cleaned))
		return result
	}

	// Find matching card type
	for name, cardType := range v.cardTypes {
		if v.matchesCardType(cleaned, cardType) {
			result.CardType = name
			result.CardTypeName = cardType.Name
			result.LuhnRequired = cardType.LuhnRequired
			break
		}
	}

	if result.CardType == "" {
		result.ErrorMessage = "unknown card type"
		return result
	}

	// Perform Luhn check
	result.LuhnValid = v.luhnCheck(cleaned)
	if result.LuhnRequired && !result.LuhnValid {
		result.ErrorMessage = "failed Luhn validation"
		return result
	}

	result.IsValid = true
	return result
}

// CreditCardValidationResult contains detailed validation results
type CreditCardValidationResult struct {
	CardNumber     string
	CleanedNumber  string
	CardType       string
	CardTypeName   string
	Length         int
	LuhnRequired   bool
	LuhnValid      bool
	IsValid        bool
	ErrorMessage   string
}

// GenerateTestNumbers generates valid test numbers for all card types
func (v *CreditCardValidator) GenerateTestNumbers() map[string][]string {
	testNumbers := make(map[string][]string)
	
	for name, cardType := range v.cardTypes {
		testNumbers[name] = cardType.ExampleNumbers
	}
	
	return testNumbers
}
