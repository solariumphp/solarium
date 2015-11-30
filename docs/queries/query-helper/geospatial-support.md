The query helper also includes support for geospatial query functions / filters. It can be hard to remember the exact syntax, in that case you can use the helper API to generate the syntax for you. You still need to place the result of the helper into a querystring yourself.

The following methods are available:

-   geofilt($field, $pointX, $pointY, $distance)
-   bbox($field, $pointX, $pointY, $distance)
-   geodist($field, $pointX, $pointY)

