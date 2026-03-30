using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

public class AdvancedQueries
{
    private readonly ApplicationDbContext _context;
    
    public AdvancedQueries(ApplicationDbContext context)
    {
        _context = context;
    }
    
    // Complex LINQ queries with EF Core
    public async Task<List<Student>> GetStudentsWithHighGradesAsync()
    {
        return await _context.Students
            .Where(s => s.Enrollments.Any(e => e.Grade == Grade.A))
            .Include(s => s.Enrollments)
            .ThenInclude(e => e.Course)
            .ToListAsync();
    }
    
    // Group by operations
    public async Task<List<dynamic>> GetCourseStatisticsAsync()
    {
        return await _context.Courses
            .SelectMany(c => c.Enrollments, (course, enrollment) => new { course, enrollment })
            .GroupBy(x => x.course)
            .Select(g => new
            {
                CourseTitle = g.Key.Title,
                EnrollmentCount = g.Count(),
                AverageGrade = g.Average(x => x.enrollment.Grade.HasValue ? (int)x.enrollment.Grade : 0),
                AGrades = g.Count(x => x.enrollment.Grade == Grade.A)
            })
            .ToListAsync();
    }
    
    // Raw SQL queries
    public async Task<List<Student>> GetStudentsWithRawSqlAsync(string searchTerm)
    {
        return await _context.Students
            .FromSqlRaw("SELECT * FROM Students WHERE FirstName LIKE '%' + {0} + '%' OR LastName LIKE '%' + {0} + '%'", searchTerm)
            .ToListAsync();
    }
    
    // Stored procedure execution
    public async Task<int> GetStudentCountAsync()
    {
        var countParam = new Microsoft.Data.SqlClient.SqlParameter("@count", System.Data.SqlDbType.Int)
        {
            Direction = System.Data.ParameterDirection.Output
        };
        
        await _context.Database.ExecuteSqlRawAsync(
            "EXEC GetStudentCount @count OUTPUT", countParam);
        
        return (int)countParam.Value;
    }
    
    // Pagination
    public async Task<PagedResult<Student>> GetStudentsPagedAsync(int page, int pageSize)
    {
        var totalCount = await _context.Students.CountAsync();
        var students = await _context.Students
            .Skip((page - 1) * pageSize)
            .Take(pageSize)
            .ToListAsync();
        
        return new PagedResult<Student>
        {
            Items = students,
            TotalCount = totalCount,
            Page = page,
            PageSize = pageSize
        };
    }
    
    // Transaction example
    public async Task<bool> TransferStudentAsync(int studentId, int fromCourseId, int toCourseId)
    {
        using var transaction = await _context.Database.BeginTransactionAsync();
        
        try
        {
            // Remove from old course
            var oldEnrollment = await _context.Enrollments
                .FirstOrDefaultAsync(e => e.StudentId == studentId && e.CourseId == fromCourseId);
            
            if (oldEnrollment != null)
            {
                _context.Enrollments.Remove(oldEnrollment);
            }
            
            // Add to new course
            var newEnrollment = new Enrollment
            {
                StudentId = studentId,
                CourseId = toCourseId,
                EnrollmentDate = DateTime.Now
            };
            
            _context.Enrollments.Add(newEnrollment);
            
            await _context.SaveChangesAsync();
            await transaction.CommitAsync();
            
            return true;
        }
        catch
        {
            await transaction.RollbackAsync();
            return false;
        }
    }
    
    // Eager vs Lazy loading examples
    public async Task DemonstrateLoadingPatterns()
    {
        // Eager loading - loads related data in single query
        var studentsWithEagerLoading = await _context.Students
            .Include(s => s.Enrollments)
            .ThenInclude(e => e.Course)
            .ToListAsync();
        
        // Explicit loading - loads related data on demand
        var student = await _context.Students.FirstAsync();
        await _context.Entry(student).Collection(s => s.Enrollments).LoadAsync();
        
        // Lazy loading (requires Microsoft.EntityFrameworkCore.Proxies)
        // var studentLazy = await _context.Students.FirstAsync();
        // var enrollments = studentLazy.Enrollments; // Automatically loaded
    }
    
    // AsNoTracking for read-only operations
    public async Task<List<Student>> GetAllStudentsReadOnlyAsync()
    {
        return await _context.Students
            .AsNoTracking()
            .ToListAsync();
    }
}

public class PagedResult<T>
{
    public List<T> Items { get; set; }
    public int TotalCount { get; set; }
    public int Page { get; set; }
    public int PageSize { get; set; }
    public int TotalPages => (int)Math.Ceiling((double)TotalCount / PageSize);
}

// Example of using FromSqlInterpolated for safer SQL
public class SafeSqlQueries
{
    private readonly ApplicationDbContext _context;
    
    public SafeSqlQueries(ApplicationDbContext context)
    {
        _context = context;
    }
    
    public async Task<List<Student>> SearchStudentsSafelyAsync(string searchTerm)
    {
        return await _context.Students
            .FromSqlInterpolated($"SELECT * FROM Students WHERE FirstName LIKE '%{searchTerm}%' OR LastName LIKE '%{searchTerm}%'")
            .ToListAsync();
    }
}
