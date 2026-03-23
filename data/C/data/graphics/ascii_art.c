/*
 * File: ascii_art.c
 * Description: ASCII art generation and display
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

// Clear screen function
void clearScreen() {
    system("clear || cls");
}

// Set cursor position
void setCursor(int x, int y) {
    printf("\033[%d;%dH", y, x);
}

// Set text color
void setColor(int color) {
    printf("\033[1;3%dm", color);
}

// Reset color
void resetColor() {
    printf("\033[0m");
}

// Draw a box
void drawBox(int x, int y, int width, int height, char ch) {
    for (int i = 0; i < height; i++) {
        for (int j = 0; j < width; j++) {
            setCursor(x + j, y + i);
            if (i == 0 || i == height - 1 || j == 0 || j == width - 1) {
                printf("%c", ch);
            } else {
                printf(" ");
            }
        }
    }
}

// Draw a filled box
void drawFilledBox(int x, int y, int width, int height, char ch) {
    for (int i = 0; i < height; i++) {
        for (int j = 0; j < width; j++) {
            setCursor(x + j, y + i);
            printf("%c", ch);
        }
    }
}

// Draw a circle
void drawCircle(int centerX, int centerY, int radius, char ch) {
    for (int y = -radius; y <= radius; y++) {
        for (int x = -radius; x <= radius; x++) {
            if (x * x + y * y <= radius * radius) {
                setCursor(centerX + x, centerY + y);
                printf("%c", ch);
            }
        }
    }
}

// Draw a triangle
void drawTriangle(int x, int y, int size, char ch) {
    for (int i = 0; i < size; i++) {
        for (int j = 0; j <= i; j++) {
            setCursor(x + j, y + i);
            printf("%c", ch);
        }
    }
}

// Draw a diamond
void drawDiamond(int centerX, int centerY, int size, char ch) {
    for (int i = 0; i < size; i++) {
        for (int j = 0; j < size - i; j++) {
            setCursor(centerX + j, centerY + i);
            printf("%c", ch);
            setCursor(centerX - j, centerY + i);
            printf("%c", ch);
        }
    }
    
    for (int i = size - 2; i >= 0; i--) {
        for (int j = 0; j < size - i; j++) {
            setCursor(centerX + j, centerY + (2 * size - 2 - i));
            printf("%c", ch);
            setCursor(centerX - j, centerY + (2 * size - 2 - i));
            printf("%c", ch);
        }
    }
}

// Draw a line
void drawLine(int x1, int y1, int x2, int y2, char ch) {
    int dx = abs(x2 - x1);
    int dy = abs(y2 - y1);
    int sx = (x1 < x2) ? 1 : -1;
    int sy = (y1 < y2) ? 1 : -1;
    int err = dx - dy;
    
    while (1) {
        setCursor(x1, y1);
        printf("%c", ch);
        
        if (x1 == x2 && y1 == y2) break;
        
        int e2 = 2 * err;
        if (e2 > -dy) {
            err -= dy;
            x1 += sx;
        }
        if (e2 < dx) {
            err += dx;
            y1 += sy;
        }
    }
}

// Simple text animation
void animateText(const char* text, int x, int y, int delay) {
    int len = strlen(text);
    for (int i = 0; i < len; i++) {
        setCursor(x + i, y);
        printf("%c", text[i]);
        fflush(stdout);
        usleep(delay * 1000);
    }
}

// Rainbow colors
void rainbowText(const char* text, int x, int y) {
    int colors[] = {1, 3, 5, 2, 4, 6}; // Red, Yellow, Magenta, Green, Blue, Cyan
    int len = strlen(text);
    
    for (int i = 0; i < len; i++) {
        setCursor(x + i, y);
        setColor(colors[i % 6]);
        printf("%c", text[i]);
    }
    resetColor();
}

// ASCII art patterns
void drawSmileyFace(int centerX, int centerY) {
    // Face
    drawCircle(centerX, centerY, 10, '*');
    
    // Eyes
    setCursor(centerX - 3, centerY - 2);
    printf("o");
    setCursor(centerX + 3, centerY - 2);
    printf("o");
    
    // Nose
    setCursor(centerX, centerY);
    printf("^");
    
    // Mouth
    setCursor(centerX - 3, centerY + 3);
    printf("\\");
    setCursor(centerX + 3, centerY + 3);
    printf("/");
    setCursor(centerX - 2, centerY + 4);
    printf("_");
    setCursor(centerX - 1, centerY + 4);
    printf("_");
    setCursor(centerX, centerY + 4);
    printf("_");
    setCursor(centerX + 1, centerY + 4);
    printf("_");
    setCursor(centerX + 2, centerY + 4);
    printf("_");
}

void drawHouse(int x, int y) {
    // House base
    drawBox(x, y + 5, 15, 10, '#');
    
    // Roof
    for (int i = 0; i < 8; i++) {
        setCursor(x + 7 - i, y + 5 - i);
        printf("/");
        setCursor(x + 7 + i, y + 5 - i);
        printf("\\");
    }
    
    // Door
    drawBox(x + 6, y + 10, 4, 5, '+');
    
    // Windows
    drawBox(x + 2, y + 7, 3, 3, 'o');
    drawBox(x + 10, y + 7, 3, 3, 'o');
}

void drawTree(int x, int y) {
    // Trunk
    drawFilledBox(x + 4, y + 8, 3, 5, '|');
    
    // Leaves (triangle layers)
    drawTriangle(x, y, 10, '*');
    drawTriangle(x + 1, y + 2, 8, '*');
    drawTriangle(x + 2, y + 4, 6, '*');
    
    // Star on top
    setCursor(x + 4, y - 1);
    setColor(1); // Red
    printf("*");
    resetColor();
}

int main() {
    clearScreen();
    
    // Title
    setColor(5); // Magenta
    setCursor(30, 2);
    printf("ASCII ART GALLERY\n");
    resetColor();
    
    // Box examples
    drawBox(10, 5, 20, 8, '*');
    setCursor(15, 9);
    printf("Box");
    
    drawFilledBox(40, 5, 15, 8, '#');
    setCursor(45, 9);
    setColor(3); // Yellow
    printf("Filled Box");
    resetColor();
    
    // Circle
    drawCircle(20, 20, 8, 'o');
    setCursor(17, 20);
    printf("Circle");
    
    // Triangle
    drawTriangle(50, 15, 10, '^');
    setCursor(52, 26);
    printf("Triangle");
    
    // Diamond
    drawDiamond(80, 20, 6, '+');
    setCursor(77, 27);
    printf("Diamond");
    
    // Line
    drawLine(10, 35, 90, 35, '-');
    setCursor(45, 36);
    setColor(4); // Blue
    printf("Line");
    resetColor();
    
    // Smiley face
    drawSmileyFace(25, 45);
    setCursor(20, 58);
    printf("Smiley Face");
    
    // House
    drawHouse(60, 40);
    setCursor(62, 58);
    setColor(2); // Green
    printf("House");
    resetColor();
    
    // Tree
    drawTree(10, 65);
    setCursor(8, 85);
    setColor(1); // Red
    printf("Christmas Tree");
    resetColor();
    
    // Animated text
    animateText("Welcome to ASCII Art!", 25, 70, 100);
    
    // Rainbow text
    rainbowText("Rainbow Colors", 35, 72);
    
    // Instructions
    setColor(6); // Cyan
    setCursor(1, 80);
    printf("Press Enter to exit...");
    resetColor();
    
    getchar();
    
    return 0;
}
