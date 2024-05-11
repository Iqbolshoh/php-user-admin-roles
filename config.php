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
    public function registerUser($name, $email, $username, $password, $role)
    {
        $name = $this->validate($name);
        $email = $this->validate($email);
        $username = $this->validate($username);

        $password_hash = $this->hashPassword($password);

        $data = array(
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => $password_hash,
            'role' => $role
        );

        $user_id = $this->insert('users', $data);

        if ($user_id) {
            return $user_id;
        }
        return false;
    }

    // checkAuthentication(): Checking roles and directing them
    function checkAuthentication()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            if ($_SESSION['role'] === 'admin') {
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

    // checkAdminRole(): For Admin access only
    function checkAdminRole()
    {
        if ($_SESSION['role'] !== 'admin') {
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
