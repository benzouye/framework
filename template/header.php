<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="<?php echo $manager->getOption('sitedesc'); ?>" />
		<meta name="keywords" content="<?php echo $manager->getOption('keywords'); ?>" />
		
		<title><?php echo $title; ?></title>
		
		<link rel="icon" href="<?php echo SITEURL; ?>assets/img/favicon.ico" />
		<link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/bootstrap.css">
		<link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/bootstrap-colorpicker.min.css" />
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
		<link rel='stylesheet' href="<?php echo SITEURL; ?>assets/css/fullcalendar.min.css" />
		<link rel='stylesheet' href="<?php echo SITEURL; ?>assets/css/scheduler.min.css" />
		<link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/leaflet.css">
		<link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/fontawesome.css">
		<link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/style.css">
	</head>
	<body>
		<div class="wrapper">
			<nav id="sidebar" class="sticky-top bg-dark no-print">
				<div class="sidebar-header">
					<h3><?php echo $manager->getOption('sitetitle'); ?></h3>
				</div>

				<ul class="list-unstyled components">
<?php
	if( $user ) {
		$menus = $manager->getMenu(1);
		foreach( $menus as $menu ) {
			$liClass = $page->alias == $menu->alias ? 'class="active" ' : '';
?>
					<li <?php echo $liClass; ?>><a href="<?php echo SITEURL.'index.php?item='.$menu->alias; ?>"><i class="fas fa-<?php echo $menu->icon; ?>"></i> <?php echo $menu->nom; ?></a></li>
<?php
		}
	}
?>
					<li id="sidebarButton">
						<a class="btn btn-dark btn-sm" title="Fermer le menu"><span class="fas fa-backspace"></span></a>
					</li>
				</ul>
			</nav>
			<main class="bg-light">
<?php
	$description = '';
	if( !$static ) {
		$badgeAction = '';
		if( $readOnly ) {
			$badgeAction = 'Consultation';
		} else {
			$badgeAction = ( $new && $action == 'edit' ) ? 'Création' : $actions[$action];
		}
		$description = ' <span class="badge badge-light">'.$badgeAction.'</span>';
	}
	
	if( $user ) {
?>

				<nav class="sticky-top navbar navbar-expand navbar-dark bg-dark no-print">
					<span class="navbar-brand"><i class="fas fa-<?php echo $page->icon; ?>"></i> <?php echo $page->nom; ?> <?php echo $description; ?></span>
					<ul class="nav navbar-nav ml-auto">
						<li id="menuButton">
							<a class="btn btn-light btn-sm" title="Accès au menu"><span class="fas fa-bars"></span></a>
						</li>
						<li class="nav-item">
							<a title="Mon profil" class="nav-link" href="<?php echo SITEURL.'index.php?item=utilisateur&action=edit&id='.$user->id_utilisateur; ?>">Identifiant : <strong><?php echo $user->identifiant; ?></strong></a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" title="Menu configuration" href="#" id="parametersMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-cog"></i></a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="parametersMenu">
<?php
		$menuIds = $manager->getMenuIds();
		foreach( $menuIds as $menuId ) {
			$menus = $manager->getMenu($menuId);
			if( count( $menus ) > 0 ) {
?>
								<div class="dropdown-divider"></div>
<?php
				foreach( $menus as $menu ) {
					$liClass = $page->alias == $menu->alias ? 'active' : '';
?>
								<a class="dropdown-item <?php echo $liClass; ?>" href="<?php echo SITEURL.'index.php?item='.$menu->alias; ?>"><i class="fas fa-sm fa-<?php echo $menu->icon; ?>"></i> <?php echo $menu->nom; ?></a>
<?php
				}
			}
		}
?>
							</div>
						</li>
					</ul>
				</nav>
<?php
	}
?>
				<section class="container-fluid">
					<div id="header" class="no-print">
					</div>
