							<ul class="list-group">
<?php
		foreach( $items as $affectation ) {
?>
								<li class="list-group-item">
									<?= $affectation->libelle; ?>
<?php
			if( $userCan->admin or $userCan->delete ) {
?>
									<form class="float-end" method="post" action="index.php?item=analyse&action=edit&id=<?= $id; ?>">
										<button title="Supprimer cette affectation" data-bs-toggle="tooltip" data-bs-placement="top" type="submit" class="btn btn-danger btn-sm"><span class="bi bi-x-lg"></button>
										<input type="hidden" name="action" value="rel-del"/>
										<input type="hidden" name="item" value="analyse"/>
										<input type="hidden" name="id" value="<?= $id; ?>"/>
										<input type="hidden" name="relation" value="affectation"/>
										<input type="hidden" name="rel_id" value="<?= $affectation->id_affectation; ?>"/>
									</form>
<?php
			}
?>
								</li>
<?php
		}
?>
							</ul>