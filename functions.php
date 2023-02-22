<?php
    function definiereAltersgruppe($prämienjahr, $jahrgang) {
        $altersjahr = $prämienjahr - $jahrgang;
        return ($altersjahr < 19) ? "Kinder" : (($altersjahr < 26) ? "Jugendliche" : "Erwachsene");
    }

    function definiereFranchisen($altersgruppe) {
        return $altersgruppe == "Kinder" ? [0, 100, 200, 300, 400, 500] : [300, 500, 1000, 1500, 2000, 2500];
    }

    function definiereUnfalldeckung($unfalldeckung) {
        return $unfalldeckung == 0 ? "nein" : ($unfalldeckung == 1 ? "ja" : "");
    }

    function definiereVersicherungsmodell($versicherungsmodell) {
        if(!isset($versicherungsmodell)) { return ""; }
        $modelle = [
            "freiearztwahl" => "Freie Arztwahl",
            "hausarztmodell" => "Hausarzt-Modell",
            "telmedmodell" => "Telmed-Modell",
            "digimedmodell" => "Digimed-Modell",
        ];
        return isset($modelle[$versicherungsmodell]) ? $modelle[$versicherungsmodell] : "";        
    }

    function berechneSelbstbehalt($franchise, $gesundheitskosten, $altersgruppe) {
        $selbstbehalt = ($gesundheitskosten - $franchise) * 0.1;
        $maxSelbstbehalt = ($altersgruppe === "Kinder") ? 350 : 700;
        return min($selbstbehalt, $maxSelbstbehalt);
    }
    
    function holeMonatspreamie($verbindung, $altersgruppe, $versicherungsmodell, $praemienregion, $unfalldeckung) {
        $sql = "SELECT * FROM ".strtolower($verbindung->real_escape_string($altersgruppe))." WHERE Versicherungsmodell='".$verbindung->real_escape_string($versicherungsmodell)."' AND Kanton='".$verbindung->real_escape_string($praemienregion)."' AND Unfall='".$verbindung->real_escape_string($unfalldeckung)."'";
        return $verbindung->query($sql)->fetch_assoc();
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
			echo "<p class='mt-3 mb-0'>Die Grundversicherung bieten wir lediglich in den Kantonen Wallis und Bern an.</p>";
			return;
		}

		echo "<div class='mt-3'>
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