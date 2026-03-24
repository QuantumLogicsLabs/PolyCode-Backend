package main

import (
	"fmt"
	"time"
)

// Product
type Computer struct {
	cpu      string
	ram      int
	storage  int
	gpu      string
	screen   string
	keyboard string
	mouse    string
	os       string
	price    float64
}

func (c *Computer) String() string {
	return fmt.Sprintf("Computer{CPU: %s, RAM: %dGB, Storage: %dGB, GPU: %s, Screen: %s, Keyboard: %s, Mouse: %s, OS: %s, Price: $%.2f}",
		c.cpu, c.ram, c.storage, c.gpu, c.screen, c.keyboard, c.mouse, c.os, c.price)
}

// Builder interface
type ComputerBuilder interface {
	SetCPU(cpu string) ComputerBuilder
	SetRAM(ram int) ComputerBuilder
	SetStorage(storage int) ComputerBuilder
	SetGPU(gpu string) ComputerBuilder
	SetScreen(screen string) ComputerBuilder
	SetKeyboard(keyboard string) ComputerBuilder
	SetMouse(mouse string) ComputerBuilder
	SetOS(os string) ComputerBuilder
	Build() *Computer
}

// Concrete builder
type GamingComputerBuilder struct {
	computer *Computer
}

func NewGamingComputerBuilder() *GamingComputerBuilder {
	return &GamingComputerBuilder{
		computer: &Computer{
			price: 0,
		},
	}
}

func (gcb *GamingComputerBuilder) SetCPU(cpu string) ComputerBuilder {
	gcb.computer.cpu = cpu
	gcb.computer.price += 300
	return gcb
}

func (gcb *GamingComputerBuilder) SetRAM(ram int) ComputerBuilder {
	gcb.computer.ram = ram
	gcb.computer.price += float64(ram) * 10
	return gcb
}

func (gcb *GamingComputerBuilder) SetStorage(storage int) ComputerBuilder {
	gcb.computer.storage = storage
	gcb.computer.price += float64(storage) * 0.5
	return gcb
}

func (gcb *GamingComputerBuilder) SetGPU(gpu string) ComputerBuilder {
	gcb.computer.gpu = gpu
	gcb.computer.price += 500
	return gcb
}

func (gcb *GamingComputerBuilder) SetScreen(screen string) ComputerBuilder {
	gcb.computer.screen = screen
	gcb.computer.price += 200
	return gcb
}

func (gcb *GamingComputerBuilder) SetKeyboard(keyboard string) ComputerBuilder {
	gcb.computer.keyboard = keyboard
	gcb.computer.price += 100
	return gcb
}

func (gcb *GamingComputerBuilder) SetMouse(mouse string) ComputerBuilder {
	gcb.computer.mouse = mouse
	gcb.computer.price += 50
	return gcb
}

func (gcb *GamingComputerBuilder) SetOS(os string) ComputerBuilder {
	gcb.computer.os = os
	gcb.computer.price += 100
	return gcb
}

func (gcb *GamingComputerBuilder) Build() *Computer {
	return gcb.computer
}

// Office computer builder
type OfficeComputerBuilder struct {
	computer *Computer
}

func NewOfficeComputerBuilder() *OfficeComputerBuilder {
	return &OfficeComputerBuilder{
		computer: &Computer{
			price: 0,
		},
	}
}

func (ocb *OfficeComputerBuilder) SetCPU(cpu string) ComputerBuilder {
	ocb.computer.cpu = cpu
	ocb.computer.price += 200
	return ocb
}

func (ocb *OfficeComputerBuilder) SetRAM(ram int) ComputerBuilder {
	ocb.computer.ram = ram
	ocb.computer.price += float64(ram) * 8
	return ocb
}

func (ocb *OfficeComputerBuilder) SetStorage(storage int) ComputerBuilder {
	ocb.computer.storage = storage
	ocb.computer.price += float64(storage) * 0.3
	return ocb
}

func (ocb *OfficeComputerBuilder) SetGPU(gpu string) ComputerBuilder {
	ocb.computer.gpu = gpu
	ocb.computer.price += 150
	return ocb
}

func (ocb *OfficeComputerBuilder) SetScreen(screen string) ComputerBuilder {
	ocb.computer.screen = screen
	ocb.computer.price += 150
	return ocb
}

func (ocb *OfficeComputerBuilder) SetKeyboard(keyboard string) ComputerBuilder {
	ocb.computer.keyboard = keyboard
	ocb.computer.price += 50
	return ocb
}

func (ocb *OfficeComputerBuilder) SetMouse(mouse string) ComputerBuilder {
	ocb.computer.mouse = mouse
	ocb.computer.price += 25
	return ocb
}

func (ocb *OfficeComputerBuilder) SetOS(os string) ComputerBuilder {
	ocb.computer.os = os
	ocb.computer.price += 100
	return ocb
}

func (ocb *OfficeComputerBuilder) Build() *Computer {
	return ocb.computer
}

// Director
type ComputerDirector struct {
	builder ComputerBuilder
}

func NewComputerDirector(builder ComputerBuilder) *ComputerDirector {
	return &ComputerDirector{builder: builder}
}

func (cd *ComputerDirector) SetBuilder(builder ComputerBuilder) {
	cd.builder = builder
}

func (cd *ComputerDirector) BuildGamingComputer() *Computer {
	return cd.builder.
		SetCPU("Intel i9-12900K").
		SetRAM(32).
		SetStorage(2000).
		SetGPU("NVIDIA RTX 4080").
		SetScreen("27\" 4K 144Hz").
		SetKeyboard("Mechanical RGB").
		SetMouse("Gaming Mouse").
		SetOS("Windows 11").
		Build()
}

func (cd *ComputerDirector) BuildOfficeComputer() *Computer {
	return cd.builder.
		SetCPU("Intel i5-12400").
		SetRAM(16).
		SetStorage(1000).
		SetGPU("Integrated Intel UHD").
		SetScreen("24\" 1080p").
		SetKeyboard("Membrane Keyboard").
		SetMouse("Standard Mouse").
		SetOS("Windows 11 Pro").
		Build()
}

func (cd *ComputerDirector) BuildBudgetComputer() *Computer {
	return cd.builder.
		SetCPU("AMD Ryzen 3 3200G").
		SetRAM(8).
		SetStorage(500).
		SetGPU("Integrated AMD Vega").
		SetScreen("21.5\" 1080p").
		SetKeyboard("Basic Keyboard").
		SetMouse("Basic Mouse").
		SetOS("Ubuntu Linux").
		Build()
}

// Another builder example: Report builder
type Report struct {
	title       string
	author      string
	content     []string
	date        time.Time
	format      string
	pageNumbers bool
	header      bool
	footer      bool
}

func (r *Report) String() string {
	return fmt.Sprintf("Report{Title: %s, Author: %s, Sections: %d, Date: %s, Format: %s, PageNumbers: %t, Header: %t, Footer: %t}",
		r.title, r.author, len(r.content), r.date.Format("2006-01-02"), r.format, r.pageNumbers, r.header, r.footer)
}

type ReportBuilder interface {
	SetTitle(title string) ReportBuilder
	SetAuthor(author string) ReportBuilder
	AddSection(section string) ReportBuilder
	SetDate(date time.Time) ReportBuilder
	SetFormat(format string) ReportBuilder
	EnablePageNumbers() ReportBuilder
	EnableHeader() ReportBuilder
	EnableFooter() ReportBuilder
	Build() *Report
}

type PDFReportBuilder struct {
	report *Report
}

func NewPDFReportBuilder() *PDFReportBuilder {
	return &PDFReportBuilder{
		report: &Report{
			format: "PDF",
		},
	}
}

func (prb *PDFReportBuilder) SetTitle(title string) ReportBuilder {
	prb.report.title = title
	return prb
}

func (prb *PDFReportBuilder) SetAuthor(author string) ReportBuilder {
	prb.report.author = author
	return prb
}

func (prb *PDFReportBuilder) AddSection(section string) ReportBuilder {
	prb.report.content = append(prb.report.content, section)
	return prb
}

func (prb *PDFReportBuilder) SetDate(date time.Time) ReportBuilder {
	prb.report.date = date
	return prb
}

func (prb *PDFReportBuilder) SetFormat(format string) ReportBuilder {
	prb.report.format = format
	return prb
}

func (prb *PDFReportBuilder) EnablePageNumbers() ReportBuilder {
	prb.report.pageNumbers = true
	return prb
}

func (prb *PDFReportBuilder) EnableHeader() ReportBuilder {
	prb.report.header = true
	return prb
}

func (prb *PDFReportBuilder) EnableFooter() ReportBuilder {
	prb.report.footer = true
	return prb
}

func (prb *PDFReportBuilder) Build() *Report {
	return prb.report
}

func main() {
	fmt.Println("=== Builder Pattern Examples ===")

	// Computer builder example
	fmt.Println("\n--- Computer Builder Example ---")

	gamingBuilder := NewGamingComputerBuilder()
	officeBuilder := NewOfficeComputerBuilder()

	director := NewComputerDirector(gamingBuilder)

	fmt.Println("Building Gaming Computer:")
	director.SetBuilder(gamingBuilder)
	gamingPC := director.BuildGamingComputer()
	fmt.Println(gamingPC)

	fmt.Println("\nBuilding Office Computer:")
	director.SetBuilder(officeBuilder)
	officePC := director.BuildOfficeComputer()
	fmt.Println(officePC)

	fmt.Println("\nBuilding Budget Computer:")
	budgetPC := director.BuildBudgetComputer()
	fmt.Println(budgetPC)

	// Manual building
	fmt.Println("\n--- Manual Building Example ---")
	customPC := NewGamingComputerBuilder().
		SetCPU("AMD Ryzen 7 5800X").
		SetRAM(64).
		SetStorage(4000).
		SetGPU("NVIDIA RTX 4090").
		SetScreen("32\" 4K 165Hz").
		SetKeyboard("Custom Mechanical").
		SetMouse("Wireless Gaming Mouse").
		SetOS("Windows 11").
		Build()
	fmt.Println(customPC)

	// Report builder example
	fmt.Println("\n--- Report Builder Example ---")
	
	report := NewPDFReportBuilder().
		SetTitle("Annual Sales Report").
		SetAuthor("John Smith").
		AddSection("Executive Summary").
		AddSection("Sales Analysis").
		AddSection("Market Trends").
		AddSection("Future Projections").
		SetDate(time.Now()).
		EnablePageNumbers().
		EnableHeader().
		EnableFooter().
		Build()
	
	fmt.Println(report)
}
