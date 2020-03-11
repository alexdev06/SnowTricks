// Add an image input
$('#add-image').click(function () {
    const index = +$('#widgets-counter-image').val();
    const tmpl = $('#trick_images').data('prototype').replace(/__name__/g, index);
    $('#trick_images').append(tmpl);
    $('#widgets-counter-image').val(index + 1)

    handleDeleteButtons()
});

// Add a video input
$('#add-video').click(function () {
    const index = +$('#widgets-counter-video').val();
    const tmpl = $('#trick_videos').data('prototype').replace(/__name__/g, index);
    $('#trick_videos').append(tmpl);
    $('#widgets-counter-video').val(index + 1)

    handleDeleteButtons()
});

// Remove video or image input 
function handleDeleteButtons() {
    $('a[data-action="delete"]').click(function (event) {
        event.preventDefault();
        const target = this.dataset.target;
        $(target).remove();
    })
}

// Count the real number of field
function updateCounter() {
    const count = +$('#trick_images div.form-group').length;
    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();

// {# Bootstrap 4 form theme bug correction on file input # }
$(document).on('change', '.custom-file-input', function () {
    let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
    $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
});