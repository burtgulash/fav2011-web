<?php
include_once "globals.php";

// Pokud se někdo dostal na tuto stránku, přesměrujeme ho na tuto stránku
// správným způsobem.
if (!isset($fromIndex))
    relative_redirect("index.php?id=kontakt");
?>

<h1 class="section_title">Kontakt</h1>
<p><b>Fotbalový klub Kopáči</b></p>

<p>
Najdete nás na sportovním hřišti ve Sportovní 5, Praha 5.  Jsme tým s
několikaletou zkušeností a střeleckou zručností. Vyhráváme za jakýchkoliv
okolností, za jakéhokoliv počasí.
</p>

<p>
V případě jakýchkoliv dotazů se obraťte na vedoucího týmu Jardu Šéfa, telefonní
číslo je 000-123-321.
</p>
