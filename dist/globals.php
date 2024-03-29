<?php

// --- definice ---

// uživatelská práva
define ("NO_PERMISSIONS", 0);
define ("MEMBER_PERMISSIONS", 1);
define ("HIGH_PERMISSIONS", 2);

// soubor s databází
define ("DATABASE", "data.db");



// --- pomocné funkce ---

// Přesměruje uživatele na stránku na serveru
function relative_redirect($to)
{
    $host = $_SERVER["HTTP_HOST"];
    $uri = rtrim(dirname($_SERVER["PHP_SELF"]), '/\\');
    header("Location: http://$host$uri/$to");
    exit;
}

// Získá práva přihlášeného uživatele
function get_permissions()
{
    if (!isset($_SESSION))
        session_start();

    if (isset($_SESSION["permissions"]))
        return $_SESSION["permissions"];
    return 0;
}
?>
