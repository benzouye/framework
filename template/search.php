			<form class="form-horizontal" method="POST" action="index.php?item=<?=$page->alias;?>">
				<div class="card border-<?= $manager->getOption('colorschema'); ?>">
					<div class="card-header bg-<?= $manager->getOption('colorschema'); ?>">
						<span class="card-title">Critères de recherche</span>
					</div>
					<div class="card-body row">
<?php
	foreach( $colonnes as $colonne ) {
		if( !in_array( $colonne->params['type'], ['password','image','file','localisation','calculation'] ) ) {
			echo $object->displaySearchInput( $colonne->name );
		}
	}
?>
					</div>
					<div class="card-footer">
						<input type="hidden" id="search" name="search" value="1">
						<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i> Rechercher</button>
						<a href="index.php?item=<?=$page->alias;?>" class="btn btn-secondary btn-sm"><i class="bi bi-caret-left"></i> Retour liste</a>
					</div>
				</div>
			</form>
