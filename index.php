<!DOCTYPE html>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
<html lang="de">
	<head>
		<?php include 'head.php'; ?>
	</head>

	<body>
    <div id="wrapper">
		<?php 
			include 'header.php';
			include 'connection.php';

			function definiereAltersgruppe($jahrgang) {
				$alter = date("Y")- $jahrgang + 1;
				if ($alter < 19) {
					$altersgruppe = "Kinder";
				} elseif ($alter < 26) {
					$altersgruppe = "Jugendliche";
				} else {
					$altersgruppe = "Erwachsene";
				}
				return $altersgruppe;
			}

			function definiereFranchisen($altersgruppe) {
				if ($altersgruppe == "Kinder") {
					$franchisen = array (0, 100, 200, 300, 400, 500);
				} else {
					$franchisen = array (300, 500, 1000, 1500, 2000, 2500);
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

		<div class="container">
			<?php
				if(isset($_GET['output'])) {
					/* Formular-Eingaben auswerten */
					if(isset($_POST["jahrgang"])) {
						$jahrgang = filter_var($_POST["jahrgang"], FILTER_SANITIZE_NUMBER_INT);
					}
			
					if(isset($_POST["postleitzahl"])) {
						$postleitzahl = filter_var($_POST["postleitzahl"], FILTER_SANITIZE_NUMBER_INT);
					}
			
					if(isset($_POST["praemienort"])) {
						$praemienregion = filter_var($_POST["praemienort"], FILTER_SANITIZE_STRING);
					}

					$gesundheitskosten = intval($_POST["gesundheitskosten"]);
					
					if(isset($_POST["versicherungsmodell"])) {
						$versicherungsmodell = definiereVersicherungsmodell($_POST["versicherungsmodell"]);
					}
					
					if(isset($_POST["unfalldeckung"])) {
						$unfalldeckung = definiereUnfalldeckung($_POST["unfalldeckung"]);
					}
				}
			?>

            <div id="introductionContainer">
                <i id="introductionIcon" class="fas fa-info fa-3x"></i>
				<p id="introduction">
					Welche der von der möglichen Jahresfranchisen von 300 bis 2500 Franken ist für Sie empfehlenswert?
					Folgender Rechner kann Ihnen helfen, anhand Ihrer Gesundheitskosten die für Sie rentable Franchise zu finden.
				</p>
            </div>
			<form action="?output=1" method="post">
				<h4>Persönliche Daten</h4>
				<div class="box">
					<div class="row">
						<div class="col-12 col-md-4 form-group">
							<label for="jahrgang">Jahrgang</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-birthday-cake"></i></span>
							<input type="text" class="form-control" size="40" min="1900" max="<?php echo date("Y"); ?>" maxlength="250" name="jahrgang" placeholder="Jahrgang" value="<?php if(isset( $jahrgang)){echo htmlspecialchars($jahrgang);}?>">
                        </div>
                        </div>

						<div class="col-12 col-md-4">
							<div class="form-group">
								<label for="postleitzahl">Postleitzahl</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fas fa-map-marker-alt"></i></span>
									<input type="text" class="form-control" id="postleitzahl" size="40" min="1000" max="9999" maxlength="4" name="postleitzahl" placeholder="Postleitzahl" value="<?php if(isset( $postleitzahl)){echo htmlspecialchars($postleitzahl);}?>">
								</div>
                            </div>
								
							<?php
								if(isset($_POST["postleitzahl"])) {
								$plz = filter_var($_POST['postleitzahl'], FILTER_SANITIZE_NUMBER_INT);

									if(is_numeric($plz) AND $plz < 10000 AND $plz > 0){
										$sql = "SELECT * FROM orte WHERE PLZ = '".$verbindung->real_escape_string($plz)."'";
										$tarif = $verbindung->query($sql);
										$c_tarif = $verbindung->query($sql);

										$count = $c_tarif->fetch_assoc();
										$count = $count['Ort'];

										if($count != "") {
											echo "<span id='praemienstadt' style=''>";
												echo "<div class='form-group'><select class='form-control' name='praemienort' style='height: 100% !important;'>";
												echo "<option  selected disabled hidden style='display: none' value='' ></option>";
												
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
											echo "</span>";
										} else {
											echo "<p style='margin-top: 5px;'>Die Grundversicherung bieten wir in den Kantonen Wallis und Bern an.</p>";
										}
									} else {
										echo "";
									}
								}
							?>
						
							<span id='praemienstadt'></span>

							<script>
								$( "#postleitzahl" ).keyup(function() {
									if($('#postleitzahl').val().length == 4){

										$.post('plz.php', {'plz': $('#postleitzahl').val() }, function(data) {
												
											if(data != ""){
												$( "#praemienstadt").show();
												$( "#praemienstadt").html(data);
											}else{
												$( "#praemienstadt").hide();
											}
										});

									}else{
										$( "#praemienstadt" ).hide();
									}
								});
							</script>				
						</div>

						<div class="col-12 col-md-4 form-group">
							<label for="gesundheitskosten">Jährliche Gesundheitskosten</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-wallet"></i></span>
							<input type="text" class="form-control" size="40" maxlength="250" name="gesundheitskosten" placeholder="Gesundheitskosten" value="<?php if(isset( $gesundheitskosten)){echo htmlspecialchars($gesundheitskosten);}?>">
						</div>
                        </div>
					</div>
				</div>

				<h4>Grundversicherung</h4>
				<div class="box">
					<div class="row">
						<div class="col-12 col-md-9">
							<label style="display: block;">Versicherungsmodell</label>
							<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<label class="btn btn-green <?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Freie Arztwahl") ? "active" : "" ?>">
									<input type="radio" name="versicherungsmodell" autocomplete="off" value="freiearztwahl"
										<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Freie Arztwahl") ? "checked" : "" ?>> Freie Arztwahl
								</label>
								<label class="btn btn-green <?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Hausarzt-Modell") ? "active" : "" ?>">
									<input type="radio" name="versicherungsmodell" autocomplete="off" value="hausarztmodell"
										<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Hausarzt-Modell") ? "checked" : "" ?>> Hausarzt-Modell
								</label>
								<label class="btn btn-green <?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Telmed-Modell") ? "active" : "" ?>">
									<input type="radio" name="versicherungsmodell" autocomplete="off" value="telmedmodell" 
										<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Telmed-Modell") ? "checked" : "" ?>> Telmed-Modell
								</label>
								<label class="btn btn-green <?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Digimed-Modell") ? "active" : "" ?>">
                                    <input type="radio" name="versicherungsmodell" autocomplete="off" value="digimedmodell" 
										<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Digimed-Modell") ? "checked" : "" ?>> Digimed-Modell
                                </label>

							</div>
						</div>

						<div class="col-12 col-md-3">
							<label style="display: block;">Unfalldeckung</label>
							<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<label class="btn btn-green <?php echo (isset($unfalldeckung) and $unfalldeckung == "ja") ? "active" : "" ?>">
									<input type="radio" name="unfalldeckung" autocomplete="off" value="1"
										<?php echo (isset($unfalldeckung) and $unfalldeckung == "ja") ? "checked" : "" ?>> ja
								</label>
								<label class="btn btn-green <?php echo (isset($unfalldeckung) and $unfalldeckung == "nein") ? "active" : "" ?>">
									<input type="radio" name="unfalldeckung" autocomplete="off" value="0"
										<?php echo (isset($unfalldeckung) and $unfalldeckung == "nein") ? "checked" : "" ?>> nein
								</label>
							</div>
						</div>
					</div>
				</div>

				<p id="submit-buttons">
					<button type="submit" class="btn btn-default violet" >
                        <i class="fas fa-check"></i> Berechnen
                    </button>

					<a href="index.php" role="button" class="btn btn-light" >
						<i class="fas fa-times"></i> Zurücksetzen
					</a>	
				</p>
			</form>
			
			<?php
				if(isset($_GET['output'])) {	
				/* Prüfen, ob alles ausgefüllt wurde */
					if (empty($jahrgang) or empty($praemienregion) or empty($versicherungsmodell) or empty($unfalldeckung)){
						echo '<div class="alert alert-warning" role="alert">Bitte füllen Sie alle Formularfelder aus.</div>';
					}elseif($gesundheitskosten <0) {
                        echo '<div class="alert alert-warning" role="alert">Bitte geben Sie positive Gesundheitskosten an.</div>';
                    }elseif($jahrgang > date("Y",strtotime('+1 year')) or $jahrgang < 1900) {
						echo '<div class="alert alert-warning" role="alert">Bitte geben Sie einen gültigen Jahrgang an.</div>';
					}else {
						$altersgruppe = definiereAltersgruppe($jahrgang);
						$franchisen = definiereFranchisen($altersgruppe);
						
						/* Jahresprämie rechnen */
						$sql = "SELECT * FROM ".strtolower($verbindung->real_escape_string($altersgruppe))." WHERE Versicherungsmodell='".$verbindung->real_escape_string($versicherungsmodell)."' AND Kanton='".$verbindung->real_escape_string($praemienregion)."' AND Unfall='".$verbindung->real_escape_string($unfalldeckung)."'";
						$tarif =$verbindung->query($sql);
						while($monatspaemie = $tarif->fetch_assoc()) {
							foreach ($franchisen as $franchisen2) {
								$jahrespraemie = ($monatspaemie[$franchisen2]*12);
								$jahrespraemie1[] = $jahrespraemie;
							}
						}
						?>
						
						<!-- Errechnung und Ausgabe der Kosten -->
						<div class="box">
                            <h4>Kosten im Jahr 2022*</h4>
							<table class="table">
								<thead>
									<tr>
										<th style="border-top: 0"; scope="col">Franchise</th>
										<th style="border-top: 0"; scope="col">Kosten in CHF</th>
									</tr>
								</thead>
								<tbody>									
									<?php
										/* Franchisekosten und Selbstbehalt berechnen */
										/*$franchisen1 = $franchisen;	 Speichert das Array Franchisen (siehe Franchisen definieren) als neues Array */
										for($i = 0; $i < 6; $i++) {
											if ($franchisen[$i] < $gesundheitskosten) {
												$franchisekosten = $franchisen[$i];	/* Wenn die Gesundheitskosten höher sind als die Franchise, so fällt die ganze Franchise an */
												$selbstbehalt2 = ($gesundheitskosten - $franchisen[$i])*0.1;
												if ($altersgruppe != "Kinder" and $selbstbehalt2 < 700){
												$selbstbehalt = ($gesundheitskosten - $franchisen[$i])*0.1; /* Nach der Franchise fallen von der Differenz vom Gesundheitskosten und Franchise der Selbstbehalt von 10% an */
												} elseif ($altersgruppe != "Kinder" and $selbstbehalt2 >= 700) {
													$selbstbehalt = 700; /* Erwachsenene zahlen einen maximalen Selbstbehalt von CHF 700.- */
												} elseif ($altersgruppe = "Kinder" and $selbstbehalt2 < 350){
												$selbstbehalt = ($gesundheitskosten - $franchisen[$i])*0.1;
												} elseif ($altersgruppe = "Kinder" and $selbstbehalt2 >= 350) {
													$selbstbehalt = 350; /* Kinder zahlen einen maximalen Selbstbehalt von CHF 350.- */
												}
											} else {
												$franchisekosten = $gesundheitskosten;
												$selbstbehalt = 0;
											}
											$selbstbehalt1[] = $selbstbehalt; /* Der jeweilige Selbstbehalt wird nach der Schleife in das Array Selbstbehalt1 gespeichert */
											$franchisekosten1[] = $franchisekosten; /* Die jeweiligen Franchisekosten werden nach der Schleife in das Array Franchisekosten1 gespeichert */
										}
									?>
										
									<!-- Kosten rechnen und ausgeben -->
									<?php
										for($i = 0; $i < 6; $i++) {
											echo "<tr>
													<td style='padding: 20px'>
														".$franchisen[$i]."
													</td>
													<td class='td-kosten'>
														<a class='a-kosten' data-toggle='collapse' href='#collapseKosten".$i."' role='button' aria-expanded='false' aria-controls='collapseKosten".$i."'>
															".$jahrespraemie1[$i] + $franchisekosten1[$i] + $selbstbehalt1[$i]."<i style='float: right;' class='fas fa-info'></i>
														</a>
														<div class='collapse' id='collapseKosten".$i."'>
															<div style='margin-top: 10px;' class='card card-body'>
																Jahresprämie: ".$jahrespraemie1[$i]."<br>
																Franchise: ".$franchisekosten1[$i]."<br>
																Selbstbehalt: ".$selbstbehalt1[$i]."
															</div>
														</div>
													</td>
												</tr>"; /* Die Arrays Jahrespraemie1, Franchisekosten1 und Selbstbehalt1 addieren und ausgeben. */
										}
									?>
								</tbody>
							</table>
						</div>
						
						<?php
							/* Verbindung schliessen */
							$verbindung->close();
							
							/* Aus folgenden drei Arrays bestehen die Kosten. 0 steht für Franchise 300/0, 1 für 500/100 usw. */
							/*
							echo $jahrespraemie1[0] . "<br>";
							echo $franchisekosten1[0] . "<br>";
							echo $selbstbehalt1[0];
							*/
						
							echo "<p>*Bitte beachten Sie, dass die Berechnung auf Ihren Angaben basiert und daher keine Garantie gewährleistet wird.</p>";
					}
				}		
			?>
		</div>
	</div>
    <?php include 'footer.php'; ?>
	</body>
</html>
