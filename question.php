<?php
$connect = require "create_connection.php";

/*

To-do: fix bug where refresh causes repeat of last answer grading

*/

$id = '';
$name = '';
$totalGuessesToday = 0;
$guessesLeft = 3;
$difficultyLevel = 'easy';
$dailyQuestionNumber = 1;

$selected = '';
$correct = null;
$guessErr = '';

$questionMessage = '';
$questionChoices = [];
$questionAnswer = '';
$filteredQuestionAnswer = '';

//  query for incorrect guesses, determine guesses left for today
function queryUserIncorrectGuesses() {
    global $connect;
    //  id required in $incorrectGuessesTodaySql
    global $id;
    global $guessesLeft;
    global $correct;

    $incorrectGuessesTodaySql = require "incorrect_guesses_query.php";
    $incorrectGuessesResult = $connect->query($incorrectGuessesTodaySql);

    if($incorrectGuessesResult && mysqli_num_rows($incorrectGuessesResult) > 0) {
        $guessesRow = $incorrectGuessesResult->fetch_assoc();
        $incorrectGuessesToday = $guessesRow['wrong_guesses'];
        $guessesLeft = 3 - $incorrectGuessesToday;

        //  redirect if no guesses remaining
        if($correct === 0 && $guessesLeft < 1) {
            ?>
                <script type="text/javascript">
                    window.location.href = 'http://localhost/game_over.php';
                </script>
            <?php
        }
    }
}

//  query for daily total guesses, determine question number and difficulty level
function queryUserTotalGuesses() {
    global $connect;
    global $id;
    global $totalGuessesToday;
    global $dailyQuestionNumber;
    global $difficultyLevel;

    $totalGuessesTodaySql = "SELECT COUNT(a.user_id) as total_guesses_today, a.user_id as id FROM 
    (SELECT guess_id, user_id, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
    FROM guesses) as a
    WHERE a.date = a.today AND a.user_id = $id
    GROUP BY id;";
    $totalGuessesResult = $connect->query($totalGuessesTodaySql);

    if($totalGuessesResult) {
        if(mysqli_num_rows($totalGuessesResult) > 0) {
            $totalGuessesRow = $totalGuessesResult->fetch_assoc();
            $totalGuessesToday = $totalGuessesRow['total_guesses_today'];
            $dailyQuestionNumber = $totalGuessesToday + 1;
            if($dailyQuestionNumber > 10) {
                $difficultyLevel = 'hard';
            } else if ($dailyQuestionNumber > 5) {
                $difficultyLevel = 'medium';
            }
        }
    }
}

//  request question data from API, decode and shuffle answer in with choices
function fetchAndParseTriviaData() {
    global $difficultyLevel;
    global $questionMessage;
    global $questionChoices;
    global $questionAnswer;
    global $filteredQuestionAnswer;

    $triviaEndpointHandle = curl_init("https://the-trivia-api.com/api/questions?limit=1&difficulty=${difficultyLevel}");

    curl_setopt($triviaEndpointHandle, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($triviaEndpointHandle);
    curl_close($triviaEndpointHandle);

    $rawTriviaData = json_decode($response, true);
    $triviaPropertiesArray = $rawTriviaData[0];

    $questionMessage = $triviaPropertiesArray['question'];
    $questionChoices = $triviaPropertiesArray['incorrectAnswers'];
    $questionAnswer = $triviaPropertiesArray['correctAnswer'];
    $filteredQuestionAnswer = str_replace("&nbsp;", "", $questionAnswer);
    array_push($questionChoices, $filteredQuestionAnswer);
    shuffle($questionChoices);
    foreach($questionChoices as $questionChoice) {
        $questionChoice = ucwords($questionChoice);
    }
}

if(isset($_COOKIE['trivia-name']) && isset($_COOKIE['trivia-id'])) {
    $id = $_COOKIE['trivia-id'];
    $name = $_COOKIE['trivia-name'];
}

//  if answer selected and user clicks check answer then test if the answer was correct and insert a row into the guesses table
if(isset($_POST['checkAnswer']) && isset($_POST['answerSelect'])) {
    $selected = $_POST['answerSelect'];
    $answer = $_POST['correct'];
    $correct = $answer === $selected ? 1 : 0;

    $addGuessSql = "INSERT INTO guesses (`guess_id`, `user_id`, `date`, `correct`) VALUES (NULL, '$id', CURRENT_TIMESTAMP, '$correct');";
    $addGuessResult = $connect->query($addGuessSql);

    if(!$addGuessResult) {
        echo 'Error when adding new guess.  Come fix me!';
    //  query totalGuesses and guessesLeft if successfully added row to guesses table and use in next page load
    } else {
        queryUserIncorrectGuesses();
        queryUserTotalGuesses();
        //  use dailyQuestionNumber to define correct API endpoint based on difficultyLevel
        fetchAndParseTriviaData();
    }

//  Display error if user submits with no answer selected
} elseif(isset($_POST['correct'])) {
        $guessErr = 'Please select an answer before submitting';

//  run queries on first page load before $correct is defined and before user has selected a score
} else {
    queryUserIncorrectGuesses();
    queryUserTotalGuesses();
    fetchAndParseTriviaData();
}
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
                    $additionalText = $dailyQuestionNumber === 11 || $dailyQuestionNumber === 6 ? ' Difficulty increased.' : null;
                    echo 'Great job! 1 point added to your score.';
                    echo "<br>";
                    echo $additionalText;
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

            <input type="hidden" name='correct' value=<?php $filteredQuestionAnswer ? var_export($filteredQuestionAnswer) : null ?>>

            <button name='checkAnswer'>Submit</button>
        </form>

        <br>
        <br>

        <progress id="guessesLeftProgress" value=<?php var_export($guessesLeft / 3); ?> max="1"> <?php $guessesLeft/3 * 100 . '%' ?></progress>

        <p style="text-align: end;"><?php echo $guessesLeft; ?>/3 Guesses Remaining!</p>
    </main>
</body>
</html>