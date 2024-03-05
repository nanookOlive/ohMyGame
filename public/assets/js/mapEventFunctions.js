// Définir une icône personnalisée pour les événements
const eventIcon = new L.Icon({
    iconUrl: 'images/events.png',
    iconSize: [28, 34],
    iconAnchor: [14, 34],
    popupAnchor: [0, -34]
});

/**
 * Set Up ALL events on the map
 * 
 * Fonction pour charger tous les événements sur la carte
 * 
 * @param {*} map
 * @param {*} user
 */
function mapAllEvents(map, user) {
    // Faire une requête GET via Axios (bibliothèque JS) vers l'API Symfony (Api\EventController) pour récupérer la liste de tous les événements
 axios.get('/api/events')
        .then(response => {
            // Vider le layerGroup avant d'ajouter de nouveaux événements
            eventsLayer.clearLayers();

            // Récupérer les données (événements) de la réponse
            const events = response.data;

            // Itérer sur chaque event dans la liste events récupérée
            events.forEach(event => {
                
                let popupContent = '<span><strong>' + event.title + '</strong></span>';
                if (user) {
                    popupContent = '<a href="/evenement/' + event.id + '" style="text-decoration:none"><strong>' + event.title + '</strong></a><br/>Proposé par ' + event.host.alias;
                }

                // créer un marqueur Leaflet à partir des coordonnées de l'évènement 
                const eventmarker = L.marker([event.latitude, event.longitude], { icon: eventIcon })
                // lier le marqueur à une fenêtre avec des informations de l'événement
                .bindPopup(popupContent);
                    
                eventsLayer.addLayer(eventmarker);
            });
        })
        .catch(error => {
            console.error('Erreur pour la récupération des événements:', error);
        });
}