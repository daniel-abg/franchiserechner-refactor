<?php
    $plz = filter_var($_POST['plz'], FILTER_SANITIZE_NUMBER_INT);

    if(is_numeric($plz) AND $plz < 10000 AND $plz > 0){
		/* Verbindung zur Datenbank */				
		include 'connection.php';

		$sql = "SELECT * FROM orte WHERE PLZ = '".$verbindung->real_escape_string($plz)."'";
		$tarif = $verbindung->query($sql);
		$c_tarif = $verbindung->query($sql);

		$count = $c_tarif->fetch_assoc();

		if($count != "") {
			$count = $count['Ort'];

			echo "<div class='form-group'><select class='form-control' name='praemienort' style='height: 100% !important;'>";
			echo "<option selected disabled hidden style='display: none' value=''></option>";
			
			$while = 0;
			while($stadt = $tarif->fetch_assoc()) {
				$while++;
				$ort = $stadt['Ort'];
				$bfs = $stadt['BFS'];
				$gemeinde = $stadt['Gemeinde'];
				$praemienregion = $stadt['Kanton'];
				
				if($while == 1) {
					$select = "selected";
				} else { 
					$select = "";
				}
				
				echo "<option ".$select." value='".$praemienregion."'>".$ort." - ".$bfs." (".$gemeinde.")</option>";
			}
			
			echo "</select></div>";
		} else {
			echo "<div style='margin-top: 5px;'>Die Grundversicherung bieten wir in den Kantonen Wallis und Bern an.</div>";
		}
	} else {
		echo "";
	}
?>
