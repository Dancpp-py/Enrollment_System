    const calendarModal = document.getElementById('calendarModal');

    document.getElementById('openCalendarModal').addEventListener('click', () => {
        document.getElementById('calendarModalTitle').textContent = 'Add Calendar Event';
        document.getElementById('calendarAction').value = 'create_event';
        document.getElementById('calendarId').value = '';
        document.getElementById('calendarTitle').value = '';
        document.getElementById('calendarEventType').value = 'Exam';
        document.getElementById('calendarDate').value = '';
        document.getElementById('calendarStartTime').value = '';
        document.getElementById('calendarEndTime').value = '';
        document.getElementById('calendarDescription').value = '';
        document.getElementById('calendarSubmitButton').textContent = 'Save Event';

        calendarModal.classList.remove('hidden');
        calendarModal.classList.add('flex');
    });

    function closeCalendar() {
        calendarModal.classList.add('hidden');
        calendarModal.classList.remove('flex');
    }

    document.getElementById('closeCalendarModal').addEventListener('click', closeCalendar);
    document.getElementById('cancelCalendarModal').addEventListener('click', closeCalendar);

    function editCalendarEvent(event) {
        document.getElementById('calendarModalTitle').textContent = 'Edit Calendar Event';
        document.getElementById('calendarAction').value = 'update_event';
        document.getElementById('calendarId').value = event.calendar_id;
        document.getElementById('calendarTitle').value = event.title || '';
        document.getElementById('calendarEventType').value = event.event_type || 'Other';
        document.getElementById('calendarDate').value = event.event_date || '';
        document.getElementById('calendarStartTime').value = event.start_time || '';
        document.getElementById('calendarEndTime').value = event.end_time || '';
        document.getElementById('calendarDescription').value = event.description || '';
        document.getElementById('calendarSubmitButton').textContent = 'Update Event';

        calendarModal.classList.remove('hidden');
        calendarModal.classList.add('flex');
    }

    function deleteCalendarEvent(calendarId, title) {
        Swal.fire({
            title: 'Delete Calendar Event?',
            text: 'Are you sure you want to delete "' + title + '"?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteCalendarId').value = calendarId;
                document.getElementById('deleteCalendarForm').submit();
            }
        });
    }

    calendarModal.addEventListener('click', (event) => {
        if (event.target === calendarModal) {
            closeCalendar();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeCalendar();
        }
    });