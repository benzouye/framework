<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="<?php echo $manager->getOption('sitedesc'); ?>" />
		<meta name="keywords" content="<?php echo $manager->getOption('keywords'); ?>" />
		
		<title><?php echo $title; ?></title>
		
		<link rel="icon" href="<?php echo SITEURL; ?>assets/img/favicon.ico" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" />
		<link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/bootstrap-colorpicker.min.css" />
		<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
		<link rel='stylesheet' href="<?php echo SITEURL; ?>assets/css/fullcalendar.min.css" />
		<link rel='stylesheet' href="<?php echo SITEURL; ?>assets/css/scheduler.min.css" />
		<link rel="stylesheet" href="<?php echo SITEURL; ?>assets/css/main.css" />
	</head>
	<body>
		<div id="wrapper" class="container panel panel-default">
			<nav class="navbar navbar-default navbar-fixed-top">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mvc-navbar" aria-expanded="false">
							<span class="sr-only"><?php echo gettext( 'Afficher/Masquer le menu' ); ?></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<span class="navbar-brand"><span class="glyphicon glyphicon-<?php echo $manager->getOption('siteicon'); ?>"></span> <a href="<?php echo SITEURL; ?>" title="Accueil du site"><?php echo $manager->getOption('sitetitle');; ?></a></span>
					</div>
					<div class="collapse navbar-collapse" id="mvc-navbar">
<?php
	if( $user ) {
?>
						<ul class="nav navbar-nav navbar-left">
<?php
		$menus = $manager->getMenu(1);
		foreach( $menus as $menu ) {
			$liClass = $page->alias == $menu->alias ? 'class="active" ' : '';
?>
							<li <?php echo $liClass; ?>><a href="<?php echo SITEURL.'index.php?item='.$menu->alias; ?>"><span class="glyphicon glyphicon-<?php echo $menu->glyphicon; ?>"></span> <?php echo $menu->nom; ?></a></li>
<?php
	}
?>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li><a href="<?php echo SITEURL.'index.php?item=utilisateur&action=edit&id='.$user->id_utilisateur; ?>">Identifiant : <strong><?php echo $user->identifiant; ?></strong></a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></a>
								<ul class="dropdown-menu">
<?php
		$menuIds = $manager->getMenuIds();
		foreach( $menuIds as $menuId ) {
			$menus = $manager->getMenu($menuId);
			if( count( $menus ) > 0 ) {
?>
									<li role="separator" class="divider"></li>
<?php
				foreach( $menus as $menu ) {
					$liClass = $page->alias == $menu->alias ? 'class="active" ' : '';
?>
									<li <?php echo $liClass; ?>><a href="<?php echo SITEURL.'index.php?item='.$menu->alias; ?>"><span class="glyphicon glyphicon-<?php echo $menu->glyphicon; ?>"></span> <?php echo $menu->nom; ?></a></li>
<?php
				}
			}
		}
?>
								</ul>
							</li>
						</ul>
<?php
	}
	if( !$static ) {
		if( $new && $action == 'edit' ) {
			$description = $page->description.' <span class="badge">Création</span>';
		} else {
			$description = $page->description.' <span class="badge">'.$actions[$action].'</span>';
		}
	} else {
		$description = $page->description;
	}
?>
					</div>
				</div>
			</nav>
			<div id="header" class="panel panel-primary no-print">
				<div class="panel-heading">
					<p class="panel-title"><span class="glyphicon glyphicon-<?php echo $page->glyphicon; ?>"></span> <?php echo $page->nom; ?></p>
					<p><?php echo $description; ?></p>
				</div>
			</div>
			<div id="main">
