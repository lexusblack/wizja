<div>
    <?php
    use dosamigos\google\maps\LatLng;
    use dosamigos\google\maps\services\DirectionsWayPoint;
    use dosamigos\google\maps\services\TravelMode;
    use dosamigos\google\maps\overlays\PolylineOptions;
    use dosamigos\google\maps\services\DirectionsRenderer;
    use dosamigos\google\maps\services\DirectionsService;
    use dosamigos\google\maps\overlays\InfoWindow;
    use dosamigos\google\maps\overlays\Marker;
    use dosamigos\google\maps\Map;
    use dosamigos\google\maps\services\DirectionsRequest;
    use dosamigos\google\maps\overlays\Polygon;
    use dosamigos\google\maps\layers\BicyclingLayer;
    use dosamigos\google\maps\services\GeocodingClient;

    $geo = new GeocodingClient();
    $t1 = $geo->lookup([
        'address' => 'ul. Faradaya 1, 03-233 '.Yii::t('app', 'Warszawa'),
    ]);
    $start = new LatLng(
        $t1->results[0]->geometry->location
    );
    $t2 = $geo->lookup([
        'address' => $model->location->address,
        'region' => $model->location->city,
    ]);
    $end = new LatLng(
        $t2->results[0]->geometry->location
    );

    $coord = new LatLng(['lat' => 52.140992, 'lng' => 19.215291]);
    $map = new Map([
        'center' => $coord,
        'zoom' => 14,
    ]);


    // lets use the directions renderer
    //        $start = new LatLng(['lat' => 39.720991014764536, 'lng' => 2.911801719665541]);
    //        $end = new LatLng(['lat' => 39.719456079114956, 'lng' => 2.8979293346405166]);
    $santo_domingo = new LatLng(['lat' => 39.72118906848983, 'lng' => 2.907628202438368]);

    // setup just one waypoint (Google allows a max of 8)
    $waypoints = [
//        new DirectionsWayPoint(['location' => $santo_domingo])
    ];

    $directionsRequest = new DirectionsRequest([
        'origin' => $start,
        'destination' => $end,
        'waypoints' => $waypoints,
        'travelMode' => TravelMode::DRIVING
    ]);



    // Lets configure the polyline that renders the direction
    $polylineOptions = new PolylineOptions([
        'strokeColor' => '#FF0000',
        'draggable' => true
    ]);

    // Now the renderer
    $directionsRenderer = new DirectionsRenderer([
        'map' => $map->getName(),
        'polylineOptions' => $polylineOptions
    ]);

    // Finally the directions service
    $directionsService = new DirectionsService([
        'directionsRenderer' => $directionsRenderer,
        'directionsRequest' => $directionsRequest
    ]);

    // Thats it, append the resulting script to the map
    $map->appendScript($directionsService->getJs());

    // Lets show the BicyclingLayer :)
    //        $bikeLayer = new BicyclingLayer(['map' => $map->getName()]);

    // Append its resulting script
    //    $map->appendScript($bikeLayer->getJs());

    // Display the map -finally :)
    echo $map->display();
    ?>
</div>