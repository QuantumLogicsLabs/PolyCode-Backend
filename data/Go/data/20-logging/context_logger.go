package main

import (
	"context"
	"encoding/json"
	"fmt"
	"time"
)

// Context keys for logging
type contextKey string

const (
	UserIDKey    contextKey = "user_id"
	RequestIDKey contextKey = "request_id"
	SessionIDKey contextKey = "session_id"
	TraceIDKey   contextKey = "trace_id"
	CorrelationIDKey contextKey = "correlation_id"
)

// ContextualLogger with context support
type ContextualLogger struct {
	*StructuredLogger
}

func NewContextualLogger() *ContextualLogger {
	return &ContextualLogger{
		StructuredLogger: NewStructuredLogger(),
	}
}

// Context helpers
func WithUserID(ctx context.Context, userID string) context.Context {
	return context.WithValue(ctx, UserIDKey, userID)
}

func WithRequestID(ctx context.Context, requestID string) context.Context {
	return context.WithValue(ctx, RequestIDKey, requestID)
}

func WithSessionID(ctx context.Context, sessionID string) context.Context {
	return context.WithValue(ctx, SessionIDKey, sessionID)
}

func WithTraceID(ctx context.Context, traceID string) context.Context {
	return context.WithValue(ctx, TraceIDKey, traceID)
}

func WithCorrelationID(ctx context.Context, correlationID string) context.Context {
	return context.WithValue(ctx, CorrelationIDKey, correlationID)
}

// Extract context values
func extractContextFields(ctx context.Context) map[string]interface{} {
	fields := make(map[string]interface{})
	
	if userID := ctx.Value(UserIDKey); userID != nil {
		fields["user_id"] = userID
	}
	
	if requestID := ctx.Value(RequestIDKey); requestID != nil {
		fields["request_id"] = requestID
	}
	
	if sessionID := ctx.Value(SessionIDKey); sessionID != nil {
		fields["session_id"] = sessionID
	}
	
	if traceID := ctx.Value(TraceIDKey); traceID != nil {
		fields["trace_id"] = traceID
	}
	
	if correlationID := ctx.Value(CorrelationIDKey); correlationID != nil {
		fields["correlation_id"] = correlationID
	}
	
	return fields
}

func (l *ContextualLogger) DebugContext(ctx context.Context, message string, fields map[string]interface{}) {
	contextFields := extractContextFields(ctx)
	merged := MergeFields(contextFields, fields)
	l.StructuredLogger.Debug(message, merged)
}

func (l *ContextualLogger) InfoContext(ctx context.Context, message string, fields map[string]interface{}) {
	contextFields := extractContextFields(ctx)
	merged := MergeFields(contextFields, fields)
	l.StructuredLogger.Info(message, merged)
}

func (l *ContextualLogger) WarnContext(ctx context.Context, message string, fields map[string]interface{}) {
	contextFields := extractContextFields(ctx)
	merged := MergeFields(contextFields, fields)
	l.StructuredLogger.Warn(message, merged)
}

func (l *ContextualLogger) ErrorContext(ctx context.Context, message string, fields map[string]interface{}) {
	contextFields := extractContextFields(ctx)
	merged := MergeFields(contextFields, fields)
	l.StructuredLogger.Error(message, merged)
}

func (l *ContextualLogger) FatalContext(ctx context.Context, message string, fields map[string]interface{}) {
	contextFields := extractContextFields(ctx)
	merged := MergeFields(contextFields, fields)
	l.StructuredLogger.Fatal(message, merged)
}

// TraceLogger for distributed tracing
type TraceLogger struct {
	*ContextualLogger
}

func NewTraceLogger() *TraceLogger {
	return &TraceLogger{
		ContextualLogger: NewContextualLogger(),
	}
}

func (l *TraceLogger) LogSpan(ctx context.Context, operation string, startTime time.Time, fields map[string]interface{}) {
	duration := time.Since(startTime)
	
	traceID := ctx.Value(TraceIDKey)
	if traceID == nil {
		traceID = generateTraceID()
		ctx = WithTraceID(ctx, traceID.(string))
	}
	
	allFields := MergeFields(fields, map[string]interface{}{
		"operation": operation,
		"duration":  duration.String(),
		"duration_ms": duration.Milliseconds(),
		"span_type": "operation",
	})
	
	if duration > 100*time.Millisecond {
		l.WarnContext(ctx, "Slow operation detected", allFields)
	} else {
		l.InfoContext(ctx, "Operation completed", allFields)
	}
}

func (l *TraceLogger) LogError(ctx context.Context, operation string, err error, fields map[string]interface{}) {
	allFields := MergeFields(fields, map[string]interface{}{
		"operation": operation,
		"error":     err.Error(),
		"error_type": fmt.Sprintf("%T", err),
	})
	
	l.ErrorContext(ctx, "Operation failed", allFields)
}

// RequestLogger for HTTP request logging
type RequestLogger struct {
	*ContextualLogger
}

func NewRequestLogger() *RequestLogger {
	return &RequestLogger{
		ContextualLogger: NewContextualLogger(),
	}
}

func (l *RequestLogger) LogRequest(ctx context.Context, method, path, remoteAddr string, headers map[string][]string) {
	fields := map[string]interface{}{
		"method":      method,
		"path":        path,
		"remote_addr": remoteAddr,
		"user_agent":  getUserAgent(headers),
		"content_type": getContentType(headers),
	}
	
	l.InfoContext(ctx, "HTTP request started", fields)
}

func (l *RequestLogger) LogResponse(ctx context.Context, statusCode int, duration time.Duration, responseSize int64) {
	fields := map[string]interface{}{
		"status_code":   statusCode,
		"duration":       duration.String(),
		"duration_ms":   duration.Milliseconds(),
		"response_size": responseSize,
	}
	
	level := LevelInfo
	if statusCode >= 400 {
		level = LevelWarn
	}
	if statusCode >= 500 {
		level = LevelError
	}
	
	switch level {
	case LevelWarn:
		l.WarnContext(ctx, "HTTP request completed", fields)
	case LevelError:
		l.ErrorContext(ctx, "HTTP request completed", fields)
	default:
		l.InfoContext(ctx, "HTTP request completed", fields)
	}
}

// BusinessLogger for business events
type BusinessLogger struct {
	*ContextualLogger
}

func NewBusinessLogger() *BusinessLogger {
	return &BusinessLogger{
		ContextualLogger: NewContextualLogger(),
	}
}

func (l *BusinessLogger) LogUserAction(ctx context.Context, action string, target string, fields map[string]interface{}) {
	allFields := MergeFields(fields, map[string]interface{}{
		"event_type": "user_action",
		"action":     action,
		"target":     target,
	})
	
	l.InfoContext(ctx, "User action performed", allFields)
}

func (l *BusinessLogger) LogBusinessEvent(ctx context.Context, eventType string, entity string, fields map[string]interface{}) {
	allFields := MergeFields(fields, map[string]interface{}{
		"event_type": "business_event",
		"business_event_type": eventType,
		"entity":     entity,
	})
	
	l.InfoContext(ctx, "Business event occurred", allFields)
}

func (l *BusinessLogger) LogTransaction(ctx context.Context, transactionID string, amount float64, currency string, status string, fields map[string]interface{}) {
	allFields := MergeFields(fields, map[string]interface{}{
		"event_type":      "transaction",
		"transaction_id":  transactionID,
		"amount":          amount,
		"currency":        currency,
		"status":          status,
	})
	
	l.InfoContext(ctx, "Transaction processed", allFields)
}

// SecurityLogger for security events
type SecurityLogger struct {
	*ContextualLogger
}

func NewSecurityLogger() *SecurityLogger {
	return &SecurityLogger{
		ContextualLogger: NewContextualLogger(),
	}
}

func (l *SecurityLogger) LogAuthentication(ctx context.Context, userID string, success bool, method string, fields map[string]interface{}) {
	allFields := MergeFields(fields, map[string]interface{}{
		"event_type": "authentication",
		"user_id":    userID,
		"success":    success,
		"method":     method,
	})
	
	if success {
		l.InfoContext(ctx, "Authentication successful", allFields)
	} else {
		l.WarnContext(ctx, "Authentication failed", allFields)
	}
}

func (l *SecurityLogger) LogAuthorization(ctx context.Context, userID string, resource string, action string, allowed bool, fields map[string]interface{}) {
	allFields := MergeFields(fields, map[string]interface{}{
		"event_type": "authorization",
		"user_id":    userID,
		"resource":   resource,
		"action":     action,
		"allowed":    allowed,
	})
	
	if allowed {
		l.InfoContext(ctx, "Authorization granted", allFields)
	} else {
		l.WarnContext(ctx, "Authorization denied", allFields)
	}
}

func (l *SecurityLogger) LogSecurityEvent(ctx context.Context, eventType string, severity string, fields map[string]interface{}) {
	allFields := MergeFields(fields, map[string]interface{}{
		"event_type": "security",
		"security_event_type": eventType,
		"severity":   severity,
	})
	
	switch severity {
	case "high", "critical":
		l.ErrorContext(ctx, "Security event detected", allFields)
	case "medium":
		l.WarnContext(ctx, "Security event detected", allFields)
	default:
		l.InfoContext(ctx, "Security event detected", allFields)
	}
}

// Utility functions
func generateTraceID() string {
	return fmt.Sprintf("trace_%d", time.Now().UnixNano())
}

func getUserAgent(headers map[string][]string) string {
	if userAgents, exists := headers["User-Agent"]; exists && len(userAgents) > 0 {
		return userAgents[0]
	}
	return ""
}

func getContentType(headers map[string][]string) string {
	if contentTypes, exists := headers["Content-Type"]; exists && len(contentTypes) > 0 {
		return contentTypes[0]
	}
	return ""
}

// ErrorAwareLogger for better error handling
type ErrorAwareLogger struct {
	*ContextualLogger
}

func NewErrorAwareLogger() *ErrorAwareLogger {
	return &ErrorAwareLogger{
		ContextualLogger: NewContextualLogger(),
	}
}

func (l *ErrorAwareLogger) ErrorContext(ctx context.Context, message string, fields map[string]interface{}) {
	// Check if there's an error in fields and add more context
	if err, ok := fields["error"]; ok {
		if e, ok := err.(error); ok {
			fields["error_type"] = fmt.Sprintf("%T", e)
			fields["error_details"] = getErrorDetails(e)
		}
	}
	
	l.ContextualLogger.ErrorContext(ctx, message, fields)
}

func getErrorDetails(err error) map[string]interface{} {
	details := make(map[string]interface{})
	
	// Add stack trace if available
	if typeErr, ok := err.(interface{ StackTrace() []string }); ok {
		details["stack_trace"] = typeErr.StackTrace()
	}
	
	// Add error code if available
	if codedErr, ok := err.(interface{ Code() string }); ok {
		details["error_code"] = codedErr.Code()
	}
	
	return details
}

// ValidationError example
type ValidationError struct {
	Field   string
	Message string
	Code    string
}

func (e *ValidationError) Error() string {
	return fmt.Sprintf("Validation error on %s: %s", e.Field, e.Message)
}

func (e *ValidationError) Code() string {
	return e.Code
}
