<?php
namespace services;

use mysqli;

class DB
{
    public $db_host = 'localhost';
    public $db_user = 'root';
    public $db_password = '';
    public $db_database = 'muzz';

    public function connectToDatabase() {
        // Making connection.
        $conn = new mysqli(
          $this->db_host,
          $this->db_user,
          $this->db_password,
          $this->db_database
        );
        // Checking connection
        if ($conn->connect_error) {
            die("Connection failed ".$conn->connect_error);
        }

        return $conn;
    }
}
