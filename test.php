<?php

$connect = mysqli_connect(
    'db',
    'trivia_user',
    'trivia_pass',
    'lamp_trivia_game'
);

$query = 'SELECT * FROM users';
$result = mysqli_query($connect, $query);

echo '<h1>MySQL Content:</h1>';

while($record = mysqli_fetch_assoc($result)) {
    echo '<p>'.$record['user_id'].'</p>';
    echo '<p>'.$record['username'].'</p>';
    echo '<p>'.$record['password'].'</p>';
    echo '<hr />';
}