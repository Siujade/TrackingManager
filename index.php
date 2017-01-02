<?php
include_once "config.php";

spl_autoload_register(function ($class) {
    include_once "$class.php";
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tracking Manager</title>
    <script src="resources/javascript/jquery-3.1.1.slim.min.js"></script>
</head>
<body>
<p>HELLO!</p>
<script>
    $('p').text('HELLO THEREE...');
</script>
</body>
</html>