<?php
	if( $userCan->admin or $userCan->create or $userCan->read or $userCan->update or $userCan->delete or ( $page->alias == 'utilisateur' && $item->{'id_'.$page->alias} == $user->id_utilisateur ) ) {
?>
					<div class="card">
						<div class="card-header">
<?php
		// Bouton suppression
		if( ( $userCan->admin or $userCan->delete ) and !$parentItem ) {
?>
							<form class="delete" method="post" action="index.php?item=<?php echo $page->alias;?>">
								<input type="hidden" name="id" value="<?php echo $item->{'id_'.$page->alias}; ?>" />
								<input type="hidden" name="item" value="<?php echo $page->alias; ?>" />
								<button id="item-delete" type="submit" class="btn btn-danger btn-sm float-right"><i class="fas fa-sm fa-times"></i> Supprimer</button>
							</form>
<?php
		}
		
		// Boutons d'action
		if( count( $objectActions ) > 0 && !$new && $visibleObjectActions ) {
			foreach( $objectActions as $objectAction ) {
				echo $object->displayObjectAction( $page->alias, $objectAction->alias, $id, 'edit' );
			}
		}
?>
							<span class="card-title">Informations principales</span>
						</div>
						<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=<?php echo $savelink; ?>">
							<input type="hidden" name="action" value="set"/>
							<input type="hidden" name="id" value="<?php echo $id; ?>"/>
							<input type="hidden" name="item" value="<?php echo $page->alias; ?>"/>
							<div class="card-body row">
<?php
		// Affichage du formulaire
		foreach( $colonnes as $colonne ) {
			// Gestion de la grille CSS
			$colGrid = property_exists( $colonne, 'grid' ) ? $colonne->grid : $grille;
			
			// Valeur par défaut
			if( property_exists( $colonne , 'default' ) ) {
				$default = $colonne->default;
			} elseif( $colonne->name == 'id_'.$page->alias ) {
				$default = $id;
			} elseif( $parentItem && $colonne->name == 'id_'.$parentItem ) {
				$default = $parentId;
			} else {
				$default = false;
			}
			$valeur = $new ? $default : $item->{$colonne->name};
			
			$adminInput = false;
			if( property_exists( $colonne, 'admin' ) ) {
				if( $colonne->admin ) {
					$adminInput = true;
				}
			} else {
				$adminInput = false;
			}
			
			if( ( $adminInput && $user->admin ) or !$adminInput ) {
?>
								<div class="form-group row col-<?php echo $colGrid->div; ?>">
									<div class="col-<?php echo $colGrid->label; ?> col-form-label form-control-sm text-right"><?php echo $colonne->nicename; ?></div>
									<div class="col-<?php echo $colGrid->value; ?> input-group input-group-sm">
										<?php echo $object->displayInput( $id, $colonne->name, $valeur, 'form-control form-control-sm' ); ?>
									</div>
								</div>
<?php
			}
		}
		// Affichage des boutons
?>
							</div>
							<div class="card-footer">
<?php
		if( $userCan->admin or $userCan->create or $userCan->update or ( $page->alias == 'utilisateur' && $item->{'id_'.$page->alias} == $user->id_utilisateur ) ) {
?>
								<button name="form-submit" type="submit" class="btn btn-success btn-sm navbar-btn">
									<i class="fas fa-sm fa-save"></i> <?php echo $new ? 'Créer' : 'Sauvegarder'; ?>
								</button>
<?php
		}
		if( $page->alias == 'utilisateur' && !$user->admin ) {
?>
								<a href="index.php" class="btn btn-secondary btn-sm">
									<i class="fas fa-sm fa-caret-left"></i> Retour accueil
								</a>
<?php
		} else {
?>
								<a href="index.php?item=<?php echo $backlink; ?>" class="btn btn-secondary btn-sm">
									<i class="fas fa-sm fa-caret-left"></i> Retour liste <?php echo $object->getPlural(); ?>
								</a>
<?php
		}
		if( $parentLink ) {
?>
								<a href="index.php?item=<?php echo $parentLink; ?>" class="btn btn-secondary btn-sm">
									<i class="fas fa-sm fa-caret-left"></i> Retour <?php echo $parentItem; ?>
								</a>
<?php
		}
		
		// Boutons d'impression
		$nbPrints = 0;
		foreach( $prints as $print ) {
			if( property_exists( $print, 'visible' ) ) {
				if( $print->visible ) {
					$nbPrints++;
				}
			}
		}
		if( $nbPrints > 0 && !$new && $visiblePrints ) {
?>
									<button class="btn btn-secondary btn-sm navbar-btn dropdown-toggle" type="button" id="menuPrint" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										<i class="fas fa-sm fa-print"></i> Imprimer
									</button>
									<div class="dropdown-menu" aria-labelledby="menuPrint">
<?php
			foreach( $prints as $print ) {
				if( $print->visible ) {
					if( $print->separator ) {
?>
										<div class="dropdown-divider"></div>
<?php
					} else {
						$printLink = 'index.php?item='.$page->alias.'&action=print&id='.$id.'&type='.$print->alias;
?>
										<a class="dropdown-item btnReload" href="<?php echo $printLink; ?>" target="_blank"><?php echo $print->nicename; ?></a>
<?php
					}
				}
			}
?>
									</div>
<?php
		}
		
		if( $parentLink && !$new && ( $userCan->admin or $userCan->create or $userCan->update ) ) {
?>
								<a href="index.php?item=<?php echo $page->alias; ?>&action=edit&parent=<?php echo $parentId; ?>" class="btn btn-secondary btn-sm">
									<i class="fas fa-sm fa-plus"></i> Nouveau <?php echo $object->getSingle(); ?> pour cette <?php echo $parentItem; ?>
								</a>
<?php
		}
?>
							</div>
						</form>
					</div>
<?php
		// Affichage des relations pour les variants en modification
		if( !$new && count($relations) > 0 ) {
?>
				<div class="row">
<?php
			foreach( $relations as $relation ) {
				if( method_exists( $object, 'get_'.$relation->item ) ) {
					$items = $object->{'get_'.$relation->item}();
					$nbItems = ( count( $items ) > 0 && !$relation->static ) ? ' <span class="badge badge-light">'.count( $items ).'</span>' : '';
					$classLink = 'btn btn-secondary btn-sm';
					if( !property_exists( $relation, 'many' ) ) {
						$addLink = 'href="index.php?item='.$relation->item.'&action=edit&parent='.$id.'"';
					} else {
						$addLink = 'data-rel-item="'.$relation->item.'" data-parent-item="'.$page->alias.'" data-parent-id="'.$id.'" data-toggle="modal" data-target="#relation-modal"';
						$classLink .= ' add-relation';
					}
?>
					<div class="col-sm-<?php echo $relation->grid; ?>">
						<div class="card">
							<div class="card-header">
								<span class="panel-title"><?php echo $relation->name .$nbItems; ?></span>
							</div>
							<div class="card-body">
<?php
					if( count( $items ) > 0 ) {
						if( file_exists( TEMPLDIR.$action.'.'.$page->alias.'.'.$relation->item.'.php' ) )
							require_once( TEMPLDIR.$action.'.'.$page->alias.'.'.$relation->item.'.php' );
						else
							$manager->setError( sprintf( M_TMPLERR, $action.'.'.$page->alias.'.'.$relation->item ) );
					} else {
?>
								<p>Aucun élément ...</p>
<?php
					}
?>
							</div>
<?php
					if( !$relation->static && ( $userCan->admin or $userCan->create or $userCan->update ) ) {
?>
							<div class="card-footer">
								<a <?php echo $addLink; ?> class="<?php echo $classLink; ?>">
									<i class="fas fa-sm fa-plus"></i> Ajouter
								</a>
							</div>
<?php
					}
?>
						</div>
					</div>
<?php
				} else {
					$manager->setError( sprintf( M_CLASSERR, $relation->name ) );
				}
				
			}
?>
				</div>
				<div id="relation-modal" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<form method="POST" action="index.php?item=<?php echo $savelink; ?>">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">Choisissez les éléments à ajouter</h4>
								</div>
								<div class="modal-body">
									<ul id="relation-ul" class="list-group"></ul>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
										<i class="fas fa-sm fa-caret-left"></i> Retour
									</button>
									<button type="submit" class="btn btn-success btn-sm">
										<i class="fas fa-sm fa-caret-save"></i> Sauvegarder
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
<?php
		}
	}
?>
