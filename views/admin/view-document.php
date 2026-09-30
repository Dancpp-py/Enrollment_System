<?php
$page_title = "Document Preview - Masinag SHS";
include '../includes/header.php';

$file = $_GET['file'] ?? '';
?>

<div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 bg-black/70 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl h-[92vh] flex flex-col overflow-hidden border border-slate-200">
        
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-[#0A1931] to-[#1E4DB7] text-white">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-file-earmark-text-fill text-amber-300 text-lg"></i>
                <h2 class="text-base font-bold tracking-tight">
                    Document Viewer
                </h2>
            </div>

            <button
                onclick="window.close()"
                class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-x-lg"></i> Close Window
            </button>
        </div>

        <div class="flex-1 bg-slate-100 p-2 sm:p-4">
            <iframe
                src="../../<?= htmlspecialchars($file) ?>"
                class="w-full h-full rounded-2xl border border-slate-200 shadow-inner bg-white"
            ></iframe>
        </div>

    </div>
</div>