package main

import (
	"fmt"
	"strings"
)

// TextFormatter provides text formatting utilities
type TextFormatter struct {
	wordWrapWidth int
	indentSize    int
}

// NewTextFormatter creates a new text formatter
func NewTextFormatter() *TextFormatter {
	return &TextFormatter{
		wordWrapWidth: 80,
		indentSize:    4,
	}
}

func (tf *TextFormatter) SetWordWrapWidth(width int) {
	tf.wordWrapWidth = width
}

func (tf *TextFormatter) SetIndentSize(size int) {
	tf.indentSize = size
}

func (tf *TextFormatter) WordWrap(text string) []string {
	if tf.wordWrapWidth <= 0 {
		return []string{text}
	}

	words := strings.Fields(text)
	if len(words) == 0 {
		return []string{""}
	}

	var lines []string
	currentLine := ""
	currentLength := 0

	for _, word := range words {
		wordLength := len(word)

		if currentLength == 0 {
			currentLine = word
			currentLength = wordLength
		} else if currentLength+1+wordLength <= tf.wordWrapWidth {
			currentLine += " " + word
			currentLength += 1 + wordLength
		} else {
			lines = append(lines, currentLine)
			currentLine = word
			currentLength = wordLength
		}
	}

	if currentLine != "" {
		lines = append(lines, currentLine)
	}

	return lines
}

func (tf *TextFormatter) Indent(text string, levels int) string {
	indent := strings.Repeat(" ", levels*tf.indentSize)
	lines := strings.Split(text, "\n")

	for i, line := range lines {
		if strings.TrimSpace(line) != "" {
			lines[i] = indent + line
		}
	}

	return strings.Join(lines, "\n")
}

func (tf *TextFormatter) JustifyLeft(text string, width int) string {
	if len(text) >= width {
		return text
	}
	return text + strings.Repeat(" ", width-len(text))
}

func (tf *TextFormatter) JustifyCenter(text string, width int) string {
	if len(text) >= width {
		return text
	}

	padding := width - len(text)
	leftPadding := padding / 2
	rightPadding := padding - leftPadding

	return strings.Repeat(" ", leftPadding) + text + strings.Repeat(" ", rightPadding)
}

func (tf *TextFormatter) TitleCase(text string) string {
	words := strings.Fields(text)
	for i, word := range words {
		if len(word) > 0 {
			words[i] = strings.ToUpper(word[:1]) + strings.ToLower(word[1:])
		}
	}
	return strings.Join(words, " ")
}

func (tf *TextFormatter) ReverseText(text string) string {
	runes := []rune(text)
	for i, j := 0, len(runes)-1; i < j; i, j = i+1, j-1 {
		runes[i], runes[j] = runes[j], runes[i]
	}
	return string(runes)
}

func (tf *TextFormatter) Truncate(text string, length int, suffix string) string {
	if len(text) <= length {
		return text
	}

	if suffix == "" {
		suffix = "..."
	}

	truncateLength := length - len(suffix)
	if truncateLength < 0 {
		return suffix[:length]
	}

	return text[:truncateLength] + suffix
}

func (tf *TextFormatter) Elide(text string, length int, elideString string) string {
	if len(text) <= length {
		return text
	}

	if elideString == "" {
		elideString = "..."
	}

	elideLength := len(elideString)
	if length <= elideLength {
		return elideString[:length]
	}

	keepLength := (length - elideLength) / 2
	left := text[:keepLength]
	right := text[len(text)-keepLength:]

	return left + elideString + right
}

func (tf *TextFormatter) MaskEmail(email string, showChars int) string {
	if showChars < 0 {
		showChars = 0
	}

	parts := strings.Split(email, "@")
	if len(parts) != 2 {
		return strings.Repeat("*", len(email))
	}

	username := parts[0]
	domain := parts[1]

	if len(username) <= showChars {
		return strings.Repeat("*", len(username)) + "@" + domain
	}

	maskedUsername := username[:showChars] + strings.Repeat("*", len(username)-showChars)
	return maskedUsername + "@" + domain
}

func (tf *TextFormatter) CreateTable(headers []string, rows [][]string) string {
	if len(headers) == 0 {
		return ""
	}

	colWidths := make([]int, len(headers))
	for i, header := range headers {
		colWidths[i] = len(header)
	}

	for _, row := range rows {
		for i, cell := range row {
			if i < len(colWidths) && len(cell) > colWidths[i] {
				colWidths[i] = len(cell)
			}
		}
	}

	var builder strings.Builder

	// Add header row
	for i, header := range headers {
		builder.WriteString(tf.JustifyLeft(header, colWidths[i]))
		if i < len(headers)-1 {
			builder.WriteString(" | ")
		}
	}
	builder.WriteString("\n")

	// Add separator row
	for i, width := range colWidths {
		builder.WriteString(strings.Repeat("-", width))
		if i < len(colWidths)-1 {
			builder.WriteString("-+-")
		}
	}
	builder.WriteString("\n")

	// Add data rows
	for _, row := range rows {
		for i, cell := range row {
			if i < len(colWidths) {
				builder.WriteString(tf.JustifyLeft(cell, colWidths[i]))
			}
			if i < len(colWidths)-1 {
				builder.WriteString(" | ")
			}
		}
		builder.WriteString("\n")
	}

	return builder.String()
}

func (tf *TextFormatter) FormatList(items []string, bullet string) string {
	if bullet == "" {
		bullet = "•"
	}

	var builder strings.Builder
	for i, item := range items {
		if i > 0 {
			builder.WriteString("\n")
		}
		builder.WriteString(bullet + " " + item)
	}

	return builder.String()
}

func main() {
	fmt.Println("=== Text Formatter Demo ===")
	tf := NewTextFormatter()

	// Text Transformation
	fmt.Printf("Title Case: %s\n", tf.TitleCase("go microservices and cloud architecture"))
	fmt.Printf("Reversed: %s\n", tf.ReverseText("Golang"))
	fmt.Printf("Truncated: %s\n", tf.Truncate("High Performance Concurrent Systems", 20, "..."))
	fmt.Printf("Elided: %s\n", tf.Elide("0123456789ABCDEF", 10, "..."))
	fmt.Printf("Masked Email: %s\n", tf.MaskEmail("developer@example.com", 3))

	// Markdown Table Formatting
	fmt.Println("\n-- Formatted Table --")
	headers := []string{"ID", "Service", "Status"}
	rows := [][]string{
		{"1", "Auth Service", "Active"},
		{"2", "Payment Gateway", "Degraded"},
		{"3", "Worker Pool", "Active"},
	}
	fmt.Println(tf.CreateTable(headers, rows))

	// Bulleted List Formatting
	fmt.Println("-- Formatted List --")
	items := []string{"Goroutines", "Channels", "Mutex Locks", "Select Multiplexing"}
	fmt.Println(tf.FormatList(items, "-"))
}
