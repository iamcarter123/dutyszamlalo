<?php
require("/var/www/html/phpmailer/index.php");
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$db = mysqli_connect('localhost', 'root', '', 'admin');
if (!$db) {
    echo "hiba";
}

session_start();

$loggedin = $_COOKIE["loggedin"];

// Check if the user is logged in, if not then redirect him to login page
if(!isset($loggedin) || $loggedin !== "1"){
    header("location: /admin");
    exit;
}

$userpathid2 = $_COOKIE["path"];
$userid2 = $_COOKIE["id"];

$userid = base64_decode($userid2);

$year = date('Y');
$nowtime = date('H:i');
$month = date('n');
$lastmonth = $month - 1;

$sqldata = sprintf("SELECT start, total, name, flag, v2admin FROM users WHERE id = '%s'", mysqli_real_escape_string($db, $userid));
$resultdata = mysqli_fetch_assoc(mysqli_query($db, $sqldata));
$start = $resultdata["start"];
$total = $resultdata["total"];
$name = $resultdata["name"];
$flag = $resultdata["flag"];
$v2admin = $resultdata["v2admin"];

if(isset($_GET["teszt"])) {
  #echo "asd";
  $text = "Szia, az emailt azért kapod mivel be vagy regisztrálva a dutyszámlálóba.<br>Időszak: <b>$year.$lastmonth - $year.$month</b><br>Dutyperced: <b>$total</b>";
  sendMail("TheCarterDEV - Havi összesítés - Teszt", "lantapo1599@gmail.com", $text, "Sikeres");
}

switch($flag) {
  case 'HU': $resultflag = "<img style='margin-left: 15px; margin-bottom: 15px;' src='assets/hu.png' width='45' height='45'>"; break;
  case 'RO': $resultflag = "<img style='margin-left: 15px; margin-bottom: 15px;' src='assets/ro.png' width='45' height='45'"; break;
  case 'LMBTQ': $resultflag = "<img style='margin-left: 15px; margin-bottom: 15px;' src='assets/lmbtq.png' width='45' height='45'"; break;
}

if (isset($_GET["changepass"]) && isset($_GET["user"]) && isset($_GET["newpass"])) {
	$changepass = $_GET["changepass"];
	$getuser = $_GET["user"];
	$newpass = $_GET["newpass"];
	$changequery = sprintf("UPDATE users SET pass = '%s' WHERE name = '%s'", mysqli_real_escape_string($db, $newpass), mysqli_real_escape_string($db, $getuser));
	$changequeryjel = mysqli_query($db, $changequery);
	if ($changequeryjel) {
		echo "Siker!";
		if (isset($_GET["redirect"])) {
			$redirect = $_GET["redirect"];
			header("Location: $redirect");
			echo "redirect lenne";
      unset($_SESSION["path"]);
      unset($_SESSION["id"]);
      unset($_SESSION["loggedin"]);
      unset($_COOKIE["loggedin"]);
      setcookie('loggedin', null, -1, '/');
      unset($_COOKIE['id']);
      setcookie('id', null, -1, '/');
		}
	} else {
		echo "Hiba!";
	}
}

?>

<!doctype html>
<html lang="en">
  <head>
    <title>Percszámláló</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <style>
  body {
    background-image: url(https://thecarterdev.hu/taxi/dashboard/include/bg.jpg);
    background-position: center center;
    background-size: cover;
    background-attachment: fixed;
    background-repeat: no-repeat;
  }
  ::-webkit-scrollbar {
	  width: 5px;
  }
  ::-webkit-scrollbar-track {
    background: #c2c2c2;
  }
  ::-webkit-scrollbar-thumb {
    background: #3d3d40;
  }
  ::-webkit-scrollbar-thumb:hover {
    background: #007bff;
  }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
  </style>
  <body class="d-flex flex-column h-100">
  <div class="text-center p-4 text-light" style="background-color: rgba(0, 0, 0, 0.2);">
  <h4>
    ©  <script>document.write(new Date().getFullYear())</script> Made by
    <a class="text-light" href="https://fb.me/fider.marton/">Carter</a> | <a class="text-light" href="https://paypal.me/itscss"><button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="bottom" title="Hogyha segítenéd a munkámat ezzel, nagyon megköszönöm. Természetesen nem kötelező, sőt inkább ne is.">
    <img style="margin-bottom: 5px;" src="assets/paypal.png" width="15px" height="15px">Támogatás</img>
</button></a><?php if ($v2admin) { ?> <button class="btn btn-primary" data-toggle="modal" data-target="#percModal">Percmodal</button><?php } ?>
    <a class="float-right btn btn-secondary" href="/admin?s=ok">Kijelentkezés</a>
    <button type="button" class="float-left btn btn-secondary" data-toggle="modal" data-target="#changeModal">Azonosító változtatás</button></h4>
  </div>

  <div class="modal fade" id="changeModal" tabindex="-1" aria-labelledby="changeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changeModalLabel">Azonosító változtatás</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <center>
          <h5>Ha azonosítót váltasz onnantól fogva azzal tudsz majd belépni!</h5>
          <form action="page.php" method="POST">
            <input type="text" name="newpass" class="form-control w-50" placeholder="Adj meg egy új azonosítót">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Bezárás</button>
        <input type="submit" name="changepassbutton" class="btn btn-warning" value="Megerősítés">
      </div>
     </form>
    </div>
  </div>
</div>
  <?php 

  if (isset($_POST["changepassbutton"])) {
    $newpassfrompost = $_POST["newpass"];
    header("Location: /admin/page.php?changepass&user=$name&newpass=$newpassfrompost&redirect=/admin");
  }
  
  if (isset($_GET["status"])) {
    if ($_GET["status"] == "1") {
      echo "
      <br><center>
      <div class='alert alert-success text-center w-50' role='alert'>
      <strong>Sikeresen hozzáadás!</strong>
      </div>
      </center>
      ";
    }
  }

  if (isset($_GET["time"])) {
    $timefromget = $_GET["time"];
    if ($timefromget != "" or $timefromget != "0") {
      echo "
      <br><center>
      <div class='alert alert-success text-center w-50' role='alert'>
      <strong>Ha most leadnád a szolgálatot ennyi dutypercel bővölne a számláló: $timefromget</strong>
      </div>
      </center>
      ";
    }
  }
  
  ?>
    <main class="flex-shrink-0 text-light" style="margin-top: 100px">
        <div class="container">
        <h1>Percszámláló &middot; <?php echo $name; echo  $resultflag; ?></h1>
        <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">  
        </div>
        </div>
        <h4>Jelenleg <span class='badge badge-warning'><?php echo $total; ?></span> dutyperced van.</h4>
            <form action="" method="POST">
                <?php if($start == 0) echo "
            <input type='submit' class='btn btn-primary' name='startduty' value='Számlálás elkezdése'><hr style='background-color:white'>
            <h5>Kezdés máskor</h5><input type='time' name='atime'><br><br>
            <input type='submit' name='submitperc' class='btn btn-primary'>
            <input type='number' class='form-control w-25 float-right' name='plusduty' placeholder='Adj hozzá plusz dutypercet'><br><br>
            <input type='submit' class='btn btn-warning float-right' name='submitplusduty' value='Plusz hozzáadása'>
            ";?>
            <?php if($start != 0) echo "
                <input type='submit' class='btn btn-danger' name='stopduty' value='Számlálás leállítása'>
                <h4>Elindítva: <span class='badge badge-warning'>$start-kor</span></h4>
                <input type='hidden' name='checkduty2' value='$start'>
                <input type='submit' class='btn btn-success' name='checkduty' value='Ha most leadom mennyi percem lesz?'>
                <hr style='background-color:white'>
                <h5>Befejezés máskor</h5><input type='time' name='atime2'><br><br>
                <input type='submit' name='submitduty' class='btn btn-primary'>
            ";?>
            </form>
            <?php 
            if(isset($_POST["startduty"])) {
                $updatestart = "UPDATE users SET start = '0', start = '$nowtime' WHERE id = '$userid'";
                $updatestartjel = mysqli_query($db, $updatestart);
                if($updatestartjel) {
                    echo "Számláló elindítva, az oldal 5 másodperc múlva frissül.";
                    #$result='<div class="alert alert-success">Thank You! I will be in touch</div>';
                    echo '<script type="text/javascript">
                    setTimeout(
                      function() {
                        window.location.href = "/admin/page.php";
                    }, 5000);
                      </script>';

                }
            }
            if (isset($_POST["stopduty"])) {

                $start_date = new DateTime($start);
                $since_start = $start_date->diff(new DateTime($nowtime));
                $minutes = $since_start->days * 24 * 60;
                $minutes += $since_start->h * 60;
                $minutes += $since_start->i;
                $addtotaltime = $minutes + $total;
                echo $since_start->h;
                $updatestop = "UPDATE users SET start = '0', total = '$addtotaltime' WHERE id = '$userid'";
                $updatestopjel = mysqli_query($db, $updatestop);
                $querylog = "INSERT INTO log (userid, start, stop, minutes, hours, totalminutes) VALUES ('$userid', '$start', '$nowtime', '$minutes', '$since_start->h', '$addtotaltime')";
                $querylogjel = mysqli_query($db, $querylog);
                if ($updatestopjel and $querylogjel) {
                    echo "<h4><span class='badge badge-warning'>Sikeresen leállítottad a számlálót! Jelenlegi dutyperced: $addtotaltime | Ennyi percet dutyztál: $minutes</span><br>Az oldal 10 másodperc múlva frissül!</h4>";
                    echo '<script type="text/javascript">
                    setTimeout(
                      function() {
                        window.location.href = "/admin/page.php";
                    }, 10000);
                      </script>';
                }
            }

            if (isset($_POST["submitperc"])) {
              $timeinput = $_POST["atime"];
              $updatesqlstart = "UPDATE users SET start = '$timeinput' WHERE id = '$userid'";
              $updatesqlstartjel = mysqli_query($db, $updatesqlstart);
              if($updatesqlstartjel) {
                echo "Számláló elindítva, az oldal 5 másodperc múlva frissül.";
                #$result='<div class="alert alert-success">Thank You! I will be in touch</div>';
                echo '<script type="text/javascript">
                setTimeout(
                  function() {
                    window.location.href = "/admin/page.php";
                }, 5000);
                  </script>';
              }
            }

            if (isset($_POST["submitduty"])) {
              $timeduty = $_POST["atime2"];
              
              $start2_date = new DateTime($start);
              $since2_start = $start2_date->diff(new DateTime($timeduty));
              $minutes2 = $since2_start->days * 24 * 60;
              $minutes2 += $since2_start->h * 60;
              $minutes2 += $since2_start->i;

              $totaltime = $minutes2 + $total;

              $updatesqlstop = "UPDATE users SET start = '0', total = '$totaltime' WHERE id = '$userid'";
              $updatesqlstop = mysqli_query($db, $updatesqlstop);
              $querylog2 = "INSERT INTO log (userid, start, stop, minutes, hours, totalminutes) VALUES ('$userid', '$start', '$timeduty', '$minutes2', '$since2_start->h', '$totaltime')";
              $querylogjel2 = mysqli_query($db, $querylog2);

              if ($updatesqlstop and $querylogjel2) {
                echo "<h4><span class='badge badge-warning'>Sikeresen leállítottad a számlálót! Jelenlegi dutyperced: $totaltime | Ennyi percet dutyztál: $minutes2</span><br>Az oldal 10 másodperc múlva frissül!</h4>";
                echo '<script type="text/javascript">
                setTimeout(
                  function() {
                    window.location.href = "/admin/page.php";
                }, 10000);
                  </script>';
              }
            }

            if(isset($_POST["submitplusduty"])) {
              $inputtime = $_POST["plusduty"];
              
              $addtwotime = $total + $inputtime;
              $querysql = "UPDATE users SET total = '$addtwotime' WHERE id = '$userid'";
              $querysqljel = mysqli_query($db, $querysql);
              #echo "$userid $inputtime $addtwotime";
              if ($querysqljel) {
                header("location: /admin/page.php?status=1");
              }
            }

            if (isset($_POST["checkduty"])) {
              $start2post = $_POST["checkduty2"];
              $start_date3 = new DateTime($start2post);
                $since_start3 = $start_date3->diff(new DateTime($nowtime));
                $minutes3 = $since_start3->days * 24 * 60;
                $minutes3 += $since_start3->h * 60;
                $minutes3 += $since_start3->i;
                $calctime = $minutes3 + $total;
                #header("location: https://thecarterdev.hu/admin/page.php?time=$calctime");
                echo "<script>location.href = '/admin/page.php?time=$calctime';</script>";
            }
            if ($v2admin) {
            ?>
            <!-- Perc -->
            <div class="modal fade" id="percModal" tabindex="-1" role="dialog" aria-labelledby="percModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title text-dark" id="percModalLabel">Perc napi szinteken</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body text-dark">
                    23. <span class="badge badge-primary">180 perc</span><br>
                    24. <span class="badge badge-primary">360 perc</span><br>
                    25. <span class="badge badge-primary">540 perc</span><br>
                    26. <span class="badge badge-primary">720 perc</span><br>
                    27. <span class="badge badge-primary">900 perc</span><br>
                    28. <span class="badge badge-primary">1080 perc</span><br>
                    29. <span class="badge badge-primary">1260 perc</span><br>
                    30. <span class="badge badge-primary">1440 perc</span><br>
                    01. <span class="badge badge-primary">1620 perc</span><br>
                    02. <span class="badge badge-primary">1800 perc</span><br>
                    03. <span class="badge badge-primary">1980 perc</span><br>
                    04. <span class="badge badge-primary">2160 perc</span><br>
                    05. <span class="badge badge-primary">2340 perc</span><br>
                    06. <span class="badge badge-primary">2520 perc</span><br>
                    07. <span class="badge badge-primary">2700 perc</span><br>
                    08. <span class="badge badge-primary">2880 perc</span><br>
                    09. <span class="badge badge-primary">3060 perc</span><br>
                    10. <span class="badge badge-primary">3240 perc</span><br>
                    11. <span class="badge badge-primary">3420 perc</span><br>
                    12. <span class="badge badge-primary">3600 perc</span><br>
                    13. <span class="badge badge-primary">3780 perc</span><br>
                    14. <span class="badge badge-primary">3960 perc</span><br>
                    15. <span class="badge badge-primary">4140 perc</span><br>
                    16. <span class="badge badge-primary">4320 perc</span><br>
                    17. <span class="badge badge-primary">4500 perc</span><br>
                    18. <span class="badge badge-primary">4680 perc</span><br>
                    19. <span class="badge badge-primary">4860 perc</span><br>
                    20. <span class="badge badge-primary">5040 perc</span><br>
                    (+31) <span class="badge badge-primary">5220 perc</span><br>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>
<table class="h-100 table table-striped table-dark table-hover"  style="margin-top: 50px">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Indítva</th>
      <th scope="col">Leállítva</th>
      <th scope="col">Hány perc</th>
      <th scope="col">Hány óra</th>
      <th scope="col">Összes perced</th>
      <th scope="col">Rögzítve</th>
    </tr>
  </thead>
  <tbody>
<?php

    if (isset($_GET["limit"]) and !empty($_GET["limit"])) {
      $limit = $_GET["limit"];
      $selectlog = "SELECT * FROM log WHERE userid = '$userid' ORDER BY id DESC LIMIT $limit";
    } else {
      $selectlog = "SELECT * FROM log WHERE userid = '$userid' ORDER BY id DESC";
    }
    
    #$selectlog = "SELECT * FROM log WHERE userid = '$userid'";
    $selectlogresult = mysqli_query($db, $selectlog);
    while($row = mysqli_fetch_array($selectlogresult)) {
    $idrow = $row["id"];
    $startrow = $row["start"];
    $stoprow = $row["stop"];
    $minutesrow = $row["minutes"];
    $hoursrow = $row["hours"];
    $totalminutesrow = $row["totalminutes"];
    $addedwhenrow = $row["addedwhen"];
    ?>
  <tr>
    <th scope="row"><?php echo $idrow; ?></th>
    <td><?php echo $startrow; ?></td>
    <td><?php echo $stoprow; ?></td>
    <td><?php echo $minutesrow; ?></td>
    <td><?php echo $hoursrow; ?></td>
    <td><?php echo $totalminutesrow; ?></td>
    <td><?php echo $addedwhenrow; ?></td>
  </tr>
      <?php } ?>
  </tbody>
</table>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="assets/tooltip.js"></script>
  </body>
</html>
