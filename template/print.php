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
				
				protected $NewPageGroup;
				protected $PageGroups;
				protected $CurrPageGroup;	
				
				function Footer() {
					// Positionnement à 1,5 cm du bas
					$this->SetXY(10, -15);
					// Numéro de page
					$this->SetFont( 'Arial', '', 8 );
					$this->Cell(0,5,utf8_decode( $this->GroupPageNo().'/'.$this->PageGroupAlias() ),0,0,'C');
				}
				
				function StartPageGroup()
				{
					$this->NewPageGroup = true;
				}
				
				function GroupPageNo()
				{
					return $this->PageGroups[$this->CurrPageGroup];
				}
				
				function PageGroupAlias()
				{
					return $this->CurrPageGroup;
				}

				function _beginpage($orientation, $format, $rotation)
				{
					parent::_beginpage($orientation, $format, $rotation);
					if($this->NewPageGroup)
					{
						// start a new group
						$n = sizeof($this->PageGroups)+1;
						$alias = "{nb$n}";
						$this->PageGroups[$alias] = 1;
						$this->CurrPageGroup = $alias;
						$this->NewPageGroup = false;
					}
					elseif($this->CurrPageGroup)
						$this->PageGroups[$this->CurrPageGroup]++;
				}

				function _putpages()
				{
					$nb = $this->page;
					if (!empty($this->PageGroups))
					{
						// do page number replacement
						foreach ($this->PageGroups as $k => $v)
						{
							for ($n = 1; $n <= $nb; $n++)
							{
								$this->pages[$n] = str_replace($k, $v, $this->pages[$n]);
							}
						}
					}
					parent::_putpages();
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
