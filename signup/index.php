<?php
session_start();

include '../config.php';

$query = new Query();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $query->checkAuthentication();
    exit;
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $profile_image = $query->saveImage($_FILES['image'], "../images/");

    $result = $query->registerUser($name, $number, $email, $username, $password, $profile_image, $role);

    if ($result) {
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $result;
        $_SESSION['name'] = $name;
        $_SESSION['number'] = $number;
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $username;
        $_SESSION['profile_image'] = $profile_image;
        $_SESSION['role'] = $role;

        $query->checkAuthentication();
        exit;
    } else {
        $error = "Xatolik: Ma'lumotlarni saqlashda xatolik yuz berdi";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <h2>Sign Up</h2>
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="number" placeholder="Number" required>

        <select name="role" required>
            <option value="" disabled selected>Select Role</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <div class="file-input-container">
            <input type="file" name="image" id="file-input" accept="image/*" />
            <label for="file-input" class="custom-file-upload">
                <i class="fas fa-upload"></i> Choose Image
            </label>
        </div>

        <input type="submit" name="submit" value="Submit">
        <p>Already have an account? <a href="../login/">Log in</a></p>
    </form>

</body>

</html>