
var radius = 10;
var border = 10;

var spinner = "<i class='fas fa-spinner fa-spin fa-2x'></i>";

$(function () {

    var uploadbutton;
    $(document).on('click', '.formsubmit', function (e) {
        e.preventDefault();

        var allowtimeout = true;
        if ($(this).hasClass('notimermessage')) {
            allowtimeout = false;
        }

        uploadbutton = $(this);

        if ($(this).closest('form')[0].checkValidity()) {
            $(this).removeClass('formsubmit');
            $(this).html(spinner);
            if (allowtimeout) {
                buttontimeout = setTimeout(function () {
                    uploadbutton.html(spinner + " &nbsp;&nbsp;<span style='font-size: large'>System is working on something</span>");
                }, 5000);
            }
            $(this).closest('form').submit();
        } else {
            $(this).closest('form').append("<input type='submit' id='newformsubmit' />");
            $('#newformsubmit').trigger("click");
            $('#newformsubmit').remove();
        }
        //$(this).closest('form').submit();
    });
    
});

$(document).on('keypress', '.inputformsubmit', function (e) {
    if (e.which == 13) {
        $('#formsubmitbutton').trigger('click');
    }
});


function validateEmail(email) {
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(String(email).toLowerCase());
}

function validatePassword() {
	confirm_password = document.getElementById('password2');
	var password = $('#password1').val();
	var password2 = $('#password2').val();

	if (password != password2) {
		confirm_password.setCustomValidity("The passwords you have entered don't match");
	} else {
		confirm_password.setCustomValidity('');
	}
}