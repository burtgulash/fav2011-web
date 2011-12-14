<!doctype html>

<?php
include_once "globals.php";

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
$perm = get_permissions();
$auth = isset($_SESSION["username"]);
if ($auth)
    $username = $_SESSION["username"];
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/styles.css" />
        <title>Kopáči</title>
    </head>
    <body>
        <div id="header">
            <div id="inner_header">
                <div id="title">
                    <a href="index.php">
                        <span id="main_title">Kopáči</span>
                        <span id="sub_title">fotbalový klub</span>
                    </a>
                </div>

                <div id="header_login">
                    <ul>
<?php
    // Pokud je uživatel přihlášen, zobrazit jeho jméno a odkaz na
    // odhlášení. V opačném případě nabídneme odkaz na přihlášení.
    if ($auth) {
        echo "<li>\n";
        echo "<a id='header_username' 
href='index.php?id=uprava&amp;user=$username'>$username</a>\n";
        echo "</li><li>\n";
        echo "<a href='index.php?logout=1'>odhlásit</a>";
        echo "</li>\n";
    } else
        echo "<li><a href='index.php?id=login'>přihlásit</a></li>";
?>
                    </ul>
                </div>

                <div id="nav">
                    <ul>
                        <li><a href="index.php?id=vysledky">výsledky</a></li>
                        <li><a href="index.php?id=kontakt">kontakt</a></li>
                        <?php
                        // Pokud je uživatel přihlášen, nabídneme mu i členy týmu.
                        if ($perm > NO_PERMISSIONS)
                            echo '<li><a href="index.php?id=clenove">členové</a></li>';
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div id="content">
            <?php
                // na základě 'id' stránky zobrazit obsah.
                $id = NULL;
                if (isset($_GET["id"]))
                    $id = $_GET["id"];

                $fromIndex = true;
                switch($id) {
                    case "login":
                        include('login.php');
                        break;
                    case "uprava":
                        include('edit.php');
                        break;
                    case "kontakt": 
                        include('kontakt.php'); 
                        break;
                    case "vysledky": 
                        include('vysledky.php'); 
                        break;
                    case "clenove": 
                        if ($perm > NO_PERMISSIONS) {
                            include('clenove.php'); 
                            break;
                        }
                    default: include('uvod.php');
                }
            ?>
        </div>

        <div id="footer">
        </div>
    </body>
</html>
