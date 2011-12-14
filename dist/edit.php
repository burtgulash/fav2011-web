<h1 class="section_title">Změna údajů</h1>
<p>Heslo není třeba vyplňovat, pokud nemá být změněno.</p>
<br />

<?php
    include_once "globals.php";

    // Pokud se někdo dostal na tuhle stránku jiným způsobem, přeposlat ho
    // na úvodní stránku
    if (!isset($_GET["user"]))
        relative_redirect("index.php");

    $username = $_GET["user"];
    $perm = get_permissions();

    // Pokud není uživatel přihlášen nebo pokud nemá dostatečná oprávnění
    // k úpravě profilu, přesměrujeme ho na úvodní stránku.
    if (!isset($_SESSION["username"]) || 
        ($perm < HIGH_PERMISSIONS && $_SESSION["username"] != $username))
        relative_redirect("index.php");

    // Vyhodnotit požadavek na změnu údajů
    if (isset($_POST["editted"])) {
        $successful_update = true;

        // Při změně hesla vytvořit dotaz na změnu hesla.
        // Pokud jsou obě hesla prázdná, přeskočit změnu hesla.
        $pass_update = "";
        if (isset($_POST["pass"]) && isset($_POST["passcheck"]) &&
            !(empty($_POST["pass"]) && empty($_POST["passcheck"])))
        {
            // Pokud je jedno z hesel vyplněné, jedná se o chybu.
            if (empty($_POST["pass"]) || empty($_POST["passcheck"]))
                $successful_update = false;
            else if ($_POST["pass"] == $_POST["passcheck"]) {
                // pass1 == pass2 ?
                $pass_hash = md5($_POST["pass"]);
                $pass_update = ", pass='$pass_hash'";
            } else
                $successful_update = false;
        }

        $jmeno = trim($_POST["jmeno"]);
        $prijmeni = trim($_POST["prijmeni"]);
        $telCislo = trim($_POST["telCislo"]);
        $mesto = trim($_POST["mesto"]);

        // Jméno a Příjmení musí být neprázdné
        if (empty($jmeno) || empty($prijmeni))
            $successful_update = false;

        // Pokud došlo k chybě, nebudeme upravovat uživatele
        if ($successful_update) {
            $db = new SQLite3(DATABASE, SQLITE3_OPEN_READWRITE);

            // Dotaz na změnu údajů a jeho vykonání
            $query = sprintf("UPDATE users SET jmeno='%s', prijmeni='%s',
                              telCislo='%s', mesto='%s'" . $pass_update .
                              "WHERE name='%s';", 
                              $db->escapeString($jmeno),
                              $db->escapeString($prijmeni),
                              $db->escapeString($telCislo),
                              $db->escapeString($mesto),
                              $db->escapeString($_GET["user"]));
            if (!($db->exec($query)))
                $successful_update = false;
            $db->close();
        }

        // při úspěšné změně údajů přeposlat uživatele na stejnou stránku
        $return_url = "index.php?id=uprava&user=" . $username . "&success=";
        if ($successful_update)
            $return_url = $return_url . "1";
        else
            $return_url = $return_url . "0";

        relative_redirect($return_url);
    }

    // Přesměrovat špatný vstup na tuto stránku.
    if (!isset($fromIndex))
        relative_redirect("index.php");


    // Dotaz k vyhledání uživatele v databázi
    $db = new SQLite3(DATABASE, SQLITE3_OPEN_READONLY);
    $query = sprintf("SELECT jmeno, prijmeni, telCislo, mesto FROM users
                       WHERE name='%s';", $db->escapeString($username));
    $db_user = $db->query($query)->fetchArray(SQLITE3_ASSOC);

    // Pokud uživatel nebyl nalezen, můžeme rovnou skončit.
    // Tento případ by neměl nikdy nastat.
    if (!$db_user) {
        echo "<p><b>Neexistující uživatel</b></p>\n";
        $db->close();
        exit;
    }
    // V případě úspěchu dáme vědět hláškou
    if (isset($_GET["success"])) {
        $status = $_GET["success"];
        if ($status == "1")
            echo "<p><b>Údaje úspěšně změněny.</b></p>\n";
        else
            echo "<p><b>Chyba při změně údajů</p></b>\n";
    }
?>


<form id="edit_form" class="form" action="edit.php?user=<?php echo $username ?>"
                   method="post" accept-charset="UTF-8">
    <input type="hidden" value="1" name="editted" />
    <label>Jméno:</label>
    <input class="field" type="text" name="jmeno" 
                     value="<?php echo $db_user['jmeno'] ?>" /><br />
    <label>Příjmení:</label>
    <input class="field" type="text" name="prijmeni" 
                     value="<?php echo $db_user['prijmeni'] ?>" /><br />
    <label>Telefonní číslo:</label>
    <input class="field" type="text" name="telCislo" 
                     value="<?php echo $db_user['telCislo'] ?>" /><br />
    <label>Město:</label>
    <input class="field" type="text" name="mesto" 
                     value="<?php echo $db_user['mesto'] ?>" /><br />

    <br />
    <br />
    <label>Heslo:</label>
    <input class="field" type="password" name="pass" /><br />
    <label>Heslo podruhé:</label>
    <input class="field" type="password" name="passcheck" /><br />

    <input type="submit" name="submit" value="Změnit údaje" /><br />
</form>

<?php
$db->close();
?>
