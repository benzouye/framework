<?php
	$model_utilisateur = (object) array(
		'itemName' => 'utilisateur',
		'table' => DBPREF.'utilisateur',
		'single' => 'utilisateur',
		'plural' => 'utilisateurs',
		'orderby' => 'identifiant',
		'relations' => array(
			(object) array(
				'item' => 'affectation',
				'name' => 'Affectations associées',
				'grid' => 6,
				'static' => false,
				'standard' => true,
				'many' => true
			),
		),
		'columns' => array(
			(object) array(
				'name' => 'id_utilisateur',
				'nicename' => 'ID',
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
				'name' => 'identifiant',
				'nicename' => 'Identifiant',
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
				'name' => 'password',
				'nicename' => 'Mot de passe',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'password' ),
				'visible' => false,
				'editable' => true,
				'required' => true,
				'admin' => false
			),
			(object) array(
				'name' => 'email',
				'nicename' => 'Courriel',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'email' ),
				'visible' => true,
				'editable' => true,
				'required' => true,
				'admin' => false
			),
			(object) array(
				'name' => 'admin',
				'nicename' => 'Administrateur ?',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => false,
				'admin' => true
			),
			(object) array(
				'name' => 'id_groupe',
				'nicename' => 'Groupe',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array(
					'type' => 'select',
					'item' => 'groupe',
					'columnKey' => 'id_groupe',
					'columnLabel' => 'nom_groupe'
				),
				'visible' => true,
				'editable' => true,
				'required' => true,
				'admin' => true
			),
			(object) array(
				'name' => 'token',
				'nicename' => 'Jeton de validation par email',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 4,
				),
				'params' => array( 'type' => 'text' ),
				'visible' => false,
				'editable' => false,
				'required' => false,
				'admin' => true
			),
			(object) array(
				'name' => 'valide',
				'nicename' => 'Email validé ?',
				'align' => 'center',
				'grid' => (object) array(
					'div' => 12,
					'label' => 2,
					'value' => 1,
				),
				'params' => array( 'type' => 'checkbox' ),
				'visible' => true,
				'editable' => true,
				'required' => false,
				'admin' => true
			),
		),
	);
