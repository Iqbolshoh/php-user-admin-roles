<?php

session_start();

class Query
{
    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Roles";
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection error: " . $this->conn->connect_error);
        }
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // sanitize() sanitizes input by escaping special characters
    function sanitize($value)
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        $value = mysqli_real_escape_string($this->conn, $value);
        return $value;
    }

    // executeQuery() executes a SQL query
    public function executeQuery($sql)
    {
        $result = $this->conn->query($sql);
        if ($result === false) {
            die("Error: " . $this->conn->error);
        }
        return $result;
    }

    // select() retrieves data from the database
    public function select($table, $columns = "*", $condition = "")
    {
        $sql = "SELECT $columns FROM $table $condition";
        return $this->executeQuery($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // insert() inserts data into the database
    public function insert($table, $data)
    {
        $keys = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        return $this->executeQuery($sql);
    }

    // update() updates data in the database
    public function update($table, $data, $condition = "")
    {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "$key = '$value', ";
        }
        $set = rtrim($set, ', ');
        $sql = "UPDATE $table SET $set $condition";
        return $this->executeQuery($sql);
    }

    // delete() deletes data from the database
    public function delete($table, $condition = "")
    {
        $sql = "DELETE FROM $table $condition";
        return $this->executeQuery($sql);
    }

    // hashPassword() hashes a password using a key
    function hashPassword($password)
    {
        $key = "AccountPassword";
        return hash_hmac('sha256', $password, $key);
    }

    // authenticate() verifies user credentials
    public function authenticate($username, $password, $table)
    {
        $username = $this->sanitize($username);
        $condition = "WHERE username = '" . $username . "' AND password = '" . $this->hashPassword($password) . "'";
        return $this->select($table, "*", $condition);
    }

    // registerUser() registers a new user
    public function registerUser($name, $number, $email, $username, $password, $profile_image, $role)
    {
        $name = $this->sanitize($name);
        $number = $this->sanitize($number);
        $email = $this->sanitize($email);
        $username = $this->sanitize($username);

        $password_hash = $this->hashPassword($password);

        $data = array(
            'name' => $name,
            'number' => $number,
            'email' => $email,
            'username' => $username,
            'password' => $password_hash,
            'profile_image' => $profile_image,
            'role' => $role
        );

        $user_id = $this->insert('accounts', $data);

        if ($user_id) {
            return $user_id;
        }
        return false;
    }

    // saveImage() handles file upload and saving
    function saveImage($files, $path)
    {
        if (is_array($files['tmp_name'])) {
            $uploaded_files = array();
            foreach ($files['tmp_name'] as $index => $tmp_name) {
                $file_name = $files['name'][$index];
                $file_info = pathinfo($file_name);
                $file_extension = $file_info['extension'];
                $new_file_name = md5($tmp_name . date("Y-m-d_H-i-s") . $_SESSION['username']) . "." . $file_extension;
                if (move_uploaded_file($tmp_name, $path . $new_file_name)) {
                    $uploaded_files[] = $new_file_name;
                }
            }
            return $uploaded_files;
        } else {
            $file_name = $files['name'];
            $file_tmp = $files['tmp_name'];

            $file_info = pathinfo($file_name);
            $file_format = $file_info['extension'];

            $new_file_name = md5($file_tmp . date("Y-m-d_H-i-s") . $_SESSION['username']) . "." . $file_format;

            if (move_uploaded_file($file_tmp, $path . $new_file_name)) {
                return $new_file_name;
            }
            return false;
        }
    }

    // isBlocked() checks if a user is blocked
    public function isBlocked()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            $result = $this->select('accounts', 'status', 'WHERE username = "' . $_SESSION['username'] . '"');
            return $result[0]['status'] === 'blocked';
        }
        return false;
    }

    // checkAuthentication() redirects users based on their role
    function checkAuthentication()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            if ($this->isBlocked()) {
                header("Location: /blocked_page.php");
                exit;
            } elseif ($_SESSION['role'] === 'admin') {
                header("Location: /admin/");
                exit;
            } elseif ($_SESSION['role'] === 'user') {
                header("Location: /");
                exit;
            }
        } else {
            header("Location: /login/");
            exit;
        }
    }

    // checkAdminRole() restricts access to admin users only
    function checkAdminRole()
    {
        if ($_SESSION['role'] !== 'admin' or $this->isBlocked()) {
            $this->checkAuthentication();
            exit;
        }
    }

    // checkUserRole() restricts access to general users only
    function checkUserRole()
    {
        if ($_SESSION['role'] !== 'user' or $this->isBlocked()) {
            $this->checkAuthentication();
            exit;
        }
    }

    // getCategories() retrieves categories from the database
    public function getCategories()
    {
        $categories = array();
        foreach ($this->select('categories', 'category_name') as $row) {
            $categories[] = $row['category_name'];
        }
        return $categories;
    }
}
