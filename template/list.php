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
					<a href="index.php?item=<?php echo $page->alias;?>&action=edit" class="btn btn-secondary btn-sm"><span class="fas fa-plus"></span><span class="d-none d-xl-inline"> Ajouter</span></a>
<?php
		}
		if( $userCan->admin or $userCan->read ) {
?>
					<a href="index.php?item=<?php echo $page->alias;?>&action=search" class="btn btn-secondary btn-sm"><span class="fas fa-search"></span><span class="d-none d-xl-inline"> Rechercher</span></a>
<?php
		}
		if( count($search) > 0 && ( $userCan->admin or $userCan->read ) ) {
?>
					<a href="index.php?item=<?php echo $page->alias;?>&action=list" class="btn btn-secondary btn-sm"><span class="fas fa-trash-alt"></span><span class="d-none d-xl-inline"> Supprimer les filtres</span></a>
<?php
		}
?>
					<div class="float-right">
						<a title="Imprimer" target="_blank" href="index.php?item=<?php echo $page->alias;?>&action=print&type=list" class="btn btn-secondary btn-sm"><span class="fas fa-print"></span></a>
						<a title="Exporter" href="index.php?item=<?php echo $page->alias;?>&action=export&type=list" class="btn btn-secondary btn-sm"><span class="fas fa-download"></span></a>
					</div>
					<?php echo $criteres; ?>
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
						<th><?php echo $colonne->nicename; ?>
<?php
					if( !in_array( $colonne->params['type'] , [ 'image', 'file', 'localisation' ] ) ) {
?>
						&nbsp;<a class="text-secondary" title="Trier par <?php echo $colonne->nicename; ?> croissant" href="index.php?item=<?php echo $page->alias;?>&orderby=<?php echo $colonne->name; ?>&orderway=asc"><span class="col-sort fas fa-sort-amount-up fa-xs"></span></a>
						&nbsp;<a class="text-secondary" title="Trier par <?php echo $colonne->nicename; ?> décroissant" href="index.php?item=<?php echo $page->alias;?>&orderby=<?php echo $colonne->name; ?>&orderway=desc"><span class="fas fa-sort-amount-down fa-xs"></span></a>
<?php
					}
?>
						</th>
<?php
				}
			}
			foreach( $objectActions as $objectAction ) {
				if( $objectAction->visible ) {
?>
						<th><?php echo $objectAction->nicename; ?></th>
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
				$trClass = property_exists( $element, 'id_etat' ) ? $stateBgClasses[$element->id_etat] : '';
				
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
					<tr class="<?php echo $trClass; ?>">
						<td class="text-center align-middle">
							<a title="Ouvrir" href="index.php?item=<?php echo $page->alias;?>&action=edit&id=<?php echo $element->{'id_'.$page->alias}; ?>" class="btn btn-secondary btn-sm">
								<i class="fas fa-xs fa-search"></i>
							</a>
						</td>
<?php
				if( ( $userCan->admin or $userCan->read ) and $page->alias == 'analyse' ) {
?>
						<td class="text-center align-middle">
							<a title="Résultat" href="index.php?item=<?php echo $page->alias;?>&action=extract&id=<?php echo $element->{'id_'.$page->alias}; ?>" class="btn btn-secondary btn-sm">
								<i class="fas fa-xs fa-chart-bar"></i>
							</a>
						</td>
<?php
				}
				foreach( $colonnes as $colonne ) {
					$tdClass = property_exists( $colonne, 'align' ) ? 'text-'.$colonne->align : '';
					if( $colonne->visible ) {
?>
						<td class="<?php echo $tdClass; ?> align-middle"><?php echo $object->displayField( $colonne->name, $element->{$colonne->name} ); ?></td>
<?php
					}
				}
				foreach( $objectActions as $objectAction ) {
					if( $objectAction->visible && ( !$readOnly or $userCan->admin ) ) {
?>
						<td class="text-center align-middle"><?php echo $object->displayObjectAction( $page->alias, $objectAction->alias, $element->{'id_'.$page->alias}, 'list' ); ?></td>
<?php
					}
				}
				if( ( $userCan->admin or $userCan->delete ) and !$parentItem ) {
?>
						<td class="text-center">
<?php
					if( !$readOnly or $userCan->admin ) {
?>
							<form class="delete" method="post" action="index.php?item=<?php echo $page->alias;?>">
								<input type="hidden" name="id" value="<?php echo $element->{'id_'.$page->alias}; ?>" />
								<input type="hidden" name="item" value="<?php echo $page->alias; ?>" />
								<button type="submit" title="Supprimer" class="btn btn-danger btn-sm"><i class="fas fa-xs fa-trash-alt"></i></button>
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
		$manager->setError( M_ACCESSERR );
	}
?>
