<?php
	$model_exemple = (object) array(
		'itemName' => 'exemple',
		'table' => DBPREF.'exemple',
		'single' => 'exemple',
		'plural' => 'exemples',
		'orderby' => 'T.date_cre DESC',
		'defaultFilters' => array(
			'id_etat' => 1
		),
		'readOnlyStates' => array(
			(object) array(
				'column' => 'id_etat',
				'values' => array( 3 ),
			),
		),
		'relations' => array(
			(object) array(
				'item' => 'document',
				'name' => 'Document',
				'grid' => 6,
				'static' => false,
			),
		),
		'columns' => array(
			(object) array(
				'name' => 'id_exemple',
				'nicename' => 'ID',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'number', 'step' => 1 ),
				'visible' => false,
				'editable' => false,
				'required' => false
			),
			(object) array(
				'name' => 'libelle',
				'nicename' => 'Libellé',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'auto-complete' => true, 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'montant',
				'nicename' => 'Montant',
				'align' => 'right',
				'unit' => '€',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 2,
				),
				'params' => array( 'type' => 'number', 'step' => 0.01 ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'image',
				'nicename' => 'Image',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'image', 'extensions' => array( 'jpg', 'jpeg', 'png', 'gif', 'svg' ) ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'fichier',
				'nicename' => 'Fichier',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'file', 'extensions' => array( 'pdf', 'doc', 'xls', 'ppt', 'odt', 'ods', 'odp' ) ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'localisation',
				'nicename' => 'Localisation',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'localisation' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'id_etat',
				'nicename' => 'Etat',
				'align' => 'center',
				'default' => 1,
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 2,
				),
				'params' => array(
					'type' => 'select',
					'item' => 'etat',
					'columnKey' => 'id_etat',
					'columnLabel' => 'libelle'
				),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'doc_count',
				'nicename' => 'Nb. docs.',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array(
					'type' => 'calculation',
					'function' => 'IF( D.id_exemple IS NULL, 0, COUNT(*) )',
					'value' => '*',
					'join' => ' LEFT JOIN '.DBPREF.'document D ON T.id_exemple = D.id_exemple ',
				),
				'visible' => true,
				'editable' => false,
				'required' => false
			),
		),
	);
?>
