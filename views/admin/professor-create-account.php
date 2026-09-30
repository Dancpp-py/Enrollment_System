<?php
require_once '../../config/db.php';
require_once '../includes/auth.php';

requireRole('Registrar', 'Super Admin');


$message = $_GET['message'] ?? null;
$messageType = $_GET['message_type'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_professor'])) {
    $professor_id = (int) ($_POST['professor_id'] ?? 0);
    $new_status = (int) ($_POST['new_status'] ?? -1);

    if ($professor_id <= 0 || !in_array($new_status, [0, 1], true)) {
        header(
            "Location: professor-accounts.php?" .
            http_build_query([
                'message_type' => 'error',
                'message' => 'Invalid professor account request.'
            ])
        );
        exit;
    }

    $updateStmt = $conn->prepare("
        UPDATE professors
        SET is_active = ?
        WHERE professor_id = ?
    ");

    if (!$updateStmt) {
        header(
            "Location: professor-create-account.php?" .
            http_build_query([
                'message_type' => 'error',
                'message' => 'Unable to prepare account status update.'
            ])
        );
        exit;
    }

    $updateStmt->bind_param("ii", $new_status, $professor_id);

    if ($updateStmt->execute()) {
        $accountStatus = $new_status === 1 ? 'activated' : 'disabled';
        $updateStmt->close();

        header(
            "Location: professor-create-account.php?" .
            http_build_query([
                'message_type' => 'success',
                'message' => "Professor account successfully {$accountStatus}."
            ])
        );
        exit;
    }

    $updateStmt->close();

    header(
        "Location: professor-accounts.php?" .
        http_build_query([
            'message_type' => 'error',
            'message' => 'Unable to update professor account.'
        ])
    );
    exit;
}

$professors = [];

$professorStmt = $conn->prepare("
    SELECT
        professor_id,
        username,
        last_name,
        first_name,
        middle_name,
        email,
        is_active
    FROM professors
    ORDER BY
        last_name ASC,
        first_name ASC
");

if ($professorStmt) {
    $professorStmt->execute();
    $professors = $professorStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $professorStmt->close();
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<main class="page-transition min-h-screen bg-slate-50 p-6 pt-20 lg:ml-72">
    <section class="mx-auto max-w-7xl space-y-8">
        <header class="rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-8 text-white shadow-xl">
            <section class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <section>
                    <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                        <i class="bi bi-people-fill"></i>
                        Account Management
                    </span>
                    <h1 class="text-3xl font-extrabold tracking-tight">
                        Professor Accounts
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-blue-100">
                        Manage professor accounts and their access to the system.
                    </p>
                </section>

                <button
                    type="button"
                    id="openCreateProfessorModal"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-[#0A1931] shadow-lg transition hover:bg-amber-300 hover:shadow-xl cursor-pointer"
                >
                    <i class="bi bi-person-plus-fill"></i>
                    Create Professor Account
                </button>
            </section>
        </header>

        <?php if (!empty($message)): ?>
            <?php $isSuccess = $messageType === 'success'; ?>
            <section
                class="<?= $isSuccess
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    : 'border-red-200 bg-red-50 text-red-700'
                ?> flex items-center gap-3 rounded-xl border px-4 py-3 text-sm"
            >
                <i
                    class="<?= $isSuccess
                        ? 'bi bi-check-circle-fill text-emerald-500'
                        : 'bi bi-exclamation-circle-fill text-red-500'
                    ?> text-base"
                ></i>
                <span>
                    <?= htmlspecialchars($message) ?>
                </span>
            </section>
        <?php endif; ?>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex flex-col gap-4 border-b border-white/10 bg-[#0A1931] px-6 py-5 md:flex-row md:items-center md:justify-between">
                <section>
                    <h2 class="text-2xl font-bold text-white">
                        Professor Accounts
                    </h2>
                    <p class="mt-1 text-sm text-blue-100">
                        <?= count($professors) ?>
                        <?= count($professors) === 1 ? 'professor account' : 'professor accounts' ?>
                        registered.
                    </p>
                </section>

                <section class="relative w-full md:w-80">
                    <label for="professorSearch" class="sr-only">
                        Search Professor Accounts
                    </label>
                    <input
                        type="text"
                        id="professorSearch"
                        placeholder="Search professor..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <i class="bi bi-search absolute left-3 top-3.5 text-slate-400"></i>
                </section>
            </header>

            <section class="overflow-x-auto">
                <table class="w-full min-w-[850px]">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Professor Name
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Email
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Username
                            </th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody id="professorTableBody" class="divide-y divide-slate-100">
                        <?php if (empty($professors)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center">
                                    <i class="bi bi-person-x mb-3 block text-4xl text-slate-300"></i>
                                    <h3 class="text-lg font-bold text-slate-700">
                                        No Professor Accounts
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Create a professor account to get started.
                                    </p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($professors as $professor): ?>
                                <?php
                                $professorName = trim(
                                    $professor['first_name'] . ' ' .
                                    ($professor['middle_name'] ?? '') . ' ' .
                                    $professor['last_name']
                                );
                                $isActive = (int) $professor['is_active'] === 1;
                                ?>
                                <tr class="professor-row transition hover:bg-slate-50">
                                    <td class="px-6 py-5">
                                        <section class="flex items-center gap-3">
                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#1E4DB7]">
                                                <i class="bi bi-person-fill text-lg"></i>
                                            </div>
                                            <section>
                                                <p class="font-semibold text-slate-800">
                                                    <?= htmlspecialchars($professorName) ?>
                                                </p>
                                                <span class="text-xs text-slate-400">
                                                    Professor ID: <?= (int) $professor['professor_id'] ?>
                                                </span>
                                            </section>
                                        </section>
                                    </td>

                                    <td class="px-6 py-5">
                                        <?php if (!empty($professor['email'])): ?>
                                            <span class="text-sm text-slate-700">
                                                <?= htmlspecialchars($professor['email']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm italic text-slate-400">
                                                No email
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-5">
                                        <?php if (!empty($professor['username'])): ?>
                                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700">
                                                <?= htmlspecialchars($professor['username']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                                                <i class="bi bi-hourglass-split"></i>
                                                Not set yet
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <form
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirmAccountStatus(
                                                <?= $isActive ? 'false' : 'true' ?>,
                                                '<?= htmlspecialchars($professorName, ENT_QUOTES) ?>'
                                            );"
                                        >
                                            <input type="hidden" name="toggle_professor" value="1">
                                            <input type="hidden" name="professor_id" value="<?= (int) $professor['professor_id'] ?>">
                                            <input type="hidden" name="new_status" value="<?= $isActive ? '0' : '1' ?>">

                                            <?php if ($isActive): ?>
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100 cursor-pointer"
                                                    title="Disable Professor Account"
                                                >
                                                    <i class="bi bi-person-dash-fill"></i>
                                                    Disable
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-100 cursor-pointer"
                                                    title="Activate Professor Account"
                                                >
                                                    <i class="bi bi-person-check-fill"></i>
                                                    Activate
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </section>
    </section>
</main>

<section id="createProfessorModal" class="fixed inset-0 z-[999] hidden" aria-hidden="true">
    <div id="createProfessorBackdrop" class="absolute inset-0 bg-slate-950/60 backdrop-blur-md"></div>

    <section class="relative z-10 flex min-h-screen items-center justify-center overflow-y-auto px-4 py-8">
        <div id="createProfessorDialog" class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl">
            <header class="relative border-b border-white/10 bg-gradient-to-b from-[#0A1931] to-[#0E254A] px-6 py-7 text-center">
                <button
                    type="button"
                    id="closeCreateProfessorModal"
                    class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-white transition hover:bg-white/20 cursor-pointer"
                    title="Close"
                    aria-label="Close"
                >
                    <i class="bi bi-x-lg"></i>
                </button>

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-amber-400/40 bg-white/10 shadow-lg">
                    <i class="bi bi-person-plus-fill text-amber-300 text-2xl"></i>
                </div>

                <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-white">
                    Add Professor
                </h2>
                <p class="mt-1 text-xs font-medium text-blue-200">
                    An account setup link will be emailed to them.
                </p>
            </header>

            <div class="p-8">
                <form
                    action="../../controllers/process_professor_create.php"
                    method="POST"
                    class="space-y-4 text-xs"
                >
                    <input type="hidden" name="action" value="create_teacher">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block font-bold uppercase tracking-wider text-slate-600">
                                Last Name
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="bi bi-person-fill text-base"></i>
                                </span>
                                <input
                                    type="text"
                                    name="last_name"
                                    required
                                    class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-4 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-600"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block font-bold uppercase tracking-wider text-slate-600">
                                First Name
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="bi bi-person-fill text-base"></i>
                                </span>
                                <input
                                    type="text"
                                    name="first_name"
                                    required
                                    class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-4 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-600"
                                >
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block font-bold uppercase tracking-wider text-slate-600">
                            Middle Name
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="bi bi-person-vcard-fill text-base"></i>
                            </span>
                            <input
                                type="text"
                                name="middle_name"
                                class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-4 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-600"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block font-bold uppercase tracking-wider text-slate-600">
                            Contact Number
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="bi bi-telephone-fill text-base"></i>
                            </span>
                            <input
                                type="text"
                                name="contact_number"
                                placeholder="09XXXXXXXXX"
                                class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-4 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-600"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block font-bold uppercase tracking-wider text-slate-600">
                            Email Address
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i class="bi bi-envelope-fill text-base"></i>
                            </span>
                            <input
                                type="email"
                                name="email"
                                required
                                class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-4 text-sm text-slate-800 outline-none focus:ring-2 focus:ring-blue-600"
                            >
                        </div>
                    </div>

                    <div class="flex gap-3 pt-3">
                        <button
                            type="submit"
                            class="btn-primary flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl py-3.5 text-sm font-bold tracking-wide shadow-lg transition duration-200"
                        >
                            <i class="bi bi-envelope-fill"></i>
                            <span>
                                Create & Send Invite
                            </span>
                        </button>

                        <button
                            type="button"
                            id="cancelCreateProfessor"
                            class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-300 px-5 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</section>

<script src="/enrollment_system/assets/js/swal.js"></script>
<script src="/enrollment_system/assets/js/professor-create-account.js"></script>