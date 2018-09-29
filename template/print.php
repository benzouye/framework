<?php
	$nomFichier = 'print_'.$page->alias.'_'.date( 'Ymd_His' ).'.pdf';
	$flagOkDoc = false;
	
	function printHeader( $pdf ) {
		$pdf->Image( SITEURL.'assets/img/logo-flat-white-bg.png', 10, 10, 20 );
		$pdf->SetXY( 38, 10 );
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(3,5,utf8_decode('A'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(34.5,5,utf8_decode('ssociation pour la '),0,0,'L');
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(3,5,utf8_decode('R'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(25,5,utf8_decode('echerche et l\''),0,0,'L');
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(1.5,5,utf8_decode('I'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(27,5,utf8_decode('nformation en '),0,0,'L');
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(3,5,utf8_decode('P'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(20,5,utf8_decode('érinatalité, A.R.I.P.'),0,1,'L');
		$pdf->SetFont( 'Arial','', 8 );
		$pdf->Cell(0,5,utf8_decode('SIEGE SOCIAL MAISON IV DE CHIFFRE - 26 RUE DES TEINTURIERS - 84000 AVIGNON'),0,0,'C');
		$pdf->SetFont( 'Arial','', 10 );
		$pdf->SetXY( 10, 25 );
		$pdf->Cell(0,5, utf8_decode('Toute correspondance doit être envoyée à :'),0,1,'C');
		$pdf->Cell(0,5, utf8_decode('ARIP - C. H. Montfavet - Avenue de la Pinède - CS 20107 - 84918 Avignon cedex 9'),0,1,'C');
		$pdf->Cell(0,5, utf8_decode('Tél. : 04 90 23 99 35 - Fax : 09 70 32 22 01 - arip@wanadoo.fr - www.arip.fr'),0,1,'C');
		$pdf->Cell(0,5, utf8_decode('Déclaration enregistrée sous le N° 93840394284 du préfet de région Provence-Alpes-Côte d\'Azur'),0,1,'C');
		$pdf->SetFont( 'Arial','I', 8 );
		$pdf->Cell(0,15, utf8_decode('Dr Michel Dugnat, Président'),0,0,'L');
		$pdf->Cell(0,15, utf8_decode('Dr Michèle Anicet, Vice-présidente'),0,1,'R');
	}
	
	function printHeaderEmarg( $pdf, $titre = 'EMARGEMENT' ) {
		$pdf->Image( SITEURL.'assets/img/logo-flat-white-bg.png', 10, 10, 20 );
		$pdf->SetXY( 40, 10 );
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(3,5,utf8_decode('A'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(34.5,5,utf8_decode('ssociation pour la '),0,0,'L');
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(3,5,utf8_decode('R'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(25,5,utf8_decode('echerche et l\''),0,0,'L');
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(1.5,5,utf8_decode('I'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(27,5,utf8_decode('nformation en '),0,0,'L');
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->Cell(3,5,utf8_decode('P'),0,0,'L');
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->Cell(20,5,utf8_decode('érinatalité, A.R.I.P.'),0,1,'L');
		$pdf->SetFont( 'Arial','B', 12 );
		$pdf->SetXY( 40, 15 );
		$pdf->Cell(0,5, utf8_decode('Colloque International de Périnatalité - Les 15, 16 et 17 novembre 2018'),0,1,'L');
		$pdf->SetFont( 'Arial','B', 16 );
		$pdf->SetXY( 40, 25 );
		$pdf->Cell(0,7, utf8_decode($titre),1,1,'C');
		$pdf->ln();
	}
	
	function printFooter( $pdf, $date = false ) {
		if( $pdf->GetY() > 230 ) {
			$pdf->AddPage('P');
		}
		$pdf->SetFont( 'Arial','', 12 );	
		$pdf->SetXY(10, -60);
		$pdf->Cell(100,5, utf8_decode( 'Fait à Avignon, le '.( $date ? $date : date('d/m/Y') ) ),0,0,'L');
		$pdf->Cell(0,5, utf8_decode( 'Signature de l\'organisateur :' ),0,1,'L');
		$pdf->Image( SITEURL.'assets/img/signature_courrier.png', 110, 235, 50 );
		$pdf->SetFont( 'Arial','', 10 );
		$pdf->SetXY(10, -30);
		$pdf->Cell(0,4,utf8_decode('ARIP - Non assujettie à la tva - article 293 B du Code général des impôts'),0,1,'C');
		$pdf->Cell(0,4,utf8_decode('Loi 1901 - APE 9499Z - SIRET 401 376 215 00034'),0,1,'C');
	}
	
	function printFooterFc( $pdf ) {
		if( $pdf->GetY() > 230 ) {
			$pdf->AddPage('P');
		}
		$pdf->SetFont( 'Arial','', 12 );
		$pdf->SetXY(10, -70);
		$pdf->Cell(100,5, utf8_decode( 'Fait à Avignon, le '.date('d/m/Y') ),0,1,'L');
		$pdf->ln();
		$pdf->cell(100,5, utf8_decode( 'Signature du responsable de formation continue' ),0,0,'L' );
		$pdf->Cell(0,5, utf8_decode( 'Signature de l\'organisateur :' ),0,1,'L');
		$pdf->Image( SITEURL.'assets/img/signature_courrier.png', 110, 235, 50 );
		$pdf->SetFont( 'Arial','', 10 );
		$pdf->SetXY(10, -29);
		$pdf->Cell(0,4,utf8_decode('ARIP - Non assujettie à la tva - article 293 B du Code général des impôts'),0,1,'C');
		$pdf->Cell(0,4,utf8_decode('Loi 1901 - APE 9499Z - SIRET 401 376 215 00034'),0,0,'C');
	}
	
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
