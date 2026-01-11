<?php
/**
 * Liste des étudiants avec pagination et recherche avancée
 * Portail Campus - Université du Sénégal
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

// Paramètres de pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = intval($_GET['per_page'] ?? 10);
$perPage = in_array($perPage, [5, 10, 25, 50]) ? $perPage : 10;
$offset = ($page - 1) * $perPage;

// Paramètres de recherche
$search = trim($_GET['search'] ?? '');
$filterFiliere = $_GET['filiere'] ?? '';
$filterNiveau = $_GET['niveau'] ?? '';
$sortBy = $_GET['sort'] ?? 'nom';
$sortOrder = strtoupper($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

// Colonnes de tri valides
$validSortColumns = ['matricule', 'nom', 'prenom', 'filiere', 'niveau', 'email'];
if (!in_array($sortBy, $validSortColumns)) {
    $sortBy = 'nom';
}

// Construction de la requête
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = '(nom LIKE ? OR prenom LIKE ? OR matricule LIKE ? OR email LIKE ?)';
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($filterFiliere)) {
    $where[] = 'filiere = ?';
    $params[] = $filterFiliere;
}

if (!empty($filterNiveau)) {
    $where[] = 'niveau = ?';
    $params[] = $filterNiveau;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Compter le total
$countSql = "SELECT COUNT(*) FROM etudiants {$whereClause}";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalStudents = $countStmt->fetchColumn();
$totalPages = ceil($totalStudents / $perPage);

// Récupérer les étudiants
$sql = "SELECT * FROM etudiants {$whereClause} ORDER BY {$sortBy} {$sortOrder} LIMIT {$perPage} OFFSET {$offset}";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Récupérer les filières distinctes pour le filtre
$filieres = $pdo->query('SELECT DISTINCT filiere FROM etudiants WHERE filiere IS NOT NULL ORDER BY filiere')->fetchAll(PDO::FETCH_COLUMN);

// Fonction pour générer l'URL de tri
function getSortUrl($column, $currentSort, $currentOrder) {
    $params = $_GET;
    $params['sort'] = $column;
    $params['order'] = ($currentSort === $column && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    return '?' . http_build_query($params);
}

// Fonction pour obtenir l'icône de tri
function getSortIcon($column, $currentSort, $currentOrder) {
    if ($currentSort !== $column) {
        return '<i class="bi bi-arrow-down-up text-muted"></i>';
    }
    return $currentOrder === 'ASC' 
        ? '<i class="bi bi-arrow-up text-primary"></i>' 
        : '<i class="bi bi-arrow-down text-primary"></i>';
}

// Fonction pour badge niveau
function getNiveauBadgeClass($niveau) {
    $classes = [
        'L1' => 'badge-niveau-l1',
        'L2' => 'badge-niveau-l2',
        'L3' => 'badge-niveau-l3',
        'M1' => 'badge-niveau-m1',
        'M2' => 'badge-niveau-m2'
    ];
    return $classes[$niveau] ?? 'bg-secondary';
}

$pageTitle = 'Liste des Étudiants';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="mb-1"><i class="bi bi-people"></i> Liste des Étudiants</h2>
        <p class="text-muted mb-0">Gérez tous les étudiants inscrits</p>
    </div>
    <a href="/students/create.php" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Nouvel Étudiant
    </a>
</div>

<!-- Formulaire de recherche avancée -->
<div class="search-form">
    <form method="GET" action="" class="row g-3">
        <div class="col-lg-4 col-md-6">
            <label class="form-label"><i class="bi bi-search"></i> Recherche</label>
            <input type="text" class="form-control" name="search" value="<?= e($search) ?>" 
                   placeholder="Nom, prénom, matricule, email...">
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label"><i class="bi bi-bookmark"></i> Filière</label>
            <select class="form-select" name="filiere">
                <option value="">Toutes</option>
                <?php foreach ($filieres as $f): ?>
                <option value="<?= e($f) ?>" <?= $filterFiliere === $f ? 'selected' : '' ?>><?= e($f) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <label class="form-label"><i class="bi bi-mortarboard"></i> Niveau</label>
            <select class="form-select" name="niveau">
                <option value="">Tous</option>
                <?php foreach (['L1', 'L2', 'L3', 'M1', 'M2'] as $n): ?>
                <option value="<?= $n ?>" <?= $filterNiveau === $n ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <label class="form-label"><i class="bi bi-list-ol"></i> Par page</label>
            <select class="form-select" name="per_page">
                <?php foreach ([5, 10, 25, 50] as $pp): ?>
                <option value="<?= $pp ?>" <?= $perPage === $pp ? 'selected' : '' ?>><?= $pp ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-search"></i> Filtrer
            </button>
            <a href="/students/list.php" class="btn btn-outline-secondary" title="Réinitialiser">
                <i class="bi bi-x-circle"></i>
            </a>
        </div>
    </form>
</div>

<!-- Info résultats -->
<div class="results-info">
    <i class="bi bi-info-circle"></i> 
    <strong><?= $totalStudents ?></strong> étudiant(s) trouvé(s)
    <?php if (!empty($search) || !empty($filterFiliere) || !empty($filterNiveau)): ?>
    <span class="text-muted">avec les filtres appliqués</span>
    <?php endif; ?>
    <?php if ($totalStudents > 0): ?>
    <span class="ms-auto text-muted">
        Affichage <?= ($offset + 1) ?>-<?= min($offset + $perPage, $totalStudents) ?> sur <?= $totalStudents ?>
    </span>
    <?php endif; ?>
</div>

<!-- Tableau des étudiants -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>
                        <a href="<?= getSortUrl('matricule', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                            Matricule <?= getSortIcon('matricule', $sortBy, $sortOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= getSortUrl('nom', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                            Nom <?= getSortIcon('nom', $sortBy, $sortOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= getSortUrl('prenom', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                            Prénom <?= getSortIcon('prenom', $sortBy, $sortOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= getSortUrl('filiere', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                            Filière <?= getSortIcon('filiere', $sortBy, $sortOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= getSortUrl('niveau', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                            Niveau <?= getSortIcon('niveau', $sortBy, $sortOrder) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= getSortUrl('email', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                            Email <?= getSortIcon('email', $sortBy, $sortOrder) ?>
                        </a>
                    </th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Aucun étudiant trouvé</p>
                        <?php if (!empty($search) || !empty($filterFiliere) || !empty($filterNiveau)): ?>
                        <a href="/students/list.php" class="btn btn-outline-primary btn-sm mt-2">
                            <i class="bi bi-arrow-clockwise"></i> Réinitialiser les filtres
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><code><?= e($student['matricule']) ?></code></td>
                    <td><strong><?= e($student['nom']) ?></strong></td>
                    <td><?= e($student['prenom']) ?></td>
                    <td><?= e($student['filiere'] ?? '-') ?></td>
                    <td>
                        <span class="badge <?= getNiveauBadgeClass($student['niveau']) ?>">
                            <?= e($student['niveau']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($student['email']): ?>
                        <a href="mailto:<?= e($student['email']) ?>" class="text-decoration-none">
                            <?= e($student['email']) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="action-buttons">
                            <a href="/students/view.php?id=<?= $student['id'] ?>" class="btn btn-info btn-action" 
                               title="Voir les détails" data-bs-toggle="tooltip">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="/students/edit.php?id=<?= $student['id'] ?>" class="btn btn-warning btn-action" 
                               title="Modifier" data-bs-toggle="tooltip">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="/students/delete.php?id=<?= $student['id'] ?>" class="btn btn-danger btn-action" 
                               title="Supprimer" data-bs-toggle="tooltip"
                               onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer l\'étudiant <?= e($student['prenom']) ?> <?= e($student['nom']) ?> ?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="mt-4" aria-label="Pagination des étudiants">
    <ul class="pagination justify-content-center">
        <?php
        $queryParams = $_GET;
        unset($queryParams['page']);
        $queryString = http_build_query($queryParams);
        $baseUrl = '/students/list.php?' . ($queryString ? $queryString . '&' : '');
        ?>
        
        <!-- Premier / Précédent -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>page=1" title="Première page">
                <i class="bi bi-chevron-double-left"></i>
            </a>
        </li>
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>page=<?= $page - 1 ?>">
                <i class="bi bi-chevron-left"></i> Préc.
            </a>
        </li>
        
        <!-- Pages -->
        <?php 
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        
        if ($startPage > 1): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
        
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        
        <?php if ($endPage < $totalPages): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
        
        <!-- Suivant / Dernier -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>page=<?= $page + 1 ?>">
                Suiv. <i class="bi bi-chevron-right"></i>
            </a>
        </li>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>page=<?= $totalPages ?>" title="Dernière page">
                <i class="bi bi-chevron-double-right"></i>
            </a>
        </li>
    </ul>
</nav>
<p class="pagination-info">
    Page <strong><?= $page ?></strong> sur <strong><?= $totalPages ?></strong>
    <?php if ($totalStudents > 0): ?>
    — Total: <strong><?= $totalStudents ?></strong> étudiant(s)
    <?php endif; ?>
</p>
<?php endif; ?>

<!-- Script pour tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
