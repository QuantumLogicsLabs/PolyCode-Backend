package formatter

import (
	"fmt"
	"regexp"
	"strings"
	"unicode"
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

// SetWordWrapWidth sets the word wrap width
func (tf *TextFormatter) SetWordWrapWidth(width int) {
	tf.wordWrapWidth = width
}

// SetIndentSize sets the indent size
func (tf *TextFormatter) SetIndentSize(size int) {
	tf.indentSize = size
}

// WordWrap wraps text to specified width
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

// Indent adds indentation to each line
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

// JustifyLeft left-justifies text to specified width
func (tf *TextFormatter) JustifyLeft(text string, width int) string {
	if len(text) >= width {
		return text
	}
	return text + strings.Repeat(" ", width-len(text))
}

// JustifyRight right-justifies text to specified width
func (tf *TextFormatter) JustifyRight(text string, width int) string {
	if len(text) >= width {
		return text
	}
	return strings.Repeat(" ", width-len(text)) + text
}

// JustifyCenter center-justifies text to specified width
func (tf *TextFormatter) JustifyCenter(text string, width int) string {
	if len(text) >= width {
		return text
	}
	
	padding := width - len(text)
	leftPadding := padding / 2
	rightPadding := padding - leftPadding
	
	return strings.Repeat(" ", leftPadding) + text + strings.Repeat(" ", rightPadding)
}

// JustifyFull fully justifies text to specified width
func (tf *TextFormatter) JustifyFull(text string, width int) []string {
	words := strings.Fields(text)
	if len(words) == 0 {
		return []string{strings.Repeat(" ", width)}
	}
	
	if len(words) == 1 {
		return []string{tf.JustifyLeft(words[0], width)}
	}
	
	var lines []string
	currentWords := []string{}
	currentLength := 0
	
	for _, word := range words {
		wordLength := len(word)
		
		if currentLength == 0 {
			currentWords = append(currentWords, word)
			currentLength = wordLength
		} else if currentLength+1+wordLength <= width {
			currentWords = append(currentWords, word)
			currentLength += 1 + wordLength
		} else {
			// Justify current line
			line := tf.justifyLine(currentWords, width)
			lines = append(lines, line)
			
			// Start new line
			currentWords = []string{word}
			currentLength = wordLength
		}
	}
	
	// Handle last line (left-justified)
	if len(currentWords) > 0 {
		lastLine := strings.Join(currentWords, " ")
		lines = append(lines, tf.JustifyLeft(lastLine, width))
	}
	
	return lines
}

func (tf *TextFormatter) justifyLine(words []string, width int) string {
	if len(words) == 1 {
		return tf.JustifyLeft(words[0], width)
	}
	
	totalSpaces := width - tf.totalWordLength(words)
	gaps := len(words) - 1
	
	spacePerGap := totalSpaces / gaps
	extraSpaces := totalSpaces % gaps
	
	var result string
	for i, word := range words {
		result += word
		
		if i < gaps {
			spaces := spacePerGap
			if i < extraSpaces {
				spaces++
			}
			result += strings.Repeat(" ", spaces)
		}
	}
	
	return result
}

func (tf *TextFormatter) totalWordLength(words []string) int {
	total := 0
	for _, word := range words {
		total += len(word)
	}
	return total
}

// TitleCase converts text to title case
func (tf *TextFormatter) TitleCase(text string) string {
	words := strings.Fields(text)
	for i, word := range words {
		if len(word) > 0 {
			words[i] = strings.ToUpper(word[:1]) + strings.ToLower(word[1:])
		}
	}
	return strings.Join(words, " ")
}

// SentenceCase converts text to sentence case
func (tf *TextFormatter) SentenceCase(text string) string {
	if len(text) == 0 {
		return text
	}
	
	// Convert to lowercase first
	lower := strings.ToLower(text)
	
	// Capitalize first letter
	return strings.ToUpper(lower[:1]) + lower[1:]
}

// AlternatingCase converts text to alternating case
func (tf *TextFormatter) AlternatingCase(text string) string {
	result := ""
	upper := true
	
	for _, char := range text {
		if upper {
			result += strings.ToUpper(string(char))
		} else {
			result += strings.ToLower(string(char))
		}
		upper = !upper
	}
	
	return result
}

// RandomCase converts text to random case
func (tf *TextFormatter) RandomCase(text string) string {
	result := ""
	
	for _, char := range text {
		if char%2 == 0 {
			result += strings.ToUpper(string(char))
		} else {
			result += strings.ToLower(string(char))
		}
	}
	
	return result
}

// RemoveExtraSpaces removes extra whitespace
func (tf *TextFormatter) RemoveExtraSpaces(text string) string {
	// Replace multiple spaces with single space
	spaceRegex := regexp.MustCompile(`\s+`)
	result := spaceRegex.ReplaceAllString(text, " ")
	
	// Trim leading and trailing spaces
	return strings.TrimSpace(result)
}

// RemoveLineBreaks removes all line breaks
func (tf *TextFormatter) RemoveLineBreaks(text string) string {
	lineBreakRegex := regexp.MustCompile(`\r?\n`)
	return lineBreakRegex.ReplaceAllString(text, " ")
}

// NormalizeLineEndings normalizes line endings to \n
func (tf *TextFormatter) NormalizeLineEndings(text string) string {
	// Replace \r\n with \n
	text = strings.ReplaceAll(text, "\r\n", "\n")
	// Replace remaining \r with \n
	text = strings.ReplaceAll(text, "\r", "\n")
	return text
}

// CountWords counts words in text
func (tf *TextFormatter) CountWords(text string) int {
	words := strings.Fields(text)
	return len(words)
}

// CountCharacters counts characters in text
func (tf *TextFormatter) CountCharacters(text string) int {
	return len([]rune(text))
}

// CountCharactersNoSpaces counts characters excluding spaces
func (tf *TextFormatter) CountCharactersNoSpaces(text string) int {
	count := 0
	for _, char := range text {
		if !unicode.IsSpace(char) {
			count++
		}
	}
	return count
}

// CountLines counts lines in text
func (tf *TextFormatter) CountLines(text string) int {
	if text == "" {
		return 0
	}
	return len(strings.Split(tf.NormalizeLineEndings(text), "\n"))
}

// CountParagraphs counts paragraphs in text
func (tf *TextFormatter) CountParagraphs(text string) int {
	if text == "" {
		return 0
	}
	
	normalized := tf.NormalizeLineEndings(text)
	paragraphs := regexp.MustCompile(`\n\s*\n`).Split(normalized, -1)
	
	count := 0
	for _, paragraph := range paragraphs {
		if strings.TrimSpace(paragraph) != "" {
			count++
		}
	}
	
	return count
}

// ExtractWords extracts all words from text
func (tf *TextFormatter) ExtractWords(text string) []string {
	// Use regex to find words (alphanumeric sequences)
	wordRegex := regexp.MustCompile(`\b\w+\b`)
	matches := wordRegex.FindAllString(text, -1)
	
	return matches
}

// ExtractNumbers extracts all numbers from text
func (tf *TextFormatter) ExtractNumbers(text string) []string {
	// Use regex to find numbers (including decimals and negatives)
	numberRegex := regexp.MustCompile(`-?\b\d+\.?\d*\b`)
	return numberRegex.FindAllString(text, -1)
}

// ExtractEmails extracts all email addresses from text
func (tf *TextFormatter) ExtractEmails(text string) []string {
	// Simple email regex (not RFC compliant but practical)
	emailRegex := regexp.MustCompile(`\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b`)
	return emailRegex.FindAllString(text, -1)
}

// ExtractURLs extracts all URLs from text
func (tf *TextFormatter) ExtractURLs(text string) []string {
	// Simple URL regex
	urlRegex := regexp.MustCompile(`https?://[^\s<>"]+`)
	return urlRegex.FindAllString(text, -1)
}

// ReplaceWords replaces all occurrences of a word
func (tf *TextFormatter) ReplaceWords(text, oldWord, newWord string) string {
	// Use word boundaries to avoid partial matches
	pattern := `\b` + regexp.QuoteMeta(oldWord) + `\b`
	re := regexp.MustCompile(`(?i)` + pattern) // Case insensitive
	return re.ReplaceAllString(text, newWord)
}

// ReplaceWholeText replaces whole text if it matches
func (tf *TextFormatter) ReplaceWholeText(text, oldText, newText string) string {
	if strings.EqualFold(text, oldText) {
		return newText
	}
	return text
}

// CapitalizeWords capitalizes first letter of each word
func (tf *TextFormatter) CapitalizeWords(text string) string {
	words := strings.Fields(text)
	for i, word := range words {
		if len(word) > 0 {
			// Capitalize first letter, keep rest as-is
			words[i] = strings.ToUpper(word[:1]) + word[1:]
		}
	}
	return strings.Join(words, " ")
}

// UncapitalizeWords uncapitalizes first letter of each word
func (tf *TextFormatter) UncapitalizeWords(text string) string {
	words := strings.Fields(text)
	for i, word := range words {
		if len(word) > 0 {
			// Uncapitalize first letter, keep rest as-is
			words[i] = strings.ToLower(word[:1]) + word[1:]
		}
	}
	return strings.Join(words, " ")
}

// SwapCase swaps case of all letters
func (tf *TextFormatter) SwapCase(text string) string {
	result := ""
	
	for _, char := range text {
		if unicode.IsUpper(char) {
			result += strings.ToLower(string(char))
		} else if unicode.IsLower(char) {
			result += strings.ToUpper(string(char))
		} else {
			result += string(char)
		}
	}
	
	return result
}

// ReverseText reverses the entire text
func (tf *TextFormatter) ReverseText(text string) string {
	runes := []rune(text)
	for i, j := 0, len(runes)-1; i < j; i, j = i+1, j-1 {
		runes[i], runes[j] = runes[j], runes[i]
	}
	return string(runes)
}

// ReverseWords reverses the order of words
func (tf *TextFormatter) ReverseWords(text string) string {
	words := strings.Fields(text)
	for i, j := 0, len(words)-1; i < j; i, j = i+1, j-1 {
		words[i], words[j] = words[j], words[i]
	}
	return strings.Join(words, " ")
}

// ReverseCharactersInWords reverses characters within each word
func (tf *TextFormatter) ReverseCharactersInWords(text string) string {
	words := strings.Fields(text)
	for i, word := range words {
		runes := []rune(word)
		for j, k := 0, len(runes)-1; j < k; j, k = j+1, k-1 {
			runes[j], runes[k] = runes[k], runes[j]
		}
		words[i] = string(runes)
	}
	return strings.Join(words, " ")
}

// PadLeft pads text on the left
func (tf *TextFormatter) PadLeft(text string, length int, padChar string) string {
	if len(text) >= length {
		return text
	}
	
	padLength := length - len(text)
	if padChar == "" {
		padChar = " "
	}
	
	return strings.Repeat(padChar, padLength) + text
}

// PadRight pads text on the right
func (tf *TextFormatter) PadRight(text string, length int, padChar string) string {
	if len(text) >= length {
		return text
	}
	
	padLength := length - len(text)
	if padChar == "" {
		padChar = " "
	}
	
	return text + strings.Repeat(padChar, padLength)
}

// PadBoth pads text on both sides
func (tf *TextFormatter) PadBoth(text string, length int, padChar string) string {
	if len(text) >= length {
		return text
	}
	
	padLength := length - len(text)
	if padChar == "" {
		padChar = " "
	}
	
	leftPad := padLength / 2
	rightPad := padLength - leftPad
	
	return strings.Repeat(padChar, leftPad) + text + strings.Repeat(padChar, rightPad)
}

// Truncate truncates text to specified length
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

// TruncateWords truncates text to specified number of words
func (tf *TextFormatter) TruncateWords(text string, wordCount int, suffix string) string {
	words := strings.Fields(text)
	if len(words) <= wordCount {
		return text
	}
	
	if suffix == "" {
		suffix = "..."
	}
	
	truncated := strings.Join(words[:wordCount], " ")
	return truncated + suffix
}

// Elide elides text in the middle
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

// Highlight highlights all occurrences of a term
func (tf *TextFormatter) Highlight(text, term, highlightPrefix, highlightSuffix string) string {
	if term == "" {
		return text
	}
	
	if highlightPrefix == "" {
		highlightPrefix = "**"
	}
	if highlightSuffix == "" {
		highlightSuffix = "**"
	}
	
	pattern := regexp.MustCompile(`(?i)` + regexp.QuoteMeta(term))
	return pattern.ReplaceAllString(text, highlightPrefix+"$0"+highlightSuffix)
}

// Mask masks portions of text
func (tf *TextFormatter) Mask(text string, start, end int, maskChar string) string {
	if maskChar == "" {
		maskChar = "*"
	}
	
	runes := []rune(text)
	if start < 0 {
		start = 0
	}
	if end > len(runes) {
		end = len(runes)
	}
	
	for i := start; i < end; i++ {
		runes[i] = []rune(maskChar)[0]
	}
	
	return string(runes)
}

// MaskEmail masks email addresses
func (tf *TextFormatter) MaskEmail(email string, showChars int) string {
	if showChars < 0 {
		showChars = 0
	}
	
	parts := strings.Split(email, "@")
	if len(parts) != 2 {
		return tf.Mask(email, 0, len(email), "*")
	}
	
	username := parts[0]
	domain := parts[1]
	
	if len(username) <= showChars {
		return tf.Mask(email, 0, len(email)-len(domain)-1, "*")
	}
	
	maskedUsername := username[:showChars] + strings.Repeat("*", len(username)-showChars)
	return maskedUsername + "@" + domain
}

// MaskPhone masks phone numbers
func (tf *TextFormatter) MaskPhone(phone string, showLast int) string {
	if showLast < 0 {
		showLast = 0
	}
	
	// Remove all non-digit characters
	digits := regexp.MustCompile(`\D`).ReplaceAllString(phone, "")
	
	if len(digits) <= showLast {
		return tf.Mask(phone, 0, len(phone)-showLast, "*")
	}
	
	masked := strings.Repeat("*", len(digits)-showLast) + digits[len(digits)-showLast:]
	
	// Reformat with common phone number pattern
	if len(masked) == 10 {
		return "***-***-" + masked[6:]
	}
	
	return masked
}

// CreateTable creates a simple text table
func (tf *TextFormatter) CreateTable(headers []string, rows [][]string) string {
	if len(headers) == 0 {
		return ""
	}
	
	// Calculate column widths
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

// FormatList formats items as a bulleted list
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

// FormatNumberedList formats items as a numbered list
func (tf *TextFormatter) FormatNumberedList(items []string) string {
	var builder strings.Builder
	for i, item := range items {
		if i > 0 {
			builder.WriteString("\n")
		}
		builder.WriteString(fmt.Sprintf("%d. %s", i+1, item))
	}
	
	return builder.String()
}

// FormatDefinitionList formats items as a definition list
func (tf *TextFormatter) FormatDefinitionList(terms []string, definitions []string) string {
	if len(terms) != len(definitions) {
		return ""
	}
	
	var builder strings.Builder
	for i := 0; i < len(terms); i++ {
		if i > 0 {
			builder.WriteString("\n\n")
		}
		builder.WriteString(tf.Bold(terms[i]))
		builder.WriteString(": " + definitions[i])
	}
	
	return builder.String()
}

// Bold formats text as bold (using markdown)
func (tf *TextFormatter) Bold(text string) string {
	return "**" + text + "**"
}

// Italic formats text as italic (using markdown)
func (tf *TextFormatter) Italic(text string) string {
	return "*" + text + "*"
}

// Code formats text as code (using markdown)
func (tf *TextFormatter) Code(text string) string {
	return "`" + text + "`"
}

// BlockCode formats text as code block (using markdown)
func (tf *TextFormatter) BlockCode(text string) string {
	return "```\n" + text + "\n```"
}

// Blockquote formats text as blockquote (using markdown)
func (tf *TextFormatter) Blockquote(text string) string {
	lines := strings.Split(text, "\n")
	for i, line := range lines {
		lines[i] = "> " + line
	}
	return strings.Join(lines, "\n")
}

// Link formats text as markdown link
func (tf *TextFormatter) Link(text, url string) string {
	return "[" + text + "](" + url + ")"
}

// Image formats text as markdown image
func (tf *TextFormatter) Image(altText, url string) string {
	return "![" + altText + "](" + url + ")"
}

// EscapeMarkdown escapes markdown characters
func (tf *TextFormatter) EscapeMarkdown(text string) string {
	// Escape special markdown characters
	replacements := map[string]string{
		"*": "\\*",
		"_": "\\_",
		"`": "\\`",
		"[": "\\[",
		"]": "\\]",
		"(": "\\(",
		")": "\\)",
		"#": "\\#",
		"!": "\\!",
		"|": "\\|",
	}
	
	result := text
	for char, replacement := range replacements {
		result = strings.ReplaceAll(result, char, replacement)
	}
	
	return result
}
