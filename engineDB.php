<?php

class EngineDB {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "surveydb";
    private $db;

    function connect() {
        $this->db = new mysqli(
            $this->servername,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    function disconnect() {
        if (!$this->db) {
            echo "No connection to close.";
            return;
        }
        if ($this->db->close()) {
            echo "Connection closed.";
        } else {
            echo "Error closing connection.";
        }
    }

    function getDB() {
        return $this->db;
    }

    function checkUserExists($email) {
        $sql = "SELECT * FROM entity_user WHERE email = '$email'";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getUser($email) {
        $sql = "SELECT * FROM entity_user WHERE email = '$email'";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function addUser($email, $password, $isAdmin) {
        $sql = "INSERT INTO entity_user (email, password, isAdmin) VALUES ('$email', '$password', $isAdmin)";
        if ($this->db->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    function updateUser($email, $password) {
        $sql = "UPDATE entity_user SET password = '$password' WHERE email = '$email'";
        if ($this->db->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    function deleteUserById($user_id) {
        $sql = "DELETE FROM entity_user WHERE user_id = $user_id";
        if ($this->db->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    function deleteUserByEmail($email) {
        $sql = "DELETE FROM entity_user WHERE email = '$email'";
        if ($this->db->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    function getAllUsers() {
        $sql = "SELECT * FROM entity_users";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            $users = array();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return $users;
        } else {
            return null;
        }
    }
};
