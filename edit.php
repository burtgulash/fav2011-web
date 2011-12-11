<?php
    include_once "redirect.php";
    session_start();

    if (!isset($_GET["user"]) || !isset($_SESSION["username"]))
        relative_redirect("index.php");

    $perm = getPermissions();
    $username = $_GET["user"];

    // Don't have permissions to edit this user
    if ($perm < 2 && $_SESSION["username"] != $username)
        relative_redirect("index.php");

    $dbfile = "data.db";
    $db = new SQLite3($dbfile, SQLITE3_OPEN_READONLY);
    $query = sprintf("SELECT jmeno, prijmeni, telCislo FROM users
                       WHERE name='%s';", $db->escapeString($username));
    $db_user = $db->query($query)->fetchArray(SQLITE3_ASSOC);
    // TODO check if db_user empty
?>
<html>
    <head>
        <title>SportKlub úprava profilu</title>
    </head>
    <body>
        <a href="index.php">Zpět</a><br />
        <form action="edit.php?user=<?php echo $username ?>" method="post" 
                                            accept-charset="UTF-8">
            <input type="hidden" value="1" name="editted" />
            <label for="jmeno">Jméno:</label>
            <input type="text" name="jmeno" 
                             value="<?php echo $db_user['jmeno'] ?>" /><br />
            <label for="prijmeni">Příjmení:</label>
            <input type="text" name="prijmeni" 
                             value="<?php echo $db_user['prijmeni'] ?>" /><br />
            <label for="telCislo">Telefonní číslo:</label>
            <input type="text" name="telCislo" 
                             value="<?php echo $db_user['telCislo'] ?>" /><br />

            <input type="password" name="pass" /><br />
            <input type="password" name="passcheck" /><br />

            <input type="submit" name="submit" value="Změnit údaje" /><br />
        </form>
    </body>
</html>

<?php
$db->close();
?>
