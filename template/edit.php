<?php
	if( $userCan->admin or $userCan->create or $userCan->read or $userCan->update or $userCan->delete or ( $page->alias == 'utilisateur' && $item->{'id_'.$page->alias} == $user->id_utilisateur ) ) {
?>
					<div class="panel panel-primary">
						<div class="panel-heading">
<?php
		if( ( $userCan->admin or $userCan->delete ) and !$parentItem ) {
?>
							<form class="delete" method="post" action="index.php?item=<?php echo $page->alias;?>">
								<input type="hidden" name="id" value="<?php echo $item->{'id_'.$page->alias}; ?>" />
								<input type="hidden" name="item" value="<?php echo $page->alias; ?>" />
								<button type="submit" title="supprimer" class="btn btn-danger btn-xs pull-right"><span class="glyphicon glyphicon-remove"></span> Supprimer</button>
							</form>
<?php
		}
?>
							<span class="panel-title">Informations principales</span>
						</div>
						<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=<?php echo $savelink; ?>">
							<input type="hidden" name="action" value="set"/>
							<input type="hidden" name="id" value="<?php echo $id; ?>"/>
							<input type="hidden" name="item" value="<?php echo $page->alias; ?>"/>
							<div class="panel-body row">
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
								<div class="form-group form-group-sm col-sm-<?php echo $colGrid->div; ?>">
									<label class="col-sm-<?php echo $colGrid->label; ?> control-label" for="<?php echo $colonne->name; ?>"><?php echo $colonne->nicename; ?></label>
									<div class="input-group col-sm-<?php echo $colGrid->value; ?>">
										<?php echo $object->displayInput( $id, $colonne->name, $valeur, 'form-control' ); ?>

									</div>
								</div>
<?php
			}
		}
		// Affichage des boutons
?>
							</div>
							<div class="panel-footer">
<?php
		if( $userCan->admin or $userCan->create or $userCan->update or ( $page->alias == 'utilisateur' && $item->{'id_'.$page->alias} == $user->id_utilisateur ) ) {
?>
								<button name="form-submit" type="submit" class="btn btn-success btn-sm navbar-btn">
									<span class="glyphicon glyphicon-floppy-disk"></span> <?php echo $new ? 'Créer' : 'Sauvegarder'; ?>
								</button>
<?php
		}
		if( $page->alias == 'utilisateur' && !$user->admin ) {
?>
								<a href="index.php" class="btn btn-default btn-sm">
									<span class="glyphicon glyphicon-backward"></span> Retour accueil
								</a>
<?php
		} else {
?>
								<a href="index.php?item=<?php echo $backlink; ?>" class="btn btn-default btn-sm">
									<span class="glyphicon glyphicon-backward"></span> Retour liste <?php echo $object->getPlural(); ?>
								</a>
<?php
		}
		if( $parentLink ) {
?>
								<a href="index.php?item=<?php echo $parentLink; ?>" class="btn btn-default btn-sm">
									<span class="glyphicon glyphicon-triangle-left"></span> Retour <?php echo $parentItem; ?>
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
								<span class="dropup">
									<button class="btn btn-default btn-sm navbar-btn dropdown-toggle" type="button" id="menuPrint" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										<span class="glyphicon glyphicon-print"></span> Imprimer
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu" aria-labelledby="menuPrint">
<?php
			foreach( $prints as $print ) {
				if( $print->visible ) {
					if( $print->separator ) {
?>
										<li role="separator" class="divider"></li>
<?php
					} else {
						$printLink = 'index.php?item='.$page->alias.'&action=print&id='.$id.'&type='.$print->alias;
?>
										<li><a class="btnReload" href="<?php echo $printLink; ?>" target="_blank"><?php echo $print->nicename; ?></a></li>
<?php
					}
				}
			}
?>
									</ul>
								</span>
<?php
		}
		
		// Boutons d'action
		if( count( $acts ) > 0 && !$new && $visibleActs ) {
			foreach( $acts as $act ) {
				if( $act->visible ) {
					$actionLink = 'index.php?item='.$page->alias.'&action=edit&id='.$id.'&act='.$act->alias;
?>
								<a class="btn btn-default btn-sm" href="<?php echo $actionLink; ?>" ><span class="glyphicon glyphicon-<?php echo $act->icon; ?>"></span> <?php echo $act->nicename; ?></a></li>
<?php
				}
			}
		}
		
		if( $parentLink && !$new && ( $userCan->admin or $userCan->create or $userCan->update ) ) {
?>
								<a href="index.php?item=<?php echo $page->alias; ?>&action=edit&parent=<?php echo $parentId; ?>" class="btn btn-default btn-sm">
									<span class="glyphicon glyphicon-plus"></span> Nouveau <?php echo $object->getSingle(); ?> pour cette <?php echo $parentItem; ?>
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
					$nbItems = ( count( $items ) > 0 && !$relation->static ) ? ' ('.count( $items ).')' : '';
					$classLink = 'btn btn-default btn-sm';
					if( !property_exists( $relation, 'many' ) ) {
						$addLink = 'href="index.php?item='.$relation->item.'&action=edit&parent='.$id.'"';
					} else {
						$addLink = 'data-rel-item="'.$relation->item.'" data-parent-item="'.$page->alias.'" data-parent-id="'.$id.'" data-toggle="modal" data-target="#relation-modal"';
						$classLink .= ' add-relation';
					}
?>
					<div class="col-sm-<?php echo $relation->grid; ?>">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<span class="panel-title"><?php echo $relation->name .$nbItems; ?></span>
							</div>
							<div class="panel-body">
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
							<div class="panel-footer">
								<a <?php echo $addLink; ?> class="<?php echo $classLink; ?>">
									<span class="glyphicon glyphicon-plus"></span> Ajouter
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
									<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
										<span class="glyphicon glyphicon-backward"></span> Retour
									</button>
									<button type="submit" class="btn btn-success btn-sm">
										<span class="glyphicon glyphicon-floppy-disk"></span> Sauvegarder
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
