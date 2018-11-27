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

      $sql= "UPDATE `user` SET `Log`= now() WHERE Rolle = '" . $rolle . "'";
      mysqli_query($con,$sql);


      $sql="SELECT News, Updated, Title, Rolle as Addr FROM RollenNews WHERE Rolle LIKE '%" . $rolle . "%'
            UNION
            SELECT News, Updated, Title, Crew as Addr FROM CrewNews WHERE '" . $crew . "' LIKE CONCAT('%',Crew,'%') OR Crew = 'alle'
            ORDER BY Updated DESC";

      $result = mysqli_query($con,$sql);

      $News = array();
      while ($row = mysqli_fetch_assoc($result)) {
        $dt = (time() - strtotime($row['Updated']))/3600;
        if ($dt > 48) {$dt = (string)floor($dt / 24) . ' Tagen';} else {$dt = (string)floor($dt) . ' Stunden';}
        $row['Updated'] = $dt;
        $News[] = $row;
      }

      $sql = "SELECT * FROM Downloads WHERE Rolle = '" . $rolle . "' OR Rolle = 'alle' OR  Rolle LIKE '%" . $rolle . "%' ORDER BY ID";
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
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
	  <title>PF Mitgliederseite</title>
	  
	<link rel="icon" href="http://palatina-films.tk/favicon-32x32.png" sizes="32x32">
	<link rel="apple-touch-icon" sizes="152x152" href="http://palatina-films.tk/apple-icon-152x152.png">
	<link rel="icon" type="image/png" sizes="96x96" href="http://palatina-films.tk/favicon-96x96.png">


	  <!-- CSS  -->
	  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/css/materialize.min.css">

	  <!-- JS -->
	  <script type="text/javascript" src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.1/js/materialize.min.js"></script>

		<style>
			body {
				display: flex;
				min-height: 100vh;
				flex-direction: column;
			}

			nav {
				z-index: 5;
			}

			.row {
				margin-bottom: 60px;
			}

			main {
				flex: 1 0 auto;
			}

			body::-webkit-scrollbar {
				width: 5px;}

			body::-webkit-scrollbar-track {
				-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
			}

			body::-webkit-scrollbar-thumb {
				background-color: darkgrey;
				outline: 1px solid slategrey;
			}

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

	<body class="navbar-fixed grey darken-2" onload="doLoad()">
		<nav class="nav-extended grey darken-4" style="height:64px;">
			<div class="nav-content container">
				<ul class="tabs tabs-transparent" style="height:64px;">
					<li class="tab" style="height:64px;"><a class="active" href="#Home"><img src="http://palatina-films.tk/Bilder/logo2.jpg" alt="banner" style="height:80%; padding-top:10%"></a></li>
					<li class="tab <?php if (!$angemeldet) {echo 'hide';} ?>" style="padding-top:10px;"><a href="#News">News</a></li>
					<li class="tab <?php if (!$angemeldet) {echo 'hide';} ?>" style="padding-top:10px;"><a href="#Kalender">Kalender</a></li>
					<li class="tab <?php if (!$angemeldet) {echo 'hide';} ?>" style="padding-top:10px;"><a href="#Drehbuch">Drehbuch</a></li>
					<li class="tab <?php if (!$angemeldet) {echo 'hide';} ?>" style="padding-top:10px;"><a href="#Downloads">Downloads</a></li>
					<?php if (!$angemeldet) {echo '<li class="tab" style="padding-top:10px;"><a href="#Login" class="white-text">Login</a></li>';} ?>
				</ul>
			</div>
		</nav>

		<div class="grey darken-2 container center" id="Home" style="margin-top:64px; margin-bottom:100px; height:100%;">
			<br/>
			<?php if (!$angemeldet) {echo '
			<h2 class="header white-text ">Palatinafilms Teamseite</h2>
			<br/>
			<h4 class="header white-text">Tut uns leid, dieser Bereich ist nur mit Passwort zugänglich!</h4>
      <br>
      <br>
      <a href="http://palatina-films.tk" class="btn center">Zurück zur Hauptseite</a>
			';} else {echo '
			<h1 class="header white-text center">Wilkommen</h1>
			<br/>
			<h3 class="header white-text center">' . $rolle . '</h3>

			<div class="center">
        <div class="center-cropped"
             style="background-image: url(\'' . $Avatar . '\');height:300px;width:300px;margin: 0 auto;" >
        </div>
  			<h4 class="header grey-text center">' . $crew . '</h4>
        <div class="center"><br><br><br><button class="btn" onclick="logout()"><i class="material-icons left">lock_open</i> LogOut</button>
        <br><br><a href="#pwChange" class="btn"><i class="material-icons left">cached</i> Passwortänderung</a>
      </div>
			';} ?>
		</div>
		</div>

		<?php if ($angemeldet) {echo '
		<div class="grey darken-2 container" id="News" style="margin-top:64px; margin-bottom:100px; height:100%;">
		<h3 class="header center white-text" style="margin-top:50px;margin-bottom:50px;">Persönliche News</h3>

			<ul class="collapsible popout" data-collapsible="accordion" style="margin-top:2%">
			';
				foreach ($News as $row)
				{
					echo '
				<li style="margin-left:0;margin-right:0;">
					<div class="collapsible-header">
						<i class="material-icons">bookmark_border</i>
						<span>' . $row['Title'] . '</span><span class="new badge" data-badge-caption=""> vor ' . $row['Updated'] . '</span>
					</div>
					<div class="collapsible-body white" style="padding:10px;margin-bottom:50px;">
						<span class="flow-text">'. $row['News'] . '</span>
					</div>
				</li>
				';}

				echo '
			</ul>
		</div>


		<div class="grey darken-2 container" id="Kalender" style="padding-top:64px; padding-bottom:56px; height:100%;">
		<!-- Seiteninhalte in div Elemente -->

			<iframe src="https://calendar.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;showTz=0&amp;mode=AGENDA&amp;height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=palatinafilms%40gmail.com&amp;color=%231B887A&amp;ctz=Europe%2FBerlin" style="border-width:0;width:100%; height:700px; margin:50px;"  frameborder="0" scrolling="no"></iframe>
		</div>

		<div class="grey darken-2 container" id="Drehbuch" style="padding-top:64px; padding-bottom:56px; height:100%;">
		<!-- Seiteninhalte in div Elemente -->

    <iframe style="width:100%;height:100%;" src="http://docs.google.com/viewer?url=http://crew.palatina-films.tk/Inhalte/Drehbuch%20Teil3.pdf&embedded=true"></iframe>

		</div>

		<div class="grey darken-2 container" id="Downloads" style="padding-top:64px; padding-bottom:56px; height:100%;">
		<!-- Seiteninhalte in div Elemente -->

    <div class="row">
    ';
    foreach ($Downloads as $row) {

    echo '
     <div class="col s12 m6 l4">
       <div class="card small">
         <div class="card-image" style="max-height:100%;">
           <img src="' . $row['Bild'] . '">
           <span class="card-title">' . $row['Name'] . '</span>
         </div>
         <div class="card-action">
           <a target="_blank" href="' . $row['url'] . '">Download</a>
         </div>
       </div>
     </div>
     ';
   }

   echo '
   </div>


		</div>
		';} else {echo'
		<div class="grey darken-2 container" id="Login" style="padding-top:64px; padding-bottom:56px; height:100%;">
			<div class="card white z-depth-4" style="margin-top: 10%;">
				<div class="card-content">
					<span class="card-title">Crewbereich Anmeldung</span>
					<div class="input-field black-text">
						<i class="material-icons prefix">person</i>
						<input id="uname" type="text" class="validate autocomplete" />
						<label for="uname">Nutzername</label>
					</div>
					<div class="input-field black-text">
						<i class="material-icons prefix">lock_outline</i>
						<input id="psw" type="password" class="validate" onkeypress="if (event.keyCode==13) {loginPost();}" />
						<label for="psw">Passwort</label>
					</div>
					<div class="card-action">
						<button class="btn waves-effect waves-light valign blue darken-1" type="submit" name="action" onclick="loginPost()">Einloggen
							<i class="material-icons right">send</i>
						</button>
						<a class="btn btn-flat right" href="mailto:&#080;&#097;&#108;&#097;&#116;&#105;&#110;&#097;&#102;&#105;&#108;&#109;&#115;&#064;&#103;&#109;&#097;&#105;&#108;&#046;&#099;&#111;&#109;?Subject=Passwort Vergessen" target="_blank">Passwort vergessen?<i class="material-icons right">question_answer</i></a>

					</div>
				</div>
			</div>
		</div>
		';} ?>

	   <div id="pCard" class="hide-on-small-only card white z-depth-4 hoverable" style="z-index:2;position:fixed;right:1%;bottom:1%;<?php if (!$angemeldet) {echo "display:none;";} ?>">
	    <div class="card-content center">
	      <span class="card-title" style='margin-right:20%;'><button onclick='closePCard()' class="btn btn-flat btn-floating left"><i class="material-icons red-text">close</i></button><?php echo $rolle; ?></span>
        <div class="center-cropped"
             style="background-image: url('<?php echo $Avatar; ?>')"; >
        </div>
	      <div class="card-action">
	        <button class="btn" onclick='logout()'><i class="material-icons left">lock_open</i> LogOut</button>
	      </div>
	    </div>
	  </div>

    <div id="pwChange" class="modal">
      <div class="modal-content">
        <h4>Passwortänderung</h4>
        <br>
        <div class="input-field black-text">
          <i class="material-icons prefix">lock_outline</i>
          <input id="pswAlt" type="password" class="validate" />
          <label for="pswAlt">Altes Passwort</label>
        </div>
        <div class="input-field black-text">
          <i class="material-icons prefix">lock_outline</i>
          <input id="pswNeu" type="password" class="validate" onkeypress="if (event.keyCode==13) {cangePW();}" />
          <label for="pswNeu">Neues Passwort</label>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#!" class="modal-action waves-effect waves-green btn-flat" onclick="changePW()">Absenden</a>
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Abbrechen</a>
      </div>
    </div>

		<footer class="hide-on-small-only grey darken-4" style="position:fixed;bottom:0px; width:100%;left:0;">
			<div class="footer-copyright" style="padding-bottom:10px;padding-top:10px;">
				<div class="container">
					<a href="http://palatina-films.tk/" class="waves-effect btn-flat white-text text-lighten-3" >Made by Palatinafilms</a>
				</div>
			</div>
		</footer>

		<script>
		function doLoad()
		{
      $('.modal').modal();

			$('input.autocomplete').autocomplete({
				data: {
					<?php
						$sql="SELECT * FROM user";
						$result = mysqli_query($con,$sql);
						while ($row = mysqli_fetch_assoc($result)) {
							echo "'" . $row['Rolle'] . "':'" . $row['Bild'] . "',
							";
						}
					?>

				},
				limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
				onAutocomplete: function(val) {
					// Callback function when value is autcompleted.
				},
				minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
			});

		}

    function changePW() {
      $.post( "action_page.php", { uname: '<?php echo $rolle; ?>', psw: document.getElementById('pswAlt').value, psw2: document.getElementById('pswNeu').value, mode: 'changePW' },
      function(data) {
      document.location = "./";
			});

			var http = new XMLHttpRequest();
			var url = 'action_page.php';
			var params = 'mode=changePW&uname=<?php echo $rolle; ?>&psw='+document.getElementById('pswAlt').value+'&psw2='+document.getElementById('pswNeu').value;
			http.open('POST', url, true);

			//Send the proper header information along with the request
			http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

			http.onreadystatechange = function() {//Call a function when the state changes.
					if(http.readyState == 4 && http.status == 200) {
						location.reload(); 
					}
			}
			http.send(params);
    }

		function closePCard()
		{
			document.getElementById('pCard').style.display = "none";
		}

		function loginPost()
		{
			var http = new XMLHttpRequest();
			var url = 'action_page.php';
			var params = 'mode=login&uname='+document.getElementById('uname').value+'&psw='+document.getElementById('psw').value;
			http.open('POST', url, true);

			//Send the proper header information along with the request
			http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

			http.onreadystatechange = function() {//Call a function when the state changes.
					if(http.readyState == 4 && http.status == 200) {
							setTimeout(() => {
								location.reload()
							}, 1000);
					}
			}
			http.send(params);
		}

		function logout() {
			document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });
			location.reload(true); 
		}

		</script>
	</body>
</html>
