$(function() {
    $('#mail_tester_form').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: 'SendMail',
            method: "POST",
            data: $( this ).serialize(),
            //dataType: 'json'
        }).done(function(response){
            $('#response_message').html(response);
        }).fail(function() {
            alert( "error" );
        });
    })
});