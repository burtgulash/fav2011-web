<?php
include_once "redirect.php";

// Pokud se někdo dostal na tuto stránku, přesměrujeme ho na tuto stránku
// správným způsobem.
if (!isset($fromIndex))
    relative_redirect("index.php?id=kontakt");
?>

Fodbalový klup <b>Kopalisté</b>.
Najdete nás na kopacím hřišti ve Sportovní 5, Praha 5.
Trenér týmu je Honza Bonza, tel.: 632421643.
