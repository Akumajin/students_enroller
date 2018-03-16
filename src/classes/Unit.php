<?php

class Unit
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllUnitsByUser($user_id) {
        $sql = "SELECT units.id as uid, units.title,units.unit_code,units.credits,enrollments.user_id FROM units
        LEFT JOIN enrollments ON units.id=enrollments.unit_id
        WHERE enrollments.user_id = 1 or enrollments.user_id IS NULL
        ORDER BY units.id ASC";
        $stmt = $this->db->query($sql);
        $stmt->execute(["user_id" => $user_id]);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;        
    }
    
    public function getUserUnits($user_id) {
        $sql = "SELECT * FROM units
        INNER JOIN enrollments ON units.id=enrollments.unit_id
        WHERE enrollments.user_id = 1
        ORDER BY units.id ASC";
        $stmt = $this->db->query($sql);
        $stmt->execute(["user_id" => $user_id]);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;        
    }

    public function enrollUser($user_id, $target_unit) {
        $result = "success";
        try {
            $sql = "INSERT INTO enrollments (user_id, unit_id, create_date_time) VALUES (:user_id, :unit_id, :datetime);";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                "user_id" => $user_id,
                "unit_id" => $target_unit,
                "datetime" => date("Y-m-d H:i:s")
                ]);
        } catch(PDOException $e) {
            print $e->getMessage();
            if ($e->errorInfo[1] == 1062) $result = "duplicate_entry";
        }
        return $result;
    }
    
    public function cancelUser($user_id, $target_unit) {
        $result = "success";
        try {
            $sql = "DELETE FROM enrollments WHERE user_id = :user_id AND unit_id = :unit_id;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                "user_id" => $user_id,
                "unit_id" => $target_unit
                ]);
        } catch(PDOException $e) {
            print $e->getMessage();
        }
        return $result;
    }
}