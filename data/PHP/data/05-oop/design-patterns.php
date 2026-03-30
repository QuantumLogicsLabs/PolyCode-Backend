<?php
/**
 * PHP Design Patterns
 * 
 * Comprehensive guide to implementing common design patterns in PHP.
 */

echo "=== PHP Design Patterns ===\n\n";

// Creational Patterns
echo "--- Creational Patterns ---\n";

// Singleton Pattern
echo "Singleton Pattern:\n";
class DatabaseConnection {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connection = "Database connection established";
        echo "Database connection created\n";
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql) {
        echo "Executing: $sql\n";
        return "Result set";
    }
    
    private function __clone() {}
    private function __wakeup() {}
}

$db1 = DatabaseConnection::getInstance();
$db2 = DatabaseConnection::getInstance();

echo "Same instance: " . ($db1 === $db2 ? 'Yes' : 'No') . "\n";
$db1->query("SELECT * FROM users");
echo "\n";

// Factory Pattern
echo "Factory Pattern:\n";
interface Animal {
    public function makeSound();
    public function getType();
}

class Dog implements Animal {
    public function makeSound() {
        return "Woof!";
    }
    
    public function getType() {
        return "Dog";
    }
}

class Cat implements Animal {
    public function makeSound() {
        return "Meow!";
    }
    
    public function getType() {
        return "Cat";
    }
}

class Bird implements Animal {
    public function makeSound() {
        return "Tweet!";
    }
    
    public function getType() {
        return "Bird";
    }
}

class AnimalFactory {
    public static function createAnimal($type) {
        switch (strtolower($type)) {
            case 'dog':
                return new Dog();
            case 'cat':
                return new Cat();
            case 'bird':
                return new Bird();
            default:
                throw new \Exception("Unknown animal type: $type");
        }
    }
}

$animals = ['dog', 'cat', 'bird'];
foreach ($animals as $animalType) {
    $animal = AnimalFactory::createAnimal($animalType);
    echo $animal->getType() . " says: " . $animal->makeSound() . "\n";
}
echo "\n";

// Abstract Factory Pattern
echo "Abstract Factory Pattern:\n";
interface GUIFactory {
    public function createButton();
    public function createCheckbox();
}

interface Button {
    public function paint();
}

interface Checkbox {
    public function paint();
}

class WindowsButton implements Button {
    public function paint() {
        echo "Windows Button\n";
    }
}

class WindowsCheckbox implements Checkbox {
    public function paint() {
        echo "Windows Checkbox\n";
    }
}

class MacButton implements Button {
    public function paint() {
        echo "Mac Button\n";
    }
}

class MacCheckbox implements Checkbox {
    public function paint() {
        echo "Mac Checkbox\n";
    }
}

class WindowsFactory implements GUIFactory {
    public function createButton() {
        return new WindowsButton();
    }
    
    public function createCheckbox() {
        return new WindowsCheckbox();
    }
}

class MacFactory implements GUIFactory {
    public function createButton() {
        return new MacButton();
    }
    
    public function createCheckbox() {
        return new MacCheckbox();
    }
}

function renderGUI(GUIFactory $factory) {
    $button = $factory->createButton();
    $checkbox = $factory->createCheckbox();
    
    echo "Rendering GUI:\n";
    $button->paint();
    $checkbox->paint();
}

echo "Windows GUI:\n";
renderGUI(new WindowsFactory());

echo "Mac GUI:\n";
renderGUI(new MacFactory());
echo "\n";

// Builder Pattern
echo "Builder Pattern:\n";
class Computer {
    private $cpu;
    private $ram;
    private $storage;
    private $graphics;
    private $os;
    
    public function setCPU($cpu) { $this->cpu = $cpu; }
    public function setRAM($ram) { $this->ram = $ram; }
    public function setStorage($storage) { $this->storage = $storage; }
    public function setGraphics($graphics) { $this->graphics = $graphics; }
    public function setOS($os) { $this->os = $os; }
    
    public function getSpecs() {
        return [
            'CPU' => $this->cpu,
            'RAM' => $this->ram,
            'Storage' => $this->storage,
            'Graphics' => $this->graphics,
            'OS' => $this->os
        ];
    }
}

class ComputerBuilder {
    private $computer;
    
    public function __construct() {
        $this->computer = new Computer();
    }
    
    public function setCPU($cpu) {
        $this->computer->setCPU($cpu);
        return $this;
    }
    
    public function setRAM($ram) {
        $this->computer->setRAM($ram);
        return $this;
    }
    
    public function setStorage($storage) {
        $this->computer->setStorage($storage);
        return $this;
    }
    
    public function setGraphics($graphics) {
        $this->computer->setGraphics($graphics);
        return $this;
    }
    
    public function setOS($os) {
        $this->computer->setOS($os);
        return $this;
    }
    
    public function build() {
        return $this->computer;
    }
}

$gamingPC = new ComputerBuilder();
$gamingPC->setCPU('Intel i9')
        ->setRAM('32GB DDR4')
        ->setStorage('1TB NVMe SSD')
        ->setGraphics('NVIDIA RTX 3080')
        ->setOS('Windows 10');

$computer = $gamingPC->build();
echo "Gaming PC Specs:\n";
foreach ($computer->getSpecs() as $component => $spec) {
    echo "$component: $spec\n";
}
echo "\n";

// Structural Patterns
echo "--- Structural Patterns ---\n";

// Adapter Pattern
echo "Adapter Pattern:\n";
interface MediaPlayer {
    public function play($audioType, $fileName);
}

interface AdvancedMediaPlayer {
    public function playVlc($fileName);
    public function playMp4($fileName);
}

class VlcPlayer implements AdvancedMediaPlayer {
    public function playVlc($fileName) {
        echo "Playing vlc file: $fileName\n";
    }
    
    public function playMp4($fileName) {
        // Do nothing
    }
}

class Mp4Player implements AdvancedMediaPlayer {
    public function playVlc($fileName) {
        // Do nothing
    }
    
    public function playMp4($fileName) {
        echo "Playing mp4 file: $fileName\n";
    }
}

class MediaAdapter implements MediaPlayer {
    private $advancedMusicPlayer;
    
    public function __construct($audioType) {
        if (strcasecmp($audioType, 'vlc') == 0) {
            $this->advancedMusicPlayer = new VlcPlayer();
        } else if (strcasecmp($audioType, 'mp4') == 0) {
            $this->advancedMusicPlayer = new Mp4Player();
        }
    }
    
    public function play($audioType, $fileName) {
        if (strcasecmp($audioType, 'vlc') == 0) {
            $this->advancedMusicPlayer->playVlc($fileName);
        } else if (strcasecmp($audioType, 'mp4') == 0) {
            $this->advancedMusicPlayer->playMp4($fileName);
        }
    }
}

class AudioPlayer implements MediaPlayer {
    private $mediaAdapter;
    
    public function play($audioType, $fileName) {
        if (strcasecmp($audioType, 'mp3') == 0) {
            echo "Playing mp3 file: $fileName\n";
        } else if (strcasecmp($audioType, 'vlc') == 0 || strcasecmp($audioType, 'mp4') == 0) {
            $this->mediaAdapter = new MediaAdapter($audioType);
            $this->mediaAdapter->play($audioType, $fileName);
        } else {
            echo "Invalid media. $audioType format not supported\n";
        }
    }
}

$audioPlayer = new AudioPlayer();
$audioPlayer->play('mp3', 'song.mp3');
$audioPlayer->play('mp4', 'movie.mp4');
$audioPlayer->play('vlc', 'video.vlc');
$audioPlayer->play('avi', 'video.avi');
echo "\n";

// Decorator Pattern
echo "Decorator Pattern:\n";
interface Pizza {
    public function getDescription();
    public function getCost();
}

class PlainPizza implements Pizza {
    public function getDescription() {
        return "Plain Pizza";
    }
    
    public function getCost() {
        return 4.00;
    }
}

abstract class PizzaDecorator implements Pizza {
    protected $pizza;
    
    public function __construct(Pizza $pizza) {
        $this->pizza = $pizza;
    }
    
    public abstract function getDescription();
    public abstract function getCost();
}

class CheeseDecorator extends PizzaDecorator {
    public function getDescription() {
        return $this->pizza->getDescription() . ", Cheese";
    }
    
    public function getCost() {
        return $this->pizza->getCost() + 1.50;
    }
}

class PepperoniDecorator extends PizzaDecorator {
    public function getDescription() {
        return $this->pizza->getDescription() . ", Pepperoni";
    }
    
    public function getCost() {
        return $this->pizza->getCost() + 2.00;
    }
}

class OlivesDecorator extends PizzaDecorator {
    public function getDescription() {
        return $this->pizza->getDescription() . ", Olives";
    }
    
    public function getCost() {
        return $this->pizza->getCost() + 0.75;
    }
}

$pizza = new PlainPizza();
echo $pizza->getDescription() . " - $" . $pizza->getCost() . "\n";

$pizza = new CheeseDecorator($pizza);
echo $pizza->getDescription() . " - $" . $pizza->getCost() . "\n";

$pizza = new PepperoniDecorator($pizza);
echo $pizza->getDescription() . " - $" . $pizza->getCost() . "\n";

$pizza = new OlivesDecorator($pizza);
echo $pizza->getDescription() . " - $" . $pizza->getCost() . "\n";
echo "\n";

// Facade Pattern
echo "Facade Pattern:\n";
class CPU {
    public function freeze() { echo "CPU freezing\n"; }
    public function jump($position) { echo "CPU jumping to $position\n"; }
    public function execute() { echo "CPU executing\n"; }
}

class Memory {
    public function load($position, $data) { echo "Memory loading $data at $position\n"; }
}

class HardDrive {
    public function read($lba, $size) { echo "Hard drive reading $size bytes from $lba\n"; return "data"; }
}

class ComputerFacade {
    private $cpu;
    private $memory;
    private $hardDrive;
    
    public function __construct() {
        $this->cpu = new CPU();
        $this->memory = new Memory();
        $this->hardDrive = new HardDrive();
    }
    
    public function start() {
        echo "Starting computer...\n";
        $this->cpu->freeze();
        $bootData = $this->hardDrive->read(0, 1024);
        $this->memory->load(0, $bootData);
        $this->cpu->jump(0);
        $this->cpu->execute();
        echo "Computer started successfully\n";
    }
}

$computer = new ComputerFacade();
$computer->start();
echo "\n";

// Behavioral Patterns
echo "--- Behavioral Patterns ---\n";

// Observer Pattern
echo "Observer Pattern:\n";
interface Observer {
    public function update($subject);
}

interface Subject {
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notify();
}

class WeatherStation implements Subject {
    private $observers = [];
    private $temperature;
    private $humidity;
    
    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer) {
        $this->observers = array_filter($this->observers, function($obs) use ($observer) {
            return $obs !== $observer;
        });
    }
    
    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
    
    public function setMeasurements($temperature, $humidity) {
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->notify();
    }
    
    public function getTemperature() { return $this->temperature; }
    public function getHumidity() { return $this->humidity; }
}

class TemperatureDisplay implements Observer {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function update($subject) {
        $temp = $subject->getTemperature();
        $humidity = $subject->getHumidity();
        echo "{$this->name}: Temp = {$temp}°C, Humidity = {$humidity}%\n";
    }
}

class FanController implements Observer {
    public function update($subject) {
        $temp = $subject->getTemperature();
        if ($temp > 25) {
            echo "Fan: Turning ON (Temperature: {$temp}°C)\n";
        } else {
            echo "Fan: Turning OFF (Temperature: {$temp}°C)\n";
        }
    }
}

$weatherStation = new WeatherStation();
$display1 = new TemperatureDisplay("Living Room");
$display2 = new TemperatureDisplay("Bedroom");
$fanController = new FanController();

$weatherStation->attach($display1);
$weatherStation->attach($display2);
$weatherStation->attach($fanController);

$weatherStation->setMeasurements(22, 65);
$weatherStation->setMeasurements(28, 70);
echo "\n";

// Strategy Pattern
echo "Strategy Pattern:\n";
interface PaymentStrategy {
    public function pay($amount);
}

class CreditCardStrategy implements PaymentStrategy {
    private $cardNumber;
    private $name;
    
    public function __construct($cardNumber, $name) {
        $this->cardNumber = $cardNumber;
        $this->name = $name;
    }
    
    public function pay($amount) {
        echo "Paid $amount using Credit Card (****" . substr($this->cardNumber, -4) . ")\n";
    }
}

class PayPalStrategy implements PaymentStrategy {
    private $email;
    
    public function __construct($email) {
        $this->email = $email;
    }
    
    public function pay($amount) {
        echo "Paid $amount using PayPal ($this->email)\n";
    }
}

class BitcoinStrategy implements PaymentStrategy {
    private $walletAddress;
    
    public function __construct($walletAddress) {
        $this->walletAddress = $walletAddress;
    }
    
    public function pay($amount) {
        echo "Paid $amount using Bitcoin ($this->walletAddress)\n";
    }
}

class ShoppingCart {
    private $amount;
    private $paymentStrategy;
    
    public function __construct($amount) {
        $this->amount = $amount;
    }
    
    public function setPaymentStrategy(PaymentStrategy $paymentStrategy) {
        $this->paymentStrategy = $paymentStrategy;
    }
    
    public function checkout() {
        $this->paymentStrategy->pay($this->amount);
    }
}

$cart = new ShoppingCart(150.00);

$cart->setPaymentStrategy(new CreditCardStrategy("1234567890123456", "John Doe"));
$cart->checkout();

$cart->setPaymentStrategy(new PayPalStrategy("john@example.com"));
$cart->checkout();

$cart->setPaymentStrategy(new BitcoinStrategy("1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa"));
$cart->checkout();
echo "\n";

// Command Pattern
echo "Command Pattern:\n";
interface Command {
    public function execute();
    public function undo();
}

class Light {
    private $isOn = false;
    
    public function turnOn() {
        $this->isOn = true;
        echo "Light is ON\n";
    }
    
    public function turnOff() {
        $this->isOn = false;
        echo "Light is OFF\n";
    }
    
    public function getState() {
        return $this->isOn;
    }
}

class LightOnCommand implements Command {
    private $light;
    
    public function __construct(Light $light) {
        $this->light = $light;
    }
    
    public function execute() {
        $this->light->turnOn();
    }
    
    public function undo() {
        $this->light->turnOff();
    }
}

class LightOffCommand implements Command {
    private $light;
    
    public function __construct(Light $light) {
        $this->light = $light;
    }
    
    public function execute() {
        $this->light->turnOff();
    }
    
    public function undo() {
        $this->light->turnOn();
    }
}

class RemoteControl {
    private $command;
    private $undoCommand;
    
    public function setCommand(Command $command) {
        $this->command = $command;
    }
    
    public function buttonWasPressed() {
        $this->command->execute();
        $this->undoCommand = $this->command;
    }
    
    public function undoButtonWasPressed() {
        if ($this->undoCommand) {
            $this->undoCommand->undo();
        }
    }
}

$light = new Light();
$lightOn = new LightOnCommand($light);
$lightOff = new LightOffCommand($light);

$remote = new RemoteControl();

$remote->setCommand($lightOn);
$remote->buttonWasPressed();

$remote->setCommand($lightOff);
$remote->buttonWasPressed();

echo "Undoing last command:\n";
$remote->undoButtonWasPressed();
echo "\n";

// Iterator Pattern
echo "Iterator Pattern:\n";
interface Iterator {
    public function hasNext();
    public function next();
}

interface Container {
    public function getIterator();
}

class NameRepository implements Container {
    private $names = ["Robert", "John", "Julie", "Lora"];
    
    public function getIterator() {
        return new NameIterator($this->names);
    }
}

class NameIterator implements Iterator {
    private $names;
    private $index = 0;
    
    public function __construct($names) {
        $this->names = $names;
    }
    
    public function hasNext() {
        return $this->index < count($this->names);
    }
    
    public function next() {
        if ($this->hasNext()) {
            return $this->names[$this->index++];
        }
        return null;
    }
}

$nameRepository = new NameRepository();
$iterator = $nameRepository->getIterator();

echo "Names:\n";
while ($iterator->hasNext()) {
    $name = $iterator->next();
    echo "Name: $name\n";
}
echo "\n";

// Template Method Pattern
echo "Template Method Pattern:\n";
abstract class GameTemplate {
    public final function play() {
        $this->initialize();
        $this->startPlay();
        $this->endPlay();
    }
    
    abstract protected function initialize();
    abstract protected function startPlay();
    abstract protected function endPlay();
}

class Cricket extends GameTemplate {
    protected function initialize() {
        echo "Cricket Game Initialized! Start playing.\n";
    }
    
    protected function startPlay() {
        echo "Cricket Game Started. Enjoy the game!\n";
    }
    
    protected function endPlay() {
        echo "Cricket Game Finished!\n";
    }
}

class Football extends GameTemplate {
    protected function initialize() {
        echo "Football Game Initialized! Start playing.\n";
    }
    
    protected function startPlay() {
        echo "Football Game Started. Enjoy the game!\n";
    }
    
    protected function endPlay() {
        echo "Football Game Finished!\n";
    }
}

echo "Playing Cricket:\n";
$cricket = new Cricket();
$cricket->play();

echo "\nPlaying Football:\n";
$football = new Football();
$football->play();
echo "\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Repository Pattern with Factory
echo "Example 1: Repository Pattern with Factory\n";
interface Repository {
    public function find($id);
    public function findAll();
    public function save($entity);
}

class UserRepository implements Repository {
    private $data = [];
    
    public function find($id) {
        return $this->data[$id] ?? null;
    }
    
    public function findAll() {
        return array_values($this->data);
    }
    
    public function save($entity) {
        if (!isset($entity->id)) {
            $entity->id = uniqid();
        }
        $this->data[$entity->id] = $entity;
        return $entity;
    }
}

class ProductRepository implements Repository {
    private $data = [];
    
    public function find($id) {
        return $this->data[$id] ?? null;
    }
    
    public function findAll() {
        return array_values($this->data);
    }
    
    public function save($entity) {
        if (!isset($entity->id)) {
            $entity->id = uniqid();
        }
        $this->data[$entity->id] = $entity;
        return $entity;
    }
}

class RepositoryFactory {
    public static function create($type) {
        switch ($type) {
            case 'user':
                return new UserRepository();
            case 'product':
                return new ProductRepository();
            default:
                throw new \Exception("Unknown repository type: $type");
        }
    }
}

$userRepo = RepositoryFactory::create('user');
$productRepo = RepositoryFactory::create('product');

$user = (object)['name' => 'John Doe', 'email' => 'john@example.com'];
$product = (object)['name' => 'Laptop', 'price' => 999];

$userRepo->save($user);
$productRepo->save($product);

echo "Users:\n";
foreach ($userRepo->findAll() as $u) {
    echo "- {$u->name} ({$u->email})\n";
}

echo "\nProducts:\n";
foreach ($productRepo->findAll() as $p) {
    echo "- {$p->name}: \${$p->price}\n";
}
echo "\n";

// Example 2: Logger Chain of Responsibility
echo "Example 2: Chain of Responsibility Logger\n";
abstract class Logger {
    const INFO = 1;
    const DEBUG = 2;
    const ERROR = 3;
    
    protected $level;
    protected $nextLogger;
    
    public function setNext(Logger $nextLogger) {
        $this->nextLogger = $nextLogger;
        return $nextLogger;
    }
    
    public function message($level, $message) {
        if ($this->level <= $level) {
            $this->write($message);
        }
        
        if ($this->nextLogger) {
            $this->nextLogger->message($level, $message);
        }
    }
    
    abstract protected function write($message);
}

class ConsoleLogger extends Logger {
    public function __construct($level) {
        $this->level = $level;
    }
    
    protected function write($message) {
        echo "Standard Console::Logger: $message\n";
    }
}

class EmailLogger extends Logger {
    public function __construct($level) {
        $this->level = $level;
    }
    
    protected function write($message) {
        echo "Email notification::Logger: $message\n";
    }
}

class FileLogger extends Logger {
    public function __construct($level) {
        $this->level = $level;
    }
    
    protected function write($message) {
        echo "File::Logger: Writing to log file: $message\n";
    }
}

function getChainOfLoggers() {
    $fileLogger = new FileLogger(Logger::DEBUG);
    $emailLogger = new EmailLogger(Logger::INFO);
    $consoleLogger = new ConsoleLogger(Logger::ERROR);
    
    $fileLogger->setNext($emailLogger)->setNext($consoleLogger);
    
    return $fileLogger;
}

$loggerChain = getChainOfLoggers();

echo "Logger Chain Test:\n";
$loggerChain->message(Logger::INFO, "This is an information.");
$loggerChain->message(Logger::DEBUG, "This is a debug level information.");
$loggerChain->message(Logger::ERROR, "This is an error information.");
echo "\n";

echo "=== End of Design Patterns ===\n";
?>
