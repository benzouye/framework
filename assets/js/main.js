// Après chargement de la page
$( document ).ready( function(){
	// Paramètres régionaux du sélecteur de date
	$( ".datepicker" ).datepicker({
		firstDay: 1,
		changeMonth: true,
		changeYear: true,
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
	$( ".colorpicker-input" ).colorpicker();

	// Affichage du formulaire de changement de mot de passe
	$( ".update-password" ).change( function(e){
		let cible = $(this).parent().parent();
		let templateInput = '<input id="new-pass-input" required type="password" placeholder="Nouveau mot de passe" name="'+$(this).data('name')+'" class="form-control form-control-sm">';
		if( $(this).prop('checked') == true ) {
			cible.append( templateInput );
		} else {
			$('#new-pass-input').remove();
		}
	});
	
	// Activation des tooltips
	$( 'a,button' ).tooltip({boundary: 'window'});
	
	// Affichage des alertes dans le header
	$('#helpers').insertAfter('#header');
	$('.alert').show('slow');
	
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
		$(this).parent().append('<input class="form-control" type="file" name="'+$(this).data('name')+'" />');
		$(this).parent().find('img').remove();
		$(this).remove();
	});
	
	// Suppression d'une image unique liée
	$('.delete-file').click( function(e) {
		$(this).parent().append('<input class="form-control" type="file" name="'+$(this).data('name')+'" />');
		$(this).parent().find('a.file-link').remove();
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
				console.log( response.message );
				$('#relation-ul').append('<input type="hidden" name="action" value="rel-set">');
				$('#relation-ul').append('<input type="hidden" name="id" value="'+parentId+'">');
				$('#relation-ul').append('<input type="hidden" name="item" value="'+parentItem+'">');
				$('#relation-ul').append('<input type="hidden" name="relation" value="'+relItem+'">');
				if( response.data.length > 0 ) {
					$.each( response.data, function( i, item ) {
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
	
	// FullCalendar
	if( $(".homepage-calendar").length ) {
		$(".homepage-calendar").each( function(e) {
			
			var canvas = $(this);
			
			$.ajax({
				method: "GET",
				url: "ajax.php",
				data: {
					ajaxGet: "analyse",
					parent_item: "analyse",
					parent_id: canvas.data("analyse")
				},
				dataType: 'json',
				error: function( response ) {
					console.log( response );
				},
				success: function( response ) {
					console.log( response.message );
					let calendarOptions = JSON.parse( response.options );
					canvas.fullCalendar( calendarOptions );
				}
			});
		});
	}
	
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
					console.log( response.message );
					let chartLabels = [];
					let chartValues = [];
					let chartOptions = JSON.parse( response.options );
					
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
									label: response.indicator,
									data: chartValues,
									backgroundColor: chartOptions.backgroundColor,
									lineTension: 0,
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
	
	// Cartes Leaflet
	L.Icon.Default.imagePath = 'assets/img/';
	var icons = {
		"1": L.icon({
			iconUrl: L.Icon.Default.imagePath+'marker-icon-red.png',
			shadowUrl: L.Icon.Default.imagePath+'marker-shadow.png',
			iconSize:     [25, 41],
			shadowSize:   [41, 41],
			iconAnchor:   [12.5, 41],
			shadowAnchor: [12.5, 41],
			popupAnchor:  [12.5, 0]
		}),
		"2": L.icon({
			iconUrl: L.Icon.Default.imagePath+'marker-icon.png',
			shadowUrl: L.Icon.Default.imagePath+'marker-shadow.png',
			iconSize:     [25, 41],
			shadowSize:   [41, 41],
			iconAnchor:   [12.5, 41],
			shadowAnchor: [12.5, 41],
			popupAnchor:  [12.5, 0]
		}),
		"3": L.icon({
			iconUrl: L.Icon.Default.imagePath+'marker-icon-green.png',
			shadowUrl: L.Icon.Default.imagePath+'marker-shadow.png',
			iconSize:     [25, 41],
			shadowSize:   [41, 41],
			iconAnchor:   [12.5, 41],
			shadowAnchor: [12.5, 41],
			popupAnchor:  [12.5, 0]
		}),
	};
	
	if( $(".leaflet-input").length ) {
		$.ajax({
			method: "GET",
			url: "ajax.php",
			data: {
				ajaxGet: "option",
				parent_item: "option",
				alias: "leafletOptions"
			},
			dataType: 'json',
			error: function( response ) {
				console.log( response );
			},
			success: function( response ) {
				console.log( response.message );
				var leafletOptions = JSON.parse( response.data );
				
				$(".leaflet-input").each( function(e) {
					var id = $(this).attr('id');
					
					if( $('input[name="'+id+'"]').val().length ) {
						leafletOptions = JSON.parse( $('input[name="'+id+'"]').val() );
					}
					
					var leafletMap = L.map( id ).setView( [ leafletOptions.center_lat, leafletOptions.center_lng ], leafletOptions.zoom );
					
					L.tileLayer( 'http://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
						maxZoom: leafletOptions.max_zoom,
						minZoom: leafletOptions.min_zoom,
						attribution: '© <a href=\"http://osm.org/copyright\">OpenStreetMap</a> contributors'
					}).addTo(leafletMap);
					
					var marker = L.marker( [ leafletOptions.lat, leafletOptions.lng ] ).addTo( leafletMap );
					
					leafletMap.on( 'click', function(e) {
						leafletMap.eachLayer( function(layer) {
							if( layer instanceof L.Marker ) {
								leafletMap.removeLayer(layer);
							}
						});
						let marker = L.marker( [ e.latlng.lat, e.latlng.lng ] ).addTo( leafletMap );
						leafletOptions.lat = e.latlng.lat;
						leafletOptions.lng = e.latlng.lng;
						leafletOptions.zoom = leafletMap.getZoom();
						leafletOptions.center_lat = leafletMap.getCenter().lat;
						leafletOptions.center_lng = leafletMap.getCenter().lng;
						
						$('input[name="'+id+'"]').val( JSON.stringify( leafletOptions ) );
					});
				});
			}
		});
	}
	
	// Analyse Leaflet
	if( $(".leaflet-display").length ) {
		$(".leaflet-display").each( function(e) {
			
			var id = $(this).attr('id');
			var leafletMap = L.map( id );
			
			$.ajax({
				method: "GET",
				url: "ajax.php",
				data: {
					ajaxGet: "option",
					parent_item: "option",
					alias: "leafletOptions"
				},
				dataType: 'json',
				error: function( response ) {
					console.log( response );
				},
				success: function( response ) {
					var leafletOptions = JSON.parse( response.data );
					leafletMap.setView( [ leafletOptions.center_lat, leafletOptions.center_lng ], leafletOptions.zoom );
					L.tileLayer( 'http://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
						maxZoom: leafletOptions.max_zoom,
						minZoom: leafletOptions.min_zoom,
						attribution: '© <a href=\"http://osm.org/copyright\">OpenStreetMap</a> contributors'
					}).addTo(leafletMap);
				}
			});
			
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
					var markers = response.data;
					
					for( marker in markers ) {
						let markerIcon = icons[markers[marker].Icon];
						let markerOptions = JSON.parse( markers[marker].Options );
						let newMarker = L.marker( [ markerOptions.lat, markerOptions.lng ], { "icon": markerIcon } ).addTo( leafletMap );
						newMarker.bindPopup( markers[marker].Label );
					}
				}
			})
		});
	}
});
