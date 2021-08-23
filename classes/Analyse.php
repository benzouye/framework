<?php
class analyse extends Model {
	
	public function get_item() {
		$retour = array();
		try {
			$requete = $this->bdd->query('
				SELECT I.id_item, I.alias, I.nom, IFNULL(A.id_analyse,0) AS visible
				FROM '.DBPREF.'item I
					LEFT JOIN '.DBPREF.'analyse_item A
						ON I.alias = A.alias
						AND A.id_analyse = '.$this->id.'
				WHERE
					I.admin = 0
					AND I.alias NOT IN ( "home", "logout" )
				ORDER BY I.nom ASC;'
			);
			$retour = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'item analyse';
			}
			$this->manager->setError( sprintf( M_ITEMSERR, $msg ) );
		}
		finally {
			return $retour;	
		}
	}
	
	public function get_affectation() {
		try {
			$requete = $this->bdd->query('
				SELECT A.*
				FROM '.DBPREF.'analyse_affectation UA
					INNER JOIN '.DBPREF.'affectation A
						ON UA.id_affectation = A.id_affectation
				WHERE UA.id_analyse = '.intval( $this->id ).'
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
						LEFT JOIN '.DBPREF.'analyse_affectation UA
							ON UA.id_affectation = A.id_affectation
							AND UA.id_analyse = '.intval( $_GET['parent_id'] ).'
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
			$analyse = $data['id'];
			
			$this->bdd->query( '
				DELETE FROM '.DBPREF.'analyse_affectation
				WHERE id_analyse = '.intval($analyse).';'
			);
			
			if( isset( $data['affectation'] ) ) {
				$requete = $this->bdd->prepare('
					INSERT INTO '.DBPREF.'analyse_affectation ( id_analyse, id_affectation )
					VALUES ( ?, ? );'
				);
				foreach( $data['affectation'] as $affectation ) {
					$requete->execute( [ $analyse, $affectation ] );
					$requete->closeCursor();
				}
			}
			$this->manager->setMessage( 'Les affectations ont bien été mis à jour pour cette analyse' );
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
				DELETE FROM '.DBPREF.'analyse_affectation
				WHERE id_analyse = ?
				AND id_affectation = ?;'
			);
			$requete->execute( array( $data['id'], $data['rel_id'] ) );
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			$this->manager->setError( sprintf( M_RELDELERR, 'analyse', 'affectation' ) );
		}
	}
	
	public function set_item( $data ) {
		try {	
			$analyse = $data['id'];
			
			$this->bdd->query( '
				DELETE FROM '.DBPREF.'analyse_item
				WHERE id_analyse = '.intval($analyse).';'
			);
			
			if( isset( $data['alias'] ) ) {
				$values = '';
				foreach( $data['alias'] as $alias ) {
					$values .= '('.intval($analyse).','.$this->bdd->quote($alias).'),';
				}
				$values = rtrim( $values, ',' );
				
				$requete = $this->bdd->query('
					INSERT INTO '.DBPREF.'analyse_item ( `id_analyse`, `alias` )
					VALUES '.$values.';'
				);
				$requete->closeCursor();
			}
			$this->manager->setMessage( 'La visibilité a bien été mis à jour pour cette analyse' );
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'analyse';
			}
			$this->manager->setError( sprintf( M_RELNEWERR, $msg, 'item' ) );
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
	
	public function getDashboardHTML( $element ) {
		$html = '';
		$classeTable = 'table table-sm table-striped table-hover table-bordered';
		$idTable = 'tableau-'.$element->id_analyse;
		
		$requete = $this->getItem( $element->id_analyse );
		$datas = $this->getDatas();
		$nbElements = count( $datas );
		
		if( $nbElements ) {
			switch( $element->id_type_analyse ) {
				case 1 :
					$table = new SimpleTable( $datas, $element->indicator, $element->percent );
					$html = $table->getHtml( $idTable, $classeTable );
					break;
				case 2 :
					$table = new PivotTable( $datas, $element->colonne, $element->ligne, $element->indicator );
					$html = $table->getHtml( $idTable, $classeTable );
					break;
				case 3 :
					$html = $element->indicator.' : '.$datas[0]->{$element->indicator};
					break;
				case 4 :
					$html = '<canvas data-analyse="'.$element->id_analyse.'" class="homepage-chart"></canvas>';
					break;
				case 5 :
					$html = '<div class="leaflet-display" id="leaflet-'.$element->id_analyse.'" data-analyse="'.$element->id_analyse.'"></div>';
					break;
				case 6 :
					$html = '<div class="homepage-calendar" id="calendar-'.$element->id_analyse.'" data-analyse="'.$element->id_analyse.'"></div>';
					break;
			}
					
		} else {
			$html = '<p><em>Aucune données à afficher</em></p>';
		}
		
		$html = '
			<div class="col-12 col-md-6 col-xl-'.$requete->grid.'">
				<div class="card card-dark border-dark">
					<div class="card-header">
						<span class="card-title">'.$requete->description.'</span> <a title="Voir les données ('.$nbElements.')" data-bs-toggle="Voir les données" data-bs-placement="top" href="index.php?item=analyse&action=extract&id='.$element->id_analyse.'" class="btn btn-light btn-sm float-end"><span class="bi bi-search"></span></a>
					</div>
					<div class="card-body">
						'.$html.'
					</div>
				</div>
			</div>';
		
		return $html;
	}
}
