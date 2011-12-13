<?php
    include_once "globals.php";
    session_start();

    // Pokud se někdo dostal na tuhle stránku jiným způsobem, přeposlat ho
    // na úvodní stránku
    if (!isset($_GET["user"]) || !isset($_SESSION["username"]))
        relative_redirect("index.php");

    $username = $_GET["user"];
    $perm = getPermissions();
    $dbfile = "data.db";

    // Pokud nemá uživatel dostatečná oprávnění k úpravě profilu...
    if ($perm < 2 && $_SESSION["username"] != $username)
        relative_redirect("index.php");

    // Vyhodnotit požadavek na změnu údajů
    if (isset($_POST["editted"])) {
        $db = new SQLite3($dbfile, SQLITE3_OPEN_READWRITE);

        // Při změně hesla vytvořit dotaz na změnu hesla
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

        // Dotaz na změnu údajů a jeho vykonání
        $query = sprintf("UPDATE users SET jmeno='%s', prijmeni='%s',
                          telCislo='%s'" . $pass_update .
                          "WHERE name='%s';", 
                          $db->escapeString($_POST["jmeno"]),
                          $db->escapeString($_POST["prijmeni"]),
                          $db->escapeString($_POST["telCislo"]),
                          $db->escapeString($_GET["user"]));
        $db->exec($query);
        $db->close();

        // při úspěšné změně údajů přeposlat uživatele na stejnou stránku
        $success_url = "edit.php?success=1&user=" . $username;
        relative_redirect($success_url);
    }

    // Dotaz k vyhledání uživatele v databázi
    $db = new SQLite3($dbfile, SQLITE3_OPEN_READONLY);
    $query = sprintf("SELECT jmeno, prijmeni, telCislo FROM users
                       WHERE name='%s';", $db->escapeString($username));
    $db_user = $db->query($query)->fetchArray(SQLITE3_ASSOC);
?>
<html>
    <head>
        <title>SportKlub úprava profilu</title>
    </head>
    <body>
        <?php
            // Pokud uživatel nebyl nalezen, můžeme rovnou skončit.
            if (!$db_user) {
                echo "<p><b>Neexistující uživatel</b></p>\n";
                echo "    </body>\n</html>";
                $db->close();
                exit;
            }
            // V případě úspěchu dáme vědět hláškou
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
