
/**
	global events
*/
var EVENT_SHOPPING_CART_UPDATED = 'eventShoppingCartUpdated';

var EVENT_LOGIN_PROCESSED = 'eventLoginProcessed';

var EVENT_LOGOUT_PROCESSED = 'eventLogoutProcessed';

var EVENT_REGISTRATION_PROCESSED = 'eventRegistrationProcessed';



function triggerGlobalEvent(event) {
	$("body").trigger(event);
}

function bindGlobalEvent(event, func) {
	$("body").bind(event, func);
}





$(function(){

	/**
	 *	default global events observers
	 */

	/**
	 * @deprecated - moved to front shopping cart helper or script
	 * 
	bindGlobalEvent(EVENT_SHOPPING_CART_UPDATED, function(e){
		var div = $("#box_shopping_cart");
		$.get(div.attr("href"), function(html){div.replaceWith(html);});
	});
	*/


	
	bindGlobalEvent(EVENT_REGISTRATION_PROCESSED, function(e){
		triggerGlobalEvent(EVENT_LOGIN_PROCESSED);
	});
	
})