package main

import (
	"encoding/json"
	"fmt"
	"regexp"
	"strconv"
	"strings"
	"time"
)

// DataFormatter provides data formatting utilities
type DataFormatter struct {
	dateFormat     string
	timeFormat     string
	numberFormat   string
	currencySymbol string
}

// NewDataFormatter creates a new data formatter
func NewDataFormatter() *DataFormatter {
	return &DataFormatter{
		dateFormat:     "2006-01-02",
		timeFormat:     "15:04:05",
		numberFormat:   "%.2f",
		currencySymbol: "$",
	}
}

func (df *DataFormatter) SetDateFormat(format string) {
	df.dateFormat = format
}

func (df *DataFormatter) SetTimeFormat(format string) {
	df.timeFormat = format
}

func (df *DataFormatter) SetNumberFormat(format string) {
	df.numberFormat = format
}

func (df *DataFormatter) SetCurrencySymbol(symbol string) {
	df.currencySymbol = symbol
}

func (df *DataFormatter) FormatDate(t time.Time) string {
	return t.Format(df.dateFormat)
}

func (df *DataFormatter) FormatTime(t time.Time) string {
	return t.Format(df.timeFormat)
}

func (df *DataFormatter) FormatDateTime(t time.Time) string {
	return t.Format(df.dateFormat + " " + df.timeFormat)
}

func (df *DataFormatter) FormatRelativeTime(t time.Time) string {
	now := time.Now()
	diff := now.Sub(t)

	if diff < time.Minute {
		return "just now"
	} else if diff < time.Hour {
		minutes := int(diff.Minutes())
		return fmt.Sprintf("%d minute%s ago", minutes, df.plural(minutes))
	} else if diff < 24*time.Hour {
		hours := int(diff.Hours())
		return fmt.Sprintf("%d hour%s ago", hours, df.plural(hours))
	} else if diff < 7*24*time.Hour {
		days := int(diff.Hours() / 24)
		return fmt.Sprintf("%d day%s ago", days, df.plural(days))
	} else if diff < 30*24*time.Hour {
		weeks := int(diff.Hours() / (24 * 7))
		return fmt.Sprintf("%d week%s ago", weeks, df.plural(weeks))
	} else if diff < 365*24*time.Hour {
		months := int(diff.Hours() / (24 * 30))
		return fmt.Sprintf("%d month%s ago", months, df.plural(months))
	} else {
		years := int(diff.Hours() / (24 * 365))
		return fmt.Sprintf("%d year%s ago", years, df.plural(years))
	}
}

func (df *DataFormatter) FormatDuration(d time.Duration) string {
	if d < time.Second {
		milliseconds := int(d.Milliseconds())
		return fmt.Sprintf("%d millisecond%s", milliseconds, df.plural(milliseconds))
	} else if d < time.Minute {
		seconds := int(d.Seconds())
		return fmt.Sprintf("%d second%s", seconds, df.plural(seconds))
	} else if d < time.Hour {
		minutes := int(d.Minutes())
		seconds := int(d.Seconds()) % 60
		if seconds > 0 {
			return fmt.Sprintf("%d minute%s %d second%s", minutes, df.plural(minutes), seconds, df.plural(seconds))
		}
		return fmt.Sprintf("%d minute%s", minutes, df.plural(minutes))
	} else if d < 24*time.Hour {
		hours := int(d.Hours())
		minutes := int(d.Minutes()) % 60
		if minutes > 0 {
			return fmt.Sprintf("%d hour%s %d minute%s", hours, df.plural(hours), minutes, df.plural(minutes))
		}
		return fmt.Sprintf("%d hour%s", hours, df.plural(hours))
	} else {
		days := int(d.Hours() / 24)
		hours := int(d.Hours()) % 24
		if hours > 0 {
			return fmt.Sprintf("%d day%s %d hour%s", days, df.plural(days), hours, df.plural(hours))
		}
		return fmt.Sprintf("%d day%s", days, df.plural(days))
	}
}

func (df *DataFormatter) FormatFileSize(bytes int64) string {
	const unit = 1024
	if bytes < unit {
		return fmt.Sprintf("%d B", bytes)
	}
	div, exp := int64(unit), 0
	for n := bytes / unit; n >= unit; n /= unit {
		div *= unit
		exp++
	}
	return fmt.Sprintf("%.1f %cB", float64(bytes)/float64(div), "KMGTPE"[exp])
}

func (df *DataFormatter) FormatNumber(number float64) string {
	return fmt.Sprintf(df.numberFormat, number)
}

func (df *DataFormatter) FormatInteger(number int) string {
	str := strconv.Itoa(number)
	var result string
	for i, digit := range str {
		if i > 0 && (len(str)-i)%3 == 0 {
			result += ","
		}
		result += string(digit)
	}
	return result
}

func (df *DataFormatter) FormatCurrency(amount float64) string {
	return df.currencySymbol + fmt.Sprintf(df.numberFormat, amount)
}

func (df *DataFormatter) FormatPercent(value float64) string {
	return fmt.Sprintf("%.1f%%", value*100)
}

func (df *DataFormatter) FormatJSON(data interface{}) (string, error) {
	jsonBytes, err := json.MarshalIndent(data, "", "  ")
	if err != nil {
		return "", err
	}
	return string(jsonBytes), nil
}

func (df *DataFormatter) FormatPhone(phone string) string {
	digits := regexpReplaceAll(`\D`, phone, "")
	switch len(digits) {
	case 10:
		return fmt.Sprintf("(%s) %s-%s", digits[:3], digits[3:6], digits[6:])
	case 11:
		return fmt.Sprintf("+%s (%s) %s-%s", digits[:1], digits[1:4], digits[4:7], digits[7:])
	default:
		return phone
	}
}

func (df *DataFormatter) FormatCreditCard(cardNumber string) string {
	digits := regexpReplaceAll(`\D`, cardNumber, "")
	switch len(digits) {
	case 16:
		return fmt.Sprintf("%s %s %s %s", digits[:4], digits[4:8], digits[8:12], digits[12:])
	case 15:
		return fmt.Sprintf("%s %s %s", digits[:4], digits[4:10], digits[10:])
	case 14:
		return fmt.Sprintf("%s %s %s %s", digits[:4], digits[4:8], digits[8:12], digits[12:])
	default:
		return cardNumber
	}
}

func (df *DataFormatter) FormatSSN(ssn string) string {
	digits := regexpReplaceAll(`\D`, ssn, "")
	if len(digits) == 9 {
		return fmt.Sprintf("%s-%s-%s", digits[:3], digits[3:5], digits[5:])
	}
	return ssn
}

func (df *DataFormatter) FormatMACAddress(mac string) string {
	hex := regexpReplaceAll(`[^0-9a-fA-F]`, mac, "")
	if len(hex) == 12 {
		var parts []string
		for i := 0; i < 12; i += 2 {
			parts = append(parts, strings.ToUpper(hex[i:i+2]))
		}
		return strings.Join(parts, ":")
	}
	return mac
}

func (df *DataFormatter) FormatOrdinal(n int) string {
	if n < 0 {
		return fmt.Sprintf("%d", n)
	}
	switch n % 100 {
	case 11, 12, 13:
		return fmt.Sprintf("%dth", n)
	default:
		switch n % 10 {
		case 1:
			return fmt.Sprintf("%dst", n)
		case 2:
			return fmt.Sprintf("%dnd", n)
		case 3:
			return fmt.Sprintf("%drd", n)
		default:
			return fmt.Sprintf("%dth", n)
		}
	}
}

func (df *DataFormatter) plural(n int) string {
	if n == 1 {
		return ""
	}
	return "s"
}

func regexpReplaceAll(pattern, text, replacement string) string {
	re := regexp.MustCompile(pattern)
	return re.ReplaceAllString(text, replacement)
}

func main() {
	fmt.Println("=== Data Formatter Demo ===")
	df := NewDataFormatter()

	// Numbers & Currency
	fmt.Printf("Formatted Integer: %s\n", df.FormatInteger(1234567))
	fmt.Printf("Formatted Currency: %s\n", df.FormatCurrency(1234.567))
	fmt.Printf("File Size: %s\n", df.FormatFileSize(10485760))

	// Regex Masking & Formatters
	fmt.Printf("Formatted Phone: %s\n", df.FormatPhone("1234567890"))
	fmt.Printf("Formatted Card: %s\n", df.FormatCreditCard("1234567812345678"))
	fmt.Printf("Formatted MAC: %s\n", df.FormatMACAddress("aabbccddeeff"))
	fmt.Printf("Ordinal 23: %s\n", df.FormatOrdinal(23))

	// Relative Time
	pastTime := time.Now().Add(-2 * time.Hour)
	fmt.Printf("Relative Time: %s\n", df.FormatRelativeTime(pastTime))
}
