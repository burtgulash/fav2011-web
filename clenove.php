<?php
include_once "globals.php";

$username = $_SESSION["username"];
$perm = getPermissions();

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
            relative_redirect("index.php?id=clenove&error=none");
        }
        $db->close();

        // Pokud se stala chyba, zobrazíme tuto stránku s chybou.
        relative_redirect("index.php?id=clenove&error=exists");
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
    relative_redirect("index.php?id=clenove");
}

// Pokud se na tuto stránku někdo dostal jinak než měl, přesměrujeme ho správně.
if (!isset($fromIndex))
    relative_redirect("index.php?id=clenove");

// Pokud nastala chyba při vkládání nového uživatele, která nemohla být ověřena
// u klienta, vypíšeme chybu.
if (isset($_GET["error"])) {
    switch ($_GET["error"]) {
        case "exists":
            echo "<p><b>Uživatel již existuje.</b></p><br />\n";
            break;
        case "none":
            echo "<p><b>Uživatel vytvořen</b></p><br />\n";
            break;
    }
}

// Hlavní uživatel může vkládat nové členy, může k tomu použít formulář.
if ($perm >= HIGH_PERMISSIONS) {
    echo "" .
"    <form id='member_form' class='form' method='post'" .
"                         action='clenove.php' accept-charset='UTF-8'>\n" .
"        <h1>Nový uživatel</h1>\n" .
"        <input name='newuser' type='hidden' value='1' />\n" .
"        <label for='name'>Uživatelské jméno:</label>\n" .
"        <input class='field' name='name' type='text' /><br />\n" .
"        <label for='pass'>Heslo:</label>\n" .
"        <input class='field' name='pass' type='password' /><br />\n" .
"        <label for='jmeno'>Jméno:</label>\n" .
"        <input class='field' name='jmeno' type='text' /><br />\n" .
"        <label for='prijmeni'>Příjmení:</label>\n" .
"        <input class='field' name='prijmeni' type='text' /><br />\n" .
"        <input type='submit' name='submit' value='Přidat uživatele'/>\n" .
"    </form>\n" .
"    <br />\n";
}

echo "<h1 class='section_title'>Členové týmu</h1>\n";

// Vypíšeme všechny členy z databáze.
$db = new SQLite3(DATABASE, SQLITE3_OPEN_READONLY);
$query = "SELECT name, jmeno, prijmeni, telCislo FROM users;";
$result = $db->query($query);

while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "" .
"        <div class='user'>\n" .
"            <span class='m_username'>" . $user["name"] . "</span><br />\n" .
"            <p class='m_info'>\n" .
"            Jméno: " . $user["jmeno"] . "<br />\n" .
"            Příjmení: " . $user["prijmeni"] . "<br />\n" .
"            Telefonní číslo: " . $user["telCislo"] . "<br />\n" .
"            </p>\n";
    // don't delete yourself
    if ($perm >= HIGH_PERMISSIONS && $user["name"] != $username) {
        echo "" .
"            <a class='removelink' href='index.php?id=uprava&user=" .
             $user["name"] . "'>upravit</a>\n" .
"            <a class='removelink' href='clenove.php?removeuser=" . 
             $user["name"] . "'>odstranit</a><br />\n";
    }

    echo "" .
"            </div><br />\n";
}

$db->close();
?>
