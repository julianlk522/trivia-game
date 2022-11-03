<?php
session_start();

$connect = require "create_connection.php";

$name = $username = $password = '';
$nameErr = $usernameErr = $passwordErr = '';

if(array_key_exists('navigateToSignIn', $_POST)) {
    header('Location: /signin.php');
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
        echo 'Successful form submission!';
        echo '<br>';
        echo '<br>';

        $sql= "INSERT INTO users (username, password) VALUES ('$username', '$password')";

        if(mysqli_query($connect, $sql)) {
            echo 'User successfully added to MySQL!';
            echo '<br>';
            echo '<br>';
        } else {
            echo 'Ruh roh';
            echo '<br>';
            echo '<br>';
        }
        // header('Location: /index.php');
    } else {
        echo 'Missing form info, please try again';
        echo '<br>';
        echo '<br>';
    }
}

// session_destroy();
?>

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
        <input type='submit' value='submit' name='submit'>
        <label for='navigateToSignIn'>Sign In Instead</label>
        <input name='navigateToSignIn' type='submit' value='Go' />
    </form>
    