<?php
/* 

insert rows into guesses table when user submits

*/
$connect = require "create_connection.php";
$guessesLeft = require "ready.php";

$questionMessage = '';
$questionChoices = [];
$questionAnswer = '';

$id = '';
$name = '';
$totalGuessesToday = 0;
$difficultyLevel = 'easy';
$dailyQuestionNumber = 1;

$selected = '';
$correct = null;
$guessErr = '';

if(isset($_COOKIE['trivia-name']) && isset($_COOKIE['trivia-id'])) {
    $id = $_COOKIE['trivia-id'];
    $name = $_COOKIE['trivia-name'];
}

if(isset($_POST['checkAnswer']) && isset($_POST['answerSelect'])) {
        $correctAnswer = $_POST['correct'];
        $selected = $_POST['answerSelect'];
        $correct = $correctAnswer === $selected ? 1 : 0;

        $addGuessSql = "INSERT INTO guesses (`guess_id`, `user_id`, `date`, `correct`) VALUES (NULL, '$id', CURRENT_TIMESTAMP, '$correct');";
        $addGuessResult = $connect->query($addGuessSql);

        if(!$addGuessResult) {
            echo 'Error when adding new guess.  Come fix me!';
        }
} else {
    $guessErr = 'Please select an answer before submitting';
}

$totalGuessesTodaySql = "SELECT COUNT(a.user_id) as total_guesses_today, a.user_id as id FROM 
(SELECT guess_id, user_id, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
FROM guesses) as a
WHERE a.date = a.today AND a.user_id = $id
GROUP BY id;";
$result = $connect->query($totalGuessesTodaySql);

if($result) {
    if(mysqli_num_rows($result) > 0) {
        $totalGuessesRow = $result->fetch_assoc();
        $totalGuessesToday = $totalGuessesRow['total_guesses_today'];
        $dailyQuestionNumber = $totalGuessesToday + 1;
        if($dailyQuestionNumber > 5) {
            $difficultyLevel = 'medium';
        } else if ($dailyQuestionNumber > 10) {
            $difficultyLevel = 'hard';
        }
    } else {
        echo 'Can\'t find guess data.  Come fix me!';
    }
}

$triviaEndpointHandle = curl_init("https://the-trivia-api.com/api/questions?limit=1&difficulty=${difficultyLevel}");

curl_setopt($triviaEndpointHandle, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($triviaEndpointHandle);
curl_close($triviaEndpointHandle);

$rawTriviaData = json_decode($response, true);
$triviaPropertiesArray = $rawTriviaData[0];

$questionMessage = $triviaPropertiesArray['question'];
$questionChoices = $triviaPropertiesArray['incorrectAnswers'];
$questionAnswer = $triviaPropertiesArray['correctAnswer'];
array_push($questionChoices, $questionAnswer);
shuffle($questionChoices);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
    <main class='container' style="max-width: 60%;">

        <p><?php echo $guessErr ? $guessErr : null; ?></p>
        <p>
            <?php if($correct !== null) {
                if($correct) {
                    echo "Great job! 1 point added to your score";
                } else {
                    echo "Sorry, that wasn't the correct answer.";
                }
            }; ?>
        </p>
        <h1>
            <?php echo "Question ${dailyQuestionNumber}"; ?>
            <?php echo $difficultyLevel ? '('.ucwords($difficultyLevel).')' : null; ?>
        </h1>
        <h2><?php echo $questionMessage; ?></h2>

        <br>
        <br>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <select name='answerSelect'>
                <option value="" disabled selected>Choose your answer</option>
                <?php foreach($questionChoices as $choice): ?>
                    <option value="<?php echo $choice; ?>">
                        <?php echo $choice; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type='hidden' name='correct' value=<?php var_export($questionAnswer); ?>>

            <button name='checkAnswer'>Submit</button>
        </form>

        <br>
        <br>

        <progress id="guessesLeftProgress" value=<?php var_export($guessesLeft / 3); ?> max="1"> <?php $guessesLeft/3 * 100 . '%' ?></progress>

        <p style="text-align: end;"><?php echo $guessesLeft; ?>/3 Guesses Remaining!</p>
    </main>
</body>
</html>