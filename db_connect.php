<?php

$db = mysqli_connect("sql8.freysqlhosting.net", "sql8173673", "k8Q9sBzWIk", "sql8173673");
if (!$db) {
    trigger_error(mysqli_error($db));
    die("Could not connect to MySQL server, please contact IT-support.");
}
