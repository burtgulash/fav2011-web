<?php
include_once "redirect.php";

$newsdb = "data.db";
// Pokud byl zaslán požadavek na nový neprázdný příspěvek,
// vložíme ho do databáze.
if (isset($_POST["newpost"])) {
    if (isset($_POST["title"]) && 
        isset($_POST["article"]) &&
        !empty($_POST["title"]) &&
        !empty($_POST["article"])) 
    {
        $db = new SQLite3($newsdb, SQLITE3_OPEN_READWRITE);
        $query = sprintf("INSERT INTO news (title, article, timeEntered) VALUES
                                   ('%s', '%s', DATETIME('NOW'));",
                        $db->escapeString($_POST["title"]), 
                        $db->escapeString($_POST["article"]));
        $db->exec($query);
        $db->close();

    }
    relative_redirect("index.php");
}
// Pokud se na tuto stránku dostal někdo jinak, přesměrujeme ho správně.
if (!isset($fromIndex))
    relative_redirect("index.php");

// Hlavní uživatel může přispívat nové zprávy, poskytneme mu k tomu formulář.
if ($perm >= 2) {
echo "".
"        <div>\n" .
"          <form action='uvod.php' method='post' accept-charset='UTF-8'>\n" .
"            <input type='hidden' name='newpost' value='1' />\n" .
"            <label for='title'>Nová zpráva:</label><br />\n" .
"            <input id='title' type='text' name='title' /><br />\n" .
"            <textarea id='article' name='article' cols='40' rows='5'>\n" .
"            </textarea><br />\n" .
"            <input type='submit' name='submit' value='Nový příspěvek' />" .
"            <br />\n" .
"        </form>\n" .
"       </div>\n" .
"       <br />\n";
}

// Získáme z databáze pět nejnovějších příspěvků a zobrazíme je.
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
