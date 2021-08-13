<?php
class Exemple extends Model {
	protected $docs = array();
	protected $events = array();
	
	public function get_scheduler() {
		try {
			$requete = $this->bdd->query('
				SELECT
					E.id_exemple AS id,
					E.libelle AS title,
					E.date_cre AS start,
					E.date_maj AS end,
					CONCAT( "'.SITEURL.'index.php?item=exemple&action=edit&id=", E.id_exemple ) AS url
				FROM '.DBPREF.'exemple E
				ORDER BY E.date_cre ASC;'
			);
			$this->events = $requete->fetchAll();
		}
		catch( Exception $e ) {
			$this->events = [ "message" => $e->getMessage() ];
		}
		finally {
			return $this->events;
		}
	}
	
	public function get_document() {
		try {
			$requete = $this->bdd->prepare('
				SELECT D.id_document, D.fichier, TD.libelle AS type_document
				FROM '.DBPREF.'document D
				INNER JOIN '.DBPREF.'type_document TD
				ON D.id_type_document = TD.id_type_document
				WHERE id_exemple = ?
				ORDER BY D.date_cre DESC;'
			);
			$requete->execute( array( $this->id ) );
			$this->docs = $requete->fetchAll();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'documents';
			}
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
		}
		finally {
			return $this->docs;	
		}
	}
}
