
    function collectFormData(form)
    {

        var dataValues = {};
        $("#"+form.id+" *:input").each(function(i){
            var fieldName = this.name;
            var fieldObject = $(this);
            var fieldValue;
            var t;
            if ((fieldObject.val() == '')
                && ((fieldObject.attr('type')=='') || (fieldObject.attr('type')=='hidden'))) {
                if (typeof(window.FCKeditorAPI) != 'undefined') {
                    fieldValue = FCKeditorAPI.GetInstance(fieldName).GetHTML();
                }
                else if (typeof(window.CKeditorAPI) != 'undefined') {
                    fieldValue = CKeditorAPI.GetInstance(fieldName).GetHTML();
                }
                else {
                    fieldValue = '';
                }
            }
            else {
                fieldValue = fieldObject.val();
            }

            if (
                ((fieldObject.attr('type')!='checkbox')&&(fieldObject.attr('type')!='radio'))
                ||
                (fieldObject.attr('checked'))
               ) {
                    dataValues[fieldName] = fieldValue;
            }
        });

        return dataValues;


		/**

		// NOT TESTED EQUIVALENT !!!

        var data = $("#"+form.id).serializeArray();
        var values = {};
        for (v in data) {
        	values[v.name] = v.value;
        }
        return values;

        */
    }


    function disableAjaxWidget(wjID)
    {
        $("#"+wjID+" a").click(function(e){
	        e.preventDefault();
            e.stopImmediatePropagation();
        });
        $("#"+wjID+" input[type=submit]").click(function(e){
	        e.preventDefault();
            e.stopImmediatePropagation();
        });
        $("#"+wjID+" *").click(function(e){
	        e.preventDefault();
            e.stopImmediatePropagation();
        });
    }

    function enableAjaxWidget(wjID)
    {
    	/*
        $("#"+wjID+">form").submit(function(e){
        	e.preventDefault();
            var form = this;
            var formData = collectFormData(form);
            var question = $(form).attr('question');
            if (( ! question) || ask(question)) {
                $.post($(form).attr("action"), formData, function(data){
                    $("#"+wjID).html(data);
                    enableAjaxWidget(wjID);
                });
            }
            else {
                enableAjaxWidget(wjID);
            }
        });
        */

        /*$("#"+wjID+" a.submitLink").click(function(e){
            e.preventDefault();
            disableAjaxWidget(wjID);
            var elId = $(this).attr("elementId");
            $("#"+wjID+" #"+elId).val("1");
            $("#"+wjID+" #"+elId).removeAttr("smLink");
            $("#"+wjID+" input[smLink=1]").remove();


            var form = $("#"+wjID+" #"+elId).get(0).form;
            var formData = collectFormData(form);
            var question = $(form).attr('question');
            if (( ! question) || ask(question)) {
                $.post($(form).attr("action"), formData, function(data){
                    $("#"+wjID).html(data);
                    enableAjaxWidget(wjID);
                });
            }
            else {
                enableAjaxWidget(wjID);
            }

        });*/

        $("#"+wjID+" a.submitLink").click(function(e){
			submitFormBySubmitLink($(this).attr("uid"));
        });

        $("#"+wjID+" input[type=submit]").click(function(e){
            e.preventDefault();
            disableAjaxWidget(wjID);
            e.stopImmediatePropagation();
            $(this).unbind('click');
            var form = $(this).get(0).form;
            var formData = collectFormData(form);
            $.post($(form).attr("action"), formData, function(data){
                $("#"+wjID).html(data);
                enableAjaxWidget(wjID);
            });
        });


        $("#"+wjID+" a").not("[no_ajax_wj_replace]").not(".submitLink").click(function(e){
            disableAjaxWidget(wjID);
            e.preventDefault();
            e.stopImmediatePropagation();
            $(this).unbind('click');
            $.get($(this).attr("href"), function(data){
                $("#"+wjID).html(data);
                enableAjaxWidget(wjID);
            });
        });


    }

    function renderAjaxWidget(wjID)
    {
    	$.get($("#"+wjID).attr("href"), function(data){
    		$("#"+wjID).html(data);
    		enableAjaxWidget(wjID);
    	});
    }

