<?php
class Manager {

	protected $bdd = null;
	protected $debug = false;
	protected $messages = array();
	protected $options = array();
	protected $analyses = array();
	protected $items = array();
	protected $user = false;
	protected $affectations = array();
	protected $users = array();
	protected $userCan;

	public function __construct( PDO $bdd, $debug ) {
		$this->bdd = $bdd;
		$this->debug = $debug;
		
		$this->setOptions();
		$this->setItemAndMenus();
	}
	
	public function getDebug() {
		return $this->debug;
	}
	
	public function setOptions() {
		try {
			$requete = $this->bdd->query( '
				SELECT *
				FROM '.DBPREF.'option;'
			);
			$this->options = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = M_OPTSERR;
			}
			$this->errors[] = $msg;
		}
	}
	
	public function setUsers() {
		try {
			$requete = $this->bdd->query( '
				SELECT *
				FROM '.DBPREF.'utilisateur;'
			);
			$this->users = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = M_USERSERR;
			}
			$this->errors[] = $msg;
		}
	}
	
	public function setAffectations( $idUser ) {
		try {
			$requete = $this->bdd->prepare( '
				SELECT id_affectation
				FROM '.DBPREF.'utilisateur_affectation
				WHERE id_utilisateur = ?;'
			);
			$requete->execute( [ $idUser ] );
			$this->affectations = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = M_USERSERR;
			}
			$this->errors[] = $msg;
		}
	}
	
	public function getAffectations() {
		return $this->affectations;
	}
	
	public function setItemAndMenus() {
		try {
			$requete = $this->bdd->query( '
				SELECT *
				FROM '.DBPREF.'item I
				ORDER BY menu, menu_order;'
			);
			$this->items = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = M_ITEMSERR;
			}
			$this->errors[] = $msg;
		}
	}
	
	public function setUser() {
		$good_ident = true;
		$good_valide = true;
		$good_mdp = true;
		$good_session = true;
		
		if( isset( $_SESSION[DBPREF.'_userId'] ) ) {
			foreach( $this->users as $user ) {
				if( $_SESSION[DBPREF.'_userId'] === $user->id_utilisateur ) {
					$good_session = true;
					$this->user = $user;
					$this->setAffectations( $user->id_utilisateur );
					break;
				} else {
					$good_session = false;
				}
			}
		} elseif( isset( $_POST['identifiant'] ) && isset ( $_POST['password'] ) && !isset( $_GET['item'] ) ) {
			$good_ident = false;
			$good_valide = false;
			$good_mdp = false;
			foreach( $this->users as $user ) {
				if( $_POST['identifiant'] === $user->identifiant ) {
					$good_ident = true;
					if( $user->valide ) {
						$good_valide = true;
						if( password_verify( $_POST['password'], $user->password ) ) {
							$good_mdp = true;
							$this->setMessage( M_LOGIN );
							$this->user = $user;
							$this->setAffectations( $user->id_utilisateur );
							$_SESSION[DBPREF.'_userId'] = $user->id_utilisateur;
							break;
						}
					}
				}
			}
		} else {
			$this->user = false;
		}
		
		if( !$good_session ) $this->setMessage( M_SESSERR ,true);
		if( !$good_ident ) $this->setMessage( M_IDENTERR ,true);
		if( $good_ident && !$good_valide ) $this->setMessage( M_VALIDERR ,true);
		if( !$good_mdp && $good_valide ) $this->setMessage( M_PASSERR ,true);
	}
	
	public function getUser() {
		$this->setUsers();
		$this->setUser();
		return $this->user;
	}
	
	public function getUserCan( $alias = false ) {
		if( $this->user ) {
			try {
				$requete = $this->bdd->query('
					SELECT
						I.alias,
						U.`admin`,
						COALESCE( GI.`create`, 0 ) AS `create`,
						COALESCE( GI.`read`, 0 ) AS `read`,
						COALESCE( GI.`update`, 0 ) AS `update`,
						COALESCE( GI.`delete`, 0 ) AS `delete`,
						COALESCE( GI.`all`, 0 ) AS `all`,
						U.`admin` + COALESCE( GI.`create`, 0 ) + COALESCE( GI.`read`, 0 ) + COALESCE( GI.`update`, 0 ) + COALESCE( GI.`delete`, 0 ) AS `access`
					FROM
						'.DBPREF.'utilisateur U
							CROSS JOIN '.DBPREF.'item I
							LEFT JOIN '.DBPREF.'groupe_item GI
								ON GI.id_groupe = U.id_groupe
								AND I.alias = GI.alias
					WHERE
						U.id_utilisateur = '.$this->user->id_utilisateur.'
						'.( $alias ? 'AND I.alias = '.$this->bdd->quote( $alias ) : '' ).';'
				);
				if( $alias ) {
					$this->userCap = $requete->fetch();
				} else {
					$this->userCap = $requete->fetchAll();
				}
			}
			catch( Exception $e ) {
				if( $this->getDebug() ) {
					$msg = $e->getMessage();
				} else {
					$msg = 'droits utilisateur';
				}
				$this->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
			}
		} else {
			$this->userCap = (object) array(
				'admin' => 0,
				'create' => 0,
				'read' => 0,
				'update' => 0,
				'delete' => 0,
				'access' => 0
			);
		}
		
		return $this->userCap;
	}
	
	public function getItems() {
		return $this->items;
	}
	
	public function getItem( $alias ) {
		$this->item = false;
		
		foreach( $this->items as $item ) {
			if( $item->alias == $alias ) {
				$this->item = $item;
				break;
			}
		}
		
		if( !$this->item ) {
			$this->errors[] = printf( M_ITEMERR, $alias );
		}
		
		return $this->item;
	}
	
	public function is_item( $alias ) {
		$flag = false;
		foreach( $this->items as $item ) {
			if( $item->alias == $alias ) {
				$flag = true;
				break;
			}
		}
		return $flag;
	}
	
	public function is_static( $alias ) {
		$flag = false;
		foreach( $this->items as $item ) {
			if( $item->static && $item->alias == $alias ) {
				$flag = true;
				break;
			}
		}
		return $flag;
	}
	
	public function is_variant( $alias ) {
		$flag = false;
		foreach( $this->items as $item ) {
			if( $item->variant && $item->alias == $alias ) {
				$flag = true;
				break;
			}
		}
		return $flag;
	}
	
	public function getMenu( $menuId ) {
		$menu = array();
		foreach( $this->items as $item ) {
			if( $item->menu == $menuId && $this->user->admin >= $item->admin ) {
				$menu[] = $item;
			}
		}
		
		return $menu;
	}
	
	public function getSelect( $id, array $data, array $colonnes ) {
		$html = '<select id="'.$id.'" name="'.$id.'">';
		$html .= '<option value="0">--- Tous ---</option>';
		foreach( $data as $option ) {
			$html .= '<option value="'.$option->{$colonnes[0]}.'">'.$option->{$colonnes[1]}.'</option>';
		}
		$html .= '</select>';
		
		return $html;
	}
	
	public function getOption( $alias ) {
		$retour = false;
		foreach( $this->options as $option ) {
			if( $option->alias == $alias ) {
				$retour = $option->valeur;
				break;
			}
		}
		if( !$retour && $retour != 0 ) {
			$this->errors[] = sprintf( M_OPTERR, $alias );
		}
		return $retour;
	}
	
	public function getItemAnalyses( $alias ) {
		$this->analyses = array();
		try {
			$requete = $this->bdd->prepare( '
				SELECT AI.id_analyse
				FROM
					'.DBPREF.'analyse_item AI
						INNER JOIN '.DBPREF.'analyse A
							ON AI.id_analyse = A.id_analyse
				WHERE AI.alias = ?
				ORDER BY A.ordre;'
			);
			$requete->execute( [ $alias ] );
			$this->analyses = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'analyse';
			}
			$this->errors[] = sprintf( M_ITEMSERR, $msg );
		}
		finally {
			return $this->analyses;
		}
	}
	
	public function setMessage( $message, $erreur = false ) {
		$this->messages[] = [
			'message' => $message,
			'error' => $erreur
		];
	}
	
	public function getNbErrors() {
		$nbErrors = 0;
		foreach( $this->messages as $message ) {
			if( $message['error'] ) $nbErrors++;
		}
		return $nbErrors;
	}
	
	public function showMessages() {
		$html = '';
		if( count( $this->messages ) ) {
			$html .= '<div class="toast-container position-absolute bottom-0 end-0 p-3">';
			foreach( $this->messages as $message ) {
				$classe = $message['error'] ? 'danger' : 'info';
				$texte  = $message['error'] ? 'white' : 'dark';
				$icone  = $message['error'] ? 'exclamation-triangle-fill' : 'check-circle-fill';
				
				$html .= '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
				$html .= '<div class="toast-header">';
				$html .= '<span class="bi bi-'.$icone.'"></span>';
				$html .= '<small class="ms-auto">'.date("d/m/Y à H:i").'</small>';
				$html .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fermer"></button>';
				$html .= '</div>';
				$html .= '<div class="toast-body text-'.$texte.' bg-'.$classe.' fw-bold">';
				$html .= $message['message'];
				$html .= '</div>';
				$html .= '</div>';
			}
			$html .= '</div>';
		}
		return $html;
	}
}
