<?php

namespace App\Contracts;

use App\Services\Notifications\OneSignalSendResult;

interface OneSignalServiceInterface
{
    /**
     * @param  list<string>  $externalIds
     */
    public function sendToUsers(string $title, string $message, array $externalIds): OneSignalSendResult;

    public function sendToAll(string $title, string $message): OneSignalSendResult;
}
