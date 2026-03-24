package main

import "fmt"

type Speaker interface {
	Speak() string
}

type User struct {
	Name string
	Age  int
}

func (u User) Speak() string {
	return "Hello, my name is " + u.Name
}

func introduce(s Speaker) {
	fmt.Println(s.Speak())
}

func main() {
	user := User{
		Name: "Saad",
		Age:  22,
	}

	fmt.Printf("user: %+v\n", user)
	introduce(user)
}
