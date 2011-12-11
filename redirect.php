<?php
function relative_redirect($to)
{
    $host = $_SERVER["HTTP_HOST"];
    $uri = rtrim(dirname($_SERVER["PHP_SELF"]), '/\\');
    header("Location: http://$host$uri/$to");
}

function getPermissions()
{
    if (!isset($_SESSION))
        session_start();

    if (isset($_SESSION["permissions"]))
        return $_SESSION["permissions"];
    return 0;
}
?>
