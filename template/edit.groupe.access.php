<?php
	if( $userCan->admin ) {
?>
							<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=<?=$savelink; ?>">
								<table class="table table-sm table-striped table-hover table-bordered table-responsive">
									<thead>
										<tr>
											<th width="200">Item</th>
											<th width="50">Création</th>
											<th width="50">Consultation</th>
											<th width="50">Modification</th>
											<th width="50">Suppression</th>
											<th width="50">Tous</th>
										</tr>
									</thead>
									<tbody>
<?php
		$gAccess = $object->get_access_group();
		foreach( $items as $element ) {
			$gCreate = false;
			$gRead = false;
			$gUpdate = false;
			$gDelete = false;
			$gAll = false;
			foreach( $gAccess as $gObject ) {
				if( $gObject->alias == $element->alias ) {
					foreach( $gObject as $right => $value ) {
						if( $right == 'create' && $value == 1 ) $gCreate = true;
						if( $right == 'read' && $value == 1 ) $gRead = true;
						if( $right == 'update' && $value == 1 ) $gUpdate = true;
						if( $right == 'delete' && $value == 1 ) $gDelete = true;
						if( $right == 'all' && $value == 1 ) $gAll = true;
					}
				}
			}
?>
										<tr>
											<td><?=$element->nom; ?></td>
											<td class="text-center"><input type="checkbox" name="access[<?=$element->alias; ?>][create]" <?=$gCreate ? 'checked="checked"' : ''; ?> /></td>
											<td class="text-center"><input type="checkbox" name="access[<?=$element->alias; ?>][read]" <?=$gRead ? 'checked="checked"' : ''; ?> /></td>
											<td class="text-center"><input type="checkbox" name="access[<?=$element->alias; ?>][update]" <?=$gUpdate ? 'checked="checked"' : ''; ?> /></td>
											<td class="text-center"><input type="checkbox" name="access[<?=$element->alias; ?>][delete]" <?=$gDelete ? 'checked="checked"' : ''; ?> /></td>
											<td class="text-center"><input type="checkbox" name="access[<?=$element->alias; ?>][all]" <?=$gAll ? 'checked="checked"' : ''; ?> /></td>
										</tr>
<?php
		}
?>
									</tbody>
								</table>
								<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-save"></i> Sauvegarder</button>
								<input type="hidden" name="item" value="groupe"/>
								<input type="hidden" name="id" value="<?=$id; ?>"/>
								<input type="hidden" name="relation" value="access"/>
								<input type="hidden" name="action" value="rel-set"/>
							</form>
<?php
	}
