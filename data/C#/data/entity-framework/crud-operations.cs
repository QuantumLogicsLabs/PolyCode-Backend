using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

public class StudentService
{
    private readonly ApplicationDbContext _context;
    
    public StudentService(ApplicationDbContext context)
    {
        _context = context;
    }
    
    // CREATE - Add new student
    public async Task<Student> AddStudentAsync(Student student)
    {
        _context.Students.Add(student);
        await _context.SaveChangesAsync();
        return student;
    }
    
    // READ - Get all students
    public async Task<List<Student>> GetAllStudentsAsync()
    {
        return await _context.Students
            .Include(s => s.Enrollments)
            .ThenInclude(e => e.Course)
            .ToListAsync();
    }
    
    // READ - Get student by ID
    public async Task<Student> GetStudentByIdAsync(int id)
    {
        return await _context.Students
            .Include(s => s.Enrollments)
            .ThenInclude(e => e.Course)
            .FirstOrDefaultAsync(s => s.Id == id);
    }
    
    // UPDATE - Update student
    public async Task<Student> UpdateStudentAsync(Student student)
    {
        _context.Entry(student).State = EntityState.Modified;
        await _context.SaveChangesAsync();
        return student;
    }
    
    // DELETE - Delete student
    public async Task<bool> DeleteStudentAsync(int id)
    {
        var student = await _context.Students.FindAsync(id);
        if (student == null)
            return false;
        
        _context.Students.Remove(student);
        await _context.SaveChangesAsync();
        return true;
    }
    
    // SEARCH - Find students by name
    public async Task<List<Student>> SearchStudentsByNameAsync(string searchTerm)
    {
        return await _context.Students
            .Where(s => s.FirstName.Contains(searchTerm) || s.LastName.Contains(searchTerm))
            .ToListAsync();
    }
    
    // ENROLL - Enroll student in course
    public async Task<Enrollment> EnrollStudentAsync(int studentId, int courseId)
    {
        var enrollment = new Enrollment
        {
            StudentId = studentId,
            CourseId = courseId,
            EnrollmentDate = DateTime.Now
        };
        
        _context.Enrollments.Add(enrollment);
        await _context.SaveChangesAsync();
        return enrollment;
    }
    
    // ASSIGN GRADE - Update enrollment grade
    public async Task<Enrollment> AssignGradeAsync(int enrollmentId, Grade grade)
    {
        var enrollment = await _context.Enrollments.FindAsync(enrollmentId);
        if (enrollment != null)
        {
            enrollment.Grade = grade;
            await _context.SaveChangesAsync();
        }
        return enrollment;
    }
}

// Example usage in a controller or service
public class ExampleUsage
{
    private readonly StudentService _studentService;
    
    public ExampleUsage(StudentService studentService)
    {
        _studentService = studentService;
    }
    
    public async Task DemonstrateCrudOperations()
    {
        // CREATE
        var newStudent = new Student
        {
            FirstName = "John",
            LastName = "Doe",
            Email = "john.doe@example.com",
            EnrollmentDate = DateTime.Now
        };
        
        var addedStudent = await _studentService.AddStudentAsync(newStudent);
        Console.WriteLine($"Added student: {addedStudent.FirstName} {addedStudent.LastName}");
        
        // READ
        var allStudents = await _studentService.GetAllStudentsAsync();
        Console.WriteLine($"Total students: {allStudents.Count}");
        
        // UPDATE
        addedStudent.Email = "john.newemail@example.com";
        var updatedStudent = await _studentService.UpdateStudentAsync(addedStudent);
        Console.WriteLine($"Updated email: {updatedStudent.Email}");
        
        // DELETE
        var deleted = await _studentService.DeleteStudentAsync(addedStudent.Id);
        Console.WriteLine($"Student deleted: {deleted}");
    }
}
