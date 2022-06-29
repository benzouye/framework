<?php
	if( $userCan->admin or $userCan->create or $userCan->read or $userCan->update or $userCan->delete or ( $page->alias == 'utilisateur' && $item->{'id_'.$page->alias} == $user->id_utilisateur ) ) {
		// Créations des onglets
?>
					<nav class="nav nav-tabs">
						<button class="nav-link active" id="nav-general-tab" data-bs-toggle="tab" data-bs-target="#nav-general">Général</button>
<?php
		// Onglets relations
		if( !$new && count($relations) > 0 ) {
			foreach( $relations as $relation ) {
				$nbItems = '';
				$toDisplay = true;
				if( property_exists( $relation, 'displayCondition' ) && method_exists( $object, 'get_display_condition' ) ) {
					$toDisplay = $object->get_display_condition();
				}
				
				$standardRelation = true;
				if( property_exists( $relation, 'standard' ) ) {
					$standardRelation = $relation->standard;
				}
				
				if( $toDisplay ) {
					if( method_exists( $object, 'get_'.$relation->item ) ) {
						$items = $object->{'get_'.$relation->item}();
						$nbItems = ( count( $items ) > 0 && !$relation->static ) ? ' <span class="badge bg-secondary text-light">'.count( $items ).'</span>' : '';
					}
				}
?>
						<button class="nav-link" id="nav-<?= $relation->item;?>-tab" data-bs-toggle="tab" data-bs-target="#nav-<?= $relation->item;?>"><?= $relation->name;?><?= $nbItems; ?></button>
<?php
			}
		}
		// Onglet historique
		if( !$new && $userCan->admin ) {
			$historiques = $object->getHistorique();
			$nbHistoriques = count( $historiques );
			if( $nbHistoriques > 0 ) {
?>
						<button class="nav-link" id="nav-histo-tab" data-bs-toggle="tab" data-bs-target="#nav-histo">Historique <span class="badge bg-secondary text-light" ><?=$nbHistoriques; ?></span></button>
<?php
			}
		}
?>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane show active" id="nav-general" role="tabpanel">
							<div class="card">
								<div class="card-header tab-card-header">
									<div class="d-flex">
<?php
		// Boutons d'action
			if( !$new ) {
				foreach( $objectActions as $objectAction ) {
					if( $objectAction->editable and ( ( !$readOnly && !$objectAction->admin ) or $userCan->admin ) ) {
?>
									<?= $object->displayObjectAction( $page->alias, $objectAction->alias, $id, 'edit' ); ?>
<?php
					}
				}
			}
			// Bouton suppression
			if( ( $userCan->admin or $userCan->delete ) and !$parentItem and ( !$readOnly or $userCan->admin ) ) {
?>
									<div class="ms-auto">
										<form class="delete" method="post" action="index.php?item=<?=$page->alias;?>">
											<input type="hidden" name="id" value="<?=$item->{'id_'.$page->alias}; ?>" />
											<input type="hidden" name="item" value="<?=$page->alias; ?>" />
											<button title="Supprimer" data-bs-toggle="tooltip" data-bs-placement="bottom" id="item-delete" type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i><span class="d-none d-xl-inline"> Supprimer</span></button>
										</form>
									</div>
<?php
			}
?>
									</div>
								</div>
								<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=<?=$savelink; ?>">
									<input type="hidden" name="action" value="set"/>
									<input type="hidden" name="id" value="<?=$id; ?>"/>
									<input type="hidden" name="item" value="<?=$page->alias; ?>"/>
									<div class="card-body row" data-masonry='{"percentPosition": true }'>
<?php
		// Affichage du formulaire
		foreach( $colonnes as $colonne ) {
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
			}
			
			if( $userCan->admin or !$adminInput ) {
				if( $readOnly && !$userCan->admin ) {
?>
										<?= $object->displayField( $colonne->name, $valeur, true ); ?>

<?php
				} else {
?>
										<?= $object->displayInput( $id, $colonne->name, $valeur ); ?>

<?php
				}
			}
		}
		// Affichage des boutons
?>
									</div>
									<div class="card-footer">
<?php
		if( ( $userCan->admin or $userCan->create or $userCan->update or ( $page->alias == 'utilisateur' && $item->{'id_'.$page->alias} == $user->id_utilisateur ) ) and ( !$readOnly or $userCan->admin ) ) {
?>
										<button title="<?=$new ? 'Créer' : 'Sauvegarder'; ?>" data-bs-toggle="tooltip" data-bs-placement="top" name="form-submit" type="submit" class="btn btn-success btn-sm navbar-btn">
											<i class="bi bi-save"></i><span class="d-none d-xl-inline"> <?=$new ? 'Créer' : 'Sauvegarder'; ?></span>
										</button>
<?php
			// Bouton création
			if( ( $userCan->admin or $userCan->create ) and !$parentItem and !$new ) {
?>
										<button title="Dupliquer" data-bs-toggle="tooltip" data-bs-placement="top" name="form-submit" formaction="index.php?item=<?=$copylink; ?>" type="submit" class="ms-1 btn btn-secondary btn-sm navbar-btn float-end">
											<i class="bi bi-box-arrow-up-right"></i><span class="d-none d-xl-inline"> Dupliquer</span>
										</button>
										<a title="Ajouter" data-bs-toggle="tooltip" data-bs-placement="bottom" href="index.php?item=<?=$page->alias; ?>&action=edit" class="ms-1 btn btn-secondary btn-sm float-end">
											<i class="bi bi-plus-lg"></i><span class="d-none d-xl-inline"> Nouveau</span>
										</a>
<?php
			}
		}
		if( $page->alias == 'utilisateur' && !$user->admin ) {
?>
										<a title="Retour Accueil" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php" class="btn btn-secondary btn-sm">
											<i class="bi bi-house"></i><span class="d-none d-xl-inline"> Retour accueil</span>
										</a>
<?php
		} else {
?>
										<a title="Retour liste <?=$object->getPlural(); ?>" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=<?=$backlink; ?>" class="btn btn-secondary btn-sm">
											<i class="bi bi-list-task"></i><span class="d-none d-xl-inline"> Retour liste <?=$object->getPlural(); ?></span>
										</a>
<?php
		}
		if( $parentLink ) {
?>
										<a title="Retour <?=$parentItem; ?>" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=<?=$parentLink; ?>" class="btn btn-secondary btn-sm">
											<i class="bi bi-caret-left-fill"></i><span class="d-none d-xl-inline"> Retour <?=$parentItem; ?></span>
										</a>
<?php
		}
		
		// Boutons d'impression
		if( !$new && count( $availablePrints ) ) {
?>
										<button class="btn btn-secondary btn-sm navbar-btn dropdown-toggle" type="button" id="menuPrint" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
											<span title="Imprimer" data-bs-toggle="tooltip" data-bs-placement="top" class="bi bi-printer"></span><span class="d-none d-xl-inline"> Imprimer</span>
										</button>
										<div class="dropdown-menu" aria-labelledby="menuPrint">
<?php
			foreach( $availablePrints as $print ) {
				if( $print->visible ) {
					if( $print->separator ) {
?>
											<div class="dropdown-divider"></div>
<?php
					} else {
						$printLink = 'index.php?item='.$page->alias.'&action=print&id='.$id.'&type='.$print->alias;
?>
											<a class="dropdown-item btnReload" href="<?=$printLink; ?>" target="_blank"><?=$print->nicename; ?></a>
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
										<a title="Ajout <?=$object->getSingle(); ?> sur même <?=$parentItem; ?>" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=<?=$page->alias; ?>&action=edit&parent=<?=$parentId; ?>" class="ms-1 btn btn-secondary btn-sm">
											<span class="bi bi-plus-square-dotted"></span><span class="d-none d-xl-inline">  Ajout <?=$object->getSingle(); ?> sur même <?=$parentItem; ?></span>
										</a>
<?php
		}
?>
									</div>
								</form>
							</div>
						</div>
<?php
		// Affichage des relations pour les variants en modification
		if( !$new && count($relations) > 0 ) {
			foreach( $relations as $relation ) {
				
				$toDisplay = true;
				if( property_exists( $relation, 'displayCondition' ) && method_exists( $object, 'get_display_condition' ) ) {
					$toDisplay = $object->get_display_condition();
				}
				
				$standardRelation = true;
				if( property_exists( $relation, 'standard' ) ) {
					$standardRelation = $relation->standard;
				}
				
				if( $toDisplay ) {
					if( method_exists( $object, 'get_'.$relation->item ) ) {
						$items = $object->{'get_'.$relation->item}();
						$classLink = 'btn btn-secondary btn-sm';
						if( !property_exists( $relation, 'many' ) ) {
							$addLink = 'href="index.php?item='.$relation->item.'&action=edit&parent='.$id.'"';
						} else {
							$addLink = 'href="#" data-rel-item="'.$relation->item.'" data-parent-item="'.$page->alias.'" data-parent-id="'.$id.'" data-bs-toggle="modal" data-bs-target="#relation-modal"';
							$classLink .= ' add-relation';
						}
?>
						<div class="tab-pane" id="nav-<?=$relation->item; ?>" role="tabpanel">
							<div class="card">
								<div class="card-body">
<?php
						if( count( $items ) > 0 or !$standardRelation ) {
							if( file_exists( TEMPLDIR.$action.'.'.$page->alias.'.'.$relation->item.'.php' ) )
								require_once( TEMPLDIR.$action.'.'.$page->alias.'.'.$relation->item.'.php' );
							else
								$manager->setMessage( sprintf( M_TMPLERR, $action.'.'.$page->alias.'.'.$relation->item ) ,true);
						} else {
?>
									<p>Aucun élément ...</p>
<?php
						}
						
						if( $standardRelation ) {
?>
								</div>
<?php
						}
						
						if( !$relation->static && ( $userCan->admin or $userCan->create or $userCan->update ) and ( !$readOnly or $userCan->admin ) ) {
?>
								<div class="card-footer">
									<a <?=$addLink; ?> class="<?=$classLink; ?>">
										<span class="bi bi-plus-square-dotted"></span>
										<span class="d-none d-xl-inline"> Ajouter</span>
									</a>
								</div>
<?php
						}
?>
							</div>
						</div>
<?php
					} else {
						$manager->setMessage( sprintf( M_CLASSERR, $relation->name ) ,true);
					}
				}
			}
?>
						<div id="relation-modal" class="modal fade" tabindex="-1" role="dialog">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<form method="POST" action="index.php?item=<?=$savelink; ?>">
										<div class="modal-header">
											<h5 class="modal-title">Choisissez les éléments à ajouter</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
										</div>
										<div class="modal-body">
											<ul id="relation-ul" class="list-group"></ul>
										</div>
										<div class="modal-footer">
											<button title="Retour" data-bs-toggle="tooltip" data-bs-placement="top" type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
												<i class="bi bi-caret-left"></i><span class="d-none d-xl-inline"> Retour</span>
											</button>
											<button title="Sauvegarder" data-bs-toggle="tooltip" data-bs-placement="top" type="submit" class="btn btn-success btn-sm">
												<i class="bi bi-save"></i><span class="d-none d-xl-inline"> Sauvegarder</span>
											</button>
										</div>
									</form>
								</div>
							</div>
						</div>
<?php
		}
		
		// Affichage de l'historique
		if( !$new && $userCan->admin ) {
			$historiques = $object->getHistorique();
			$nbHistoriques = count( $historiques );
?>
						<div class="tab-pane" id="nav-histo" role="tabpanel">
							<div class="card">
								<div class="card-body">
<?php
			if( $nbHistoriques > 0 ) {
?>
									<table class="table table-sm table-striped table-hover table-bordered table-responsive">
										<thead>
											<tr>
												<th>Date</th>
												<th>Utilisateur</th>
												<th>Détails</th>
											</tr>
										</thead>
										<tbody>
<?php
				foreach( $historiques as $historique ) {
?>
											<tr>
												<td><?=$historique->date_cre; ?></td>
												<td><?=$historique->identifiant; ?></td>
<?php
					$actions = json_decode( $historique->action );
					$details = '<ul>';
					foreach( $actions as $nom => $valeur ) {
						$details .= '<li><em>'.$nom.'</em> = <strong>'.$valeur.'</strong></li>';
					}
					$details .= '</ul>';
?>
												<td><?=$details; ?></td>
											</tr>
<?php
				}
?>
										</tbody>
									</table>
<?php
			} else {
?>
									<p>Aucun élément ...</p>
<?php
			}
?>
								</div>
							</div>
						</div>
<?php
		}
?>
					</div>
<?php
	}
