<?php

namespace App\Service;

final class EmailService
{
    public function __construct(
        Private readonly string $emailUser,
        Private readonly string $emailPassword,
        Private readonly string $emailSmtp,
        Private readonly int $emailPort
    ) {}

    public function getEmailConfig(): string
    {
        return "Username: {$this->emailUser}, Password: {$this->emailPassword}, SMTP: {$this->emailSmtp}, Port: {$this->emailPort}";
    }
}
