<?php
/* 

Progress bar for incorrect guesses out of max incorrect (3)

*/
$connect = require "create_connection.php";

$id = '';
$name = '';
$totalGuessesToday = 0;
$difficultyLevel = 'easy';

$questionNumber = 1;
$questionMessage = '';
$questionChoices = [];
$questionAnswer = '';

if(isset($_COOKIE['trivia-name']) && isset($_COOKIE['trivia-id'])) {
    $id = $_COOKIE['trivia-id'];
    $name = $_COOKIE['trivia-name'];
}

/*
if(isset($_POST['checkAnswer'])) {
    ?>
        //  check the answer
    <?php
}
*/

$totalGuessesTodaySql = "SELECT COUNT(a.user_id) as total_daily_guesses, a.user_id as id FROM 
(SELECT guess_id, user_id, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
FROM guesses) as a
WHERE a.date = a.today AND a.user_id = $id
GROUP BY id;";
$result = $connect->query($totalGuessesTodaySql);

if($result) {
    if(mysqli_num_rows($result) > 0) {
        $totalGuessesRow = $result->fetch_assoc();
        $totalGuessesToday = $totalGuessesRow['total_daily_guesses'];
        $questionNumber = $totalGuessesToday + 1;
        if($questionNumber > 5) {
            $difficultyLevel = 'medium';
        } else if ($questionNumber > 10) {
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
    <main class='container'>
            <h1>
                <?php echo "Question ${questionNumber}"; ?>
                <?php echo $difficultyLevel ? '('.ucwords($difficultyLevel).')' : null; ?>
            </h1>
            <h2><?php echo $questionMessage; ?></h2>
        <br>
        <br>
            <select>
                <option value="" disabled selected>Choose your answer</option>
                <?php foreach($questionChoices as $choice): ?>
                    <option value="<?php echo $choice; ?>">
                        <?php echo $choice; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <button name='checkAnswer'>Submit</button>
        </form>
    </main>
</body>
</html>