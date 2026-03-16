"""
Data Visualizer
Create various charts and graphs from data using matplotlib.
"""

import matplotlib.pyplot as plt
import numpy as np
from typing import List, Dict, Tuple, Optional
import random

class DataVisualizer:
    """Create various data visualizations."""
    
    def __init__(self):
        """Initialize visualizer with default settings."""
        plt.style.use('seaborn-v0_8' if 'seaborn-v0_8' in plt.style.available else 'default')
        self.colors = ['#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd', 
                      '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf']
    
    def line_chart(self, x_data: List[float], y_data: List[float], 
                   title: str = "Line Chart", xlabel: str = "X", 
                   ylabel: str = "Y", save_path: Optional[str] = None):
        """Create a line chart."""
        plt.figure(figsize=(10, 6))
        plt.plot(x_data, y_data, linewidth=2, marker='o', markersize=4)
        plt.title(title, fontsize=16, fontweight='bold')
        plt.xlabel(xlabel, fontsize=12)
        plt.ylabel(ylabel, fontsize=12)
        plt.grid(True, alpha=0.3)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def bar_chart(self, categories: List[str], values: List[float],
                  title: str = "Bar Chart", xlabel: str = "Categories",
                  ylabel: str = "Values", save_path: Optional[str] = None):
        """Create a bar chart."""
        plt.figure(figsize=(10, 6))
        bars = plt.bar(categories, values, color=self.colors[:len(categories)])
        
        # Add value labels on bars
        for bar, value in zip(bars, values):
            plt.text(bar.get_x() + bar.get_width()/2, bar.get_height() + 0.01,
                    f'{value:.2f}', ha='center', va='bottom')
        
        plt.title(title, fontsize=16, fontweight='bold')
        plt.xlabel(xlabel, fontsize=12)
        plt.ylabel(ylabel, fontsize=12)
        plt.xticks(rotation=45)
        plt.grid(True, alpha=0.3, axis='y')
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def pie_chart(self, labels: List[str], values: List[float],
                  title: str = "Pie Chart", save_path: Optional[str] = None):
        """Create a pie chart."""
        plt.figure(figsize=(8, 8))
        colors = self.colors[:len(labels)]
        wedges, texts, autotexts = plt.pie(values, labels=labels, colors=colors,
                                          autopct='%1.1f%%', startangle=90)
        
        # Make percentage text bold
        for autotext in autotexts:
            autotext.set_color('white')
            autotext.set_fontweight('bold')
        
        plt.title(title, fontsize=16, fontweight='bold')
        plt.axis('equal')
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def scatter_plot(self, x_data: List[float], y_data: List[float],
                    title: str = "Scatter Plot", xlabel: str = "X",
                    ylabel: str = "Y", save_path: Optional[str] = None):
        """Create a scatter plot."""
        plt.figure(figsize=(10, 6))
        plt.scatter(x_data, y_data, alpha=0.7, s=50, 
                   c=range(len(x_data)), cmap='viridis')
        
        # Add trend line
        z = np.polyfit(x_data, y_data, 1)
        p = np.poly1d(z)
        plt.plot(x_data, p(x_data), "r--", alpha=0.8, linewidth=2)
        
        plt.title(title, fontsize=16, fontweight='bold')
        plt.xlabel(xlabel, fontsize=12)
        plt.ylabel(ylabel, fontsize=12)
        plt.grid(True, alpha=0.3)
        plt.colorbar(label='Data Point Index')
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def histogram(self, data: List[float], bins: int = 10,
                 title: str = "Histogram", xlabel: str = "Values",
                 ylabel: str = "Frequency", save_path: Optional[str] = None):
        """Create a histogram."""
        plt.figure(figsize=(10, 6))
        plt.hist(data, bins=bins, alpha=0.7, color=self.colors[0], edgecolor='black')
        
        # Add statistics
        mean_val = np.mean(data)
        std_val = np.std(data)
        plt.axvline(mean_val, color='red', linestyle='dashed', linewidth=2, 
                   label=f'Mean: {mean_val:.2f}')
        plt.axvline(mean_val + std_val, color='orange', linestyle='dashed', 
                   linewidth=1, label=f'±1 Std: {std_val:.2f}')
        plt.axvline(mean_val - std_val, color='orange', linestyle='dashed', linewidth=1)
        
        plt.title(title, fontsize=16, fontweight='bold')
        plt.xlabel(xlabel, fontsize=12)
        plt.ylabel(ylabel, fontsize=12)
        plt.legend()
        plt.grid(True, alpha=0.3)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def box_plot(self, data_sets: List[List[float]], labels: List[str],
                 title: str = "Box Plot", ylabel: str = "Values",
                 save_path: Optional[str] = None):
        """Create a box plot."""
        plt.figure(figsize=(10, 6))
        box_plot = plt.boxplot(data_sets, labels=labels, patch_artist=True)
        
        # Color the boxes
        for patch, color in zip(box_plot['boxes'], self.colors):
            patch.set_facecolor(color)
            patch.set_alpha(0.7)
        
        plt.title(title, fontsize=16, fontweight='bold')
        plt.ylabel(ylabel, fontsize=12)
        plt.grid(True, alpha=0.3)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def heat_map(self, data: List[List[float]], x_labels: List[str], 
                y_labels: List[str], title: str = "Heat Map",
                save_path: Optional[str] = None):
        """Create a heat map."""
        plt.figure(figsize=(10, 8))
        im = plt.imshow(data, cmap='viridis', aspect='auto')
        
        # Add text annotations
        for i in range(len(y_labels)):
            for j in range(len(x_labels)):
                text = plt.text(j, i, f'{data[i][j]:.2f}',
                              ha="center", va="center", color="white")
        
        plt.title(title, fontsize=16, fontweight='bold')
        plt.xticks(range(len(x_labels)), x_labels)
        plt.yticks(range(len(y_labels)), y_labels)
        plt.colorbar(im)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def multiple_lines(self, data_sets: List[Tuple[List[float], List[float]]],
                      labels: List[str], title: str = "Multiple Lines",
                      xlabel: str = "X", ylabel: str = "Y",
                      save_path: Optional[str] = None):
        """Create a chart with multiple line series."""
        plt.figure(figsize=(12, 6))
        
        for i, (x_data, y_data) in enumerate(data_sets):
            plt.plot(x_data, y_data, linewidth=2, marker='o', markersize=4,
                    label=labels[i], color=self.colors[i % len(self.colors)])
        
        plt.title(title, fontsize=16, fontweight='bold')
        plt.xlabel(xlabel, fontsize=12)
        plt.ylabel(ylabel, fontsize=12)
        plt.legend()
        plt.grid(True, alpha=0.3)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def subplots_grid(self, plots_data: List[Dict], rows: int = 2, cols: int = 2,
                      save_path: Optional[str] = None):
        """Create a grid of subplots."""
        fig, axes = plt.subplots(rows, cols, figsize=(15, 10))
        axes = axes.flatten() if rows * cols > 1 else [axes]
        
        for i, plot_data in enumerate(plots_data):
            if i >= len(axes):
                break
            
            ax = axes[i]
            plot_type = plot_data['type']
            
            if plot_type == 'line':
                ax.plot(plot_data['x'], plot_data['y'])
            elif plot_type == 'bar':
                ax.bar(plot_data['x'], plot_data['y'])
            elif plot_type == 'scatter':
                ax.scatter(plot_data['x'], plot_data['y'])
            elif plot_type == 'hist':
                ax.hist(plot_data['data'], bins=plot_data.get('bins', 10))
            
            ax.set_title(plot_data['title'])
            ax.set_xlabel(plot_data.get('xlabel', ''))
            ax.set_ylabel(plot_data.get('ylabel', ''))
            ax.grid(True, alpha=0.3)
        
        # Hide empty subplots
        for i in range(len(plots_data), len(axes)):
            axes[i].set_visible(False)
        
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path, dpi=300, bbox_inches='tight')
        
        plt.show()
    
    def generate_sample_data(self, data_type: str, size: int = 50):
        """Generate sample data for demonstration."""
        if data_type == 'linear':
            x = np.linspace(0, 10, size)
            y = 2 * x + np.random.normal(0, 0.5, size)
            return x, y
        
        elif data_type == 'sine':
            x = np.linspace(0, 4*np.pi, size)
            y = np.sin(x) + np.random.normal(0, 0.1, size)
            return x, y
        
        elif data_type == 'exponential':
            x = np.linspace(0, 5, size)
            y = np.exp(x/2) + np.random.normal(0, 0.5, size)
            return x, y
        
        elif data_type == 'normal':
            return np.random.normal(0, 1, size)
        
        elif data_type == 'uniform':
            return np.random.uniform(0, 10, size)
        
        else:
            raise ValueError(f"Unknown data type: {data_type}")

def main():
    """Demonstrate data visualization capabilities."""
    print("Data Visualizer")
    print("=" * 30)
    print("Creating various charts and graphs")
    print()
    
    visualizer = DataVisualizer()
    
    # Generate sample data
    print("1. Line Chart - Linear Data")
    x_data, y_data = visualizer.generate_sample_data('linear', 30)
    visualizer.line_chart(x_data, y_data, "Linear Relationship", "X Values", "Y Values")
    
    print("2. Bar Chart - Sales Data")
    categories = ['Q1', 'Q2', 'Q3', 'Q4']
    values = [120, 150, 180, 165]
    visualizer.bar_chart(categories, values, "Quarterly Sales", "Quarter", "Sales ($1000s)")
    
    print("3. Pie Chart - Market Share")
    labels = ['Product A', 'Product B', 'Product C', 'Product D', 'Others']
    values = [35, 25, 20, 15, 5]
    visualizer.pie_chart(labels, values, "Market Share Distribution")
    
    print("4. Scatter Plot - Random Data")
    x_data = np.random.normal(0, 1, 50)
    y_data = 2 * x_data + np.random.normal(0, 0.5, 50)
    visualizer.scatter_plot(x_data, y_data, "Correlation Analysis", "Variable X", "Variable Y")
    
    print("5. Histogram - Normal Distribution")
    data = visualizer.generate_sample_data('normal', 100)
    visualizer.histogram(data, bins=15, "Normal Distribution", "Values", "Frequency")
    
    print("6. Box Plot - Multiple Datasets")
    data_sets = [
        visualizer.generate_sample_data('normal', 50),
        visualizer.generate_sample_data('uniform', 50),
        visualizer.generate_sample_data('normal', 50) + 2,
        visualizer.generate_sample_data('uniform', 50) - 1
    ]
    labels = ['Normal 1', 'Uniform 1', 'Normal 2', 'Uniform 2']
    visualizer.box_plot(data_sets, labels, "Distribution Comparison")
    
    print("7. Heat Map - Correlation Matrix")
    # Create correlation matrix
    data1 = visualizer.generate_sample_data('normal', 100)
    data2 = visualizer.generate_sample_data('normal', 100)
    data3 = data1 + data2 + np.random.normal(0, 0.1, 100)
    
    correlation_matrix = np.corrcoef([data1, data2, data3])
    x_labels = ['Variable 1', 'Variable 2', 'Variable 3']
    y_labels = ['Variable 1', 'Variable 2', 'Variable 3']
    visualizer.heat_map(correlation_matrix, x_labels, y_labels, "Correlation Matrix")
    
    print("8. Multiple Lines - Comparison")
    datasets = []
    labels = ['Linear', 'Sine', 'Exponential']
    
    for data_type in ['linear', 'sine', 'exponential']:
        x, y = visualizer.generate_sample_data(data_type, 50)
        datasets.append((x, y))
    
    visualizer.multiple_lines(datasets, labels, "Function Comparison", "X", "Y")
    
    print("9. Subplots Grid - Multiple Views")
    plots_data = [
        {
            'type': 'line',
            'x': visualizer.generate_sample_data('linear', 20)[0],
            'y': visualizer.generate_sample_data('linear', 20)[1],
            'title': 'Linear Data',
            'xlabel': 'X',
            'ylabel': 'Y'
        },
        {
            'type': 'bar',
            'x': ['A', 'B', 'C', 'D'],
            'y': [10, 15, 12, 18],
            'title': 'Bar Chart',
            'xlabel': 'Category',
            'ylabel': 'Value'
        },
        {
            'type': 'scatter',
            'x': np.random.normal(0, 1, 30),
            'y': np.random.normal(0, 1, 30),
            'title': 'Scatter Plot',
            'xlabel': 'X',
            'ylabel': 'Y'
        },
        {
            'type': 'hist',
            'data': np.random.normal(0, 1, 50),
            'title': 'Histogram',
            'xlabel': 'Value',
            'ylabel': 'Frequency',
            'bins': 10
        }
    ]
    
    visualizer.subplots_grid(plots_data, rows=2, cols=2)
    
    print("\nAll visualizations completed!")
    print("Note: Close each plot window to continue to the next one.")

if __name__ == "__main__":
    main()
