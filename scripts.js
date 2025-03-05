window.addEventListener("scroll", function () {
  var topNav = document.getElementById("top-nav");
  if (window.pageYOffset > 0) {
    topNav.classList.add("scrolled");
  } else {
    topNav.classList.remove("scrolled");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  var themeToggle = document.getElementById("theme");

  // Fonction pour définir le cookie de mode
  function setModeCookie(newMode) {
      document.cookie = "mode=" + newMode + "; path=/; max-age=2592000"; // Durée de vie de 30 jours
  }

  // Récupérer le mode actuel à partir du cookie
  var currentMode = document.cookie.replace(/(?:(?:^|.*;\s*)mode\s*\=\s*([^;]*).*$)|^.*$/, "$1");

  // Si aucun cookie de mode n'est trouvé, on défini le mode par défaut
  if (!currentMode) {
      currentMode = themeToggle.checked ? "Nuit" : "Jour";
      setModeCookie(currentMode); // Définir le cookie avec le mode par défaut
  }

  // Mettre à jour le bouton bascule en fonction du mode actuel
  themeToggle.checked = (currentMode === "Nuit");

  // Écouter les changements sur le bouton bascule
  themeToggle.addEventListener("change", function () {
      var newMode = themeToggle.checked ? "Nuit" : "Jour";
      setModeCookie(newMode); // Définir le cookie avec le nouveau mode
      window.location.href = window.location.pathname + "?mode=" + newMode; // Mettre à jour l'URL avec le nouveau mode
  });
});



function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}

let departAutocomplete, arriveAutocomplete;

function initAutocomplete() {
  const ileDeFranceBounds = new google.maps.LatLngBounds(
    new google.maps.LatLng(48.1251, 1.44617),
    new google.maps.LatLng(49.2415, 3.559)
  );

  const options = {
    types: ["address"], 
    bounds: ileDeFranceBounds,
    strictBounds: true,
  };

  departAutocomplete = new google.maps.places.Autocomplete(
    document.getElementById("depart"),
    options
  );
  arriveAutocomplete = new google.maps.places.Autocomplete(
    document.getElementById("arrive"),
    options
  );

  departAutocomplete.addListener("place_changed", function () {
    handlePlaceSelect(departAutocomplete, "depart");
  });
  arriveAutocomplete.addListener("place_changed", function () {
    handlePlaceSelect(arriveAutocomplete, "arrive");
  });
}

function handlePlaceSelect(autocomplete, elementId) {
  const place = autocomplete.getPlace();
  if (!place.geometry) {
    window.alert(
      "Aucune adresse valide trouvée pour l'entrée: '" + place.name + "'"
    );
    return;
  }
  if (place.address_components) {
    const address = place.formatted_address;
    document.getElementById(elementId).value = address;
  } else {
    window.alert("Aucune adresse détaillée disponible pour ce lieu.");
  }
}

function echangerLesValeurs() {
  var depart = document.getElementById("depart");
  var arrive = document.getElementById("arrive");
  var temp = depart.value;
  depart.value = arrive.value;
  arrive.value = temp;
}


function remplirFormulaire(depart, arrive, datetime) {
  document.getElementById('depart').value = depart;
  document.getElementById('arrive').value = arrive;
}