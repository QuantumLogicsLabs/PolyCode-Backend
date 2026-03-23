/*
 * File: turtle_graphics.c
 * Description: Simple turtle graphics implementation
 */

#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <unistd.h>

// Turtle structure
typedef struct {
    double x, y;        // Position
    double angle;       // Heading angle in degrees
    int pen_down;       // Pen state (1 = down, 0 = up)
    char canvas[50][100]; // Drawing canvas
} Turtle;

// Clear screen
void clear_screen() {
    system("clear || cls");
}

// Initialize turtle
void turtle_init(Turtle* turtle) {
    turtle->x = 50.0;
    turtle->y = 25.0;
    turtle->angle = 0.0;
    turtle->pen_down = 1;
    
    // Clear canvas
    for (int i = 0; i < 50; i++) {
        for (int j = 0; j < 100; j++) {
            turtle->canvas[i][j] = ' ';
        }
    }
}

// Convert degrees to radians
double to_radians(double degrees) {
    return degrees * M_PI / 180.0;
}

// Draw turtle on canvas
void draw_turtle(Turtle* turtle) {
    int x = (int)turtle->x;
    int y = (int)turtle->y;
    
    if (x >= 0 && x < 100 && y >= 0 && y < 50) {
        turtle->canvas[y][x] = 'T';
    }
}

// Display canvas
void display_canvas(Turtle* turtle) {
    clear_screen();
    
    printf("Turtle Graphics Canvas\n");
    printf("====================\n");
    
    for (int i = 0; i < 50; i++) {
        for (int j = 0; j < 100; j++) {
            printf("%c", turtle->canvas[i][j]);
        }
        printf("\n");
    }
    
    printf("\nPosition: (%.1f, %.1f), Angle: %.1f°, Pen: %s\n",
           turtle->x, turtle->y, turtle->angle, turtle->pen_down ? "Down" : "Up");
}

// Move turtle forward
void turtle_forward(Turtle* turtle, double distance) {
    double new_x = turtle->x + distance * cos(to_radians(turtle->angle));
    double new_y = turtle->y + distance * sin(to_radians(turtle->angle));
    
    if (turtle->pen_down) {
        // Draw line from current position to new position
        int steps = (int)distance;
        if (steps < 1) steps = 1;
        
        for (int i = 0; i <= steps; i++) {
            double t = (double)i / steps;
            int x = (int)(turtle->x + t * (new_x - turtle->x));
            int y = (int)(turtle->y + t * (new_y - turtle->y));
            
            if (x >= 0 && x < 100 && y >= 0 && y < 50) {
                turtle->canvas[y][x] = '*';
            }
        }
    }
    
    turtle->x = new_x;
    turtle->y = new_y;
}

// Move turtle backward
void turtle_backward(Turtle* turtle, double distance) {
    turtle_forward(turtle, -distance);
}

// Turn turtle left
void turtle_left(Turtle* turtle, double angle) {
    turtle->angle -= angle;
    turtle->angle = fmod(turtle->angle, 360.0);
    if (turtle->angle < 0) turtle->angle += 360.0;
}

// Turn turtle right
void turtle_right(Turtle* turtle, double angle) {
    turtle_left(turtle, -angle);
}

// Pen up
void turtle_pen_up(Turtle* turtle) {
    turtle->pen_down = 0;
}

// Pen down
void turtle_pen_down_func(Turtle* turtle) {
    turtle->pen_down = 1;
}

// Go to position
void turtle_goto(Turtle* turtle, double x, double y) {
    turtle->x = x;
    turtle->y = y;
}

// Set heading
void turtle_set_heading(Turtle* turtle, double angle) {
    turtle->angle = angle;
}

// Get position
void turtle_position(Turtle* turtle, double* x, double* y) {
    *x = turtle->x;
    *y = turtle->y;
}

// Get heading
double turtle_heading(Turtle* turtle) {
    return turtle->angle;
}

// Draw square
void draw_square(Turtle* turtle, double size) {
    for (int i = 0; i < 4; i++) {
        turtle_forward(turtle, size);
        turtle_right(turtle, 90);
    }
}

// Draw triangle
void draw_triangle(Turtle* turtle, double size) {
    for (int i = 0; i < 3; i++) {
        turtle_forward(turtle, size);
        turtle_right(turtle, 120);
    }
}

// Draw circle
void draw_circle(Turtle* turtle, double radius) {
    int steps = 36;
    double angle_step = 360.0 / steps;
    double side_length = 2 * M_PI * radius / steps;
    
    for (int i = 0; i < steps; i++) {
        turtle_forward(turtle, side_length);
        turtle_right(turtle, angle_step);
    }
}

// Draw star
void draw_star(Turtle* turtle, double size) {
    for (int i = 0; i < 5; i++) {
        turtle_forward(turtle, size);
        turtle_right(turtle, 144);
    }
}

// Draw spiral
void draw_spiral(Turtle* turtle, double max_radius) {
    double radius = 1.0;
    double angle = 0.0;
    
    while (radius < max_radius) {
        turtle_forward(turtle, 1.0);
        turtle_right(turtle, 5.0);
        radius += 0.1;
        angle += 5.0;
    }
}

// Draw flower
void draw_flower(Turtle* turtle, double size) {
    for (int i = 0; i < 6; i++) {
        draw_circle(turtle, size / 2);
        turtle_right(turtle, 60);
    }
}

// Draw tree (fractal-like)
void draw_tree(Turtle* turtle, double size, int depth) {
    if (depth == 0) return;
    
    turtle_forward(turtle, size);
    turtle_right(turtle, 30);
    draw_tree(turtle, size * 0.7, depth - 1);
    turtle_left(turtle, 60);
    draw_tree(turtle, size * 0.7, depth - 1);
    turtle_right(turtle, 30);
    turtle_backward(turtle, size);
}

// Draw polygon
void draw_polygon(Turtle* turtle, int sides, double size) {
    double angle = 360.0 / sides;
    
    for (int i = 0; i < sides; i++) {
        turtle_forward(turtle, size);
        turtle_right(turtle, angle);
    }
}

// Draw house
void draw_house(Turtle* turtle, double size) {
    // Draw base
    draw_square(turtle, size);
    
    // Draw roof
    turtle_forward(turtle, size);
    turtle_left(turtle, 90);
    draw_triangle(turtle, size);
    
    // Draw door
    turtle_goto(turtle, 50, 25);
    turtle_set_heading(turtle, 0);
    turtle_forward(turtle, size / 4);
    turtle_right(turtle, 90);
    turtle_forward(turtle, size / 3);
    turtle_right(turtle, 90);
    turtle_forward(turtle, size / 4);
    turtle_right(turtle, 90);
    turtle_forward(turtle, size / 3);
}

// Animation example
void animate_turtle() {
    Turtle turtle;
    turtle_init(&turtle);
    
    for (int i = 0; i < 36; i++) {
        display_canvas(&turtle);
        turtle_forward(&turtle, 2);
        turtle_right(&turtle, 10);
        usleep(100000); // 0.1 second delay
    }
}

// Interactive turtle
void interactive_turtle() {
    Turtle turtle;
    turtle_init(&turtle);
    
    char command[100];
    double value;
    
    printf("Interactive Turtle Graphics\n");
    printf("Commands: forward <distance>, back <distance>, left <angle>, right <angle>\n");
    printf("          penup, pendown, goto <x> <y>, heading <angle>\n");
    printf("          square <size>, triangle <size>, circle <radius>\n");
    printf("          star <size>, clear, quit\n");
    
    while (1) {
        display_canvas(&turtle);
        printf("\nCommand: ");
        fgets(command, sizeof(command), stdin);
        
        if (strncmp(command, "forward", 7) == 0) {
            sscanf(command + 7, "%lf", &value);
            turtle_forward(&turtle, value);
        } else if (strncmp(command, "back", 4) == 0) {
            sscanf(command + 4, "%lf", &value);
            turtle_backward(&turtle, value);
        } else if (strncmp(command, "left", 4) == 0) {
            sscanf(command + 4, "%lf", &value);
            turtle_left(&turtle, value);
        } else if (strncmp(command, "right", 5) == 0) {
            sscanf(command + 5, "%lf", &value);
            turtle_right(&turtle, value);
        } else if (strncmp(command, "penup", 5) == 0) {
            turtle_pen_up(&turtle);
        } else if (strncmp(command, "pendown", 7) == 0) {
            turtle_pen_down_func(&turtle);
        } else if (strncmp(command, "goto", 4) == 0) {
            double x, y;
            sscanf(command + 4, "%lf %lf", &x, &y);
            turtle_goto(&turtle, x, y);
        } else if (strncmp(command, "heading", 7) == 0) {
            sscanf(command + 7, "%lf", &value);
            turtle_set_heading(&turtle, value);
        } else if (strncmp(command, "square", 6) == 0) {
            sscanf(command + 6, "%lf", &value);
            draw_square(&turtle, value);
        } else if (strncmp(command, "triangle", 8) == 0) {
            sscanf(command + 8, "%lf", &value);
            draw_triangle(&turtle, value);
        } else if (strncmp(command, "circle", 6) == 0) {
            sscanf(command + 6, "%lf", &value);
            draw_circle(&turtle, value);
        } else if (strncmp(command, "star", 4) == 0) {
            sscanf(command + 4, "%lf", &value);
            draw_star(&turtle, value);
        } else if (strncmp(command, "clear", 5) == 0) {
            turtle_init(&turtle);
        } else if (strncmp(command, "quit", 4) == 0) {
            break;
        } else {
            printf("Unknown command\n");
        }
    }
}

// Demo function
void turtle_demo() {
    Turtle turtle;
    
    // Demo 1: Square
    printf("Drawing Square...\n");
    turtle_init(&turtle);
    turtle_goto(&turtle, 20, 20);
    draw_square(&turtle, 15);
    display_canvas(&turtle);
    sleep(2);
    
    // Demo 2: Circle
    printf("Drawing Circle...\n");
    turtle_init(&turtle);
    turtle_goto(&turtle, 50, 25);
    draw_circle(&turtle, 10);
    display_canvas(&turtle);
    sleep(2);
    
    // Demo 3: Star
    printf("Drawing Star...\n");
    turtle_init(&turtle);
    turtle_goto(&turtle, 80, 25);
    draw_star(&turtle, 8);
    display_canvas(&turtle);
    sleep(2);
    
    // Demo 4: Flower
    printf("Drawing Flower...\n");
    turtle_init(&turtle);
    turtle_goto(&turtle, 50, 25);
    draw_flower(&turtle, 8);
    display_canvas(&turtle);
    sleep(2);
    
    // Demo 5: Tree
    printf("Drawing Tree...\n");
    turtle_init(&turtle);
    turtle_goto(&turtle, 50, 40);
    turtle_set_heading(&turtle, 0);
    draw_tree(&turtle, 15, 4);
    display_canvas(&turtle);
    sleep(2);
}

int main() {
    printf("=== Turtle Graphics Demo ===\n\n");
    
    // Run demo
    turtle_demo();
    
    // Interactive mode
    printf("\n=== Interactive Mode ===\n");
    printf("Press Enter to enter interactive mode, or Ctrl+C to exit...\n");
    getchar();
    
    interactive_turtle();
    
    return 0;
}
