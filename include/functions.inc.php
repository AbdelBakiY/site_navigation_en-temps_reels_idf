<?php

/**
 * Définit le répertoire des images.
 *
 * @var string Chemin du répertoire des images.
 *
 * @author Groupe B2
 * @version PHP 8
 */
define('IMAGES_DIRECTORY', './images/rondomes');


/**
 * Retourne une chaîne de caractères représentant une liste de lieux à partir des données du fichier 'lignes.txt'.
 *
 * @return string La chaîne de caractères représentant une liste de lieux.
 *
 * @author Groupe B2
 * @version PHP 8
 */

function nomLieux(): string
{
    $data = "<datalist id=\"suggestions\">";
    $nom_fichier = "./data/lignes.txt";
    $fichier = fopen($nom_fichier, "r");
    while (!feof($fichier)) {
        $ligne = fgets($fichier);
        $data .= "<option value=\"" . $ligne . "\" ></option> \n";
    }
    fclose($fichier);
    $data .= "</datalist>";
    return $data;
}




/**
 * Réinitialise les cookies en supprimant certains et en réinitialisant d'autres à leur valeur par défaut.
 *
 * @return void
 *
 * @author Groupe B2
 * @version PHP 8
 */

function resetCookie(): void
{
    if (isset($_COOKIE)) {
        foreach ($_COOKIE as $key => $value) {
            if ($key == 'mode') {
                if ($_COOKIE[$key] != 'Nuit' && $_COOKIE[$key] != 'Jour') {
                    setcookie($key, "Jour", time() + (86400 * 30), "/");
                }
            }

            if ($key !== 'mode' && $key !== 'historique_trajets') {
                setcookie($key, "", time() - 3600, "/");
                unset($_COOKIE[$key]);
            }
        }
    }
}



/**
 * Définit le cookie 'mode' pour stocker le mode d'affichage actuel.
 * Si le paramètre 'mode' est présent dans $_GET, utilise sa valeur pour définir le cookie.
 * Sinon, utilise la valeur du cookie 'mode' actuel s'il existe, sinon utilise 'Jour' par défaut.
 *
 * @return void
 *
 * @author Groupe B2
 * @version PHP 8
 */


function setStyleCookie(): void
{
    $mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_COOKIE['mode']) ? $_COOKIE['mode'] : 'Jour');
    setcookie('mode', $mode, time() + (86400 * 30), "/");
}

/**
 * Définit le token utilisé pour l'authentification ou l'identification.
 *
 * @var string Le token d'authentification ou d'identification.
 *
 * @author Groupe B2
 * @version PHP 8
 */
define("TOKEN", "2374346e-167c-4843-9c70-92c38f66e8a6");


/**
 * Récupère l'identifiant de la gare à partir de son nom en utilisant l'API Navitia.
 *
 * @param string $gare Le nom de la gare.
 * @return string L'identifiant de la gare.
 *
 * @throws Exception Si une erreur survient lors de l'appel à l'API.
 *
 * @author Groupe B2
 * @version PHP 8
 */
function recupererIDGare(String $gare): string
{

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.navitia.io/v1/coverage/fr-idf/places?q=" . urlencode($gare));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: " . TOKEN));
    $fluxJson = curl_exec($curl);
    if (curl_errno($curl) || $fluxJson === false || $fluxJson === null) {
        return "None";
    }


    $data = json_decode($fluxJson, true);
    curl_close($curl);
    if (isset($data["places"][0]["id"])) {
        $gareID = $data["places"][0]["id"];
        return $gareID;
    } else {
        return "None";
    }

   
}






/**
 * Récupère les prochains départs à partir de l'identifiant de la gare en utilisant l'API Navitia.
 *
 * @param string $gare Le nom de la gare.
 * @return string Une liste des prochains départs depuis la gare spécifiée.
 *
 * @throws Exception Si une erreur survient lors de l'appel à l'API.
 *
 * @see recupererIDGare()
 * @author Groupe B2
 * @version PHP 8
 */
function  departures(string $gare): string
{
    if (recupererIDGare($gare) == "None") {
        return "Nom de la Gare est incorrecte ";
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.navitia.io/v1/coverage/fr-idf/stop_areas/" . recupererIDGare($gare) . "/departures");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: " . TOKEN));
    $fluxJson = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Erreur Curl:' . curl_error($curl);
    }
    $data = json_decode($fluxJson, true);
    curl_close($curl);

    $heures = "<ul>";
    foreach ($data['departures'] as $departure) {
        $time = new DateTime($departure['stop_date_time']['departure_date_time']);
        $lineCode = $departure['display_informations']['code'];
        $direction = $departure['display_informations']['direction'];

        $heures .= "<li>";
        $heures .= $departure['display_informations']['physical_mode'] . " " . $lineCode . " vers " . $direction . " - Départ à: " . $time->format('H:i');
        $heures .= "</li>";
    }
    $heures .= "</ul>";
    return $heures;
}






/**
 * Récupère les prochains départs filtrés par type de transport et/ou direction à partir de l'identifiant de la gare en utilisant l'API Navitia.
 *
 * @param string $gare Le nom de la gare.
 * @param string|null $typeTransport Le type de transport à filtrer (optionnel).
 * @param string|null $direction La direction à filtrer (optionnel).
 * @return string Une liste des prochains départs depuis la gare spécifiée, filtrés selon les critères fournis.
 *
 * @throws Exception Si une erreur survient lors de l'appel à l'API.
 *
 * @see departures()
 * @author Groupe B2
 * @version PHP 8
 */
function departuresFiltered(string $gare, string $typeTransport = null, string $direction = null): string
{
    if (recupererIDGare($gare) == "None") {
        return "Nom de la Gare est incorrecte ";
    }

    $departuresHtml = departures($gare);

    $filteredResults = "<div class='departures-container'>";
    $filteredResults .= "<h2>Horaires de Passage</h2>";
    $filteredResults .= "<div class='departures-list'>";

    preg_match_all("/<li>(.*?)<\/li>/s", $departuresHtml, $matches);

    if (!empty($matches[1])) {
        foreach ($matches[1] as $match) {
            $conditionTransport = is_null($typeTransport) || stripos($match, $typeTransport) !== false;
            $conditionDirection = is_null($direction) || stripos($match, $direction) !== false;

            if ($conditionTransport && $conditionDirection) {
                $filteredResults .= "<div class='departure-item'>$match</div>";
            }
        }
    }

    $filteredResults .= "</div>";
    $filteredResults .= "</div>";

    return $filteredResults;
}












/**
 * Formate une chaîne de caractères représentant une date au format "Ymd\THis" en une date au format "d-m-Y H:i:s".
 *
 * @param string $dateString La chaîne de caractères représentant une date au format "Ymd\THis".
 * @return string La date formatée au format "d-m-Y H:i:s", ou un message d'erreur si le format de date est invalide.
 *
 * @see https://www.php.net/manual/fr/datetime.createfromformat.php
 * @author Groupe B2
 * @version PHP 8
 */
function formatDateFromString($dateString)
{
    $date = DateTime::createFromFormat("Ymd\THis", $dateString);

    if ($date) {
        return $date->format("d-m-Y H:i:s");
    } else {
        return "Format de date invalide.";
    }
}



/**
 * Formate une date et une heure en un format spécifique.
 *
 * @param string $inputDateTime La date et l'heure d'entrée à formater.
 * @return string La date et l'heure formatées.
 *
 * @author Groupe B2
 * @version PHP 8
 */
function formatDateTime($inputDateTime)
{

    $formattedDateTime = str_replace(['-', ':'], '', $inputDateTime);


    $formattedDateTime = substr($formattedDateTime, 0, 8) . substr($formattedDateTime, 8);


    $formattedDateTime .= '00';

    return $formattedDateTime;
}





/**
 * Formate une date et une heure en un format spécifique (heure, date).
 *
 * @param string $datetime La date et l'heure à formater.
 * @return string La date et l'heure formatées dans le format "heure, date".
 *
 * @see https://www.php.net/manual/fr/function.strtotime.php
 * @see https://www.php.net/manual/fr/function.date.php
 * @author Groupe B2
 * @version PHP 8
 */
function formaterDateHeure($datetime)
{
    $timestamp = strtotime($datetime);

    $heure = date('H:i', $timestamp);
    $date = date('d/m/Y', $timestamp);

    return "$heure, $date";
}





/**
 * Recherche et affiche les trajets disponibles entre deux lieux avec leurs détails.
 *
 * @param string $depart Le lieu de départ.
 * @param string $arriver Le lieu d'arrivée.
 * @param string $date La date du trajet au format "Ymd\THis".
 * @return string Une liste des trajets disponibles avec leurs détails.
 *
 * @throws Exception Si une erreur survient lors de l'appel à l'API ou du formatage des dates.
 *
 * @see recupererIDGare()
 * @see formatDateTime()
 * @see formatDateFromString()
 * @see formaterDateHeure()
 * @see formatSection()
 * @author Groupe B2
 * @version PHP 8
 */
function naviguer(string $depart, string $arriver, $date): string
{
    if ($depart == $arriver) {
        return "le lieu de depart doit etre différent de lieu  d'arriver";
    }

    if (recupererIDGare($arriver) == "None" || recupererIDGare($depart) == "None") {
        return "format de lieu de depart ou d'arriver est faux , il faut resseyer avec des nouvelles valeur ";
    }


    $url = "https://api.navitia.io/v1/coverage/fr-idf/journeys?from=" . recupererIDGare($depart) . "&to=" . recupererIDGare($arriver) . "&datetime=" . formatDateTime($date);
    $contextOptions = [
        "http" => [
            "method" => "GET",
            "header" => "Authorization: " . TOKEN
        ]
    ];
    $context = stream_context_create($contextOptions);
    $fluxJson = file_get_contents($url, false, $context);

    if ($fluxJson === FALSE) {
        return 'Erreur lors de la requête.';
    }

    $data = json_decode($fluxJson, true);
    if (empty($data['journeys'])) {
        return 'Aucun trajet trouvé.';
    }
    $html = "<h2 id=\"trajet\">Trajets proposés</h2>";
    $html .= "<div class='trajets-container'>";
    $numTrajet = 1;
    foreach ($data['journeys'] as $journey) {
        $heureDepart = formatDateFromString($journey["departure_date_time"]);
        $heureArrivee = formatDateFromString($journey["arrival_date_time"]);
        $duree = round($journey["duration"] / 60);
        $prix = isset($journey['fare']['total']['value']) ? $journey['fare']['total']['value'] / 100 : "N/A";

        $html .= "<div class='trajet-box'>";
        $html .= "<h2>Trajet $numTrajet</h2>";
        $html .= "
        <div class='trajet-info' style='display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: auto auto; gap: 0.5%; justify-content: center; align-items: center; width: 40%; margin-left:32%'>
            <p style='grid-column: 1 / 2; grid-row: 1 / 2;'><strong style='color: #4a8196;'>Départ:</strong> " . formaterDateHeure($heureDepart) . "</p>
            <p style='grid-column: 2 / 3; grid-row: 1 / 2;'><strong style='color: #4a8196;'>Arrivée:</strong> " . formaterDateHeure($heureArrivee) . "</p>
            <p style='grid-column: 1 / 2; grid-row: 2 / 3;'><strong style='color: #4a8196;'>Durée:</strong> $duree minutes</p>
            <p style='grid-column: 2 / 3; grid-row: 2 / 3;'><strong style='color: #4a8196;'>Coût:</strong> $prix €</p>
        </div>";
        $html .= "<div class='correspondances-container'>\n";

        $indexSection = 1;
        foreach ($journey['sections'] as $index => $section) {
            if ($section['type'] == 'public_transport' || $section['type'] == 'street_network' && $section['mode'] == 'walking') {
                $html .= formatSection($section, $indexSection, ($section['type'] == 'public_transport'));

                if ($index + 2 < count($journey['sections'])) {
                    $html .= "<div class='icon-changement'></div>\n";
                }

                $indexSection++;
            }
        }



        $html .= "</div>";
        $html .= "</div>";
        $numTrajet++;
    }
    $html .= "</div>";


    return $html;
}









/**
 * Formate une section de trajet avec ses détails, tels que les arrêts, les correspondances, etc.
 *
 * @param array $section Les données de la section de trajet.
 * @param int $indexSection L'index de la section de trajet.
 * @param bool $isPublicTransport Indique si la section de trajet est un transport en commun.
 * @return string Le code HTML représentant la section de trajet formatée.
 * @author Groupe B2
 * @see formaterDateHeure()
 * @throws Exception Si une erreur survient lors du formatage des dates ou si les données de la section sont invalides.
 * @version PHP 8
 */
function formatSection($section, $indexSection, $isPublicTransport)
{
    $html = "<div class='correspondance'>";
    if ($isPublicTransport) {
        $gareDepart = $section["from"]["name"];
        $gareArrivee = $section["to"]["name"];
        $typeTransport = $section["display_informations"]["commercial_mode"];
        $codeTransport = $section["display_informations"]["code"];
        $direction = $section["display_informations"]["direction"];

        $titreCorrespondance = "Correspondance " . $indexSection;
        $html .= "<a class='decouvrir-horaires' href=\"" . str_replace(' ', '', "horaires.php?gare=" . urlencode($gareDepart) . "&amp;typeTransport=" . urlencode($typeTransport . " " . $codeTransport) . "&amp;direction=" . urlencode(substr($direction, 0, strpos($direction, '('))) . "\"" . "   >") . "Découvrir horaires de passage</a>";

        $html .= "<h3 class='titre-correspondance'>$titreCorrespondance</h3>";

        $detailsCorrespondance = "
        <div style='display:grid; grid-template-rows:auto auto; justify-content:center; align-items:center;'>
            <p style='background: linear-gradient(#388E3C, #1976D2); -webkit-background-clip: text; color: transparent; font-size: 16px; text-align: center; margin-bottom: 8px;'>
                <strong>" . $typeTransport . " " . $codeTransport . "</strong>
            </p>
            <p><strong style='color: #4a8196;'>Direction:</strong> " . $direction . "</p>
        </div>";

        $html .= "<div class='details-correspondance'>";
        $html .= "<div>$detailsCorrespondance</div>";
        $html .= "<script>
             function toggleStations(containerId, button) {
            var container = document.getElementById(containerId);
            var isDisplayed = container.style.display === 'block';
            container.style.display = isDisplayed ? 'none' : 'block';
            button.classList.toggle('open', !isDisplayed);
            }
            </script>
        </div>";
    }

    if ($section['type'] == 'public_transport') {
        $gareDepart = $section["from"]["name"];
        $gareArrivee = $section["to"]["name"];
        $dureeSection = round($section["duration"] / 60);
        $nombreArrets = isset($section["stop_date_times"]) ? count($section["stop_date_times"]) : 0;

        $stationsListId = "stationsList_" . $indexSection . "_" . uniqid();
        $toggleButtonId = "toggleButton_" . $indexSection . "_" . uniqid();

        $html .= "<div class='section-details'>";

        $html .= "<div class='station-depart'><strong>$gareDepart</strong></div>";

        if ($nombreArrets > 2) {
            $html .= "<div class='trajet-info'>";
            $html .= "<button id='$toggleButtonId' class='toggle-stations-button' onclick='toggleStations(\"$stationsListId\", this)'></button>";
            $html .= "<span>$dureeSection min ($nombreArrets arrêts)</span>";
            $html .= "</div>";

            $html .= "<div id='$stationsListId' class='stations-list' style='display: none;'>";
            foreach ($section["stop_date_times"] as $index => $stop) {
                if ($index != 0 && $index != $nombreArrets - 1) {
                    $stationName = $stop["stop_point"]["name"];
                    $html .= "<div class='station'>$stationName</div>";
                }
            }
            $html .= "</div>";
        } else {
            $html .= "<div class='trajet-info sans-bouton'>$dureeSection min ($nombreArrets arrêts)</div>";
        }

        $html .= "<div class='station-arrivee'><strong>$gareArrivee</strong></div>";
        $html .= "</div>";
    } elseif ($section['type'] == 'street_network' && $section['mode'] == 'walking') {

        $dureeMarche = round($section["duration"] / 60);
        $de = isset($section["from"]["name"]) ? "de " . $section["from"]["name"] : "d'un point non spécifié";
        $a = isset($section["to"]["name"]) ? "à " . $section["to"]["name"] : "à un point non spécifié";

        $titreCorrespondance = "Marche $indexSection";
        $html .= "<h3 class='titre-correspondance'>$titreCorrespondance</h3>";
        $html .= "<div><span style='background: linear-gradient(#388E3C, #1976D2); -webkit-background-clip:text; color:transparent'>Durée:</span> $dureeMarche minutes</div>";
        $html .= "<div><span style='background: linear-gradient(#388E3C, #1976D2); -webkit-background-clip:text; color:transparent'>Itinéraire:</span> $de $a</div>";
        $html .= "<img src='images/marche.png' alt='Homme qui marche' style='display:block; margin-top:8%;margin-left:40%; width:20%; height:auto;'/>";
    }

    $html .= "</div>";
    return $html;
}






/**
 * Génère un graphique représentant les statistiques des recherches de gares à partir d'un fichier.
 *
 * @param string $filename Le nom du fichier contenant les données des recherches de gares.
 * @return string Le code HTML représentant le graphique des statistiques.
 * @author Groupe B2
 * @version PHP 8
 */
function generateGraph($filename)
{
    // Lire le fichier et compter les occurrences de chaque gare
    $gares = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $compteGares = array_count_values($gares);

    $html = ''; // Initialiser la variable qui contiendra le code HTML

    // Définir un tableau de couleurs
    $colors = ['#FF6347', '#4682B4', '#32CD32', '#FFD700'];

    // Vérifier s'il y a des données
    if (count($compteGares) > 0) {
        // Tri des données par nombre décroissant de recherches
        arsort($compteGares);

        // Ne garder que les quatre premiers éléments ou moins si moins de quatre gares sont disponibles
        $topGares = array_slice($compteGares, 0, min(4, count($compteGares)), true);

        // La plus grande valeur pour déterminer la largeur maximale des barres
        $maxValue = max($topGares);
        $maxWidthPercentage = 80; // Maximum width of a bar as a percentage of the container

        // Construction du tableau HTML
        $html .= '<table class="statistictable">';
        $i = 0; // Index pour parcourir le tableau des couleurs
        foreach ($topGares as $gare => $count) {
            $color = $colors[$i % count($colors)]; // Sélectionner une couleur de manière cyclique
            $widthPercentage = ($count / $maxValue * $maxWidthPercentage);
            $html .= '<tr style="height:50px">';
            $html .= '<th>' . htmlspecialchars($gare) . '</th>';
            $html .= '<td style="width: 100%;"><div class="bar" style="width: ' . $widthPercentage . '%; background-color: ' . $color . ';">' . $count . '</div></td>';
            $html .= '</tr>';
            $i++; // Incrémenter l'index
        }
        $html .= '</table>';
    } else {
        $html .= '<p>Aucune recherche de gare n\'a été enregistrée.</p>';
    }
    return $html;
}






/**
 * Ajoute le nom d'une gare à un fichier de données.
 *
 * @param string $gareName Le nom de la gare à ajouter au fichier.
 * @return string Un message indiquant le résultat de l'opération.
 *
 * @version PHP 8
 * @author Groupe B2
 */
function addGareToFile($gareName)
{
    $filename = "./data/gares_recherche"; // Chemin vers le fichier où les noms des gares seront stockés

    // Ouvrir le fichier en mode ajout
    $file = fopen($filename, "a");
    if (!$file) {
        return "Erreur lors de l'ouverture du fichier.";
    }

    // Écrire le nom de la gare dans le fichier
    fwrite($file, $gareName . "\n");
    fclose($file);

    return "Le nom de la gare a été enregistré avec succès.";
}






/**
 * Retourne aléatoirement le chemin d'accès à une image ou son titre à partir du répertoire spécifié.
 *
 * @param int $para Si $para est 0, retourne le chemin d'accès à une image, sinon retourne le titre de l'image.
 * @return string Le chemin d'accès à une image ou son titre.
 *
 * @version PHP 8
 * @author Groupe B2
 */
function getrondomimg($para)
{
    $images = glob(IMAGES_DIRECTORY . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    // Sélectionne aléatoirement une image parmi la liste
    $randomImage = $images[array_rand($images)];

    // Récupère le titre de l'image à partir du nom du fichier (sans l'extension) et remplace les underscores par des espaces
    $imageTitle = str_replace('_', ' ', pathinfo($randomImage, PATHINFO_FILENAME));

    // Retourne un tableau contenant le chemin d'accès à l'image et son titre
    if ($para == 0) {
        return $randomImage;
    } else return $imageTitle;
}



/**
 * Incrémente le compteur de visites.
 *
 * Si le fichier spécifié n'existe pas, il est créé et initialisé à zéro.
 * Le compteur est ensuite incrémenté et sauvegardé dans le fichier.
 *
 * @param string $filename Le nom du fichier où est stocké le compteur.
 * @return int Le nouveau nombre de visites après l'incrémentation.
 *
 * @version PHP 8
 * @author Groupe B2
 */
function incrementHitsCounter($filename)
{
    if (!file_exists($filename)) {
        file_put_contents($filename, '0');
    }

    $hits = intval(file_get_contents($filename));

    $hits++;
    file_put_contents($filename, $hits);

    return $hits;
}





/**
 * Incrémente le nombre de visites pour la date actuelle.
 * Les données sont stockées dans un fichier texte.
 *
 * @return void
 *
 * @version PHP 8
 * @author Groupe B2
 */
function incrementVisits()
{
    $directory = 'data';
    $filename = $directory . '/visits_data.txt';
    $currentDate = date('Y-m-d');

    // Vérifie si le répertoire existe, sinon le crée
    if (!file_exists($directory)) {
        mkdir($directory);
    }

    // Vérifie si le fichier existe
    if (file_exists($filename)) {
        // Lit le contenu du fichier
        $data = file_get_contents($filename);

        // Découpe les lignes en entrées individuelles
        $lines = explode(PHP_EOL, $data);

        // Parcours des entrées pour trouver la date actuelle
        $found = false;
        foreach ($lines as &$line) {
            $parts = explode(',', $line);
            if ($parts[0] === $currentDate) {
                // Si la date actuelle est trouvée, incrémente le nombre de visites
                $parts[1]++;
                $line = implode(',', $parts);
                $found = true;
                break;
            }
        }

        // Si la date actuelle n'est pas trouvée, ajoute une nouvelle entrée
        if (!$found) {
            $lines[] = "$currentDate,1";
        }

        // Réassemble les lignes pour former un seul texte
        $data = implode(PHP_EOL, $lines);
    } else {
        // Si le fichier n'existe pas, crée une nouvelle entrée avec la date actuelle
        $data = "$currentDate,1";
    }

    // Enregistre les données mises à jour dans le fichier
    file_put_contents($filename, $data);
}





/**
 * Génère un graphique représentant le nombre de visites par jour pour les 4 derniers jours à partir d'un fichier.
 *
 * @param string $filename Le nom du fichier contenant les données des visites par jour.
 * @return string Le code HTML représentant le graphique des visites.
 *
 * @version PHP 8
 * @author Groupe B2
 */
function generateGraph2($filename)
{
    // Lire le fichier et compter les occurrences de chaque date
    $data = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Inverser l'ordre des données pour obtenir les 4 derniers jours à partir de la fin
    $data = array_reverse($data);

    // Garder les données des 4 derniers jours
    $last4Days = array_slice($data, 0, 4, true);

    // Trouver la valeur maximale de visites parmi les 4 derniers jours
    $maxVisits = 0;
    foreach ($last4Days as $line) {
        list($date, $visits) = explode(',', $line);
        $maxVisits = max($maxVisits, $visits);
    }

    // Couleurs des barres
    $colors = ['#FF6347', '#4682B4', '#32CD32', '#FFD700'];

    // Construction du tableau HTML
    $html = '<h2>Nombre de visites par jour (4 derniers jours)</h2>';
    $html .= '<table class="statistictable">';
    $i = 0;
    foreach ($last4Days as $line) {
        list($date, $visits) = explode(',', $line);

        // Calculer la largeur de la barre en fonction de la valeur maximale de visites
        $barWidth = ($visits / $maxVisits) * 500; // 200 pixels est la largeur maximale

        $html .= '<tr>';
        $html .= '<th>' . htmlspecialchars($date) . '</th>';
        $html .= '<td style="width: 100%;"><div class="bar" style="width: ' . $barWidth . 'px; background-color: ' . $colors[$i] . ';">' . $visits . '</div></td>';
        $html .= '</tr>';
        $i++;
    }
    $html .= '</table>';
    return $html;
}
