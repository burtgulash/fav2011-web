<!doctype html>

<?php
include "redirect.php";

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


function check_database($name, $pass)
{
    $userdb = "data.db";
    $db = new SQLite3($userdb, SQLITE3_OPEN_READONLY);
    $pass_hash = md5($pass);
    $query = sprintf("SELECT 1 FROM users WHERE name='%s' AND pass='%s';", 
             $db->escapeString($name), $db->escapeString($pass_hash));

    $res = $db->query($query);
    // TODO shit
    if (count($res->fetchArray()) == 2) {
        $db->close();
        return true;
    }
    $db->close();
    return false;
}

function get_user_permissions($username)
{
    $userdb = "data.db";
    $db = new SQLite3($userdb, SQLITE3_OPEN_READONLY);
    $query = sprintf("SELECT permissions FROM users WHERE name='%s';",
                      $db->escapeString($username));
    $res = $db->query($query)->fetchArray();
    return $res["permissions"];
}

// if already logged in, redirect to index
if (isset($_SESSION["username"]))
    relative_redirect("index.php");

// try logging in
if (isset($_POST["submitted"]))
    if (login_successful())
        relative_redirect("index.php");


// prepopulate name field
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
