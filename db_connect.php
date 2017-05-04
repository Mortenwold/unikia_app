<?php

$db = mysqli_connect("localhost", "root", "", "unikia");
if (!$db) {
    trigger_error(mysqli_error($db));
    die("Kunne ikke knytte til server");
}
