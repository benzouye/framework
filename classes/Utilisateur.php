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
?>