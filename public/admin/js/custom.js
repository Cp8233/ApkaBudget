$(document).ready(function () {
    // Ajax setup to include CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Common function to handle form submission
    function handleFormSubmission(form) {
        // Clear previous errors
        $(form).find('.text-danger').text('');
        $('#response-message').html(''); // Clear previous message

        $.ajax({
            url: $(form).attr('action'),  // Get the form action (URL)
            method: $(form).attr('method'),  // Get the form method (POST)
            data: $(form).serialize(),  // Get the form data
            dataType: "json",  // Expected data type from server
            success: function (response) {
                if (response.status) {
                    // Show success message instead of alert
                    $('#response-message').html('<div class="alert alert-success">' + response.message + '</div>');

                    window.location.href = response.redirect;

                } else {
                    // Invalid login - show message in #response-message
                    $('#response-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    // Loop through all errors and display them next to corresponding input fields
                    $.each(errors, function (key, value) {
                        $("#" + key + "_error").text(value[0]);
                    });
                } else if (xhr.status === 401) {
                    // Show login error (invalid credentials)
                    $('#response-message').html('<div class="alert alert-danger">' + xhr.responseJSON.message + '</div>');
                }
            }
        });
    }

    // Attach the common AJAX form handler to the login form (or any form)
    $(".ajax-form").submit(function (event) {
        event.preventDefault();  // Prevent default form submission
        handleFormSubmission(this);  // Call the AJAX function
    });

});