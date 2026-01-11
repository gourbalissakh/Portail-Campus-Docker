<?php
/**
 * Voir les détails d'un étudiant
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    setFlashMessage('error', 'Étudiant non trouvé.');
    header('Location: /students/list.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM etudiants WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    setFlashMessage('error', 'Étudiant non trouvé.');
    header('Location: /students/list.php');
    exit;
}

$pageTitle = 'Détails de l\'étudiant';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-person"></i> Fiche Étudiant</h4>
                <span class="badge bg-light text-dark"><?= e($student['matricule']) ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person-badge"></i> Informations Personnelles
                        </h5>
                        <dl class="row">
                            <dt class="col-sm-4">Nom</dt>
                            <dd class="col-sm-8"><?= e($student['nom']) ?></dd>
                            
                            <dt class="col-sm-4">Prénom</dt>
                            <dd class="col-sm-8"><?= e($student['prenom']) ?></dd>
                            
                            <dt class="col-sm-4">Date de naissance</dt>
                            <dd class="col-sm-8">
                                <?php if ($student['date_naissance']): ?>
                                <?= date('d/m/Y', strtotime($student['date_naissance'])) ?>
                                <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-sm-4">Adresse</dt>
                            <dd class="col-sm-8">
                                <?= $student['adresse'] ? e($student['adresse']) : '<span class="text-muted">Non renseignée</span>' ?>
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-mortarboard"></i> Informations Académiques
                        </h5>
                        <dl class="row">
                            <dt class="col-sm-4">Matricule</dt>
                            <dd class="col-sm-8"><code><?= e($student['matricule']) ?></code></dd>
                            
                            <dt class="col-sm-4">Filière</dt>
                            <dd class="col-sm-8">
                                <?= $student['filiere'] ? e($student['filiere']) : '<span class="text-muted">Non renseignée</span>' ?>
                            </dd>
                            
                            <dt class="col-sm-4">Niveau</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-primary fs-6"><?= e($student['niveau']) ?></span>
                            </dd>
                        </dl>
                        
                        <h5 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="bi bi-envelope"></i> Contact
                        </h5>
                        <dl class="row">
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">
                                <?php if ($student['email']): ?>
                                <a href="mailto:<?= e($student['email']) ?>"><?= e($student['email']) ?></a>
                                <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-sm-4">Téléphone</dt>
                            <dd class="col-sm-8">
                                <?php if ($student['telephone']): ?>
                                <a href="tel:<?= e($student['telephone']) ?>"><?= e($student['telephone']) ?></a>
                                <?php else: ?>
                                <span class="text-muted">Non renseigné</span>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> Créé le <?= date('d/m/Y à H:i', strtotime($student['created_at'])) ?>
                            <?php if ($student['updated_at'] !== $student['created_at']): ?>
                            | Modifié le <?= date('d/m/Y à H:i', strtotime($student['updated_at'])) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="/students/list.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                    <div>
                        <a href="/students/edit.php?id=<?= $student['id'] ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                        <a href="/students/delete.php?id=<?= $student['id'] ?>" class="btn btn-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')">
                            <i class="bi bi-trash"></i> Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
