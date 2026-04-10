public class ControlStatementsExample {
    public static void main(String[] args) {
        // If-Else Statements
        System.out.println("=== If-Else Statements ===");
        int age = 18;
        
        if (age >= 18) {
            System.out.println("You are eligible to vote");
        } else {
            System.out.println("You are not eligible to vote");
        }
        
        // If-Else If-Else Ladder
        int score = 85;
        if (score >= 90) {
            System.out.println("Grade: A");
        } else if (score >= 80) {
            System.out.println("Grade: B");
        } else if (score >= 70) {
            System.out.println("Grade: C");
        } else if (score >= 60) {
            System.out.println("Grade: D");
        } else {
            System.out.println("Grade: F");
        }
        
        // Switch Statement
        System.out.println("\n=== Switch Statement ===");
        int day = 3;
        String dayName;
        
        switch (day) {
            case 1:
                dayName = "Monday";
                break;
            case 2:
                dayName = "Tuesday";
                break;
            case 3:
                dayName = "Wednesday";
                break;
            case 4:
                dayName = "Thursday";
                break;
            case 5:
                dayName = "Friday";
                break;
            case 6:
                dayName = "Saturday";
                break;
            case 7:
                dayName = "Sunday";
                break;
            default:
                dayName = "Invalid day";
                break;
        }
        System.out.println("Day " + day + " is " + dayName);
        
        // Enhanced Switch (Java 14+)
        String month = "January";
        int days;
        switch (month.toLowerCase()) {
            case "january", "march", "may", "july", "august", "october", "december":
                days = 31;
                break;
            case "april", "june", "september", "november":
                days = 30;
                break;
            case "february":
                days = 28; // Ignoring leap years
                break;
            default:
                days = 0;
                break;
        }
        System.out.println(month + " has " + days + " days");
        
        // For Loop
        System.out.println("\n=== For Loop ===");
        System.out.println("Numbers 1 to 5:");
        for (int i = 1; i <= 5; i++) {
            System.out.print(i + " ");
        }
        System.out.println();
        
        // Enhanced For Loop (For-Each)
        System.out.println("\n=== Enhanced For Loop ===");
        int[] numbers = {10, 20, 30, 40, 50};
        System.out.println("Array elements:");
        for (int num : numbers) {
            System.out.print(num + " ");
        }
        System.out.println();
        
        // While Loop
        System.out.println("\n=== While Loop ===");
        int count = 1;
        System.out.println("Countdown from 5:");
        while (count <= 5) {
            System.out.print((6 - count) + " ");
            count++;
        }
        System.out.println();
        
        // Do-While Loop
        System.out.println("\n=== Do-While Loop ===");
        int number = 1;
        System.out.println("Numbers 1 to 3:");
        do {
            System.out.print(number + " ");
            number++;
        } while (number <= 3);
        System.out.println();
        
        // Break Statement
        System.out.println("\n=== Break Statement ===");
        System.out.println("Breaking at 3:");
        for (int i = 1; i <= 5; i++) {
            if (i == 3) {
                break;
            }
            System.out.print(i + " ");
        }
        System.out.println();
        
        // Continue Statement
        System.out.println("\n=== Continue Statement ===");
        System.out.println("Skipping 3:");
        for (int i = 1; i <= 5; i++) {
            if (i == 3) {
                continue;
            }
            System.out.print(i + " ");
        }
        System.out.println();
        
        // Nested Loops
        System.out.println("\n=== Nested Loops ===");
        System.out.println("Multiplication table (2x2):");
        for (int i = 1; i <= 2; i++) {
            for (int j = 1; j <= 2; j++) {
                System.out.print(i * j + "\t");
            }
            System.out.println();
        }
        
        // Labeled Break and Continue
        System.out.println("\n=== Labeled Break ===");
        outer: for (int i = 1; i <= 3; i++) {
            for (int j = 1; j <= 3; j++) {
                if (i == 2 && j == 2) {
                    break outer;
                }
                System.out.print(i + "," + j + " ");
            }
        }
        System.out.println();
    }
}
