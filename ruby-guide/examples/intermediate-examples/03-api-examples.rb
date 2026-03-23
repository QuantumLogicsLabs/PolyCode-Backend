# API Examples in Ruby
# Demonstrating various API integration patterns

require 'json'
require 'net/http'
require 'uri'
require 'httparty'

class APIExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "🔌 API Examples in Ruby"
    puts "======================"
    puts "Explore API integration patterns and techniques!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 API Examples Menu:"
      puts "1. REST API Client"
      puts "2. GraphQL API"
      puts "3. WebSocket API"
      puts "4. OAuth Authentication"
      puts "5. Rate Limiting"
      puts "6. Error Handling"
      puts "7. API Testing"
      puts "8. Caching Strategies"
      puts "9. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-9): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        rest_api_client
      when 2
        graphql_api
      when 3
        websocket_api
      when 4
        oauth_authentication
      when 5
        rate_limiting
      when 6
        error_handling
      when 7
        api_testing
      when 8
        caching_strategies
      when 9
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def rest_api_client
    puts "\n🔌 Example 1: REST API Client"
    puts "=" * 45
    puts "Building a comprehensive REST API client."
    puts ""
    
    class RESTAPIClient
      def initialize(base_url, headers = {})
        @base_url = base_url
        @headers = {
          'Content-Type' => 'application/json',
          'Accept' => 'application/json'
        }.merge(headers)
      end
      
      def get(endpoint, params = {})
        uri = build_uri(endpoint, params)
        response = Net::HTTP.get_response(uri)
        handle_response(response)
      end
      
      def post(endpoint, data = {})
        uri = build_uri(endpoint)
        request = Net::HTTP::Post.new(uri)
        set_headers(request)
        request.body = data.to_json
        response = Net::HTTP.start(uri.host, uri.port, use_ssl: uri.scheme == 'https') do |http|
          http.request(request)
        end
        handle_response(response)
      end
      
      def put(endpoint, data = {})
        uri = build_uri(endpoint)
        request = Net::HTTP::Put.new(uri)
        set_headers(request)
        request.body = data.to_json
        response = Net::HTTP.start(uri.host, uri.port, use_ssl: uri.scheme == 'https') do |http|
          http.request(request)
        end
        handle_response(response)
      end
      
      def delete(endpoint)
        uri = build_uri(endpoint)
        request = Net::HTTP::Delete.new(uri)
        set_headers(request)
        response = Net::HTTP.start(uri.host, uri.port, use_ssl: uri.scheme == 'https') do |http|
          http.request(request)
        end
        handle_response(response)
      end
      
      private
      
      def build_uri(endpoint, params = {})
        uri = URI("#{@base_url}#{endpoint}")
        uri.query = URI.encode_www_form(params) unless params.empty?
        uri
      end
      
      def set_headers(request)
        @headers.each { |key, value| request[key] = value }
      end
      
      def handle_response(response)
        case response.code.to_i
        when 200..299
          { success: true, data: JSON.parse(response.body), status: response.code }
        when 400..499
          { success: false, error: JSON.parse(response.body), status: response.code }
        when 500..599
          { success: false, error: "Server error: #{response.code}", status: response.code }
        else
          { success: false, error: "Unknown error: #{response.code}", status: response.code }
        end
      rescue JSON::ParserError
        { success: false, error: "Invalid JSON response", status: response.code }
      end
    end
    
    # Usage examples
    puts "REST API Client Examples:"
    
    client = RESTAPIClient.new("https://jsonplaceholder.typicode.com")
    
    # GET request
    puts "\nGET request example:"
    get_result = client.get("/posts/1")
    puts "Result: #{get_result[:success] ? 'Success' : 'Failed'}"
    puts "Status: #{get_result[:status]}"
    puts "Data: #{get_result[:data]['title']}" if get_result[:success]
    
    # POST request
    puts "\nPOST request example:"
    post_data = { title: "Test Post", body: "Test content", userId: 1 }
    post_result = client.post("/posts", post_data)
    puts "Result: #{post_result[:success] ? 'Success' : 'Failed'}"
    puts "Status: #{post_result[:status]}"
    puts "Created ID: #{post_result[:data]['id']}" if post_result[:success]
    
    @examples << {
      title: "REST API Client",
      description: "Comprehensive REST API client with error handling",
      code: <<~RUBY
        class RESTAPIClient
          def initialize(base_url)
            @base_url = base_url
          end
          
          def get(endpoint)
            uri = URI("\#{@base_url}\#{endpoint}")
            response = Net::HTTP.get_response(uri)
            JSON.parse(response.body)
          end
        end
      RUBY
    }
    
    puts "\n✅ REST API Client example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def graphql_api
    puts "\n🔍 Example 2: GraphQL API"
    puts "=" * 45
    puts "Working with GraphQL APIs in Ruby."
    puts ""
    
    class GraphQLClient
      def initialize(endpoint_url, headers = {})
        @endpoint_url = endpoint_url
        @headers = {
          'Content-Type' => 'application/json',
          'Accept' => 'application/json'
        }.merge(headers)
      end
      
      def query(query_string, variables = {})
        payload = {
          query: query_string,
          variables: variables
        }
        
        uri = URI(@endpoint_url)
        request = Net::HTTP::Post.new(uri)
        @headers.each { |key, value| request[key] = value }
        request.body = payload.to_json
        
        response = Net::HTTP.start(uri.host, uri.port, use_ssl: uri.scheme == 'https') do |http|
          http.request(request)
        end
        
        handle_response(response)
      end
      
      def mutation(mutation_string, variables = {})
        query(mutation_string, variables)
      end
      
      private
      
      def handle_response(response)
        case response.code.to_i
        when 200..299
          data = JSON.parse(response.body)
          if data['errors']
            { success: false, errors: data['errors'], data: data['data'] }
          else
            { success: true, data: data['data'] }
          end
        else
          { success: false, error: "HTTP #{response.code}: #{response.message}" }
        end
      rescue JSON::ParserError
        { success: false, error: "Invalid JSON response" }
      end
    end
    
    # GraphQL query examples
    puts "GraphQL Query Examples:"
    
    client = GraphQLClient.new("https://api.github.com/graphql")
    
    # Example GraphQL queries
    user_query = <<~GQL
      query($login: String!) {
        user(login: $login) {
          login
          name
          bio
          followers {
            totalCount
          }
          repositories(first: 5) {
            edges {
              node {
                name
                stargazerCount
              }
            }
          }
        }
      }
    GQL
    
    puts "\nGraphQL query structure:"
    puts user_query
    
    # Repository query
    repo_query = <<~GQL
      query($owner: String!, $name: String!) {
        repository(owner: $owner, name: $name) {
          name
          description
          stargazerCount
          forkCount
          primaryLanguage {
            name
          }
          issues(first: 10) {
            edges {
              node {
                title
                state
              }
            }
          }
        }
      }
    GQL
    
    puts "\nRepository query example:"
    puts repo_query
    
    @examples << {
      title: "GraphQL API",
      description: "GraphQL client with query and mutation support",
      code: <<~RUBY
        class GraphQLClient
          def query(query_string, variables = {})
            payload = { query: query_string, variables: variables }
            response = HTTParty.post(@endpoint_url, body: payload.to_json, headers: @headers)
            JSON.parse(response.body)
          end
        end
      RUBY
    }
    
    puts "\n✅ GraphQL API example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All API Examples"
    puts "=" * 45
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate different API integration patterns!"
  end
end

if __FILE__ == $0
  examples = APIExamples.new
  examples.start_examples
end
