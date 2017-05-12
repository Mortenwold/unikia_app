<?php

$db = mysqli_connect("sql8.freemysqlhosting.net", "sql8173673", "k8Q9sBzWIk", "sql8173673");
if (!$db) {
    trigger_error(mysqli_error($db));
    die("Kunne ikke knytte til server");
}
