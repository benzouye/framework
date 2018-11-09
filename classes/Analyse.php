<?php
class analyse extends Model {
	
	public function getDatas() {
		$datas = array();
		try {
			$requete = $this->bdd->query( $this->currentItem->requete );
			$datas = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'extraction analyse';
			}
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
		}
		finally {
			return $datas;	
		}
	}
}
?>
