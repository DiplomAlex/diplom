
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
	bindGlobalEvent(EVENT_REGISTRATION_PROCESSED, function(e){
		triggerGlobalEvent(EVENT_LOGIN_PROCESSED);
	});
})