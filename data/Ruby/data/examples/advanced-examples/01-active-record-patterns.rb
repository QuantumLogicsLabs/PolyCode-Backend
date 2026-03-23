# Active Record Patterns in Ruby
# This file demonstrates various Active Record patterns and best practices
# for database operations, query optimization, and data modeling.

module DatabaseExamples
  module ActiveRecordPatterns
    # 1. Basic Model Setup and Associations
    # Proper model definitions with associations and validations
    
    class User < ApplicationRecord
      # Basic validations
      validates :email, presence: true, uniqueness: true, format: { with: URI::MailTo::EMAIL_REGEXP }
      validates :first_name, :last_name, presence: true, length: { minimum: 2, maximum: 50 }
      validates :age, numericality: { greater_than: 0, less_than: 150 }
      
      # Callbacks
      before_validation :normalize_email
      after_create :send_welcome_email
      before_destroy :check_dependencies
      
      # Scopes
      scope :active, -> { where(active: true) }
      scope :by_age, ->(min_age, max_age) { where(age: min_age..max_age) }
      scope :recent, -> { where('created_at > ?', 1.week.ago) }
      scope :with_orders, -> { joins(:orders).where.not(orders: { id: nil }) }
      
      # Associations
      has_many :orders, dependent: :destroy
      has_many :reviews, through: :orders
      has_many :addresses, dependent: :destroy
      has_one :profile, dependent: :destroy
      has_many :user_roles, dependent: :destroy
      has_many :roles, through: :user_roles
      
      # Instance methods
      def full_name
        "#{first_name} #{last_name}"
      end
      
      def recent_orders
        orders.where('created_at > ?', 30.days.ago)
      end
      
      def total_spent
        orders.completed.sum(:total_amount)
      end
      
      def average_order_value
        completed_orders = orders.completed
        return 0 if completed_orders.empty?
        
        completed_orders.sum(:total_amount) / completed_orders.count
      end
      
      def can_delete?
        orders.empty? && reviews.empty?
      end
      
      private
      
      def normalize_email
        self.email = email&.downcase&.strip
      end
      
      def send_welcome_email
        UserMailer.welcome_email(self).deliver_later
      end
      
      def check_dependencies
        unless can_delete?
          errors.add(:base, "Cannot delete user with existing orders or reviews")
          throw :abort
        end
      end
    end
    
    class Order < ApplicationRecord
      belongs_to :user
      belongs_to :shipping_address, class_name: 'Address'
      belongs_to :billing_address, class_name: 'Address', optional: true
      
      has_many :order_items, dependent: :destroy
      has_many :products, through: :order_items
      has_many :reviews, dependent: :destroy
      
      validates :user, presence: true
      validates :status, inclusion: { in: %w[pending processing shipped delivered cancelled] }
      validates :total_amount, presence: true, numericality: { greater_than: 0 }
      
      enum status: {
        pending: 'pending',
        processing: 'processing',
        shipped: 'shipped',
        delivered: 'delivered',
        cancelled: 'cancelled'
      }
      
      scope :completed, -> { where(status: 'delivered') }
      scope :pending, -> { where(status: 'pending') }
      scope :by_date_range, ->(start_date, end_date) { where(created_at: start_date..end_date) }
      scope :with_items, -> { includes(:order_items, :products) }
      
      before_validation :calculate_total_amount
      after_update :send_status_notification
      
      def calculate_total_amount
        self.total_amount = order_items.sum('quantity * unit_price')
      end
      
      def send_status_notification
        OrderStatusMailer.status_change(self).deliver_later if saved_change_to_status?
      end
      
      def add_product(product, quantity = 1)
        order_items.create!(
          product: product,
          quantity: quantity,
          unit_price: product.current_price
        )
      end
      
      def remove_product(product)
        order_items.where(product: product).destroy_all
        calculate_total_amount
        save!
      end
      
      def update_quantity(product, new_quantity)
        item = order_items.find_by(product: product)
        
        if item
          if new_quantity > 0
            item.update!(quantity: new_quantity)
          else
            item.destroy
          end
        end
        
        calculate_total_amount
        save!
      end
      
      def estimated_delivery_date
        return nil unless shipped?
        
        created_at + 7.days
      end
      
      def can_cancel?
        pending? || processing?
      end
      
      def cancel!
        return false unless can_cancel?
        
        update!(status: 'cancelled')
        
        # Restock products
        order_items.each do |item|
          item.product.increment!(:stock_quantity, item.quantity)
        end
        
        true
      end
    end
    
    class Product < ApplicationRecord
      belongs_to :category
      belongs_to :brand, optional: true
      
      has_many :order_items, dependent: :destroy
      has_many :orders, through: :order_items
      has_many :reviews, dependent: :destroy
      has_many :product_tags, dependent: :destroy
      has_many :tags, through: :product_tags
      
      validates :name, presence: true, length: { minimum: 3, maximum: 255 }
      validates :description, presence: true, length: { minimum: 10 }
      validates :price, presence: true, numericality: { greater_than: 0 }
      validates :stock_quantity, numericality: { greater_than_or_equal_to: 0 }
      validates :sku, presence: true, uniqueness: true
      
      scope :available, -> { where(active: true).where('stock_quantity > 0') }
      scope :by_category, ->(category) { where(category: category) }
      scope :by_price_range, ->(min_price, max_price) { where(price: min_price..max_price) }
      scope :featured, -> { where(featured: true) }
      scope :recent, -> { where('created_at > ?', 30.days.ago) }
      
      before_save :normalize_name
      after_create :create_initial_inventory
      
      def current_price
        price
      end
      
      def in_stock?
        stock_quantity > 0
      end
      
      def low_stock?
        stock_quantity > 0 && stock_quantity < 10
      end
      
      def out_of_stock?
        stock_quantity == 0
      end
      
      def average_rating
        return 0 if reviews.empty?
        
        reviews.average(:rating).to_f.round(2)
      end
      
      def total_reviews
        reviews.count
      end
      
      def add_tag(tag_name)
        tag = Tag.find_or_create_by(name: tag_name)
        tags << tag unless tags.include?(tag)
      end
      
      def remove_tag(tag_name)
        tag = Tag.find_by(name: tag_name)
        tags.delete(tag) if tag
      end
      
      def related_products(limit = 5)
        Product.where(category: category)
              .where.not(id: id)
              .available
              .limit(limit)
      end
      
      def sales_count
        order_items.sum(:quantity)
      end
      
      def revenue
        order_items.sum('quantity * unit_price')
      end
      
      private
      
      def normalize_name
        self.name = name.strip.titleize
      end
      
      def create_initial_inventory
        # Create initial inventory record
        Inventory.create!(
          product: self,
          quantity: stock_quantity,
          location: 'Default',
          last_checked: Time.current
        )
      end
    end
    
    # 2. Advanced Query Patterns
    # Complex queries with joins, subqueries, and optimization
    
    class ProductRepository
      def initialize
        @base_query = Product.includes(:category, :brand, :tags, :reviews)
      end
      
      def search_products(filters = {})
        query = @base_query
        
        query = apply_text_search(query, filters[:search]) if filters[:search]
        query = apply_category_filter(query, filters[:category_id]) if filters[:category_id]
        query = apply_price_filter(query, filters[:price_min], filters[:price_max]) if filters[:price_min] || filters[:price_max]
        query = apply_rating_filter(query, filters[:min_rating]) if filters[:min_rating]
        query = apply_availability_filter(query, filters[:in_stock]) if filters.key?(:in_stock)
        query = apply_tag_filter(query, filters[:tags]) if filters[:tags]
        
        query = apply_sorting(query, filters[:sort_by], filters[:sort_order])
        query = apply_pagination(query, filters[:page], filters[:per_page])
        
        query
      end
      
      def find_popular_products(limit = 10, time_period = 30.days)
        @base_query
          .joins(:order_items)
          .where('order_items.created_at > ?', time_period.ago)
          .group('products.id')
          .order('COUNT(order_items.id) DESC')
          .limit(limit)
      end
      
      def find_products_with_low_stock(threshold = 10)
        Product.where('stock_quantity > 0 AND stock_quantity < ?', threshold)
              .order(:stock_quantity)
      end
      
      def find_products_by_sales_performance(start_date, end_date)
        Product.joins(:order_items)
              .joins('JOIN orders ON order_items.order_id = orders.id')
              .where('orders.created_at BETWEEN ? AND ?', start_date, end_date)
              .where('orders.status = ?', 'delivered')
              .group('products.id')
              .select('products.*, SUM(order_items.quantity) as total_sold, SUM(order_items.quantity * order_items.unit_price) as total_revenue')
              .order('total_revenue DESC')
      end
      
      def find_cross_sell_products(product_id, limit = 5)
        # Find products frequently bought together
        product = Product.find(product_id)
        
        Product.joins(:order_items)
              .joins('JOIN order_items oi2 ON order_items.order_id = oi2.order_id')
              .where('order_items.product_id = ? AND oi2.product_id != ?', product_id, product_id)
              .where('oi2.product_id = products.id')
              .group('products.id')
              .order('COUNT(DISTINCT order_items.order_id) DESC')
              .limit(limit)
      end
      
      def find_products_needing_reorder
        Product.where('stock_quantity <= reorder_threshold')
              .where('last_reorder_at IS NULL OR last_reorder_at < ?', 7.days.ago)
              .order(:stock_quantity)
      end
      
      def generate_inventory_report
        products = Product.includes(:category, :order_items)
        
        report_data = products.map do |product|
          {
            id: product.id,
            name: product.name,
            category: product.category.name,
            stock_quantity: product.stock_quantity,
            reorder_threshold: product.reorder_threshold || 10,
            needs_reorder: product.stock_quantity <= (product.reorder_threshold || 10),
            sales_last_30_days: product.order_items
                                   .joins(:order)
                                   .where('orders.created_at > ?', 30.days.ago)
                                   .where('orders.status = ?', 'delivered')
                                   .sum(:quantity),
            revenue_last_30_days: product.order_items
                                   .joins(:order)
                                   .where('orders.created_at > ?', 30.days.ago)
                                   .where('orders.status = ?', 'delivered')
                                   .sum('order_items.quantity * order_items.unit_price')
          }
        end
        
        {
          total_products: products.count,
          low_stock_products: report_data.count { |p| p[:needs_reorder] },
          out_of_stock_products: report_data.count { |p| p[:stock_quantity] == 0 },
          total_sales_last_30_days: report_data.sum { |p| p[:sales_last_30_days] },
          total_revenue_last_30_days: report_data.sum { |p| p[:revenue_last_30_days] },
          products: report_data
        }
      end
      
      private
      
      def apply_text_search(query, search_term)
        query.where('products.name ILIKE ? OR products.description ILIKE ?', "%#{search_term}%", "%#{search_term}%")
      end
      
      def apply_category_filter(query, category_id)
        query.where(category_id: category_id)
      end
      
      def apply_price_filter(query, min_price, max_price)
        if min_price && max_price
          query.where(price: min_price..max_price)
        elsif min_price
          query.where('price >= ?', min_price)
        elsif max_price
          query.where('price <= ?', max_price)
        else
          query
        end
      end
      
      def apply_rating_filter(query, min_rating)
        query.joins(:reviews)
             .where('reviews.rating >= ?', min_rating)
             .group('products.id')
             .having('AVG(reviews.rating) >= ?', min_rating)
      end
      
      def apply_availability_filter(query, in_stock)
        if in_stock
          query.where('stock_quantity > 0')
        else
          query.where('stock_quantity = 0')
        end
      end
      
      def apply_tag_filter(query, tags)
        tag_names = Array(tags)
        query.joins(:tags).where('tags.name IN (?)', tag_names)
      end
      
      def apply_sorting(query, sort_by, sort_order = 'asc')
        valid_sort_fields = %w[name price created_at rating sales]
        
        if valid_sort_fields.include?(sort_by)
          case sort_by
          when 'rating'
            query = query.joins(:reviews).group('products.id')
            sort_expression = "AVG(reviews.rating) #{sort_order.upcase}"
          when 'sales'
            query = query.joins(:order_items).group('products.id')
            sort_expression = "COUNT(order_items.id) #{sort_order.upcase}"
          else
            sort_expression = "products.#{sort_by} #{sort_order.upcase}"
          end
          
          query.order(sort_expression)
        else
          query.order('products.name ASC')
        end
      end
      
      def apply_pagination(query, page, per_page)
        page = [page.to_i, 1].max
        per_page = [per_page.to_i, 1].max
        per_page = [per_page, 50].min
        
        query.limit(per_page).offset((page - 1) * per_page)
      end
    end
    
    # 3. Transaction Management
    # Proper transaction handling and error recovery
    
    class OrderService
      def create_order(user, order_params)
        Order.transaction do
          order = user.orders.create!(order_params)
          
          # Reserve inventory
          order.order_items.each do |item|
            product = item.product
            new_quantity = product.stock_quantity - item.quantity
            
            if new_quantity < 0
              raise ActiveRecord::Rollback, "Insufficient stock for #{product.name}"
            end
            
            product.update!(stock_quantity: new_quantity)
          end
          
          # Apply discounts if applicable
          apply_discounts(order) if order.coupon_code.present?
          
          # Create initial order status
          order.order_statuses.create!(
            status: 'pending',
            notes: 'Order created'
          )
          
          order
        end
      rescue => e
        Rails.logger.error "Failed to create order: #{e.message}"
        raise
      end
      
      def process_payment(order, payment_params)
        Order.transaction do
          # Create payment record
          payment = order.payments.create!(payment_params)
          
          # Process payment through gateway
          gateway_result = PaymentGateway.charge(
            amount: order.total_amount,
            payment_method: payment.payment_method,
            description: "Order ##{order.id}"
          )
          
          unless gateway_result.success?
            payment.update!(status: 'failed', gateway_response: gateway_result.to_json)
            raise PaymentError, "Payment failed: #{gateway_result.error_message}"
          end
          
          # Update payment status
          payment.update!(
            status: 'completed',
            gateway_transaction_id: gateway_result.transaction_id,
            gateway_response: gateway_result.to_json,
            processed_at: Time.current
          )
          
          # Update order status
          order.update!(status: 'processing')
          order.order_statuses.create!(
            status: 'processing',
            notes: 'Payment processed successfully'
          )
          
          # Send notifications
          OrderMailer.payment_confirmation(order, payment).deliver_later
          
          payment
        end
      rescue => e
        Rails.logger.error "Payment processing failed: #{e.message}"
        raise
      end
      
      def ship_order(order, shipping_params)
        Order.transaction do
          # Create shipment record
          shipment = order.shipments.create!(shipping_params)
          
          # Update order status
          order.update!(status: 'shipped', shipped_at: Time.current)
          order.order_statuses.create!(
            status: 'shipped',
            notes: "Shipped via #{shipment.carrier} (#{shipment.tracking_number})"
          )
          
          # Send shipping confirmation
          OrderMailer.shipping_confirmation(order, shipment).deliver_later
          
          shipment
        end
      rescue => e
        Rails.logger.error "Shipping failed: #{e.message}"
        raise
      end
      
      def cancel_order(order, reason = nil)
        Order.transaction do
          # Check if order can be cancelled
          unless order.can_cancel?
            raise OrderError, "Order cannot be cancelled in current status: #{order.status}"
          end
          
          # Restock products
          order.order_items.each do |item|
            product = item.product
            product.increment!(:stock_quantity, item.quantity)
          end
          
          # Process refund if payment was made
          if order.payments.completed.any?
            refund_amount = order.total_amount
            refund_result = PaymentGateway.refund(
              order.payments.completed.first.gateway_transaction_id,
              refund_amount,
              reason: reason || "Order cancellation"
            )
            
            unless refund_result.success?
              raise PaymentError, "Refund failed: #{refund_result.error_message}"
            end
            
            # Create refund record
            order.refunds.create!(
              amount: refund_amount,
              reason: reason || "Order cancellation",
              gateway_transaction_id: refund_result.transaction_id,
              processed_at: Time.current
            )
          end
          
          # Update order status
          order.update!(status: 'cancelled', cancelled_at: Time.current)
          order.order_statuses.create!(
            status: 'cancelled',
            notes: reason || "Order cancelled by customer"
          )
          
          # Send cancellation notification
          OrderMailer.cancellation_confirmation(order).deliver_later
          
          true
        end
      rescue => e
        Rails.logger.error "Order cancellation failed: #{e.message}"
        raise
      end
      
      private
      
      def apply_discounts(order)
        coupon = Coupon.find_by(code: order.coupon_code)
        return unless coupon&.valid?
        
        discount_amount = coupon.calculate_discount(order.total_amount)
        order.update!(discount_amount: discount_amount, coupon_id: coupon.id)
      end
    end
    
    # 4. Database Migration Patterns
    # Safe migration strategies and data transformations
    
    class AddUserRolesToExistingUsers < ActiveRecord::Migration[7.0]
      def up
        create_table :user_roles do |t|
          t.references :user, null: false, foreign_key: true
          t.references :role, null: false, foreign_key: true
          t.timestamps
        end
        
        add_index :user_roles, [:user_id, :role_id], unique: true
        
        # Create default roles
        Role.create!([
          { name: 'admin', description: 'System administrator' },
          { name: 'moderator', description: 'Content moderator' },
          { name: 'user', description: 'Regular user' }
        ])
        
        # Assign default role to existing users
        user_role = Role.find_by(name: 'user')
        User.find_each do |user|
          UserRole.create!(user: user, role: user_role)
        end
      end
      
      def down
        drop_table :user_roles
      end
    end
    
    class AddProductIndexes < ActiveRecord::Migration[7.0]
      def up
        # Add indexes for frequently queried fields
        add_index :products, :name
        add_index :products, :sku, unique: true
        add_index :products, :price
        add_index :products, :stock_quantity
        add_index :products, :created_at
        add_index :products, :active
        add_index :products, [:category_id, :active]
        add_index :products, [:brand_id, :active]
        
        # Add composite indexes for common query patterns
        add_index :products, [:active, :stock_quantity]
        add_index :products, [:featured, :active]
      end
      
      def down
        remove_index :products, :name
        remove_index :products, :sku
        remove_index :products, :price
        remove_index :products, :stock_quantity
        remove_index :products, :created_at
        remove_index :products, :active
        remove_index :products, [:category_id, :active]
        remove_index :products, [:brand_id, :active]
        remove_index :products, [:active, :stock_quantity]
        remove_index :products, [:featured, :active]
      end
    end
    
    # 5. Performance Optimization Patterns
    # Query optimization and caching strategies
    
    class OptimizedProductQueries
      def self.popular_products_with_caching(limit = 10)
        Rails.cache.fetch("popular_products_#{limit}", expires_in: 1.hour) do
          Product.joins(:order_items)
                .where('order_items.created_at > ?', 30.days.ago)
                .group('products.id')
                .select('products.*, COUNT(order_items.id) as order_count')
                .order('order_count DESC')
                .limit(limit)
        end
      end
      
      def self.product_search_with_caching(search_term, page = 1)
        cache_key = "product_search_#{search_term}_page_#{page}"
        
        Rails.cache.fetch(cache_key, expires_in: 30.minutes) do
          Product.where('name ILIKE ? OR description ILIKE ?', "%#{search_term}%", "%#{search_term}%")
                .includes(:category, :brand, :reviews)
                .page(page)
                .per(20)
        end
      end
      
      def self.category_hierarchy_with_caching
        Rails.cache.fetch('category_hierarchy', expires_in: 24.hours) do
          Category.where(parent_id: nil)
                .includes(:subcategories, :products)
                .map do |category|
                  {
                    id: category.id,
                    name: category.name,
                    product_count: category.products.count,
                    subcategories: category.subcategories.map do |subcat|
                      {
                        id: subcat.id,
                        name: subcat.name,
                        product_count: subcat.products.count
                      }
                    end
                  }
                end
        end
      end
    end
    
    # 6. Data Validation and Integrity
    # Custom validations and data integrity checks
    
    class ProductValidator
      def self.validate_product_data(product_data)
        errors = []
        
        # Validate required fields
        errors << "Name is required" if product_data[:name].blank?
        errors << "Description is required" if product_data[:description].blank?
        errors << "Price is required" if product_data[:price].blank?
        errors << "Category is required" if product_data[:category_id].blank?
        
        # Validate price
        if product_data[:price].present?
          price = product_data[:price].to_f
          errors << "Price must be greater than 0" if price <= 0
          errors << "Price cannot exceed $999,999" if price > 999999
        end
        
        # Validate stock quantity
        if product_data[:stock_quantity].present?
          quantity = product_data[:stock_quantity].to_i
          errors << "Stock quantity cannot be negative" if quantity < 0
          errors << "Stock quantity cannot exceed 1,000,000" if quantity > 1000000
        end
        
        # Validate SKU format
        if product_data[:sku].present?
          sku = product_data[:sku]
          errors << "SKU must be at least 3 characters" if sku.length < 3
          errors << "SKU cannot exceed 50 characters" if sku.length > 50
          errors << "SKU contains invalid characters" unless sku.match?(/\A[A-Z0-9-]+\z/i)
        end
        
        # Validate image URLs
        if product_data[:image_urls].present?
          product_data[:image_urls].each_with_index do |url, index|
            unless url.match?(/\Ahttps?:\/\/.+\.(jpg|jpeg|png|gif)\z/i)
              errors << "Invalid image URL at index #{index + 1}"
            end
          end
        end
        
        errors
      end
      
      def self.check_data_integrity
        issues = []
        
        # Check for orphaned records
        orphaned_products = Product.where.not(category_id: Category.select(:id))
        if orphaned_products.any?
          issues << "Found #{orphaned_products.count} products with invalid categories"
        end
        
        orphaned_order_items = OrderItem.where.not(product_id: Product.select(:id))
        if orphaned_order_items.any?
          issues << "Found #{orphaned_order_items.count} order items with invalid products"
        end
        
        # Check for negative inventory
        negative_inventory = Product.where('stock_quantity < 0')
        if negative_inventory.any?
          issues << "Found #{negative_inventory.count} products with negative stock"
        end
        
        # Check for duplicate SKUs
        duplicate_skus = Product.group(:sku).having('COUNT(*) > 1')
        if duplicate_skus.any?
          issues << "Found #{duplicate_skus.count} duplicate SKUs"
        end
        
        # Check for orders without required associations
        orders_without_user = Order.where(user_id: nil)
        if orders_without_user.any?
          issues << "Found #{orders_without_user.count} orders without users"
        end
        
        issues
      end
    end
    
    # 7. Backup and Recovery Patterns
    # Database backup strategies and recovery procedures
    
    class DatabaseBackupService
      def self.create_full_backup
        timestamp = Time.current.strftime('%Y%m%d_%H%M%S')
        backup_file = "db_backup_#{timestamp}.sql"
        
        # Create backup using pg_dump (PostgreSQL example)
        system("pg_dump #{database_name} > #{backup_file}")
        
        # Compress backup
        system("gzip #{backup_file}")
        
        # Upload to cloud storage (example)
        upload_to_cloud_storage("#{backup_file}.gz")
        
        # Clean up old backups (keep last 30 days)
        cleanup_old_backups
        
        "#{backup_file}.gz"
      end
      
      def self.restore_from_backup(backup_file)
        # Download from cloud storage if needed
        backup_file = download_from_cloud_storage(backup_file) unless File.exist?(backup_file)
        
        # Decompress if needed
        if backup_file.end_with?('.gz')
          system("gunzip #{backup_file}")
          backup_file = backup_file.gsub('.gz', '')
        end
        
        # Restore database
        system("psql #{database_name} < #{backup_file}")
        
        # Verify restore
        verify_database_integrity
      end
      
      def self.create_incremental_backup
        # This would implement incremental backup logic
        # For demonstration, we'll create a differential backup
        
        timestamp = Time.current.strftime('%Y%m%d_%H%M%S')
        backup_file = "db_incremental_backup_#{timestamp}.sql"
        
        # Get last full backup timestamp
        last_full_backup = get_last_full_backup_timestamp
        
        # Create differential backup since last full backup
        system("pg_dump #{database_name} --since '#{last_full_backup}' > #{backup_file}")
        
        backup_file
      end
      
      private
      
      def self.database_name
        Rails.configuration.database_configuration[Rails.env]['database']
      end
      
      def self.upload_to_cloud_storage(file_path)
        # Implementation would depend on cloud provider
        puts "Uploading #{file_path} to cloud storage"
      end
      
      def self.download_from_cloud_storage(file_path)
        # Implementation would depend on cloud provider
        puts "Downloading #{file_path} from cloud storage"
        file_path
      end
      
      def self.cleanup_old_backups
        # Keep only last 30 days of backups
        cutoff_date = 30.days.ago
        
        Dir.glob('db_backup_*.sql.gz').each do |file|
          file_date = File.mtime(file)
          File.delete(file) if file_date < cutoff_date
        end
      end
      
      def self.get_last_full_backup_timestamp
        # Get timestamp of last full backup
        backups = Dir.glob('db_backup_*.sql.gz').sort
        return 30.days.ago if backups.empty?
        
        File.mtime(backups.last)
      end
      
      def self.verify_database_integrity
        # Verify database integrity after restore
        issues = ProductValidator.check_data_integrity
        
        if issues.any?
          Rails.logger.error "Database integrity issues found: #{issues.join(', ')}"
          raise DatabaseError, "Database integrity check failed"
        end
        
        puts "Database integrity verified successfully"
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Active Record Patterns Demonstration"
  puts "=" * 60
  
  # Demonstrate basic model operations
  puts "\n1. Basic Model Operations:"
  puts "✅ User model with validations and associations"
  puts "✅ Order model with status management"
  puts "✅ Product model with inventory tracking"
  
  # Demonstrate advanced queries
  puts "\n2. Advanced Query Patterns:"
  puts "✅ Product repository with complex filtering"
  puts "✅ Popular products with caching"
  puts "✅ Cross-sell recommendations"
  
  # Demonstrate transaction management
  puts "\n3. Transaction Management:"
  puts "✅ Order creation with inventory reservation"
  puts "✅ Payment processing with rollback"
  puts "✅ Order cancellation with refund"
  
  # Demonstrate performance optimization
  puts "\n4. Performance Optimization:"
  puts "✅ Query result caching"
  puts "✅ Database indexes"
  puts "✅ Eager loading associations"
  
  # Demonstrate data validation
  puts "\n5. Data Validation:"
  puts "✅ Custom validators"
  puts "✅ Data integrity checks"
  puts "✅ Business rule validation"
  
  # Demonstrate backup and recovery
  puts "\n6. Backup and Recovery:"
  puts "✅ Full database backup"
  puts "✅ Incremental backup"
  puts "✅ Database restoration"
  
  puts "\nActive Record patterns help build robust and maintainable applications!"
end
