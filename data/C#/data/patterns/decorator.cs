using System;

// Decorator Pattern - Coffee Shop Example
public interface ICoffee
{
    string GetDescription();
    decimal GetCost();
}

public class SimpleCoffee : ICoffee
{
    public string GetDescription() => "Simple Coffee";
    public decimal GetCost() => 2.00m;
}

public abstract class CoffeeDecorator : ICoffee
{
    protected readonly ICoffee _coffee;
    
    protected CoffeeDecorator(ICoffee coffee)
    {
        _coffee = coffee;
    }
    
    public virtual string GetDescription() => _coffee.GetDescription();
    public virtual decimal GetCost() => _coffee.GetCost();
}

public class MilkDecorator : CoffeeDecorator
{
    public MilkDecorator(ICoffee coffee) : base(coffee) { }
    
    public override string GetDescription() => _coffee.GetDescription() + ", Milk";
    public override decimal GetCost() => _coffee.GetCost() + 0.50m;
}

public class SugarDecorator : CoffeeDecorator
{
    public SugarDecorator(ICoffee coffee) : base(coffee) { }
    
    public override string GetDescription() => _coffee.GetDescription() + ", Sugar";
    public override decimal GetCost() => _coffee.GetCost() + 0.25m;
}

public class WhippedCreamDecorator : CoffeeDecorator
{
    public WhippedCreamDecorator(ICoffee coffee) : base(coffee) { }
    
    public override string GetDescription() => _coffee.GetDescription() + ", Whipped Cream";
    public override decimal GetCost() => _coffee.GetCost() + 1.00m;
}

// Demonstration
public class DecoratorPatternDemo
{
    public static void Main()
    {
        // Simple coffee
        ICoffee coffee = new SimpleCoffee();
        Console.WriteLine($"{coffee.GetDescription()}: ${coffee.GetCost()}");
        
        // Add milk
        coffee = new MilkDecorator(coffee);
        Console.WriteLine($"{coffee.GetDescription()}: ${coffee.GetCost()}");
        
        // Add sugar
        coffee = new SugarDecorator(coffee);
        Console.WriteLine($"{coffee.GetDescription()}: ${coffee.GetCost()}");
        
        // Add whipped cream
        coffee = new WhippedCreamDecorator(coffee);
        Console.WriteLine($"{coffee.GetDescription()}: ${coffee.GetCost()}");
        
        // Complex order
        ICoffee complexCoffee = new WhippedCreamDecorator(
            new SugarDecorator(
                new MilkDecorator(new SimpleCoffee())));
        
        Console.WriteLine($"Complex order: {complexCoffee.GetDescription()}: ${complexCoffee.GetCost()}");
    }
}
