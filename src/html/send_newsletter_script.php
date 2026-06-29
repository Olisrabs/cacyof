<?php
/**
 * send_newsletter_script.php
 * Handles newsletter dispatch.
 *
 * ── SMTP CONFIG ─────────────────────────────────────────────
 * Fill in your SMTP credentials below.
 * If you have PHPMailer installed via Composer (vendor/autoload.php),
 * set USE_PHPMAILER = true. Otherwise PHP mail() is used.
 * ────────────────────────────────────────────────────────────
 */
define('USE_PHPMAILER', false);           // true = PHPMailer, false = PHP mail()
define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USER',     'your@email.com'); // ← change
define('SMTP_PASS',     'your_password');  // ← change
define('FROM_EMAIL',    'your@email.com'); // ← change
define('FROM_NAME',     'CACYOF BOUESTI');

error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
session_start();
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}

// ── Input ──────────────────────────────────────────────────
$medium      = trim($_POST['medium']      ?? 'email');  // email | sms | whatsapp
$type        = trim($_POST['type']        ?? '');
$recipients  = trim($_POST['recipients']  ?? '');
$message     = trim($_POST['message']     ?? '');       // SMS / WhatsApp message
$subject     = trim($_POST['subject']     ?? '');
$salutation  = trim($_POST['salutation']  ?? '');
$heading     = trim($_POST['heading']     ?? '');
$body        = trim($_POST['body']        ?? '');
$event_date  = trim($_POST['event_date']  ?? '');
$event_venue = trim($_POST['event_venue'] ?? '');
$closing     = trim($_POST['closing']     ?? '');
$sender      = trim($_POST['sender']      ?? 'CACYOF BOUESTI Secretariat');

if (!$recipients) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a recipient group.']);
    exit();
}

// Medium-specific validation
if ($medium === 'email') {
    if (!$subject || !$heading || !$body) {
        echo json_encode(['status' => 'error', 'message' => 'Subject, Heading, and Body are required for email.']);
        exit();
    }
} else {
    if (!$message) {
        echo json_encode(['status' => 'error', 'message' => 'Message is required for ' . ucfirst($medium) . '.']);
        exit();
    }
}

// ── Fetch email addresses ───────────────────────────────────
$emails = [];

switch ($recipients) {
    case 'members':
        // Active workers + newsletter subscribers, deduplicated
        $r = $conn->query("SELECT email FROM workers WHERE status = 'active' AND email != ''");
        while ($row = $r->fetch_assoc()) $emails[] = strtolower(trim($row['email']));

        // Check if newsletter table exists before querying
        $chk = $conn->query("SHOW TABLES LIKE 'newsletter'");
        if ($chk && $chk->num_rows > 0) {
            $r2 = $conn->query("SELECT email FROM newsletter WHERE email != ''");
            while ($row = $r2->fetch_assoc()) $emails[] = strtolower(trim($row['email']));
        }
        break;

    case 'workers':
        $r = $conn->query("SELECT email FROM workers WHERE status = 'active' AND email != ''");
        while ($row = $r->fetch_assoc()) $emails[] = strtolower(trim($row['email']));
        break;

    case 'executives':
        $r = $conn->query("SELECT email FROM workers WHERE is_executive = 'yes' AND status = 'active' AND email != ''");
        while ($row = $r->fetch_assoc()) $emails[] = strtolower(trim($row['email']));
        break;

    case 'alumni':
        $r = $conn->query("SELECT email FROM workers WHERE status = 'alumni' AND email != ''");
        while ($row = $r->fetch_assoc()) $emails[] = strtolower(trim($row['email']));
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown recipient group.']);
        exit();
}

// ── SMS / WhatsApp branch ─────────────────────────────────
if ($medium === 'sms' || $medium === 'whatsapp') {
    // Fetch phone numbers from the workers table
    $phones = [];
    $phoneCol = 'phone'; // adjust column name if different

    switch ($recipients) {
        case 'members':
            $r = $conn->query("SELECT {$phoneCol} FROM workers WHERE status = 'active' AND {$phoneCol} != ''");
            while ($row = $r->fetch_assoc()) $phones[] = trim($row[$phoneCol]);
            break;
        case 'workers':
            $r = $conn->query("SELECT {$phoneCol} FROM workers WHERE status = 'active' AND {$phoneCol} != ''");
            while ($row = $r->fetch_assoc()) $phones[] = trim($row[$phoneCol]);
            break;
        case 'executives':
            $r = $conn->query("SELECT {$phoneCol} FROM workers WHERE is_executive = 'yes' AND status = 'active' AND {$phoneCol} != ''");
            while ($row = $r->fetch_assoc()) $phones[] = trim($row[$phoneCol]);
            break;
        case 'alumni':
            $r = $conn->query("SELECT {$phoneCol} FROM workers WHERE status = 'alumni' AND {$phoneCol} != ''");
            while ($row = $r->fetch_assoc()) $phones[] = trim($row[$phoneCol]);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown recipient group.']);
            exit();
    }

    $phones = array_unique(array_filter($phones));
    $total  = count($phones);

    if ($total === 0) {
        echo json_encode(['status' => 'error', 'message' => 'No phone numbers found for the selected group.']);
        exit();
    }

    /**
     * ── INTEGRATION POINT ──────────────────────────────────────
     * Replace the stub below with your real SMS / WhatsApp API.
     *
     * Popular options:
     *   SMS       → Africa's Talking, Twilio, Termii, etc.
     *   WhatsApp  → Twilio WhatsApp API, WA Business API, etc.
     *
     * Example (Twilio SMS):
     *   $twilio = new Twilio\Rest\Client($accountSid, $authToken);
     *   $twilio->messages->create($phone, ['from' => $from, 'body' => $message]);
     * ──────────────────────────────────────────────────────────
     */
    $sent   = 0;
    $failed = 0;
    foreach ($phones as $phone) {
        // TODO: replace with real API call
        // Stub: assume success for now
        $sent++;
    }

    $mediumLabel = $medium === 'whatsapp' ? 'WhatsApp' : 'SMS';
    $msg = "{$mediumLabel} newsletter queued for {$sent} of {$total} recipient(s).";
    if ($failed > 0) $msg .= " {$failed} number(s) were skipped.";

    echo json_encode([
        'status'  => $sent > 0 ? 'success' : 'error',
        'message' => $msg,
        'sent'    => $sent,
        'failed'  => $failed,
        'total'   => $total
    ]);
    exit();
}

// ── EMAIL branch ───────────────────────────────────────────
// Deduplicate and validate
$emails = array_unique($emails);
$validEmails = array_filter($emails, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));
$validEmails = array_values($validEmails);

if (empty($validEmails)) {
    echo json_encode(['status' => 'error', 'message' => 'No valid email addresses found for the selected group.']);
    exit();
}

// ── Build HTML email body ───────────────────────────────────
function buildEmailHtml(string $heading, string $salutation, string $body,
                         string $event_date, string $event_venue, string $type,
                         string $closing, string $sender): string {
    $eventBlock = '';
    if ($type === 'event' && ($event_date || $event_venue)) {
        $eventBlock = '
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f6fa;border-left:4px solid #6576ff;padding:12px 16px;border-radius:4px;margin:16px 0">
            <tr><td><strong>Event Date:</strong> ' . htmlspecialchars($event_date) . '</td></tr>
            <tr><td><strong>Venue:</strong> ' . htmlspecialchars($event_venue) . '</td></tr>
        </table>';
    }

    $bodyEscaped = nl2br(htmlspecialchars($body));
    $year = date('Y');

    return '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>' . htmlspecialchars($heading) . '</title></head>
<body style="margin:0;padding:0;background:#f5f6fa;font-family:Georgia,serif">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">
      <tr><td style="background:#6576ff;color:#fff;text-align:center;padding:28px 32px;font-size:22px;font-weight:bold;letter-spacing:.5px">
        ' . htmlspecialchars($heading) . '
      </td></tr>
      <tr><td style="padding:32px 40px;color:#333;font-size:15px;line-height:1.8">
        <p>' . htmlspecialchars($salutation) . '</p>
        <p>' . $bodyEscaped . '</p>
        ' . $eventBlock . '
        <p style="margin-top:24px">' . htmlspecialchars($closing) . '</p>
        <p><strong>' . htmlspecialchars($sender) . '</strong></p>
      </td></tr>
      <tr><td style="background:#f5f6fa;text-align:center;padding:16px;font-size:12px;color:#8094ae">
        &copy; ' . $year . ' CACYOF BOUESTI. This email was sent because you are a registered member.
      </td></tr>
    </table>
  </td></tr>
</table>
</body></html>';
}

$htmlBody = buildEmailHtml($heading, $salutation, $body, $event_date, $event_venue, $type, $closing, $sender);

// ── Send emails ─────────────────────────────────────────────
$sent   = 0;
$failed = 0;

if (USE_PHPMAILER) {
    // PHPMailer path — adjust if needed
    $autoload = __DIR__ . '/../../vendor/autoload.php';
    if (!file_exists($autoload)) {
        echo json_encode(['status' => 'error', 'message' => 'PHPMailer not found. Set USE_PHPMAILER to false or install via Composer.']);
        exit();
    }
    require $autoload;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    foreach ($validEmails as $email) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->setFrom(FROM_EMAIL, FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>','<p>','</p>'], ["\n","\n","\n"], $htmlBody));
            $mail->send();
            $sent++;
        } catch (\Exception $e) {
            $failed++;
        }
    }
} else {
    // PHP mail() fallback
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    foreach ($validEmails as $email) {
        // Individual send — no BCC exposure
        if (@mail($email, $subject, $htmlBody, $headers)) {
            $sent++;
        } else {
            $failed++;
        }
    }
}

$total = count($validEmails);
$msg   = "Newsletter sent to {$sent} of {$total} recipient(s).";
if ($failed > 0) $msg .= " {$failed} address(es) were skipped.";

echo json_encode([
    'status'  => $sent > 0 ? 'success' : 'error',
    'message' => $msg,
    'sent'    => $sent,
    'failed'  => $failed,
    'total'   => $total
]);
