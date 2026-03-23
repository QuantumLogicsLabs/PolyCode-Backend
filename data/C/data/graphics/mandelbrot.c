/*
 * File: mandelbrot.c
 * Description: Mandelbrot set fractal generator
 */

#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <complex.h>

#define WIDTH 80
#define HEIGHT 40
#define MAX_ITERATIONS 100

// Characters for different iteration counts
const char CHARS[] = " .:-=+*#%@";

// Calculate Mandelbrot set
int mandelbrot(double complex c) {
    double complex z = 0;
    int iterations = 0;
    
    while (cabs(z) <= 2 && iterations < MAX_ITERATIONS) {
        z = z * z + c;
        iterations++;
    }
    
    return iterations;
}

// Generate and display Mandelbrot set
void generateMandelbrot() {
    double xmin = -2.5, xmax = 1.0;
    double ymin = -1.0, ymax = 1.0;
    
    printf("Mandelbrot Set Fractal\n");
    printf("====================\n\n");
    
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            // Map screen coordinates to complex plane
            double real = xmin + (x / (double)WIDTH) * (xmax - xmin);
            double imag = ymin + (y / (double)HEIGHT) * (ymax - ymin);
            
            double complex c = real + imag * I;
            int iterations = mandelbrot(c);
            
            // Map iterations to character
            char ch;
            if (iterations == MAX_ITERATIONS) {
                ch = '@'; // Inside the set
            } else {
                int char_index = (iterations * (sizeof(CHARS) - 2)) / MAX_ITERATIONS;
                ch = CHARS[char_index];
            }
            
            printf("%c", ch);
        }
        printf("\n");
    }
}

// Zoom into specific area
void zoomMandelbrot(double center_x, double center_y, double zoom) {
    double xmin = center_x - zoom;
    double xmax = center_x + zoom;
    double ymin = center_y - zoom;
    double ymax = center_y + zoom;
    
    printf("Zoomed Mandelbrot Set (center: %.3f, %.3f, zoom: %.3f)\n", 
           center_x, center_y, zoom);
    printf("========================================================\n\n");
    
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            double real = xmin + (x / (double)WIDTH) * (xmax - xmin);
            double imag = ymin + (y / (double)HEIGHT) * (ymax - ymin);
            
            double complex c = real + imag * I;
            int iterations = mandelbrot(c);
            
            char ch;
            if (iterations == MAX_ITERATIONS) {
                ch = '@';
            } else {
                int char_index = (iterations * (sizeof(CHARS) - 2)) / MAX_ITERATIONS;
                ch = CHARS[char_index];
            }
            
            printf("%c", ch);
        }
        printf("\n");
    }
}

// Calculate Julia set (related to Mandelbrot)
int julia(double complex z, double complex c) {
    int iterations = 0;
    
    while (cabs(z) <= 2 && iterations < MAX_ITERATIONS) {
        z = z * z + c;
        iterations++;
    }
    
    return iterations;
}

void generateJulia(double complex c) {
    double xmin = -2.0, xmax = 2.0;
    double ymin = -2.0, ymax = 2.0;
    
    printf("Julia Set (c = %.3f + %.3fi)\n", creal(c), cimag(c));
    printf("================================\n\n");
    
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            double real = xmin + (x / (double)WIDTH) * (xmax - xmin);
            double imag = ymin + (y / (double)HEIGHT) * (ymax - ymin);
            
            double complex z = real + imag * I;
            int iterations = julia(z, c);
            
            char ch;
            if (iterations == MAX_ITERATIONS) {
                ch = '@';
            } else {
                int char_index = (iterations * (sizeof(CHARS) - 2)) / MAX_ITERATIONS;
                ch = CHARS[char_index];
            }
            
            printf("%c", ch);
        }
        printf("\n");
    }
}

// Color version (using ANSI escape codes)
void coloredMandelbrot() {
    double xmin = -2.5, xmax = 1.0;
    double ymin = -1.0, ymax = 1.0;
    
    printf("Colored Mandelbrot Set\n");
    printf("======================\n\n");
    
    for (int y = 0; y < HEIGHT; y++) {
        for (int x = 0; x < WIDTH; x++) {
            double real = xmin + (x / (double)WIDTH) * (xmax - xmin);
            double imag = ymin + (y / (double)HEIGHT) * (ymax - ymin);
            
            double complex c = real + imag * I;
            int iterations = mandelbrot(c);
            
            if (iterations == MAX_ITERATIONS) {
                printf("\033[0;30m@\033[0m"); // Black
            } else {
                // Map iterations to colors
                int color = (iterations * 7) / MAX_ITERATIONS + 31;
                printf("\033[1;%dm%c\033[0m", color, CHARS[iterations % (sizeof(CHARS) - 1)]);
            }
        }
        printf("\n");
    }
}

int main() {
    // Generate basic Mandelbrot set
    generateMandelbrot();
    
    printf("\nPress Enter to continue to zoomed view...\n");
    getchar();
    
    // Zoom into interesting area
    zoomMandelbrot(-0.7, 0.0, 0.1);
    
    printf("\nPress Enter to continue to Julia set...\n");
    getchar();
    
    // Generate Julia set
    double complex c = -0.7 + 0.27015 * I; // Interesting Julia constant
    generateJulia(c);
    
    printf("\nPress Enter to continue to colored version...\n");
    getchar();
    
    // Generate colored version
    coloredMandelbrot();
    
    printf("\nFractal generation completed!\n");
    
    return 0;
}
