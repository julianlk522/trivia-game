<?php

/* 

Query for number of user's guesses today and display "Question (n+1)"

*/

$questionNumber = 1;
$questionMessage = '';
$questionChoices = [];
$questionAnswer = '';
$questionDifficulty = '';

$triviaEndpointHandle = curl_init("https://the-trivia-api.com/api/questions?limit=1");

curl_setopt($triviaEndpointHandle, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($triviaEndpointHandle);

curl_close($triviaEndpointHandle);

$rawTriviaData = json_decode($response, true);
$triviaPropertiesArray = $rawTriviaData[0];

$questionMessage = $triviaPropertiesArray['question'];
$questionDifficulty = $triviaPropertiesArray['difficulty'] ? $triviaPropertiesArray['difficulty'] : null;
$questionChoices = $triviaPropertiesArray['incorrectAnswers'];
$questionAnswer = $triviaPropertiesArray['correctAnswer'];
array_push($questionChoices, $questionAnswer);
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
                <?php echo $questionDifficulty ? "($questionDifficulty)" : null; ?>
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
    </main>
</body>
</html>