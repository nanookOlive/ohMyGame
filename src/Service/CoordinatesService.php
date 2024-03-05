<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CoordinatesService
{
    public function __construct(
        private HttpClientInterface $client
    ) {
    }

    /**
     * Retourne les coordonnés GPS d'une adresse
     * ex : 1 rue Ameline Nantes
     *
     * @param string $address
     * @return array|null $coordinates[longitude, latitude]
     */
    public function getCoordinates(string $address)
    {
        $response = $this->client->request(
            'GET',
            'https://api-adresse.data.gouv.fr/search/?q=' . urlencode($address)
        );
        $content = $response->toArray();

        $coordinates = [];
        if (!empty($content['features'][0])) {
            $jsonCoordinates = $content['features'][0]['geometry']['coordinates'];

            $coordinates['longitude'] = $jsonCoordinates[0];
            $coordinates['latitude'] = $jsonCoordinates[1];

            return $coordinates;
        }

        return null;
    }
}
