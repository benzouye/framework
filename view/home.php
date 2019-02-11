<?php
	if( !$user ) {
		// On affiche le formulaire de connexion
		include( VIEWDIR.'login.php' );
	} else {
		// Sinon on affiche la page d'accueil
?>
				<div class="row">
<?php
		echo $manager->getOption('homeText');
		
		$analyse = new Analyse( $bdd, $manager, $model_analyse );
		$analyses = $analyse->getItems();
		
		foreach( $analyses as $element ) {
			$html = '';
			$classeTable = 'table table-sm table-striped table-hover table-bordered';
			$idTable = 'tableau-'.$element->id_analyse;
			
			if( $element->flag_accueil ) {
				$requete = $analyse->getItem( $element->id_analyse );
				$datas = $analyse->getDatas();
				$nbElements = count( $datas );
				
				if( $nbElements ) {
					switch( $element->id_type_analyse ) {
						case 1 :
							$table = new SimpleTable( $datas, $element->indicator, $element->percent );
							$html = $table->getHtml( $idTable, $classeTable );
							break;
						case 2 :
							$table = new PivotTable( $datas, $element->colonne, $element->ligne, $element->indicator );
							$html = $table->getHtml( $idTable, $classeTable );
							break;
						case 3 :
							$html = $element->indicator.' : '.$datas[0]->{$element->indicator};
							break;
						case 4 :
							$html = '<canvas data-analyse="'.$element->id_analyse.'" class="homepage-chart"></canvas>';
							break;
						case 5 :
							$html = '<div class="leaflet-display" id="leaflet-'.$element->id_analyse.'" data-analyse="'.$element->id_analyse.'"></div>';
							break;
						case 6 :
							$html = '<div class="homepage-calendar" id="calendar-'.$element->id_analyse.'" data-analyse="'.$element->id_analyse.'"></div>';
							break;
					}
							
				} else {
					$html = '<p><em>Aucune données à afficher</em></p>';
				}
?>
					<div class="col-<?php echo $requete->grid; ?>">
						<div class="card card-dark">
							<div class="card-header">
								<span class="card-title"><?php echo $requete->description; ?></span> <span class="badge badge-light float-right"><?php echo $nbElements; ?></span>
							</div>
							<div class="card-body">
								<?php echo $html; ?>
							</div>
							<div class="card-footer no-print">
								<a href="index.php?item=analyse&action=extract&id=<?php echo $element->id_analyse; ?>" class="btn btn-secondary btn-sm"><span class="fas fa-search"></span> Voir les données</a>
							</div>
						</div>
					</div>
<?php
			}
		}
?>
				</div>
<?php
	}
?>
