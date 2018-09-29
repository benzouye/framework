			<form class="form-horizontal" method="POST" action="index.php?item=<?php echo $page->alias;?>">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<span class="panel-title">Critères de recherche</span>
					</div>
					<div class="panel-body row">
<?php
	foreach( $colonnes as $colonne ) {
		if( $colonne->params['type'] != 'password' && $colonne->params['type'] != 'image' ) {
			$colGrid = $grille;
?>
						<div class="form-group form-group-sm col-sm-<?php echo $colGrid->div; ?>">
							<label class="col-sm-<?php echo $colGrid->label; ?>" for="<?php echo $colonne->name; ?>"><?php echo $colonne->nicename; ?></label>
							<div class="col-sm-<?php echo $colGrid->value; ?> input-group">
								<?php echo $object->displaySearchInput( $colonne->name, 'form-control' ); ?>

							</div>
						</div>
<?php
		}
	}
?>
					</div>
					<div class="panel-footer">
						<input type="hidden" id="search" name="search">
						<button type="submit" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-search"></span> Rechercher</button>
						<a href="index.php?item=<?php echo $page->alias;?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-backward"></span> Retour liste</a>
					</div>
				</div>
			</form>
