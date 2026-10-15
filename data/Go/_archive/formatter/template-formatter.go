package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"strings"
	"text/template"
	"time"
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
			"trim":  strings.TrimSpace,
			"split": strings.Split,
			"join":  strings.Join,
			"replace": func(old, new, s string) string {
				return strings.ReplaceAll(s, old, new)
			},
			"contains":  strings.Contains,
			"hasPrefix": strings.HasPrefix,
			"hasSuffix": strings.HasSuffix,
			"repeat":    strings.Repeat,
			"length": func(v interface{}) int {
				switch val := v.(type) {
				case string:
					return len(val)
				case []interface{}:
					return len(val)
				case []map[string]interface{}:
					return len(val)
				default:
					return 0
				}
			},
			"format": fmt.Sprintf,
			"plural": func(n int) string {
				if n == 1 {
					return ""
				}
				return "s"
			},
			"date": func(layout string, t interface{}) string {
				switch v := t.(type) {
				case time.Time:
					return v.Format(layout)
				case string:
					parsed, err := time.Parse(time.RFC3339, v)
					if err == nil {
						return parsed.Format(layout)
					}
					return v
				default:
					return fmt.Sprintf("%v", t)
				}
			},
			"toJSON": func(v interface{}) string {
				b, err := json.MarshalIndent(v, "", "  ")
				if err != nil {
					return "{}"
				}
				return string(b)
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

func (tf *TemplateFormatter) FormatEmail(to, subject, body string, cc []string) (string, error) {
	templateStr := `To: {{.to}}
{{if .cc}}Cc: {{join .cc ", "}}
{{end}}Subject: {{.subject}}

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

	return tf.FormatTemplate(templateStr, data)
}

func (tf *TemplateFormatter) FormatAPIResponse(response map[string]interface{}) (string, error) {
	templateStr := `{
{{if .success}}  "success": true,
  "data": {{.data | toJSON}},
  "message": "{{.message}}"
{{else}}  "success": false,
  "error": {
    "code": {{.error.code}},
    "message": "{{.error.message}}"
  }
{{end}},
  "timestamp": "{{.timestamp}}",
  "requestId": "{{.requestId}}"
}`

	return tf.FormatTemplate(templateStr, response)
}

func main() {
	fmt.Println("=== Template Formatter Demo ===")
	tf := NewTemplateFormatter()

	// 1. Basic Template
	templateStr := "Hello {{.name | upper}}! You have {{.count}} new notification{{.count | plural}}."
	data := map[string]interface{}{
		"name":  "Alice",
		"count": 3,
	}
	res, _ := tf.FormatTemplate(templateStr, data)
	fmt.Println(res)

	// 2. Email Formatting
	fmt.Println("\n-- Formatted Email --")
	email, _ := tf.FormatEmail("client@example.com", "Project Status", "All pipeline tests have passed.", []string{"lead@example.com", "qa@example.com"})
	fmt.Println(email)

	// 3. API Response Formatting with toJSON
	fmt.Println("\n-- Formatted API Response --")
	apiResp := map[string]interface{}{
		"success": true,
		"data": map[string]interface{}{
			"userId": "USR-101",
			"roles":  []string{"admin", "developer"},
		},
		"message":   "User loaded successfully",
		"timestamp": time.Now().Format(time.RFC3339),
		"requestId": "req-987654",
	}
	jsonOut, _ := tf.FormatAPIResponse(apiResp)
	fmt.Println(jsonOut)
}
