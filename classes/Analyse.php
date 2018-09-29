<?php
class analyse extends Model {
	
	public function setItem( $data, $register = false ) {
		parent::setItem( $data, $register );
	}
	
	public function getTypeAnalyse() {
		$type = '';
		try {
			$requete = $this->bdd->query('
				SELECT TA.classe
				FROM '.DBPREF.'type_analyse TA
					INNER JOIN '.DBPREF.'analyse A
						ON TA.id_type_analyse = A.id_type_analyse
				WHERE A.id_analyse = '.$this->id
			);
			$type = $requete->fetchColumn();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'type analyse';
			}
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
		}
		finally {
			return $type;	
		}
	}
	
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