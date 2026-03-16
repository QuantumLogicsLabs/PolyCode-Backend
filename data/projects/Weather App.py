"""
Weather App
A simple weather application that fetches and displays weather information.
"""

import json
import urllib.request
import urllib.parse
from datetime import datetime
import sys

class WeatherApp:
    """Weather information application."""
    
    def __init__(self, api_key=None):
        """
        Initialize weather app.
        
        Args:
            api_key (str): OpenWeatherMap API key (optional for demo)
        """
        self.api_key = api_key
        self.base_url = "http://api.openweathermap.org/data/2.5/weather"
        self.demo_data = self._get_demo_data()
    
    def _get_demo_data(self):
        """Get demo weather data for testing without API key."""
        return {
            'New York': {
                'temp': 22.5,
                'feels_like': 24.0,
                'humidity': 65,
                'pressure': 1013,
                'description': 'partly cloudy',
                'wind_speed': 3.5,
                'visibility': 10000,
                'sunrise': datetime.now().replace(hour=6, minute=30),
                'sunset': datetime.now().replace(hour=19, minute=45)
            },
            'London': {
                'temp': 15.2,
                'feels_like': 14.8,
                'humidity': 78,
                'pressure': 1015,
                'description': 'light rain',
                'wind_speed': 5.2,
                'visibility': 8000,
                'sunrise': datetime.now().replace(hour=6, minute=15),
                'sunset': datetime.now().replace(hour=18, minute=30)
            },
            'Tokyo': {
                'temp': 28.3,
                'feels_like': 30.1,
                'humidity': 72,
                'pressure': 1010,
                'description': 'clear sky',
                'wind_speed': 2.8,
                'visibility': 12000,
                'sunrise': datetime.now().replace(hour=5, minute=45),
                'sunset': datetime.now().replace(hour=20, minute=15)
            }
        }
    
    def get_weather(self, city):
        """
        Get weather information for a city.
        
        Args:
            city (str): City name
            
        Returns:
            dict: Weather information or None if failed
        """
        if not self.api_key:
            # Use demo data for testing
            return self._get_demo_weather(city)
        
        try:
            # Build URL with API key
            params = {
                'q': city,
                'appid': self.api_key,
                'units': 'metric'  # Celsius
            }
            url = f"{self.base_url}?{urllib.parse.urlencode(params)}"
            
            # Make API request
            with urllib.request.urlopen(url) as response:
                data = json.loads(response.read().decode('utf-8'))
                return self._parse_weather_data(data)
        
        except Exception as e:
            print(f"Error fetching weather: {e}")
            return None
    
    def _get_demo_weather(self, city):
        """Get demo weather data for testing."""
        city_key = city.title()
        if city_key in self.demo_data:
            return {
                'city': city,
                'success': True,
                **self.demo_data[city_key]
            }
        else:
            return {
                'city': city,
                'success': False,
                'error': 'City not found in demo data'
            }
    
    def _parse_weather_data(self, data):
        """Parse weather data from API response."""
        try:
            weather = {
                'city': data['name'],
                'country': data['sys']['country'],
                'success': True,
                'temp': data['main']['temp'],
                'feels_like': data['main']['feels_like'],
                'humidity': data['main']['humidity'],
                'pressure': data['main']['pressure'],
                'description': data['weather'][0]['description'],
                'wind_speed': data.get('wind', {}).get('speed', 0),
                'visibility': data.get('visibility', 0) / 1000,  # Convert to km
                'sunrise': datetime.fromtimestamp(data['sys']['sunrise']),
                'sunset': datetime.fromtimestamp(data['sys']['sunset'])
            }
            return weather
        except KeyError as e:
            return {
                'city': data.get('name', 'Unknown'),
                'success': False,
                'error': f'Invalid data format: {e}'
            }
    
    def display_weather(self, weather_data):
        """
        Display weather information in a formatted way.
        
        Args:
            weather_data (dict): Weather information
        """
        if not weather_data or not weather_data.get('success'):
            error = weather_data.get('error', 'Unknown error') if weather_data else 'No data available'
            print(f"❌ Unable to get weather: {error}")
            return
        
        print("\n" + "="*50)
        print(f"Weather for {weather_data['city']}")
        print("="*50)
        
        # Temperature
        temp = weather_data['temp']
        feels_like = weather_data['feels_like']
        print(f"🌡️  Temperature: {temp:.1f}°C (feels like {feels_like:.1f}°C)")
        
        # Description
        description = weather_data['description']
        icon = self._get_weather_icon(description)
        print(f"{icon} Conditions: {description.title()}")
        
        # Humidity and Pressure
        humidity = weather_data['humidity']
        pressure = weather_data['pressure']
        print(f"💧 Humidity: {humidity}%")
        print(f"📊 Pressure: {pressure} hPa")
        
        # Wind and Visibility
        wind_speed = weather_data['wind_speed']
        visibility = weather_data['visibility']
        print(f"💨 Wind: {wind_speed:.1f} m/s")
        print(f"👁️  Visibility: {visibility:.1f} km")
        
        # Sun times
        sunrise = weather_data['sunrise']
        sunset = weather_data['sunset']
        print(f"🌅 Sunrise: {sunrise.strftime('%H:%M')}")
        print(f"🌇 Sunset: {sunset.strftime('%H:%M')}")
        
        print("="*50)
    
    def _get_weather_icon(self, description):
        """Get weather icon based on description."""
        desc_lower = description.lower()
        
        if 'clear' in desc_lower:
            return "☀️"
        elif 'cloud' in desc_lower:
            return "☁️"
        elif 'rain' in desc_lower or 'drizzle' in desc_lower:
            return "🌧️"
        elif 'snow' in desc_lower:
            return "❄️"
        elif 'thunder' in desc_lower:
            return "⛈️"
        elif 'mist' in desc_lower or 'fog' in desc_lower:
            return "🌫️"
        elif 'wind' in desc_lower:
            return "💨"
        else:
            return "🌤️"
    
    def get_weather_forecast(self, city, days=5):
        """
        Get weather forecast (demo implementation).
        
        Args:
            city (str): City name
            days (int): Number of days to forecast
            
        Returns:
            list: Forecast data
        """
        # This is a demo implementation
        # In a real app, you'd use the forecast API endpoint
        base_weather = self.get_weather(city)
        
        if not base_weather or not base_weather.get('success'):
            return []
        
        forecast = []
        base_temp = base_weather['temp']
        
        for i in range(days):
            forecast_date = datetime.now() + timedelta(days=i+1)
            
            # Simulate temperature variation
            temp_variation = (i - days//2) * 2  # Simple variation pattern
            forecast_temp = base_temp + temp_variation
            
            # Simulate weather conditions
            conditions = ['sunny', 'partly cloudy', 'cloudy', 'light rain', 'clear']
            condition = conditions[i % len(conditions)]
            
            forecast.append({
                'date': forecast_date,
                'temp': forecast_temp,
                'condition': condition,
                'humidity': base_weather['humidity'] + (i * 2) % 20 - 10
            })
        
        return forecast
    
    def display_forecast(self, forecast):
        """
        Display weather forecast.
        
        Args:
            forecast (list): Forecast data
        """
        if not forecast:
            print("No forecast data available.")
            return
        
        print("\n" + "="*50)
        print("WEATHER FORECAST")
        print("="*50)
        
        for day in forecast:
            date_str = day['date'].strftime('%A, %B %d')
            temp = day['temp']
            condition = day['condition']
            icon = self._get_weather_icon(condition)
            humidity = day['humidity']
            
            print(f"{date_str}: {icon} {temp:.1f}°C, {condition.title()}, Humidity: {humidity}%")
        
        print("="*50)
    
    def save_weather_report(self, weather_data, filename=None):
        """
        Save weather report to file.
        
        Args:
            weather_data (dict): Weather information
            filename (str): Output filename
        """
        if not filename:
            timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
            city = weather_data.get('city', 'unknown').replace(' ', '_')
            filename = f"weather_report_{city}_{timestamp}.txt"
        
        try:
            with open(filename, 'w') as f:
                f.write(f"Weather Report for {weather_data['city']}\n")
                f.write(f"Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
                f.write("="*50 + "\n\n")
                
                f.write(f"Temperature: {weather_data['temp']:.1f}°C\n")
                f.write(f"Feels Like: {weather_data['feels_like']:.1f}°C\n")
                f.write(f"Conditions: {weather_data['description'].title()}\n")
                f.write(f"Humidity: {weather_data['humidity']}%\n")
                f.write(f"Pressure: {weather_data['pressure']} hPa\n")
                f.write(f"Wind Speed: {weather_data['wind_speed']:.1f} m/s\n")
                f.write(f"Visibility: {weather_data['visibility']:.1f} km\n")
                
                if 'sunrise' in weather_data:
                    f.write(f"Sunrise: {weather_data['sunrise'].strftime('%H:%M')}\n")
                    f.write(f"Sunset: {weather_data['sunset'].strftime('%H:%M')}\n")
            
            print(f"✅ Weather report saved to {filename}")
        
        except Exception as e:
            print(f"❌ Error saving report: {e}")

def main():
    """Main application entry point."""
    print("Weather App")
    print("="*30)
    print("Get current weather information for any city")
    print()
    
    # Initialize app (no API key for demo)
    app = WeatherApp()
    
    # Demo cities
    demo_cities = ["New York", "London", "Tokyo", "Paris", "Sydney"]
    
    print("Demo mode - Available cities:", ", ".join(demo_cities))
    print("(Get a free API key from openweathermap.org for real data)")
    print()
    
    while True:
        print("\nOptions:")
        print("1. Get current weather")
        print("2. Get weather forecast")
        print("3. Save weather report")
        print("4. Exit")
        
        choice = input("\nSelect option (1-4): ").strip()
        
        if choice == "1":
            city = input("Enter city name: ").strip()
            if city:
                weather = app.get_weather(city)
                app.display_weather(weather)
            else:
                print("Please enter a city name.")
        
        elif choice == "2":
            city = input("Enter city name: ").strip()
            if city:
                forecast = app.get_weather_forecast(city, days=5)
                app.display_forecast(forecast)
            else:
                print("Please enter a city name.")
        
        elif choice == "3":
            city = input("Enter city name: ").strip()
            if city:
                weather = app.get_weather(city)
                if weather and weather.get('success'):
                    app.save_weather_report(weather)
                else:
                    print("Cannot save report - weather data unavailable.")
            else:
                print("Please enter a city name.")
        
        elif choice == "4":
            print("Goodbye!")
            break
        
        else:
            print("Invalid option. Please try again.")

if __name__ == "__main__":
    main()
