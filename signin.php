<?php
$connect = require "create_connection.php";

$username = $password = '';
$usernameErr = $passwordErr = '';

if(isset($_POST['navigateToSignUp'])) {
    ?>
        <script type="text/javascript">
            window.location.href = 'http://localhost/signup.php';
        </script>
    <?php
}

if(isset($_POST['submit'])) {

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

    if(empty($usernameErr) && empty($passwordErr)) {
        $checkUserSql= "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $checkResult = $connect->query($checkUserSql);

        if($checkResult && mysqli_num_rows($checkResult) > 0) {
            $row = $checkResult -> fetch_assoc();
            $nameToBeStored = $row["name"];
            $idToBeStored = $row["user_id"];
            setcookie("trivia-name", $nameToBeStored, time() + 86400);
            setcookie("trivia-id", $idToBeStored, time() + 86400);
            ?>
                <script type="text/javascript">
                    window.location.href = 'http://localhost/ready.php';
                </script>
            <?php
        } else {
            echo 'User not found.  Try re-typing your password or signing up instead';
        }
    } else {
        echo 'Missing username or password input, please try again';
        echo '<br>';
        echo '<br>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In to Trivia Game</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>
<main style="text-align: center; max-width: 60%;">
    <h1>Welcome to the Trivia Game!</h1>
        <h2>Sign In to begin</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for='username'>Username: </label>
            <input name='username'>
            <div><?php echo $usernameErr ? $usernameErr : null; ?></div>
            <br>
            <label for='password'>Password: </label>
            <input name='password' type='password'>
            <div><?php echo $passwordErr ? $passwordErr : null; ?></div>
            <br>
            <input type='submit' value='Submit' name='submit'>
            <label for='navigateToSignUp'>Sign Up Instead</label>
            <input name='navigateToSignUp' type='submit' value='Sign Up' />
        </form>
</main>
</body>
</html>
    