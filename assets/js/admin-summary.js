async function notifyMissingRequirements(enrollmentId, buttonEl) {

    const originalText = buttonEl.innerHTML;
    buttonEl.disabled = true;
    buttonEl.innerHTML = '<i class="bi bi-hourglass-split"></i>';

    try {
        const formData = new FormData();
        formData.append('enrollment_id', enrollmentId);

        const response = await fetch('/enrollment_system/controllers/notify_missing_requirements.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Notification Sent',
                text: data.message,
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Not Sent',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }

    } catch (err) {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong while sending the notification.',
            confirmButtonText: 'OK'
        });
    } finally {
        buttonEl.disabled = false;
        buttonEl.innerHTML = originalText;
    }
}