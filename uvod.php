<?php
include_once "globals.php";

define ("ARTICLES_PER_PAGE", 4);

// Pokud byl zaslán požadavek na nový neprázdný příspěvek,
// vložíme ho do databáze.
if (isset($_POST["newpost"])) {
    if (isset($_POST["title"]) && 
        isset($_POST["article"]) &&
        !empty($_POST["title"]) &&
        !empty($_POST["article"])) 
    {
        $db = new SQLite3(DATABASE, SQLITE3_OPEN_READWRITE);
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

echo "<h1 class='section_title'>Novinky</h1>\n";

// Hlavní uživatel může přispívat nové zprávy, poskytneme mu k tomu formulář.
if ($perm >= HIGH_PERMISSIONS) {
echo "
        <div>
          <form id='article_form' class='form' action='uvod.php'
method='post' accept-charset='UTF-8'>
            <h1>Nová zpráva</h1>
            <input type='hidden' name='newpost' value='1' />
            <input class='field' type='text' name='title' required='required'/>
            <br />
            <textarea name='article' cols='40' rows='5'></textarea>
            <input type='submit' name='submit' value='Nový příspěvek' />
            <br />
        </form>
       </div>
       <br />";
}

// Získáme z databáze pět nejnovějších příspěvků a zobrazíme je.
$db = new SQLite3(DATABASE, SQLITE3_OPEN_READONLY);

$page = 0;
if (isset($_GET["page"])) {
    $num = (int) $_GET["page"];
    if ($num > 0)
        $page = $num;
}
$start = $page * ARTICLES_PER_PAGE;

$query = "SELECT title, article, strftime('%d.%m.%Y', timeEntered)
           AS time FROM news 
           ORDER BY timeEntered DESC LIMIT ".$start.", ".ARTICLES_PER_PAGE.";";
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
    echo "    <a class='pagelink' href='index.php?page=$newer'>novější</a>\n";
    echo "</li>\n";
}

// Pokud existují starší, rovněž.
$query = "SELECT COUNT(title) FROM news";
$result= $db->query($query)->fetchArray(SQLITE3_ASSOC);
$numArticles = $result["COUNT(title)"];
if ($numArticles > $start + ARTICLES_PER_PAGE) {
    $older = $page + 1;
    echo "<li>\n";
    echo "    <a class='pagelink' href='index.php?page=$older'>starší</a>\n";
    echo "</li>\n";
}
echo "</ul>\n";

$db->close();
?>
