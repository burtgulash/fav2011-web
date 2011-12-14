<?php
include_once "globals.php";

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
    $db = new SQLite3(DATABASE, SQLITE3_OPEN_READONLY);
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
    $db = new SQLite3(DATABASE, SQLITE3_OPEN_READONLY);
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
    else  {
        $prefill_username = "";
        if (isset($_POST["username"]))
            $prefill_username = $_POST["username"];

        relative_redirect("index.php?id=login&username=" . $prefill_username);
    }

// Pokud se na tuto stránku dostal někdo jinak, přesměrujeme ho správně.
else if (!isset($fromIndex))
    relative_redirect("index.php?id=login");


// Při neúspěšném přihlášení předvyplníme pole "uživatelské jméno".
$name_entered = "";
if (isset($_GET["username"]))
    $name_entered = $_GET["username"];
?>



<h1 class="section_title">Přihlásit se</h1>

<!-- Formulář k přihlášení uživatele -->
<form class="form" id="login_form" action="login.php" 
                                       method="post" accept-charset="UTF-8">
    <input type="hidden" name="submitted" value="1" />
    <label>Jméno:</label>
    <input class="field" maxlength="50" name="username" type="text" 
               value="<?php echo $name_entered?>" required="required" />
    <label>Heslo:</label>
    <input class="field" maxlength="50" name="pass" type="password" 
                                                  required="required"/>
    <input type="submit" value="Přihlásit se" />
</form>
