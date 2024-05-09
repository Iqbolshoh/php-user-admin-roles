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
    $role = 'user';
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
</head>

<body>
    <?php if (isset($error)) : ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <h2>Sign Up</h2>
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="number" placeholder="Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <div class="file-input-container">
            <input type="file" name="image" id="file-input" accept="image/*" />
            <label for="file-input" class="custom-file-upload">Choose Image</label>
        </div>

        <input type="submit" name="submit" value="Submit">
        <p>Already have an account? <a href="../login/">Log in</a></p>
    </form>

</body>

</html>