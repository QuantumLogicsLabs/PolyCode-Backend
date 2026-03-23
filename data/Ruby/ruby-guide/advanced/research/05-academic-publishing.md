# Academic Publishing in Ruby

## Overview

Academic publishing involves the dissemination of research findings through scholarly journals, conferences, and books. Ruby provides excellent tools for managing the entire publishing workflow, from manuscript preparation to citation management and journal submission.

## Manuscript Management

### Academic Paper Manager
```ruby
class AcademicPaper
  attr_reader :title, :authors, :abstract, :keywords, :sections, :references, :metadata

  def initialize(title, primary_author)
    @title = title
    @authors = [primary_author]
    @abstract = ""
    @keywords = []
    @sections = []
    @references = []
    @figures = []
    @tables = []
    @supplementary_materials = []
    @metadata = {
      created_at: Time.now,
      last_modified: Time.now,
      word_count: 0,
      status: :draft,
      journal: nil,
      submission_date: nil,
      acceptance_date: nil,
      doi: nil,
      impact_factor: nil
    }
  end

  def add_author(author, affiliation = nil, email = nil, corresponding: false)
    author_info = {
      name: author,
      affiliation: affiliation,
      email: email,
      corresponding: corresponding,
      order: @authors.length + 1
    }
    
    @authors << author_info
    puts "Added author: #{author}"
  end

  def set_abstract(abstract)
    @abstract = abstract
    update_metadata
    puts "Abstract set (#{abstract.length} characters)"
  end

  def add_keywords(keywords)
    @keywords.concat(Array(keywords)).uniq!
    puts "Added keywords: #{keywords.join(', ')}"
  end

  def add_section(title, content, level = 1)
    section = {
      title: title,
      content: content,
      level: level,
      word_count: content.split(/\s+/).length,
      created_at: Time.now
    }
    
    @sections << section
    update_metadata
    puts "Added section: #{title} (Level #{level})"
  end

  def add_figure(caption, file_path, width = nil, height = nil)
    figure = {
      id: "fig_#{@figures.length + 1}",
      caption: caption,
      file_path: file_path,
      width: width,
      height: height,
      referenced_in: []
    }
    
    @figures << figure
    puts "Added figure: #{caption}"
  end

  def add_table(caption, data, headers = nil)
    table = {
      id: "tbl_#{@tables.length + 1}",
      caption: caption,
      data: data,
      headers: headers,
      referenced_in: []
    }
    
    @tables << table
    puts "Added table: #{caption}"
  end

  def add_reference(reference, format = :apa)
    ref = {
      id: "ref_#{@references.length + 1}",
      citation: reference,
      format: format,
      referenced_in: []
    }
    
    @references << ref
    puts "Added reference: #{reference[0..50]}..."
  end

  def add_supplementary_material(description, file_path, type = :data)
    material = {
      description: description,
      file_path: file_path,
      type: type,
      size: File.size(file_path)
    }
    
    @supplementary_materials << material
    puts "Added supplementary material: #{description}"
  end

  def update_section(section_index, content)
    return false unless @sections[section_index]
    
    @sections[section_index][:content] = content
    @sections[section_index][:word_count] = content.split(/\s+/).length
    @sections[section_index][:last_modified] = Time.now
    
    update_metadata
    puts "Updated section #{section_index + 1}"
  end

  def reference_figure(figure_id, section_index)
    figure = @figures.find { |f| f[:id] == figure_id }
    return false unless figure
    
    figure[:referenced_in] << section_index
    puts "Referenced figure #{figure_id} in section #{section_index + 1}"
  end

  def reference_table(table_id, section_index)
    table = @tables.find { |t| t[:id] == table_id }
    return false unless table
    
    table[:referenced_in] << section_index
    puts "Referenced table #{table_id} in section #{section_index + 1}"
  end

  def cite_reference(reference_id, section_index)
    reference = @references.find { |r| r[:id] == reference_id }
    return false unless reference
    
    reference[:referenced_in] << section_index
    puts "Cited reference #{reference_id} in section #{section_index + 1}"
  end

  def generate_manuscript(format = :academic)
    case format
    when :academic
      generate_academic_manuscript
    when :conference
      generate_conference_paper
    when :thesis
      generate_thesis_document
    else
      generate_academic_manuscript
    end
  end

  def check_compliance(journal_requirements = {})
    compliance_report = {
      passed: [],
      failed: [],
      warnings: []
    }
    
    # Check word count
    if journal_requirements[:min_words]
      if @metadata[:word_count] >= journal_requirements[:min_words]
        compliance_report[:passed] << "Word count requirement met"
      else
        compliance_report[:failed] << "Word count too short"
      end
    end
    
    # Check abstract length
    if journal_requirements[:abstract_max_words]
      abstract_words = @abstract.split(/\s+/).length
      if abstract_words <= journal_requirements[:abstract_max_words]
        compliance_report[:passed] << "Abstract length acceptable"
      else
        compliance_report[:failed] << "Abstract too long"
      end
    end
    
    # Check keyword count
    if journal_requirements[:min_keywords]
      if @keywords.length >= journal_requirements[:min_keywords]
        compliance_report[:passed] << "Keyword count acceptable"
      else
        compliance_report[:failed] << "Insufficient keywords"
      end
    end
    
    # Check figure references
    unreferenced_figures = @figures.select { |f| f[:referenced_in].empty? }
    if unreferenced_figures.empty?
      compliance_report[:passed] << "All figures referenced"
    else
      compliance_report[:warnings] << "#{unreferenced_figures.length} figures not referenced"
    end
    
    # Check table references
    unreferenced_tables = @tables.select { |t| t[:referenced_in].empty? }
    if unreferenced_tables.empty?
      compliance_report[:passed] << "All tables referenced"
    else
      compliance_report[:warnings] << "#{unreferenced_tables.length} tables not referenced"
    end
    
    compliance_report
  end

  def export_to_latex
    latex = []
    
    # Document header
    latex << "\\documentclass{article}"
    latex << "\\usepackage{graphicx}"
    latex << "\\usepackage{cite}"
    latex << "\\usepackage{hyperref}"
    latex << "\\title{#{@title}}"
    latex << "\\author{#{@authors.map { |a| a[:name] }.join(' and ')}}"
    latex << "\\date{\\today}"
    latex << "\\begin{document}"
    latex << "\\maketitle"
    
    # Abstract
    latex << "\\begin{abstract}"
    latex << @abstract
    latex << "\\end{abstract}"
    
    # Sections
    @sections.each do |section|
      latex << "\\#{'sub' * (section[:level] - 1)}section{#{section[:title]}}"
      latex << section[:content]
    end
    
    # References
    latex << "\\bibliographystyle{plain}"
    latex << "\\bibliography{references}"
    latex << "\\end{document}"
    
    latex.join("\n")
  end

  def export_to_markdown
    markdown = []
    
    # Title and authors
    markdown << "# #{@title}"
    markdown << ""
    markdown << "**Authors:** #{@authors.map { |a| a[:name] }.join(', ')}"
    markdown << ""
    
    # Abstract
    markdown << "## Abstract"
    markdown << ""
    markdown << @abstract
    markdown << ""
    
    # Keywords
    markdown << "**Keywords:** #{@keywords.join(', ')}"
    markdown << ""
    
    # Sections
    @sections.each do |section|
      header = "#" * (section[:level] + 2) + " #{section[:title]}"
      markdown << header
      markdown << ""
      markdown << section[:content]
      markdown << ""
    end
    
    markdown.join("\n")
  end

  def calculate_metrics
    {
      total_words: @metadata[:word_count],
      sections_count: @sections.length,
      figures_count: @figures.length,
      tables_count: @tables.length,
      references_count: @references.length,
      authors_count: @authors.length,
      reading_time: estimate_reading_time,
      complexity_score: calculate_complexity_score
    }
  end

  def get_submission_status
    {
      status: @metadata[:status],
      journal: @metadata[:journal],
      submission_date: @metadata[:submission_date],
      days_under_review: @metadata[:submission_date] ? 
        ((Time.now - @metadata[:submission_date]) / 86400).to_i : nil,
      estimated_decision: estimate_decision_date
    }
  end

  private

  def update_metadata
    @metadata[:last_modified] = Time.now
    @metadata[:word_count] = @sections.sum { |s| s[:word_count] } + @abstract.split(/\s+/).length
  end

  def generate_academic_manuscript
    manuscript = []
    
    # Title page
    manuscript << @title
    manuscript << "=" * @title.length
    manuscript << ""
    
    # Authors
    @authors.each do |author|
      line = author[:name]
      line += "¹" if author[:affiliation]
      line += "*" if author[:corresponding]
      manuscript << line
    end
    manuscript << ""
    
    # Affiliations
    affiliations = @authors.map { |a| a[:affiliation] }.compact.uniq
    affiliations.each_with_index do |affiliation, i|
      manuscript << "¹#{affiliation}" if i == 0
    end
    manuscript << ""
    
    # Corresponding author
    corresponding = @authors.find { |a| a[:corresponding] }
    if corresponding
      manuscript << "*Corresponding author: #{corresponding[:email]}"
    end
    manuscript << ""
    
    # Abstract
    manuscript << "Abstract"
    manuscript << "-" * 8
    manuscript << @abstract
    manuscript << ""
    
    # Keywords
    manuscript << "Keywords: #{@keywords.join(', ')}"
    manuscript << ""
    
    # Main content
    @sections.each do |section|
      header = "#" * section[:level] + " #{section[:title]}"
      manuscript << header
      manuscript << ""
      manuscript << section[:content]
      manuscript << ""
    end
    
    # References
    if @references.any?
      manuscript << "References"
      manuscript << "-" * 10
      @references.each_with_index do |ref, i|
        manuscript << "#{i + 1}. #{ref[:citation]}"
      end
    end
    
    manuscript.join("\n")
  end

  def generate_conference_paper
    # Similar to academic manuscript but with conference-specific formatting
    generate_academic_manuscript
  end

  def generate_thesis_document
    # Thesis-specific formatting
    generate_academic_manuscript
  end

  def estimate_reading_time
    words_per_minute = 200
    (@metadata[:word_count] / words_per_minute.to_f).round(1)
  end

  def calculate_complexity_score
    factors = {
      sections: @sections.length * 10,
      figures: @figures.length * 5,
      tables: @tables.length * 5,
      references: @references.length * 2,
      authors: @authors.length * 1
    }
    
    base_score = factors.values.sum
    normalized_score = [base_score / 100.0, 10.0].min
    normalized_score.round(2)
  end

  def estimate_decision_date
    return nil unless @metadata[:submission_date]
    
    # Typical review times by journal type
    review_times = {
      high_impact: 90,    # 3 months
      medium_impact: 60,  # 2 months
      low_impact: 30      # 1 month
    }
    
    # Estimate based on impact factor
    days = if @metadata[:impact_factor] && @metadata[:impact_factor] > 5
            review_times[:high_impact]
          elsif @metadata[:impact_factor] && @metadata[:impact_factor] > 2
            review_times[:medium_impact]
          else
            review_times[:low_impact]
          end
    
    @metadata[:submission_date] + days * 86400
  end
end
```

### Journal Submission Manager
```ruby
class JournalSubmissionManager
  def initialize
    @journals = []
    @submissions = []
    @templates = {}
    @requirements = {}
  end

  def add_journal(name, details = {})
    journal = {
      name: name,
      publisher: details[:publisher],
      impact_factor: details[:impact_factor],
      issn: details[:issn],
      frequency: details[:frequency],
      acceptance_rate: details[:acceptance_rate],
      review_time: details[:review_time],
      open_access: details[:open_access] || false,
      article_processing_charge: details[:article_processing_charge],
      subject_areas: details[:subject_areas] || [],
      guidelines_url: details[:guidelines_url],
      submission_url: details[:submission_url],
      editor_in_chief: details[:editor_in_chief]
    }
    
    @journals << journal
    puts "Added journal: #{name}"
  end

  def create_submission_template(journal_name, template_data)
    template = {
      journal: journal_name,
      title_format: template_data[:title_format],
      abstract_format: template_data[:abstract_format],
      structure: template_data[:structure],
      reference_style: template_data[:reference_style],
      figure_requirements: template_data[:figure_requirements],
      table_requirements: template_data[:table_requirements],
      word_limits: template_data[:word_limits]
    }
    
    @templates[journal_name] = template
    puts "Created template for #{journal_name}"
  end

  def submit_paper(paper, journal_name, cover_letter = nil)
    journal = @journals.find { |j| j[:name] == journal_name }
    return { success: false, error: "Journal not found" } unless journal
    
    # Check compliance
    requirements = @requirements[journal_name] || {}
    compliance = paper.check_compliance(requirements)
    
    if compliance[:failed].any?
      return { 
        success: false, 
        error: "Paper does not meet requirements", 
        issues: compliance[:failed] + compliance[:warnings]
      }
    end
    
    # Create submission
    submission = {
      id: "sub_#{@submissions.length + 1}",
      paper: paper,
      journal: journal,
      cover_letter: cover_letter,
      status: :submitted,
      submitted_at: Time.now,
      decision: nil,
      decision_date: nil,
      reviewer_comments: [],
      revision_requested: false,
      revision_deadline: nil
    }
    
    @submissions << submission
    
    # Update paper metadata
    paper.metadata[:journal] = journal_name
    paper.metadata[:submission_date] = Time.now
    paper.metadata[:status] = :under_review
    
    puts "Submitted '#{paper.title}' to #{journal_name}"
    
    { success: true, submission_id: submission[:id] }
  end

  def track_submission(submission_id)
    submission = @submissions.find { |s| s[:id] == submission_id }
    return { error: "Submission not found" } unless submission
    
    status_report = {
      id: submission[:id],
      paper_title: submission[:paper].title,
      journal: submission[:journal][:name],
      status: submission[:status],
      submitted_at: submission[:submitted_at],
      days_under_review: ((Time.now - submission[:submitted_at]) / 86400).to_i,
      estimated_decision: estimate_decision_date(submission)
    }
    
    if submission[:decision]
      status_report[:decision] = submission[:decision]
      status_report[:decision_date] = submission[:decision_date]
    end
    
    if submission[:reviewer_comments].any?
      status_report[:reviewer_comments] = submission[:reviewer_comments]
    end
    
    if submission[:revision_requested]
      status_report[:revision_requested] = true
      status_report[:revision_deadline] = submission[:revision_deadline]
    end
    
    status_report
  end

  def update_submission_status(submission_id, status, details = {})
    submission = @submissions.find { |s| s[:id] == submission_id }
    return false unless submission
    
    old_status = submission[:status]
    submission[:status] = status
    
    case status
    when :under_review
      submission[:reviewer_comments] = details[:comments] || []
    when :revision_requested
      submission[:revision_requested] = true
      submission[:revision_deadline] = details[:deadline]
      submission[:paper].metadata[:status] = :revision_requested
    when :accepted
      submission[:decision] = :accepted
      submission[:decision_date] = Time.now
      submission[:paper].metadata[:status] = :accepted
      submission[:paper].metadata[:acceptance_date] = Time.now
    when :rejected
      submission[:decision] = :rejected
      submission[:decision_date] = Time.now
      submission[:paper].metadata[:status] = :rejected
    when :withdrawn
      submission[:decision] = :withdrawn
      submission[:decision_date] = Time.now
      submission[:paper].metadata[:status] = :draft
    end
    
    puts "Updated submission #{submission_id}: #{old_status} → #{status}"
    true
  end

  def find_suitable_journals(paper, criteria = {})
    suitable_journals = []
    
    @journals.each do |journal|
      score = calculate_journal_suitability(paper, journal, criteria)
      
      if score > 0.5  # Threshold for suitability
        suitable_journals << {
          journal: journal,
          suitability_score: score,
          reasons: get_suitability_reasons(paper, journal, criteria)
        }
      end
    end
    
    suitable_journals.sort_by { |j| -j[:suitability_score] }
  end

  def generate_submission_report
    report = []
    report << "Academic Publishing Report"
    report << "=" * 30
    report << ""
    
    # Summary statistics
    total_submissions = @submissions.length
    accepted = @submissions.count { |s| s[:decision] == :accepted }
    rejected = @submissions.count { |s| s[:decision] == :rejected }
    under_review = @submissions.count { |s| s[:status] == :under_review }
    
    report << "Total Submissions: #{total_submissions}"
    report << "Accepted: #{accepted} (#{total_submissions > 0 ? (accepted.to_f / total_submissions * 100).round(1) : 0}%)"
    report << "Rejected: #{rejected} (#{total_submissions > 0 ? (rejected.to_f / total_submissions * 100).round(1) : 0}%)"
    report << "Under Review: #{under_review}"
    report << ""
    
    # Journal performance
    journal_stats = {}
    @journals.each do |journal|
      submissions = @submissions.select { |s| s[:journal][:name] == journal[:name] }
      accepted_count = submissions.count { |s| s[:decision] == :accepted }
      
      if submissions.any?
        acceptance_rate = accepted_count.to_f / submissions.length
        journal_stats[journal[:name]] = {
          submissions: submissions.length,
          accepted: accepted_count,
          acceptance_rate: acceptance_rate
        }
      end
    end
    
    report << "Journal Performance:"
    journal_stats.sort_by { |_, stats| -stats[:acceptance_rate] }.each do |journal, stats|
      report << "  #{journal}: #{stats[:submissions]} submissions, #{(stats[:acceptance_rate] * 100).round(1)}% acceptance"
    end
    report << ""
    
    # Timeline analysis
    if @submissions.any?
      avg_review_time = calculate_average_review_time
      report << "Average Review Time: #{avg_review_time.round(1)} days"
    end
    
    report.join("\n")
  end

  def export_bibliography(format = :bibtex)
    bibliography = []
    
    @submissions.each do |submission|
      next unless submission[:decision] == :accepted
      
      paper = submission[:paper]
      
      case format
      when :bibtex
        entry = generate_bibtex_entry(paper, submission[:journal])
      when :apa
        entry = generate_apa_citation(paper, submission[:journal])
      else
        entry = generate_bibtex_entry(paper, submission[:journal])
      end
      
      bibliography << entry
    end
    
    bibliography.join("\n\n")
  end

  private

  def calculate_journal_suitability(paper, journal, criteria)
    score = 0.0
    
    # Subject area matching
    if paper.keywords.any? && journal[:subject_areas].any?
      matching_keywords = paper.keywords.select { |kw| 
        journal[:subject_areas].any? { |area| area.downcase.include?(kw.downcase) || kw.downcase.include?(area.downcase) }
      }
      score += 0.3 * (matching_keywords.length.to_f / paper.keywords.length)
    end
    
    # Impact factor preference
    if criteria[:impact_factor_preference]
      case criteria[:impact_factor_preference]
      when :high
        score += 0.2 if journal[:impact_factor] && journal[:impact_factor] > 5
      when :medium
        score += 0.2 if journal[:impact_factor] && journal[:impact_factor] >= 2 && journal[:impact_factor] <= 5
      when :low
        score += 0.2 if journal[:impact_factor] && journal[:impact_factor] < 2
      end
    end
    
    # Open access preference
    if criteria[:open_access] && journal[:open_access]
      score += 0.2
    end
    
    # Acceptance rate (inverse relationship - higher acceptance rate = higher suitability)
    if journal[:acceptance_rate]
      score += 0.2 * journal[:acceptance_rate]
    end
    
    # Review time preference
    if criteria[:fast_review] && journal[:review_time]
      score += 0.1 if journal[:review_time] < 60  # Less than 2 months
    end
    
    score
  end

  def get_suitability_reasons(paper, journal, criteria)
    reasons = []
    
    # Subject area matching
    if paper.keywords.any? && journal[:subject_areas].any?
      matching_keywords = paper.keywords.select { |kw| 
        journal[:subject_areas].any? { |area| area.downcase.include?(kw.downcase) }
      }
      reasons << "Subject area match: #{matching_keywords.join(', ')}" if matching_keywords.any?
    end
    
    # Impact factor
    if journal[:impact_factor]
      reasons << "Impact factor: #{journal[:impact_factor]}"
    end
    
    # Open access
    reasons << "Open access journal" if journal[:open_access]
    
    # High acceptance rate
    if journal[:acceptance_rate] && journal[:acceptance_rate] > 0.3
      reasons << "High acceptance rate (#{(journal[:acceptance_rate] * 100).round(1)}%)"
    end
    
    reasons
  end

  def estimate_decision_date(submission)
    base_days = submission[:journal][:review_time] || 60
    random_variation = (rand - 0.5) * 30  # ±15 days variation
    
    submission[:submitted_at] + (base_days + random_variation) * 86400
  end

  def calculate_average_review_time
    completed_submissions = @submissions.select { |s| s[:decision_date] }
    return 0 unless completed_submissions.any?
    
    total_days = completed_submissions.sum do |s|
      (s[:decision_date] - s[:submitted_at]) / 86400
    end
    
    total_days / completed_submissions.length
  end

  def generate_bibtex_entry(paper, journal)
    authors = paper.authors.map { |a| a[:name] }.join(' and ')
    year = Time.now.year
    title = paper.title
    
    key = "#{authors.split(' ').first}#{year}#{title.split(' ').first}".gsub(/[^a-zA-Z0-9]/, '')
    
    entry = []
    entry << "@article{#{key},"
    entry << "  title = {#{title}},"
    entry << "  author = {#{authors}},"
    entry << "  journal = {#{journal[:name]}},"
    entry << "  year = {#{year}},"
    entry << "  volume = {1},"
    entry << "  pages = {1--10},"
    entry << "  doi = {10.1000/#{key}}"
    entry << "}"
    
    entry.join("\n")
  end

  def generate_apa_citation(paper, journal)
    authors = paper.authors.map { |a| a[:name] }.join(', ')
    year = Time.now.year
    title = paper.title
    
    "#{authors} (#{year}). #{title}. *#{journal[:name]}*, 1(1), 1-10. https://doi.org/10.1000/example"
  end
end
```

## Citation Management

### Bibliography Manager
```ruby
class BibliographyManager
  def initialize
    @references = []
    @categories = {}
    @tags = {}
    @notes = {}
    @search_index = {}
  end

  def add_reference(source_type, details)
    reference = {
      id: generate_id,
      type: source_type,
      details: details,
      added_at: Time.now,
      last_accessed: Time.now,
      access_count: 0,
      tags: [],
      notes: [],
      citations: [],
      related_references: []
    }
    
    # Validate required fields based on source type
    case source_type
    when :journal_article
      validate_journal_article(reference)
    when :book
      validate_book(reference)
    when :conference_paper
      validate_conference_paper(reference)
    when :thesis
      validate_thesis(reference)
    end
    
    @references << reference
    index_reference(reference)
    
    puts "Added #{source_type}: #{details[:title] || 'Untitled'}"
    reference[:id]
  end

  def search_references(query, filters = {})
    results = []
    
    @references.each do |ref|
      matches = true
      
      # Text search
      if query && !query.empty?
        searchable_text = build_searchable_text(ref)
        matches = false unless searchable_text.downcase.include?(query.downcase)
      end
      
      # Apply filters
      if filters[:type]
        matches = false unless ref[:type] == filters[:type]
      end
      
      if filters[:year]
        matches = false unless ref[:details][:year] == filters[:year]
      end
      
      if filters[:author]
        matches = false unless ref[:details][:authors]&.any? { |author| 
          author.downcase.include?(filters[:author].downcase) 
        }
      end
      
      if filters[:tags]
        matches = false unless (ref[:tags] & filters[:tags]).any?
      end
      
      results << ref if matches
    end
    
    # Sort by relevance (simple implementation)
    results.sort_by { |ref| -ref[:access_count] }
  end

  def add_tag(reference_id, tag)
    reference = find_reference(reference_id)
    return false unless reference
    
    reference[:tags] << tag unless reference[:tags].include?(tag)
    @tags[tag] ||= []
    @tags[tag] << reference_id unless @tags[tag].include?(reference_id)
    
    puts "Added tag '#{tag}' to reference #{reference_id}"
  end

  def add_note(reference_id, note)
    reference = find_reference(reference_id)
    return false unless reference
    
    note_entry = {
      content: note,
      created_at: Time.now,
      id: generate_note_id
    }
    
    reference[:notes] << note_entry
    puts "Added note to reference #{reference_id}"
  end

  def record_citation(reference_id, context = nil)
    reference = find_reference(reference_id)
    return false unless reference
    
    citation = {
      date: Time.now,
      context: context,
      id: generate_citation_id
    }
    
    reference[:citations] << citation
    reference[:access_count] += 1
    reference[:last_accessed] = Time.now
    
    puts "Recorded citation for reference #{reference_id}"
  end

  def find_duplicates(threshold = 0.8)
    duplicates = []
    
    @references.combination(2).each do |ref1, ref2|
      similarity = calculate_similarity(ref1, ref2)
      
      if similarity >= threshold
        duplicates << {
          reference1: ref1[:id],
          reference2: ref2[:id],
          similarity: similarity,
          reason: get_similarity_reason(ref1, ref2)
        }
      end
    end
    
    duplicates.sort_by { |dup| -dup[:similarity] }
  end

  def export_bibliography(format = :apa, filters = {})
    references = filters.any? ? search_references(nil, filters) : @references
    
    case format
    when :apa
      export_apa_format(references)
    when :mla
      export_mla_format(references)
    when :chicago
      export_chicago_format(references)
    when :bibtex
      export_bibtex_format(references)
    else
      export_apa_format(references)
    end
  end

  def generate_statistics
    stats = {
      total_references: @references.length,
      by_type: Hash.new(0),
      by_year: Hash.new(0),
      by_author: Hash.new(0),
      most_accessed: @references.max_by { |ref| ref[:access_count] },
      recently_added: @references.max_by { |ref| ref[:added_at] },
      tag_distribution: Hash.new(0),
      average_citations: 0
    }
    
    @references.each do |ref|
      # By type
      stats[:by_type][ref[:type]] += 1
      
      # By year
      if ref[:details][:year]
        stats[:by_year][ref[:details][:year]] += 1
      end
      
      # By author
      if ref[:details][:authors]
        ref[:details][:authors].each do |author|
          stats[:by_author][author] += 1
        end
      end
      
      # Tag distribution
      ref[:tags].each { |tag| stats[:tag_distribution][tag] += 1 }
    end
    
    # Average citations
    total_citations = @references.sum { |ref| ref[:citations].length }
    stats[:average_citations] = total_citations.to_f / @references.length
    
    stats
  end

  def backup_bibliography(filename)
    backup_data = {
      references: @references,
      categories: @categories,
      tags: @tags,
      notes: @notes,
      exported_at: Time.now
    }
    
    File.write(filename, JSON.pretty_generate(backup_data))
    puts "Bibliography backed up to #{filename}"
  end

  def restore_bibliography(filename)
    return false unless File.exist?(filename)
    
    backup_data = JSON.parse(File.read(filename))
    
    @references = backup_data['references'] || []
    @categories = backup_data['categories'] || {}
    @tags = backup_data['tags'] || {}
    @notes = backup_data['notes'] || {}
    
    # Rebuild search index
    @references.each { |ref| index_reference(ref) }
    
    puts "Bibliography restored from #{filename}"
    true
  end

  private

  def generate_id
    "ref_#{@references.length + 1}_#{Time.now.to_i}"
  end

  def generate_note_id
    "note_#{Time.now.to_i}_#{rand(1000)}"
  end

  def generate_citation_id
    "cite_#{Time.now.to_i}_#{rand(1000)}"
  end

  def find_reference(id)
    @references.find { |ref| ref[:id] == id }
  end

  def validate_journal_article(reference)
    required_fields = [:title, :authors, :journal, :year, :volume, :pages]
    missing_fields = required_fields.reject { |field| reference[:details][field] }
    
    if missing_fields.any?
      raise "Missing required fields for journal article: #{missing_fields.join(', ')}"
    end
  end

  def validate_book(reference)
    required_fields = [:title, :authors, :publisher, :year]
    missing_fields = required_fields.reject { |field| reference[:details][field] }
    
    if missing_fields.any?
      raise "Missing required fields for book: #{missing_fields.join(', ')}"
    end
  end

  def validate_conference_paper(reference)
    required_fields = [:title, :authors, :conference, :year]
    missing_fields = required_fields.reject { |field| reference[:details][field] }
    
    if missing_fields.any?
      raise "Missing required fields for conference paper: #{missing_fields.join(', ')}"
    end
  end

  def validate_thesis(reference)
    required_fields = [:title, :author, :institution, :year]
    missing_fields = required_fields.reject { |field| reference[:details][field] }
    
    if missing_fields.any?
      raise "Missing required fields for thesis: #{missing_fields.join(', ')}"
    end
  end

  def index_reference(reference)
    searchable_text = build_searchable_text(reference)
    
    # Simple word-based indexing
    searchable_text.downcase.split(/\s+/).each do |word|
      @search_index[word] ||= []
      @search_index[word] << reference[:id]
    end
  end

  def build_searchable_text(reference)
    text_parts = []
    
    text_parts << reference[:details][:title] if reference[:details][:title]
    text_parts << reference[:details][:authors]&.join(' ') if reference[:details][:authors]
    text_parts << reference[:details][:journal] if reference[:details][:journal]
    text_parts << reference[:details][:conference] if reference[:details][:conference]
    text_parts << reference[:details][:publisher] if reference[:details][:publisher]
    text_parts << reference[:details][:abstract] if reference[:details][:abstract]
    text_parts << reference[:tags].join(' ')
    text_parts << reference[:notes].map { |note| note[:content] }.join(' ')
    
    text_parts.compact.join(' ')
  end

  def calculate_similarity(ref1, ref2)
    # Simple similarity calculation based on title and authors
    similarity = 0.0
    
    # Title similarity
    if ref1[:details][:title] && ref2[:details][:title]
      title1 = ref1[:details][:title].downcase
      title2 = ref2[:details][:title].downcase
      title_similarity = jaccard_similarity(title1.split(/\s+/), title2.split(/\s+/))
      similarity += 0.6 * title_similarity
    end
    
    # Author similarity
    if ref1[:details][:authors] && ref2[:details][:authors]
      authors1 = ref1[:details][:authors].map(&:downcase)
      authors2 = ref2[:details][:authors].map(&:downcase)
      author_similarity = jaccard_similarity(authors1, authors2)
      similarity += 0.4 * author_similarity
    end
    
    similarity
  end

  def jaccard_similarity(set1, set2)
    intersection = set1 & set2
    union = set1 | set2
    return 1.0 if union.empty?
    intersection.length.to_f / union.length
  end

  def get_similarity_reason(ref1, ref2)
    reasons = []
    
    if ref1[:details][:title] && ref2[:details][:title]
      title1 = ref1[:details][:title].downcase
      title2 = ref2[:details][:title].downcase
      
      if title1.include?(title2[0..10]) || title2.include?(title1[0..10])
        reasons << "Similar titles"
      end
    end
    
    if ref1[:details][:authors] && ref2[:details][:authors]
      authors1 = ref1[:details][:authors].map(&:downcase)
      authors2 = ref2[:details][:authors].map(&:downcase)
      
      shared_authors = authors1 & authors2
      if shared_authors.any?
        reasons << "Shared authors: #{shared_authors.join(', ')}"
      end
    end
    
    reasons.join('; ')
  end

  def export_apa_format(references)
    bibliography = []
    
    references.each do |ref|
      citation = case ref[:type]
                when :journal_article
                  format_apa_journal_article(ref)
                when :book
                  format_apa_book(ref)
                when :conference_paper
                  format_apa_conference_paper(ref)
                when :thesis
                  format_apa_thesis(ref)
                else
                  "Unknown reference type"
                end
      
      bibliography << citation
    end
    
    bibliography
  end

  def format_apa_journal_article(ref)
    details = ref[:details]
    authors = format_authors(details[:authors])
    year = details[:year]
    title = details[:title]
    journal = details[:journal]
    volume = details[:volume]
    pages = details[:pages]
    
    "#{authors} (#{year}). #{title}. *#{journal}*, #{volume}, #{pages}."
  end

  def format_apa_book(ref)
    details = ref[:details]
    authors = format_authors(details[:authors])
    year = details[:year]
    title = details[:title]
    publisher = details[:publisher]
    
    "#{authors} (#{year}). *#{title}*. #{publisher}."
  end

  def format_apa_conference_paper(ref)
    details = ref[:details]
    authors = format_authors(details[:authors])
    year = details[:year]
    title = details[:title]
    conference = details[:conference]
    
    "#{authors} (#{year}). #{title}. *Proceedings of #{conference}*."
  end

  def format_apa_thesis(ref)
    details = ref[:details]
    author = details[:author]
    year = details[:year]
    title = details[:title]
    institution = details[:institution]
    
    "#{author} (#{year}). *#{title}* [Doctoral dissertation, #{institution}]."
  end

  def format_authors(authors)
    return '' unless authors
    
    case authors.length
    when 1
      authors.first
    when 2
      authors.join(' & ')
    when 3..7
      "#{authors[0...-1].join(', ')}, & #{authors.last}"
    else
      "#{authors.first} et al."
    end
  end

  def export_mla_format(references)
    # MLA formatting implementation
    references.map { |ref| "MLA format for #{ref[:id]}" }
  end

  def export_chicago_format(references)
    # Chicago formatting implementation
    references.map { |ref| "Chicago format for #{ref[:id]}" }
  end

  def export_bibtex_format(references)
    bibtex = []
    
    references.each do |ref|
      key = generate_bibtex_key(ref)
      entry = generate_bibtex_entry(ref, key)
      bibtex << entry
    end
    
    bibtex
  end

  def generate_bibtex_key(ref)
    details = ref[:details]
    
    author_part = case ref[:type]
                 when :book, :journal_article
                   details[:authors]&.first&.split(' ')&.first
                 when :thesis
                   details[:author]&.split(' ')&.first
                 else
                   'unknown'
                 end
    
    year_part = details[:year] || 'n.d.'
    title_part = details[:title]&.split(' ')&.first&.gsub(/[^a-zA-Z0-9]/, '') || 'unknown'
    
    "#{author_part}#{year_part}#{title_part}"
  end

  def generate_bibtex_entry(ref, key)
    details = ref[:details]
    
    case ref[:type]
    when :journal_article
      "@article{#{key},\n  title = {#{details[:title]}},\n  author = {#{format_bibtex_authors(details[:authors])}},\n  journal = {#{details[:journal]}},\n  year = {#{details[:year]}},\n  volume = {#{details[:volume]}},\n  pages = {#{details[:pages]}}\n}"
    when :book
      "@book{#{key},\n  title = {#{details[:title]}},\n  author = {#{format_bibtex_authors(details[:authors])}},\n  publisher = {#{details[:publisher]}},\n  year = {#{details[:year]}}\n}"
    else
      "@misc{#{key},\n  title = {#{details[:title]}}\n}"
    end
  end

  def format_bibtex_authors(authors)
    return '' unless authors
    
    authors.map { |author| author.gsub(' ', ' and ') }.join(' and ')
  end
end
```

## Best Practices

1. **Journal Selection**: Choose appropriate journals based on scope, impact factor, and audience
2. **Compliance**: Follow journal guidelines strictly for formatting and submission
3. **Citation Management**: Use proper citation styles and maintain accurate bibliographies
4. **Version Control**: Keep track of manuscript versions and revisions
5. **Peer Review**: Respond constructively to reviewer feedback
6. **Ethical Publishing**: Avoid plagiarism, duplicate publication, and data fabrication
7. **Open Science**: Consider preprints, data sharing, and open access options

## Conclusion

Academic publishing is a complex process that requires careful attention to detail, proper formatting, and strategic journal selection. Ruby provides excellent tools for managing manuscripts, citations, and the entire publishing workflow, making it easier for researchers to focus on their research while ensuring compliance with publishing standards.

## Further Reading

- [Publication Ethics](https://publicationethics.org/)
- [Journal Citation Reports](https://jcr.clarivate.com/)
- [Open Access Directory](https://doaj.org/)
- [ORCID](https://orcid.org/)
- [CrossRef](https://www.crossref.org/)
