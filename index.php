
<?php $title = "Horaires et les itinéraires des trains dans la région parisienne" ?>
<?php
 require("./include/header.inc.php");
 incrementVisits();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['depart'], $_POST['arrive'], $_POST['datetime'])) {
    $depart = $_POST['depart'];
    $arrive = $_POST['arrive'];
    $datetime = $_POST['datetime'];

    $trajet = array(
      'depart' => $depart,
      'arrive' => $arrive,
      'datetime' => $datetime
    );

    $historique_trajets = isset($_COOKIE['historique_trajets']) ? unserialize($_COOKIE['historique_trajets']) : array();

    $historique_trajets[] = $trajet;

    if (count($historique_trajets) > 5) {
      array_shift($historique_trajets);
    }

    $historique_trajets_serialized = serialize($historique_trajets);
    setcookie('historique_trajets', $historique_trajets_serialized, time() + (7 * 24 * 3600), '/');
  }
}
?>




<section id="navg">
<div class="form_rondom">


  <div class="recherche">

  <h2>Itinéraires de Trains en Île-de-France</h2>
  <div class="form-box">
    <h3>Planifiez votre itinéraire</h3>
    <form action="index.php#trajet" method="post">
      <label for="depart">Départ</label>
      <input type="text" id="depart" name="depart" value="<?php echo isset($_POST['depart']) ? htmlspecialchars($_POST['depart']) : ''; ?>" required="required" />
      <button type="button" id="exchangeIcon" onclick="echangerLesValeurs();">⇅</button>

      <label for="arrive">Arrivé</label>
      <input type="text" id="arrive" name="arrive" value="<?php echo isset($_POST['arrive']) ? htmlspecialchars($_POST['arrive']) : ''; ?>" required="required" />
      <label for="datetime">Quand</label>
      <input type="datetime-local" id="datetime" name="datetime" required="required" value="<?php echo isset($_POST['datetime']) ? htmlspecialchars($_POST['datetime']) : date('Y-m-d\TH:i'); ?>" />
      <button type="submit">GO!</button>
    </form>
  </div>
  </div >
  <div class="rondom-img">
    <h2>Image aléatoire </h2>
    <div class="image-gare">
    <figure  style="text-align: center;" >
      <img src="<?=getrondomimg(0)?>" alt="image de garre"  style="display: block; margin: 0 auto"/>
      <figcaption ><?=getrondomimg(1)?></figcaption>
      </figure>
</div>
</div>
  </div>

  <div class="traj">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if (isset($_POST['depart'], $_POST['arrive'], $_POST['datetime'])) {
        $depart = $_POST['depart'];
        addGareToFile($depart);
        $arrive = $_POST['arrive'];
        addGareToFile($arrive);
        $datetime = $_POST['datetime'];
        echo naviguer($depart, $arrive, $datetime);
      }
    } else {
      if (isset($_COOKIE['historique_trajets'])) {
        $historique_trajets = unserialize($_COOKIE['historique_trajets']);
        if (!empty($historique_trajets)) {
          echo "<div class='historique-box'>";
          echo "<h3>Historique des trajets</h3>";
          foreach ($historique_trajets as $trajet) {
            echo "<div class='trajet-box'>";
            echo "<p>Départ: " . htmlspecialchars($trajet['depart']) . "</p>";
            echo "<p>Arrivée: " . htmlspecialchars($trajet['arrive']) . "</p>";
            echo "<p>Date: " . htmlspecialchars($trajet['datetime']) . "</p>";
            echo "</div>";
          }
          echo "</div>";
        } else {
          echo "<div class='historique-box'>";
          echo "<h3>Historique des trajets</h3>";
          echo "<p>Aucun historique de trajets trouvé.</p>";
          echo "</div>";
        }
      } else {
        echo "<div class='historique-box'>";
        echo "<h3>Historique des trajets</h3>";
        echo "<p>Aucun historique de trajets trouvé.</p>";
        echo "</div>";
      }
    }


    ?>
  </div>

</section>

<?php require("./include/footer.inc.php"); ?>