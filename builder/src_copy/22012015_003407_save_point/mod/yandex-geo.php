<?php
/**
 * Делает запрос на сервис геолокации Яндекс и возвращает название города
 */

//http://geocode-maps.yandex.ru/1.x/?format=json&lang=RU_ru&kind=locality&geocode={X},{Y}
// где X и Y берутся из navigator.geolocation

// function success_callback(position){
//      соответственно X == position.coords.longitude и Y == position.coords.latitude
// }
// navigator.geolocation.getCurrentPosition(success_callback, error_callback, options)


/*
    if ("geolocation" in navigator) {
      // geolocation is available
    } else {
        // geolocation IS NOT available
    }
 */