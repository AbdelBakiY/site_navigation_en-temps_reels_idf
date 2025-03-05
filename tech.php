<?php $title = "Statistique des différents gares recherché" ?>
<?php



require("./include/header.inc.php");

incrementVisits();

?>



<article class="historique-box">   

<div id="nasa-img">
<h2>APOD (Astronomy Picture of The Day)</h2>
</div>

    <p>Image du jour :</p>
    <?php
    $current_date = date('Y-m-d');
    $hd="false";
    $apod_data = file_get_contents("https://api.nasa.gov/planetary/apod?api_key=I48RcjZfIP46M62LXyih73diomEmZESqg3aOqSW9&date={$current_date}&hd={$hd}");
    $apod_json = json_decode($apod_data);

    if (isset($apod_json->explanation)) {
        echo '<p style="text-align: justify;" lang="en">' . $apod_json->explanation . '</p>';
    }

    if ($apod_json->media_type === 'image') {
        echo '<figure><figcaption>l\'image du jour fournis par la nasa</figcaption><img id="apodImage" src="' . $apod_json->url . '" alt="APOD Image"/></figure>';
    } elseif ($apod_json->media_type === 'video') {
        echo '<video src="' . $apod_json->url . '" autoplay controls></video>';
    }
    ?>

</article>


<?php require("./include/footer.inc.php"); ?>