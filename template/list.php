<?php
	if( $userCan->admin or $userCan->read ) {
		$nbItems = count($items);
		$analyses = $manager->getItemAnalyses( $page->alias );
		if( count( $analyses ) > 0 ) {
?>
			<div class="row">
<?php
			$oAnalyse = new Analyse( $bdd, $manager, $model_analyse );
			
			foreach( $analyses as $analyse ) {
				$element = $oAnalyse->getItem( $analyse->id_analyse );
				echo $oAnalyse->getDashboardHTML( $element );
			}
?>
			</div>
<?php
		}
?>
			<div class="card border-dark">
				<div class="card-body">
<?php
		if( !$parentItem && ( $userCan->admin or $userCan->create ) ) {
?>
					<a data-bs-toggle="tooltip" title="Ajouter" data-bs-placement="bottom" href="index.php?item=<?=$page->alias;?>&action=edit" class="btn btn-secondary btn-sm"><span class="bi bi-plus-lg"></span><span class="d-none d-xl-inline"> Ajouter</span></a>
<?php
		}
		if( $userCan->admin or $userCan->read ) {
?>
					<a data-bs-toggle="tooltip" title="Rechercher" data-bs-placement="bottom" href="index.php?item=<?=$page->alias;?>&action=search" class="btn btn-secondary btn-sm"><span class="bi bi-search"></span><span class="d-none d-xl-inline"> Rechercher</span></a>
<?php
		}
		if( count($search) > 0 && ( $userCan->admin or $userCan->read ) ) {
?>
					<a data-bs-toggle="tooltip" title="Suppr. filtres" data-bs-placement="bottom" href="index.php?item=<?=$page->alias;?>&action=list" class="btn btn-secondary btn-sm"><span class="bi bi-trash"></span><span class="d-none d-xl-inline"> Supprimer les filtres</span></a>
<?php
		}
?>
					<div class="float-end">
						<a title="Imprimer" data-bs-toggle="tooltip" data-bs-placement="bottom" target="_blank" href="index.php?item=<?=$page->alias;?>&action=print&type=list" class="btn btn-secondary btn-sm"><span class="bi bi-printer"></span></a>
						<a title="Exporter" data-bs-toggle="tooltip" data-bs-placement="bottom" href="index.php?item=<?=$page->alias;?>&action=export&type=list" class="btn btn-secondary btn-sm"><span class="bi bi-download"></span></a>
					</div>
					<?=$criteres; ?>
				</div>
			</div>
			<?php $object->displayPagination( $p ); ?>
<?php
		if( $nbItems>0 ) {
?>
			<div class="table-responsive">
			<table class="table table-sm table-striped table-hover table-bordered">
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
						<th><?=$colonne->nicename; ?>
<?php
					if( !in_array( $colonne->params['type'] , [ 'image', 'file', 'localisation' ] ) ) {
?>
						&nbsp;<a class="text-secondary" title="Trier par <?=$colonne->nicename; ?> croissant" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=<?=$page->alias;?>&orderby=<?=$colonne->name; ?>&orderway=asc"><span class="col-sort bi bi-sort-up"></span></a>
						&nbsp;<a class="text-secondary" title="Trier par <?=$colonne->nicename; ?> décroissant" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=<?=$page->alias;?>&orderby=<?=$colonne->name; ?>&orderway=desc"><span class="bi bi-sort-down"></span></a>
<?php
					}
?>
						</th>
<?php
				}
			}
			foreach( $objectActions as $objectAction ) {
				if( $objectAction->listable ) {
?>
						<th><?=$objectAction->nicename; ?></th>
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
				
				// Gestion readonly state
				$readOnly = false;
				foreach( $object->getReadOnlyStates() as $readOnlyState ) {
					if( property_exists( $element, $readOnlyState->column ) ) {
						if( in_array( $element->{$readOnlyState->column}, $readOnlyState->values ) ) {
							$readOnly = true;
						}
					}
				}
?>
					<tr>
						<td class="text-center align-middle">
							<a title="Ouvrir" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=<?=$page->alias;?>&action=edit&id=<?=$element->{'id_'.$page->alias}; ?>" class="btn btn-secondary btn-sm">
								<i class="bi bi-search"></i>
							</a>
						</td>
<?php
				if( ( $userCan->admin or $userCan->read ) and $page->alias == 'analyse' ) {
?>
						<td class="text-center align-middle">
							<a title="Résultat" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=<?=$page->alias;?>&action=extract&id=<?=$element->{'id_'.$page->alias}; ?>" class="btn btn-secondary btn-sm">
								<i class="bi bi-bar-chart"></i>
							</a>
						</td>
<?php
				}
				foreach( $colonnes as $colonne ) {
					$tdClass = property_exists( $colonne, 'align' ) ? 'text-'.$colonne->align : '';
					if( $colonne->visible ) {
?>
						<td class="<?=$tdClass; ?> align-middle"><?=$object->displayField( $colonne->name, $element->{$colonne->name} ); ?></td>
<?php
					}
				}
				foreach( $objectActions as $objectAction ) {
					if( $objectAction->listable && ( !$readOnly or $userCan->admin ) ) {
?>
						<td class="text-center align-middle"><?=$object->displayObjectAction( $page->alias, $objectAction->alias, $element->{'id_'.$page->alias}, 'list' ); ?></td>
<?php
					}
				}
				if( ( $userCan->admin or $userCan->delete ) and !$parentItem ) {
?>
						<td class="text-center align-middle">
<?php
					if( !$readOnly or $userCan->admin ) {
?>
							<form class="delete" method="post" action="index.php?item=<?=$page->alias;?>">
								<input type="hidden" name="id" value="<?=$element->{'id_'.$page->alias}; ?>" />
								<input type="hidden" name="item" value="<?=$page->alias; ?>" />
								<button type="submit" title="Supprimer" data-bs-toggle="tooltip" data-bs-placement="top" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
							</form>
<?php
					}
?>
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
		$manager->setMessage( M_ACCESSERR ,true);
	}
