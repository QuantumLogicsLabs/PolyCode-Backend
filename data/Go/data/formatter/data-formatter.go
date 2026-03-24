package formatter

import (
	"encoding/json"
	"fmt"
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

// SetDateFormat sets the date format
func (df *DataFormatter) SetDateFormat(format string) {
	df.dateFormat = format
}

// SetTimeFormat sets the time format
func (df *DataFormatter) SetTimeFormat(format string) {
	df.timeFormat = format
}

// SetNumberFormat sets the number format
func (df *DataFormatter) SetNumberFormat(format string) {
	df.numberFormat = format
}

// SetCurrencySymbol sets the currency symbol
func (df *DataFormatter) SetCurrencySymbol(symbol string) {
	df.currencySymbol = symbol
}

// FormatDate formats a time.Time as date string
func (df *DataFormatter) FormatDate(t time.Time) string {
	return t.Format(df.dateFormat)
}

// FormatTime formats a time.Time as time string
func (df *DataFormatter) FormatTime(t time.Time) string {
	return t.Format(df.timeFormat)
}

// FormatDateTime formats a time.Time as date and time string
func (df *DataFormatter) FormatDateTime(t time.Time) string {
	return t.Format(df.dateFormat + " " + df.timeFormat)
}

// FormatRelativeTime formats time as relative time
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

// FormatDuration formats a time.Duration as human readable string
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

// FormatFileSize formats bytes as human readable file size
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

// FormatNumber formats a number with specified precision
func (df *DataFormatter) FormatNumber(number float64) string {
	return fmt.Sprintf(df.numberFormat, number)
}

// FormatInteger formats an integer with thousands separator
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

// FormatCurrency formats a number as currency
func (df *DataFormatter) FormatCurrency(amount float64) string {
	return df.currencySymbol + fmt.Sprintf(df.numberFormat, amount)
}

// FormatPercent formats a number as percentage
func (df *DataFormatter) FormatPercent(value float64) string {
	return fmt.Sprintf("%.1f%%", value*100)
}

// FormatScientific formats a number in scientific notation
func (df *DataFormatter) FormatScientific(number float64) string {
	return fmt.Sprintf("%.2e", number)
}

// FormatBinary formats a number as binary string
func (df *DataFormatter) FormatBinary(number int) string {
	return fmt.Sprintf("0b%b", number)
}

// FormatHexadecimal formats a number as hexadecimal string
func (df *DataFormatter) FormatHexadecimal(number int) string {
	return fmt.Sprintf("0x%X", number)
}

// FormatOctal formats a number as octal string
func (df *DataFormatter) FormatOctal(number int) string {
	return fmt.Sprintf("0o%o", number)
}

// FormatBoolean formats a boolean as string
func (df *DataFormatter) FormatBoolean(value bool) string {
	if value {
		return "true"
	}
	return "false"
}

// FormatYesNo formats a boolean as Yes/No
func (df *DataFormatter) FormatYesNo(value bool) string {
	if value {
		return "Yes"
	}
	return "No"
}

// FormatOnOff formats a boolean as On/Off
func (df *DataFormatter) FormatOnOff(value bool) string {
	if value {
		return "On"
	}
	return "Off"
}

// FormatJSON formats data as JSON string
func (df *DataFormatter) FormatJSON(data interface{}) (string, error) {
	jsonBytes, err := json.MarshalIndent(data, "", "  ")
	if err != nil {
		return "", err
	}
	return string(jsonBytes), nil
}

// FormatJSONCompact formats data as compact JSON string
func (df *DataFormatter) FormatJSONCompact(data interface{}) (string, error) {
	jsonBytes, err := json.Marshal(data)
	if err != nil {
		return "", err
	}
	return string(jsonBytes), nil
}

// FormatXML formats data as XML string (basic implementation)
func (df *DataFormatter) FormatXML(data interface{}) string {
	// This is a simplified XML formatter
	// In practice, you might want to use encoding/xml
	switch v := data.(type) {
	case map[string]interface{}:
		return df.formatMapAsXML(v)
	case []interface{}:
		return df.formatSliceAsXML(v)
	default:
		return fmt.Sprintf("<value>%v</value>", v)
	}
}

func (df *DataFormatter) formatMapAsXML(data map[string]interface{}) string {
	var builder strings.Builder
	builder.WriteString("<map>")
	
	for key, value := range data {
		builder.WriteString(fmt.Sprintf("<%s>%v</%s>", key, value, key))
	}
	
	builder.WriteString("</map>")
	return builder.String()
}

func (df *DataFormatter) formatSliceAsXML(data []interface{}) string {
	var builder strings.Builder
	builder.WriteString("<array>")
	
	for _, value := range data {
		builder.WriteString(fmt.Sprintf("<item>%v</item>", value))
	}
	
	builder.WriteString("</array>")
	return builder.String()
}

// FormatCSV formats data as CSV string
func (df *DataFormatter) FormatCSV(data [][]string) string {
	var builder strings.Builder
	
	for i, row := range data {
		for j, cell := range row {
			if j > 0 {
				builder.WriteString(",")
			}
			
			// Escape quotes and wrap in quotes if needed
			if strings.Contains(cell, ",") || strings.Contains(cell, "\"") || strings.Contains(cell, "\n") {
				cell = strings.ReplaceAll(cell, "\"", "\"\"")
				builder.WriteString(fmt.Sprintf("\"%s\"", cell))
			} else {
				builder.WriteString(cell)
			}
		}
		
		if i < len(data)-1 {
			builder.WriteString("\n")
		}
	}
	
	return builder.String()
}

// FormatSQL formats a SQL query with proper indentation
func (df *DataFormatter) FormatSQL(query string) string {
	// Basic SQL formatter
	query = strings.TrimSpace(query)
	
	// Split by keywords
	keywords := []string{"SELECT", "FROM", "WHERE", "ORDER BY", "GROUP BY", "HAVING", "INSERT INTO", "VALUES", "UPDATE", "DELETE", "JOIN", "INNER JOIN", "LEFT JOIN", "RIGHT JOIN", "OUTER JOIN"}
	
	for _, keyword := range keywords {
		query = strings.ReplaceAll(query, strings.ToUpper(keyword), "\n"+keyword)
	}
	
	// Clean up extra whitespace
	lines := strings.Split(query, "\n")
	var formattedLines []string
	
	for _, line := range lines {
		line = strings.TrimSpace(line)
		if line != "" {
			formattedLines = append(formattedLines, line)
		}
	}
	
	return strings.Join(formattedLines, "\n")
}

// FormatURL formats and validates a URL
func (df *DataFormatter) FormatURL(url string) string {
	// Basic URL formatting
	if !strings.HasPrefix(url, "http://") && !strings.HasPrefix(url, "https://") {
		url = "https://" + url
	}
	
	return url
}

// FormatPhone formats a phone number
func (df *DataFormatter) FormatPhone(phone string) string {
	// Remove all non-digit characters
	digits := regexpReplaceAll(`\D`, phone, "")
	
	switch len(digits) {
	case 10:
		// US format: (123) 456-7890
		return fmt.Sprintf("(%s) %s-%s", digits[:3], digits[3:6], digits[6:])
	case 11:
		// US format with country code: +1 (123) 456-7890
		return fmt.Sprintf("+%s (%s) %s-%s", digits[:1], digits[1:4], digits[4:7], digits[7:])
	default:
		// Return original if format not recognized
		return phone
	}
}

// FormatCreditCard formats a credit card number
func (df *DataFormatter) FormatCreditCard(cardNumber string) string {
	// Remove all non-digit characters
	digits := regexpReplaceAll(`\D`, cardNumber, "")
	
	switch len(digits) {
	case 16:
		// Format: 1234 5678 9012 3456
		return fmt.Sprintf("%s %s %s %s", digits[:4], digits[4:8], digits[8:12], digits[12:])
	case 15:
		// Format: 1234 567890 12345
		return fmt.Sprintf("%s %s %s", digits[:4], digits[4:10], digits[10:])
	case 14:
		// Format: 1234 5678 9012 34
		return fmt.Sprintf("%s %s %s %s", digits[:4], digits[4:8], digits[8:12], digits[12:])
	default:
		// Return original if format not recognized
		return cardNumber
	}
}

// FormatSSN formats a Social Security Number
func (df *DataFormatter) FormatSSN(ssn string) string {
	// Remove all non-digit characters
	digits := regexpReplaceAll(`\D`, ssn, "")
	
	if len(digits) == 9 {
		// Format: 123-45-6789
		return fmt.Sprintf("%s-%s-%s", digits[:3], digits[3:5], digits[5:])
	}
	
	// Return original if format not recognized
	return ssn
}

// FormatIPAddress formats an IP address
func (df *DataFormatter) FormatIPAddress(ip string) string {
	// Basic IP formatting (IPv4 only)
	parts := strings.Split(ip, ".")
	if len(parts) == 4 {
		var formattedParts []string
		for _, part := range parts {
			if len(part) == 0 {
				formattedParts = append(formattedParts, "0")
			} else {
				formattedParts = append(formattedParts, part)
			}
		}
		return strings.Join(formattedParts, ".")
	}
	
	return ip
}

// FormatMACAddress formats a MAC address
func (df *DataFormatter) FormatMACAddress(mac string) string {
	// Remove all non-hex characters
	hex := regexpReplaceAll(`[^0-9a-fA-F]`, mac, "")
	
	if len(hex) == 12 {
		// Format: AA:BB:CC:DD:EE:FF
		var parts []string
		for i := 0; i < 12; i += 2 {
			parts = append(parts, strings.ToUpper(hex[i:i+2]))
		}
		return strings.Join(parts, ":")
	}
	
	return mac
}

// FormatUUID formats a UUID
func (df *DataFormatter) FormatUUID(uuid string) string {
	// Remove all non-hex characters except hyphens
	clean := regexpReplaceAll(`[^0-9a-fA-F-]`, uuid, "")
	
	if len(clean) == 36 {
		// Standard UUID format: 12345678-1234-1234-1234-123456789012
		return strings.ToUpper(clean)
	}
	
	return uuid
}

// FormatHash formats a hash (MD5, SHA1, SHA256, etc.)
func (df *DataFormatter) FormatHash(hash string) string {
	// Remove all non-hex characters
	clean := regexpReplaceAll(`[^0-9a-fA-F]`, hash, "")
	
	// Convert to uppercase
	return strings.ToUpper(clean)
}

// FormatVersion formats a version number
func (df *DataFormatter) FormatVersion(version string) string {
	// Basic version formatting
	parts := strings.Split(version, ".")
	
	// Ensure semantic versioning format (major.minor.patch)
	for len(parts) < 3 {
		parts = append(parts, "0")
	}
	
	return strings.Join(parts[:3], ".")
}

// FormatCoordinates formats geographic coordinates
func (df *DataFormatter) FormatCoordinates(latitude, longitude float64, precision int) string {
	format := fmt.Sprintf("%%.%df", precision)
	
	latStr := fmt.Sprintf(format, latitude)
	lonStr := fmt.Sprintf(format, longitude)
	
	// Add N/S and E/W suffixes
	latSuffix := "N"
	if latitude < 0 {
		latSuffix = "S"
		latStr = fmt.Sprintf(format, -latitude)
	}
	
	lonSuffix := "E"
	if longitude < 0 {
		lonSuffix = "W"
		lonStr = fmt.Sprintf(format, -longitude)
	}
	
	return fmt.Sprintf("%s°%s, %s°%s", latStr, latSuffix, lonStr, lonSuffix)
}

// FormatTemperature formats temperature with unit
func (df *DataFormatter) FormatTemperature(temp float64, unit string) string {
	switch strings.ToUpper(unit) {
	case "C", "CELSIUS":
		return fmt.Sprintf("%.1f°C", temp)
	case "F", "FAHRENHEIT":
		return fmt.Sprintf("%.1f°F", temp)
	case "K", "KELVIN":
		return fmt.Sprintf("%.1fK", temp)
	default:
		return fmt.Sprintf("%.1f°%s", temp, unit)
	}
}

// FormatWeight formats weight with unit
func (df *DataFormatter) FormatWeight(weight float64, unit string) string {
	switch strings.ToUpper(unit) {
	case "KG", "KILOGRAM":
		return fmt.Sprintf("%.1f kg", weight)
	case "G", "GRAM":
		return fmt.Sprintf("%.1f g", weight)
	case "LB", "POUND":
		return fmt.Sprintf("%.1f lb", weight)
	case "OZ", "OUNCE":
		return fmt.Sprintf("%.1f oz", weight)
	default:
		return fmt.Sprintf("%.1f %s", weight, unit)
	}
}

// FormatLength formats length with unit
func (df *DataFormatter) FormatLength(length float64, unit string) string {
	switch strings.ToUpper(unit) {
	case "M", "METER":
		return fmt.Sprintf("%.1f m", length)
	case "CM", "CENTIMETER":
		return fmt.Sprintf("%.1f cm", length)
	case "KM", "KILOMETER":
		return fmt.Sprintf("%.1f km", length)
	case "FT", "FOOT":
		return fmt.Sprintf("%.1f ft", length)
	case "IN", "INCH":
		return fmt.Sprintf("%.1f in", length)
	case "MI", "MILE":
		return fmt.Sprintf("%.1f mi", length)
	default:
		return fmt.Sprintf("%.1f %s", length, unit)
	}
}

// FormatSpeed formats speed with unit
func (df *DataFormatter) FormatSpeed(speed float64, unit string) string {
	switch strings.ToUpper(unit) {
	case "M/S", "MPS":
		return fmt.Sprintf("%.1f m/s", speed)
	case "KM/H", "KMH":
		return fmt.Sprintf("%.1f km/h", speed)
	case "MPH":
		return fmt.Sprintf("%.1f mph", speed)
	case "KNOTS":
		return fmt.Sprintf("%.1f knots", speed)
	default:
		return fmt.Sprintf("%.1f %s", speed, unit)
	}
}

// FormatDataSize formats data size with unit
func (df *DataFormatter) FormatDataSize(bytes int64) string {
	const unit = 1024
	
	if bytes < unit {
		return fmt.Sprintf("%d bytes", bytes)
	}
	
	div, exp := int64(unit), 0
	for n := bytes / unit; n >= unit; n /= unit {
		div *= unit
		exp++
	}
	
	return fmt.Sprintf("%.1f %ciB", float64(bytes)/float64(div), "KMGTPE"[exp])
}

// FormatBandwidth formats bandwidth speed
func (df *DataFormatter) FormatBandwidth(bps float64) string {
	const unit = 1000
	
	if bps < unit {
		return fmt.Sprintf("%.1f bps", bps)
	}
	
	div, exp := unit, 0
	for n := bps / unit; n >= unit; n /= unit {
		div *= unit
		exp++
	}
	
	return fmt.Sprintf("%.1f %cbps", bps/float64(div), "KMGTPE"[exp])
}

// FormatPercentage formats a ratio as percentage
func (df *DataFormatter) FormatPercentage(part, total float64) string {
	if total == 0 {
		return "0%"
	}
	
	percentage := (part / total) * 100
	return fmt.Sprintf("%.1f%%", percentage)
}

// FormatRatio formats a ratio
func (df *DataFormatter) FormatRatio(a, b int) string {
	if b == 0 {
		return "∞"
	}
	
	gcd := df.gcd(a, b)
	simplifiedA := a / gcd
	simplifiedB := b / gcd
	
	if simplifiedB == 1 {
		return fmt.Sprintf("%d", simplifiedA)
	}
	
	return fmt.Sprintf("%d:%d", simplifiedA, simplifiedB)
}

// FormatOrdinal formats a number as ordinal (1st, 2nd, 3rd, etc.)
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

// FormatRomanNumerals formats a number as Roman numerals
func (df *DataFormatter) FormatRomanNumerals(n int) string {
	if n <= 0 || n > 3999 {
		return fmt.Sprintf("%d", n)
	}
	
	roman := []struct {
		value int
		symbol string
	}{
		{1000, "M"},
		{900, "CM"},
		{500, "D"},
		{400, "CD"},
		{100, "C"},
		{90, "XC"},
		{50, "L"},
		{40, "XL"},
		{10, "X"},
		{9, "IX"},
		{5, "V"},
		{4, "IV"},
		{1, "I"},
	}
	
	var result string
	for _, r := range roman {
		for n >= r.value {
			result += r.symbol
			n -= r.value
		}
	}
	
	return result
}

// Helper functions

func (df *DataFormatter) plural(n int) string {
	if n == 1 {
		return ""
	}
	return "s"
}

func (df *DataFormatter) gcd(a, b int) int {
	for b != 0 {
		a, b = b, a%b
	}
	return a
}

func regexpReplaceAll(pattern, text, replacement string) string {
	// Simple regex replacement (in practice, use regexp package)
	// This is a placeholder implementation
	return strings.ReplaceAll(text, pattern, replacement)
}

// FormatStruct formats a struct as a string
func (df *DataFormatter) FormatStruct(data interface{}) string {
	jsonStr, err := df.FormatJSON(data)
	if err != nil {
		return fmt.Sprintf("%+v", data)
	}
	return jsonStr
}

// FormatMap formats a map as a string
func (df *DataFormatter) FormatMap(data map[string]interface{}) string {
	var builder strings.Builder
	builder.WriteString("{")
	
	first := true
	for key, value := range data {
		if !first {
			builder.WriteString(", ")
		}
		first = false
		
		builder.WriteString(fmt.Sprintf("%s: %v", key, value))
	}
	
	builder.WriteString("}")
	return builder.String()
}

// FormatSlice formats a slice as a string
func (df *DataFormatter) FormatSlice(data []interface{}) string {
	var builder strings.Builder
	builder.WriteString("[")
	
	for i, value := range data {
		if i > 0 {
			builder.WriteString(", ")
		}
		builder.WriteString(fmt.Sprintf("%v", value))
	}
	
	builder.WriteString("]")
	return builder.String()
}

// FormatArray formats an array as a string
func (df *DataFormatter) FormatArray(data [10]interface{}) string {
	var builder strings.Builder
	builder.WriteString("[")
	
	for i, value := range data {
		if i > 0 {
			builder.WriteString(", ")
		}
		builder.WriteString(fmt.Sprintf("%v", value))
	}
	
	builder.WriteString("]")
	return builder.String()
}

// FormatPointer formats a pointer as a string
func (df *DataFormatter) FormatPointer(ptr interface{}) string {
	if ptr == nil {
		return "nil"
	}
	return fmt.Sprintf("%p", ptr)
}

// FormatChannel formats a channel as a string
func (df *DataFormatter) FormatChannel(ch interface{}) string {
	if ch == nil {
		return "nil"
	}
	return fmt.Sprintf("%p", ch)
}

// FormatFunction formats a function as a string
func (df *DataFormatter) FormatFunction(fn interface{}) string {
	if fn == nil {
		return "nil"
	}
	return fmt.Sprintf("%p", fn)
}

// FormatInterface formats an interface as a string
func (df *DataFormatter) FormatInterface(data interface{}) string {
	if data == nil {
		return "nil"
	}
	return fmt.Sprintf("%v", data)
}
