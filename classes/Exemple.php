<?php
class Exemple extends Model {
	protected $docs = array();
	
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
?>