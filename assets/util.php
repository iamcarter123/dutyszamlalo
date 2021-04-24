<?php

require("connection.php");

$maintenancesql = sprintf("SELECT * FROM maintenance");
$rowmaintenancesql = mysqli_fetch_assoc(mysqli_query($db, $maintenancesql));
$getmaintenancestatus = $rowmaintenancesql["state"];

function isMaintenance() {
    if ($GLOBALS["getmaintenancestatus"]) {
?>   
<!doctype html>
<title>Karbantartás folyamatban</title>
<style>
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>
<article>
    <h1>Hamarosan visszatérünk!</h1>
    <div>
        <p>Bocsi, de jelenleg karbantartás folyik az oldalon. Ha kérdeznéd miért, azért hogyha valamit szeretnél csinálni ne vesszen el miközbe bütykölöm az oldalt.</p>
        <p>&mdash; Carter</p>
    </div>
</article>
        <?php
        exit;
    }
}
?>