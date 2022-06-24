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
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
		}
		finally {
			return $retour;	
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
			$this->manager->setMessage( sprintf( M_RELNEWERR, $msg, 'item' ) ,true);
		}
	}
	
	public function getDatas() {
		$datas = array();
		try {
			$sql = $this->currentItem->requete;
			$where = '';
			$endFrom = min(
				strpos( $sql, 'GROUP BY' ) ? strpos( $sql, 'GROUP BY' ) : 9999,
				strpos( $sql, 'ORDER BY' ) ? strpos( $sql, 'ORDER BY' ) : 9999
			);
			
			$select = substr( $sql, 0, strpos( $sql, 'FROM' )-1 );
			$from = substr( $sql, strpos( $sql, 'FROM' ), $endFrom-strpos( $sql, 'FROM' )-1 );
			$endSql = substr( $sql, $endFrom );
			
			if( $this->currentItem->flag_affect ) {
				$where = strpos( $sql, 'WHERE' ) ? ' AND ' : ' WHERE ';
				$where .= ( $this->currentItem->alias_affect ? $this->currentItem->alias_affect.'.' : '' ).'id_affectation';
				$affectations = $this->manager->getAffectations();
				$tabAffectations = array();
				foreach( $affectations as $affectation ) {
					array_push( $tabAffectations, $affectation->id_affectation );
				}
				$where .= count( $tabAffectations ) ? ' IN ( '.implode( ",", $tabAffectations ).' ) ' : ' = 0 ';
			}
			
			$sql = $select.$from.$where.$endSql;
			
			if( strpos( $sql, '$$affectation$$' ) ) {
				$sql = str_replace( '$$affectation$$', $where, $sql );
			}
			
			$requete = $this->bdd->query( $sql );
			$datas = $requete->fetchAll();
			$requete->closeCursor();
		}
		catch( Exception $e ) {
			if( $this->manager->getDebug() ) {
				$msg = $e->getMessage();
			} else {
				$msg = 'extraction analyse';
			}
			$this->manager->setMessage( sprintf( M_ITEMSERR, $msg ) ,true);
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
					$table = new PivotTable( $datas, $element->colonne, $element->ligne, $element->indicator, $element->comptage );
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
				<div class="card card-dark border-'.$this->manager->getOption('colorschema').'">
					<div class="card-header bg-'.$this->manager->getOption('colorschema').'">
						<span class="card-title">'.$requete->description.'</span> <a title="Voir les données ('.$nbElements.')" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=analyse&action=extract&id='.$element->id_analyse.'" class="btn btn-light btn-sm float-end"><span class="bi bi-search"></span></a>
					</div>
					<div class="card-body">
						'.$html.'
					</div>
				</div>
			</div>';
		
		return $html;
	}
}
