// Paramètres régionaux du sélecteur de date
$( ".datepicker" ).datepicker({
	firstDay: 1,
	closeText: 'Fermer',
	prevText: 'Précédent',
	nextText: 'Suivant',
	currentText: 'Aujourd\'hui',
	monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
	monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
	dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
	dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
	dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
	weekHeader: 'Sem.',
	dateFormat: "yy-mm-dd"
});

// Sélecteur de couleur
$( ".colorpicker" ).colorpicker();

// Affichage du formulaire de changement de mot de passe
$( ".update-password" ).change( function(e){
	var templateInput = '<input required type="password" placeholder="Nouveau mot de passe" name="'+$(this).data('name')+'" class="form-control">';
	if( $(this).prop('checked') == true ) {
		$(templateInput).insertAfter(this);
	} else {
		$(this).next().remove();
	}
});

// Après chargement de la page
$( document ).ready( function(){
	// Reload après print
	$('.btnReload').click( function() {
		window.location.reload();
	});
	
	// Activation des tooltips
	$( 'a,button' ).tooltip();
	
	// Affichage des alertes dans le header
	$('#helpers').insertAfter('#header');
	$('.alert').show('slow', function() {
		// Et disparition temporisée des alertes
		// setTimeout( function() { $('.alert').hide( 'slow' ); }, 3000 );
	});
	
	// Alerte à la suppression
	$('form.delete').on("submit",function(e) {
		var reponse = confirm('Voulez vraiment supprimer cet enregistrement ?');
		if( reponse ) $(this).append('<input type="hidden" name="action" value="delete"/>');
		return reponse;
	});
	
	// Alerte à la suppression relation
	$('form.delete-rel').on("submit",function(e) {
		var reponse = confirm('Voulez vraiment supprimer cet enregistrement ?');
		if( reponse ) $(this).append('<input type="hidden" name="action" value="rel-del"/>');
		return reponse;
	});
	
	// Suppression d'une image unique liée
	$('.delete-image').click( function(e) {
		$(this).parent().append('<input class="form-control" type="file" name="'+$(this).data('name')+'" value="jpg" />');
		$(this).parent().find('img').remove();
		$(this).remove();
	});
	
	// Ajout d'une relation
	$('.add-relation').click( function(e) {
		var parentItem = $(this).data('parent-item');
		var parentId = $(this).data('parent-id');
		var relItem = $(this).data('rel-item');
		
		$('#relation-ul').empty();
		
		$.ajax({
			method: "GET",
			url: "ajax.php",
			data: {
				relation: relItem,
				parent_item: parentItem,
				parent_id: parentId
			},
			dataType: 'json',
			success: function( items ) {
				$('#relation-ul').append('<input type="hidden" name="action" value="rel-set">');
				$('#relation-ul').append('<input type="hidden" name="id" value="'+parentId+'">');
				$('#relation-ul').append('<input type="hidden" name="item" value="'+parentItem+'">');
				$('#relation-ul').append('<input type="hidden" name="relation" value="'+relItem+'">');
				if( items.length > 0 ) {
					$.each( items, function( i, item ) {
						$('#relation-ul').append('<li class="list-group-item"><input type="checkbox" name="'+relItem+'[]" value="'+item.id+'" /> '+item.nom+'</li>');
					});
				} else {
					$('#relation-ul').append('<p>Aucun élément disponible ...</p>');
				}
			},
			error: function(data) {
				console.log(data);
				$('#relation-ul').append('<li class="list-group-item">Une erreur JavaScript est survenue ... merci de prévenir l\'administrateur de l\'application</li>');
			}
		});
	});

// Custom combobox
$( function() {
		$.widget( "custom.combobox", {
			_create: function() {
				this.wrapper = $( "<span>" )
					.addClass( "custom-combobox" )
					.insertAfter( this.element );
 
				this.element.hide();
				this._createAutocomplete();
				this._createShowAllButton();
			},
 
			_createAutocomplete: function() {
				var selected = this.element.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
 
				this.input = $( "<input>" )
					.appendTo( this.wrapper )
					.val( value )
					.attr( "title", "" )
					.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: $.proxy( this, "_source" )
					})
					.tooltip({
						classes: {
							"ui-tooltip": "ui-state-highlight"
						}
					});
 
				this._on( this.input, {
					autocompleteselect: function( event, ui ) {
						ui.item.option.selected = true;
						this._trigger( "select", event, {
							item: ui.item.option
						});
					},
 
					autocompletechange: "_removeIfInvalid"
				});
			},
 
			_createShowAllButton: function() {
				var input = this.input,
					wasOpen = false;
 
				$( "<a>" )
					.attr( "tabIndex", -1 )
					.tooltip()
					.appendTo( this.wrapper )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "custom-combobox-toggle ui-corner-right" )
					.on( "mousedown", function() {
						wasOpen = input.autocomplete( "widget" ).is( ":visible" );
					})
					.on( "click", function() {
						input.trigger( "focus" );
 
						// Close if already visible
						if ( wasOpen ) {
							return;
						}
 
						// Pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
					});
			},
 
			_source: function( request, response ) {
				var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
				response( this.element.children( "option" ).map(function() {
					var text = $( this ).text();
					if ( this.value && ( !request.term || matcher.test(text) ) )
						return {
							label: text,
							value: text,
							option: this
						};
				}) );
			},
 
			_removeIfInvalid: function( event, ui ) {
 
				// Selected an item, nothing to do
				if ( ui.item ) {
					return;
				}
 
				// Search for a match (case-insensitive)
				var value = this.input.val(),
					valueLowerCase = value.toLowerCase(),
					valid = false;
				this.element.children( "option" ).each(function() {
					if ( $( this ).text().toLowerCase() === valueLowerCase ) {
						this.selected = valid = true;
						return false;
					}
				});
 
				// Found a match, nothing to do
				if ( valid ) {
					return;
				}
 
				// Remove invalid value
				this.input
					.val( "" )
					.attr( "title", value + " didn't match any item" )
					.tooltip( "open" );
				this.element.val( "" );
				this._delay(function() {
					this.input.tooltip( "close" ).attr( "title", "" );
				}, 2500 );
				this.input.autocomplete( "instance" ).term = "";
			},
 
			_destroy: function() {
				this.wrapper.remove();
				this.element.show();
			}
		});
 
		$( ".auto-complete" ).combobox();
	});
});
