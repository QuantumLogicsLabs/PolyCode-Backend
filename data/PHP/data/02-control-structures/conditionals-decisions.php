<?php
/**
 * PHP Conditionals and Decision Making
 * 
 * Understanding if-else statements, switch cases, and conditional expressions in PHP.
 */

echo "=== PHP Conditionals and Decision Making ===\n\n";

// If Statement
echo "--- If Statement ---\n";

$age = 25;
if ($age >= 18) {
    echo "You are an adult.\n";
}

$score = 85;
if ($score >= 90) {
    echo "Grade: A\n";
} elseif ($score >= 80) {
    echo "Grade: B\n";
} elseif ($score >= 70) {
    echo "Grade: C\n";
} elseif ($score >= 60) {
    echo "Grade: D\n";
} else {
    echo "Grade: F\n";
}

// Multiple conditions
echo "\nMultiple conditions:\n";
$username = "admin";
$password = "secret123";
$isActive = true;

if ($username === "admin" && $password === "secret123" && $isActive) {
    echo "Login successful!\n";
} else {
    echo "Login failed!\n";
}

// Nested if statements
echo "\nNested if statements:\n";
$role = "manager";
$department = "sales";
$yearsOfService = 5;

if ($role === "manager") {
    if ($department === "sales") {
        if ($yearsOfService >= 3) {
            echo "Eligible for sales manager bonus.\n";
        } else {
            echo "Not enough service for bonus.\n";
        }
    } else {
        echo "Not in sales department.\n";
    }
} else {
    echo "Not a manager.\n";
}

// Switch Statement
echo "\n--- Switch Statement ---\n";

$day = "Wednesday";
switch ($day) {
    case "Monday":
        echo "Start of the work week.\n";
        break;
    case "Tuesday":
    case "Wednesday":
    case "Thursday":
        echo "Mid-week days.\n";
        break;
    case "Friday":
        echo "TGIF! End of the work week.\n";
        break;
    case "Saturday":
    case "Sunday":
        echo "Weekend!\n";
        break;
    default:
        echo "Invalid day.\n";
}

// Switch with multiple cases
echo "\nSwitch with multiple cases:\n";
$grade = 'B';
switch ($grade) {
    case 'A':
    case 'B':
    case 'C':
        echo "Passing grade.\n";
        break;
    case 'D':
        echo "Conditional pass.\n";
        break;
    case 'F':
        echo "Failing grade.\n";
        break;
    default:
        echo "Invalid grade.\n";
}

// Switch with expressions (PHP 8.0+)
echo "\nSwitch expression (PHP 8.0+ style):\n";
$status = "active";
$message = match ($status) {
    "active" => "Account is active",
    "inactive" => "Account is inactive",
    "suspended" => "Account is suspended",
    "deleted" => "Account has been deleted",
    default => "Unknown status"
};
echo "Message: $message\n\n";

// Ternary Operator
echo "--- Ternary Operator ---\n";

$isLoggedIn = true;
$welcomeMessage = $isLoggedIn ? "Welcome back!" : "Please log in.";
echo "Welcome message: $welcomeMessage\n";

// Nested ternary
$score = 75;
$result = $score >= 90 ? "Excellent" : ($score >= 70 ? "Good" : "Needs improvement");
echo "Result: $result\n";

// Ternary with assignment
$age = 20;
$canVote = $age >= 18 ? true : false;
echo "Can vote: " . ($canVote ? "Yes" : "No") . "\n\n";

// Null Coalescing Operator
echo "--- Null Coalescing Operator ---\n";

$name = $_GET['name'] ?? 'Guest';
echo "Hello, $name!\n";

// Chain null coalescing
$config = [
    'database' => [
        'host' => 'localhost'
    ]
];

$host = $config['database']['host'] ?? 'default_host';
$port = $config['database']['port'] ?? 3306;
$username = $config['database']['username'] ?? 'root';

echo "Database config:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Username: $username\n\n";

// Spaceship Operator
echo "--- Spaceship Operator ---\n";

$values = [10, 20, 5, 15];
sort($values);

echo "Sorted values: " . implode(", ", $values) . "\n";

// Comparison examples
echo "\nSpaceship operator comparisons:\n";
echo "10 <=> 20: " . (10 <=> 20) . "\n"; // -1
echo "20 <=> 10: " . (20 <=> 10) . "\n"; // 1
echo "10 <=> 10: " . (10 <=> 10) . "\n"; // 0

// String comparison
echo "'apple' <=> 'banana': " . ('apple' <=> 'banana') . "\n";
echo "'banana' <=> 'apple': " . ('banana' <=> 'apple') . "\n";
echo "'apple' <=> 'apple': " . ('apple' <=> 'apple') . "\n\n";

// Logical Operators
echo "--- Logical Operators ---\n";

// AND (&&)
$x = 5;
$y = 10;
if ($x > 0 && $y > 0) {
    echo "Both numbers are positive.\n";
}

// OR (||)
$hasPermission = true;
$isAdmin = false;
if ($hasPermission || $isAdmin) {
    echo "Access granted.\n";
}

// NOT (!)
$isBlocked = false;
if (!$isBlocked) {
    echo "User is not blocked.\n";
}

// XOR (exclusive OR)
$condition1 = true;
$condition2 = false;
if ($condition1 xor $condition2) {
    echo "Exactly one condition is true.\n";
}

// Complex logical expressions
echo "\nComplex logical expressions:\n";
$age = 25;
$hasLicense = true;
$hasInsurance = false;

if ($age >= 18 && $hasLicense && $hasInsurance) {
    echo "Can drive legally.\n";
} elseif ($age >= 18 && $hasLicense && !$hasInsurance) {
    echo "Can drive but needs insurance.\n";
} elseif ($age < 18) {
    echo "Too young to drive.\n";
} else {
    echo "Cannot drive.\n";
}

// Short-circuit evaluation
echo "\nShort-circuit evaluation:\n";
function expensiveOperation() {
    echo "Expensive operation called!\n";
    return true;
}

// Second condition won't be evaluated because first is false
if (false && expensiveOperation()) {
    echo "This won't be printed.\n";
} else {
    echo "Short-circuit prevented expensive operation.\n";
}

// Second condition will be evaluated because first is true
if (true || expensiveOperation()) {
    echo "Short-circuit prevented expensive operation.\n";
}
echo "\n";

// Type Juggling in Conditionals
echo "--- Type Juggling in Conditionals ---\n";

// Truthy and falsy values
$values = [0, "", "0", [], null, false, "hello", 1, [1, 2]];

echo "Truthy/Falsy evaluation:\n";
foreach ($values as $value) {
    if ($value) {
        echo "Value: ";
        var_export($value);
        echo " is truthy\n";
    } else {
        echo "Value: ";
        var_export($value);
        echo " is falsy\n";
    }
}

// Strict comparison
echo "\nStrict vs loose comparison:\n";
$strictValues = [0, "", "0", false];

foreach ($strictValues as $value) {
    echo "Value: ";
    var_export($value);
    echo " == false: " . ($value == false ? 'true' : 'false') . "\n";
    echo "Value: ";
    var_export($value);
    echo " === false: " . ($value === false ? 'true' : 'false') . "\n\n";
}

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: User Authentication
echo "Example 1: User Authentication\n";
function authenticateUser($username, $password, $remember = false) {
    // Simulated user database
    $users = [
        'admin' => ['password' => 'admin123', 'role' => 'admin', 'active' => true],
        'user' => ['password' => 'user123', 'role' => 'user', 'active' => true],
        'guest' => ['password' => 'guest123', 'role' => 'guest', 'active' => false]
    ];
    
    if (!isset($users[$username])) {
        return ['success' => false, 'message' => 'User not found'];
    }
    
    $user = $users[$username];
    
    if (!$user['active']) {
        return ['success' => false, 'message' => 'Account is inactive'];
    }
    
    if ($user['password'] !== $password) {
        return ['success' => false, 'message' => 'Invalid password'];
    }
    
    $sessionData = [
        'username' => $username,
        'role' => $user['role'],
        'login_time' => date('Y-m-d H:i:s'),
        'remember' => $remember
    ];
    
    return ['success' => true, 'message' => 'Login successful', 'session' => $sessionData];
}

$loginResult = authenticateUser('admin', 'admin123', true);
echo "Login result: " . ($loginResult['success'] ? $loginResult['message'] : $loginResult['message']) . "\n";
if ($loginResult['success']) {
    echo "Session created for: " . $loginResult['session']['username'] . "\n";
}
echo "\n";

// Example 2: Shopping Cart Discount Calculator
echo "Example 2: Shopping Cart Discount Calculator\n";
function calculateDiscount($cartTotal, $customerType, $isFirstTime = false) {
    $discount = 0;
    $reason = "";
    
    // First-time customer discount
    if ($isFirstTime) {
        $discount = max($discount, 15);
        $reason = $discount > 0 ? "First-time customer" : $reason;
    }
    
    // Customer type discount
    switch ($customerType) {
        case 'vip':
            $discount = max($discount, 20);
            $reason = $discount >= 20 ? "VIP customer" : $reason;
            break;
        case 'premium':
            $discount = max($discount, 10);
            $reason = $discount >= 10 ? "Premium customer" : $reason;
            break;
        case 'regular':
            // No additional discount
            break;
    }
    
    // Volume discount
    if ($cartTotal >= 1000) {
        $discount = max($discount, 25);
        $reason = $discount >= 25 ? "High volume purchase" : $reason;
    } elseif ($cartTotal >= 500) {
        $discount = max($discount, 15);
        $reason = $discount >= 15 ? "Medium volume purchase" : $reason;
    } elseif ($cartTotal >= 100) {
        $discount = max($discount, 5);
        $reason = $discount >= 5 ? "Low volume purchase" : $reason;
    }
    
    return [
        'discount_percentage' => $discount,
        'discount_reason' => $reason ?: "No discount",
        'final_total' => $cartTotal * (1 - $discount / 100)
    ];
}

$scenarios = [
    ['total' => 150, 'type' => 'regular', 'first_time' => true],
    ['total' => 1200, 'type' => 'vip', 'first_time' => false],
    ['total' => 600, 'type' => 'premium', 'first_time' => false],
    ['total' => 80, 'type' => 'regular', 'first_time' => false]
];

foreach ($scenarios as $scenario) {
    $result = calculateDiscount($scenario['total'], $scenario['type'], $scenario['first_time']);
    echo "Cart: \${$scenario['total']}, Type: {$scenario['type']}, First time: " . ($scenario['first_time'] ? 'Yes' : 'No') . "\n";
    echo "Discount: {$result['discount_percentage']}% ({$result['discount_reason']})\n";
    echo "Final total: \${$result['final_total']}\n\n";
}

// Example 3: Weather Advisor
echo "Example 3: Weather Advisor\n";
function getWeatherAdvice($temperature, $humidity, $isRaining, $windSpeed) {
    $advice = [];
    
    // Temperature advice
    if ($temperature < 0) {
        $advice[] = "Freezing weather! Wear heavy winter clothing.";
    } elseif ($temperature < 10) {
        $advice[] = "Cold weather. Wear a jacket.";
    } elseif ($temperature < 20) {
        $advice[] = "Cool weather. Light jacket recommended.";
    } elseif ($temperature < 30) {
        $advice[] = "Pleasant weather. Comfortable clothing.";
    } else {
        $advice[] = "Hot weather. Wear light clothing and stay hydrated.";
    }
    
    // Humidity advice
    if ($humidity > 80) {
        $advice[] = "Very humid. It might feel uncomfortable.";
    } elseif ($humidity < 30) {
        $advice[] = "Very dry. Use moisturizer.";
    }
    
    // Rain advice
    if ($isRaining) {
        $advice[] = "It's raining. Take an umbrella!";
    }
    
    // Wind advice
    if ($windSpeed > 50) {
        $advice[] = "Strong winds! Be careful outdoors.";
    } elseif ($windSpeed > 25) {
        $advice[] = "Windy day. Secure loose items.";
    }
    
    // Combined conditions
    if ($isRaining && $windSpeed > 30) {
        $advice[] = "Stormy conditions! Stay indoors if possible.";
    }
    
    if ($temperature > 25 && $humidity > 70 && $isRaining) {
        $advice[] = "Perfect conditions for fungi growth!";
    }
    
    return empty($advice) ? ["Normal weather conditions."] : $advice;
}

$weatherConditions = [
    ['temp' => -5, 'humidity' => 65, 'rain' => false, 'wind' => 20],
    ['temp' => 15, 'humidity' => 85, 'rain' => true, 'wind' => 35],
    ['temp' => 35, 'humidity' => 25, 'rain' => false, 'wind' => 10],
    ['temp' => 22, 'humidity' => 50, 'rain' => false, 'wind' => 15]
];

foreach ($weatherConditions as $weather) {
    echo "Weather: {$weather['temp']}°C, {$weather['humidity']}% humidity, ";
    echo "Rain: " . ($weather['rain'] ? 'Yes' : 'No') . ", Wind: {$weather['wind']} km/h\n";
    $advice = getWeatherAdvice($weather['temp'], $weather['humidity'], $weather['rain'], $weather['wind']);
    foreach ($advice as $tip) {
        echo "  - $tip\n";
    }
    echo "\n";
}

// Example 4: File Type Handler
echo "Example 4: File Type Handler\n";
function handleFileUpload($fileName, $fileSize, $mimeType) {
    $allowedTypes = [
        'image/jpeg' => ['max_size' => 5242880, 'category' => 'image'],
        'image/png' => ['max_size' => 5242880, 'category' => 'image'],
        'image/gif' => ['max_size' => 5242880, 'category' => 'image'],
        'application/pdf' => ['max_size' => 10485760, 'category' => 'document'],
        'text/plain' => ['max_size' => 1048576, 'category' => 'text'],
        'application/zip' => ['max_size' => 20971520, 'category' => 'archive']
    ];
    
    // Check if file type is allowed
    if (!isset($allowedTypes[$mimeType])) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    $fileInfo = $allowedTypes[$mimeType];
    
    // Check file size
    if ($fileSize > $fileInfo['max_size']) {
        $maxSizeMB = $fileInfo['max_size'] / 1048576;
        return ['success' => false, 'message' => "File too large. Max size: {$maxSizeMB}MB"];
    }
    
    // Process based on category
    switch ($fileInfo['category']) {
        case 'image':
            $processing = 'Image will be resized and optimized';
            break;
        case 'document':
            $processing = 'Document will be scanned for viruses';
            break;
        case 'text':
            $processing = 'Text file will be validated for encoding';
            break;
        case 'archive':
            $processing = 'Archive will be extracted and scanned';
            break;
        default:
            $processing = 'File will be stored as-is';
    }
    
    return [
        'success' => true,
        'message' => 'File uploaded successfully',
        'category' => $fileInfo['category'],
        'processing' => $processing
    ];
}

$files = [
    ['name' => 'photo.jpg', 'size' => 2048000, 'type' => 'image/jpeg'],
    ['name' => 'document.pdf', 'size' => 15728640, 'type' => 'application/pdf'],
    ['name' => 'script.exe', 'size' => 1024000, 'type' => 'application/octet-stream'],
    ['name' => 'notes.txt', 'size' => 512000, 'type' => 'text/plain']
];

foreach ($files as $file) {
    $result = handleFileUpload($file['name'], $file['size'], $file['type']);
    echo "File: {$file['name']} (" . round($file['size']/1024) . "KB)\n";
    echo "Result: " . ($result['success'] ? $result['message'] : $result['message']) . "\n";
    if ($result['success']) {
        echo "Category: {$result['category']}\n";
        echo "Processing: {$result['processing']}\n";
    }
    echo "\n";
}

echo "=== End of Conditionals and Decision Making ===\n";
?>
