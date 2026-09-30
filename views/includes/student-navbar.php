<?php
if (empty($_SESSION['student_id'])) {
    header("Location: /enrollment_system/student-teacher-login");
    exit;
}

$student_id = (int) $_SESSION['student_id'];

$studentNavbarStmt = $conn->prepare("
    SELECT
        s.student_number,
        s.first_name,
        s.last_name
    FROM students s
    WHERE s.student_id = ?
    LIMIT 1
");
$studentNavbarStmt->bind_param("i", $student_id);
$studentNavbarStmt->execute();
$studentNavbarResult = $studentNavbarStmt->get_result();
$studentNavbar = $studentNavbarResult->fetch_assoc();
$studentNavbarStmt->close();

$studentFirstName = $studentNavbar['first_name'] ?? 'Student';
$studentLastName = $studentNavbar['last_name'] ?? '';

$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitles = [
    'student-dashboard.php' => 'Dashboard',
    'student-calendar.php' => 'Academic Calendar',
    'student-curriculum.php' => 'Curriculum',
    'student-subject-view.php' => 'Subjects',
    'student-material-view.php' => 'Subjects',
    'student-grades.php' => 'Grades',
    'student-quiz.php' => 'Quizzes'
];

$pageTitle = $pageTitles[$currentPage] ?? 'Dashboard';

$notificationStmt = $conn->prepare("
    SELECT
        notification_id,
        lms_class_id,
        notification_type,
        reference_id,
        title,
        message,
        is_read,
        created_at
    FROM lms_notifications
    WHERE student_id = ?
    ORDER BY created_at DESC
    LIMIT 10
");
$notificationStmt->bind_param("i", $student_id);
$notificationStmt->execute();
$notificationResult = $notificationStmt->get_result();

$notifications = [];
$unreadNotificationCount = 0;

while ($notification = $notificationResult->fetch_assoc()) {
    $notifications[] = $notification;

    if ((int) $notification['is_read'] === 0) {
        $unreadNotificationCount++;
    }
}

$notificationStmt->close();
?>

<nav class="fixed top-0 left-0 right-0 lg:left-72 z-50 h-16 bg-white border-b border-slate-200">
    <div class="h-full flex items-center justify-between gap-4 px-4 sm:px-6 lg:px-8 pl-16 sm:pl-16 lg:pl-8">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#0A1931] truncate">
                <?= htmlspecialchars($pageTitle) ?>
            </h1>
            <p class="hidden sm:block text-xs text-slate-500 mt-0.5">
                Masinag Senior High School
            </p>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <div class="relative">
                <button
                    type="button"
                    id="notification-button"
                    class="relative cursor-pointer w-10 h-10 flex items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-[#1E4DB7] transition"
                    aria-label="Notifications">
                    <i class="bi bi-bell-fill text-lg"></i>

                    <?php if ($unreadNotificationCount > 0): ?>
                        <span
                            id="notification-count"
                            class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 flex items-center justify-center rounded-full bg-rose-500 text-white text-[10px] font-extrabold border-2 border-white">
                            <?= $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount ?>
                        </span>
                    <?php endif; ?>
                </button>

                <div
                    id="notification-dropdown"
                    class="hidden absolute right-0 top-12 w-[calc(100vw-2rem)] sm:w-[360px] max-w-[360px] bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden">

                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-800">
                                Notifications
                            </h2>
                            <p class="text-xs text-slate-500">
                                Your latest updates
                            </p>
                        </div>

                        <?php if ($unreadNotificationCount > 0): ?>
                            <button
                                type="button"
                                id="mark-all-read"
                                class="text-xs cursor-pointer font-bold text-[#1E4DB7] hover:text-blue-800 transition">
                                Mark all as read
                            </button>
                        <?php endif; ?>
                    </div>

                    <div id="notification-list" class="max-h-[420px] overflow-y-auto">
                        <?php if (empty($notifications)): ?>
                            <div class="px-5 py-10 text-center">
                                <i class="bi bi-bell-slash text-2xl text-slate-300"></i>
                                <p class="text-sm font-semibold text-slate-500 mt-2">
                                    No notifications
                                </p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                                <?php
                                $isUnread = (int) $notification['is_read'] === 0;

                                if ($notification['notification_type'] === 'Material') {
                                    $notificationTarget = '/enrollment_system/views/student/student-material-view.php?lms_class_id=' . (int) $notification['lms_class_id'];
                                } elseif ($notification['notification_type'] === 'Quiz') {
                                    $notificationTarget = '/enrollment_system/views/student/student-quiz.php';
                                } else {
                                    $notificationTarget = '/enrollment_system/views/student/student-dashboard.php';
                                }
                                ?>

                                <div
                                    class="notification-item <?= $isUnread ? 'bg-blue-50' : 'bg-white' ?> px-4 py-4 border-b border-slate-100 hover:bg-slate-50 transition cursor-pointer"
                                    data-notification-id="<?= (int) $notification['notification_id'] ?>"
                                    data-unread="<?= $isUnread ? '1' : '0' ?>"
                                    data-target="<?= htmlspecialchars($notificationTarget) ?>">

                                    <div class="flex gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1E4DB7] flex items-center justify-center shrink-0">
                                            <?php if ($notification['notification_type'] === 'Material'): ?>
                                                <i class="bi bi-file-earmark-text-fill"></i>
                                            <?php else: ?>
                                                <i class="bi bi-clipboard-check-fill"></i>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <p class="text-sm font-bold text-slate-800">
                                                    <?= htmlspecialchars($notification['title']) ?>
                                                </p>

                                                <?php if ($isUnread): ?>
                                                    <span class="notification-unread-dot w-2 h-2 mt-1.5 rounded-full bg-blue-600 shrink-0"></span>
                                                <?php endif; ?>
                                            </div>

                                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                                <?= htmlspecialchars($notification['message']) ?>
                                            </p>

                                            <p class="text-[10px] text-slate-400 mt-2">
                                                <?= htmlspecialchars(date('M d, Y h:i A', strtotime($notification['created_at']))) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="hidden sm:block h-8 w-px bg-slate-200"></div>

            <div class="hidden sm:flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#0A1931] text-white flex items-center justify-center font-bold">
                    <?= strtoupper(substr($studentFirstName, 0, 1)) ?>
                </div>

                <div class="leading-tight">
                    <p class="text-sm font-bold text-slate-800">
                        <?= htmlspecialchars($studentFirstName . ' ' . $studentLastName) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
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
</script>