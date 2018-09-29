<?php
	if( $userCan->admin or $userCan->read ) {
?>
			<div class="panel panel-default">
				<div class="container">
<?php
		if( !$parentItem && ( $userCan->admin or $userCan->create ) ) {
?>
					<a href="index.php?item=<?php echo $page->alias;?>&action=edit" class="btn btn-default btn-sm navbar-btn"><span class="glyphicon glyphicon-plus"></span> Ajouter</a>
<?php
		}
		if( $userCan->admin or $userCan->read ) {
?>
					<a href="index.php?item=<?php echo $page->alias;?>&action=search" class="btn btn-default btn-sm navbar-btn"><span class="glyphicon glyphicon-search"></span> Rechercher</a>
<?php
		}
		if( count($search) > 0 && ( $userCan->admin or $userCan->read ) ) {
			if( count($items)>0 ) {
?>
					<a href="index.php?item=<?php echo $page->alias;?>&action=list" class="btn btn-default btn-sm navbar-btn"><span class="glyphicon glyphicon-remove"></span> Supprimer les filtres</a>
<?php
			} else {
?>
					<a href="index.php?item=<?php echo $page->alias;?>&action=list" class="btn btn-default btn-sm navbar-btn"><span class="glyphicon glyphicon-backward"></span> Retour à la liste</a>
<?php
			}
		}
?>
					<?php echo $criteres; ?>
				</div>
			</div>
			<?php $object->displayPagination( $p ); ?>
<?php
		if( count($items)>0 ) {
?>
			<div class="table-responsive">
			<table class="table table-striped table-hover table-bordered">
				<thead>
					<tr>
						<th width="50">Ouvrir</th>
<?php
			if( ( $userCan->admin or $userCan->read ) and $page->alias == 'analyse' ) {
?>
						<th width="50">Voir</th>
<?php
			}
			foreach( $colonnes as $colonne ) {
				if( $colonne->visible ) {
?>
						<th><?php echo $colonne->nicename; ?></th>
<?php
				}
			}
			if( ( $userCan->admin or $userCan->delete ) and !$parentItem ) {
?>
						<th width="50">Suppr.</th>
<?php
			}
?>
					</tr>
				</thead>
				<tbody>
<?php
			foreach( $items as $element ) {
				$tdClass = '';
				$trClass = property_exists( $element, 'id_etat' ) ? $classes[$element->id_etat] : '';
?>
					<tr class="<?php echo $trClass; ?>">
						<td class="text-center">
							<a title="Ouvrir" href="index.php?item=<?php echo $page->alias;?>&action=edit&id=<?php echo $element->{'id_'.$page->alias}; ?>" class="btn btn-default btn-xs">
								<span class="glyphicon glyphicon-search"></span>
							</a>
						</td>
<?php
				if( ( $userCan->admin or $userCan->read ) and $page->alias == 'analyse' ) {
?>
						<td class="text-center">
							<a title="Résultat" href="index.php?item=<?php echo $page->alias;?>&action=extract&id=<?php echo $element->{'id_'.$page->alias}; ?>" class="btn btn-default btn-xs">
								<span class="glyphicon glyphicon-signal"></span>
							</a>
						</td>
<?php
				}
				foreach( $colonnes as $colonne ) {
					$tdClass = property_exists( $colonne, 'align' ) ? 'text-'.$colonne->align : '';
					if( $colonne->visible ) {
?>
						<td class="<?php echo $tdClass; ?>"><?php echo $object->displayField( $colonne->name, $element->{$colonne->name} ); ?></td>
<?php
					}
				}
				if( ( $userCan->admin or $userCan->delete ) and !$parentItem ) {
?>
						<td class="text-center">
							<form class="delete" method="post" action="index.php?item=<?php echo $page->alias;?>">
								<input type="hidden" name="id" value="<?php echo $element->{'id_'.$page->alias}; ?>" />
								<input type="hidden" name="item" value="<?php echo $page->alias; ?>" />
								<button type="submit" title="Supprimer" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></button>
							</form>
						</td>
<?php
				}
?>
					</tr>
<?php
			}
?>
				</tbody>
			</table>
			</div>
			<?php $object->displayPagination( $p, 2, true ); ?>
<?php
		}
	} else {
		$manager->setError( M_ACCESSERR );
	}
?>
