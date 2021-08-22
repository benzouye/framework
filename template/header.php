<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="<?=$manager->getOption('sitedesc'); ?>" />
		<meta name="keywords" content="<?=$manager->getOption('keywords'); ?>" />
		
		<title><?=$title; ?></title>
		
		<link rel="manifest" href="<?=SITEURL; ?>manifest.webmanifest">
		<link rel="icon" href="<?=SITEURL; ?>assets/img/favicon.ico" />
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
		<link rel='stylesheet' href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.9.0/main.min.css" />
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
		<link rel="stylesheet" href="<?=SITEURL; ?>assets/css/style.css">
	</head>
	<body>
		<div class="container-fluid">
			<div class="row min-vh-100 flex-column flex-xl-row">
				<aside class="col-12 col-xl-2 p-0 bg-dark flex-shrink-1">
					<nav id="sidemenu" class="sticky-top navbar navbar-expand navbar-dark bg-dark flex-xl-column flex-row flex-wrap align-items-start py-2">
						<span class="navbar-brand mx-3">
							<i class="me-2 bi bi-<?= $manager->getOption('siteicon'); ?>"></i> <?= $manager->getOption('sitetitle'); ?>
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
								<li class="nav-item">
									<a title="<?= $menu->nom; ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" class="nav-link <?= $liClass; ?>" href="<?= SITEURL.'index.php?item='.$menu->alias; ?>">
										<i class="bi bi-<?= $menu->icon; ?>"></i> <span class="d-none d-xl-inline"><?= $menu->nom; ?></span>
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
		$description = ' <span class="badge bg-dark">'.$badgeAction.'</span>';
	}
	
	if( $user ) {
?>

				<nav id="topmenu" class="ps-3 sticky-top navbar navbar-expand navbar-light bg-light no-print">
					<span class="navbar-brand"><i class="me-2 bi bi-<?= $page->icon; ?>"></i> <?= $page->nom; ?> <?= $description; ?></span>
					<div class="nav navbar-nav ms-auto pe-2 fs-5">
<?php
		$menuCount = 0;
		$menuIds = $manager->getMenuIds();
		foreach( $menuIds as $menuId ) {
			$menus = $manager->getMenu($menuId);
			$menuCount += count( $menus );
		}
		if( $menuCount > 0 ) {
?>
						<div class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" title="Menu configuration" data-bs-placement="bottom" href="#" id="parametersMenu" aria-haspopup="true" aria-expanded="false"><i class="bi bi-gear"></i></a>
							<div class="dropdown-menu dropdown-menu-end" aria-labelledby="parametersMenu">
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
								<a class="dropdown-item <?= $liClass; ?>" href="<?= SITEURL.'index.php?item='.$menu->alias; ?>"><i class="bi bi-<?= $menu->icon; ?>"></i> <?= $menu->nom; ?></a>
<?php
						}
					}
				}
				$premierMenu = false;
			}
?>
							</div>
						</div>
<?php
		}
?>
						<div class="nav-item">
							<a title="Mon profil" data-bs-toggle="tooltip" data-bs-placement="bottom" class="nav-link" href="<?= SITEURL.'index.php?item=utilisateur&action=edit&id='.$user->id_utilisateur; ?>"><span class="bi bi-person-circle"></span></a>
						</div>
						<div class="nav-item me-3">
							<a title="Quitter" data-bs-toggle="tooltip" data-bs-placement="bottom" class="nav-link" href="<?= SITEURL.'index.php?item=logout'; ?>"><span class="bi bi-power"></span></a>
						</div>
					</div>
				</nav>
<?php
	}
?>
				<section class="container-fluid">
					<div id="header" class="no-print">
					</div>
