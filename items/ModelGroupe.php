<?php
	$model_groupe = (object) array(
		'itemName' => 'groupe',
		'table' => DBPREF.'groupe',
		'single' => 'groupe',
		'plural' => 'groupes',
		'orderby' => 'nom_groupe',
		'relations' => array(
			(object) array( 'item' => 'access', 'name' => 'Accès', 'grid' => 8, 'static' => true ),
			(object) array( 'item' => 'utilisateur', 'name' => 'Utilisateurs associés', 'grid' => 4, 'static' => true ),
		),
		'columns' => array(
			(object) array(
				'name' => 'id_groupe',
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
				'name' => 'nom_groupe',
				'nicename' => 'Libellé',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			)
		)
	);
?>