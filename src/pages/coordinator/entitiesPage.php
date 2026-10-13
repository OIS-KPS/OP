<!-- src/pages/coordinator/entitiesPage.php -->
<?php
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
$view = $view ?? 'active';
$viewParam = $view === 'archived' ? '&view=archived' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Entities - OJT Portal</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/ICS-PORTAL/public/css/style.css">
    <style>
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: subpixel-antialiased;
            -moz-osx-font-smoothing: auto;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 subpixel-antialiased selection:bg-[#0F2854] selection:text-white">

<div class="flex min-h-screen">
    
    <!-- Sidebar Component -->
    <?php include __DIR__ . '/../../components/coordinator_sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">

        <!-- Header Component -->
        <?php include __DIR__ . '/../../components/header.php'; ?>

        <main class="p-8 max-w-[1400px] w-full mx-auto space-y-6 flex-1 relative">

            <!-- Alerts -->
            <?php if (!empty($message)): ?>
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs px-4 py-3 rounded-2xl flex items-center justify-between font-bold shadow-xs">
                    <span>✓ <?= e($message); ?></span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-extrabold cursor-pointer">✕</button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="bg-rose-50 border border-rose-300 text-rose-900 text-xs px-4 py-3 rounded-2xl flex items-center justify-between font-bold shadow-xs">
                    <span>⚠ <?= e($error); ?></span>
                    <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 font-extrabold cursor-pointer">✕</button>
                </div>
            <?php endif; ?>

            <!-- Page Header Card -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-7 rounded-2xl border border-slate-200/90 shadow-xs">
                <div>
                    <h1 class="text-base font-extrabold text-slate-950 tracking-tight leading-snug">Entity Dictionary</h1>
                    <p class="text-xs font-semibold text-slate-600 mt-1">
                        <?= $view === 'archived'
                            ? 'Archived entities are excluded from extraction. Restore them or delete them permanently.'
                            : 'Manage technical skills, software tools, and task entities used to scan student accomplishment reports.'; ?>
                    </p>
                </div>

                <?php if ($view !== 'archived'): ?>
                    <button onclick="openModal('addEntityModal')" class="px-5 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer shrink-0">
                        <span class="text-sm font-bold">+</span> Add Entities
                    </button>
                <?php endif; ?>
            </div>

            <!-- Filter & Search Controls -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <form method="GET" action="entities.php" class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
                    <input type="hidden" name="view" value="<?= e($view); ?>">

                    <!-- View switch + Quick Activity Filters -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 text-xs">
                        <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1 shrink-0">
                            <a href="entities.php" class="px-3.5 py-1.5 rounded-lg font-bold transition-all <?= ($view === 'active') ? 'bg-[#0F2854] text-white shadow-xs' : 'text-slate-600 hover:bg-white'; ?>">
                                Active <span class="ml-1 opacity-90 font-bold"><?= (int) ($activeCount ?? 0); ?></span>
                            </a>
                            <a href="entities.php?view=archived" class="px-3.5 py-1.5 rounded-lg font-bold transition-all <?= ($view === 'archived') ? 'bg-rose-700 text-white shadow-xs' : 'text-slate-600 hover:bg-white'; ?>">
                                Archive <span class="ml-1 opacity-90 font-bold"><?= (int) ($archivedCount ?? 0); ?></span>
                            </a>
                        </div>

                        <span class="mx-0.5 h-6 w-px bg-slate-300 shrink-0"></span>

                        <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
                            <a href="entities.php<?= $view === 'archived' ? '?view=archived' : ''; ?>" class="px-3.5 py-1.5 rounded-lg font-bold transition-all whitespace-nowrap <?= ($typeFilter === 'All') ? 'bg-[#0F2854] text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                All <span class="ml-1 opacity-90 font-bold"><?= count($entities); ?></span>
                            </a>
                            <a href="entities.php?it_related=yes<?= $viewParam; ?>" class="px-3.5 py-1.5 rounded-lg font-bold transition-all whitespace-nowrap <?= ($typeFilter === 'yes') ? 'bg-[#0F2854] text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                IT
                            </a>
                            <a href="entities.php?it_related=no<?= $viewParam; ?>" class="px-3.5 py-1.5 rounded-lg font-bold transition-all whitespace-nowrap <?= ($typeFilter === 'no') ? 'bg-[#0F2854] text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                                Clerical
                            </a>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="flex items-center gap-2">
                        <div class="relative w-full sm:w-64">
                            <input type="text" name="search" value="<?= e($search); ?>" placeholder="Search entities..." class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-3 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854]">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>

                        <?php /* No category filter: category is no longer set anywhere
                               in the app, so filtering on it would silently hide
                               every entry added from now on. The controller still
                               honours ?category= so old bookmarked URLs keep working. */ ?>
                        <?php if ($search !== '' || $categoryFilter !== 'All' || $typeFilter !== 'All'): ?>
                            <a href="entities.php<?= $view === 'archived' ? '?view=archived' : ''; ?>" class="px-3 py-2 text-slate-600 hover:text-slate-900 text-xs font-bold whitespace-nowrap">Reset</a>
                        <?php endif; ?>
                    </div>

                </form>
            </div>

            <!-- Entities Clean Scrollable Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-200/70 flex justify-between items-center bg-slate-50/60 shrink-0">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-950 tracking-wider uppercase">Entity List</h3>
                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5"><?= $view === 'archived' ? 'Archived entities excluded from reviews and extraction' : 'Entities recognized during weekly document reviews'; ?></p>
                    </div>
                    <span class="text-xs font-black text-slate-900 bg-white px-3.5 py-1 rounded-lg border border-slate-300 shadow-2xs">
                        <?= count($entities); ?> <?= $view === 'archived' ? 'Archived' : 'Total'; ?>
                    </span>
                </div>

                <?php if (!empty($entities)): ?>
                    <div class="w-full overflow-y-auto max-h-[620px] thin-scrollbar">
                        <table class="w-full table-fixed text-left border-collapse text-xs">
                            <colgroup>
                                <col style="width: 58%;">
                                <col style="width: 20%;">
                                <col style="width: 22%;">
                            </colgroup>
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                    <th class="py-4 px-4">Entity</th>
                                    <th class="py-4 px-4">Type</th>
                                    <th class="py-4 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/80 text-slate-800 bg-white">
                                <?php foreach ($entities as $ent): 
                                    $isIt = ($ent['it_related'] === 'yes');
                                ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-4 align-middle">
                                            <p class="font-extrabold text-slate-950 text-sm tracking-tight break-words"><?= e($ent['entity_name']); ?></p>
                                        </td>
                                        <td class="py-4 px-4 align-middle">
                                            <?php if ($isIt): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-[10px] font-bold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> IT
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 text-[10px] font-bold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Clerical
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-3 align-middle">
                                            <div class="flex flex-nowrap items-center justify-end gap-1.5 whitespace-nowrap">
                                                <?php if ($view === 'archived'): ?>
                                                    <form method="POST" action="entities.php?view=archived" class="inline-block" onsubmit="return confirm('Restore <?= htmlspecialchars(addslashes($ent['entity_name'])); ?> to the active entity list?');">
                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="action" value="restore">
                                                        <input type="hidden" name="id" value="<?= (int)$ent['id']; ?>">
                                                        <button type="submit" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold rounded-lg text-[10px] transition-colors border border-emerald-300 shadow-2xs cursor-pointer">
                                                            Restore
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="entities.php?view=archived" class="inline-block" onsubmit="return confirm('Permanently delete <?= htmlspecialchars(addslashes($ent['entity_name'])); ?>? This cannot be undone.');">
                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="action" value="permanent_delete">
                                                        <input type="hidden" name="id" value="<?= (int)$ent['id']; ?>">
                                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-900 font-bold rounded-lg text-[10px] transition-colors border border-rose-300 shadow-2xs cursor-pointer">
                                                            Delete
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button type="button" onclick='openEditModal(<?= json_encode($ent, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-lg text-[10px] transition-colors border border-slate-300 shadow-2xs cursor-pointer">
                                                        Edit
                                                    </button>
                                                    <form method="POST" action="entities.php" class="inline-block" onsubmit="return confirm('Archive <?= htmlspecialchars(addslashes($ent['entity_name'])); ?>? It will stop being used for entity matching but stay in the Archive tab.');">
                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="action" value="archive">
                                                        <input type="hidden" name="id" value="<?= (int)$ent['id']; ?>">
                                                        <button type="submit" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold rounded-lg text-[10px] transition-colors border border-amber-300 shadow-2xs cursor-pointer">
                                                            Archive
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                        <?= $view === 'archived' ? 'No archived entities yet.' : 'No entities found matching your search.'; ?>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<!-- ==========================================
     ADD Entity MODAL
     ========================================== -->
<div id="addEntityModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
            <h3 class="text-sm font-black text-slate-950">Add New Entity</h3>
            <button onclick="closeModal('addEntityModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form method="POST" action="entities.php" class="space-y-3.5 text-xs">
                        <?= csrf_field(); ?>
            <input type="hidden" name="action" value="create">

            <div>
                <label class="block font-bold text-slate-700 mb-1">Entity Name *</label>
                <input type="text" name="entity_name" required placeholder="e.g., React, PostgreSQL, Cable Crimping" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Type (IT or Clerical?)</label>
                <select name="it_related" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    <option value="yes">IT (counts toward technical skills)</option>
                    <option value="no">Clerical (administrative or general task)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Other Names / Aliases <span class="text-slate-500 font-medium">(separate with | )</span></label>
                <input type="text" name="aliases" placeholder="e.g., reactjs|react.js|react framework" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                <button type="button" onclick="closeModal('addEntityModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Save Entity</button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================
     EDIT Entity MODAL
     ========================================== -->
<div id="editEntityModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex overflow-y-auto items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
            <h3 class="text-sm font-black text-slate-950">Edit Entity</h3>
            <button onclick="closeModal('editEntityModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form method="POST" action="entities.php" class="space-y-3.5 text-xs">
                        <?= csrf_field(); ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block font-bold text-slate-700 mb-1">Entity Name *</label>
                <input type="text" name="entity_name" id="edit_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Type (IT or Clerical?)</label>
                <select name="it_related" id="edit_it_related" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    <option value="yes">IT</option>
                    <option value="no">Clerical</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Other Names / Aliases <span class="text-slate-500 font-medium">(separate with | )</span></label>
                <input type="text" name="aliases" id="edit_aliases" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200/70">
                <button type="button" onclick="closeModal('editEntityModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl font-bold shadow-xs transition-colors cursor-pointer">Update Entity</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function openEditModal(ent) {
    document.getElementById('edit_id').value = ent.id;
    document.getElementById('edit_name').value = ent.entity_name || '';
    document.getElementById('edit_it_related').value = ent.it_related || 'yes';
    document.getElementById('edit_aliases').value = ent.aliases || '';
    openModal('editEntityModal');
}
</script>

</body>
</html>