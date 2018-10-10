<?php
	if( !$user ) {
		$object = new Utilisateur( $bdd, $manager, $model_utilisateur );
		if( !isset($_POST['identifiant']) and !isset( $_GET['validation'] ) ) {
			$colonnes = $object->getColumns();
?>
			<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=register">
				<div class="card panel-primary">
					<div class="card-header">
						<span class="card-title">Informations principales</span>
					</div>
					<div class="card-body row">
<?php
		// Affichage du formulaire
		foreach( $colonnes as $colonne ) {
			// Test si colonnes à afficher
			$adminInput = false;
			if( property_exists( $colonne, 'admin' ) ) {
				if( $colonne->admin ) {
					$adminInput = true;
				}
			} else {
				$adminInput = false;
			}
			
			if( !$adminInput and $colonne->name != 'id_utilisateur' ) {
			
				// Gestion de la grille CSS
				$colGrid = property_exists( $colonne, 'grid' ) ? $colonne->grid : $grille;
				
				// Valeur par défaut
				$valeur = property_exists( $colonne , 'default' ) ? $colonne->default : '';
?>
						<div class="row form-group form-group-sm col-sm-<?php echo $colGrid->div; ?>">
							<label class="col-sm-<?php echo $colGrid->label; ?> control-label" for="<?php echo $colonne->name; ?>"><?php echo $colonne->nicename; ?></label>
							<div class="input-group col-sm-<?php echo $colGrid->value; ?>">
								<?php echo $object->displayInput( 0, $colonne->name, $valeur, 'form-control' ); ?>

							</div>
						</div>
<?php
			}
		}
		// Affichage des boutons
?>
					</div>
					<div class="card-footer">
						<button name="form-submit" type="submit" class="btn btn-success btn-sm navbar-btn">
							<i class="fas fa-sm fa-step-forward"></i> Envoyer
						</button>
						<a href="index.php" class="btn btn-danger btn-sm">
							<i class="fas fa-sm fa-times"></i> Annuler
						</a>
					</div>
				</div>
			</form>
<?php
		} else {
			if( isset( $_POST['identifiant'], $_POST['email'], $_POST['password'] ) ) {
				// Génération du token de validation
				$token = uniqid();
				$validationLink = SITEURL.'index.php?item=register&validation=1&identifiant='.$_POST['identifiant'].'&token='.urlencode($token);
				$_POST['token'] = $token;
				$_POST['valide'] = 0;
				
				// Enregistrement du compte non validé
				$nbAvant = $manager->getNbErrors();
				$object->setItem( $_POST, true );
				$nbApres = $manager->getNbErrors();
				
				// Envoi du mail de validation
				if( $nbAvant === $nbApres ) {
					// Adresse email
					$destinataire = $_POST['email'];
					
					// Header HTML
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: WebApp <'.$manager->getOption('sitemail').'>' . "\r\n";
					$headers .= 'Reply-To: WebApp <'.$manager->getOption('sitemail').'>' . "\r\n";

					// Sujet
					$sujet = '[WebApp] Activez votre compte';

					// message
					$message = '
<html>
	<head>
		<title>[WebApp] Activez votre compte</title>
	</head>
	<body>
		<style>p{ margin-top: 20px; } td{ border: 1px solid; padding: 5px; }</style>
		<h1>Bienvenue sur le site Internet '.$title.' ( '.SITEURL.' )</h1>
		<p>Vous avez fait la démarche de créer un compte sur notre site.</p>
		<p>Pour activer ce compte, veuillez cliquer sur le lien ci dessous.</p>
		<p><a href="'.$validationLink.'">Valider mon compte</a></p>
		<p>Ceci est un mail automatique, Merci de ne pas y répondre.</p>
	</body>
</html>';
					if( mail($destinataire, $sujet, $message, $headers) ) {
						$manager->setMessage( sprintf( M_EMAILOK, $_POST['email'] ) );
					} else {
						$manager->setError( sprintf( M_EMAILERR, $_POST['email'], $manager->getOption('sitemail'), $manager->getOption('sitemail') ) );
					}
				} else {
					
				}
			}
			if( isset( $_GET['validation'], $_GET['identifiant'], $_GET['token'] ) ) {
				// Enregistrement validation compte
				$object->valideEmailUser( $_GET['identifiant'], $_GET['token'] );
			}
?>
			<a href="index.php" class="btn btn-default btn-sm">
				<i class="fas fa-sm fa-caret-left"></i> Retour accueil
			</a>
<?php
		}
	} else {
		$manager->setError( M_VALOK );
	}
?>
