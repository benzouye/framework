
				<div class="row">
					<div class="col-12 col-lg-4">
						<p>Veuillez vous connecter.</p>
						<form method="POST" action="index.php">
							<div class="form-group">
								<input type="text" class="form-control" id="identifiant" name="identifiant" placeholder="Nom d'utilisateur" autofocus>
							</div>
							<div class="form-group">
								<input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe">
							</div>
							<button type="submit" class="btn btn-success">Se connecter</button>
<?php
	if( $manager->getOption('allowregister') ) {
?>
							<a class="btn btn-secondary" href="index.php?item=register">Pas encore inscrit ?</a>
<?php
	}
?>
						</form>
					</div>
				</div>
