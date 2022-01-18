<?php
class Model {
	protected $bdd;
	protected $manager;
	protected $table;
	protected $selectLabel;
	protected $etatsColors;
	protected $itemName;
	protected $parentItem;
	protected $parentId = false;
	protected $single;
	protected $plural;
	protected $columns;
	protected $fileExtensions = [ 'png', 'jpg', 'jpeg', 'gif', 'bmp', 'pdf', 'doc', 'xls', 'ppt', 'odt', 'ods', 'odp' ];
	protected $ignoredParams = ['item','columnKey','columnLabel','where','extensions', 'value'];
	protected $disabledExceptions = ['select','checkbox','calculation'];
	protected $defaultFilters;
	protected $objectActions;
	protected $prints;
	protected $orderby;
	protected $order;
	protected $items;
	protected $nbItems;
	protected $currentItem = false;
	protected $searchSep = ' / ';
	protected $foreignColumns;
	protected $relations;
	protected $id;
	protected $lastId;
	protected $nextId;
	protected $grille;
	
	public function __construct( PDO $bdd, Manager $manager, stdClass $model ) {
		$this->bdd = $bdd;
		$this->manager = $manager;
		$this->itemName = $model->itemName;
		$this->table = $model->table;
		$this->single = $model->single;
		$this->plural = $model->plural;
		$this->columns = $model->columns;
		$this->orderby = $model->orderby;
		$this->grille = (object) array( 'div' => 6, 'label' => 4, 'value' => 8 );
		
		// Clés étrangères
		$this->foreignColumns = array();
		foreach( $this->columns as $colonne ) {
			if( $colonne->params['type'] == 'select' ) {
				global ${'model_'.$colonne->params['item']};
				$this->foreignColumns[$colonne->params['item']] = new Model( $this->bdd, $this->manager, ${'model_'.$colonne->params['item']} );
			}
		}
		
		// Next ID
		try {
			$requete = $this->bdd->query('
				SELECT AUTO_INCREMENT
				FROM INFORMATION_SCHEMA.TABLES
				WHERE
					TABLE_NAME = "'.$this->table.'"
					AND TABLE_SCHEMA = "'.DBNAME.'"'
			);
			$this->nextId = $requete->fetchColumn();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = $this->single;
			}
			$this->manager->setMessage( sprintf( M_IDERR, $msg ) ,true);
		}
		
		// Color schemes
		try {
			$requete = $this->bdd->query('
				SELECT E.id_etat, S.libelle, S.dark
				FROM '.DBPREF.'etat E
					INNER JOIN '.DBPREF.'colorscheme S
						ON E.id_colorscheme = S.id_colorscheme;'
			);
			$this->etatsColors = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'etats colorschemes';
			}
			$this->manager->setMessage( sprintf( M_IDERR, $msg ) ,true);
		}
		
		// Select Label
		$this->selectLabel = false;
		if( property_exists( $model, 'selectLabel' ) ) {
			$this->selectLabel = $model->selectLabel;
		}
		
		// ReadOnly
		$this->readOnlyStates = array();
		if( property_exists( $model, 'readOnlyStates' ) ) {
			$this->readOnlyStates = $model->readOnlyStates;
		}
		
		// Default filters
		$this->defaultFilters = array();
		if( property_exists( $model, 'defaultFilters' ) ) {
			$this->defaultFilters = $model->defaultFilters;
		}
		
		// Actions
		$this->objectActions = array();
		if( property_exists( $model, 'objectActions' ) ) {
			$this->objectActions = $model->objectActions;
		}
		
		// Impressions
		$this->prints = array();
		if( property_exists( $model, 'prints' ) ) {
			$this->prints = $model->prints;
		}
		
		// Relations
		$this->relations = array();
		if( property_exists( $model, 'relations' ) ) {
			$this->relations = $model->relations;
		}
		
		// Parent
		$this->parentItem = false;
		if( property_exists( $model, 'parentItem' ) ) {
			$this->parentItem = $model->parentItem;
		}
	}
	
	public function getNextId() {
		return $this->nextId;
	}
	
	public function getParentId() {
		return $this->parentId;
	}
	
	public function getNbItems() {
		return $this->nbItems;
	}
	
	public function getColumns() {
		return $this->columns;
	}
	
	public function getColumn( $name ) {
		$retour = new stdClass();
		foreach( $this->columns as $colonne ) {
			if( $colonne->name == $name ) {
				$retour = $colonne;
				break;
			}
		}
		return $retour;
	}
	
	public function getDistinctValues( $colonne, $valeur ) {
		try {
			$test = false;
			$retour = array();
			foreach( $this->columns as $source ) {
				if( $source->name == $colonne ) {
					$test = true;
					break;
				}
			}
			
			if( $test ) {
				$requete = $this->bdd->prepare( '
					SELECT DISTINCT '.$colonne.' AS value
					FROM '.$this->table.'
					WHERE '.$colonne.' LIKE ?
					ORDER BY '.$colonne
				);
				$requete->execute( array( '%'.$valeur.'%' ) );
				$retour = $requete->fetchAll();
			}
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = $this->plural;
			}
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
		}
		finally {
			return $retour;
		}
	}
	
	public function getItems( array $search = null, $paginate = false, $page = 1, $orderby = false, $ownerLimit ) {
		$nbparpage = $this->manager->getOption('nbparpage');
		$nbparpage = $nbparpage < 1 ? 140000000 : $nbparpage;
		$page = $page-1;
		
		try {
			$select = '';
			$where = 'WHERE 1=1 ';
			$join = '';
			$groupby = '';
			$limit = '';
			$isCalculated = false;
			
			if( $ownerLimit ) {
				$where .= ' AND T.user_cre = '.$ownerLimit;
			}
			
			if( $paginate ) $limit = 'LIMIT '.$page*$nbparpage.', '.$nbparpage;
			
			if( $search ) $where .= $this->getSearchCriteria( $search, true );
			
			foreach( $this->columns as $colonne ) {
				if( $colonne->name == 'id_affectation' ) {
					$where .= ' AND T.id_affectation ';
					$affectations = $this->manager->getAffectations();
					$tabAffectations = array();
					foreach( $affectations as $affectation ) {
						array_push( $tabAffectations, $affectation->id_affectation );
					}
					$where .= count( $tabAffectations ) ? ' IN ( '.implode( ",", $tabAffectations ).' ) ' : ' = 0 ';
				}
				if( $colonne->params['type'] == 'calculation' ) {
					$groupby = 'GROUP BY T.id_'.$this->itemName;
					$join = $colonne->params['join'];
					$select .= $colonne->params['function'].' AS '.$colonne->name.',';
					$isCalculated = true;
				} else {
					$select .= 'T.'.$colonne->name.',';
				}
			}
			$select = rtrim( $select, ',' );
			
			$requete = $this->bdd->query('
				SELECT COUNT(*)
				FROM '.$this->table.' T
				'.$where.';'
			);
			$this->nbItems = $requete->fetchColumn();
			$requete->closeCursor();
			
			$requete = $this->bdd->query('
				SELECT '.$select.'
				'.( $this->selectLabel ? ','.$this->selectLabel : '' ).'
				FROM '.$this->table.' T
				'.$join.'
				'.$where.'
				'.$groupby.'
				ORDER BY '.( $orderby ? $orderby : $this->orderby ).'
				'.$limit.';'
			);
			$this->items = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = $this->plural;
			}
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
			$this->items = array();
			$this->nbItems = 0;
		}
		finally {
			return $this->items;
		}
	}
	
	public function getRelatedItems( $id_related ) {
		try {
			$select = '';
			$where = 'WHERE '.$this->columns[0]->name.' = '.intval($id_related);
			
			foreach( $this->columns as $colonne ) {
				$select .= $colonne->name.',';
			}
			$select = rtrim( $select, ',' );
			
			$requete = $this->bdd->query('
				SELECT '.$select.'
				FROM '.$this->table.'
				'.$where.'
				ORDER BY '.$this->orderby.';'
			);
			$this->items = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = $this->plural;
			}
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
			$this->items = array();
		}
		finally {
			return $this->items;
		}
	}
	
	public function getParentItem() {
		return $this->parentItem;
	}
	
	public function getItem( $id, $action = 'list' ) {
		$select = '';
		$join = '';
		$groupby = '';
		
		foreach( $this->columns as $colonne ) {
			if( $colonne->params['type'] == 'calculation' ) {
				$groupby = 'GROUP BY T.id_'.$this->itemName;
				$join = $colonne->params['join'];
				$select .= ', '.$colonne->params['function'].' AS '.$colonne->name;
				break;
			}
		}
		
		if( $id == $this->nextId && $action == 'edit' ) {
			// Réservation next ID
			try {
				$requete = $this->bdd->query('
					ALTER TABLE '.$this->table.'
					AUTO_INCREMENT = '.($this->nextId + 1)
				);
				$requete->closeCursor();
			}
			catch( Exception $e ) {
				if( $this->manager->getDebug() ) {
					$msg = $e->getMessage();
				} else {
					$msg = $this->single;
				}
				$this->manager->setMessage( sprintf( M_UPIDERR, $msg ) ,true);
			}
		}
		
		try {
			$requete = $this->bdd->prepare('
				SELECT T.* '.$select.'
				FROM '.$this->table.' T
				'.$join.'
				WHERE T.id_'.$this->itemName.' = ?
				'.$groupby.';'
			);
			$requete->execute(array($id));
			$this->currentItem = $requete->fetch();
			$requete->closeCursor();
			
			if( $this->currentItem ) {
				if( property_exists( $this->currentItem, 'id_'.$this->itemName ) )
					$this->id = $this->currentItem->{'id_'.$this->itemName};
				
				if( $this->parentItem ) {
					if( property_exists( $this->currentItem, 'id_'.$this->parentItem ) ) {
						$this->parentId = $this->currentItem->{'id_'.$this->parentItem};
					}
				}
			} else {
				$this->currentItem = new stdClass();
			}
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = $this->single;
			}
			$this->manager->setMessage( sprintf( M_ITEMERR, $msg ) ,true);
			$this->currentItem = new stdClass();
		}
		finally {
			return $this->currentItem;
		}
	}
	
	public function getDefaultFilters() {
		return $this->defaultFilters;
	}
	
	public function getReadOnlyStates() {
		return $this->readOnlyStates;
	}
	
	public function getPrints() {
		return $this->prints;
	}
	
	public function getObjectActions() {
		return $this->objectActions;
	}
	
	public function displayObjectAction( $page, $alias, $id, $action ) {
		$display = '';
		$cible = $this->getItem( $id );
		$requestedAction = false;
		
		foreach( $this->objectActions as $objectAction ) {
			if( $objectAction->alias == $alias ) {
				$requestedAction = $objectAction;
				break;
			}
		}
		
		if( $requestedAction ) {
			if( $action == 'edit' && $requestedAction->editable ) {
				$actionLink = 'index.php?item='.$page.'&action=edit&id='.$id.'&oa='.$requestedAction->alias.'&oai='.$id;
				$display = '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="'.$requestedAction->nicename.'" class="btn btn-'.$requestedAction->color.' btn-sm float-end" href="'.$actionLink.'" ><i class="bi bi-'.$requestedAction->icon.'"></i><span class="d-none d-xl-inline"> '.$requestedAction->nicename.'</span></a>';
			}
			if( $action == 'list' && $requestedAction->listable ) {
				$actionLink = 'index.php?item='.$page.'&oa='.$requestedAction->alias.'&oai='.$id;
				$display = '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="'.$requestedAction->nicename.'" class="btn btn-'.$requestedAction->color.' btn-sm" href="'.$actionLink.'" ><i class="bi bi-'.$requestedAction->icon.'"></i></a>';
			}
		}
		return $display;
	}
	
	public function getRelations() {
		return $this->relations;
	}
	
	public function getItemName() {
		return $this->itemName;
	}
	
	public function getSingle() {
		return $this->single;
	}
	
	public function getPlural() {
		return $this->plural;
	}
	
	public function setItem( $data, $register = false ) {
		$this->id = $register ? $this->nextId : $data['id_'.$this->itemName];
		$columns = '';
		$values = '';
		$update = '';
		foreach( $this->columns as $colonne ) {
			
			// Traitement upload des fichiers
			if( in_array( $colonne->params['type'], ['image','file'] ) && isset( $_FILES[$colonne->name] ) ) {
				
				$handle = new Verot\Upload\Upload( $_FILES[$colonne->name] );
				$data[$colonne->name] = null;
				$extension = $handle->file_src_name_ext;
				$nomFichier = $this->itemName.'_'.$this->id.'_'.$colonne->name.'_'.uniqid();
				
				if( $handle->uploaded ) {
					if( isset( $colonne->params['extensions'] ) ) {
						$extensions = $colonne->params['extensions'];
					} else {
						$extensions = $this->fileExtensions;
					}
					if( in_array( $extension, $extensions ) ) {
						$handle->file_new_name_body = $nomFichier;
						
						if( $colonne->params['type'] == ['image'] ) {
							$handle->image_resize = true;
							$handle->image_x = DFWIDTH;
							$handle->image_ratio_y = true;
						}
						
						$handle->process( UPLDIR );
						
						if ($handle->processed) {
							$data[$colonne->name] = $nomFichier.'.'.$extension;
							$handle->clean();
						} else {
							$this->manager->setMessage( $handle->error ,true);
						}
					} else {
						$this->manager->setMessage( 'Fichier joint non enregistré car son extension ( '.$extension.' ) n\'est pas autorisée' ,true);
					}
				}
			}
			
			$flag = false;
			
			foreach( $data as $key => $value ) {
				if( $colonne->name == $key ) {
					if( ( $colonne->params['type'] == 'select' or $colonne->params['type'] == 'date' ) && $colonne->required == false && !$value ) {
						break;
					}
					if( $colonne->params['type'] == 'date' ) {
						$value = date( DBDATE, strtotime(str_replace('/','-',$value)) );
					}
					if( $colonne->params['type'] == 'password' ) {
						$value = password_hash( $value, PASSWORD_BCRYPT );
					}
					$columns .= $key.',';
					$values .= $this->bdd->quote( $value ).',';
					$update .= $colonne->name.' = '.$this->bdd->quote( $value ).',';
					$flag = true;
					break;
				}
			}
			if( $colonne->params['type'] == 'select' && $colonne->required == false && !$value ) {
				$columns .= $colonne->name.',';
				$values .= 'NULL,';
				$update .= $colonne->name.' = NULL,';
			}
			if( $colonne->params['type'] == 'checkbox' && !$flag && $colonne->editable ) {
				$columns .= $colonne->name.',';
				$values .= '0,';
				$update .= $colonne->name.' = 0,';
			}
		}
		$idUser = $register ? 1 : $this->manager->getUser()->id_utilisateur;
		$columns = $columns.' date_cre, user_cre';
		$values = $values.' NOW(), '.$idUser;
		$update = $update.' date_maj = NOW(), user_maj = '.$idUser;
		
		try {
			if( $register ) {
				$requete = $this->bdd->query( '
					INSERT INTO '.$this->table.' ( '.$columns.' )
					VALUES( '.$values.' );'
				);
			} else {
				$requete = $this->bdd->query( '
					INSERT INTO '.$this->table.' ( '.$columns.' )
					VALUES( '.$values.' )
					ON DUPLICATE KEY UPDATE '.$update.';' 
				);
			}
			$requete->closeCursor();
			
			$this->setHistorique();
			
			$this->manager->setMessage( sprintf( M_ITEMSET, $this->single ) );
		}
		catch( Exception $e ) {
			if( $e->getCode() == 23000 ) {
				$this->manager->setMessage( sprintf( M_ITEMSETKEYERR, $this->single, 'identifiant unique' ) ,true);
			} else {
				$this->manager->setMessage( sprintf( M_ITEMSETERR, $this->single, $id ) ,true);
			}
		}
	}
	
	public function deleteItem( $id ) {
		try {
			$this->getItem( $id );
			$requete = $this->bdd->query('
				DELETE
				FROM '.$this->table.'
				WHERE id_'.$this->itemName.' = '.$id.';'
			);
			$requete->closeCursor();
			
			$this->setHistorique();
			
			$this->manager->setMessage( 'L\'élément <em>'.$this->single.'</em> ID = '.$id.' a bien été supprimé.' );
			
			return true;
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = $this->single;
			}
			if( $e->getCode() == 23000 ) {
				$this->manager->setMessage( sprintf( M_ITEMDELKEYERR, $msg, $id ) ,true);
			} else {
				$this->manager->setMessage( sprintf( M_ITEMDELERR, $msg, $id ) ,true);
			}
			
			return false;
		}
	}
	
	private function setHistorique() {
		try {
			$requete = $this->bdd->prepare( '
				INSERT INTO '.DBPREF.'historique ( user_cre, date_cre, item, item_id, action )
				VALUES( :user_cre, NOW(), :item, :item_id, :action );'
			);
			$requete->execute( array(
				'user_cre' =>  $this->manager->getUser()->id_utilisateur ? $this->manager->getUser()->id_utilisateur : 1,
				'item' => $this->itemName,
				'item_id' => $this->id,
				'action' => json_encode( $_POST )
			));
		}
		catch( Exception $e ) {
			$this->manager->setMessage( 'Erreur lors de la création de l\'historique' ,true);
		}
		$requete->closeCursor();
	}
	
	public function getHistorique() {
		$historique = false;
		try {
			$requete = $this->bdd->query( '
				SELECT
					H.user_cre,
					U.identifiant,
					DATE_FORMAT( H.date_cre, "%d/%m/%Y à %H:%i:%s" ) AS date_cre,
					action
				FROM
					'.DBPREF.'historique H
						INNER JOIN '.DBPREF.'utilisateur U
							ON U.user_cre = U.id_utilisateur
				WHERE
					H.item = '.$this->bdd->quote( $this->itemName ).'
					AND H.item_id = '.$this->id.'
				ORDER BY H.date_cre DESC
				LIMIT 3;'
			);
			$historique = $requete->fetchAll();
			
			return $historique;
		}
		catch( Exception $e ) {
			$this->manager->setMessage( 'Erreur lors de la récupération de l\'historique' ,true);
		}
	}
	
	public function getSearchCriteria( $search, $sql = false ) {
		$criteres = '';
		foreach( $search as $input => $valeur ) {
			foreach( $this->columns as $colonne ) {
				if( $colonne->name == $input ) {
					switch( $colonne->params['type'] ) {
						case 'select' :
							if( !empty( $valeur ) ) {
								$libelle = '';
								$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems( null, false, 1, false, false );
								foreach( $foreignItems as $foreignItem ) {
									if( is_array( $valeur ) ) {
										foreach( $valeur as $valeurUnique ) {
											if( $foreignItem->{$colonne->params['columnKey']} == $valeurUnique ) {
												$libelle .= $foreignItem->{$colonne->params['columnLabel']}.',';
												break;
											}
										}
									} else {
										if( $foreignItem->{$colonne->params['columnKey']} == $valeur ) {
											$libelle = $foreignItem->{$colonne->params['columnLabel']};
											break;
										}
									}
								}
								$libelle = rtrim( $libelle, ',' );
								$criteres .= $sql
									? ' AND '.$colonne->name
									: $colonne->nicename.' = '.$libelle.$this->searchSep;
								
								if( $sql ) {
									if( is_array( $valeur ) ) {
										if( count( $valeur ) ) {
											$clauseIn = '';
											foreach( $valeur as $valeurUnique ) {
												$clauseIn .= '"'.$valeurUnique.'",';
											}
											$clauseIn = rtrim( $clauseIn, ',' );
											$criteres .= ' IN ( '.$clauseIn.' ) ';
										}
									} else {
										$criteres .= ' = '.$valeur.' ';
									}
								}
							}
							break;
						case 'checkbox' :
							if( strlen( $valeur ) > 0 ) {
								$criteres .= $sql
									? ' AND '.$colonne->name.' = '.$valeur.' '
									: $colonne->nicename.' '.($valeur==1?'Oui':'Non').$this->searchSep;
							}
							break;
						case 'date' :
							$linkWord = ( $search[$input][0] > 0 && $search[$input][1] > 0 ) ? ' et ' : $colonne->nicename;
							if ( $search[$input][0] > 0 ) {
								$criteres .= $sql
									? ' AND '.$colonne->name.' >= "'.$search[$input][0].'" '
									: $colonne->nicename.' >= '.date( UIDATE, strtotime($search[$input][0]));
							}
							if ( $search[$input][1] > 0 ) {
								$criteres .= $sql
									? ' AND '.$colonne->name.' <= "'.$search[$input][1].'" '
									: $linkWord.' <= '.date( UIDATE, strtotime($search[$input][1]));
							}
							if( $search[$input][0] > 0 && $search[$input][1] > 0 && !$sql ) {
								$criteres .= $this->searchSep;
							}
							break;
						case 'number' :
							$linkWord = ( $search[$input][0] > 0 && $search[$input][1] > 0 ) ? ' et ' : $colonne->nicename;
							if ( $search[$input][0] > 0 ) {
								$criteres .= $sql
									? ' AND '.$colonne->name.' >= "'.$search[$input][0].'" '
									: $colonne->nicename.' >= '.$search[$input][0];
							}
							if ( $search[$input][1] > 0 ) {
								$criteres .= $sql
									? ' AND '.$colonne->name.' <= "'.$search[$input][1].'" '
									: $linkWord.' <= '.$search[$input][1];
							}
							if( $search[$input][0] > 0 && $search[$input][1] > 0 && !$sql ) {
								$criteres .= $this->searchSep;
							}
							break;
						case 'image' :
							break;
						case 'file' :
							break;
						case 'localisation' :
							break;
						case 'calculation' :
							break;
						default :
							if( strlen( $valeur ) > 0 ) {
								$criteres .= $sql
									? 'AND '.$colonne->name.' LIKE '.$this->bdd->quote('%'.$valeur.'%').' '
									: $colonne->nicename.' contient '.$valeur.$this->searchSep;
							}
					}
				}
			}
		}
		if( !$sql ) {
			$criteres = rtrim( $criteres, ' / ' );
		}
		
		return $criteres;
	}
	
	public function displayInput( $id, $name, $valeur ) {
		$html = '';
		$class = ' form-control form-control-sm ';
		$colonne = $this->getColumn( $name );
		$format = 'name ="'.$colonne->name.'" ';
		$colGrid = property_exists( $colonne, 'grid' ) ? $colonne->grid : $this->grille;
		
		$html .= '<div class="row mb-2 col-12 col-md-'.$colGrid->div.( $colonne->params['type'] != 'textarea' ? ' d-flex align-items-center' : '' ).'">';
		$html .= '<label class="col-4 col-md-'.$colGrid->label.' col-form-label col-form-label-sm text-end">'.$colonne->nicename.'</label>';
		$html .= '<div class="col-8 col-md-'.$colGrid->value.'">';
		
		if( property_exists( $colonne, 'default' ) && $valeur == '' ) {
			$valeur = $colonne->default;
		}
		
		if( $colonne->params['type'] == 'datetime-local' ) {
			$valeur = str_replace( ' ', 'T', $valeur );
		}
		
		if( $colonne->params['type'] == 'color' && $colonne->editable ) {
			$format .= 'type="color" ';
		}
		
		foreach( $colonne->params as $key=>$value ) {
			if( !in_array( $key , $this->ignoredParams ) )
				$format .= $key.'="'.( $value == 'image' ? 'file' : $value ).'" ';
		}
		
		if( !$colonne->editable ) {
			if( in_array( $colonne->params['type'] , $this->disabledExceptions ) ) {
				$format .= 'disabled="disabled" ';
			} else {
				$format .= 'readonly="readonly" ';
			}
		}
		
		if( $colonne->required ) {
			$format .= 'required="required" ';
		}
		
		if( $colonne->params['type'] == 'text' && isset($colonne->params['auto-complete']) ) {
			$class .= " auto-complete ";
			$format .= ' data-parent-item="'.$this->itemName.'" data-colonne="'.$colonne->name.'" ';
		}
		
		if( !in_array ( $colonne->params['type'], [ 'checkbox', 'select' ] ) ) {
			$format .= ' class="'.$class.'" ';
		}
		
		if( property_exists( $colonne, 'unit' ) ) {
			$html .= '<div class="input-group input-group-sm"><span class="input-group-text">'.$colonne->unit.'</span>';
		}
		
		switch( $colonne->params['type'] ) {
			case 'password' :
				if( $id == 0 ) {
					$format .= 'value="'.$valeur.'" ';
					$html .= '<input '.$format.'>';
				} else {
					$html .= '<div class="input-group"><div class="input-group-text" title="Chocher pour changer de mot de passe" data-bs-toggle="tooltip" data-bs-placement="top"><input class="form-check-input form-check-sm mt-0 update-password" type="checkbox" id="'.$name.'" data-name="'.$name.'"></div></div>';
				}
				break;
			case 'textarea' :
				$html .= '<textarea '.$format.'>'.$valeur.'</textarea>';
				break;
			case 'select' :
				$where = isset( $colonne->params['where'] ) ? $colonne->params['where'] : null;
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems( $where, false, 1, false, false );
				
				$nbForeignItems = count( $foreignItems );
				
				if( $nbForeignItems == 1 or !$colonne->editable ) {
					$valeur = $nbForeignItems == 1 ? $foreignItems[0]->{$colonne->params['columnKey']} : $valeur;
					
					$libelle = $foreignItems[0]->{$colonne->params['columnLabel']};
					foreach( $foreignItems as $foreignItem ) {
						if( $foreignItem->{$colonne->params['columnKey']} == $valeur ) {
							$libelle = $foreignItem->{$colonne->params['columnLabel']};
							break;
						}
					}
					
					$html .= '<input type="hidden" name="'.$colonne->name.'" value="'.$valeur.'"><p class="col-form-label col-form-label-sm">'.$libelle.'</p>';
				} else {
					$html .= '<select class="form-select form-select-sm" '.$format.'>';
					$html .= '<option '.($valeur=='' ? 'selected="selected"':'').' '.( $colonne->required ? 'disabled="disabled"':'' ).' value="">'.( $colonne->required ? '-- Choix obligatoire --':'-- Aucun --' ).'</option>';
					foreach( $foreignItems as $foreignItem ) {
						$selected = $valeur == $foreignItem->{$colonne->params['columnKey']} ? 'selected="selected"' : '';
						$html .= '<option '.$selected.' value="'.$foreignItem->{$colonne->params['columnKey']}.'">'.$foreignItem->{$colonne->params['columnLabel']}.'</option>';
					}
					$html .= '</select>';
				}
				break;
			case 'checkbox' :
				$html .= '<input class="form-check-input" type="checkbox" '.$format.' value="1" id="'.$name.'" '.($valeur==1 ? 'checked="checked"':'').'>';
				break;
			case 'image' :
				if( strlen($valeur) > 4  ) {
					$html .= '<div class="card"><img src="'.SITEURL.UPLDIR.$valeur.'" class="card-img" alt="Image"><div class="card-img-overlay"><button title="Supprimer cette image" data-name="'.$colonne->name.'" class="delete-image btn btn-danger btn-sm"><span class="bi bi-x-lg"></span></button></div></div>';
				} else {
					$format .= 'value="'.$valeur.'" ';
					$html .= '<input '.$format.'>';
				}
				break;
			case 'file' :
				if( strlen($valeur) > 4 ) {
					$html .= '<a class="file-link btn btn-secondary btn-sm" href="'.SITEURL.UPLDIR.$valeur.'" target="_blank"><span class="bi bi-search"></span> Consulter</a><button title="Supprimer ce fichier" data-bs-toggle="tooltip" data-bs-placement="top" data-name="'.$colonne->name.'" class="ms-2 delete-file btn btn-danger btn-sm"><span class="bi bi-x-lg"></span></button>';
				} else {
					$format .= 'value="'.$valeur.'" ';
					$html .= '<input '.$format.'>';
				}
				break;
			case 'localisation' :
					$html .= '<input type="hidden" name="'.$colonne->name.'" value="'.htmlspecialchars($valeur).'"><div class="leaflet-input" id="'.$colonne->name.'"></div>';
				break;
			default :
				$format .= 'value="'.$valeur.'" ';
				$html .= '<input '.$format.'>';
		}
		
		if( property_exists( $colonne, 'unit' ) ) {
			$html .= '</div>';
		}
		
		$html .= '</div></div>';
		
		return $html;
	}
	
	public function displaySearchInput( $name ) {
		$html = '';
		$colonne = $this->getColumn( $name );
		$colGrid = property_exists( $colonne, 'grid' ) ? $colonne->grid : $this->grille;
		$class = ' form-control form-control-sm ';
		
		if( $colonne->params['type'] == 'date' or $colonne->params['type'] == 'number' ) {
			$format = 'name ="'.$colonne->name.'[]" ';
		} else {
			$format = 'name ="'.$colonne->name.'" ';
		}
		
		if( !in_array ( $colonne->params['type'], [ 'checkbox', 'select' ] ) ) {
			$format .= ' class="'.$class.'" ';
		}
		
		foreach( $colonne->params as $key=>$value ) {
			if( !in_array( $key , $this->ignoredParams ) && $key != 'disabled' )
			$format .= $key.'="'.$value.'" ';
		}
		
		$html .= '<div class="row mb-2 col-12 col-md-12">';
		$html .= '<label class="col-4 col-md-2 col-form-label col-form-label-sm text-end">'.$colonne->nicename.'</label>';
		$html .= '<div class="col-8 col-md-6">';
		
		switch( $colonne->params['type'] ) {
			case 'select' :
				$html .= '<select class="form-select form-select-sm" '.$format.'>';
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems( null, false, 1, false, false );
				$html .= '<option selected="selected" value="0">-- Tout --</option>';
				foreach( $foreignItems as $foreignItem ) {
					$html .= '<option value="'.$foreignItem->{$colonne->params['columnKey']}.'">'.$foreignItem->{$colonne->params['columnLabel']}.'</option>';
				}
				$html .= '</select>';
				break;
			case 'checkbox' :
				$html .= '<select class="form-select form-select-sm" '.$format.'>';
				$html .= '<option selected="selected" value>-- Tout --</option>';
				$html .= '<option value="0">Non</option>';
				$html .= '<option value="1">Oui</option>';
				$html .= '</select>';
				break;
			case 'number' :
				$html .= '<div class="input-group input-group-sm"><span class="input-group-text">Entre</span><input '.$format.'><span class="input-group-text">et</span><input '.$format.'></div>';
				break;
			case 'date' :
				$html .= '<div class="input-group input-group-sm"><span class="input-group-text">Entre le</span><input '.$format.'><span class="input-group-text">et le</span><input '.$format.'></div>';
				break;
			case 'image' :
				break;
			case 'file' :
				break;
			case 'localisation' :
				break;
			case 'calculation' :
				break;
			default :
				$html .= '<input '.$format.'>';
		}
		
		$html .= '</div></div>';
		
		return $html;
	}
	
	public function displayField( $name, $valeur, $edit = false ) {
		$html = '';
		$colonne = $this->getColumn( $name );
		
		if( $colonne->name == 'id_etat' ) {
			$colorScheme = '';
			$colorText = '';
			foreach( $this->etatsColors as $couleur ) {
				if( $couleur->id_etat == $valeur ) {
					$colorScheme = $couleur->libelle;
					$colorText = $couleur->dark ? 'text-dark' : '';
					break;
				}
			}
		}
		
		if( $edit ) {
			$html .= '<div class="row mb-2 col-12 col-md-12">';
			$html .= '<label class="col-4 col-md-2 col-form-label col-form-label-sm text-end">'.$colonne->nicename.'</label>';
			$html .= '<div class="col-8 col-md-6"><p class="col-form-label col-form-label-sm">';
		}
		
		switch( $colonne->params['type'] ) {
			case 'number' :
				$html .= $valeur;
				if( property_exists( $colonne, 'unit' ) ) {
					$html .= ' '.$colonne->unit;
				}
				break;
			case 'color' :
				$html .= '<span style="background: '.$valeur.'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				break;
			case 'date' :
				if( $valeur ) {
					$html .= date( UIDATE, strtotime($valeur));
				}
				break;
			case 'datetime-local' :
				if( $valeur ) {
					$html .= date( UIDATETIME, strtotime($valeur));
				}
				break;
			case 'textarea' :
				$html .= nl2br( strip_tags( $valeur ) );
				break;
			case 'time' :
				$html .= substr( $valeur, 0, 5);
				break;
			case 'select' :
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems( null, false, 1, false, false );
				foreach( $foreignItems as $foreignItem ) {
					if( $foreignItem->{$colonne->params['columnKey']} == $valeur ) {
						if( $colonne->name == 'id_etat' ) {
							$html .= '<span class="badge bg-'.$colorScheme.' '.$colorText.'">'.$foreignItem->{$colonne->params['columnLabel']}.'</span>';
						} else {
							$html .= $foreignItem->{$colonne->params['columnLabel']};
						}
						break;
					}
				}
				break;
			case 'checkbox' :
				$html .= '<span class="bi bi-'.($valeur == 1 ? 'check' : 'x').'-circle"></span>';
				break;
			case 'url' :
				$html .= '<a data-bs-toggle="tooltip" target="_blank" href="'.$valeur.'" title="Ouvrir le lien">'.$valeur.'</a>';
				break;
			case 'image' :
				if( strlen($valeur) > 4  ) {
					$html .= '<img class="img-fluid" alt="Image" src="'.SITEURL.UPLDIR.$valeur.'" />';
				}
				break;
			case 'file' :
				if( strlen($valeur) > 4  ) {
					$html .= '<a data-bs-toggle="tooltip" class="btn btn-sm btn-secondary" target="_blank" href="'.SITEURL.UPLDIR.$valeur.'" title="Voir le fichier" data-bs-toggle="tooltip" data-bs-placement="top"><i class="bi bi-search"></i></a>';
				}
				break;
			case 'localisation' :
				$valeur = json_decode( $valeur );
				if( is_object( $valeur ) ) {
					$html .= $valeur->lat.'<br />'.$valeur->lng ;
				}
				break;
			case 'date' :
				$dateObject = date_create_from_format( 'Y-m-d', $valeur );
				$html .= date_format( $dateObject, 'd/m/Y' );
				break;
			case 'datetime-local' :
				$dateObject = date_create_from_format( 'Y-m-d H:i:s', $valeur );
				$html .= date_format( $dateObject, 'd/m/Y à H:i' );
				break;
			default :
				$html .= $valeur;
		}
		
		if( $edit ) {
			$html .= '</p></div></div>';
		}
		
		return $html;
	}
	
	public function displayFieldPDF( $name, $valeur, $pdf, $largeur = 277, $hauteur = 7 ) {
		$html = '';
		$colonne = $this->getColumn( $name );
		
		switch( $colonne->params['type'] ) {
			case 'number' :
				$html = $valeur;
				if( property_exists( $colonne, 'unit' ) ) {
					$html .= ' '.$colonne->unit;
				}
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'C', 1 );
				break;
			case 'date' :
				if( $valeur != null ) {
					$html = date( UIDATE, strtotime($valeur));
					$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'C', 1 );
				}
				break;
			case 'textarea' :
				$html = strip_tags( $valeur );
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'L', 1 );
				break;
			case 'time' :
				$html = substr( $valeur, 0, 5);
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'C', 1 );
				break;
			case 'select' :
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems( null, false, 1, false, false );
				foreach( $foreignItems as $foreignItem ) {
					if( $foreignItem->{$colonne->params['columnKey']} == $valeur ) {
						$html = $foreignItem->{$colonne->params['columnLabel']};
						break;
					}
				}
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'L', 1 );
				break;
			case 'checkbox' :
				$html = ($valeur == 1 ? 'Oui' : 'Non');
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'C', 1 );
				break;
			case 'image' :
				$html = $valeur;
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'L', 1 );
				break;
			case 'file' :
				$html = $valeur;
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'L', 1 );
				break;
			case 'localisation' :
				$valeur = json_decode( $valeur );
				$html = $valeur->lat.'/'.$valeur->lng;
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'L', 1 );
				break;
			default :
				$html = $valeur;
				$pdf->Cell( $largeur, $hauteur, $html, 1, 0, 'C', 1 );
		}
	}
	
	public function getFieldXLS( $name, $valeur ) {
		$texte = '';
		$colonne = $this->getColumn( $name );
		
		switch( $colonne->params['type'] ) {
			case 'number' :
				$texte = $valeur;
				if( property_exists( $colonne, 'unit' ) ) {
					$texte .= ' '.$colonne->unit;
				}
				break;
			case 'date' :
				if( $valeur != null ) {
					$texte = date( UIDATE, strtotime($valeur));
				}
				break;
			case 'textarea' :
				$texte = strip_tags( $valeur );
				break;
			case 'time' :
				$texte = substr( $valeur, 0, 5);
				break;
			case 'select' :
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems( null, false, 1, false, false );
				foreach( $foreignItems as $foreignItem ) {
					if( $foreignItem->{$colonne->params['columnKey']} == $valeur ) {
						$texte = $foreignItem->{$colonne->params['columnLabel']};
						break;
					}
				}
				break;
			case 'checkbox' :
				$texte = ($valeur == 1 ? 'X' : '');
				break;
			default :
				$texte = $valeur;
		}
		
		return $texte;
	}
	
	public function displayPagination( $paged = 1, $range = 2, $bottom = false ) {
		$nbparpage = $this->manager->getOption('nbparpage');
		
		$pages = Ceil( $this->nbItems / ( $nbparpage < 1 ? 140000000 : $nbparpage ) );
		
		if( $paged > $pages ) $paged = $pages;
		$plural = $this->nbItems > 1 ? true : false;
		$showitems = ($range * 2)+1;
		if( $pages > 1 ) {
?>
					<nav aria-label="Navigation pages" class="text-center">
						<ul class="pagination pagination-sm justify-content-center">
<?php
			if($paged > 2 && $paged > $range+1 && $showitems < $pages) {
?>
							<li class="page-item"><a class="page-link" title="Première page" href="<?='index.php?item='.$this->itemName.'&p=1'; ?>">&laquo;</a></li>
<?php			
			}
			if($paged > 1 && $showitems < $pages) {
?>
							<li class="page-item"><a class="page-link" title="Page précédente" href="<?='index.php?item='.$this->itemName.'&p='.($paged-1); ?>">&lsaquo;</a></li>
<?php			
			}

			for ($i=1; $i <= $pages; $i++) {
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
					if( $paged == $i ) {
?>
							<li class="page-item active"><a class="page-link"><?=$i; ?></a></li>
<?php				
					} else {
?>
							<li class="page-item"><a class="page-link" href="<?='index.php?item='.$this->itemName.'&p='.$i; ?>" class="inactive"><?=$i; ?></a></li>
<?php				
					}
				}
			}

			if ($paged < $pages && $showitems < $pages) {
?>
							<li class="page-item"><a class="page-link" title="Page suivante" href="<?='index.php?item='.$this->itemName.'&p='.($paged+1); ?>">&rsaquo;</a></li>
<?php			
			}
			if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
?>
							<li class="page-item"><a class="page-link" title="Dernière page" href="<?='index.php?item='.$this->itemName.'&p='.$pages; ?>">&raquo;</a></li>
<?php			
			}
?>
						</ul>
					</nav>
<?php
			if( !$bottom ) {
?>
					<small><strong><?=$this->nbItems; ?></strong> <em>élément<?=$plural ? 's' : ''; ?> sur <?=$pages; ?> page(s)</em></small>
<?php
			}
		} else {
			if( !$bottom ) {
?>
					<small><strong><?=$this->nbItems; ?></strong> <em>élément<?=$plural ? 's' : ''; ?></em></small>
<?php
			}
		}
	}
}
