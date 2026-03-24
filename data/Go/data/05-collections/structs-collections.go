package main

import (
	"fmt"
	"sort"
)

func main() {
	fmt.Println("=== Structs as Collections ===")
	
	// Basic struct
	fmt.Println("\n--- Basic Struct ---")
	
	type Person struct {
		Name string
		Age  int
		City string
	}
	
	person1 := Person{Name: "Alice", Age: 30, City: "New York"}
	person2 := Person{Name: "Bob", Age: 25, City: "San Francisco"}
	
	fmt.Printf("Person1: %+v\n", person1)
	fmt.Printf("Person2: %+v\n", person2)
	
	// Slice of structs
	fmt.Println("\n--- Slice of Structs ---")
	
	people := []Person{
		{Name: "Alice", Age: 30, City: "New York"},
		{Name: "Bob", Age: 25, City: "San Francisco"},
		{Name: "Charlie", Age: 35, City: "Chicago"},
		{Name: "Diana", Age: 28, City: "Boston"},
		{Name: "Eve", Age: 32, City: "Seattle"},
	}
	
	fmt.Printf("People slice: %v\n", people)
	
	// Iterating over slice of structs
	fmt.Println("\n--- Iterating Over People ---")
	for i, person := range people {
		fmt.Printf("%d: %s is %d years old from %s\n", 
			i, person.Name, person.Age, person.City)
	}
	
	// Filtering structs
	fmt.Println("\n--- Filtering People ---")
	
	// People over 30
	over30 := filterPeople(people, func(p Person) bool {
		return p.Age > 30
	})
	fmt.Printf("People over 30: %v\n", over30)
	
	// People from specific cities
	cityPeople := filterPeople(people, func(p Person) bool {
		return p.City == "New York" || p.City == "San Francisco"
	})
	fmt.Printf("People from NY or SF: %v\n", cityPeople)
	
	// Sorting structs
	fmt.Println("\n--- Sorting People ---")
	
	// Sort by age
	sort.Slice(people, func(i, j int) bool {
		return people[i].Age < people[j].Age
	})
	fmt.Printf("Sorted by age: %v\n", people)
	
	// Sort by name
	sort.Slice(people, func(i, j int) bool {
		return people[i].Name < people[j].Name
	})
	fmt.Printf("Sorted by name: %v\n", people)
	
	// Map of structs
	fmt.Println("\n--- Map of Structs ---")
	
	employeeMap := map[string]Person{
		"EMP001": {Name: "Alice", Age: 30, City: "New York"},
		"EMP002": {Name: "Bob", Age: 25, City: "San Francisco"},
		"EMP003": {Name: "Charlie", Age: 35, City: "Chicago"},
	}
	
	fmt.Printf("Employee map: %v\n", employeeMap)
	
	for empID, person := range employeeMap {
		fmt.Printf("%s: %s, %d, %s\n", empID, person.Name, person.Age, person.City)
	}
	
	// Complex struct with nested structs
	fmt.Println("\n--- Nested Structs ---")
	
	type Address struct {
		Street  string
		City    string
		State   string
		ZipCode string
	}
	
	type Employee struct {
		ID      string
		Name    string
		Age     int
		Address Address
		Skills  []string
	}
	
	employees := []Employee{
		{
			ID:   "EMP001",
			Name: "Alice Johnson",
			Age:  30,
			Address: Address{
				Street:  "123 Main St",
				City:    "New York",
				State:   "NY",
				ZipCode: "10001",
			},
			Skills: []string{"Go", "Python", "JavaScript"},
		},
		{
			ID:   "EMP002",
			Name: "Bob Smith",
			Age:  25,
			Address: Address{
				Street:  "456 Oak Ave",
				City:    "San Francisco",
				State:   "CA",
				ZipCode: "94102",
			},
			Skills: []string{"Java", "C++", "Python"},
		},
	}
	
	fmt.Printf("Employees: %v\n", employees)
	
	// Access nested struct data
	fmt.Println("\n--- Nested Data Access ---")
	for _, emp := range employees {
		fmt.Printf("%s lives at %s, %s, %s %s\n", 
			emp.Name, emp.Address.Street, emp.Address.City, 
			emp.Address.State, emp.Address.ZipCode)
		fmt.Printf("Skills: %v\n", emp.Skills)
		fmt.Println()
	}
	
	// Struct methods
	fmt.Println("\n--- Struct Methods ---")
	
	type Rectangle struct {
		Width  float64
		Height float64
	}
	
	// Method with value receiver
	func (r Rectangle) Area() float64 {
		return r.Width * r.Height
	}
	
	// Method with pointer receiver
	func (r *Rectangle) Scale(factor float64) {
		r.Width *= factor
		r.Height *= factor
	}
	
	rect := Rectangle{Width: 10, Height: 5}
	fmt.Printf("Rectangle: %.2f x %.2f\n", rect.Width, rect.Height)
	fmt.Printf("Area: %.2f\n", rect.Area())
	
	rect.Scale(2)
	fmt.Printf("After scaling by 2: %.2f x %.2f\n", rect.Width, rect.Height)
	fmt.Printf("New area: %.2f\n", rect.Area())
	
	// Advanced struct operations
	fmt.Println("\n--- Advanced Operations ---")
	
	// Group by city
	cityGroups := groupByCity(people)
	for city, residents := range cityGroups {
		fmt.Printf("%s: %v\n", city, residents)
	}
	
	// Find person by name
	found := findPersonByName(people, "Charlie")
	if found != nil {
		fmt.Printf("Found Charlie: %+v\n", *found)
	}
	
	// Calculate average age
	avgAge := averageAge(people)
	fmt.Printf("Average age: %.2f\n", avgAge)
	
	// Struct pointers
	fmt.Println("\n--- Struct Pointers ---")
	
	personPtr := &Person{Name: "Frank", Age: 40, City: "Austin"}
	fmt.Printf("Person pointer: %+v\n", *personPtr)
	
	// Modify through pointer
	personPtr.Age = 41
	fmt.Printf("After modification: %+v\n", *personPtr)
	
	// Slice of pointers
	peoplePtrs := make([]*Person, len(people))
	for i := range people {
		peoplePtrs[i] = &people[i]
	}
	
	fmt.Printf("First person via pointer: %+v\n", *peoplePtrs[0])
}

// Helper functions for struct collections

func filterPeople(people []Person, predicate func(Person) bool) []Person {
	var result []Person
	for _, person := range people {
		if predicate(person) {
			result = append(result, person)
		}
	}
	return result
}

func groupByCity(people []Person) map[string][]Person {
	groups := make(map[string][]Person)
	for _, person := range people {
		groups[person.City] = append(groups[person.City], person)
	}
	return groups
}

func findPersonByName(people []Person, name string) *Person {
	for _, person := range people {
		if person.Name == name {
			return &person
		}
	}
	return nil
}

func averageAge(people []Person) float64 {
	if len(people) == 0 {
		return 0
	}
	
	sum := 0
	for _, person := range people {
		sum += person.Age
	}
	
	return float64(sum) / float64(len(people))
}

// Advanced struct operations

func sortPeopleByField(people []Person, field string) {
	switch field {
	case "Name":
		sort.Slice(people, func(i, j int) bool {
			return people[i].Name < people[j].Name
		})
	case "Age":
		sort.Slice(people, func(i, j int) bool {
			return people[i].Age < people[j].Age
		})
	case "City":
		sort.Slice(people, func(i, j int) bool {
			return people[i].City < people[j].City
		})
	}
}

func uniqueCities(people []Person) []string {
	citySet := make(map[string]bool)
	for _, person := range people {
		citySet[person.City] = true
	}
	
	cities := make([]string, 0, len(citySet))
	for city := range citySet {
		cities = append(cities, city)
	}
	
	return cities
}

func peopleByAgeRange(people []Person, minAge, maxAge int) []Person {
	return filterPeople(people, func(p Person) bool {
		return p.Age >= minAge && p.Age <= maxAge
	})
}

func addSkill(employees []Employee, empID string, skill string) []Employee {
	for i, emp := range employees {
		if emp.ID == empID {
			// Check if skill already exists
			skillExists := false
			for _, s := range emp.Skills {
				if s == skill {
					skillExists = true
					break
				}
			}
			
			if !skillExists {
				employees[i].Skills = append(employees[i].Skills, skill)
			}
			break
		}
	}
	return employees
}

func employeesWithSkill(employees []Employee, skill string) []Employee {
	var result []Employee
	for _, emp := range employees {
		for _, s := range emp.Skills {
			if s == skill {
				result = append(result, emp)
				break
			}
		}
	}
	return result
}

// Demonstrate advanced operations
func demonstrateAdvancedStructOperations() {
	fmt.Println("\n--- Advanced Struct Operations ---")
	
	people := []Person{
		{Name: "Alice", Age: 30, City: "New York"},
		{Name: "Bob", Age: 25, City: "San Francisco"},
		{Name: "Charlie", Age: 35, City: "Chicago"},
		{Name: "Diana", Age: 28, City: "New York"},
		{Name: "Eve", Age: 32, City: "San Francisco"},
	}
	
	// Unique cities
	cities := uniqueCities(people)
	fmt.Printf("Unique cities: %v\n", cities)
	
	// People by age range
	middleAged := peopleByAgeRange(people, 25, 35)
	fmt.Printf("People aged 25-35: %v\n", middleAged)
	
	// Sort by different fields
	fmt.Println("\n--- Sorting by Different Fields ---")
	
	peopleCopy := make([]Person, len(people))
	copy(peopleCopy, people)
	
	sortPeopleByField(peopleCopy, "Name")
	fmt.Printf("Sorted by name: %v\n", peopleCopy)
	
	sortPeopleByField(peopleCopy, "Age")
	fmt.Printf("Sorted by age: %v\n", peopleCopy)
	
	sortPeopleByField(peopleCopy, "City")
	fmt.Printf("Sorted by city: %v\n", peopleCopy)
}
