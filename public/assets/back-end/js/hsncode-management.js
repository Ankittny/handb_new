"use strict";

let getYesWord = $('#message-yes-word').data('text');
let getCancelWord = $('#message-cancel-word').data('text');
let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');

$('.delete-hsn').on('click', function () {
    let hsnId = $(this).attr("id");
    var urlElement = document.getElementById('url-container');
    var deleteUrl = urlElement.getAttribute('data-delete-url');
    Swal.fire({
        title: messageAreYouSureDeleteThis,
        text: messageYouWillNotAbleRevertThis,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: getYesWord,
        cancelButtonText: getCancelWord,
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: deleteUrl, 
                type: 'POST',
                data: {
                    hsnId: hsnId,
                },
                success: function(response) {
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while canceling the order.');
                }
            });
        }
        else{
           
        }
    })
});