<?php
session_start();

$connect = require "create_connection.php";

$username = $password = '';
$usernameErr = $passwordErr = '';

if(array_key_exists('navigateToSignUp', $_POST)) {
    header('Location: /signup.php');
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
        echo 'Successful form submission!';
        echo '<br>';
        echo '<br>';

        $sql= "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $connect->query($sql);

        if($result) {
            if(mysqli_num_rows($result) > 0) {
                echo 'user exists!';
            } else {
                echo 'user does not exist';
            }
        }

        // if(mysqli_query($connect, $sql)) {
        //     echo 'Let\'s see...';
        //     echo '<br>';
        //     echo '<br>';
        //     var_dump()
        // } else {
        //     echo 'Ruh roh';
        //     echo '<br>';
        //     echo '<br>';
        // }
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
        <input type='submit' value='submit' name='submit'>
        <label for='navigateToSignUp'>Sign Up Instead</label>
        <input name='navigateToSignUp' type='submit' value='Go' />
    </form>
    