<?php
namespace Generated\Models;

use Generated\Interfaces\EnrollmentRepositoryInterface;
use App\Config\Database;
use PDO;

class Enrollment implements EnrollmentRepositoryInterface
{
    private $db;
    private $table = 'enrollments';
    private $allowedFields = [];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->loadAllowedFields();
    }

    private function loadAllowedFields(): void
    {
        try {
            $stmt = $this->db->query("DESCRIBE `school_management`.`{$this->table}`");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $this->allowedFields = array_filter($columns, function($col) {
                return strtolower($col) !== 'id';
            });
        } catch (\PDOException $e) {
            $this->allowedFields = [];
        }
    }

    private function filterData(array $data): array
    {
        return array_filter($data, function($key) {
            return in_array($key, $this->allowedFields);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM `school_management`.`{$this->table}`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithRelations(): array
    {
        $items = $this->getAll();
        
        foreach ($items as &$item) {
            $item = $this->loadRelations($item);
        }
        
        return $items;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `school_management`.`{$this->table}` WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $result = $this->loadRelations($result);
        }
        
        return $result ?: null;
    }

    private function loadRelations(array $item): array
    {
        
        // Relación: enrollments.course_id -> courses
        if (isset($item['course_id']) && $item['course_id']) {
            $stmt = $this->db->prepare("SELECT * FROM `courses` WHERE id = ?");
            $stmt->execute([$item['course_id']]);
            $item['course_data'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Relación: enrollments.student_id -> students
        if (isset($item['student_id']) && $item['student_id']) {
            $stmt = $this->db->prepare("SELECT * FROM `students` WHERE id = ?");
            $stmt->execute([$item['student_id']]);
            $item['student_data'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $item;
    }

    public function create(array $data): bool
    {
        $data = $this->filterData($data);
        
        if (empty($data)) {
            return false;
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO `school_management`.`{$this->table}` ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute(array_values($data));
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->filterData($data);
        
        if (empty($data)) {
            return false;
        }

        $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE `school_management`.`{$this->table}` SET $fields WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $values = array_values($data);
        $values[] = $id;
        
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `school_management`.`{$this->table}` WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllowedFields(): array
    {
        return $this->allowedFields;
    }
}