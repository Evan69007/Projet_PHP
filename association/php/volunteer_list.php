<?php
require 'config.php';

try {
    $stmt = $pdo->query("
        SELECT *
		FROM benevoles
		ORDER BY nom
    ");

    $benevoles = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit;
}

$stmt2 = $pdo->query("
	SELECT d.quantite_kg, d.id_collecte, c.id, c.id_benevole, b.id
	FROM collectes c
	JOIN benevoles b ON b.id = c.id_benevole
	JOIN dechets_collectes d ON d.id_collecte = c.id
	");

$total = $stmt2->fetchAll();

foreach ($total as $tot) {
    $idBenevole = $tot['id_benevole'];
    $quantiteKg = $tot['quantite_kg'];
    
    // Sum the quantities by id_benevole
    if (!isset($sumByBenevole[$idBenevole])) {
        $sumByBenevole[$idBenevole] = 0;
    }
    $sumByBenevole[$idBenevole] += $quantiteKg;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Bénévoles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
<div class="flex h-screen">
    <!-- Barre de navigation -->
    <div class="bg-cyan-200 text-white w-64 p-6">
        <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
            <li><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fas fa-tachometer-alt mr-3"></i> Tableau de bord</a></li>
            <li><a href="collection_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
            <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
            <li>
                <a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole
                </a>
            </li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
        <div class="mt-6">
            <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg shadow-md">
                Déconnexion
            </button>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="flex-1 p-8 overflow-y-auto">
        <!-- Titre -->
        <h1 class="text-4xl font-bold text-blue-800 mb-6">Liste des Bénévoles</h1>

        <!-- Tableau des bénévoles -->
        <div class="overflow-hidden rounded-lg shadow-lg bg-white">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-blue-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Nom</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Rôle</th>
					<th class="py-3 px-4 text-left">Poids total déchets ramassées (en kg)</th>
                    <th class="py-3 px-4 text-left">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                 <?php foreach ($benevoles as $benevole) : ?> <!-- boucle pour afficher les benevoles -->
                    <tr class="hover:bg-gray-100 transition duration-200">
                        <td class="py-3 px-4"><?= $benevole["nom"] ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($benevole['email']) ?></td>
                        <td class="py-3 px-4">
                            <?= $benevole['role'] ? htmlspecialchars($benevole['role']) : 'Aucun rôle' ?>
                        </td>
						<td class="py-3 px-4"> <?= isset($sumByBenevole[$benevole["id"]]) ? $sumByBenevole[$benevole["id"]] : 0 ?> </td>
                        <td class="py-3 px-4 flex space-x-2">
                            <a href="volunteer_edit.php?id=<?= $benevole['id'] ?>" class="bg-cyan-200 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                                ✏️ Modifier
                            </a>
                            <a href="volunteer_delete.php?id=<?= $benevole['id'] ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette collecte ?');">
                                🗑️ Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

