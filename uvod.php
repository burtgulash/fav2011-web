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

$page = 0;
if (isset($_GET["page"])) {
    $num = (int) $_GET["page"];
    if ($num > 0)
        $page = $num;
}
$start = $page * 5;
$end = $start + 5;

$query = "SELECT title, article, strftime('%H:%M, %d.%m.%Y', timeEntered)
                   AS time FROM news 
                   ORDER BY timeEntered DESC LIMIT ".$start.", ".$end.";";
$result = $db->query($query);

while($article = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "        <div class='a_body'>\n";
    echo "            <span class='a_title'>" . $article['title'] . "</span>\n";
    echo "            <p class='article'>" . $article['article'] . "</p>\n";
    echo "            <span class='timedate article'>" . $article['time'] . 
                     "</span>\n";
    echo "        </div>\n";
}


echo "<ul>\n";
// Pokud existují novější příspěvky, dáme uživateli možnost se na ně přesunout
if ($page > 0) {
    $newer = $page - 1;
    echo "<li>\n";
    echo "<a class='pagelink' href='index.php?page=$newer'>novější</a>\n";
    echo "</li>\n";
}

// Pokud existují starší, rovněž.
$query = "SELECT COUNT(title) FROM news";
$result= $db->query($query)->fetchArray(SQLITE3_ASSOC);
$numArticles = $result["COUNT(title)"];
if ($numArticles > $end) {
    $older = $page + 1;
    echo "<li>\n";
    echo "<a class='pagelink' href='index.php?page=$older'>starší</a>\n";
    echo "</li>\n";
}
echo "</ul>\n";

$db->close();
?>
