<?php
include_once "redirect.php";

$username = $_SESSION["username"];
$perm = getPermissions();
$dbfile = "data.db";

if (isset($_POST["newuser"])) {
    if ($perm >= 2 && !empty($_POST["name"]) && !empty($_POST["pass"])) {
        $pass_hash = md5($_POST["pass"]);
        $db = new SQLite3($dbfile, SQLITE3_OPEN_READWRITE);
        $query = sprintf("SELECT 1 FROM users WHERE name='%s';",
                         $db->escapeString($_POST["name"]));
        $res = $db->query($query);

        // TODO better check
        if (count($res->fetchArray()) < 2) {
            $query = sprintf("INSERT INTO users (name, pass, jmeno, prijmeni) 
                                   VALUES ('%s', '%s', '%s', '%s');",
                             $db->escapeString($_POST["name"]),
                             $db->escapeString($pass_hash),
                             $db->escapeString($_POST["jmeno"]),
                             $db->escapeString($_POST["prijmeni"]));
            $db->exec($query);
        }
        $db->close();
    }

    relative_redirect("index.php?id=clenove&error=exists");
}

if (isset($_GET["removeuser"])) {
    // don't delete yourself
    if ($perm >= 2 && $_GET["removeuser"] != $username) {
        $db = new SQLite3($dbfile, SQLITE3_OPEN_READWRITE);
        $query = sprintf("DELETE FROM users WHERE name='%s';", 
                         $db->escapeString($_GET["removeuser"]));
        $db->exec($query);
        $db->close();
    }
    relative_redirect("index.php?id=clenove");
}

if (!isset($fromIndex))
    relative_redirect("index.php");

// handle GET errors
if (isset($_GET["error"])) {
	switch ($_GET["error"]) {
		case "exists":
			echo "<p><b>Uživatel již existuje.</b></p><br />\n";
			break;
	}
}

if ($perm >= 2) {
    echo "" .
"    <form method='post' action='clenove.php' accept-charset='UTF-8'>\n" .
"        <input name='newuser' type='hidden' value='1' />\n" .
"        <label for='name'>Uživatelské jméno:</label>\n" .
"        <input name='name' type='text' /><br />\n" .
"        <label for='pass'>Heslo:</label>\n" .
"        <input name='pass' type='password' /><br />\n" .
"        <label for='jmeno'>Jméno:</label>\n" .
"        <input name='jmeno' type='text' /><br />\n" .
"        <label for='prijmeni'>Příjmení:</label>\n" .
"        <input name='prijmeni' type='text' /><br />\n" .
"        <input type='submit' name='submit' value='Přidat uživatele'/>\n" .
"    </form>\n" .
"    <br />\n";
}

$db = new SQLite3($dbfile, SQLITE3_OPEN_READONLY);
$query = "SELECT name, jmeno, prijmeni, telCislo FROM users;";
$result = $db->query($query);

while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "" .
"        <div class='user'>\n" .
"            <b>" . $user["name"] . "</b><br />\n" .
"            Jméno: " . $user["jmeno"] . "<br />\n" .
"            Příjmení: " . $user["prijmeni"] . "<br />\n" .
"            Telefonní číslo: " . $user["telCislo"] . "<br />\n";
    // don't delete yourself
    if ($perm >= 2 && $user["name"] != $username)
        echo "" .
"            <a href='clenove.php?removeuser=" . $user["name"] . "'>
                                                     odstranit</a><br />\n";

    echo "" .
"            </div><br />\n";
}

$db->close();
?>
