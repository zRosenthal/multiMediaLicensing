<?php
/**
 *
 * index.php will serve as the ROUTER
 * it will direct request to the correct function
 */



//require controller to handle all the work
require_once "../src/controller.php";

//define a few regex
$pattern = '/^\/test.(mp3|jpg|mp4)\/?$/';

$pattern2 = '/^\/show\/s\/test.(mp3|jpg|mp4)\/?$/';

$pattern3 = '/^\/show\/u\/test.(mp3|jpg|mp4)\/?$/';

//get request path
$path = $_SERVER['REQUEST_URI'];

//get request method
$method = $_SERVER['REQUEST_METHOD'];

//ROUTING LOGIC
//route to proper controller function
if (preg_match($pattern, $path)) {

    if ($method == "POST") {

        unlock($path);

    } else if ($method == "GET") {

        purchase($path);

    }


} else if (preg_match($pattern2, $path)) {


    getScrambledMedia($path);


} else if (preg_match($pattern3, $path))  {

    getUnscrambledMedia($path);

} else {

    require "index.html";

}
