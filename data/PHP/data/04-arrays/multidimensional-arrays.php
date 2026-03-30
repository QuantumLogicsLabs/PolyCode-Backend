<?php
/**
 * PHP Multidimensional Arrays
 * 
 * Working with multidimensional arrays, nested structures, and complex data organization.
 */

echo "=== PHP Multidimensional Arrays ===\n\n";

// Creating Multidimensional Arrays
echo "--- Creating Multidimensional Arrays ---\n";

// Two-dimensional array (matrix)
$matrix = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

echo "Matrix:\n";
foreach ($matrix as $row) {
    echo "[" . implode(", ", $row) . "]\n";
}

// Three-dimensional array
$cube = [
    [
        [1, 2],
        [3, 4]
    ],
    [
        [5, 6],
        [7, 8]
    ]
];

echo "\n3D Cube:\n";
foreach ($cube as $layer => $plane) {
    echo "Layer $layer:\n";
    foreach ($plane as $row) {
        echo "  [" . implode(", ", $row) . "]\n";
    }
}

// Associative multidimensional array
$students = [
    'student1' => [
        'name' => 'John Doe',
        'age' => 20,
        'grades' => ['Math' => 85, 'Science' => 90, 'English' => 78]
    ],
    'student2' => [
        'name' => 'Jane Smith',
        'age' => 21,
        'grades' => ['Math' => 92, 'Science' => 88, 'English' => 95]
    ]
];

echo "\nStudents data:\n";
print_r($students);

// Accessing Elements
echo "\n--- Accessing Elements ---\n";

// Accessing matrix elements
echo "Matrix element [1][2]: {$matrix[1][2]}\n";

// Accessing cube elements
echo "Cube element [0][1][0]: {$cube[0][1][0]}\n";

// Accessing associative elements
echo "Student 1 name: {$students['student1']['name']}\n";
echo "Student 2 Math grade: {$students['student2']['grades']['Math']}\n";

// Dynamic access
$studentKey = 'student1';
$subject = 'Science';
echo "Student 1 $subject grade: {$students[$studentKey]['grades'][$subject]}\n\n";

// Modifying Elements
echo "--- Modifying Elements ---\n";

// Modify matrix element
$matrix[0][0] = 99;
echo "Modified matrix [0][0]: {$matrix[0][0]}\n";

// Add new student
$students['student3'] = [
    'name' => 'Bob Johnson',
    'age' => 19,
    'grades' => ['Math' => 75, 'Science' => 82, 'English' => 88]
];

echo "Added student 3 name: {$students['student3']['name']}\n";

// Add new grade for student 1
$students['student1']['grades']['History'] = 82;
echo "Student 1 History grade: {$students['student1']['grades']['History']}\n\n";

// Traversing Multidimensional Arrays
echo "--- Traversing Multidimensional Arrays ---\n";

// Nested foreach loops
echo "All matrix elements:\n";
foreach ($matrix as $rowIndex => $row) {
    foreach ($row as $colIndex => $value) {
        echo "[$rowIndex][$colIndex] = $value\n";
    }
}

// Traversing associative array
echo "\nAll student information:\n";
foreach ($students as $studentId => $student) {
    echo "Student ID: $studentId\n";
    echo "Name: {$student['name']}\n";
    echo "Age: {$student['age']}\n";
    echo "Grades:\n";
    foreach ($student['grades'] as $subject => $grade) {
        echo "  $subject: $grade\n";
    }
    echo "\n";
}

// Recursive traversal
echo "Recursive traversal of cube:\n";
function traverseArray($array, $level = 0) {
    foreach ($array as $key => $value) {
        $indent = str_repeat("  ", $level);
        if (is_array($value)) {
            echo "$indent$key:\n";
            traverseArray($value, $level + 1);
        } else {
            echo "$indent$key: $value\n";
        }
    }
}

traverseArray($cube);
echo "\n";

// Searching in Multidimensional Arrays
echo "--- Searching in Multidimensional Arrays ---\n";

// Find element in matrix
function findInMatrix($matrix, $target) {
    foreach ($matrix as $rowIndex => $row) {
        foreach ($row as $colIndex => $value) {
            if ($value === $target) {
                return ['row' => $rowIndex, 'col' => $colIndex];
            }
        }
    }
    return null;
}

$result = findInMatrix($matrix, 5);
if ($result) {
    echo "Found 5 at [{$result['row']}][{$result['col']}]\n";
}

// Find student by name
function findStudentByName($students, $name) {
    foreach ($students as $studentId => $student) {
        if ($student['name'] === $name) {
            return ['id' => $studentId, 'data' => $student];
        }
    }
    return null;
}

$student = findStudentByName($students, 'Jane Smith');
if ($student) {
    echo "Found Jane Smith with ID: {$student['id']}\n";
}

// Find all students with grade above 85
function findStudentsWithHighGrades($students, $threshold) {
    $result = [];
    foreach ($students as $studentId => $student) {
        foreach ($student['grades'] as $subject => $grade) {
            if ($grade > $threshold) {
                $result[] = [
                    'student' => $student['name'],
                    'subject' => $subject,
                    'grade' => $grade
                ];
            }
        }
    }
    return $result;
}

$highGrades = findStudentsWithHighGrades($students, 85);
echo "Grades above 85:\n";
foreach ($highGrades as $item) {
    echo "- {$item['student']} - {$item['subject']}: {$item['grade']}\n";
}
echo "\n";

// Filtering Multidimensional Arrays
echo "--- Filtering Multidimensional Arrays ---\n";

// Filter matrix rows
function filterMatrixRows($matrix, $condition) {
    return array_filter($matrix, $condition);
}

$filteredRows = filterMatrixRows($matrix, function($row) {
    return array_sum($row) > 15;
});

echo "Matrix rows with sum > 15:\n";
foreach ($filteredRows as $row) {
    echo "[" . implode(", ", $row) . "]\n";
}

// Filter students by age
function filterStudentsByAge($students, $minAge) {
    return array_filter($students, function($student) use ($minAge) {
        return $student['age'] >= $minAge;
    });
}

$olderStudents = filterStudentsByAge($students, 20);
echo "\nStudents 20 years or older:\n";
foreach ($olderStudents as $studentId => $student) {
    echo "- {$student['name']} ({$student['age']})\n";
}
echo "\n";

// Sorting Multidimensional Arrays
echo "--- Sorting Multidimensional Arrays ---\n";

// Sort students by name
$studentsByName = $students;
uasort($studentsByName, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

echo "Students sorted by name:\n";
foreach ($studentsByName as $studentId => $student) {
    echo "- {$student['name']}\n";
}

// Sort students by average grade
function getAverageGrade($student) {
    return array_sum($student['grades']) / count($student['grades']);
}

$studentsByGrade = $students;
uasort($studentsByGrade, function($a, $b) {
    return getAverageGrade($b) - getAverageGrade($a);
});

echo "\nStudents sorted by average grade (descending):\n";
foreach ($studentsByGrade as $studentId => $student) {
    $avg = round(getAverageGrade($student), 1);
    echo "- {$student['name']}: $avg\n";
}
echo "\n";

// Transforming Multidimensional Arrays
echo "--- Transforming Multidimensional Arrays ---\n";

// Flatten 2D array
function flatten2D($array) {
    $result = [];
    foreach ($array as $subArray) {
        $result = array_merge($result, $subArray);
    }
    return $result;
}

$flattened = flatten2D($matrix);
echo "Flattened matrix: " . implode(", ", $flattened) . "\n";

// Extract specific column
function extractColumn($array, $column) {
    return array_column($array, $column);
}

$studentNames = extractColumn($students, 'name');
echo "Student names: " . implode(", ", $studentNames) . "\n";

// Transform to grade summary
function createGradeSummary($students) {
    $summary = [];
    foreach ($students as $studentId => $student) {
        $summary[$studentId] = [
            'name' => $student['name'],
            'average' => round(getAverageGrade($student), 1),
            'highest' => max($student['grades']),
            'lowest' => min($student['grades'])
        ];
    }
    return $summary;
}

$gradeSummary = createGradeSummary($students);
echo "\nGrade summary:\n";
foreach ($gradeSummary as $studentId => $summary) {
    echo "- {$summary['name']}: Avg={$summary['average']}, High={$summary['highest']}, Low={$summary['lowest']}\n";
}
echo "\n";

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Shopping Cart
echo "Example 1: Shopping Cart\n";
$cart = [
    'items' => [
        ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'quantity' => 1],
        ['id' => 2, 'name' => 'Mouse', 'price' => 25.50, 'quantity' => 2],
        ['id' => 3, 'name' => 'Keyboard', 'price' => 75.00, 'quantity' => 1]
    ],
    'shipping' => ['method' => 'standard', 'cost' => 10.00],
    'discount' => ['code' => 'SAVE10', 'amount' => 50.00]
];

function calculateCartTotal($cart) {
    $subtotal = 0;
    foreach ($cart['items'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $total = $subtotal + $cart['shipping']['cost'] - $cart['discount']['amount'];
    return [
        'subtotal' => $subtotal,
        'shipping' => $cart['shipping']['cost'],
        'discount' => $cart['discount']['amount'],
        'total' => max(0, $total)
    ];
}

$cartTotal = calculateCartTotal($cart);
echo "Cart totals:\n";
foreach ($cartTotal as $key => $value) {
    echo ucfirst($key) . ": $" . number_format($value, 2) . "\n";
}
echo "\n";

// Example 2: Employee Management System
echo "Example 2: Employee Management System\n";
$employees = [
    ['id' => 1, 'name' => 'John Doe', 'department' => 'Engineering', 'salary' => 75000, 'skills' => ['PHP', 'JavaScript', 'MySQL']],
    ['id' => 2, 'name' => 'Jane Smith', 'department' => 'Marketing', 'salary' => 65000, 'skills' => ['SEO', 'Analytics', 'Content']],
    ['id' => 3, 'name' => 'Bob Johnson', 'department' => 'Engineering', 'salary' => 80000, 'skills' => ['Python', 'Django', 'PostgreSQL']],
    ['id' => 4, 'name' => 'Alice Brown', 'department' => 'HR', 'salary' => 55000, 'skills' => ['Recruitment', 'Training', 'Communication']]
];

function getDepartmentStats($employees) {
    $stats = [];
    foreach ($employees as $employee) {
        $dept = $employee['department'];
        if (!isset($stats[$dept])) {
            $stats[$dept] = [
                'count' => 0,
                'total_salary' => 0,
                'employees' => []
            ];
        }
        $stats[$dept]['count']++;
        $stats[$dept]['total_salary'] += $employee['salary'];
        $stats[$dept]['employees'][] = $employee['name'];
    }
    
    foreach ($stats as $dept => &$data) {
        $data['average_salary'] = round($data['total_salary'] / $data['count'], 2);
    }
    
    return $stats;
}

$deptStats = getDepartmentStats($employees);
echo "Department statistics:\n";
foreach ($deptStats as $dept => $stats) {
    echo "$dept Department:\n";
    echo "  Employees: {$stats['count']}\n";
    echo "  Total salary: $" . number_format($stats['total_salary'], 2) . "\n";
    echo "  Average salary: $" . number_format($stats['average_salary'], 2) . "\n";
    echo "  Team members: " . implode(", ", $stats['employees']) . "\n\n";
}

// Example 3: Recipe Database
echo "Example 3: Recipe Database\n";
$recipes = [
    [
        'name' => 'Spaghetti Carbonara',
        'category' => 'Italian',
        'prep_time' => 15,
        'cook_time' => 20,
        'ingredients' => [
            ['name' => 'Spaghetti', 'amount' => 400, 'unit' => 'g'],
            ['name' => 'Bacon', 'amount' => 200, 'unit' => 'g'],
            ['name' => 'Eggs', 'amount' => 4, 'unit' => 'pieces'],
            ['name' => 'Parmesan', 'amount' => 100, 'unit' => 'g']
        ],
        'difficulty' => 'Medium'
    ],
    [
        'name' => 'Caesar Salad',
        'category' => 'Salad',
        'prep_time' => 15,
        'cook_time' => 0,
        'ingredients' => [
            ['name' => 'Romaine lettuce', 'amount' => 2, 'unit' => 'heads'],
            ['name' => 'Croutons', 'amount' => 100, 'unit' => 'g'],
            ['name' => 'Parmesan', 'amount' => 50, 'unit' => 'g'],
            ['name' => 'Caesar dressing', 'amount' => 100, 'unit' => 'ml']
        ],
        'difficulty' => 'Easy'
    ]
];

function getRecipesByCategory($recipes, $category) {
    return array_filter($recipes, function($recipe) use ($category) {
        return $recipe['category'] === $category;
    });
}

function getTotalTime($recipe) {
    return $recipe['prep_time'] + $recipe['cook_time'];
}

function getIngredientList($recipe) {
    $list = [];
    foreach ($recipe['ingredients'] as $ingredient) {
        $list[] = "{$ingredient['amount']} {$ingredient['unit']} {$ingredient['name']}";
    }
    return $list;
}

$italianRecipes = getRecipesByCategory($recipes, 'Italian');
echo "Italian recipes:\n";
foreach ($italianRecipes as $recipe) {
    echo "- {$recipe['name']} (Total time: " . getTotalTime($recipe) . " min)\n";
    echo "  Ingredients: " . implode(", ", getIngredientList($recipe)) . "\n";
}
echo "\n";

// Example 4: Game Board
echo "Example 4: Game Board\n";
class GameBoard {
    private $board;
    private $size;
    
    public function __construct($size = 8) {
        $this->size = $size;
        $this->initializeBoard();
    }
    
    private function initializeBoard() {
        $this->board = [];
        for ($row = 0; $row < $this->size; $row++) {
            $this->board[$row] = [];
            for ($col = 0; $col < $this->size; $col++) {
                $this->board[$row][$col] = '.';
            }
        }
    }
    
    public function placePiece($row, $col, $piece) {
        if ($this->isValidPosition($row, $col)) {
            $this->board[$row][$col] = $piece;
            return true;
        }
        return false;
    }
    
    public function isValidPosition($row, $col) {
        return $row >= 0 && $row < $this->size && $col >= 0 && $col < $this->size;
    }
    
    public function display() {
        echo "  ";
        for ($col = 0; $col < $this->size; $col++) {
            echo sprintf("%2d", $col);
        }
        echo "\n";
        
        for ($row = 0; $row < $this->size; $row++) {
            echo sprintf("%2d", $row);
            for ($col = 0; $col < $this->size; $col++) {
                echo sprintf("%2s", $this->board[$row][$col]);
            }
            echo "\n";
        }
    }
    
    public function getBoard() {
        return $this->board;
    }
}

$board = new GameBoard(6);
$board->placePiece(0, 0, 'X');
$board->placePiece(0, 1, 'O');
$board->placePiece(1, 0, 'O');
$board->placePiece(1, 1, 'X');
$board->placePiece(2, 2, 'X');

echo "Game board:\n";
$board->display();
echo "\n";

// Example 5: Data Analysis
echo "Example 5: Data Analysis\n";
$salesData = [
    'Q1' => [
        'January' => ['product1' => 100, 'product2' => 150, 'product3' => 200],
        'February' => ['product1' => 120, 'product2' => 180, 'product3' => 220],
        'March' => ['product1' => 140, 'product2' => 200, 'product3' => 250]
    ],
    'Q2' => [
        'April' => ['product1' => 160, 'product2' => 220, 'product3' => 280],
        'May' => ['product1' => 180, 'product2' => 240, 'product3' => 300],
        'June' => ['product1' => 200, 'product2' => 260, 'product3' => 320]
    ]
];

function analyzeSales($salesData) {
    $analysis = [
        'total_sales' => 0,
        'quarter_totals' => [],
        'product_totals' => [],
        'best_month' => null,
        'best_product' => null
    ];
    
    foreach ($salesData as $quarter => $months) {
        $quarterTotal = 0;
        foreach ($months as $month => $products) {
            $monthTotal = array_sum($products);
            $quarterTotal += $monthTotal;
            $analysis['total_sales'] += $monthTotal;
            
            // Track best month
            if ($analysis['best_month'] === null || $monthTotal > $analysis['best_month']['sales']) {
                $analysis['best_month'] = ['month' => $month, 'sales' => $monthTotal];
            }
            
            // Accumulate product totals
            foreach ($products as $product => $sales) {
                if (!isset($analysis['product_totals'][$product])) {
                    $analysis['product_totals'][$product] = 0;
                }
                $analysis['product_totals'][$product] += $sales;
            }
        }
        $analysis['quarter_totals'][$quarter] = $quarterTotal;
    }
    
    // Find best product
    if (!empty($analysis['product_totals'])) {
        $maxProduct = max($analysis['product_totals']);
        $analysis['best_product'] = [
            'product' => array_search($maxProduct, $analysis['product_totals']),
            'sales' => $maxProduct
        ];
    }
    
    return $analysis;
}

$analysis = analyzeSales($salesData);
echo "Sales Analysis:\n";
echo "Total sales: {$analysis['total_sales']}\n";
echo "Quarter totals:\n";
foreach ($analysis['quarter_totals'] as $quarter => $total) {
    echo "  $quarter: $total\n";
}
echo "Best month: {$analysis['best_month']['month']} ({$analysis['best_month']['sales']})\n";
echo "Best product: {$analysis['best_product']['product']} ({$analysis['best_product']['sales']})\n";
echo "Product totals:\n";
foreach ($analysis['product_totals'] as $product => $total) {
    echo "  $product: $total\n";
}
echo "\n";

echo "=== End of Multidimensional Arrays ===\n";
?>
