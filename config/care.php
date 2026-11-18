<?php

$geofence = env('CARE_GEOFENCE_METERS');

return [
    /*
    | Metros máximos entre la ubicación enviada y el domicilio del turno.
    | Null desactiva la geocerca. No hay un máximo por defecto.
    */
    'geofence_meters' => $geofence === null || $geofence === '' ? null : (float) $geofence,
];
