<?php
class Model {
	protected $bdd;
	protected $manager;
	protected $table;
	protected $itemName;
	protected $parentItem;
	protected $parentId = false;
	protected $single;
	protected $prints;
	protected $actions;
	protected $plural;
	protected $columns;
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
	
	public function __construct( PDO $bdd, Manager $manager, stdClass $model ) {
		$this->bdd = $bdd;
		$this->manager = $manager;
		$this->itemName = $model->itemName;
		$this->table = $model->table;
		$this->single = $model->single;
		$this->plural = $model->plural;
		$this->columns = $model->columns;
		$this->orderby = $model->orderby;
		
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
			$this->manager->setError( sprintf( M_IDERR, $msg ) );
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
		
		// Impressions
		$this->prints = array();
		if( property_exists( $model, 'prints' ) ) {
			$this->prints = $model->prints;
		}
		
		// Actions
		$this->actions = array();
		if( property_exists( $model, 'actions' ) ) {
			$this->actions = $model->actions;
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
	
	public function getPrints() {
		return $this->prints;
	}
	
	public function getActions() {
		return $this->actions;
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
	
	public function getItems( array $search = null, $paginate = false, $page = 1 ) {
		$nbparpage = $this->manager->getOption('nbparpage');
		$nbparpage = $nbparpage < 1 ? 140000000 : $nbparpage;
		$page = $page-1;
		
		try {
			$select = '';
			$where = '';
			$limit = '';
			
			if( $paginate ) $limit = 'LIMIT '.$page*$nbparpage.', '.$nbparpage;
			
			if( $search ) $where = $this->getSearchCriteria( $search, true );
			
			foreach( $this->columns as $colonne ) {
				$select .= $colonne->name.',';
			}
			$select = rtrim( $select, ',' );
			
			$requete = $this->bdd->query('
				SELECT COUNT(*)
				FROM '.$this->table.'
				'.$where.';'
			);
			$this->nbItems = $requete->fetchColumn();
			$requete->closeCursor();
			
			$requete = $this->bdd->query('
				SELECT '.$select.'
				FROM '.$this->table.'
				'.$where.'
				ORDER BY '.$this->orderby.'
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
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
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
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
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
				$this->manager->setError( sprintf( M_UPIDERR, $msg ) );
			}
		}
		
		try {
			$requete = $this->bdd->prepare('
				SELECT *
				FROM '.$this->table.'
				WHERE id_'.$this->itemName.' = ?;'
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
			$this->manager->setError( sprintf( M_ITEMERR, $msg ) );
			$this->currentItem = new stdClass();
		}
		finally {
			return $this->currentItem;
		}
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
	
	public function getSearchCriteria( $search, $sql = false ) {
		$criteres = $sql ? 'WHERE 1=1 ' : '';
		foreach( $search as $input => $valeur ) {
			foreach( $this->columns as $colonne ) {
				if( $colonne->name == $input ) {
					switch( $colonne->params['type'] ) {
						case 'select' :
							if( $valeur > 0 ) {
								$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems();
								foreach( $foreignItems as $foreignItem ) {
									if( $foreignItem->{$colonne->params['columnKey']} == $valeur ) {
										$libelle = $foreignItem->{$colonne->params['columnLabel']};
										break;
									}
								}
								$criteres .= $sql
									? 'AND '.$colonne->name.' = '.$valeur.' '
									: $colonne->nicename.' = '.$libelle.$this->searchSep;
							}
							break;
						case 'checkbox' :
							if( strlen( $valeur ) > 0 ) {
								$criteres .= $sql
									? 'AND '.$colonne->name.' = 1 '
									: $colonne->nicename.' coché'.$this->searchSep;
							}
							break;
						case 'date' :
							$linkWord = ( $search[$input][0] > 0 && $search[$input][1] > 0 ) ? ' et ' : $colonne->nicename;
							if ( $search[$input][0] > 0 ) {
								$criteres .= $sql
									? 'AND '.$colonne->name.' >= "'.$search[$input][0].'" '
									: $colonne->nicename.' >= '.date( UIDATE, strtotime($search[$input][0]));
							}
							if ( $search[$input][1] > 0 ) {
								$criteres .= $sql
									? 'AND '.$colonne->name.' <= "'.$search[$input][1].'" '
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
									? 'AND '.$colonne->name.' >= "'.$search[$input][0].'" '
									: $colonne->nicename.' >= '.$search[$input][0];
							}
							if ( $search[$input][1] > 0 ) {
								$criteres .= $sql
									? 'AND '.$colonne->name.' <= "'.$search[$input][1].'" '
									: $linkWord.' <= '.$search[$input][1];
							}
							if( $search[$input][0] > 0 && $search[$input][1] > 0 && !$sql ) {
								$criteres .= $this->searchSep;
							}
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
	
	public function setItem( $data, $register = false ) {
		$this->id = $register ? $this->nextId : $data['id_'.$this->itemName];
		$columns = '';
		$values = '';
		$update = '';
		foreach( $this->columns as $colonne ) {
			if( $colonne->params['type'] == 'image' ) {
				// Traitement upload des images
				$width = isset( $colonne->params['width'] ) ? $colonne->params['width'] : DFWIDTH;
				$height = isset( $colonne->params['height'] ) ? $colonne->params['height'] : DFHEIGHT;
				
				$up = new Telechargement( SITEDIR.UPLDIR,'form-submit',$colonne->name );
				$up->Set_Redim($width,$height);
				$up->Upload();
				$messages = $up->Get_Tab_message();
				$resultats = $up->Get_Tab_result();
				
				$data[$colonne->name] = $resultats['resultat'][0][SITEDIR.UPLDIR][0]['nom'];
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
						$value = password_hash($value, PASSWORD_BCRYPT );
					}
					$columns .= $key.',';
					$values .= $this->bdd->quote( $value ).',';
					$update .= $colonne->name.' = '.$this->bdd->quote( $value ).',';
					$flag = true;
					break;
				}
			}
			if( $colonne->params['type'] == 'checkbox' && !$flag ) {
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
			
			$this->manager->setMessage( sprintf( M_ITEMSET, $this->single ) );
		}
		catch( Exception $e ) {
			if( $e->getCode() == 23000 ) {
				$this->manager->setError( sprintf( M_ITEMSETKEYERR, $this->single, $data['identifiant'] ) );
			} else {
				$this->manager->setError( sprintf( M_ITEMSETERR, $this->single, $id ) );
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
				$this->manager->setError( sprintf( M_ITEMDELKEYERR, $msg, $id ) );
			} else {
				$this->manager->setError( sprintf( M_ITEMDELERR, $msg, $id ) );
			}
			
			return false;
		}
	}
	
	public function displayInput( $id, $name, $valeur, $class = '' ) {
		$html = '';
		$colonne = $this->getColumn( $name );
		$format = 'name ="'.$colonne->name.'" ';
		
		if( property_exists( $colonne, 'default' ) && $valeur == '' ) {
			$valeur = $colonne->default;
		}
		
		if( $colonne->params['type'] == 'date' && $colonne->editable ) {
			$valeur = $valeur ? date( UIDATE, strtotime($valeur) ) : '';
			$format .= 'type="text" ';
			$class .= ' datepicker ';
		}
		
		if( $colonne->params['type'] == 'color' && $colonne->editable ) {
			$format .= 'type="text" ';
			$class .= ' colorpicker ';
		}
		
		foreach( $colonne->params as $key=>$value ) {
			if( $key != 'item' && $key != 'columnKey' && $key != 'columnLabel' && $key != 'where' )
				$format .= $key.'="'.( $value == 'image' ? 'file' : $value ).'" ';
		}
		
		if( !$colonne->editable ) {
			$class .= ' form-control-plaintext ';
		}
		
		if( $colonne->required ) {
			$format .= 'required ';
		}
		
		if( $colonne->params['type'] == 'select' && isset($colonne->params['autocomplete']) ) $class = "auto-complete";
		if( $colonne->params['type'] != 'checkbox' ) $format .= ' class="'.$class.'" ';
		
		switch( $colonne->params['type'] ) {
			case 'password' :
				if( $id == 0 ) {
					$format .= 'value="'.$valeur.'" ';
					$html .= '<input '.$format.'>';
				} else {
					$html .= '<div class="custom-control custom-checkbox"><input title="cocher pour changer le mot de passe" class="custom-control-input update-password" type="checkbox" id="'.$name.'" data-name="'.$name.'"><label class="custom-control-label" for="'.$name.'"></label></div>';
				}
				break;
			case 'textarea' :
				$html .= '<textarea '.$format.'>'.$valeur.'</textarea>';
				break;
			case 'select' :
				$where = isset( $colonne->params['where'] ) ? $colonne->params['where'] : null;
				$html .= '<select '.$format.'><option '.($valeur=='' ? 'selected="selected"':'').' disabled="disabled" value>-- Aucun --</option>';
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems( $where );
				foreach( $foreignItems as $foreignItem ) {
					$selected = $valeur == $foreignItem->{$colonne->params['columnKey']} ? 'selected="selected"' : '';
					$html .= '<option '.$selected.' value="'.$foreignItem->{$colonne->params['columnKey']}.'">'.$foreignItem->{$colonne->params['columnLabel']}.'</option>';
				}
				$html .= '</select>';
				break;
			case 'checkbox' :
				$format .= ' value="1" ';
				if( $valeur == 1 ) $format .= 'checked="checked" ';
				$html .= '<div class="custom-control custom-checkbox"><input '.$format.' class="custom-control-input" type="checkbox" id="'.$name.'"><label class="custom-control-label" for="'.$name.'"></label></div>';
				break;
			case 'image' :
				if( strlen($valeur) > 4  ) {
					$html .= '<img alt="Image" src="'.SITEURL.UPLDIR.$valeur.'" /><a title="Supprimer cette image" data-name="'.$colonne->name.'" class="delete-image btn btn-danger btn-xs"><i class="fas fa-sm fa-times"></i></a>';
				} else {
					$format .= 'value="'.$valeur.'" ';
					$html .= '<input '.$format.'>';
				}
				break;
			default :
				$format .= 'value="'.$valeur.'" ';
				$html .= '<input '.$format.'>';
		}
		
		if( property_exists( $colonne, 'unit' ) ) {
			$html .= '<div class="input-group-append"><span class="input-group-text">'.$colonne->unit.'</span></div>';
		}
		
		return $html;
	}
	
	public function displaySearchInput( $name, $class = false ) {
		$html = '';
		$colonne = $this->getColumn( $name );
		if( $colonne->params['type'] == 'date' or $colonne->params['type'] == 'number' ) {
			$format = 'name ="'.$colonne->name.'[]" ';
		} else {
			$format = 'name ="'.$colonne->name.'" ';
		}
		
		if( $colonne->params['type'] == 'date' ) {
			$format .= 'type="text" ';
			$class .= ' datepicker ';
		}
		
		if( $class && $colonne->params['type'] != 'checkbox' ) $format .= 'class ="'.$class.'" ';
		
		foreach( $colonne->params as $key=>$value ) {
			if( $key != 'item' && $key != 'columnKey' && $key != 'columnLabel' && $key != 'where' )
			$format .= $key.'="'.$value.'" ';
		}
		
		switch( $colonne->params['type'] ) {
			case 'select' :
				$html .= '<select '.$format.'>';
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems();
				$html .= '<option selected="selected" value="0">-- Tout --</option>';
				foreach( $foreignItems as $foreignItem ) {
					$html .= '<option value="'.$foreignItem->{$colonne->params['columnKey']}.'">'.$foreignItem->{$colonne->params['columnLabel']}.'</option>';
				}
				$html .= '</select>';
				break;
			case 'checkbox' :
				$format .= 'value="1" ';
				$html .= '<input '.$format.'>';
				break;
			case 'number' :
				$html .= '<div class="input-group-prepend"><span class="input-group-text form-control-sm">Entre</span></div><input '.$format.'><div class="input-group-prepend"><span class="input-group-text form-control-sm">et</span></div><input '.$format.'>';
				break;
			case 'date' :
				$html .= '<div class="input-group-prepend"><span class="input-group-text form-control-sm">Entre le</span></div><input '.$format.'><div class="input-group-prepend"><span class="input-group-text form-control-sm">et le</span></div><input '.$format.'>';
				break;
			default :
				$html .= '<input '.$format.'>';
		}
		
		return $html;
	}
	
	public function displayField( $name, $valeur ) {
		$html = '';
		$colonne = $this->getColumn( $name );
		
		switch( $colonne->params['type'] ) {
			case 'color' :
				$html = '<span style="background: '.$valeur.'">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				break;
			case 'date' :
				if( $valeur != null ) {
					$html = date( UIDATE, strtotime($valeur));
				}
				break;
			case 'textarea' :
				$html = strip_tags( $valeur );
				break;
			case 'time' :
				$html = substr( $valeur, 0, 5);
				break;
			case 'select' :
				$foreignItems = $this->foreignColumns[$colonne->params['item']]->getItems();
				foreach( $foreignItems as $foreignItem ) {
					if( $foreignItem->{$colonne->params['columnKey']} == $valeur ) {
						$html = $foreignItem->{$colonne->params['columnLabel']};
						break;
					}
				}
				break;
			case 'checkbox' :
				$html = '<span class="glyphicon glyphicon-'.($valeur == 1 ? 'ok' : 'remove').'-circle"></span>';
				break;
			case 'url' :
				$html = '<a href="'.$valeur.'" title="Lien">'.$valeur.'</a>';
				break;
			case 'image' :
				if( strlen($valeur) > 4  ) {
					$html .= '<img alt="Image" src="'.SITEURL.UPLDIR.$valeur.'" />';
				}
				break;
			default :
				$html = $valeur;
		}
		
		return $html;
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
							<li class="page-item"><a class="page-link" title="Première page" href="<?php echo 'index.php?item='.$this->itemName.'&p=1'; ?>">&laquo;</a></li>
<?php			
			}
			if($paged > 1 && $showitems < $pages) {
?>
							<li class="page-item"><a class="page-link" title="Page précédente" href="<?php echo 'index.php?item='.$this->itemName.'&p='.($paged-1); ?>">&lsaquo;</a></li>
<?php			
			}

			for ($i=1; $i <= $pages; $i++) {
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
					if( $paged == $i ) {
?>
							<li class="page-item active"><a class="page-link"><?php echo $i; ?></a></li>
<?php				
					} else {
?>
							<li class="page-item"><a class="page-link" href="<?php echo 'index.php?item='.$this->itemName.'&p='.$i; ?>" class="inactive"><?php echo $i; ?></a></li>
<?php				
					}
				}
			}

			if ($paged < $pages && $showitems < $pages) {
?>
							<li class="page-item"><a class="page-link" title="Page suivante" href="<?php echo 'index.php?item='.$this->itemName.'&p='.($paged+1); ?>">&rsaquo;</a></li>
<?php			
			}
			if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
?>
							<li class="page-item"><a class="page-link" title="Dernière page" href="<?php echo 'index.php?item='.$this->itemName.'&p='.$pages; ?>">&raquo;</a></li>
<?php			
			}
?>
						</ul>
					</nav>
<?php
			if( !$bottom ) {
?>
					<small><strong><?php echo $this->nbItems; ?></strong> <em>élément<?php echo $plural ? 's' : ''; ?> sur <?php echo $pages; ?> page(s)</em></small>
<?php
			}
		} else {
			if( !$bottom ) {
?>
					<small><strong><?php echo $this->nbItems; ?></strong> <em>élément<?php echo $plural ? 's' : ''; ?></em></small>
<?php
			}
		}
	}
}
?>
