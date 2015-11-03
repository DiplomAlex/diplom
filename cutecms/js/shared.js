
function getBrowserWidth()
{
    var width;
    if ($.browser.msie) {
        width = document.body.clientWidth;
    }
    else {
        width = window.innerWidth;
    }
    return width;
}

function getBrowserHeight()
{
    var height;
    if ($.browser.msie) {
        height = document.documentElement.clientHeight;
    }
    else {
        height = window.innerHeight;
    }
    return height;
}


function submitFormBySubmitLink(uid) {
    var input = $("input[uid="+uid+"]");
    var form = input.get(0).form;
    input.removeAttr("smLink").val("1");
    $("[smLink=1]", form).remove();
    form.submit();
    return false;
}
