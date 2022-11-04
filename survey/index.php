<?php
    error_reporting(0);

    define('CONFIG', array(
        'host' => '',
        'user' => '',
        'password' => '',
        'db' => ''
    ));

    $conn = mysqli_connect(CONFIG['host'], CONFIG['user'], CONFIG['password'], CONFIG['db']);

    if(
        isset($_POST['submit']) &&
        isset($_POST['answer'])
    ) {
        $id = $_POST['id'];
        $answer = $_POST['answer'];

        $query = "
            UPDATE
        ";

        if($answer == "answer-1")
        {
            $query .= "
                    question
                SET
                    answer_1_count = answer_1_count + 1
            ";
        } elseif($answer == "answer-2")
        {
            $query .= "
                    question
                SET
                    answer_2_count = answer_2_count + 1
            ";
        }

        $query .= "
            WHERE
                id_question = $id;
        ";
        
        mysqli_query($conn, $query);

        $notification = "Zagłosowano!";
    }

    $query = "
        SELECT
            *
        FROM
            question
        WHERE
            CURRENT_TIMESTAMP > start AND
            CURRENT_TIMESTAMP < end
        LIMIT
            1;
    ";

    $result = mysqli_query($conn, $query);
    $count = mysqli_num_rows($result);

    if($count) {
        $row = mysqli_fetch_assoc($result);
        //  id_question
        //  content
        //  answer_1_content
        //  answer_1_count
        //  answer_2_content
        //  answer_2_count
        //  start
        //  end
        extract($row);
    }
    
    mysqli_close($conn);
?>

<?php if($count):  ?>

<!DOCTYPE html>
<html lang="pl_PL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ankieta</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        <?php if(($answer_1_count + $answer_2_count) >= 2): ?>
        :root {
            --answer-indicator: <?php echo ($answer_2_count * 100) / ($answer_1_count + $answer_2_count) . '%'; ?>;
        }

        body {
            background: linear-gradient(to right, green 50%, red 50%);
            background-size: 200% 200%;
            background-position: var(--answer-indicator) 50%;
            animation: anim 1s ease;
        }
        
        @keyframes anim
        {
            0% { background-position: 50% 50%; }

            50% { background-position: 50% 50%; }
        }
        <?php endif ?>
        
        .box {
            width: 250px;
            background-color: #FFF;
            padding: 20px 10px;
            border: #DDD solid 1px;
        }
    </style>
</head>
<body>
    <div class="box">
        <form action method="POST">
            <label><?php echo $content; ?></label>
            <input type="hidden" name="id" value="<?php echo $id_question; ?>">
            <p>
                <input type="radio" id="answer-1" name="answer" value="answer-1" required>
                <label for="answer-1"><?php echo $answer_1_content; ?></label>
            </p>
            <p>
                <input type="radio" id="answer-2" name="answer" value="answer-2">
                <label for="answer-2"><?php echo $answer_2_content; ?></label>
            </p>
            <p>
                <input type="submit" name="submit" value="Wyślij">
            </p>
        </form>
        <?php echo isset($notification) ? $notification . '<br>' : ''; ?>
        <small>od <?php echo $start; ?></small><br>
        <small>do <?php echo $end; ?></small>
    </div>
</body>
</html>
<?php else: ?>
<p>Ankieta nieaktywna! Spróbuj ponownie później.</p>
<?php endif; ?>