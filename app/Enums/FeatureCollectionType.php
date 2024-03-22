<?php

namespace App\Enums;

enum FeatureCollectionType: string
{
    case GeologyPoints = 'geology_points';
    case GeologyLines = 'geology_lines';
    case GeologyPolygons = 'geology_polygons';
    case GeomorfologyPoints = 'geomorfology_points';
    case GeomorfologyLines = 'geomorfology_lines';
    case GeomorfologyPolygons = 'geomorfology_polygons';
    case ResourceProspections = 'resource_prospections';

    public static function asSelectArray(): array
    {
        return [
            self::GeologyPoints->value => __(self::GeologyPoints->value),
            self::GeologyLines->value => __(self::GeologyLines->value),
            self::GeologyPolygons->value => __(self::GeologyPolygons->value),
            self::GeomorfologyPoints->value => __(self::GeomorfologyPoints->value),
            self::GeomorfologyLines->value => __(self::GeomorfologyLines->value),
            self::GeomorfologyPolygons->value => __(self::GeomorfologyPolygons->value),
            self::ResourceProspections->value => __(self::ResourceProspections->value),
        ];
    }
}
