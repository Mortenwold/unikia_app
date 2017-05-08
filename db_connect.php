<?php

$db = mysqli_connect("student.cs.hioa.no", "s232324", "", "s232324");
if (!$db) {
    trigger_error(mysqli_error($db));
    die("Kunne ikke knytte til server");
}
