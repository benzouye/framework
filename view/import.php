<?php
	if( $user ) {	
		
		if( !empty( $_GET['action'] ) ) {
			
			if( file_exists( TEMPLDIR.'import.'.$_GET['action'].'.php' ) ) {
?>
					<div class="row">
						<div class="col-12">
							<div class="card card-dark border-<?= $manager->getOption('colorschema'); ?>">
								<div class="card-header bg-<?= $manager->getOption('colorschema'); ?>">
									<span class="panel-title">Import des données pour l'élément <strong><?= $_GET['action']; ?></strong></span>
								</div>
<?php
				$importObject = new Model( $bdd, $manager, ${'model_'.$_GET['action']} );
				$importColumns = $importObject->getColumns();
				$excludedTypes = ['calculation'];
			
				if( isset( $_FILES['fichier'] ) ) {
					$handle = new Verot\Upload\Upload( $_FILES['fichier'] );
					$extension = $handle->file_src_name_ext;
					$nomFichier = 'import_'.uniqid();
					
					if( $handle->uploaded ) {
						$handle->file_new_name_body = $nomFichier;
						$handle->process( UPLDIR );
						
						if ($handle->processed) {
							$handle->clean();
						} else {
							$this->manager->setMessage( $handle->error, true );
						}
					}
					
					switch( $extension ) {
						case 'xls':
							$reader = new PhpOffice\PhpSpreadsheet\Reader\Xls();
							break;
						case 'xlsx':
							$reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
							break;
						case 'ods':
							$reader = new PhpOffice\PhpSpreadsheet\Reader\Ods();
							break;
					}
					$spreadsheet = $reader->load( ROOTDIR.UPLDIR.$nomFichier.'.'.$extension );
					$sheet = $spreadsheet->getActiveSheet();
					$importDatas = $sheet->toArray();
					unlink( UPLDIR.$nomFichier.'.'.$extension );
					
					require_once( TEMPLDIR.'import.'.$_GET['action'].'.php' );
				
				} else {
?>
								<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=import&action=<?= $_GET['action']; ?>">
									<div class="card-body">
										<div class="table-responsive">
											<table class="table table-sm table-striped table-hover table-bordered">
												<thead>
													<tr>
														<th>Nom affiché</th>
														<th>Nom en base</th>
														<th>Type</th>
													</tr>
												</thead>
												<tbody>
<?php
					foreach( $importColumns as $colonne ) {
						if( !in_array( $colonne->params['type'], $excludedTypes ) ) {
?>
													<tr>
														<td><?= $colonne->nicename; ?></td>
														<td><?= $colonne->name; ?></td>
														<td><?= $colonne->params['type']; ?></td>
													</tr>
<?php
						}
					}
?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="card-footer">
										<div class="row">
											<div class="col-4">
												<input class="form-control form-control-sm" required="required" type="file" accept=".ods,.xls,.xlsx" name="fichier" />
											</div>
											<div class="col-4">
												<input class="btn btn-sm btn-success" type="submit" value="Lancer l'import">
											</div>
										</div>
									</div>
								</form>
<?php
				}
?>
							</div>
						</div>
					</div>
<?php
			} else {
				$manager->setMessage( 'Aucun élément spécifié pour l\'import, réessayez depuis l\'écran de liste de l\'élément souhaité', true );
			}
		} else {
			$manager->setMessage( 'Le fichier template n\'existe pas pour l\'import de cet élément', true );
		}
	}
