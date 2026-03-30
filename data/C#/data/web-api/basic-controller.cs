using Microsoft.AspNetCore.Mvc;
using System.Collections.Generic;

[ApiController]
[Route("api/[controller]")]
public class ProductsController : ControllerBase
{
    private static List<Product> _products = new List<Product>
    {
        new Product { Id = 1, Name = "Laptop", Price = 1200 },
        new Product { Id = 2, Name = "Mouse", Price = 25 },
        new Product { Id = 3, Name = "Keyboard", Price = 75 }
    };
    
    [HttpGet]
    public ActionResult<IEnumerable<Product>> GetAll()
    {
        return Ok(_products);
    }
    
    [HttpGet("{id}")]
    public ActionResult<Product> GetById(int id)
    {
        var product = _products.Find(p => p.Id == id);
        if (product == null)
        {
            return NotFound();
        }
        return Ok(product);
    }
    
    [HttpPost]
    public ActionResult<Product> Create(Product product)
    {
        product.Id = _products.Count + 1;
        _products.Add(product);
        return CreatedAtAction(nameof(GetById), new { id = product.Id }, product);
    }
    
    [HttpPut("{id}")]
    public IActionResult Update(int id, Product product)
    {
        var existingProduct = _products.Find(p => p.Id == id);
        if (existingProduct == null)
        {
            return NotFound();
        }
        
        existingProduct.Name = product.Name;
        existingProduct.Price = product.Price;
        
        return NoContent();
    }
    
    [HttpDelete("{id}")]
    public IActionResult Delete(int id)
    {
        var product = _products.Find(p => p.Id == id);
        if (product == null)
        {
            return NotFound();
        }
        
        _products.Remove(product);
        return NoContent();
    }
}

public class Product
{
    public int Id { get; set; }
    public string Name { get; set; }
    public decimal Price { get; set; }
}
