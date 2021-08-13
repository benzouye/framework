							<ul class="list-group">
<?php
		foreach( $items as $document ) {
?>
								<li class="list-group-item">
									<a class="btn btn-sm btn-secondary" target="_blank" href="<?=SITEURL.UPLDIR.$document->fichier; ?>" title="Voir le fichier" data-bs-toggle="tooltip" data-bs-placement="top" >
										<span class="bi bi-search"></span> Consulter <?=$document->type_document; ?>
									</a>
<?php
			if( $userCan->admin or $userCan->update ) {
?>
									<a title="Modifier" data-bs-toggle="tooltip" data-bs-placement="top" href="index.php?item=document&action=edit&id=<?=$document->id_document; ?>" class="btn btn-success btn-sm"><span class="bi bi-pencil"></span></a>
<?php
			}
			if( $userCan->admin or $userCan->delete ) {
?>
									<form class="delete float-end" method="post" action="index.php?item=exemple&action=edit&id=<?=$id; ?>">
										<button title="Supprimer" data-bs-toggle="tooltip" data-bs-placement="top" type="submit" class="btn btn-danger btn-sm"><span class="bi bi-x-lg"></span></button>
										<input type="hidden" name="item" value="document"/>
										<input type="hidden" name="id" value="<?=$document->id_document; ?>"/>
									</form>
<?php
			}
?>
								</li>
<?php
		}
?>
							</ul>
