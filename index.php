<!doctype html>

<?php
include_once "redirect.php";


function log_out()
{
    if (isset($_SESSION["username"])) {
        session_destroy();
        session_start();
    }
}


session_start();

if (isset($_GET["logout"]))
    log_out();

$perm = getPermissions();
$auth = isset($_SESSION["username"]);
if ($auth)
    $username = $_SESSION["username"];
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
        <title>SportKlub</title>
    </head>
    <body>
        <div id="header">
            <h2><a href="index.php">SPORTKLUB</a></h2>
        </div>

        <div id="login">
            <?php
                if ($auth) {
                    echo $username;
                    echo '<a href="index.php?logout=1">odhlásit</a>';
                } else {
                    echo '<a href="login.php">přihlásit</a>';
                }
            ?>
        </div>

        <div id="nav">
            <ul>
                <li><a href="index.php?id=vysledky">výsledky</a></li>
                <li><a href="index.php?id=kontakt">kontakt</a></li>
                <?php
                if ($perm > 0)
                    echo '<li><a href="index.php?id=clenove">členové</a></li>';
                ?>
            </ul>
        </div>

        <div id="content">
            <?php
                $id = NULL;
                if (isset($_GET["id"]))
                    $id = $_GET["id"];
                switch($id) {
                    case "kontakt": 
                        include('kontakt.php'); 
                        break;
                    case "vysledky": 
                        include('vysledky.php'); 
                        break;
                    case "clenove": 
                        if ($perm > 0) {
                            include('clenove.php'); 
                            break;
                        }
                    default: include('uvod.php');
                }
            ?>
        </div>
    </body>
</html>
