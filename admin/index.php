<?php
include '../config.php';
$query = new Query;
$query->checkAdminRole(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../images/favicon.ico">
</head>

<body>
    <div class="admin-panel">
        <h2>Welcome Admin!</h2>
        <p>This is your Dashboard.</p>
        <a href="../logout/">Logout</a>
        <!-- <h1><?= $_SESSION['role'] ?></h1>
        <h1><?= $_SESSION['id'] ?></h1>
        <h1><?= $_SESSION['name'] ?></h1>
        <h1><?= $_SESSION['loggedin'] ?></h1>
        <h1><?= $_SESSION['number'] ?></h1>
        <h1><?= $_SESSION['email'] ?></h1>
        <h1><?= $_SESSION['username'] ?></h1>
        <h1><?= $_SESSION['profile_image'] ?></h1>
        <h1><?= $_SESSION['status'] ?></h1></div> -->
</body>

</html>