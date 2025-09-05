<?php
// subscribe.php - Feliratkozás kezelése
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Betöltjük a PHPMailer-t
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// 2. Betöltjük az .env fájlt
require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 2. Bekért adatok
    $nev = htmlspecialchars($_POST['nev'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');

   // ✅ Email formátum ellenőrzés – később aktiválható
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Érvénytelen e-mail cím!";
        exit;
    }
    

   // 3. Mentés CSV fájlba
    $file = fopen('submissions.csv', 'a');
    fputcsv($file, [$nev, $email, date('Y-m-d H:i:s')]);
    fclose($file);

    // 4. E-mail küldése
    $mail = new PHPMailer(true);

    try {

        // ✅ Karakterkódolás beállítása UTF-8-ra
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // SMTP beállítások – EZEKET CSERÉLD KI SAJÁT ADATOKRA
        $mail->isSMTP();
        $mail->Host =  $_ENV['SMTP_HOST']; // Pl. smtp.gmail.com
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER']; // Saját e-mail címed
        $mail->Password = $_ENV['SMTP_PASS']; // Saját jelszavad vagy alkalmazásjelszó
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Vagy 'ssl'
        $mail->Port = 465; // 465 ha ssl-t használsz

        // Feladó és címzett
        $mail->setFrom($_ENV['SMTP_USER'], 'Angol Online Nyelviskola');
        $mail->addAddress('bokakrisztina1@gmail.com'); // Ide kapod az értesítést

        // Üzenet tartalma
        $mail->isHTML(true);
        $mail->Subject = 'Új feliratkozó az angol nyelviskola landingoldalról';
        $mail->Body = "<strong>Név:</strong> {$nev}<br><strong>E-mail:</strong> {$email}";

        $mail->send();

        // Sikeres küldés után átirányítás
        header('Location: /Angol-online-nyelviskola/koszono-oldal/index.html');
        exit;

    } catch (Exception $e) {
        echo "Hiba történt: {$mail->ErrorInfo}";
    }

} else {
    header("Location: /Angol-online-nyelviskola/404error/index.html");
    exit;
}