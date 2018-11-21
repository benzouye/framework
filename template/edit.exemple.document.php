							<ul class="list-group">
<?php
		foreach( $items as $document ) {
?>
								<li class="list-group-item">
									<a class="btn btn-sm btn-secondary" target="_blank" href="<?php echo SITEURL.UPLDIR.$document->fichier; ?>" title="Voir le fichier"><i class="fas fa-sm fa-search"></i> Consulter <?php echo $document->type_document; ?></a>
								</li>
<?php
		}
?>
							</ul>