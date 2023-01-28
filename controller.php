<?php    
    function definiereAltersgruppe($jahrgang) {
        $altersjahr = date("Y") - $jahrgang;
        
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
?>