<?php
	if(isset($_POST['plz'])) {
		$plz = filter_var($_POST['plz'], FILTER_SANITIZE_NUMBER_INT);
		holeGemeinde($plz);
	}

	function holeGemeinde($plz) {
    if(!is_numeric($plz) OR $plz >= 10000 OR $plz <= 0) { 
		echo "";
		return;
	}

	include 'connection.php';

	$sql = "SELECT * FROM orte WHERE PLZ = '".$verbindung->real_escape_string($plz)."'";
	$resultat = $verbindung->query($sql);
	
	if($resultat->num_rows == 0) { 
		echo "<p style='margin-top: 5px;'>Die Grundversicherung bieten wir in den Kantonen Wallis und Bern an.</p>";
		return;
	}

	echo "<div class='form-group'>
			<select class='form-control' name='praemienort' style='height: 100% !important;'>";	
				while($eintrag = $resultat->fetch_assoc()) {
					$gemeinde = $eintrag['Gemeinde'];
					$bfs = $eintrag['BFS'];
					$ort = $eintrag['Ort'];
					$praemienregion = $eintrag['Kanton'];	
					echo "<option value='".$praemienregion."'>".$ort." - ".$bfs." (".$gemeinde.")</option>";
				}	
	echo "	</select>
		</div>";
	}
?>
