<?php
	if( !$user ) {
		$object = new Utilisateur( $bdd, $manager, $model_utilisateur );
		if( !isset($_POST['identifiant']) and !isset( $_GET['validation'] ) ) {
			$colonnes = $object->getColumns();
?>
			<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=register">
				<div class="card panel-primary">
					<div class="card-header">
						<span class="card-title">Données à renseigner</span>
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
						<div class="form-group row col-12 col-md-<?php echo $colGrid->div; ?>">
							<div class="col-6 col-md-<?php echo $colGrid->label; ?> col-form-label form-control-sm text-right"><?php echo $colonne->nicename; ?></div>
							<div class="col-6 col-md-<?php echo $colGrid->value; ?> input-group input-group-sm">
								<?php echo $object->displayInput( 0, $colonne->name, $valeur, 'form-control form-control-sm' ); ?>
							</div>
						</div>
<?php
			}
		}
		// Affichage des boutons
?>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-success btn-sm navbar-btn">
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
				$_POST['user_cre'] = 1;
				
				// Enregistrement du compte non validé
				$nbAvant = $manager->getNbErrors();
				$object->setItem( $_POST, true );
				$nbApres = $manager->getNbErrors();
				
				// Envoi du mail de validation
				if( $nbAvant === $nbApres ) {
					// Adresse email
					$title = $manager->getOption('sitetitle');
					$email = $manager->getOption('sitemail');
					$destinataire = $_POST['email'];
					
					// Header HTML
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					$headers .= 'From: '.$title.' <'.$email.'>' . "\r\n";
					$headers .= 'Reply-To: '.$title.' <'.$email.'>' . "\r\n";

					// Sujet
					$sujet = '['.$title.'] Activez votre compte';

					// message
					$message = '
<html>
	<head>
		<title>['.$title.'] Activez votre compte</title>
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
						$manager->setError( sprintf( M_EMAILERR, $_POST['email'], $email, $email ) );
					}
				} else {
					$manager->setError( sprintf( M_ITEMSETERR, $_POST['email'] ) );
				}
			}
			if( isset( $_GET['validation'], $_GET['identifiant'], $_GET['token'] ) ) {
				// Enregistrement validation compte
				$object->valideEmailUser( $_GET['identifiant'], $_GET['token'] );
			}
?>
			<a href="index.php" class="btn btn-primary btn-sm">
				<i class="fas fa-sm fa-caret-left"></i> Retour accueil
			</a>
<?php
		}
	} else {
		$manager->setError( M_VALOK );
	}
?>
