<?php
	$nbItems = count( $items );
	
	if( $nbItems > 0 ) {
		$pdf->StartPageGroup();
		$pdf->AddPage('L');
		$pdf->SetTextColor( 0, 0, 0 );
		$pdf->SetFillColor( 255, 255, 255 );
		printHeaderList( $pdf, $object, $nbItems );
		
		$nbColonnes = count( $colonnes );
		$largeur = 277/$nbColonnes;
		$hauteur = 5;
		$maxHauteur = 0;
		
		$cpt = 0;
		$pdf->SetFont( 'Arial','B', 12 );
		foreach( $colonnes as $colonne ) {
			$pdf->setXY( 10 + ( $largeur*$cpt ), 20 );
			$pdf->MultiCell( $largeur, $hauteur, utf8_decode( $colonne->nicename ), 0, 'C' );
			$maxHauteur = $pdf->getY() > $maxHauteur ? $pdf->getY() : $maxHauteur;
			$cpt++;
		}
		
		$cpt = 0;
		$pdf->rect( 10, 20, 277, $maxHauteur );
		foreach( $colonnes as $colonne ) {
			$pdf->rect( 10 + ( $largeur*$cpt ), 20, $largeur, $maxHauteur );
			$cpt++;
		}
		
		$pdf->setXY( 10, $maxHauteur );
		$pdf->SetFont( 'Arial','', 10 );
		foreach( $items as $element ) {
			foreach( $colonnes as $colonne ) {
				$object->displayFieldPDF( $colonne->name, $element->{$colonne->name}, $pdf, $largeur, $hauteur );
			}
			$pdf->ln();
		}
	}
?>
