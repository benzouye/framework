							<ul class="list-group">
<?php
		foreach( $items as $utilisateur ) {
?>
								<li class="list-group-item">
									<?php echo $utilisateur->identifiant; ?>
								</li>
<?php
		}
?>
							</ul>