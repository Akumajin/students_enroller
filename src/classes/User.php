<?php

class User
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * from tbl_users where username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["username" => $username]);
        return $stmt->fetch();
    }

    public function getUserById($id) {
        $sql = "SELECT * from tbl_users where id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["id" => $id]);
        return $stmt->fetch();
    }

    public function insertUser($user_data) {
        $result = "success";
        try {
            $sql = "INSERT INTO tbl_users (username, full_name, password, is_admin) VALUES (:username, :fullname, :password, 0)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                "username" => $user_data["username"],
                "fullname" => $user_data["fullname"],
                "password" => password_hash($user_data['password'], PASSWORD_DEFAULT)
                ]);
        } catch(PDOException $e) {
            if ($e->errorInfo[1] == 1062) $result = "duplicate_entry";
        }
        return $result;
    }

    public function getAllUsers() {
        $sql = "SELECT * from tbl_users";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ComponentEntity($row);
        }
        return $results;        
    }    
}