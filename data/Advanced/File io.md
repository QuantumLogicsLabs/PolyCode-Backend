# File I/O

Read and write files using FILE* pointers.

## Example
```c
FILE *f = fopen("test.txt","w");
fprintf(f,"Hello\n");
fclose(f);
Practice

Read a file and count the number of lines.
