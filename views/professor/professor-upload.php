<?php
require_once "../../config/db.php";
require_once "../includes/auth.php";

$professor_id = currentProfessorId();

$syncStmt = $conn->prepare("
    INSERT IGNORE INTO lms_classes (
        schedule_id,
        status
    )
    SELECT
        cs.schedule_id,
        'Active'
    FROM class_schedules cs

    INNER JOIN sections sec
        ON sec.section_id = cs.section_id

    INNER JOIN school_years sy
        ON sy.school_year_id = sec.school_year_id

    LEFT JOIN lms_classes existing_lms
        ON existing_lms.schedule_id = cs.schedule_id

    WHERE cs.professor_id = ?
      AND sy.is_active = 1
      AND existing_lms.lms_class_id IS NULL
");

$syncStmt->bind_param("i", $professor_id);
$syncStmt->execute();
$syncStmt->close();
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/enrollment_system/uploads/';

function getFileExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function formatFileSize($bytes)
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }

    if ($bytes < 1024 * 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }

    if ($bytes < 1024 * 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 1) . ' MB';
    }

    return number_format($bytes / (1024 * 1024 * 1024), 1) . ' GB';
}

function getMaterialIcon($extension)
{
    return match ($extension) {
        'pdf' => 'bi-file-earmark-pdf-fill',
        'doc', 'docx' => 'bi-file-earmark-word-fill',
        'xls', 'xlsx' => 'bi-file-earmark-excel-fill',
        'ppt', 'pptx' => 'bi-file-earmark-ppt-fill',
        'jpg', 'jpeg', 'png' => 'bi-file-earmark-image-fill',
        'zip' => 'bi-file-earmark-zip-fill',
        default => 'bi-file-earmark-fill'
    };
}

function getMaterialIconClass($extension)
{
    return match ($extension) {
        'pdf' => 'bg-red-100 text-red-600',
        'doc', 'docx' => 'bg-blue-100 text-[#1E4DB7]',
        'xls', 'xlsx' => 'bg-emerald-100 text-emerald-600',
        'ppt', 'pptx' => 'bg-orange-100 text-orange-600',
        'jpg', 'jpeg', 'png' => 'bg-purple-100 text-purple-600',
        'zip' => 'bg-amber-100 text-amber-600',
        default => 'bg-slate-100 text-slate-600'
    };
}

$message = '';
$messageType = '';

if (isset($_GET['message'], $_GET['message_type'])) {
    $message = $_GET['message'];
    $messageType = $_GET['message_type'];
}

$classStmt = $conn->prepare("
    SELECT
        lc.lms_class_id,
        sub.subject_code,
        sub.subject_name,
        sec.section_id,
        sec.section_name,
        sec.grade_level,
        str.strand_name,
        cs.day_of_week,
        cs.start_time,
        cs.end_time,
        r.room_name
    FROM lms_classes lc
    INNER JOIN class_schedules cs
        ON cs.schedule_id = lc.schedule_id
    INNER JOIN curriculum c
        ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub
        ON sub.subject_id = c.subject_id
    INNER JOIN sections sec
        ON sec.section_id = cs.section_id
    INNER JOIN strands str
        ON str.strand_id = sec.strand_id
    INNER JOIN school_years sy
        ON sy.school_year_id = sec.school_year_id
    LEFT JOIN rooms r
        ON r.room_id = cs.room_id
    WHERE cs.professor_id = ?
      AND lc.status = 'Active'
      AND sy.is_active = 1
    ORDER BY
        sub.subject_name,
        sec.grade_level,
        sec.section_name
");

$classStmt->bind_param("i", $professor_id);
$classStmt->execute();
$classes = $classStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$classStmt->close();

$materialsStmt = $conn->prepare("
    SELECT
        lm.material_id,
        lm.lms_class_id,
        lm.title,
        lm.description,
        lm.original_file_name,
        lm.stored_file_name,
        lm.file_path,
        lm.file_type,
        lm.file_size,
        lm.created_at,
        sub.subject_code,
        sub.subject_name,
        sec.section_name,
        sec.grade_level,
        str.strand_name
    FROM lms_materials lm
    INNER JOIN lms_classes lc
        ON lc.lms_class_id = lm.lms_class_id
    INNER JOIN class_schedules cs
        ON cs.schedule_id = lc.schedule_id
    INNER JOIN curriculum c
        ON c.curriculum_id = cs.curriculum_id
    INNER JOIN subjects sub
        ON sub.subject_id = c.subject_id
    INNER JOIN sections sec
        ON sec.section_id = cs.section_id
    INNER JOIN strands str
        ON str.strand_id = sec.strand_id
    WHERE lm.uploaded_by = ?
      AND cs.professor_id = ?
    ORDER BY lm.created_at DESC
");

$materialsStmt->bind_param("ii", $professor_id, $professor_id);
$materialsStmt->execute();
$materials = $materialsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$materialsStmt->close();

include '../includes/header.php';
include '../includes/professor-sidebar.php';
?>

<main class="min-h-screen p-6 pt-20 lg:ml-72">
    <section class="mx-auto max-w-7xl space-y-8">
        <header class="rounded-3xl bg-gradient-to-r from-[#0A1931] via-[#132A52] to-[#1E4DB7] p-6 text-white shadow-xl">
            <section>
                <span class="mb-2 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-amber-300">
                    <i class="bi bi-folder-fill"></i>
                    Teacher Portal
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight text-white">
                    Learning Materials
                </h1>
                <span class="mt-1 block max-w-2xl text-sm text-blue-100">
                    Upload and manage learning materials that students can view or download.
                </span>
            </section>
        </header>

        <?php if ($message !== ''): ?>
            <section class="<?= $messageType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' ?> rounded-xl border px-5 py-4">
                <section class="flex items-center gap-3">
                    <i class="bi <?= $messageType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' ?>"></i>
                    <span class="text-sm font-semibold">
                        <?= htmlspecialchars($message) ?>
                    </span>
                </section>
            </section>
        <?php endif; ?>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="bg-[#0A1931] px-6 py-5">
                <h2 class="text-2xl font-bold text-white">
                    Upload Learning Material
                </h2>
                <span class="mt-1 block text-sm text-blue-100">
                    Add a file for your students to access.
                </span>
            </header>

            <form
                id="material-upload-form"
                method="POST"
                action="../../controllers/process_professor_upload.php"
                enctype="multipart/form-data"
                class="space-y-6 p-6"
            >
                <input type="hidden" name="upload_material" value="1">

                <section>
                    <label for="material-class" class="mb-2 block text-sm font-semibold text-slate-700">
                        Subject & Section
                    </label>
                    <select
                        id="material-class"
                        name="lms_class_id"
                        required
                        class="w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Select Subject / Section</option>
                        <?php foreach ($classes as $class): ?>
                            <?php
                            $sectionLabel = $class['strand_name'] . ' ' . str_replace('Grade ', '', $class['grade_level']) . '-' . $class['section_name'];
                            ?>
                            <option value="<?= (int) $class['lms_class_id'] ?>">
                                <?= htmlspecialchars($class['subject_code'] . ' - ' . $class['subject_name'] . ' | ' . $sectionLabel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </section>

                <section>
                    <label for="material-title" class="mb-2 block text-sm font-semibold text-slate-700">
                        Material Title
                    </label>
                    <input
                        id="material-title"
                        name="title"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="e.g. Introduction to Functions"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    >
                </section>

                <section>
                    <label for="material-description" class="mb-2 block text-sm font-semibold text-slate-700">
                        Description
                        <span class="font-normal text-slate-400">(Optional)</span>
                    </label>
                    <textarea
                        id="material-description"
                        name="description"
                        rows="3"
                        placeholder="Add a short description about this material..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </section>

                <section>
                    <label for="material-file" class="mb-2 block text-sm font-semibold text-slate-700">
                        File
                    </label>
                    <label
                        for="material-file"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-blue-400 hover:bg-blue-50/40"
                    >
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-[#1E4DB7]">
                            <i class="bi bi-cloud-arrow-up-fill text-2xl"></i>
                        </span>
                        <span class="mt-4 text-base font-semibold text-slate-700">
                            Click to select a file
                        </span>
                        <span class="mt-1 text-sm text-slate-400">
                            PPT, PPTX, PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP
                        </span>
                        <span
                            id="selected-file-name"
                            class="mt-3 hidden rounded-lg bg-white px-3 py-2 text-sm font-medium text-[#1E4DB7] shadow-sm"
                        ></span>
                    </label>
                    <input
                        id="material-file"
                        name="material_file"
                        type="file"
                        required
                        accept=".ppt,.pptx,.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
                        class="hidden"
                    >
                </section>

                <section class="flex justify-end border-t border-slate-200 pt-6">
                    <button
                        type="submit"
                        class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-[#1E4DB7] px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-[#132A52]"
                    >
                        <i class="bi bi-cloud-arrow-up-fill mr-2"></i>
                        Upload Material
                    </button>
                </section>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex flex-col gap-4 border-b border-white/10 bg-[#0A1931] px-6 py-5 md:flex-row md:items-center md:justify-between">
                <section>
                    <h2 class="text-2xl font-bold text-white">
                        Uploaded Materials
                    </h2>
                    <span class="mt-1 block text-sm text-blue-100">
                        Materials currently available to your students.
                    </span>
                </section>

                <section class="relative w-full md:w-72">
                    <label for="material-search" class="sr-only">Search Materials</label>
                    <input
                        id="material-search"
                        type="text"
                        placeholder="Search materials..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-slate-700 outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <i class="bi bi-search absolute left-3 top-4 text-slate-400"></i>
                </section>
            </header>

            <section class="overflow-x-auto">
                <table class="w-full min-w-[1050px]">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">File</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Material</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Subject</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Section</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Date Uploaded</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">Action</th>
                        </tr>
                    </thead>

                    <tbody id="materials-list">
                        <?php if (empty($materials)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="bi bi-folder2-open mb-3 block text-4xl text-slate-300"></i>
                                    <h3 class="text-lg font-bold text-slate-700">
                                        No learning materials yet
                                    </h3>
                                    <span class="mt-1 block text-sm text-slate-500">
                                        Upload your first learning material above.
                                    </span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($materials as $material): ?>
                                <?php
                                $extension = getFileExtension($material['original_file_name']);
                                $sectionLabel = $material['strand_name'] . ' ' . str_replace('Grade ', '', $material['grade_level']) . '-' . $material['section_name'];
                                $searchText = strtolower(
                                    $material['title'] . ' ' .
                                    $material['subject_name'] . ' ' .
                                    $material['subject_code'] . ' ' .
                                    $sectionLabel . ' ' .
                                    $material['original_file_name']
                                );
                                ?>
                                <tr
                                    class="material-row border-t border-slate-200 transition hover:bg-slate-50"
                                    data-search="<?= htmlspecialchars($searchText) ?>"
                                >
                                    <td class="px-6 py-4">
                                        <section class="flex items-center gap-3">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl <?= getMaterialIconClass($extension) ?>">
                                                <i class="bi <?= getMaterialIcon($extension) ?> text-lg"></i>
                                            </span>
                                            <section>
                                                <span class="block max-w-xs truncate text-sm font-semibold text-slate-800">
                                                    <?= htmlspecialchars($material['original_file_name']) ?>
                                                </span>
                                                <span class="block text-xs text-slate-400">
                                                    <?= formatFileSize($material['file_size']) ?>
                                                </span>
                                            </section>
                                        </section>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="font-medium text-slate-700">
                                            <?= htmlspecialchars($material['title']) ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?= htmlspecialchars($material['subject_name']) ?>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?= htmlspecialchars($sectionLabel) ?>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <?= date('F d, Y', strtotime($material['created_at'])) ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <section class="flex items-center justify-center gap-2">
                                            <a
                                                href="material-view.php?id=<?= (int) $material['material_id'] ?>"
                                                target="_blank"
                                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100"
                                                title="View"
                                            >
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <button
                                                type="button"
                                                class="edit-material-button inline-flex items-center justify-center rounded-xl bg-[#1E4DB7] px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#132A52]"
                                                data-id="<?= (int) $material['material_id'] ?>"
                                                data-title="<?= htmlspecialchars($material['title'], ENT_QUOTES) ?>"
                                                data-description="<?= htmlspecialchars($material['description'] ?? '', ENT_QUOTES) ?>"
                                                data-file="<?= htmlspecialchars($material['original_file_name'], ENT_QUOTES) ?>"
                                                title="Edit"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="delete-material-button inline-flex items-center justify-center rounded-xl bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100"
                                                data-id="<?= (int) $material['material_id'] ?>"
                                                data-title="<?= htmlspecialchars($material['title'], ENT_QUOTES) ?>"
                                                title="Delete"
                                            >
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </section>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <section id="no-material-results" class="hidden px-6 py-12 text-center">
                <i class="bi bi-search mb-3 block text-4xl text-slate-300"></i>
                <h3 class="text-lg font-bold text-slate-700">No materials found</h3>
                <span class="mt-1 block text-sm text-slate-500">
                    Try searching for another material.
                </span>
            </section>
        </section>
    </section>
</main>

<section
    id="edit-material-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4"
>
    <section class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-xl">
        <header class="flex items-center justify-between bg-[#0A1931] px-6 py-5">
            <section>
                <h2 class="text-xl font-bold text-white">Edit Learning Material</h2>
                <span class="mt-1 block text-sm text-blue-100">
                    Update the material information or replace the file.
                </span>
            </section>

            <button
                type="button"
                id="close-edit-modal"
                class="flex h-9 w-9 items-center justify-center rounded-xl text-white transition hover:bg-white/10"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        </header>

        <form
            id="edit-material-form"
            method="POST"
            action="../../controllers/process_professor_upload.php"
            enctype="multipart/form-data"
            class="space-y-5 p-6"
        >
            <input type="hidden" name="update_material" value="1">
            <input type="hidden" id="edit-material-id" name="material_id">

            <section>
                <label for="edit-material-title" class="mb-2 block text-sm font-semibold text-slate-700">
                    Material Title
                </label>
                <input
                    id="edit-material-title"
                    name="title"
                    type="text"
                    required
                    maxlength="255"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
            </section>

            <section>
                <label for="edit-material-description" class="mb-2 block text-sm font-semibold text-slate-700">
                    Description
                </label>
                <textarea
                    id="edit-material-description"
                    name="description"
                    rows="3"
                    class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </section>

            <section class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <span class="block text-xs font-bold uppercase tracking-wide text-slate-400">
                    Current File
                </span>
                <section class="mt-2 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-[#1E4DB7]">
                        <i class="bi bi-file-earmark-fill"></i>
                    </span>
                    <span id="edit-current-file" class="text-sm font-semibold text-slate-700">
                        —
                    </span>
                </section>
            </section>

            <section>
                <label for="edit-material-file" class="mb-2 block text-sm font-semibold text-slate-700">
                    Replace File
                    <span class="font-normal text-slate-400">(Optional)</span>
                </label>
                <label
                    for="edit-material-file"
                    class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-4 transition hover:border-blue-400 hover:bg-blue-50/40"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-[#1E4DB7]">
                        <i class="bi bi-arrow-repeat"></i>
                    </span>
                    <section>
                        <span class="block text-sm font-semibold text-slate-700">
                            Click to replace the file
                        </span>
                        <span id="edit-selected-file-name" class="block text-xs text-slate-400">
                            No new file selected
                        </span>
                    </section>
                </label>
                <input
                    id="edit-material-file"
                    name="replacement_file"
                    type="file"
                    accept=".ppt,.pptx,.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip"
                    class="hidden"
                >
            </section>

            <footer class="flex justify-end gap-3 border-t border-slate-200 pt-5">
                <button
                    type="button"
                    id="cancel-edit-modal"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="rounded-xl bg-[#1E4DB7] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#132A52]"
                >
                    <i class="bi bi-save2-fill mr-2"></i>
                    Save Changes
                </button>
            </footer>
        </form>
    </section>
</section>

<section
    id="delete-material-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4"
>
    <section class="w-full max-w-md rounded-2xl bg-white shadow-xl">
        <section class="p-6 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="bi bi-trash-fill text-xl"></i>
            </div>
            <h2 class="mt-4 text-xl font-bold text-slate-800">Delete Material?</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">
                Are you sure you want to delete
                <strong id="delete-material-name" class="text-slate-700">this material</strong>?
                This action cannot be undone.
            </p>

            <form
                method="POST"
                action="../../controllers/process_professor_upload.php"
                id="delete-material-form"
            >
                <input type="hidden" name="delete_material" value="1">
                <input type="hidden" id="delete-material-id" name="material_id">

                <footer class="mt-6 flex justify-center gap-3">
                    <button
                        type="button"
                        id="cancel-delete"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
                    >
                        <i class="bi bi-trash-fill mr-2"></i>
                        Delete
                    </button>
                </footer>
            </form>
        </section>
    </section>
</section>

<script src="/enrollment_system/assets/js/professor-upload.js"></script>
<script src="/enrollment_system/assets/js/swal.js"></script>