<?php
// /src/modules/attachments/attachment-service.php
require_once __DIR__ . '/attachment-repository.php';

class AttachmentService {
    private AttachmentRepository $repository;

    public function __construct(AttachmentRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Handles the entire file upload business process
     */
    public function uploadTaskFile(string $taskId, string $projectId, string $currentUserId, array $file, string $customName, string $description): void {
        
        // 1. Authorization Check
        if (!$this->repository->canUserUpload($taskId, $currentUserId)) {
            throw new Exception("Unauthorized: You must be assigned to this task or be part of the project team to upload files.");
        }

        // 2. Check if PHP reported an upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
            ];
            $errorMessage = $errorMessages[$file['error']] ?? 'Unknown upload error.';
            throw new Exception("Upload Failed: " . $errorMessage);
        }

        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmp  = $file['tmp_name'];

        // 3. Resolve Custom Name
        if (!empty($customName)) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if (!preg_match("/\.$ext$/i", $customName)) {
                $customName .= '.' . $ext;
            }
            $fileNameToSave = $customName;
        } else {
            $fileNameToSave = $fileName;
        }

        $fileType = mime_content_type($fileTmp);
        
        // 4. Set up Structured Local File Storage Path
        $formattedProjectId = 'PRJ-' . str_pad($projectId, 3, '0', STR_PAD_LEFT);
        $uploadDir = __DIR__ . '/../../../uploads/' . $formattedProjectId . '/task_' . $taskId . '/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $uniqueName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9.\-]/', '_', $fileNameToSave);
        $targetPath = $uploadDir . $uniqueName;
        $fileUrl = '/uploads/' . $formattedProjectId . '/task_' . $taskId . '/' . $uniqueName;

        // 5. Compression Logic (Only for images > 5MB)
        $maxSizeBytes = 5 * 1024 * 1024; // 5MB limit before compression kicks in

        if ($fileSize > $maxSizeBytes && strpos($fileType, 'image') !== false) {
            $image = null;
            if ($fileType == 'image/jpeg') $image = imagecreatefromjpeg($fileTmp);
            elseif ($fileType == 'image/png') $image = imagecreatefrompng($fileTmp);
            
            if ($image) {
                imagejpeg($image, $targetPath, 60);
                imagedestroy($image);
                $fileSize = filesize($targetPath); // Update size after compression
            } else {
                move_uploaded_file($fileTmp, $targetPath);
            }
        } else {
            // Move file normally (Documents, or Images < 5MB)
            move_uploaded_file($fileTmp, $targetPath);
        }

        // 6. Save metadata to Database
        $this->repository->saveAttachment($taskId, $fileNameToSave, $fileUrl, $fileSize, $currentUserId, $description);
    }

    /**
     * Fetches all attachments for a project and groups them by task ID.
     */
    public function getGroupedAttachmentsForProject($projectId) {
        // 1. Get raw data from its own repository
        $rawAttachments = $this->repository->getAttachmentsByProjectId($projectId);
        
        // 2. Perform business logic (grouping)
        $groupedAttachments = [];
        foreach ($rawAttachments as $file) {
            $tid = $file['task_id'];
            $groupedAttachments[$tid][] = $file;
        }
        
        return $groupedAttachments;
    }
}