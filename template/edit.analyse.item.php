<?php
	if( $userCan->admin ) {
?>
							<form class="form-horizontal" enctype="multipart/form-data" method="POST" action="index.php?item=<?php echo $savelink; ?>">
								<ul class="list-group">
<?php
		foreach( $items as $element ) {
			$checked = $element->visible ? 'checked="checked"' : '';
?>
									<li class="list-group-item">
										<div class="custom-control custom-checkbox">
											<input <?php echo $checked; ?> name="alias[]" value="<?php echo $element->alias; ?>" type="checkbox" class="custom-control-input" id="check-<?php echo $element->alias; ?>">
											<label class="custom-control-label" for="check-<?php echo $element->alias; ?>"><?php echo $element->nom; ?></label>
										</div>
									</li>
<?php
		}
?>
								</ul>
								<button type="submit" class="btn btn-success btn-sm"><i class="fas fa-sm fa-save"></i> Sauvegarder</button>
								<input type="hidden" name="item" value="analyse"/>
								<input type="hidden" name="id" value="<?php echo $id; ?>"/>
								<input type="hidden" name="relation" value="item"/>
								<input type="hidden" name="action" value="rel-set"/>
							</form>
<?php
	}
?>
