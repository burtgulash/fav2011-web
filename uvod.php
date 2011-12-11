<?php
include_once "redirect.php";

$newsdb = "data.db";
if (isset($_POST["newpost"]) && 
    isset($_POST["title"]) && 
    isset($_POST["article"])) 
{
    $db = new SQLite3($newsdb, SQLITE3_OPEN_READWRITE);
    $query = sprintf("INSERT INTO news (title, article, timeEntered) VALUES
                               ('%s', '%s', DATETIME('NOW'));",
                    $db->escapeString($_POST["title"]), 
                    $db->escapeString($_POST["article"]));
    $db->exec($query);
    $db->close();

    relative_redirect("index.php");
}

if ($perm >= 2) {
    echo "<div>\n";
    echo "<form action='uvod.php' method='post' accept-charset='UTF-8'>\n";
    echo "<input type='hidden' name='newpost' value='1' />\n";
    echo "<label for='title'>Nová zpráva:</label><br />\n";
    echo "<input id='title' type='text' name='title' /><br />\n";
    echo "<textarea id='article' name='article' cols='40' rows='5'>\n";
    echo "</textarea><br />\n";
    echo "<input type='submit' name='submit' value='Nový příspěvek' /><br />\n";
    echo "</form>\n";
    echo "</div>\n";
    echo "<br />\n";
}

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
