<?php

// $connect = mysqli_connect(
//     'db',
//     'trivia_user',
//     'trivia_pass',
//     'lamp_trivia_game'
// );

// $query = 'SELECT * FROM users';
// $result = mysqli_query($connect, $query);

// echo '<h1>MySQL Content:</h1>';

// while($record = mysqli_fetch_assoc($result)) {
//     echo '<p>'.$record['user_id'].'</p>';
//     echo '<p>'.$record['username'].'</p>';
//     echo '<p>'.$record['password'].'</p>';
//     echo '<hr />';
// }

$connect = new mysqli('db', 'trivia_user', 'trivia_pass', 'lamp_trivia_game');

if($connect->connect_error) {
    die("Connection failed: $connect->connect_error");
}

echo 'Connected!';
echo '<br>';
echo '<br>';

$query = 'SELECT * FROM users';
$result = mysqli_query($connect, $query);

echo '<h1>MySQL Content:</h1>';

while($record = mysqli_fetch_assoc($result)) {
    echo '<p>'.$record['user_id'].'</p>';
    echo '<p>'.$record['username'].'</p>';
    echo '<p>'.$record['password'].'</p>';
    echo '<hr />';
}
?>


<?php
    $feedback = [
        [
            'id' => 1,
            'name' => 'Julian',
            'email' => 'julian@email.com',
            'body' => 'Julian\'s message'
        ],
        [
            'id' => 2,
            'name' => 'Brad',
            'email' => 'brad@email.com',
            'body' => 'Brad\'s message'
        ],
        [
            'id' => 3,
            'name' => 'Sneha',
            'email' => 'sneha@email.com',
            'body' => 'Sneha\'s message'
        ]
    ];

    var_dump($feedback);
?>

<h2>Markup here</h2>

<?php if(empty($feedback)): ?>
    <p>No feedback to show</p>
<?php endif; ?>

<?php foreach($feedback as $item): ?>
    <p>
        <?php echo $item['body']; ?>
    </p>
<?php endforeach; ?>


<div>More markup after script</div>