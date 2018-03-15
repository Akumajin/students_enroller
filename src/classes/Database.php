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
            $sql ="CREATE TABLE IF NOT EXISTS users (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                username varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
                full_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                password varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
            );
            CREATE TABLE IF NOT EXISTS modules (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                title varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                description text COLLATE utf8mb4_unicode_ci,
                credits int(11) NOT NULL);
            CREATE TABLE IF NOT EXISTS enrollments(
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                user_id int(11) NOT NULL,
                module_id int(11) NOT NULL,
                create_date_time datetime NOT NULL,
                UNIQUE KEY (module_id, user_id),
                CONSTRAINT FK_User FOREIGN KEY (user_id) REFERENCES users(id),
                CONSTRAINT FK_Module FOREIGN KEY (module_id) REFERENCES modules(id));";
             $db->exec($sql);
        } catch(PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}