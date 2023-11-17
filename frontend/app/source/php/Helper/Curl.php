<?php

namespace NavetSearch\Helper;

class Curl
{
    public $response = false;
    public $isValid = false;
    public $errorMessage = false;
    public $oAuth = false;

    public function __construct($type, $url, $data = null, $contentType = 'json', $headers = null)
    {
        //Arguments are stored here
        $arguments = null;
        
        switch (strtoupper($type)) {
            /**
             * Method: GET
             */
            case 'GET':
                // Append $data as querystring to $url
                if (is_array($data) && !empty($data)) {
                    $url .= '?' . http_build_query($data);
                }

                // Set curl options for GET
                $arguments = array(
                    CURLOPT_RETURNTRANSFER      => true,
                    CURLOPT_HEADER              => false,
                    CURLOPT_FOLLOWLOCATION      => true,
                    CURLOPT_SSL_VERIFYPEER      => true,
                    CURLOPT_SSL_VERIFYHOST      => true,
                    CURLOPT_URL                 => $url,
                    CURLOPT_CONNECTTIMEOUT_MS   => 2000,
                );

                break;

            /**
             * Method: POST
             */
            case 'POST':
                // Set curl options for POST
                $arguments = array(
                    CURLOPT_RETURNTRANSFER      => 1,
                    CURLOPT_URL                 => $url,
                    CURLOPT_POST                => 1,
                    CURLOPT_HEADER              => false,
                    CURLOPT_CONNECTTIMEOUT_MS   => 3000,
                    CURLOPT_REFERER             => ''
                );

                if (in_array($contentType, array("json", "jsonp"))) {
                    $arguments[CURLOPT_POSTFIELDS] = json_encode($data);
                } else {
                    $arguments[CURLOPT_POSTFIELDS] = http_build_query($data) ;
                }

                break;
        }

        /**
         * Set up headers if given
         */
        if ($headers && $headers = $this->convertHeaderArray($headers)) {
            $arguments[CURLOPT_HTTPHEADER] = $headers;
        }

        /**
         * Do the actual curl
         */
        $ch = curl_init();
        curl_setopt_array($ch, $arguments);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        /**
         * json to object
         */
        if($decodedJson = json_decode($response)) {
          $response = (object) $decodedJson; 
        } else {
          $response = false; 
        }

        if(is_object($response) && !empty($response)) {
            if(isset($response->errors) && $response->status != 200) {
                $this->isValid = false;
            } else {
                $this->isValid = true;
            }
        } else {
            $this->isValid = false;
        }

        /**
         * Debugging curl
         */
        if(isset($_GET['curldebug'])) {
            echo '<pre>'; 
                var_dump(array_merge((array) $response, ['httpCode' => $httpcode], $arguments)); 
            echo '</pre>'; 
            die("Curl debug done."); 
        }

        /**
         * Return the response
         */
        $this->response = $response;
    }

    private function convertHeaderArray($header) {
        if(is_array($header) && !empty($header)) {
            $result = array();
            foreach($header as $key => $value) {
                $result[] = $key . ": " .$value;
            }
            return $result;
        }
        return false;
    }
}