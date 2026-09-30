<?php
require_once '../../config/db.php';
require_once '../includes/helpers.php';

$rawToken = $_GET['token'] ?? '';

if (empty($rawToken)) {
    showError("Invalid Link", "This setup link is missing required information.");
    exit;
}

$hashedToken = hash('sha256', $rawToken);

$stmt = $conn->prepare("
    SELECT pr.reset_id, pr.expires_at, pr.used, p.first_name, p.last_name
    FROM professor_password_resets pr
    INNER JOIN professors p ON p.professor_id = pr.professor_id
    WHERE pr.token = ?
    LIMIT 1
");
$stmt->bind_param("s", $hashedToken);
$stmt->execute();
$reset = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reset) {
    showError("Invalid Link", "This setup link is invalid or does not exist.");
    exit;
}

if ((int) $reset['used'] === 1) {
    showError("Link Already Used", "This setup link has already been used. Please contact the admin if you need a new one.");
    exit;
}

if (strtotime($reset['expires_at']) < time()) {
    showError("Link Expired", "This setup link has expired. Please contact the admin for a new invite.");
    exit;
}

$professorName = trim($reset['first_name'] . ' ' . $reset['last_name']);
$error = $_GET['error'] ?? null;

include '../includes/header.php';
?>

<div class="page-transition min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-br from-[#060F1E] via-[#0A1931] to-[#1E4DB7] relative overflow-hidden">

    <!-- Glows -->
    <div class="hero-glow-blob w-96 h-96 bg-blue-500/20 -top-24 -left-24"></div>
    <div class="hero-glow-blob w-96 h-96 bg-amber-500/10 -bottom-24 -right-24"></div>

    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">

        <!-- Header -->
        <div class="bg-gradient-to-b from-[#0A1931] to-[#0E254A] py-8 px-6 flex flex-col items-center text-center relative border-b border-white/10">

            <div class="w-20 h-20 rounded-full bg-white/10 border-2 border-amber-400/40 p-1 flex items-center justify-center shadow-lg backdrop-blur-sm">
                <i class="bi bi-shield-lock-fill text-amber-300 text-3xl"></i>
            </div>

            <h1 class="text-white text-2xl font-extrabold tracking-tight mt-4">
                Set Your Password
            </h1>

            <p class="text-blue-200 text-xs mt-1 font-medium">
                Welcome, <?= htmlspecialchars($professorName) ?>
            </p>
        </div>

        <div class="p-8 space-y-6">

            <?php if (!empty($error)): ?>
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2.5">
                    <i class="bi bi-exclamation-circle-fill text-red-500 text-base"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="../../controllers/process_professor_set_password.php" method="POST" class="space-y-4 text-xs">

                <input type="hidden" name="token" value="<?= htmlspecialchars($rawToken) ?>">

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        New Password
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-key-fill text-base"></i>
                        </span>

                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm text-slate-800"
                        >
                    </div>
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Confirm Password
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="bi bi-shield-check text-base"></i>
                        </span>

                        <input
                            type="password"
                            name="password_confirm"
                            required
                            minlength="8"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm text-slate-800"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full btn-primary py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2 mt-2"
                >
                    <i class="bi bi-shield-check"></i>
                    <span>Set Password & Activate Account</span>
                </button>

            </form>
        </div>
    </div>
</div>