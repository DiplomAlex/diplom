function restoreState(ctl_id) { //восстановить состояние одного контрола по его ID - показать, если был видим
	if (jQuery.cookie("ctlState_" + ctl_id) == "1") jQuery('#' + ctl_id).show();
}
function restoreStateEx(ctl_id) { //то же, но + скрыть, если был скрыт
	if (jQuery.cookie("ctlState_" + ctl_id) == "1") { jQuery('#' + ctl_id).show(); } else { jQuery('#' + ctl_id).hide(); }
}
function restoreStateXe(ctl_id) { //то же, но + скрыть, если был скрыт
	if (jQuery.cookie("ctlState_" + ctl_id) == "0") { jQuery('#' + ctl_id).hide(); } else { jQuery('#' + ctl_id).show(); }
}
function clearState(ctl_id) { //сбросить куку с состоянием контрола
	jQuery.cookie("ctlState_" + ctl_id, "1", { expires: -5 } );
	jQuery.cookie("ctlState_" + ctl_id, "0", { expires: -5 } );
}
function saveState(ctl_id) { //сохранить состояние контрола по ID
	clearState(ctl_id);
	jQuery.cookie("ctlState_" + ctl_id, jQuery("#" + ctl_id).is(':visible') ? "1" : "0", { path: '/', expires: 0 });
}
function saveStateInverted(ctl_id) { //сохранить иныерсию состояния по ID. Нужно при асинхронном toggle
	clearState(ctl_id);
	jQuery.cookie("ctlState_" + ctl_id, jQuery("#" + ctl_id).is(':visible') ? "0" : "1", { path: '/', expires: 0 });
}
function restoreAll(id_array) { //восстановить состояния всех перечисленных в массиве контролов
	for (x in id_array)	restoreState(id_array[x]);
}
function restoreAllEx(id_array) { //
	for (x in id_array)	restoreStateEx(id_array[x]);
}
function restoreByPrefix(prefix) { //восстановить состояния всех контролов, ID которых начинается с указанной подстроки
	var ctl = jQuery("*[id^='" + prefix + "']").get();
	for (ctl_obj in ctl) restoreState(ctl[ctl_obj].id);
}
function restoreByPrefixEx(prefix) { //
	var ctl = jQuery("*[id^='" + prefix + "']").get();
	for (ctl_obj in ctl) restoreStateEx(ctl[ctl_obj].id);
}
function restoreBothState(ctl_id) {
	if (jQuery.cookie("ctlState_" + ctl_id) == "1") { jQuery('#' + ctl_id).show(); } else if (jQuery.cookie("ctlState_" + ctl_id) == "0") { jQuery('#' + ctl_id).hide(); }
}
function restoreBothBySelector(selector) {
	var ctl = jQuery(selector).get();
	for (ctl_obj in ctl) restoreBothState(ctl[ctl_obj].id);
}
