<?php
	$model_colorscheme = (object) array(
		'itemName' => 'colorscheme',
		'table' => DBPREF.'colorscheme',
		'single' => 'code couleur',
		'plural' => 'codes couleur',
		'orderby' => 'libelle',
		'columns' => array(
			(object) array(
				'name' => 'id_colorscheme',
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
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
			(object) array(
				'name' => 'dark',
				'nicename' => 'Texte foncé',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => true
			),
		)
	);
?>