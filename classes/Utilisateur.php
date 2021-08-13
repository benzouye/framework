<?php
class utilisateur extends Model {
	protected $access = array();
	
	public function get_access() {
		try {
			$requete = $this->bdd->query('
				SELECT id_item, nom
				FROM '.DBPREF.'item
				ORDER BY nom ASC;'
			);
			$this->access = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'accès utilisateur';
			}
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
		}
		finally {
			return $this->access;	
		}
	}
	
	public function get_affectation() {
		try {
			$requete = $this->bdd->query('
				SELECT A.*
				FROM '.DBPREF.'utilisateur_affectation UA
					INNER JOIN '.DBPREF.'affectation A
						ON UA.id_affectation = A.id_affectation
				WHERE UA.id_utilisateur = '.intval( $this->id ).'
				ORDER BY A.libelle ASC;'
			);
			$this->users = $requete->fetchAll();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'affectation';
			}
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
		}
		finally {
			return $this->users;	
		}
	}
	
	public function get_affectation_dispo() {
		try {
			$requete = $this->bdd->query('
				SELECT
					A.id_affectation AS id,
					A.libelle AS nom,
					IF( UA.id_affectation IS NULL, 0, 1 ) AS active
				FROM
					'.DBPREF.'affectation A
						LEFT JOIN '.DBPREF.'utilisateur_affectation UA
							ON UA.id_affectation = A.id_affectation
							AND UA.id_utilisateur = '.intval( $_GET['parent_id'] ).'
				ORDER BY A.libelle ASC;'
			);
			$this->users = $requete->fetchAll();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'affectation';
			}
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
		}
		finally {
			return $this->users;	
		}
	}
	
	public function set_affectation( $data ) {
		try {	
			$utilisateur = $data['id'];
			
			$this->bdd->query( '
				DELETE FROM '.DBPREF.'utilisateur_affectation
				WHERE id_utilisateur = '.intval($utilisateur).';'
			);
			
			if( isset( $data['affectation'] ) ) {
				$requete = $this->bdd->prepare('
					INSERT INTO '.DBPREF.'utilisateur_affectation ( id_utilisateur, id_affectation )
					VALUES ( ?, ? );'
				);
				foreach( $data['affectation'] as $affectation ) {
					$requete->execute( [ $utilisateur, $affectation ] );
					$requete->closeCursor();
				}
			}
			$this->manager->setMessage( 'Les affectations ont bien été mis à jour pour cet utilisateur' );
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'groupe';
			}
			$this->manager->setError( sprintf( M_RELNEWERR, $msg, 'accès' ) );
		}
	}
	
	public function del_affectation( $data ) {
		try {
			$requete = $this->bdd->prepare('
				DELETE FROM '.DBPREF.'utilisateur_affectation
				WHERE id_utilisateur = ?
				AND id_affectation = ?;'
			);
			$requete->execute( array( $data['id'], $data['rel_id'] ) );
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			$this->manager->setError( sprintf( M_RELDELERR, 'utilisateur', 'affectation' ) );
		}
	}
	
	public function valideEmailUser( $identifiant, $token ) {
		try {
			$requete = $this->bdd->prepare('
				UPDATE '.DBPREF.'utilisateur
				SET
					valide = 1,
					token = NULL
				WHERE
					identifiant = :identifiant
					AND token = :token;'
			);
			$requete->execute( array(
				':identifiant' => $identifiant,
				':token' => $token
			));
			$valide = $requete->rowCount();
			$requete->closeCursor();
			
			if( $valide ) {
				$this->manager->setMessage( M_VALEMAILOK );
			} else {
				$this->manager->setError( M_TOKENERR );
			}
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = $manager->getOption('sitemail');
			}
			$this->manager->setError( sprintf( M_VALEMAILERR, $manager->getOption('sitemail'), $msg ) );
		}
	}
}
