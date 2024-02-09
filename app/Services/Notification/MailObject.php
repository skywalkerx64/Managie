<?php

namespace App\Services\Notification;

use App\Models\User;

class MailObject
{
    public function __construct(
        public string $subject = "",
        public string $title = "",
        public string $preheeader = "",
        public string $intro = "",
        public string $corpus = "",
        public string $outro = "",
        public string $template = "emails.default",
        public array $data = [],
        public array $files = []
        )
    {
        
    }
}
