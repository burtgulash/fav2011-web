<?php
include_once "globals.php";

$username = $_SESSION["username"];
$perm = get_permissions();

// Pokud byl zaslán požadavek na přidání uživatele, přidáme ho do databáze.
if (isset($_POST["newuser"])) {
    if ($perm >= HIGH_PERMISSIONS && 
        !empty($_POST["name"]) && !empty($_POST["pass"])) 
    {
        $pass_hash = md5($_POST["pass"]);
        $db = new SQLite3(DATABASE, SQLITE3_OPEN_READWRITE);
        $query = sprintf("SELECT 1 FROM users WHERE name='%s';",
                         $db->escapeString($_POST["name"]));
        $res = $db->query($query)->fetchArray(SQLITE3_ASSOC);

        // Pokud uživatel neexistuje, můžeme ho přidat.
        if (!$res) {
            $query = sprintf("INSERT INTO users (name, pass, permissions, 
                                                 jmeno, prijmeni) 
                                   VALUES ('%s', '%s', %d, '%s', '%s');",
                             $db->escapeString($_POST["name"]),
                             $db->escapeString($pass_hash),
                             MEMBER_PERMISSIONS,
                             $db->escapeString($_POST["jmeno"]),
                             $db->escapeString($_POST["prijmeni"]));
            $db->exec($query);
            $db->close();
            relative_redirect("index.php?id=clenove&status=added");
        }
        $db->close();

        // Pokud se stala chyba, zobrazíme tuto stránku s chybou.
        relative_redirect("index.php?id=clenove&status=exists");
    }
}

// Požadavek na odstranění uživatele
if (isset($_GET["removeuser"])) {
    // Musíme se ujistit, že uživatel, který chce někoho odstranit má nejvyšší
    // práva a nemaže sebe.
    if ($perm >= HIGH_PERMISSIONS && $_GET["removeuser"] != $username) {
        $db = new SQLite3(DATABASE, SQLITE3_OPEN_READWRITE);
        $query = sprintf("DELETE FROM users WHERE name='%s';", 
                         $db->escapeString($_GET["removeuser"]));
        $db->exec($query);
        $db->close();
    }
    relative_redirect("index.php?id=clenove&status=removed");
}

// Pokud se na tuto stránku někdo dostal jinak než měl, přesměrujeme ho správně.
if (!isset($fromIndex))
    relative_redirect("index.php?id=clenove");

// Pokud nastala chyba při vkládání nového uživatele, která nemohla být ověřena
// u klienta, vypíšeme chybu.
if (isset($_GET["status"])) {
    switch ($_GET["status"]) {
        case "exists":
            echo "<p><b>Uživatel již existuje.</b></p><br />\n";
            break;
        case "added":
            echo "<p><b>Uživatel vytvořen</b></p><br />\n";
            break;
		case "removed":
            echo "<p><b>Uživatel odstraněn</b></p><br />\n";
			break;
    }
}

// Hlavní uživatel může vkládat nové členy, může k tomu použít formulář.
if ($perm >= HIGH_PERMISSIONS) {
    echo "
    <form id='member_form' class='form' method='post'
action='clenove.php' accept-charset='UTF-8'>
        <h1>Nový uživatel</h1>
        <input name='newuser' type='hidden' value='1' />
        <label>Uživatelské jméno:</label>
        <input class='field' name='name' type='text' required='required'/>
        <br />
        <label>Heslo:</label>
        <input class='field' name='pass' type='password' required='required'/>
        <br />
        <label>Jméno:</label>
        <input class='field' name='jmeno' type='text' required='required'/>
        <br />
        <label>Příjmení:</label>
        <input class='field' name='prijmeni' type='text' required='required' />
        <br />
        <input type='submit' name='submit' value='Přidat uživatele'/>
    </form>
    <br />";
}

echo "<h1 class='section_title'>Členové týmu</h1>\n";

// Vypíšeme všechny členy z databáze.
$db = new SQLite3(DATABASE, SQLITE3_OPEN_READONLY);
$query = "SELECT name, jmeno, prijmeni, telCislo, mesto FROM users
          ORDER BY permissions DESC;";
$result = $db->query($query);

// Postupně vypsat všechny uživatele a jejich údaje.
while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "
        <div class='user'>
            <span class='m_username'>" . $user["name"] . "</span><br />
            <p class='m_info'>
                Jméno: " . $user["jmeno"] . "<br />
                Příjmení: " . $user["prijmeni"] . "<br />";
    if (!empty($user["telCislo"]))
        echo "Telefonní číslo: " . $user["telCislo"] . "<br />";
    if (!empty($user["mesto"]))
        echo "Město: " . $user["mesto"] . "<br />";
    echo "</p>";

    // don't delete yourself
    if ($perm >= HIGH_PERMISSIONS && $user["name"] != $username) {
        echo "
            <a class='removelink' href='index.php?id=uprava&user=" .
             $user["name"] . "'>upravit</a>
            <a class='removelink' href='clenove.php?removeuser=" . 
             $user["name"] . "'>odstranit</a><br />\n";
    }

    echo "" .
"            </div><br />\n";
}

$db->close();
?>
