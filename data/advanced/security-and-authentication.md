# Ruby Security and Authentication

## Authentication Systems

### Devise Authentication
```ruby
# Gemfile
gem 'devise', '~> 4.7'
gem 'devise-jwt', '~> 0.3'
gem 'devise-token-auth', '~> 1.1'

# config/initializers/devise.rb
Devise.setup do |config|
  # The secret key used by Devise to sign session cookies
  config.secret_key = ENV['DEVISE_SECRET_KEY'] if ENV['DEVISE_SECRET_KEY'].present?
  
  # Mailer sender
  config.mailer_sender = 'noreply@example.com'
  
  # Configure which ORM to use
  config.orm = :active_record
  
  # Configure the authentication keys
  config.authentication_keys = [:email]
  
  # Configure parameters for authentication
  config.request_keys = []
  
  # Configure sign-out behavior
  config.sign_out_via = :delete
  
  # Password length
  config.password_length = 8..128
  
  # Email regex
  config.email_regexp = /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
  
  # Password encryption
  config.stretches = Rails.env.test? ? 1 : 11
  
  # Pepper for password encryption
  config.pepper = ENV['DEVISE_PEPPER'] if ENV['DEVISE_PEPPER'].present?
  
  # Confirmable module
  config.reconfirmable = true
  
  # Timeoutable module
  config.timeout_in = 30.minutes
  
  # Rememberable module
  config.expire_all_remember_me_on_sign_out = true
  config.extend_remember_period = 2.weeks
  
  # Password reset
  config.reset_password_within = 6.hours
  
  # Scoping for sign out
  config.sign_out_all_scopes = false
  
  # Navigation configuration
  config.navigational_formats = [:html, :turbo_stream]
  
  # Skip session storage for API
  config.skip_session_storage = [:http_auth]
  
  # Strong parameters
  config.clean_up_csrf_token_on_authentication = true
  
  # CSRF protection
  config.csrf_protection = true
  
  # Allow login with email only
  config.case_insensitive_keys = [:email]
  config.strip_whitespace_keys = [:email]
  
  # Authentication strategies
  config.http_authenticatable = [:token]
  config.token_authentication_key = :auth_token
end

# app/models/user.rb
class User < ApplicationRecord
  # Include default devise modules
  devise :database_authenticatable, :registerable, :confirmable,
         :recoverable, :rememberable, :validatable, :timeoutable,
         :trackable, :lockable
  
  # Validations
  validates :name, presence: true, length: { minimum: 2, maximum: 50 }
  validates :role, inclusion: { in: %w[user admin moderator] }
  
  # Callbacks
  after_create :send_welcome_email
  
  # Scopes
  scope :active, -> { where(active: true) }
  scope :admin, -> { where(role: 'admin') }
  
  # Instance methods
  def admin?
    role == 'admin'
  end
  
  def moderator?
    role == 'moderator'
  end
  
  def active_for_authentication?
    super && active?
  end
  
  def send_welcome_email
    UserMailer.welcome_email(self).deliver_later
  end
end

# app/controllers/application_controller.rb
class ApplicationController < ActionController::Base
  protect_from_forgery with: :exception
  
  before_action :configure_permitted_parameters, if: :devise_controller?
  
  protected
  
  def configure_permitted_parameters
    devise_parameter_sanitizer.permit(:sign_up, keys: [:name, :role])
    devise_parameter_sanitizer.permit(:account_update, keys: [:name])
  end
end

# app/controllers/users/sessions_controller.rb
class Users::SessionsController < Devise::SessionsController
  # POST /resource/sign_in
  def create
    super
  end
  
  # DELETE /resource/sign_out
  def destroy
    super
  end
  
  protected
  
  def after_sign_in_path_for(resource)
    if resource.admin?
      admin_dashboard_path
    else
      root_path
    end
  end
  
  def after_sign_out_path_for(resource_or_scope)
    new_user_session_path
  end
end

# app/controllers/users/registrations_controller.rb
class Users::RegistrationsController < Devise::RegistrationsController
  # POST /resource
  def create
    super
  end
  
  # PUT /resource
  def update
    super
  end
  
  # DELETE /resource
  def destroy
    super
  end
  
  protected
  
  def after_sign_up_path_for(resource)
    root_path
  end
  
  def after_update_path_for(resource)
    edit_user_registration_path
  end
end
```

### JWT Authentication
```ruby
# Gemfile
gem 'jwt', '~> 2.2'
gem 'bcrypt', '~> 3.1'

# lib/jwt_token_service.rb
class JwtTokenService
  SECRET_KEY = Rails.application.credentials.jwt_secret || Rails.application.secrets.secret_key_base
  ALGORITHM = 'HS256'
  
  def self.encode(payload, exp = 24.hours.from_now)
    payload[:exp] = exp.to_i
    JWT.encode(payload, SECRET_KEY, ALGORITHM)
  end
  
  def self.decode(token)
    decoded = JWT.decode(token, SECRET_KEY, true, algorithm: ALGORITHM)
    decoded[0]
  rescue JWT::ExpiredSignature
    nil
  rescue JWT::DecodeError
    nil
  end
  
  def self.valid?(token)
    !decode(token).nil?
  end
  
  def self.user_from_token(token)
    decoded = decode(token)
    return nil unless decoded
    
    User.find_by(id: decoded['user_id'])
  end
end

# app/controllers/concerns/jwt_authenticable.rb
module JwtAuthenticable
  extend ActiveSupport::Concern
  
  included do
    before_action :authenticate_request!
  end
  
  private
  
  def authenticate_request!
    header = request.headers['Authorization']
    header = header.split(' ').last if header
    
    @current_user = JwtTokenService.user_from_token(header)
    
    render json: { error: 'Not Authorized' }, status: :unauthorized unless @current_user
  end
  
  def current_user
    @current_user
  end
end

# app/controllers/api/v1/auth_controller.rb
class Api::V1::AuthController < ApplicationController
  def login
    user = User.find_by(email: params[:email])
    
    if user&.valid_password?(params[:password])
      token = JwtTokenService.encode({ user_id: user.id })
      
      render json: {
        token: token,
        user: user.as_json(except: [:password_digest])
      }
    else
      render json: { error: 'Invalid credentials' }, status: :unauthorized
    end
  end
  
  def register
    user = User.new(user_params)
    
    if user.save
      token = JwtTokenService.encode({ user_id: user.id })
      
      render json: {
        token: token,
        user: user.as_json(except: [:password_digest])
      }, status: :created
    else
      render json: { errors: user.errors.full_messages }, status: :unprocessable_entity
    end
  end
  
  def refresh_token
    user = current_user
    
    if user
      token = JwtTokenService.encode({ user_id: user.id })
      render json: { token: token }
    else
      render json: { error: 'Invalid token' }, status: :unauthorized
    end
  end
  
  private
  
  def user_params
    params.require(:user).permit(:name, :email, :password, :password_confirmation)
  end
end

# app/models/user.rb (JWT additions)
class User < ApplicationRecord
  has_secure_password
  
  def generate_jwt_token
    JwtTokenService.encode({ user_id: id })
  end
  
  def self.from_jwt_token(token)
    decoded = JwtTokenService.decode(token)
    return nil unless decoded
    
    find_by(id: decoded['user_id'])
  end
end
```

### OAuth Integration
```ruby
# Gemfile
gem 'omniauth', '~> 2.0'
gem 'omniauth-facebook', '~> 7.0'
gem 'omniauth-google-oauth2', '~> 1.0'
gem 'omniauth-github', '~> 2.0'

# config/initializers/omniauth.rb
Rails.application.config.middleware.use OmniAuth::Builder do
  provider :facebook, ENV['FACEBOOK_APP_ID'], ENV['FACEBOOK_APP_SECRET'],
           scope: 'email,public_profile'
  
  provider :google_oauth2, ENV['GOOGLE_CLIENT_ID'], ENV['GOOGLE_CLIENT_SECRET'],
           scope: 'email,profile'
  
  provider :github, ENV['GITHUB_CLIENT_ID'], ENV['GITHUB_CLIENT_SECRET'],
           scope: 'user:email'
end

# app/models/concerns/omniauthable.rb
module Omniauthable
  extend ActiveSupport::Concern
  
  included do
    def self.from_omniauth(auth_hash)
      user = find_by(email: auth_hash[:info][:email])
      
      if user
        user.update_from_omniauth(auth_hash)
      else
        user = create_from_omniauth(auth_hash)
      end
      
      user
    end
    
    def create_from_omniauth(auth_hash)
      create(
        name: auth_hash[:info][:name],
        email: auth_hash[:info][:email],
        password: Devise.friendly_token[0, 20],
        provider: auth_hash[:provider],
        uid: auth_hash[:uid]
      )
    end
    
    def update_from_omniauth(auth_hash)
      update(
        name: auth_hash[:info][:name],
        provider: auth_hash[:provider],
        uid: auth_hash[:uid]
      )
    end
  end
  
  def update_from_omniauth(auth_hash)
    update(
      name: auth_hash[:info][:name],
      provider: auth_hash[:provider],
      uid: auth_hash[:uid]
    )
  end
end

# app/models/user.rb (OAuth additions)
class User < ApplicationRecord
  include Omniauthable
  
  # Add provider and uid columns
  # t.string :provider
  # t.string :uid
  
  def self.from_omniauth(auth_hash)
    user = find_or_initialize_by(provider: auth_hash[:provider], uid: auth_hash[:uid])
    
    if user.new_record?
      user.email = auth_hash[:info][:email]
      user.name = auth_hash[:info][:name]
      user.password = Devise.friendly_token[0, 20]
      user.save!
    end
    
    user
  end
end

# app/controllers/omniauth_callbacks_controller.rb
class OmniauthCallbacksController < Devise::OmniauthCallbacksController
  def facebook
    @user = User.from_omniauth(request.env['omniauth.auth'])
    
    if @user.persisted?
      sign_in_and_redirect @user, event: :authentication
      set_flash_message(:notice, :success, kind: 'Facebook') if is_navigational_format?
    else
      session['devise.facebook_data'] = request.env['omniauth.auth']
      redirect_to new_user_registration_url
    end
  end
  
  def google_oauth2
    @user = User.from_omniauth(request.env['omniauth.auth'])
    
    if @user.persisted?
      sign_in_and_redirect @user, event: :authentication
      set_flash_message(:notice, :success, kind: 'Google') if is_navigational_format?
    else
      session['devise.google_data'] = request.env['omniauth.auth']
      redirect_to new_user_registration_url
    end
  end
  
  def github
    @user = User.from_omniauth(request.env['omniauth.auth'])
    
    if @user.persisted?
      sign_in_and_redirect @user, event: :authentication
      set_flash_message(:notice, :success, kind: 'GitHub') if is_navigational_format?
    else
      session['devise.github_data'] = request.env['omniauth.auth']
      redirect_to new_user_registration_url
    end
  end
  
  def failure
    redirect_to root_path, alert: 'Authentication failed, please try again.'
  end
end
```

## Authorization Systems

### Role-Based Access Control (RBAC)
```ruby
# app/models/ability.rb
class Ability
  include CanCan::Ability
  
  def initialize(user)
    user ||= User.new # Guest user
    
    if user.admin?
      can :manage, :all
    elsif user.moderator?
      can :read, :all
      can :manage, Post
      can :manage, Comment
    else
      can :read, :all
      can :manage, Post, user_id: user.id
      can :manage, Comment, user_id: user.id
    end
  end
end

# app/models/concerns/authorizable.rb
module Authorizable
  extend ActiveSupport::Concern
  
  included do
    def self.authorize(action, resource)
      current_ability.authorize! action, resource
    end
  end
  
  private
  
  def current_ability
    @current_ability ||= Ability.new(current_user)
  end
end

# app/controllers/application_controller.rb
class ApplicationController < ActionController::Base
  include Authorizable
  rescue_from CanCan::AccessDenied do |exception|
    respond_to do |format|
      format.html { redirect_to root_path, alert: exception.message }
      format.json { render json: { error: exception.message }, status: :forbidden }
    end
  end
  
  private
  
  def authorize!(action, resource)
    current_ability.authorize! action, resource
  end
end

# app/controllers/posts_controller.rb
class PostsController < ApplicationController
  before_action :authenticate_user!
  before_action :set_post, only: [:show, :edit, :update, :destroy]
  before_action :authorize_post!, only: [:edit, :update, :destroy]
  
  # GET /posts
  def index
    @posts = Post.all
    authorize! :read, Post
  end
  
  # GET /posts/1
  def show
    authorize! :read, @post
  end
  
  # GET /posts/new
  def new
    @post = Post.new
    authorize! :create, Post
  end
  
  # POST /posts
  def create
    @post = Post.new(post_params)
    @post.user = current_user
    
    authorize! :create, @post
    
    if @post.save
      redirect_to @post, notice: 'Post was successfully created.'
    else
      render :new
    end
  end
  
  # GET /posts/1/edit
  def edit
    authorize! :update, @post
  end
  
  # PATCH/PUT /posts/1
  def update
    authorize! :update, @post
    
    if @post.update(post_params)
      redirect_to @post, notice: 'Post was successfully updated.'
    else
      render :edit
    end
  end
  
  # DELETE /posts/1
  def destroy
    authorize! :destroy, @post
    @post.destroy
    redirect_to posts_url, notice: 'Post was successfully destroyed.'
  end
  
  private
  
  def set_post
    @post = Post.find(params[:id])
  end
  
  def authorize_post!
    authorize! params[:action].to_sym, @post
  end
  
  def post_params
    params.require(:post).permit(:title, :content, :published)
  end
end

# app/models/role.rb
class Role < ApplicationRecord
  has_many :users
  has_many :permissions
  
  def self.admin
    find_by(name: 'admin')
  end
  
  def self.moderator
    find_by(name: 'moderator')
  end
  
  def self.user
    find_by(name: 'user')
  end
end

# app/models/permission.rb
class Permission < ApplicationRecord
  belongs_to :role
  belongs_to :resource, polymorphic: true
  
  def self.grant(role, action, resource_class)
    create!(
      role: role,
      action: action,
      resource: resource_class
    )
  end
  
  def self.revoke(role, action, resource_class)
    where(
      role: role,
      action: action,
      resource: resource_class
    ).destroy_all
  end
end
```

### Policy-Based Authorization
```ruby
# Gemfile
gem 'pundit', '~> 2.1'

# app/policies/application_policy.rb
class ApplicationPolicy
  attr_reader :user, :record
  
  def initialize(user, record)
    @user = user
    @record = record
  end
  
  def index?
    false
  end
  
  def show?
    scope.where(id: record.id).exists?
  end
  
  def create?
    false
  end
  
  def new?
    create?
  end
  
  def update?
    false
  end
  
  def edit?
    update?
  end
  
  def destroy?
    false
  end
  
  class Scope
    attr_reader :user, :scope
    
    def initialize(user, scope)
      @user = user
      @scope = scope
    end
    
    def resolve
      scope.all
    end
  end
end

# app/policies/post_policy.rb
class PostPolicy < ApplicationPolicy
  def index?
    true
  end
  
  def show?
    true
  end
  
  def create?
    user.present?
  end
  
  def update?
    user.present? && (user.admin? || record.user == user)
  end
  
  def destroy?
    user.present? && (user.admin? || record.user == user)
  end
  
  def publish?
    user.present? && (user.admin? || record.user == user)
  end
  
  class Scope < ApplicationPolicy::Scope
    def resolve
      if user.admin?
        scope.all
      else
        scope.where(published: true).or(scope.where(user: user))
      end
    end
  end
end

# app/policies/user_policy.rb
class UserPolicy < ApplicationPolicy
  def index?
    user.admin?
  end
  
  def show?
    user.admin? || record == user
  end
  
  def create?
    true
  end
  
  def update?
    user.admin? || record == user
  end
  
  def destroy?
    user.admin?
  end
  
  def change_role?
    user.admin?
  end
  
  class Scope < ApplicationPolicy::Scope
    def resolve
      if user.admin?
        scope.all
      else
        scope.where(id: user.id)
      end
    end
  end
end

# app/controllers/application_controller.rb
class ApplicationController < ActionController::Base
  include Pundit
  
  rescue_from Pundit::NotAuthorizedError, with: :user_not_authorized
  
  private
  
  def user_not_authorized
    respond_to do |format|
      format.html { redirect_to root_path, alert: 'You are not authorized to perform this action.' }
      format.json { render json: { error: 'Not authorized' }, status: :forbidden }
    end
  end
end

# app/controllers/posts_controller.rb
class PostsController < ApplicationController
  before_action :authenticate_user!, except: [:index, :show]
  before_action :set_post, only: [:show, :edit, :update, :destroy, :publish]
  
  # GET /posts
  def index
    @posts = policy_scope(Post).includes(:user).recent
  end
  
  # GET /posts/1
  def show
    authorize @post
  end
  
  # GET /posts/new
  def new
    @post = Post.new
    authorize @post
  end
  
  # POST /posts
  def create
    @post = Post.new(post_params)
    @post.user = current_user
    authorize @post
    
    if @post.save
      redirect_to @post, notice: 'Post was successfully created.'
    else
      render :new
    end
  end
  
  # GET /posts/1/edit
  def edit
    authorize @post
  end
  
  # PATCH/PUT /posts/1
  def update
    authorize @post
    
    if @post.update(post_params)
      redirect_to @post, notice: 'Post was successfully updated.'
    else
      render :edit
    end
  end
  
  # DELETE /posts/1
  def destroy
    authorize @post
    @post.destroy
    redirect_to posts_url, notice: 'Post was successfully destroyed.'
  end
  
  # POST /posts/1/publish
  def publish
    authorize @post, :publish?
    
    if @post.update(published: true, published_at: Time.current)
      redirect_to @post, notice: 'Post was successfully published.'
    else
      redirect_to @post, alert: 'Failed to publish post.'
    end
  end
  
  private
  
  def set_post
    @post = Post.find(params[:id])
  end
  
  def post_params
    params.require(:post).permit(:title, :content, :published)
  end
end
```

## Security Best Practices

### Input Validation and Sanitization
```ruby
# app/models/concerns/validatable.rb
module Validatable
  extend ActiveSupport::Concern
  
  included do
    # XSS prevention
    def self.sanitize_html(content)
      ActionController::Base.helpers.sanitize(content)
    end
    
    # SQL injection prevention
    def self.sanitize_sql(query)
      ActiveRecord::Base.sanitize_sql(query)
    end
    
    # Email validation
    def self.valid_email?(email)
      email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
    end
    
    # Phone validation
    def self.valid_phone?(phone)
      phone.match?(/\A[\d\s\-\+\(\)]+\z/)
    end
    
    # URL validation
    def self.valid_url?(url)
      url.match?(/\Ahttps?:\/\/[^\s\/$.?#].[^\s]*\z/i)
    end
  end
end

# app/models/user.rb
class User < ApplicationRecord
  include Validatable
  
  validates :email, presence: true, format: { with: /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i }
  validates :phone, format: { with: /\A[\d\s\-\+\(\)]+\z/, allow_blank: true }
  validates :website, format: { with: /\Ahttps?:\/\/[^\s\/$.?#].[^\s]*\z/i, allow_blank: true }
  
  before_validation :normalize_email
  before_validation :normalize_phone
  
  private
  
  def normalize_email
    self.email = email.downcase.strip if email.present?
  end
  
  def normalize_phone
    self.phone = phone.gsub(/[^\d]/, '') if phone.present?
  end
end

# app/controllers/application_controller.rb
class ApplicationController < ActionController::Base
  protect_from_forgery with: :exception
  
  # Strong parameters
  def sanitize_params(params, permitted_fields)
    params.require(:resource).permit(permitted_fields)
  end
  
  # Content Security Policy
  def set_csp_header
    response.headers['Content-Security-Policy'] = [
      "default-src 'self'",
      "script-src 'self' 'unsafe-inline' https://cdn.example.com",
      "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
      "font-src 'self' https://fonts.gstatic.com",
      "img-src 'self' data: https:",
      "connect-src 'self' https://api.example.com"
    ].join('; ')
  end
  
  # Rate limiting
  def rate_limit!(key, limit: 10, period: 1.hour)
    cache_key = "rate_limit:#{key}"
    
    current_count = Rails.cache.read(cache_key) || 0
    
    if current_count >= limit
      render json: { error: 'Rate limit exceeded' }, status: :too_many_requests
      return false
    end
    
    Rails.cache.write(cache_key, current_count + 1, expires_in: period)
    true
  end
  
  # IP whitelist
  def ip_whitelisted?
    whitelist = ENV['IP_WHITELIST']&.split(',') || []
    whitelist.include?(request.remote_ip) || Rails.env.development?
  end
  
  # Check for suspicious activity
  def suspicious_activity?
    # Check for common attack patterns
    suspicious_params = [
      '<script',
      'javascript:',
      'onload=',
      'onerror=',
      'onclick=',
      '../',
      'union select',
      'drop table',
      'insert into',
      'delete from',
      'update set'
    ]
    
    params.to_s.downcase.match?(/#{suspicious_params.join('|')}/)
  end
  
  before_action :check_suspicious_activity
  
  private
  
  def check_suspicious_activity
    if suspicious_activity?
      Rails.logger.warn "Suspicious activity detected from IP: #{request.remote_ip}"
      render json: { error: 'Invalid request' }, status: :bad_request
    end
  end
end

# app/models/concerns/secure_model.rb
module SecureModel
  extend ActiveSupport::Concern
  
  included do
    # Encrypt sensitive data
    def self.encrypt_sensitive_data(data, key = nil)
      key ||= Rails.application.credentials.encryption_key
      cipher = OpenSSL::Cipher.new('aes-256-gcm')
      cipher.encrypt
      cipher.key = key
      iv = cipher.random_iv
      
      encrypted = cipher.update(data) + cipher.final
      tag = cipher.auth_tag
      
      Base64.encode64(iv + tag + encrypted)
    end
    
    # Decrypt sensitive data
    def self.decrypt_sensitive_data(encrypted_data, key = nil)
      key ||= Rails.application.credentials.encryption_key
      data = Base64.decode64(encrypted_data)
      
      iv = data[0..11]
      tag = data[12..27]
      encrypted = data[28..-1]
      
      cipher = OpenSSL::Cipher.new('aes-256-gcm')
      cipher.decrypt
      cipher.key = key
      cipher.iv = iv
      cipher.auth_tag = tag
      
      cipher.update(encrypted) + cipher.final
    end
    
    # Generate secure random string
    def self.generate_secure_token(length = 32)
      SecureRandom.urlsafe_base64(length)
    end
    
    # Hash password securely
    def self.hash_password(password, salt = nil)
      salt ||= SecureRandom.hex(16)
      hash = BCrypt::Password.create(password + salt, cost: 12)
      [hash, salt]
    end
    
    # Verify password
    def self.verify_password(password, hash, salt)
      BCrypt::Password.new(hash) == (password + salt)
    end
  end
end
```

### Session Security
```ruby
# config/initializers/session_store.rb
Rails.application.config.session_store :cookie_store,
  key: '_app_session',
  secure: Rails.env.production?,
  httponly: true,
  same_site: :strict,
  expire_after: 30.minutes

# app/controllers/concerns/session_security.rb
module SessionSecurity
  extend ActiveSupport::Concern
  
  included do
    before_action :validate_session
    before_action :check_session_timeout
    before_action :require_https_in_production
  end
  
  private
  
  def validate_session
    return unless session[:user_id]
    
    # Check if session is valid
    user = User.find_by(id: session[:user_id])
    
    unless user && user.sessions.exists?(session_id: session.id)
      reset_session
      redirect_to root_path, alert: 'Session expired. Please log in again.'
    end
  end
  
  def check_session_timeout
    return unless session[:last_activity]
    
    if session[:last_activity] < 30.minutes.ago
      reset_session
      redirect_to root_path, alert: 'Session expired due to inactivity.'
    end
  end
  
  def require_https_in_production
    if Rails.env.production? && !request.ssl?
      redirect_to request.url.gsub(/^http:/, 'https:'), status: :moved_permanently
    end
  end
  
  def update_last_activity
    session[:last_activity] = Time.current
  end
end

# app/models/session.rb
class Session < ApplicationRecord
  belongs_to :user
  
  validates :session_id, presence: true, uniqueness: true
  validates :ip_address, presence: true
  validates :user_agent, presence: true
  
  scope :active, -> { where(expires_at: nil).or('expires_at > ?', Time.current) }
  scope :expired, -> { where.not(expires_at: nil).where('expires_at <= ?', Time.current) }
  
  def self.create_from_request(user, request)
    create!(
      user: user,
      session_id: request.session_options[:id],
      ip_address: request.remote_ip,
      user_agent: request.user_agent,
      created_at: Time.current,
      expires_at: 30.minutes.from_now
    )
  end
  
  def self.cleanup_expired
    expired.destroy_all
  end
  
  def expired?
    expires_at && expires_at <= Time.current
  end
  
  def extend_expiry!
    update!(expires_at: 30.minutes.from_now)
  end
end

# app/controllers/application_controller.rb
class ApplicationController < ActionController::Base
  include SessionSecurity
  
  before_action :track_session_activity
  
  private
  
  def track_session_activity
    return unless current_user && session[:session_id]
    
    session_record = Session.find_by(session_id: session[:session_id])
    session_record&.extend_expiry!
  end
end
```

## Password Security

### Password Policies
```ruby
# app/models/concerns/password_policy.rb
module PasswordPolicy
  extend ActiveSupport::Concern
  
  included do
    # Password strength validation
    def validate_password_strength(password)
      errors = []
      
      if password.length < 8
        errors << 'Password must be at least 8 characters long'
      end
      
      if password.length > 128
        errors << 'Password must be less than 128 characters long'
      end
      
      unless password.match?(/[a-z]/)
        errors << 'Password must contain at least one lowercase letter'
      end
      
      unless password.match?(/[A-Z]/)
        errors << 'Password must contain at least one uppercase letter'
      end
      
      unless password.match?(/\d/)
        errors << 'Password must contain at least one digit'
      end
      
      unless password.match?(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/)
        errors << 'Password must contain at least one special character'
      end
      
      # Check for common patterns
      if password.match?(/123456|password|qwerty|admin/i)
        errors << 'Password cannot contain common patterns'
      end
      
      # Check for repeated characters
      if password.match?(/(.)\1{2,}/)
        errors << 'Password cannot contain three or more repeated characters'
      end
      
      errors
    end
    
    # Check if password is compromised
    def password_compromised?(password)
      # Use HaveIBeenPwned API
      sha1_hash = Digest::SHA1.hexdigest(password)
      prefix, suffix = sha1_hash[0..4], sha1_hash[5..-1]
      
      uri = URI("https://api.pwnedpasswords.com/range/#{prefix}")
      response = Net::HTTP.get(uri)
      
      response.split("\r\n").any? do |line|
        hash_suffix, count = line.split(':')
        hash_suffix == suffix && count.to_i > 0
      end
    rescue
      false
    end
    
    # Generate secure password
    def generate_secure_password(length = 16)
      characters = ('a'..'z').to_a + ('A'..'Z').to_a + (0..9).to_a + '!@#$%^&*()_+-=[]{}|;:,.<>?'.split('')
      
      password = Array.new(length) { characters.sample }.join
      
      # Ensure password meets requirements
      until validate_password_strength(password).empty?
        password = Array.new(length) { characters.sample }.join
      end
      
      password
    end
  end
end

# app/models/user.rb
class User < ApplicationRecord
  include PasswordPolicy
  
  has_secure_password
  
  validate :password_strength, if: :password_required?
  validate :password_not_compromised, if: :password_required?
  
  before_save :hash_password
  
  def password_strength
    return unless password.present?
    
    errors = validate_password_strength(password)
    errors.each { |error| self.errors.add(:password, error) }
  end
  
  def password_not_compromised
    return unless password.present?
    
    if password_compromised?(password)
      errors.add(:password, 'This password has been compromised. Please choose a different password.')
    end
  end
  
  def password_required?
    new_record? || password.present?
  end
  
  def hash_password
    return unless password.present?
    
    # Use bcrypt with high cost
    self.password_digest = BCrypt::Password.create(password, cost: 12)
  end
  
  def reset_password_token!
    update(
      reset_password_token: SecureRandom.urlsafe_base64(32),
      reset_password_sent_at: Time.current
    )
  end
  
  def password_reset_token_valid?
    reset_password_sent_at && reset_password_sent_at > 2.hours.ago
  end
  
  def reset_password!(new_password)
    self.password = new_password
    self.reset_password_token = nil
    self.reset_password_sent_at = nil
    
    save!
  end
end

# app/controllers/passwords_controller.rb
class PasswordsController < ApplicationController
  def forgot
    if request.post?
      user = User.find_by(email: params[:email])
      
      if user
        user.reset_password_token!
        PasswordMailer.reset_password_instructions(user).deliver_later
        redirect_to root_path, notice: 'Password reset instructions have been sent to your email.'
      else
        redirect_to root_path, alert: 'Email not found.'
      end
    end
  end
  
  def reset
    @user = User.find_by(reset_password_token: params[:token])
    
    unless @user&.password_reset_token_valid?
      redirect_to root_path, alert: 'Invalid or expired reset token.'
    end
    
    if request.patch?
      if @user.reset_password!(params[:user][:password])
        redirect_to root_path, notice: 'Password has been reset successfully.'
      else
        render :reset
      end
    end
  end
end
```

### Two-Factor Authentication
```ruby
# Gemfile
gem 'rqrcode', '~> 2.0'
gem 'rotp', '~> 6.0'

# app/models/concerns/two_factor_authenticable.rb
module TwoFactorAuthenticable
  extend ActiveSupport::Concern
  
  included do
    # Generate TOTP secret
    def generate_two_factor_secret
      self.two_factor_secret = ROTP::Base32.random_base32
      save!
    end
  
    # Generate QR code
    def two_factor_qr_code
      return unless two_factor_secret.present?
      
      issuer = Rails.application.config.app_name
      label = "#{issuer}:#{email}"
      
      totp = ROTP::TOTP.new(two_factor_secret, issuer: issuer)
      qr_code = RQRCode::QRCode.new(totp.provisioning_uri(label))
      
      qr_code.as_png(size: 200, border_modules: 2).to_data_url
    end
  
    # Verify TOTP code
    def verify_two_factor_code(code)
      return false unless two_factor_secret.present?
      
      totp = ROTP::TOTP.new(two_factor_secret)
      totp.verify(code)
    end
  
    # Generate backup codes
    def generate_backup_codes
      codes = Array.new(10) { SecureRandom.hex(4).upcase }
      self.backup_codes = codes.map { |code| BCrypt::Password.create(code) }
      save!
      codes
    end
  
    # Verify backup code
    def verify_backup_code(code)
      return false unless backup_codes.present?
      
      backup_codes.any? { |hashed_code| BCrypt::Password.new(hashed_code) == code }
    end
  
    # Enable two-factor authentication
    def enable_two_factor!(verification_code)
      return false unless verify_two_factor_code(verification_code)
      
      self.two_factor_enabled = true
      backup_codes = generate_backup_codes
      save!
      
      backup_codes
    end
  
    # Disable two-factor authentication
    def disable_two_factor!
      self.two_factor_enabled = false
      self.two_factor_secret = nil
      self.backup_codes = nil
      save!
    end
  end
end

# app/models/user.rb
class User < ApplicationRecord
  include TwoFactorAuthenticable
  
  # Add columns for 2FA
  # t.string :two_factor_secret
  # t.boolean :two_factor_enabled, default: false
  # t.text :backup_codes
end

# app/controllers/two_factor_authentication_controller.rb
class TwoFactorAuthenticationController < ApplicationController
  before_action :authenticate_user!
  
  def show
    if current_user.two_factor_enabled?
      redirect_to two_factor_disable_path
    else
      @qr_code = current_user.two_factor_qr_code if current_user.two_factor_secret.present?
    end
  end
  
  def create
    current_user.generate_two_factor_secret if current_user.two_factor_secret.blank?
    @qr_code = current_user.two_factor_qr_code
  end
  
  def verify
    backup_codes = current_user.enable_two_factor!(params[:verification_code])
    
    if backup_codes
      session[:backup_codes] = backup_codes
      redirect_to two_factor_complete_path
    else
      flash.now[:alert] = 'Invalid verification code'
      render :show
    end
  end
  
  def complete
    @backup_codes = session.delete(:backup_codes) || []
  end
  
  def disable
    if current_user.verify_two_factor_code(params[:verification_code])
      current_user.disable_two_factor!
      redirect_to two_factor_authentication_path, notice: 'Two-factor authentication has been disabled.'
    else
      flash.now[:alert] = 'Invalid verification code'
      render :disable
    end
  end
end

# app/controllers/sessions_controller.rb
class SessionsController < ApplicationController
  def create
    user = User.find_by(email: params[:email])
    
    if user&.valid_password?(params[:password])
      if user.two_factor_enabled?
        session[:pre_auth_user_id] = user.id
        redirect_to two_factor_challenge_path
      else
        sign_in(user)
        redirect_to root_path, notice: 'Signed in successfully.'
      end
    else
      flash.now[:alert] = 'Invalid email or password'
      render :new
    end
  end
  
  def two_factor_challenge
    @user = User.find(session[:pre_auth_user_id])
  end
  
  def two_factor_verify
    user = User.find(session[:pre_auth_user_id])
    
    if user.verify_two_factor_code(params[:code]) || user.verify_backup_code(params[:code])
      session.delete(:pre_auth_user_id)
      sign_in(user)
      redirect_to root_path, notice: 'Signed in successfully.'
    else
      flash.now[:alert] = 'Invalid code'
      render :two_factor_challenge
    end
  end
end
```

## Security Testing

### Security Testing with RSpec
```ruby
# spec/security/authentication_spec.rb
RSpec.describe 'Authentication Security', type: :request do
  let(:user) { create(:user, password: 'SecurePassword123!') }
  
  describe 'POST /login' do
    context 'with valid credentials' do
      it 'authenticates user successfully' do
        post '/login', params: { email: user.email, password: 'SecurePassword123!' }
        
        expect(response).to have_http_status(302)
        expect(session[:user_id]).to eq(user.id)
      end
    end
    
    context 'with invalid credentials' do
      it 'does not authenticate user' do
        post '/login', params: { email: user.email, password: 'wrongpassword' }
        
        expect(response).to have_http_status(200)
        expect(session[:user_id]).to be_nil
        expect(response.body).to include('Invalid email or password')
      end
    end
    
    context 'with SQL injection attempt' do
      it 'prevents SQL injection' do
        post '/login', params: { email: "' OR '1'='1", password: 'password' }
        
        expect(response).to have_http_status(200)
        expect(session[:user_id]).to be_nil
      end
    end
    
    context 'with XSS attempt' do
      it 'prevents XSS' do
        post '/login', params: { email: '<script>alert("xss")</script>', password: 'password' }
        
        expect(response).to have_http_status(200)
        expect(response.body).not_to include('<script>')
      end
    end
  end
  
  describe 'Rate limiting' do
    it 'limits login attempts' do
      5.times do
        post '/login', params: { email: user.email, password: 'wrongpassword' }
      end
      
      post '/login', params: { email: user.email, password: 'wrongpassword' }
      
      expect(response).to have_http_status(429)
      expect(response.body).to include('Rate limit exceeded')
    end
  end
end

# spec/security/authorization_spec.rb
RSpec.describe 'Authorization Security', type: :request do
  let(:admin) { create(:user, :admin) }
  let(:moderator) { create(:user, :moderator) }
  let(:regular_user) { create(:user) }
  let(:post) { create(:post, user: regular_user) }
  
  describe 'POST /posts' do
    context 'as admin' do
      it 'allows creating posts' do
        sign_in admin
        post '/posts', params: { post: { title: 'Test', content: 'Content' } }
        
        expect(response).to have_http_status(302)
      end
    end
    
    context 'as regular user' do
      it 'allows creating posts' do
        sign_in regular_user
        post '/posts', params: { post: { title: 'Test', content: 'Content' } }
        
        expect(response).to have_http_status(302)
      end
    end
  end
  
  describe 'PATCH /posts/:id' do
    context 'as admin' do
      it 'allows editing any post' do
        sign_in admin
        patch "/posts/#{post.id}", params: { post: { title: 'Updated' } }
        
        expect(response).to have_http_status(302)
      end
    end
    
    context 'as post owner' do
      it 'allows editing own post' do
        sign_in regular_user
        patch "/posts/#{post.id}", params: { post: { title: 'Updated' } }
        
        expect(response).to have_http_status(302)
      end
    end
    
    context 'as other user' do
      let(:other_user) { create(:user) }
      
      it 'prevents editing other user\'s post' do
        sign_in other_user
        patch "/posts/#{post.id}", params: { post: { title: 'Updated' } }
        
        expect(response).to have_http_status(403)
      end
    end
  end
end

# spec/security/password_spec.rb
RSpec.describe 'Password Security', type: :model do
  describe User do
    let(:user) { User.new(email: 'test@example.com') }
    
    describe 'password validation' do
      it 'rejects weak passwords' do
        user.password = 'password'
        
        expect(user).not_to be_valid
        expect(user.errors[:password]).to include('Password must contain at least one uppercase letter')
        expect(user.errors[:password]).to include('Password must contain at least one digit')
        expect(user.errors[:password]).to include('Password must contain at least one special character')
      end
      
      it 'accepts strong passwords' do
        user.password = 'SecurePassword123!'
        
        expect(user).to be_valid
      end
      
      it 'rejects compromised passwords' do
        allow_any_instance_of(User).to receive(:password_compromised?).and_return(true)
        
        user.password = 'SecurePassword123!'
        
        expect(user).not_to be_valid
        expect(user.errors[:password]).to include('This password has been compromised')
      end
    end
    
    describe 'password hashing' do
      it 'uses bcrypt with high cost' do
        user.password = 'SecurePassword123!'
        user.save!
        
        password = BCrypt::Password.new(user.password_digest)
        expect(password.cost).to eq(12)
      end
    end
  end
end

# spec/security/session_spec.rb
RSpec.describe 'Session Security', type: :request do
  let(:user) { create(:user) }
  
  describe 'session timeout' do
    it 'expires session after inactivity' do
      sign_in user
      session[:last_activity] = 31.minutes.ago
      
      get '/dashboard'
      
      expect(response).to redirect_to(root_path)
      expect(flash[:alert]).to include('Session expired')
    end
  end
  
  describe 'session validation' do
    it 'invalidates session when user is deleted' do
      sign_in user
      user.destroy!
      
      get '/dashboard'
      
      expect(response).to redirect_to(root_path)
      expect(flash[:alert]).to include('Session expired')
    end
  end
  
  describe 'HTTPS requirement' do
    it 'redirects to HTTPS in production' do
      allow(Rails.env).to receive(:production?).and_return(true)
      
      get '/dashboard', headers: { 'HTTPS' => 'off' }
      
      expect(response).to have_http_status(301)
      expect(response.location).to start_with('https://')
    end
  end
end
```

### Security Scanning Tools
```ruby
# spec/support/security_scanner.rb
class SecurityScanner
  def self.scan_application
    results = {
      sql_injection: scan_sql_injection,
      xss: scan_xss,
      csrf: scan_csrf,
      authentication: scan_authentication,
      authorization: scan_authorization,
      session_security: scan_session_security,
      input_validation: scan_input_validation,
      encryption: scan_encryption
    }
    
    results
  end
  
  private
  
  def self.scan_sql_injection
    vulnerabilities = []
    
    # Check for raw SQL without sanitization
    Dir.glob('app/**/*.rb').each do |file|
      content = File.read(file)
      
      if content.match(/execute\(["'].*?\$\{.*?\}.*?["']/)
        vulnerabilities << { file: file, issue: 'Potential SQL injection via string interpolation' }
      end
      
      if content.match(/where\(["'].*?\$\{.*?\}.*?["']/)
        vulnerabilities << { file: file, issue: 'Potential SQL injection in where clause' }
      end
    end
    
    vulnerabilities
  end
  
  def self.scan_xss
    vulnerabilities = []
    
    Dir.glob('app/**/*.rb').each do |file|
      content = File.read(file)
      
      if content.match(/render.*html:.*params\[/)
        vulnerabilities << { file: file, issue: 'Potential XSS via unescaped parameter' }
      end
      
      if content.match(/content_for.*params\[/)
        vulnerabilities << { file: file, issue: 'Potential XSS via unescaped content' }
      end
    end
    
    vulnerabilities
  end
  
  def self.scan_csrf
    vulnerabilities = []
    
    # Check for CSRF protection
    unless File.read('app/controllers/application_controller.rb').match?(/protect_from_forgery/)
      vulnerabilities << { file: 'app/controllers/application_controller.rb', issue: 'CSRF protection not enabled' }
    end
    
    vulnerabilities
  end
  
  def self.scan_authentication
    vulnerabilities = []
    
    Dir.glob('app/controllers/**/*.rb').each do |file|
      content = File.read(file)
      
      if content.match(/def.*without.*authenticate_user/)
        vulnerabilities << { file: file, issue: 'Action without authentication requirement' }
      end
    end
    
    vulnerabilities
  end
  
  def self.scan_authorization
    vulnerabilities = []
    
    Dir.glob('app/controllers/**/*.rb').each do |file|
      content = File.read(file)
      
      if content.match(/def.*update.*without.*authorization/)
        vulnerabilities << { file: file, issue: 'Update action without authorization check' }
      end
      
      if content.match(/def.*destroy.*without.*authorization/)
        vulnerabilities << { file: file, issue: 'Destroy action without authorization check' }
      end
    end
    
    vulnerabilities
  end
  
  def self.scan_session_security
    vulnerabilities = []
    
    # Check session configuration
    session_config = File.read('config/initializers/session_store.rb')
    
    unless session_config.match?(/secure: true/)
      vulnerabilities << { file: 'config/initializers/session_store.rb', issue: 'Session not configured for HTTPS' }
    end
    
    unless session_config.match?(/httponly: true/)
      vulnerabilities << { file: 'config/initializers/session_store.rb', issue: 'Session not configured as HTTP-only' }
    end
    
    unless session_config.match?(/same_site: :strict/)
      vulnerabilities << { file: 'config/initializers/session_store.rb', issue: 'Session not configured with strict same-site policy' }
    end
    
    vulnerabilities
  end
  
  def self.scan_input_validation
    vulnerabilities = []
    
    Dir.glob('app/models/**/*.rb').each do |file|
      content = File.read(file)
      
      if content.match(/params\[:.*\]/) && !content.match(/permit\(/)
        vulnerabilities << { file: file, issue: 'Parameters used without strong parameters' }
      end
    end
    
    vulnerabilities
  end
  
  def self.scan_encryption
    vulnerabilities = []
    
    # Check for hardcoded secrets
    Dir.glob('app/**/*.rb').each do |file|
      content = File.read(file)
      
      if content.match(/['"]\w*_secret['"]\s*=\s*['"][^'"]+['"]/)
        vulnerabilities << { file: file, issue: 'Hardcoded secret found' }
      end
      
      if content.match(/['"]\w*_key['"]\s*=\s*['"][^'"]+['"]/)
        vulnerabilities << { file: file, issue: 'Hardcoded key found' }
      end
    end
    
    vulnerabilities
  end
end

# spec/security/security_scan_spec.rb
RSpec.describe 'Security Scan', type: :system do
  it 'passes security scan' do
    results = SecurityScanner.scan_application
    
    results.each do |category, vulnerabilities|
      if vulnerabilities.any?
        puts "#{category.to_s.humanize} vulnerabilities:"
        vulnerabilities.each do |vuln|
          puts "  #{vuln[:file]}: #{vuln[:issue]}"
        end
      end
    end
    
    total_vulnerabilities = results.values.flatten.size
    
    expect(total_vulnerabilities).to eq(0), "Found #{total_vulnerabilities} security vulnerabilities"
  end
end
```

## Best Practices

### Security Checklist
```ruby
# app/services/security_audit_service.rb
class SecurityAuditService
  def self.perform_audit
    audit_results = {
      authentication: audit_authentication,
      authorization: audit_authorization,
      input_validation: audit_input_validation,
      session_security: audit_session_security,
      data_protection: audit_data_protection,
      infrastructure: audit_infrastructure,
      logging: audit_logging
    }
    
    generate_report(audit_results)
  end
  
  private
  
  def self.audit_authentication
    {
      password_policy: check_password_policy,
      two_factor_auth: check_two_factor_auth,
      session_management: check_session_management,
      rate_limiting: check_rate_limiting
    }
  end
  
  def self.audit_authorization
    {
      rbac_implementation: check_rbac_implementation,
      policy_enforcement: check_policy_enforcement,
      privilege_escalation: check_privilege_escalation
    }
  end
  
  def self.audit_input_validation
    {
      parameter_sanitization: check_parameter_sanitization,
      sql_injection_protection: check_sql_injection_protection,
      xss_protection: check_xss_protection,
      csrf_protection: check_csrf_protection
    }
  end
  
  def self.audit_session_security
    {
      secure_cookies: check_secure_cookies,
      session_timeout: check_session_timeout,
      session_fixation: check_session_fixation
    }
  end
  
  def self.audit_data_protection
    {
      encryption_at_rest: check_encryption_at_rest,
      encryption_in_transit: check_encryption_in_transit,
      data_backup_security: check_data_backup_security
    }
  end
  
  def self.audit_infrastructure
    {
      ssl_configuration: check_ssl_configuration,
      firewall_rules: check_firewall_rules,
      network_security: check_network_security
    }
  end
  
  def self.audit_logging
    {
      security_logging: check_security_logging,
      log_retention: check_log_retention,
      log_protection: check_log_protection
    }
  end
  
  def self.generate_report(results)
    report = "# Security Audit Report\n\n"
    
    results.each do |category, checks|
      report += "## #{category.to_s.humanize}\n\n"
      
      checks.each do |check_name, result|
        status = result[:status] == :pass ? '✅' : '❌'
        report += "#{status} **#{check_name.to_s.humanize}**: #{result[:message]}\n"
        
        if result[:recommendations]
          result[:recommendations].each do |rec|
            report += "   - #{rec}\n"
          end
        end
      end
      
      report += "\n"
    end
    
    report
  end
end
```

## Common Pitfalls

### Security Pitfalls
```ruby
# Pitfall: Not using strong parameters
# Bad: Using params directly
def create
  @user = User.new(params[:user])
  @user.save
end

# Solution: Use strong parameters
def create
  @user = User.new(user_params)
  @user.save
end

private

def user_params
  params.require(:user).permit(:name, :email, :password)
end

# Pitfall: Not validating input
# Bad: No input validation
def search
  @results = Post.where("title LIKE '%#{params[:query]}%'")
end

# Solution: Validate and sanitize input
def search
  return if params[:query].blank?
  
  @results = Post.where("title ILIKE ?", "%#{params[:query]}%")
end

# Pitfall: Not using HTTPS
# Bad: No HTTPS enforcement
class ApplicationController < ActionController::Base
  # No HTTPS enforcement
end

# Solution: Enforce HTTPS in production
class ApplicationController < ActionController::Base
  before_action :force_https_in_production
  
  private
  
  def force_https_in_production
    if Rails.env.production? && !request.ssl?
      redirect_to request.url.gsub(/^http:/, 'https:'), status: :moved_permanently
    end
  end
end

# Pitfall: Weak password policies
# Bad: Simple password validation
validates :password, length: { minimum: 6 }

# Solution: Strong password validation
validates :password, length: { minimum: 8, maximum: 128 }
validate :password_strength

def password_strength
  errors = []
  
  unless password.match?(/[a-z]/)
    errors << 'Password must contain at least one lowercase letter'
  end
  
  unless password.match?(/[A-Z]/)
    errors << 'Password must contain at least one uppercase letter'
  end
  
  unless password.match?(/\d/)
    errors << 'Password must contain at least one digit'
  end
  
  unless password.match?(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/)
    errors << 'Password must contain at least one special character'
  end
  
  errors.each { |error| self.errors.add(:password, error) }
end

# Pitfall: Not implementing rate limiting
# Bad: No rate limiting
def login
  # Login logic without rate limiting
end

# Solution: Implement rate limiting
def login
  return unless rate_limit!("login:#{request.remote_ip}", limit: 5, period: 1.hour)
  
  # Login logic
end

# Pitfall: Storing sensitive data in plain text
# Bad: Storing passwords in plain text
def encrypt_password(password)
  # No encryption
  password
end

# Solution: Use proper encryption
def encrypt_password(password)
  BCrypt::Password.create(password, cost: 12)
end
```

## Summary

Ruby security and authentication provides:

**Authentication Systems:**
- Devise authentication framework
- JWT token-based authentication
- OAuth integration (Facebook, Google, GitHub)
- Two-factor authentication (TOTP, backup codes)

**Authorization Systems:**
- Role-based access control (RBAC)
- Policy-based authorization (Pundit)
- Custom authorization policies
- Permission management

**Security Best Practices:**
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection
- Session security

**Password Security:**
- Password strength validation
- Compromised password detection
- Secure password hashing
- Password reset functionality

**Security Testing:**
- Authentication testing
- Authorization testing
- Security scanning tools
- Vulnerability assessment

**Security Auditing:**
- Comprehensive security checklist
- Automated security audits
- Security reporting
- Best practices enforcement

**Common Pitfalls:**
- Missing input validation
- Weak password policies
- No rate limiting
- Insecure session management
- Missing HTTPS enforcement

Ruby provides robust security frameworks and tools for building secure applications when following established security best practices and implementing comprehensive authentication and authorization systems.
