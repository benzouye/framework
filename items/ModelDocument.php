<?php
	$model_document = (object) array(
		'itemName' => 'document',
		'parentItem' => 'inscription',
		'table' => DBPREF.'document',
		'single' => 'document',
		'plural' => 'documents',
		'orderby' => 'date_cre',
		'columns' => array(
			(object) array(
				'name' => 'id_document',
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
				'name' => 'id_inscription',
				'nicename' => 'Inscription',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'number' ),
				'visible' => true,
				'editable' => false,
				'required' => true
			),
			(object) array(
				'name' => 'id_type_document',
				'nicename' => 'Type de document',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array(
					'type' => 'select',
					'item' => 'type_document',
					'columnKey' => 'id_type_document',
					'columnLabel' => 'libelle'
				),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'fichier',
				'nicename' => 'Nom du fichier',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'url' ),
				'visible' => true,
				'editable' => false,
				'required' => false
			),
		)
	);
?>