
<?php $title = "Plan du site" ?>
<?php 

require("./include/header.inc.php"); 


incrementVisits();


$filename = './data/gares_recherche';
?>


<section class="historique-box">
<div id="pan_site">
    <h1>Plan du site</h1>
</div>
<div class="lienplan">
<ul>
    <li><a href="./index.php">Accueil</a></li>
    <li><a href="./horaires.php">horaires</a></li>
    <li><a href="./a-propos.php">a propos</a></li>
    <li><a href="./statistique.php">statistique</a></li>
    <li><a href="./tech.php">Page technique</a></li>
</ul>
</div>
</section>


<?php require("./include/footer.inc.php"); ?>