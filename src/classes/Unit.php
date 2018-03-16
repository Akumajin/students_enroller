<?php

class Unit
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllUnitsByUser($user_id) {
        $sql = "SELECT tbl_units.id as uid, tbl_units.title, tbl_units.unit_code, tbl_units.credits, tbl_enrollments.user_id FROM tbl_units
        LEFT JOIN tbl_enrollments ON tbl_units.id = tbl_enrollments.unit_id
        WHERE tbl_enrollments.user_id = :user_id or tbl_enrollments.user_id IS NULL
        ORDER BY uid ASC;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array("user_id" => $user_id));
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;        
    }
    
    public function getUserUnits($user_id) {
        $sql = "SELECT * FROM tbl_units
        INNER JOIN tbl_enrollments ON tbl_units.id = tbl_enrollments.unit_id
        WHERE tbl_enrollments.user_id = :user_id
        ORDER BY tbl_units.id ASC";
        $stmt = $this->db->prepare($sql);
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
            $sql = "INSERT INTO tbl_enrollments (user_id, unit_id, create_date_time) VALUES (:user_id, :unit_id, :datetime);";
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
            $sql = "DELETE FROM tbl_enrollments WHERE user_id = :user_id AND unit_id = :unit_id;";
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