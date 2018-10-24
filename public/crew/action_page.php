<?php

$mode = $_POST['mode'];
$usr = $_POST['uname'];
$psw = $_POST['psw'];
$psw2 = $_POST['psw2'];

echo 'Working Check. Result: ' . $mode;
print_r($_POST);

$con = mysqli_connect("localhost","u383818627_user","404noswagfound","u383818627_user");

$usr = $con->real_escape_string($usr);
$psw = $con->real_escape_string($psw);
$psw2 = $con->real_escape_string($psw2);

// Check connection
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if ($mode == 'login') {
  $sql="SELECT * FROM user WHERE Rolle = '" . $usr . "'";
  $result = mysqli_query($con,$sql);

  // Associative array
  while ($row = mysqli_fetch_assoc($result)) {
      echo $row["Rolle"] . " (" . $row["Crew"] . ") " . $row["Hash"] . $psw;
      var_dump(password_verify($psw, $row["Hash"]));
      if(password_verify($psw, $row["Hash"]))
      {
        echo "Angemeldet";
        setcookie("Pswd", $psw, time() + (10 * 365 * 24 * 60 * 60));
        setcookie("Rolle", $row["Rolle"], time() + (10 * 365 * 24 * 60 * 60));
      }
      else {
        echo "falsch";
      }
      break;
  }
}
elseif ($mode = "pwChange")
{
  $sql="SELECT * FROM user WHERE Rolle = '" . $usr . "'";
  $result = mysqli_query($con,$sql);

  // Associative array
  while ($row = mysqli_fetch_assoc($result)) {
    echo 'Found ' . $usr;
    if(password_verify($psw, $row["Hash"]))
    {
      echo 'Correct';
      $sql="UPDATE user SET Hash = '" . password_hash($psw2, PASSWORD_DEFAULT) . "' WHERE Rolle = '" . $usr . "'";
      $result = mysqli_query($con,$sql);
      setcookie("Pswd", $psw2, time() + (10 * 365 * 24 * 60 * 60));
    }
    else {
      echo "Invalid Hash";
    }
    break;
  }
}

if ($result) {
mysqli_free_result($result);
}

mysqli_close($con);

//header('Location: http://crew.palatina-films.tk/');
?>
