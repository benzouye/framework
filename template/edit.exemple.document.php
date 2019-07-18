							<ul class="list-group">
<?php
		foreach( $items as $document ) {
?>
								<li class="list-group-item">
									<a class="btn btn-sm btn-secondary" target="_blank" href="<?php echo SITEURL.UPLDIR.$document->fichier; ?>" title="Voir le fichier">
										<i class="fas fa-sm fa-search"></i> Consulter <?php echo $document->type_document; ?>
									</a>
<?php
			if( $userCan->admin or $userCan->delete ) {
?>
									<form class="delete float-right" method="post" action="index.php?item=exemple&action=edit&id=<?php echo $id; ?>">
										<button title="Supprimer ce document" type="submit" class="btn btn-danger btn-sm"><span class="fas fa-sm fa-times"></button>
										<input type="hidden" name="item" value="document"/>
										<input type="hidden" name="id" value="<?php echo $document->id_document; ?>"/>
									</form>
<?php
			}
?>
								</li>
<?php
		}
?>
							</ul>
