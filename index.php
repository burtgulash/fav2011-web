<!doctype html>

<?php
session_start();
$auth = $_SESSION["auth"];
$username = $_SESSION["username"];
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
        <title>SportKlub</title>
    </head>
    <body>
        <div id="header">
            <h2>SPORTKLUB</h2>
        </div>

        <div id="login">
            <?php
                if ($auth) {
                    echo $username;
                    echo '<a href="logout.php">odhlásit</a>';
                } else {
                    echo '<a href="login.php">přihlásit</a>';
                }
            ?>
        </div>

        <div id="nav">
            <ul>
                <li><a href="index.php?id=vysledky">výsledky</a></li>
                <li><a href="index.php?id=clenove">členové</a></li>
                <?php
                if ($auth)
                    echo '<li><a href="index.php?id=kontakt">kontakt</a></li>';
                ?>
            </ul>
        </div>

        <div id="content">
            <?php
                $id = $_GET["id"];
                switch($id) {
                    case "kontakt": 
                        include('kontakt.php'); 
                        break;
                    case "vysledky": 
                        include('vysledky.php'); 
                        break;
                    case "clenove": 
                        if ($auth) {
                            include('clenove.php'); 
                            break;
                        }
                    default: include('uvod.php');
                }
            ?>
        </div>
    </body>
</html>
