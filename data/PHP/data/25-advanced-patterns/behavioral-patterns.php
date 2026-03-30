<?php
/**
 * Advanced Behavioral Design Patterns
 * 
 * Implementation of complex behavioral patterns beyond basic observer and strategy.
 */

// Chain of Responsibility Pattern for Request Processing
interface RequestHandler
{
    public function setNext(RequestHandler $handler): RequestHandler;
    public function handle(Request $request): ?string;
    public function canHandle(Request $request): bool;
}

abstract class AbstractRequestHandler implements RequestHandler
{
    private ?RequestHandler $nextHandler = null;
    
    public function setNext(RequestHandler $handler): RequestHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }
    
    public function handle(Request $request): ?string
    {
        if ($this->canHandle($request)) {
            return $this->process($request);
        }
        
        return $this->nextHandler?->handle($request);
    }
    
    abstract protected function process(Request $request): string;
    abstract public function canHandle(Request $request): bool;
}

class Request
{
    private string $type;
    private array $data;
    private string $priority;
    private array $metadata;
    
    public function __construct(string $type, array $data = [], string $priority = 'normal', array $metadata = [])
    {
        $this->type = $type;
        $this->data = $data;
        $this->priority = $priority;
        $this->metadata = $metadata;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function getPriority(): string
    {
        return $this->priority;
    }
    
    public function getMetadata(string $key): ?string
    {
        return $this->metadata[$key] ?? null;
    }
    
    public function setMetadata(string $key, string $value): void
    {
        $this->metadata[$key] = $value;
    }
    
    public function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }
}

class AuthenticationHandler extends AbstractRequestHandler
{
    private array $validTokens = ['token123', 'token456', 'token789'];
    
    public function canHandle(Request $request): bool
    {
        return $request->getMetadata('auth_required') === 'true';
    }
    
    protected function process(Request $request): string
    {
        $token = $request->getMetadata('token');
        
        if (!$token) {
            throw new Exception('Authentication token required');
        }
        
        if (!in_array($token, $this->validTokens)) {
            throw new Exception('Invalid authentication token');
        }
        
        echo "✓ Authentication successful\n";
        return "Authentication passed";
    }
}

class ValidationHandler extends AbstractRequestHandler
{
    private array $validators = [
        'user' => ['name' => 'required', 'email' => 'required'],
        'order' => ['user_id' => 'required', 'amount' => 'required'],
        'payment' => ['order_id' => 'required', 'amount' => 'required']
    ];
    
    public function canHandle(Request $request): bool
    {
        return isset($this->validators[$request->getType()]);
    }
    
    protected function process(Request $request): string
    {
        $rules = $this->validators[$request->getType()];
        $data = $request->getData();
        
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        echo "✓ Validation successful\n";
        return "Validation passed";
    }
}

class LoggingHandler extends AbstractRequestHandler
{
    private array $logs = [];
    
    public function canHandle(Request $request): bool
    {
        return true; // Log all requests
    }
    
    protected function process(Request $request): string
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $request->getType(),
            'priority' => $request->getPriority(),
            'data' => $request->getData()
        ];
        
        $this->logs[] = $logEntry;
        
        echo "📝 Logged request: {$request->getType()}\n";
        return "Request logged";
    }
    
    public function getLogs(): array
    {
        return $this->logs;
    }
}

class BusinessLogicHandler extends AbstractRequestHandler
{
    private array $processors = [];
    
    public function __construct()
    {
        $this->processors = [
            'user' => [$this, 'processUser'],
            'order' => [$this, 'processOrder'],
            'payment' => [$this, 'processPayment']
        ];
    }
    
    public function canHandle(Request $request): bool
    {
        return isset($this->processors[$request->getType()]);
    }
    
    protected function process(Request $request): string
    {
        $processor = $this->processors[$request->getType()];
        return $processor($request);
    }
    
    private function processUser(Request $request): string
    {
        $data = $request->getData();
        echo "👤 Processing user: {$data['name']}\n";
        return "User processed successfully";
    }
    
    private function processOrder(Request $request): string
    {
        $data = $request->getData();
        echo "📦 Processing order #{$data['order_id']}\n";
        return "Order processed successfully";
    }
    
    private function processPayment(Request $request): string
    {
        $data = $request->getData();
        echo "💳 Processing payment: \${$data['amount']}\n";
        return "Payment processed successfully";
    }
}

// Command Pattern for Undo/Redo Functionality
interface Command
{
    public function execute(): void;
    public function undo(): void;
    public function getDescription(): string;
}

class TextEditor
{
    private string $content = '';
    private array $history = [];
    private int $historyIndex = -1;
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    
    public function append(string $text): void
    {
        $this->content .= $text;
    }
    
    public function saveState(): void
    {
        $this->history = array_slice($this->history, 0, $this->historyIndex + 1);
        $this->history[] = $this->content;
        $this->historyIndex++;
    }
    
    public function undo(): bool
    {
        if ($this->historyIndex > 0) {
            $this->historyIndex--;
            $this->content = $this->history[$this->historyIndex];
            return true;
        }
        return false;
    }
    
    public function redo(): bool
    {
        if ($this->historyIndex < count($this->history) - 1) {
            $this->historyIndex++;
            $this->content = $this->history[$this->historyIndex];
            return true;
        }
        return false;
    }
    
    public function getHistoryCount(): int
    {
        return count($this->history);
    }
    
    public function getHistoryIndex(): int
    {
        return $this->historyIndex;
    }
}

class InsertTextCommand implements Command
{
    private TextEditor $editor;
    private string $text;
    private int $position;
    private string $previousContent;
    
    public function __construct(TextEditor $editor, string $text, int $position)
    {
        $this->editor = $editor;
        $this->text = $text;
        $this->position = $position;
    }
    
    public function execute(): void
    {
        $this->previousContent = $this->editor->getContent();
        
        $content = $this->editor->getContent();
        $before = substr($content, 0, $this->position);
        $after = substr($content, $this->position);
        
        $this->editor->setContent($before . $this->text . $after);
        $this->editor->saveState();
        
        echo "Inserted text: '{$this->text}' at position {$this->position}\n";
    }
    
    public function undo(): void
    {
        $this->editor->setContent($this->previousContent);
        $this->editor->saveState();
        
        echo "Undid text insertion\n";
    }
    
    public function getDescription(): string
    {
        return "Insert '{$this->text}' at position {$this->position}";
    }
}

class DeleteTextCommand implements Command
{
    private TextEditor $editor;
    private int $start;
    private int $length;
    private string $deletedText;
    
    public function __construct(TextEditor $editor, int $start, int $length)
    {
        $this->editor = $editor;
        $this->start = $start;
        $this->length = $length;
    }
    
    public function execute(): void
    {
        $content = $this->editor->getContent();
        $this->deletedText = substr($content, $this->start, $this->length);
        
        $before = substr($content, 0, $this->start);
        $after = substr($content, $this->start + $this->length);
        
        $this->editor->setContent($before . $after);
        $this->editor->saveState();
        
        echo "Deleted {$this->length} characters from position {$this->start}\n";
    }
    
    public function undo(): void
    {
        $content = $this->editor->getContent();
        $before = substr($content, 0, $this->start);
        $after = substr($content, $this->start);
        
        $this->editor->setContent($before . $this->deletedText . $after);
        $this->editor->saveState();
        
        echo "Undid text deletion\n";
    }
    
    public function getDescription(): string
    {
        return "Delete {$this->length} characters from position {$this->start}";
    }
}

class ReplaceTextCommand implements Command
{
    private TextEditor $editor;
    private string $search;
    private string $replace;
    private array $replacements = [];
    
    public function __construct(TextEditor $editor, string $search, string $replace)
    {
        $this->editor = $editor;
        $this->search = $search;
        $this->replace = $replace;
    }
    
    public function execute(): void
    {
        $content = $this->editor->getContent();
        $this->replacements = [];
        
        $offset = 0;
        while (($pos = strpos($content, $this->search, $offset)) !== false) {
            $this->replacements[$pos] = substr($content, $pos, strlen($this->search));
            $offset = $pos + strlen($this->search);
        }
        
        $content = str_replace($this->search, $this->replace, $content);
        $this->editor->setContent($content);
        $this->editor->saveState();
        
        echo "Replaced '{$this->search}' with '{$this->replace}' (" . count($this->replacements) . " occurrences)\n";
    }
    
    public function undo(): void
    {
        $content = $this->editor->getContent();
        
        // Reverse replacements
        foreach (array_reverse($this->replacements, true) as $position => $originalText) {
            $before = substr($content, 0, $position);
            $after = substr($content, $position + strlen($this->replace));
            $content = $before . $originalText . $after;
        }
        
        $this->editor->setContent($content);
        $this->editor->saveState();
        
        echo "Undid text replacement\n";
    }
    
    public function getDescription(): string
    {
        return "Replace '{$this->search}' with '{$this->replace}'";
    }
}

class CommandManager
{
    private array $commandHistory = [];
    private int $currentCommand = -1;
    
    public function executeCommand(Command $command): void
    {
        // Clear any commands after current position
        $this->commandHistory = array_slice($this->commandHistory, 0, $this->currentCommand + 1);
        
        $command->execute();
        $this->commandHistory[] = $command;
        $this->currentCommand++;
    }
    
    public function undo(): bool
    {
        if ($this->currentCommand >= 0) {
            $command = $this->commandHistory[$this->currentCommand];
            $command->undo();
            $this->currentCommand--;
            return true;
        }
        return false;
    }
    
    public function redo(): bool
    {
        if ($this->currentCommand < count($this->commandHistory) - 1) {
            $this->currentCommand++;
            $command = $this->commandHistory[$this->currentCommand];
            $command->execute();
            return true;
        }
        return false;
    }
    
    public function getCommandHistory(): array
    {
        return array_map(fn($cmd) => $cmd->getDescription(), $this->commandHistory);
    }
    
    public function getCurrentCommandIndex(): int
    {
        return $this->currentCommand;
    }
}

// Mediator Pattern for Chat System
interface ChatMediator
{
    public function sendMessage(User $from, User $to, string $message): void;
    public function registerUser(User $user): void;
    public function broadcast(User $from, string $message): void;
    public function createRoom(string $name): ChatRoom;
}

class ChatRoom
{
    private string $name;
    private array $users = [];
    private ChatMediator $mediator;
    
    public function __construct(string $name, ChatMediator $mediator)
    {
        $this->name = $name;
        $this->mediator = $mediator;
    }
    
    public function addUser(User $user): void
    {
        $this->users[$user->getName()] = $user;
        $user->joinRoom($this);
    }
    
    public function removeUser(User $user): void
    {
        unset($this->users[$user->getName()]);
        $user->leaveRoom($this);
    }
    
    public function sendMessage(User $from, string $message): void
    {
        echo "[{$this->name}] {$from->getName()}: $message\n";
        
        foreach ($this->users as $user) {
            if ($user !== $from) {
                $user->receiveMessage($from, $message, $this);
            }
        }
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getUsers(): array
    {
        return $this->users;
    }
}

class User
{
    private string $name;
    private ChatMediator $mediator;
    private array $rooms = [];
    private array $messages = [];
    
    public function __construct(string $name, ChatMediator $mediator)
    {
        $this->name = $name;
        $this->mediator = $mediator;
        $this->mediator->registerUser($this);
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function sendTo(User $to, string $message): void
    {
        $this->mediator->sendMessage($this, $to, $message);
    }
    
    public function broadcast(string $message): void
    {
        $this->mediator->broadcast($this, $message);
    }
    
    public function receiveMessage(User $from, string $message, ?ChatRoom $room = null): void
    {
        $timestamp = date('H:i:s');
        $source = $room ? "[{$room->getName()}]" : "[Direct]";
        $log = "[$timestamp] $source {$from->getName()}: $message";
        
        $this->messages[] = $log;
        echo "{$this->name} received: $log\n";
    }
    
    public function joinRoom(ChatRoom $room): void
    {
        $this->rooms[$room->getName()] = $room;
        echo "{$this->name} joined room: {$room->getName()}\n";
    }
    
    public function leaveRoom(ChatRoom $room): void
    {
        unset($this->rooms[$room->getName()]);
        echo "{$this->name} left room: {$room->getName()}\n";
    }
    
    public function sendMessageInRoom(ChatRoom $room, string $message): void
    {
        $room->sendMessage($this, $message);
    }
    
    public function getMessages(): array
    {
        return $this->messages;
    }
    
    public function getRooms(): array
    {
        return $this->rooms;
    }
}

class ChatSystem implements ChatMediator
{
    private array $users = [];
    private array $rooms = [];
    
    public function sendMessage(User $from, User $to, string $message): void
    {
        $to->receiveMessage($from, $message);
    }
    
    public function registerUser(User $user): void
    {
        $this->users[$user->getName()] = $user;
        echo "{$user->getName()} registered in chat system\n";
    }
    
    public function broadcast(User $from, string $message): void
    {
        echo "[Broadcast] {$from->getName()}: $message\n";
        
        foreach ($this->users as $user) {
            if ($user !== $from) {
                $user->receiveMessage($from, $message);
            }
        }
    }
    
    public function createRoom(string $name): ChatRoom
    {
        $room = new ChatRoom($name, $this);
        $this->rooms[$name] = $room;
        echo "Chat room created: $name\n";
        return $room;
    }
    
    public function getRoom(string $name): ?ChatRoom
    {
        return $this->rooms[$name] ?? null;
    }
    
    public function getUsers(): array
    {
        return $this->users;
    }
    
    public function getRooms(): array
    {
        return $this->rooms;
    }
}

// Memento Pattern for State Management
class EditorMemento
{
    private string $content;
    private int $cursorPosition;
    private array $selection;
    private string $timestamp;
    
    public function __construct(string $content, int $cursorPosition, array $selection = [])
    {
        $this->content = $content;
        $this->cursorPosition = $cursorPosition;
        $this->selection = $selection;
        $this->timestamp = date('Y-m-d H:i:s');
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function getCursorPosition(): int
    {
        return $this->cursorPosition;
    }
    
    public function getSelection(): array
    {
        return $this->selection;
    }
    
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }
}

class TextEditorOriginator
{
    private string $content = '';
    private int $cursorPosition = 0;
    private array $selection = [];
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    
    public function getCursorPosition(): int
    {
        return $this->cursorPosition;
    }
    
    public function setCursorPosition(int $position): void
    {
        $this->cursorPosition = $position;
    }
    
    public function getSelection(): array
    {
        return $this->selection;
    }
    
    public function setSelection(int $start, int $end): void
    {
        $this->selection = ['start' => $start, 'end' => $end];
    }
    
    public function save(): EditorMemento
    {
        return new EditorMemento($this->content, $this->cursorPosition, $this->selection);
    }
    
    public function restore(EditorMemento $memento): void
    {
        $this->content = $memento->getContent();
        $this->cursorPosition = $memento->getCursorPosition();
        $this->selection = $memento->getSelection();
        
        echo "Restored state from {$memento->getTimestamp()}\n";
    }
    
    public function insertText(string $text, int $position = null): void
    {
        $pos = $position ?? $this->cursorPosition;
        $this->content = substr($this->content, 0, $pos) . $text . substr($this->content, $pos);
        $this->cursorPosition = $pos + strlen($text);
    }
    
    public function deleteText(int $start, int $length): void
    {
        $this->content = substr($this->content, 0, $start) . substr($this->content, $start + $length);
        $this->cursorPosition = min($this->cursorPosition, $start);
    }
    
    public function selectText(int $start, int $end): void
    {
        $this->selection = ['start' => $start, 'end' => $end];
    }
    
    public function getSelectedText(): string
    {
        if (empty($this->selection)) {
            return '';
        }
        
        return substr($this->content, $this->selection['start'], 
                      $this->selection['end'] - $this->selection['start']);
    }
}

class Caretaker
{
    private array $mementos = [];
    private int $currentIndex = -1;
    
    public function save(EditorMemento $memento): void
    {
        // Clear any mementos after current position
        $this->mementos = array_slice($this->mementos, 0, $this->currentIndex + 1);
        
        $this->mementos[] = $memento;
        $this->currentIndex++;
        
        echo "Saved state at {$memento->getTimestamp()}\n";
    }
    
    public function undo(EditorOriginator $originator): bool
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $memento = $this->mementos[$this->currentIndex];
            $originator->restore($memento);
            return true;
        }
        return false;
    }
    
    public function redo(EditorOriginator $originator): bool
    {
        if ($this->currentIndex < count($this->mementos) - 1) {
            $this->currentIndex++;
            $memento = $this->mementos[$this->currentIndex];
            $originator->restore($memento);
            return true;
        }
        return false;
    }
    
    public function getHistory(): array
    {
        return array_map(fn($m) => $m->getTimestamp(), $this->mementos);
    }
    
    public function getCurrentIndex(): int
    {
        return $this->currentIndex;
    }
}

// Behavioral Patterns Examples
class BehavioralPatternsExamples
{
    public function demonstrateChainOfResponsibility(): void
    {
        echo "Chain of Responsibility Pattern Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        // Create handler chain
        $authHandler = new AuthenticationHandler();
        $validationHandler = new ValidationHandler();
        $loggingHandler = new LoggingHandler();
        $businessHandler = new BusinessLogicHandler();
        
        // Set up chain
        $authHandler->setNext($validationHandler)
                  ->setNext($loggingHandler)
                  ->setNext($businessHandler);
        
        // Process requests
        echo "Processing requests:\n";
        
        try {
            echo "\n1. Authenticated user request:\n";
            $request1 = new Request('user', ['name' => 'John', 'email' => 'john@example.com'], 'high');
            $request1->setMetadata('auth_required', 'true');
            $request1->setMetadata('token', 'token123');
            
            $result1 = $authHandler->handle($request1);
            echo "Result: $result1\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        
        try {
            echo "\n2. Invalid request:\n";
            $request2 = new Request('user', ['name' => ''], 'normal');
            $result2 = $authHandler->handle($request2);
            echo "Result: $result2\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        
        try {
            echo "\n3. Order request:\n";
            $request3 = new Request('order', ['user_id' => 1, 'amount' => 100], 'medium');
            $result3 = $authHandler->handle($request3);
            echo "Result: $result3\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        
        // Get logs from logging handler
        echo "\nRequest Logs:\n";
        $logs = $loggingHandler->getLogs();
        foreach (array_slice($logs, -3) as $log) {
            echo "  {$log['timestamp']} - {$log['type']} ({$log['priority']})\n";
        }
    }
    
    public function demonstrateCommand(): void
    {
        echo "\nCommand Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $editor = new TextEditor();
        $commandManager = new CommandManager();
        
        // Execute commands
        echo "Executing commands:\n";
        
        $editor->setContent('Hello World');
        $editor->saveState();
        
        $commandManager->executeCommand(new InsertTextCommand($editor, ' Beautiful', 5));
        $commandManager->executeCommand(new InsertTextCommand($editor, ' PHP', 15));
        $commandManager->executeCommand(new ReplaceTextCommand($editor, 'World', 'Universe'));
        
        echo "\nCurrent content: " . $editor->getContent() . "\n";
        
        // Undo operations
        echo "\nUndo operations:\n";
        $commandManager->undo();
        echo "After undo: " . $editor->getContent() . "\n";
        
        $commandManager->undo();
        echo "After undo: " . $editor->getContent() . "\n";
        
        // Redo operations
        echo "\nRedo operations:\n";
        $commandManager->redo();
        echo "After redo: " . $editor->getContent() . "\n";
        
        // More commands
        echo "\nMore commands:\n";
        $commandManager->executeCommand(new DeleteTextCommand($editor, 6, 10));
        echo "After delete: " . $editor->getContent() . "\n";
        
        // Show command history
        echo "\nCommand History:\n";
        $history = $commandManager->getCommandHistory();
        foreach ($history as $index => $description) {
            $current = $index === $commandManager->getCurrentCommandIndex() ? ' (current)' : '';
            echo "  $index: $description $current\n";
        }
    }
    
    public function demonstrateMediator(): void
    {
        echo "\nMediator Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $chatSystem = new ChatSystem();
        
        // Create users
        $alice = new User('Alice', $chatSystem);
        $bob = new User('Bob', $chatSystem);
        $charlie = new User('Charlie', $chatSystem);
        
        // Create chat rooms
        $generalRoom = $chatSystem->createRoom('General');
        $devRoom = $chatSystem->createRoom('Development');
        
        // Join rooms
        $generalRoom->addUser($alice);
        $generalRoom->addUser($bob);
        $devRoom->addUser($bob);
        $devRoom->addUser($charlie);
        
        // Send messages
        echo "\nSending messages:\n";
        
        $alice->sendTo($bob, 'Hi Bob! How are you?');
        $bob->sendTo($alice, "I'm good, Alice! Thanks for asking.");
        
        $alice->broadcast('Hello everyone!');
        
        $charlie->sendMessageInRoom($devRoom, 'Anyone working on the new feature?');
        $bob->sendMessageInRoom($devRoom, 'Yes, I\'m debugging the API endpoint.');
        
        // Show user messages
        echo "\nAlice's messages:\n";
        foreach ($alice->getMessages() as $message) {
            echo "  $message\n";
        }
        
        echo "\nBob's messages:\n";
        foreach ($bob->getMessages() as $message) {
            echo "  $message\n";
        }
        
        // Show room info
        echo "\nRoom Information:\n";
        echo "General Room Users: " . implode(', ', array_keys($generalRoom->getUsers())) . "\n";
        echo "Development Room Users: " . implode(', ', array_keys($devRoom->getUsers())) . "\n";
        
        // Leave room
        echo "\nCharlie leaving Development room:\n";
        $devRoom->removeUser($charlie);
        echo "Development Room Users: " . implode(', ', array_keys($devRoom->getUsers())) . "\n";
    }
    
    public function demonstrateMemento(): void
    {
        echo "\nMemento Pattern Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $editor = new TextEditorOriginator();
        $caretaker = new Caretaker();
        
        // Initial state
        echo "Initial state:\n";
        $editor->setContent('Hello World');
        $editor->setCursorPosition(11);
        echo "Content: " . $editor->getContent() . "\n";
        echo "Cursor: " . $editor->getCursorPosition() . "\n";
        
        // Save state
        echo "\nSaving state:\n";
        $caretaker->save($editor->save());
        
        // Make changes
        echo "\nMaking changes:\n";
        $editor->insertText(' Beautiful', 5);
        $editor->insertText(' PHP', 15);
        echo "Content: " . $editor->getContent() . "\n";
        echo "Cursor: " . $editor->getCursorPosition() . "\n";
        
        // Save another state
        echo "\nSaving state:\n";
        $caretaker->save($editor->save());
        
        // More changes
        echo "\nMaking more changes:\n";
        $editor->selectText(6, 15);
        echo "Selected: '" . $editor->getSelectedText() . "'\n";
        
        $editor->deleteText(6, 9);
        echo "After deletion: " . $editor->getContent() . "\n";
        
        // Save another state
        echo "\nSaving state:\n";
        $caretaker->save($editor->save());
        
        // Undo operations
        echo "\nUndo operations:\n";
        $caretaker->undo($editor);
        echo "After undo: " . $editor->getContent() . "\n";
        
        $caretaker->undo($editor);
        echo "After undo: " . $editor->getContent() . "\n";
        
        // Redo operations
        echo "\nRedo operations:\n";
        $caretaker->redo($editor);
        echo "After redo: " . $editor->getContent() . "\n";
        
        // Show history
        echo "\nState History:\n";
        $history = $caretaker->getHistory();
        foreach ($history as $index => $timestamp) {
            $current = $index === $caretaker->getCurrentIndex() ? ' (current)' : '';
            echo "  $index: $timestamp $current\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nBehavioral Patterns Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Chain of Responsibility:\n";
        echo "   • Keep handlers focused on single responsibilities\n";
        echo "   • Use consistent interface for all handlers\n";
        echo "   • Implement proper error handling and propagation\n";
        echo "   • Consider performance for long chains\n";
        echo "   • Use for request processing, validation, etc.\n\n";
        
        echo "2. Command Pattern:\n";
        echo "   • Encapsulate all operation details in commands\n";
        echo "   • Implement proper undo/redo functionality\n";
        echo "   • Use command managers for complex operations\n";
        echo "   • Consider command queuing and batching\n";
        echo "   • Use for GUI operations, macros, transactions\n\n";
        
        echo "3. Mediator Pattern:\n";
        echo "   • Keep mediator focused on coordination only\n";
        echo "   • Avoid tight coupling between colleagues\n";
        echo "   • Use for complex communication patterns\n";
        echo "   • Consider performance for many colleagues\n";
        echo "   • Use for chat systems, GUI frameworks, etc.\n\n";
        
        echo "4. Memento Pattern:\n";
        echo "   • Keep mementos immutable\n";
        echo "   • Don't expose internal state of originator\n";
        echo "   • Use caretaker for memento management\n";
        echo "   • Consider memory usage for large states\n";
        echo "   • Use for undo/redo, game states, etc.\n\n";
        
        echo "5. General Guidelines:\n";
        echo "   • Choose patterns based on problem context\n";
        echo "   • Keep patterns simple and focused\n";
        echo "   • Document pattern usage and intent\n";
        echo "   • Test pattern implementations thoroughly\n";
        echo "   • Avoid anti-patterns and over-engineering";
    }
    
    public function runAllExamples(): void
    {
        echo "Advanced Behavioral Design Patterns Examples\n";
        echo str_repeat("=", 45) . "\n";
        
        $this->demonstrateChainOfResponsibility();
        $this->demonstrateCommand();
        $this->demonstrateMediator();
        $this->demonstrateMemento();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runBehavioralPatternsDemo(): void
{
    $examples = new BehavioralPatternsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runBehavioralPatternsDemo();
}
?>
