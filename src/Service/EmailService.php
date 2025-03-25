<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


final class EmailService
{
    private PHPMailer $mailer;
    public function __construct(
        private readonly string $emailUser,
        private readonly string $emailPassword,
        private readonly string $emailSmtp,
        private readonly int $emailPort, 
        
    ){
        $this->mailer = new PHPMailer(true);
    }

    /**
     * Méthode pour envoyer des emails
     * @param string $receiver paramètre qui va recevoir l'adresse du destinataire du mail
     * @param string $subject paramètre qui va recevoir l'objet du mail
     * @param string $body paramètre qui va recevoir le contenu du mail
     * @throws
     */
    public function sendEmail(string $receiver, string $subject, string $body){
        try {
            //Server setting
            $this->getEmailConfig();

            //Recipients
            $this->mailer->setFrom($this->emailUser, 'Mailer');
            $this->mailer->addAddress($receiver);     // Add a recipient

            // Content
            $this->mailer->isHTML(true);               // Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();
            echo 'Le message à bien été envoyé.';
        } catch (Exception $e) {
            echo "Le mail n'a pas été envoyé." . $this->mailer->ErrorInfo;
        }
    }

    public function getEmailConfig(): void
    {
        //Server setting
        $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $this->mailer->isSMTP();                                            // Send using SMTP
        $this->mailer->Host       = $this->emailSmtp;                        // Set the SMTP server to send through
        $this->mailer->SMTPAuth   = true;                                   // Enable SMTP authentication
        $this->mailer->Username   = $this->emailUser;                       // SMTP username
        $this->mailer->Password   = $this->emailPassword;                   // SMTP password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        //Port 587 :
        //$this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;             // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->mailer->Port       = $this->emailPort;                       // TCP port to connect to
    }
}
