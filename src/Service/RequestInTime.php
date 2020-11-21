<?php

namespace App\Service;

class RequestInTime
{
    /**
     * Check the delay interval to validate the response
     */
    public function isRequestInTime(\Datetime $tokenRequestAt = null)
    {
        if ($tokenRequestAt === null) {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $tokenRequestAt->getTimestamp();
        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $response = true;

        return $response;
    }
}
