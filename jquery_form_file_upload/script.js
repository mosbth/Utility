$.fn.ajaxSubmit.debug = true;

$(document).ajaxError(function(ev,xhr,o,err) {
    alert(err);
    if (window.console && window.console.log) console.log(err);
});

$(function() {

    $('#uploadForm').ajaxForm({
        beforeSubmit: function(a,f,o) {
            o.dataType = $('#uploadResponseType')[0].value;
            $('#uploadOutput').html('Submitting...');
        },
        success: function(data) {
            var $out = $('#uploadOutput');
            $out.html('Form success handler received: <strong>' + typeof data + '</strong>');
            if (typeof data == 'object' && data.nodeType)
                data = elementToString(data.documentElement, true);
            else if (typeof data == 'object')
                data = objToString(data);
            $out.append('<div><pre>'+ data +'</pre></div>');
        }
    });


    // pre-submit callback
    function showRequest(formData, jqForm) {
        alert('About to submit: \n\n' + $.param(formData));
        return true;
    }

    // post-submit callback
    function showResponse(responseText, statusText)  {
        alert('this: ' + this.tagName +
            '\nstatus: ' + statusText +
            '\n\nresponseText: \n' +
            responseText + '\n\nThe output div should have already been updated with the responseText.');
    }

   // helper
    function objToString(o) {
        var s = '{\n';
        for (var p in o)
            s += '    "' + p + '": "' + o[p] + '"\n';
        return s + '}';
    }

    // helper
    function elementToString(n, useRefs) {
        var attr = "", nest = "", a = n.attributes;
        for (var i=0; a && i < a.length; i++)
            attr += ' ' + a[i].nodeName + '="' + a[i].nodeValue + '"';

        if (n.hasChildNodes == false)
            return "<" + n.nodeName + "\/>";

        for (var i=0; i < n.childNodes.length; i++) {
            var c = n.childNodes.item(i);
            if (c.nodeType == 1)       nest += elementToString(c);
            else if (c.nodeType == 2)  attr += " " + c.nodeName + "=\"" + c.nodeValue + "\" ";
            else if (c.nodeType == 3)  nest += c.nodeValue;
        }
        var s = "<" + n.nodeName + attr + ">" + nest + "<\/" + n.nodeName + ">";
        return useRefs ? s.replace(/</g,'&lt;').replace(/>/g,'&gt;') : s;
    };

});

