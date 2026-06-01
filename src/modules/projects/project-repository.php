<?php
// /src/modules/projects/project-repository.php
require_once __DIR__ . '/../../core/database.php';

class ProjectRepository {
    
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllProjects() {
        $sql = "SELECT 
                    p.id, p.name, p.project_location, p.status, 
                    p.progress_percentage, p.created_at,
                    u.first_name, u.last_name,
                    (SELECT file_url FROM task_attachments 
                     WHERE project_id = p.id AND is_cover_photo = TRUE 
                     ORDER BY uploaded_at DESC LIMIT 1) as cover_photo_url
                FROM projects p
                LEFT JOIN users u ON p.project_lead_id = u.user_id
                ORDER BY p.created_at DESC";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createProjectTransaction($name, $location, $area, $leadId, $tasks, $team) {
        try {
            $this->db->beginTransaction();
            
            // 1. Insert Project
            $sql = "INSERT INTO projects (name, project_location, project_area, status, progress_percentage, project_lead_id) 
                    VALUES (:name, :location, :area, 'not yet started', 0, :lead_id) RETURNING id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name'     => $name,
                ':location' => $location,
                ':area'     => $area,
                ':lead_id'  => $leadId ?: null
            ]);
            $projectId = $stmt->fetch()['id'];
            
            // 2. Insert Tasks
            if (!empty($tasks['titles'])) {
                $taskSql = "INSERT INTO tasks (project_id, title, task_category, assignee_id, deadline) 
                            VALUES (:pid, :title, :cat, :assignee, :deadline)";
                $taskStmt = $this->db->prepare($taskSql);
                
                for ($i = 0; $i < count($tasks['titles']); $i++) {
                    $taskStmt->execute([
                        ':pid'      => $projectId,
                        ':title'    => $tasks['titles'][$i],
                        ':cat'      => $tasks['categories'][$i],
                        ':assignee' => !empty($tasks['assignees'][$i]) ? $tasks['assignees'][$i] : null,
                        ':deadline' => !empty($tasks['deadlines'][$i]) ? $tasks['deadlines'][$i] : null
                    ]);
                }
            }
            
            // 3. Insert Team
            if (!empty($team['user_ids'])) {
                $teamSql = "INSERT INTO project_team (project_id, project_lead_id, user_id, project_role) 
                            VALUES (:pid, :lead_id, :uid, :role)";
                $teamStmt = $this->db->prepare($teamSql);
                
                for ($i = 0; $i < count($team['user_ids']); $i++) {
                    $teamStmt->execute([
                        ':pid'     => $projectId,
                        ':lead_id' => $leadId ?: null,
                        ':uid'     => $team['user_ids'][$i],
                        ':role'    => !empty($team['roles'][$i]) ? $team['roles'][$i] : 'Team Member'
                    ]);
                }
            }
            
            $this->db->commit();
            return $projectId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getProjectById($projectId) {
        $sql = "SELECT p.*, 
                       u.first_name AS lead_first_name, u.last_name AS lead_last_name,
                       (SELECT file_url FROM task_attachments 
                        WHERE project_id = p.id AND is_cover_photo = TRUE 
                        ORDER BY uploaded_at DESC LIMIT 1) as cover_photo_url
                FROM projects p 
                LEFT JOIN users u ON p.project_lead_id = u.user_id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProjectTeam($projectId) {
        $sql = "SELECT pt.user_id, pt.project_role, u.first_name, u.last_name , u.avatar_url
                FROM project_team pt
                JOIN users u ON pt.user_id = u.user_id
                WHERE pt.project_id = :project_id
                ORDER BY CASE WHEN pt.project_role = 'lead' THEN 1 ELSE 2 END, u.last_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProjectTasks($projectId) {
        $sql = "SELECT t.*, u.first_name, u.last_name 
                FROM tasks t
                LEFT JOIN users u ON t.assignee_id = u.user_id
                WHERE t.project_id = :project_id
                ORDER BY t.deadline ASC, t.title ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveCoverPhotoRecord($projectId, $fileName, $fileUrl, $fileSize, $uploadedBy) {
        $sql = "INSERT INTO task_attachments (project_id, file_name, file_url, file_size, uploaded_by, is_cover_photo, uploaded_at) 
                VALUES (:pid, :fname, :furl, :fsize, :upby, TRUE, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pid'   => $projectId,
            ':fname' => $fileName,
            ':furl'  => $fileUrl,
            ':fsize' => $fileSize,
            ':upby'  => $uploadedBy
        ]);
    }

    public function getTaskTemplates() {
    // We select from 'materials' now and grab the material_category
    $sql = "SELECT m.name AS template_name, m.slug, m.material_category, 
                   tt.title, tt.category AS task_category, tt.days_offset
            FROM materials m
            LEFT JOIN task_template tt ON m.id = tt.template_id
            ORDER BY m.material_category ASC, m.name ASC, tt.sort_order ASC";
            
    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProject($projectId) {
        try {
            $this->db->beginTransaction();

            // 1. Delete associated attachments
            $stmtAtt = $this->db->prepare("DELETE FROM task_attachments WHERE project_id = :pid");
            $stmtAtt->execute([':pid' => $projectId]);

            // 2. Delete associated team members
            $stmtTeam = $this->db->prepare("DELETE FROM project_team WHERE project_id = :pid");
            $stmtTeam->execute([':pid' => $projectId]);

            // 3. Delete associated tasks
            $stmtTasks = $this->db->prepare("DELETE FROM tasks WHERE project_id = :pid");
            $stmtTasks->execute([':pid' => $projectId]);

            // 4. Finally, delete the project itself
            $stmtProj = $this->db->prepare("DELETE FROM projects WHERE id = :pid");
            $stmtProj->execute([':pid' => $projectId]);

            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateProjectTransaction($projectId, $name, $location, $area, $leadId, $tasks, $team) {
        try {
            $this->db->beginTransaction();

            // 1. Update Project basic info
            $sql = "UPDATE projects SET name = :name, project_location = :location, project_area = :area, project_lead_id = :lead_id WHERE id = :pid";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':name' => $name, ':location' => $location, ':area' => $area, ':lead_id' => $leadId ?: null, ':pid' => $projectId]);

            // 2. Intelligent Task Sync
            $existingTasksStmt = $this->db->prepare("SELECT id FROM tasks WHERE project_id = :pid");
            $existingTasksStmt->execute([':pid' => $projectId]);
            $existingTaskIds = $existingTasksStmt->fetchAll(PDO::FETCH_COLUMN);

            $submittedTaskIds = $tasks['ids'] ?? [];
            
            // A. Delete removed tasks
            $tasksToDelete = array_diff($existingTaskIds, array_filter($submittedTaskIds));
            if (!empty($tasksToDelete)) {
                $inQuery = implode(',', array_fill(0, count($tasksToDelete), '?'));
                $delStmt = $this->db->prepare("DELETE FROM tasks WHERE id IN ($inQuery)");
                $delStmt->execute(array_values($tasksToDelete));
            }

            // B. Insert or Update Tasks
            $insertTaskSql = "INSERT INTO tasks (project_id, title, task_category, assignee_id, deadline) VALUES (:pid, :title, :cat, :assignee, :deadline)";
            $updateTaskSql = "UPDATE tasks SET title = :title, task_category = :cat, assignee_id = :assignee, deadline = :deadline WHERE id = :tid";
            
            $insertStmt = $this->db->prepare($insertTaskSql);
            $updateStmt = $this->db->prepare($updateTaskSql);

            if (!empty($tasks['titles'])) {
                for ($i = 0; $i < count($tasks['titles']); $i++) {
                    $tid = $submittedTaskIds[$i] ?? null;
                    if ($tid) { // Update existing
                        $updateStmt->execute([
                            ':title'    => $tasks['titles'][$i],
                            ':cat'      => $tasks['categories'][$i],
                            ':assignee' => !empty($tasks['assignees'][$i]) ? $tasks['assignees'][$i] : null,
                            ':deadline' => !empty($tasks['deadlines'][$i]) ? $tasks['deadlines'][$i] : null,
                            ':tid'      => $tid
                        ]);
                    } else { // Insert newly added milestone
                        $insertStmt->execute([
                            ':pid'      => $projectId,
                            ':title'    => $tasks['titles'][$i],
                            ':cat'      => $tasks['categories'][$i],
                            ':assignee' => !empty($tasks['assignees'][$i]) ? $tasks['assignees'][$i] : null,
                            ':deadline' => !empty($tasks['deadlines'][$i]) ? $tasks['deadlines'][$i] : null
                        ]);
                    }
                }
            }

            // 3. Sync Team Members (Wipe and replace is safe here)
            $this->db->prepare("DELETE FROM project_team WHERE project_id = :pid")->execute([':pid' => $projectId]);
            if (!empty($team['user_ids'])) {
                $teamSql = "INSERT INTO project_team (project_id, project_lead_id, user_id, project_role) VALUES (:pid, :lead_id, :uid, :role)";
                $teamStmt = $this->db->prepare($teamSql);
                
                for ($i = 0; $i < count($team['user_ids']); $i++) {
                    $teamStmt->execute([
                        ':pid'     => $projectId,
                        ':lead_id' => $leadId ?: null,
                        ':uid'     => $team['user_ids'][$i],
                        ':role'    => !empty($team['roles'][$i]) ? $team['roles'][$i] : 'Team Member'
                    ]);
                }
            }

            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}