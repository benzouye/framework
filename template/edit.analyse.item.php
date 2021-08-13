<?php
	if( $userCan->admin ) {
?>
							<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=<?=$savelink; ?>">
								<ul class="list-group">
<?php
		foreach( $items as $element ) {
			$checked = $element->visible ? 'checked="checked"' : '';
?>
									<li class="list-group-item">
										<div class="custom-control custom-checkbox">
											<input <?=$checked; ?> name="alias[]" value="<?=$element->alias; ?>" type="checkbox" class="custom-control-input" id="check-<?=$element->alias; ?>">
											<label class="custom-control-label" for="check-<?=$element->alias; ?>"><?=$element->nom; ?></label>
										</div>
									</li>
<?php
		}
?>
									<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-save"></i> Sauvegarder</button>
								</ul>
								<input type="hidden" name="item" value="analyse"/>
								<input type="hidden" name="id" value="<?=$id; ?>"/>
								<input type="hidden" name="relation" value="item"/>
								<input type="hidden" name="action" value="rel-set"/>
							</form>
<?php
	}
