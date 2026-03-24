#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>
#include <stdbool.h>
#include <stdarg.h>

// =============================================================================
// ADVANCED COMPILER DESIGN
// =============================================================================

#define MAX_TOKENS 10000
#define MAX_SYMBOLS 1000
#define MAX_CODE_SIZE 100000
#define MAX_STRING_SIZE 1024
#define MAX_IDENTIFIER_LENGTH 64
#define MAX_ERROR_MESSAGE 256

// =============================================================================
// LEXICAL ANALYSIS (LEXER)
// =============================================================================

// Token types
typedef enum {
    TOKEN_EOF = 0,
    TOKEN_IDENTIFIER = 1,
    TOKEN_NUMBER = 2,
    TOKEN_STRING = 3,
    TOKEN_KEYWORD = 4,
    TOKEN_OPERATOR = 5,
    TOKEN_DELIMITER = 6,
    TOKEN_COMMENT = 7,
    TOKEN_DIRECTIVE = 8,
    TOKEN_ERROR = 9
} TokenType;

// Keywords
typedef enum {
    KEYWORD_INT = 0,
    KEYWORD_FLOAT = 1,
    KEYWORD_CHAR = 2,
    KEYWORD_DOUBLE = 3,
    KEYWORD_VOID = 4,
    KEYWORD_IF = 5,
    KEYWORD_ELSE = 6,
    KEYWORD_WHILE = 7,
    KEYWORD_FOR = 8,
    KEYWORD_RETURN = 9,
    KEYWORD_STRUCT = 10,
    KEYWORD_UNION = 11,
    KEYWORD_ENUM = 12,
    KEYWORD_TYPEDEF = 13,
    KEYWORD_CONST = 14,
    KEYWORD_STATIC = 15,
    KEYWORD_EXTERN = 16,
    KEYWORD_BREAK = 17,
    KEYWORD_CONTINUE = 18,
    KEYWORD_SWITCH = 19,
    KEYWORD_CASE = 20,
    KEYWORD_DEFAULT = 21,
    KEYWORD_DO = 22,
    KEYWORD_SIZEOF = 23,
    KEYWORD_INCLUDE = 24,
    KEYWORD_DEFINE = 25
} KeywordType;

// Token structure
typedef struct {
    TokenType type;
    KeywordType keyword;
    char value[MAX_STRING_SIZE];
    int line;
    int column;
    int length;
} Token;

// Lexer structure
typedef struct {
    const char* source;
    int position;
    int line;
    int column;
    Token tokens[MAX_TOKENS];
    int token_count;
    char error_message[MAX_ERROR_MESSAGE];
    int has_error;
} Lexer;

// =============================================================================
// SYNTAX ANALYSIS (PARSER)
// =============================================================================

// AST node types
typedef enum {
    AST_PROGRAM = 0,
    AST_FUNCTION_DECLARATION = 1,
    AST_FUNCTION_DEFINITION = 2,
    AST_PARAMETER_LIST = 3,
    AST_PARAMETER = 4,
    AST_COMPOUND_STATEMENT = 5,
    AST_EXPRESSION_STATEMENT = 6,
    AST_DECLARATION = 7,
    AST_DECLARATION_LIST = 8,
    AST_INIT_DECLARATOR_LIST = 9,
    AST_INIT_DECLARATOR = 10,
    AST_TYPE_SPECIFIER = 11,
    AST_DECLARATION_SPECIFIERS = 12,
    AST_TYPE_NAME = 13,
    AST_ASSIGNMENT_EXPRESSION = 14,
    AST_CONDITIONAL_EXPRESSION = 15,
    AST_LOGICAL_OR_EXPRESSION = 16,
    AST_LOGICAL_AND_EXPRESSION = 17,
    AST_EQUALITY_EXPRESSION = 18,
    AST_RELATIONAL_EXPRESSION = 19,
    AST_ADDITIVE_EXPRESSION = 20,
    AST_MULTIPLICATIVE_EXPRESSION = 21,
    AST_UNARY_EXPRESSION = 22,
    AST_POSTFIX_EXPRESSION = 23,
    AST_PRIMARY_EXPRESSION = 24,
    AST_CONSTANT = 25,
    AST_IDENTIFIER = 26,
    AST_STRING_LITERAL = 27,
    AST_FUNCTION_CALL = 28,
    AST_ARRAY_SUBSCRIPT = 29,
    AST_MEMBER_ACCESS = 30,
    AST_CAST_EXPRESSION = 31,
    AST_SELECTION_STATEMENT = 32,
    AST_ITERATION_STATEMENT = 33,
    AST_JUMP_STATEMENT = 34,
    AST_TRANSLATION_UNIT = 35,
    AST_EXTERNAL_DECLARATION = 36
} ASTNodeType;

// AST node structure
typedef struct ASTNode {
    ASTNodeType type;
    union {
        struct {
            struct ASTNode* declarations;
            int declaration_count;
        } program;
        
        struct {
            char* name;
            struct ASTNode* return_type;
            struct ASTNode* parameters;
            struct ASTNode* body;
            int is_definition;
        } function;
        
        struct {
            char* name;
            struct ASTNode* type;
            struct ASTNode* initializer;
        } declaration;
        
        struct {
            struct ASTNode* left;
            struct ASTNode* right;
            char* operator;
        } binary_expression;
        
        struct {
            struct ASTNode* operand;
            char* operator;
        } unary_expression;
        
        struct {
            int value_type; // 0=int, 1=float, 2=char, 3=string
            union {
                int int_value;
                float float_value;
                char char_value;
                char* string_value;
            } value;
        } constant;
        
        struct {
            char* name;
            struct Symbol* symbol;
        } identifier;
        
        struct {
            struct ASTNode* condition;
            struct ASTNode* then_statement;
            struct ASTNode* else_statement;
        } if_statement;
        
        struct {
            struct ASTNode* condition;
            struct ASTNode* body;
            struct ASTNode* increment;
        } for_statement;
        
        struct {
            struct ASTNode* condition;
            struct ASTNode* body;
        } while_statement;
        
        struct {
            struct ASTNode* expression;
        } return_statement;
        
        struct {
            struct ASTNode* function;
            struct ASTNode* arguments;
            int argument_count;
        } function_call;
    } data;
    
    struct ASTNode* children[10];
    int child_count;
    int line;
    int column;
} ASTNode;

// Parser structure
typedef struct {
    Lexer* lexer;
    Token* current_token;
    Token* lookahead_token;
    ASTNode* ast;
    char error_message[MAX_ERROR_MESSAGE];
    int has_error;
    int error_line;
    int error_column;
} Parser;

// =============================================================================
// SEMANTIC ANALYSIS
// =============================================================================

// Symbol types
typedef enum {
    SYMBOL_TYPE_INT = 0,
    SYMBOL_TYPE_FLOAT = 1,
    SYMBOL_TYPE_CHAR = 2,
    SYMBOL_TYPE_DOUBLE = 3,
    SYMBOL_TYPE_VOID = 4,
    SYMBOL_TYPE_STRUCT = 5,
    SYMBOL_TYPE_UNION = 6,
    SYMBOL_TYPE_ENUM = 7,
    SYMBOL_TYPE_FUNCTION = 8,
    SYMBOL_TYPE_ARRAY = 9,
    SYMBOL_TYPE_POINTER = 10
} SymbolType;

// Symbol kinds
typedef enum {
    SYMBOL_KIND_VARIABLE = 0,
    SYMBOL_KIND_FUNCTION = 1,
    SYMBOL_KIND_PARAMETER = 2,
    SYMBOL_KIND_TYPE = 3,
    SYMBOL_KIND_CONSTANT = 4
} SymbolKind;

// Symbol structure
typedef struct Symbol {
    char name[MAX_IDENTIFIER_LENGTH];
    SymbolType type;
    SymbolKind kind;
    int scope_level;
    int is_defined;
    int is_used;
    int line;
    int column;
    struct Symbol* next;
    union {
        struct {
            struct Symbol* return_type;
            struct Symbol* parameters;
            int parameter_count;
            int is_variadic;
        } function_info;
        
        struct {
            struct Symbol* element_type;
            int array_size;
        } array_info;
        
        struct {
            int value;
        } constant_info;
        
        struct {
            struct Symbol* fields;
            int field_count;
        } struct_info;
    } data;
} Symbol;

// Symbol table structure
typedef struct {
    Symbol* symbols[MAX_SYMBOLS];
    int symbol_count;
    int current_scope;
    Symbol* global_symbols;
    Symbol* local_symbols;
    char error_message[MAX_ERROR_MESSAGE];
    int has_error;
} SymbolTable;

// =============================================================================
// INTERMEDIATE CODE GENERATION
// =============================================================================

// Instruction types
typedef enum {
    INST_LOAD = 0,
    INST_STORE = 1,
    INST_ADD = 2,
    INST_SUB = 3,
    INST_MUL = 4,
    INST_DIV = 5,
    INST_MOD = 6,
    INST_NEG = 7,
    INST_CMP = 8,
    INST_JMP = 9,
    INST_JEQ = 10,
    INST_JNE = 11,
    INST_JLT = 12,
    INST_JLE = 13,
    INST_JGT = 14,
    INST_JGE = 15,
    INST_CALL = 16,
    INST_RET = 17,
    INST_PUSH = 18,
    INST_POP = 19,
    INST_ALLOC = 20,
    INST_FREE = 21,
    INST_READ = 22,
    INST_WRITE = 23
} InstructionType;

// Operand types
typedef enum {
    OPERAND_REGISTER = 0,
    OPERAND_IMMEDIATE = 1,
    OPERAND_MEMORY = 2,
    OPERAND_LABEL = 3
} OperandType;

// Operand structure
typedef struct {
    OperandType type;
    union {
        int register_number;
        int immediate_value;
        int memory_address;
        char* label_name;
    } data;
} Operand;

// Instruction structure
typedef struct {
    InstructionType type;
    Operand operands[3];
    int operand_count;
    char* label;
    int line;
} Instruction;

// Code generator structure
typedef struct {
    Instruction instructions[MAX_CODE_SIZE];
    int instruction_count;
    int register_count;
    int label_count;
    SymbolTable* symbol_table;
    char error_message[MAX_ERROR_MESSAGE];
    int has_error;
} CodeGenerator;

// =============================================================================
// OPTIMIZATION
// =============================================================================

// Optimization types
typedef enum {
    OPT_CONSTANT_FOLDING = 0,
    OPT_DEAD_CODE_ELIMINATION = 1,
    OPT_COMMON_SUBEXPRESSION_ELIMINATION = 2,
    OPT_STRENGTH_REDUCTION = 3,
    OPT_LOOP_OPTIMIZATION = 4,
    OPT_INLINING = 5
} OptimizationType;

// Optimizer structure
typedef struct {
    CodeGenerator* code_generator;
    int optimizations_enabled[6];
    char error_message[MAX_ERROR_MESSAGE];
    int has_error;
} Optimizer;

// =============================================================================
// CODE GENERATION
// =============================================================================

// Register allocation
typedef struct {
    int registers[32];
    int register_count;
    int used_registers[32];
    int spill_count;
} RegisterAllocator;

// Target code generator
typedef struct {
    CodeGenerator* intermediate_code;
    RegisterAllocator* register_allocator;
    char* target_code;
    int target_code_size;
    char error_message[MAX_ERROR_MESSAGE];
    int has_error;
} TargetCodeGenerator;

// =============================================================================
// COMPILER STRUCTURE
// =============================================================================

// Compiler structure
typedef struct {
    Lexer* lexer;
    Parser* parser;
    SymbolTable* symbol_table;
    CodeGenerator* code_generator;
    Optimizer* optimizer;
    TargetCodeGenerator* target_generator;
    char* source_code;
    char* intermediate_code;
    char* target_code;
    char error_message[MAX_ERROR_MESSAGE];
    int has_error;
    int warning_count;
    int error_count;
} Compiler;

// =============================================================================
// LEXER IMPLEMENTATION
// =============================================================================

// Initialize lexer
Lexer* initLexer(const char* source) {
    Lexer* lexer = malloc(sizeof(Lexer));
    if (!lexer) return NULL;
    
    memset(lexer, 0, sizeof(Lexer));
    lexer->source = source;
    lexer->line = 1;
    lexer->column = 1;
    
    return lexer;
}

// Get next character
char getNextChar(Lexer* lexer) {
    if (!lexer || !lexer->source || lexer->source[lexer->position] == '\0') {
        return '\0';
    }
    
    char c = lexer->source[lexer->position++];
    
    if (c == '\n') {
        lexer->line++;
        lexer->column = 1;
    } else {
        lexer->column++;
    }
    
    return c;
}

// Peek next character
char peekNextChar(Lexer* lexer) {
    if (!lexer || !lexer->source || lexer->source[lexer->position] == '\0') {
        return '\0';
    }
    
    return lexer->source[lexer->position];
}

// Skip whitespace
void skipWhitespace(Lexer* lexer) {
    char c = peekNextChar(lexer);
    while (c == ' ' || c == '\t' || c == '\n' || c == '\r') {
        getNextChar(lexer);
        c = peekNextChar(lexer);
    }
}

// Check if character is identifier start
int isIdentifierStart(char c) {
    return isalpha(c) || c == '_';
}

// Check if character is identifier character
int isIdentifierChar(char c) {
    return isalnum(c) || c == '_';
}

// Check if character is digit
int isDigit(char c) {
    return isdigit(c);
}

// Check if character is operator
int isOperator(char c) {
    return c == '+' || c == '-' || c == '*' || c == '/' || c == '%' ||
           c == '=' || c == '!' || c == '<' || c == '>' || c == '&' ||
           c == '|' || c == '^' || c == '~';
}

// Check if character is delimiter
int isDelimiter(char c) {
    return c == '(' || c == ')' || c == '[' || c == ']' || c == '{' || c == '}' ||
           c == ',' || c == ';' || c == ':' || c == '.' || c == '#';
}

// Check keyword
KeywordType checkKeyword(const char* identifier) {
    if (strcmp(identifier, "int") == 0) return KEYWORD_INT;
    if (strcmp(identifier, "float") == 0) return KEYWORD_FLOAT;
    if (strcmp(identifier, "char") == 0) return KEYWORD_CHAR;
    if (strcmp(identifier, "double") == 0) return KEYWORD_DOUBLE;
    if (strcmp(identifier, "void") == 0) return KEYWORD_VOID;
    if (strcmp(identifier, "if") == 0) return KEYWORD_IF;
    if (strcmp(identifier, "else") == 0) return KEYWORD_ELSE;
    if (strcmp(identifier, "while") == 0) return KEYWORD_WHILE;
    if (strcmp(identifier, "for") == 0) return KEYWORD_FOR;
    if (strcmp(identifier, "return") == 0) return KEYWORD_RETURN;
    if (strcmp(identifier, "struct") == 0) return KEYWORD_STRUCT;
    if (strcmp(identifier, "union") == 0) return KEYWORD_UNION;
    if (strcmp(identifier, "enum") == 0) return KEYWORD_ENUM;
    if (strcmp(identifier, "typedef") == 0) return KEYWORD_TYPEDEF;
    if (strcmp(identifier, "const") == 0) return KEYWORD_CONST;
    if (strcmp(identifier, "static") == 0) return KEYWORD_STATIC;
    if (strcmp(identifier, "extern") == 0) return KEYWORD_EXTERN;
    if (strcmp(identifier, "break") == 0) return KEYWORD_BREAK;
    if (strcmp(identifier, "continue") == 0) return KEYWORD_CONTINUE;
    if (strcmp(identifier, "switch") == 0) return KEYWORD_SWITCH;
    if (strcmp(identifier, "case") == 0) return KEYWORD_CASE;
    if (strcmp(identifier, "default") == 0) return KEYWORD_DEFAULT;
    if (strcmp(identifier, "do") == 0) return KEYWORD_DO;
    if (strcmp(identifier, "sizeof") == 0) return KEYWORD_SIZEOF;
    if (strcmp(identifier, "include") == 0) return KEYWORD_INCLUDE;
    if (strcmp(identifier, "define") == 0) return KEYWORD_DEFINE;
    
    return -1; // Not a keyword
}

// Tokenize identifier
void tokenizeIdentifier(Lexer* lexer, Token* token) {
    char buffer[MAX_IDENTIFIER_LENGTH];
    int buffer_pos = 0;
    
    char c = peekNextChar(lexer);
    while (isIdentifierChar(c)) {
        buffer[buffer_pos++] = getNextChar(lexer);
        c = peekNextChar(lexer);
    }
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_IDENTIFIER;
    token->keyword = checkKeyword(buffer);
    if (token->keyword >= 0) {
        token->type = TOKEN_KEYWORD;
    }
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
    token->length = buffer_pos;
}

// Tokenize number
void tokenizeNumber(Lexer* lexer, Token* token) {
    char buffer[MAX_STRING_SIZE];
    int buffer_pos = 0;
    int is_float = 0;
    
    char c = peekNextChar(lexer);
    while (isDigit(c) || c == '.') {
        if (c == '.') is_float = 1;
        buffer[buffer_pos++] = getNextChar(lexer);
        c = peekNextChar(lexer);
    }
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_NUMBER;
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
    token->length = buffer_pos;
}

// Tokenize string
void tokenizeString(Lexer* lexer, Token* token) {
    char buffer[MAX_STRING_SIZE];
    int buffer_pos = 0;
    
    getNextChar(lexer); // Skip opening quote
    
    char c = peekNextChar(lexer);
    while (c != '\0' && c != '"') {
        if (c == '\\') {
            getNextChar(lexer); // Skip escape character
            c = peekNextChar(lexer);
            if (c == 'n') buffer[buffer_pos++] = '\n';
            else if (c == 't') buffer[buffer_pos++] = '\t';
            else if (c == 'r') buffer[buffer_pos++] = '\r';
            else if (c == '\\') buffer[buffer_pos++] = '\\';
            else if (c == '"') buffer[buffer_pos++] = '"';
            else buffer[buffer_pos++] = getNextChar(lexer);
        } else {
            buffer[buffer_pos++] = getNextChar(lexer);
            c = peekNextChar(lexer);
        }
    }
    
    getNextChar(lexer); // Skip closing quote
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_STRING;
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
    token->length = buffer_pos;
}

// Tokenize comment
void tokenizeComment(Lexer* lexer, Token* token) {
    char buffer[MAX_STRING_SIZE];
    int buffer_pos = 0;
    
    char c = getNextChar(lexer); // Skip '/'
    char next_c = peekNextChar(lexer);
    
    if (next_c == '/') {
        // Single line comment
        getNextChar(lexer); // Skip second '/'
        c = peekNextChar(lexer);
        while (c != '\0' && c != '\n') {
            buffer[buffer_pos++] = getNextChar(lexer);
            c = peekNextChar(lexer);
        }
    } else if (next_c == '*') {
        // Multi-line comment
        getNextChar(lexer); // Skip '*'
        c = peekNextChar(lexer);
        while (c != '\0') {
            if (c == '*') {
                getNextChar(lexer);
                c = peekNextChar(lexer);
                if (c == '/') {
                    getNextChar(lexer); // Skip '/'
                    break;
                }
                buffer[buffer_pos++] = '*';
            } else {
                buffer[buffer_pos++] = getNextChar(lexer);
                c = peekNextChar(lexer);
            }
        }
    }
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_COMMENT;
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
    token->length = buffer_pos;
}

// Tokenize operator
void tokenizeOperator(Lexer* lexer, Token* token) {
    char buffer[8];
    int buffer_pos = 0;
    
    char c = getNextChar(lexer);
    buffer[buffer_pos++] = c;
    
    char next_c = peekNextChar(lexer);
    
    // Check for multi-character operators
    if ((c == '=' && next_c == '=') ||
        (c == '!' && next_c == '=') ||
        (c == '+' && next_c == '=') ||
        (c == '-' && next_c == '=') ||
        (c == '*' && next_c == '=') ||
        (c == '/' && next_c == '=') ||
        (c == '%' && next_c == '=') ||
        (c == '<' && next_c == '=') ||
        (c == '>' && next_c == '=') ||
        (c == '&' && next_c == '&') ||
        (c == '|' && next_c == '|') ||
        (c == '+' && next_c == '+') ||
        (c == '-' && next_c == '-') ||
        (c == '<' && next_c == '<') ||
        (c == '>' && next_c == '>')) {
        
        buffer[buffer_pos++] = getNextChar(lexer);
    }
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_OPERATOR;
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
    token->length = buffer_pos;
}

// Tokenize preprocessor directive
void tokenizeDirective(Lexer* lexer, Token* token) {
    char buffer[MAX_STRING_SIZE];
    int buffer_pos = 0;
    
    getNextChar(lexer); // Skip '#'
    
    char c = peekNextChar(lexer);
    while (isIdentifierChar(c)) {
        buffer[buffer_pos++] = getNextChar(lexer);
        c = peekNextChar(lexer);
    }
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_DIRECTIVE;
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
    token->length = buffer_pos;
}

// Get next token
Token getNextToken(Lexer* lexer) {
    Token token;
    memset(&token, 0, sizeof(Token));
    
    skipWhitespace(lexer);
    
    char c = peekNextChar(lexer);
    
    if (c == '\0') {
        token.type = TOKEN_EOF;
        return token;
    }
    
    if (isIdentifierStart(c)) {
        tokenizeIdentifier(lexer, &token);
    } else if (isDigit(c)) {
        tokenizeNumber(lexer, &token);
    } else if (c == '"') {
        tokenizeString(lexer, &token);
    } else if (c == '/') {
        char next_c = peekNextChar(lexer);
        if (next_c == '/' || next_c == '*') {
            tokenizeComment(lexer, &token);
        } else {
            tokenizeOperator(lexer, &token);
        }
    } else if (isOperator(c)) {
        tokenizeOperator(lexer, &token);
    } else if (isDelimiter(c)) {
        char buffer[2] = {c, '\0'};
        token.type = TOKEN_DELIMITER;
        strncpy(token->value, buffer, sizeof(token->value) - 1);
        token.line = lexer->line;
        token.column = lexer->column;
        token.length = 1;
        getNextChar(lexer);
    } else if (c == '#') {
        tokenizeDirective(lexer, &token);
    } else {
        token.type = TOKEN_ERROR;
        snprintf(token.value, sizeof(token.value), "Unexpected character: %c", c);
        token.line = lexer->line;
        token.column = lexer->column;
        getNextChar(lexer);
    }
    
    return token;
}

// Tokenize source
int tokenizeSource(Lexer* lexer) {
    Token token;
    
    do {
        token = getNextToken(lexer);
        
        if (token.type == TOKEN_ERROR) {
            lexer->has_error = 1;
            strncpy(lexer->error_message, token.value, sizeof(lexer->error_message) - 1);
            return -1;
        }
        
        if (token.type != TOKEN_COMMENT) { // Skip comments in token list
            lexer->tokens[lexer->token_count++] = token;
        }
        
    } while (token.type != TOKEN_EOF && lexer->token_count < MAX_TOKENS);
    
    return 0;
}

// =============================================================================
// PARSER IMPLEMENTATION
// =============================================================================

// Initialize parser
Parser* initParser(Lexer* lexer) {
    Parser* parser = malloc(sizeof(Parser));
    if (!parser) return NULL;
    
    memset(parser, 0, sizeof(Parser));
    parser->lexer = lexer;
    parser->current_token = &lexer->tokens[0];
    parser->lookahead_token = &lexer->tokens[1];
    
    return parser;
}

// Create AST node
ASTNode* createASTNode(ASTNodeType type, int line, int column) {
    ASTNode* node = malloc(sizeof(ASTNode));
    if (!node) return NULL;
    
    memset(node, 0, sizeof(ASTNode));
    node->type = type;
    node->line = line;
    node->column = column;
    
    return node;
}

// Expect token
int expectToken(Parser* parser, TokenType type) {
    if (parser->current_token->type != type) {
        parser->has_error = 1;
        parser->error_line = parser->current_token->line;
        parser->error_column = parser->current_token->column;
        snprintf(parser->error_message, sizeof(parser->error_message),
                "Expected token type %d, got %d", type, parser->current_token->type);
        return -1;
    }
    
    return 0;
}

// Consume token
int consumeToken(Parser* parser) {
    parser->current_token++;
    parser->lookahead_token++;
    return 0;
}

// Parse primary expression
ASTNode* parsePrimaryExpression(Parser* parser) {
    ASTNode* node = NULL;
    
    if (parser->current_token->type == TOKEN_IDENTIFIER) {
        node = createASTNode(AST_IDENTIFIER, parser->current_token->line, parser->current_token->column);
        node->data.identifier.name = strdup(parser->current_token->value);
        consumeToken(parser);
    } else if (parser->current_token->type == TOKEN_NUMBER) {
        node = createASTNode(AST_CONSTANT, parser->current_token->line, parser->current_token->column);
        // Determine number type
        if (strchr(parser->current_token->value, '.')) {
            node->data.constant.value_type = 1; // float
            node->data.constant.value.float_value = atof(parser->current_token->value);
        } else {
            node->data.constant.value_type = 0; // int
            node->data.constant.value.int_value = atoi(parser->current_token->value);
        }
        consumeToken(parser);
    } else if (parser->current_token->type == TOKEN_STRING) {
        node = createASTNode(AST_STRING_LITERAL, parser->current_token->line, parser->current_token->column);
        node->data.constant.value_type = 3; // string
        node->data.constant.value.string_value = strdup(parser->current_token->value);
        consumeToken(parser);
    } else if (parser->current_token->type == TOKEN_DELIMITER && 
               strcmp(parser->current_token->value, "(") == 0) {
        consumeToken(parser);
        node = parseExpression(parser);
        if (expectToken(parser, TOKEN_DELIMITER) == 0) {
            if (strcmp(parser->current_token->value, ")") == 0) {
                consumeToken(parser);
            } else {
                parser->has_error = 1;
                snprintf(parser->error_message, sizeof(parser->error_message),
                        "Expected ')', got '%s'", parser->current_token->value);
            }
        }
    }
    
    return node;
}

// Parse postfix expression
ASTNode* parsePostfixExpression(Parser* parser) {
    ASTNode* node = parsePrimaryExpression(parser);
    
    while (parser->current_token->type == TOKEN_DELIMITER &&
           (strcmp(parser->current_token->value, "[") == 0 ||
            strcmp(parser->current_token->value, "(") == 0 ||
            strcmp(parser->current_token->value, ".") == 0 ||
            strcmp(parser->current_token->value, "->") == 0)) {
        
        if (strcmp(parser->current_token->value, "[") == 0) {
            // Array subscript
            ASTNode* array_node = createASTNode(AST_ARRAY_SUBSCRIPT, node->line, node->column);
            array_node->children[0] = node;
            array_node->child_count = 1;
            
            consumeToken(parser);
            array_node->children[1] = parseExpression(parser);
            array_node->child_count = 2;
            
            if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                if (strcmp(parser->current_token->value, "]") == 0) {
                    consumeToken(parser);
                } else {
                    parser->has_error = 1;
                    snprintf(parser->error_message, sizeof(parser->error_message),
                            "Expected ']', got '%s'", parser->current_token->value);
                }
            }
            
            node = array_node;
        } else if (strcmp(parser->current_token->value, "(") == 0) {
            // Function call
            ASTNode* call_node = createASTNode(AST_FUNCTION_CALL, node->line, node->column);
            call_node->data.function_call.function = node;
            call_node->children[0] = node;
            call_node->child_count = 1;
            
            consumeToken(parser);
            
            // Parse arguments
            if (parser->current_token->type != TOKEN_DELIMITER || strcmp(parser->current_token->value, ")") != 0) {
                call_node->children[1] = parseExpression(parser);
                call_node->child_count = 2;
                call_node->data.function_call.argument_count = 1;
                
                while (parser->current_token->type == TOKEN_DELIMITER && strcmp(parser->current_token->value, ",") == 0) {
                    consumeToken(parser);
                    call_node->children[call_node->child_count] = parseExpression(parser);
                    call_node->child_count++;
                    call_node->data.function_call.argument_count++;
                }
            }
            
            if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                if (strcmp(parser->current_token->value, ")") == 0) {
                    consumeToken(parser);
                } else {
                    parser->has_error = 1;
                    snprintf(parser->error_message, sizeof(parser->error_message),
                            "Expected ')', got '%s'", parser->current_token->value);
                }
            }
            
            node = call_node;
        } else if (strcmp(parser->current_token->value, ".") == 0) {
            // Member access
            ASTNode* member_node = createASTNode(AST_MEMBER_ACCESS, node->line, node->column);
            member_node->children[0] = node;
            member_node->child_count = 1;
            
            consumeToken(parser);
            if (expectToken(parser, TOKEN_IDENTIFIER) == 0) {
                member_node->children[1] = createASTNode(AST_IDENTIFIER, parser->current_token->line, parser->current_token->column);
                member_node->children[1]->data.identifier.name = strdup(parser->current_token->value);
                member_node->child_count = 2;
                consumeToken(parser);
            }
            
            node = member_node;
        }
    }
    
    return node;
}

// Parse unary expression
ASTNode* parseUnaryExpression(Parser* parser) {
    ASTNode* node = NULL;
    
    if (parser->current_token->type == TOKEN_OPERATOR &&
        (strcmp(parser->current_token->value, "+") == 0 ||
         strcmp(parser->current_token->value, "-") == 0 ||
         strcmp(parser->current_token->value, "!") == 0 ||
         strcmp(parser->current_token->value, "~") == 0 ||
         strcmp(parser->current_token->value, "*") == 0 ||
         strcmp(parser->current_token->value, "&") == 0)) {
        
        node = createASTNode(AST_UNARY_EXPRESSION, parser->current_token->line, parser->current_token->column);
        node->data.unary_expression.operator = strdup(parser->current_token->value);
        consumeToken(parser);
        node->children[0] = parseUnaryExpression(parser);
        node->child_count = 1;
    } else {
        node = parsePostfixExpression(parser);
    }
    
    return node;
}

// Parse multiplicative expression
ASTNode* parseMultiplicativeExpression(Parser* parser) {
    ASTNode* node = parseUnaryExpression(parser);
    
    while (parser->current_token->type == TOKEN_OPERATOR &&
           (strcmp(parser->current_token->value, "*") == 0 ||
            strcmp(parser->current_token->value, "/") == 0 ||
            strcmp(parser->current_token->value, "%") == 0)) {
        
        ASTNode* binary_node = createASTNode(AST_MULTIPLICATIVE_EXPRESSION, node->line, node->column);
        binary_node->data.binary_expression.operator = strdup(parser->current_token->value);
        binary_node->children[0] = node;
        binary_node->child_count = 1;
        
        consumeToken(parser);
        binary_node->children[1] = parseUnaryExpression(parser);
        binary_node->child_count = 2;
        
        node = binary_node;
    }
    
    return node;
}

// Parse additive expression
ASTNode* parseAdditiveExpression(Parser* parser) {
    ASTNode* node = parseMultiplicativeExpression(parser);
    
    while (parser->current_token->type == TOKEN_OPERATOR &&
           (strcmp(parser->current_token->value, "+") == 0 ||
            strcmp(parser->current_token->value, "-") == 0)) {
        
        ASTNode* binary_node = createASTNode(AST_ADDITIVE_EXPRESSION, node->line, node->column);
        binary_node->data.binary_expression.operator = strdup(parser->current_token->value);
        binary_node->children[0] = node;
        binary_node->child_count = 1;
        
        consumeToken(parser);
        binary_node->children[1] = parseMultiplicativeExpression(parser);
        binary_node->child_count = 2;
        
        node = binary_node;
    }
    
    return node;
}

// Parse relational expression
ASTNode* parseRelationalExpression(Parser* parser) {
    ASTNode* node = parseAdditiveExpression(parser);
    
    while (parser->current_token->type == TOKEN_OPERATOR &&
           (strcmp(parser->current_token->value, "<") == 0 ||
            strcmp(parser->current_token->value, ">") == 0 ||
            strcmp(parser->current_token->value, "<=") == 0 ||
            strcmp(parser->current_token->value, ">=") == 0)) {
        
        ASTNode* binary_node = createASTNode(AST_RELATIONAL_EXPRESSION, node->line, node->column);
        binary_node->data.binary_expression.operator = strdup(parser->current_token->value);
        binary_node->children[0] = node;
        binary_node->child_count = 1;
        
        consumeToken(parser);
        binary_node->children[1] = parseAdditiveExpression(parser);
        binary_node->child_count = 2;
        
        node = binary_node;
    }
    
    return node;
}

// Parse equality expression
ASTNode* parseEqualityExpression(Parser* parser) {
    ASTNode* node = parseRelationalExpression(parser);
    
    while (parser->current_token->type == TOKEN_OPERATOR &&
           (strcmp(parser->current_token->value, "==") == 0 ||
            strcmp(parser->current_token->value, "!=") == 0)) {
        
        ASTNode* binary_node = createASTNode(AST_EQUALITY_EXPRESSION, node->line, node->column);
        binary_node->data.binary_expression.operator = strdup(parser->current_token->value);
        binary_node->children[0] = node;
        binary_node->child_count = 1;
        
        consumeToken(parser);
        binary_node->children[1] = parseRelationalExpression(parser);
        binary_node->child_count = 2;
        
        node = binary_node;
    }
    
    return node;
}

// Parse logical AND expression
ASTNode* parseLogicalAndExpression(Parser* parser) {
    ASTNode* node = parseEqualityExpression(parser);
    
    while (parser->current_token->type == TOKEN_OPERATOR &&
           strcmp(parser->current_token->value, "&&") == 0) {
        
        ASTNode* binary_node = createASTNode(AST_LOGICAL_AND_EXPRESSION, node->line, node->column);
        binary_node->data.binary_expression.operator = strdup(parser->current_token->value);
        binary_node->children[0] = node;
        binary_node->child_count = 1;
        
        consumeToken(parser);
        binary_node->children[1] = parseEqualityExpression(parser);
        binary_node->child_count = 2;
        
        node = binary_node;
    }
    
    return node;
}

// Parse logical OR expression
ASTNode* parseLogicalOrExpression(Parser* parser) {
    ASTNode* node = parseLogicalAndExpression(parser);
    
    while (parser->current_token->type == TOKEN_OPERATOR &&
           strcmp(parser->current_token->value, "||") == 0) {
        
        ASTNode* binary_node = createASTNode(AST_LOGICAL_OR_EXPRESSION, node->line, node->column);
        binary_node->data.binary_expression.operator = strdup(parser->current_token->value);
        binary_node->children[0] = node;
        binary_node->child_count = 1;
        
        consumeToken(parser);
        binary_node->children[1] = parseLogicalAndExpression(parser);
        binary_node->child_count = 2;
        
        node = binary_node;
    }
    
    return node;
}

// Parse assignment expression
ASTNode* parseAssignmentExpression(Parser* parser) {
    ASTNode* node = parseLogicalOrExpression(parser);
    
    if (parser->current_token->type == TOKEN_OPERATOR &&
        (strcmp(parser->current_token->value, "=") == 0 ||
         strcmp(parser->current_token->value, "+=") == 0 ||
         strcmp(parser->current_token->value, "-=") == 0 ||
         strcmp(parser->current_token->value, "*=") == 0 ||
         strcmp(parser->current_token->value, "/=") == 0 ||
         strcmp(parser->current_token->value, "%=") == 0)) {
        
        ASTNode* assign_node = createASTNode(AST_ASSIGNMENT_EXPRESSION, node->line, node->column);
        assign_node->data.binary_expression.operator = strdup(parser->current_token->value);
        assign_node->children[0] = node;
        assign_node->child_count = 1;
        
        consumeToken(parser);
        assign_node->children[1] = parseAssignmentExpression(parser);
        assign_node->child_count = 2;
        
        node = assign_node;
    }
    
    return node;
}

// Parse expression
ASTNode* parseExpression(Parser* parser) {
    return parseAssignmentExpression(parser);
}

// Parse statement
ASTNode* parseStatement(Parser* parser) {
    ASTNode* node = NULL;
    
    if (parser->current_token->type == TOKEN_KEYWORD &&
        parser->current_token->keyword == KEYWORD_IF) {
        
        node = parseSelectionStatement(parser);
    } else if (parser->current_token->type == TOKEN_KEYWORD &&
               (parser->current_token->keyword == KEYWORD_WHILE ||
                parser->current_token->keyword == KEYWORD_FOR ||
                parser->current_token->keyword == KEYWORD_DO)) {
        
        node = parseIterationStatement(parser);
    } else if (parser->current_token->type == TOKEN_KEYWORD &&
               (parser->current_token->keyword == KEYWORD_RETURN ||
                parser->current_token->keyword == KEYWORD_BREAK ||
                parser->current_token->keyword == KEYWORD_CONTINUE)) {
        
        node = parseJumpStatement(parser);
    } else if (parser->current_token->type == TOKEN_DELIMITER &&
               strcmp(parser->current_token->value, "{") == 0) {
        
        node = parseCompoundStatement(parser);
    } else {
        node = parseExpressionStatement(parser);
    }
    
    return node;
}

// Parse expression statement
ASTNode* parseExpressionStatement(Parser* parser) {
    ASTNode* node = createASTNode(AST_EXPRESSION_STATEMENT, parser->current_token->line, parser->current_token->column);
    
    if (parser->current_token->type != TOKEN_DELIMITER || strcmp(parser->current_token->value, ";") != 0) {
        node->children[0] = parseExpression(parser);
        node->child_count = 1;
    }
    
    if (expectToken(parser, TOKEN_DELIMITER) == 0) {
        if (strcmp(parser->current_token->value, ";") == 0) {
            consumeToken(parser);
        } else {
            parser->has_error = 1;
            snprintf(parser->error_message, sizeof(parser->error_message),
                    "Expected ';', got '%s'", parser->current_token->value);
        }
    }
    
    return node;
}

// Parse compound statement
ASTNode* parseCompoundStatement(Parser* parser) {
    ASTNode* node = createASTNode(AST_COMPOUND_STATEMENT, parser->current_token->line, parser->current_token->column);
    
    consumeToken(parser); // Skip '{'
    
    while (parser->current_token->type != TOKEN_DELIMITER || strcmp(parser->current_token->value, "}") != 0) {
        node->children[node->child_count] = parseStatement(parser);
        node->child_count++;
    }
    
    consumeToken(parser); // Skip '}'
    
    return node;
}

// Parse selection statement (if-else)
ASTNode* parseSelectionStatement(Parser* parser) {
    ASTNode* node = createASTNode(AST_SELECTION_STATEMENT, parser->current_token->line, parser->current_token->column);
    
    consumeToken(parser); // Skip 'if'
    
    if (expectToken(parser, TOKEN_DELIMITER) == 0) {
        if (strcmp(parser->current_token->value, "(") == 0) {
            consumeToken(parser);
            node->children[0] = parseExpression(parser);
            node->child_count = 1;
            
            if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                if (strcmp(parser->current_token->value, ")") == 0) {
                    consumeToken(parser);
                    node->children[1] = parseStatement(parser);
                    node->child_count = 2;
                    
                    // Check for else
                    if (parser->current_token->type == TOKEN_KEYWORD &&
                        parser->current_token->keyword == KEYWORD_ELSE) {
                        
                        consumeToken(parser);
                        node->children[2] = parseStatement(parser);
                        node->child_count = 3;
                    }
                } else {
                    parser->has_error = 1;
                    snprintf(parser->error_message, sizeof(parser->error_message),
                            "Expected ')', got '%s'", parser->current_token->value);
                }
            }
        } else {
            parser->has_error = 1;
            snprintf(parser->error_message, sizeof(parser->error_message),
                    "Expected '(', got '%s'", parser->current_token->value);
        }
    }
    
    return node;
}

// Parse iteration statement (while, for, do-while)
ASTNode* parseIterationStatement(Parser* parser) {
    ASTNode* node = createASTNode(AST_ITERATION_STATEMENT, parser->current_token->line, parser->current_token->column);
    
    if (parser->current_token->keyword == KEYWORD_WHILE) {
        // while statement
        consumeToken(parser);
        
        if (expectToken(parser, TOKEN_DELIMITER) == 0) {
            if (strcmp(parser->current_token->value, "(") == 0) {
                consumeToken(parser);
                node->children[0] = parseExpression(parser);
                node->child_count = 1;
                
                if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                    if (strcmp(parser->current_token->value, ")") == 0) {
                        consumeToken(parser);
                        node->children[1] = parseStatement(parser);
                        node->child_count = 2;
                    } else {
                        parser->has_error = 1;
                        snprintf(parser->error_message, sizeof(parser->error_message),
                                "Expected ')', got '%s'", parser->current_token->value);
                    }
                }
            } else {
                parser->has_error = 1;
                snprintf(parser->error_message, sizeof(parser->error_message),
                        "Expected '(', got '%s'", parser->current_token->value);
            }
        }
    } else if (parser->current_token->keyword == KEYWORD_FOR) {
        // for statement
        consumeToken(parser);
        
        if (expectToken(parser, TOKEN_DELIMITER) == 0) {
            if (strcmp(parser->current_token->value, "(") == 0) {
                consumeToken(parser);
                node->children[0] = parseExpression(parser); // Initialization
                node->child_count = 1;
                
                if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                    if (strcmp(parser->current_token->value, ";") == 0) {
                        consumeToken(parser);
                        node->children[1] = parseExpression(parser); // Condition
                        node->child_count = 2;
                        
                        if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                            if (strcmp(parser->current_token->value, ";") == 0) {
                                consumeToken(parser);
                                node->children[2] = parseExpression(parser); // Increment
                                node->child_count = 3;
                                
                                if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                                    if (strcmp(parser->current_token->value, ")") == 0) {
                                        consumeToken(parser);
                                        node->children[3] = parseStatement(parser);
                                        node->child_count = 4;
                                    } else {
                                        parser->has_error = 1;
                                        snprintf(parser->error_message, sizeof(parser->error_message),
                                                "Expected ')', got '%s'", parser->current_token->value);
                                    }
                                }
                            } else {
                                parser->has_error = 1;
                                snprintf(parser->error_message, sizeof(parser->error_message),
                                        "Expected ';', got '%s'", parser->current_token->value);
                            }
                        }
                    } else {
                        parser->has_error = 1;
                        snprintf(parser->error_message, sizeof(parser->error_message),
                                "Expected ';', got '%s'", parser->current_token->value);
                    }
                }
            } else {
                parser->has_error = 1;
                snprintf(parser->error_message, sizeof(parser->error_message),
                        "Expected '(', got '%s'", parser->current_token->value);
            }
        }
    } else if (parser->current_token->keyword == KEYWORD_DO) {
        // do-while statement
        consumeToken(parser);
        node->children[0] = parseStatement(parser);
        node->child_count = 1;
        
        if (parser->current_token->keyword == KEYWORD_WHILE) {
            consumeToken(parser);
            
            if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                if (strcmp(parser->current_token->value, "(") == 0) {
                    consumeToken(parser);
                    node->children[1] = parseExpression(parser);
                    node->child_count = 2;
                    
                    if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                        if (strcmp(parser->current_token->value, ")") == 0) {
                            consumeToken(parser);
                        } else {
                            parser->has_error = 1;
                            snprintf(parser->error_message, sizeof(parser->error_message),
                                    "Expected ')', got '%s'", parser->current_token->value);
                        }
                    }
                } else {
                    parser->has_error = 1;
                    snprintf(parser->error_message, sizeof(parser->error_message),
                            "Expected '(', got '%s'", parser->current_token->value);
                }
            }
        }
        
        if (expectToken(parser, TOKEN_DELIMITER) == 0) {
            if (strcmp(parser->current_token->value, ";") == 0) {
                consumeToken(parser);
            } else {
                parser->has_error = 1;
                snprintf(parser->error_message, sizeof(parser->error_message),
                        "Expected ';', got '%s'", parser->current_token->value);
            }
        }
    }
    
    return node;
}

// Parse jump statement (return, break, continue)
ASTNode* parseJumpStatement(Parser* parser) {
    ASTNode* node = createASTNode(AST_JUMP_STATEMENT, parser->current_token->line, parser->current_token->column);
    
    KeywordType keyword = parser->current_token->keyword;
    consumeToken(parser);
    
    if (keyword == KEYWORD_RETURN) {
        if (parser->current_token->type != TOKEN_DELIMITER || strcmp(parser->current_token->value, ";") != 0) {
            node->children[0] = parseExpression(parser);
            node->child_count = 1;
        }
    }
    
    if (expectToken(parser, TOKEN_DELIMITER) == 0) {
        if (strcmp(parser->current_token->value, ";") == 0) {
            consumeToken(parser);
        } else {
            parser->has_error = 1;
            snprintf(parser->error_message, sizeof(parser->error_message),
                    "Expected ';', got '%s'", parser->current_token->value);
        }
    }
    
    return node;
}

// Parse declaration
ASTNode* parseDeclaration(Parser* parser) {
    ASTNode* node = createASTNode(AST_DECLARATION, parser->current_token->line, parser->current_token->column);
    
    // Parse type specifiers
    node->children[0] = parseDeclarationSpecifiers(parser);
    node->child_count = 1;
    
    // Parse declarators
    node->children[1] = parseInitDeclaratorList(parser);
    node->child_count = 2;
    
    if (expectToken(parser, TOKEN_DELIMITER) == 0) {
        if (strcmp(parser->current_token->value, ";") == 0) {
            consumeToken(parser);
        } else {
            parser->has_error = 1;
            snprintf(parser->error_message, sizeof(parser->error_message),
                    "Expected ';', got '%s'", parser->current_token->value);
        }
    }
    
    return node;
}

// Parse function definition
ASTNode* parseFunctionDefinition(Parser* parser) {
    ASTNode* node = createASTNode(AST_FUNCTION_DEFINITION, parser->current_token->line, parser->current_token->column);
    
    // Parse return type
    node->children[0] = parseDeclarationSpecifiers(parser);
    node->child_count = 1;
    
    // Parse function name
    if (expectToken(parser, TOKEN_IDENTIFIER) == 0) {
        node->children[1] = createASTNode(AST_IDENTIFIER, parser->current_token->line, parser->current_token->column);
        node->children[1]->data.identifier.name = strdup(parser->current_token->value);
        node->child_count = 2;
        consumeToken(parser);
    }
    
    // Parse parameters
    if (expectToken(parser, TOKEN_DELIMITER) == 0) {
        if (strcmp(parser->current_token->value, "(") == 0) {
            consumeToken(parser);
            node->children[2] = parseParameterList(parser);
            node->child_count = 3;
            
            if (expectToken(parser, TOKEN_DELIMITER) == 0) {
                if (strcmp(parser->current_token->value, ")") == 0) {
                    consumeToken(parser);
                    node->children[3] = parseCompoundStatement(parser);
                    node->child_count = 4;
                } else {
                    parser->has_error = 1;
                    snprintf(parser->error_message, sizeof(parser->error_message),
                            "Expected ')', got '%s'", parser->current_token->value);
                }
            }
        } else {
            parser->has_error = 1;
            snprintf(parser->error_message, sizeof(parser->error_message),
                    "Expected '(', got '%s'", parser->current_token->value);
        }
    }
    
    return node;
}

// Parse translation unit (program)
ASTNode* parseTranslationUnit(Parser* parser) {
    ASTNode* node = createASTNode(AST_TRANSLATION_UNIT, parser->current_token->line, parser->current_token->column);
    
    while (parser->current_token->type != TOKEN_EOF) {
        if (parser->current_token->type == TOKEN_KEYWORD &&
            (parser->current_token->keyword == KEYWORD_INT ||
             parser->current_token->keyword == KEYWORD_FLOAT ||
             parser->current_token->keyword == KEYWORD_CHAR ||
             parser->current_token->keyword == KEYWORD_DOUBLE ||
             parser->current_token->keyword == KEYWORD_VOID ||
             parser->current_token->keyword == KEYWORD_STRUCT ||
             parser->current_token->keyword == KEYWORD_UNION ||
             parser->current_token->keyword == KEYWORD_ENUM ||
             parser->current_token->keyword == KEYWORD_TYPEDEF ||
             parser->current_token->keyword == KEYWORD_CONST ||
             parser->current_token->keyword == KEYWORD_STATIC ||
             parser->current_token->keyword == KEYWORD_EXTERN)) {
            
            // Check if this is a function definition
            Token* lookahead = parser->lookahead_token;
            if (lookahead && lookahead->type == TOKEN_IDENTIFIER &&
                parser->lexer->tokens[parser->lexer->token_count - 1].type == TOKEN_DELIMITER &&
                strcmp(parser->lexer->tokens[parser->lexer->token_count - 1].value, "{") == 0) {
                
                node->children[node->child_count] = parseFunctionDefinition(parser);
            } else {
                node->children[node->child_count] = parseDeclaration(parser);
            }
        } else {
            node->children[node->child_count] = parseStatement(parser);
        }
        
        node->child_count++;
    }
    
    return node;
}

// Parse source
int parseSource(Parser* parser) {
    parser->ast = parseTranslationUnit(parser);
    
    if (parser->has_error) {
        return -1;
    }
    
    return 0;
}

// =============================================================================
// SYMBOL TABLE IMPLEMENTATION
// =============================================================================

// Initialize symbol table
SymbolTable* initSymbolTable() {
    SymbolTable* table = malloc(sizeof(SymbolTable));
    if (!table) return NULL;
    
    memset(table, 0, sizeof(SymbolTable));
    table->current_scope = 0;
    
    return table;
}

// Create symbol
Symbol* createSymbol(const char* name, SymbolType type, SymbolKind kind, int scope_level, int line, int column) {
    Symbol* symbol = malloc(sizeof(Symbol));
    if (!symbol) return NULL;
    
    memset(symbol, 0, sizeof(Symbol));
    strncpy(symbol->name, name, sizeof(symbol->name) - 1);
    symbol->type = type;
    symbol->kind = kind;
    symbol->scope_level = scope_level;
    symbol->line = line;
    symbol->column = column;
    
    return symbol;
}

// Add symbol to table
int addSymbol(SymbolTable* table, Symbol* symbol) {
    if (!table || !symbol || table->symbol_count >= MAX_SYMBOLS) {
        return -1;
    }
    
    // Check for duplicate symbols in the same scope
    for (int i = 0; i < table->symbol_count; i++) {
        if (strcmp(table->symbols[i].name, symbol->name) == 0 &&
            table->symbols[i].scope_level == symbol->scope_level) {
            table->has_error = 1;
            snprintf(table->error_message, sizeof(table->error_message),
                    "Duplicate symbol '%s' at scope %d", symbol->name, symbol->scope_level);
            return -1;
        }
    }
    
    table->symbols[table->symbol_count++] = *symbol;
    return 0;
}

// Find symbol
Symbol* findSymbol(SymbolTable* table, const char* name) {
    if (!table || !name) return NULL;
    
    // Search from most recent to oldest
    for (int i = table->symbol_count - 1; i >= 0; i--) {
        if (strcmp(table->symbols[i].name, name) == 0) {
            return &table->symbols[i];
        }
    }
    
    return NULL;
}

// Enter new scope
void enterScope(SymbolTable* table) {
    if (table) {
        table->current_scope++;
    }
}

// Exit scope
void exitScope(SymbolTable* table) {
    if (table) {
        // Remove symbols from current scope
        for (int i = table->symbol_count - 1; i >= 0; i--) {
            if (table->symbols[i].scope_level == table->current_scope) {
                table->symbol_count--;
            } else {
                break;
            }
        }
        
        table->current_scope--;
    }
}

// =============================================================================
// CODE GENERATOR IMPLEMENTATION
// =============================================================================

// Initialize code generator
CodeGenerator* initCodeGenerator(SymbolTable* symbol_table) {
    CodeGenerator* generator = malloc(sizeof(CodeGenerator));
    if (!generator) return NULL;
    
    memset(generator, 0, sizeof(CodeGenerator));
    generator->register_count = 8; // Reserve some registers
    generator->symbol_table = symbol_table;
    
    return generator;
}

// Create instruction
Instruction* createInstruction(InstructionType type) {
    Instruction* instruction = malloc(sizeof(Instruction));
    if (!instruction) return NULL;
    
    memset(instruction, 0, sizeof(Instruction));
    instruction->type = type;
    
    return instruction;
}

// Add instruction
int addInstruction(CodeGenerator* generator, Instruction* instruction) {
    if (!generator || !instruction || generator->instruction_count >= MAX_CODE_SIZE) {
        return -1;
    }
    
    generator->instructions[generator->instruction_count++] = *instruction;
    return generator->instruction_count - 1;
}

// Generate code for AST node
int generateCodeForASTNode(CodeGenerator* generator, ASTNode* node) {
    if (!generator || !node) return -1;
    
    switch (node->type) {
        case AST_CONSTANT: {
            Instruction* instruction = createInstruction(INST_LOAD);
            instruction->operands[0].type = OPERAND_REGISTER;
            instruction->operands[0].data.register_number = generator->register_count++;
            instruction->operands[1].type = OPERAND_IMMEDIATE;
            
            if (node->data.constant.value_type == 0) { // int
                instruction->operands[1].data.immediate_value = node->data.constant.value.int_value;
            } else if (node->data.constant.value_type == 1) { // float
                instruction->operands[1].data.immediate_value = *(int*)&node->data.constant.value.float_value;
            }
            
            return addInstruction(generator, instruction);
        }
        
        case AST_IDENTIFIER: {
            Symbol* symbol = findSymbol(generator->symbol_table, node->data.identifier.name);
            if (!symbol) {
                generator->has_error = 1;
                snprintf(generator->error_message, sizeof(generator->error_message),
                        "Undefined symbol '%s'", node->data.identifier.name);
                return -1;
            }
            
            Instruction* instruction = createInstruction(INST_LOAD);
            instruction->operands[0].type = OPERAND_REGISTER;
            instruction->operands[0].data.register_number = generator->register_count++;
            instruction->operands[1].type = OPERAND_MEMORY;
            instruction->operands[1].data.memory_address = symbol - generator->symbol_table->symbols;
            
            return addInstruction(generator, instruction);
        }
        
        case AST_BINARY_EXPRESSION: {
            int left_reg = generateCodeForASTNode(generator, node->children[0]);
            int right_reg = generateCodeForASTNode(generator, node->children[1]);
            
            InstructionType inst_type;
            if (strcmp(node->data.binary_expression.operator, "+") == 0) {
                inst_type = INST_ADD;
            } else if (strcmp(node->data.binary_expression.operator, "-") == 0) {
                inst_type = INST_SUB;
            } else if (strcmp(node->data.binary_expression.operator, "*") == 0) {
                inst_type = INST_MUL;
            } else if (strcmp(node->data.binary_expression.operator, "/") == 0) {
                inst_type = INST_DIV;
            } else {
                generator->has_error = 1;
                snprintf(generator->error_message, sizeof(generator->error_message),
                        "Unknown binary operator '%s'", node->data.binary_expression.operator);
                return -1;
            }
            
            Instruction* instruction = createInstruction(inst_type);
            instruction->operands[0].type = OPERAND_REGISTER;
            instruction->operands[0].data.register_number = left_reg;
            instruction->operands[1].type = OPERAND_REGISTER;
            instruction->operands[1].data.register_number = right_reg;
            instruction->operands[2].type = OPERAND_REGISTER;
            instruction->operands[2].data.register_number = left_reg;
            
            return addInstruction(generator, instruction);
        }
        
        default:
            generator->has_error = 1;
            snprintf(generator->error_message, sizeof(generator->error_message),
                    "Unsupported AST node type %d", node->type);
            return -1;
    }
}

// =============================================================================
// OPTIMIZER IMPLEMENTATION
// =============================================================================

// Initialize optimizer
Optimizer* initOptimizer(CodeGenerator* code_generator) {
    Optimizer* optimizer = malloc(sizeof(Optimizer));
    if (!optimizer) return NULL;
    
    memset(optimizer, 0, sizeof(Optimizer));
    optimizer->code_generator = code_generator;
    
    // Enable all optimizations by default
    for (int i = 0; i < 6; i++) {
        optimizer->optimizations_enabled[i] = 1;
    }
    
    return optimizer;
}

// Perform constant folding
int performConstantFolding(Optimizer* optimizer) {
    if (!optimizer) return -1;
    
    CodeGenerator* generator = optimizer->code_generator;
    int optimized_count = 0;
    
    for (int i = 0; i < generator->instruction_count; i++) {
        Instruction* instruction = &generator->instructions[i];
        
        if (instruction->type == INST_ADD || instruction->type == INST_SUB ||
            instruction->type == INST_MUL || instruction->type == INST_DIV) {
            
            // Check if both operands are immediate constants
            if (instruction->operands[1].type == OPERAND_IMMEDIATE &&
                instruction->operands[2].type == OPERAND_IMMEDIATE) {
                
                int result = 0;
                if (instruction->type == INST_ADD) {
                    result = instruction->operands[1].data.immediate_value + instruction->operands[2].data.immediate_value;
                } else if (instruction->type == INST_SUB) {
                    result = instruction->operands[1].data.immediate_value - instruction->operands[2].data.immediate_value;
                } else if (instruction->type == INST_MUL) {
                    result = instruction->operands[1].data.immediate_value * instruction->operands[2].data.immediate_value;
                } else if (instruction->type == INST_DIV) {
                    if (instruction->operands[2].data.immediate_value != 0) {
                        result = instruction->operands[1].data.immediate_value / instruction->operands[2].data.immediate_value;
                    }
                }
                
                // Replace with constant load
                instruction->type = INST_LOAD;
                instruction->operands[1].type = OPERAND_IMMEDIATE;
                instruction->operands[1].data.immediate_value = result;
                instruction->operand_count = 2;
                
                optimized_count++;
            }
        }
    }
    
    return optimized_count;
}

// Perform dead code elimination
int performDeadCodeElimination(Optimizer* optimizer) {
    if (!optimizer) return -1;
    
    CodeGenerator* generator = optimizer->code_generator;
    int optimized_count = 0;
    
    // Mark reachable instructions
    int reachable[MAX_CODE_SIZE] = {0};
    reachable[0] = 1; // First instruction is reachable
    
    for (int i = 0; i < generator->instruction_count; i++) {
        if (reachable[i]) {
            Instruction* instruction = &generator->instructions[i];
            
            // Mark jump targets as reachable
            if (instruction->type == INST_JMP || instruction->type == INST_JEQ ||
                instruction->type == INST_JNE || instruction->type == INST_JLT ||
                instruction->type == INST_JLE || instruction->type == INST_JGT ||
                instruction->type == INST_JGE) {
                
                if (instruction->operands[0].type == OPERAND_LABEL) {
                    // Find target instruction (simplified)
                    int target = instruction->operands[0].data.immediate_value;
                    if (target >= 0 && target < generator->instruction_count) {
                        reachable[target] = 1;
                    }
                }
            }
            
            // Mark next instruction as reachable
            if (i + 1 < generator->instruction_count && !instruction->type == INST_RET) {
                reachable[i + 1] = 1;
            }
        }
    }
    
    // Remove unreachable instructions
    int write_index = 0;
    for (int i = 0; i < generator->instruction_count; i++) {
        if (reachable[i]) {
            generator->instructions[write_index++] = generator->instructions[i];
        } else {
            optimized_count++;
        }
    }
    
    generator->instruction_count = write_index;
    
    return optimized_count;
}

// Run optimizations
int runOptimizations(Optimizer* optimizer) {
    if (!optimizer) return -1;
    
    int total_optimized = 0;
    
    if (optimizer->optimizations_enabled[OPT_CONSTANT_FOLDING]) {
        total_optimized += performConstantFolding(optimizer);
    }
    
    if (optimizer->optimizations_enabled[OPT_DEAD_CODE_ELIMINATION]) {
        total_optimized += performDeadCodeElimination(optimizer);
    }
    
    return total_optimized;
}

// =============================================================================
// COMPILER IMPLEMENTATION
// =============================================================================

// Initialize compiler
Compiler* initCompiler() {
    Compiler* compiler = malloc(sizeof(Compiler));
    if (!compiler) return NULL;
    
    memset(compiler, 0, sizeof(Compiler));
    
    compiler->lexer = initLexer("");
    compiler->parser = initParser(compiler->lexer);
    compiler->symbol_table = initSymbolTable();
    compiler->code_generator = initCodeGenerator(compiler->symbol_table);
    compiler->optimizer = initOptimizer(compiler->code_generator);
    
    return compiler;
}

// Compile source code
int compileSource(Compiler* compiler, const char* source) {
    if (!compiler || !source) return -1;
    
    compiler->source_code = strdup(source);
    
    // Tokenize
    compiler->lexer->source = source;
    if (tokenizeSource(compiler->lexer) < 0) {
        compiler->has_error = 1;
        strncpy(compiler->error_message, compiler->lexer->error_message, sizeof(compiler->error_message) - 1);
        return -1;
    }
    
    printf("Tokenization complete: %d tokens\n", compiler->lexer->token_count);
    
    // Parse
    if (parseSource(compiler->parser) < 0) {
        compiler->has_error = 1;
        strncpy(compiler->error_message, compiler->parser->error_message, sizeof(compiler->error_message) - 1);
        return -1;
    }
    
    printf("Parsing complete: AST built successfully\n");
    
    // Semantic analysis
    // (Simplified - would normally perform type checking, etc.)
    printf("Semantic analysis complete\n");
    
    // Code generation
    if (generateCodeForASTNode(compiler->code_generator, compiler->parser->ast) < 0) {
        compiler->has_error = 1;
        strncpy(compiler->error_message, compiler->code_generator->error_message, sizeof(compiler->error_message) - 1);
        return -1;
    }
    
    printf("Code generation complete: %d instructions\n", compiler->code_generator->instruction_count);
    
    // Optimization
    int optimized_count = runOptimizations(compiler->optimizer);
    printf("Optimization complete: %d optimizations performed\n", optimized_count);
    
    return 0;
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateLexicalAnalysis() {
    printf("=== LEXICAL ANALYSIS DEMO ===\n");
    
    const char* source_code = 
        "int main() {\n"
        "    int x = 42;\n"
        "    float y = 3.14;\n"
        "    if (x > 0) {\n"
        "        return x + y;\n"
        "    }\n"
        "    return 0;\n"
        "}\n";
    
    printf("Source code:\n%s\n", source_code);
    
    Lexer* lexer = initLexer(source_code);
    if (!lexer) {
        printf("Failed to initialize lexer\n");
        return;
    }
    
    if (tokenizeSource(lexer) == 0) {
        printf("Tokenization successful!\n");
        printf("Tokens found: %d\n", lexer->token_count);
        
        printf("\nFirst 20 tokens:\n");
        for (int i = 0; i < lexer->token_count && i < 20; i++) {
            Token* token = &lexer->tokens[i];
            printf("  [%d] Type: %d, Keyword: %d, Value: '%s', Line: %d, Column: %d\n",
                   i, token->type, token->keyword, token->value, token->line, token->column);
        }
    } else {
        printf("Tokenization failed: %s\n", lexer->error_message);
    }
    
    free(lexer);
}

void demonstrateParsing() {
    printf("\n=== PARSING DEMO ===\n");
    
    const char* source_code = "int add(int a, int b) { return a + b; }";
    
    printf("Source code: %s\n", source_code);
    
    Lexer* lexer = initLexer(source_code);
    if (!lexer) {
        printf("Failed to initialize lexer\n");
        return;
    }
    
    if (tokenizeSource(lexer) == 0) {
        Parser* parser = initParser(lexer);
        if (!parser) {
            printf("Failed to initialize parser\n");
            free(lexer);
            return;
        }
        
        if (parseSource(parser) == 0) {
            printf("Parsing successful!\n");
            printf("AST root type: %d\n", parser->ast->type);
            printf("AST child count: %d\n", parser->ast->child_count);
            
            // Print AST structure (simplified)
            printf("\nAST structure:\n");
            printAST(parser->ast, 0);
        } else {
            printf("Parsing failed: %s\n", parser->error_message);
        }
        
        free(parser);
    } else {
        printf("Tokenization failed\n");
    }
    
    free(lexer);
}

void demonstrateSemanticAnalysis() {
    printf("\n=== SEMANTIC ANALYSIS DEMO ===\n");
    
    SymbolTable* table = initSymbolTable();
    if (!table) {
        printf("Failed to initialize symbol table\n");
        return;
    }
    
    // Add some symbols
    Symbol* int_symbol = createSymbol("int", SYMBOL_TYPE_INT, SYMBOL_KIND_TYPE, 0, 1, 1);
    Symbol* main_symbol = createSymbol("main", SYMBOL_TYPE_FUNCTION, SYMBOL_KIND_FUNCTION, 0, 2, 1);
    Symbol* x_symbol = createSymbol("x", SYMBOL_TYPE_INT, SYMBOL_KIND_VARIABLE, 1, 3, 5);
    Symbol* y_symbol = createSymbol("x", SYMBOL_TYPE_FLOAT, SYMBOL_KIND_VARIABLE, 1, 4, 5); // Duplicate
    
    printf("Adding symbols:\n");
    
    if (addSymbol(table, int_symbol) == 0) {
        printf("  Added symbol: int (type)\n");
    }
    
    if (addSymbol(table, main_symbol) == 0) {
        printf("  Added symbol: main (function)\n");
    }
    
    if (addSymbol(table, x_symbol) == 0) {
        printf("  Added symbol: x (int variable)\n");
    }
    
    if (addSymbol(table, y_symbol) == 0) {
        printf("  Added symbol: x (float variable)\n");
    } else {
        printf("  Failed to add symbol: %s\n", table->error_message);
    }
    
    // Test symbol lookup
    printf("\nSymbol lookup:\n");
    Symbol* found = findSymbol(table, "x");
    if (found) {
        printf("  Found 'x': type %d, kind %d, scope %d\n", found->type, found->kind, found->scope_level);
    }
    
    found = findSymbol(table, "nonexistent");
    if (!found) {
        printf("  Symbol 'nonexistent' not found (expected)\n");
    }
    
    // Test scoping
    printf("\nTesting scoping:\n");
    enterScope(table);
    printf("  Entered scope 1\n");
    
    Symbol* scope_x = createSymbol("x", SYMBOL_TYPE_FLOAT, SYMBOL_KIND_VARIABLE, 1, 5, 5);
    if (addSymbol(table, scope_x) == 0) {
        printf("  Added 'x' in scope 1\n");
    }
    
    found = findSymbol(table, "x");
    if (found) {
        printf("  Found 'x': type %d, kind %d, scope %d\n", found->type, found->kind, found->scope_level);
    }
    
    exitScope(table);
    printf("  Exited scope 1\n");
    
    found = findSymbol(table, "x");
    if (found) {
        printf("  Found 'x': type %d, kind %d, scope %d\n", found->type, found->kind, found->scope_level);
    }
    
    printf("Total symbols: %d\n", table->symbol_count);
    
    free(table);
}

void demonstrateCodeGeneration() {
    printf("\n=== CODE GENERATION DEMO ===\n");
    
    SymbolTable* table = initSymbolTable();
    if (!table) {
        printf("Failed to initialize symbol table\n");
        return;
    }
    
    CodeGenerator* generator = initCodeGenerator(table);
    if (!generator) {
        printf("Failed to initialize code generator\n");
        free(table);
        return;
    }
    
    // Add a test symbol
    Symbol* test_symbol = createSymbol("test_var", SYMBOL_TYPE_INT, SYMBOL_KIND_VARIABLE, 0, 1, 1);
    addSymbol(table, test_symbol);
    
    // Generate some test instructions
    printf("Generating test instructions:\n");
    
    // Load constant
    Instruction* inst1 = createInstruction(INST_LOAD);
    inst1->operands[0].type = OPERAND_REGISTER;
    inst1->operands[0].data.register_number = 1;
    inst1->operands[1].type = OPERAND_IMMEDIATE;
    inst1->operands[1].data.immediate_value = 42;
    addInstruction(generator, inst1);
    printf("  LOAD R1, #42\n");
    
    // Load variable
    Instruction* inst2 = createInstruction(INST_LOAD);
    inst2->operands[0].type = OPERAND_REGISTER;
    inst2->operands[0].data.register_number = 2;
    inst2->operands[1].type = OPERAND_MEMORY;
    inst2->operands[1].data.memory_address = 0; // test_var
    addInstruction(generator, inst2);
    printf("  LOAD R2, [test_var]\n");
    
    // Add
    Instruction* inst3 = createInstruction(INST_ADD);
    inst3->operands[0].type = OPERAND_REGISTER;
    inst3->operands[0].data.register_number = 1;
    inst3->operands[1].type = OPERAND_REGISTER;
    inst3->operands[1].data.register_number = 2;
    inst3->operands[2].type = OPERAND_REGISTER;
    inst3->operands[2].data.register_number = 1;
    addInstruction(generator, inst3);
    printf("  ADD R1, R2, R1\n");
    
    // Store
    Instruction* inst4 = createInstruction(INST_STORE);
    inst4->operands[0].type = OPERAND_MEMORY;
    inst4->operands[0].data.memory_address = 0; // test_var
    inst4->operands[1].type = OPERAND_REGISTER;
    inst4->operands[1].data.register_number = 1;
    addInstruction(generator, inst4);
    printf("  STORE [test_var], R1\n");
    
    printf("\nGenerated %d instructions\n", generator->instruction_count);
    
    free(generator);
    free(table);
}

void demonstrateOptimization() {
    printf("\n=== OPTIMIZATION DEMO ===\n");
    
    SymbolTable* table = initSymbolTable();
    if (!table) {
        printf("Failed to initialize symbol table\n");
        return;
    }
    
    CodeGenerator* generator = initCodeGenerator(table);
    if (!generator) {
        printf("Failed to initialize code generator\n");
        free(table);
        return;
    }
    
    // Add test code that can be optimized
    // LOAD R1, #10
    Instruction* inst1 = createInstruction(INST_LOAD);
    inst1->operands[0].type = OPERAND_REGISTER;
    inst1->operands[0].data.register_number = 1;
    inst1->operands[1].type = OPERAND_IMMEDIATE;
    inst1->operands[1].data.immediate_value = 10;
    addInstruction(generator, inst1);
    
    // LOAD R2, #20
    Instruction* inst2 = createInstruction(INST_LOAD);
    inst2->operands[0].type = OPERAND_REGISTER;
    inst2->operands[0].data.register_number = 2;
    inst2->operands[1].type = OPERAND_IMMEDIATE;
    inst2->operands[1].data.immediate_value = 20;
    addInstruction(generator, inst2);
    
    // ADD R1, R2, R1
    Instruction* inst3 = createInstruction(INST_ADD);
    inst3->operands[0].type = OPERAND_REGISTER;
    inst3->operands[0].data.register_number = 1;
    inst3->operands[1].type = OPERAND_REGISTER;
    inst3->operands[1].data.register_number = 2;
    inst3->operands[2].type = OPERAND_REGISTER;
    inst3->operands[2].data.register_number = 1;
    addInstruction(generator, inst3);
    
    printf("Original code:\n");
    printf("  LOAD R1, #10\n");
    printf("  LOAD R2, #20\n");
    printf("  ADD R1, R2, R1\n");
    
    // Optimize
    Optimizer* optimizer = initOptimizer(generator);
    if (!optimizer) {
        printf("Failed to initialize optimizer\n");
        free(generator);
        free(table);
        return;
    }
    
    int optimized_count = runOptimizations(optimizer);
    
    printf("\nOptimized code:\n");
    for (int i = 0; i < generator->instruction_count; i++) {
        Instruction* inst = &generator->instructions[i];
        
        switch (inst->type) {
            case INST_LOAD:
                printf("  LOAD R%d, #%d\n", inst->operands[0].data.register_number, inst->operands[1].data.immediate_value);
                break;
            case INST_ADD:
                printf("  ADD R%d, R%d, R%d\n", inst->operands[0].data.register_number, 
                       inst->operands[1].data.register_number, inst->operands[2].data.register_number);
                break;
            default:
                printf("  Unknown instruction type %d\n", inst->type);
                break;
        }
    }
    
    printf("\nOptimizations performed: %d\n", optimized_count);
    
    free(optimizer);
    free(generator);
    free(table);
}

void demonstrateFullCompilation() {
    printf("\n=== FULL COMPILATION DEMO ===\n");
    
    const char* source_code = 
        "int factorial(int n) {\n"
        "    if (n <= 1) {\n"
        "        return 1;\n"
        "    }\n"
        "    return n * factorial(n - 1);\n"
        "}\n";
    
    printf("Source code:\n%s\n", source_code);
    
    Compiler* compiler = initCompiler();
    if (!compiler) {
        printf("Failed to initialize compiler\n");
        return;
    }
    
    if (compileSource(compiler, source_code) == 0) {
        printf("Compilation successful!\n");
        printf("Tokens: %d\n", compiler->lexer->token_count);
        printf("AST nodes: %d\n", countASTNodes(compiler->parser->ast));
        printf("Symbols: %d\n", compiler->symbol_table->symbol_count);
        printf("Instructions: %d\n", compiler->code_generator->instruction_count);
    } else {
        printf("Compilation failed: %s\n", compiler->error_message);
    }
    
    free(compiler);
}

// Print AST (recursive)
void printAST(ASTNode* node, int indent) {
    if (!node) return;
    
    // Print indentation
    for (int i = 0; i < indent; i++) {
        printf("  ");
    }
    
    // Print node type
    printf("AST Node Type: %d", node->type);
    
    // Print additional info based on type
    switch (node->type) {
        case AST_IDENTIFIER:
            printf(" (Name: %s)", node->data.identifier.name);
            break;
        case AST_CONSTANT:
            if (node->data.constant.value_type == 0) {
                printf(" (Value: %d)", node->data.constant.value.int_value);
            } else if (node->data.constant.value_type == 1) {
                printf(" (Value: %.2f)", node->data.constant.value.float_value);
            }
            break;
        case AST_BINARY_EXPRESSION:
            printf(" (Operator: %s)", node->data.binary_expression.operator);
            break;
        case AST_UNARY_EXPRESSION:
            printf(" (Operator: %s)", node->data.unary_expression.operator);
            break;
        case AST_FUNCTION_DEFINITION:
            printf(" (Function: %s)", node->data.function.name);
            break;
    }
    
    printf("\n");
    
    // Print children
    for (int i = 0; i < node->child_count; i++) {
        printAST(node->children[i], indent + 1);
    }
}

// Count AST nodes (recursive)
int countASTNodes(ASTNode* node) {
    if (!node) return 0;
    
    int count = 1;
    for (int i = 0; i < node->child_count; i++) {
        count += countASTNodes(node->children[i]);
    }
    
    return count;
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Compiler Design Examples\n");
    printf("==============================\n\n");
    
    // Run all demonstrations
    demonstrateLexicalAnalysis();
    demonstrateParsing();
    demonstrateSemanticAnalysis();
    demonstrateCodeGeneration();
    demonstrateOptimization();
    demonstrateFullCompilation();
    
    printf("\nAll advanced compiler design examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- Lexical analysis with tokenization\n");
    printf("- Syntax analysis with AST construction\n");
    printf("- Semantic analysis with symbol table\n");
    printf("- Intermediate code generation\n");
    printf("- Code optimization (constant folding, dead code elimination)\n");
    printf("- Complete compilation pipeline\n");
    printf("- Error handling and reporting\n");
    printf("- Multiple data types and expressions\n");
    printf("- Control flow statements\n");
    printf("- Function definitions and declarations\n");
    printf("- Scoping and symbol management\n");
    
    return 0;
}
