<!doctype html>

<?php
include "redirect.php";

// Funkce, která přihlásí uživatele. V případě úspěchu vrací true, 
// v opačném false.
function login_successful()
{
    if (empty($_POST["username"]))
        return false;
    if (empty($_POST["pass"]))
        return false;

    $username = trim($_POST["username"]);
    if (!check_database($username, trim($_POST["pass"])))
        return false;

    session_start();
    $_SESSION["username"] = $username;
    $_SESSION["permissions"] = get_user_permissions($username);
    return true;
}

// Pomocná funkce, která zkontroluje, jestli sedí uživatel a jeho heslo.
function check_database($name, $pass)
{
    $userdb = "data.db";
    $db = new SQLite3($userdb, SQLITE3_OPEN_READONLY);
    // Hesla jsou šifrována md5 hash funkcí.
    $pass_hash = md5($pass);

    // Dotaz na databázi, jestli existuje uživatel a má správné heslo.
    $query = sprintf("SELECT 1 FROM users WHERE name='%s' AND pass='%s';", 
             $db->escapeString($name), $db->escapeString($pass_hash));

    $res = $db->query($query)->fetchArray(SQLITE3_ASSOC);
    $db->close();
    if ($res)
        return true;
    return false;
}


// Získat uživatelská práva, není zkontrolováno, jestli uživatel existuje,
// proto není vhodné tuto funkci používat jinde než v login_successful, kde
// je tento případ už ošetřen.
function get_user_permissions($username)
{
    $userdb = "data.db";
    $db = new SQLite3($userdb, SQLITE3_OPEN_READONLY);
    $query = sprintf("SELECT permissions FROM users WHERE name='%s';",
                      $db->escapeString($username));
    $res = $db->query($query)->fetchArray();
    return $res["permissions"];
}

// Pokud je uživatel už přihlášen, přesměrujeme ho na úvodní stránku.
if (isset($_SESSION["username"]))
    relative_redirect("index.php");

// Zde probíhá přihlášení.
if (isset($_POST["submitted"]))
    if (login_successful())
        relative_redirect("index.php");


// Při neúspěšném přihlášení předvyplníme pole "uživatelské jméno".
$name_entered = "";
if (isset($_POST["username"]))
    $name_entered = $_POST["username"];
?>


<html>
    <head>
        <title>SportKlub přihlášení</title>
    </head>
    <body>
        <form id="login" action="login.php" method="post" accept-charset="UTF-8">
            <fieldset>
                <legend>Přihlásit se</legend>
                <input type="hidden" name="submitted" value="1" />
                <label for="username">Jméno:</label>
                <input id="username" maxlength="50" name="username" 
                       type="text" value="<?php echo $name_entered?>"/>
                <label for="pass">Heslo:</label>
                <input id="pass" maxlength="50" name="pass" type="password" />
                <input name="submit" type="submit" value="Přihlásit se" />
            </fieldset>
        </form>
    </body>
</html>
