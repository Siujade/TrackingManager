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
    <title>Title</title>
    <link rel="stylesheet" href="resources/css/main.css">
    <script src="resources/javascript/jquery-3.1.1.slim.min.js"></script>
</head>
<body>
<header>
    <ul>
        <li class="active" data-target="actions"><a href="#">Actions</a></li>
        <li data-target="controls"><a href="#">Controls</a></li>
        <li data-target="history"><a href="#">History</a></li>
    </ul>
</header>
<div class="container">
    <section id="actions">
        <h1>Actions HTML HERE</h1>
    </section>
    <section id="controls">
        <h1>CONTROLS HTML HERE</h1>
    </section>
    <section id="history">
        <h1>History HTML HERE</h1>
    </section>
</div>
<script>
    $('li').click(function(){
        var self = $(this);
        var window = self.attr('data-target');

        if(self.hasClass('active')) {
            return false;
        }

        $('section').hide();
        $('section#'+window).show();
        $('.active').removeClass('active');
        self.addClass('active');
    })
</script>
</body>
</html>