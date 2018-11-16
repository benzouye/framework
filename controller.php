<?php
	// Initialisation variables générales
	$logout = false;
	$register = false;
	$readOnly = false;
	$template = '';
	$emails = array();
	$emailsOrga = array();
	
	// Initialisation du manager global
	$manager = new Manager( $bdd, $debug );
	
	// Initialisation de la page affichée
	if( !isset( $_GET['item'] ) ) {
		$page = $manager->getItem( HOMEPAGE );
	} else {
		if( $_GET['item'] == 'logout' ) {
			// Déconnexion de session
			$logout = true;
			session_unset();
			session_destroy();
			$manager->setMessage( M_LOGOUT );
		}
		if( $_GET['item'] == 'register' ) {
			$register = true;
		}
	}
	
	// Initialisation utilisateur
	$user = $manager->getUser();
	
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
						$manager->setError( M_ACCESSERR );
						$page = $manager->getItem( HOMEPAGE );
					}
				} elseif ( $userCan->access > 0 or $register ) {
					$page = $manager->getItem( $_GET['item'] );
				} else {
					$manager->setError( M_ACCESSERR );
					$page = $manager->getItem( HOMEPAGE );
				}
			} else {
				$manager->setError( M_DBITEMERR );
				$page = $manager->getItem( HOMEPAGE );
			}
		}
	}
	
	$static = $manager->is_static( $page->alias );
	$variant = $manager->is_variant( $page->alias );
	$type = isset( $_GET['type'] ) ? $_GET['type'] : 'fiche';
	
	// Chargement et définition template
	$title = $manager->getOption('sitetitle');
	if( $static ) {
		// Pour une page statique
		$_SESSION[DBPREF.'_search'] = array();
		$_SESSION[DBPREF.'_item'] = $page->alias;
		$_SESSION[DBPREF.'_page'] = 1;
		$action = 'list';
		$title = $page->nom;
		if( file_exists( VIEWDIR.$page->alias.'.php' ) ) {
			$template = VIEWDIR.$page->alias.'.php';
		} else {
			$manager->setError( sprintf( M_VIEWERR, $page->alias ) );
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
					$manager->setError( sprintf( M_CLASSERR, htmlspecialchars($_GET['oa']) ) );
				}
			} else {
				$manager->setError( M_ACCESSERR );
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
							$cible->setItem( $_POST );
						} else {
							$manager->setError( M_ACCESSERR );
						}
						break;
					case 'delete':
						if( $userCan->admin or $userCan->delete ) {
							$cible->deleteItem( intval( $_POST['id'] ) );
						} else {
							$manager->setError( M_ACCESSERR );
						}
						break;
					case 'rel-set':
						if( $userCan->admin or $userCan->create or $userCan->update ) {
							$cible = new $nomClasse( $bdd, $manager, ${'model_'.$_POST['item']} );
							if( isset( $_POST['relation'] ) ) $cible->{'set_'.$_POST['relation']}( $_POST );
							else $manager->setError( sprintf( M_RELSETERR, $_POST['item'], $_POST['relation'] ) );
						} else {
							$manager->setError( M_ACCESSERR );
						}
						break;
					case 'rel-del':
						if( $userCan->admin or $userCan->delete ) {
							$cible = new $nomClasse( $bdd, $manager, ${'model_'.$_POST['item']} );
							if( isset( $_POST['relation'], $_POST['rel_id'] ) ) $cible->{'del_'.$_POST['relation']}( $_POST );
							else $manager->setError( sprintf( M_RELDELERR, $_POST['item'], $_POST['relation'] ) );
						} else {
							$manager->setError( M_ACCESSERR );
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
					$cible->setItem( $_POST );
					// On affiche la copie
					header( 'Location: index.php?item='.$cible->getItemName().'&action=edit&id='.$_POST['id_'.$cible->getItemName()] ); 
				} else {
					$manager->setError( M_ACCESSERR );
				}
			}
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
		if( isset( $_GET['action'] ) ) {
			if( array_key_exists( $_GET['action'], $actions ) ) {
				$action = $_GET['action'];
				
				// Si retour à la liste purge de la session
				if( $_GET['action'] == 'list' ) {
					$_SESSION[DBPREF.'_search'] = array();
					$_SESSION[DBPREF.'_page'] = 1;
				}
			} else {
				$action = 'list';
				$manager->setError( M_DBITEMERR );
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
		}
		
		// Initialisation du numéro de page à afficher
		if( isset( $_GET['p'] ) ) $_SESSION[DBPREF.'_page'] = intval( $_GET['p'] );
		
		if( isset( $_POST['search'] ) ) {
			$_SESSION[DBPREF.'_search'] = $_POST;
			$_SESSION[DBPREF.'_page'] = 1;
		}
		
		$search = $_SESSION[DBPREF.'_search'];
		$p = $_SESSION[DBPREF.'_page'];
		
		// Traitement recherche
		$criteres = '';
		$paginate = $_SESSION[DBPREF.'_action'] == 'print' ? false : true;
		if( count($search) > 0 ) {
			$items = $object->getItems( $search, $paginate, $p );
			$criteres = '<span class="search">';
			$criteres .= $object->getSearchCriteria( $search );
			$criteres .= '</span>';
		} else {
			$items = $object->getItems( null, $paginate, $p );
		}
		$manager->refreshData();
		
		// Initialisation de l'id demandé
		$next = $object->getNextId();
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
		$visiblePrints = false;
		$prints = array();
		$visibleObjectActions = false;
		$objectActions = array();
		if( $variant ) {
			if( method_exists( $object, 'getPrints' ) ) {
				$prints = $object->getPrints();
				foreach( $prints as $print ) {
					if( $print->visible )
						$visiblePrints = true;
				}
			}
			if( method_exists( $object, 'getObjectActions' ) ) {
				$objectActions = $object->getObjectActions();
				foreach( $objectActions as $objectAction ) {
					if( $objectAction->visible )
						$visibleObjectActions = true;
				}
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
			$manager->setError( sprintf( M_TMPLERR, $page->alias.'.'.$action ) );
		}
		$subTitle = ( $new && $action == 'edit' ) ? 'Création' : $actions[$action];
		$title = $page->nom.' | '.$subTitle;
	}
?>
