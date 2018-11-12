<?php
	$model_analyse = (object) array(
		'itemName' => 'analyse',
		'table' => DBPREF.'analyse',
		'single' => 'analyse',
		'plural' => 'analyses',
		'orderby' => 'ordre',
		'columns' => array(
			(object) array(
				'name' => 'id_analyse',
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
				'name' => 'description',
				'nicename' => 'Description',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 6,
				),
				'params' => array( 'type' => 'textarea' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'requete',
				'nicename' => 'Code SQL',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'textarea', 'rows' => 10 ),
				'visible' => false,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'id_type_analyse',
				'nicename' => 'Type de tableau',
				'align' => 'center',
				'default' => 1,
				'grid' => (object) array(
					'div' => 6,
					'label' => 4,
					'value' => 8,
				),
				'params' => array(
					'type' => 'select',
					'item' => 'type_analyse',
					'columnKey' => 'id_type_analyse',
					'columnLabel' => 'libelle'
				),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'options',
				'nicename' => 'Paramètres JSON',
				'default' => '{ "type": "bar", "backgroundColor" : [ "#aaaaaa" ], "options": { "legend": { "display": false } } }',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'textarea', 'rows' => 2 ),
				'visible' => false,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'indicator',
				'nicename' => 'Champ Indicateur',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => false,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'percent',
				'nicename' => 'Afficher % (si droit)',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => false,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'colonne',
				'nicename' => 'Champ Colonne (si croisé)',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => false,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'ligne',
				'nicename' => 'Champ Ligne (si croisé)',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => false,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'flag_accueil',
				'nicename' => 'Visible sur page d\'accueil',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'ordre',
				'nicename' => 'Ordre',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'number' ),
				'visible' => true,
				'editable' => true,
				'required' => false
			),
			(object) array(
				'name' => 'grid',
				'nicename' => 'Taille de la grille',
				'default' => 6,
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 10,
				),
				'params' => array( 'type' => 'number', 'step' => 1 ),
				'visible' => false,
				'editable' => true,
				'required' => true
			),
		)
	);
?>
