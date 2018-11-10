// Après chargement de la page
$( document ).ready( function(){
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
	
	// Autocomplete
	$('input[type="text"].auto-complete').each( function(e) {
		let parentItem = $(this).data('parent-item');
		let colonne = $(this).data('colonne');
		let sourceLink = 'ajax.php?ajaxGet=distinct&parent_item='+parentItem+'&colonne='+colonne;
		
		$(this).autocomplete({ source: sourceLink });
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
				ajaxGet: 'relation',
				relation: relItem,
				parent_item: parentItem,
				parent_id: parentId
			},
			dataType: 'json',
			success: function( response ) {
				$('#relation-ul').append('<input type="hidden" name="action" value="rel-set">');
				$('#relation-ul').append('<input type="hidden" name="id" value="'+parentId+'">');
				$('#relation-ul').append('<input type="hidden" name="item" value="'+parentItem+'">');
				$('#relation-ul').append('<input type="hidden" name="relation" value="'+relItem+'">');
				if( reponse.data.length > 0 ) {
					$.each( reponse.data, function( i, item ) {
						$('#relation-ul').append('<li class="list-group-item"><input type="checkbox" name="'+relItem+'[]" value="'+item.id+'" /> '+item.nom+'</li>');
					});
				} else {
					$('#relation-ul').append('<p>Aucun élément disponible ...</p>');
				}
			},
			error: function( response ) {
				console.log( response );
				$('#relation-ul').append('<li class="list-group-item">Une erreur JavaScript est survenue ... merci de prévenir l\'administrateur de l\'application</li>');
			}
		});
	});
	
	// Graphique ChartJS
	if( $(".homepage-chart").length ) {
		$(".homepage-chart").each( function(e) {
			
			var canvas = $(this);
			
			$.ajax({
				method: "GET",
				url: "ajax.php",
				data: {
					ajaxGet: "analyse",
					parent_item: "analyse",
					parent_id: $(this).data("analyse")
				},
				dataType: 'json',
				error: function( response ) {
					console.log( response );
				},
				success: function( response ) {
					let chartLabels = [];
					let chartValues = [];
					let chartOptions = JSON.parse( response.item.options );
					
					for( item in response.data ) {
						chartLabels.push( response.data[item].Label );
						chartValues.push( response.data[item].Value );
					}
					
					let graphOptions = {
						type: chartOptions.type,
						data:{
							labels: chartLabels,
							datasets:[
								{
									label: response.item.description,
									data: chartValues,
									backgroundColor: chartOptions.backgroundColor,
								}
							]
						},
						options: chartOptions.options
					};
					
					let graphique = new Chart( canvas, graphOptions );
				}
			});
		});
	}
});
