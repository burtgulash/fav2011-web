<?php
$newsdb = "data.db";
$db = new SQLite3($newsdb, SQLITE3_OPEN_READONLY);

$query = "SELECT title, article, timeEntered FROM news 
                   ORDER BY timeEntered DESC LIMIT 0,5;";
$result = $db->query($query);

while($article = $result->fetchArray(SQLITE3_ASSOC)) {
	echo "            " . $article['timeEntered'] . "<br />\n";
    echo "            <b>" . $article['title'] . "</b> <br />\n";
    echo "            " . $article['article'] . "<br /><br />\n";
}

$db->close();
?>
