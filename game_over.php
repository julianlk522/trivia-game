<?php
$connect = require "create_connection.php";

$id = $name = '';
$correctGuessesToday = 0;

if(isset($_COOKIE['trivia-name']) && isset($_COOKIE['trivia-id'])) {
    $id = $_COOKIE['trivia-id'];
    $name = $_COOKIE['trivia-name'];
}

if(isset($_POST['leaderboards'])) {
    ?>
        <script type="text/javascript">
            window.location.href = 'http://localhost/leaderboards.php';
        </script>
    <?php
}

//  correct guesses today per id
$correctGuessesTodaySql="SELECT COUNT(a.user_id) as correct_guesses FROM 
    (SELECT guess_id, user_id, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
    FROM guesses) as a
WHERE a.user_id = $id AND a.date = a.today AND a.correct = 1;";
$correctGuessesResult = $connect->query($correctGuessesTodaySql);

if($correctGuessesResult) {
    if(mysqli_num_rows($correctGuessesResult) > 0) {
        $guessesRow = $correctGuessesResult->fetch_assoc();
        $correctGuessesToday = $guessesRow['correct_guesses'];
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
    <title>Game Over</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
<main style="text-align: center; max-width: 60%;">
    <h1>Sorry <?php echo $name ? $name : "friend" ?>, you're all out of guesses!</h1>
    <h2>You got <?php echo $correctGuessesToday ? ucwords($correctGuessesToday) : null; ?> points</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <button name='leaderboards'>View Daily Leaderboards</button>
    </form>
</main>
</body>
</html>