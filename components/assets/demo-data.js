window.onbeforeunload = function() {
	if ( document.body.classList.contains( 'ajax_processing' ) ) {
		return confirm( 'Request to the server was not completed. Are you sure you want to interrupt it?' );
	}
};

/**
 * Submission of the first step form
 * Generation of clothes posts
 */
doc_on(
	'click',
	'#demo_generate_clothes',
	function () {
		let form = document.getElementById( 'generate_demo_clothes' );
		let data = serialize_form( form );

		data._wpnonce = document.getElementById( '_wpnonce' ).value;
		send_ajax(
			data,
			function (response) {
				alert( 'Clothes posts was created' );
				document.querySelector( '.demo-data-step-1' ).classList.add( 'hidden' );
				document.querySelector( '.demo-data-step-2' ).classList.remove( 'hidden' );
			},
			form
		)
	}
);

/**
 * Submission of the second step
 * Generation of terms and assigning them to posts
 */
doc_on(
	'click',
	'#demo_generate_clothes_types',
	function () {
		let form = document.getElementById( 'generate_demo_clothes_types' );
		let data = serialize_form( form );

		data._wpnonce = document.getElementById( '_wpnonce' ).value;
		send_ajax(
			data,
			function (response) {
				alert( 'Clothes terms was created' );
				document.querySelector( '.demo-data-step-2' ).classList.add( 'hidden' );
				document.querySelector( '.demo-data-step-3' ).classList.remove( 'hidden' );
			},
			form
		)
	}
)