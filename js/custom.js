doc_on(
	'click',
	'#cci_create',
	function () {
		let form = document.getElementById( 'cci_form' );

		if ( ! cci_validate_form( form ) ) {
			cci_add_form_errors( form );
			return false;
		}
		let data = serialize_form( form );
		send_ajax(
			data,
			function ( response ) {
				alert( 'New item was created' );
				location.reload();
			},
			form
		)
	}
);

function cci_validate_form( form ) {
	let elements = form.querySelectorAll( '.form-required' );
	return Array.prototype.slice.call( form.querySelectorAll( '.form-required' ) ).every(
		function (el) {
			return el.value;
		}
	)
}

function cci_add_form_errors( form ) {
	let elements = form.querySelectorAll( '.form-required' ).forEach(
		function (el) {
			if ( ! el.value ) {
				el.classList.add( 'form-invalid' )
				el.addEventListener(
					'change',
					function ( e ) {
						e.target.classList.remove( 'form-invalid' )
					},
					{ once: true }
				)
			}
		}
	);
}
