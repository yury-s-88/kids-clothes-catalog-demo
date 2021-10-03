/**
 * @callback requestCallback
 * @param {*} response - something from server
 */

/**
 * Short way to add event listener with event delegation
 * @param event_type
 * @param target_selector
 * @param callback
 */
function doc_on( event_type, target_selector, callback ) {
	document.addEventListener(
		event_type,
		function ( event ) {
			if ( event.target.matches( target_selector ) ) {
				callback.call( event.target, event ) // .call to not lose context
			}
		}
	);
}

/**
 * Wrapper for XMLHttpRequest
 * @param {{}} data - object containing pairs key - value
 * @param {{requestCallback}} callback - function to be invoked after request successfully done
 * @param {Element}container - element to attach spinner. body element if not set
 */
function send_ajax(data, callback, container) {

	document.body.classList.add( 'ajax_processing' );
	if ( ! container ) {
		container = document.body;
	}
	container.classList.add( 'processing' );

	let formData = new FormData();
	for (let property in data) {
		if ( data.hasOwnProperty( property ) ) {
			formData.append( property, data[property] );
		}
	}
	let xhr = new XMLHttpRequest();
	xhr.open( 'POST', ajaxurl );
	xhr.responseType = "json";
	xhr.onload       = function() {
		document.body.classList.remove( 'ajax_processing' );
		container.classList.remove( 'processing' );
		if ( xhr.status !== 200 ) {
			alert( 'Server error' );
		} else {
			let response = xhr.response;
			if (response.success !== true) {
				if (response.data && response.data.message) {
					alert( response.data.message )
				} else {
					alert( 'Unexpected error' );
				}
			} else {
				callback( xhr.response );
			}
		}
	};
	xhr.onerror      = function() {
		document.body.classList.add( 'ajax_processing' );
		container.classList.add( 'processing' );
		alert( 'Unexpected error' );
	};
	xhr.send( formData );
}

/**
 * Serializes html form to object
 * @param form - HTMLFormElement
 * @returns {{}} - object that contains pairs key - value;
 */
function serialize_form(form) {
	let form_controls = form.querySelectorAll(
		'input:not([type="radio"]):not([type="checkbox"]),' +
		'textarea,' +
		'select,' +
		'input[type="checkbox"]:checked,' +
		'input[type="radio"]:checked'
	);

	if ( ! form_controls.length ) {
		return {};
	}
	let res = {};
	form_controls.forEach(
		function (element ) {
			res[element.name] = element.value;
		}
	);
	return res;
}