<?php
function relative_redirect($to)
{
    $host = $_SERVER["HTTP_HOST"];
    $uri = rtrim(dirname($_SERVER["PHP_SELF"]), '/\\');
    header("Location: http://$host$uri/$to");
}
?>
