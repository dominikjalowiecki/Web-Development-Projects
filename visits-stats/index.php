<?php
    error_reporting(0);

    define('CONFIG', array(
        'host' => '',
        'user' => '',
        'password' => '',
        'db' => ''
    ));

    $conn = mysqli_connect(CONFIG['host'], CONFIG['user'], CONFIG['password'], CONFIG['db']);

    $path = explode('/', $_SERVER['PHP_SELF']);

    $when_visited = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
    $what_visited = end($path).', '.$_SERVER['PHP_SELF'];
    $servername = $_SERVER['SERVER_NAME'];
    $ip_addr = $_SERVER['REMOTE_ADDR'] ?? null;
    $hostname = $_SERVER['REMOTE_HOST'] ?? null;
    $if_https = ($_SERVER['HTTPS'] ?? 'off') == 'on' ? True : False;
    $user_info = $_SERVER['HTTP_USER_AGENT'];  

    $query = "
        INSERT INTO
            visit
        VALUES
            (Null, '$when_visited', '$what_visited', '$servername', '$ip_addr', '$hostname', '$if_https', '$user_info');
    ";

    mysqli_query($conn, $query);

    $query = "
        SELECT
            (
                SELECT
                    COUNT(*)
                FROM
                    visit
                WHERE
                    DATE_ADD(when_visited, INTERVAL 60 SECOND) >= '$when_visited'
            ) as minute,
            (
                SELECT
                    COUNT(*)
                FROM
                    visit
                WHERE
                    DATE_ADD(when_visited, INTERVAL 60 MINUTE) >= '$when_visited'
            ) as hour,
            (
                SELECT
                    COUNT(*)
                FROM
                    visit
                WHERE
                    DATE_ADD(when_visited, INTERVAL 1 DAY) >= '$when_visited'
            ) as day,
            (
                SELECT
                    COUNT(*)
                FROM
                    visit
                WHERE
                    DATE_FORMAT(when_visited, '%Y %m') = DATE_FORMAT('$when_visited', '%Y %m')
            ) as month,
            (
                SELECT
                    COUNT(*)
                FROM
                    visit
                WHERE
                    DATE_FORMAT(when_visited, '%Y') = DATE_FORMAT('$when_visited', '%Y')
            ) as year,
            (
                SELECT
                    COUNT(*)
                FROM
                    visit
                WHERE
                    if_https = False
            ) as http,
            (
                SELECT
                    COUNT(*)
                FROM
                    visit
                WHERE
                    if_https = True
            ) as https;
    ";

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    $ref = "1";
    for($i=0;$i<strlen($row['year']);$i++)
    {
        $ref .= "0";
    }
    $ref = (int) $ref;

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .chart .percentage.minute:after {
            content: "<?php echo $row['minute'] ?>";
            width: <?php echo ($row['minute']/$ref*100)."%" ?>;
        }
        .chart .percentage.hour:after {
            content: "<?php echo $row['hour'] ?>";
            width: <?php echo ($row['hour']/$ref*100)."%" ?>;
        }
        .chart .percentage.day:after {
            content: "<?php echo $row['day'] ?>";
            width: <?php echo ($row['day']/$ref*100)."%" ?>;
        }
        .chart .percentage.month:after {
            content: "<?php echo $row['month'] ?>";
            width: <?php echo ($row['month']/$ref*100)."%" ?>;
        }
        .chart .percentage.year:after {
            content: "<?php echo $row['year'] ?>";
            width: <?php echo ($row['year']/$ref*100)."%" ?>;
        }
        .chart .percentage.protokol:after {
            content: "<?php echo $row['http'] ?>";
            width: 100%;
            background: linear-gradient(to right, #007c7d 50%, #005254 50%);
            background-size: 200% 200%;
            background-position: <?php echo (100*$row['https']/($row['http']+$row['https'])).'%';?> 50%;
            animation: anim2 1s ease;
        }

        @keyframes anim2
        {
            0% { background-position: 50% 50%; }

            50% { background-position: 50% 50%; }
        }

        .chart .percentage.protokol {
            position: relative;
        }
        .chart .percentage.protokol .text {
            left: -110px;
        }
        .chart .percentage.protokol:before {
            content: "<?php echo $row['https'] ?>";
            width: 100%;
            padding: 10px 0;
            margin-top: 3px;
            position: absolute;
            text-align: right;
            top: 0;
            right: 0;
            z-index: 50;
        }

    </style>
</head>
<body>
    <dl class="chart">
        <dt class="title">Wejścia na stronę</dt>
        <dd class="percentage minute">
            <span class="text">Co minutę</span>
        </dd>
        <dd class="percentage hour">
            <span class="text">Co godzinę</span>
        </dd>
        <dd class="percentage day">
            <span class="text">Dziennie</span>
        </dd>
        <dd class="percentage month">
            <span class="text">W tym miesiącu</span>
        </dd>
        <dd class="percentage year">
            <span class="text">W tym roku</span>
        </dd>
        <dd class="percentage protokol">
            <span class="text">http/https</span>
        </dd>
    </dl>
</body>
</html>