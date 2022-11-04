<?php
$connect = require "create_connection.php";

$incorrectGuessesToday = 0;
$guessesLeft = 3;
$guessOrGuesses = 'guesses ';

if(isset($_COOKIE['trivia-name']) && isset($_COOKIE['trivia-id'])) {
    $name = $_COOKIE['trivia-name'];
    $id = $_COOKIE['trivia-id'];
}

if(isset($_POST['continue'])) {
    ?>
        <script type="text/javascript">
            window.location.href = 'http://localhost/question.php';
        </script>
    <?php
}

//  incorrect guesses per id
$sql= "SELECT COUNT(a.user_id) as wrong_guesses FROM 
    (SELECT guess_id, user_id, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
    FROM guesses) as a
WHERE a.user_id = $id AND a.date = a.today AND a.correct = 0;";
    $result = $connect->query($sql);

if($result) {
    if(mysqli_num_rows($result) > 0) {
        $guessesRow = $result->fetch_assoc();
        $incorrectGuessesToday = $guessesRow['wrong_guesses'];
        $guessesLeft = 3 - $incorrectGuessesToday;
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
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <button name='continue'>Continue to Questions</button>
    </form>
</main>
</body>
</html>