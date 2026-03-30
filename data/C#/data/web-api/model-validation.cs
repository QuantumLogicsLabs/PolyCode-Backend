using Microsoft.AspNetCore.Mvc;
using System.ComponentModel.DataAnnotations;

[ApiController]
[Route("api/[controller]")]
public class UsersController : ControllerBase
{
    [HttpPost]
    public ActionResult<User> CreateUser([FromBody] CreateUserRequest request)
    {
        if (!ModelState.IsValid)
        {
            return BadRequest(ModelState);
        }
        
        var user = new User
        {
            Id = 1,
            Name = request.Name,
            Email = request.Email,
            Age = request.Age
        };
        
        return CreatedAtAction(nameof(GetUser), new { id = user.Id }, user);
    }
    
    [HttpGet("{id}")]
    public ActionResult<User> GetUser(int id)
    {
        // Implementation here
        return Ok(new User { Id = id, Name = "John Doe", Email = "john@example.com", Age = 30 });
    }
}

public class CreateUserRequest
{
    [Required(ErrorMessage = "Name is required")]
    [StringLength(100, MinimumLength = 2, ErrorMessage = "Name must be between 2 and 100 characters")]
    public string Name { get; set; }
    
    [Required(ErrorMessage = "Email is required")]
    [EmailAddress(ErrorMessage = "Invalid email format")]
    public string Email { get; set; }
    
    [Range(18, 120, ErrorMessage = "Age must be between 18 and 120")]
    public int Age { get; set; }
    
    [RegularExpression(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$", 
        ErrorMessage = "Password must be at least 8 characters long and contain uppercase, lowercase, and numbers")]
    public string Password { get; set; }
}

public class User
{
    public int Id { get; set; }
    public string Name { get; set; }
    public string Email { get; set; }
    public int Age { get; set; }
}
