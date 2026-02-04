<?php

namespace App\Session;

use Illuminate\Session\DatabaseSessionHandler as BaseDatabaseSessionHandler;

/**
 * Custom Database Session Handler
 * 
 * Override default behavior untuk tidak menyimpan user_agent
 * karena tidak diperlukan untuk sistem ini
 */
class DatabaseSessionHandler extends BaseDatabaseSessionHandler
{
    /**
     * Get the default payload for the session.
     *
     * @param  string  $data
     * @return array
     */
    protected function getDefaultPayload($data)
    {
        $payload = [
            'payload' => base64_encode($data),
            'last_activity' => $this->currentTime(),
        ];

        if (! $this->container) {
            return $payload;
        }

        return tap($payload, function (&$payload) {
            $this->addUserInformation($payload);
            $this->addRequestInformation($payload);
        });
    }

    /**
     * Add the request information to the session payload.
     *
     * @param  array  $payload
     * @return void
     */
    protected function addRequestInformation(&$payload)
    {
        if ($this->container->bound('request')) {
            $payload['ip_address'] = $this->ipAddress();
            // Removed user_agent tracking
        }
    }
}
