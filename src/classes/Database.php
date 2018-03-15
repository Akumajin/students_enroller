<?php

class Database
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function createDbIfNotExist() {
        try {
            $db =  $this->db;
            $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
            $sql ="CREATE TABLE IF NOT EXISTS enrollments(
               id INT(11) AUTO_INCREMENT PRIMARY KEY,
               user_id int(11) NOT NULL,
               module_id int(11) NOT NULL,
               create_date_time datetime NOT NULL);
            CREATE TABLE IF NOT EXISTS modules (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                title varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                description text COLLATE utf8mb4_unicode_ci NOT NULL,
                credits int(11) NOT NULL);          
            CREATE TABLE IF NOT EXISTS users (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                username varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                full_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                password varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
            );";
             $db->exec($sql);
        } catch(PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}