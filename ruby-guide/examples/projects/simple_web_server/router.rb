# Router class - Simple routing system

class Router
  def initialize
    @routes = {
      'GET' => [],
      'POST' => [],
      'PUT' => [],
      'DELETE' => []
    }
    @not_found_handler = nil
  end

  def get(path, handler)
    add_route('GET', path, handler)
  end

  def post(path, handler)
    add_route('POST', path, handler)
  end

  def put(path, handler)
    add_route('PUT', path, handler)
  end

  def delete(path, handler)
    add_route('DELETE', path, handler)
  end

  def not_found(&block)
    @not_found_handler = block
  end

  def route(request)
    handler = find_handler(request.method, request.path)
    
    if handler
      begin
        handler.call(request)
      rescue => e
        puts "Error executing handler: #{e.message}"
        Response.internal_error("Handler error: #{e.message}")
      end
    else
      handle_not_found(request)
    end
  end

  private

  def add_route(method, path, handler)
    @routes[method] << Route.new(path, handler)
  end

  def find_handler(method, path)
    routes = @routes[method]
    return nil unless routes
    
    routes.find { |route| route.matches?(path) }
  end

  def handle_not_found(request)
    if @not_found_handler
      @not_found_handler.call(request)
    else
      Response.not_found("Page not found: #{request.path}")
    end
  end

  # Route class for individual route handling
  class Route
    def initialize(path, handler)
      @path = path
      @handler = handler
      @pattern = compile_pattern(path)
    end

    def matches?(path)
      if @path.include?('*')
        # Wildcard matching
        pattern = @path.gsub('*', '.*')
        path.match?(/\A#{pattern}\z/)
      else
        # Exact matching
        @path == path
      end
    end

    def extract_params(path)
      return {} unless @path.include?('*')
      
      pattern = @path.gsub('*', '(.+)')
      match = path.match(/\A#{pattern}\z/)
      return {} unless match
      
      { wildcard: match[1] }
    end

    def call(request)
      params = extract_params(request.path)
      @handler.call(request)
    end

    private

    def compile_pattern(path)
      if path.include?('*')
        pattern = path.gsub('*', '(.+)')
        /\A#{pattern}\z/
      else
        /\A#{Regexp.escape(path)}\z/
      end
    end
  end
end
