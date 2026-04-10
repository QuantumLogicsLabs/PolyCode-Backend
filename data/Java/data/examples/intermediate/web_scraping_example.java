"""
Web Scraping Example - Intermediate Level
====================================

This example demonstrates web scraping techniques using Java.
Shows how to extract data from websites, handle pagination,
and work with different HTML structures.

Learning Objectives:
- HTTP requests and response handling
- HTML parsing with BeautifulSoup
- Data extraction and cleaning
- Pagination handling
- Error handling and retries
- Rate limiting and respectful scraping
- Data storage (CSV, JSON)
"""

import requests
from bs4 import BeautifulSoup
import csv
import json
import time
import random
from typing import List, Dict, Optional
from urllib.parse import urljoin, urlparse
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class WebScraper:
    """Generic web scraper with common functionality"""
    
    def __init__(self, base_url: str, delay_range: tuple = (1, 3)):
        self.base_url = base_url
        self.session = requests.Session()
        self.delay_range = delay_range
        self.headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        }
        self.session.headers.update(self.headers)
    
    def get_page(self, url: str, retries: int = 3) -> Optional[BeautifulSoup]:
        """Get page content with retry logic"""
        for attempt in range(retries):
            try:
                response = self.session.get(url, timeout=10)
                response.raise_for_status()
                
                # Parse HTML
                soup = BeautifulSoup(response.content, 'html.parser')
                return soup
                
            except requests.RequestException as e:
                logger.warning(f"Attempt {attempt + 1} failed for {url}: {e}")
                if attempt < retries - 1:
                    time.sleep(2 ** attempt)  # Exponential backoff
                else:
                    logger.error(f"Failed to fetch {url} after {retries} attempts")
                    return None
        
        return None
    
    def random_delay(self):
        """Add random delay to be respectful"""
        delay = random.uniform(*self.delay_range)
        time.sleep(delay)
    
    def extract_links(self, soup: BeautifulSoup, link_selector: str) -> List[str]:
        """Extract links from page"""
        links = []
        for link in soup.select(link_selector):
            href = link.get('href')
            if href:
                # Convert relative URLs to absolute
                absolute_url = urljoin(self.base_url, href)
                links.append(absolute_url)
        return links
    
    def extract_text(self, element, default: str = "") -> str:
        """Extract text from element with fallback"""
        return element.get_text(strip=True) if element else default
    
    def extract_attribute(self, element, attribute: str, default: str = "") -> str:
        """Extract attribute from element with fallback"""
        return element.get(attribute, default) if element else default

class BookScraper(WebScraper):
    """Scraper for book information from a demo website"""
    
    def __init__(self):
        # Using a demo API endpoint for this example
        super().__init__("https://jsonplaceholder.typicode.com")
    
    def scrape_book_list(self, max_pages: int = 5) -> List[Dict]:
        """Scrape list of books from multiple pages"""
        all_books = []
        
        for page in range(1, max_pages + 1):
            logger.info(f"Scraping page {page}")
            
            # Construct URL for current page
            url = f"{self.base_url}/posts?_page={page}"
            
            # Get page content
            soup = self.get_page(url)
            if not soup:
                continue
            
            # Extract book information
            books = self._extract_books_from_page(soup)
            all_books.extend(books)
            
            # Add delay between requests
            self.random_delay()
            
            # Check if there are more pages
            if len(books) == 0:
                logger.info("No more books found, stopping pagination")
                break
        
        logger.info(f"Total books scraped: {len(all_books)}")
        return all_books
    
    def _extract_books_from_page(self, soup: BeautifulSoup) -> List[Dict]:
        """Extract book information from a single page"""
        books = []
        
        # Using JSONPlaceholder posts as example "books"
        # In real scraping, you'd use actual HTML selectors
        posts = soup.find_all('div', class_='post') if soup.find_all('div') else []
        
        for post in posts:
            book = {
                'id': self.extract_text(post.find('h2')),
                'title': self.extract_text(post.find('h2')),
                'author': self.extract_text(post.find('p')),
                'content': self.extract_text(post.find('div', class_='content')),
                'url': self.extract_attribute(post.find('a'), 'href'),
                'timestamp': self.extract_attribute(post.find('time'), 'datetime')
            }
            
            # Clean and validate data
            book = self._clean_book_data(book)
            if book['id']:  # Only add if we got valid data
                books.append(book)
        
        return books
    
    def _clean_book_data(self, book: Dict) -> Dict:
        """Clean and validate book data"""
        # Clean title
        if book['title']:
            book['title'] = book['title'].strip()
            book['title'] = ' '.join(book['title'].split())  # Normalize whitespace
        
        # Clean author
        if book['author']:
            book['author'] = book['author'].strip()
        
        # Extract numeric ID
        if book['id']:
            try:
                book['id'] = int(book['id'])
            except ValueError:
                book['id'] = None
        
        # Validate required fields
        if not book['title'] or not book['id']:
            logger.warning(f"Invalid book data: {book}")
            return {}
        
        return book
    
    def save_to_csv(self, books: List[Dict], filename: str = "books.csv"):
        """Save books to CSV file"""
        if not books:
            logger.warning("No books to save")
            return
        
        fieldnames = ['id', 'title', 'author', 'content', 'url', 'timestamp']
        
        try:
            with open(filename, 'w', newline='', encoding='utf-8') as csvfile:
                writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
                writer.writeheader()
                writer.writerows(books)
            
            logger.info(f"Saved {len(books)} books to {filename}")
            
        except Exception as e:
            logger.error(f"Error saving to CSV: {e}")
    
    def save_to_json(self, books: List[Dict], filename: str = "books.json"):
        """Save books to JSON file"""
        try:
            with open(filename, 'w', encoding='utf-8') as jsonfile:
                json.dump(books, jsonfile, indent=2, ensure_ascii=False)
            
            logger.info(f"Saved {len(books)} books to {filename}")
            
        except Exception as e:
            logger.error(f"Error saving to JSON: {e}")

class NewsScraper(WebScraper):
    """Scraper for news articles with more complex HTML structure"""
    
    def __init__(self):
        # Using Hacker News API for this example
        super().__init__("https://hacker-news.firebaseio.com")
    
    def scrape_top_stories(self, limit: int = 30) -> List[Dict]:
        """Scrape top stories from Hacker News"""
        url = f"{self.base_url}/v0/topstories.json"
        
        response = self.session.get(url)
        response.raise_for_status()
        
        story_ids = response.json()[:limit]
        
        stories = []
        for story_id in story_ids:
            # Get individual story details
            story_url = f"{self.base_url}/v0/item/{story_id}.json"
            story_response = self.session.get(story_url)
            story_response.raise_for_status()
            
            story_data = story_response.json()
            
            story = {
                'id': story_data.get('id'),
                'title': story_data.get('title'),
                'url': story_data.get('url'),
                'score': story_data.get('score'),
                'by': story_data.get('by'),
                'time': story_data.get('time'),
                'descendants': story_data.get('descendants', 0)
            }
            
            stories.append(story)
            
            # Add delay between requests
            self.random_delay()
        
        return stories
    
    def analyze_stories(self, stories: List[Dict]) -> Dict:
        """Analyze scraped stories for insights"""
        if not stories:
            return {}
        
        total_score = sum(story.get('score', 0) for story in stories)
        avg_score = total_score / len(stories)
        
        top_contributors = {}
        for story in stories:
            author = story.get('by', 'unknown')
            top_contributors[author] = top_contributors.get(author, 0) + 1
        
        # Sort by contribution count
        top_contributors = dict(sorted(top_contributors.items(), 
                                     key=lambda x: x[1], reverse=True))[:10]
        
        analysis = {
            'total_stories': len(stories),
            'total_score': total_score,
            'average_score': avg_score,
            'top_contributors': top_contributors,
            'scraped_at': time.strftime('%Y-%m-%d %H:%M:%S')
        }
        
        return analysis

class ProductScraper(WebScraper):
    """E-commerce product scraper with price tracking"""
    
    def __init__(self):
        # Using a mock e-commerce site
        super().__init__("https://fakestoreapi.com")
    
    def scrape_products(self, category: str = "electronics") -> List[Dict]:
        """Scrape products from a specific category"""
        url = f"{self.base_url}/products/category/{category}"
        
        soup = self.get_page(url)
        if not soup:
            return []
        
        products = []
        
        # Extract product cards (mock structure)
        product_cards = soup.find_all('div', class_='product-card')
        
        for card in product_cards:
            product = {
                'name': self.extract_text(card.find('h3', class_='product-name')),
                'price': self._extract_price(card.find('span', class_='price')),
                'original_price': self._extract_price(card.find('span', class_='original-price')),
                'discount': self._extract_discount(card),
                'rating': self._extract_rating(card.find('div', class_='rating')),
                'reviews': self._extract_review_count(card.find('span', class_='review-count')),
                'availability': self._extract_availability(card.find('div', class_='stock')),
                'image_url': self.extract_attribute(card.find('img'), 'src'),
                'product_url': self.extract_attribute(card.find('a'), 'href')
            }
            
            # Validate and clean product data
            product = self._clean_product_data(product)
            if product['name']:
                products.append(product)
        
        return products
    
    def _extract_price(self, price_element) -> Optional[float]:
        """Extract and parse price from element"""
        if not price_element:
            return None
        
        price_text = price_element.get_text(strip=True)
        if not price_text:
            return None
        
        # Remove currency symbols and parse
        import re
        price_match = re.search(r'[\d,]+\.?\d*', price_text)
        
        if price_match:
            try:
                return float(price_match.group().replace(',', ''))
            except ValueError:
                return None
        
        return None
    
    def _extract_discount(self, product_element) -> Optional[int]:
        """Calculate discount percentage"""
        price = product_element.get('price', 0)
        original_price = product_element.get('original_price', 0)
        
        if price and original_price and original_price > price:
            discount = int(((original_price - price) / original_price) * 100)
            return discount
        
        return None
    
    def _extract_rating(self, rating_element) -> Optional[float]:
        """Extract rating from stars or numeric rating"""
        if not rating_element:
            return None
        
        # Try to extract numeric rating
        rating_text = rating_element.get_text(strip=True)
        
        import re
        rating_match = re.search(r'[\d.]+', rating_text)
        
        if rating_match:
            try:
                return float(rating_match.group())
            except ValueError:
                return None
        
        # Try to count stars
        stars = rating_element.find_all('i', class_='star')
        if stars:
            return len(stars)
        
        return None
    
    def _extract_review_count(self, review_element) -> Optional[int]:
        """Extract review count"""
        if not review_element:
            return None
        
        review_text = review_element.get_text(strip=True)
        
        import re
        review_match = re.search(r'(\d+)', review_text)
        
        if review_match:
            try:
                return int(review_match.group())
            except ValueError:
                return None
        
        return None
    
    def _extract_availability(self, stock_element) -> str:
        """Extract product availability"""
        if not stock_element:
            return "Unknown"
        
        stock_text = stock_element.get_text(strip=True).lower()
        
        if 'in stock' in stock_text or 'available' in stock_text:
            return "In Stock"
        elif 'out of stock' in stock_text or 'unavailable' in stock_text:
            return "Out of Stock"
        elif 'limited' in stock_text:
            return "Limited Stock"
        else:
            return "Unknown"
    
    def _clean_product_data(self, product: Dict) -> Dict:
        """Clean and validate product data"""
        # Clean name
        if product['name']:
            product['name'] = product['name'].strip()
        
        # Validate price
        if product['price'] and product['price'] <= 0:
            product['price'] = None
        
        # Validate rating
        if product['rating']:
            product['rating'] = min(5.0, max(0.0, product['rating']))
        
        return product

def demonstrate_scraping():
    """Demonstrate different scraping scenarios"""
    
    print("=== Web Scraping Examples ===\n")
    
    # Example 1: Book scraping
    print("1. Scraping book information...")
    book_scraper = BookScraper()
    books = book_scraper.scrape_book_list(max_pages=3)
    
    if books:
        book_scraper.save_to_csv(books, "scraped_books.csv")
        book_scraper.save_to_json(books, "scraped_books.json")
        print(f"Successfully scraped {len(books)} books")
    else:
        print("No books found")
    
    print("\n" + "="*50 + "\n")
    
    # Example 2: News scraping
    print("2. Scraping news stories...")
    news_scraper = NewsScraper()
    stories = news_scraper.scrape_top_stories(limit=10)
    
    if stories:
        analysis = news_scraper.analyze_stories(stories)
        print(f"Successfully scraped {len(stories)} stories")
        print(f"Average score: {analysis['average_score']:.1f}")
        print(f"Top contributor: {list(analysis['top_contributors'].keys())[0] if analysis['top_contributors'] else 'None'}")
    else:
        print("No stories found")
    
    print("\n" + "="*50 + "\n")
    
    # Example 3: Product scraping
    print("3. Scraping product information...")
    product_scraper = ProductScraper()
    products = product_scraper.scrape_products("electronics")
    
    if products:
        print(f"Successfully scraped {len(products)} products")
        
        # Show some sample products
        for i, product in enumerate(products[:3]):
            print(f"\nProduct {i+1}:")
            print(f"  Name: {product.get('name', 'N/A')}")
            print(f"  Price: ${product.get('price', 'N/A')}")
            print(f"  Rating: {product.get('rating', 'N/A')}/5")
            print(f"  Availability: {product.get('availability', 'N/A')}")
    else:
        print("No products found")

def demonstrate_error_handling():
    """Demonstrate error handling in scraping"""
    
    print("\n=== Error Handling Examples ===\n")
    
    scraper = WebScraper("https://invalid-url-that-does-not-exist.com")
    
    # Test invalid URL
    print("Testing invalid URL handling...")
    soup = scraper.get_page("https://invalid-url.com/page")
    
    if soup is None:
        print("✓ Correctly handled invalid URL")
    else:
        print("✗ Should have failed for invalid URL")
    
    # Test timeout handling
    print("\nTesting timeout handling...")
    scraper = WebScraper("https://httpbin.org/delay/10")
    soup = scraper.get_page("https://httpbin.org/delay/10")
    
    if soup is None:
        print("✓ Correctly handled timeout")
    else:
        print("✓ Successfully handled delayed response")

def demonstrate_respectful_scraping():
    """Demonstrate respectful scraping practices"""
    
    print("\n=== Respectful Scraping Practices ===\n")
    
    scraper = WebScraper("https://jsonplaceholder.typicode.com", delay_range=(2, 4))
    
    print("Scraping with delays between requests...")
    
    for i in range(3):
        print(f"Request {i+1}...")
        soup = scraper.get_page(f"{scraper.base_url}/posts/{i+1}")
        
        if soup:
            title = scraper.extract_text(soup.find('title'))
            print(f"  Title: {title}")
        
        if i < 2:  # Don't delay after last request
            scraper.random_delay()
    
    print("✓ Scraped with respectful delays")

if __name__ == "__main__":
    """Main execution"""
    try:
        demonstrate_scraping()
        demonstrate_error_handling()
        demonstrate_respectful_scraping()
        
        print("\n=== Scraping Complete ===")
        print("Files created:")
        print("- scraped_books.csv")
        print("- scraped_books.json")
        
    except KeyboardInterrupt:
        print("\nScraping interrupted by user")
    except Exception as e:
        print(f"\nUnexpected error: {e}")
        logger.exception("Scraping failed")

"""
Exercise Ideas:
1. Add proxy support to handle IP rotation
2. Implement concurrent scraping with ThreadPoolExecutor
3. Add JavaScript rendering support with Selenium
4. Create a generic scraper class that works with any site
5. Add data validation and deduplication
6. Implement rate limiting with exponential backoff
7. Add support for different output formats (Excel, XML)
8. Create a web interface for the scraper

Key Concepts Covered:
- HTTP requests and session management
- HTML parsing with BeautifulSoup
- Data extraction and cleaning
- Error handling and retries
- Rate limiting and respectful scraping
- Data storage in multiple formats
- Pagination handling
- Complex data structure parsing

Best Practices:
- Always check robots.txt before scraping
- Add delays between requests
- Handle errors gracefully
- Validate and clean extracted data
- Use appropriate user agents
- Respect rate limits
- Store data in structured formats
- Log scraping activities
- Handle different data formats gracefully

Security Considerations:
- Validate all extracted data
- Sanitize file paths
- Handle malformed HTML gracefully
- Use secure connection methods
- Respect privacy and terms of service
- Implement proper error handling
- Avoid executing untrusted code
- Use secure storage for sensitive data
"""
