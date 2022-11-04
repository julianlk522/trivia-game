<?php
$connect = new mysqli('db', 'trivia_user', 'trivia_pass', 'lamp_trivia_game');

if($connect->connect_error) {
    die("Connection failed: $connect->connect_error");
    echo 'Something went wrong while connecting to the database :(';
}

return $connect;
?>