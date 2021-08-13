							<ul class="list-group">
<?php
		foreach( $items as $utilisateur ) {
?>
								<li class="list-group-item">
									<?=$utilisateur->identifiant; ?>
								</li>
<?php
		}
?>
							</ul>
