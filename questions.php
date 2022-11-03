<?php

/* 

Header
Guesses remaining
Begin questions prompt / link

Make query to db to see current user's remaining guesses today
  
*/

if(isset($_COOKIE['trivia-user'])) {
    $username = $_COOKIE['trivia-user'];
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Ready For Questions</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
<main style="text-align: center; max-width: 60%;">
    <h1><?php echo $username ? $username : null; ?></h1>
        <h2>You have ___ guesses left for today</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type='submit' value='Continue to Questions' name='continue'>
        </form>
</main>
</body>
</html>