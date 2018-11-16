			<form class="form-horizontal" method="POST" action="index.php?item=<?php echo $page->alias;?>">
				<div class="card">
					<div class="card-header">
						<span class="card-title">Critères de recherche</span>
					</div>
					<div class="card-body row">
<?php
	foreach( $colonnes as $colonne ) {
		if( !in_array( $colonne->params['type'], ['password','image','file'] ) ) {
			$colGrid = $grille;
?>
						<div class="form-group row col-<?php echo $colGrid->div; ?>">
							<div class="col-<?php echo $colGrid->label; ?> col-form-label form-control-sm text-right"><?php echo $colonne->nicename; ?></div>
							<div class="col-<?php echo $colGrid->value; ?> input-group input-group-sm">
								<?php echo $object->displaySearchInput( $colonne->name, 'form-control form-control-sm' ); ?>

							</div>
						</div>
<?php
		}
	}
?>
					</div>
					<div class="card-footer">
						<input type="hidden" id="search" name="search">
						<button type="submit" class="btn btn-success btn-sm"><i class="fas fa-sm fa-search"></i> Rechercher</button>
						<a href="index.php?item=<?php echo $page->alias;?>" class="btn btn-secondary btn-sm"><i class="fas fa-sm fa-caret-left"></i> Retour liste</a>
					</div>
				</div>
			</form>
