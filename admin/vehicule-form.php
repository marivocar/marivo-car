<?php
require_once __DIR__ . '/includes/auth.php';
if (est_lecteur()) {
    header('Location: vehicules.php?msg=acces_refuse');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicule = ['marque'=>'','modele'=>'','annee'=>date('Y'),'transmission'=>'Manuelle','carburant'=>'Diesel',
             'nb_places'=>5,'prix_jour'=>'','image'=>'default-car.jpg','description'=>'','disponible'=>1,'categorie_id'=>null];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $existant = $stmt->fetch();
    if ($existant) $vehicule = $existant;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque = trim($_POST['marque'] ?? '');
    $modele = trim($_POST['modele'] ?? '');
    $annee = (int)($_POST['annee'] ?? 0);
    $transmission = $_POST['transmission'] ?? 'Manuelle';
    $carburant = $_POST['carburant'] ?? 'Diesel';
    $nb_places = (int)($_POST['nb_places'] ?? 5);
    $prix_jour = $_POST['prix_jour'] ?? 0;
    $categorie_id = $_POST['categorie_id'] ?: null;
    $description = trim($_POST['description'] ?? '');
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    $image = $vehicule['image'];

    if ($marque === '' || $modele === '') $erreurs[] = "La marque et le modèle sont requis.";
    if (!is_numeric($prix_jour) || $prix_jour <= 0) $erreurs[] = "Le prix par jour doit être un nombre positif.";

    // --- Upload d'image (optionnel) ---
    if (!empty($_FILES['image']['name'])) {
        $extensionsAutorisees = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $extensionsAutorisees)) {
            $erreurs[] = "Format d'image non autorisé (jpg, jpeg, png, webp uniquement).";
        } elseif ($_FILES['image']['size'] > 4 * 1024 * 1024) {
            $erreurs[] = "L'image ne doit pas dépasser 4 Mo.";
        } else {
            $nouveauNom = uniqid('vehicule_') . '.' . $ext;
            $dossierDestination = __DIR__ . '/../assets/uploads/cars/';
            if (!is_dir($dossierDestination)) mkdir($dossierDestination, 0755, true);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $dossierDestination . $nouveauNom)) {
                $image = $nouveauNom;
            } else {
                $erreurs[] = "Erreur lors du téléversement de l'image.";
            }
        }
    }

    if (empty($erreurs)) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE vehicules SET marque=:marque, modele=:modele, annee=:annee,
                transmission=:transmission, carburant=:carburant, nb_places=:places, prix_jour=:prix,
                categorie_id=:cat, description=:desc, disponible=:dispo, image=:image WHERE id=:id");
            $stmt->execute([
                ':marque'=>$marque, ':modele'=>$modele, ':annee'=>$annee, ':transmission'=>$transmission,
                ':carburant'=>$carburant, ':places'=>$nb_places, ':prix'=>$prix_jour, ':cat'=>$categorie_id,
                ':desc'=>$description, ':dispo'=>$disponible, ':image'=>$image, ':id'=>$id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO vehicules
                (marque, modele, annee, transmission, carburant, nb_places, prix_jour, categorie_id, description, disponible, image)
                VALUES (:marque, :modele, :annee, :transmission, :carburant, :places, :prix, :cat, :desc, :dispo, :image)");
            $stmt->execute([
                ':marque'=>$marque, ':modele'=>$modele, ':annee'=>$annee, ':transmission'=>$transmission,
                ':carburant'=>$carburant, ':places'=>$nb_places, ':prix'=>$prix_jour, ':cat'=>$categorie_id,
                ':desc'=>$description, ':dispo'=>$disponible, ':image'=>$image
            ]);
        }
        header('Location: vehicules.php?msg=enregistre');
        exit;
    } else {
        $vehicule = array_merge($vehicule, compact('marque','modele','annee','transmission','carburant','nb_places','prix_jour','categorie_id','description','disponible'));
    }
}

$admin_page_title = $id ? 'Modifier le véhicule' : 'Ajouter un véhicule';
$admin_active = 'vehicules';
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-topbar">
    <h2 style="font-size:1.4rem;"><?= $id ? 'Modifier le véhicule' : 'Ajouter un véhicule' ?></h2>
    <a href="vehicules.php" class="btn btn-dark btn-sm">← Retour à la liste</a>
</div>

<?php if ($erreurs): ?>
    <div class="alert alert-error"><ul style="padding-left:18px;"><?php foreach ($erreurs as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
<?php endif; ?>

<div class="form-card" style="max-width:760px;">
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Marque *</label>
                <input type="text" name="marque" value="<?= htmlspecialchars($vehicule['marque']) ?>" required>
            </div>
            <div class="form-group">
                <label>Modèle *</label>
                <input type="text" name="modele" value="<?= htmlspecialchars($vehicule['modele']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Année</label>
                <input type="number" name="annee" min="1990" max="2030" value="<?= htmlspecialchars($vehicule['annee']) ?>">
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie_id">
                    <option value="">— Aucune —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $vehicule['categorie_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Transmission</label>
                <select name="transmission">
                    <option value="Manuelle" <?= $vehicule['transmission']==='Manuelle'?'selected':'' ?>>Manuelle</option>
                    <option value="Automatique" <?= $vehicule['transmission']==='Automatique'?'selected':'' ?>>Automatique</option>
                </select>
            </div>
            <div class="form-group">
                <label>Carburant</label>
                <select name="carburant">
                    <?php foreach (['Essence','Diesel','Hybride','Électrique'] as $c): ?>
                        <option value="<?= $c ?>" <?= $vehicule['carburant']===$c?'selected':'' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Nombre de places</label>
                <input type="number" name="nb_places" min="1" max="9" value="<?= htmlspecialchars($vehicule['nb_places']) ?>">
            </div>
            <div class="form-group">
                <label>Prix / jour (MAD) *</label>
                <input type="number" step="0.01" name="prix_jour" value="<?= htmlspecialchars($vehicule['prix_jour']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($vehicule['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Photo du véhicule <?= $id ? '(laisser vide pour conserver l\'image actuelle)' : '' ?></label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
            <?php if (!empty($vehicule['image']) && $vehicule['image'] !== 'default-car.jpg'): ?>
                <img src="<?= SITE_URL ?>/assets/uploads/cars/<?= htmlspecialchars($vehicule['image']) ?>" style="max-width:160px; margin-top:10px; border-radius:4px;">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="disponible" value="1" <?= $vehicule['disponible'] ? 'checked' : '' ?> style="width:auto; margin-right:8px;"> Véhicule disponible à la location</label>
        </div>

        <button type="submit" class="btn btn-gold"><?= $id ? 'Enregistrer les modifications' : 'Ajouter le véhicule' ?></button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>