using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

public class PerformanceOptimization
{
    private readonly ApplicationDbContext _context;
    
    public PerformanceOptimization(ApplicationDbContext context)
    {
        _context = context;
    }
    
    // 1. Use projection instead of loading full entities
    public async Task<List<StudentDto>> GetStudentDtosAsync()
    {
        // Bad: Loads full entities
        // var students = await _context.Students.Include(s => s.Enrollments).ToListAsync();
        
        // Good: Projects only needed data
        return await _context.Students
            .Select(s => new StudentDto
            {
                Id = s.Id,
                FullName = s.FirstName + " " + s.LastName,
                Email = s.Email,
                EnrollmentCount = s.Enrollments.Count
            })
            .ToListAsync();
    }
    
    // 2. Batch operations for better performance
    public async Task UpdateStudentEmailsAsync(Dictionary<int, string> emailUpdates)
    {
        // Bad: Multiple round trips
        // foreach (var update in emailUpdates)
        // {
        //     var student = await _context.Students.FindAsync(update.Key);
        //     student.Email = update.Value;
        //     await _context.SaveChangesAsync();
        // }
        
        // Good: Batch update
        var studentIds = emailUpdates.Keys.ToList();
        var students = await _context.Students
            .Where(s => studentIds.Contains(s.Id))
            .ToListAsync();
        
        foreach (var student in students)
        {
            if (emailUpdates.TryGetValue(student.Id, out var newEmail))
            {
                student.Email = newEmail;
            }
        }
        
        await _context.SaveChangesAsync();
    }
    
    // 3. Use AsNoTracking for read-only queries
    public async Task<List<Student>> GetAllStudentsForReportingAsync()
    {
        return await _context.Students
            .AsNoTracking()
            .ToListAsync();
    }
    
    // 4. Efficient pagination
    public async Task<List<Student>> GetStudentsCursorPagedAsync(int lastId, int pageSize)
    {
        // More efficient than offset-based pagination for large datasets
        return await _context.Students
            .Where(s => s.Id > lastId)
            .OrderBy(s => s.Id)
            .Take(pageSize)
            .AsNoTracking()
            .ToListAsync();
    }
    
    // 5. Split queries for complex queries (EF Core 5+)
    public async Task<List<Student>> GetStudentsWithSplitQueriesAsync()
    {
        return await _context.Students
            .Include(s => s.Enrollments)
            .ThenInclude(e => e.Course)
            .AsSplitQuery()
            .AsNoTracking()
            .ToListAsync();
    }
    
    // 6. Use compiled queries for frequently executed queries
    private static readonly Func<ApplicationDbContext, int, Task<Student>> GetStudentByIdQuery =
        EF.CompileAsyncQuery((ApplicationDbContext context, int id) =>
            context.Students.FirstOrDefault(s => s.Id == id));
    
    public async Task<Student> GetStudentByIdCompiledAsync(int id)
    {
        return await GetStudentByIdQuery(_context, id);
    }
    
    // 7. Bulk operations with EF Core Extensions
    public async Task BulkInsertStudentsAsync(List<Student> students)
    {
        // For bulk operations, consider using EFCore.BulkExtensions
        // await _context.BulkInsertAsync(students);
        
        // Alternative: Use AddRange for better performance than multiple Add calls
        await _context.Students.AddRangeAsync(students);
        await _context.SaveChangesAsync();
    }
    
    // 8. Optimize indexes with proper query design
    public async Task<List<Student>> GetStudentsByEmailDomainAsync(string domain)
    {
        // Ensure there's an index on Email column for this query
        return await _context.Students
            .Where(s => EF.Functions.Like(s.Email, $"%@{domain}"))
            .AsNoTracking()
            .ToListAsync();
    }
    
    // 9. Use value objects for better performance
    public async Task<List<CourseSummary>> GetCourseSummariesAsync()
    {
        return await _context.Courses
            .Select(c => new CourseSummary
            {
                CourseId = c.Id,
                Title = c.Title,
                StudentCount = c.Enrollments.Count,
                AverageGrade = c.Enrollments
                    .Where(e => e.Grade.HasValue)
                    .Average(e => (int)e.Grade)
            })
            .AsNoTracking()
            .ToListAsync();
    }
    
    // 10. Connection pooling and configuration
    public void ConfigureDbContextOptions(DbContextOptionsBuilder optionsBuilder)
    {
        optionsBuilder.UseSqlServer(
            "YourConnectionString",
            options => options.EnableRetryOnFailure(
                maxRetryCount: 3,
                maxRetryDelay: TimeSpan.FromSeconds(30),
                errorNumbersToAdd: null));
        
        // Configure query split behavior globally
        optionsBuilder.UseQueryTrackingBehavior(QueryTrackingBehavior.NoTracking);
        
        // Enable sensitive data logging only in development
        // optionsBuilder.EnableSensitiveDataLogging();
        // optionsBuilder.EnableDetailedErrors();
    }
}

// DTO classes for projection
public class StudentDto
{
    public int Id { get; set; }
    public string FullName { get; set; }
    public string Email { get; set; }
    public int EnrollmentCount { get; set; }
}

public class CourseSummary
{
    public int CourseId { get; set; }
    public string Title { get; set; }
    public int StudentCount { get; set; }
    public double AverageGrade { get; set; }
}

// Example of configuration in Program.cs
public class DbContextConfiguration
{
    public static void ConfigureDbContext(IServiceCollection services, IConfiguration configuration)
    {
        services.AddDbContext<ApplicationDbContext>((serviceProvider, options) =>
        {
            options.UseSqlServer(configuration.GetConnectionString("DefaultConnection"),
                sqlOptions => sqlOptions.EnableRetryOnFailure());
            
            // Performance optimizations
            options.EnableSensitiveDataLogging(false);
            options.EnableDetailedErrors(false);
            options.UseQueryTrackingBehavior(QueryTrackingBehavior.NoTracking);
        });
    }
}
