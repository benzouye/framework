<?php
class SimpleTable {
	
	protected $datas = array();
	protected $headers = array();
	protected $indicator;
	protected $total = 0;
	protected $percent;
	protected $html = '';
	
	/*
	* 
	* Create a pivot table from raw datas
	*
	* @param Array $datas  Array containing datas to process (as objects) into the pivot table
	* @param string $indicator  String containing the datas property to count and sum
	* @param boolean $percent  Wether to add a percent column or not
	*/
	public function __construct( $datas, $indicator, $percent = true ) {
		
		$this->datas = $datas;
		$this->indicator = $indicator;
		$this->percent = $percent;
		
		// Somme de l'indicateur
		foreach( $datas as $data ) {
			$this->total += floatval($data->{$this->indicator});
		}
		
		// Entête de colonne
		foreach( $this->datas[0] as $key => $value ) {
			$this->headers[] = $key;
		}
	}
	
	/*
	* 
	* Get the table datas
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
	* Get HTML code for processed table
	*
	* @param boolean $id  ID of the HTML table element
	* @param boolean $css  CSS class to add to table markup
	* @return string
	*/
	public function getHtml( $id = 'pivot-table', $css = '' ) {
		$this->html .= '<div class="table-responsive"><table id="'.$id.'" class="simple-table '.$css.'">';
		$this->html .= '<thead>';
		
		// Ligne entête
		$this->html .= '<tr>';
		foreach( $this->headers as $header ) {
			$this->html .= '<th>';
			$this->html .= $header;
			$this->html .= '</th>';
		}
		if( $this->percent ) {
			$this->html .= '<th>';
			$this->html .= '%';
			$this->html .= '</th>';
		}
		$this->html .= '</tr>';
		$this->html .= '</thead>';
		$this->html .= '<tbody>';
		
		// Lignes données
		foreach( $this->datas as $data ) {
			$this->html .= '<tr class="text-center">';
			foreach( $this->headers as $column ) {
				$this->html .= '<td>';
				$this->html .= $data->{$column};
				$this->html .= '</td>';
			}
			if( $this->percent ) {
				$this->html .= '<td>';
				$this->html .= round( floatval($data->{$this->indicator}) / $this->total * 100, 2 );
				$this->html .= '</td>';
			}
			$this->html .= '</tr>';
		}
		$this->html .= '</tbody>';
		
		// Ligne total
		$this->html .= '<tfoot>';
		$this->html .= '<tr>';
		$premiere = true;
		foreach( $this->headers as $header ) {
			$this->html .= '<th>';
			if( $premiere ) {
				$this->html .= 'TOTAL';
			}
			if( $header == $this->indicator ) {
				$this->html .= $this->total;
			}
			$this->html .= '</th>';
			$premiere = false;
		}
		if( $this->percent ) {
			$this->html .= '<th>';
			$this->html .= '100';
			$this->html .= '</th>';
		}
		$this->html .= '</tr>';
		$this->html .= '</foot>';
		
		$this->html .= '</table></div>';
		
		return $this->html;
	}
}
?>
