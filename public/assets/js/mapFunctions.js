// Déclaration de la variable map (accessible globalement)
let map = null; 

// Définir une icône personnalisée pour tous les players
const playersIcon = new L.Icon({
    iconUrl: '/images/players.png',
    iconSize: [28, 34],
    iconAnchor: [14, 34],
    popupAnchor: [0, -34]
  });

  // Définir une icône personnalisée pour l'utilisateur
const userIcon = new L.Icon({
    iconUrl: '/images/user.png', 
    iconSize: [28, 34],
    iconAnchor: [14, 34],
    popupAnchor: [0, -34]
  });

/**
 * Set up global map for France 
 */
function mapGlobal() {
    let lat = 46.630642;
    let lng = 2.4077227;

    // Vérifiez si map est défini avant de l'arrêter
    if (map) {
        map.off();
        map.remove();
    }

    // Créez une nouvelle carte
    const newMap = L.map('map', {
        center: [lat, lng],
        zoom: 6,
        minZoom: 5,
        maxZoom: 17,
        scrollWheelZoom: false
    });

    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(newMap);

    // Affectez la nouvelle carte à la variable globale
    map = newMap;

    return map; // Retournez la carte pour qu'elle puisse être utilisée ailleurs si nécessaire
}


/**
 * Set Up map around a given user
 *
 * @param {*} user 
 * @returns 
 */
function mapUser(user) {

    let lat = user.latitude;
    let lng = user.longitude;

    // Vérifiez si map est défini avant de l'arrêter
    if (map) {
        map.off();
        map.remove();
    }

    // Créez une nouvelle carte
    const newMap = L.map('map', {
        center: [lat, lng],
        zoom: 13,
        minZoom: 5,
        maxZoom: 17,
        scrollWheelZoom: false
    });

    // Ajoutez des tuiles à la carte
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(newMap);

    // Marqueur pour l'utilisateur
    const usermarker = L.marker([lat, lng], { icon: userIcon })
        .bindPopup('<div style="text-align: center;"><a href="/profil" style="text-decoration:none">Tu es ici !</a></div>')
        .addTo(newMap);

    return newMap;
}

/**
 * Set Up users for a given game 
 *
 * @param {*} map 
 * @param {*} user 
 * @param {*} gameId 
 */
function mapGameUsers(map, user, gameId) {
    axios.get('/api/users/game/' + gameId)
        .then(function (response) {
            // Vider le layerGroup avant d'ajouter de nouveaux utilisateurs
            usersLayer.clearLayers();

            response.data.forEach(element => {
                // marqueur pour les utilisateurs, sauf l'utilisateur actuel
                createMarkers(map, user, element, usersLayer);
            })
        })
        .catch(function (error) {
            // en cas d’échec de la requête
            console.log(error);
        });
}

/**
 * Set Up ALL users on the map
 *
 * @param {*} map 
 * @param {*} user 
 */
function mapAllUsers(map, user) {
    axios.get('/api/users/')
        .then(function (response) {
            // Vider le layerGroup avant d'ajouter de nouveaux utilisateurs
            usersLayer.clearLayers();

            response.data.forEach(element => {
                // marqueur pour les utilisateurs, sauf l'utilisateur actuel
                createMarkers(map, user, element, usersLayer);
            })
        })
        .catch(function (error) {
            // en cas d’échec de la requête
            console.log(error);
        });
}

/**
 * Créer des marqueurs pour les utilisateurs
 *
 * @param {*} map 
 * @param {*} user 
 * @param {*} element 
 * @param {*} layer 
 */
function createMarkers(map, user, element, layer) {
    if (!user) {
        // Aucun utilisateur
        if (element.latitude != null && element.longitude != null) {
            const marker = L.marker([element.latitude, element.longitude], { icon: playersIcon })
                .addTo(layer);
        }
    } else {
        // Utilisateur
        if (element.id !== user.id) {
            // Si l'utilisateur n'est pas l'utilisateur actuel
            if (element.latitude != null && element.longitude != null) {
                // Si l'utilisateur a des coordonnées
                const marker = L.marker([element.latitude, element.longitude], { icon: playersIcon })
                    .bindPopup('<div style="text-align: center;"><a href="/profil/joueur/' + element.id +'" style="text-decoration:none">' + element.alias + '</a></div>')
                layer.addLayer(marker);
            }
        }
    }
}


