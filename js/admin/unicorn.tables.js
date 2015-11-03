$(document).ready(function(){
	$('.data-table-paging').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""l>t<"F"fp>',
        "oLanguage": {
            "sSearch":"Поиск:",
            "sLengthMenu":     "Показать _MENU_",
            "oPaginate": {
                "sFirst": "Первая",
                "sLast": "Последняя",
                "sPrevious": "Предыдущая",
                "sNext": "Следующая",
            },
            "sZeroRecords":"Ничего не найдено",
        },
	});
	var dataTabl = $('.data-table').dataTable({
		"bJQueryUI": true,
        "bPaginate": false,
		"sDom": '<""l>t<"F"fp>',
        "oLanguage": {
            "sSearch":"Поиск:",
            "sLengthMenu":     "Показать _MENU_",
            "oPaginate": {
                "sFirst": "Первая",
                "sLast": "Последняя",
                "sPrevious": "Предыдущая",
                "sNext": "Следующая",
            },
            "sZeroRecords":"Ничего не найдено",
        },
	});
	$('input[type=checkbox],input[type=radio],input[type=file]').uniform();
	
	$("span.icon input:checkbox, th input:checkbox").click(function() {
		var checkedStatus = this.checked;
		var checkbox = $(this).parents('.widget-box').find('tr td:first-child input:checkbox');		
		checkbox.each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	});	
});
