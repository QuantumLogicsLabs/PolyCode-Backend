package main

import (
	"fmt"
	"sync"
)

// Observer interface
type Observer interface {
	Update(data interface{})
}

// Subject interface
type Subject interface {
	Attach(observer Observer)
	Detach(observer Observer)
	Notify(data interface{})
}

// Concrete subject
type WeatherStation struct {
	observers []Observer
	temperature float64
	humidity    float64
	mu          sync.RWMutex
}

func NewWeatherStation() *WeatherStation {
	return &WeatherStation{
		observers: make([]Observer, 0),
	}
}

func (ws *WeatherStation) Attach(observer Observer) {
	ws.mu.Lock()
	defer ws.mu.Unlock()
	ws.observers = append(ws.observers, observer)
}

func (ws *WeatherStation) Detach(observer Observer) {
	ws.mu.Lock()
	defer ws.mu.Unlock()
	
	for i, obs := range ws.observers {
		if obs == observer {
			ws.observers = append(ws.observers[:i], ws.observers[i+1:]...)
			break
		}
	}
}

func (ws *WeatherStation) Notify(data interface{}) {
	ws.mu.RLock()
	defer ws.mu.RUnlock()
	
	for _, observer := range ws.observers {
		observer.Update(data)
	}
}

func (ws *WeatherStation) SetMeasurements(temp, humidity float64) {
	ws.mu.Lock()
	defer ws.mu.Unlock()
	
	ws.temperature = temp
	ws.humidity = humidity
	
	data := map[string]float64{
		"temperature": temp,
		"humidity":    humidity,
	}
	
	ws.Notify(data)
}

// Concrete observers
type TemperatureDisplay struct {
	name string
}

func NewTemperatureDisplay(name string) *TemperatureDisplay {
	return &TemperatureDisplay{name: name}
}

func (td *TemperatureDisplay) Update(data interface{}) {
	if measurements, ok := data.(map[string]float64); ok {
		fmt.Printf("[%s] Temperature: %.1f°C\n", td.name, measurements["temperature"])
	}
}

type HumidityDisplay struct {
	name string
}

func NewHumidityDisplay(name string) *HumidityDisplay {
	return &HumidityDisplay{name: name}
}

func (hd *HumidityDisplay) Update(data interface{}) {
	if measurements, ok := data.(map[string]float64); ok {
		fmt.Printf("[%s] Humidity: %.1f%%\n", hd.name, measurements["humidity"])
	}
}

type StatisticsDisplay struct {
	temperatures []float64
	humidities   []float64
	mu           sync.Mutex
}

func NewStatisticsDisplay() *StatisticsDisplay {
	return &StatisticsDisplay{
		temperatures: make([]float64, 0),
		humidities:   make([]float64, 0),
	}
}

func (sd *StatisticsDisplay) Update(data interface{}) {
	sd.mu.Lock()
	defer sd.mu.Unlock()
	
	if measurements, ok := data.(map[string]float64); ok {
		sd.temperatures = append(sd.temperatures, measurements["temperature"])
		sd.humidities = append(sd.humidities, measurements["humidity"])
		
		if len(sd.temperatures) > 0 {
			avgTemp := 0.0
			for _, t := range sd.temperatures {
				avgTemp += t
			}
			avgTemp /= float64(len(sd.temperatures))
			
			avgHum := 0.0
			for _, h := range sd.humidities {
				avgHum += h
			}
			avgHum /= float64(len(sd.humidities))
			
			fmt.Printf("[Stats] Avg Temperature: %.1f°C, Avg Humidity: %.1f%%\n", avgTemp, avgHum)
		}
	}
}

func main() {
	fmt.Println("=== Observer Pattern Example ===")

	// Create weather station
	weatherStation := NewWeatherStation()

	// Create observers
	tempDisplay1 := NewTemperatureDisplay("Living Room")
	tempDisplay2 := NewTemperatureDisplay("Bedroom")
	humidityDisplay := NewHumidityDisplay("Bathroom")
	statsDisplay := NewStatisticsDisplay()

	// Attach observers
	weatherStation.Attach(tempDisplay1)
	weatherStation.Attach(tempDisplay2)
	weatherStation.Attach(humidityDisplay)
	weatherStation.Attach(statsDisplay)

	// Change weather conditions
	fmt.Println("\n--- Weather Update 1 ---")
	weatherStation.SetMeasurements(25.5, 60.2)

	fmt.Println("\n--- Weather Update 2 ---")
	weatherStation.SetMeasurements(24.8, 65.1)

	fmt.Println("\n--- Weather Update 3 ---")
	weatherStation.SetMeasurements(26.2, 58.7)

	// Detach an observer
	fmt.Println("\n--- Detaching Bedroom Display ---")
	weatherStation.Detach(tempDisplay2)

	fmt.Println("\n--- Weather Update 4 ---")
	weatherStation.SetMeasurements(23.9, 70.3)
}
