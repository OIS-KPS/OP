<!-- src/pages/coordinator/entitiesPage.php -->
<?php
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
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
                    <p class="text-xs font-semibold text-slate-600 mt-1">Manage technical skills, software tools, and task entities used to scan student accomplishment reports.</p>
                </div>

                <button onclick="openModal('addEntityModal')" class="px-5 py-2.5 bg-[#0F2854] hover:bg-blue-900 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer shrink-0">
                    <span class="text-sm font-bold">+</span> Add Entities
                </button>
            </div>

            <!-- Filter & Search Controls -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs">
                <form method="GET" action="entities.php" class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
                    
                    <!-- Quick Activity Filters -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0 text-xs">
                        <a href="entities.php" class="px-3.5 py-1.5 rounded-lg font-bold transition-all <?= ($typeFilter === 'All') ? 'bg-[#0F2854] text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                            All <span class="ml-1 opacity-90 font-bold"><?= count($entities); ?></span>
                        </a>
                        <a href="entities.php?activity_type=Software" class="px-3.5 py-1.5 rounded-lg font-bold transition-all <?= ($typeFilter === 'Software') ? 'bg-[#0F2854] text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                            Software
                        </a>
                        <a href="entities.php?activity_type=Hardware" class="px-3.5 py-1.5 rounded-lg font-bold transition-all <?= ($typeFilter === 'Hardware') ? 'bg-[#0F2854] text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                            Hardware
                        </a>
                        <a href="entities.php?activity_type=Clerical" class="px-3.5 py-1.5 rounded-lg font-bold transition-all <?= ($typeFilter === 'Clerical') ? 'bg-[#0F2854] text-white shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'; ?>">
                            Clerical
                        </a>
                    </div>

                    <!-- Search and Category Dropdown -->
                    <div class="flex items-center gap-2">
                        <!-- Category Dropdown -->
                        <select name="category" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 font-bold focus:outline-none focus:border-[#0F2854] cursor-pointer">
                            <option value="All">All Categories</option>
                            <?php foreach ($allCategories as $cat): ?>
                                <option value="<?= e($cat); ?>" <?= ($categoryFilter === $cat) ? 'selected' : ''; ?>>
                                    <?= e($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" name="search" value="<?= e($search); ?>" placeholder="Search entities..." class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-3 py-2 text-xs font-semibold text-slate-900 placeholder-slate-500 focus:outline-none focus:border-[#0F2854]">
                            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>

                        <?php if ($search !== '' || $categoryFilter !== 'All' || $typeFilter !== 'All'): ?>
                            <a href="entities.php" class="px-3 py-2 text-slate-600 hover:text-slate-900 text-xs font-bold whitespace-nowrap">Reset</a>
                        <?php endif; ?>
                    </div>

                </form>
            </div>

            <!-- Entities Clean Scrollable Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-200/70 flex justify-between items-center bg-slate-50/60 shrink-0">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-950 tracking-wider uppercase">Entity List</h3>
                        <p class="text-[11px] font-semibold text-slate-600 mt-0.5">Entities recognized during weekly document reviews</p>
                    </div>
                    <span class="text-xs font-black text-slate-900 bg-white px-3.5 py-1 rounded-lg border border-slate-300 shadow-2xs">
                        <?= count($entities); ?> Total
                    </span>
                </div>

                <?php if (!empty($entities)): ?>
                    <div class="overflow-y-auto overflow-x-auto max-h-[620px] thin-scrollbar">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider border-b border-slate-200 font-black">
                                    <th class="py-4 px-6">Entity / Tool</th>
                                    <th class="py-4 px-6">Category</th>
                                    <th class="py-4 px-6">Type</th>
                                    <th class="py-4 px-6">Technical?</th>
                                    <th class="py-4 px-6">Other Names (Aliases)</th>
                                    <th class="py-4 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/80 text-slate-800 bg-white">
                                <?php foreach ($entities as $ent): 
                                    $isSoftware = ($ent['activity_type'] === 'Software');
                                    $isHardware = ($ent['activity_type'] === 'Hardware');
                                    $isIt = ($ent['it_related'] === 'yes');
                                ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-6 align-middle">
                                            <p class="font-extrabold text-slate-950 text-sm tracking-tight"><?= e($ent['entity_name']); ?></p>
                                            <?php if (!empty($ent['description'])): ?>
                                                <p class="text-[11px] text-slate-600 font-semibold mt-0.5 leading-normal"><?= e($ent['description']); ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap align-middle">
                                            <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px] border border-slate-300">
                                                <?= e($ent['category']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap align-middle">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold <?= $isSoftware ? 'bg-blue-50 text-blue-900 border border-blue-200' : ($isHardware ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-slate-100 text-slate-800 border border-slate-300'); ?>">
                                                <?= e($ent['activity_type']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap align-middle">
                                            <?php if ($isIt): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100/80 text-emerald-900 border border-emerald-300 text-[10px] font-bold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Yes
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 text-[10px] font-bold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> No
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 text-slate-700 font-medium max-w-xs truncate text-[11px] align-middle">
                                            <?= !empty($ent['aliases']) ? e($ent['aliases']) : '<span class="text-slate-400 font-normal">None</span>'; ?>
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap align-middle space-x-1.5">
                                            <button type="button" onclick='openEditModal(<?= json_encode($ent, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' class="px-3.5 py-1.5 bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl text-xs transition-colors border border-slate-300 shadow-2xs cursor-pointer">
                                                Edit
                                            </button>
                                            <form method="POST" action="entities.php" class="inline-block" onsubmit="return confirm('Are you sure you want to delete <?= e($ent['entity_name']); ?>?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int)$ent['id']; ?>">
                                                <button type="submit" class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-900 font-bold rounded-xl text-xs transition-colors border border-rose-300 shadow-2xs cursor-pointer">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="py-16 text-center text-slate-500 text-xs font-semibold">
                        No entities found matching your search.
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<!-- ==========================================
     ADD Entity MODAL
     ========================================== -->
<div id="addEntityModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
            <h3 class="text-sm font-black text-slate-950">Add New Entity</h3>
            <button onclick="closeModal('addEntityModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form method="POST" action="entities.php" class="space-y-3.5 text-xs">
            <input type="hidden" name="action" value="create">

            <div>
                <label class="block font-bold text-slate-700 mb-1">Entity Name *</label>
                <input type="text" name="entity_name" required placeholder="e.g., React, PostgreSQL, Cable Crimping" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Category</label>
                    <input type="text" name="category" placeholder="e.g., Programming, Database" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Activity Type</label>
                    <select name="activity_type" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        <option value="Software">Software</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Clerical">Clerical</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Counts as Technical Task?</label>
                <select name="it_related" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    <option value="yes">Yes (Counts toward technical skills)</option>
                    <option value="no">No (Administrative or general task)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Other Names / Aliases <span class="text-slate-500 font-medium">(separate with | )</span></label>
                <input type="text" name="aliases" placeholder="e.g., reactjs|react.js|react framework" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Brief Description</label>
                <textarea name="description" rows="2" placeholder="Explain this technical task or skill..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"></textarea>
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
<div id="editEntityModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl border border-slate-300 shadow-2xl max-w-lg w-full p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-200/70 pb-3">
            <h3 class="text-sm font-black text-slate-950">Edit Entity</h3>
            <button onclick="closeModal('editEntityModal')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center cursor-pointer">✕</button>
        </div>

        <form method="POST" action="entities.php" class="space-y-3.5 text-xs">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block font-bold text-slate-700 mb-1">Entity Name *</label>
                <input type="text" name="entity_name" id="edit_name" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Category</label>
                    <input type="text" name="category" id="edit_category" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Activity Type</label>
                    <select name="activity_type" id="edit_activity_type" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                        <option value="Software">Software</option>
                        <option value="Hardware">Hardware</option>
                        <option value="Clerical">Clerical</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Counts as Technical Task?</label>
                <select name="it_related" id="edit_it_related" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Other Names / Aliases <span class="text-slate-500 font-medium">(separate with | )</span></label>
                <input type="text" name="aliases" id="edit_aliases" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Brief Description</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 font-semibold text-slate-900 focus:outline-none focus:border-[#0F2854] focus:bg-white transition-colors"></textarea>
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
    document.getElementById('edit_category').value = ent.category || '';
    document.getElementById('edit_activity_type').value = ent.activity_type || 'Other';
    document.getElementById('edit_it_related').value = ent.it_related || 'yes';
    document.getElementById('edit_aliases').value = ent.aliases || '';
    document.getElementById('edit_description').value = ent.description || '';
    openModal('editEntityModal');
}
</script>

</body>
</html>