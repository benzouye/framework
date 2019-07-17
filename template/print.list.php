<?php
	$nbItems = count( $items );
	
	if( $nbItems > 0 ) {
		$pdf->StartPageGroup();
		$pdf->AddPage('L');
		$pdf->SetTextColor( 0, 0, 0 );
		$pdf->SetFillColor( 255, 255, 255 );
		printHeaderList( $pdf, $object, $nbItems );
		
		$pdf->setXY( 10, 20 );
		$nbColonnes = count( $colonnes );
		$largeur = 277;
		$hauteur = 7;
		
		$pdf->SetFont( 'Arial','B', 12 );
		foreach( $colonnes as $colonne ) {
			$pdf->Cell( 277/$nbColonnes, $hauteur, utf8_decode( $colonne->nicename ), 1, 0, 'C' );
		}
		$pdf->ln();
		
		$pdf->SetFont( 'Arial','', 10 );
		foreach( $items as $element ) {
			foreach( $colonnes as $colonne ) {
				$object->displayFieldPDF( $colonne->name, $element->{$colonne->name}, $pdf, $nbColonnes, $largeur, $hauteur );
			}
			$pdf->Cell( 277/$nbColonnes, $hauteur, '', 0, 1, 'C', 1 );
		}
	}
?>
