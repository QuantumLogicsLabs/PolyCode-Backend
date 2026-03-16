"""
Weather API Example - Intermediate Level
=====================================

This example demonstrates how to work with external APIs to fetch weather data.
Shows real-world API integration, error handling, and data processing.

Learning Objectives:
- HTTP requests with the requests library
- API key management
- JSON data parsing
- Error handling for network operations
- Data formatting and display
- Environment variables
- Configuration management
"""

import os
import requests
from typing import Dict, Optional, Any
import json
from datetime import datetime

class WeatherAPI:
    """Weather API client for fetching weather data"""
    
    def __init__(self, api_key: Optional[str] = None):
        """Initialize weather API client
        
        Args:
            api_key: OpenWeatherMap API key (free at openweathermap.org)
        """
        self.api_key = api_key or os.getenv('WEATHER_API_KEY')
        self.base_url = "https://api.openweathermap.org/data/2.5"
        self.session = requests.Session()
        
        if not self.api_key:
            print("Warning: No API key provided. Set WEATHER_API_KEY environment variable.")
    
    def get_current_weather(self, city: str, units: str = "metric") -> Optional[Dict[str, Any]]:
        """Get current weather for a city
        
        Args:
            city: City name
            units: Temperature units (metric, imperial, kelvin)
            
        Returns:
            Weather data dictionary or None if error
        """
        if not self.api_key:
            return {"error": "API key required"}
        
        url = f"{self.base_url}/weather"
        params = {
            "q": city,
            "appid": self.api_key,
            "units": units
        }
        
        try:
            response = self.session.get(url, params=params, timeout=10)
            response.raise_for_status()
            return response.json()
            
        except requests.exceptions.RequestException as e:
            return {"error": f"Network error: {e}"}
        except json.JSONDecodeError:
            return {"error": "Invalid response format"}
    
    def get_weather_forecast(self, city: str, days: int = 5, units: str = "metric") -> Optional[Dict[str, Any]]:
        """Get weather forecast for a city
        
        Args:
            city: City name
            days: Number of days (max 5 for free API)
            units: Temperature units
            
        Returns:
            Forecast data dictionary or None if error
        """
        if not self.api_key:
            return {"error": "API key required"}
        
        url = f"{self.base_url}/forecast"
        params = {
            "q": city,
            "appid": self.api_key,
            "units": units,
            "cnt": days * 8  # 8 forecasts per day (3-hour intervals)
        }
        
        try:
            response = self.session.get(url, params=params, timeout=10)
            response.raise_for_status()
            return response.json()
            
        except requests.exceptions.RequestException as e:
            return {"error": f"Network error: {e}"}
    
    def format_current_weather(self, weather_data: Dict[str, Any]) -> str:
        """Format current weather data for display"""
        if "error" in weather_data:
            return f"Error: {weather_data['error']}"
        
        try:
            city = weather_data["name"]
            country = weather_data["sys"]["country"]
            temp = weather_data["main"]["temp"]
            feels_like = weather_data["main"]["feels_like"]
            humidity = weather_data["main"]["humidity"]
            pressure = weather_data["main"]["pressure"]
            description = weather_data["weather"][0]["description"]
            wind_speed = weather_data["wind"]["speed"]
            
            formatted = f"""
Weather for {city}, {country}
{'='*40}
Temperature: {temp}°C (feels like {feels_like}°C)
Conditions: {description.title()}
Humidity: {humidity}%
Pressure: {pressure} hPa
Wind Speed: {wind_speed} m/s
Updated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
"""
            return formatted
            
        except KeyError as e:
            return f"Error parsing weather data: Missing key {e}"
    
    def format_forecast(self, forecast_data: Dict[str, Any]) -> str:
        """Format forecast data for display"""
        if "error" in forecast_data:
            return f"Error: {forecast_data['error']}"
        
        try:
            city = forecast_data["city"]["name"]
            country = forecast_data["city"]["country"]
            
            formatted = f"\n5-Day Forecast for {city}, {country}\n"
            formatted += "="*50 + "\n"
            
            # Group forecasts by day
            daily_forecasts = {}
            for item in forecast_data["list"]:
                date = datetime.fromtimestamp(item["dt"]).strftime('%Y-%m-%d')
                if date not in daily_forecasts:
                    daily_forecasts[date] = []
                daily_forecasts[date].append(item)
            
            # Display each day's forecast
            for date, forecasts in list(daily_forecasts.items())[:5]:
                temps = [f["main"]["temp"] for f in forecasts]
                avg_temp = sum(temps) / len(temps)
                max_temp = max(temps)
                min_temp = min(temps)
                
                # Get most common weather description
                descriptions = [f["weather"][0]["description"] for f in forecasts]
                main_condition = max(set(descriptions), key=descriptions.count)
                
                formatted += f"{date}: {main_condition.title()}\n"
                formatted += f"  Temp: {min_temp:.1f}°C - {max_temp:.1f}°C (Avg: {avg_temp:.1f}°C)\n\n"
            
            return formatted
            
        except KeyError as e:
            return f"Error parsing forecast data: Missing key {e}"

def interactive_weather_app():
    """Interactive weather application"""
    print("Weather Application")
    print("="*30)
    
    # Initialize API client
    weather = WeatherAPI()
    
    if not weather.api_key:
        print("To use this app:")
        print("1. Get a free API key from https://openweathermap.org/api")
        print("2. Set environment variable: export WEATHER_API_KEY='your_key'")
        print("3. Or pass the key directly: WeatherAPI('your_key')")
        return
    
    while True:
        print("\nOptions:")
        print("1. Current Weather")
        print("2. Weather Forecast")
        print("3. Compare Cities")
        print("4. Exit")
        
        choice = input("\nEnter your choice (1-4): ").strip()
        
        if choice == "4":
            print("Goodbye!")
            break
        
        if choice in ["1", "2", "3"]:
            cities = input("Enter city name(s), separated by commas: ").strip()
            city_list = [city.strip() for city in cities.split(",")]
            
            if choice == "1":
                print("\n" + "="*50)
                for city in city_list:
                    data = weather.get_current_weather(city)
                    formatted = weather.format_current_weather(data)
                    print(formatted)
            
            elif choice == "2":
                print("\n" + "="*50)
                for city in city_list:
                    data = weather.get_weather_forecast(city)
                    formatted = weather.format_forecast(data)
                    print(formatted)
            
            elif choice == "3":
                print("\n" + "="*50)
                print("Current Weather Comparison")
                print("="*50)
                
                weather_data = []
                for city in city_list:
                    data = weather.get_current_weather(city)
                    if "error" not in data:
                        weather_data.append(data)
                
                if weather_data:
                    # Sort by temperature
                    weather_data.sort(key=lambda x: x["main"]["temp"], reverse=True)
                    
                    for data in weather_data:
                        city = data["name"]
                        temp = data["main"]["temp"]
                        feels_like = data["main"]["feels_like"]
                        description = data["weather"][0]["description"]
                        print(f"{city}: {temp}°C (feels like {feels_like}°C) - {description}")
                else:
                    print("No valid weather data retrieved")
        
        else:
            print("Invalid choice. Please try again.")

# Example usage
if __name__ == "__main__":
    # Demo with sample data (no API key required)
    print("Weather API Demo")
    print("="*30)
    
    # Create weather client
    weather = WeatherAPI()
    
    # Example cities to test
    cities = ["London", "New York", "Tokyo", "Sydney"]
    
    print("Current Weather Demo:")
    for city in cities:
        data = weather.get_current_weather(city)
        formatted = weather.format_current_weather(data)
        print(formatted)
    
    # Uncomment to run interactive app (requires API key)
    # interactive_weather_app()

"""
Exercise Ideas:
1. Add weather alerts for extreme conditions
2. Create weather history tracking
3. Add unit conversion (Celsius to Fahrenheit)
4. Implement caching to reduce API calls
5. Add weather map integration
6. Create weather notifications
7. Add historical weather data
8. Implement retry logic for failed requests

Key Concepts Covered:
- HTTP requests with requests library
- API key management
- JSON data parsing
- Error handling and exceptions
- Type hints with typing module
- Environment variables
- Session management
- Data formatting and display
- Interactive CLI applications
- Configuration management
"""
