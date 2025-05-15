<?php

define('HISTORIQUE_FILE', __DIR__ . '/historique.json');

// Crée un fichier vide s'il n'existe pas
if (!file_exists(HISTORIQUE_FILE)) {
    file_put_contents(HISTORIQUE_FILE, json_encode([]));
}

function jouerPartie() {
    $options = ['1' => 'Pierre', '2' => 'Feuille', '3' => 'Ciseaux'];

    echo "\n=== Nouvelle Partie ===\n";
    foreach ($options as $key => $val) {
        echo "$key. $val\n";
    }
    echo "0. Retour au menu\n";
    echo "Votre choix : ";
    $choix = trim(fgets(STDIN));

    if ($choix === '0') {
        echo "Retour au menu principal.\n";
        return;
    }

    if (!array_key_exists($choix, $options)) {
        echo "Choix invalide.\n";
        return;
    }

    $joueur = $options[$choix];
    $ordi = $options[array_rand($options)];

    echo "Vous avez choisi : $joueur\n";
    echo "L'ordinateur a choisi : $ordi\n";

    $resultat = determinerGagnant($joueur, $ordi);
    echo "Résultat : $resultat\n";

    enregistrerPartie($joueur, $ordi, $resultat);
}

function determinerGagnant($joueur, $ordi) {
    if ($joueur === $ordi) return "Égalité";

    $gagnants = [
        'Pierre' => 'Ciseaux',
        'Feuille' => 'Pierre',
        'Ciseaux' => 'Feuille'
    ];

    return $gagnants[$joueur] === $ordi ? "Victoire" : "Défaite";
}

function enregistrerPartie($joueur, $ordi, $resultat) {
    $data = json_decode(@file_get_contents(HISTORIQUE_FILE), true);
    if (!is_array($data)) {
        $data = [];
    }

    $data[] = [
        'date' => date('Y-m-d H:i:s'),
        'joueur' => $joueur,
        'ordinateur' => $ordi,
        'résultat' => $resultat
    ];

    file_put_contents(HISTORIQUE_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

function afficherHistorique() {
    echo "\n=== Historique ===\n";
    $data = json_decode(@file_get_contents(HISTORIQUE_FILE), true);
    if (!is_array($data) || empty($data)) {
        echo "Aucune partie enregistrée.\n";
        return;
    }

    foreach ($data as $i => $partie) {
        echo ($i+1) . ". " . $partie['date'] . " - Joueur: {$partie['joueur']} | Ordi: {$partie['ordinateur']} | Résultat: {$partie['résultat']}\n";
    }
}

function afficherStats() {
    echo "\n=== Statistiques ===\n";
    $data = json_decode(@file_get_contents(HISTORIQUE_FILE), true);
    if (!is_array($data) || empty($data)) {
        echo "Aucune donnée disponible.\n";
        return;
    }

    $victoires = $défaites = $égalités = 0;
    foreach ($data as $partie) {
        switch ($partie['résultat']) {
            case 'Victoire': $victoires++; break;
            case 'Défaite': $défaites++; break;
            case 'Égalité': $égalités++; break;
        }
    }

    echo "Total parties : " . count($data) . "\n";
    echo "Victoires : $victoires\n";
    echo "Défaites : $défaites\n";
    echo "Égalités : $égalités\n";
}

// === Menu principal ===
do {
    echo "\n=== Menu Principal ===\n";
    echo "1. Nouvelle partie\n";
    echo "2. Voir l'historique\n";
    echo "3. Voir les statistiques\n";
    echo "0. Quitter\n";
    echo "Votre choix : ";
    $choix = trim(fgets(STDIN));

    switch ($choix) {
        case '1': jouerPartie(); break;
        case '2': afficherHistorique(); break;
        case '3': afficherStats(); break;
        case '0': exit("À bientôt !\n");
        default: echo "Choix invalide.\n"; break;
    }
} while (true);
