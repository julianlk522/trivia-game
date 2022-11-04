<?php

//  incorrect guesses today per id
$incorrectGuessesTodaySql="SELECT COUNT(a.user_id) as wrong_guesses FROM 
    (SELECT guess_id, user_id, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
    FROM guesses) as a
WHERE a.user_id = $id AND a.date = a.today AND a.correct = 0;";

return $incorrectGuessesTodaySql;
?>