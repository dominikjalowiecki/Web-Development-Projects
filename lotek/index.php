<?php error_reporting(0); ?>
<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lotek</title>
    <style>
        html,
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #eeeeee;
        }

        .header {
            height: 170px;
        }

        .header .image {
            height: 125px;
            padding: 0 20px;
            display: flex;
            align-items: center;
        }
        .header img {
            height: 75px;
        }

        .section {
            padding: 50px 25px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .sub-section.form {
            margin-bottom: 20px;
        }

        .sub-section.balls {
            padding: 20px;
            border-radius: 10px;
            border: solid 2px #ddd;
            display: flex;
            justify-content: center;
            flex-direction: column;
        }

        .sub-section.balls ul {
            padding: 0;
        }

        .strip {
            width: 100%;
            height: 25px;
            padding: 10px 0;
            background-color: #000;
            color: #fff;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .line {
            border: none;
            border-bottom: solid 2px #ddd;
        }

        .num-input {
            width: 35px;
        }

        .drawn-number {
            list-style: none;
            color: #000;
            width: 60px;
            height: 60px;
            margin: 5px;
            padding: 50px;
            background: linear-gradient(160deg, rgba(247,255,168,1) 0%, rgba(255,214,0,1) 31%, rgba(159,171,0,1) 100%);
            float: left;
            border-radius: 100px;
            font-weight: bold;
            font-size: 50px;
            text-align: center;
            transition: 0.5s ease;
            animation: draw 1s;
        }

        .footer {
            text-align: center;
        }

        @keyframes draw {
            0% {
                color: #0000;
            }

            75% {
                color: #0000;
                transform: scale(1.05);
                box-shadow: 10px 15px 10px #0003;
            }
        }

        @keyframes opacity {
            0% {
                color: #0000;
            }

            75% {
                color: #0000;
            }
        }
    </style>
</head>
<body>
    <audio controls src="./Piotr-Figiel-Pilkarski-Poker-Muzyka-meczu.mp3" autoplay loop>
        Your browser does not support the audio element.
    </audio>

    <div class="header">
        <div class="strip">
            <small>Ulubiona gra Polaków powraca w nowym wydaniu ⚽ Wygraj albo odejdź z niczym!</small>
        </div>
        <div class="image">
            <a href="#"><img src="./logo.png"></a>
        </div>
    </div>
    <hr class="line">
    <div class="section">
        <div class="sub-section form">
        <h3 style="text-decoration: underline;">Wybierz 6 różnych liczb od 1 do 49.</h3>
        <form action="" method="POST">
            <p>
                <input class="num-input" type="number" name="n1" min="1" max="49">
                <input class="num-input" type="number" name="n2" min="1" max="49">
                <input class="num-input" type="number" name="n3" min="1" max="49">
                <input class="num-input" type="number" name="n4" min="1" max="49">
                <input class="num-input" type="number" name="n5" min="1" max="49">
                <input class="num-input" type="number" name="n6" min="1" max="49">
            </p>
            <p>
                <input type="submit" name="submit" value="Losuj">
            </p>
        </form>
        </div>

        <div class="sub-section balls">
        <?php
            if(isset($_POST['submit']))
            {
                if(
                    !empty($_POST['n1']) &&
                    !empty($_POST['n2']) &&
                    !empty($_POST['n3']) &&
                    !empty($_POST['n4']) &&
                    !empty($_POST['n5']) &&
                    !empty($_POST['n6'])
                )
                {
                    function numberRepeat($number, $arr)
                    {
                        for($i=0;$i<count($arr);$i++)
                        {
                            if($number==$arr[$i])
                            {
                                return $i;
                            }
                        }

                        return -1;
                    }

                    function inputNumberRepeat($arr)
                    {
                        for($i=0;$i<count($arr);$i++)
                        {
                            if(numberRepeat($arr[$i], $arr) != $i)
                            {
                                return True;
                            }
                        }

                        return False;
                    }

                    $user_input = array(
                        $_POST['n1'],
                        $_POST['n2'],
                        $_POST['n3'],
                        $_POST['n4'],
                        $_POST['n5'],
                        $_POST['n6']
                    );

                    if(!inputNumberRepeat($user_input))
                    {
                        $random_generated = array();

                        $i = 0;
                        while($i<6)
                        {
                            $tmp = random_int(1,49);
                            if(numberRepeat($tmp, $random_generated) == -1)
                            {
                                $random_generated[] = $tmp;
                                $i++;
                            }
                        }

                        echo "<p>Wybrane liczby: ";
                        foreach($user_input as $number)
                        {
                            echo $number.', ';
                        }
                        echo "</p>";

                        $counter = 0;

                        echo "<h4>Wylosowane liczby to: </h4>";
                        echo "<ul>";
                        foreach($random_generated as $number)
                        {
                            if(numberRepeat($number, $user_input) != -1)
                            {
                                $counter++;
                                echo "<li class='drawn-number' style='color: #F00;'>".$number."</li>";
                            } else
                            {
                                echo "<li class='drawn-number'>".$number."</li>";
                            }
                        }
                        echo "</ul>";
                        echo "<p style='clear: both; animation: opacity 1.5s;'>Trafiłeś ".$counter." na 6 liczb!</p>";
                    } else
                    {
                        echo "Zastosuj się do instrukcji!";
                    }
                } else
                {
                    echo "Zastosuj się do instrukcji!";
                }
            } else
            {
                echo "Spróbuj swoich sił!";
            }
        ?>
        </div>
    </div>
    <hr class="line">
    <div class="footer">
        <p>Dominik Jałowiecki 2020</p>
    </div>
</body>
</html>