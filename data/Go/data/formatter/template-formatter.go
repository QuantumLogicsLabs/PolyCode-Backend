package formatter

import (
	"bytes"
	"fmt"
	"io/ioutil"
	"os"
	"regexp"
	"strings"
	"text/template"
)

// TemplateFormatter provides template-based formatting utilities
type TemplateFormatter struct {
	funcMap template.FuncMap
	delims  []string
}

// NewTemplateFormatter creates a new template formatter
func NewTemplateFormatter() *TemplateFormatter {
	return &TemplateFormatter{
		funcMap: template.FuncMap{
			"upper": strings.ToUpper,
			"lower": strings.ToLower,
			"title": strings.Title,
			"trim":  strings.TrimSpace,
			"split": strings.Split,
			"join":  strings.Join,
			"replace": func(old, new, s string) string {
				return strings.ReplaceAll(s, old, new)
			},
			"contains": strings.Contains,
			"hasPrefix": strings.HasPrefix,
			"hasSuffix": strings.HasSuffix,
			"repeat": strings.Repeat,
			"length": len,
			"format": fmt.Sprintf,
			"plural": func(n int) string {
				if n == 1 {
					return ""
				}
				return "s"
			},
			"ordinal": func(n int) string {
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
			},
		},
		delims: []string{"{{", "}}"},
	}
}

// SetDelimiters sets custom template delimiters
func (tf *TemplateFormatter) SetDelimiters(left, right string) {
	tf.delims = []string{left, right}
}

// AddFunction adds a custom function to the function map
func (tf *TemplateFormatter) AddFunction(name string, fn interface{}) {
	tf.funcMap[name] = fn
}

// FormatTemplate formats a template string with data
func (tf *TemplateFormatter) FormatTemplate(templateStr string, data interface{}) (string, error) {
	tmpl, err := template.New("template").Delims(tf.delims[0], tf.delims[1]).Funcs(tf.funcMap).Parse(templateStr)
	if err != nil {
		return "", fmt.Errorf("template parse error: %w", err)
	}
	
	var buf bytes.Buffer
	err = tmpl.Execute(&buf, data)
	if err != nil {
		return "", fmt.Errorf("template execution error: %w", err)
	}
	
	return buf.String(), nil
}

// FormatTemplateFile formats a template from file with data
func (tf *TemplateFormatter) FormatTemplateFile(filename string, data interface{}) (string, error) {
	templateStr, err := ioutil.ReadFile(filename)
	if err != nil {
		return "", fmt.Errorf("failed to read template file: %w", err)
	}
	
	return tf.FormatTemplate(string(templateStr), data)
}

// CreateTemplate creates a reusable template
func (tf *TemplateFormatter) CreateTemplate(name, templateStr string) (*template.Template, error) {
	return template.New(name).Delims(tf.delims[0], tf.delims[1]).Funcs(tf.funcMap).Parse(templateStr)
}

// ExecuteTemplate executes a template with data
func (tf *TemplateFormatter) ExecuteTemplate(tmpl *template.Template, data interface{}) (string, error) {
	var buf bytes.Buffer
	err := tmpl.Execute(&buf, data)
	if err != nil {
		return "", fmt.Errorf("template execution error: %w", err)
	}
	return buf.String(), nil
}

// ValidateTemplate validates a template string
func (tf *TemplateFormatter) ValidateTemplate(templateStr string) error {
	_, err := template.New("validation").Delims(tf.delims[0], tf.delims[1]).Funcs(tf.funcMap).Parse(templateStr)
	return err
}

// ExtractVariables extracts variable names from template
func (tf *TemplateFormatter) ExtractVariables(templateStr string) []string {
	// Simple regex to extract variables
	varRegex := regexp.MustCompile(`\{\{\s*(\w+)\s*\}\}`)
	matches := varRegex.FindAllStringSubmatch(templateStr, -1)
	
	var vars []string
	seen := make(map[string]bool)
	
	for _, match := range matches {
		if len(match) > 1 {
			varName := match[1]
			if !seen[varName] {
				vars = append(vars, varName)
				seen[varName] = true
			}
		}
	}
	
	return vars
}

// ExtractFunctions extracts function names from template
func (tf *TemplateFormatter) ExtractFunctions(templateStr string) []string {
	// Simple regex to extract functions
	funcRegex := regexp.MustCompile(`\{\{\s*(\w+)\s+[^}]*\}\}`)
	matches := funcRegex.FindAllStringSubmatch(templateStr, -1)
	
	var funcs []string
	seen := make(map[string]bool)
	
	for _, match := range matches {
		if len(match) > 1 {
			funcName := match[1]
			if !seen[funcName] {
				funcs = append(funcs, funcName)
				seen[funcName] = true
			}
		}
	}
	
	return funcs
}

// TemplateBuilder helps build complex templates
type TemplateBuilder struct {
	formatter *TemplateFormatter
	template  string
	data      map[string]interface{}
}

// NewTemplateBuilder creates a new template builder
func NewTemplateBuilder() *TemplateBuilder {
	return &TemplateBuilder{
		formatter: NewTemplateFormatter(),
		template:  "",
		data:      make(map[string]interface{}),
	}
}

// AddText adds plain text to the template
func (tb *TemplateBuilder) AddText(text string) *TemplateBuilder {
	tb.template += text
	return tb
}

// AddVariable adds a variable placeholder
func (tb *TemplateBuilder) AddVariable(name string) *TemplateBuilder {
	tb.template += fmt.Sprintf("%s%s%s", tb.formatter.delims[0], name, tb.formatter.delims[1])
	return tb
}

// AddFunction adds a function call to the template
func (tb *TemplateBuilder) AddFunction(name string, args ...string) *TemplateBuilder {
	argsStr := strings.Join(args, " ")
	tb.template += fmt.Sprintf("%s%s %s%s", tb.formatter.delims[0], name, argsStr, tb.formatter.delims[1])
	return tb
}

// AddLine adds a line break
func (tb *TemplateBuilder) AddLine() *TemplateBuilder {
	tb.template += "\n"
	return tb
}

// AddConditional adds a conditional block
func (tb *TemplateBuilder) AddConditional(condition string, content string) *TemplateBuilder {
	tb.template += fmt.Sprintf("%sif %s%s%s\n", tb.formatter.delims[0], condition, tb.formatter.delims[1], content)
	tb.template += fmt.Sprintf("%send%s", tb.formatter.delims[0], tb.formatter.delims[1])
	return tb
}

// AddLoop adds a loop block
func (tb *TemplateBuilder) AddLoop(variable, collection string, content string) *TemplateBuilder {
	tb.template += fmt.Sprintf("%srange %s%s%s\n", tb.formatter.delims[0], collection, tb.formatter.delims[1], content)
	tb.template += fmt.Sprintf("%send%s", tb.formatter.delims[0], tb.formatter.delims[1])
	return tb
}

// SetData sets a data value
func (tb *TemplateBuilder) SetData(key string, value interface{}) *TemplateBuilder {
	tb.data[key] = value
	return tb
}

// SetDataMap sets multiple data values
func (tb *TemplateBuilder) SetDataMap(data map[string]interface{}) *TemplateBuilder {
	for key, value := range data {
		tb.data[key] = value
	}
	return tb
}

// Build builds and executes the template
func (tb *TemplateBuilder) Build() (string, error) {
	return tb.formatter.FormatTemplate(tb.template, tb.data)
}

// GetTemplate returns the current template string
func (tb *TemplateBuilder) GetTemplate() string {
	return tb.template
}

// GetData returns the current data map
func (tb *TemplateBuilder) GetData() map[string]interface{} {
	return tb.data
}

// Common template patterns

// FormatGreeting formats a greeting template
func (tf *TemplateFormatter) FormatGreeting(name, title string, formal bool) (string, error) {
	template := ""
	if formal {
		template = fmt.Sprintf("Dear %s %s,\n\n{{.message}}\n\nSincerely,\n{{.sender}}", title, name)
	} else {
		template = fmt.Sprintf("Hi %s,\n\n{{.message}}\n\n{{.sender}}", name)
	}
	
	data := map[string]interface{}{
		"message": "Welcome to our service!",
		"sender":  "The Team",
	}
	
	return tf.FormatTemplate(template, data)
}

// FormatEmail formats an email template
func (tf *TemplateFormatter) FormatEmail(to, subject, body string, cc []string) (string, error) {
	template := `To: {{.to}}
{{if .cc}}Cc: {{join .cc ", "}}{{end}}
Subject: {{.subject}}

{{.body}}

---
{{.signature}}`

	data := map[string]interface{}{
		"to":        to,
		"subject":   subject,
		"body":      body,
		"cc":        cc,
		"signature": "Best regards,\nThe Team",
	}

	return tf.FormatTemplate(template, data)
}

// FormatReport formats a report template
func (tf *TemplateFormatter) FormatReport(title string, sections []map[string]interface{}) (string, error) {
	template := `{{.title}}
{{repeat "=" .titleLength}}

{{range .sections}}
{{.title}}
{{repeat "-" .titleLength}}
{{.content}}

{{end}}
---
Generated on {{.date}}`

	titleLength := len(title)
	for _, section := range sections {
		if len(section["title"].(string)) > titleLength {
			titleLength = len(section["title"].(string))
		}
	}

	data := map[string]interface{}{
		"title":        title,
		"titleLength":  titleLength,
		"sections":     sections,
		"date":         "2024-01-01", // Would use current date
	}

	return tf.FormatTemplate(template, data)
}

// FormatInvoice formats an invoice template
func (tf *TemplateFormatter) FormatInvoice(invoice map[string]interface{}) (string, error) {
	template := `{{.company.name}}
{{.company.address}}
{{.company.city}}, {{.company.state}} {{.company.zip}}
{{.company.phone}}
{{.company.email}}

INVOICE #{{.number}}
Date: {{.date}}
Due: {{.dueDate}}
Status: {{.status}}

Bill To:
{{.customer.name}}
{{.customer.address}}
{{.customer.city}}, {{.customer.state}} {{.customer.zip}}

{{range .items}}
{{.description}}                    {{printf "%-8.2f" .quantity}}    {{printf "%10.2f" .unitPrice}}    {{printf "%12.2f" .total}}
{{end}}
{{repeat "-" 60}}

{{range .items}}
{{.description}}                    {{printf "%-8.2f" .quantity}}    {{printf "%10.2f" .unitPrice}}    {{printf "%12.2f" .total}}
{{end}}
{{repeat "-" 60}}

Subtotal:                              {{printf "%12.2f" .subtotal}}
Tax ({{.taxRate}}%):                      {{printf "%12.2f" .tax}}
Total:                                 {{printf "%12.2f" .total}}

Payment Terms: {{.paymentTerms}}
Notes: {{.notes}}`

	return tf.FormatTemplate(template, invoice)
}

// FormatReceipt formats a receipt template
func (tf *TemplateFormatter) FormatReceipt(receipt map[string]interface{}) (string, error) {
	template := `{{.store.name}}
{{.store.address}}
{{.store.phone}}
{{.store.email}}

RECEIPT #{{.number}}
Date: {{.date}}
Cashier: {{.cashier}}

{{range .items}}
{{.name}}                    {{printf "%-8.2f" .quantity}}    {{printf "%10.2f" .price}}    {{printf "%12.2f" .total}}
{{end}}
{{repeat "-" 50}}

Subtotal:                    {{printf "%12.2f" .subtotal}}
Tax ({{.taxRate}}%):            {{printf "%12.2f" .tax}}
Total:                       {{printf "%12.2f" .total}}
Payment: {{.payment}}
Change: {{.change}}

Thank you for shopping with us!
{{.store.website}}`

	return tf.FormatTemplate(template, receipt)
}

// FormatLetter formats a letter template
func (tf *TemplateFormatter) FormatLetter(letter map[string]interface{}) (string, error) {
	template := `{{.sender.name}}
{{.sender.address}}
{{.sender.city}}, {{.sender.state}} {{.sender.zip}}
{{.sender.email}}
{{.sender.phone}}

{{.date}}

{{.recipient.name}}
{{.recipient.title}}
{{.recipient.company}}
{{.recipient.address}}
{{.recipient.city}}, {{.recipient.state}} {{.recipient.zip}}

Dear {{.recipient.name}},

{{.salutation}}

{{.body}}

{{.closing}}

{{.sender.name}}
{{.sender.title}}
{{.sender.company}}`

	return tf.FormatTemplate(template, letter)
}

// FormatResume formats a resume template
func (tf *TemplateFormatter) FormatResume(resume map[string]interface{}) (string, error) {
	template := `{{.name}}
{{.contact.email}} | {{.contact.phone}} | {{.contact.linkedin}} | {{.contact.github}}

{{.summary.title}}
{{.summary.content}}

{{range .sections}}
{{.title}}
{{range .items}}
{{.title}} - {{.organization}} ({{.duration}})
{{range .details}}
- {{.}}
{{end}}
{{end}}

{{end}}

{{.skills.title}}
{{range .skills.items}}
{{.}} {{end}}

{{.education.title}}
{{range .education.items}}
{{.degree}} in {{.major}} - {{.institution}} ({{.year}})
{{.gpa}}
{{end}}`

	return tf.FormatTemplate(template, resume)
}

// FormatBlog formats a blog post template
func (tf *TemplateFormatter) FormatBlog(post map[string]interface{}) (string, error) {
	template := `{{.title}}
{{.author.name}} | {{.date | date "2006-01-02"}} | {{.readingTime}} min read

{{.content}}

{{if .tags}}
Tags: {{join .tags ", "}}
{{end}}

{{if .comments}}
Comments ({{len .comments}})
{{range .comments}}
{{.author.name}} on {{.date | date "2006-01-02 15:04"}}:
{{.content}}
{{end}}
{{end}}`

	return tf.FormatTemplate(template, post)
}

// FormatProduct formats a product description template
func (tf *TemplateFormatter) FormatProduct(product map[string]interface{}) (string, error) {
	template := `{{.name}}
{{.category}} | {{.brand}}
Rating: {{repeat "*" .rating}} ({{.reviews}} reviews)

{{.description}}

{{if .features}}
Features:
{{range .features}}
- {{.}}
{{end}}
{{end}}

{{if .specifications}}
Specifications:
{{range .specifications}}
- {{.name}}: {{.value}}
{{end}}
{{end}}

{{.price.currency}} {{printf "%.2f" .price.amount}}
{{if .price.discount}}
Was: {{.price.currency}} {{printf "%.2f" .price.original}}
Save: {{.price.discount}}%
{{end}}

{{if .availability}}
{{.availability}}
{{end}}`

	return tf.FormatTemplate(template, product)
}

// FormatNewsletter formats a newsletter template
func (tf *TemplateFormatter) FormatNewsletter(newsletter map[string]interface{}) (string, error) {
	template := `{{.title}}
{{.date | date "January 2, 2006"}}

{{.header}}

{{range .sections}}
{{.title}}
{{.content}}

{{end}}

{{if .events}}
Upcoming Events:
{{range .events}}
- {{.date | date "January 2"}}: {{.name}} - {{.location}}
{{end}}
{{end}}

{{if .footer}}
{{.footer}}
{{end}}

Unsubscribe | Update Preferences
{{.company.name}} | {{.company.website}}`

	return tf.FormatTemplate(template, newsletter)
}

// FormatAPIResponse formats an API response template
func (tf *TemplateFormatter) FormatAPIResponse(response map[string]interface{}) (string, error) {
	template := `{
{{if .success}}
  "success": true,
  "data": {{.data | toJSON}},
  "message": "{{.message}}"
{{else}}
  "success": false,
  "error": {
    "code": {{.error.code}},
    "message": "{{.error.message}}"
  }
{{end}},
  "timestamp": "{{.timestamp}}",
  "requestId": "{{.requestId}}"
}`

	return tf.FormatTemplate(template, response)
}

// FormatConfig formats a configuration file template
func (tf *TemplateFormatter) FormatConfig(config map[string]interface{}) (string, error) {
	template := `# Configuration File
# Generated on {{.date}}

{{range .sections}}
[{{.name}}]
{{range .settings}}
{{.key}} = {{.value}}
{{end}}

{{end}}`

	return tf.FormatTemplate(template, config)
}

// FormatManifest formats a manifest file template
func (tf *TemplateFormatter) FormatManifest(manifest map[string]interface{}) (string, error) {
	template := `apiVersion: {{.apiVersion}}
kind: {{.kind}}
metadata:
  name: {{.metadata.name}}
  namespace: {{.metadata.namespace}}
  labels:
{{range .metadata.labels}}
    {{.key}}: {{.value}}
{{end}}
spec:
{{range .spec}}
  {{.key}}: {{.value}}
{{end}}`

	return tf.FormatTemplate(template, manifest)
}

// Template utilities

// ValidateTemplateData validates that template data contains all required variables
func (tf *TemplateFormatter) ValidateTemplateData(templateStr string, data map[string]interface{}) error {
	variables := tf.ExtractVariables(templateStr)
	
	for _, variable := range variables {
		if _, exists := data[variable]; !exists {
			return fmt.Errorf("missing required variable: %s", variable)
		}
	}
	
	return nil
}

// GetTemplateInfo returns information about a template
func (tf *TemplateFormatter) GetTemplateInfo(templateStr string) map[string]interface{} {
	info := make(map[string]interface{})
	
	info["variables"] = tf.ExtractVariables(templateStr)
	info["functions"] = tf.ExtractFunctions(templateStr)
	info["length"] = len(templateStr)
	info["lineCount"] = strings.Count(templateStr, "\n") + 1
	
	// Count delimiters
	leftDelim := tf.delims[0]
	rightDelim := tf.delims[1]
	info["leftDelimiters"] = strings.Count(templateStr, leftDelim)
	info["rightDelimiters"] = strings.Count(templateStr, rightDelim)
	
	return info
}

// MergeTemplates merges multiple templates
func (tf *TemplateFormatter) MergeTemplates(templates ...string) string {
	var result strings.Builder
	
	for i, template := range templates {
		if i > 0 {
			result.WriteString("\n")
		}
		result.WriteString(template)
	}
	
	return result.String()
}

// SplitTemplate splits a template into sections
func (tf *TemplateFormatter) SplitTemplate(templateStr string, separator string) []string {
	return strings.Split(templateStr, separator)
}

// SaveTemplateToFile saves a template to a file
func (tf *TemplateFormatter) SaveTemplateToFile(filename, templateStr string) error {
	return ioutil.WriteFile(filename, []byte(templateStr), 0644)
}

// LoadTemplateFromFile loads a template from a file
func (tf *TemplateFormatter) LoadTemplateFromFile(filename string) (string, error) {
	content, err := ioutil.ReadFile(filename)
	if err != nil {
		return "", err
	}
	return string(content), nil
}

// CreateTemplateFromData creates a template from structured data
func (tf *TemplateFormatter) CreateTemplateFromData(data map[string]interface{}) (string, error) {
	template := ""
	
	for key, value := range data {
		template += fmt.Sprintf("%s%s: %s%s\n", tf.delims[0], key, tf.delims[1], value)
	}
	
	return template, nil
}

// TemplateCache provides template caching
type TemplateCache struct {
	cache map[string]*template.Template
	tf    *TemplateFormatter
}

// NewTemplateCache creates a new template cache
func NewTemplateCache() *TemplateCache {
	return &TemplateCache{
		cache: make(map[string]*template.Template),
		tf:    NewTemplateFormatter(),
	}
}

// GetTemplate gets a template from cache or creates it
func (tc *TemplateCache) GetTemplate(name, templateStr string) (*template.Template, error) {
	if tmpl, exists := tc.cache[name]; exists {
		return tmpl, nil
	}
	
	tmpl, err := tc.tf.CreateTemplate(name, templateStr)
	if err != nil {
		return nil, err
	}
	
	tc.cache[name] = tmpl
	return tmpl, nil
}

// ClearCache clears the template cache
func (tc *TemplateCache) ClearCache() {
	tc.cache = make(map[string]*template.Template)
}

// CacheSize returns the number of cached templates
func (tc *TemplateCache) CacheSize() int {
	return len(tc.cache)
}

// RemoveTemplate removes a template from cache
func (tc *TemplateCache) RemoveTemplate(name string) {
	delete(tc.cache, name)
}

// ListTemplates returns a list of cached template names
func (tc *TemplateCache) ListTemplates() []string {
	var names []string
	for name := range tc.cache {
		names = append(names, name)
	}
	return names
}
