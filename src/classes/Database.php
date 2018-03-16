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
            $sql ="CREATE TABLE IF NOT EXISTS tbl_users (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                username varchar(20) NOT NULL UNIQUE,
                full_name varchar(20) NOT NULL,
                password varchar(100) NOT NULL) ENGINE=InnoDB;
            CREATE TABLE IF NOT EXISTS tbl_units (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                title varchar(200) NOT NULL,
                unit_code varchar(50) NOT NULL,
                credits int(11) NOT NULL) ENGINE=InnoDB;
            CREATE TABLE IF NOT EXISTS tbl_enrollments(
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                user_id int(11) NOT NULL,
                unit_id int(11) NOT NULL,
                create_date_time datetime NOT NULL,
                UNIQUE KEY (unit_id, user_id),
                FOREIGN KEY (user_id) REFERENCES tbl_users(id),
                FOREIGN KEY (unit_id) REFERENCES tbl_units(id)) ENGINE=InnoDB;
            INSERT INTO tbl_units (id, title, unit_code, credits) VALUES
                (1, 'Academic Listening and Speaking', 'LANG10007', 10),
                (2, 'Academic Reading and Writing', 'LANG10008', 10),
                (3, 'Communicating Science', 'PHYS10001', 20),
                (4, 'Contemporary European Cinema', 'MODL10009', 20),
                (5, 'Historical Studies 1: Western Art Music (up to 1750)', 'MUSI10045', 20),
                (6, 'Introduction to Cognitive Psychology', 'PSYC10006', 10),
                (7, 'Living Religions', 'THRS10028', 20),
                (8, 'Principles of Economics', 'EFIM10050', 20),
                (9, 'The Archaeology of Myth: From the Trojan War to the end of Atlantis', 'CLAS12384', 20),
                (10, 'Understanding Crime, Harm and Society', 'SPOL10020', 20);";
             $db->exec($sql);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}