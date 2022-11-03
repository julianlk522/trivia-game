<?php

$connect = require "create_connection.php";

$name = $username = $password = '';
$nameErr = $usernameErr = $passwordErr = '';

if(isset($_POST['navigateToSignIn'])) {
    ?>
        <script type="text/javascript">
            window.location.href = 'http://localhost/signin.php';
        </script>
    <?php
}

if(isset($_POST['submit'])) {

    if(empty($_POST['name'])) {
        $nameErr = 'Error: please provide a name';
    } else {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if(empty($_POST['username'])) {
        $usernameErr = 'Error: please provide a username';
    } else {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if(empty($_POST['password'])) {
        $passwordErr = 'Error: please provide a password';
    } else {
        $password = $_POST['password'];
    }

    if(empty($nameErr) && empty($usernameErr) && empty($passwordErr)) {
        $checkUserSql= "SELECT * FROM users WHERE username = '$username'";
        $testResult = $connect->query($checkUserSql);

        if($testResult) {
            if(mysqli_num_rows($testResult) > 0) {
                echo 'User already exists!  Trying signing in instead.';
            } else {
                echo 'user does not exist';
                echo '<br>';
                echo '<br>';

                $insertSql= "INSERT INTO users (name, username, password) VALUES ('$name', '$username', '$password')";

                $connect->query($insertSql);
                echo 'New user created successfully!';
                // navigate to next page...
            }
        } else {
            echo 'Missing form info, please try again';
            echo '<br>';
            echo '<br>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up for Trivia Game</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
    <main style="text-align: center; max-width: 60%;">
    <h1>Welcome to the Trivia Game!</h1>
        <h2>Sign up to begin</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for='name'>Name: </label>
            <input name='name'>
            <div><?php echo $nameErr ? $nameErr : null; ?></div>
            <br>
            <label for='username'>Username: </label>
            <input name='username'>
            <div><?php echo $usernameErr ? $usernameErr : null; ?></div>
            <br>
            <label for='password'>Password: </label>
            <input name='password' type='password'>
            <div><?php echo $passwordErr ? $passwordErr : null; ?></div>
            <br>
            <input type='submit' value='Submit' name='submit'>
            <label for='navigateToSignIn'>Sign In Instead</label>
            <input name='navigateToSignIn' type='submit' value='Sign In' />
        </form>
    </main>
</body>
</html>
    