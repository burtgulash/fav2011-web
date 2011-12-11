<?php
include_once "redirect.php";

if (!isset($fromIndex))
    relative_redirect("index.php");

$dbfile = "data.db";
$db = new SQLite3($dbfile, SQLITE3_OPEN_READONLY);

$query = "SELECT jmeno,prijmeni,telCislo FROM users;";
$result = $db->query($query);

while ($user = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "<div class='user'>\n";

    echo "Jméno: " . $user["jmeno"] . "<br />\n";
    echo "Příjmení: " . $user["prijmeni"] . "<br />\n";
    echo "Telefonní číslo: " . $user["telCislo"] . "<br />\n";

    echo "</div><br />\n";
}
?>
