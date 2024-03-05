// Déclaration de la variable osm (couche tuile)
const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
});

// Déclaration de la variable osmHOT (couche tuile)
const osmHOT = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors, Tiles style by Humanitarian OpenStreetMap Team hosted by OpenStreetMap France'
});

// Déclaration des couches d'utilisateurs et d'événements (usersLayer et eventsLayer utilisées pour regrouper les marqueurs associés) 
let usersLayer = L.layerGroup(),
eventsLayer = L.layerGroup();

function displayUsersAndEvents(map, user) {
    
    mapAllUsers(map, user);
    usersLayer.addTo(map);
    mapAllEvents(map, user);
    eventsLayer.addTo(map);
}

    axios.get('/api/user')
    .then(function (response) {
        let user = response.data;

        if (user.latitude != null && user.longitude != null) {
            map = mapUser(user);
            
            // Ajoutez des écouteurs d'événements pour les boutons
            if (document.getElementById('usersButton')){
                document.getElementById('usersButton').addEventListener('click', function () {
                    if (this.getAttribute('aria-pressed') === 'true') {
                        this.setAttribute('aria-pressed', 'false');
                        this.classList.remove('btn-outline-primary')
                        this.classList.add('btn-primary');
                        map.addLayer(usersLayer);
                    } else {
                        this.setAttribute('aria-pressed', 'true');
                        this.classList.remove('btn-primary')
                        this.classList.add('btn-outline-primary');
                        map.removeLayer(usersLayer);
                    }

                });
            }

            if (document.getElementById('eventsButton')){
                document.getElementById('eventsButton').addEventListener('click', function () {
                    if (this.getAttribute('aria-pressed') === 'true') {
                        this.setAttribute('aria-pressed', 'false');
                        this.classList.remove('btn-outline-danger')
                        this.classList.add('btn-danger');
                        map.addLayer(eventsLayer);
                    } else {
                        this.setAttribute('aria-pressed', 'true');
                        this.classList.remove('btn-danger')
                        this.classList.add('btn-outline-danger');
                        map.removeLayer(eventsLayer);
                    }
            
                });
            }
        } else {
        // user no coords
            map = mapGlobal();
        }

        // Couches de markers selon la page
        if (document.getElementById('gameId')) {
            let gameId = document.getElementById('gameId').value;
            map.addLayer(usersLayer);
            mapGameUsers(map, user, gameId);
        } else {
            displayUsersAndEvents(map, user);
        }

        let baseMaps = {
            "OpenStreetMap": osm,
            "OpenStreetMap.HOT": osmHOT
        };

        let overlayMaps = {
            "Joueurs": usersLayer,
            "Évènements": eventsLayer
        };

        // Créer le contrôle des couches
        let layerControl = L.control.layers(baseMaps, overlayMaps).addTo(map);
    })
    .catch(function (error) {

        let user = null;
        map = mapGlobal();

        if (document.getElementById('gameId')) {
            let gameId = document.getElementById('gameId').value;
            mapGameUsers(map, user, gameId);
            usersLayer.addTo(map);
        } else {
            displayUsersAndEvents(map, user);
        }

    console.log(error);
});
    