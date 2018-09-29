<?php
class document extends Model {
	
	public function deleteItem( $id ) {
		// On supprime l'enregistrement normalement
		parent::deleteItem( $id );
		
		// Et on supprime le fichier lié
		if( unlink( UPLDIR.$this->currentItem->fichier ) ) {
			$this->manager->setMessage('Le fichier physique a bien été supprimé');
		} else {
			$this->manager->setError('Le fichier physique n\'a pas pu être supprimé');
		}
	}
	
}
?>