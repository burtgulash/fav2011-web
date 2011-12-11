<?php
    include_once "redirect.php";
    session_start();


    if (!isset($_GET["user"]) || !isset($_SESSION["username"]))
        relative_redirect("index.php");

    $username = $_GET["user"];
    $perm = getPermissions();
    $dbfile = "data.db";

    // Don't have permissions to edit this user
    if ($perm < 2 && $_SESSION["username"] != $username)
        relative_redirect("index.php");

    if (isset($_POST["editted"])) {
        $db = new SQLite3($dbfile, SQLITE3_OPEN_READWRITE);

        $pass_update = "";
        if (isset($_POST["pass"]) && isset($_POST["passcheck"]) &&
            !empty($_POST["pass"]) && !empty($_POST["passcheck"])) 
        {
            // pass1 == pass2 ?
            if ($_POST["pass"] == $_POST["passcheck"]) {
                $pass_hash = md5($_POST["pass"]);
                $pass_update = ", pass='$pass_hash'";
            }
        }

        $query = sprintf("UPDATE users SET jmeno='%s', prijmeni='%s',
                          telCislo='%s'" . $pass_update .
                          "WHERE name='%s';", 
                          $db->escapeString($_POST["jmeno"]),
                          $db->escapeString($_POST["prijmeni"]),
                          $db->escapeString($_POST["telCislo"]),
                          $db->escapeString($_GET["user"]));
        $db->exec($query);
        $db->close();

        $success_url = "edit.php?success=1&user=" . $username;
        relative_redirect($success_url);
    }

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
        <?php
            if (isset($_GET["success"]))
                echo "<p><b>Údaje úspěšně změněny.</b></p>";
        ?>
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

            <label for="pass">Heslo:</label>
            <input type="password" name="pass" /><br />
            <label for="passcheck">Heslo podruhé:</label>
            <input type="password" name="passcheck" /><br />

            <input type="submit" name="submit" value="Změnit údaje" /><br />
        </form>
    </body>
</html>

<?php
$db->close();
?>
