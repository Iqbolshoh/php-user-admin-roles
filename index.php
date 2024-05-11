<?php
include 'config.php';
$query = new Query;
$query->checkUserRole(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="admin-panel">
        <h2>Welcome User!</h2>
        <p>This is your Dashboard.</p>
        <a href="../logout/">Logout</a>
    </div>
</body>

</html>