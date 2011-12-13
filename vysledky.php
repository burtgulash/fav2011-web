<?php
include_once "redirect.php";

$perm = getPermissions();
$dbfile = "data.db";

// Pokud byl zaslán požadavek na přidání nového zápasu, který má neprázdná data,
// vložíme ho do databáze.
if (isset($_POST["newmatch"])) {
    if ($perm >= 2 && 
        isset($_POST["proti"]) &&
        isset($_POST["my"]) &&
        isset($_POST["oni"]) &&
        !empty($_POST["proti"]) &&
        !empty($_POST["my"]) &&
        !empty($_POST["oni"]))
    {
        // Pokud nebyl zadán datum zápasu, použijeme momentální čas.
        $date = "NOW";
        if (isset($_POST["date"]))
            $date = $_POST["date"];

        $db = new SQLite3($dbfile, SQLITE3_OPEN_READWRITE);
        $query = sprintf("INSERT INTO scores (opponent, ours, theirs, timePlayed) 
                        VALUES ('%s', '%s', '%s', JULIANDAY('%s'));", 
                        $db->escapeString($_POST["proti"]),
                        $db->escapeString($_POST["my"]),
                        $db->escapeString($_POST["oni"]),
                        $db->escapeString($date));
        $db->exec($query);

        $db->close();
    }

    relative_redirect("index.php?id=vysledky");
}
// Pokud se někdo na tuto stránku dostal jinak, přesměrujeme ho správně.
if (!isset($fromIndex))
    relative_redirect("index.php?id=vysledky");

// Hlavní uživatel může vkládat nové odehrané zápasy, může k tomu použít 
// formulář.
if ($perm >= 2) {
    echo "" .
"    <form method='post' action='vysledky.php' accept-charset='UTF-8'>\n" .
"        <input name='newmatch' type='hidden' value='1' />\n" .
"        <label for='proti'>Proti:</label>\n" .
"        <input name='proti' type='text' /><br />\n" .
"        <label for='my'>Naše skóre:</label>\n" .
"        <input name='my' type='text' /><br />\n" .
"        <label for='oni'>Jejich skóre:</label>\n" .
"        <input name='oni' type='text' /><br />\n" .
"        <label for='date'>Datum zápasu:</label>\n" .
"        <input name='date' type='date' /><br />\n" .
"        <input type='submit' name='submit' value='vložit' /><br />\n" .
"    </form>";
}
?>
<table border="1">
    <tr>
        <th>datum</th>
        <th>proti</th>
        <th>my:oni</th>
    </tr>

<?php
// Vypíšeme deset nejnovějších zápasů.
$db = new SQLite3($dbfile, SQLITE3_OPEN_READONLY);
$query = "SELECT opponent, ours, theirs,
                 strftime('%d.%m.%Y',timePlayed) AS datePlayed 
                 FROM scores ORDER BY timePlayed DESC;";
$result = $db->query($query);

while ($match = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "<tr>\n";

    echo "<td>";
    echo $match["datePlayed"];
    echo "</td>\n";

    echo "<td>";
    echo $match["opponent"];
    echo "</td>\n";

    echo "<td>";
    echo $match["ours"] . ":" . $match["theirs"];
    echo "</td>\n";
    echo "</tr>\n";
}
$db->close();
?>
</table>
