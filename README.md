# PHP static maps url generator
The package is used for static maps (images) URL generate. 

For now, it works only with maptiler.com. I welcome your commits to add new providers (such as Openstreet maps, Mapbox, Google.Maps, Yandex.Maps, Bing.Maps, 2GIS, and others)

## Quickstart
Add package to your project via Composer:
`composer require surzhikov/static-map-url-generator`

#### Centered map example:
<img src="https://user-images.githubusercontent.com/7311895/188896878-c81763a0-8b8c-41c0-90f6-f713396db1f5.png" width="250" >

```php
<?php
use \Surzhikov\StaticMapUrlGenerator\Map;

$map = Map::provider('maptiler')
  ->apiKey('XXXXXXXXXXXXXXXXXXXX') // Your MapTiler token
  ->width(250) // Width of resulted image
  ->height(210) // Height of resulted image
  ->retina(true) // For retina screen devices
  ->mapStyle('basic-v2') // Map style 
  ->centered(32.413403, 34.765209, 12) // Center map on Lng/Lat, zoom
  ->url(); // Get URL for static map
```
```
Will return:
https://api.maptiler.com/maps/basic-v2/static/32.413403,34.765209,12/250x210@2x.png?key=[TOKEN_IS_HIDDEN]&attribution=1
```

#### Bounded map example
<img src="https://user-images.githubusercontent.com/7311895/188898474-8a963085-6752-4b39-ae8a-936a90f180fe.png" width="250">

```php
<?php
use \Surzhikov\StaticMapUrlGenerator\Map;

$map = Map::provider('maptiler')
  ->apiKey('XXXXXXXXXXXXXXXXXXXX') // Your MapTiler token
  ->width(250) // Width of resulted image
  ->height(210) // Height of resulted image
  ->retina(true) // For retina screen devices
  ->mapStyle('streets-v2') // Map style 
  ->bounds(11, 51, 14, 54) // Bounds: minLng, minLat, maxLng, maxLat
  ->url(); // Get URL for static map
```

```
Will return:
https://api.maptiler.com/maps/streets-v2/static/11,51,14,54/250x210@2x.png?key=[TOKEN_IS_HIDDEN]&attribution=1
```

#### Auto-fit map with polylines / markers example

<img src="https://user-images.githubusercontent.com/7311895/188896970-32701cd5-1378-47f4-be28-df9d4b25ecda.png" width="250">

```php
<?php
use \Surzhikov\StaticMapUrlGenerator\Map;
$polygon1 = [[-59.83703613, -3.23649764], [-59.81506347, -3.10212100], [-59.83154296, -3.05001113], [-59.89746093, -2.99789874], [-59.94689941, -2.95675562], [-59.90020751, -2.86349227], [-59.86450195, -2.78942477], [-59.95239257, -2.75376097], [-60.00457763, -2.87446482], [-59.99633789, -2.82508749], [-60.02105712, -2.70986560], [-60.08148193, -2.71260911], [-60.08148193, -2.84703323], [-60.08697509, -2.91286794], [-60.40008544, -2.97047016], [-60.55938720, -3.14600100], [-60.55664062, -3.39279086], [-60.29571533, -3.77655930], [-59.96063232, -3.44488305], [-59.83703613, -3.23649764]];

$polygon2 = [[-60.72418212, -2.44920493], [-60.66375732, -2.27906185], [-60.63629150, -2.11713280], [-60.77087402, -2.00459579], [-60.93292236, -2.00734069], [-61.06201171, -2.08694073], [-61.06201171, -2.37785715], [-60.79559326, -2.57817004], [-60.72418212, -2.44920493]];

$poligon1StrokeColor = 'rgba(0,0,255,0.5)'; // Color can be set as rgba()
$poligon2StrokeColor = urlencode('#00ff00'); // Or as urlencoded #hex
$poligon1FillColor = 'rgba(0,0,255,0.2)'; 
$poligon2FillColor = 'rgba(0,255,0,0.5)'; 

$map = Map::provider('maptiler')
  ->apiKey('XXXXXXXXXXXXXXXXXXXX') // Your MapTiler token
  ->width(500) // Width of resulted image
  ->height(350) // Height of resulted image
  ->retina(false) // For retina screen devices
  ->mapStyle('streets-v2') // Map style 
  ->autoFit() // Set map position to auto-fit
  ->addPolyline($polygon1, 2, $poligon1StrokeColor, $poligon1FillColor, true)
  ->addPolyline($polygon2, 2, $poligon2StrokeColor, $poligon2FillColor, true)
  ->addMarker([-60.70495605, -2.8003989], 'red')
  ->url(); // Get URL for static map
```

```
Will return:
https://api.maptiler.com/maps/streets-v2/static/auto/500x350.png?key=[TOKEN_IS_HIDDEN]&path=stroke:rgba(0,0,255,0.5)|width:2|fill:rgba(0,0,255,0.2)|enc:bcwRn|elJ{fYkhCudI~eBudI~zKc`G~sH}eQybH}mMe~E{}EhdPjqVdeIqsHor@coUnyCbPrxJbgY?nzKja@~fJ|c|@`ha@rb^leo@ePp}iAw}q@_x_Aim`Akug@mcW&path=stroke:%2300ff00|width:2|fill:rgba(0,255,0,0.5)|enc:nj}MbesqJkf`@sxJas^ujDi~TbhYbPxs^npNxeXfyw@?|bf@c`s@aeXi}L&markers=-60.70495605,-2.8003989,red&attribution=1
```

#### Marker params
```php
addMarker($point, $color, $anchor, $icon, $scale);
```
- point – Point for marker [lng, lat].
- color – Color of polyline stroke. Example: 'rgba(0,0,255,0.2)' or urlencode('#EF00FF')
- anchor – Marker anchor (top, left, bottom, right, center, topleft, bottomleft, topright, bottomright), default - bottom
- icon – URL to a remote image (URL-encoded)
- scale – the scale of the image

#### Polyline params
```php
addPolyline($points, $strokeWidth, $strokeColor, $fillColor, $asEnc);
```
- points – array of points, like [[lng, lat], [lng, lat], [lng, lat], [lng, lat]].
- strokeWidth – width of polyline stroke.
- strokeColor – color of polyline stroke. Example: 'rgba(0,0,255,0.2)' or urlencode('#EF00FF')
- fillColor - color of polyline fill.
- asEnc – ture/false – use Google Polyline encoding format (default: true).
