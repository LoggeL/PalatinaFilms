<?php
$con = mysqli_connect("localhost","u383818627_user","404noswagfound","u383818627_user");

$angemeldet = false;

if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if (isset($_COOKIE["Pswd"]) && isset($_COOKIE["Rolle"]))
{
  $angemeldet = true;

  $sql="SELECT * FROM user WHERE Rolle = '" . $_COOKIE["Rolle"] . "'";
  $result = mysqli_query($con,$sql);

  while ($row = mysqli_fetch_assoc($result)) {
    if(password_verify($_COOKIE["Pswd"],$row["Hash"])) {
      $Avatar = $row["Bild"];
      $rolle = $row["Rolle"];
      $crew = $row["Crew"];

      if (strpos($crew, 'Organisation') == false) {
	echo "Nicht berechtigt";
        exit();
      }

      $Users = array();
      $sql = "SELECT * FROM user ORDER BY Log DESC";
      $result = mysqli_query($con,$sql);
      while ($row = mysqli_fetch_assoc($result)) {
        $Users[] = $row;
      }

      $sql="SELECT News, Updated, Title, Rolle as Addr FROM RollenNews
            UNION
            SELECT News, Updated, Title, Crew as Addr FROM CrewNews WHERE '" . $crew . "' LIKE CONCAT('%',Crew,'%')
            ORDER BY Updated DESC";

      $result = mysqli_query($con,$sql);


      $News = array();
      while ($row = mysqli_fetch_assoc($result)) {
        $dt = (time() - strtotime($row['Updated']))/3600;
        if ($dt > 48) {$dt = (string)floor($dt / 24) . ' Tagen';} else {$dt = (string)floor($dt) . ' Stunden';}
        $row['Updated'] = $dt;
        $News[] = $row;
      }

      $sql = "SELECT * FROM Downloads";
      $result = mysqli_query($con,$sql);
      $Downloads = array();

      while ($row = mysqli_fetch_assoc($result)) {
        $Downloads[] = $row;
      }

    }
    break;
  }
  mysqli_free_result($result);
}

if (!$angemeldet)
{
  exit();
}

function secondsToTime($s)
{
    $h = floor($s / 3600);
    $s -= $h * 3600;
    $m = floor($s / 60);
    $s -= $m * 60;
    return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $s);
}
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
	  <title>PF Mitgliederseite</title>

	  <!-- CSS  -->
	  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/css/materialize.min.css">

	  <!-- JS -->
	  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/js/materialize.min.js"></script>

		<style>

    .center-cropped {
      width: 150px;
      height: 150px;
      background-position: top center;
      background-repeat: no-repeat;
      background-size: cover;
      border-radius: 50%;
    }

			</style>

	</head>
	<body class="container">

    <h1>Aktive News</h1>

    <?php

    foreach ($News as $row)
    echo "<h4>" . $row['Title'] . "</h4>
    " . $row['News'] . "<br><b>vor " . $row['Updated'] . " an ".$row['Addr']."</b>";
    ?>

    <h1>Aktive User</h1>

      <table class="highlight centered striped">
        <thead>
          <tr>
              <th>Name</th>
              <th>Grp</th>
              <th>Bild</th>
              <th>Log</th>
          </tr>
        </thead>
        <tbody>

          <?php
          foreach ($Users as $row)
          echo '
          <tr>
            <td>'.$row['Rolle'].'</td>
            <td>'.$row['Crew'].'</td>
            <td>
              <div class="center-cropped"
                   style="background-image: url(\'' . $row['Bild'] . '\')"; >
              </div>
            <td>'.secondsToTime(time()-strtotime($row['Log'])).'</td>
          </tr>
          ';
          ?>
        </tbody>
      </table>


      <h1>Aktive Downloads</h1>

        <table class="highlight centered striped">
          <thead>
            <tr>
                <th>Name</th>
                <th>Grp</th>
                <th>Bild</th>
                <th>url</th>
            </tr>
          </thead>
          <tbody>

            <?php
            foreach ($Downloads as $row)
            echo '
            <tr>
              <td>'.$row['Name'].'</td>
              <td>'.$row['Rolle'].'</td>
              <td><img src="'.$row['Bild'].'" style="max-height:200px;" alt="Bild"></td>
              <td><a class="btn" href="'.$row['url'].'">Link</a></td>
            </tr>
            ';
            ?>
          </tbody>
        </table>

	</body>
</html>
