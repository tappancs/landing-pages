<?php
// subscribe.php - Feliratkozás kezelése
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Betöltjük a PHPMailer-t
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 2. Bekért adatok
    $nev = htmlspecialchars($_POST['nev'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');

    // 3. Mentés CSV fájlba
    $file = fopen('submissions.csv', 'a');
    fputcsv($file, [$nev, $email, date('Y-m-d H:i:s')]);
    fclose($file);

    // 4. E-mail küldése
    $mail = new PHPMailer(true);

    try {
        // SMTP beállítások – EZEKET CSERÉLD KI SAJÁT ADATOKRA
        $mail->isSMTP();
        $mail->Host = 'smtp.mail.bokakrisztina.com'; // Pl. smtp.gmail.com
        $mail->SMTPAuth = true;
        $mail->Username = 'bokakrisztina1@gmail.com'; // Saját e-mail címed
        $mail->Password = 'jelszavad1';          // Saját jelszavad vagy alkalmazásjelszó
        $mail->SMTPSecure ='ssl';              // Vagy 'ssl'
        $mail->Port = 465;                      // 465 ha ssl-t használsz

        // Feladó és címzett
        $mail->setFrom('bokakrisztina1@gmail.com', 'Angol Online Nyelviskola');
        $mail->addAddress('bokakrisztina1@gmail.com'); // Ide kapod az értesítést

        // Üzenet tartalma
        $mail->isHTML(true);
        $mail->Subject = 'Új feliratkozó az angol nyelviskola landingoldalról';
        $mail->Body = "<strong>Név:</strong> {$nev}<br><strong>E-mail:</strong> {$email}";

        $mail->send();

        // Sikeres küldés után átirányítás
        header('Location: koszono.html');
        exit;

    } catch (Exception $e) {
        echo "Hiba történt: {$mail->ErrorInfo}";
    }
}
?>
