<?php
/**
 * Modifier un étudiant
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

// Récupérer l'étudiant
$stmt = $pdo->prepare('SELECT * FROM etudiants WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    setFlashMessage('error', 'Étudiant non trouvé.');
    header('Location: /students/list.php');
    exit;
}

$errors = [];
$data = $student;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'filiere' => trim($_POST['filiere'] ?? ''),
        'niveau' => $_POST['niveau'] ?? 'L1',
        'date_naissance' => $_POST['date_naissance'] ?? '',
        'adresse' => trim($_POST['adresse'] ?? '')
    ];
    
    // Validation
    if (empty($data['nom'])) {
        $errors[] = 'Le nom est obligatoire.';
    }
    if (empty($data['prenom'])) {
        $errors[] = 'Le prénom est obligatoire.';
    }
    if (!empty($data['email']) && !isValidEmail($data['email'])) {
        $errors[] = 'L\'email n\'est pas valide.';
    }
    if (!empty($data['telephone']) && !isValidPhone($data['telephone'])) {
        $errors[] = 'Le téléphone doit être un numéro sénégalais valide.';
    }
    
    // Vérifier l'unicité de l'email (sauf pour l'étudiant actuel)
    if (!empty($data['email'])) {
        $checkStmt = $pdo->prepare('SELECT id FROM etudiants WHERE email = ? AND id != ?');
        $checkStmt->execute([$data['email'], $id]);
        if ($checkStmt->fetch()) {
            $errors[] = 'Cet email est déjà utilisé par un autre étudiant.';
        }
    }
    
    // Mise à jour si pas d'erreurs
    if (empty($errors)) {
        $sql = 'UPDATE etudiants SET nom = ?, prenom = ?, email = ?, telephone = ?, 
                filiere = ?, niveau = ?, date_naissance = ?, adresse = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'] ?: null,
            $data['telephone'] ?: null,
            $data['filiere'] ?: null,
            $data['niveau'],
            $data['date_naissance'] ?: null,
            $data['adresse'] ?: null,
            $id
        ]);
        
        setFlashMessage('success', 'Étudiant modifié avec succès !');
        header('Location: /students/list.php');
        exit;
    }
    
    // Conserver le matricule pour l'affichage
    $data['matricule'] = $student['matricule'];
}

$pageTitle = 'Modifier l\'étudiant';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Modifier l'étudiant</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <strong>Matricule :</strong> <code><?= e($student['matricule']) ?></code>
                    <small class="text-muted">(non modifiable)</small>
                </div>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?= e($data['nom']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="prenom" name="prenom" 
                                   value="<?= e($data['prenom']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= e($data['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" 
                                   value="<?= e($data['telephone'] ?? '') ?>" placeholder="77XXXXXXX">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="date_naissance" name="date_naissance" 
                                   value="<?= e($data['date_naissance'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filiere" class="form-label">Filière</label>
                            <select class="form-select" id="filiere" name="filiere">
                                <option value="">-- Sélectionner --</option>
                                <option value="GLAR" <?= ($data['filiere'] ?? '') === 'GLAR' ? 'selected' : '' ?>>GLAR</option>
                                <option value="Informatique" <?= ($data['filiere'] ?? '') === 'Informatique' ? 'selected' : '' ?>>Informatique</option>
                                <option value="Mathématiques" <?= ($data['filiere'] ?? '') === 'Mathématiques' ? 'selected' : '' ?>>Mathématiques</option>
                                <option value="Physique" <?= ($data['filiere'] ?? '') === 'Physique' ? 'selected' : '' ?>>Physique</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="niveau" class="form-label">Niveau</label>
                            <select class="form-select" id="niveau" name="niveau">
                                <?php foreach (['L1', 'L2', 'L3', 'M1', 'M2'] as $n): ?>
                                <option value="<?= $n ?>" <?= ($data['niveau'] ?? '') === $n ? 'selected' : '' ?>><?= $n ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= e($data['adresse'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/students/list.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
