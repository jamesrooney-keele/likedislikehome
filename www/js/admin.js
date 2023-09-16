
$(document).on('keyup change', '#user_email', function (e) {
    var email = $(this).val();
    var userid = $('#userid').val();
    $('#emailHelp').html("");
    $('#emailHelp').hide();

    if (email.length > 1 && validateEmail(email)) {
        var url = "/admin/ajax.php";
        var params = "action=checkemailfordupes&userid=" + userid + "&email=" + email;
        $.ajax({
            url: url,
            type: "POST",
            data: params
        }).done(function (response) {
            if (response.trim() == '0') {
                $('#emailsuccess').show();
                $('#emailfail').hide();
                $('#saveuser').attr('disabled', false);
            } else {
                $('#emailsuccess').hide();
                $('#emailfail').show();
                $('#saveuser').attr('disabled', true);
                $('#emailHelp').html("There is already a user on the system with that email address");
                $('#emailHelp').show();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('#emailsuccess').hide();
            $('#emailfail').show();
            $('#saveuser').attr('disabled', true);
        });
    } else {
        $('#emailsuccess').hide();
        $('#emailfail').show();
        $('#saveuser').attr('disabled', true);
    }
});


$(document).on('keyup change', '#url', function (e) {
    var siteurl = $(this).val();
    var siteid = $('#siteid').val();
    $('#urlHelp').html("");
    $('#urlHelp').hide();

    if (siteurl.length > 1) {
        var url = "/admin/ajax.php";
        var params = "action=checkurlfordupes&siteid=" + siteid + "&url=" + siteurl;
        $.ajax({
            url: url,
            type: "POST",
            data: params
        }).done(function (response) {
            if (response.trim() == '0') {
                $('#urlsuccess').show();
                $('#urlfail').hide();
                $('#savesite').attr('disabled', false);
            } else {
                $('#urlsuccess').hide();
                $('#urlfail').show();
                $('#savesite').attr('disabled', true);
                $('#urlHelp').html("There is already a site on the system with that url");
                $('#urlHelp').show();
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('#urlsuccess').hide();
            $('#urlfail').show();
            $('#savesite').attr('disabled', true);
        });
    } else {
        $('#urlsuccess').hide();
        $('#urlfail').show();
        $('#savesite').attr('disabled', true);
    }
});

$(function () {
    $('#password1,#password2').change(function () {
        validatePassword();
    });
})
