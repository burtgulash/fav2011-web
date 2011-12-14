<?php
include_once "globals.php";

$perm = get_permissions();

// Pokud byl zaslán požadavek na přidání nového zápasu, který má neprázdná data,
// vložíme ho do databáze.
if (isset($_POST["newmatch"]) && $perm >= HIGH_PERMISSIONS) {
	$proti =  trim($_POST["proti"]);
	var_dump($_POST["my"]);
	$my = (int) $_POST["my"];
	$oni = (int) $_POST["oni"];

	if (!empty($proti)) {
		// Pokud nebyl zadán datum zápasu, použijeme momentální čas.
		$date = "NOW";
		if (isset($_POST["date"]))
			$date = $_POST["date"];

		$db = new SQLite3(DATABASE, SQLITE3_OPEN_READWRITE);
		$query = sprintf("INSERT INTO scores (opponent, ours, theirs, 
                      timePlayed) VALUES ('%s', '%d', '%d', JULIANDAY('%s'));",
						$db->escapeString($proti),
						$my, $oni,
						$db->escapeString($date));
		$db->exec($query);
		$db->close();
	}
    relative_redirect("index.php?id=vysledky");
}


// Pokud se někdo na tuto stránku dostal jinak, přesměrujeme ho správně.
if (!isset($fromIndex))
    relative_redirect("index.php?id=vysledky");


echo "<h1 class='section_title'>Odehrané zápasy</h1>\n";

// Hlavní uživatel může vkládat nové odehrané zápasy, může k tomu použít 
// formulář.
if ($perm >= HIGH_PERMISSIONS) {
    $date = date("Y-m-d");
    echo "
    <form id='score_form' class='form' method='post' action='vysledky.php'
                                          accept-charset='UTF-8'>
        <h1>Nový zápas</h1>
        <input name='newmatch' type='hidden' value='1' />
        <label>
            Proti:
            <span>Tým protivníka</span>
        </label>
        <input class='field' name='proti' type='text' required='required' />
        <label>
            My:
            <span>Naše skóre</span>
        </label>
        <input class='field' name='my' type='number' min='0' step='1'
                   value='0' required='required' />
        <br />
        <label>
            oni:
            <span>Jejich skóre</span>
        </label>
        <input class='field' name='oni' type='number' min='0' step='1' 
                   value='0' required='required' />
        <br />
        <label>
            Datum zápasu:
             <span>9999-12-31</span>
         </label>
        <input class='field' name='date' type='date' value='$date'/><br />
        <input type='submit' name='submit' value='vložit' /><br />
    </form>";
}
?>
<table id="vysledky">
    <tr>
        <th>datum</th>
        <th>proti</th>
        <th>my : oni</th>
    </tr>

<?php
// Vypíšeme deset nejnovějších zápasů.
$db = new SQLite3(DATABASE, SQLITE3_OPEN_READONLY);
$query = "SELECT opponent, ours, theirs,
                 strftime('%d.%m.%Y',timePlayed) AS datePlayed 
                 FROM scores ORDER BY timePlayed DESC;";
$result = $db->query($query);

while ($match = $result->fetchArray(SQLITE3_ASSOC)) {
    if ($match["ours"] >= $match["theirs"])
        $match_result = "win";
    else
        $match_result = "loss";

    echo "<tr class='$match_result'>\n";

    echo "<td>";
    echo $match["datePlayed"];
    echo "</td>\n";

    echo "<td>";
    echo $match["opponent"];
    echo "</td>\n";

    echo "<td>";
    echo $match["ours"] . " : " . $match["theirs"];
    echo "</td>\n";
    echo "</tr>\n";
}
$db->close();
?>
</table>
