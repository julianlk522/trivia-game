<?php
$connect = require "create_connection.php";

$id = '';
$name = '';
$incorrectGuessesToday = 0;
$guessesLeft = 3;
$guessOrGuesses = 'guesses ';

if(isset($_COOKIE['trivia-name']) && isset($_COOKIE['trivia-id'])) {
    $id = $_COOKIE['trivia-id'];
    $name = $_COOKIE['trivia-name'];
}

$incorrectGuessesTodaySql = require "incorrect_guesses_query.php";
$result = $connect->query($incorrectGuessesTodaySql);

if($result) {
    if(mysqli_num_rows($result) > 0) {
        $guessesRow = $result->fetch_assoc();
        $incorrectGuessesToday = $guessesRow['wrong_guesses'];
        $guessesLeft = 3 - $incorrectGuessesToday;
        if($guessesLeft === 0) {
            ?>
                <script type="text/javascript">
                    window.location.href = 'http://localhost/game_over.php';
                </script>
            <?php
        }
        $guessOrGuesses = $guessesLeft == 1 ? 'guess ' : 'guesses ';
    } else {
        echo 'Can\'t find guess data.  Come fix me!';
    }
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
    <h1>Are You Ready?</h1>
    <h2><?php echo $name ? ucwords($name) : null; ?></h2>
    <h2>
        <?php
            echo "You have ${guessesLeft} ${guessOrGuesses} left for today";
        ?>
    </h2>

    <br>

    <a href='http://localhost/question.php' role='button' style="width: 50%; margin-bottom: 1rem;">Begin</a>
    <a href='http://localhost/leaderboards.php' role='button' style="width: 50%;" class="secondary">Show Daily Leaderboards</a>
</main>
</body>
</html>