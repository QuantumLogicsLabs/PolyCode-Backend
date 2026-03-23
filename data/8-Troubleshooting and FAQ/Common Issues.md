# Common Issues and Solutions

## Script Won't Run
**Problem**: Double-clicking doesn't execute the script
**Solution**: 
- Ensure file extension is .bat not .txt
- Check file associations
- Run from Command Prompt instead

## Variables Not Expanding
**Problem**: %VAR% shows literal text instead of value
**Solution**:
- Use delayed expansion: !VAR!
- Enable with: setlocal enabledelayedexpansion
- Check variable scope

## Permission Denied
**Problem**: Access denied errors
**Solution**:
- Run as Administrator
- Check file permissions
- Use appropriate user context

## Path Issues
**Problem**: File not found errors
**Solution**:
- Use full paths
- Check current directory: %CD%
- Verify file existence with IF EXIST

## Special Characters
**Problem**: Script breaks with special characters
**Solution**:
- Use quotes: "file with spaces.txt"
- Escape special characters: ^, &, |, <, >
- Use delayed expansion

## Loop Problems
**Problem**: FOR loops not working as expected
**Solution**:
- Check delimiters and tokens
- Use proper variable expansion
- Handle empty files gracefully
