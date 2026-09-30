<?php

// POP-UP
function renderSwal($icon, $title, $text, $action = "window.history.back();")
{
    echo "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <script src='/enrollment_system/assets/js/swal.js'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    text: '$text',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $action
                });
            </script>
        </body>
        </html>";

    exit();
}

// CONFIRMATION POP-UP
function renderSwalConfirm($icon, $title, $text, $confirmAction, $cancelAction = "window.history.back();")
{
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <script src='/enrollment_system/assets/js/swal.js'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    $confirmAction
                } else {
                    $cancelAction
                }
            });
        </script>
    </body>
    </html>";
    exit();
}


// METHODS
function showError($title, $text, $action = 'window.history.back();') {
    renderSwal('error', $title, $text, $action);
}

function showSuccess($title, $text, $action = 'window.history.back();') {
    renderSwal('success', $title, $text, $action);
}

function showConfirm($title, $text, $confirmAction, $cancelAction = "window.history.back();") {
    renderSwalConfirm(
        'warning',
        $title,
        $text,
        $confirmAction,
        $cancelAction
    );
}
