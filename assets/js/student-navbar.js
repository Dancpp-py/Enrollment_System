const notificationButton = document.getElementById('notification-button');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationItems = document.querySelectorAll('.notification-item');
    const markAllReadButton = document.getElementById('mark-all-read');

    const notificationController = '/enrollment_system/controllers/process_student_notifications.php';

    function updateNotificationCount(count) {
        let badge = document.getElementById('notification-count');

        if (count <= 0) {
            if (badge) {
                badge.remove();
            }

            return;
        }

        if (!badge) {
            badge = document.createElement('span');
            badge.id = 'notification-count';
            badge.className = 'absolute -top-1 -right-1 min-w-[20px] h-5 px-1 flex items-center justify-center rounded-full bg-rose-500 text-white text-[10px] font-extrabold border-2 border-white';
            notificationButton.appendChild(badge);
        }

        badge.textContent = count > 99 ? '99+' : count;
    }

    async function markNotificationRead(notificationItem) {
        const notificationId = notificationItem.dataset.notificationId;

        if (!notificationId) {
            console.error('Notification ID is missing.');
            return false;
        }

        try {
            const response = await fetch(notificationController, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: new URLSearchParams({
                    action: 'mark_read',
                    notification_id: notificationId
                })
            });

            const responseText = await response.text();

            let result;

            try {
                result = JSON.parse(responseText);
            } catch (error) {
                console.error('Invalid server response:', responseText);
                return false;
            }

            if (!response.ok || !result.success) {
                console.error(
                    'Notification update failed:',
                    result.message || 'Unknown error'
                );
                return false;
            }

            notificationItem.dataset.unread = '0';
            notificationItem.classList.remove('bg-blue-50');
            notificationItem.classList.add('bg-white');

            const unreadDot = notificationItem.querySelector(
                '.notification-unread-dot'
            );

            if (unreadDot) {
                unreadDot.remove();
            }

            updateNotificationCount(result.unread_count);

            return true;
        } catch (error) {
            console.error('Notification request failed:', error);
            return false;
        }
    }

    if (notificationButton) {
        notificationButton.addEventListener('click', function(event) {
            event.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
    }

    if (notificationDropdown) {
        notificationDropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    document.addEventListener('click', function() {
        if (notificationDropdown) {
            notificationDropdown.classList.add('hidden');
        }
    });

    notificationItems.forEach(function(notificationItem) {
        notificationItem.addEventListener('click', async function(event) {
            event.preventDefault();

            const target = this.dataset.target;
            const isUnread = this.dataset.unread === '1';

            if (isUnread) {
                const success = await markNotificationRead(this);

                if (!success) {
                    return;
                }
            }

            if (notificationDropdown) {
                notificationDropdown.classList.add('hidden');
            }

            if (target) {
                window.location.href = target;
            }
        });
    });

    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', async function(event) {
            event.preventDefault();
            markAllReadButton.disabled = true;

            try {
                const response = await fetch(notificationController, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: new URLSearchParams({
                        action: 'mark_all_read'
                    })
                });

                const responseText = await response.text();

                let result;

                try {
                    result = JSON.parse(responseText);
                } catch (error) {
                    console.error('Invalid server response:', responseText);
                    markAllReadButton.disabled = false;
                    return;
                }

                if (!response.ok || !result.success) {
                    console.error(
                        'Mark all failed:',
                        result.message || 'Unknown error'
                    );
                    markAllReadButton.disabled = false;
                    return;
                }

                document.querySelectorAll(
                    '.notification-item[data-unread="1"]'
                ).forEach(function(notificationItem) {
                    notificationItem.dataset.unread = '0';
                    notificationItem.classList.remove('bg-blue-50');
                    notificationItem.classList.add('bg-white');

                    const unreadDot = notificationItem.querySelector(
                        '.notification-unread-dot'
                    );

                    if (unreadDot) {
                        unreadDot.remove();
                    }
                });

                updateNotificationCount(0);
                markAllReadButton.remove();
            } catch (error) {
                console.error('Mark all request failed:', error);
                markAllReadButton.disabled = false;
            }
        });
    }