<?php

class User
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function dada() {
        return "salam";
    }
    public function getUserByUsernameAndPassword($username, $password) {

        $sql = "SELECT * from users where username = :username and password = :password";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["username" => $username,"password" => $password]);

        return $stmt->fetch();
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