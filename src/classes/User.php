<?php

class User
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getUserByUsername($username) {

        $sql = "SELECT * from users where username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["username" => $username]);

        return $stmt->fetch();
    }
    public function insertUser($user_data) {
        $sql = "INSERT INTO `users` (`username`, `full_name`, `password`) VALUES (:username, :fullname, :password)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "username" => $user_data["username"],
            "fullname" => $user_data["fullname"],
            "password" => password_hash($user_data['password'], PASSWORD_DEFAULT)
            ]);
        return $result;
    }
    public function getAllUsers() {
        $sql = "SELECT * from users";
        $stmt = $this->db->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ComponentEntity($row);
        }
        return $results;        
    }    
}