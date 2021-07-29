<?php
require("/var/www/html/phpmailer/index.php");
require("/var/www/html/admin/assets/regtext.php");
//asdasdasd
?>
<!doctype html>
<html lang="en">
  <head>
    <title>TheCarterDEV</title>
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
  </style>
  <body class="d-flex flex-column h-100">
  <main class="flex-shrink-0 text-light" style="margin-top: 100px">
      <center>
      <div class="shadow p-3 mb-5 bg-white rounded text-dark w-50">
          <h1>Szia! Amennyiben van saját fiókód, írd be az azonosítód!</h1>
          <form action="https://thecarterdev.hu/admin/index.php" method="POST">
          <input style="text-align: center;" type="text" name="password" placeholder="Írd be az azonosítód!" class="form-control w-50"><br>
          <input type="submit" name="submit" class="btn btn-primary" value="Azonosítás">
          </div>
          </form>
          <br><button class="btn btn-primary" data-target="#resetpassModal" data-toggle="modal">Elfelejtett jelszó?</button>
          <div class="modal fade text-dark" id="resetpassModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Jelszó visszaállítása</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Add meg az email címed, arra fogsz kapni egy üzenetet.</p>
                  <form action="index.php" method="POST">
                  <input type="email" name="email" class="form-control w-50" required>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                  <input type="submit" class="btn btn-primary" name="submitemail" value="Küldés">
                </div>
              </div>
              </form>
            </div>
          </div>
          <?php 
  #Qerror_reporting(E_ALL); 
  #ini_set('display_errors', 1);
  error_reporting(0);
          include('assets/connection.php');
          include('assets/rs.php');
          $servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "admin";

          if(isset($_POST["submitemail"]) and !empty($_POST["email"])){
            $emailfrompost = $_POST["email"];
            $checkemail = sprintf("SELECT * FROM users WHERE email = '%s'", mysqli_real_escape_string($con, $emailfrompost));
            #$checkemailrow = mysqli_fetch_assoc(mysqli_query($con, $checkemail));
            $countemailrow = mysqli_num_rows(mysqli_query($con, $checkemail));
            #echo $countemailrow;
            if ($countemailrow) {
              $emailkey = generateRandomString();
              $insertemailkey = sprintf("UPDATE users SET emailkey = '%s' WHERE email = '%s'", mysqli_real_escape_string($con, $emailkey), mysqli_real_escape_string($con, $emailfrompost));
              $insertemailkeyjel = mysqli_query($con, $insertemailkey);
              if ($insertemailkeyjel) {
                #echo "Siker";
                $emailtext = "Szia!<br>A dutyperc számláló felületén jelszó visszaállítási kérelmed érkezett.<br>Amennyiben te voltál kattints <a href='https://thecarterdev.hu/admin?resetpassword=$emailkey&email=$emailfrompost' target='_blank'>ide</a>.<br>Amennyiben nem te kérelmezted ezt, ajánlott egy jelszóváltoztatást végezni!";
                if (sendMail("Jelszó visszaállítás", $emailfrompost, $emailtext, "<br>Sikeres kérelem, nézd meg a emailjeid!")) {
                  #echo "Nézd meg a postafiókod!";
                  echo "<br>";
                }
              } else {
                echo "Hiba történt a kulcs készítése közben";
              }
            } else {
              echo "Nem található ilyen email cím az adatbázisban!";
            }
          }
          if (isset($_GET["resetpassword"]) and isset($_GET["email"])) {
            $emailfromget = $_GET["email"];
            $resetkeyfromget = $_GET["resetpassword"];
            $selectresetkey = sprintf("SELECT * FROM users WHERE email = '%s'", mysqli_real_escape_string($con, $emailfromget));
            #$selectresetkeyjel = mysqli_num_rows(mysqli_query($con, $selectresetkey));
            $selectresetkeyjel = mysqli_fetch_assoc(mysqli_query($con, $selectresetkey));
            $checkemailsql = $selectresetkeyjel["email"];
            $checkemailkeysql = $selectresetkeyjel["emailkey"];
            $idfromsql = $selectresetkeyjel["id"];
            if ($emailfromget == $checkemailsql and $resetkeyfromget == $checkemailkeysql) {
              echo "
              <br><br><label>Add meg az új jelszavad</label>
              <form action='' method='POST'>
              <input class='form-control w-50' type='text' name='newpass'>
              <br><input type='submit' name='setnewpass' class='btn btn-warning' value='Rögzítés'>
              </form>
              ";
                
            }
          }

          if (isset($_POST["setnewpass"])) {
            echo "ok";
            echo $_POST["newpass"];
            $newpass = $_POST["newpass"];
            $nulla = "0";
            $setpassandemailkey = sprintf("UPDATE users SET pass = '%s', emailkey = '%s' WHERE id = '%s'", mysqli_real_escape_string($con, $newpass), mysqli_real_escape_string($con, $nulla), mysqli_real_escape_string($con, $idfromsql));
            $setpassandemailkeyjel = mysqli_query($con, $setpassandemailkey);
            if ($setpassandemailkeyjel) {
              echo "<br>Sikeres változtatás!";
              unset($_SESSION["path"]);
              unset($_SESSION["id"]);
              unset($_SESSION["loggedin"]);
              unset($_COOKIE["loggedin"]);
              setcookie('loggedin', null, -1, '/');
              unset($_COOKIE['id']);
              setcookie('id', null, -1, '/');
            }
          }

          session_start();

#echo $_SESSION["path"];
if( (isset($_GET['s']) && $_GET['s'] == "ok")) {
    echo "<br><br><div class='alert alert-success w-50'>Sikeresen törölted a session sütiket illetve kijelentkeztél!</div>";
    unset($_SESSION["path"]);
    unset($_SESSION["id"]);
    unset($_SESSION["loggedin"]);
    unset($_COOKIE["loggedin"]);
    setcookie('loggedin', null, -1, '/');
    unset($_COOKIE['id']);
    setcookie('id', null, -1, '/');
  }
#$path = $_SESSION["path"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if(isset($_COOKIE["loggedin"]) && $_COOKIE["loggedin"] === "1"){
  header("location: https://thecarterdev.hu/admin/page.php");
  exit;
}
          
          if (isset($_POST["submit"])) {
            $pass = $_POST["password"];
            $sql = "select * from users where pass = '$pass'";  
            $result = mysqli_query($con, $sql);  
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);  
            $count = mysqli_num_rows($result);
              
            if($count == 1){  
              #echo "asd"; 
              $sqlpass = sprintf("SELECT * FROM users WHERE pass = '%s'", mysqli_real_escape_string($conn, $pass));
              $resultpass = mysqli_fetch_assoc(mysqli_query($conn, $sqlpass));
              $wpass = $resultpass["pass"];
              $wname = $resultpass["name"];
              $wpath = $resultpass["path"];
              $wid = $resultpass["id"];

              header("location: https://thecarterdev.hu/admin/page.php");
              #$_SESSION["loggedin"] = true;
              #$_SESSION["path"] = "$wpath";
              #$_SESSION["id"] = "$wid";
              #$_COOKIE["loggedin"] = true;
              #$_COOKIE["id"] = $wid;

              setcookie("loggedin", true, time() + (10 * 365 * 24 * 60 * 60), "/");
              setcookie("id",  base64_encode($wid), time() + (10 * 365 * 24 * 60 * 60), "/");
            } else {
              echo "<br><div class='alert alert-danger w-50' role='alert'>
              Sikertelen azonosítás!
              </div>
              ";
            }

                // Store data in session variables
            
          }

          ?>
      <br><br>

      <!--<div class="alert alert-warning w-75">
        <strong>Figyelem!</strong> Karbantartás van az este folyamán, megkérlek téged hogy az este folyamán ne használd az oldalt! :3
      </div>-->

      <div class="card w-75 text-dark">
        <h5 class="card-header">UPDATE 2021.04.04</h5>
        <div class="card-body">
          <h5 class="card-title">Azonosítás fejlesztése</h5>
          <p class="card-text">Cső! Fejlesztettem a belépési mechanizmust ezáltal mostantól elég egyszer beirnod az azonosítód és a oldaladra fog irányítani!</p>
        </div>
      </div>
      <br><button class="btn btn-primary" data-target="#newuserModal" data-toggle="modal">Regisztráció</button>
          <div class="modal fade text-dark" id="newuserModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Regisztráció</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Dutyperc számlálóba való regisztrációhoz, add meg az email címedet!</p>
                  <form action="index.php" method="POST">
                  <input type="email" name="newuseremail" class="form-control w-50" required>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                  <input type="submit" class="btn btn-primary" name="submitnewuseremail" value="Regisztráció">
                </div>
              </div>
              </form>
            </div>
          </div>
          <?php 
          
          if(isset($_POST["submitnewuseremail"])) {
            $newuseremailfrompost = $_POST["newuseremail"];
            $continue = true;
            $checkqueryemail = sprintf("SELECT email FROM users WHERE email = '%s'", mysqli_real_escape_string($con, $newuseremailfrompost));
            $checkqueryemailjel = mysqli_num_rows(mysqli_query($con, $checkqueryemail));
            if ($checkqueryemailjel) {
              $continue = false;
              echo "<script>alert('Már van használatban ilyen email cím!');</script>";
            }
            $regemailkey = generateRandomString();
            $insertregkey = sprintf("INSERT INTO register (email, emailkey) VALUES ('%s', '%s')", mysqli_real_escape_string($con, $newuseremailfrompost), mysqli_real_escape_string($con, $regemailkey));
            $insertregkeyjel = mysqli_query($con, $insertregkey);
            if ($insertregkeyjel and $continue) {
            	$regtext = "Szia, mivel regisztráltál az oldalra kapsz egy hitelesítő linket hogy tényleg te voltál-e az.<br>Ha igen, kattints <a href='https://thecarterdev.hu/admin?register=$regemailkey' target='_blank'>ide</a>.";
            	sendMail("TheCarterDEV - Dutyszámláló", $newuseremailfrompost, $regtext, "<br>Első lépés megvan, további instrukciók a emailedben!");
              #sendMail("TheCarterDEV - Dutyszámláló", $newuseremailfrompost, $regtext, "Csekk");
            	#sendMail("Tesztsubject", "fidermarton@gmail.com", "Tesztemail tesztszöveggel<br>asd");
            	/*$emailtext2 = "Szia!<br>A dutyperc számláló felületén jelszó visszaállítási kérelmed érkezett.<br>Amennyiben te voltál kattints <a href='https://thecarterdev.hu/admin?resetpassword=$emailkey&email=$emailfrompost' target='_blank'>ide</a>.<br>Amennyiben nem te kérelmezted ezt, ajánlott egy jelszóváltoztatást végezni!";
                if (sendMail("Jelszó visszaállítás", $newuseremailfrompost, $emailtext2, "<br>Sikeres kérelem, nézd meg a emailjeid!")) {
                  #echo "Nézd meg a postafiókod!";
                  echo "<br>";
              }*/
            }
          }

          if(isset($_GET["register"])) {
          	$emailkeyfromget = $_GET["register"];
          	$selectemailkey = sprintf("SELECT * FROM register WHERE emailkey = '%s'", mysqli_real_escape_string($con, $emailkeyfromget));
          	$selectemailkeyrow = mysqli_fetch_assoc(mysqli_query($con, $selectemailkey));
          	$resultemailkey = $selectemailkeyrow["emailkey"];
          	$resultemail = $selectemailkeyrow["email"];
          	$resultid = $selectemailkeyrow["id"];
          	if ($emailkeyfromget == $resultemailkey) {
          		?>
          		<br><br>
          		<div class="shadow p-3 mb-5 bg-white rounded w-50 text-dark">
          			<h5>Felhasználói adatok</h5>
          			<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
          			<label>Felhasználónév</label>
          			<input type="text" name="username" class="form-control">
          			<br><label>Jelszóa</label>
          			<input type="password" name="pass" class="form-control">
        			<br><input type="submit" name="submitreg" class="btn btn-info" value="Regisztráció érvényesítése">
        			</form>
          		</div>
          		<?php
          	}
          }
          if(isset($_POST["submitreg"])) {
          	$sqlusername = $_POST["username"];
          	$sqlpassword = $_POST["pass"];
          	$insertnewuser = sprintf("INSERT INTO users (name, start, total, pass, path, email, emailkey, v2admin) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", mysqli_real_escape_string($con, $sqlusername), mysqli_real_escape_string($con, "0"), mysqli_real_escape_string($con, "0"), mysqli_real_escape_string($con, $sqlpassword), mysqli_real_escape_string($con, "0"), mysqli_real_escape_string($con, $resultemail), mysqli_real_escape_string($con, "0"), mysqli_escape_string($con, "0"));
          	$insertnewuserjel = mysqli_query($con, $insertnewuser);
          	$deletereg = sprintf("DELETE FROM register WHERE id = '%s'", mysqli_real_escape_string($con, $resultid));
          	$deleteregjel = mysqli_query($con, $deletereg);
          	if ($insertnewuser and $deleteregjel) {
          		echo "Fiók sikeresen létrehozva!";
          		#header("Location: https://thecarterdev.hu/admin");
          		echo "<meta http-equiv='refresh' content='0.1; URL=https://thecarterdev.hu/admin' />";
          		sendMail("TheCarterDEV - Dutyszámláló", $resultemail, $succesregtext, "EmailCheck");
          	}
          }
          
          ?>

      </center>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <?php if (isset($_GET["resetpass"])) { ?>
    <script type="text/javascript">
    	$('#resetpassModal').modal('show')
    </script>
	<?php } ?>
  </main>
  </body>
</html>

<?php 

$db = mysqli_connect("localhost", "root", "", "admin");

if (isset($_GET["adduser"]) and isset($_GET["name"]) and isset($_GET["pass"]) and isset($_GET["sp"])) {
  if ($_GET["sp"] == "CN65") {
    $name = $_GET["name"];
    $pass = $_GET["pass"];
    if (empty($name) or empty($pass)) {
      echo "üres egy mező";
      exit;
    }
    $query = "INSERT INTO users (name, start, total, pass, path) VALUES ('$name', '0', '0', '$pass', '0')";
    $queryjel = mysqli_query($db, $query);
    if ($queryjel) {
      echo "Sikeres beillesztés!";
    }
  }
}



