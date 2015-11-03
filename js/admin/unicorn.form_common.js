$(document).ready(function(){
	$('input[type=checkbox],input[type=radio],input[type=file]').uniform();
	
	$('select').select2();
  //  $('.colorpicker').colorpicker();
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    }); 
    $('.table input[type=checkbox], .table tr td a, .table tr td button').on('click', function(e){
        e.stopPropagation();
    })
   $('.table tr').on('click', function(){
        var check = $(this).find('input[type=checkbox]');     
            if(!check.is(':checked')){ 
                check.attr('checked', 'checked');
                check.closest('span').addClass('checked');
                var len = $('input[type="checkbox"]').not('.mass_actions_form_check_all').length;
                console.log(len);
                console.log($('input[type="checkbox"]').filter(':checked').length);
                    if(len == $('input[type="checkbox"]').filter(':checked').length){
                        $('.mass_actions_form_check_all').attr('checked', true);
                        $('.mass_actions_form_check_all').parent('span').attr('class', 'checked');
                    }
            }else{
                check.removeAttr('checked');
                check.closest('span').removeClass('checked');
                $('.mass_actions_form_check_all').attr('checked', false);
                $('.mass_actions_form_check_all').parent('span').attr('class', '');
            }
             
    });

});
