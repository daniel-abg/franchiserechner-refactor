<!DOCTYPE html>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
<html lang="de">
	<head>
		<?php include 'head.php'; ?>
	</head>

	<body>
    <div id="wrapper">
		<?php 
			define("PRAEMIENJAHR", 2023);

			include 'header.php';
			include 'connection.php';
			include 'functions.php';
			include 'plz.php';

			if(isset($_GET['output'])) {
				if(isset($_POST["jahrgang"])) {
					$jahrgang = filter_var($_POST["jahrgang"], FILTER_SANITIZE_NUMBER_INT);
				}
				if(isset($_POST["postleitzahl"])) {
					$postleitzahl = filter_var($_POST["postleitzahl"], FILTER_SANITIZE_NUMBER_INT);
				}	
				if(isset($_POST["praemienort"])) {
					$praemienregion = filter_var($_POST["praemienort"], FILTER_SANITIZE_STRING);
				}
				if(isset($_POST["gesundheitskosten"])) {
					$gesundheitskosten = intval($_POST["gesundheitskosten"]);
				}
				if(isset($_POST["versicherungsmodell"])) {
					$versicherungsmodell = definiereVersicherungsmodell($_POST["versicherungsmodell"]);
				}
				if(isset($_POST["unfalldeckung"])) {
					$unfalldeckung = definiereUnfalldeckung($_POST["unfalldeckung"]);
				}
			}
		?>

		<div class="container">
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
						<div class="col-12 col-md-4 mb-3">
							<label for="jahrgang" class="form-label fw-bold">Jahrgang</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
								<input type="text" class="form-control" id="jahrgang" size="40" min="1900" max="<?php echo date("Y"); ?>" maxlength="4" name="jahrgang"
									placeholder="Jahrgang" autocomplete="off" value="<?php if(isset( $jahrgang)){echo htmlspecialchars($jahrgang);}?>">
                       		</div>
                        </div>

						<div class="col-12 col-md-4 mb-3">
							<label for="postleitzahl" class="form-label fw-bold">Postleitzahl</label>
							<div class="input-group">
								<span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
								<input type="text" class="form-control" id="postleitzahl" size="40" min="1000" max="9999" maxlength="4" name="postleitzahl"
									placeholder="Postleitzahl" autocomplete="off" value="<?php if(isset( $postleitzahl)){echo htmlspecialchars($postleitzahl);}?>">
							</div>

							<span id='praemienstadt'>
								<?php
									if(isset($postleitzahl)) {
										holeGemeinde($postleitzahl);
									}
								?>
							</span>

							<script>
								$("#postleitzahl").keyup(function() {
									if($('#postleitzahl').val().length != 4) { 
										$("#praemienstadt").hide();
										return;
									}

									$.post('plz.php', {'plz': $('#postleitzahl').val() }, function(data) {
										if(data == ""){ 
											$("#praemienstadt").hide();
											return;
										}
										
										$("#praemienstadt").show(); 
										$("#praemienstadt").html(data);										
									});
								});
							</script>				
						</div>

						<div class="col-12 col-md-4 mb-3">
							<label for="gesundheitskosten" class="form-label fw-bold">Jährliche Gesundheitskosten</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-wallet"></i></span>
								<input type="text" class="form-control" id="gesundheitskosten" size="40" maxlength="250" name="gesundheitskosten" 
									placeholder="Gesundheitskosten" autocomplete="off" value="<?php if(isset( $gesundheitskosten)){echo htmlspecialchars($gesundheitskosten);}?>">
							</div>
                        </div>
					</div>
				</div>

				<h4>Grundversicherung</h4>
				<div class="box">
					<div class="row">
						<div class="col-12 col-md-9 mb-3">
							<label style="display: block;" class="form-label fw-bold">Versicherungsmodell</label>
							<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
								<input type="radio" class="btn-check" name="versicherungsmodell" id="freiearztwahl" value="freiearztwahl" autocomplete="off"
									<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Freie Arztwahl") ? "checked" : "" ?>>
								<label class="btn btn-green" for="freiearztwahl">Freie Arztwahl</label>

								<input type="radio" class="btn-check" name="versicherungsmodell" id="hausarztmodell" value="hausarztmodell" autocomplete="off" 
									<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Hausarzt-Modell") ? "checked" : "" ?>>
								<label class="btn btn-green" for="hausarztmodell">Hausarzt-Modell</label>

								<input type="radio" class="btn-check" name="versicherungsmodell" id="telmedmodell" value="telmedmodell" autocomplete="off"
									<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Telmed-Modell") ? "checked" : "" ?>>
								<label class="btn btn-green" for="telmedmodell">Telmed-Modell</label>

								<input type="radio" class="btn-check" name="versicherungsmodell" id="digimedmodell" value="digimedmodell" autocomplete="off"
									<?php echo (isset($versicherungsmodell) and $versicherungsmodell == "Digimed-Modell") ? "checked" : "" ?>>
								<label class="btn btn-green" for="digimedmodell">Digimed-Modell</label>
							</div>
						</div>

						<div class="col-12 col-md-3 mb-3">
							<label style="display: block;" class="form-label fw-bold">Unfalldeckung</label>
							<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
								<input type="radio" class="btn-check" name="unfalldeckung" id="ja" value="1" autocomplete="off"
									<?php echo (isset($unfalldeckung) and $unfalldeckung == "ja") ? "checked" : "" ?>>
								<label class="btn btn-green" for="ja">Ja</label>

								<input type="radio" class="btn-check" name="unfalldeckung" id="nein" value="0" autocomplete="off"
									<?php echo (isset($unfalldeckung) and $unfalldeckung == "nein") ? "checked" : "" ?>>
								<label class="btn btn-green" for="nein">Nein</label>
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
					if (empty($jahrgang) or empty($praemienregion) or empty($versicherungsmodell) or empty($unfalldeckung)) {
						echo '<div class="alert alert-warning" role="alert">Bitte füllen Sie alle Formularfelder aus.</div>';
					} elseif($gesundheitskosten < 0) {
                        echo '<div class="alert alert-warning" role="alert">Bitte geben Sie positive Gesundheitskosten an.</div>';
                    } elseif($jahrgang > date("Y",strtotime('+1 year')) or $jahrgang < 1900) {
						echo '<div class="alert alert-warning" role="alert">Bitte geben Sie einen gültigen Jahrgang an.</div>';
					} else {
						$altersgruppe = definiereAltersgruppe(PRAEMIENJAHR, $jahrgang);
						$franchisen = definiereFranchisen($altersgruppe);
						$monatspaemie = holeMonatspreamie($verbindung, $altersgruppe, $versicherungsmodell, $praemienregion, $unfalldeckung);
						
						foreach($franchisen as $franchise) {
							if ($gesundheitskosten > $franchise) {
								$franchisekosten = $franchise;
								$selbstbehalt = berechneSelbstbehalt($franchise, $gesundheitskosten, $altersgruppe);
							} else {
								$franchisekosten = $gesundheitskosten;
								$selbstbehalt = 0;
							}
							
							$jahrespraemie[] = ($monatspaemie[$franchise]*12);
							$franchisekostenArray[] = $franchisekosten;
							$selbstbehaltArray[] = $selbstbehalt;
						}			
					?>
						
						<div class="box">
                            <h4>Kosten im Jahr <?php echo PRAEMIENJAHR ?>*</h4>
							<table class="table">
								<thead>
									<tr>
										<th style="border-top: 0"; scope="col">Franchise</th>
										<th style="border-top: 0"; scope="col">Kosten in CHF</th>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i = 0; $i < 6; $i++) {
											echo "<tr>
													<td style='padding: 20px'>
														".$franchisen[$i]."
													</td>
													<td class='td-kosten'>
														<a class='a-kosten' data-bs-toggle='collapse' href='#collapseKosten".$i."' role='button' aria-expanded='false' aria-controls='collapseKosten".$i."'>
															".$jahrespraemie[$i] + $franchisekostenArray[$i] + $selbstbehaltArray[$i]."<i style='float: right;' class='fas fa-info'></i>
														</a>
														<div class='collapse' id='collapseKosten".$i."'>
															<div style='margin-top: 10px;' class='card card-body'>
																Jahresprämie: ".$jahrespraemie[$i]."<br>
																Franchise: ".$franchisekostenArray[$i]."<br>
																Selbstbehalt: ".$selbstbehaltArray[$i]."
															</div>
														</div>
													</td>
												</tr>";
										}
									?>
								</tbody>
							</table>
						</div>
						
						<?php
							$verbindung->close();
							echo '<div class="alert alert-info" role="alert">*Bitte beachten Sie, dass die Berechnung auf Ihren Angaben basiert und daher keine Garantie gewährleistet wird.</div>';					}
				}
			?>
		</div>
	</div>
    <?php include 'footer.php'; ?>
	</body>
</html>
