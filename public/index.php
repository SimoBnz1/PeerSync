<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Enums/TicketStatus.php';
require_once __DIR__ . '/../app/Entities/User.php';
require_once __DIR__ . '/../app/Entities/Skill.php';
require_once __DIR__ . '/../app/Entities/HelpRequest.php';
require_once __DIR__ . '/../app/Repositories/UserRepository.php';
require_once __DIR__ . '/../app/Repositories/SkillRepository.php';
require_once __DIR__ . '/../app/Repositories/HelpRequestRepository.php';
require_once __DIR__ . '/../app/Services/HelpRequestService.php';
require_once __DIR__ . '/../app/Controllers/HelpRequestController.php';

$controller = new HelpRequestController();

if (isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->handleCreate();
}
if (isset($_GET['action']) && $_GET['action'] === 'assign') {
    $controller->handleAssign();
}

$data = $controller->index();
$myRequests = $data['myRequests'];
$pendingOthers = $data['pendingOthers'];
$myInterventions = $data['myInterventions'] ?? []; // 🌟 استلام البيانات الجديدة
$skills = $data['skills'];

require_once __DIR__ . '/../app/Views/layouts/header.php';
?>

<?php if (isset($_GET['error'])): ?>
    <div class="mb-6 p-4 bg-rose-950/40 border border-rose-900 text-rose-400 rounded-xl text-sm text-left">
        ⚠️ <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-900 text-emerald-400 rounded-xl text-sm text-left">
        ✅ Demande d'aide acceptée avec succès ! Vous pouvez maintenant contacter votre collègue.
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-left" dir="ltr">
    
    <!-- Colonne Formulaire : Nouvelle Demande -->
    <div class="lg:col-span-1 bg-slate-900/40 border border-slate-900 p-6 rounded-2xl backdrop-blur-sm h-fit sticky top-24">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2 justify-start">
            <span>🚀</span> Nouvelle demande d'aide
        </h2>
        
        <form action="index.php?action=create" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5 text-left">Concept bloquant</label>
                <input type="text" name="title" placeholder="Ex: Problème avec la POO" required
                       class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 placeholder-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition text-left">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5 text-left">Description précise du problème</label>
                <textarea name="description" rows="4" placeholder="Expliquez en détail..." required
                          class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 placeholder-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition text-left"></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5 text-left">Technologie ciblée</label>
                <select name="skill_id" required
                        class="w-full bg-slate-950/80 border border-slate-800 text-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition text-left">
                    <option value="" class="text-slate-600">Choisir une technologie...</option>
                    <?php foreach ($skills as $skill): ?>
                        <option value="<?= $skill->getId() ?>"><?= htmlspecialchars($skill->getName()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/10">
                Publier la demande
            </button>
        </form>
    </div>

    <!-- Colonne d'affichage des demandes -->
    <div class="lg:col-span-2 space-y-10">
        
        <!-- 🌟 🌟 السيكسيون الجديدة: الطلبات اللي أنا عاونت فيها (Mes Interventions) 🌟 🌟 -->
        <?php if (!empty($myInterventions)): ?>
        <div>
            <h2 class="text-base font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-emerald-400 mb-4 flex items-center gap-2 justify-start">
                <span>🤝</span> Mes interventions en cours
                <span class="text-xs bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 px-2 py-0.5 rounded-full font-normal">
                    <?= count($myInterventions) ?> action(s)
                </span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($myInterventions as $request): ?>
                    <div class="bg-gradient-to-br from-slate-900/60 to-slate-900/20 border border-cyan-500/30 p-5 rounded-2xl flex flex-col justify-between shadow-lg shadow-cyan-950/10 text-left relative overflow-hidden">
                        <!-- Neon Accent Line -->
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-cyan-500 to-emerald-500"></div>
                        
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                    <?= htmlspecialchars($request->getSkill()->getName()) ?>
                                </span>
                                <span class="text-[10px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/30 font-medium animate-pulse">
                                    ● En cours
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-100 mb-1">
                                <?= htmlspecialchars($request->getTitle()) ?>
                            </h3>
                            <p class="text-xs text-slate-400 mb-4 leading-relaxed line-clamp-2">
                                <?= htmlspecialchars($request->getDescription()) ?>
                            </p>
                        </div>
                        
                        <div class="border-t border-slate-900 pt-3 flex items-center justify-between mt-auto">
                            <span class="text-xs text-slate-400">
                                Étudiant à aider : <strong class="text-cyan-400 font-semibold"><?= htmlspecialchars($request->getStudent()->getName()) ?></strong>
                            </span>
                            
                            <span class="text-[10px] text-slate-500 italic">
                                Contactez-le en salle 📍
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>


        <!-- 📥 Section : Mes Demandes d'Aide -->
        <div>
            <h2 class="text-base font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400 mb-4 flex items-center gap-2 justify-start">
                <span>📥</span> Mes demandes actuelles
                <span class="text-xs bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-2 py-0.5 rounded-full font-normal">
                    <?= count($myRequests) ?> demande(s)
                </span>
            </h2>

            <?php if (empty($myRequests)): ?>
                <div class="text-center py-6 border border-dashed border-slate-900 rounded-2xl text-slate-600 text-xs">
                    Vous n'avez soumis aucune demande d'aide pour le moment.
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($myRequests as $request): ?>
                        <div class="bg-slate-900/10 border border-slate-900 p-4 rounded-xl flex flex-col justify-between text-left">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">
                                        <?= htmlspecialchars($request->getSkill()->getName()) ?>
                                    </span>
                                    
                                    <?php if ($request->getStatus() === TicketStatus::PENDING): ?>
                                        <span class="text-[10px] text-amber-400 bg-amber-500/5 px-2 py-0.5 rounded border border-amber-500/10">⏳ En attente</span>
                                    <?php elseif ($request->getStatus() === TicketStatus::ASSIGNED): ?>
                                        <span class="text-[10px] text-cyan-400 bg-cyan-500/5 px-2 py-0.5 rounded border border-cyan-500/10">🤝 Assignée</span>
                                    <?php else: ?>
                                        <span class="text-[10px] text-emerald-400 bg-emerald-500/5 px-2 py-0.5 rounded border border-emerald-500/10">✅ Résolue</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-xs font-bold text-slate-200 mb-1"><?= htmlspecialchars($request->getTitle()) ?></h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2"><?= htmlspecialchars($request->getDescription()) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>


        <!-- 🚀 Section : Demandes des autres utilisateurs -->
        <div>
            <h2 class="text-base font-bold text-slate-200 mb-4 flex items-center gap-2 justify-start">
                <span>🤝</span> Demandes de mes collègues en attente
                <span class="text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded-full font-normal">
                    <?= count($pendingOthers) ?> disponible(s)
                </span>
            </h2>

            <?php if (empty($pendingOthers)): ?>
                <div class="text-center py-10 border border-dashed border-slate-900 rounded-2xl text-slate-500 text-xs">
                    🎉 Aucune demande en attente. Tout le monde avance à merveille !
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($pendingOthers as $request): ?>
                        <div class="bg-slate-900/20 border border-slate-900/60 p-5 rounded-2xl flex flex-col justify-between transition hover:border-slate-800 text-left">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                        <?= htmlspecialchars($request->getSkill()->getName()) ?>
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-100 mb-1">
                                    <?= htmlspecialchars($request->getTitle()) ?>
                                </h3>
                                <p class="text-xs text-slate-400 mb-4 leading-relaxed line-clamp-3">
                                    <?= htmlspecialchars($request->getDescription()) ?>
                                </p>
                            </div>
                            
                            <div class="border-t border-slate-900/80 pt-3 flex items-center justify-between mt-auto">
                                <span class="text-[11px] text-slate-500">
                                    Par : <strong class="text-slate-400"><?= htmlspecialchars($request->getStudent()->getName()) ?></strong>
                                </span>
                                
                                <a href="index.php?action=assign&id=<?= $request->getId() ?>" 
                                   class="text-xs bg-indigo-600/10 hover:bg-indigo-600 text-indigo-400 hover:text-white border border-indigo-500/20 px-3 py-1.5 rounded-lg transition font-medium">
                                    Apporter mon aide &rarr;
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php
require_once __DIR__ . '/../app/Views/layouts/footer.php';
?>