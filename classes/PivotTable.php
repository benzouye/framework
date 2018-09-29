<?php
class PivotTable {
	
	protected $datas = array();
	protected $column;
	protected $columns = array();
	protected $row;
	protected $rows = array();
	protected $indicator;
	protected $html = '';
	
	/*
	* 
	* Create a pivot table from raw datas
	*
	* @param Array $datas  Array containing datas to process (as objects) into the pivot table
	* @param string $column  String containing the datas property put in columns
	* @param string $row  String containing the datas property put in rows
	* @param string $indicator  String containing the datas property to count and sum
	*/
	public function __construct( $datas, $column, $row, $indicator ) {
		
		$this->column = $column;
		$this->row = $row;
		$this->indicator = $indicator;
		
		// Initialisation des colonnes
		foreach( $datas as $data ) {
			if( !in_array( $data->{$this->column}, $this->columns ) )
				$this->columns[] = $data->{$this->column};
		}
		$this->columns[] = 'TOTAL';
		
		// Initialisation des lignes
		foreach( $datas as $data ) {
			if( !in_array( $data->{$this->row}, $this->rows ) )
				$this->rows[] = $data->{$this->row};
		}
		$this->rows[] = 'TOTAL';
		
		// Initialisation du résultat
		foreach( $this->columns as $column ) {
			foreach( $this->rows as $row ) {
				$this->datas[$row][$column] = array( $this->indicator => 0, 'count' => 0 );
			}
		}
		
		// Remplissage de l'indicateur
		foreach( $datas as $data ) {
			foreach( $this->columns as $column ) {
				if( $data->{$this->column} == $column ) {
					$this->datas['TOTAL'][$column][$this->indicator] += $data->{$this->indicator};
					$this->datas['TOTAL'][$column]['count'] += 1;
				}
				foreach( $this->rows as $row ) {
					if( $data->{$this->column} == $column && $data->{$this->row} == $row ) {
						$this->datas[$row][$column][$this->indicator] += $data->{$this->indicator};
						$this->datas[$row][$column]['count'] += 1;
						$this->datas[$row]['TOTAL'][$this->indicator] += $data->{$this->indicator};
						$this->datas[$row]['TOTAL']['count'] += 1;
					}
				}
			}
		}
		
		// Remplissage du total global
		$totalCount = 0;
		$totalIndicator = 0;
		$totaux = $this->datas['TOTAL'];
		foreach( $totaux as $column => $valeurs ) {
			$totalCount += $valeurs['count'];
			$totalIndicator += $valeurs[$this->indicator];
		}
		$this->datas['TOTAL']['TOTAL']['count'] = $totalCount;
		$this->datas['TOTAL']['TOTAL'][$this->indicator] = $totalIndicator;
	}
	
	/*
	* 
	* Get the processed pivot table
	*
	* @param boolean $json  Wether the result should be JSON formatted or not
	* @return Array
	*/
	public function getDatas( $json = false ) {
		if( $json ) {
			return json_encode( $this->datas );
		} else {
			return $this->datas;
		}
	}
	
	/*
	* 
	* Get HTML code for processed pivot table
	*
	* @param boolean $css  CSS class to add to table markup
	* @return string
	*/
	public function getHtml( $id = 'pivot-table', $css = '' ) {
		$this->html .= '<table id="'.$id.'" class="pivot-table '.$css.'">';
		$this->html .= '<thead>';
		$this->html .= '<tr>';
		$this->html .= '<th>'.$this->column.'</th>';
		foreach( $this->columns as $column ) {
			$this->html .= '<th colspan="2">'.$column.'</th>';
		}
		$this->html .= '</tr>';
		$this->html .= '<tr>';
		$this->html .= '<th>'.$this->row.'</th>';
		foreach( $this->columns as $column ) {
			$this->html .= '<th>Nb.</th><th>'.$this->indicator.'</th>';
		}
		$this->html .= '</tr>';
		$this->html .= '</thead>';
		$this->html .= '<tbody>';
		foreach( $this->datas as $row => $columns ) {
			$tag = $row == 'TOTAL' ? 'th' : 'td';
			$this->html .= '<tr>';
			$this->html .= '<'.$tag.'>'.$row.'</'.$tag.'>';
			foreach( $columns as $column => $value ) {
				$this->html .= '<'.$tag.' class="text-center">'.$value['count'].'</'.$tag.'><'.$tag.' class="text-center">'.$value[$this->indicator].'</'.$tag.'>';
			}
			$this->html .= '</tr>';
		}
		$this->html .= '</tbody>';
		$this->html .= '</table>';
		
		return $this->html;
	}
}
?>