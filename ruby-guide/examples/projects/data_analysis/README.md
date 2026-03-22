# Data Analysis Tool

A command-line data analysis tool that demonstrates statistical calculations, CSV processing, and algorithm implementation in Ruby.

## Features

- CSV file processing and analysis
- Statistical calculations (mean, median, mode, standard deviation)
- Data filtering and sorting
- Visualization concepts (basic console output)
- Report generation

## Concepts Demonstrated

- Statistical algorithms implementation
- CSV file handling and parsing
- Data validation and error handling
- Command-line argument processing
- Report generation
- Data visualization concepts

## How to Run

```bash
ruby main.rb data.csv
```

## Usage Examples

```
Data Analysis Tool
=================

Usage: ruby main.rb [options] <csv_file>
Options:
  --column <name>    Analyze specific column
  --filter <field:value>  Filter data
  --sort <field>     Sort by field
  --output <file>    Save results to file
  --stats            Show detailed statistics
  --visualize        Show basic visualization

Examples:
  ruby main.rb data.csv --stats
  ruby main.rb data.csv --column price --filter category:electronics
  ruby main.rb data.csv --sort price --output results.txt
```

## Project Structure

```
data_analysis/
├── main.rb              # Main application entry point
├── data_analyzer.rb     # Core analysis logic
├── statistics.rb        # Statistical calculations
├── csv_processor.rb     # CSV file handling
├── report_generator.rb  # Report generation
├── visualizer.rb        # Basic visualization
├── sample_data.csv      # Sample data file
└── README.md            # This file
```

## Code Overview

### DataAnalyzer Class
Main analysis engine that:
- Coordinates analysis operations
- Handles command-line options
- Manages data processing pipeline
- Generates reports

### Statistics Module
Statistical calculations including:
- Mean, median, mode
- Standard deviation and variance
- Percentiles and quartiles
- Correlation coefficients

### CSVProcessor Class
Handles CSV operations with:
- File parsing and validation
- Data type inference
- Filtering and sorting
- Error handling

### ReportGenerator Class
Creates reports with:
- Summary statistics
- Data insights
- Formatted output
- Export capabilities

## Sample Data Format

```csv
name,age,city,salary,department
John,30,New York,75000,Engineering
Jane,25,San Francisco,65000,Marketing
Bob,35,Chicago,80000,Engineering
Alice,28,Boston,70000,Sales
Charlie,32,Seattle,72000,Marketing
```

## Analysis Examples

### Basic Statistics
```
Age Statistics:
- Mean: 30.0
- Median: 30.0
- Mode: N/A
- Standard Deviation: 3.54
- Range: 10
```

### Department Analysis
```
Department Breakdown:
- Engineering: 2 employees
- Marketing: 2 employees
- Sales: 1 employee
```

### Salary Analysis
```
Salary Statistics:
- Mean: $72,000
- Min: $65,000
- Max: $80,000
- Range: $15,000
```

## Extensions to Try

1. **Advanced Statistics**: Add regression analysis, hypothesis testing
2. **Data Visualization**: Add ASCII charts or integrate with charting gems
3. **Multiple File Support**: Process multiple CSV files
4. **Database Integration**: Support database queries
5. **Real-time Analysis**: Stream processing capabilities
6. **Machine Learning**: Add clustering or classification algorithms
