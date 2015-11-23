var MSG_ERROR_GENERAL = 'An error has ocurred, try again later';
var MSG_ERROR_FIRST_NAME = 'Must complete first name.';
var MSG_ERROR_LAST_NAME = 'Must complete last name.';
var MSG_ERROR_EMAIL = 'Must enter a valid email.';
var MSG_ERROR_PHONE = 'Must enter a valid phone number.';


var MSG_ENVIANDO = 'Sending...';

var MSG_ERROR_FORM = 'Verify your input data.';

(function($) {

    $.fn.isValid = function() {
        var value = $.trim(this.val());
        
        var placeholder = this.attr("placeholder");
        if (value == placeholder)
            value = '';

        switch (this.attr('type').toLowerCase()) {
            case 'text':
                if (!value || (value === ''))
                    return false;
                break;

            case 'tel':
                //  '+', '.', ' ', '(', ')'
                var tel = value.replace(/(\()|(\))|(\-)|(\.)|(\+)|(\ )/g,'');

                if (!tel || (tel === ''))
                    return false;
                
                var filtro = /^\d+$/;
                if (filtro.test(tel) == true)
                    return true;
                else
                    return false;
                
                break;

            case 'email':
                if (!value || (value === ''))
                    return false;

                var filtro = /^[A-Za-z][A-Za-z0-9_.-]*@[A-Za-z0-9_.-]+\.[A-Za-z0-9_.]+[A-za-z]$/;
                if (filtro.test(value) == true)
                    return true;
                else
                    return false;
                break;

            default:
                return true;
        }
        return true;
    };
})(jQuery);

$( document ).ready(function() {
	$('#contact_form').find('input[type=text], input[type=tel], input[type=email], select').on('keyup change', function() {
        $(this).parent().parent().removeClass('has-error');
		$(this).parent().find('span i').addClass('glyphicon-unchecked');
		$(this).parent().find('span i').removeClass('glyphicon-remove');
		$('#solicitud-error').hide();
    });

	$('#contact_form').on('submit', function(evt) {
	    var first_name = $('#first_name');
	    var last_name = $('#last_name');
	    var email = $('#email');
	    var phone = $('#phone');

		fieldOk(first_name);
	    if (!first_name.isValid()) {
	        showError(MSG_ERROR_FIRST_NAME, first_name);
	        return false;
	    }

		fieldOk(last_name);
	    if (!last_name.isValid()) {
	        showError(MSG_ERROR_LAST_NAME, last_name);
	        return false;
	    }

	    fieldOk(phone);
	    if (!phone.isValid()) {
	        showError(MSG_ERROR_PHONE, phone);
	        return false;
	    }

		fieldOk(email);
	    if (!email.isValid()) {
	        showError(MSG_ERROR_EMAIL, email);
	        return false;
	    }

		
	    
	});
});

function fieldOk(field)
{
	field.parent().parent().addClass('has-success');
	field.parent().find('span i').removeClass('glyphicon-unchecked');
	field.parent().find('span i').addClass('glyphicon-ok');
}

function showError(message, field)
{
	field.parent().parent().addClass('has-error');
	field.parent().find('span i').removeClass('glyphicon-unchecked');
	field.parent().find('span i').addClass('glyphicon-remove');
	$('#solicitud-error-message').html(message);
	$('#solicitud-error').show();
}
