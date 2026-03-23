# Ruby on Rails Introduction Workshop

## Overview

This workshop introduces Ruby on Rails, the popular web application framework. Perfect for developers who know Ruby basics and want to learn web development with Rails.

## Workshop Structure

### Part 1: Rails Fundamentals
- What is Ruby on Rails?
- Rails philosophy and conventions
- MVC architecture
- Setting up a Rails application

### Part 2: Models and Databases
- Active Record basics
- Database migrations
- Model validations and associations
- Database queries and relationships

### Part 3: Controllers and Views
- Routes and controllers
- View templates and ERB
- Forms and user input
- RESTful routing

### Part 4: Rails Features
- Asset pipeline
- Testing in Rails
- Rails console
- Deployment basics

## Part 1: Rails Fundamentals

### Exercise 1: Understanding Rails MVC
```ruby
# Exercise: Understand the Model-View-Controller pattern

# Model (app/models/user.rb)
class User < ApplicationRecord
  validates :name, presence: true
  validates :email, presence: true, uniqueness: true
  
  has_many :posts
  has_many :comments, through: :posts
  
  def full_name
    "#{first_name} #{last_name}"
  end
  
  def recent_posts
    posts.where('created_at > ?', 1.week.ago)
  end
end

# Controller (app/controllers/users_controller.rb)
class UsersController < ApplicationController
  before_action :set_user, only: [:show, :edit, :update, :destroy]
  
  def index
    @users = User.all
  end
  
  def show
    # @user is set by before_action
    @posts = @user.posts.includes(:comments)
  end
  
  def new
    @user = User.new
  end
  
  def create
    @user = User.new(user_params)
    
    if @user.save
      redirect_to @user, notice: 'User was successfully created.'
    else
      render :new
    end
  end
  
  private
  
  def set_user
    @user = User.find(params[:id])
  end
  
  def user_params
    params.require(:user).permit(:name, :email, :first_name, :last_name)
  end
end

# View (app/views/users/show.html.erb)
<h1><%= @user.name %></h1>
<p>Email: <%= @user.email %></p>
<p>Full Name: <%= @user.full_name %></p>

<h2>Posts</h2>
<% @posts.each do |post| %>
  <div class="post">
    <h3><%= post.title %></h3>
    <p><%= post.content %></p>
    <small>Comments: <%= post.comments.count %></small>
  </div>
<% end %>
```

### Exercise 2: Rails Routes
```ruby
# Exercise: Configure Rails routes

# config/routes.rb
Rails.application.routes.draw do
  # Root route
  root 'home#index'
  
  # Resource routes (RESTful)
  resources :users
  resources :posts do
    resources :comments
  end
  
  # Nested resources
  resources :categories do
    resources :products
  end
  
  # Custom routes
  get '/about', to: 'pages#about'
  get '/contact', to: 'pages#contact'
  
  # Member routes
  resources :users do
    member do
      get :profile
      post :follow
    end
  end
  
  # Collection routes
  resources :posts do
    collection do
      get :published
      get :drafts
    end
  end
  
  # Namespace routes
  namespace :admin do
    resources :users
    resources :posts
  end
end

# Route helpers available in views:
# users_path           -> /users
# new_user_path        -> /users/new
# edit_user_path(1)    -> /users/1/edit
# user_path(1)         -> /users/1
# admin_users_path     -> /admin/users
```

### Exercise 3: Rails Console
```ruby
# Exercise: Practice with Rails console commands

# In Rails console (rails console):

# Create records
user = User.create(name: "Alice", email: "alice@example.com")
post = Post.create(title: "Hello Rails", content: "My first post", user: user)

# Find records
user = User.find(1)
users = User.where("created_at > ?", 1.week.ago)
user = User.find_by(email: "alice@example.com")

# Associations
user.posts
post.user
user.posts.create(title: "Another post", content: "More content")

# Queries
User.joins(:posts).where(posts: { published: true })
User.includes(:posts).references(:posts)
User.left_joins(:posts).where(posts: { id: nil })

# Calculations
User.count
User.where(active: true).count
Post.average(:comments_count)

# Scopes
class Post < ApplicationRecord
  scope :published, -> { where(published: true) }
  scope :recent, -> { where('created_at > ?', 1.week.ago) }
  scope :by_user, ->(user) { where(user: user) }
end

# Using scopes
Post.published.recent
Post.by_user(user).published
```

## Part 2: Models and Databases

### Exercise 4: Database Migrations
```ruby
# Exercise: Create and run database migrations

# Generate migration
# rails generate migration CreateUsers

class CreateUsers < ActiveRecord::Migration[7.0]
  def change
    create_table :users do |t|
      t.string :name, null: false
      t.string :email, null: false
      t.string :first_name
      t.string :last_name
      t.text :bio
      t.boolean :active, default: true
      t.timestamps
    end
    
    add_index :users, :email, unique: true
    add_index :users, :name
  end
end

# Add column migration
# rails generate migration AddAvatarToUsers avatar:string

class AddAvatarToUsers < ActiveRecord::Migration[7.0]
  def change
    add_column :users, :avatar, :string
    add_column :users, :avatar_processing, :boolean, default: false
  end
end

# Run migrations
# rails db:migrate

# Rollback migration
# rails db:rollback

# Reset database
# rails db:migrate:reset
```

### Exercise 5: Model Validations
```ruby
# Exercise: Implement comprehensive model validations

class User < ApplicationRecord
  # Presence validations
  validates :name, presence: { message: "Name cannot be blank" }
  validates :email, presence: true
  
  # Uniqueness validations
  validates :email, uniqueness: { 
    case_sensitive: false,
    message: "Email already exists"
  }
  
  # Format validations
  validates :email, format: { 
    with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i,
    message: "Invalid email format"
  }
  
  # Length validations
  validates :name, length: { 
    minimum: 2,
    maximum: 50,
    too_short: "Name must be at least 2 characters",
    too_long: "Name cannot be more than 50 characters"
  }
  
  # Numericality validations
  validates :age, numericality: { 
    greater_than: 0,
    less_than: 150,
    only_integer: true
  }
  
  # Inclusion validations
  validates :status, inclusion: { 
    in: %w[active inactive suspended],
    message: "%{value} is not a valid status"
  }
  
  # Custom validations
  validate :password_complexity
  
  # Callbacks
  before_save :normalize_email
  after_create :send_welcome_email
  
  # Scopes
  scope :active, -> { where(active: true) }
  scope :by_name, ->(name) { where('name ILIKE ?', "%#{name}%") }
  scope :recent, -> { where('created_at > ?', 1.week.ago) }
  
  private
  
  def password_complexity
    if password.present? && password.length < 8
      errors.add(:password, "Password must be at least 8 characters")
    end
  end
  
  def normalize_email
    self.email = email.downcase.strip if email.present?
  end
  
  def send_welcome_email
    UserMailer.welcome_email(self).deliver_later
  end
end

# Test validations
user = User.new(name: "", email: "invalid-email")
user.valid?  # false
user.errors.full_messages  # ["Name cannot be blank", "Invalid email format"]
```

### Exercise 6: Model Associations
```ruby
# Exercise: Set up comprehensive model associations

class User < ApplicationRecord
  has_many :posts, dependent: :destroy
  has_many :comments, through: :posts
  has_many :likes, dependent: :destroy
  has_many :liked_posts, through: :likes, source: :post
  
  # One-to-one relationship
  has_one :profile, dependent: :destroy
  
  # Polymorphic associations
  has_many :notifications, as: :notifiable, dependent: :destroy
  
  # Through associations
  has_many :friendships
  has_many :friends, through: :friendships
  has_many :inverse_friendships, class_name: 'Friendship', foreign_key: 'friend_id'
  has_many :inverse_friends, through: :inverse_friendships, source: :user
end

class Post < ApplicationRecord
  belongs_to :user
  has_many :comments, dependent: :destroy
  has_many :likes, dependent: :destroy
  has_many :liking_users, through: :likes, source: :user
  
  # Polymorphic associations
  belongs_to :category, polymorphic: true
  
  # Nested attributes
  accepts_nested_attributes_for :comments, allow_destroy: true
  
  # Callbacks
  before_destroy :log_destruction
  
  # Scopes
  scope :published, -> { where(published: true) }
  scope :by_category, ->(category) { where(category: category) }
  scope :recent, -> { order(created_at: :desc) }
  
  private
  
  def log_destruction
    Rails.logger.info "Post #{id} was destroyed"
  end
end

class Comment < ApplicationRecord
  belongs_to :post
  belongs_to :user
  
  # Polymorphic associations
  belongs_to :commentable, polymorphic: true
  
  # Validations
  validates :content, presence: true, length: { minimum: 5 }
  
  # Callbacks
  after_create :notify_post_author
  
  private
  
  def notify_post_author
    Notification.create(
      user: post.user,
      notifiable: self,
      message: "New comment on your post"
    )
  end
end

class Like < ApplicationRecord
  belongs_to :user
  belongs_to :post
  
  # Ensure unique likes
  validates :user_id, uniqueness: { scope: :post_id }
end

class Profile < ApplicationRecord
  belongs_to :user
  
  # Validations
  validates :bio, length: { maximum: 500 }
  validates :location, length: { maximum: 100 }
  
  # Callbacks
  after_create :set_default_avatar
end

# Usage examples
user = User.find(1)
posts = user.posts.includes(:comments, :likes)
user.liked_posts
user.friends.count

post = Post.find(1)
post.user.name
post.comments.count
post.liking_users
```

## Part 3: Controllers and Views

### Exercise 7: Controller Patterns
```ruby
# Exercise: Implement advanced controller patterns

class PostsController < ApplicationController
  before_action :authenticate_user!, except: [:index, :show]
  before_action :set_post, only: [:show, :edit, :update, :destroy]
  before_action :authorize_post!, only: [:edit, :update, :destroy]
  
  # GET /posts
  def index
    @posts = Post.includes(:user, :comments)
                   .published
                   .recent
                   .page(params[:page])
                   .per(10)
    
    respond_to do |format|
      format.html
      format.json { render json: @posts }
    end
  end
  
  # GET /posts/1
  def show
    @comments = @post.comments.includes(:user).recent
    @like = current_user.likes.find_or_initialize_by(post: @post) if user_signed_in?
    
    respond_to do |format|
      format.html
      format.json { render json: @post }
    end
  end
  
  # GET /posts/new
  def new
    @post = current_user.posts.new
  end
  
  # POST /posts
  def create
    @post = current_user.posts.new(post_params)
    
    respond_to do |format|
      if @post.save
        format.html { redirect_to @post, notice: 'Post was successfully created.' }
        format.json { render json: @post, status: :created }
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
        format.json { render json: @post }
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
  
  # Custom actions
  def publish
    @post.update(published: true, published_at: Time.current)
    redirect_to @post, notice: 'Post published!'
  end
  
  def unpublish
    @post.update(published: false, published_at: nil)
    redirect_to @post, notice: 'Post unpublished.'
  end
  
  private
  
  def set_post
    @post = Post.find(params[:id])
  end
  
  def post_params
    params.require(:post).permit(:title, :content, :category_id, :published)
  end
  
  def authorize_post!
    return if current_user == @post.user || current_user.admin?
    
    redirect_to posts_path, alert: 'You are not authorized to perform this action.'
  end
end
```

### Exercise 8: View Templates
```erb
<!-- Exercise: Create comprehensive view templates -->

<!-- app/views/posts/index.html.erb -->
<div class="posts-index">
  <div class="header">
    <h1>Posts</h1>
    <% if user_signed_in? %>
      <%= link_to 'New Post', new_post_path, class: 'btn btn-primary' %>
    <% end %>
  </div>
  
  <div class="filters">
    <%= form_with url: posts_path, method: :get, class: 'filter-form' do |form| %>
      <%= form.text_field :search, placeholder: 'Search posts...', value: params[:search] %>
      <%= form.select :category, options_from_collection_for_select(Category.all, :id, :name, params[:category]), 
                     { include_blank: 'All Categories' } %>
      <%= form.submit 'Filter' %>
    <% end %>
  </div>
  
  <div class="posts-list">
    <% @posts.each do |post| %>
      <div class="post-card">
        <div class="post-header">
          <h2><%= link_to post.title, post %></h2>
          <div class="post-meta">
            by <%= link_to post.user.name, post.user %> • 
            <%= time_ago_in_words(post.created_at) %> ago
          </div>
        </div>
        
        <div class="post-content">
          <%= truncate(post.content, length: 200) %>
        </div>
        
        <div class="post-footer">
          <%= link_to "#{post.comments.count} comments", post %> •
          <%= link_to "#{post.likes.count} likes", post %>
          
          <% if user_signed_in? && current_user == post.user %>
            <div class="post-actions">
              <%= link_to 'Edit', edit_post_path(post) %> •
              <%= link_to 'Delete', post, method: :delete, confirm: 'Are you sure?' %>
            </div>
          <% end %>
        </div>
      </div>
    <% end %>
  </div>
  
  <%= paginate @posts %>
</div>

<!-- app/views/posts/show.html.erb -->
<div class="post-show">
  <article class="post">
    <header class="post-header">
      <h1><%= @post.title %></h1>
      <div class="post-meta">
        by <%= link_to @post.user.name, @post.user %> • 
        <%= @post.created_at.strftime('%B %d, %Y') %> •
        Category: <%= link_to @post.category.name, @post.category %>
      </div>
    </header>
    
    <div class="post-content">
      <%= simple_format(@post.content) %>
    </div>
    
    <footer class="post-footer">
      <div class="interactions">
        <% if user_signed_in? %>
          <%= link_to 'Like', post_likes_path(@post), method: :post, class: 'like-btn' %>
          <%= link_to 'Comment', '#comment-form', class: 'comment-btn' %>
        <% end %>
        
        <div class="counts">
          <%= @post.likes.count %> likes • <%= @post.comments.count %> comments
        </div>
      </div>
      
      <% if user_signed_in? && current_user == @post.user %>
        <div class="author-actions">
          <%= link_to 'Edit', edit_post_path(@post) %> •
          <%= link_to 'Delete', @post, method: :delete, confirm: 'Are you sure?' %>
        </div>
      <% end %>
    </footer>
  </article>
  
  <section class="comments">
    <h2>Comments (<%= @comments.count %>)</h2>
    
    <% @comments.each do |comment| %>
      <div class="comment">
        <div class="comment-header">
          <%= link_to comment.user.name, comment.user %> •
          <%= time_ago_in_words(comment.created_at) %> ago
        </div>
        <div class="comment-content">
          <%= simple_format(comment.content) %>
        </div>
      </div>
    <% end %>
    
    <% if user_signed_in? %>
      <div id="comment-form" class="comment-form">
        <h3>Add a Comment</h3>
        <%= form_with model: [@post, @comment] do |form| %>
          <div class="field">
            <%= form.text_area :content, placeholder: 'Write your comment...', rows: 4 %>
          </div>
          <div class="actions">
            <%= form.submit 'Post Comment', class: 'btn btn-primary' %>
          </div>
        <% end %>
      </div>
    <% end %>
  </section>
</div>
```

### Exercise 9: Forms and User Input
```erb
<!-- Exercise: Create advanced form templates -->

<!-- app/views/posts/_form.html.erb -->
<%= form_with(model: post, local: true) do |form| %>
  <% if post.errors.any? %>
    <div class="error-messages">
      <h3><%= pluralize(post.errors.count, "error") %> prohibited this post from being saved:</h3>
      <ul>
        <% post.errors.full_messages.each do |message| %>
          <li><%= message %></li>
        <% end %>
      </ul>
    </div>
  <% end %>
  
  <div class="field">
    <%= form.label :title %>
    <%= form.text_field :title, class: 'form-control' %>
    <small class="form-text">Maximum 100 characters</small>
  </div>
  
  <div class="field">
    <%= form.label :content %>
    <%= form.text_area :content, rows: 10, class: 'form-control' %>
  </div>
  
  <div class="field">
    <%= form.label :category_id %>
    <%= form.collection_select :category_id, Category.all, :id, :name, 
                           { prompt: 'Select a category' }, { class: 'form-control' } %>
  </div>
  
  <div class="field">
    <%= form.label :tags %>
    <%= form.text_field :tag_list, value: post.tag_list.to_s, class: 'form-control' %>
    <small class="form-text">Separate tags with commas</small>
  </div>
  
  <div class="field">
    <%= form.check_box :published %>
    <%= form.label :published %>
  </div>
  
  <div class="field">
    <%= form.label :featured_image %>
    <%= form.file_field :featured_image, class: 'form-control' %>
  </div>
  
  <div class="actions">
    <%= form.submit post.new_record? ? 'Create Post' : 'Update Post', class: 'btn btn-primary' %>
    <%= link_to 'Cancel', posts_path, class: 'btn btn-secondary' %>
  </div>
<% end %>

<!-- app/views/users/_form.html.erb -->
<%= form_with(model: user, local: true) do |form| %>
  <div class="user-form">
    <div class="basic-info">
      <h3>Basic Information</h3>
      
      <div class="field">
        <%= form.label :first_name %>
        <%= form.text_field :first_name, class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.label :last_name %>
        <%= form.text_field :last_name, class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.label :email %>
        <%= form.email_field :email, class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.label :username %>
        <%= form.text_field :username, class: 'form-control' %>
      </div>
    </div>
    
    <div class="profile-info">
      <h3>Profile Information</h3>
      
      <div class="field">
        <%= form.label :bio %>
        <%= form.text_area :bio, rows: 4, class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.label :location %>
        <%= form.text_field :location, class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.label :website %>
        <%= form.url_field :website, class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.label :avatar %>
        <%= form.file_field :avatar, class: 'form-control' %>
      </div>
    </div>
    
    <div class="preferences">
      <h3>Preferences</h3>
      
      <div class="field">
        <%= form.label :timezone %>
        <%= form.time_zone_select :timezone, ActiveSupport::TimeZone.all, 
                               class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.label :language %>
        <%= form.select :language, 
                       options_for_select([['English', 'en'], ['Spanish', 'es'], ['French', 'fr']]),
                       class: 'form-control' %>
      </div>
      
      <div class="field">
        <%= form.check_box :email_notifications %>
        <%= form.label :email_notifications %>
      </div>
    </div>
    
    <div class="actions">
      <%= form.submit 'Save Profile', class: 'btn btn-primary' %>
      <%= link_to 'Cancel', user_path(@user), class: 'btn btn-secondary' %>
    </div>
  </div>
<% end %>
```

## Part 4: Rails Features

### Exercise 10: Asset Pipeline
```ruby
# Exercise: Configure and use the Rails asset pipeline

# app/assets/stylesheets/application.scss
@import "bootstrap";
@import "variables";
@import "components";
@import "layout";

// Custom variables
$primary-color: #007bff;
$secondary-color: #6c757d;
$success-color: #28a745;
$danger-color: #dc3545;

// Global styles
body {
  font-family: 'Inter', sans-serif;
  line-height: 1.6;
  color: #333;
}

// Component styles
.btn {
  padding: 8px 16px;
  border-radius: 4px;
  text-decoration: none;
  display: inline-block;
  
  &.btn-primary {
    background-color: $primary-color;
    color: white;
    
    &:hover {
      background-color: darken($primary-color, 10%);
    }
  }
}

// app/assets/javascripts/application.js
//= require rails-ujs
//= require turbolinks
//= require bootstrap
//= require_tree .

// Custom JavaScript
document.addEventListener('turbolinks:load', function() {
  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  });
  
  // Initialize modals
  var modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'))
  var modalList = modalTriggerList.map(function (modalTriggerEl) {
    return new bootstrap.Modal(modalTriggerEl)
  });
});

// app/assets/javascripts/posts.js
class PostsManager {
  constructor() {
    this.initEventListeners();
  }
  
  initEventListeners() {
    document.addEventListener('click', (e) => {
      if (e.target.matches('.like-btn')) {
        this.handleLike(e.target);
      }
      
      if (e.target.matches('.comment-btn')) {
        this.handleComment(e.target);
      }
    });
  }
  
  handleLike(button) {
    const postId = button.dataset.postId;
    
    fetch(`/posts/${postId}/likes`, {
      method: 'POST',
      headers: {
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => response.json())
    .then(data => {
      this.updateLikeButton(button, data);
    });
  }
  
  updateLikeButton(button, data) {
    button.textContent = `${data.likes} likes`;
    button.classList.toggle('liked', data.liked);
  }
  
  handleComment(button) {
    const commentForm = document.getElementById('comment-form');
    commentForm.scrollIntoView({ behavior: 'smooth' });
    commentForm.querySelector('textarea').focus();
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  new PostsManager();
});
```

### Exercise 11: Testing in Rails
```ruby
# Exercise: Write comprehensive Rails tests

# spec/models/user_spec.rb
require 'rails_helper'

RSpec.describe User, type: :model do
  # Validations
  it { should validate_presence_of(:name) }
  it { should validate_presence_of(:email) }
  it { should validate_uniqueness_of(:email).case_insensitive }
  it { should validate_length_of(:name).is_at_least(2).is_at_most(50) }
  it { should allow_value('user@example.com').for(:email) }
  it { should_not allow_value('invalid-email').for(:email) }
  
  # Associations
  it { should have_many(:posts).dependent(:destroy) }
  it { should have_many(:comments).through(:posts) }
  it { should have_one(:profile).dependent(:destroy) }
  
  # Scopes
  describe '.active' do
    it 'returns only active users' do
      active_user = create(:user, active: true)
      inactive_user = create(:user, active: false)
      
      expect(User.active).to include(active_user)
      expect(User.active).not_to include(inactive_user)
    end
  end
  
  # Instance methods
  describe '#full_name' do
    it 'returns the full name' do
      user = build(:user, first_name: 'John', last_name: 'Doe')
      expect(user.full_name).to eq('John Doe')
    end
    
    it 'handles missing names' do
      user = build(:user, first_name: nil, last_name: 'Doe')
      expect(user.full_name).to eq('Doe')
    end
  end
  
  # Callbacks
  describe 'after_create' do
    it 'sends welcome email' do
      expect(UserMailer).to receive(:welcome_email)
      create(:user)
    end
  end
end

# spec/controllers/posts_controller_spec.rb
require 'rails_helper'

RSpec.describe PostsController, type: :controller do
  let(:user) { create(:user) }
  let(:post) { create(:post, user: user) }
  
  before do
    sign_in user
  end
  
  describe 'GET #index' do
    it 'returns a successful response' do
      get :index
      expect(response).to be_successful
    end
    
    it 'assigns @posts' do
      get :index
      expect(assigns(:posts)).to_not be_nil
    end
  end
  
  describe 'GET #show' do
    it 'returns a successful response' do
      get :show, params: { id: post.to_param }
      expect(response).to be_successful
    end
    
    it 'assigns @post' do
      get :show, params: { id: post.to_param }
      expect(assigns(:post)).to eq(post)
    end
  end
  
  describe 'POST #create' do
    context 'with valid parameters' do
      it 'creates a new Post' do
        expect {
          post :create, params: { post: attributes_for(:post) }
        }.to change(Post, :count).by(1)
      end
      
      it 'redirects to the created post' do
        post :create, params: { post: attributes_for(:post) }
        expect(response).to redirect_to(Post.last)
      end
    end
    
    context 'with invalid parameters' do
      it 'does not create a new Post' do
        expect {
          post :create, params: { post: attributes_for(:post, title: nil) }
        }.not_to change(Post, :count)
      end
      
      it 'renders the new template' do
        post :create, params: { post: attributes_for(:post, title: nil) }
        expect(response).to render_template(:new)
      end
    end
  end
end

# spec/features/user_creates_post_spec.rb
require 'rails_helper'

RSpec.feature 'User creates a post', type: :feature do
  let(:user) { create(:user) }
  
  before do
    sign_in user
  end
  
  scenario 'with valid data' do
    visit new_post_path
    
    fill_in 'Title', with: 'My First Post'
    fill_in 'Content', with: 'This is the content of my first post.'
    select 'Technology', from: 'Category'
    click_button 'Create Post'
    
    expect(page).to have_content('Post was successfully created')
    expect(page).to have_content('My First Post')
    expect(page).to have_content('This is the content of my first post.')
  end
  
  scenario 'with invalid data' do
    visit new_post_path
    
    fill_in 'Title', with: ''
    fill_in 'Content', with: 'Content without title'
    click_button 'Create Post'
    
    expect(page).to have_content('error')
    expect(page).to have_content("Title can't be blank")
  end
end
```

### Exercise 12: Deployment Basics
```ruby
# Exercise: Configure Rails for deployment

# config/environments/production.rb
Rails.application.configure do
  config.cache_classes = true
  config.eager_load = true
  config.consider_all_requests_local = false
  config.action_controller.perform_caching = true
  config.public_file_server.enabled = ENV['RAILS_SERVE_STATIC_FILES'].present?
  config.assets.compile = false
  config.log_level = :info
  config.log_tags = [:request_id]
  config.i18n.fallbacks = true
  config.active_support.deprecation = :notify
  config.active_record.migration_error = :page_load
  config.active_record.verbose_query_logs = true
  
  # Asset pipeline
  config.assets.digest = true
  config.assets.js_compressor = :terser
  config.assets.css_compressor = :sass
  
  # Database
  config.database_configuration = {
    adapter: 'postgresql',
    encoding: 'unicode',
    pool: 5
  }
  
  # Security
  config.force_ssl = true
  config.ssl_options = { hsts: { expires: 1.year, subdomains: true, preload: true } }
end

# Dockerfile
FROM ruby:3.2.2-alpine

# Install dependencies
RUN apk add --no-cache build-base postgresql-dev tzdata

# Set working directory
WORKDIR /app

# Copy Gemfile and Gemfile.lock
COPY Gemfile Gemfile.lock

# Install gems
RUN bundle config set --local without 'development test'
RUN bundle install --jobs 4 --retry 3

# Copy application code
COPY . .

# Precompile assets
RUN bundle exec rails assets:precompile

# Set environment variables
ENV RAILS_ENV=production
ENV RAILS_LOG_TO_STDOUT=true
ENV RAILS_SERVE_STATIC_FILES=true

# Expose port
EXPOSE 3000

# Start the application
CMD ["bundle", "exec", "rails", "server", "-b", "0.0.0.0"]

# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "3000:3000"
    environment:
      - DATABASE_URL=postgresql://postgres:password@db:5432/myapp_production
      - REDIS_URL=redis://redis:6379/0
    depends_on:
      - db
      - redis
    volumes:
      - ./log:/app/log

  db:
    image: postgres:15-alpine
    environment:
      - POSTGRES_DB=myapp_production
      - POSTGRES_USER=postgres
      - POSTGRES_PASSWORD=password
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data

volumes:
  postgres_data:
  redis_data:

# Capfile for Capistrano
require 'capistrano/rails'
require 'capistrano/bundler'
require 'capistrano/rbenv'
require 'capistrano/puma'

# Deploy configuration
set :application, 'myapp'
set :repo_url, 'git@github.com:username/myapp.git'
set :deploy_to, '/var/www/myapp'
set :branch, 'main'
set :rbenv_type, :user
set :rbenv_ruby, '3.2.2'
set :rbenv_prefix, "/usr/local/rbenv/versions/#{fetch(:rbenv_ruby)}/bin"
set :keep_assets, 2
set :keep_releases, 5

# Server configuration
server 'your-server.com', user: 'deploy', roles: %w{app db web}
```

## Workshop Projects

### Project 1: Blog Application
```ruby
# Build a complete blog application with:
# - User authentication
# - Post creation and management
# - Comments system
# - Tagging functionality
# - Search capabilities
# - Admin panel

# Key features to implement:
# - User registration and login
# - CRUD operations for posts
# - Rich text editing
# - Image uploads
# - Comment threading
# - Tag cloud
# - Full-text search
# - RSS feed
# - Admin dashboard
```

### Project 2: E-commerce Store
```ruby
# Build an e-commerce application with:
# - Product catalog
# - Shopping cart
# - Order management
# - Payment processing
# - User accounts
# - Admin interface

# Key features to implement:
# - Product management
# - Category hierarchy
# - Shopping cart functionality
# - Order processing
# - Payment integration (Stripe)
# - User profiles
# - Order history
# - Admin dashboard
# - Inventory management
```

## Workshop Summary

### Key Concepts Covered
1. **Rails Architecture**: MVC pattern and conventions
2. **Models**: Active Record, validations, associations
3. **Controllers**: RESTful routing, actions, filters
4. **Views**: ERB templates, forms, helpers
5. **Rails Features**: Asset pipeline, testing, deployment

### Next Steps
1. Build real-world applications
2. Learn advanced Rails topics
3. Explore Rails ecosystem (gems, tools)
4. Study performance optimization
5. Learn about API development

### Resources for Continued Learning
- [Rails Guides](https://guides.rubyonrails.org/)
- [Rails API Documentation](https://api.rubyonrails.org/)
- [Rails Tutorial](https://www.railstutorial.org/)
- [GoRails](https://gorails.com/)
- [RailsCasts](https://www.railscasts.com/)

## Conclusion

Congratulations on completing the Ruby on Rails Introduction Workshop! You now have a solid foundation in Rails development. Continue building applications and exploring the Rails ecosystem to become a proficient Rails developer.

Remember: Rails follows conventions over configuration, so embrace the Rails way and build amazing web applications!
