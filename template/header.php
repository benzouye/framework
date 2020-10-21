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
		<div class="container-fluid">
			<div class="row min-vh-100 flex-column flex-xl-row">
				<aside class="col-12 col-xl-2 p-0 bg-dark flex-shrink-1">
					<nav class="navbar navbar-expand navbar-dark bg-dark flex-xl-column flex-row align-items-start py-2">
						<span class="navbar-brand">
							<i class="fas fa-<?= $manager->getOption('siteicon'); ?> fa-fw"></i> <?= $manager->getOption('sitetitle'); ?>
						</span>
						<div class="collapse navbar-collapse ">
							<ul class="flex-xl-column flex-row navbar-nav w-100 justify-content-start">
<?php
	if( $user ) {
		$menus = $manager->getMenu(1);
		foreach( $menus as $menu ) {
			$flagUserCan = false;
			foreach( $userCaps as $cap ) {
				if( $cap->alias == $menu->alias && $cap->access > 0 ) $flagUserCan = true;
			}
			if( $flagUserCan ) {
				$liClass = $page->alias == $menu->alias ? 'active' : '';
?>
								<li class="nav-item <?= $liClass; ?>">
									<a title="<?= $menu->nom; ?>" data-toggle="tooltip" class="nav-link" href="<?= SITEURL.'index.php?item='.$menu->alias; ?>">
										<i class="fas fa-<?= $menu->icon; ?> fa-fw"></i> <span class="d-none d-xl-inline"><?= $menu->nom; ?></span>
									</a>
								</li>
<?php
			}
		}
	}
?>
							</ul>
						</div>
					</nav>
				</aside>
				<main class="col-12 col-xl-10 p-0 bg-faded flex-grow-1">
<?php
	$description = '';
	if( !$static ) {
		$badgeAction = '';
		if( $readOnly ) {
			$badgeAction = 'Consultation';
		} else {
			$badgeAction = ( $new && $action == 'edit' ) ? 'Création' : $actions[$action];
		}
		$description = ' <span class="badge badge-dark">'.$badgeAction.'</span>';
	}
	
	if( $user ) {
?>

				<nav class="sticky-top navbar navbar-expand navbar-light bg-light no-print">
					<span class="navbar-brand"><i class="fas fa-<?= $page->icon; ?>"></i> <?= $page->nom; ?> <?= $description; ?></span>
					<ul class="nav navbar-nav ml-auto">
<?php
		$menuCount = 0;
		$menuIds = $manager->getMenuIds();
		foreach( $menuIds as $menuId ) {
			$menus = $manager->getMenu($menuId);
			$menuCount += count( $menus );
		}
		if( $menuCount > 0 ) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" title="Menu configuration" href="#" id="parametersMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-cog"></i></a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="parametersMenu">
<?php
			$premierMenu = true;
			foreach( $menuIds as $menuId ) {
				$menus = $manager->getMenu($menuId);
				if( count( $menus ) > 0 ) {
					if( !$premierMenu ) {
?>
								<div class="dropdown-divider"></div>
<?php
					}
					foreach( $menus as $menu ) {
						$flagUserCan = false;
						foreach( $userCaps as $cap ) {
							if( $cap->alias == $menu->alias && $cap->access > 0 ) $flagUserCan = true;
						}
						if( $flagUserCan ) {
							$liClass = $page->alias == $menu->alias ? 'active' : '';
?>
								<a class="dropdown-item <?= $liClass; ?>" href="<?= SITEURL.'index.php?item='.$menu->alias; ?>"><i class="fas fa-sm fa-<?= $menu->icon; ?>"></i> <?= $menu->nom; ?></a>
<?php
						}
					}
				}
				$premierMenu = false;
			}
?>
							</div>
						</li>
<?php
		}
?>
						<li class="nav-item">
							<a title="Mon profil" class="nav-link" href="<?= SITEURL.'index.php?item=utilisateur&action=edit&id='.$user->id_utilisateur; ?>"><span class="fas fa-user"></span></a>
						</li>
						<li class="nav-item">
							<a title="Se déconnecter" class="nav-link" href="<?= SITEURL.'index.php?item=logout'; ?>"><span class="fas fa-power-off"></span></a>
						</li>
					</ul>
				</nav>
<?php
	}
?>
				<section class="container-fluid">
					<div id="header" class="no-print">
					</div>
