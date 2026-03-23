# Project Management in Ruby

## Overview

Project management is crucial for successful software development. Ruby provides excellent tools and frameworks for managing projects, from simple task tracking to complex project management systems. This guide covers project management concepts, tools, and Ruby implementations.

## Project Management Fundamentals

### Project Lifecycle
```ruby
class ProjectLifecycle
  def self.phases
    {
      initiation: {
        description: "Project kickoff and requirements gathering",
        activities: [
          "Define project scope and objectives",
          "Identify stakeholders",
          "Create project charter",
          "Conduct feasibility study",
          "Secure project approval"
        ],
        deliverables: [
          "Project charter",
          "Requirements document",
          "Stakeholder analysis",
          "Feasibility report"
        ]
      },
      planning: {
        description: "Detailed project planning and resource allocation",
        activities: [
          "Create work breakdown structure",
          "Develop project schedule",
          "Allocate resources and budget",
          "Risk assessment and mitigation",
          "Quality planning"
        ],
        deliverables: [
          "Project plan",
          "Schedule and timeline",
          "Resource allocation plan",
          "Risk register",
          "Quality management plan"
        ]
      },
      execution: {
        description: "Project implementation and monitoring",
        activities: [
          "Execute project tasks",
          "Monitor progress against plan",
          "Manage changes and issues",
          "Communicate with stakeholders",
          "Quality assurance"
        ],
        deliverables: [
          "Completed deliverables",
          "Progress reports",
          "Change requests",
          "Issue logs",
          "Quality metrics"
        ]
      },
      monitoring: {
        description: "Performance tracking and control",
        activities: [
          "Track project metrics",
          "Monitor budget and schedule",
          "Quality control",
          "Risk monitoring",
          "Stakeholder communication"
        ],
        deliverables: [
          "Performance reports",
          "Budget tracking",
          "Schedule updates",
          "Quality reports",
          "Risk status updates"
        ]
      },
      closure: {
        description: "Project completion and lessons learned",
        activities: [
          "Final deliverables acceptance",
          "Project documentation",
          "Lessons learned analysis",
          "Resource release",
          "Stakeholder sign-off"
        ],
        deliverables: [
          "Final project report",
          "Lessons learned document",
          "Project archive",
          "Resource release plan",
          "Stakeholder acceptance"
        ]
      }
    }
  end

  def self.success_criteria
    {
      scope: [
        "All deliverables completed as specified",
        "Project objectives achieved",
        "Stakeholder requirements met"
      ],
      schedule: [
        "Project completed on time",
        "Milestones achieved as planned",
        "No schedule overruns"
      ],
      budget: [
        "Project completed within budget",
        "Cost controls effective",
        "ROI targets met"
      ],
      quality: [
        "Quality standards met",
        "Defect rates within acceptable limits",
        "User satisfaction high"
      ],
      stakeholder: [
        "Stakeholder expectations managed",
        "Communication effective",
        "Relationships maintained"
      ]
    }
  end
end

# Usage example
puts "Project Lifecycle Phases:"
ProjectLifecycle.phases.each do |phase, details|
  puts "\n#{phase.to_s.capitalize}:"
  puts "  Description: #{details[:description]}"
  puts "  Activities:"
  details[:activities].each { |activity| puts "    - #{activity}" }
end
```

### Project Management Methodologies
```ruby
class ProjectMethodologies
  def self.waterfall
    {
      description: "Sequential, linear project management approach",
      characteristics: [
        "Sequential phases",
        "Detailed upfront planning",
        "Minimal flexibility",
        "Documentation heavy",
        "Formal change control"
      ],
      phases: [
        "Requirements",
        "Design",
        "Implementation",
        "Testing",
        "Deployment",
        "Maintenance"
      ],
      advantages: [
        "Clear structure and documentation",
        "Easy to understand",
        "Milestones clearly defined",
        "Good for regulated industries"
      ],
      disadvantages: [
        "Inflexible to changes",
        "Late testing",
        "Risk of scope creep",
        "Poor for complex projects"
      ],
      best_for: [
        "Construction projects",
        "Manufacturing",
        "Government projects",
        "Simple, well-defined projects"
      ]
    }
  end

  def self.agile
    {
      description: "Iterative, flexible project management approach",
      characteristics: [
        "Iterative development",
        "Flexible planning",
        "Customer collaboration",
        "Rapid delivery",
        "Continuous improvement"
      ],
      principles: [
        "Individuals and interactions over processes and tools",
        "Working software over comprehensive documentation",
        "Customer collaboration over contract negotiation",
        "Responding to change over following a plan"
      ],
      advantages: [
        "Flexible to changes",
        "Early value delivery",
        "Customer involvement",
        "Continuous feedback",
        "Risk reduction"
      ],
      disadvantages: [
        "Less predictable timeline",
        "Requires customer involvement",
        "Can be chaotic without discipline",
        "Documentation may be lacking"
      ],
      best_for: [
        "Software development",
        "Complex projects",
        "Uncertain requirements",
        "Innovative projects"
      ]
    }
  end

  def self.scrum
    {
      description: "Agile framework for complex product development",
      roles: {
        product_owner: "Manages product backlog and requirements",
        scrum_master: "Facilitates Scrum process and removes impediments",
        development_team: "Cross-functional team that delivers product increments"
      },
      events: [
        "Sprint Planning",
        "Daily Scrum",
        "Sprint Review",
        "Sprint Retrospective",
        "Product Backlog Refinement"
      ],
      artifacts: [
        "Product Backlog",
        "Sprint Backlog",
        "Increment"
      ],
      values: [
        "Commitment",
        "Courage",
        "Focus",
        "Openness",
        "Respect"
      ]
    }
  end

  def self.kanban
    {
      description: "Visual workflow management method",
      principles: [
        "Visualize the workflow",
        "Limit work in progress",
        "Manage flow",
        "Make policies explicit",
        "Implement feedback loops",
        "Improve collaboratively"
      ],
      practices: [
        "Visualize work on Kanban board",
        "Limit WIP to improve flow",
        "Manage flow to reduce waste",
        "Make policies explicit",
        "Implement feedback loops",
        "Improve collaboratively"
      ],
      metrics: [
        "Lead time",
        "Cycle time",
        "Throughput",
        "Work in Progress",
        "Cumulative flow"
      ],
      advantages: [
        "Flexible and adaptive",
        "Visual workflow",
        "Focus on flow",
        "Continuous improvement",
        "No prescribed roles"
      ]
    }
  end
end

# Usage example
puts "\nProject Methodologies:"
puts "\nWaterfall:"
waterfall = ProjectMethodologies.waterfall
waterfall[:advantages].each { |advantage| puts "  ✓ #{advantage}" }

puts "\nAgile:"
agile = ProjectMethodologies.agile
agile[:principles].each { |principle| puts "  ✓ #{principle}" }
```

## Ruby Project Management Tools

### Task Management System
```ruby
class TaskManager
  def initialize
    @tasks = {}
    @projects = {}
    @users = {}
    @task_id_counter = 1
  end

  def create_project(name, description = nil)
    project_id = generate_id
    @projects[project_id] = {
      id: project_id,
      name: name,
      description: description,
      tasks: [],
      created_at: Time.now,
      updated_at: Time.now
    }
    project_id
  end

  def create_task(title, description, project_id = nil, assignee_id = nil)
    task_id = generate_id
    task = {
      id: task_id,
      title: title,
      description: description,
      project_id: project_id,
      assignee_id: assignee_id,
      status: :todo,
      priority: :medium,
      created_at: Time.now,
      updated_at: Time.now,
      due_date: nil,
      tags: []
    }
    
    @tasks[task_id] = task
    
    if project_id && @projects[project_id]
      @projects[project_id][:tasks] << task_id
      @projects[project_id][:updated_at] = Time.now
    end
    
    task_id
  end

  def update_task_status(task_id, status)
    return false unless @tasks[task_id]
    
    @tasks[task_id][:status] = status
    @tasks[task_id][:updated_at] = Time.now
    
    # Update project timestamp
    project_id = @tasks[task_id][:project_id]
    if project_id && @projects[project_id]
      @projects[project_id][:updated_at] = Time.now
    end
    
    true
  end

  def assign_task(task_id, user_id)
    return false unless @tasks[task_id] && @users[user_id]
    
    @tasks[task_id][:assignee_id] = user_id
    @tasks[task_id][:updated_at] = Time.now
    true
  end

  def get_project_tasks(project_id)
    return [] unless @projects[project_id]
    
    @projects[project_id][:tasks].map { |task_id| @tasks[task_id] }.compact
  end

  def get_user_tasks(user_id)
    @tasks.values.select { |task| task[:assignee_id] == user_id }
  end

  def get_tasks_by_status(status)
    @tasks.values.select { |task| task[:status] == status }
  end

  def get_overdue_tasks
    now = Time.now
    @tasks.values.select { |task| task[:due_date] && task[:due_date] < now && task[:status] != :done }
  end

  def add_user(name, email)
    user_id = generate_id
    @users[user_id] = {
      id: user_id,
      name: name,
      email: email,
      created_at: Time.now
    }
    user_id
  end

  def get_project_statistics(project_id)
    return nil unless @projects[project_id]
    
    tasks = get_project_tasks(project_id)
    
    {
      total_tasks: tasks.length,
      completed_tasks: tasks.count { |t| t[:status] == :done },
      in_progress_tasks: tasks.count { |t| t[:status] == :in_progress },
      todo_tasks: tasks.count { |t| t[:status] == :todo },
      overdue_tasks: tasks.count { |t| t[:due_date] && t[:due_date] < Time.now && t[:status] != :done },
      completion_rate: tasks.empty? ? 0 : (tasks.count { |t| t[:status] == :done }.to_f / tasks.length * 100).round(2)
    }
  end

  def generate_report
    {
      total_projects: @projects.length,
      total_tasks: @tasks.length,
      total_users: @users.length,
      task_status_breakdown: {
        todo: @tasks.count { |t| t[:status] == :todo },
        in_progress: @tasks.count { |t| t[:status] == :in_progress },
        done: @tasks.count { |t| t[:status] == :done }
      },
      overdue_tasks: get_overdue_tasks.length,
      projects_by_status: @projects.values.map do |project|
        stats = get_project_statistics(project[:id])
        {
          name: project[:name],
          completion_rate: stats[:completion_rate]
        }
      end
    }
  end

  private

  def generate_id
    @task_id_counter += 1
    "task_#{@task_id_counter}"
  end
end

# Usage example
manager = TaskManager.new

# Create users
user1 = manager.add_user("Alice Johnson", "alice@example.com")
user2 = manager.add_user("Bob Smith", "bob@example.com")

# Create project
project_id = manager.create_project("Web App Redesign", "Complete redesign of company website")

# Create tasks
task1 = manager.create_task("Design mockups", "Create initial design mockups", project_id, user1)
task2 = manager.create_task("Implement homepage", "Build the homepage with new design", project_id, user2)
task3 = manager.create_task("Test responsive design", "Ensure design works on all devices", project_id, user1)

# Update task status
manager.update_task_status(task1, :done)
manager.update_task_status(task2, :in_progress)

# Get project statistics
stats = manager.get_project_statistics(project_id)
puts "\nProject Statistics:"
stats.each { |metric, value| puts "  #{metric}: #{value}" }

# Generate report
report = manager.generate_report
puts "\nOverall Report:"
report.each { |metric, value| puts "  #{metric}: #{value}" }
```

### Sprint Management System
```ruby
class SprintManager
  def initialize
    @sprints = []
    @backlog = []
    @team_members = []
    @sprint_id_counter = 1
  end

  def create_sprint(name, duration_days, start_date = Date.today)
    sprint_id = generate_id
    sprint = {
      id: sprint_id,
      name: name,
      duration_days: duration_days,
      start_date: start_date,
      end_date: start_date + duration_days - 1,
      stories: [],
      status: :planned,
      velocity: 0,
      created_at: Time.now
    }
    
    @sprints << sprint
    sprint_id
  end

  def add_story(title, description, points = 1)
    story_id = generate_story_id
    story = {
      id: story_id,
      title: title,
      description: description,
      points: points,
      status: :backlog,
      assignee_id: nil,
      created_at: Time.now
    }
    
    @backlog << story
    story_id
  end

  def add_story_to_sprint(sprint_id, story_id)
    sprint = find_sprint(sprint_id)
    story = find_story(story_id)
    
    return false unless sprint && story
    
    # Remove from backlog if present
    @backlog.delete(story)
    
    # Add to sprint
    sprint[:stories] << story
    story[:status] = :sprint_backlog
    sprint[:updated_at] = Time.now
    
    true
  end

  def start_sprint(sprint_id)
    sprint = find_sprint(sprint_id)
    return false unless sprint
    
    sprint[:status] = :active
    sprint[:stories].each { |story| story[:status] = :in_progress }
    true
  end

  def complete_sprint(sprint_id)
    sprint = find_sprint(sprint_id)
    return false unless sprint
    
    sprint[:status] = :completed
    sprint[:velocity] = calculate_sprint_velocity(sprint)
    
    # Move incomplete stories back to backlog
    incomplete_stories = sprint[:stories].select { |story| story[:status] != :done }
    incomplete_stories.each do |story|
      story[:status] = :backlog
      @backlog << story
    end
    
    true
  end

  def update_story_status(story_id, status)
    story = find_story(story_id)
    return false unless story
    
    story[:status] = status
    story[:updated_at] = Time.now
    true
  end

  def assign_story(story_id, assignee_id)
    story = find_story(story_id)
    return false unless story
    
    story[:assignee_id] = assignee_id
    story[:updated_at] = Time.now
    true
  end

  def get_sprint_burndown(sprint_id)
    sprint = find_sprint(sprint_id)
    return nil unless sprint
    
    days = (0...sprint[:duration_days]).map do |day|
      date = sprint[:start_date] + day
      {
        day: day + 1,
        date: date,
        remaining_points: calculate_remaining_points(sprint, date)
      }
    end
    
    {
      sprint_id: sprint_id,
      sprint_name: sprint[:name],
      duration: sprint[:duration_days],
      burndown_data: days
    }
  end

  def get_team_velocity
    completed_sprints = @sprints.select { |sprint| sprint[:status] == :completed }
    
    if completed_sprints.empty?
      0
    else
      completed_sprints.map { |sprint| sprint[:velocity] }.sum / completed_sprints.length
    end
  end

  def get_backlog_priority
    @backlog.sort_by { |story| story[:points] }
  end

  def get_sprint_report(sprint_id)
    sprint = find_sprint(sprint_id)
    return nil unless sprint
    
    {
      sprint: {
        id: sprint[:id],
        name: sprint[:name],
        status: sprint[:status],
        duration: sprint[:duration_days],
        start_date: sprint[:start_date],
        end_date: sprint[:end_date],
        velocity: sprint[:velocity]
      },
      stories: sprint[:stories].map do |story|
        {
          id: story[:id],
          title: story[:title],
          points: story[:points],
          status: story[:status],
          assignee_id: story[:assignee_id]
        }
      end,
      metrics: {
        total_points: sprint[:stories].sum { |s| s[:points] },
        completed_points: sprint[:stories].count { |s| s[:status] == :done },
        in_progress_points: sprint[:stories].count { |s| s[:status] == :in_progress },
        completion_rate: calculate_sprint_completion_rate(sprint)
      }
    }
  end

  private

  def find_sprint(sprint_id)
    @sprints.find { |sprint| sprint[:id] == sprint_id }
  end

  def find_story(story_id)
    all_stories = @backlog + @sprints.flat_map { |sprint| sprint[:stories] }
    all_stories.find { |story| story[:id] == story_id }
  end

  def calculate_sprint_velocity(sprint)
    sprint[:stories].select { |story| story[:status] == :done }.sum { |story| story[:points] }
  end

  def calculate_sprint_completion_rate(sprint)
    total_points = sprint[:stories].sum { |story| story[:points] }
    completed_points = sprint[:stories].select { |story| story[:status] == :done }.sum { |story| story[:points] }
    
    total_points > 0 ? (completed_points.to_f / total_points * 100).round(2) : 0
  end

  def calculate_remaining_points(sprint, date)
    # Simplified calculation - in practice, this would track daily progress
    days_passed = (date - sprint[:start_date]).to_i
    total_days = sprint[:duration_days]
    
    if days_passed >= total_days
      0
    else
      # Simulate linear progress
      total_points = sprint[:stories].sum { |story| story[:points] }
      completed_ratio = days_passed.to_f / total_days
      remaining_points = total_points * (1 - completed_ratio)
      remaining_points.round
    end
  end

  def generate_id
    @sprint_id_counter += 1
    "sprint_#{@sprint_id_counter}"
  end

  def generate_story_id
    Time.now.to_f.to_s.gsub('.', '')
  end
end

# Usage example
sprint_manager = SprintManager.new

# Add stories to backlog
story1 = sprint_manager.add_story("User authentication", "Implement user login and registration", 5)
story2 = sprint_manager.add_story("Dashboard", "Create user dashboard with analytics", 8)
story3 = sprint_manager.add_story("Settings page", "User settings and preferences", 3)
story4 = sprint_manager.add_story("Profile page", "User profile management", 5)

# Create sprint
sprint_id = sprint_manager.create_sprint("Sprint 1", 14, Date.today)

# Add stories to sprint
sprint_manager.add_story_to_sprint(sprint_id, story1)
sprint_manager.add_story_to_sprint(sprint_id, story2)
sprint_manager.add_story_to_sprint(sprint_id, story3)

# Start sprint
sprint_manager.start_sprint(sprint_id)

# Update story statuses
sprint_manager.update_story_status(story1, :done)
sprint_manager.update_story_status(story3, :in_progress)

# Get sprint report
report = sprint_manager.get_sprint_report(sprint_id)
puts "\nSprint Report:"
puts "Sprint: #{report[:sprint][:name]}"
puts "Status: #{report[:sprint][:status]}"
puts "Total Points: #{report[:metrics][:total_points]}"
puts "Completion Rate: #{report[:metrics][:completion_rate]}%"

# Get burndown chart data
burndown = sprint_manager.get_sprint_burndown(sprint_id)
puts "\nBurndown Chart:"
burndown[:burndown_data].first(5).each do |day|
  puts "Day #{day[:day]}: #{day[:remaining_points]} points remaining"
end
```

## Resource Management

### Resource Allocation System
```ruby
class ResourceManager
  def initialize
    @resources = {}
    @allocations = {}
    @projects = {}
    @resource_id_counter = 1
  end

  def add_resource(name, type, skills = [], availability = 1.0)
    resource_id = generate_id
    @resources[resource_id] = {
      id: resource_id,
      name: name,
      type: type,  # :developer, :designer, :manager, :qa
      skills: skills,
      availability: availability,
      current_allocation: 0,
      hourly_rate: 100,  # Default rate
      created_at: Time.now
    }
    resource_id
  end

  def create_project(name, start_date, end_date)
    project_id = generate_id
    @projects[project_id] = {
      id: project_id,
      name: name,
      start_date: start_date,
      end_date: end_date,
      requirements: [],
      budget: 0,
      created_at: Time.now
    }
    project_id
  end

  def add_project_requirement(project_id, skill, hours, priority = :medium)
    project = @projects[project_id]
    return false unless project
    
    requirement = {
      id: generate_requirement_id,
      skill: skill,
      hours: hours,
      priority: priority,  # :low, :medium, :high, :critical
      allocated: false,
      resource_id: nil
    }
    
    project[:requirements] << requirement
    requirement[:id]
  end

  def allocate_resource(resource_id, project_id, requirement_id, hours)
    resource = @resources[resource_id]
    project = @projects[project_id]
    requirement = find_requirement(project_id, requirement_id)
    
    return false unless resource && project && requirement
    
    # Check resource availability
    if resource[:current_allocation] + hours > resource[:availability]
      return false
    end
    
    # Check skill match
    unless resource[:skills].include?(requirement[:skill])
      return false
    end
    
    # Create allocation
    allocation_id = generate_allocation_id
    allocation = {
      id: allocation_id,
      resource_id: resource_id,
      project_id: project_id,
      requirement_id: requirement_id,
      hours: hours,
      start_date: project[:start_date],
      end_date: project[:end_date],
      status: :active,
      created_at: Time.now
    }
    
    @allocations[allocation_id] = allocation
    
    # Update resource allocation
    resource[:current_allocation] += hours
    resource[:updated_at] = Time.now
    
    # Update requirement
    requirement[:allocated] = true
    requirement[:resource_id] = resource_id
    
    true
  end

  def release_allocation(allocation_id)
    allocation = @allocations[allocation_id]
    return false unless allocation
    
    resource = @resources[allocation[:resource_id]]
    requirement = find_requirement(allocation[:project_id], allocation[:requirement_id])
    
    # Update resource allocation
    resource[:current_allocation] -= allocation[:hours]
    resource[:updated_at] = Time.now
    
    # Update requirement
    requirement[:allocated] = false
    requirement[:resource_id] = nil
    
    # Update allocation status
    allocation[:status] = :released
    allocation[:released_at] = Time.now
    
    true
  end

  def get_resource_utilization(resource_id)
    resource = @resources[resource_id]
    return nil unless resource
    
    utilization = resource[:current_allocation] / resource[:availability] * 100
    {
      resource_id: resource_id,
      name: resource[:name],
      utilization: utilization.round(2),
      status: utilization > 90 ? :overutilized : (utilization > 70 ? :optimal : :underutilized)
    }
  end

  def get_project_resource_plan(project_id)
    project = @projects[project_id]
    return nil unless project
    
    allocations = @allocations.values.select { |alloc| alloc[:project_id] == project_id }
    
    {
      project_id: project_id,
      project_name: project[:name],
      total_cost: calculate_project_cost(project_id),
      allocated_resources: allocations.map do |alloc|
        resource = @resources[alloc[:resource_id]]
        {
          resource_name: resource[:name],
          skill: find_requirement(project_id, alloc[:requirement_id])[:skill],
          hours: alloc[:hours],
          cost: alloc[:hours] * resource[:hourly_rate]
        }
      end,
      unallocated_requirements: project[:requirements].select { |req| !req[:allocated] }
    }
  end

  def optimize_allocations
    # Simple optimization algorithm
    unallocated_requirements = []
    
    @projects.each do |project_id, project|
      project[:requirements].each do |requirement|
        unless requirement[:allocated]
          unallocated_requirements << {
            project_id: project_id,
            requirement: requirement
          }
        end
      end
    end
    
    # Sort by priority
    unallocated_requirements.sort_by! { |req| priority_weight(req[:requirement][:priority]) }
    
    allocations_made = 0
    
    unallocated_requirements.each do |req|
      best_resource = find_best_resource(req[:requirement][:skill], req[:requirement][:hours])
      
      if best_resource
        if allocate_resource(best_resource[:id], req[:project_id], req[:requirement][:id], req[:requirement][:hours])
          allocations_made += 1
        end
      end
    end
    
    allocations_made
  end

  def generate_resource_report
    {
      total_resources: @resources.length,
      total_projects: @projects.length,
      total_allocations: @allocations.count { |alloc| alloc[:status] == :active },
      resource_utilization: @resources.keys.map { |id| get_resource_utilization(id) },
      project_costs: @projects.keys.map { |id| calculate_project_cost(id) },
      optimization_opportunities: find_optimization_opportunities
    }
  end

  private

  def find_requirement(project_id, requirement_id)
    project = @projects[project_id]
    return nil unless project
    
    project[:requirements].find { |req| req[:id] == requirement_id }
  end

  def find_best_resource(skill, hours)
    available_resources = @resources.values.select do |resource|
      resource[:skills].include?(skill) &&
      resource[:current_allocation] + hours <= resource[:availability]
    end
    
    # Prefer resources with lower current allocation
    available_resources.min_by { |resource| resource[:current_allocation] }
  end

  def calculate_project_cost(project_id)
    allocations = @allocations.values.select { |alloc| alloc[:project_id] == project_id }
    
    allocations.sum do |alloc|
      resource = @resources[alloc[:resource_id]]
      alloc[:hours] * resource[:hourly_rate]
    end
  end

  def find_optimization_opportunities
    opportunities = []
    
    # Find underutilized resources
    @resources.each do |resource_id, resource|
      utilization = resource[:current_allocation] / resource[:availability]
      if utilization < 0.7
        opportunities << {
          type: :underutilized_resource,
          resource_id: resource_id,
          resource_name: resource[:name],
          current_utilization: utilization.round(2),
          available_hours: resource[:availability] - resource[:current_allocation]
        }
      end
    end
    
    # Find unallocated requirements
    @projects.each do |project_id, project|
      project[:requirements].each do |requirement|
        unless requirement[:allocated]
          opportunities << {
            type: :unallocated_requirement,
            project_id: project_id,
            project_name: project[:name],
            skill: requirement[:skill],
            hours: requirement[:hours],
            priority: requirement[:priority]
          }
        end
      end
    end
    
    opportunities
  end

  def priority_weight(priority)
    case priority
    when :critical then 4
    when :high then 3
    when :medium then 2
    when :low then 1
    else 0
    end
  end

  def generate_id
    @resource_id_counter += 1
    "resource_#{@resource_id_counter}"
  end

  def generate_requirement_id
    Time.now.to_f.to_s.gsub('.', '')
  end

  def generate_allocation_id
    Time.now.to_f.to_s.gsub('.', '')
  end
end

# Usage example
resource_manager = ResourceManager.new

# Add resources
dev1 = resource_manager.add_resource("Alice Johnson", :developer, ["Ruby", "Rails", "JavaScript"], 1.0)
dev2 = resource_manager.add_resource("Bob Smith", :developer, ["Python", "Django", "JavaScript"], 0.8)
designer1 = resource_manager.add_resource("Carol White", :designer, ["UI/UX", "Figma", "Photoshop"], 0.5)

# Create project
project_id = resource_manager.create_project("E-commerce Website", Date.today, Date.today + 60)

# Add requirements
req1 = resource_manager.add_project_requirement(project_id, "Ruby", 160, :high)
req2 = resource_manager.add_project_requirement(project_id, "UI/UX", 40, :medium)
req3 = resource_manager.add_project_requirement(project_id, "JavaScript", 80, :medium)

# Allocate resources
resource_manager.allocate_resource(dev1, project_id, req1, 160)
resource_manager.allocate_resource(designer1, project_id, req2, 40)

# Get resource utilization
utilization = resource_manager.get_resource_utilization(dev1)
puts "\nResource Utilization:"
puts "#{utilization[:name]}: #{utilization[:utilization]}% (#{utilization[:status]})"

# Get project resource plan
plan = resource_manager.get_project_resource_plan(project_id)
puts "\nProject Resource Plan:"
puts "Total Cost: $#{plan[:total_cost]}"
puts "Allocated Resources: #{plan[:allocated_resources].length}"
puts "Unallocated Requirements: #{plan[:unallocated_requirements].length}"

# Optimize allocations
optimizations = resource_manager.optimize_allocations
puts "\nOptimizations Made: #{optimizations}"

# Generate report
report = resource_manager.generate_resource_report
puts "\nResource Report:"
puts "Total Resources: #{report[:total_resources]}"
puts "Total Projects: #{report[:total_projects]}"
puts "Active Allocations: #{report[:total_allocations]}"
```

## Risk Management

### Risk Assessment System
```ruby
class RiskManager
  def initialize
    @risks = []
    @mitigation_plans = {}
    @risk_id_counter = 1
  end

  def identify_risk(title, description, category, probability, impact, project_id = nil)
    risk_id = generate_id
    risk = {
      id: risk_id,
      title: title,
      description: description,
      category: category,  # :technical, :schedule, :budget, :resource, :external
      probability: probability,  # 1-5 scale
      impact: impact,         # 1-5 scale
      risk_score: probability * impact,
      status: :identified,
      project_id: project_id,
      created_at: Time.now,
      updated_at: Time.now
    }
    
    @risks << risk
    risk_id
  end

  def create_mitigation_plan(risk_id, strategy, actions, owner_id, due_date)
    risk = find_risk(risk_id)
    return false unless risk
    
    plan = {
      risk_id: risk_id,
      strategy: strategy,
      actions: actions,
      owner_id: owner_id,
      due_date: due_date,
      status: :planned,
      created_at: Time.now,
      updated_at: Time.now
    }
    
    @mitigation_plans[risk_id] = plan
    risk[:status] = :mitigation_planned
    risk[:updated_at] = Time.now
    
    true
  end

  def update_risk_status(risk_id, status)
    risk = find_risk(risk_id)
    return false unless risk
    
    risk[:status] = status
    risk[:updated_at] = Time.now
    true
  end

  def update_risk_probability(risk_id, probability)
    risk = find_risk(risk_id)
    return false unless risk
    
    old_score = risk[:risk_score]
    risk[:probability] = probability
    risk[:risk_score] = probability * risk[:impact]
    risk[:updated_at] = Time.now
    
    # Update status based on new risk score
    if risk[:risk_score] > old_score
      risk[:status] = :escalated
    elsif risk[:risk_score] < old_score
      risk[:status] = :reduced
    end
    
    true
  end

  def update_risk_impact(risk_id, impact)
    risk = find_risk(risk_id)
    return false unless risk
    
    old_score = risk[:risk_score]
    risk[:impact] = impact
    risk[:risk_score] = risk[:probability] * impact
    risk[:updated_at] = Time.now
    
    # Update status based on new risk score
    if risk[:risk_score] > old_score
      risk[:status] = :escalated
    elsif risk[:risk_score] < old_score
      risk[:status] = :reduced
    end
    
    true
  end

  def get_high_risks(threshold = 15)
    @risks.select { |risk| risk[:risk_score] >= threshold }
  end

  def get_risks_by_category(category)
    @risks.select { |risk| risk[:category] == category }
  end

  def get_project_risks(project_id)
    @risks.select { |risk| risk[:project_id] == project_id }
  end

  def get_risk_heatmap
    {
      high_probability_high_impact: @risks.select { |r| r[:probability] >= 4 && r[:impact] >= 4 },
      high_probability_low_impact: @risks.select { |r| r[:probability] >= 4 && r[:impact] <= 2 },
      low_probability_high_impact: @risks.select { |r| r[:probability] <= 2 && r[:impact] >= 4 },
      low_probability_low_impact: @risks.select { |r| r[:probability] <= 2 && r[:impact] <= 2 }
    }
  end

  def get_risk_trend
    # Simplified trend analysis
    {
      total_risks: @risks.length,
      high_risks: get_high_risks.length,
      mitigated_risks: @risks.count { |r| r[:status] == :mitigated },
      active_risks: @risks.count { |r| [:identified, :mitigation_planned, :escalated].include?(r[:status]) },
      by_category: {
        technical: @risks.count { |r| r[:category] == :technical },
        schedule: @risks.count { |r| r[:category] == :schedule },
        budget: @risks.count { |r| r[:category] == :budget },
        resource: @risks.count { |r| r[:category] == :resource },
        external: @risks.count { |r| r[:category] == :external }
      }
    }
  end

  def generate_risk_report
    {
      summary: get_risk_trend,
      high_risks: get_high_risks.map { |risk| 
        {
          id: risk[:id],
          title: risk[:title],
          category: risk[:category],
          risk_score: risk[:risk_score],
          status: risk[:status]
        }
      },
      heatmap: get_risk_heatmap,
      mitigation_plans: @mitigation_plans.map do |risk_id, plan|
        risk = find_risk(risk_id)
        {
          risk_title: risk[:title],
          strategy: plan[:strategy],
          actions: plan[:actions],
          owner_id: plan[:owner_id],
          due_date: plan[:due_date],
          status: plan[:status]
        }
      end
    }
  end

  def review_mitigation_plans
    overdue_plans = []
    
    @mitigation_plans.each do |risk_id, plan|
      if plan[:due_date] < Date.today && plan[:status] != :completed
        overdue_plans << {
          risk_id: risk_id,
          risk_title: find_risk(risk_id)[:title],
          due_date: plan[:due_date],
          owner_id: plan[:owner_id]
        }
      end
    end
    
    overdue_plans
  end

  private

  def find_risk(risk_id)
    @risks.find { |risk| risk[:id] == risk_id }
  end

  def generate_id
    @risk_id_counter += 1
    "risk_#{@risk_id_counter}"
  end
end

# Usage example
risk_manager = RiskManager.new

# Identify risks
risk1 = risk_manager.identify_risk(
  "Technical Debt",
  "Legacy codebase may cause maintenance issues",
  :technical,
  4,  # probability
  3   # impact
)

risk2 = risk_manager.identify_risk(
  "Schedule Delay",
  "Key team member may leave mid-project",
  :resource,
  3,  # probability
  5   # impact
)

risk3 = risk_manager.identify_risk(
  "Budget Overrun",
  "Unexpected infrastructure costs",
  :budget,
  2,  # probability
  4   # impact
)

# Create mitigation plans
risk_manager.create_mitigation_plan(
  risk1,
  "Refactor legacy code incrementally",
  ["Schedule regular refactoring sessions", "Add automated tests", "Document legacy components"],
  "dev_lead",
  Date.today + 30
)

risk_manager.create_mitigation_plan(
  risk2,
  "Cross-train team members",
  ["Document knowledge", "Pair programming", "Hire backup resources"],
  "project_manager",
  Date.today + 15
)

# Get high risks
high_risks = risk_manager.get_high_risks(12)
puts "\nHigh Risks (Score >= 12):"
high_risks.each { |risk| puts "  #{risk[:title]}: #{risk[:risk_score]} (#{risk[:category]})" }

# Get risk heatmap
heatmap = risk_manager.get_risk_heatmap
puts "\nRisk Heatmap:"
puts "High Probability, High Impact: #{heatmap[:high_probability_high_impact].length}"
puts "High Probability, Low Impact: #{heatmap[:high_probability_low_impact].length}"
puts "Low Probability, High Impact: #{heatmap[:low_probability_high_impact].length}"
puts "Low Probability, Low Impact: #{heatmap[:low_probability_low_impact].length}"

# Generate risk report
report = risk_manager.generate_risk_report
puts "\nRisk Report Summary:"
report[:summary].each { |metric, value| puts "  #{metric}: #{value}" }
```

## Best Practices

### Project Management Best Practices
```ruby
class ProjectManagementBestPractices
  def self.communication_practices
    {
      stakeholder_communication: [
        "Regular status updates",
        "Clear and concise reporting",
        "Tailor communication to audience",
        "Use visual aids for complex information",
        "Document decisions and changes"
      ],
      team_communication: [
        "Daily stand-ups",
        "Weekly team meetings",
        "Clear task assignments",
        "Open feedback culture",
        "Celebrate successes"
      ],
      documentation: [
        "Maintain project documentation",
        "Document decisions and rationale",
        "Create knowledge base",
        "Version control documentation",
        "Regular documentation reviews"
      ]
    }
  end

  def self.planning_practices
    {
      requirement_management: [
        "Clear requirement definitions",
        "Stakeholder involvement",
        "Requirements prioritization",
        "Change control process",
        "Regular requirement reviews"
      ],
      scope_management: [
        "Clear project boundaries",
        "Scope change procedures",
        "Regular scope reviews",
        "Stakeholder alignment",
        "Impact analysis for changes"
      ],
      risk_management: [
        "Early risk identification",
        "Regular risk assessments",
        "Mitigation planning",
        "Risk monitoring",
        "Contingency planning"
      ]
    }
  end

  def self.execution_practices
    {
      quality_management: [
        "Code reviews",
        "Automated testing",
        "Continuous integration",
        "Quality metrics",
        "Regular quality audits"
      ],
      change_management: [
        "Change control process",
        "Impact analysis",
        "Stakeholder communication",
        "Rollback plans",
        "Change documentation"
      ],
      monitoring_control: [
        "Progress tracking",
        "Performance metrics",
        "Budget monitoring",
        "Schedule tracking",
        "Quality metrics"
      ]
    }
  end

  def self.team_management_practices
    {
      team_building: [
        "Clear roles and responsibilities",
        "Regular team meetings",
        "Team building activities",
        "Conflict resolution",
        "Recognition and rewards"
      ],
      skill_development: [
        "Training programs",
        "Mentorship opportunities",
        "Skill assessments",
        "Career development plans",
        "Knowledge sharing"
      ],
      performance_management: [
        "Clear performance expectations",
        "Regular performance reviews",
        "Goal setting",
        "Feedback mechanisms",
        "Performance improvement plans"
      ]
    }
  end
end

# Usage example
puts "\nProject Management Best Practices:"
practices = ProjectManagementBestPractices.communication_practices
practices.each { |category, items| puts "#{category}: #{items.join(', ')}" }
```

## Conclusion

Project management is essential for successful Ruby development projects. By understanding project management methodologies, using appropriate tools, and following best practices, you can deliver projects on time, within budget, and to the required quality standards.

## Further Reading

- [Project Management Institute (PMI)](https://www.pmi.org/)
- [Agile Manifesto](https://agilemanifesto.org/)
- [Scrum Guide](https://scrumguides.org/)
- [Kanban Guide](https://kanbanize.com/kanban-guide/)
- [Ruby Project Management Tools](https://www.ruby-toolbox.com/categories/project-management)
