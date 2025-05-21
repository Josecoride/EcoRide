<?php
$query = strtolower($_GET['q'] ?? '');

$villes = json_decode(file_get_contents('villes.json'));

$resultats = array_filter($villes, function($ville) use ($query) {
    return strpos(strtolower($ville), $query) !== false;
});

echo json_encode(array_values($resultats));
