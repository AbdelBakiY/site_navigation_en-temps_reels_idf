<?php

declare(strict_types=1);
echo "<!DOCTYPE html>
<html lang=\"fr\">";
require("./include/functions.inc.php");

setStyleCookie();

$mode = isset($_GET['mode']) ? $_GET['mode'] : (isset($_COOKIE['mode']) ? $_COOKIE['mode'] : 'Jour');
setcookie('mode', $mode, time() + (86400 * 30), "/");
resetCookie();
?>


<head>
    <meta charset="utf-8" />
    <title><?php echo $title ?></title>
    <link rel="shortcut icon" href="images/logo.png" />
    <link rel="stylesheet" href=<?php echo "\"mode" . $mode . ".css\""; ?> />
    <script src="<?= htmlspecialchars("https://maps.googleapis.com/maps/api/js?key=AIzaSyA-kUxHa-9zg4HUO0SIzfVYC2pu9xOIQko&libraries=places&callback=initAutocomplete")?>" async="async" defer="defer"></script>

    <style>
        .site-header {
            background: url("images/<?php echo "mode" . $mode . ".webp"; ?>") no-repeat center center;
        }   
    </style>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "20%";
            document.querySelector('header').classList.add("blur-background");
            document.querySelector('footer').classList.add("blur-background");
            document.querySelector('section').classList.add("blur-background");
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.querySelector('header').classList.remove("blur-background");
            document.querySelector('main').classList.remove("blur-background");
            document.querySelector('footer').classList.remove("blur-background");
            document.querySelector('section').classList.remove("blur-background");
        }
    </script>

</head>

<body>
    <header class="site-header">


        <nav class="top-nav" id="top-nav">
            <div class="menu-bar" onclick="openNav()" tabindex="0">☰ Menu</div>


            <a href="index.php" title="retourner à l'accueil ">
                <div class="enTete-text">
                    <?php echo  htmlspecialchars("Trains IDF : Horaires & Itinéraires en Région Parisienne")?>
                </div>
            </a>
            <label for="theme" class="theme">
                <span class="theme__toggle-wrap">
                <input id="theme" class="theme__toggle" type="checkbox" role="switch" name="theme" value="<?php echo $mode == "Nuit" ? 'dark' : 'Jour'; ?>" <?php echo $mode == "Nuit" ? 'checked="checked"' : ''; ?> tabindex="0" />

                    <span class="theme__icon">
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                        <span class="theme__icon-part"></span>
                    </span>
                </span>
            </label>
        </nav>







        <div class="header-content">
            <?php if (basename($_SERVER['PHP_SELF']) == 'horaires.php') : ?>
                <h1>Consultation des Horaires</h1>
                <div class="search-form-container">
                    <form action="horaires.php" method="get" class="search-form">
                        <label for="gare">Gare</label>
                        <input type="text" id="gare" name="gare" value="<?php echo htmlspecialchars($_GET['gare'] ?? '') ?>" list="suggestions" required="required"/>
                        <?php echo nomLieux();?>
                        <label for="typeTransport">Type de transport (facultatif)</label>
                        <input type="text" id="typeTransport" name="typeTransport" value="<?php echo htmlspecialchars($_GET['typeTransport'] ?? '') ?>"/>
                        <label for="direction">Direction (facultatif)</label>
                        <input type="text" id="direction" name="direction" value="<?php echo htmlspecialchars($_GET['direction'] ?? '') ?>"/>
                        <button type="submit">Voir les horaires</button>
                    </form>
                </div>


            <?php elseif (basename($_SERVER['PHP_SELF']) == 'statistique.php') : ?>
        <div>
                <h1>Graphique des recherches de gares et des adresses</h1>
                <p>Découvrez les statistiques les plus récentes sur les gares les plus recherchées et les adresses les plus visitées. Notre page offre une présentation visuelle claire de ces données grâce à des graphiques dynamiques, vous permettant ainsi de mieux comprendre les tendances de recherche et de fréquentation.</p>
                <a href="statistique.php#stat" class="cta">Voir les statistiques</a>
        </div>

            <?php elseif (basename($_SERVER['PHP_SELF']) == 'tech.php'): ?>
                <h1>Nasa image du jour</h1>
                <p>Découvrez l'univers à travers les yeux de la NASA ! Notre fonctionnalité 'Image du Jour' vous propose chaque jour une nouvelle merveille captivante de l'espace. Explorez des galaxies lointaines, des nébuleuses chatoyantes et des planètes fascinantes, directement depuis votre navigateur. </p>

                <a href="tech.php#nasa-img" class="cta">Regardez l'image</a>
               
               
                <?php elseif (basename($_SERVER['PHP_SELF']) == 'plan.php'): ?>
                <h1>Plan du site</h1>
                <p>Bienvenue sur la page du plan du site ! Ici, vous trouverez une liste complète de toutes les pages disponibles sur notre site Web </p>
                <a href="plan.php#pan_site" class="cta">Regardez le plan</a>
                <?php elseif (basename($_SERVER['PHP_SELF']) == 'a-propos.php'): ?>
                <h1>À Propos de Nous</h1>
                <p>Bienvenue sur notre page "À Propos de Nous". Nous sommes deux passionnés d'informatique et de développement web. Découvrez un aperçu de nos compétences et de nos intérêts dans le domaine de la technologie.</p>
                <a href="a-propos.php#propos" class="cta">A propos</a>

                
        <?php else : ?>
                <h1>Planifiez votre itinéraire</h1>
                <p>Bienvenue sur notre plateforme dédiée à la navigation ferroviaire en Île-de-France. Planifiez vos déplacements et consultez les horaires de trains pour explorer facilement la région parisienne. Simplifiez votre expérience de voyage avec nos outils intuitifs et nos informations en temps réel.</p>

                <a href="index.php#navg" class="cta">Naviguer en temps réel</a>
            <?php endif; ?>


        </div>
    </header>
    <div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()" tabindex="0"><?=htmlspecialchars("×")?></a>       
    <a href="index.php">Accueil</a>
    <a href="horaires.php">Horaires de passage</a>
        <a href="statistique.php">Statistique</a>
    </div>
    <main>