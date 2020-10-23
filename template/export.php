		<h2>Export XLS de la liste de résultats : <?php echo count( $items ); ?> <?php echo count( $items ) > 1 ? $object->getPlural() : $object->getSingle(); ?></h2>
<?php
	$csv = '';
	$entetes = array();
	$nom = date('Ymd_His').'.xls';
	$chemin = EXPDIR.$nom;
	$lien = SITEURL.$chemin;
	$notPrintColonnes = [ 'password', 'file', 'image' ];
	
	if( count( $items ) > 0 ) {
		
		$excel = [];
		$entetes = [];
		foreach( $colonnes as $colonne ) {
			if( !in_array( $colonne->params['type'], $notPrintColonnes ) && !$colonne->admin ) {
				$entetes[] = $colonne->nicename;
			}
		}
		array_push( $excel, $entetes );
		
		foreach( $items as $element ) {
			$ligne = [];
			foreach( $colonnes as $colonne ) {
				if( !in_array( $colonne->params['type'], $notPrintColonnes ) && !$colonne->admin ) {
					array_push( $ligne, $object->getFieldXLS( $colonne->name, $element->{$colonne->name} ) );
				}
			}
			array_push( $excel, $ligne );
		}
		
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->fromArray( $excel, NULL, 'A1' );
		$writer = new PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		$writer->save($chemin);
?>
		<p>
			<a href="index.php?item=<?php echo $backlink; ?>" class="btn btn-secondary btn-sm">
				<i class="fas fa-sm fa-caret-left"></i> Retour liste <?php echo $object->getPlural(); ?>
			</a>
			<a href="<?php echo $lien; ?>" download="<?php echo $nom; ?>" class="btn btn-success btn-sm">
				<i class="fas fa-sm fa-upload"></i> Télécharger ces données au format XLS
			</a>
		</p>
<?php
	} else {
?>
		<p>
			<a href="index.php?item=<?php echo $backlink; ?>" class="btn btn-secondary btn-sm">
				<i class="fas fa-sm fa-caret-left"></i> Retour liste <?php echo $object->getPlural(); ?>
			</a>
		</p>
<?php
	}
?>
