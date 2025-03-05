
<?php $title = "Statistiques" ?>
<?php

require("./include/header.inc.php"); 

incrementVisits();



$filename = './data/gares_recherche';
?>


    <?php echo '<section class="statistics-section">';
    echo '<h2>Statistiques des gares et addreses les plus rechercher</h2>';
    echo '<div class="graph-container" id="stat">' . generateGraph($filename) . '</div>';
    echo '</section>';
     ?> 


<section class="statistics-section">
    <h2>Statistiques des visites</h2>
    <div class="graph-container" id="stat_visit">
        <?php
        $filename = './data/visits_data.txt';

         echo generateGraph2($filename); ?>
    </div>
</section>


<?php require("./include/footer.inc.php"); ?>