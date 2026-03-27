<?php
/**
 * PHP File Uploads and Handling
 * 
 * Comprehensive guide to handling file uploads, validation, and processing in PHP.
 */

echo "=== PHP File Uploads and Handling ===\n\n";

// Basic File Upload Configuration
echo "--- Basic File Upload Configuration ---\n";

// PHP configuration directives (informational)
$uploadConfig = [
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_input_time' => ini_get('max_input_time'),
    'memory_limit' => ini_get('memory_limit'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: 'system default'
];

echo "Current upload configuration:\n";
foreach ($uploadConfig as $key => $value) {
    echo "$key: $value\n";
}
echo "\n";

// File Upload Handler Class
echo "--- File Upload Handler Class ---\n";

class FileUploadHandler {
    private $allowedTypes = [];
    private $maxSize;
    private $uploadDir;
    private $errors = [];
    private $uploadedFiles = [];
    
    public function __construct($uploadDir = 'uploads/', $maxSize = 5242880) {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->maxSize = $maxSize;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function setAllowedTypes(array $types) {
        $this->allowedTypes = $types;
        return $this;
    }
    
    public function addAllowedType($type) {
        $this->allowedTypes[] = $type;
        return $this;
    }
    
    public function upload($file, $newName = null) {
        $this->errors = [];
        
        if (!$this->validateFile($file)) {
            return false;
        }
        
        $filename = $newName ?: $this->generateSecureFilename($file['name']);
        $filepath = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $fileInfo = [
                'original_name' => $file['name'],
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => $file['size'],
                'mime_type' => $file['type'],
                'extension' => pathinfo($file['name'], PATHINFO_EXTENSION),
                'uploaded_at' => date('Y-m-d H:i:s')
            ];
            
            $this->uploadedFiles[] = $fileInfo;
            return $fileInfo;
        }
        
        $this->errors[] = "Failed to move uploaded file";
        return false;
    }
    
    public function uploadMultiple($files) {
        $results = [];
        
        // Handle single file upload in multiple format
        if (!is_array($files['name'])) {
            return $this->upload($files);
        }
        
        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                $result = $this->upload($file);
                if ($result) {
                    $results[] = $result;
                }
            }
        }
        
        return $results;
    }
    
    private function validateFile($file) {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = "Invalid file upload";
            return false;
        }
        
        // Check upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }
        
        // Check file size
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = "File size exceeds maximum allowed size";
            return false;
        }
        
        // Check if file is empty
        if ($file['size'] === 0) {
            $this->errors[] = "File is empty";
            return false;
        }
        
        // Check file type
        if (!empty($this->allowedTypes) && !in_array($file['type'], $this->allowedTypes)) {
            $this->errors[] = "File type not allowed: " . $file['type'];
            return false;
        }
        
        // Additional MIME type verification
        $detectedType = $this->detectMimeType($file['tmp_name']);
        if ($detectedType !== $file['type']) {
            $this->errors[] = "File type mismatch detected";
            return false;
        }
        
        // Check file extension
        $allowedExtensions = $this->getAllowedExtensions();
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) {
            $this->errors[] = "File extension not allowed: .$extension";
            return false;
        }
        
        return true;
    }
    
    private function detectMimeType($filepath) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        return $mimeType;
    }
    
    private function getAllowedExtensions() {
        $extensions = [];
        
        $mimeMap = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'application/pdf' => ['pdf'],
            'text/plain' => ['txt'],
            'application/msword' => ['doc'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx']
        ];
        
        foreach ($this->allowedTypes as $mime) {
            if (isset($mimeMap[$mime])) {
                $extensions = array_merge($extensions, $mimeMap[$mime]);
            }
        }
        
        return array_unique($extensions);
    }
    
    private function generateSecureFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Sanitize basename
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        
        // Generate unique identifier
        $unique = uniqid() . '_' . bin2hex(random_bytes(4));
        
        return $basename . '_' . $unique . '.' . $extension;
    }
    
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            case UPLOAD_ERR_FORM_SIZE:
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            case UPLOAD_ERR_PARTIAL:
                return "The uploaded file was only partially uploaded";
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk";
            case UPLOAD_ERR_EXTENSION:
                return "A PHP extension stopped the file upload";
            default:
                return "Unknown upload error";
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getUploadedFiles() {
        return $this->uploadedFiles;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
}

// Image Processing
echo "--- Image Processing ---\n";

class ImageProcessor {
    private $image;
    private $imageInfo;
    
    public function __construct($filepath) {
        $this->imageInfo = getimagesize($filepath);
        
        if (!$this->imageInfo) {
            throw new \Exception("Invalid image file");
        }
        
        $this->loadImage($filepath);
    }
    
    private function loadImage($filepath) {
        switch ($this->imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filepath);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filepath);
                break;
            default:
                throw new \Exception("Unsupported image type");
        }
    }
    
    public function resize($maxWidth, $maxHeight) {
        $width = $this->imageInfo[0];
        $height = $this->imageInfo[1];
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($this->imageInfo[2] === IMAGETYPE_PNG || $this->imageInfo[2] === IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize image
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Replace old image
        imagedestroy($this->image);
        $this->image = $newImage;
        
        // Update image info
        $this->imageInfo[0] = $newWidth;
        $this->imageInfo[1] = $newHeight;
    }
    
    public function createThumbnail($size, $outputPath) {
        $width = $this->imageInfo[0];
        $height = $this->imageInfo[1];
        
        // Calculate crop dimensions
        if ($width > $height) {
            $cropWidth = $height;
            $cropHeight = $height;
            $cropX = ($width - $height) / 2;
            $cropY = 0;
        } else {
            $cropWidth = $width;
            $cropHeight = $width;
            $cropX = 0;
            $cropY = ($height - $width) / 2;
        }
        
        // Create thumbnail
        $thumbnail = imagecreatetruecolor($size, $size);
        
        if ($this->imageInfo[2] === IMAGETYPE_PNG || $this->imageInfo[2] === IMAGETYPE_GIF) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $size, $size, $transparent);
        }
        
        imagecopyresampled($thumbnail, $this->image, 0, 0, $cropX, $cropY, $size, $size, $cropWidth, $cropHeight);
        
        // Save thumbnail
        $this->saveImage($thumbnail, $outputPath);
        imagedestroy($thumbnail);
    }
    
    public function save($outputPath, $quality = 90) {
        $this->saveImage($this->image, $outputPath, $quality);
    }
    
    private function saveImage($image, $outputPath, $quality = 90) {
        $extension = strtolower(pathinfo($outputPath, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($image, $outputPath, $quality);
                break;
            case 'png':
                $pngQuality = 9 - round(($quality * 9) / 100);
                imagepng($image, $outputPath, $pngQuality);
                break;
            case 'gif':
                imagegif($image, $outputPath);
                break;
            default:
                throw new \Exception("Unsupported output format");
        }
    }
    
    public function getWidth() {
        return $this->imageInfo[0];
    }
    
    public function getHeight() {
        return $this->imageInfo[1];
    }
    
    public function getMimeType() {
        return $this->imageInfo['mime'];
    }
    
    public function __destruct() {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }
}

// File Storage Manager
echo "--- File Storage Manager ---\n";

class FileStorageManager {
    private $storageDir;
    private $database; // Simulated database
    
    public function __construct($storageDir = 'storage/') {
        $this->storageDir = rtrim($storageDir, '/') . '/';
        $this->database = []; // Simulated database
        
        // Create storage directories
        $this->createStorageStructure();
    }
    
    private function createStorageStructure() {
        $directories = [
            $this->storageDir,
            $this->storageDir . 'images/',
            $this->storageDir . 'documents/',
            $this->storageDir . 'temp/',
            $this->storageDir . 'backups/'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    public function storeFile($fileInfo, $category = 'documents') {
        $record = [
            'id' => uniqid(),
            'original_name' => $fileInfo['original_name'],
            'filename' => $fileInfo['filename'],
            'category' => $category,
            'size' => $fileInfo['size'],
            'mime_type' => $fileInfo['mime_type'],
            'uploaded_at' => $fileInfo['uploaded_at'],
            'downloads' => 0,
            'is_active' => true
        ];
        
        $this->database[$record['id']] = $record;
        
        // Move file to appropriate category directory
        $categoryDir = $this->storageDir . $category . '/';
        if (!is_dir($categoryDir)) {
            mkdir($categoryDir, 0755, true);
        }
        
        $newPath = $categoryDir . $fileInfo['filename'];
        if (rename($fileInfo['filepath'], $newPath)) {
            $record['filepath'] = $newPath;
            $this->database[$record['id']] = $record;
            return $record;
        }
        
        return false;
    }
    
    public function getFile($id) {
        return $this->database[$id] ?? null;
    }
    
    public function getFilesByCategory($category) {
        return array_filter($this->database, function($file) use ($category) {
            return $file['category'] === $category && $file['is_active'];
        });
    }
    
    public function deleteFile($id) {
        if (!isset($this->database[$id])) {
            return false;
        }
        
        $file = $this->database[$id];
        
        // Move to backup instead of permanent deletion
        $backupPath = $this->storageDir . 'backups/' . $file['filename'];
        if (rename($file['filepath'], $backupPath)) {
            $file['is_active'] = false;
            $file['deleted_at'] = date('Y-m-d H:i:s');
            $this->database[$id] = $file;
            return true;
        }
        
        return false;
    }
    
    public function getStorageStats() {
        $stats = [
            'total_files' => 0,
            'total_size' => 0,
            'categories' => []
        ];
        
        foreach ($this->database as $file) {
            if ($file['is_active']) {
                $stats['total_files']++;
                $stats['total_size'] += $file['size'];
                
                $category = $file['category'];
                if (!isset($stats['categories'][$category])) {
                    $stats['categories'][$category] = ['files' => 0, 'size' => 0];
                }
                $stats['categories'][$category]['files']++;
                $stats['categories'][$category]['size'] += $file['size'];
            }
        }
        
        return $stats;
    }
}

// Practical Examples
echo "--- Practical Examples ---\n";

// Example 1: Complete File Upload System
echo "Example 1: Complete File Upload System\n";

// Simulate file upload data
$simulatedUpload = [
    'name' => 'profile_photo.jpg',
    'type' => 'image/jpeg',
    'size' => 2048000, // 2MB
    'tmp_name' => '/tmp/phpABC123'
];

echo "Setting up file upload handler...\n";
$handler = new FileUploadHandler('uploads/', 5242880); // 5MB limit
$handler->setAllowedTypes([
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf'
]);

echo "Attempting to upload simulated file...\n";
$result = $handler->upload($simulatedUpload);

if ($result) {
    echo "✓ File uploaded successfully:\n";
    echo "  Original name: {$result['original_name']}\n";
    echo "  Secure filename: {$result['filename']}\n";
    echo "  Size: " . round($result['size'] / 1024, 2) . " KB\n";
    echo "  MIME type: {$result['mime_type']}\n";
    echo "  Path: {$result['filepath']}\n";
} else {
    echo "✗ Upload failed:\n";
    foreach ($handler->getErrors() as $error) {
        echo "  - $error\n";
    }
}
echo "\n";

// Example 2: Image Processing Workflow
echo "Example 2: Image Processing Workflow\n";

// Create a test image (simulation)
echo "Image processing workflow:\n";
echo "1. Load image\n";
echo "2. Resize to maximum dimensions (800x600)\n";
echo "3. Create thumbnail (150x150)\n";
echo "4. Save processed images\n";

// Since we can't actually create images in this demo, show the workflow
$imageProcessorSteps = [
    'load' => 'Load original image from upload',
    'resize' => 'Resize to fit within 800x600 pixels while maintaining aspect ratio',
    'thumbnail' => 'Create 150x150 square thumbnail by cropping and resizing',
    'save' => 'Save both resized image and thumbnail to appropriate directories'
];

foreach ($imageProcessorSteps as $step => $description) {
    echo "$step: $description\n";
}
echo "\n";

// Example 3: File Management System
echo "Example 3: File Management System\n";

$storageManager = new FileStorageManager('storage/');

// Simulate storing files
$files = [
    ['original_name' => 'document.pdf', 'filename' => 'doc_abc123.pdf', 'size' => 1024000, 'mime_type' => 'application/pdf', 'filepath' => 'uploads/doc_abc123.pdf', 'uploaded_at' => date('Y-m-d H:i:s')],
    ['original_name' => 'photo.jpg', 'filename' => 'img_def456.jpg', 'size' => 2048000, 'mime_type' => 'image/jpeg', 'filepath' => 'uploads/img_def456.jpg', 'uploaded_at' => date('Y-m-d H:i:s')],
    ['original_name' => 'report.pdf', 'filename' => 'doc_ghi789.pdf', 'size' => 512000, 'mime_type' => 'application/pdf', 'filepath' => 'uploads/doc_ghi789.pdf', 'uploaded_at' => date('Y-m-d H:i:s')]
];

foreach ($files as $file) {
    $stored = $storageManager->storeFile($file, $file['mime_type'] === 'image/jpeg' ? 'images' : 'documents');
    if ($stored) {
        echo "✓ Stored: {$stored['original_name']} (ID: {$stored['id']})\n";
    }
}

echo "\nStorage statistics:\n";
$stats = $storageManager->getStorageStats();
echo "Total files: {$stats['total_files']}\n";
echo "Total size: " . round($stats['total_size'] / 1024 / 1024, 2) . " MB\n";
echo "Categories:\n";
foreach ($stats['categories'] as $category => $data) {
    echo "  $category: {$data['files']} files, " . round($data['size'] / 1024, 2) . " KB\n";
}
echo "\n";

// Example 4: Security Best Practices
echo "Example 4: Security Best Practices\n";

class SecureFileUploader {
    private $handler;
    private $scanner;
    
    public function __construct() {
        $this->handler = new FileUploadHandler('secure_uploads/', 2097152); // 2MB limit
        $this->scanner = new VirusScanner(); // Simulated
    }
    
    public function uploadWithSecurity($file) {
        // Step 1: Basic validation
        if (!$this->handler->upload($file)) {
            return ['success' => false, 'errors' => $this->handler->getErrors()];
        }
        
        $uploadedFile = $this->handler->getUploadedFiles()[0];
        
        // Step 2: Virus scan
        if (!$this->scanner->scan($uploadedFile['filepath'])) {
            unlink($uploadedFile['filepath']);
            return ['success' => false, 'errors' => ['File contains malicious content']];
        }
        
        // Step 3: Content validation for images
        if (strpos($uploadedFile['mime_type'], 'image/') === 0) {
            if (!$this->validateImageContent($uploadedFile['filepath'])) {
                unlink($uploadedFile['filepath']);
                return ['success' => false, 'errors' => ['Invalid image content']];
            }
        }
        
        return ['success' => true, 'file' => $uploadedFile];
    }
    
    private function validateImageContent($filepath) {
        $imageInfo = @getimagesize($filepath);
        return $imageInfo !== false;
    }
}

// Simulated virus scanner
class VirusScanner {
    public function scan($filepath) {
        // In real implementation, this would use antivirus software
        // For demo, we'll simulate the scan
        echo "Scanning file for viruses...\n";
        return true; // Assume clean
    }
}

echo "Secure upload process:\n";
$secureUploader = new SecureFileUploader();
echo "1. Basic file validation\n";
echo "2. Virus scanning\n";
echo "3. Content validation\n";
echo "4. Secure filename generation\n";
echo "5. Storage in protected directory\n";
echo "6. Access control implementation\n";
echo "\n";

// Example 5: AJAX Upload Handler
echo "Example 5: AJAX Upload Handler (Simulation)\n";

class AjaxUploadHandler {
    private $handler;
    
    public function __construct() {
        $this->handler = new FileUploadHandler('ajax_uploads/');
    }
    
    public function handleUpload() {
        // Simulate AJAX response
        $response = [
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'data' => []
        ];
        
        // Simulate processing
        echo "AJAX Upload Flow:\n";
        echo "1. Client sends file via XMLHttpRequest\n";
        echo "2. Server receives and validates file\n";
        echo "3. File is processed and stored\n";
        echo "4. JSON response sent back to client\n";
        echo "5. Client updates UI based on response\n";
        
        return json_encode($response);
    }
}

$ajaxHandler = new AjaxUploadHandler();
echo $ajaxHandler->handleUpload() . "\n\n";

echo "=== End of File Uploads and Handling ===\n";
?>
