<?php

namespace App\Service;

class Captcha 
{
    /**
     * ReCAPTCHA decode functionnality
     * 
     */
    public function captchaCheck()
    {
        $secret = "6LfXKNYUAAAAAOvOl0Zg1Bqg-sB9ZzVls-79uPyi";
        $response = $_POST['g-recaptcha-response'];
        $remoteip = $_SERVER['REMOTE_ADDR'];

        $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
            . $secret
            . "&response=" . $response
            . "&remoteip=" . $remoteip;

        $decode = json_decode(file_get_contents($api_url), true);

        return $decode;
    }
}