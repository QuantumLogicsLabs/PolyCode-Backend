# Ruby Web Development

## Ruby on Rails Framework

### Rails Application Structure
```ruby
# Rails directory structure
my_app/
├── app/
│   ├── assets/
│   ├── controllers/
│   ├── helpers/
│   ├── jobs/
│   ├── mailers/
│   ├── models/
│   └── views/
├── config/
├── db/
├── lib/
├── log/
├── public/
├── test/
├── tmp/
└── vendor/

# Gemfile for Rails application
source 'https://rubygems.org'

# Bundle edge Rails instead: gem 'rails', github: 'rails/rails'
gem 'rails', '~> 6.0.0'

# Use postgresql as the database for Active Record
gem 'pg', '~> 1.1'

# Use Puma as the app server
gem 'puma', '~> 3.0'

# Use SCSS for stylesheets
gem 'sass-rails', '~> 5'

# Use Uglifier as compressor for JavaScript assets
gem 'uglifier', '>= 1.3.0'

# Turbolinks makes navigating your web application faster
gem 'turbolinks', '~> 5'

# Build JSON APIs with ease
gem 'jbuilder', '~> 2.5'

group :development, :test do
  gem 'byebug', platform: :mri
  gem 'rspec-rails', '~> 3.0'
  gem 'factory_bot_rails', '~> 4.0'
end

group :development do
  gem 'web-console', '>= 3.3.0'
  gem 'listen', '~> 3.1'
end

group :test do
  gem 'capybara', '~> 2.13'
  gem 'selenium-webdriver'
  gem 'webdrivers'
end
```

### Models and ActiveRecord
```ruby
# app/models/user.rb
class User < ApplicationRecord
  # Validations
  validates :name, presence: true, length: { minimum: 2, maximum: 50 }
  validates :email, presence: true, uniqueness: true, format: { with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i }
  validates :age, numericality: { greater_than_or_equal_to: 0, less_than: 150 }
  
  # Callbacks
  before_save :normalize_email
  after_create :send_welcome_email
  after_destroy :cleanup_user_data
  
  # Associations
  has_many :posts, dependent: :destroy
  has_many :comments, through: :posts
  has_one :profile, dependent: :destroy
  belongs_to :company, optional: true
  
  # Scopes
  scope :active, -> { where(active: true) }
  scope :by_age, ->(min_age, max_age) { where(age: min_age..max_age) }
  scope :recent, -> { order(created_at: :desc) }
  
  # Class methods
  def self.search(query)
    where("name ILIKE ? OR email ILIKE ?", "%#{query}%", "%#{query}%")
  end
  
  # Instance methods
  def full_name
    profile ? "#{name} #{profile.last_name}" : name
  end
  
  def admin?
    role == 'admin'
  end
  
  # Delegated methods
  delegate :bio, :avatar_url, to: :profile, prefix: true, allow_nil: true
  
  private
  
  def normalize_email
    self.email = email.downcase.strip
  end
  
  def send_welcome_email
    UserMailer.welcome_email(self).deliver_later
  end
  
  def cleanup_user_data
    # Cleanup related data
  end
end

# app/models/post.rb
class Post < ApplicationRecord
  belongs_to :user
  has_many :comments, dependent: :destroy
  has_many :likes, dependent: :destroy
  has_many :taggings, dependent: :destroy
  has_many :tags, through: :taggings
  
  validates :title, presence: true, length: { maximum: 200 }
  validates :content, presence: true, length: { minimum: 10 }
  validates :user_id, presence: true
  
  scope :published, -> { where(published: true) }
  scope :recent, -> { order(created_at: :desc) }
  scope :by_tag, ->(tag_name) { joins(:tags).where(tags: { name: tag_name }) }
  
  def self.popular
    joins(:likes).group('posts.id').having('COUNT(likes.id) > 5')
  end
  
  def excerpt(length: 100)
    content.truncate(length, separator: ' ')
  end
  
  def tag_list
    tags.pluck(:name).join(', ')
  end
  
  def tag_list=(tag_string)
    tag_names = tag_string.split(',').map(&:strip).reject(&:empty?)
    
    self.tags = tag_names.map do |name|
      Tag.find_or_create_by(name: name)
    end
  end
end

# app/models/comment.rb
class Comment < ApplicationRecord
  belongs_to :post
  belongs_to :user
  
  validates :content, presence: true, length: { minimum: 1, maximum: 1000 }
  validates :user_id, presence: true
  validates :post_id, presence: true
  
  default_scope -> { order(created_at: :asc) }
  
  def editable_by?(user)
    self.user == user || post.user == user
  end
end
```

### Controllers
```ruby
# app/controllers/users_controller.rb
class UsersController < ApplicationController
  before_action :authenticate_user!
  before_action :set_user, only: [:show, :edit, :update, :destroy]
  before_action :authorize_user!, only: [:edit, :update, :destroy]
  
  # GET /users
  def index
    @users = User.includes(:profile).active.recent.page(params[:page])
    
    if params[:search].present?
      @users = @users.search(params[:search])
    end
    
    respond_to do |format|
      format.html
      format.json { render json: @users }
    end
  end
  
  # GET /users/1
  def show
    @posts = @user.posts.published.recent.limit(10)
    
    respond_to do |format|
      format.html
      format.json { render json: @user }
    end
  end
  
  # GET /users/new
  def new
    @user = User.new
    @user.build_profile
  end
  
  # GET /users/1/edit
  def edit
    @user.build_profile unless @user.profile
  end
  
  # POST /users
  def create
    @user = User.new(user_params)
    
    respond_to do |format|
      if @user.save
        format.html { redirect_to @user, notice: 'User was successfully created.' }
        format.json { render :show, status: :created, location: @user }
      else
        format.html { render :new }
        format.json { render json: @user.errors, status: :unprocessable_entity }
      end
    end
  end
  
  # PATCH/PUT /users/1
  def update
    respond_to do |format|
      if @user.update(user_params)
        format.html { redirect_to @user, notice: 'User was successfully updated.' }
        format.json { render :show, status: :ok, location: @user }
      else
        format.html { render :edit }
        format.json { render json: @user.errors, status: :unprocessable_entity }
      end
    end
  end
  
  # DELETE /users/1
  def destroy
    @user.destroy
    respond_to do |format|
      format.html { redirect_to users_url, notice: 'User was successfully destroyed.' }
      format.json { head :no_content }
    end
  end
  
  private
  
  def set_user
    @user = User.find(params[:id])
  end
  
  def user_params
    params.require(:user).permit(:name, :email, :age, :role, 
                                   profile_attributes: [:id, :last_name, :bio, :avatar_url])
  end
  
  def authorize_user!
    unless @user == current_user || current_user.admin?
      redirect_to root_path, alert: 'You are not authorized to perform this action.'
    end
  end
end

# app/controllers/posts_controller.rb
class PostsController < ApplicationController
  before_action :authenticate_user!, except: [:index, :show]
  before_action :set_post, only: [:show, :edit, :update, :destroy]
  before_action :authorize_post!, only: [:edit, :update, :destroy]
  
  # GET /posts
  def index
    @posts = Post.includes(:user, :tags).published.recent.page(params[:page])
    
    if params[:tag].present?
      @posts = @posts.by_tag(params[:tag])
    end
    
    respond_to do |format|
      format.html
      format.json { render json: @posts }
    end
  end
  
  # GET /posts/1
  def show
    @comments = @post.comments.includes(:user).order(:created_at)
    @comment = @post.comments.build(user: current_user)
  end
  
  # GET /posts/new
  def new
    @post = Post.new(user: current_user)
  end
  
  # GET /posts/1/edit
  def edit
  end
  
  # POST /posts
  def create
    @post = Post.new(post_params)
    @post.user = current_user
    
    respond_to do |format|
      if @post.save
        format.html { redirect_to @post, notice: 'Post was successfully created.' }
        format.json { render :show, status: :created, location: @post }
      else
        format.html { render :new }
        format.json { render json: @post.errors, status: :unprocessable_entity }
      end
    end
  end
  
  # PATCH/PUT /posts/1
  def update
    respond_to do |format|
      if @post.update(post_params)
        format.html { redirect_to @post, notice: 'Post was successfully updated.' }
        format.json { render :show, status: :ok, location: @post }
      else
        format.html { render :edit }
        format.json { render json: @post.errors, status: :unprocessable_entity }
      end
    end
  end
  
  # DELETE /posts/1
  def destroy
    @post.destroy
    respond_to do |format|
      format.html { redirect_to posts_url, notice: 'Post was successfully destroyed.' }
      format.json { head :no_content }
    end
  end
  
  private
  
  def set_post
    @post = Post.find(params[:id])
  end
  
  def post_params
    params.require(:post).permit(:title, :content, :published, :tag_list)
  end
  
  def authorize_post!
    unless @post.user == current_user || current_user.admin?
      redirect_to root_path, alert: 'You are not authorized to perform this action.'
    end
  end
end
```

### Views and ERB Templates
```ruby
<!-- app/views/users/index.html.erb -->
<div class="container">
  <h1>Users</h1>
  
  <!-- Search form -->
  <div class="row mb-3">
    <div class="col-md-6">
      <%= form_with(url: users_path, method: :get, local: true, class: "form-inline") do |form| %>
        <div class="form-group mr-2">
          <%= form.text_field :search, value: params[:search], class: "form-control", placeholder: "Search users..." %>
        </div>
        <%= form.submit "Search", class: "btn btn-primary" %>
      <% end %>
    </div>
  </div>
  
  <!-- Users list -->
  <div class="row">
    <% @users.each do |user| %>
      <div class="col-md-4 mb-3">
        <div class="card">
          <% if user.profile&.avatar_url.present? %>
            <img src="<%= user.profile.avatar_url %>" class="card-img-top" alt="<%= user.name %>">
          <% end %>
          <div class="card-body">
            <h5 class="card-title"><%= user.name %></h5>
            <p class="card-text">
              <small class="text-muted"><%= user.email %></small><br>
              <% if user.profile %>
                <%= user.profile.bio %>
              <% end %>
            </p>
            <%= link_to "View Profile", user, class: "btn btn-primary btn-sm" %>
            <% if current_user&.admin? %>
              <%= link_to "Edit", edit_user_path(user), class: "btn btn-secondary btn-sm" %>
            <% end %>
          </div>
        </div>
      </div>
    <% end %>
  </div>
  
  <!-- Pagination -->
  <div class="row">
    <div class="col-md-12">
      <%= paginate @users %>
    </div>
  </div>
</div>

<!-- app/views/posts/show.html.erb -->
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <!-- Post content -->
      <article class="mb-4">
        <header class="mb-3">
          <h1><%= @post.title %></h1>
          <div class="text-muted">
            By <%= link_to @post.user.name, @post.user %> on <%= @post.created_at.strftime("%B %d, %Y") %>
            <% if @post.tags.any? %>
              | Tags: <%= @post.tag_list %>
            <% end %>
          </div>
        </header>
        
        <div class="post-content">
          <%= simple_format(@post.content) %>
        </div>
        
        <% if @post.user == current_user || current_user&.admin? %>
          <div class="mt-3">
            <%= link_to "Edit", edit_post_path(@post), class: "btn btn-secondary" %>
            <%= link_to "Delete", @post, method: :delete, data: { confirm: "Are you sure?" }, class: "btn btn-danger" %>
          </div>
        <% end %>
      </article>
      
      <!-- Comments section -->
      <section class="comments">
        <h3>Comments (<%= @comments.count %>)</h3>
        
        <!-- Comment form -->
        <% if current_user %>
          <div class="comment-form mb-4">
            <h4>Add a comment</h4>
            <%= form_with(model: [@post, @comment], local: true) do |form| %>
              <div class="form-group">
                <%= form.text_area :content, class: "form-control", rows: 3, placeholder: "Write your comment..." %>
              </div>
              <div class="form-group">
                <%= form.submit "Post Comment", class: "btn btn-primary" %>
              </div>
            <% end %>
          </div>
        <% else %>
          <p><%= link_to "Sign in", new_user_session_path %> to add a comment.</p>
        <% end %>
        
        <!-- Comments list -->
        <% @comments.each do |comment| %>
          <div class="comment mb-3">
            <div class="comment-header">
              <strong><%= comment.user.name %></strong>
              <small class="text-muted"><%= comment.created_at.strftime("%B %d, %Y at %I:%M %p") %></small>
              <% if comment.editable_by?(current_user) %>
                <div class="float-right">
                  <%= link_to "Edit", edit_post_comment_path(@post, comment), class: "btn btn-sm btn-outline-secondary" %>
                  <%= link_to "Delete", [@post, comment], method: :delete, data: { confirm: "Are you sure?" }, class: "btn btn-sm btn-outline-danger" %>
                </div>
              <% end %>
            </div>
            <div class="comment-body">
              <%= simple_format(comment.content) %>
            </div>
          </div>
        <% end %>
      </section>
    </div>
    
    <div class="col-md-4">
      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Author info -->
        <div class="card mb-3">
          <div class="card-header">
            About the Author
          </div>
          <div class="card-body">
            <h5><%= @post.user.name %></h5>
            <% if @post.user.profile&.bio.present? %>
              <p><%= @post.user.profile.bio %></p>
            <% end %>
            <%= link_to "View Profile", @post.user, class: "btn btn-primary btn-sm" %>
          </div>
        </div>
        
        <!-- Related posts -->
        <div class="card">
          <div class="card-header">
            Related Posts
          </div>
          <div class="card-body">
            <% @post.user.posts.published.where.not(id: @post.id).recent.limit(5).each do |related_post| %>
              <div class="mb-2">
                <%= link_to related_post.title, related_post, class: "text-decoration-none" %>
                <small class="text-muted d-block"><%= related_post.created_at.strftime("%B %d, %Y") %></small>
              </div>
            <% end %>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
```

## Sinatra Framework

### Basic Sinatra Application
```ruby
# Gemfile
source 'https://rubygems.org'

gem 'sinatra'
gem 'sinatra-activerecord'
gem 'sqlite3'
gem 'rake'
gem 'thin'

group :development do
  gem 'pry'
  gem 'sinatra-reloader'
end

# app.rb
require 'sinatra'
require 'sinatra/activerecord'
require './models'

class MyApp < Sinatra::Base
  configure :development do
    register Sinatra::Reloader
  end
  
  # Root route
  get '/' do
    @posts = Post.all.order(created_at: :desc).limit(10)
    erb :index
  end
  
  # Posts routes
  get '/posts' do
    @posts = Post.all.order(created_at: :desc)
    erb :posts
  end
  
  get '/posts/new' do
    @post = Post.new
    erb :new_post
  end
  
  post '/posts' do
    @post = Post.new(params[:post])
    
    if @post.save
      redirect to "/posts/#{@post.id}"
    else
      erb :new_post
    end
  end
  
  get '/posts/:id' do
    @post = Post.find(params[:id])
    erb :show_post
  end
  
  # API routes
  get '/api/posts' do
    content_type :json
    Post.all.to_json
  end
  
  post '/api/posts' do
    content_type :json
    post = Post.new(JSON.parse(request.body.read))
    
    if post.save
      status 201
      post.to_json
    else
      status 422
      { errors: post.errors.full_messages }.to_json
    end
  end
  
  # Error handling
  error ActiveRecord::RecordNotFound do
    status 404
    erb :not_found
  end
  
  error do
    status 500
    erb :error
  end
end

# models.rb
require 'active_record'

class Post < ActiveRecord::Base
  validates :title, presence: true
  validates :content, presence: true
  
  def excerpt
    content[0..100] + (content.length > 100 ? "..." : "")
  end
end

# Rakefile
require 'sinatra/activerecord/rake'

# Database configuration
db_config = {
  adapter: 'sqlite3',
  database: 'db/database.sqlite3'
}

ActiveRecord::Base.establish_connection(db_config)

# Migration tasks
namespace :db do
  desc "Run migrations"
  task :migrate do
    ActiveRecord::Migration.verbose = true
    ActiveRecord::Migrator.migrate('db/migrate')
  end
  
  desc "Create database"
  task :create do
    puts "Creating database..."
    # Database creation logic
  end
  
  desc "Drop database"
  task :drop do
    puts "Dropping database..."
    File.delete('db/database.sqlite3') if File.exist?('db/database.sqlite3')
  end
  
  desc "Reset database"
  task :reset => [:drop, :create, :migrate]
end

# config.ru
require './app'
run MyApp
```

### Sinatra Views and Templates
```ruby
<!-- views/index.erb -->
<!DOCTYPE html>
<html>
<head>
  <title>My Blog</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="/">My Blog</a>
      <div class="navbar-nav">
        <a class="nav-link" href="/posts">Posts</a>
        <a class="nav-link" href="/posts/new">New Post</a>
      </div>
    </div>
  </nav>
  
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-8">
        <h1>Latest Posts</h1>
        
        <% @posts.each do |post| %>
          <div class="card mb-3">
            <div class="card-body">
              <h2><%= link_to post.title, "/posts/#{post.id}" %></h2>
              <p class="text-muted">
                Posted on <%= post.created_at.strftime("%B %d, %Y") %>
              </p>
              <p><%= post.excerpt %></p>
              <%= link_to "Read more", "/posts/#{post.id}", class: "btn btn-primary" %>
            </div>
          </div>
        <% end %>
      </div>
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            About
          </div>
          <div class="card-body">
            <p>Welcome to my blog! Here you'll find my latest thoughts and ideas.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- views/show_post.erb -->
<!DOCTYPE html>
<html>
<head>
  <title><%= @post.title %></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="/">My Blog</a>
      <div class="navbar-nav">
        <a class="nav-link" href="/posts">Posts</a>
        <a class="nav-link" href="/posts/new">New Post</a>
      </div>
    </div>
  </nav>
  
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-8">
        <article>
          <header class="mb-3">
            <h1><%= @post.title %></h1>
            <p class="text-muted">
              Posted on <%= @post.created_at.strftime("%B %d, %Y at %I:%M %p") %>
            </p>
          </header>
          
          <div class="post-content">
            <%= simple_format(@post.content) %>
          </div>
        </article>
        
        <div class="mt-4">
          <%= link_to "Back to Posts", "/posts", class: "btn btn-secondary" %>
          <%= link_to "Edit", "/posts/#{@post.id}/edit", class: "btn btn-primary" %>
          <%= link_to "Delete", "/posts/#{@post.id}", method: :delete, 
              data: { confirm: "Are you sure?" }, class: "btn btn-danger" %>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            Actions
          </div>
          <div class="card-body">
            <%= link_to "Edit Post", "/posts/#{@post.id}/edit", class: "btn btn-primary btn-block mb-2" %>
            <%= link_to "Delete Post", "/posts/#{@post.id}", method: :delete, 
                data: { confirm: "Are you sure?" }, class: "btn btn-danger btn-block" %>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
```

## API Development

### Rails API Application
```ruby
# Gemfile for API-only Rails app
source 'https://rubygems.org'

gem 'rails', '~> 6.0.0'
gem 'pg', '~> 1.1'
gem 'puma', '~> 3.0'

# API gems
gem 'jbuilder', '~> 2.5'
gem 'rack-cors', require: 'rack/cors'

# Authentication
gem 'devise_token_auth'

# Background jobs
gem 'sidekiq'
gem 'redis'

group :development, :test do
  gem 'rspec-rails', '~> 3.0'
  gem 'factory_bot_rails', '~> 4.0'
end

# config/application.rb
require_relative 'boot'

require "rails/all"

Bundler.require(*Rails.groups)

module ApiApp
  class Application < Rails::Application
    config.load_defaults 6.0
    config.api_only = true
    
    # CORS configuration
    config.middleware.insert_before 0, Rack::Cors do
      allow do
        origins '*'
        resource '*', headers: :any, methods: [:get, :post, :put, :patch, :delete, :options, :head]
      end
    end
  end
end

# app/controllers/application_controller.rb
class ApplicationController < ActionController::API
  include DeviseTokenAuth::Concerns::SetUserByToken
  
  private
  
  def render_error(message, status = :unprocessable_entity)
    render json: { error: message }, status: status
  end
  
  def render_not_found(resource = 'Resource')
    render_error("#{resource} not found", :not_found)
  end
end

# app/controllers/api/v1/users_controller.rb
class Api::V1::UsersController < ApplicationController
  before_action :authenticate_user!, only: [:show, :update, :destroy]
  before_action :set_user, only: [:show, :update, :destroy]
  
  # GET /api/v1/users
  def index
    @users = User.all
    render json: @users
  end
  
  # GET /api/v1/users/1
  def show
    render json: @user
  end
  
  # POST /api/v1/users
  def create
    @user = User.new(user_params)
    
    if @user.save
      render json: @user, status: :created
    else
      render json: { errors: @user.errors.full_messages }, status: :unprocessable_entity
    end
  end
  
  # PATCH/PUT /api/v1/users/1
  def update
    if @user.update(user_params)
      render json: @user
    else
      render json: { errors: @user.errors.full_messages }, status: :unprocessable_entity
    end
  end
  
  # DELETE /api/v1/users/1
  def destroy
    @user.destroy
    head :no_content
  end
  
  private
  
  def set_user
    @user = User.find(params[:id])
  rescue ActiveRecord::RecordNotFound
    render_not_found 'User'
  end
  
  def user_params
    params.require(:user).permit(:name, :email, :password, :password_confirmation)
  end
end

# app/controllers/api/v1/posts_controller.rb
class Api::V1::PostsController < ApplicationController
  before_action :authenticate_user!, except: [:index, :show]
  before_action :set_post, only: [:show, :update, :destroy]
  
  # GET /api/v1/posts
  def index
    @posts = Post.includes(:user).published.recent.page(params[:page])
    
    if params[:search].present?
      @posts = @posts.where("title ILIKE ? OR content ILIKE ?", "%#{params[:search]}%", "%#{params[:search]}%")
    end
    
    render json: {
      posts: @posts,
      meta: {
        current_page: @posts.current_page,
        total_pages: @posts.total_pages,
        total_count: @posts.total_count
      }
    }
  end
  
  # GET /api/v1/posts/1
  def show
    render json: @post, include: [:user, :comments]
  end
  
  # POST /api/v1/posts
  def create
    @post = current_user.posts.build(post_params)
    
    if @post.save
      render json: @post, status: :created
    else
      render json: { errors: @post.errors.full_messages }, status: :unprocessable_entity
    end
  end
  
  # PATCH/PUT /api/v1/posts/1
  def update
    if @post.update(post_params)
      render json: @post
    else
      render json: { errors: @post.errors.full_messages }, status: :unprocessable_entity
    end
  end
  
  # DELETE /api/v1/posts/1
  def destroy
    @post.destroy
    head :no_content
  end
  
  private
  
  def set_post
    @post = Post.find(params[:id])
  rescue ActiveRecord::RecordNotFound
    render_not_found 'Post'
  end
  
  def post_params
    params.require(:post).permit(:title, :content, :published, :tag_list)
  end
end
```

### API Versioning and Documentation
```ruby
# config/routes.rb
Rails.application.routes.draw do
  namespace :api do
    namespace :v1 do
      resources :users, only: [:index, :show, :create, :update, :destroy]
      resources :posts, only: [:index, :show, :create, :update, :destroy]
      resources :comments, only: [:index, :show, :create, :destroy]
      
      # Authentication
      post 'auth/sign_in', to: 'sessions#create'
      post 'auth/sign_out', to: 'sessions#destroy'
      
      # Nested routes
      resources :posts do
        resources :comments, only: [:index, :create]
        resources :likes, only: [:create, :destroy]
      end
    end
    
    # Future versions
    namespace :v2 do
      # V2 API routes
    end
  end
end

# API Documentation with Rswag
# spec/swagger_helper.rb
require 'rswag/specs'

RSpec.configure do |config|
  config.extend Rswag::Specs::Extension
  config.include Rswag::Specs::Helper
  config.swagger_root = Rails.root.join('swagger')
  config.swagger_docs = {
    'v1/swagger.yaml' => {
      url: '/api/v1/swagger.yaml',
      openapi: '3.0.0',
      info: {
        title: 'Blog API',
        version: '1.0.0',
        description: 'API documentation for the blog application'
      },
      paths: {},
      components: {
        securitySchemes: {
          bearerAuth: {
            type: :http,
            scheme: :bearer
          }
        }
      }
    }
  }
end

# spec/requests/api/v1/users_spec.rb
require 'swagger_helper'

RSpec.describe 'Users API', type: :request do
  path '/api/v1/users' do
    get('List all users') do
      tags 'Users'
      produces 'application/json'
      
      response(200, 'successful') do
        schema type: :array, items: { '$ref' => '#/components/schemas/User' }
        
        run_test! do
          create_list(:user, 3)
          get '/api/v1/users'
          expect(response).to have_http_status(200)
          expect(response.content_type).to eq('application/json; charset=utf-8')
        end
      end
    end
    
    post('Create a user') do
      tags 'Users'
      consumes 'application/json'
      produces 'application/json'
      
      parameter name: :user, in: :body, schema: {
        type: :object,
        properties: {
          name: { type: :string },
          email: { type: :string },
          password: { type: :string }
        },
        required: ['name', 'email', 'password']
      }
      
      response(201, 'User created') do
        schema '$ref' => '#/components/schemas/User'
        
        run_test! do
          user_attributes = attributes_for(:user)
          post '/api/v1/users', params: { user: user_attributes }
          expect(response).to have_http_status(201)
          expect(response.content_type).to eq('application/json; charset=utf-8')
        end
      end
      
      response(422, 'Invalid request') do
        schema type: :object,
          properties: {
            errors: { type: :array, items: { type: :string } }
          }
        
        run_test! do
          post '/api/v1/users', params: { user: { name: '' } }
          expect(response).to have_http_status(422)
        end
      end
    end
  end
  
  path '/api/v1/users/{id}' do
    parameter name: :id, in: :path, type: :integer, description: 'User ID'
    
    get('Show a user') do
      tags 'Users'
      produces 'application/json'
      
      response(200, 'successful') do
        schema '$ref' => '#/components/schemas/User'
        
        run_test! do
          user = create(:user)
          get "/api/v1/users/#{user.id}"
          expect(response).to have_http_status(200)
        end
      end
      
      response(404, 'User not found') do
        run_test! do
          get '/api/v1/users/999'
          expect(response).to have_http_status(404)
        end
      end
    end
  end
end
```

## Background Jobs and Services

### Sidekiq Background Jobs
```ruby
# Gemfile
gem 'sidekiq'
gem 'redis'
gem 'sidekiq-cron'

# config/initializers/sidekiq.rb
Sidekiq.configure_server do |config|
  config.redis = Redis.new(url: ENV['REDIS_URL'] || 'redis://localhost:6379/0')
end

Sidekiq.configure_client do |config|
  config.redis = Redis.new(url: ENV['REDIS_URL'] || 'redis://localhost:6379/0')
end

# app/workers/email_worker.rb
class EmailWorker
  include Sidekiq::Worker
  
  def perform(user_id, email_type)
    user = User.find(user_id)
    
    case email_type
    when 'welcome'
      UserMailer.welcome_email(user).deliver_now
    when 'password_reset'
      UserMailer.password_reset_email(user).deliver_now
    when 'notification'
      UserMailer.notification_email(user).deliver_now
    end
  rescue ActiveRecord::RecordNotFound => e
    Sidekiq.logger.error "User not found: #{e.message}"
  end
end

# app/workers/report_worker.rb
class ReportWorker
  include Sidekiq::Worker
  
  def perform(report_type, user_id)
    user = User.find(user_id)
    
    case report_type
    when 'monthly_activity'
      generate_monthly_activity_report(user)
    when 'user_analytics'
      generate_user_analytics_report(user)
    when 'system_health'
      generate_system_health_report
    end
  rescue ActiveRecord::RecordNotFound => e
    Sidekiq.logger.error "User not found: #{e.message}"
  end
  
  private
  
  def generate_monthly_activity_report(user)
    # Generate monthly activity report
    report_data = {
      user_id: user.id,
      posts_count: user.posts.where(created_at: 1.month.ago..Time.current).count,
      comments_count: user.comments.where(created_at: 1.month.ago..Time.current).count,
      likes_received: user.posts.joins(:likes).where(likes: { created_at: 1.month.ago..Time.current }).count
    }
    
    # Send report via email or save to database
    UserMailer.monthly_report(user, report_data).deliver_now
  end
  
  def generate_user_analytics_report(user)
    # Generate user analytics report
  end
  
  def generate_system_health_report
    # Generate system health report
  end
end

# app/workers/image_processing_worker.rb
class ImageProcessingWorker
  include Sidekiq::Worker
  
  def perform(image_id, operations = [])
    image = Image.find(image_id)
    
    operations.each do |operation|
      case operation
      when :resize
        resize_image(image)
      when :crop
        crop_image(image)
      when :compress
        compress_image(image)
      when :watermark
        add_watermark(image)
      end
    end
    
    image.update(processed: true)
  rescue ActiveRecord::RecordNotFound => e
    Sidekiq.logger.error "Image not found: #{e.message}"
  end
  
  private
  
  def resize_image(image)
    # Image resizing logic
  end
  
  def crop_image(image)
    # Image cropping logic
  end
  
  def compress_image(image)
    # Image compression logic
  end
  
  def add_watermark(image)
    # Watermark logic
  end
end

# app/workers/cleanup_worker.rb
class CleanupWorker
  include Sidekiq::Worker
  sidekiq_options retry: 3, queue: 'low'
  
  def perform
    cleanup_old_sessions
    cleanup_expired_tokens
    cleanup_orphaned_files
  end
  
  private
  
  def cleanup_old_sessions
    # Clean up sessions older than 30 days
    Session.where('created_at < ?', 30.days.ago).delete_all
  end
  
  def cleanup_expired_tokens
    # Clean up expired authentication tokens
    Token.where('expires_at < ?', Time.current).delete_all
  end
  
  def cleanup_orphaned_files
    # Clean up files without associated records
    Image.where.not(id: Post.select(:image_id)).delete_all
  end
end

# Cron jobs
# config/initializers/sidekiq_cron.rb
Sidekiq::Cron::Job.load_from_hash({
  'Send daily digest' => {
    'cron' => '0 8 * * *',
    'class' => 'DigestWorker',
    'queue' => 'default'
  },
  'Generate reports' => {
    'cron' => '0 1 * * 0',
    'class' => 'ReportGenerationWorker',
    'queue' => 'reports'
  },
  'Cleanup old data' => {
    'cron' => '0 2 * * *',
    'class' => 'CleanupWorker',
    'queue' => 'low'
  }
})
```

### Service Objects
```ruby
# app/services/user_registration_service.rb
class UserRegistrationService
  include ActiveModel::Model
  include ActiveModel::Attributes
  
  attr_accessor :name, :email, :password, :password_confirmation
  
  validates :name, presence: true, length: { minimum: 2, maximum: 50 }
  validates :email, presence: true, format: { with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i }
  validates :password, presence: true, length: { minimum: 8 }
  validates :password_confirmation, presence: true
  
  def initialize(params = {})
    @name = params[:name]
    @email = params[:email]
    @password = params[:password]
    @password_confirmation = params[:password_confirmation]
  end
  
  def call
    return failure(errors) unless valid?
    
    user = create_user
    return failure(user.errors.full_messages) unless user.persisted?
    
    send_welcome_email(user)
    success(user)
  rescue StandardError => e
    failure([e.message])
  end
  
  private
  
  def create_user
    User.create(
      name: name,
      email: email.downcase,
      password: password,
      password_confirmation: password_confirmation
    )
  end
  
  def send_welcome_email(user)
    EmailWorker.perform_async(user.id, 'welcome')
  end
  
  def success(user)
    OpenStruct.new(success?: true, user: user, errors: [])
  end
  
  def failure(errors)
    OpenStruct.new(success?: false, user: nil, errors: errors)
  end
end

# app/services/post_publishing_service.rb
class PostPublishingService
  def initialize(post, current_user)
    @post = post
    @current_user = current_user
  end
  
  def call
    return failure(['Post not found']) unless @post
    return failure(['Not authorized']) unless authorized?
    
    ActiveRecord::Base.transaction do
      publish_post
      notify_subscribers
      update_search_index
    end
    
    success(@post)
  rescue StandardError => e
    failure([e.message])
  end
  
  private
  
  def authorized?
    @post.user == @current_user || @current_user.admin?
  end
  
  def publish_post
    @post.update!(published: true, published_at: Time.current)
  end
  
  def notify_subscribers
    NotificationWorker.perform_async(@post.id, 'post_published')
  end
  
  def update_search_index
    SearchIndexWorker.perform_async(@post.id, 'update')
  end
  
  def success(post)
    OpenStruct.new(success?: true, post: post, errors: [])
  end
  
  def failure(errors)
    OpenStruct.new(success?: false, post: nil, errors: errors)
  end
end

# app/services/data_export_service.rb
class DataExportService
  def initialize(user, format: 'csv')
    @user = user
    @format = format
  end
  
  def call
    data = collect_data
    formatted_data = format_data(data)
    
    success(formatted_data)
  rescue StandardError => e
    failure([e.message])
  end
  
  private
  
  def collect_data
    {
      user: @user.as_json,
      posts: @user.posts.includes(:comments, :likes).as_json,
      comments: @user.comments.includes(:post).as_json,
      likes: @user.likes.includes(:post).as_json
    }
  end
  
  def format_data(data)
    case @format
    when 'csv'
      to_csv(data)
    when 'json'
      to_json(data)
    when 'xml'
      to_xml(data)
    else
      to_json(data)
    end
  end
  
  def to_csv(data)
    CSV.generate do |csv|
      csv << ['User Data Export']
      csv << ['Name', data[:user]['name']]
      csv << ['Email', data[:user]['email']]
      csv << []
      csv << ['Posts']
      data[:posts].each do |post|
        csv << [post['title'], post['content']]
      end
    end
  end
  
  def to_json(data)
    JSON.pretty_generate(data)
  end
  
  def to_xml(data)
    # XML formatting logic
  end
  
  def success(data)
    OpenStruct.new(success?: true, data: data, errors: [])
  end
  
  def failure(errors)
    OpenStruct.new(success?: false, data: nil, errors: errors)
  end
end
```

## Web Testing

### Capybara Feature Testing
```ruby
# Gemfile
group :test do
  gem 'capybara', '~> 2.13'
  gem 'selenium-webdriver'
  gem 'webdrivers'
  gem 'rspec-rails', '~> 3.0'
  gem 'factory_bot_rails', '~> 4.0'
end

# spec/features/user_registration_spec.rb
require 'rails_helper'

RSpec.feature 'User Registration', type: :feature do
  scenario 'User registers successfully' do
    visit new_user_registration_path
    
    fill_in 'Name', with: 'John Doe'
    fill_in 'Email', with: 'john@example.com'
    fill_in 'Password', with: 'password123'
    fill_in 'Password confirmation', with: 'password123'
    
    click_button 'Sign up'
    
    expect(page).to have_content('Welcome! You have signed up successfully.')
    expect(page).to have_current_path(root_path)
    expect(User.last.email).to eq('john@example.com')
  end
  
  scenario 'User fails registration with invalid data' do
    visit new_user_registration_path
    
    fill_in 'Name', with: ''
    fill_in 'Email', with: 'invalid_email'
    fill_in 'Password', with: '123'
    fill_in 'Password confirmation', with: '456'
    
    click_button 'Sign up'
    
    expect(page).to have_content('Name can\'t be blank')
    expect(page).to have_content('Email is invalid')
    expect(page).to have_content('Password confirmation doesn\'t match Password')
    expect(User.count).to eq(0)
  end
  
  scenario 'User tries to register with existing email' do
    existing_user = create(:user, email: 'existing@example.com')
    
    visit new_user_registration_path
    
    fill_in 'Name', with: 'Jane Doe'
    fill_in 'Email', with: 'existing@example.com'
    fill_in 'Password', with: 'password123'
    fill_in 'Password confirmation', with: 'password123'
    
    click_button 'Sign up'
    
    expect(page).to have_content('Email has already been taken')
    expect(User.count).to eq(1)
  end
end

# spec/features/post_management_spec.rb
RSpec.feature 'Post Management', type: :feature do
  given!(:user) { create(:user) }
  given!(:post) { create(:post, user: user, published: false) }
  
  scenario 'User creates a new post' do
    sign_in user
    visit new_post_path
    
    fill_in 'Title', with: 'My First Post'
    fill_in 'Content', with: 'This is the content of my first post. It should be at least 10 characters long.'
    
    click_button 'Create Post'
    
    expect(page).to have_content('Post was successfully created.')
    expect(page).to have_current_path(post_path(Post.last))
    expect(Post.last.title).to eq('My First Post')
  end
  
  scenario 'User publishes a post' do
    sign_in user
    visit edit_post_path(post)
    
    check 'Published'
    click_button 'Update Post'
    
    expect(page).to have_content('Post was successfully updated.')
    expect(post.reload.published?).to be true
  end
  
  scenario 'User views published posts' do
    create(:post, user: user, published: true)
    create(:post, user: user, published: true)
    
    visit posts_path
    
    expect(page).to have_content(user.posts.published.count, count: 2)
    expect(page).to have_link(post.title)
  end
  
  scenario 'User deletes a post' do
    sign_in user
    visit post_path(post)
    
    click_link 'Delete'
    
    expect(page).to have_content('Post was successfully destroyed.')
    expect(Post.count).to eq(0)
  end
  
  scenario 'Unauthorized user cannot edit post' do
    other_user = create(:user)
    
    sign_in other_user
    visit edit_post_path(post)
    
    expect(page).to have_content('You are not authorized to perform this action.')
    expect(current_path).to eq(root_path)
  end
end

# spec/features/api/posts_spec.rb
RSpec.feature 'API Posts', type: :feature do
  scenario 'User retrieves all posts' do
    create_list(:post, 3, published: true)
    
    visit '/api/v1/posts'
    
    expect(page).to have_http_status(200)
    
    json_response = JSON.parse(page.body)
    expect(json_response['posts'].length).to eq(3)
  end
  
  scenario 'User creates a post via API' do
    user = create(:user)
    
    page.driver.post '/api/v1/posts', 
      headers: { 'Authorization' => "Bearer #{user.auth_token}" },
      body: {
        post: {
          title: 'API Post',
          content: 'This post was created via API',
          published: true
        }
      }.to_json
    
    expect(page).to have_http_status(201)
    
    json_response = JSON.parse(page.body)
    expect(json_response['title']).to eq('API Post')
  end
end
```

### System Testing
```ruby
# Gemfile
group :test do
  gem 'rails_system_testing'
  gem 'puma'
end

# test/application_system_test_case.rb
require 'test_helper'

class ApplicationSystemTestCase < ActionDispatch::SystemTestCase
  driven_by :selenium, using: :chrome
  Capybara.server = :puma
  
  def setup
    super
    # System test setup
  end
  
  def teardown
    super
    # System test teardown
  end
  
  def sign_in(user)
    visit new_user_session_path
    fill_in 'Email', with: user.email
    fill_in 'Password', with: user.password
    click_button 'Log in'
  end
  
  def sign_out
    click_link 'Log out'
  end
  
  def create_post(title:, content:, published: false)
    visit new_post_path
    fill_in 'Title', with: title
    fill_in 'Content', with: content
    check 'Published' if published
    click_button 'Create Post'
  end
end

# test/system/user_flow_test.rb
require 'application_system_test_case'

class UserFlowTest < ApplicationSystemTestCase
  test 'user can register, create post, and comment' do
    # Registration
    visit new_user_registration_path
    
    fill_in 'Name', with: 'John Doe'
    fill_in 'Email', with: 'john@example.com'
    fill_in 'Password', with: 'password123'
    fill_in 'Password confirmation', with: 'password123'
    
    click_button 'Sign up'
    
    assert_text 'Welcome! You have signed up successfully.'
    
    # Sign in
    sign_in(User.last)
    
    # Create post
    create_post(
      title: 'My First Post',
      content: 'This is the content of my first post. It should be at least 10 characters long.',
      published: true
    )
    
    assert_text 'Post was successfully created.'
    
    # Add comment
    fill_in 'Write your comment...', with: 'Great post!'
    click_button 'Post Comment'
    
    assert_text 'Comment was successfully created.'
    
    # View post
    visit posts_path
    click_link 'My First Post'
    
    assert_text 'My First Post'
    assert_text 'Great post!'
  end
  
  test 'user can edit their profile' do
    user = create(:user, name: 'John', email: 'john@example.com')
    
    sign_in(user)
    visit edit_user_registration_path(user)
    
    fill_in 'Name', with: 'John Updated'
    fill_in 'Email', with: 'john.updated@example.com'
    fill_in 'Current password', with: user.password
    
    click_button 'Update'
    
    assert_text 'Your account has been updated successfully.'
    
    user.reload
    assert_equal 'John Updated', user.name
    assert_equal 'john.updated@example.com', user.email
  end
  
  test 'user cannot access admin features' do
    user = create(:user)
    
    sign_in(user)
    visit admin_dashboard_path
    
    assert_text 'You are not authorized to access this page.'
    assert_equal root_path, current_path
  end
end

# test/system/api_test.rb
require 'application_system_test_case'

class ApiTest < ApplicationSystemTestCase
  test 'API returns correct JSON responses' do
    create_list(:post, 3, published: true)
    
    visit '/api/v1/posts'
    
    assert_equal 200, page.status_code
    
    json_response = JSON.parse(page.body)
    assert_equal 3, json_response['posts'].length
  end
  
  test 'API handles authentication' do
    visit '/api/v1/posts'
    
    assert_equal 200, page.status_code
    
    # Test unauthorized access
    page.driver.post '/api/v1/posts', body: { post: { title: 'Test' } }.to_json
    
    assert_equal 401, page.status_code
  end
end
```

## Best Practices

### Rails Best Practices
```ruby
# Good: Fat models, skinny controllers
class Post < ApplicationRecord
  # Business logic in model
  def publish!
    update!(published: true, published_at: Time.current)
    notify_subscribers
    update_search_index
  end
  
  private
  
  def notify_subscribers
    # Notification logic
  end
  
  def update_search_index
    # Search index logic
  end
end

class PostsController < ApplicationController
  # Controller only handles HTTP concerns
  def update
    if @post.update(post_params)
      redirect_to @post, notice: 'Post was successfully updated.'
    else
      render :edit
    end
  end
  
  private
  
  def post_params
    params.require(:post).permit(:title, :content, :published)
  end
end

# Good: Use service objects for complex operations
class PostPublishingService
  def initialize(post, current_user)
    @post = post
    @current_user = current_user
  end
  
  def call
    # Complex publishing logic
  end
end

# Good: Use background jobs for heavy operations
class EmailWorker
  include Sidekiq::Worker
  
  def perform(user_id, email_type)
    # Email sending logic
  end
end

# Good: Proper error handling
class ApplicationController < ActionController::API
  rescue_from ActiveRecord::RecordNotFound, with: :not_found
  rescue_from StandardError, with: :server_error
  
  private
  
  def not_found(exception)
    render json: { error: exception.message }, status: :not_found
  end
  
  def server_error(exception)
    Rails.logger.error exception
    render json: { error: 'Internal server error' }, status: :internal_server_error
  end
end
```

### Security Best Practices
```ruby
# Good: Strong parameters
class UsersController < ApplicationController
  def create
    @user = User.new(user_params)
    # ...
  end
  
  private
  
  def user_params
    params.require(:user).permit(:name, :email, :age)
  end
end

# Good: CSRF protection
class ApplicationController < ActionController::Base
  protect_from_forgery with: :exception
end

# Good: Authentication and authorization
class ApplicationController < ActionController::Base
  before_action :authenticate_user!
  
  private
  
  def authenticate_user!
    redirect_to login_path unless current_user
  end
  
  def authorize_user!
    redirect_to root_path, alert: 'Not authorized' unless authorized?
  end
end

# Good: SQL injection prevention
class Post < ApplicationRecord
  # Using parameterized queries
  def self.search(query)
    where("title ILIKE ? OR content ILIKE ?", "%#{query}%", "%#{query}%")
  end
end

# Good: XSS prevention
class PostsController < ApplicationController
  def show
    # Sanitize user input
    @content = sanitize_content(params[:content])
  end
  
  private
  
  def sanitize_content(content)
    # Sanitization logic
  end
end
```

## Common Pitfalls

### Web Development Pitfalls
```ruby
# Pitfall: N+1 queries
# Bad: Loading associations in loop
@posts.each do |post|
  puts post.user.name  # N+1 query
  puts post.comments.count  # N+1 query
end

# Solution: Eager loading
@posts.includes(:user, :comments).each do |post|
  puts post.user.name
  puts post.comments.count
end

# Pitfall: Mass assignment vulnerability
# Bad: Allowing all parameters
def update
  @user.update(params[:user])  # Dangerous
end

# Solution: Strong parameters
def update
  @user.update(user_params)  # Safe
end

# Pitfall: Not handling errors properly
# Bad: No error handling
def create
  @post = Post.new(post_params)
  @post.save
end

# Solution: Proper error handling
def create
  @post = Post.new(post_params)
  
  if @post.save
    redirect_to @post, notice: 'Post created successfully.'
  else
    render :new, status: :unprocessable_entity
  end
end

# Pitfall: Not using background jobs for heavy operations
# Bad: Sending email in controller
def create
  @user = User.new(user_params)
  
  if @user.save
    UserMailer.welcome_email(@user).deliver_now  # Blocks request
    redirect_to @user
  else
    render :new
  end
end

# Solution: Use background jobs
def create
  @user = User.new(user_params)
  
  if @user.save
    EmailWorker.perform_async(@user.id, 'welcome')
    redirect_to @user
  else
    render :new
  end
end
```

## Summary

Ruby web development provides:

**Rails Framework:**
- MVC architecture with convention over configuration
- ActiveRecord for database operations
- ActionView for templating
- ActionController for request handling
- Rich ecosystem of gems and extensions

**Sinatra Framework:**
- Lightweight microframework
- Flexible routing and middleware
- Easy API development
- Minimal configuration required

**API Development:**
- RESTful API design patterns
- API versioning strategies
- Authentication and authorization
- Comprehensive API documentation

**Background Processing:**
- Sidekiq for job processing
- Redis for job queuing
- Cron jobs for scheduled tasks
- Error handling and retries

**Service Objects:**
- Business logic encapsulation
- Clean separation of concerns
- Reusable service patterns
- Error handling strategies

**Web Testing:**
- Capybara for feature testing
- System testing with Rails
- API testing strategies
- Comprehensive test coverage

**Best Practices:**
- Fat models, skinny controllers
- Proper error handling
- Security considerations
- Performance optimization

**Common Pitfalls:**
- N+1 query problems
- Mass assignment vulnerabilities
- Poor error handling
- Blocking operations in controllers

Ruby web development provides robust frameworks and tools for building modern web applications with proper architecture, security, and performance considerations.
