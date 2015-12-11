<?php

/**
 * Scrambler Class
 * Used to encode and decode multimedia
 *
 * Encoding Method:
 * Uses a 2 digit key x and y respectively
 *
 *Assume file with n lines 1 through n
 *
 * for lines 1 and n
 *
 * if length of both lines are greater than x + y
 *
 * we will take first x characters and replace with last y characters
 *
 * then will swap lines 1 and n
 *
 * it does this for the first and last 10 lines
 *
 * NOTE: number of lines can be changed very easily
 *
 * See example @/src/exampleEncode.txt
 *
 */
class Scrambler
{

    //constants for encoding and decoding
    const IV = '1234567812345678';
    const PASS = '7a16bcd5209fef1a';
    const METHOD = 'aes-128-cbc';
    const PATH = '../media/';

    /**
     * Generates 2 random digits between 3 and 9
     * to be used as key
     *
     * @param $fName - fileName
     * @return string - Encrypted Key
     */
    public function generateKey($fName)
    {

        $key =  rand(3,9) . rand(3,9);

        return $this->encryptKey($key, $fName);

    }

    /**
     * form long key fileName + 2 digit key + current time
     * encrypt key with openssl using constants defined above
     *
     * @param $key - 2 digit scramble key
     * @param $fName - fileName
     * @return string - Encrypted Key
     */
    private function encryptKey($key, $fName)
    {

        $key = $fName .'?' . $key .'?' . time();

        return openssl_encrypt ($key, self::METHOD, self::PASS, true, self::IV);

    }

    /**
     * decrypt using openssl and constants defined above
     *
     *
     * @param $cipherKey
     * @param $file - fileName
     * @return bool - false on unsuccessful | 2 digit key
     */
    private function decrypt($cipherKey, $fName) {

        $key = openssl_decrypt($cipherKey, self::METHOD, self::PASS, true, self::IV);

        //explode on delimeter to grab fileName, realKey, time in an array
        $keyParts = explode('?', $key);

        //check to make sure key is for requested file
        if($fName != $keyParts[0]) {

            return false;

        }

        return $keyParts[1];

    }

    /**
     * Swaps first $d1 letters with last $d2 letters
     *
     * @param $d1
     * @param $d2
     * @param $line - line from file
     * @return string - scrambled line
     */
    private function scrambleLine($d1, $d2, $line )
    {
        //get lenght of line
        $len = strlen($line);

        //create and return line
        return  substr($line, $len - (1 + $d2), $d2 ) . substr($line, $d1, $len - (1 + $d1 + $d2)) . substr($line, 0, $d1) . "\n";

    }

    /**
     * unscrambles file and saves n appropriate location
     *
     * @param $fileName
     * @param $key - hexidecimal representation of encrypted key
     * @return bool - true on Success| false
     */
    public function unscramble($fileName, $key) {

        //get binary representation of key
        $key = hex2bin($key);

        //get Real Key 2 digit form
        $key = $this->decrypt($key, $fileName);

        //if key was not successfully decrypted return false
        if(!$key) {
            return false;
        }

        //unscramble file
        $str = $this->doSramble('scrambledMedia/' . $fileName, $key, true);

        $path = self::PATH . 'unscrambledMedia/' . $fileName;

        //save file
        file_put_contents( $path, $str);

        return true;
    }

    /**
     * scrable file
     *
     * @param $fileName
     * @param $key - encrypted key
     */
    public function scramble($fileName, $key) {

        //get 2 digit key
        $key = $this->decrypt($key, $fileName);

        //scramble file
        $str = $this->doSramble($fileName, $key);

        $path = self::PATH . 'scrambledMedia/' . $fileName;

        //save file
        file_put_contents( $path, $str);
    }


    /**
     * Perform scrambling algorithm - See top of file
     *
     * @param $fileName
     * @param $key - 2 digit key
     * @param bool|false $reverse - true for unscrambling
     * @return string - new file contents
     */
    private function doSramble( $fileName,$key, $reverse = false)
    {

        //extract ints from key
        $d1 = intval(substr( $key, $reverse, 1));
        $d2 = intval(substr( $key, !$reverse, 1 ));

        //turn file into an array
        $file = file(self::PATH . $fileName);

        $lastIndex = count($file) - 1;

        //append newline char to last line of file for consistency
        $file[$lastIndex] = $file[$lastIndex] . "\n";

        //Start line and number of lines
        $i =0;
        $x = $i+10;

        while($i < $x) {

            //check to make sure both lines are of proper lenght
            if (((strlen($file[$i]) - 1) > ($d1 + $d2)) && ((strlen($file[$lastIndex - $i]) - 1) > ($d1 + $d2)) ) {
                //(un)scramble lines in pair - line from top of file and its opposite line at the bottom of the file
                $line1 = $this->scrambleLine($d1, $d2, $file[$i]);
                $line2 = $this->scrambleLine($d1, $d2, $file[$lastIndex - $i]);

                //swap lines
                $file[$lastIndex - $i] = $line1;
                $file[$i] = $line2;

            } else {

                //if not proper length we just swap lines
                $temp = $file[$i];

                $file[$i] = $file[$lastIndex - $i];

                $file[$lastIndex - $i] = $temp;

                //iterate x so we still (un)scramble proper number of lines
                $x++;

            }

            $i++;
        }


        //chop newline form end that we added
        $file[$lastIndex] = substr( $file[$lastIndex], 0, strlen($file[$lastIndex])-1);

        //create string form line array
        return $str = implode("", $file);

    }
}