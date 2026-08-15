<?php

declare(strict_types=1);

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email 
{
    protected string $email;
    protected string $nombre;
    protected string $apellido;
    protected string $token;

    public function __construct(string $email, string $nombre, string $apellido, string $token)
    {
        $this->email = $email;
        $this->nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $this->apellido = htmlspecialchars($apellido, ENT_QUOTES, 'UTF-8');
        $this->token = $token;
    }

    /**
     * Instancia y configura PHPMailer mediante variables de entorno.
     */
    private function crearTransporte(): PHPMailer
    {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'] ?? '';
        $mail->SMTPAuth = true;
        $mail->Port = (int) ($_ENV['MAIL_PORT'] ?? 587);
        $mail->Username = $_ENV['MAIL_USER'] ?? '';
        $mail->Password = $_ENV['MAIL_PASS'] ?? '';

        // Configura cifrado TLS/SSL según el proveedor (ej: 'tls' o 'ssl')
        if (!empty($_ENV['MAIL_ENCRYPTION'])) {
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        }

        $mail->setFrom(
            $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@mibiblioteca.com', 
            $_ENV['MAIL_FROM_NAME'] ?? 'Mi Biblioteca'
        );
        
        // Destinatario real
        $mail->addAddress($this->email, "{$this->nombre} {$this->apellido}");

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

    public function enviarConfirmacion(): bool
    {   
        try {
            $mail = $this->crearTransporte();
            $mail->Subject = 'Confirma tu Cuenta';

            $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:3000';
            $urlConfirmacion = "{$baseUrl}/confirmar?token={$this->token}";

            $contenido = '<html>';
            $contenido .= "<p><strong>Hola {$this->nombre}</strong>, has creado tu cuenta en Mi Biblioteca. Solo debes confirmarla en el siguiente enlace:</p>";
            $contenido .= "<p><a href='{$urlConfirmacion}'>Confirmar Cuenta</a></p>";
            $contenido .= "<p>Si tú no creaste esta cuenta, puedes ignorar este mensaje.</p>";
            $contenido .= '</html>';

            $mail->Body = $contenido;

            return $mail->send();
        } catch (Exception $e) {
            // error_log($e->getMessage());
            return false;
        }
    }

    public function enviarInstrucciones(): bool
    {
        try {
            $mail = $this->crearTransporte();
            $mail->Subject = 'Restablece tu Password';

            $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:3000';
            $urlRestablecer = "{$baseUrl}/reestablecer?token={$this->token}";

            $contenido = '<html>';
            $contenido .= "<p><strong>Hola {$this->nombre}</strong>, parece que has olvidado tu password. Sigue el siguiente enlace para recuperarlo:</p>";
            $contenido .= "<p><a href='{$urlRestablecer}'>Restablecer Password</a></p>";
            $contenido .= "<p>Si tú no solicitaste este cambio, puedes ignorar este mensaje.</p>";
            $contenido .= '</html>';

            $mail->Body = $contenido;

            return $mail->send();
        } catch (Exception $e) {
            // error_log($e->getMessage());
            return false;
        }
    }
}