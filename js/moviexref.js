
$( document ).ready( function () {
	
	$( '#what, #close-what' ).click( function(e) {
		e.preventDefault();
		$( '#what-text' ).slideToggle();
	} );
	
	$( '#submit' ).click( function() {
		$( '#first, #second' ).each( function() {
			if( $(this).val() == 'Enter a Movie or an Actor' ) {
				$(this).val('');
			}
		} );
	} );
	
	$( '#first, #second' ).focus( function() {
		if( $(this).val() == 'Enter a Movie or an Actor' ) {
			$(this).val('');
			$(this).removeClass('italic');
		}
	} );

	$( '#first, #second' ).blur( function() {
		if( $(this).val() == '' ) {
			$(this).val('Enter a Movie or an Actor');
			$(this).addClass('italic');
		}
	} );
   
	$( '#first, #second' ).each( function() {

		var box = $(this).attr( 'id' );

		$(this).autocomplete( '/search', {
			dataType: "json",
			formatItem: function( data, i, max, value, result ) {
				return value;
			},
			parse: function( data ) {
			var array = new Array();
			for(var i=0;i<data.length;i++) {
				array[array.length] = { data: data[i], value: $("<div/>").html(data[i].name).text(), result: $("<div/>").html(data[i].name).text() }
			}
			return array;
			}
		} );

		$(this).result( function( e, data ) {
			$( '#' + box + '-id' ).val( data.id );
			$( '#' + box + '-type' ).val( data.type );
		} );         

	} );
   
} );
