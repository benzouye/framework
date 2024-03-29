<?php
	$nomFichier = 'print_'.$page->alias.'_'.date( 'Ymd_His' ).'.pdf';
	$flagOkDoc = false;
	
	try {
		$pagination = ( $type == 'list' );
		foreach( $prints as $print ) {
			if( $print->alias == $type ) {
				if( $print->pagination ) {
					$pagination = true;
				}
				break;
			}
		}
	
		function printHeaderList( $pdf, $object, $nb ) {
			$pdf->SetFont( 'Arial','B', 16 );
			$pdf->SetXY( 10, 10 );
			$pdf->Cell(277,7, 'Liste de résultats : '.$nb.' '.( $nb > 1 ? $object->getPlural() : $object->getSingle() ),1,1,'C');
			$pdf->ln(3);
		}
		
		if( $pagination ) {
			class PDF extends tFPDF {
				
				protected $NewPageGroup;
				protected $PageGroups = [];
				protected $CurrPageGroup;	
				
				function Footer() {
					// Positionnement à 1,5 cm du bas
					$this->SetXY(10, -15);
					// Numéro de page
					$this->SetFont( 'Arial', '', 8 );
					$this->Cell(0,5,$this->GroupPageNo().'/'.$this->PageGroupAlias(),0,0,'C');
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
						$n = count($this->PageGroups)+1;
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
								$this->pages[$n] = str_replace(
									$this->UTF8ToUTF16BE( $k, false),
									$this->UTF8ToUTF16BE( $v, false),
									$this->pages[$n]
								);
							}
						}
					}
					parent::_putpages();
				}
			}
			$pdf = new PDF();
		} else {
			$pdf = new tFPDF();
		}
		$pdf->AddFont( 'Arial','','arial.ttf',true);
		$pdf->AddFont( 'Arial','B','arialbd.ttf',true);
		$pdf->AddFont( 'Arial','I','ariali.ttf',true);
		$pdf->AddFont( 'Arial','BI','arialbi.ttf',true);
		$pdf->AliasNbPages();
		$pdf->SetTitle( $title );
		$pdf->SetFont( 'Arial', '', 10 );
		
		// Utilisation du template dédié
		if( file_exists( TEMPLDIR.'print.'.$page->alias.'.'.$type.'.php' ) ) {
			$fichier = TEMPLDIR.'print.'.$page->alias.'.'.$type.'.php';
			include( $fichier );
		
			if( method_exists( $page->alias, 'setDocument' ) ) {
				$flagOkDoc = $object->setDocument( $nomFichier, $type );
			}
			if( $flagOkDoc ) {
				$pdf->Output( 'F', UPLDIR.$nomFichier, true );
			}
		}
		// Ou du template général
		elseif( file_exists( TEMPLDIR.'print.'.$type.'.php' ) ) {
			$fichier = TEMPLDIR.'print.'.$type.'.php';
			include( $fichier );
		}
		// Ou message d'erreur
		else {
			$fichier = $action.'.'.$page->alias.'.'.$type;
			$pdf->AddPage('P');
			$pdf->MultiCell(190,5,'Le fichier template d\'impression '.$fichier.' n\'existe pas. Contactez l\'administrateur pour faire corriger ce problème.', 1 );
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
