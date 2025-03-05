# 🚇 Site de Navigation en Temps Réel - Île-de-France

Ce projet permet de **naviguer en temps réel** dans la région **Île-de-France** en utilisant des **API de transport public** pour afficher les itinéraires et les informations de trajet.  

⚠️ **Actuellement, l'API Navitia est devenue payante, donc la navigation en temps réel ne fonctionne plus. Cependant, la logique de manipulation des API, l'affichage des cartes, et l'algorithme de calcul de chemin restent fonctionnels.**

---

## 📌 Fonctionnalités

- 🔍 Recherche d'itinéraires entre deux points
- 🗺️ Affichage des trajets sur **Google Maps**
- ⚡ Calcul du chemin le plus court avec l'algorithme de **Prim**
- 💪 Manipulation des API (Navitia, Google Maps)
- 🎯 Interface utilisateur dynamique avec **HTML, CSS, JS**
- 🍪 Gestion des sessions et cookies (PHP)  

---

## 🛠️ Technologies utilisées

| Technologie      | Description                  |
|----------------|-----------------------------|
| **Frontend**   | HTML, CSS, JavaScript      |
| **Backend**    | PHP                        |
| **API**        | Google Maps API, Navitia API (désactivée) |
| **BDD**        | MySQL (si besoin pour stocker les utilisateurs) |

---

## 🚀 Installation

### Prérequis

- **Serveur local** (XAMPP, WAMP, LAMP)
- **PHP 7+**
- **MySQL**  

---

### Étapes

1. **Cloner le projet** :
```bash
git clone https://github.com/AbdelBakiY/site_navigation_en-temps_reels_idf
cd site_navigation_en-temps_reels_idf
