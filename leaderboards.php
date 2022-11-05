<?php 
$connect = require "create_connection.php";

$scoresTodaySql="SELECT count(a.id) as correct_guesses, a.name as name
FROM
	(SELECT guess_id, users.user_id as id, users.name as name, DATE_FORMAT(date, '%Y-%m-%d') as date, CURDATE() as today, correct
    FROM guesses JOIN users ON guesses.user_id = users.user_id) as a
WHERE a.date = a.today AND a.correct = 1
GROUP BY a.name;";
$userScoresResult = $connect->query($scoresTodaySql);

$date = date("m/d/Y");
$userScores = [];

if(isset($_POST['signIn'])) {
    ?>
        <script type="text/javascript">
            window.location.href = 'http://localhost/signin.php';
        </script>
    <?php
}

if($userScoresResult) {
    if(mysqli_num_rows($userScoresResult) > 0) {
        while($userData = mysqli_fetch_assoc($userScoresResult)) {
            $name = $userData['name'];
            $score = $userData['correct_guesses'];
            $userScores[$name] = $score;
        }
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
    <title>Daily Leaderboards</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
    <main style="max-width: 60%;">
        <h1 style="text-align: center;">High Scores <?php echo $date ? 'for '.$date : null; ?></h1>
        
        <br>

        <table role="grid">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50%;">Name</th>
                        <th scope="col">Score</th>
                    </tr>
                </thead>
            <tbody>
                <?php foreach ($userScores as $key => $score): ?>
                    <tr scope="row">
                        <td><?= $key ?></td>
                        <td><?= $score ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <br>
        <br>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <button name='signIn' class="outline contrast">Back To Sign In</button>
    </form>
    </main>
</body>
</html>