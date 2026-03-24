# Advanced Compiler Design

This file contains comprehensive advanced compiler design examples in C, including lexical analysis, syntax analysis, semantic analysis, intermediate code generation, optimization, and a complete compilation pipeline.

## 📚 Advanced Compiler Design Fundamentals

### 🔧 Compiler Concepts
- **Lexical Analysis**: Tokenization and lexical analysis
- **Syntax Analysis**: Parsing and Abstract Syntax Tree (AST) construction
- **Semantic Analysis**: Type checking and symbol table management
- **Code Generation**: Intermediate code generation and optimization
- **Target Code Generation**: Machine code generation for specific architectures

### 🎯 Compiler Architecture
- **Multi-pass Design**: Separate passes for different compilation phases
- **Error Handling**: Comprehensive error detection and reporting
- **Optimization**: Various optimization techniques for performance
- **Extensibility**: Modular design for easy extension and modification

## 🔤 Lexical Analysis (Lexer)

### Token Types
```c
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
```

### Token Structure
```c
// Token structure
typedef struct {
    TokenType type;
    KeywordType keyword;
    char value[MAX_STRING_SIZE];
    int line;
    int column;
    int length;
} Token;
```

### Lexer Implementation
```c
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
    // ... more keywords
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
```

**Lexer Benefits**:
- **Token Recognition**: Accurate identification of all language tokens
- **Error Reporting**: Detailed error messages with line and column information
- **Efficiency**: Single-pass tokenization for performance
- **Extensibility**: Easy to add new token types and keywords

## 🌳 Syntax Analysis (Parser)

### AST Node Types
```c
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
    AST_SELECTION_STATEMENT = 32,
    AST_ITERATION_STATEMENT = 33,
    AST_JUMP_STATEMENT = 34,
    AST_TRANSLATION_UNIT = 35
} ASTNodeType;
```

### AST Node Structure
```c
// AST node structure
typedef struct ASTNode {
    ASTNodeType type;
    union {
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
```

### Parser Implementation
```c
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

// Parse binary expression
ASTNode* parseBinaryExpression(Parser* parser, int precedence) {
    ASTNode* left = parseUnaryExpression(parser);
    
    while (isBinaryOperator(parser->current_token->value) &&
           getOperatorPrecedence(parser->current_token->value) >= precedence) {
        
        char* operator = strdup(parser->current_token->value);
        consumeToken(parser);
        
        ASTNode* right = parseBinaryExpression(parser, getOperatorPrecedence(operator) + 1);
        
        ASTNode* binary_node = createASTNode(getBinaryExpressionType(operator), left->line, left->column);
        binary_node->data.binary_expression.operator = operator;
        binary_node->children[0] = left;
        binary_node->children[1] = right;
        binary_node->child_count = 2;
        
        left = binary_node;
    }
    
    return left;
}

// Parse expression
ASTNode* parseExpression(Parser* parser) {
    return parseBinaryExpression(parser, 0);
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
             parser->current_token->keyword == KEYWORD_VOID)) {
            
            // Check if this is a function definition
            Token* lookahead = parser->lookahead_token;
            if (lookahead && lookahead->type == TOKEN_IDENTIFIER) {
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
```

**Parser Benefits**:
- **Recursive Descent**: Clean and maintainable parsing logic
- **AST Construction**: Complete abstract syntax tree generation
- **Error Recovery**: Robust error handling and recovery
- **Extensibility**: Easy to add new language constructs

## 📊 Semantic Analysis

### Symbol Types
```c
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
```

### Symbol Structure
```c
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
```

### Symbol Table Implementation
```c
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
```

**Semantic Analysis Benefits**:
- **Type Checking**: Comprehensive type validation
- **Scope Management**: Proper handling of variable scopes
- **Symbol Resolution**: Accurate symbol lookup and validation
- **Error Detection**: Early detection of semantic errors

## 🔧 Intermediate Code Generation

### Instruction Types
```c
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
    INST_FREE = 21
} InstructionType;
```

### Instruction Structure
```c
// Instruction structure
typedef struct {
    InstructionType type;
    Operand operands[3];
    int operand_count;
    char* label;
    int line;
} Instruction;
```

### Code Generator Implementation
```c
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
```

**Code Generation Benefits**:
- **Three Address Code**: Clean intermediate representation
- **Register Allocation**: Efficient register usage
- **Instruction Selection**: Appropriate instruction generation
- **Optimization Ready**: Code suitable for optimization passes

## ⚡ Optimization

### Optimization Types
```c
// Optimization types
typedef enum {
    OPT_CONSTANT_FOLDING = 0,
    OPT_DEAD_CODE_ELIMINATION = 1,
    OPT_COMMON_SUBEXPRESSION_ELIMINATION = 2,
    OPT_STRENGTH_REDUCTION = 3,
    OPT_LOOP_OPTIMIZATION = 4,
    OPT_INLINING = 5
} OptimizationType;
```

### Optimizer Implementation
```c
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
                    int target = instruction->operands[0].data.immediate_value;
                    if (target >= 0 && target < generator->instruction_count) {
                        reachable[target] = 1;
                    }
                }
            }
            
            // Mark next instruction as reachable
            if (i + 1 < generator->instruction_count && instruction->type != INST_RET) {
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
```

**Optimization Benefits**:
- **Performance**: Improved code execution speed
- **Size Reduction**: Smaller executable size
- **Efficiency**: Better resource utilization
- **Quality**: Higher quality generated code

## 🔧 Best Practices

### 1. Error Handling
```c
// Good: Comprehensive error handling
int parseExpression(Parser* parser) {
    if (!parser) {
        parser->has_error = 1;
        strcpy(parser->error_message, "Parser is null");
        return -1;
    }
    
    ASTNode* node = parsePrimaryExpression(parser);
    if (!node) {
        return -1; // Error already set by parsePrimaryExpression
    }
    
    return 0;
}

// Bad: No error handling
int parseExpression(Parser* parser) {
    ASTNode* node = parsePrimaryExpression(parser);
    // No null check
    return 0;
}
```

### 2. Memory Management
```c
// Good: Proper memory management
ASTNode* createASTNode(ASTNodeType type, int line, int column) {
    ASTNode* node = malloc(sizeof(ASTNode));
    if (!node) return NULL;
    
    memset(node, 0, sizeof(ASTNode));
    node->type = type;
    node->line = line;
    node->column = column;
    
    return node;
}

void freeASTNode(ASTNode* node) {
    if (!node) return;
    
    // Free children recursively
    for (int i = 0; i < node->child_count; i++) {
        freeASTNode(node->children[i]);
    }
    
    // Free node-specific data
    switch (node->type) {
        case AST_IDENTIFIER:
            free(node->data.identifier.name);
            break;
        case AST_STRING_LITERAL:
            free(node->data.constant.value.string_value);
            break;
        case AST_BINARY_EXPRESSION:
        case AST_UNARY_EXPRESSION:
            free(node->data.binary_expression.operator);
            break;
    }
    
    free(node);
}

// Bad: Memory leaks
ASTNode* createASTNode(ASTNodeType type, int line, int column) {
    ASTNode* node = malloc(sizeof(ASTNode));
    // No error checking
    return node;
}
```

### 3. Modular Design
```c
// Good: Modular design with clear interfaces
typedef struct {
    int (*tokenize)(Lexer* lexer);
    Token (*get_next_token)(Lexer* lexer);
    void (*reset)(Lexer* lexer);
} LexerInterface;

typedef struct {
    int (*parse)(Parser* parser);
    ASTNode* (*parse_expression)(Parser* parser);
    ASTNode* (*parse_statement)(Parser* parser);
} ParserInterface;

// Bad: Monolithic design
void compileEverything(const char* source) {
    // Tokenization, parsing, semantic analysis, code generation all in one function
    // Hard to maintain and test
}
```

### 4. Extensibility
```c
// Good: Extensible design
typedef struct {
    TokenType type;
    const char* pattern;
    int (*parse_function)(Lexer* lexer, Token* token);
} TokenPattern;

// Add new token types easily
TokenPattern token_patterns[] = {
    {TOKEN_IDENTIFIER, "[a-zA-Z_][a-zA-Z0-9_]*", parse_identifier},
    {TOKEN_NUMBER, "[0-9]+(\\.[0-9]+)?", parse_number},
    {TOKEN_STRING, "\"([^\"\\\\]|\\\\.)*\"", parse_string},
    // Add new patterns here
    {TOKEN_EOF, NULL, NULL}
};

// Bad: Hard to extend
Token getNextToken(Lexer* lexer) {
    // Hardcoded logic for each token type
    // Adding new token types requires modifying this function
}
```

### 5. Performance
```c
// Good: Efficient data structures
typedef struct {
    Token* tokens;
    int capacity;
    int count;
} TokenArray;

int addToken(TokenArray* array, Token* token) {
    if (array->count >= array->capacity) {
        // Double capacity
        array->capacity *= 2;
        array->tokens = realloc(array->tokens, array->capacity * sizeof(Token));
    }
    
    array->tokens[array->count++] = *token;
    return array->count - 1;
}

// Bad: Inefficient operations
Token* tokens[MAX_TOKENS]; // Fixed size, may waste memory or be too small
int token_count = 0;

void addToken(Token* token) {
    tokens[token_count++] = *token; // No bounds checking
}
```

## ⚠️ Common Pitfalls

### 1. Left Recursion
```c
// Wrong: Left recursion causes infinite recursion
ASTNode* parseExpression(Parser* parser) {
    ASTNode* node = parseExpression(parser); // Left recursion!
    // Parse rest of expression
    return node;
}

// Right: Eliminate left recursion
ASTNode* parseExpression(Parser* parser) {
    ASTNode* node = parseTerm(parser);
    while (isAdditiveOperator(parser->current_token->value)) {
        // Parse additive operators
    }
    return node;
}
```

### 2. Memory Leaks
```c
// Wrong: Memory leaks in AST construction
ASTNode* parseBinaryExpression(Parser* parser) {
    ASTNode* left = parsePrimaryExpression(parser);
    ASTNode* right = parsePrimaryExpression(parser);
    
    ASTNode* node = createASTNode(AST_BINARY_EXPRESSION, left->line, left->column);
    node->children[0] = left;
    node->children[1] = right;
    
    // If parsing fails, left and right are leaked
    return node;
}

// Right: Proper cleanup on error
ASTNode* parseBinaryExpression(Parser* parser) {
    ASTNode* left = parsePrimaryExpression(parser);
    if (!left) return NULL;
    
    ASTNode* right = parsePrimaryExpression(parser);
    if (!right) {
        freeASTNode(left);
        return NULL;
    }
    
    ASTNode* node = createASTNode(AST_BINARY_EXPRESSION, left->line, left->column);
    if (!node) {
        freeASTNode(left);
        freeASTNode(right);
        return NULL;
    }
    
    node->children[0] = left;
    node->children[1] = right;
    return node;
}
```

### 3. Operator Precedence
```c
// Wrong: Incorrect operator precedence
ASTNode* parseExpression(Parser* parser) {
    ASTNode* node = parsePrimaryExpression(parser);
    
    while (isOperator(parser->current_token->value)) {
        // All operators have same precedence - wrong!
        char* operator = strdup(parser->current_token->value);
        consumeToken(parser);
        ASTNode* right = parsePrimaryExpression(parser);
        
        node = createBinaryNode(operator, node, right);
    }
    
    return node;
}

// Right: Correct operator precedence
ASTNode* parseExpression(Parser* parser) {
    return parseBinaryExpression(parser, 0); // Start with lowest precedence
}

ASTNode* parseBinaryExpression(Parser* parser, int precedence) {
    ASTNode* left = parseUnaryExpression(parser);
    
    while (isBinaryOperator(parser->current_token->value) &&
           getOperatorPrecedence(parser->current_token->value) >= precedence) {
        
        char* operator = strdup(parser->current_token->value);
        consumeToken(parser);
        
        int next_precedence = getOperatorPrecedence(operator) + 1;
        ASTNode* right = parseBinaryExpression(parser, next_precedence);
        
        left = createBinaryNode(operator, left, right);
    }
    
    return left;
}
```

### 4. Symbol Table Scoping
```c
// Wrong: Incorrect scope handling
void exitScope(SymbolTable* table) {
    table->current_scope--;
    // Symbols from previous scope are still accessible - wrong!
}

// Right: Proper scope cleanup
void exitScope(SymbolTable* table) {
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
```

## 🔧 Real-World Applications

### 1. C Compiler
```c
// Compile C source code
int compileCFile(const char* filename) {
    char* source = readFile(filename);
    if (!source) return -1;
    
    Compiler* compiler = initCompiler();
    if (!compiler) {
        free(source);
        return -1;
    }
    
    int result = compileSource(compiler, source);
    
    if (result == 0) {
        // Generate object file
        generateObjectFile(compiler->target_code, filename);
    }
    
    free(compiler);
    free(source);
    return result;
}
```

### 2. Script Language Interpreter
```c
// Execute script
int executeScript(const char* script) {
    Lexer* lexer = initLexer(script);
    tokenizeSource(lexer);
    
    Parser* parser = initParser(lexer);
    parseSource(parser);
    
    // Interpret AST directly
    return interpretAST(parser->ast);
}
```

### 3. Code Formatter
```c
// Format source code
int formatSourceCode(const char* source, char* formatted, int max_size) {
    Lexer* lexer = initLexer(source);
    tokenizeSource(lexer);
    
    Parser* parser = initParser(lexer);
    parseSource(parser);
    
    // Generate formatted code from AST
    return generateFormattedCode(parser->ast, formatted, max_size);
}
```

### 4. Static Analysis Tool
```c
// Analyze code for potential issues
int analyzeCode(const char* source) {
    Lexer* lexer = initLexer(source);
    tokenizeSource(lexer);
    
    Parser* parser = initParser(lexer);
    parseSource(parser);
    
    // Perform static analysis
    return performStaticAnalysis(parser->ast);
}
```

## 📚 Further Reading

### Books
- "Compilers: Principles, Techniques, and Tools" by Aho, Lam, Sethi, Ullman
- "Modern Compiler Implementation in C" by Andrew Appel
- "Programming Language Pragmatics" by Michael L. Scott

### Topics
- Advanced optimization techniques
- Just-in-time compilation
- Garbage collection
- Type systems and type inference
- Compiler construction tools (Lex, Yacc, ANTLR)
- Intermediate representations (SSA, three-address code)

Advanced compiler design in C provides the foundation for building sophisticated language processing tools. Master these techniques to create high-performance compilers, interpreters, and language analysis tools that can handle complex programming languages efficiently!
