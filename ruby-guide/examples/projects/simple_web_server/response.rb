# Response class - HTTP response building

class Response
  attr_reader :status, :body, :headers

  def initialize(status = 200, body = '', content_type = 'text/html')
    @status = status
    @body = body
    @headers = {
      'Content-Type' => content_type,
      'Content-Length' => body.bytesize.to_s,
      'Server' => 'SimpleWebServer/1.0 (Ruby)',
      'Connection' => 'close'
    }
  end

  def set_header(name, value)
    @headers[name] = value
  end

  def add_header(name, value)
    @headers[name] = value
  end

  def status_text
    case @status
    when 200
      'OK'
    when 201
      'Created'
    when 302
      'Found'
    when 400
      'Bad Request'
    when 401
      'Unauthorized'
    when 403
      'Forbidden'
    when 404
      'Not Found'
    when 500
      'Internal Server Error'
    else
      'Unknown'
    end
  end

  def to_s
    <<~RESPONSE
      HTTP/1.1 #{@status} #{status_text}
      #{@headers.map { |k, v| "#{k}: #{v}" }.join("\r\n")}
      
      #{@body}
    RESPONSE
  end

  # Class methods for common responses
  def self.ok(body = '', content_type = 'text/html')
    new(200, body, content_type)
  end

  def self.not_found(body = 'Not Found')
    new(404, body)
  end

  def self.internal_error(body = 'Internal Server Error')
    new(500, body)
  end

  def self.redirect(location, body = 'Redirecting...')
    response = new(302, body)
    response.set_header('Location', location)
    response
  end

  def self.json(data)
    json_body = data.is_a?(String) ? data : data.to_json
    new(200, json_body, 'application/json')
  end

  def self.file(file_path)
    return not_found('File not found') unless File.exist?(file_path)
    return not_found('Not a file') unless File.file?(file_path)
    
    content_type = get_content_type(file_path)
    content = File.read(file_path)
    
    new(200, content, content_type)
  end

  def self.html(content)
    new(200, content, 'text/html')
  end

  def self.text(content)
    new(200, content, 'text/plain')
  end

  private

  def self.get_content_type(file_path)
    ext = File.extname(file_path).downcase
    
    case ext
    when '.html'
      'text/html'
    when '.css'
      'text/css'
    when '.js'
      'application/javascript'
    when '.json'
      'application/json'
    when '.png'
      'image/png'
    when '.jpg', '.jpeg'
      'image/jpeg'
    when '.gif'
      'image/gif'
    when '.svg'
      'image/svg+xml'
    when '.txt'
      'text/plain'
    else
      'application/octet-stream'
    end
  end
end
