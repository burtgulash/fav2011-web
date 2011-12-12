<!doctype html>

<?php
include_once "redirect.php";

// Slouží pouze k odhlášení uživatele
function log_out()
{
    if (isset($_SESSION["username"])) {
        session_destroy();
        session_start();
    }
}


session_start();

// při požadavku na odhlášení odhlásit uživatele
if (isset($_GET["logout"]))
    log_out();

// Získat přístupová práva uživatele a údaje, jestli je přihlášen. Pokud ano,
// uložíme si jeho uživatelské jméno.
$perm = getPermissions();
$auth = isset($_SESSION["username"]);
if ($auth)
    $username = $_SESSION["username"];
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
        <title>SportKlub</title>
    </head>
    <body>
        <div id="header">
            <h2 id="title"><a href="index.php">SPORTKLUB</a></h2>
        </div>

        <div id="login">
			<ul>
            <?php
                // Pokud je uživatel přihlášen, zobrazit jeho jméno a odkaz na
                // odhlášení. V opačném případě nabídneme odkaz na přihlášení.
                if ($auth) {
					echo "<li>\n";
                    echo "<a id='username' href='edit.php?user=$username'>
                                                 $username</a>\n";
					echo "</li><li>\n";
                    echo "<a href='index.php?logout=1'>odhlásit</a>";
					echo "</li>\n";
                } else
                    echo "<li><a href='login.php'>přihlásit</a></li>\n";
            ?>
			</ul>
        </div>

        <div id="nav">
            <ul>
                <li><a href="index.php?id=vysledky">výsledky</a></li>
                <li><a href="index.php?id=kontakt">kontakt</a></li>
                <?php
                // Pokud je uživatel přihlášen, nabídneme mu i členy týmu.
                if ($perm > 0)
                    echo '<li><a href="index.php?id=clenove">členové</a></li>';
                ?>
            </ul>
        </div>

        <div id="content">
            <?php
                // na základě 'id' stránky zobrazit obsah.
                $id = NULL;
                if (isset($_GET["id"]))
                    $id = $_GET["id"];

                $fromIndex = true;
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
