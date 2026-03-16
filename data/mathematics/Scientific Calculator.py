"""
Scientific Calculator
Advanced mathematical calculator with scientific functions.
"""

import math
import cmath
from typing import Union, List

class ScientificCalculator:
    """Advanced scientific calculator with comprehensive mathematical functions."""
    
    def __init__(self):
        """Initialize calculator with history."""
        self.history = []
        self.memory = 0
        self.angle_mode = 'degrees'  # 'degrees' or 'radians'
    
    def set_angle_mode(self, mode: str):
        """Set angle mode for trigonometric functions."""
        if mode.lower() in ['degrees', 'radians']:
            self.angle_mode = mode.lower()
        else:
            raise ValueError("Angle mode must be 'degrees' or 'radians'")
    
    def _convert_angle(self, angle: float) -> float:
        """Convert angle to radians if needed."""
        if self.angle_mode == 'degrees':
            return math.radians(angle)
        return angle
    
    def basic_operations(self, a: float, b: float, operation: str) -> float:
        """Perform basic arithmetic operations."""
        operations = {
            '+': a + b,
            '-': a - b,
            '*': a * b,
            '/': a / b if b != 0 else float('inf'),
            '^': a ** b,
            '%': a % b
        }
        
        if operation not in operations:
            raise ValueError(f"Unsupported operation: {operation}")
        
        result = operations[operation]
        self._add_to_history(f"{a} {operation} {b} = {result}")
        return result
    
    def trigonometric(self, angle: float, function: str) -> float:
        """Perform trigonometric calculations."""
        angle_rad = self._convert_angle(angle)
        
        functions = {
            'sin': math.sin(angle_rad),
            'cos': math.cos(angle_rad),
            'tan': math.tan(angle_rad),
            'asin': math.degrees(math.asin(angle_rad)) if self.angle_mode == 'degrees' else math.asin(angle_rad),
            'acos': math.degrees(math.acos(angle_rad)) if self.angle_mode == 'degrees' else math.acos(angle_rad),
            'atan': math.degrees(math.atan(angle_rad)) if self.angle_mode == 'degrees' else math.atan(angle_rad)
        }
        
        if function not in functions:
            raise ValueError(f"Unsupported function: {function}")
        
        result = functions[function]
        self._add_to_history(f"{function}({angle}) = {result}")
        return result
    
    def logarithmic(self, value: float, base: float = math.e, operation: str = 'log') -> float:
        """Perform logarithmic calculations."""
        if value <= 0:
            raise ValueError("Logarithm argument must be positive")
        
        if operation == 'log':
            result = math.log(value, base) if base != math.e else math.log(value)
        elif operation == 'log10':
            result = math.log10(value)
        elif operation == 'log2':
            result = math.log2(value)
        else:
            raise ValueError(f"Unsupported operation: {operation}")
        
        self._add_to_history(f"{operation}({value}) = {result}")
        return result
    
    def exponential(self, base: float, exponent: float = None) -> float:
        """Perform exponential calculations."""
        if exponent is None:
            result = math.exp(base)
            self._add_to_history(f"e^{base} = {result}")
        else:
            result = base ** exponent
            self._add_to_history(f"{base}^{exponent} = {result}")
        return result
    
    def square_root(self, value: Union[float, complex]) -> Union[float, complex]:
        """Calculate square root."""
        if value < 0:
            result = cmath.sqrt(value)
        else:
            result = math.sqrt(value)
        
        self._add_to_history(f"√{value} = {result}")
        return result
    
    def cube_root(self, value: float) -> float:
        """Calculate cube root."""
        result = value ** (1/3)
        self._add_to_history(f"∛{value} = {result}")
        return result
    
    def factorial(self, n: int) -> int:
        """Calculate factorial."""
        if n < 0:
            raise ValueError("Factorial is not defined for negative numbers")
        if n > 170:  # Prevent overflow
            raise ValueError("Factorial too large (max 170)")
        
        result = math.factorial(n)
        self._add_to_history(f"{n}! = {result}")
        return result
    
    def combinations(self, n: int, k: int) -> int:
        """Calculate combinations (n choose k)."""
        if n < 0 or k < 0 or k > n:
            raise ValueError("Invalid values for combinations")
        
        result = math.comb(n, k)
        self._add_to_history(f"C({n},{k}) = {result}")
        return result
    
    def permutations(self, n: int, k: int) -> int:
        """Calculate permutations (n P k)."""
        if n < 0 or k < 0 or k > n:
            raise ValueError("Invalid values for permutations")
        
        result = math.perm(n, k)
        self._add_to_history(f"P({n},{k}) = {result}")
        return result
    
    def statistics(self, data: List[float], operation: str) -> float:
        """Calculate statistical measures."""
        if not data:
            raise ValueError("Data list cannot be empty")
        
        if operation == 'mean':
            result = sum(data) / len(data)
        elif operation == 'median':
            sorted_data = sorted(data)
            n = len(sorted_data)
            if n % 2 == 0:
                result = (sorted_data[n//2 - 1] + sorted_data[n//2]) / 2
            else:
                result = sorted_data[n//2]
        elif operation == 'mode':
            from collections import Counter
            counts = Counter(data)
            max_count = max(counts.values())
            result = [x for x, count in counts.items() if count == max_count]
            result = result[0] if len(result) == 1 else result
        elif operation == 'std':
            mean = sum(data) / len(data)
            variance = sum((x - mean) ** 2 for x in data) / len(data)
            result = math.sqrt(variance)
        elif operation == 'var':
            mean = sum(data) / len(data)
            result = sum((x - mean) ** 2 for x in data) / len(data)
        else:
            raise ValueError(f"Unsupported operation: {operation}")
        
        self._add_to_history(f"{operation}({data}) = {result}")
        return result
    
    def geometry(self, operation: str, **kwargs) -> float:
        """Calculate geometric formulas."""
        if operation == 'circle_area':
            radius = kwargs.get('radius')
            if radius is None:
                raise ValueError("Radius is required")
            result = math.pi * radius ** 2
        
        elif operation == 'circle_circumference':
            radius = kwargs.get('radius')
            if radius is None:
                raise ValueError("Radius is required")
            result = 2 * math.pi * radius
        
        elif operation == 'sphere_volume':
            radius = kwargs.get('radius')
            if radius is None:
                raise ValueError("Radius is required")
            result = (4/3) * math.pi * radius ** 3
        
        elif operation == 'sphere_surface':
            radius = kwargs.get('radius')
            if radius is None:
                raise ValueError("Radius is required")
            result = 4 * math.pi * radius ** 2
        
        elif operation == 'rectangle_area':
            length = kwargs.get('length')
            width = kwargs.get('width')
            if length is None or width is None:
                raise ValueError("Length and width are required")
            result = length * width
        
        elif operation == 'triangle_area':
            base = kwargs.get('base')
            height = kwargs.get('height')
            if base is None or height is None:
                raise ValueError("Base and height are required")
            result = 0.5 * base * height
        
        else:
            raise ValueError(f"Unsupported operation: {operation}")
        
        self._add_to_history(f"{operation}({kwargs}) = {result}")
        return result
    
    def physics(self, operation: str, **kwargs) -> float:
        """Calculate physics formulas."""
        if operation == 'kinetic_energy':
            mass = kwargs.get('mass')
            velocity = kwargs.get('velocity')
            if mass is None or velocity is None:
                raise ValueError("Mass and velocity are required")
            result = 0.5 * mass * velocity ** 2
        
        elif operation == 'potential_energy':
            mass = kwargs.get('mass')
            height = kwargs.get('height')
            gravity = kwargs.get('gravity', 9.81)
            if mass is None or height is None:
                raise ValueError("Mass and height are required")
            result = mass * gravity * height
        
        elif operation == 'force':
            mass = kwargs.get('mass')
            acceleration = kwargs.get('acceleration')
            if mass is None or acceleration is None:
                raise ValueError("Mass and acceleration are required")
            result = mass * acceleration
        
        elif operation == 'momentum':
            mass = kwargs.get('mass')
            velocity = kwargs.get('velocity')
            if mass is None or velocity is None:
                raise ValueError("Mass and velocity are required")
            result = mass * velocity
        
        else:
            raise ValueError(f"Unsupported operation: {operation}")
        
        self._add_to_history(f"{operation}({kwargs}) = {result}")
        return result
    
    def memory_operations(self, operation: str, value: float = None) -> Union[float, None]:
        """Memory operations."""
        if operation == 'store':
            self.memory = value
            self._add_to_history(f"Memory store: {value}")
            return None
        elif operation == 'recall':
            self._add_to_history(f"Memory recall: {self.memory}")
            return self.memory
        elif operation == 'clear':
            self.memory = 0
            self._add_to_history("Memory cleared")
            return None
        elif operation == 'add':
            self.memory += value
            self._add_to_history(f"Memory add: {value} (total: {self.memory})")
            return None
        else:
            raise ValueError(f"Unsupported memory operation: {operation}")
    
    def _add_to_history(self, calculation: str):
        """Add calculation to history."""
        self.history.append(calculation)
        if len(self.history) > 100:  # Limit history size
            self.history.pop(0)
    
    def get_history(self, limit: int = 10) -> List[str]:
        """Get calculation history."""
        return self.history[-limit:]
    
    def clear_history(self):
        """Clear calculation history."""
        self.history = []
    
    def convert_units(self, value: float, from_unit: str, to_unit: str) -> float:
        """Unit conversion."""
        conversions = {
            # Temperature
            ('celsius', 'fahrenheit'): value * 9/5 + 32,
            ('fahrenheit', 'celsius'): (value - 32) * 5/9,
            ('celsius', 'kelvin'): value + 273.15,
            ('kelvin', 'celsius'): value - 273.15,
            
            # Length
            ('meter', 'foot'): value * 3.28084,
            ('foot', 'meter'): value / 3.28084,
            ('meter', 'inch'): value * 39.3701,
            ('inch', 'meter'): value / 39.3701,
            ('kilometer', 'mile'): value * 0.621371,
            ('mile', 'kilometer'): value / 0.621371,
            
            # Weight
            ('kilogram', 'pound'): value * 2.20462,
            ('pound', 'kilogram'): value / 2.20462,
            ('gram', 'ounce'): value * 0.035274,
            ('ounce', 'gram'): value / 0.035274,
        }
        
        key = (from_unit.lower(), to_unit.lower())
        if key not in conversions:
            raise ValueError(f"Conversion from {from_unit} to {to_unit} not supported")
        
        result = conversions[key]
        self._add_to_history(f"{value} {from_unit} = {result} {to_unit}")
        return result

def main():
    """Interactive scientific calculator."""
    print("Scientific Calculator")
    print("=" * 40)
    print("Advanced mathematical calculator")
    print()
    
    calc = ScientificCalculator()
    
    while True:
        print("\nMain Menu:")
        print("1. Basic Operations (+, -, *, /, ^, %)")
        print("2. Trigonometric Functions")
        print("3. Logarithmic Functions")
        print("4. Exponential Functions")
        print("5. Roots and Powers")
        print("6. Factorial and Combinations")
        print("7. Statistics")
        print("8. Geometry")
        print("9. Physics")
        print("10. Unit Conversion")
        print("11. Memory Operations")
        print("12. View History")
        print("13. Settings")
        print("14. Exit")
        
        choice = input("\nSelect option (1-14): ").strip()
        
        try:
            if choice == "1":
                a = float(input("First number: "))
                b = float(input("Second number: "))
                op = input("Operation (+, -, *, /, ^, %): ")
                result = calc.basic_operations(a, b, op)
                print(f"Result: {result}")
            
            elif choice == "2":
                angle = float(input("Angle: "))
                func = input("Function (sin, cos, tan, asin, acos, atan): ")
                result = calc.trigonometric(angle, func)
                print(f"Result: {result}")
            
            elif choice == "3":
                value = float(input("Value: "))
                op = input("Operation (log, log10, log2): ")
                if op == 'log':
                    base = float(input("Base (default e): ") or math.e)
                    result = calc.logarithmic(value, base, op)
                else:
                    result = calc.logarithmic(value, operation=op)
                print(f"Result: {result}")
            
            elif choice == "4":
                base = float(input("Base: "))
                exp_input = input("Exponent (optional): ")
                if exp_input:
                    exponent = float(exp_input)
                    result = calc.exponential(base, exponent)
                else:
                    result = calc.exponential(base)
                print(f"Result: {result}")
            
            elif choice == "5":
                value = float(input("Value: "))
                op = input("Operation (sqrt, cbrt): ")
                if op == 'sqrt':
                    result = calc.square_root(value)
                elif op == 'cbrt':
                    result = calc.cube_root(value)
                else:
                    print("Invalid operation")
                    continue
                print(f"Result: {result}")
            
            elif choice == "6":
                op = input("Operation (factorial, combinations, permutations): ")
                if op == 'factorial':
                    n = int(input("Number: "))
                    result = calc.factorial(n)
                elif op == 'combinations':
                    n = int(input("n: "))
                    k = int(input("k: "))
                    result = calc.combinations(n, k)
                elif op == 'permutations':
                    n = int(input("n: "))
                    k = int(input("k: "))
                    result = calc.permutations(n, k)
                else:
                    print("Invalid operation")
                    continue
                print(f"Result: {result}")
            
            elif choice == "7":
                data_str = input("Enter numbers separated by spaces: ")
                data = [float(x) for x in data_str.split()]
                op = input("Operation (mean, median, mode, std, var): ")
                result = calc.statistics(data, op)
                print(f"Result: {result}")
            
            elif choice == "8":
                op = input("Operation (circle_area, circle_circumference, sphere_volume, sphere_surface, rectangle_area, triangle_area): ")
                if op in ['circle_area', 'circle_circumference', 'sphere_volume', 'sphere_surface']:
                    radius = float(input("Radius: "))
                    result = calc.geometry(op, radius=radius)
                elif op == 'rectangle_area':
                    length = float(input("Length: "))
                    width = float(input("Width: "))
                    result = calc.geometry(op, length=length, width=width)
                elif op == 'triangle_area':
                    base = float(input("Base: "))
                    height = float(input("Height: "))
                    result = calc.geometry(op, base=base, height=height)
                else:
                    print("Invalid operation")
                    continue
                print(f"Result: {result}")
            
            elif choice == "9":
                op = input("Operation (kinetic_energy, potential_energy, force, momentum): ")
                if op in ['kinetic_energy', 'potential_energy', 'force', 'momentum']:
                    mass = float(input("Mass: "))
                    if op == 'kinetic_energy':
                        velocity = float(input("Velocity: "))
                        result = calc.physics(op, mass=mass, velocity=velocity)
                    elif op == 'potential_energy':
                        height = float(input("Height: "))
                        result = calc.physics(op, mass=mass, height=height)
                    elif op == 'force':
                        acceleration = float(input("Acceleration: "))
                        result = calc.physics(op, mass=mass, acceleration=acceleration)
                    elif op == 'momentum':
                        velocity = float(input("Velocity: "))
                        result = calc.physics(op, mass=mass, velocity=velocity)
                else:
                    print("Invalid operation")
                    continue
                print(f"Result: {result}")
            
            elif choice == "10":
                value = float(input("Value: "))
                from_unit = input("From unit: ")
                to_unit = input("To unit: ")
                result = calc.convert_units(value, from_unit, to_unit)
                print(f"Result: {result}")
            
            elif choice == "11":
                op = input("Operation (store, recall, clear, add): ")
                if op == 'store':
                    value = float(input("Value to store: "))
                    calc.memory_operations(op, value)
                elif op == 'recall':
                    result = calc.memory_operations(op)
                    print(f"Memory value: {result}")
                elif op in ['clear', 'add']:
                    if op == 'add':
                        value = float(input("Value to add: "))
                        calc.memory_operations(op, value)
                    else:
                        calc.memory_operations(op)
            
            elif choice == "12":
                history = calc.get_history()
                print("\nRecent Calculations:")
                for i, calc_str in enumerate(history, 1):
                    print(f"{i}. {calc_str}")
            
            elif choice == "13":
                mode = input("Angle mode (degrees/radians): ")
                calc.set_angle_mode(mode)
                print(f"Angle mode set to {calc.angle_mode}")
            
            elif choice == "14":
                print("Goodbye!")
                break
            
            else:
                print("Invalid option. Please try again.")
        
        except (ValueError, KeyError) as e:
            print(f"Error: {e}")
        except KeyboardInterrupt:
            print("\nGoodbye!")
            break

if __name__ == "__main__":
    main()
