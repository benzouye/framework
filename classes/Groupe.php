<?php
class groupe extends Model {
	protected $access = array();
	protected $users = array();
	protected $accessGroup = array();
	
	public function get_access() {
		try {
			$requete = $this->bdd->query('
				SELECT alias, nom
				FROM '.DBPREF.'item
				WHERE admin = 0
				ORDER BY nom ASC;'
			);
			$this->access = $requete->fetchAll();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'items';
			}
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
		}
		finally {
			return $this->access;	
		}
	}
	
	public function get_utilisateur() {
		try {
			$requete = $this->bdd->query('
				SELECT *
				FROM '.DBPREF.'utilisateur
				WHERE id_groupe = '.intval( $this->id ).'
				ORDER BY identifiant ASC;'
			);
			$this->users = $requete->fetchAll();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'utilisateurs';
			}
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
		}
		finally {
			return $this->users;	
		}
	}
	
	public function get_access_group() {
		try {
			$requete = $this->bdd->query('
				SELECT I.alias, I.nom, GI.create, GI.read, GI.update, GI.delete, GI.all
				FROM
					'.DBPREF.'groupe_item GI
						INNER JOIN '.DBPREF.'item I
							ON GI.alias = I.alias
				WHERE
					I.admin = 0
					AND GI.id_groupe = '.intval( $this->id ).'
				ORDER BY I.nom ASC;'
			);
			$this->accessGroup = $requete->fetchAll();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'items accessibles';
			}
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
		}
		finally {
			return $this->accessGroup;	
		}
	}
	
	public function set_access( $data ) {
		try {	
			$groupe = $data['id'];
			
			$this->bdd->query( '
				DELETE FROM '.DBPREF.'groupe_item
				WHERE id_groupe = '.intval($groupe).';'
			);
			
			if( isset( $data['access'] ) ) {
				$values = '';
				foreach( $data['access'] as $alias => $access ) {
					$values .= '('
						.intval($groupe).','
						.$this->bdd->quote($alias).','
						.( isset( $access['create'] )	? 1 : 0 ).','
						.( isset( $access['read'] )		? 1 : 0 ).','
						.( isset( $access['update'] )	? 1 : 0 ).','
						.( isset( $access['delete'] )	? 1 : 0 ).','
						.( isset( $access['all'] )		? 1 : 0 ).'),';
				}
				$values = rtrim( $values, ',' );
				
				$requete = $this->bdd->query('
					INSERT INTO '.DBPREF.'groupe_item ( `id_groupe`, `alias`, `create`, `read`, `update`, `delete`, `all` )
					VALUES '.$values.';'
				);
				$requete->closeCursor();
			}
			$this->manager->setMessage( 'Les accès ont bien été mis à jour pour ce groupe' );
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'groupe';
			}
			$this->manager->setMessage( sprintf( M_RELNEWERR, $msg, 'accès' ) ,true);
		}
	}
}
