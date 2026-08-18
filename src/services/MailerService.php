<?php
// src/services/MailerService.php

namespace services;

// 1. Load Composer Autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class MailerService 
{
    private $mailer;

    public function __construct() 
    {
        // 2. Automatically load the root .env file
        if (class_exists(Dotenv::class) && file_exists(__DIR__ . '/../../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();
        }

        $this->mailer = new PHPMailer(true);

        // Server Settings from your .env file
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USERNAME'] ?? '';
        $this->mailer->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $_ENV['MAIL_PORT'] ?? 587;

        // Bypass SSL Certificate verification for localhost (XAMPP fix)
        $this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );

        // Default Sender
        $fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? ($_ENV['MAIL_USERNAME'] ?? 'no-reply@nbsc.edu.ph');
        $fromName  = $_ENV['MAIL_FROM_NAME'] ?? 'NBSC OJT Portal';
        $this->mailer->setFrom($fromEmail, $fromName);
        $this->mailer->isHTML(true);
    }

    /**
     * Send Welcome/Invitation Email to Newly Created User
     */
    public function sendWelcomeEmail($recipientEmail, $recipientName, $role) 
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($recipientEmail, $recipientName);

            $this->mailer->Subject = "Welcome to NBSC OJT Management Portal";
            $roleLabel = ($role === 'student') ? 'Student Intern' : 'Industry Supervisor';

            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; color: #1e293b;'>
                    <h2 style='color: #0F2854; margin-top: 0;'>Welcome to NBSC OJT Portal</h2>
                    <p>Hello <strong>" . htmlspecialchars($recipientName) . "</strong>,</p>
                    <p>An account has been pre-registered for you as a <strong>{$roleLabel}</strong> on the NBSC OJT Portal.</p>
                    <div style='background-color: #f8fafc; padding: 16px; border-radius: 8px; margin: 20px 0;'>
                        <p style='margin: 0; font-size: 14px;'><strong>Registered Email:</strong> " . htmlspecialchars($recipientEmail) . "</p>
                    </div>
                    <p>You can now sign in using your registered email address via Google Sign-In.</p>
                    <p style='margin-top: 24px;'><a href='http://localhost/ICS-PORTAL/auth/login.php' style='background-color: #0F2854; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>Access Portal</a></p>
                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin-top: 32px;'>
                    <p style='font-size: 11px; color: #94a3b8; text-align: center;'>Northern Bukidnon State College - Institute for Computer Studies</p>
                </div>
            ";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Send Password Reset / Change Email with One-Time Link
     */
    public function sendPasswordResetEmail($recipientEmail, $recipientName, $resetLink, $expiresMinutes = 15) 
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($recipientEmail, $recipientName);

            $this->mailer->Subject = "Password Change Request — NBSC OJT Portal";

            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; color: #1e293b;'>
                    
                    <!-- Header Bar -->
                    <div style='background-color: #0F2854; padding: 20px 24px;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 16px; letter-spacing: 0.5px;'>🔒 Password Change Request</h2>
                    </div>
                    
                    <div style='padding: 24px;'>
                        <p style='margin-top: 0;'>Hello <strong>" . htmlspecialchars($recipientName) . "</strong>,</p>
                        <p>We received a request to change the password for your NBSC OJT Portal account. Click the button below to set a new password:</p>
                        
                        <!-- CTA Button -->
                        <div style='text-align: center; margin: 28px 0;'>
                            <a href='" . htmlspecialchars($resetLink) . "' style='background-color: #0F2854; color: #ffffff; padding: 12px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 14px;'>Change My Password</a>
                        </div>

                        <!-- Security Info Box -->
                        <div style='background-color: #FFFBEB; border: 1px solid #FDE68A; padding: 14px 16px; border-radius: 8px; margin: 20px 0;'>
                            <p style='margin: 0 0 6px; font-size: 13px; font-weight: bold; color: #92400E;'>⏱ This link expires in {$expiresMinutes} minutes</p>
                            <p style='margin: 0; font-size: 12px; color: #92400E;'>If you did not request this change, you can safely ignore this email. Your password will remain unchanged.</p>
                        </div>

                        <p style='font-size: 12px; color: #64748b; margin-bottom: 0;'>If the button doesn't work, copy and paste this link into your browser:</p>
                        <p style='font-size: 11px; color: #94a3b8; word-break: break-all;'>" . htmlspecialchars($resetLink) . "</p>
                    </div>

                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 0;'>
                    <p style='font-size: 11px; color: #94a3b8; text-align: center; padding: 16px 24px; margin: 0;'>Northern Bukidnon State College — Institute for Computer Studies</p>
                </div>
            ";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Mailer Error (Password Reset): " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}