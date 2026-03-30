"""
Digital Forensics File Analyzer
==============================

Comprehensive file analysis tool for digital forensics investigations.
Demonstrates file metadata analysis, content examination, and forensic techniques.
"""

import os
import hashlib
import time
import json
import magic
import exifread
from typing import Dict, List, Optional, Tuple
from datetime import datetime
from dataclasses import dataclass, asdict
import hashlib
import struct
import mimetypes
import stat

try:
    import pytsk3
    TSK_AVAILABLE = True
except ImportError:
    print("Warning: pytsk3 not available. Some forensic features will be limited.")
    TSK_AVAILABLE = False

@dataclass
class FileMetadata:
    """File metadata structure"""
    name: str
    path: str
    size: int
    created_time: datetime
    modified_time: datetime
    accessed_time: datetime
    file_type: str
    mime_type: str
    permissions: str
    owner: Optional[str] = None
    group: Optional[str] = None
    inode: Optional[int] = None
    device: Optional[int] = None
    checksums: Dict[str, str] = None
    
    def to_dict(self) -> Dict:
        """Convert to dictionary for JSON serialization"""
        data = asdict(self)
        data['created_time'] = self.created_time.isoformat()
        data['modified_time'] = self.modified_time.isoformat()
        data['accessed_time'] = self.accessed_time.isoformat()
        return data

@dataclass
class ContentAnalysis:
    """Content analysis results"""
    file_type: str
    encoding: Optional[str]
    language: Optional[str]
    strings_found: List[str]
    suspicious_patterns: List[str]
    personal_info: List[str]
    urls: List[str]
    email_addresses: List[str]
    phone_numbers: List[str]
    credit_cards: List[str]
    keywords: List[str]

class FileAnalyzer:
    """Digital forensics file analyzer"""
    
    def __init__(self):
        self.suspicious_patterns = [
            r'password',
            r'password123',
            r'admin',
            r'root',
            r'secret',
            r'confidential',
            r'classified',
            r'private key',
            r'BEGIN.*PRIVATE KEY',
            r'BEGIN.*CERTIFICATE',
            r'eval\(',
            r'system\(',
            r'shell_exec',
            r'base64_decode',
            r'javascript:',
            r'<script',
            r'exec\(',
            r'cmd\.exe',
            r'powershell',
            r'bash',
            r'sh\.',
            r'\\x[0-9a-fA-F]{2}',
        ]
        
        self.personal_info_patterns = {
            'ssn': r'\b\d{3}-\d{2}-\d{4}\b',
            'credit_card': r'\b\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}\b',
            'email': r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b',
            'phone': r'\b\d{3}[-.\s]?\d{3}[-.\s]?\d{4}\b',
            'ip_address': r'\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b',
            'url': r'https?://[^\s<>"{}|\\^`\[\]]+',
        }
        
        self.forensic_keywords = [
            'hack', 'exploit', 'vulnerability', 'backdoor', 'malware', 'virus',
            'trojan', 'rootkit', 'botnet', 'phishing', 'spam', 'fraud',
            'illegal', 'criminal', 'stolen', 'leaked', 'breach', 'attack',
            'weapon', 'bomb', 'terror', 'drugs', 'money laundering'
        ]
    
    def get_file_metadata(self, file_path: str) -> FileMetadata:
        """Extract comprehensive file metadata"""
        try:
            stat_info = os.stat(file_path)
            
            # Basic file information
            name = os.path.basename(file_path)
            path = os.path.abspath(file_path)
            size = stat_info.st_size
            
            # Timestamps
            created_time = datetime.fromtimestamp(stat_info.st_ctime)
            modified_time = datetime.fromtimestamp(stat_info.st_mtime)
            accessed_time = datetime.fromtimestamp(stat_info.st_atime)
            
            # File type and permissions
            file_type = 'file' if os.path.isfile(file_path) else 'directory'
            mime_type = mimetypes.guess_type(file_path)[0] or 'unknown'
            permissions = oct(stat_info.st_mode)[-3:]
            
            # Unix-specific metadata
            owner = None
            group = None
            inode = None
            device = None
            
            try:
                import pwd
                import grp
                owner = pwd.getpwuid(stat_info.st_uid).pw_name
                group = grp.getgrgid(stat_info.st_gid).gr_name
            except (ImportError, KeyError):
                pass
            
            inode = stat_info.st_ino
            device = stat_info.st_dev
            
            # Calculate checksums
            checksums = self.calculate_checksums(file_path)
            
            return FileMetadata(
                name=name,
                path=path,
                size=size,
                created_time=created_time,
                modified_time=modified_time,
                accessed_time=accessed_time,
                file_type=file_type,
                mime_type=mime_type,
                permissions=permissions,
                owner=owner,
                group=group,
                inode=inode,
                device=device,
                checksums=checksums
            )
            
        except Exception as e:
            print(f"Error getting metadata for {file_path}: {e}")
            return None
    
    def calculate_checksums(self, file_path: str) -> Dict[str, str]:
        """Calculate multiple file checksums"""
        checksums = {}
        
        try:
            with open(file_path, 'rb') as f:
                content = f.read()
                
                # MD5
                checksums['md5'] = hashlib.md5(content).hexdigest()
                
                # SHA-1
                checksums['sha1'] = hashlib.sha1(content).hexdigest()
                
                # SHA-256
                checksums['sha256'] = hashlib.sha256(content).hexdigest()
                
                # CRC32
                checksums['crc32'] = format(hashlib.new('crc32', content).hexdigest(), '08x')
        
        except Exception as e:
            print(f"Error calculating checksums for {file_path}: {e}")
        
        return checksums
    
    def analyze_file_content(self, file_path: str, max_size: int = 1024*1024) -> ContentAnalysis:
        """Analyze file content for forensic evidence"""
        try:
            # Determine file type
            file_type = self.detect_file_type(file_path)
            
            # Read file content
            content = self.read_file_content(file_path, max_size)
            
            if content is None:
                return ContentAnalysis(
                    file_type=file_type,
                    encoding=None,
                    language=None,
                    strings_found=[],
                    suspicious_patterns=[],
                    personal_info=[],
                    urls=[],
                    email_addresses=[],
                    phone_numbers=[],
                    credit_cards=[],
                    keywords=[]
                )
            
            # Detect encoding
            encoding = self.detect_encoding(content)
            
            # Detect programming language
            language = self.detect_programming_language(file_path, content)
            
            # Extract strings
            strings_found = self.extract_strings(content)
            
            # Find suspicious patterns
            suspicious_patterns = self.find_suspicious_patterns(content)
            
            # Find personal information
            personal_info = self.find_personal_information(content)
            
            # Extract URLs
            urls = self.extract_urls(content)
            
            # Extract email addresses
            email_addresses = self.extract_email_addresses(content)
            
            # Extract phone numbers
            phone_numbers = self.extract_phone_numbers(content)
            
            # Extract credit card numbers
            credit_cards = self.extract_credit_cards(content)
            
            # Find forensic keywords
            keywords = self.find_keywords(content)
            
            return ContentAnalysis(
                file_type=file_type,
                encoding=encoding,
                language=language,
                strings_found=strings_found,
                suspicious_patterns=suspicious_patterns,
                personal_info=personal_info,
                urls=urls,
                email_addresses=email_addresses,
                phone_numbers=phone_numbers,
                credit_cards=credit_cards,
                keywords=keywords
            )
            
        except Exception as e:
            print(f"Error analyzing content of {file_path}: {e}")
            return None
    
    def detect_file_type(self, file_path: str) -> str:
        """Detect file type using magic numbers"""
        try:
            # Use python-magic if available
            mime = magic.Magic(mime=True)
            file_type = mime.from_file(file_path)
            return file_type
        except:
            # Fallback to extension-based detection
            ext = os.path.splitext(file_path)[1].lower()
            type_map = {
                '.txt': 'text/plain',
                '.pdf': 'application/pdf',
                '.doc': 'application/msword',
                '.docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                '.jpg': 'image/jpeg',
                '.jpeg': 'image/jpeg',
                '.png': 'image/png',
                '.gif': 'image/gif',
                '.mp3': 'audio/mpeg',
                '.mp4': 'video/mp4',
                '.zip': 'application/zip',
                '.exe': 'application/x-msdownload',
                '.dll': 'application/x-msdownload',
                '.py': 'text/x-python',
                '.js': 'application/javascript',
                '.html': 'text/html',
                '.css': 'text/css'
            }
            return type_map.get(ext, 'application/octet-stream')
    
    def read_file_content(self, file_path: str, max_size: int) -> Optional[bytes]:
        """Read file content with size limit"""
        try:
            with open(file_path, 'rb') as f:
                return f.read(max_size)
        except Exception as e:
            print(f"Error reading file {file_path}: {e}")
            return None
    
    def detect_encoding(self, content: bytes) -> Optional[str]:
        """Detect text encoding"""
        try:
            import chardet
            result = chardet.detect(content)
            return result['encoding']
        except ImportError:
            # Fallback: try common encodings
            encodings = ['utf-8', 'ascii', 'latin-1', 'utf-16']
            for encoding in encodings:
                try:
                    content.decode(encoding)
                    return encoding
                except UnicodeDecodeError:
                    continue
            return None
    
    def detect_programming_language(self, file_path: str, content: bytes) -> Optional[str]:
        """Detect programming language"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            
            # Check file extension first
            ext = os.path.splitext(file_path)[1].lower()
            lang_map = {
                '.py': 'Python',
                '.js': 'JavaScript',
                '.java': 'Java',
                '.cpp': 'C++',
                '.c': 'C',
                '.cs': 'C#',
                '.php': 'PHP',
                '.rb': 'Ruby',
                '.go': 'Go',
                '.rs': 'Rust',
                '.swift': 'Swift',
                '.kt': 'Kotlin',
                '.html': 'HTML',
                '.css': 'CSS',
                '.sql': 'SQL',
                '.sh': 'Shell',
                '.bat': 'Batch',
                '.ps1': 'PowerShell'
            }
            
            if ext in lang_map:
                return lang_map[ext]
            
            # Check content-based detection
            if 'def ' in content_str or 'import ' in content_str:
                return 'Python'
            elif 'function ' in content_str or 'var ' in content_str:
                return 'JavaScript'
            elif 'public class ' in content_str:
                return 'Java'
            elif '#include' in content_str:
                return 'C/C++'
            elif '<?php' in content_str:
                return 'PHP'
            
            return None
            
        except Exception:
            return None
    
    def extract_strings(self, content: bytes, min_length: int = 4) -> List[str]:
        """Extract printable strings from binary content"""
        strings = []
        current_string = ""
        
        for byte in content:
            if 32 <= byte <= 126:  # Printable ASCII
                current_string += chr(byte)
            else:
                if len(current_string) >= min_length:
                    strings.append(current_string)
                current_string = ""
        
        # Add last string if it meets criteria
        if len(current_string) >= min_length:
            strings.append(current_string)
        
        return strings[:100]  # Limit to first 100 strings
    
    def find_suspicious_patterns(self, content: bytes) -> List[str]:
        """Find suspicious patterns in content"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            patterns_found = []
            
            for pattern in self.suspicious_patterns:
                matches = re.findall(pattern, content_str, re.IGNORECASE)
                if matches:
                    patterns_found.extend(matches[:5])  # Limit matches per pattern
            
            return patterns_found[:20]  # Limit total patterns
            
        except Exception:
            return []
    
    def find_personal_information(self, content: bytes) -> List[str]:
        """Find personal information in content"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            personal_info = []
            
            for info_type, pattern in self.personal_info_patterns.items():
                matches = re.findall(pattern, content_str)
                if matches:
                    personal_info.extend(matches)
            
            return personal_info[:10]  # Limit results
            
        except Exception:
            return []
    
    def extract_urls(self, content: bytes) -> List[str]:
        """Extract URLs from content"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            url_pattern = r'https?://[^\s<>"{}|\\^`\[\]]+'
            urls = re.findall(url_pattern, content_str, re.IGNORECASE)
            return list(set(urls))[:20]  # Remove duplicates and limit
            
        except Exception:
            return []
    
    def extract_email_addresses(self, content: bytes) -> List[str]:
        """Extract email addresses from content"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            email_pattern = r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'
            emails = re.findall(email_pattern, content_str)
            return list(set(emails))[:20]  # Remove duplicates and limit
            
        except Exception:
            return []
    
    def extract_phone_numbers(self, content: bytes) -> List[str]:
        """Extract phone numbers from content"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            phone_pattern = r'\b\d{3}[-.\s]?\d{3}[-.\s]?\d{4}\b'
            phones = re.findall(phone_pattern, content_str)
            return list(set(phones))[:10]  # Remove duplicates and limit
            
        except Exception:
            return []
    
    def extract_credit_cards(self, content: bytes) -> List[str]:
        """Extract potential credit card numbers"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            card_pattern = r'\b\d{4}[-\s]?\d{4}[-\s]?\d{4}[-\s]?\d{4}\b'
            cards = re.findall(card_pattern, content_str)
            
            # Basic Luhn algorithm validation
            valid_cards = []
            for card in cards:
                if self.validate_luhn(card.replace('-', '').replace(' ', '')):
                    valid_cards.append(card)
            
            return valid_cards[:5]  # Limit results
            
        except Exception:
            return []
    
    def validate_luhn(self, card_number: str) -> bool:
        """Validate credit card number using Luhn algorithm"""
        try:
            total = 0
            reverse_digits = card_number[::-1]
            
            for i, digit in enumerate(reverse_digits):
                n = int(digit)
                if i % 2 == 1:
                    n *= 2
                    if n > 9:
                        n -= 9
                total += n
            
            return total % 10 == 0
            
        except Exception:
            return False
    
    def find_keywords(self, content: bytes) -> List[str]:
        """Find forensic keywords in content"""
        try:
            content_str = content.decode('utf-8', errors='ignore')
            keywords_found = []
            
            for keyword in self.forensic_keywords:
                if keyword.lower() in content_str.lower():
                    keywords_found.append(keyword)
            
            return keywords_found
            
        except Exception:
            return []
    
    def analyze_exif_data(self, file_path: str) -> Dict:
        """Extract EXIF metadata from image files"""
        try:
            with open(file_path, 'rb') as f:
                tags = exifread.process_file(f)
                
            exif_data = {}
            for tag, value in tags.items():
                if not tag.startswith('JPEGThumbnail'):
                    exif_data[tag] = str(value)
            
            return exif_data
            
        except Exception as e:
            print(f"Error reading EXIF data from {file_path}: {e}")
            return {}
    
    def analyze_directory(self, directory_path: str, recursive: bool = True) -> Dict:
        """Analyze all files in a directory"""
        results = {
            'directory': directory_path,
            'files_analyzed': 0,
            'total_size': 0,
            'file_types': {},
            'suspicious_files': [],
            'files_with_personal_info': [],
            'files_with_keywords': [],
            'analysis_time': datetime.now().isoformat()
        }
        
        try:
            for root, dirs, files in os.walk(directory_path):
                for file_name in files:
                    file_path = os.path.join(root, file_name)
                    
                    # Analyze file
                    metadata = self.get_file_metadata(file_path)
                    if metadata:
                        content_analysis = self.analyze_file_content(file_path)
                        
                        results['files_analyzed'] += 1
                        results['total_size'] += metadata.size
                        
                        # Track file types
                        file_type = metadata.mime_type
                        results['file_types'][file_type] = results['file_types'].get(file_type, 0) + 1
                        
                        # Check for suspicious content
                        if content_analysis and content_analysis.suspicious_patterns:
                            results['suspicious_files'].append({
                                'file': file_path,
                                'patterns': content_analysis.suspicious_patterns[:5]
                            })
                        
                        # Check for personal information
                        if content_analysis and content_analysis.personal_info:
                            results['files_with_personal_info'].append({
                                'file': file_path,
                                'info': content_analysis.personal_info[:5]
                            })
                        
                        # Check for keywords
                        if content_analysis and content_analysis.keywords:
                            results['files_with_keywords'].append({
                                'file': file_path,
                                'keywords': content_analysis.keywords
                            })
                
                # Break if not recursive
                if not recursive:
                    break
        
        except Exception as e:
            print(f"Error analyzing directory {directory_path}: {e}")
        
        return results
    
    def generate_forensic_report(self, file_path: str) -> str:
        """Generate comprehensive forensic report"""
        metadata = self.get_file_metadata(file_path)
        content_analysis = self.analyze_file_content(file_path)
        
        if not metadata:
            return f"Error: Unable to analyze file {file_path}"
        
        report = []
        report.append("=" * 60)
        report.append("DIGITAL FORENSICS ANALYSIS REPORT")
        report.append("=" * 60)
        report.append(f"Analysis Date: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        report.append(f"File: {metadata.path}")
        report.append("")
        
        # File Metadata
        report.append("FILE METADATA:")
        report.append("-" * 40)
        report.append(f"Name: {metadata.name}")
        report.append(f"Size: {metadata.size} bytes")
        report.append(f"Type: {metadata.file_type}")
        report.append(f"MIME Type: {metadata.mime_type}")
        report.append(f"Permissions: {metadata.permissions}")
        
        if metadata.owner:
            report.append(f"Owner: {metadata.owner}")
        if metadata.group:
            report.append(f"Group: {metadata.group}")
        
        report.append(f"Created: {metadata.created_time}")
        report.append(f"Modified: {metadata.modified_time}")
        report.append(f"Accessed: {metadata.accessed_time}")
        
        if metadata.inode:
            report.append(f"Inode: {metadata.inode}")
        
        # Checksums
        if metadata.checksums:
            report.append("\nCHECKSUMS:")
            report.append("-" * 40)
            for algo, checksum in metadata.checksums.items():
                report.append(f"{algo.upper()}: {checksum}")
        
        # Content Analysis
        if content_analysis:
            report.append("\nCONTENT ANALYSIS:")
            report.append("-" * 40)
            report.append(f"File Type: {content_analysis.file_type}")
            
            if content_analysis.encoding:
                report.append(f"Encoding: {content_analysis.encoding}")
            
            if content_analysis.language:
                report.append(f"Language: {content_analysis.language}")
            
            # Strings found
            if content_analysis.strings_found:
                report.append(f"\nStrings Found ({len(content_analysis.strings_found)}):")
                for string in content_analysis.strings_found[:10]:
                    report.append(f"  {string}")
            
            # Suspicious patterns
            if content_analysis.suspicious_patterns:
                report.append(f"\n⚠️  SUSPICIOUS PATTERNS FOUND:")
                for pattern in content_analysis.suspicious_patterns:
                    report.append(f"  - {pattern}")
            
            # Personal information
            if content_analysis.personal_info:
                report.append(f"\n🔒 PERSONAL INFORMATION FOUND:")
                for info in content_analysis.personal_info:
                    report.append(f"  - {info}")
            
            # URLs
            if content_analysis.urls:
                report.append(f"\n🌐 URLS FOUND:")
                for url in content_analysis.urls[:5]:
                    report.append(f"  - {url}")
            
            # Email addresses
            if content_analysis.email_addresses:
                report.append(f"\n📧 EMAIL ADDRESSES FOUND:")
                for email in content_analysis.email_addresses[:5]:
                    report.append(f"  - {email}")
            
            # Phone numbers
            if content_analysis.phone_numbers:
                report.append(f"\n📞 PHONE NUMBERS FOUND:")
                for phone in content_analysis.phone_numbers:
                    report.append(f"  - {phone}")
            
            # Credit cards
            if content_analysis.credit_cards:
                report.append(f"\n💳 CREDIT CARD NUMBERS FOUND:")
                for card in content_analysis.credit_cards:
                    report.append(f"  - {card}")
            
            # Keywords
            if content_analysis.keywords:
                report.append(f"\n🔍 FORENSIC KEYWORDS FOUND:")
                for keyword in content_analysis.keywords:
                    report.append(f"  - {keyword}")
        
        # Security Assessment
        report.append("\nSECURITY ASSESSMENT:")
        report.append("-" * 40)
        
        risk_level = "LOW"
        risk_factors = []
        
        if content_analysis and content_analysis.suspicious_patterns:
            risk_level = "HIGH"
            risk_factors.append("Suspicious patterns detected")
        
        if content_analysis and content_analysis.personal_info:
            if risk_level == "LOW":
                risk_level = "MEDIUM"
            risk_factors.append("Personal information present")
        
        if content_analysis and content_analysis.keywords:
            if risk_level == "LOW":
                risk_level = "MEDIUM"
            risk_factors.append("Forensic keywords found")
        
        report.append(f"Risk Level: {risk_level}")
        
        if risk_factors:
            report.append("Risk Factors:")
            for factor in risk_factors:
                report.append(f"  - {factor}")
        else:
            report.append("No significant security risks detected")
        
        return "\n".join(report)
    
    def save_analysis(self, file_path: str, output_file: str):
        """Save analysis results to file"""
        report = self.generate_forensic_report(file_path)
        
        with open(output_file, 'w') as f:
            f.write(report)
        
        print(f"Analysis report saved to: {output_file}")

def main():
    """Main function to demonstrate file analyzer"""
    print("=== Digital Forensics File Analyzer ===\n")
    
    analyzer = FileAnalyzer()
    
    print("Choose analysis mode:")
    print("1. Analyze single file")
    print("2. Analyze directory")
    print("3. Create test files and analyze")
    print("4. Batch analyze multiple files")
    
    choice = input("Select mode (1-4): ").strip()
    
    if choice == "1":
        # Single file analysis
        file_path = input("Enter file path to analyze: ").strip()
        
        if os.path.exists(file_path):
            print(f"\nAnalyzing file: {file_path}")
            report = analyzer.generate_forensic_report(file_path)
            print("\n" + report)
            
            # Save report
            output_file = f"{os.path.splitext(file_path)[0]}_forensic_report.txt"
            analyzer.save_analysis(file_path, output_file)
        else:
            print(f"File not found: {file_path}")
    
    elif choice == "2":
        # Directory analysis
        directory_path = input("Enter directory path to analyze: ").strip()
        
        if os.path.isdir(directory_path):
            recursive = input("Analyze recursively? (y/n): ").strip().lower() == 'y'
            
            print(f"\nAnalyzing directory: {directory_path}")
            results = analyzer.analyze_directory(directory_path, recursive)
            
            print(f"\nDirectory Analysis Results:")
            print(f"Files analyzed: {results['files_analyzed']}")
            print(f"Total size: {results['total_size']} bytes")
            print(f"Suspicious files: {len(results['suspicious_files'])}")
            print(f"Files with personal info: {len(results['files_with_personal_info'])}")
            print(f"Files with keywords: {len(results['files_with_keywords'])}")
            
            # Show file types
            print(f"\nFile Types:")
            for file_type, count in sorted(results['file_types'].items(), key=lambda x: x[1], reverse=True):
                print(f"  {file_type}: {count}")
            
            # Show suspicious files
            if results['suspicious_files']:
                print(f"\n⚠️  Suspicious Files:")
                for file_info in results['suspicious_files'][:5]:
                    print(f"  {file_info['file']}")
                    for pattern in file_info['patterns']:
                        print(f"    - {pattern}")
            
            # Save directory report
            output_file = f"{os.path.basename(directory_path)}_directory_analysis.json"
            with open(output_file, 'w') as f:
                json.dump(results, f, indent=2)
            print(f"\nDirectory analysis saved to: {output_file}")
        
        else:
            print(f"Directory not found: {directory_path}")
    
    elif choice == "3":
        # Create test files
        print("Creating test files for analysis...")
        
        # Create test directory
        test_dir = "forensic_test_files"
        os.makedirs(test_dir, exist_ok=True)
        
        # Test file 1: Text with personal info
        with open(os.path.join(test_dir, "personal_info.txt"), 'w') as f:
            f.write("Contact Information:\n")
            f.write("Name: John Doe\n")
            f.write("Email: john.doe@example.com\n")
            f.write("Phone: 555-123-4567\n")
            f.write("Credit Card: 4532-1234-5678-9012\n")
            f.write("SSN: 123-45-6789\n")
            f.write("Address: 123 Main St, Anytown, USA\n")
        
        # Test file 2: Code with suspicious patterns
        with open(os.path.join(test_dir, "suspicious.py"), 'w') as f:
            f.write("# Suspicious Python script\n")
            f.write("import os\n")
            f.write("password = 'secret123'\n")
            f.write("def system_cmd(cmd):\n")
            f.write("    return os.system(cmd)\n")
            f.write("eval('print(\"hack\")')\n")
            f.write("shell_exec('whoami')\n")
        
        # Test file 3: Document with keywords
        with open(os.path.join(test_dir, "document.txt"), 'w') as f:
            f.write("This document contains information about illegal activities.\n")
            f.write("The vulnerability allows for exploitation of the system.\n")
            f.write("Malware was detected on the compromised network.\n")
            f.write("The attack vector was identified as a phishing attempt.\n")
            f.write("Stolen data was found in the breach.\n")
        
        # Test file 4: HTML with XSS
        with open(os.path.join(test_dir, "webpage.html"), 'w') as f:
            f.write("<html><head><title>Test Page</title></head>\n")
            f.write("<body>\n")
            f.write("<script>alert('XSS')</script>\n")
            f.write("<img src=x onerror=alert(1)>\n")
            f.write("<a href='javascript:alert(1)'>Click me</a>\n")
            f.write("</body></html>\n")
        
        print(f"Created 4 test files in {test_dir}/")
        
        # Analyze the test directory
        results = analyzer.analyze_directory(test_dir, recursive=False)
        
        print(f"\nTest Directory Analysis:")
        print(f"Files analyzed: {results['files_analyzed']}")
        print(f"Suspicious files: {len(results['suspicious_files'])}")
        print(f"Files with personal info: {len(results['files_with_personal_info'])}")
        print(f"Files with keywords: {len(results['files_with_keywords'])}")
        
        # Analyze individual files
        for file_name in os.listdir(test_dir):
            file_path = os.path.join(test_dir, file_name)
            if os.path.isfile(file_path):
                print(f"\n--- Analyzing {file_name} ---")
                report = analyzer.generate_forensic_report(file_path)
                print(report)
    
    elif choice == "4":
        # Batch analyze multiple files
        files_input = input("Enter file paths (comma-separated): ").strip()
        file_paths = [path.strip() for path in files_input.split(',')]
        
        batch_results = {
            'total_files': len(file_paths),
            'analyzed_files': 0,
            'suspicious_files': [],
            'files_with_personal_info': [],
            'files_with_keywords': [],
            'analysis_time': datetime.now().isoformat()
        }
        
        for file_path in file_paths:
            if os.path.exists(file_path):
                print(f"\nAnalyzing: {file_path}")
                metadata = analyzer.get_file_metadata(file_path)
                content_analysis = analyzer.analyze_file_content(file_path)
                
                batch_results['analyzed_files'] += 1
                
                if content_analysis and content_analysis.suspicious_patterns:
                    batch_results['suspicious_files'].append(file_path)
                
                if content_analysis and content_analysis.personal_info:
                    batch_results['files_with_personal_info'].append(file_path)
                
                if content_analysis and content_analysis.keywords:
                    batch_results['files_with_keywords'].append(file_path)
            else:
                print(f"File not found: {file_path}")
        
        print(f"\nBatch Analysis Results:")
        print(f"Total files: {batch_results['total_files']}")
        print(f"Analyzed files: {batch_results['analyzed_files']}")
        print(f"Suspicious files: {len(batch_results['suspicious_files'])}")
        print(f"Files with personal info: {len(batch_results['files_with_personal_info'])}")
        print(f"Files with keywords: {len(batch_results['files_with_keywords'])}")
        
        # Save batch results
        with open('batch_forensic_analysis.json', 'w') as f:
            json.dump(batch_results, f, indent=2)
        print("\nBatch analysis saved to: batch_forensic_analysis.json")
    
    else:
        print("Invalid choice")
    
    print("\n=== Forensic Analyzer Demo Completed ===")
    print("Features demonstrated:")
    print("- File metadata extraction")
    print("- Content analysis and pattern detection")
    print("- Personal information identification")
    print("- Suspicious pattern detection")
    print("- Keyword searching")
    print("- Directory analysis")
    print("- Comprehensive reporting")
    
    print("\nForensic Capabilities:")
    print("- File type detection")
    print("- Encoding detection")
    print("- Programming language identification")
    print("- String extraction")
    print("- URL and email extraction")
    print("- Credit card validation")
    print("- Checksum calculation")
    print("- Security risk assessment")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install dependencies: pip install python-magic exifread chardet
2. Run analyzer: python file_analyzer.py
3. Choose analysis mode (single file, directory, test files, batch)
4. View comprehensive forensic analysis report
5. Reports saved to text and JSON files

Key Concepts:
- File Metadata: Creation, modification, access times
- Content Analysis: Pattern matching and extraction
- Personal Information: Sensitive data detection
- Suspicious Patterns: Security threat identification
- Forensic Keywords: Evidence discovery
- Checksums: File integrity verification

Detection Capabilities:
- Personal information (SSN, credit cards, emails, phones)
- Suspicious code patterns (eval, system calls, backdoors)
- URLs and network addresses
- Programming languages and file types
- Forensic keywords and evidence
- EXIF metadata from images

Applications:
- Digital forensics investigations
- Security incident response
- Data breach analysis
- Evidence collection
- Compliance auditing
- Malware analysis
- File integrity verification

Legal Note:
Only analyze files and systems you own or have proper authorization to examine.
Unauthorized forensic analysis may violate privacy laws and regulations.
"""
