<?php $title = "Horaires de passage" ?>
<?php
require("./include/header.inc.php");
incrementVisits();

$gare = isset($_GET['gare']) ? $_GET['gare'] : '';
addGareToFile($gare);
$typeTransport = isset($_GET['typeTransport']) ? $_GET['typeTransport'] : '';
$direction = isset($_GET['direction']) ? $_GET['direction'] : '';



?>




<?php
if (!empty($gare)) {
    if (!empty($typeTransport) && !empty($direction)) {
        echo departuresFiltered(urldecode($gare), urldecode($typeTransport), urldecode($direction));
    } elseif (!empty($typeTransport)) {
        echo departuresFiltered(urldecode($gare), urldecode($typeTransport));
    } else {
        echo departuresFiltered($gare);
    }
} else {
    echo "<p style=\"text-align:center;\">Veuillez spécifier une gare pour voir les horaires.</p>";
}
?>

<script src="scripts.js"></script>
<?php
require("./include/footer.inc.php");
?>