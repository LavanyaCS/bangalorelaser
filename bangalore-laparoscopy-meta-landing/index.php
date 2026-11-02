<?php
$config = require __DIR__ . '/config.php';
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$success = isset($_GET['sent']) && $_GET['sent'] === '1';
$error = '';
$old = ['name'=>'', 'phone'=>'', 'city'=>'', 'treatment'=>'', 'call_time'=>'', 'message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $value) {
        $old[$key] = trim((string)($_POST[$key] ?? ''));
    }
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Your session expired. Please refresh the page and try again.';
    } elseif ($old['name'] === '' || !preg_match('/^[0-9+\s()-]{8,18}$/', $old['phone'])) {
        $error = 'Please enter your name and a valid mobile number.';
    } elseif (!filter_var($config['recipient_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'The enquiry email is not configured. Please call the clinic.';
    } else {
        $subject = 'New Meta Ads Landing Page Enquiry - ' . $config['clinic_name'];
        $body = "New appointment enquiry\n\n"
            . "Name: {$old['name']}\nPhone: {$old['phone']}\nCity: {$old['city']}\n"
            . "Treatment: {$old['treatment']}\nPreferred call time: {$old['call_time']}\n"
            . "Message: {$old['message']}\n"
            . "Source: Website landing page\nIP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unavailable') . "\n";
        $headers = [];
        $headers[] = 'From: ' . $config['clinic_name'] . ' Website <' . $config['sender_email'] . '>';
        $headers[] = 'Reply-To: ' . $config['sender_email'];
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $sent = @mail($config['recipient_email'], $subject, $body, implode("\r\n", $headers));
        if ($sent) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?sent=1#appointment');
            exit;
        } else {
            $error = 'We could not send your request right now. Please call or WhatsApp the clinic directly.';
        }
    }
}
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$phone = preg_replace('/\D+/', '', $config['phone_digits']);
$wa = preg_replace('/\D+/', '', $config['whatsapp_digits']);
$widgetId = trim($config['trustindex_widget_id']);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bangalore Laparoscopy Center | Dr. Uday Ravi</title>
<meta name="description" content="Consult Dr. Uday Ravi at Bangalore Laparoscopy Center for laparoscopic, robotic and colorectal surgical care. Patients travelling from West Bengal can contact our team for appointment guidance.">
<meta name="theme-color" content="#5ED4E4">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="#home" aria-label="Bangalore Laparoscopy Center home">
      <span class="brand-mark">✚</span><span><strong>Bangalore<br>Laparoscopy Center</strong><small>Laparoscopic | Robotic | Minimal Access Surgery</small></span>
    </a>
    <button class="menu-toggle" aria-label="Open menu" aria-expanded="false">☰</button>
    <nav class="main-nav">
      <a href="#home">Home</a><a href="#about">About</a><a href="#treatments">Treatments</a>
      <a href="#travel">Outstation Patients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a><a href="#contact">Contact</a>
    </nav>
    <a class="btn btn-orange nav-call" href="tel:<?= e($phone) ?>">☎ <?= e($config['phone_display']) ?></a>
  </div>
</header>

<main>
<section class="hero" id="home">
 <div class="hero-overlay"></div>
 <div class="container hero-grid">
  <div class="hero-copy">
   <span class="eyebrow">Advanced Surgical Care in Bengaluru</span>
   <h1>Expert Laparoscopic &amp;<br>Robotic Surgical Care</h1>
   <p class="lead">Consult <?= e($config['doctor_name']) ?> for piles, fissure, fistula, hernia, gallbladder and other gastrointestinal conditions.</p>
   <div class="benefits">
    <div><b>✧</b><span>Minimally<br>Invasive</span></div><div><b>◷</b><span>Recovery<br>Guidance</span></div>
    <div><b>◇</b><span>Patient-focused<br>Care</span></div><div><b>✚</b><span>Advanced<br>Surgical Care</span></div>
   </div>
   <div class="hero-actions"><a class="btn btn-orange" href="tel:<?= e($phone) ?>">☎ Call Now<br><small><?= e($config['phone_display']) ?></small></a><a class="btn btn-white" href="#appointment">▦ Book an Appointment</a></div>
   <p class="micro-note">Treatment suitability and outcomes vary by individual. Consultation is required.</p>
  </div>
  <div class="doctor-panel">
   <div class="doctor-placeholder"><span>DR</span><small>Replace with approved doctor photo</small></div>
   <div class="doctor-card"><h2><?= e($config['doctor_name']) ?></h2><p>MBBS, MS, FMAS, FALS, FISCP, FIAGES, FARIS, DMAS (France)</p><hr><strong>16+ Years</strong><br><small>Reported surgical experience — confirm before publishing</small></div>
  </div>
 </div>
</section>

<section class="section treatments" id="treatments">
 <div class="container">
  <div class="section-heading"><span class="eyebrow light">Our Expertise</span><h2>Treatments &amp; Surgical Services</h2><p>Consult the surgeon to understand diagnosis, available treatment options and the care plan appropriate for you.</p></div>
  <div class="treatment-grid">
   <?php foreach (['Piles (Haemorrhoids)','Anal Fissure','Anal Fistula','Pilonidal Sinus','Hernia Surgery','Gallbladder Surgery','Appendix Surgery','Bariatric Surgery','Gastrointestinal Surgery'] as $i=>$treatment): ?>
    <article class="treatment-card"><span class="treatment-icon"><?= ['◉','◌','◎','⌁','✚','❋','◍','◒','◉'][$i] ?></span><h3><?= e($treatment) ?></h3><p>Assessment and treatment options based on the doctor's evaluation.</p></article>
   <?php endforeach; ?>
  </div>
 </div>
</section>

<section class="section split-section" id="about">
 <div class="container split-grid">
  <div><span class="eyebrow">Why Choose Us</span><h2>Care guided by experience and individual needs</h2>
   <ul class="check-list">
    <li><b>Experienced surgeon</b><span>Consultation with <?= e($config['doctor_name']) ?>.</span></li>
    <li><b>Laparoscopic and robotic surgery</b><span>Discuss the appropriate approach for your condition.</span></li>
    <li><b>Clear treatment guidance</b><span>Understand the diagnosis, options, risks and recovery plan.</span></li>
    <li><b>Patient-centred support</b><span>Assistance with appointment coordination and pre-visit questions.</span></li>
   </ul>
  </div>
  <div class="travel-card" id="travel"><span class="eyebrow">For Outstation Patients</span><h2>Travelling from West Bengal to Bengaluru?</h2>
   <p>Contact the clinic before making travel arrangements. Our team can help coordinate an appointment and share clinic visit information.</p>
   <div class="travel-steps"><div><b>01</b><span>Call or send an enquiry</span></div><div><b>02</b><span>Discuss consultation availability</span></div><div><b>03</b><span>Confirm your appointment before travel</span></div></div>
   <a class="btn btn-orange" href="#appointment">Plan Your Consultation</a>
   <small>Travel, accommodation and hospital admission support should be confirmed directly with the clinic.</small>
  </div>
 </div>
</section>

<section class="offer-band">
 <div class="container offer-inner"><div><span>Special information for</span><strong>Outstation Patients</strong></div><div class="offer-free"><b>FREE</b><strong>Phone Consultation</strong></div><p>Call the clinic to confirm whether a complimentary phone consultation is available and understand the next steps.</p><a class="btn btn-orange" href="tel:<?= e($phone) ?>">☎ Call Now</a></div>
</section>

<section class="section proof-section" id="reviews">
 <div class="container proof-grid">
  <div class="review-box"><span class="eyebrow">Patient Feedback</span><h2>Google Reviews</h2>
   <?php if ($widgetId !== ''): ?>
    <script defer async src="https://cdn.trustindex.io/loader.js?<?= e($widgetId) ?>"></script>
    <div class="trustindex-widget"></div>
   <?php else: ?>
    <div class="review-placeholder"><div class="google-g">G</div><h3>Read patient reviews on Google</h3><p>Connect the correct Google Business Profile review widget to display live reviews here.</p><a class="btn btn-outline" href="<?= e($config['google_reviews_url']) ?>" target="_blank" rel="noopener">View Google Profile &amp; Reviews ↗</a></div>
   <?php endif; ?>
   <p class="micro-note">Only genuine reviews from the clinic's verified profile should be displayed. Do not add sample or invented testimonials.</p>
  </div>
  <div class="doctor-about"><span class="eyebrow">Meet Your Surgeon</span><h2>About <?= e($config['doctor_name']) ?></h2><p>Dr. Uday Ravi's profile lists qualifications including MBBS, MS, FMAS, FALS, FISCP, FIAGES, FARIS and DMAS (France). Please confirm the final credentials and experience details with the clinic before publication.</p><a href="<?= e($config['blogger_profile_url']) ?>" target="_blank" rel="noopener" class="text-link">View Doctor Profile ↗</a></div>
 </div>
</section>

<section class="section appointment-section" id="appointment">
 <div class="container appointment-grid">
  <div><span class="eyebrow">Take the First Step</span><h2>Request an Appointment</h2><p>Share your contact details and our team can get back to you. For urgent medical concerns, seek immediate medical care rather than waiting for a website response.</p>
   <div class="contact-mini"><b>Call:</b> <a href="tel:<?= e($phone) ?>"><?= e($config['phone_display']) ?></a><br><b>WhatsApp:</b> <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">Message the clinic</a></div>
  </div>
  <form class="appointment-form" method="post" action="#appointment">
   <h3>Book an Appointment</h3><p class="form-intro">Fields marked * are required.</p>
   <?php if ($success): ?><div class="notice success">Thank you. Your enquiry has been submitted. The clinic team will contact you.</div><?php endif; ?>
   <?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
   <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
   <label>Your Name *<input name="name" required maxlength="90" value="<?= e($old['name']) ?>" placeholder="Enter your name"></label>
   <label>Mobile Number *<input name="phone" required inputmode="tel" maxlength="18" value="<?= e($old['phone']) ?>" placeholder="Enter your phone number"></label>
   <label>Your City<input name="city" maxlength="80" value="<?= e($old['city']) ?>" placeholder="e.g. Kolkata, Siliguri"></label>
   <label>Interested Treatment<select name="treatment"><option value="">Select treatment</option><?php foreach (['Piles','Anal Fissure','Anal Fistula','Pilonidal Sinus','Hernia','Gallbladder','Appendix','Bariatric','Other / Not sure'] as $v): ?><option <?= $old['treatment']===$v?'selected':'' ?>><?= e($v) ?></option><?php endforeach; ?></select></label>
   <label>Preferred Call Time<input name="call_time" maxlength="60" value="<?= e($old['call_time']) ?>" placeholder="e.g. 10 AM – 1 PM"></label>
   <label>Message (optional)<textarea name="message" rows="3" maxlength="500" placeholder="Share a brief question (please avoid sensitive medical details)."><?= e($old['message']) ?></textarea></label>
   <button class="btn btn-orange full" type="submit">Request a Call Back →</button>
   <small>By submitting, you agree that the clinic may contact you about this enquiry. Read our <a href="privacy-policy.php">Privacy Policy</a>.</small>
  </form>
 </div>
</section>

<section class="section faq-section" id="faq">
 <div class="container"><div class="section-heading"><span class="eyebrow">Need Help?</span><h2>Frequently Asked Questions</h2></div>
  <div class="faq-list">
   <?php
   $faqs = [
    ['Can I speak to the clinic before travelling?','Yes. Call or submit the enquiry form to ask about consultation availability and the information you should bring.'],
    ['How do I book an appointment?','Call the clinic or submit the appointment request form. Please wait for confirmation before travelling.'],
    ['Do you accept patients from West Bengal?','Patients can contact the clinic from any location to enquire about consultation availability. Confirm the visit plan directly with the clinic.'],
    ['What treatments are available?','The clinic profile lists laparoscopic, robotic and colorectal surgical services. The doctor will advise which options are appropriate after assessment.'],
    ['How long is the hospital stay or recovery?','This depends on the diagnosis, procedure and individual health factors. Your surgeon will explain the expected plan after evaluation.'],
    ['Do you provide travel or accommodation arrangements?','Please contact the clinic to confirm what assistance is currently available. Do not book travel until your appointment is confirmed.'],
    ['Can I get a free phone consultation?','Call the clinic to confirm current availability and any terms of the complimentary phone consultation offer.']
   ];
   foreach ($faqs as $faq): ?>
    <details class="faq-item"><summary><?= e($faq[0]) ?></summary><p><?= e($faq[1]) ?></p></details>
   <?php endforeach; ?>
  </div>
 </div>
</section>

<section class="locations-section" id="contact"><div class="container"><span class="eyebrow">Visit Us</span><h2>Contact &amp; Clinic Locations</h2>
 <div class="location-grid">
  <article><h3>Rajajinagar Centre</h3><p>Tejas Arcade, 9/1, Dr Rajkumar Road, A Block, Milk Colony, 2nd Stage, Rajajinagar, Bengaluru – 560010.</p></article>
  <article><h3>Indiranagar Centre</h3><p>725, Chinmaya Mission Hospital Road, 1st Stage, Defence Colony, Indiranagar, Bengaluru – 560038.</p></article>
  <article><h3>Contact the Clinic</h3><p><a href="tel:<?= e($phone) ?>"><?= e($config['phone_display']) ?></a><br><a href="mailto:<?= e($config['recipient_email']) ?>"><?= e($config['recipient_email']) ?></a></p></article>
 </div>
 <p class="micro-note">Confirm current operating locations and appointment availability with the clinic before visiting.</p>
</div></section>
</main>

<footer class="site-footer"><div class="container footer-grid">
 <div><div class="brand footer-brand"><span class="brand-mark">✚</span><strong>Bangalore<br>Laparoscopy Center</strong></div><p>Laparoscopic | Robotic | Minimal Access Surgery</p></div>
 <div><h4>Quick Links</h4><a href="#about">About</a><a href="#treatments">Treatments</a><a href="#travel">Outstation Patients</a><a href="#reviews">Reviews</a><a href="#faq">FAQ</a></div>
 <div><h4>Contact</h4><a href="tel:<?= e($phone) ?>"><?= e($config['phone_display']) ?></a><a href="mailto:<?= e($config['recipient_email']) ?>"><?= e($config['recipient_email']) ?></a><a href="privacy-policy.php">Privacy Policy</a><a href="terms.php">Terms &amp; Conditions</a></div>
</div><div class="copyright">© <?= date('Y') ?> <?= e($config['clinic_name']) ?>. All rights reserved. | Medical information on this page is general and not a substitute for professional consultation.</div></footer>

<div class="floating-actions"><a class="float-call" href="tel:<?= e($phone) ?>">☎ Call</a><a class="float-wa" href="https://wa.me/<?= e($wa) ?>?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20an%20appointment." target="_blank" rel="noopener">WhatsApp</a></div>
<div class="popup-backdrop" id="consultPopup" aria-hidden="true"><div class="popup-card" role="dialog" aria-modal="true" aria-labelledby="popupTitle"><button class="popup-close" aria-label="Close popup">×</button><span class="eyebrow">Outstation Patient Assistance</span><h2 id="popupTitle">Planning treatment in Bengaluru?</h2><p>Call the clinic to enquire about a complimentary phone consultation and appointment availability.</p><a class="btn btn-orange full" href="tel:<?= e($phone) ?>">Call <?= e($config['phone_display']) ?></a><a class="text-link" href="#appointment" id="popupBook">Request an appointment instead</a></div></div>
<script src="assets/script.js"></script>
</body></html>
