<?php
session_start();
include '../connection.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:../login.php');
    exit();
}

$message = [];

if (isset($_POST['send_message'])) {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $number  = $_POST['number'];
    $msg     = $_POST['message'];
    $user_id = $_SESSION['user_id'] ?? 0;

    if (empty($name) || empty($email) || empty($msg)) {
        $message[] = ['type' => 'error', 'text' => 'Please fill in all required fields.'];
    } else {
        $stmt = $conn->prepare("INSERT INTO message (user_id, name, email, number, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $email, $number, $msg]);
        $message[] = ['type' => 'success', 'text' => 'Thank you! Your message has been sent. We\'ll get back to you shortly.'];
    }
}

$user_name  = $_SESSION['user_name']  ?? '';
$user_email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — Bloom&amp;Petal</title>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-hero">
    <div class="breadcrumb"><a href="index.php">Home</a> / Contact</div>
    <h1>Get In Touch</h1>
    <p>We'd love to hear from you — questions, custom orders, or just to say hello!</p>
</div>

<?php foreach ($message as $msg): ?>
<div class="flash-msg flash-msg-<?php echo $msg['type']; ?>">
    <i class="bi <?php echo $msg['type']==='success' ? 'bi-check-circle' : 'bi-exclamation-circle'; ?>"></i>
    <?php echo $msg['text']; ?>
</div>
<?php endforeach; ?>

<div class="contact-layout">
    <div class="contact-info">
        <h2>We're Here<br>For You</h2>
        <p>Whether you need help with an order, want to arrange something special, or simply have a question — our team is ready to help.</p>

        <div class="contact-detail">
            <i class="bi bi-geo-alt"></i>
            <div>
                <strong>Visit Us</strong>
                123 Rose Avenue, Tunis, Tunisia
            </div>
        </div>
        <div class="contact-detail">
            <i class="bi bi-telephone"></i>
            <div>
                <strong>Call Us</strong>
                +216 XX XXX XXX
            </div>
        </div>
        <div class="contact-detail">
            <i class="bi bi-envelope"></i>
            <div>
                <strong>Email Us</strong>
                hello@bloomandpetal.com
            </div>
        </div>
        <div class="contact-detail">
            <i class="bi bi-clock"></i>
            <div>
                <strong>Opening Hours</strong>
                Mon–Sat: 8:00 AM – 7:00 PM<br>
                Sunday: 9:00 AM – 5:00 PM
            </div>
        </div>

        <div class="contact-socials">
            <p class="socials-label">Follow Us</p>
            <div class="footer-socials contact-socials-links">
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-pinterest"></i></a>
            </div>
        </div>
    </div>

    <div class="contact-form-box">
        <h3>Send Us a Message</h3>
        <form method="POST" action="contact.php">
            <div class="form-row">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user_name); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="number" placeholder="+216 XX XXX XXX">
                </div>
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
            </div>
            <div class="form-group">
                <label>Your Message *</label>
                <textarea name="message" class="textarea-lg" placeholder="Tell us how we can help..." required></textarea>
            </div>
            <button type="submit" name="send_message" class="btn-primary btn-full">
                <i class="bi bi-send"></i> Send Message
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
