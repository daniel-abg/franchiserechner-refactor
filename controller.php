<?php    
    function definiereAltersgruppe($prämienjahr, $jahrgang) {
        $altersjahr = $prämienjahr - $jahrgang;
        
        if ($altersjahr < 19) {
            $altersgruppe = "Kinder";
        } elseif ($altersjahr < 26) {
            $altersgruppe = "Jugendliche";
        } else {
            $altersgruppe = "Erwachsene";
        }
        return $altersgruppe;
    }

    function definiereFranchisen($altersgruppe) {
        if ($altersgruppe == "Kinder") {
            $franchisen = array(0, 100, 200, 300, 400, 500);
        } else {
            $franchisen = array(300, 500, 1000, 1500, 2000, 2500);
        }
        return $franchisen;
    }

    function definiereUnfalldeckung($unfalldeckung) {				
        switch($unfalldeckung){
            case 0:
                $unfalldeckung = "nein";
                break;
            case 1:
                $unfalldeckung = "ja";
                break;
            default:
                $unfalldeckung = '';
        }
        return $unfalldeckung;
    }

    function definiereVersicherungsmodell($versicherungsmodell) {				
        switch($_POST["versicherungsmodell"]){
            case "freiearztwahl":
                $versicherungsmodell = "Freie Arztwahl";
                break;
            case "hausarztmodell":
                $versicherungsmodell = "Hausarzt-Modell";
                break;
            case "telmedmodell":
                $versicherungsmodell = "Telmed-Modell";
                break;
            case "digimedmodell":
                $versicherungsmodell = "Digimed-Modell";
                break;
            default:
                $versicherungsmodell = "";
        }
        return $versicherungsmodell;
    }

    function berechneSelbstbehalt($franchise, $gesundheitskosten, $altersgruppe) {
        $selbstbehalt2 = ($gesundheitskosten - $franchise)*0.1;
        if ($altersgruppe != "Kinder" and $selbstbehalt2 < 700){
            $selbstbehalt = ($gesundheitskosten - $franchise)*0.1; /* Nach der Franchise fallen von der Differenz vom Gesundheitskosten und Franchise der Selbstbehalt von 10% an */
        } elseif ($altersgruppe != "Kinder" and $selbstbehalt2 >= 700) {
            $selbstbehalt = 700; /* Erwachsenene zahlen einen maximalen Selbstbehalt von CHF 700.- */
        } elseif ($altersgruppe = "Kinder" and $selbstbehalt2 < 350){
            $selbstbehalt = ($gesundheitskosten - $franchise)*0.1;
        } elseif ($altersgruppe = "Kinder" and $selbstbehalt2 >= 350) {
            $selbstbehalt = 350; /* Kinder zahlen einen maximalen Selbstbehalt von CHF 350.- */
        }
        return $selbstbehalt;
    }
    
    function holeMonatspreamie($verbindung, $altersgruppe, $versicherungsmodell, $praemienregion, $unfalldeckung) {
        $sql = "SELECT * FROM ".strtolower($verbindung->real_escape_string($altersgruppe))." WHERE Versicherungsmodell='".$verbindung->real_escape_string($versicherungsmodell)."' AND Kanton='".$verbindung->real_escape_string($praemienregion)."' AND Unfall='".$verbindung->real_escape_string($unfalldeckung)."'";
        $tarif = $verbindung->query($sql);
        return $tarif->fetch_assoc();	
    }
?>