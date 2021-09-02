<?php
	$model_etat = (object) array(
		'itemName' => 'etat',
		'table' => DBPREF.'etat',
		'single' => 'état',
		'plural' => 'états',
		'orderby' => 'libelle',
		'columns' => array(
			(object) array(
				'name' => 'id_etat',
				'nicename' => 'ID',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'number', 'step' => 1 ),
				'visible' => false,
				'editable' => false,
				'required' => false,
				'admin' => false
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
				'required' => true,
				'admin' => false
			),
			(object) array(
				'name' => 'id_colorscheme',
				'nicename' => 'Couleur',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 2,
				),
				'params' => array(
					'type' => 'select',
					'item' => 'colorscheme',
					'columnKey' => 'id_colorscheme',
					'columnLabel' => 'libelle'
				),
				'visible' => true,
				'editable' => true,
				'required' => true,
				'admin' => false
			),
		)
	);
