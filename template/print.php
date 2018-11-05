<?php
	$nomFichier = 'print_'.$page->alias.'_'.date( 'Ymd_His' ).'.pdf';
	$flagOkDoc = false;
	
	try {
		$pagination = false;
		foreach( $prints as $print ) {
			if( $print->alias == $type ) {
				if( $print->pagination ) {
					$pagination = true;
				}
				break;
			}
		}
		
		if( $pagination ) {
			class PDF extends FPDF {
				function Footer() {
					// Positionnement à 1,5 cm du bas
					$this->SetXY(10, -15);
					// Numéro de page
					$this->SetFont( 'Arial', '', 8 );
					$this->Cell(0,5,utf8_decode('Page '.$this->PageNo().'/{nb}'),0,0,'C');
				}
			}
			$pdf = new PDF();
		} else {
			$pdf = new FPDF();
		}
		$pdf->AliasNbPages();
		$pdf->SetTitle( $title );
		$pdf->SetFont( 'Arial', '', 10 );
		
		// Utilisation du template dédié
		if( file_exists( TEMPLDIR.'print.'.$page->alias.'.'.$type.'.php' ) ) {
			include( TEMPLDIR.'print.'.$page->alias.'.'.$type.'.php' );
		}
		// Ou message d'erreur
		else {
			$pdf->AddPage('P');
			$pdf->MultiCell(190,5,utf8_decode('Le fichier template d\'impression pour print.'.$page->alias.'.'.$type.' n\'existe pas. Contactez l\'administrateur pour faire corriger ce problème. '), 1 );
		}
		
		if( method_exists( $page->alias, 'setDocument' ) ) {
			$flagOkDoc = $object->setDocument( $nomFichier, $type );
		}
		if( $flagOkDoc ) {
			$pdf->Output( 'F', UPLDIR.$nomFichier, true );
		}
		$pdf->Output();
	}
	catch( Exception $e ) {
		if( $manager->getDebug() ) {
			$msg = $e->getMessage();
		} else {
			$msg = M_IMPERR;
		}
		echo $msg;
	}
?>
