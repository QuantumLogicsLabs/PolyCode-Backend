using System;
using Microsoft.Quantum.Simulation.Core;
using Microsoft.Quantum.Simulation.Simulators;

class Driver
{
    static void Main()
    {
        int zeroCount = 0;
        int oneCount = 0;

        using var sim = new QuantumSimulator();

        for (int i = 0; i < 10; i++)
        {
            var result = FlipAQubit.FlipAndMeasure.Run(sim).Result;
            if (result == Result.Zero)
                zeroCount++;
            else
                oneCount++;
        }

        Console.WriteLine($"Zero results: {zeroCount}");
        Console.WriteLine($"One results: {oneCount}");
    }
}