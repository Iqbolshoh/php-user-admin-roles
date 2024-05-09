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

    // validate(): here converts @#$%^ characters to html
    function validate($value)
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        $value = mysqli_real_escape_string($this->conn, $value);
        return $value;
    }

    // executeQuery(): to execute the query
    public function executeQuery($sql)
    {
        $result = $this->conn->query($sql);
        if ($result === false) {
            die("Xatolik: " . $this->conn->error);
        }
        return $result;
    }

    // select(): To add information to the database.
    public function select($table, $columns = "*", $condition = "")
    {
        $sql = "SELECT $columns FROM $table $condition";
        return $this->executeQuery($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // insert(): To add information to the database.
    public function insert($table, $data)
    {
        $keys = implode(', ', array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        return $this->executeQuery($sql);
    }

    // update(): To update data in the database.
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

    // delete(): To delete information.
    public function delete($table, $condition = "")
    {
        $sql = "DELETE FROM $table $condition";
        return $this->executeQuery($sql);
    }

    // hashPassword(): Password hashing
    function hashPassword($password)
    {
        $key = "AccountPassword";
        return hash_hmac('sha256', $password, $key);
    }

    // authenticate(): To verify the user's login information.
    public function authenticate($username, $password, $table)
    {
        $username = $this->validate($username);
        $condition = "WHERE username = '" . $username . "' AND password = '" . $this->hashPassword($password) . "'";
        return $this->select($table, "*", $condition);
    }

    // registerUser(): To register a new user.
    public function registerUser($name, $number, $email, $username, $password, $profile_image, $role)
    {
        $name = $this->validate($name);
        $number = $this->validate($number);
        $email = $this->validate($email);
        $username = $this->validate($username);

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

    // saveImage(): To upload a picture
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

    // checkAuthentication(): Checking roles and directing them
    function checkAuthentication()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            if ($_SESSION['role'] === 'admin') {
                header("Location: /admin/");
                exit;
            } elseif ($_SESSION['role'] === 'seller') {
                header("Location: /seller/");
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

    // checkAdminRole(): For Admin access only
    function checkAdminRole()
    {
        if ($_SESSION['role'] !== 'admin') {
            $this->checkAuthentication();
            exit;
        }
    }

    // checkSellerRole(): For Seller access only
    function checkSellerRole()
    {
        if ($_SESSION['role'] !== 'seller') {
            $this->checkAuthentication();
            exit;
        }
    }

    // checkUserRole(): For user access only
    function checkUserRole()
    {
        if ($_SESSION['role'] !== 'user') {
            $this->checkAuthentication();
            exit;
        }
    }
}
