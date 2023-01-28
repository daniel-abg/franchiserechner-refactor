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
	$tarif = $verbindung->query($sql);
	$c_tarif = $verbindung->query($sql);

	$count = $c_tarif->fetch_assoc();

	if($count == "") { 
		echo "<p style='margin-top: 5px;'>Die Grundversicherung bieten wir in den Kantonen Wallis und Bern an.</p>";
		return;
	}

	$count = $count['Ort'];

	echo "<div class='form-group'>
				<select class='form-control' name='praemienort' style='height: 100% !important;'>
					<option selected disabled hidden style='display: none' value=''></option>";
	
	$while = 0;
	while($stadt = $tarif->fetch_assoc()) {
		$while++;
		$ort = $stadt['Ort'];
		$bfs = $stadt['BFS'];
		$gemeinde = $stadt['Gemeinde'];
		$praemienregion = $stadt['Kanton'];
		$select = ($while == 1) ? "selected" : "";		
		echo "<option ".$select." value='".$praemienregion."'>".$ort." - ".$bfs." (".$gemeinde.")</option>";
	}
	
	echo "		</select>
		</div>";
	}
?>
