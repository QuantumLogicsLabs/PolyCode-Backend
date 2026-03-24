# Formatter Package

A comprehensive text and data formatting package for Go that provides utilities for formatting strings, numbers, dates, templates, and various data structures.

## Overview

The formatter package is organized into several components:

- **Basic** - Simple string formatting utilities
- **TextFormatter** - Advanced text formatting and manipulation
- **DataFormatter** - Data type formatting (dates, numbers, currency, etc.)
- **TemplateFormatter** - Template-based formatting with Go templates

## Files

- **formatter.go** - Basic formatting utilities
- **text-formatter.go** - Advanced text formatting
- **data-formatter.go** - Data type formatting
- **template-formatter.go** - Template-based formatting
- **README.md** - This file

## Features

### Basic Formatting
- Greeting formatting
- Case conversion
- String padding and alignment
- Text wrapping and justification
- Number formatting

### Text Formatting
- Word wrapping and indentation
- Text case conversion (title, sentence, alternating)
- Text extraction (words, numbers, emails, URLs)
- Text manipulation (reverse, truncate, mask)
- Table and list formatting
- Markdown formatting

### Data Formatting
- Date and time formatting
- Number and currency formatting
- File size and data size formatting
- Geographic coordinate formatting
- Temperature, weight, length formatting
- JSON, XML, CSV formatting
- Phone number and credit card formatting

### Template Formatting
- Go template execution
- Custom template functions
- Template validation and analysis
- Common template patterns
- Template caching and optimization

## Usage Examples

### Basic Formatter
```go
package main

import (
    "fmt"
    "go-learning-guide/formatter"
)

func main() {
    f := formatter.NewFormatter()
    
    // Format greeting
    greeting := f.FormatGreeting("Alice")
    fmt.Println(greeting)
    
    // Format number
    formatted := f.FormatNumber(1234.567)
    fmt.Printf("Formatted number: %s\n", formatted)
}
```

### Text Formatter
```go
package main

import (
    "fmt"
    "go-learning-guide/formatter"
)

func main() {
    tf := formatter.NewTextFormatter()
    
    // Word wrap
    text := "This is a long text that needs to be wrapped to fit within a specific width."
    wrapped := tf.WordWrap(text)
    for _, line := range wrapped {
        fmt.Println(line)
    }
    
    // Justify text
    justified := tf.JustifyCenter("Hello World", 20)
    fmt.Printf("Centered: '%s'\n", justified)
    
    // Extract emails
    content := "Contact us at support@example.com or sales@example.com"
    emails := tf.ExtractEmails(content)
    fmt.Printf("Emails: %v\n", emails)
}
```

### Data Formatter
```go
package main

import (
    "fmt"
    "time"
    "go-learning-guide/formatter"
)

func main() {
    df := formatter.NewDataFormatter()
    
    // Format date
    now := time.Now()
    formattedDate := df.FormatDate(now)
    fmt.Printf("Date: %s\n", formattedDate)
    
    // Format currency
    amount := 1234.56
    formattedCurrency := df.FormatCurrency(amount)
    fmt.Printf("Amount: %s\n", formattedCurrency)
    
    // Format file size
    fileSize := df.FormatFileSize(1048576)
    fmt.Printf("File size: %s\n", fileSize)
    
    // Format phone
    phone := df.FormatPhone("1234567890")
    fmt.Printf("Phone: %s\n", phone)
}
```

### Template Formatter
```go
package main

import (
    "fmt"
    "go-learning-guide/formatter"
)

func main() {
    tf := formatter.NewTemplateFormatter()
    
    // Simple template
    template := "Hello {{.name}}! You have {{.count}} messages."
    data := map[string]interface{}{
        "name":  "Alice",
        "count": 5,
    }
    
    result, err := tf.FormatTemplate(template, data)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
        return
    }
    
    fmt.Println(result)
    
    // Email template
    email, err := tf.FormatEmail("user@example.com", "Welcome", "Welcome to our service!", nil)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
        return
    }
    
    fmt.Println(email)
}
```

## API Reference

### Basic Formatter

#### Methods
- `FormatGreeting(name string) string` - Format greeting
- `FormatNumber(number float64) string` - Format number
- `FormatInteger(number int) string` - Format integer
- `FormatCurrency(amount float64) string` - Format currency
- `FormatPercent(value float64) string` - Format percentage

### Text Formatter

#### Methods
- `WordWrap(text string) []string` - Wrap text to specified width
- `Indent(text string, levels int) string` - Indent text
- `JustifyLeft(text string, width int) string` - Left justify text
- `JustifyRight(text string, width int) string` - Right justify text
- `JustifyCenter(text string, width int) string` - Center justify text
- `TitleCase(text string) string` - Convert to title case
- `SentenceCase(text string) string` - Convert to sentence case
- `ExtractWords(text string) []string` - Extract words from text
- `ExtractNumbers(text string) []string` - Extract numbers from text
- `ExtractEmails(text string) []string` - Extract emails from text
- `CreateTable(headers []string, rows [][]string) string` - Create text table

### Data Formatter

#### Methods
- `FormatDate(t time.Time) string` - Format date
- `FormatTime(t time.Time) string` - Format time
- `FormatDateTime(t time.Time) string` - Format date and time
- `FormatRelativeTime(t time.Time) string` - Format relative time
- `FormatDuration(d time.Duration) string` - Format duration
- `FormatFileSize(bytes int64) string` - Format file size
- `FormatNumber(number float64) string` - Format number
- `FormatCurrency(amount float64) string` - Format currency
- `FormatPhone(phone string) string` - Format phone number
- `FormatCreditCard(cardNumber string) string` - Format credit card

### Template Formatter

#### Methods
- `FormatTemplate(templateStr string, data interface{}) (string, error)` - Format template
- `FormatTemplateFile(filename string, data interface{}) (string, error)` - Format template from file
- `CreateTemplate(name, templateStr string) (*template.Template, error)` - Create reusable template
- `ValidateTemplate(templateStr string) error` - Validate template
- `ExtractVariables(templateStr string) []string` - Extract variables from template

#### Template Builder
- `NewTemplateBuilder() *TemplateBuilder` - Create template builder
- `AddText(text string) *TemplateBuilder` - Add text
- `AddVariable(name string) *TemplateBuilder` - Add variable
- `SetData(key string, value interface{}) *TemplateBuilder` - Set data
- `Build() (string, error)` - Build template

## Template Functions

The template formatter includes these built-in functions:

- `upper` - Convert to uppercase
- `lower` - Convert to lowercase
- `title` - Convert to title case
- `trim` - Trim whitespace
- `split` - Split string
- `join` - Join strings
- `replace` - Replace text
- `contains` - Check if contains substring
- `hasPrefix` - Check if has prefix
- `hasSuffix` - Check if has suffix
- `repeat` - Repeat string
- `length` - Get string length
- `format` - Format using fmt.Sprintf
- `plural` - Add plural suffix
- `ordinal` - Convert to ordinal number

## Common Templates

### Email Template
```go
template := `To: {{.to}}
Subject: {{.subject}}

{{.body}}

---
{{.signature}}`
```

### Invoice Template
```go
template := `INVOICE #{{.number}}
Date: {{.date}}
Due: {{.dueDate}}

{{range .items}}
{{.description}}    {{.quantity}}    {{.unitPrice}}    {{.total}}
{{end}}`
```

### Report Template
```go
template := `{{.title}}
{{range .sections}}
{{.title}}
{{.content}}
{{end}}`
```

## Error Handling

The formatter package follows Go's error handling conventions:

```go
result, err := tf.FormatTemplate(template, data)
if err != nil {
    // Handle error
    fmt.Printf("Template error: %v\n", err)
} else {
    // Use result
    fmt.Println(result)
}
```

Common errors include:
- Template parsing errors
- Template execution errors
- File I/O errors
- Invalid data types

## Performance Considerations

### Template Caching
For frequently used templates, use the template cache:

```go
cache := formatter.NewTemplateCache()
tmpl, err := cache.GetTemplate("email", emailTemplate)
if err != nil {
    // Handle error
}
result, err := tf.ExecuteTemplate(tmpl, data)
```

### Text Processing
- Large text processing may require significant memory
- Consider streaming for very large files
- Use regex patterns carefully for performance

### Data Formatting
- Number formatting is generally fast
- Date/time formatting may be slower for complex formats
- JSON formatting can be memory intensive for large objects

## Testing

Run tests with:

```bash
go test ./formatter
go test -v ./formatter
go test -bench ./formatter
```

## Examples

The formatter package includes comprehensive examples in the main Go learning guide. See the `data/` directory for complete usage examples.

## Dependencies

The formatter package uses only Go standard library:
- `fmt` - Formatting
- `strings` - String manipulation
- `time` - Date and time
- `text/template` - Template processing
- `encoding/json` - JSON encoding
- `regexp` - Regular expressions
- `io/ioutil` - File I/O

## Contributing

When contributing to the formatter package:

1. Follow Go coding conventions
2. Add comprehensive tests for new functions
3. Update documentation
4. Consider performance implications
5. Handle edge cases appropriately

## License

This package is part of the Go learning guide and is provided for educational purposes.

## Version History

- **v1.0.0** - Initial release with basic formatting functions
- **v1.1.0** - Added text formatter and data formatter
- **v1.2.0** - Added template formatter with advanced features
- **v1.3.0** - Performance optimizations and bug fixes

## Related Packages

- `go-learning-guide/calculator` - Mathematical operations
- `go-learning-guide/validator` - Input validation functions

## Troubleshooting

### Common Issues

1. **Template Errors**: Check template syntax and data types
2. **Performance**: Use template caching for frequent use
3. **Memory Usage**: Monitor memory usage with large templates
4. **Encoding Issues**: Ensure proper encoding for text processing

### Debugging Tips

1. Use `ValidateTemplate` to check template syntax
2. Use `GetTemplateInfo` to analyze template structure
3. Check data types against template expectations
4. Use smaller templates for debugging complex issues

## Best Practices

1. **Template Design**: Keep templates simple and readable
2. **Error Handling**: Always check and handle errors
3. **Performance**: Cache frequently used templates
4. **Security**: Validate user input in templates
5. **Testing**: Write comprehensive tests for templates

## Internationalization

The formatter package supports basic internationalization:

```go
// Set locale-specific formatting
df.SetCurrencySymbol("€")
df.SetDateFormat("02-01-2006")
df.SetNumberFormat("%.2f")
```

## Security Considerations

When working with templates:

1. **Input Validation**: Validate user input before formatting
2. **Template Injection**: Avoid untrusted template strings
3. **XSS Prevention**: Escape user content in templates
4. **File Access**: Validate file paths and permissions

## Advanced Usage

### Custom Template Functions
```go
tf.AddFunction("custom", func(args ...interface{}) string {
    // Custom function logic
    return "custom result"
})
```

### Template Composition
```go
header := tf.LoadTemplateFromFile("header.tmpl")
body := tf.LoadTemplateFromFile("body.tmpl")
footer := tf.LoadTemplateFromFile("footer.tmpl")
template := tf.MergeTemplates(header, body, footer)
```

### Dynamic Templates
```go
builder := formatter.NewTemplateBuilder()
builder.AddText("Hello ")
builder.AddVariable("name")
builder.AddText("!")
template := builder.GetTemplate()
result, err := tf.FormatTemplate(template, data)
```

## Future Enhancements

Planned features for future versions:

1. **More Template Functions** - Additional built-in functions
2. **Internationalization** - Better i18n support
3. **Performance** - More optimization and caching
4. **Validation** - Template and data validation
5. **Security** - Enhanced security features

## Format Examples

### Date Formatting
```go
df.SetDateFormat("2006-01-02 15:04:05")
df.FormatDate(time.Now())
// Output: 2024-01-15 14:30:45
```

### Number Formatting
```go
df.SetNumberFormat("%.2f")
df.FormatNumber(1234.567)
// Output: 1234.57
```

### Currency Formatting
```go
df.SetCurrencySymbol("€")
df.FormatCurrency(1234.56)
// Output: €1234.56
```

### Template Formatting
```go
template := "Hello {{.name}}! You have {{.count}} {{.plural .count \"message\" \"messages\"}}."
data := map[string]interface{}{
    "name":  "Alice",
    "count": 5,
}
tf.FormatTemplate(template, data)
// Output: Hello Alice! You have 5 messages.
```

## Integration Examples

### Web Application
```go
func formatHandler(w http.ResponseWriter, r *http.Request) {
    tf := formatter.NewTemplateFormatter()
    
    template := "{{.title}}\n\n{{.content}}"
    data := map[string]interface{}{
        "title":   "Welcome",
        "content": "Welcome to our site!",
    }
    
    result, err := tf.FormatTemplate(template, data)
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }
    
    fmt.Fprint(w, result)
}
```

### CLI Application
```go
func main() {
    tf := formatter.NewTemplateFormatter()
    
    config, err := tf.FormatTemplateFile("config.tmpl", configData)
    if err != nil {
        log.Fatal(err)
    }
    
    fmt.Println(config)
}
```

### Email Service
```go
func sendEmail(to, subject, body string) error {
    tf := formatter.NewTemplateFormatter()
    
    email, err := tf.FormatEmail(to, subject, body, []string{"cc@example.com"})
    if err != nil {
        return err
    }
    
    // Send email using email service
    return emailService.Send(email)
}
```
