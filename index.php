<?php include 'config.php';
$query = new Query;
$query->checkUserRole(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../images/favicon.ico">
</head>

<body>
    <div class="admin-panel">
        <h2>Welcome User!</h2>
        <p>This is your Dashboard.</p>
        <a href="./logout/">Logout</a>
    </div>
    <!-- <h3> <?= $_SESSION['role'] ?></h3>
    <h3> <?= $_SESSION['id'] ?></h3>
    <h3> <?= $_SESSION['name'] ?></h3>
    <h3> <?= $_SESSION['loggedin'] ?></h3>
    <h3> <?= $_SESSION['number'] ?></h3>
    <h3> <?= $_SESSION['email'] ?></h3>
    <h3> <?= $_SESSION['username'] ?></h3>
    <h3> <?= $_SESSION['profile_image'] ?></h3> -->

</body>

</html>