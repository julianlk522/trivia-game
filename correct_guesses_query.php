<?php

//  correct guesses today per id
$correctGuessesTodaySql="SELECT COUNT(a.user_id) as correct_guesses FROM 
    (SELECT guess_id, user_id, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
    FROM guesses) as a
WHERE a.user_id = $id AND a.date = a.today AND a.correct = 1;";

return $correctGuessesTodaySql;
?>