"""
File Manager Utility
Comprehensive file operations and management tool.
"""

import os
import shutil
import hashlib
import time
from datetime import datetime
import mimetypes

class FileManager:
    """File management operations utility."""
    
    def __init__(self, base_directory="."):
        """Initialize file manager with base directory."""
        self.base_directory = base_directory
        self.supported_operations = [
            'list', 'copy', 'move', 'delete', 'rename', 
            'search', 'info', 'duplicate', 'organize'
        ]
    
    def list_files(self, directory=None, show_hidden=False, recursive=False):
        """
        List files in directory with details.
        
        Args:
            directory (str): Directory to list (default: base_directory)
            show_hidden (bool): Show hidden files
            recursive (bool): List files recursively
            
        Returns:
            list: File information
        """
        target_dir = directory or self.base_directory
        files_info = []
        
        try:
            if recursive:
                for root, dirs, filenames in os.walk(target_dir):
                    for filename in filenames:
                        if not show_hidden and filename.startswith('.'):
                            continue
                        file_path = os.path.join(root, filename)
                        files_info.append(self._get_file_info(file_path))
            else:
                for item in os.listdir(target_dir):
                    if not show_hidden and item.startswith('.'):
                        continue
                    item_path = os.path.join(target_dir, item)
                    files_info.append(self._get_file_info(item_path))
            
            return sorted(files_info, key=lambda x: x['name'])
        except PermissionError:
            return [{"error": f"Permission denied: {target_dir}"}]
    
    def _get_file_info(self, file_path):
        """Get comprehensive file information."""
        try:
            stat = os.stat(file_path)
            return {
                'name': os.path.basename(file_path),
                'path': file_path,
                'size': stat.st_size,
                'size_human': self._format_size(stat.st_size),
                'modified': datetime.fromtimestamp(stat.st_mtime),
                'created': datetime.fromtimestamp(stat.st_ctime),
                'is_directory': os.path.isdir(file_path),
                'is_file': os.path.isfile(file_path),
                'extension': os.path.splitext(file_path)[1].lower(),
                'mime_type': mimetypes.guess_type(file_path)[0] or 'unknown'
            }
        except (OSError, PermissionError):
            return {"error": f"Cannot access: {file_path}"}
    
    def _format_size(self, size_bytes):
        """Format file size in human-readable format."""
        for unit in ['B', 'KB', 'MB', 'GB', 'TB']:
            if size_bytes < 1024.0:
                return f"{size_bytes:.1f} {unit}"
            size_bytes /= 1024.0
        return f"{size_bytes:.1f} PB"
    
    def copy_file(self, source, destination):
        """
        Copy file or directory.
        
        Args:
            source (str): Source path
            destination (str): Destination path
            
        Returns:
            bool: Success status
        """
        try:
            if os.path.isfile(source):
                shutil.copy2(source, destination)
            elif os.path.isdir(source):
                shutil.copytree(source, destination)
            return True
        except Exception as e:
            print(f"Copy failed: {e}")
            return False
    
    def move_file(self, source, destination):
        """
        Move file or directory.
        
        Args:
            source (str): Source path
            destination (str): Destination path
            
        Returns:
            bool: Success status
        """
        try:
            shutil.move(source, destination)
            return True
        except Exception as e:
            print(f"Move failed: {e}")
            return False
    
    def delete_file(self, path):
        """
        Delete file or directory.
        
        Args:
            path (str): Path to delete
            
        Returns:
            bool: Success status
        """
        try:
            if os.path.isfile(path):
                os.remove(path)
            elif os.path.isdir(path):
                shutil.rmtree(path)
            return True
        except Exception as e:
            print(f"Delete failed: {e}")
            return False
    
    def rename_file(self, old_path, new_name):
        """
        Rename file or directory.
        
        Args:
            old_path (str): Current path
            new_name (str): New name
            
        Returns:
            bool: Success status
        """
        try:
            new_path = os.path.join(os.path.dirname(old_path), new_name)
            os.rename(old_path, new_path)
            return True
        except Exception as e:
            print(f"Rename failed: {e}")
            return False
    
    def search_files(self, pattern, directory=None, case_sensitive=False):
        """
        Search for files by name pattern.
        
        Args:
            pattern (str): Search pattern
            directory (str): Directory to search
            case_sensitive (bool): Case sensitive search
            
        Returns:
            list: Matching files
        """
        target_dir = directory or self.base_directory
        matches = []
        search_pattern = pattern if case_sensitive else pattern.lower()
        
        try:
            for root, dirs, files in os.walk(target_dir):
                for file in files:
                    filename = file if case_sensitive else file.lower()
                    if search_pattern in filename:
                        file_path = os.path.join(root, file)
                        matches.append(self._get_file_info(file_path))
        except Exception as e:
            print(f"Search failed: {e}")
        
        return matches
    
    def find_duplicates(self, directory=None):
        """
        Find duplicate files by content hash.
        
        Args:
            directory (str): Directory to search
            
        Returns:
            dict: Groups of duplicate files
        """
        target_dir = directory or self.base_directory
        file_hashes = {}
        duplicates = {}
        
        try:
            for root, dirs, files in os.walk(target_dir):
                for file in files:
                    file_path = os.path.join(root, file)
                    file_hash = self._get_file_hash(file_path)
                    
                    if file_hash in file_hashes:
                        if file_hash not in duplicates:
                            duplicates[file_hash] = [file_hashes[file_hash]]
                        duplicates[file_hash].append(file_path)
                    else:
                        file_hashes[file_hash] = file_path
        except Exception as e:
            print(f"Duplicate search failed: {e}")
        
        return duplicates
    
    def _get_file_hash(self, file_path):
        """Calculate MD5 hash of file content."""
        try:
            hash_md5 = hashlib.md5()
            with open(file_path, "rb") as f:
                for chunk in iter(lambda: f.read(4096), b""):
                    hash_md5.update(chunk)
            return hash_md5.hexdigest()
        except Exception:
            return None
    
    def organize_by_type(self, directory=None):
        """
        Organize files into folders by type.
        
        Args:
            directory (str): Directory to organize
            
        Returns:
            dict: Organization results
        """
        target_dir = directory or self.base_directory
        type_folders = {
            'images': ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.tiff', '.webp'],
            'documents': ['.pdf', '.doc', '.docx', '.txt', '.rtf', '.odt'],
            'videos': ['.mp4', '.avi', '.mkv', '.mov', '.wmv', '.flv'],
            'audio': ['.mp3', '.wav', '.flac', '.aac', '.ogg', '.wma'],
            'archives': ['.zip', '.rar', '.7z', '.tar', '.gz'],
            'code': ['.py', '.js', '.html', '.css', '.java', '.cpp', '.c'],
            'spreadsheets': ['.xls', '.xlsx', '.csv', '.ods'],
            'presentations': ['.ppt', '.pptx', '.odp']
        }
        
        results = {'organized': 0, 'errors': []}
        
        try:
            for file in os.listdir(target_dir):
                file_path = os.path.join(target_dir, file)
                if not os.path.isfile(file_path):
                    continue
                
                file_ext = os.path.splitext(file)[1].lower()
                target_folder = 'other'
                
                for folder, extensions in type_folders.items():
                    if file_ext in extensions:
                        target_folder = folder
                        break
                
                folder_path = os.path.join(target_dir, target_folder)
                os.makedirs(folder_path, exist_ok=True)
                
                try:
                    shutil.move(file_path, os.path.join(folder_path, file))
                    results['organized'] += 1
                except Exception as e:
                    results['errors'].append(f"Failed to move {file}: {e}")
        
        except Exception as e:
            results['errors'].append(f"Organization failed: {e}")
        
        return results
    
    def get_directory_stats(self, directory=None):
        """
        Get directory statistics.
        
        Args:
            directory (str): Directory to analyze
            
        Returns:
            dict: Directory statistics
        """
        target_dir = directory or self.base_directory
        stats = {
            'total_files': 0,
            'total_directories': 0,
            'total_size': 0,
            'file_types': {},
            'largest_files': []
        }
        
        try:
            for root, dirs, files in os.walk(target_dir):
                stats['total_directories'] += len(dirks)
                
                for file in files:
                    file_path = os.path.join(root, file)
                    try:
                        file_size = os.path.getsize(file_path)
                        stats['total_files'] += 1
                        stats['total_size'] += file_size
                        
                        # Track file types
                        ext = os.path.splitext(file)[1].lower()
                        stats['file_types'][ext] = stats['file_types'].get(ext, 0) + 1
                        
                        # Track largest files
                        stats['largest_files'].append({
                            'name': file,
                            'path': file_path,
                            'size': file_size
                        })
                    
                    except (OSError, PermissionError):
                        continue
            
            # Sort largest files and keep top 10
            stats['largest_files'] = sorted(
                stats['largest_files'], 
                key=lambda x: x['size'], 
                reverse=True
            )[:10]
            
            stats['total_size_human'] = self._format_size(stats['total_size'])
            
        except Exception as e:
            stats['error'] = str(e)
        
        return stats

def main():
    """Demonstrate file manager functionality."""
    print("File Manager Utility")
    print("=" * 30)
    
    fm = FileManager()
    
    # List current directory
    print("\n1. Current Directory Contents:")
    files = fm.list_files(show_hidden=False)
    for file_info in files:
        if 'error' in file_info:
            print(f"  {file_info['error']}")
        else:
            icon = "📁" if file_info['is_directory'] else "📄"
            print(f"  {icon} {file_info['name']} ({file_info['size_human']})")
    
    # Directory statistics
    print("\n2. Directory Statistics:")
    stats = fm.get_directory_stats()
    print(f"  Files: {stats['total_files']}")
    print(f"  Directories: {stats['total_directories']}")
    print(f"  Total Size: {stats['total_size_human']}")
    
    if stats['file_types']:
        print("  File Types:")
        for ext, count in sorted(stats['file_types'].items()):
            print(f"    {ext or 'no extension'}: {count}")
    
    # Search demonstration
    print("\n3. Search Demonstration:")
    search_results = fm.search_files(".py")
    print(f"  Found {len(search_results)} Java files")
    for result in search_results[:5]:  # Show first 5
        print(f"    📄 {result['name']} ({result['size_human']})")
    
    # File operations menu
    print("\n4. File Operations Demo:")
    print("Available operations:", ', '.join(fm.supported_operations))
    
    # Create a test file for demonstration
    test_file = "test_file.txt"
    try:
        with open(test_file, 'w') as f:
            f.write("This is a test file for the file manager demonstration.")
        
        print(f"\nCreated test file: {test_file}")
        
        # Get file info
        file_info = fm._get_file_info(test_file)
        print(f"  Size: {file_info['size_human']}")
        print(f"  Type: {file_info['mime_type']}")
        print(f"  Modified: {file_info['modified']}")
        
        # Clean up
        fm.delete_file(test_file)
        print(f"  Deleted test file")
        
    except Exception as e:
        print(f"  Demo failed: {e}")

if __name__ == "__main__":
    main()
