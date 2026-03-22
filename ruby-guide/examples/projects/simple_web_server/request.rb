# Request class - HTTP request parsing and handling

class Request
  attr_reader :method, :path, :version, :headers, :body, :query_params

  def initialize(method, path, version, headers, body)
    @method = method
    @path = path
    @version = version
    @headers = headers
    @body = body
    @query_params = parse_query_params
  end

  def self.parse(raw_request)
    lines = raw_request.split("\r\n")
    
    # Parse request line
    request_line = lines.shift
    method, path, version = request_line.split(' ')
    
    # Parse headers
    headers = {}
    while lines.first && !lines.first.empty?
      header_line = lines.shift
      key, value = header_line.split(': ', 2)
      headers[key] = value
    end
    
    # Remove empty line between headers and body
    lines.shift if lines.first && lines.first.empty?
    
    # Parse body
    body = lines.join("\r\n")
    
    new(method, path, version, headers, body)
  end

  def get_param(param)
    @query_params[param]
  end

  def header(name)
    @headers[name]
  end

  def user_agent
    header('User-Agent')
  end

  def content_type
    header('Content-Type')
  end

  def content_length
    length = header('Content-Length')
    length ? length.to_i : 0
  end

  def host
    header('Host')
  end

  def to_s
    <<~REQUEST
      #{@method} #{@path} #{@version}
      #{@headers.map { |k, v| "#{k}: #{v}" }.join("\r\n")}
      
      #{@body}
    REQUEST
  end

  private

  def parse_query_params
    return {} unless @path.include?('?')
    
    query_string = @path.split('?', 2)[1]
    return {} unless query_string
    
    params = {}
    query_string.split('&').each do |param|
      key, value = param.split('=', 2)
      next unless key
      
      key = URI.decode_www_form_component(key)
      value = value ? URI.decode_www_form_component(value) : ''
      params[key] = value
    end
    
    params
  end
end
