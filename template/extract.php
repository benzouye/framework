		<h2><?php echo $item->description; ?></h2>
<?php
	$lignes = $object->getDatas();
	$csv = '';
	$entetes = array();
	$nom = date('Ymd_His').'.xls';
	$chemin = EXPDIR.$nom;
	$lien = SITEURL.$chemin;
	
	if( count( $lignes )>0 ) {
		
		foreach( $lignes[0] as $entete => $valeur ) {
			$entetes[] = $entete;
		}
		
		$excel = array();
		array_push( $excel, $entetes );
		foreach( $lignes as $ligne ) {
			array_push( $excel, (array) $ligne );
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
		<table class="table table-sm table-striped table-hover table-bordered table-responsive">
			<thead>
				<tr>
<?php
		foreach( $entetes as $entete ) {
?>
					<th><?php echo $entete; ?></th>
<?php
		}
?>
				</tr>
			</thead>
			<tbody>
<?php
		foreach( $lignes as $ligne ) {
?>
				<tr>
<?php
			foreach( $entetes as $entete ) {
?>
					<td class="text-center"><?php echo $ligne->{$entete}; ?></td>
<?php
			}
?>
				</tr>
<?php
		}
?>
			</tbody>
		</table>
<?php
	} else {
?>
		<p>Aucun résultat ne correspond à la requête ...</p>
<?php
	}
?>
		<p>
			<a href="index.php?item=<?php echo $backlink; ?>" class="btn btn-secondary btn-sm">
				<i class="fas fa-sm fa-caret-left"></i> Retour liste <?php echo $object->getPlural(); ?>
			</a>
		</p>
