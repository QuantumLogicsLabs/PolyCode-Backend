#!/usr/bin/env ruby

# Ruby Guide Documentation Generator
# This script generates comprehensive documentation for the Ruby Guide project
# including table of contents, cross-references, and formatted output

require 'fileutils'
require 'json'
require 'yaml'
require 'erb'

class DocumentationGenerator
  def initialize
    @project_root = Dir.pwd
    @output_dir = File.join(@project_root, 'docs')
    @source_dir = File.join(@project_root, 'ruby-guide')
    @config_file = File.join(@project_root, '.doc-config.json')
    @log_file = File.join(@project_root, 'docs-generation.log')
    
    @config = load_config
    @toc = []
    @cross_references = {}
    @stats = {
      files_processed: 0,
      sections_found: 0,
      code_blocks: 0,
      examples: 0
    }
  end

  def generate
    puts "📚 Ruby Guide Documentation Generator"
    puts "==================================="
    
    log("Documentation generation started at #{Time.now}")
    
    setup_output_directory
    scan_source_files
    generate_table_of_contents
    generate_cross_references
    generate_documentation_files
    generate_search_index
    generate_statistics
    
    puts "\n✅ Documentation generated successfully!"
    puts "📁 Output directory: #{@output_dir}"
    puts "📊 Files processed: #{@stats[:files_processed]}"
    puts "📋 Check docs-generation.log for details"
    
    log("Documentation generation completed")
  rescue => e
    puts "\n❌ Documentation generation failed: #{e.message}"
    log("Documentation generation failed: #{e.message}")
    exit 1
  end

  private

  def load_config
    default_config = {
      output_formats: ['html', 'markdown', 'pdf'],
      include_toc: true,
      include_search: true,
      include_statistics: true,
      theme: 'default',
      code_highlighting: true,
      generate_api_docs: true
    }
    
    if File.exist?(@config_file)
      JSON.parse(File.read(@config_file)).merge(default_config)
    else
      default_config
    end
  end

  def setup_output_directory
    puts "\n📁 Setting up output directory..."
    
    FileUtils.mkdir_p(@output_dir) unless Dir.exist?(@output_dir)
    
    # Create subdirectories
    subdirs = ['assets', 'css', 'js', 'images', 'api']
    subdirs.each do |subdir|
      dir_path = File.join(@output_dir, subdir)
      FileUtils.mkdir_p(dir_path) unless Dir.exist?(dir_path)
    end
    
    puts "✅ Output directory ready"
    log("Output directory setup completed")
  end

  def scan_source_files
    puts "\n🔍 Scanning source files..."
    
    @markdown_files = Dir.glob(File.join(@source_dir, '**/*.md'))
    @ruby_files = Dir.glob(File.join(@source_dir, '**/*.rb'))
    
    puts "Found #{@markdown_files.length} Markdown files"
    puts "Found #{@ruby_files.length} Ruby files"
    
    # Process each file
    (@markdown_files + @ruby_files).each do |file|
      process_file(file)
      @stats[:files_processed] += 1
    end
    
    log("Source files scanned: #{@markdown_files.length + @ruby_files.length}")
  end

  def process_file(file_path)
    relative_path = file_path.sub(@source_dir, '').sub(/^\//, '')
    file_info = {
      path: relative_path,
      title: extract_title(file_path),
      sections: extract_sections(file_path),
      code_blocks: extract_code_blocks(file_path),
      examples: extract_examples(file_path),
      references: extract_references(file_path)
    }
    
    @toc << file_info
    @stats[:sections_found] += file_info[:sections].length
    @stats[:code_blocks] += file_info[:code_blocks].length
    @stats[:examples] += file_info[:examples].length
    
    # Add to cross-references
    file_info[:sections].each do |section|
      ref_key = "#{relative_path}##{section[:id]}"
      @cross_references[ref_key] = {
        title: section[:title],
        file: relative_path,
        section: section[:id]
      }
    end
  end

  def extract_title(file_path)
    content = File.read(file_path)
    
    # Try to find first H1 heading
    if content.match(/^#\s+(.+)$/)
      $1.strip
    else
      # Fallback to filename
      File.basename(file_path, '.*').gsub(/[-_]/, ' ').split.map(&:capitalize).join(' ')
    end
  end

  def extract_sections(file_path)
    content = File.read(file_path)
    sections = []
    
    # Find all headings (H2, H3, H4)
    content.scan(/^(#{'##'}\s+(.+)$)/) do |match|
      level = match[0].length - 1
      title = match[1].strip
      id = title.downcase.gsub(/[^a-z0-9\s-]/, '').gsub(/\s+/, '-')
      
      sections << {
        level: level,
        title: title,
        id: id,
        line_number: content.lines.find_index { |line| line.match(/^#{'##'}\s+#{Regexp.escape(title)}$/) } + 1
      }
    end
    
    sections
  end

  def extract_code_blocks(file_path)
    content = File.read(file_path)
    code_blocks = []
    
    # Find fenced code blocks
    content.scan(/^```(\w+)?\n(.*?)\n```/m) do |match|
      language = match[0] || 'text'
      code = match[1]
      
      code_blocks << {
        language: language,
        code: code,
        line_number: content.lines.find_index { |line| line.match(/^```#{language}/) } + 1
      }
    end
    
    code_blocks
  end

  def extract_examples(file_path)
    content = File.read(file_path)
    examples = []
    
    # Find code examples (Ruby files or marked examples)
    if file_path.end_with?('.rb')
      examples << {
        type: 'ruby_file',
        content: content,
        description: extract_ruby_description(content)
      }
    else
      # Look for marked examples in Markdown
      content.scan(/```ruby\n(.*?)\n```/m) do |match|
        examples << {
          type: 'embedded',
          content: match[0],
          description: 'Ruby code example'
        }
      end
    end
    
    examples
  end

  def extract_ruby_description(content)
    # Try to extract description from Ruby file comments
    first_comment = content.match(/^#\s*(.+)$/m)
    first_comment ? first_comment[1] : 'Ruby code example'
  end

  def extract_references(file_path)
    content = File.read(file_path)
    references = []
    
    # Find internal links
    content.scan(/\[([^\]]+)\]\(([^)]+)\)/) do |match|
      link_text = match[0]
      link_target = match[1]
      
      if link_target.start_with?('#')
        references << {
          type: 'internal',
          text: link_text,
          target: link_target
        }
      end
    end
    
    references
  end

  def generate_table_of_contents
    puts "\n📋 Generating table of contents..."
    
    toc_content = generate_toc_content
    toc_file = File.join(@output_dir, 'table_of_contents.md')
    
    File.write(toc_file, toc_content)
    puts "✅ Table of contents generated"
    
    log("Table of contents generated")
  end

  def generate_toc_content
    toc_content = <<~TOC
      # Ruby Guide - Table of Contents
      
      This document provides a comprehensive overview of all content in the Ruby Guide.
      
      ## Quick Navigation
      
      - [Getting Started](#getting-started)
      - [Ruby Fundamentals](#ruby-fundamentals)
      - [Advanced Topics](#advanced-topics)
      - [Practical Applications](#practical-applications)
      - [Resources](#resources)
      
    TOC
    
    # Organize content by directory
    organized_toc = organize_by_directory
    
    organized_toc.each do |category, files|
      toc_content << "\n## #{category}\n\n"
      
      files.each do |file|
        toc_content << "### [#{file[:title]}](#{file[:path]})\n\n"
        
        if file[:sections].any?
          toc_content << "#### Sections:\n"
          file[:sections].each do |section|
            toc_content << "- [#{section[:title]}](#{file[:path]}##{section[:id]})\n"
          end
          toc_content << "\n"
        end
        
        if file[:examples].any?
          toc_content << "#### Examples:\n"
          file[:examples].each_with_index do |example, i|
            toc_content << "- [Example #{i + 1}](#{file[:path]}##{example[:type]}-#{i})\n"
          end
          toc_content << "\n"
        end
      end
    end
    
    toc_content << <<~TOC
      
      ## Statistics
      
      - **Total Files**: #{@stats[:files_processed]}
      - **Total Sections**: #{@stats[:sections_found]}
      - **Code Blocks**: #{@stats[:code_blocks]}
      - **Examples**: #{@stats[:examples]}
      
      ## Search
      
      Use the search functionality to find specific topics:
      
      ```bash
      # Search in documentation
      grep -r "topic_name" #{@output_dir}
      
      # Search by file
      find #{@output_dir} -name "*.md" -exec grep -l "topic_name" {} \;
      ```
      
      ## Cross-References
      
      This documentation includes cross-references between related topics.
      Internal links are automatically maintained and validated.
      
      ---
      
      *Generated on #{Time.now}*
    TOC
    
    toc_content
  end

  def organize_by_directory
    organized = {}
    
    @toc.each do |file|
      dir = File.dirname(file[:path])
      
      # Categorize by directory
      category = case dir
               when 'advanced' then 'Advanced Topics'
               when 'basics' then 'Ruby Fundamentals'
               when 'examples' then 'Practical Applications'
               when 'tutorials' then 'Tutorials'
               when 'reference' then 'Reference'
               else dir.split('/').map(&:capitalize).join(' ')
               end
      
      organized[category] ||= []
      organized[category] << file
    end
    
    # Sort categories and files
    organized.keys.sort.each do |category|
      organized[category].sort_by! { |file| file[:path] }
    end
    
    organized
  end

  def generate_cross_references
    puts "\n🔗 Generating cross-references..."
    
    cross_ref_content = generate_cross_ref_content
    cross_ref_file = File.join(@output_dir, 'cross_references.json')
    
    File.write(cross_ref_file, JSON.pretty_generate(@cross_references))
    puts "✅ Cross-references generated"
    
    log("Cross-references generated: #{@cross_references.keys.length} references")
  end

  def generate_cross_ref_content
    # Create a comprehensive cross-reference map
    cross_ref_map = {
      sections: {},
      files: {},
      topics: {}
    }
    
    @toc.each do |file|
      file_key = file[:path]
      
      # File information
      cross_ref_map[:files][file_key] = {
        title: file[:title],
        sections: file[:sections].map { |s| s[:id] },
        examples: file[:examples].length,
        code_blocks: file[:code_blocks].length
      }
      
      # Section information
      file[:sections].each do |section|
        section_key = "#{file_key}##{section[:id]}"
        cross_ref_map[:sections][section_key] = {
          title: section[:title],
          file: file_key,
          level: section[:level]
        }
      end
      
      # Extract topics from titles and content
      topics = extract_topics(file)
      topics.each do |topic|
        topic_key = topic.downcase.gsub(/[^a-z0-9]/, '')
        cross_ref_map[:topics][topic_key] ||= []
        cross_ref_map[:topics][topic_key] << {
          file: file_key,
          title: file[:title],
          context: topic
        }
      end
    end
    
    cross_ref_map
  end

  def extract_topics(file)
    topics = []
    
    # Extract from title
    topics.concat(file[:title].split.map(&:strip))
    
    # Extract from section titles
    file[:sections].each { |section| topics.concat(section[:title].split.map(&:strip)) }
    
    # Extract from code blocks (language names)
    file[:code_blocks].each { |block| topics << block[:language] }
    
    # Extract from content (simple keyword extraction)
    if File.exist?(File.join(@source_dir, file[:path]))
      content = File.read(File.join(@source_dir, file[:path]))
      
      # Find important Ruby keywords
      ruby_keywords = %w[class module def method if else end while for each do yield block proc lambda]
      ruby_keywords.each do |keyword|
        topics << keyword if content.match(/\b#{keyword}\b/)
      end
    end
    
    topics.uniq
  end

  def generate_documentation_files
    puts "\n📄 Generating documentation files..."
    
    if @config[:output_formats].include?('html')
      generate_html_documentation
    end
    
    if @config[:output_formats].include?('markdown')
      generate_markdown_documentation
    end
    
    if @config[:output_formats].include?('pdf')
      generate_pdf_documentation
    end
    
    if @config[:generate_api_docs]
      generate_api_documentation
    end
    
    puts "✅ Documentation files generated"
    log("Documentation files generated")
  end

  def generate_html_documentation
    puts "Generating HTML documentation..."
    
    html_template = load_html_template
    html_content = render_html_content(html_template)
    
    html_file = File.join(@output_dir, 'index.html')
    File.write(html_file, html_content)
    
    # Copy CSS and JS assets
    copy_html_assets
    
    puts "✅ HTML documentation generated"
  end

  def load_html_template
    template_path = File.join(@project_root, 'scripts', 'templates', 'documentation.html.erb')
    
    if File.exist?(template_path)
      ERB.new(File.read(template_path))
    else
      # Use built-in template
      ERB.new(builtin_html_template)
    end
  end

  def builtin_html_template
    <<~HTML
      <!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ruby Guide Documentation</title>
        <link rel="stylesheet" href="assets/style.css">
        <link rel="stylesheet" href="assets/highlight.css">
      </head>
      <body>
        <header class="header">
          <h1>Ruby Guide</h1>
          <nav class="navigation">
            <ul>
              <li><a href="#toc">Table of Contents</a></li>
              <li><a href="#search">Search</a></li>
              <li><a href="#api">API Reference</a></li>
            </ul>
          </nav>
        </header>
        
        <main class="main-content">
          <section id="toc">
            <h2>Table of Contents</h2>
            <%= render_table_of_contents %>
          </section>
          
          <section id="content">
            <%= render_content %>
          </section>
        </main>
        
        <footer class="footer">
          <p>Generated on <%= Time.now.strftime('%Y-%m-%d %H:%M:%S') %></p>
        </footer>
        
        <script src="assets/search.js"></script>
        <script src="assets/navigation.js"></script>
      </body>
      </html>
    HTML
  end

  def render_html_content(template)
    template.result(binding)
  end

  def render_table_of_contents
    organized_toc = organize_by_directory
    toc_html = ""
    
    organized_toc.each do |category, files|
      toc_html += "<h3>#{category}</h3>\n<ul>\n"
      
      files.each do |file|
        toc_html += "<li><a href=\"##{file[:path].gsub(/[^a-z0-9]/, '-')}\">#{file[:title]}</a></li>\n"
      end
      
      toc_html += "</ul>\n"
    end
    
    toc_html
  end

  def render_content
    content_html = ""
    
    @toc.each do |file|
      content_html += "<section id=\"#{file[:path].gsub(/[^a-z0-9]/, '-')}\">\n"
      content_html += "<h2>#{file[:title]}</h2>\n"
      
      # Add sections
      file[:sections].each do |section|
        content_html += "<h3 id=\"#{section[:id]\">#{section[:title]}</h3>\n"
      end
      
      # Add examples
      if file[:examples].any?
        content_html += "<h4>Examples</h4>\n"
        file[:examples].each_with_index do |example, i|
          content_html += "<div class=\"example\">\n"
          content_html += "<h5>Example #{i + 1}</h5>\n"
          content_html += "<pre><code>#{CG.escape(example[:content])}</code></pre>\n"
          content_html += "</div>\n"
        end
      end
      
      content_html += "</section>\n"
    end
    
    content_html
  end

  def copy_html_assets
    # Create basic CSS
    css_content = <<~CSS
      body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        color: #333;
      }
      
      .header {
        border-bottom: 2px solid #e1e5e9;
        padding-bottom: 20px;
        margin-bottom: 40px;
      }
      
      .navigation ul {
        list-style: none;
        padding: 0;
        display: flex;
        gap: 20px;
      }
      
      .navigation a {
        text-decoration: none;
        color: #007bff;
        font-weight: 500;
      }
      
      .navigation a:hover {
        text-decoration: underline;
      }
      
      .main-content {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 40px;
      }
      
      .toc {
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
      }
      
      .toc h3 {
        margin-top: 0;
        font-size: 1.1em;
        color: #495057;
      }
      
      .toc ul {
        list-style: none;
        padding: 0;
      }
      
      .toc li {
        margin-bottom: 8px;
      }
      
      .toc a {
        text-decoration: none;
        color: #6c757d;
        font-size: 0.9em;
      }
      
      .toc a:hover {
        color: #007bff;
      }
      
      .example {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 15px;
        margin: 20px 0;
      }
      
      .example pre {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        overflow-x: auto;
      }
      
      .footer {
        margin-top: 60px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
        text-align: center;
        color: #6c757d;
        font-size: 0.9em;
      }
      
      @media (max-width: 768px) {
        .main-content {
          grid-template-columns: 1fr;
        }
        
        .toc {
          position: static;
          max-height: none;
        }
      }
    CSS
    
    css_file = File.join(@output_dir, 'assets', 'style.css')
    File.write(css_file, css_content)
    
    # Create basic JavaScript
    js_content = <<~JS
      // Simple search functionality
      function initSearch() {
        const searchInput = document.getElementById('search-input');
        if (!searchInput) return;
        
        searchInput.addEventListener('input', (e) => {
          const query = e.target.value.toLowerCase();
          const sections = document.querySelectorAll('section');
          
          sections.forEach(section => {
            const text = section.textContent.toLowerCase();
            if (text.includes(query) || query === '') {
              section.style.display = 'block';
            } else {
              section.style.display = 'none';
            }
          });
        });
      }
      
      // Initialize when DOM is ready
      document.addEventListener('DOMContentLoaded', initSearch);
    JS
    
    js_file = File.join(@output_dir, 'assets', 'search.js')
    File.write(js_file, js_content)
  end

  def generate_markdown_documentation
    puts "Generating Markdown documentation..."
    
    # Create a single comprehensive Markdown file
    markdown_content = generate_comprehensive_markdown
    
    markdown_file = File.join(@output_dir, 'ruby-guide.md')
    File.write(markdown_file, markdown_content)
    
    puts "✅ Markdown documentation generated"
  end

  def generate_comprehensive_markdown
    content = <<~MARKDOWN
      # Ruby Guide - Complete Documentation
      
      This is the complete documentation for the Ruby Guide project.
      
      ## Table of Contents
      
    MARKDOWN
    
    organized_toc = organize_by_directory
    
    organized_toc.each do |category, files|
      content += "\n## #{category}\n\n"
      
      files.each do |file|
        content += "### #{file[:title]}\n\n"
        content += "**File**: `#{file[:path]}`\n\n"
        
        if file[:sections].any?
          content += "**Sections**:\n"
          file[:sections].each do |section|
            content += "- [#{section[:title]}](#{file[:path]}##{section[:id]})\n"
          end
          content += "\n"
        end
        
        if file[:examples].any?
          content += "**Examples**: #{file[:examples].length}\n\n"
        end
        
        if file[:code_blocks].any?
          content += "**Code Blocks**: #{file[:code_blocks].length}\n\n"
        end
      end
    end
    
    content += <<~MARKDOWN
      
      ## Statistics
      
      - **Total Files**: #{@stats[:files_processed]}
      - **Total Sections**: #{@stats[:sections_found]}
      - **Code Blocks**: #{@stats[:code_blocks]}
      - **Examples**: #{@stats[:examples]}
      
      ## Cross-References
      
      #{@cross_references.keys.length} cross-references available.
      
      ---
      
      *Generated on #{Time.now}*
    MARKDOWN
    
    content
  end

  def generate_pdf_documentation
    puts "Generating PDF documentation..."
    
    # This would require additional dependencies like Prawn or wkhtmltopdf
    # For now, create a placeholder
    pdf_content = <<~PDF
      # Ruby Guide - PDF Documentation
      
      This PDF documentation would be generated using a PDF library.
      To enable PDF generation, install the required dependencies:
      
      ```bash
      gem install prawn
      gem install prawn-table
      ```
      
      Then update the configuration to include PDF generation.
      
      ## Current Status
      
      PDF generation is not yet implemented.
      Please install the required dependencies and update the configuration.
      
      ---
      
      *Generated on #{Time.now}*
    PDF
    
    pdf_file = File.join(@output_dir, 'ruby-guide.pdf.md')
    File.write(pdf_file, pdf_content)
    
    puts "✅ PDF placeholder generated (requires additional setup)"
  end

  def generate_api_documentation
    puts "Generating API documentation..."
    
    api_content = generate_api_content
    api_file = File.join(@output_dir, 'api', 'index.md')
    
    File.write(api_file, api_content)
    
    puts "✅ API documentation generated"
  end

  def generate_api_content
    content = <<~API
      # Ruby Guide API Documentation
      
      This section provides API documentation for the Ruby Guide code examples and utilities.
      
      ## Available APIs
      
      ### Ruby Core APIs
      - [String Methods](#string-methods)
      - [Array Methods](#array-methods)
      - [Hash Methods](#hash-methods)
      - [Class Methods](#class-methods)
      
      ### Ruby Standard Library APIs
      - [File System](#file-system)
      - [Network](#network)
      - [Date/Time](#date-time)
      - [JSON](#json)
      
      ### Ruby Gems APIs
      - [Rails APIs](#rails-apis)
      - [RSpec APIs](#rspec-apis)
      - [Sinatra APIs](#sinatra-apis)
      
      ## String Methods
      
      ### Common Methods
      - `length` - Returns the length of the string
      - `upcase` - Converts to uppercase
      - `downcase` - Converts to lowercase
      - `strip` - Removes leading and trailing whitespace
      - `include?` - Checks if string contains substring
      
      ### Examples
      ```ruby
      str = "Hello, Ruby!"
      str.length        # => 12
      str.upcase        # => "HELLO, RUBY!"
      str.downcase      # => "hello, ruby!"
      str.include?("Ruby") # => true
      ```
      
      ## Array Methods
      
      ### Common Methods
      - `length` - Returns the number of elements
      - `first` - Returns the first element
      - `last` - Returns the last element
      - `push` - Adds element to end
      - `pop` - Removes and returns last element
      - `map` - Transforms elements
      - `select` - Filters elements
      
      ### Examples
      ```ruby
      arr = [1, 2, 3, 4, 5]
      arr.length        # => 5
      arr.first         # => 1
      arr.last          # => 5
      arr.map { |x| x * 2 } # => [2, 4, 6, 8, 10]
      arr.select { |x| x.even? } # => [2, 4]
      ```
      
      ## Hash Methods
      
      ### Common Methods
      - `keys` - Returns all keys
      - `values` - Returns all values
      - `include?` - Checks if key exists
      - `merge` - Combines hashes
      - `transform_values` - Transforms values
      
      ### Examples
      ```ruby
      hash = { name: "Ruby", version: "3.2" }
      hash.keys         # => [:name, :version]
      hash.values       # => ["Ruby", "3.2"]
      hash.include?(:name) # => true
      hash.merge({ creator: "Matz" })
      ```
      
      ---
      
      *Generated on #{Time.now}*
    API
    
    content
  end

  def generate_search_index
    return unless @config[:include_search]
    
    puts "\n🔍 Generating search index..."
    
    search_index = generate_search_index_content
    search_file = File.join(@output_dir, 'search_index.json')
    
    File.write(search_file, JSON.pretty_generate(search_index))
    puts "✅ Search index generated"
    
    log("Search index generated")
  end

  def generate_search_index_content
    index = {
      documents: [],
      terms: {},
      metadata: {
        generated_at: Time.now.iso8601,
        total_documents: @toc.length,
        total_terms: 0
      }
    }
    
    @toc.each do |file|
      doc = {
        id: file[:path],
        title: file[:title],
        url: file[:path],
        content: extract_search_content(file),
        sections: file[:sections].map { |s| s[:title] },
        examples: file[:examples].length,
        code_blocks: file[:code_blocks].map { |cb| cb[:language] }
      }
      
      index[:documents] << doc
      
      # Extract terms
      terms = extract_search_terms(doc)
      terms.each do |term|
        index[:terms][term] ||= []
        index[:terms][term] << doc[:id]
      end
    end
    
    index[:metadata][:total_terms] = index[:terms].keys.length
    
    index
  end

  def extract_search_content(file)
    content_parts = []
    
    # Add title
    content_parts << file[:title]
    
    # Add section titles
    content_parts.concat(file[:sections].map { |s| s[:title] })
    
    # Add example content
    content_parts.concat(file[:examples].map { |e| e[:content] })
    
    # Add code content (comments and method names)
    file[:code_blocks].each do |code_block|
      # Extract comments
      comments = code_block[:code].scan(/^#\s*(.+)$/).flatten
      content_parts.concat(comments)
      
      # Extract method definitions
      methods = code_block[:code].scan(/^\s*def\s+(\w+)/).flatten
      content_parts.concat(methods)
      
      # Extract class definitions
      classes = code_block[:code].scan(/^\s*class\s+(\w+)/).flatten
      content_parts.concat(classes)
    end
    
    content_parts.join(' ').downcase
  end

  def extract_search_terms(doc)
    # Simple term extraction
    terms = []
    
    # Split content into words
    words = doc[:content].split(/\s+/)
    
    # Filter and normalize terms
    words.each do |word|
      # Remove punctuation and normalize
      term = word.gsub(/[^a-z0-9]/, '')
      
      # Skip empty terms and very short terms
      next if term.length < 3
      
      # Skip common stop words
      next if %w[the and for are but not you all can had].include?(term)
      
      terms << term
    end
    
    terms.uniq
  end

  def generate_statistics
    return unless @config[:include_statistics]
    
    puts "\n📊 Generating statistics..."
    
    stats_content = generate_statistics_content
    stats_file = File.join(@output_dir, 'statistics.json')
    
    File.write(stats_file, JSON.pretty_generate(stats_content))
    
    # Also create a readable statistics report
    stats_report = generate_statistics_report
    report_file = File.join(@output_dir, 'statistics.md')
    File.write(report_file, stats_report)
    
    puts "✅ Statistics generated"
    log("Statistics generated")
  end

  def generate_statistics_content
    {
      generation: {
        timestamp: Time.now.iso8601,
        duration: Time.now - @start_time,
        ruby_version: RUBY_VERSION
      },
      files: {
        total: @stats[:files_processed],
        markdown: @markdown_files.length,
        ruby: @ruby_files.length,
        by_directory: calculate_files_by_directory
      },
      content: {
        total_sections: @stats[:sections_found],
        total_code_blocks: @stats[:code_blocks],
        total_examples: @stats[:examples],
        by_language: calculate_code_by_language
      },
      structure: {
        average_sections_per_file: @stats[:sections_found].to_f / @stats[:files_processed],
        average_examples_per_file: @stats[:examples].to_f / @stats[:files_processed],
        largest_file: find_largest_file
      },
      cross_references: {
        total_references: @cross_references.keys.length,
        broken_links: check_broken_links
      }
    }
  end

  def generate_statistics_report
    report = <<~REPORT
      # Ruby Guide Documentation Statistics
      
      Generated on #{Time.now}
      
      ## Overview
      
      - **Total Files Processed**: #{@stats[:files_processed]}
      - **Total Sections**: #{@stats[:sections_found]}
      - **Total Code Blocks**: #{@stats[:code_blocks]}
      - **Total Examples**: #{@stats[:examples]}
      
      ## File Distribution
      
      ### By Type
      - Markdown Files: #{@markdown_files.length}
      - Ruby Files: #{@ruby_files.length}
      
      ### By Directory
      #{format_directory_stats}
      
      ## Content Analysis
      
      ### Sections per File
      - Average: #{(@stats[:sections_found].to_f / @stats[:files_processed]).round(2)}
      - Maximum: #{max_sections_per_file}
      
      ### Examples per File
      - Average: #{(@stats[:examples].to_f / @stats[:files_processed]).round(2)}
      - Maximum: #{max_examples_per_file}
      
      ## Code Analysis
      
      ### Code Blocks by Language
      #{format_language_stats}
      
      ## Cross-References
      
      - Total References: #{@cross_references.keys.length}
      - Broken Links: #{check_broken_links.length}
      
      ---
      
      *Generated by Ruby Guide Documentation Generator*
    REPORT
    
    report
  end

  def calculate_files_by_directory
    directory_stats = {}
    
    (@markdown_files + @ruby_files).each do |file|
      dir = File.dirname(file.sub(@source_dir, ''))
      directory_stats[dir] ||= 0
      directory_stats[dir] += 1
    end
    
    directory_stats
  end

  def calculate_code_by_language
    language_stats = Hash.new(0)
    
    @toc.each do |file|
      file[:code_blocks].each do |code_block|
        language_stats[code_block[:language]] += 1
      end
    end
    
    language_stats
  end

  def find_largest_file
    @toc.max_by { |file| file[:sections].length }
  end

  def max_sections_per_file
    @toc.map { |file| file[:sections].length }.max
  end

  def max_examples_per_file
    @toc.map { |file| file[:examples].length }.max
  end

  def format_directory_stats
    calculate_files_by_directory.map do |dir, count|
      "- #{dir}: #{count} files"
    end.join("\n      ")
  end

  def format_language_stats
    calculate_code_by_language.map do |lang, count|
      "- #{lang}: #{count} blocks"
    end.join("\n      ")
  end

  def check_broken_links
    broken_links = []
    
    @toc.each do |file|
      file[:references].each do |ref|
        if ref[:type] == 'internal'
          target = ref[:target]
          
          # Check if target exists in cross-references
          unless @cross_references.key?(target)
            broken_links << {
              file: file[:path],
              link: ref[:text],
              target: target
            }
          end
        end
      end
    end
    
    broken_links
  end

  def log(message)
    File.open(@log_file, 'a') do |file|
      file.puts("[#{Time.now}] #{message}")
    end
  end
end

# Main execution
if __FILE__ == $0
  generator = DocumentationGenerator.new
  generator.generate
end
