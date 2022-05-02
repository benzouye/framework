<?php
	// Initialisation variables générales
	$logout = false;
	$register = false;
	$readOnly = false;
	$template = '';
	$emails = array();
	$emailsOrga = array();
	$sitItemOk = true;
	
	// Initialisation du manager global
	$manager = new Manager( $bdd, $debug );
	
	// Initialisation de la page affichée
	if( !isset( $_GET['item'] ) ) {
		$page = $manager->getItem( HOMEPAGE );
	} else {
		if( $_GET['item'] == 'logout' ) {
			// Déconnexion de session
			$logout = true;
			unset( $_SESSION[DBPREF.'_userId'] );
			unset( $_SESSION[DBPREF.'_item'] );
			unset( $_SESSION[DBPREF.'_search'] );
			unset( $_SESSION[DBPREF.'_page'] );
			unset( $_SESSION[DBPREF.'_action'] );
			unset( $_SESSION[DBPREF.'_orderby'] );
			$manager->setMessage( M_LOGOUT );
		}
		if( $_GET['item'] == 'register' ) {
			$register = true;
		}
	}
	
	// Initialisation utilisateur
	$user = $manager->getUser();
	$userCaps = $manager->getUserCan();
	
	// Initialisation de la page affichée
	if( isset( $_GET['item'] ) ) {
		if( $logout ) {
			$page = $manager->getItem( HOMEPAGE );
		} else {
			if( $manager->is_item( $_GET['item'] ) ) {
				$userCan = $manager->getUserCan( $_GET['item'] );
				if( isset( $_GET['id'] ) && $_GET['item'] == 'utilisateur' ) {
					if( $user->admin or $_GET['id'] == $user->id_utilisateur ) {
						$page = $manager->getItem( $_GET['item'] );
					} else {
						$manager->setMessage( M_ACCESSERR ,true);
						$page = $manager->getItem( HOMEPAGE );
					}
				} elseif ( $userCan->access > 0 or $register ) {
					$page = $manager->getItem( $_GET['item'] );
				} else {
					$manager->setMessage( M_ACCESSERR ,true);
					$page = $manager->getItem( HOMEPAGE );
				}
			} else {
				$manager->setMessage( M_DBITEMERR ,true);
				$page = $manager->getItem( HOMEPAGE );
			}
		}
	}
	
	$static = $manager->is_static( $page->alias );
	$variant = $manager->is_variant( $page->alias );
	$type = isset( $_GET['type'] ) ? $_GET['type'] : 'fiche';
	
	// Chargement et définition template
	$title = $manager->getOption('sitetitle').' | '.$page->nom;
	if( $static ) {
		// Pour une page statique
		$_SESSION[DBPREF.'_search'] = array();
		$_SESSION[DBPREF.'_item'] = $page->alias;
		$_SESSION[DBPREF.'_page'] = 1;
		$_SESSION[DBPREF.'_orderby'] = false;
		$action = 'list';
		if( file_exists( VIEWDIR.$page->alias.'.php' ) ) {
			$template = VIEWDIR.$page->alias.'.php';
		} else {
			$manager->setMessage( sprintf( M_VIEWERR, $page->alias ) ,true);
		}
	} else {
		// Traitement des actions propre à l'objet
		if( isset( $_GET['item'], $_GET['oa'], $_GET['oai'] ) ) {
			if( $userCan->admin or $userCan->update ) {
				$nomClasse = ucfirst($_GET['item']);
				$cibleAction = new $nomClasse( $bdd, $manager, ${'model_'.$_GET['item']} );
				if( method_exists( $cibleAction, $_GET['oa'] ) ) {
					$cibleAction->{$_GET['oa']}();
				} else {
					$manager->setMessage( sprintf( M_CLASSERR, htmlspecialchars($_GET['oa']) ) ,true);
				}
			} else {
				$manager->setMessage( M_ACCESSERR ,true);
			}
		}
		
		// Traitement création/mise à jour/suppression
		if( isset( $_POST['action'], $_POST['item'], $_POST['id'] ) ) {
			$nomClasse = ucfirst($_POST['item']);
			$cibleVariant = $manager->is_variant( $_POST['item'] );
			
			if( $cibleVariant ) {
				$cible = new $nomClasse( $bdd, $manager, ${'model_'.$_POST['item']} );
			} else {
				$cible = new Model( $bdd, $manager, ${'model_'.$_POST['item']} );
			}
			
			if( !isset( $_GET['copy'] ) ) {
				switch( $_POST['action'] ) {
					case 'set':
						if( $userCan->admin or $userCan->create or $userCan->update or ( $page->alias == 'utilisateur' && $_POST['id'] == $user->id_utilisateur ) ) {
							$setItemOk = $cible->setItem( $_POST );
						} else {
							$manager->setMessage( M_ACCESSERR ,true);
						}
						break;
					case 'delete':
						if( $userCan->admin or $userCan->delete ) {
							$cible->deleteItem( intval( $_POST['id'] ) );
						} else {
							$manager->setMessage( M_ACCESSERR ,true);
						}
						break;
					case 'rel-set':
						if( $userCan->admin or $userCan->create or $userCan->update ) {
							$cible = new $nomClasse( $bdd, $manager, ${'model_'.$_POST['item']} );
							if( isset( $_POST['relation'] ) ) $cible->{'set_'.$_POST['relation']}( $_POST );
							else $manager->setMessage( sprintf( M_RELSETERR, $_POST['item'], $_POST['relation'] ) ,true);
						} else {
							$manager->setMessage( M_ACCESSERR ,true);
						}
						break;
					case 'rel-del':
						if( $userCan->admin or $userCan->delete ) {
							$cible = new $nomClasse( $bdd, $manager, ${'model_'.$_POST['item']} );
							if( isset( $_POST['relation'], $_POST['rel_id'] ) ) $cible->{'del_'.$_POST['relation']}( $_POST );
							else $manager->setMessage( sprintf( M_RELDELERR, $_POST['item'], $_POST['relation'] ) ,true);
						} else {
							$manager->setMessage( M_ACCESSERR ,true);
						}
						break;
				}
			} else {
				// Duplication
				if( $userCan->admin or $userCan->create ) {
					// On sauvegarde la source
					$cible->setItem( $_POST );
					// On récuoère un nouvel ID
					$_POST['id_'.$cible->getItemName()] = $cible->getNextId();
					// On sauvegarde la copie
					$setItemOk = $cible->setItem( $_POST );
					// On affiche la copie
					if( $setItemOk ) header( 'Location: index.php?item='.$cible->getItemName().'&action=edit&id='.$_POST['id_'.$cible->getItemName()] ); 
				} else {
					$manager->setMessage( M_ACCESSERR ,true);
				}
			}
			$manager->setOptions();
			$manager->setItemAndMenus();
		}
		
		// Chargement de l'objet associé pour les pages non statiques
		if( $variant ) {
			$nomClasse = ucfirst($page->alias);
			$object = new $nomClasse( $bdd, $manager, ${'model_'.$page->alias} );
		} else {
			$object = new Model( $bdd, $manager, ${'model_'.$page->alias} );
		}
		
		// Chargement des colonnes
		$colonnes = $object->getColumns();
		
		// Chargement des relations
		$relations = $object->getRelations();
		
		// Chargement des filtres par défaut
		$defaultFilters = $object->getDefaultFilters();
		
		// Initialisation de l'action demandée
		if( !empty( $_GET['action'] ) ) {
			if( array_key_exists( $_GET['action'], $actions ) ) {
				$action = $_GET['action'];
				
				// Si retour à la liste purge de la session
				if( $_GET['action'] == 'list' ) {
					$_SESSION[DBPREF.'_search'] = array();
					$_SESSION[DBPREF.'_page'] = 1;
				}
			} else {
				$action = 'list';
				$manager->setMessage( M_DBITEMERR ,true);
			}
		} else {
			$action = 'list';
		}
		$_SESSION[DBPREF.'_action'] = $action;
		
		// Réinitialisation session si changement item
		if( $_SESSION[DBPREF.'_item'] != $page->alias ) {
			$_SESSION[DBPREF.'_search'] = $defaultFilters;
			$_SESSION[DBPREF.'_item'] = $page->alias;
			$_SESSION[DBPREF.'_page'] = 1;
			$_SESSION[DBPREF.'_orderby'] = false;
		}
		
		// Initialisation du numéro de page à afficher
		if( !empty( $_GET['p'] ) ) $_SESSION[DBPREF.'_page'] = intval( $_GET['p'] );
		
		if( !empty( $_POST['search'] ) ) {
			$_SESSION[DBPREF.'_search'] = $_POST;
			$_SESSION[DBPREF.'_page'] = 1;
		}
		
		$search = $_SESSION[DBPREF.'_search'];
		$p = $_SESSION[DBPREF.'_page'];
		
		// Traitement recherche et tri
		$criteres = '';
		$paginate = in_array( $_SESSION[DBPREF.'_action'], [ 'print', 'export' ] ) ? false : true;
		
		if( !empty( $_GET['orderby'] ) && !empty( $_GET['orderway'] ) ) {
			$_SESSION[DBPREF.'_orderby'] = '`'.$_GET['orderby'].'` '.$_GET['orderway'];
		}
		$orderby = $_SESSION[DBPREF.'_orderby'];
		
		// Utilisateur limité
		$ownerLimit = ( $userCan->all or $userCan->admin ) ? false : $user->id_utilisateur;
		
		if( count($search) > 0 ) {
			$items = $object->getItems( $search, $paginate, $p, $orderby, $ownerLimit );
			$criteres = '<span id="search-criteria" class="form-text">';
			$criteres .= $object->getSearchCriteria( $search );
			$criteres .= '</span>';
		} else {
			$items = $object->getItems( null, $paginate, $p, $orderby, $ownerLimit );
		}
		
		// Initialisation de l'id demandé
		$next = $object->getNextId();
		if( !$setItemOk ) unset( $_GET['id'] );
		$id = isset( $_GET['id'] ) ? intval($_GET['id']) : $next;
		$new = $id == $next;
		
		// Chargement de l'item hors création
		$item = $object->getItem( $id, $action );
		
		// Gestion readonly state
		foreach( $object->getReadOnlyStates() as $readOnlyState ) {
			if( property_exists( $item, $readOnlyState->column ) ) {
				if( in_array( $item->{$readOnlyState->column}, $readOnlyState->values ) ) {
					$readOnly = true;
				}
			}
		}
		
		// Chargement des impressions et actions
		$prints = array();
		$availablePrints = array();
		$objectActions = array();
		if( $variant ) {
			if( method_exists( $object, 'getPrints' ) ) {
				$prints = $object->getPrints();
				foreach( $prints as $print ) {
					if( $print->visible && ( $user->admin or !$print->admin ) ) {
						$availablePrints[] = $print;
					}
				}
			}
			if( method_exists( $object, 'getObjectActions' ) ) {
				$objectActions = $object->getObjectActions();
			}
		}
		
		// Définition de l'item parent
		$parentItem = $object->getParentItem();
		$parentId = isset( $_GET['parent'] ) ? intval($_GET['parent']) : $object->getParentId();
		$parentLink = $parentItem ? $parentItem.'&action=edit&id='.$parentId : false;
		$backlink = $page->alias .'&p='.$p;
		$savelink = $parentItem ? $page->alias.'&action=edit&id='.$id.'&parent='.$parentId : $page->alias.'&action=edit&id='.$id;
		$copylink = $page->alias.'&action=edit&copy=1&id='.$id;
		$dellink = $page->alias.'&action=delete&id='.$id;
		
		if( file_exists( TEMPLDIR.$page->alias.'.'.$action.'.php' ) ) {
			$template = TEMPLDIR.$page->alias.'.'.$action.'.php';
		} elseif( file_exists( TEMPLDIR.$action.'.php' ) ) {
			$template = TEMPLDIR.$action.'.php';
		} elseif( file_exists( VIEWDIR.HOMEPAGE.'.php' ) ) {
			$template = VIEWDIR.HOMEPAGE.'.php';
		} else {
			$template = false;
			$manager->setMessage( sprintf( M_TMPLERR, $page->alias.'.'.$action ) ,true);
		}
		$subTitle = ( $new && $action == 'edit' ) ? 'Création' : $actions[$action];
		$title .= ' | '.$subTitle;
	}
	
