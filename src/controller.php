<?php
/**
 * controller
 * handles all requeets
 *
 * NOTE: All functions are called from AJAX
 * and send a response back to the broswer
 */

//require Scrambler class file
//we can now create objects and call function
require_once "scrambler.php";

/**
 * Is called when user enters product key
 * will validate product key and unscramble appropriate file
 *
 * @param $path
 * @return true on successful unscramble or false on unsuccessful
 */
function unlock($path) {

    //extract file name
    $file = substr($path, 1);

    //extract file extension
    $type = explode('.', $file)[1];

    //check if key was sent
    if(isset($_POST['key'])) {

        //create new Scrambler instance
        $scrambler = new Scrambler();

        //unscramble file and return true or false
        echo $scrambler->unscramble($file, $_POST['key']);
    }

}

/**
 * Called when a user purchases media
 * generates key and scrambles file
 *
 * @param $path
 * @return hexidecimal representation of key
 */
function purchase($path) {

    $file = substr($path, 1);

    $scrambler = new Scrambler();

    $key = $scrambler->generateKey($file);


    $scrambler->scramble($file, $key);

    echo json_encode(bin2hex($key));

}

/**
 * Called when a user trys to view media
 * only meaningful/useful after unlcok has been called on same file
 * will delete unscrambled file after sent to browser
 *
 * @param $path
 * @return  unscrambled file and content header
 */
function getUnscrambledMedia($path) {

    $file = explode('/',$path)[3];


    $type = explode('.', $file)[1];

    $filePath = '../media/unscrambledMedia/' . $file;

    switch($type) {
        case "mp3":
            header('Content-Type', 'audio/mpeg');
            readfile($filePath);
            break;
        case "mp4":
            header('Content-Type', 'video/mp4');
            readfile($filePath);
            break;
        case "jpg":
            header("content-type: image/your_image_type");
            readfile($filePath);
            break;

    }

    unlink($filePath);


}

/**
 * Called when a user trys to view  scrambled media
 * only meaningful/useful after purchase has been called on same file
 *
 * @param $path
 * @return  unscrambled file and content header
 */
function getScrambledMedia($path) {

    error_log($path);

    $file = explode('/',$path)[3];


    $type = explode('.', $file)[1];


    $filePath = '../media/scrambledMedia/' . $file;

    switch($type) {
        case "mp3":
            header('Content-Type', 'audio/mpeg');
            readfile($filePath);
            break;
        case "mp4":
            header('Content-Type', 'video/mp4');
            readfile($filePath);
            break;
        case "jpg":
            header("content-type: image/your_image_type");
            readfile($filePath);
            break;

    }

}