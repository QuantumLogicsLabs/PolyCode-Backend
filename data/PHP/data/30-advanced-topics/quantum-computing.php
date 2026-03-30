<?php
/**
 * Quantum Computing in PHP
 * 
 * Quantum computing concepts, quantum algorithms, and quantum simulation.
 */

// Quantum Computing Framework
class QuantumComputingFramework
{
    private array $qubits;
    private array $gates;
    private array $circuits;
    private QuantumSimulator $simulator;
    private QuantumAlgorithms $algorithms;
    
    public function __construct()
    {
        $this->qubits = [];
        $this->gates = [];
        $this->circuits = [];
        $this->simulator = new QuantumSimulator();
        $this->algorithms = new QuantumAlgorithms();
        
        $this->initializeQuantumGates();
    }
    
    private function initializeQuantumGates(): void
    {
        $this->gates = [
            'pauli_x' => new PauliXGate(),
            'pauli_y' => new PauliYGate(),
            'pauli_z' => new PauliZGate(),
            'hadamard' => new HadamardGate(),
            'cnot' => new CNOTGate(),
            'phase' => new PhaseGate(),
            'rx' => new RotationXGate(),
            'ry' => new RotationYGate(),
            'rz' => new RotationZGate(),
            'swap' => new SwapGate(),
            'toffoli' => new ToffoliGate(),
            'fredkin' => new FredkinGate()
        ];
    }
    
    public function createQubit(string $id, float $alpha = 1.0, float $beta = 0.0): Qubit
    {
        $qubit = new Qubit($id, $alpha, $beta);
        $this->qubits[$id] = $qubit;
        
        echo "Created qubit: $id (|0⟩ amplitude: $alpha, |1⟩ amplitude: $beta)\n";
        return $qubit;
    }
    
    public function getQubit(string $id): ?Qubit
    {
        return $this->qubits[$id] ?? null;
    }
    
    public function createQuantumCircuit(string $name, array $qubits): QuantumCircuit
    {
        $circuit = new QuantumCircuit($name, $qubits);
        $this->circuits[$name] = $circuit;
        
        echo "Created quantum circuit: $name with " . count($qubits) . " qubits\n";
        return $circuit;
    }
    
    public function getCircuit(string $name): ?QuantumCircuit
    {
        return $this->circuits[$name] ?? null;
    }
    
    public function applyGate(string $gateName, array $qubits, array $params = []): void
    {
        if (!isset($this->gates[$gateName])) {
            throw new Exception("Quantum gate not found: $gateName");
        }
        
        $gate = $this->gates[$gateName];
        
        if (isset($params['angle'])) {
            $gate->setAngle($params['angle']);
        }
        
        foreach ($qubits as $qubitId) {
            if (!isset($this->qubits[$qubitId])) {
                throw new Exception("Qubit not found: $qubitId");
            }
            
            $gate->apply($this->qubits[$qubitId]);
        }
        
        echo "Applied quantum gate: $gateName to qubits: " . implode(', ', $qubits) . "\n";
    }
    
    public function measureQubit(string $id): int
    {
        if (!isset($this->qubits[$id])) {
            throw new Exception("Qubit not found: $id");
        }
        
        $result = $this->simulator->measure($this->qubits[$id]);
        
        echo "Measured qubit $id: result = $result\n";
        return $result;
    }
    
    public function getQuantumState(string $id): array
    {
        if (!isset($this->qubits[$id])) {
            throw new Exception("Qubit not found: $id");
        }
        
        $state = $this->simulator->getState($this->qubits[$id]);
        
        echo "Quantum state of qubit $id:\n";
        echo "  |0⟩ amplitude: " . round($state['alpha'], 4) . "\n";
        echo "  |1⟩ amplitude: " . round($state['beta'], 4) . "\n";
        echo "  |0⟩ probability: " . round($state['alpha_prob'], 4) . "\n";
        echo "  |1⟩ probability: " . round($state['beta_prob'], 4) . "\n";
        
        return $state;
    }
    
    public function runQuantumAlgorithm(string $algorithmName, array $params = []): array
    {
        return $this->algorithms->run($algorithmName, $params, $this);
    }
    
    public function getSimulator(): QuantumSimulator
    {
        return $this->simulator;
    }
    
    public function getAlgorithms(): QuantumAlgorithms
    {
        return $this->algorithms;
    }
    
    public function getQubits(): array
    {
        return $this->qubits;
    }
    
    public function getGates(): array
    {
        return $this->gates;
    }
    
    public function getCircuits(): array
    {
        return $this->circuits;
    }
}

// Qubit Class
class Qubit
{
    private string $id;
    private ComplexNumber $alpha;
    private ComplexNumber $beta;
    private bool $measured;
    private ?int $measurementResult;
    
    public function __construct(string $id, float $alpha = 1.0, float $beta = 0.0)
    {
        $this->id = $id;
        $this->alpha = new ComplexNumber($alpha, 0);
        $this->beta = new ComplexNumber($beta, 0);
        $this->measured = false;
        $this->measurementResult = null;
        
        $this->normalize();
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getAlpha(): ComplexNumber
    {
        return $this->alpha;
    }
    
    public function getBeta(): ComplexNumber
    {
        return $this->beta;
    }
    
    public function setAlpha(ComplexNumber $alpha): void
    {
        $this->alpha = $alpha;
        $this->normalize();
    }
    
    public function setBeta(ComplexNumber $beta): void
    {
        $this->beta = $beta;
        $this->normalize();
    }
    
    public function isMeasured(): bool
    {
        return $this->measured;
    }
    
    public function getMeasurementResult(): ?int
    {
        return $this->measurementResult;
    }
    
    public function measure(int $result): void
    {
        $this->measured = true;
        $this->measurementResult = $result;
        
        // Collapse the wavefunction
        if ($result === 0) {
            $this->alpha = new ComplexNumber(1, 0);
            $this->beta = new ComplexNumber(0, 0);
        } else {
            $this->alpha = new ComplexNumber(0, 0);
            $this->beta = new ComplexNumber(1, 0);
        }
    }
    
    public function reset(): void
    {
        $this->alpha = new ComplexNumber(1, 0);
        $this->beta = new ComplexNumber(0, 0);
        $this->measured = false;
        $this->measurementResult = null;
    }
    
    public function normalize(): void
    {
        $norm = sqrt($this->alpha->magnitudeSquared() + $this->beta->magnitudeSquared());
        
        if ($norm > 0) {
            $this->alpha = $this->alpha->divide(new ComplexNumber($norm, 0));
            $this->beta = $this->beta->divide(new ComplexNumber($norm, 0));
        }
    }
    
    public function getProbability0(): float
    {
        return $this->alpha->magnitudeSquared();
    }
    
    public function getProbability1(): float
    {
        return $this->beta->magnitudeSquared();
    }
    
    public function __toString(): string
    {
        if ($this->measured) {
            return "Qubit({$this->id}: |{$this->measurementResult}⟩)";
        }
        
        return "Qubit({$this->id}: {$this->alpha}|0⟩ + {$this->beta}|1⟩)";
    }
}

// Complex Number Class
class ComplexNumber
{
    public float $real;
    public float $imaginary;
    
    public function __construct(float $real = 0, float $imaginary = 0)
    {
        $this->real = $real;
        $this->imaginary = $imaginary;
    }
    
    public function add(ComplexNumber $other): ComplexNumber
    {
        return new ComplexNumber($this->real + $other->real, $this->imaginary + $other->imaginary);
    }
    
    public function subtract(ComplexNumber $other): ComplexNumber
    {
        return new ComplexNumber($this->real - $other->real, $this->imaginary - $other->imaginary);
    }
    
    public function multiply(ComplexNumber $other): ComplexNumber
    {
        $real = $this->real * $other->real - $this->imaginary * $other->imaginary;
        $imaginary = $this->real * $other->imaginary + $this->imaginary * $other->real;
        
        return new ComplexNumber($real, $imaginary);
    }
    
    public function divide(ComplexNumber $other): ComplexNumber
    {
        $denominator = $other->real * $other->real + $other->imaginary * $other->imaginary;
        
        if ($denominator == 0) {
            throw new Exception("Division by zero");
        }
        
        $real = ($this->real * $other->real + $this->imaginary * $other->imaginary) / $denominator;
        $imaginary = ($this->imaginary * $other->real - $this->real * $other->imaginary) / $denominator;
        
        return new ComplexNumber($real, $imaginary);
    }
    
    public function magnitude(): float
    {
        return sqrt($this->real * $this->real + $this->imaginary * $this->imaginary);
    }
    
    public function magnitudeSquared(): float
    {
        return $this->real * $this->real + $this->imaginary * $this->imaginary;
    }
    
    public function conjugate(): ComplexNumber
    {
        return new ComplexNumber($this->real, -$this->imaginary);
    }
    
    public function __toString(): string
    {
        if ($this->imaginary >= 0) {
            return "{$this->real} + {$this->imaginary}i";
        } else {
            return "{$this->real} - " . abs($this->imaginary) . "i";
        }
    }
}

// Quantum Gate Base Class
abstract class QuantumGate
{
    protected string $name;
    protected array $matrix;
    protected ?float $angle;
    
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->angle = null;
        $this->initializeMatrix();
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getMatrix(): array
    {
        return $this->matrix;
    }
    
    public function getAngle(): ?float
    {
        return $this->angle;
    }
    
    public function setAngle(float $angle): void
    {
        $this->angle = $angle;
        $this->updateMatrix();
    }
    
    abstract protected function initializeMatrix(): void;
    abstract protected function updateMatrix(): void;
    
    public function apply(Qubit $qubit): void
    {
        if ($qubit->isMeasured()) {
            return;
        }
        
        $alpha = $qubit->getAlpha();
        $beta = $qubit->getBeta();
        
        $newAlpha = $this->matrix[0][0]->multiply($alpha)->add($this->matrix[0][1]->multiply($beta));
        $newBeta = $this->matrix[1][0]->multiply($alpha)->add($this->matrix[1][1]->multiply($beta));
        
        $qubit->setAlpha($newAlpha);
        $qubit->setBeta($newBeta);
    }
    
    protected function createComplexMatrix(array $realMatrix): array
    {
        $complexMatrix = [];
        
        foreach ($realMatrix as $row) {
            $complexRow = [];
            foreach ($row as $value) {
                $complexRow[] = new ComplexNumber($value, 0);
            }
            $complexMatrix[] = $complexRow;
        }
        
        return $complexMatrix;
    }
}

// Pauli-X Gate (NOT Gate)
class PauliXGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('pauli_x');
    }
    
    protected function initializeMatrix(): void
    {
        $this->matrix = $this->createComplexMatrix([
            [0, 1],
            [1, 0]
        ]);
    }
    
    protected function updateMatrix(): void
    {
        // Pauli-X gate doesn't depend on angle
    }
}

// Pauli-Y Gate
class PauliYGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('pauli_y');
    }
    
    protected function initializeMatrix(): void
    {
        $this->matrix = [
            [new ComplexNumber(0, 0), new ComplexNumber(0, -1)],
            [new ComplexNumber(0, 1), new ComplexNumber(0, 0)]
        ];
    }
    
    protected function updateMatrix(): void
    {
        // Pauli-Y gate doesn't depend on angle
    }
}

// Pauli-Z Gate
class PauliZGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('pauli_z');
    }
    
    protected function initializeMatrix(): void
    {
        $this->matrix = $this->createComplexMatrix([
            [1, 0],
            [0, -1]
        ]);
    }
    
    protected function updateMatrix(): void
    {
        // Pauli-Z gate doesn't depend on angle
    }
}

// Hadamard Gate
class HadamardGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('hadamard');
    }
    
    protected function initializeMatrix(): void
    {
        $invSqrt2 = 1 / sqrt(2);
        
        $this->matrix = $this->createComplexMatrix([
            [$invSqrt2, $invSqrt2],
            [$invSqrt2, -$invSqrt2]
        ]);
    }
    
    protected function updateMatrix(): void
    {
        // Hadamard gate doesn't depend on angle
    }
}

// CNOT Gate (Controlled-NOT)
class CNOTGate extends QuantumGate
{
    private Qubit $control;
    private Qubit $target;
    
    public function __construct()
    {
        parent::__construct('cnot');
    }
    
    protected function initializeMatrix(): void
    {
        // CNOT is a 2-qubit gate, matrix is handled differently
        $this->matrix = [];
    }
    
    protected function updateMatrix(): void
    {
        // CNOT gate doesn't depend on angle
    }
    
    public function apply(Qubit $qubit): void
    {
        // CNOT requires two qubits, handled in the framework
    }
    
    public function applyTwoQubits(Qubit $control, Qubit $target): void
    {
        if ($control->isMeasured() || $target->isMeasured()) {
            return;
        }
        
        // If control qubit is |1⟩, flip the target qubit
        if ($control->getProbability1() > 0.5) {
            $pauliX = new PauliXGate();
            $pauliX->apply($target);
        }
    }
}

// Phase Gate
class PhaseGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('phase');
    }
    
    protected function initializeMatrix(): void
    {
        $this->matrix = $this->createComplexMatrix([
            [1, 0],
            [0, 1]
        ]);
    }
    
    protected function updateMatrix(): void
    {
        if ($this->angle !== null) {
            $phase = new ComplexNumber(cos($this->angle), sin($this->angle));
            $this->matrix = [
                [new ComplexNumber(1, 0), new ComplexNumber(0, 0)],
                [new ComplexNumber(0, 0), $phase]
            ];
        }
    }
}

// Rotation-X Gate
class RotationXGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('rx');
    }
    
    protected function initializeMatrix(): void
    {
        $this->updateMatrix();
    }
    
    protected function updateMatrix(): void
    {
        if ($this->angle !== null) {
            $halfAngle = $this->angle / 2;
            $cosHalf = cos($halfAngle);
            $sinHalf = sin($halfAngle);
            
            $this->matrix = $this->createComplexMatrix([
                [$cosHalf, -$sinHalf],
                [-$sinHalf, $cosHalf]
            ]);
        }
    }
}

// Rotation-Y Gate
class RotationYGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('ry');
    }
    
    protected function initializeMatrix(): void
    {
        $this->updateMatrix();
    }
    
    protected function updateMatrix(): void
    {
        if ($this->angle !== null) {
            $halfAngle = $this->angle / 2;
            $cosHalf = cos($halfAngle);
            $sinHalf = sin($halfAngle);
            
            $this->matrix = $this->createComplexMatrix([
                [$cosHalf, -$sinHalf],
                [$sinHalf, $cosHalf]
            ]);
        }
    }
}

// Rotation-Z Gate
class RotationZGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('rz');
    }
    
    protected function initializeMatrix(): void
    {
        $this->updateMatrix();
    }
    
    protected function updateMatrix(): void
    {
        if ($this->angle !== null) {
            $halfAngle = $this->angle / 2;
            $cosHalf = cos($halfAngle);
            $sinHalf = sin($halfAngle);
            
            $this->matrix = [
                [new ComplexNumber($cosHalf, -$sinHalf), new ComplexNumber(0, 0)],
                [new ComplexNumber(0, 0), new ComplexNumber($cosHalf, $sinHalf)]
            ];
        }
    }
}

// SWAP Gate
class SwapGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('swap');
    }
    
    protected function initializeMatrix(): void
    {
        // SWAP is a 2-qubit gate
        $this->matrix = [];
    }
    
    protected function updateMatrix(): void
    {
        // SWAP gate doesn't depend on angle
    }
    
    public function applyTwoQubits(Qubit $qubit1, Qubit $qubit2): void
    {
        if ($qubit1->isMeasured() || $qubit2->isMeasured()) {
            return;
        }
        
        // Swap the states of the two qubits
        $tempAlpha = $qubit1->getAlpha();
        $tempBeta = $qubit1->getBeta();
        
        $qubit1->setAlpha($qubit2->getAlpha());
        $qubit1->setBeta($qubit2->getBeta());
        
        $qubit2->setAlpha($tempAlpha);
        $qubit2->setBeta($tempBeta);
    }
}

// Toffoli Gate (Controlled-Controlled-NOT)
class ToffoliGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('toffoli');
    }
    
    protected function initializeMatrix(): void
    {
        // Toffoli is a 3-qubit gate
        $this->matrix = [];
    }
    
    protected function updateMatrix(): void
    {
        // Toffoli gate doesn't depend on angle
    }
    
    public function applyThreeQubits(Qubit $control1, Qubit $control2, Qubit $target): void
    {
        if ($control1->isMeasured() || $control2->isMeasured() || $target->isMeasured()) {
            return;
        }
        
        // If both control qubits are |1⟩, flip the target qubit
        if ($control1->getProbability1() > 0.5 && $control2->getProbability1() > 0.5) {
            $pauliX = new PauliXGate();
            $pauliX->apply($target);
        }
    }
}

// Fredkin Gate (Controlled-SWAP)
class FredkinGate extends QuantumGate
{
    public function __construct()
    {
        parent::__construct('fredkin');
    }
    
    protected function initializeMatrix(): void
    {
        // Fredkin is a 3-qubit gate
        $this->matrix = [];
    }
    
    protected function updateMatrix(): void
    {
        // Fredkin gate doesn't depend on angle
    }
    
    public function applyThreeQubits(Qubit $control, Qubit $target1, Qubit $target2): void
    {
        if ($control->isMeasured() || $target1->isMeasured() || $target2->isMeasured()) {
            return;
        }
        
        // If control qubit is |1⟩, swap the target qubits
        if ($control->getProbability1() > 0.5) {
            $swapGate = new SwapGate();
            $swapGate->applyTwoQubits($target1, $target2);
        }
    }
}

// Quantum Circuit
class QuantumCircuit
{
    private string $name;
    private array $qubits;
    private array $gates;
    private array $operations;
    
    public function __construct(string $name, array $qubits)
    {
        $this->name = $name;
        $this->qubits = $qubits;
        $this->gates = [];
        $this->operations = [];
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getQubits(): array
    {
        return $this->qubits;
    }
    
    public function addGate(QuantumGate $gate, array $qubitIndices, array $params = []): void
    {
        $operation = [
            'gate' => $gate,
            'qubits' => $qubitIndices,
            'params' => $params
        ];
        
        $this->operations[] = $operation;
        $this->gates[] = $gate;
        
        echo "Added gate {$gate->getName()} to circuit {$this->name}\n";
    }
    
    public function execute(array $qubits): void
    {
        echo "Executing quantum circuit: {$this->name}\n";
        
        foreach ($this->operations as $operation) {
            $gate = $operation['gate'];
            $qubitIndices = $operation['qubits'];
            $params = $operation['params'];
            
            if (isset($params['angle'])) {
                $gate->setAngle($params['angle']);
            }
            
            $selectedQubits = [];
            foreach ($qubitIndices as $index) {
                if (isset($qubits[$index])) {
                    $selectedQubits[] = $qubits[$index];
                }
            }
            
            if (count($selectedQubits) === 1) {
                $gate->apply($selectedQubits[0]);
            } elseif (count($selectedQubits) === 2) {
                if ($gate instanceof CNOTGate) {
                    $gate->applyTwoQubits($selectedQubits[0], $selectedQubits[1]);
                } elseif ($gate instanceof SwapGate) {
                    $gate->applyTwoQubits($selectedQubits[0], $selectedQubits[1]);
                }
            } elseif (count($selectedQubits) === 3) {
                if ($gate instanceof ToffoliGate) {
                    $gate->applyThreeQubits($selectedQubits[0], $selectedQubits[1], $selectedQubits[2]);
                } elseif ($gate instanceof FredkinGate) {
                    $gate->applyThreeQubits($selectedQubits[0], $selectedQubits[1], $selectedQubits[2]);
                }
            }
        }
    }
    
    public function getOperations(): array
    {
        return $this->operations;
    }
    
    public function getGateCount(): int
    {
        return count($this->gates);
    }
    
    public function getDepth(): int
    {
        // Simplified depth calculation
        return count($this->operations);
    }
}

// Quantum Simulator
class QuantumSimulator
{
    private array $measurementHistory;
    
    public function __construct()
    {
        $this->measurementHistory = [];
    }
    
    public function measure(Qubit $qubit): int
    {
        if ($qubit->isMeasured()) {
            return $qubit->getMeasurementResult();
        }
        
        $prob0 = $qubit->getProbability0();
        $prob1 = $qubit->getProbability1();
        
        // Quantum measurement based on probability amplitudes
        $random = mt_rand() / mt_getrandmax();
        
        $result = $random < $prob0 ? 0 : 1;
        
        $qubit->measure($result);
        
        $this->measurementHistory[] = [
            'qubit_id' => $qubit->getId(),
            'result' => $result,
            'prob0' => $prob0,
            'prob1' => $prob1,
            'timestamp' => microtime(true)
        ];
        
        return $result;
    }
    
    public function getState(Qubit $qubit): array
    {
        $alpha = $qubit->getAlpha();
        $beta = $qubit->getBeta();
        
        return [
            'alpha' => $alpha,
            'beta' => $beta,
            'alpha_prob' => $alpha->magnitudeSquared(),
            'beta_prob' => $beta->magnitudeSquared(),
            'measured' => $qubit->isMeasured(),
            'measurement_result' => $qubit->getMeasurementResult()
        ];
    }
    
    public function getMeasurementHistory(): array
    {
        return $this->measurementHistory;
    }
    
    public function calculateEntanglement(array $qubits): float
    {
        if (count($qubits) !== 2) {
            return 0;
        }
        
        // Simplified entanglement calculation
        $qubit1 = $qubits[0];
        $qubit2 = $qubits[1];
        
        if ($qubit1->isMeasured() || $qubit2->isMeasured()) {
            return 0;
        }
        
        // Calculate correlation between measurement probabilities
        $correlation = abs($qubit1->getProbability0() - $qubit2->getProbability0());
        
        return $correlation;
    }
    
    public function calculateFidelity(Qubit $qubit1, Qubit $qubit2): float
    {
        $state1 = $this->getState($qubit1);
        $state2 = $this->getState($qubit2);
        
        // Fidelity calculation using inner product
        $innerProduct = $state1['alpha']->multiply($state2['alpha']->conjugate())
            ->add($state1['beta']->multiply($state2['beta']->conjugate()));
        
        return $innerProduct->magnitude();
    }
}

// Quantum Algorithms
class QuantumAlgorithms
{
    private array $algorithms;
    
    public function __construct()
    {
        $this->algorithms = [
            'grover' => new GroverAlgorithm(),
            'shor' => new ShorAlgorithm(),
            'deutsch_jozsa' => new DeutschJozsaAlgorithm(),
            'bernstein_vazirani' => new BernsteinVaziraniAlgorithm(),
            'quantum_fourier_transform' => new QuantumFourierTransform()
        ];
    }
    
    public function run(string $algorithmName, array $params, QuantumComputingFramework $framework): array
    {
        if (!isset($this->algorithms[$algorithmName])) {
            throw new Exception("Quantum algorithm not found: $algorithmName");
        }
        
        $algorithm = $this->algorithms[$algorithmName];
        
        echo "Running quantum algorithm: $algorithmName\n";
        
        return $algorithm->execute($params, $framework);
    }
    
    public function getAlgorithm(string $name): ?QuantumAlgorithm
    {
        return $this->algorithms[$name] ?? null;
    }
    
    public function getAlgorithms(): array
    {
        return array_keys($this->algorithms);
    }
}

// Quantum Algorithm Base Class
abstract class QuantumAlgorithm
{
    protected string $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    abstract public function execute(array $params, QuantumComputingFramework $framework): array;
}

// Grover's Algorithm
class GroverAlgorithm extends QuantumAlgorithm
{
    public function __construct()
    {
        parent::__construct('grover');
    }
    
    public function execute(array $params, QuantumComputingFramework $framework): array
    {
        $n = $params['n'] ?? 3; // Number of qubits
        $target = $params['target'] ?? 5; // Target state (0-7 for 3 qubits)
        
        echo "Grover's Algorithm - Searching for state: $target\n";
        
        // Create qubits
        $qubits = [];
        for ($i = 0; $i < $n; $i++) {
            $qubits[] = $framework->createQubit("g_qubit_$i");
        }
        
        // Initialize superposition
        foreach ($qubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
        
        // Grover iterations
        $iterations = floor(M_PI / 4 * sqrt(pow(2, $n)));
        
        echo "Running $iterations Grover iterations\n";
        
        for ($i = 0; $i < $iterations; $i++) {
            // Oracle phase flip
            $this->applyOracle($target, $qubits, $framework);
            
            // Diffusion operator
            $this->applyDiffusion($qubits, $framework);
        }
        
        // Measure results
        $results = [];
        foreach ($qubits as $i => $qubit) {
            $results[] = $framework->measureQubit($qubit->getId());
        }
        
        // Convert binary result to decimal
        $foundState = 0;
        for ($i = 0; $i < count($results); $i++) {
            $foundState += $results[$i] * pow(2, count($results) - 1 - $i);
        }
        
        echo "Grover's result: $foundState (target was: $target)\n";
        
        return [
            'algorithm' => 'grover',
            'target' => $target,
            'found' => $foundState,
            'success' => $foundState === $target,
            'iterations' => $iterations,
            'measurements' => $results
        ];
    }
    
    private function applyOracle(int $target, array $qubits, QuantumComputingFramework $framework): void
    {
        // Simplified oracle - apply phase flip to target state
        $targetBinary = str_pad(decbin($target), count($qubits), '0', STR_PAD_LEFT);
        
        echo "  Applying oracle for target: $targetBinary\n";
        
        // In a real implementation, this would apply a phase flip only to the target state
        // For simplicity, we'll apply a Z gate to the last qubit if the target is odd
        if ($target % 2 === 1) {
            $framework->applyGate('pauli_z', [$qubits[count($qubits) - 1]->getId()]);
        }
    }
    
    private function applyDiffusion(array $qubits, QuantumComputingFramework $framework): void
    {
        echo "  Applying diffusion operator\n";
        
        // Apply Hadamard gates
        foreach ($qubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
        
        // Apply X gates
        foreach ($qubits as $qubit) {
            $framework->applyGate('pauli_x', [$qubit->getId()]);
        }
        
        // Apply multi-qubit Z gate (simplified)
        if (count($qubits) >= 2) {
            $framework->applyGate('pauli_z', [$qubits[0]->getId()]);
        }
        
        // Apply X gates again
        foreach ($qubits as $qubit) {
            $framework->applyGate('pauli_x', [$qubit->getId()]);
        }
        
        // Apply Hadamard gates again
        foreach ($qubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
    }
}

// Shor's Algorithm (simplified)
class ShorAlgorithm extends QuantumAlgorithm
{
    public function __construct()
    {
        parent::__construct('shor');
    }
    
    public function execute(array $params, QuantumComputingFramework $framework): array
    {
        $N = $params['number'] ?? 15; // Number to factor
        $a = $params['base'] ?? 2; // Base for modular exponentiation
        
        echo "Shor's Algorithm - Factoring: $N\n";
        
        // Simplified Shor's algorithm - just demonstrate the concept
        $qubits = [];
        $n = ceil(log2($N));
        
        for ($i = 0; $i < $n; $i++) {
            $qubits[] = $framework->createQubit("s_qubit_$i");
        }
        
        // Initialize superposition
        foreach ($qubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
        
        // Apply quantum Fourier transform (simplified)
        echo "  Applying Quantum Fourier Transform\n";
        for ($i = 0; $i < count($qubits); $i++) {
            $framework->applyGate('hadamard', [$qubits[$i]->getId()]);
        }
        
        // Measure
        $results = [];
        foreach ($qubits as $qubit) {
            $results[] = $framework->measureQubit($qubit->getId());
        }
        
        // In a real implementation, we would use the measurement results
        // to find the period and then calculate the factors
        $factors = $this->findFactors($N);
        
        echo "Shor's result: Factors of $N are " . implode(', ', $factors) . "\n";
        
        return [
            'algorithm' => 'shor',
            'number' => $N,
            'factors' => $factors,
            'base' => $a,
            'measurements' => $results
        ];
    }
    
    private function findFactors(int $N): array
    {
        // Simplified factoring for demonstration
        $factors = [];
        
        for ($i = 2; $i <= sqrt($N); $i++) {
            if ($N % $i === 0) {
                $factors[] = $i;
                $factors[] = $N / $i;
                break;
            }
        }
        
        if (empty($factors)) {
            $factors = [$N]; // Prime number
        }
        
        sort($factors);
        return $factors;
    }
}

// Deutsch-Jozsa Algorithm
class DeutschJozsaAlgorithm extends QuantumAlgorithm
{
    public function __construct()
    {
        parent::__construct('deutsch_jozsa');
    }
    
    public function execute(array $params, QuantumComputingFramework $framework): array
    {
        $n = $params['n'] ?? 2; // Number of input qubits
        $function = $params['function'] ?? 'balanced'; // 'constant' or 'balanced'
        
        echo "Deutsch-Jozsa Algorithm - Determining if function is constant or balanced\n";
        
        // Create qubits
        $inputQubits = [];
        for ($i = 0; $i < $n; $i++) {
            $inputQubits[] = $framework->createQubit("dj_input_$i");
        }
        
        $outputQubit = $framework->createQubit("dj_output");
        
        // Initialize output qubit to |1⟩
        $outputQubit->setAlpha(new ComplexNumber(0, 0));
        $outputQubit->setBeta(new ComplexNumber(1, 0));
        
        // Apply Hadamard to all qubits
        foreach ($inputQubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
        $framework->applyGate('hadamard', [$outputQubit->getId()]);
        
        // Apply oracle (simplified)
        echo "  Applying oracle function: $function\n";
        
        if ($function === 'balanced') {
            // Apply X gate to first input qubit
            $framework->applyGate('pauli_x', [$inputQubits[0]->getId()]);
        }
        
        // Apply Hadamard to input qubits again
        foreach ($inputQubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
        
        // Measure input qubits
        $results = [];
        foreach ($inputQubits as $qubit) {
            $results[] = $framework->measureQubit($qubit->getId());
        }
        
        // Determine if function is constant or balanced
        $allZeros = true;
        foreach ($results as $result) {
            if ($result !== 0) {
                $allZeros = false;
                break;
            }
        }
        
        $determined = $allZeros ? 'constant' : 'balanced';
        
        echo "Deutsch-Jozsa result: Function is $determined\n";
        
        return [
            'algorithm' => 'deutsch_jozsa',
            'n' => $n,
            'function' => $function,
            'determined' => $determined,
            'correct' => $determined === $function,
            'measurements' => $results
        ];
    }
}

// Bernstein-Vazirani Algorithm
class BernsteinVaziraniAlgorithm extends QuantumAlgorithm
{
    public function __construct()
    {
        parent::__construct('bernstein_vazirani');
    }
    
    public function execute(array $params, QuantumComputingFramework $framework): array
    {
        $n = $params['n'] ?? 3; // Number of qubits
        $secret = $params['secret'] ?? 5; // Secret string (as integer)
        
        echo "Bernstein-Vazirani Algorithm - Finding secret string\n";
        
        // Create qubits
        $qubits = [];
        for ($i = 0; $i < $n; $i++) {
            $qubits[] = $framework->createQubit("bv_qubit_$i");
        }
        
        // Initialize superposition
        foreach ($qubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
        
        // Apply oracle (simplified - applies Z gates based on secret)
        echo "  Applying oracle with secret: " . str_pad(decbin($secret), $n, '0', STR_PAD_LEFT) . "\n";
        
        $secretBinary = str_pad(decbin($secret), $n, '0', STR_PAD_LEFT);
        for ($i = 0; $i < $n; $i++) {
            if ($secretBinary[$i] === '1') {
                $framework->applyGate('pauli_z', [$qubits[$i]->getId()]);
            }
        }
        
        // Apply Hadamard gates again
        foreach ($qubits as $qubit) {
            $framework->applyGate('hadamard', [$qubit->getId()]);
        }
        
        // Measure
        $results = [];
        foreach ($qubits as $qubit) {
            $results[] = $framework->measureQubit($qubit->getId());
        }
        
        // Convert binary result to decimal
        $foundSecret = 0;
        for ($i = 0; $i < count($results); $i++) {
            $foundSecret += $results[$i] * pow(2, count($results) - 1 - $i);
        }
        
        echo "Bernstein-Vazirani result: $foundSecret (secret was: $secret)\n";
        
        return [
            'algorithm' => 'bernstein_vazirani',
            'n' => $n,
            'secret' => $secret,
            'found' => $foundSecret,
            'success' => $foundSecret === $secret,
            'measurements' => $results
        ];
    }
}

// Quantum Fourier Transform
class QuantumFourierTransform extends QuantumAlgorithm
{
    public function __construct()
    {
        parent::__construct('quantum_fourier_transform');
    }
    
    public function execute(array $params, QuantumComputingFramework $framework): array
    {
        $n = $params['n'] ?? 3; // Number of qubits
        $input = $params['input'] ?? 5; // Input state (as integer)
        
        echo "Quantum Fourier Transform - Transforming state: $input\n";
        
        // Create qubits
        $qubits = [];
        for ($i = 0; $i < $n; $i++) {
            $qubits[] = $framework->createQubit("qft_qubit_$i");
        }
        
        // Prepare input state
        $inputBinary = str_pad(decbin($input), $n, '0', STR_PAD_LEFT);
        for ($i = 0; $i < $n; $i++) {
            if ($inputBinary[$i] === '1') {
                $qubits[$i]->setAlpha(new ComplexNumber(0, 0));
                $qubits[$i]->setBeta(new ComplexNumber(1, 0));
            }
        }
        
        // Apply QFT
        echo "  Applying Quantum Fourier Transform\n";
        
        for ($i = 0; $i < $n; $i++) {
            $framework->applyGate('hadamard', [$qubits[$i]->getId()]);
            
            for ($j = $i + 1; $j < $n; $j++) {
                $angle = M_PI / pow(2, $j - $i);
                $framework->applyGate('phase', [$qubits[$j]->getId()], ['angle' => $angle]);
            }
        }
        
        // Measure
        $results = [];
        foreach ($qubits as $qubit) {
            $results[] = $framework->measureQubit($qubit->getId());
        }
        
        echo "QFT completed\n";
        
        return [
            'algorithm' => 'quantum_fourier_transform',
            'n' => $n,
            'input' => $input,
            'output' => $results,
            'transformed' => true
        ];
    }
}

// Quantum Computing Examples
class QuantumComputingExamples
{
    public function demonstrateBasicQuantumConcepts(): void
    {
        echo "Basic Quantum Computing Concepts Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        $quantum = new QuantumComputingFramework();
        
        // Create qubits
        echo "Creating qubits:\n";
        $qubit1 = $quantum->createQubit('q1', 1, 0); // |0⟩ state
        $qubit2 = $quantum->createQubit('q2', 0, 1); // |1⟩ state
        $qubit3 = $quantum->createQubit('q3', 1/sqrt(2), 1/sqrt(2)); // Superposition
        
        // Show quantum states
        echo "\nInitial quantum states:\n";
        $quantum->getQuantumState('q1');
        $quantum->getQuantumState('q2');
        $quantum->getQuantumState('q3');
        
        // Apply quantum gates
        echo "\nApplying quantum gates:\n";
        
        // Apply NOT gate to |0⟩
        $quantum->applyGate('pauli_x', ['q1']);
        echo "After Pauli-X on q1:\n";
        $quantum->getQuantumState('q1');
        
        // Apply Hadamard to |1⟩
        $quantum->applyGate('hadamard', ['q2']);
        echo "After Hadamard on q2:\n";
        $quantum->getQuantumState('q2');
        
        // Apply Z gate to superposition
        $quantum->applyGate('pauli_z', ['q3']);
        echo "After Pauli-Z on q3:\n";
        $quantum->getQuantumState('q3');
        
        // Measure qubits
        echo "\nMeasuring qubits:\n";
        $result1 = $quantum->measureQubit('q1');
        $result2 = $quantum->measureQubit('q2');
        $result3 = $quantum->measureQubit('q3');
        
        echo "Measurement results: q1=$result1, q2=$result2, q3=$result3\n";
        
        // Show final states
        echo "\nFinal quantum states:\n";
        $quantum->getQuantumState('q1');
        $quantum->getQuantumState('q2');
        $quantum->getQuantumState('q3');
    }
    
    public function demonstrateQuantumGates(): void
    {
        echo "\nQuantum Gates Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $quantum = new QuantumComputingFramework();
        
        // Create qubits for gate demonstrations
        $qubits = [];
        for ($i = 0; $i < 4; $i++) {
            $qubits[] = $quantum->createQubit("gate_qubit_$i");
        }
        
        echo "Demonstrating quantum gates:\n";
        
        // Pauli gates
        echo "\nPauli gates:\n";
        $quantum->applyGate('pauli_x', [$qubits[0]->getId()]);
        echo "Pauli-X on {$qubits[0]->getId()}: ";
        $quantum->getQuantumState($qubits[0]->getId());
        
        $quantum->applyGate('pauli_y', [$qubits[1]->getId()]);
        echo "Pauli-Y on {$qubits[1]->getId()}: ";
        $quantum->getQuantumState($qubits[1]->getId());
        
        $quantum->applyGate('pauli_z', [$qubits[2]->getId()]);
        echo "Pauli-Z on {$qubits[2]->getId()}: ";
        $quantum->getQuantumState($qubits[2]->getId());
        
        // Rotation gates
        echo "\nRotation gates:\n";
        $quantum->applyGate('rx', [$qubits[0]->getId()], ['angle' => M_PI / 4]);
        echo "RX(π/4) on {$qubits[0]->getId()}: ";
        $quantum->getQuantumState($qubits[0]->getId());
        
        $quantum->applyGate('ry', [$qubits[1]->getId()], ['angle' => M_PI / 2]);
        echo "RY(π/2) on {$qubits[1]->getId()}: ";
        $quantum->getQuantumState($qubits[1]->getId());
        
        $quantum->applyGate('rz', [$qubits[2]->getId()], ['angle' => M_PI / 3]);
        echo "RZ(π/3) on {$qubits[2]->getId()}: ";
        $quantum->getQuantumState($qubits[2]->getId());
        
        // Phase gate
        echo "\nPhase gate:\n";
        $quantum->applyGate('phase', [$qubits[3]->getId()], ['angle' => M_PI / 2]);
        echo "Phase(π/2) on {$qubits[3]->getId()}: ";
        $quantum->getQuantumState($qubits[3]->getId());
        
        // Multi-qubit gates
        echo "\nMulti-qubit gates:\n";
        
        // Create new qubits for multi-qubit operations
        $control1 = $quantum->createQubit('control1');
        $control2 = $quantum->createQubit('control2');
        $target1 = $quantum->createQubit('target1');
        $target2 = $quantum->createQubit('target2');
        
        // Set control qubits to |1⟩
        $control1->setAlpha(new ComplexNumber(0, 0));
        $control1->setBeta(new ComplexNumber(1, 0));
        
        $control2->setAlpha(new ComplexNumber(0, 0));
        $control2->setBeta(new ComplexNumber(1, 0));
        
        // CNOT gate
        echo "CNOT gate:\n";
        $cnot = new CNOTGate();
        $cnot->applyTwoQubits($control1, $target1);
        echo "After CNOT (control=|1⟩, target=|0⟩): target state = ";
        $quantum->getQuantumState($target1->getId());
        
        // SWAP gate
        echo "\nSWAP gate:\n";
        $swap = new SwapGate();
        $swap->applyTwoQubits($target1, $target2);
        echo "After SWAP: states exchanged\n";
        
        // Toffoli gate
        echo "\nToffoli gate:\n";
        $toffoli = new ToffoliGate();
        $toffoli->applyThreeQubits($control1, $control2, $target1);
        echo "After Toffoli (controls=|1⟩,|1⟩, target=|0⟩): target state = ";
        $quantum->getQuantumState($target1->getId());
    }
    
    public function demonstrateQuantumCircuits(): void
    {
        echo "\nQuantum Circuits Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $quantum = new QuantumComputingFramework();
        
        // Create a quantum circuit for Bell state
        echo "Creating Bell state circuit:\n";
        
        $bellQubits = [
            $quantum->createQubit('bell_qubit_0'),
            $quantum->createQubit('bell_qubit_1')
        ];
        
        $bellCircuit = $quantum->createQuantumCircuit('bell_state', $bellQubits);
        
        // Add gates to create Bell state
        $bellCircuit->addGate(new HadamardGate(), [0]);
        $bellCircuit->addGate(new CNOTGate(), [0, 1]);
        
        // Execute circuit
        $bellCircuit->execute($bellQubits);
        
        echo "Bell state created:\n";
        foreach ($bellQubits as $i => $qubit) {
            echo "  Qubit $i: ";
            $quantum->getQuantumState($qubit->getId());
        }
        
        // Test entanglement
        $simulator = $quantum->getSimulator();
        $entanglement = $simulator->calculateEntanglement($bellQubits);
        echo "Entanglement measure: " . round($entanglement, 4) . "\n";
        
        // Create GHZ state circuit
        echo "\nCreating GHZ state circuit:\n";
        
        $ghzQubits = [
            $quantum->createQubit('ghz_qubit_0'),
            $quantum->createQubit('ghz_qubit_1'),
            $quantum->createQubit('ghz_qubit_2')
        ];
        
        $ghzCircuit = $quantum->createQuantumCircuit('ghz_state', $ghzQubits);
        
        // Add gates to create GHZ state
        $ghzCircuit->addGate(new HadamardGate(), [0]);
        $ghzCircuit->addGate(new CNOTGate(), [0, 1]);
        $ghzCircuit->addGate(new CNOTGate(), [1, 2]);
        
        // Execute circuit
        $ghzCircuit->execute($ghzQubits);
        
        echo "GHZ state created:\n";
        foreach ($ghzQubits as $i => $qubit) {
            echo "  Qubit $i: ";
            $quantum->getQuantumState($qubit->getId());
        }
        
        // Measure GHZ state
        echo "\nMeasuring GHZ state:\n";
        $ghzResults = [];
        foreach ($ghzQubits as $qubit) {
            $ghzResults[] = $quantum->measureQubit($qubit->getId());
        }
        
        echo "GHZ measurement results: " . implode(', ', $ghzResults) . "\n";
        
        // Check if all measurements are the same (GHZ property)
        $allSame = count(array_unique($ghzResults)) === 1;
        echo "All measurements same: " . ($allSame ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateQuantumAlgorithms(): void
    {
        echo "\nQuantum Algorithms Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $quantum = new QuantumComputingFramework();
        
        echo "Available quantum algorithms:\n";
        $algorithms = $quantum->getAlgorithms();
        foreach ($algorithms as $algorithm) {
            echo "  - $algorithm\n";
        }
        
        // Run Grover's algorithm
        echo "\nRunning Grover's Algorithm:\n";
        $groverResult = $quantum->runQuantumAlgorithm('grover', [
            'n' => 3,
            'target' => 5
        ]);
        
        echo "Grover's Algorithm Results:\n";
        echo "  Target: {$groverResult['target']}\n";
        echo "  Found: {$groverResult['found']}\n";
        echo "  Success: " . ($groverResult['success'] ? 'Yes' : 'No') . "\n";
        echo "  Iterations: {$groverResult['iterations']}\n";
        echo "  Measurements: " . implode(', ', $groverResult['measurements']) . "\n";
        
        // Run Deutsch-Jozsa algorithm
        echo "\nRunning Deutsch-Jozsa Algorithm:\n";
        $djResult = $quantum->runQuantumAlgorithm('deutsch_jozsa', [
            'n' => 2,
            'function' => 'balanced'
        ]);
        
        echo "Deutsch-Jozsa Results:\n";
        echo "  Function type: {$djResult['function']}\n";
        echo "  Determined: {$djResult['determined']}\n";
        echo "  Correct: " . ($djResult['correct'] ? 'Yes' : 'No') . "\n";
        echo "  Measurements: " . implode(', ', $djResult['measurements']) . "\n";
        
        // Run Bernstein-Vazirani algorithm
        echo "\nRunning Bernstein-Vazirani Algorithm:\n";
        $bvResult = $quantum->runQuantumAlgorithm('bernstein_vazirani', [
            'n' => 3,
            'secret' => 5
        ]);
        
        echo "Bernstein-Vazirani Results:\n";
        echo "  Secret: {$bvResult['secret']}\n";
        echo "  Found: {$bvResult['found']}\n";
        echo "  Success: " . ($bvResult['success'] ? 'Yes' : 'No') . "\n";
        echo "  Measurements: " . implode(', ', $bvResult['measurements']) . "\n";
        
        // Run Shor's algorithm (simplified)
        echo "\nRunning Shor's Algorithm (simplified):\n";
        $shorResult = $quantum->runQuantumAlgorithm('shor', [
            'number' => 15,
            'base' => 2
        ]);
        
        echo "Shor's Results:\n";
        echo "  Number: {$shorResult['number']}\n";
        echo "  Factors: " . implode(', ', $shorResult['factors']) . "\n";
        echo "  Base: {$shorResult['base']}\n";
        
        // Run Quantum Fourier Transform
        echo "\nRunning Quantum Fourier Transform:\n";
        $qftResult = $quantum->runQuantumAlgorithm('quantum_fourier_transform', [
            'n' => 3,
            'input' => 5
        ]);
        
        echo "QFT Results:\n";
        echo "  Input: {$qftResult['input']}\n";
        echo "  Output: " . implode(', ', $qftResult['output']) . "\n";
        echo "  Transformed: " . ($qftResult['transformed'] ? 'Yes' : 'No') . "\n";
    }
    
    public function demonstrateQuantumSimulation(): void
    {
        echo "\nQuantum Simulation Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $quantum = new QuantumComputingFramework();
        $simulator = $quantum->getSimulator();
        
        // Create qubits for simulation
        echo "Creating quantum system for simulation:\n";
        
        $systemQubits = [
            $quantum->createQubit('sys_qubit_0', 1/sqrt(2), 1/sqrt(2)),
            $quantum->createQubit('sys_qubit_1', 1/sqrt(2), -1/sqrt(2)),
            $quantum->createQubit('sys_qubit_2', 1, 0)
        ];
        
        echo "Initial system states:\n";
        foreach ($systemQubits as $i => $qubit) {
            echo "  Qubit $i: ";
            $quantum->getQuantumState($qubit->getId());
        }
        
        // Simulate quantum evolution
        echo "\nSimulating quantum evolution:\n";
        
        for ($step = 0; $step < 5; $step++) {
            echo "  Evolution step $step:\n";
            
            // Apply random gates to simulate evolution
            $gates = ['hadamard', 'pauli_x', 'pauli_z', 'phase'];
            $randomGate = $gates[array_rand($gates)];
            $randomQubit = $systemQubits[array_rand($systemQubits)];
            
            $quantum->applyGate($randomGate, [$randomQubit->getId()]);
            
            // Show state after evolution
            echo "    Applied $randomGate to {$randomQubit->getId()}\n";
            echo "    New state: ";
            $quantum->getQuantumState($randomQubit->getId());
        }
        
        // Calculate system properties
        echo "\nSystem properties:\n";
        
        // Calculate entanglement between qubit pairs
        for ($i = 0; $i < count($systemQubits) - 1; $i++) {
            for ($j = $i + 1; $j < count($systemQubits); $j++) {
                $entanglement = $simulator->calculateEntanglement([$systemQubits[$i], $systemQubits[$j]]);
                echo "  Entanglement ($i, $j): " . round($entanglement, 4) . "\n";
            }
        }
        
        // Calculate fidelity between qubits
        for ($i = 0; $i < count($systemQubits) - 1; $i++) {
            for ($j = $i + 1; $j < count($systemQubits); $j++) {
                $fidelity = $simulator->calculateFidelity($systemQubits[$i], $systemQubits[$j]);
                echo "  Fidelity ($i, $j): " . round($fidelity, 4) . "\n";
            }
        }
        
        // Perform measurements
        echo "\nSystem measurements:\n";
        $measurementResults = [];
        foreach ($systemQubits as $i => $qubit) {
            $result = $quantum->measureQubit($qubit->getId());
            $measurementResults[] = $result;
            echo "  Qubit $i: $result\n";
        }
        
        // Show measurement history
        echo "\nMeasurement history:\n";
        $history = $simulator->getMeasurementHistory();
        echo "  Total measurements: " . count($history) . "\n";
        
        if (!empty($history)) {
            $lastMeasurement = end($history);
            echo "  Last measurement: qubit {$lastMeasurement['qubit_id']} = {$lastMeasurement['result']}\n";
            echo "  Probability (|0⟩): " . round($lastMeasurement['prob0'], 4) . "\n";
            echo "  Probability (|1⟩): " . round($lastMeasurement['prob1'], 4) . "\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nQuantum Computing Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Qubit Management:\n";
        echo "   • Always normalize qubit states\n";
        echo "   • Handle measurement collapse properly\n";
        echo "   • Use proper complex number arithmetic\n";
        echo "   • Track quantum vs classical states\n";
        echo "   • Implement proper error handling\n\n";
        
        echo "2. Quantum Gates:\n";
        echo "   • Use unitary matrices for gates\n";
        echo "   • Implement proper gate composition\n";
        echo "   • Handle multi-qubit gates correctly\n";
        echo "   • Use gate optimization techniques\n";
        echo "   • Validate gate implementations\n\n";
        
        echo "3. Quantum Circuits:\n";
        echo "   • Minimize circuit depth\n";
        echo "   • Use gate cancellation\n";
        echo "   • Implement proper circuit optimization\n";
        echo "   • Handle circuit decomposition\n";
        echo "   • Use circuit visualization\n\n";
        
        echo "4. Quantum Algorithms:\n";
        echo "   • Understand algorithm complexity\n";
        echo "   • Implement proper initialization\n";
        echo "   • Use correct oracle implementations\n";
        echo "   • Handle measurement interpretation\n";
        echo "   • Validate algorithm correctness\n\n";
        
        echo "5. Simulation:\n";
        echo "   • Use efficient state representation\n";
        echo "   • Implement proper measurement\n";
        echo "   • Handle numerical precision\n";
        echo "   • Use probabilistic methods\n";
        echo "   • Validate simulation accuracy";
    }
    
    public function runAllExamples(): void
    {
        echo "Quantum Computing Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateBasicQuantumConcepts();
        $this->demonstrateQuantumGates();
        $this->demonstrateQuantumCircuits();
        $this->demonstrateQuantumAlgorithms();
        $this->demonstrateQuantumSimulation();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runQuantumComputingDemo(): void
{
    $examples = new QuantumComputingExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runQuantumComputingDemo();
}
?>
