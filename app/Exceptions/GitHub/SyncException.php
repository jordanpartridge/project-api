<?php

namespace App\Exceptions\GitHub;

use Exception;
use Throwable;

class SyncException extends Exception
{
    protected $syncDetails;

    public function __construct(
        string $message = "", 
        array $syncDetails = [], 
        int $code = 0, 
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->syncDetails = $syncDetails;
    }

    public function getSyncDetails(): array
    {
        return $this->syncDetails;
    }

    public function getDetailedMessage(): string
    {
        $details = $this->getSyncDetails();
        $message = $this->getMessage();

        $detailedMessage = "GitHub Sync Error\n";
        $detailedMessage .= "Message: {$message}\n";

        foreach ($details as $key => $value) {
            $detailedMessage .= "{$key}: {$value}\n";
        }

        return $detailedMessage;
    }
}
