<?php
require_once 'config/db.php';

$errors = [];
$success = false;
$formData = [
    'name' => '',
    'email' => '',
    'company' => '',
    'phone' => '',
    'project_type' => '',
    'budget' => '',
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $field => $_) {
        $formData[$field] = trim($_POST[$field] ?? '');
    }

    if ($formData['name'] === '') {
        $errors[] = 'Please share your name so we know who to follow up with.';
    }

    if ($formData['email'] === '' || ! filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'We need a valid email address to respond to you.';
    }

    if ($formData['phone'] === '') {
        $errors[] = 'A phone number helps us connect quickly; please add one.';
    }

    if ($formData['message'] === '') {
        $errors[] = 'Let us know a bit about your project or question.';
    }

    if (! $errors) {
        $createTableSql = <<<SQL
CREATE TABLE IF NOT EXISTS talk_to_expert_leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    company VARCHAR(190) NULL,
    phone VARCHAR(60) NOT NULL,
    project_type VARCHAR(120) NULL,
    budget VARCHAR(120) NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

        if (! $conn->query($createTableSql)) {
            $errors[] = 'We could not prepare the database table. Please try again later or reach us directly at info@syntrex.io.';
        }

        if (! $errors) {
            $stmt = $conn->prepare('INSERT INTO talk_to_expert_leads (name, email, company, phone, project_type, budget, message) VALUES (?, ?, ?, ?, ?, ?, ?)');

            if ($stmt) {
                $stmt->bind_param(
                    'sssssss',
                    $formData['name'],
                    $formData['email'],
                    $formData['company'],
                    $formData['phone'],
                    $formData['project_type'],
                    $formData['budget'],
                    $formData['message']
                );

                if ($stmt->execute()) {
                    $success = true;
                    foreach ($formData as $field => $_) {
                        $formData[$field] = '';
                    }
                } else {
                    $errors[] = 'Something went wrong while saving your request. Please try again or email us at info@syntrex.io.';
                }

                $stmt->close();
            } else {
                $errors[] = 'Unable to submit your request right now. Please try again shortly.';
            }
        }
    }
}

require_once 'templates/header.php';
?>
    <style>
        .talk-to-expert-section .form-group {
            margin-bottom: 20px;
        }

        .talk-to-expert-section .form-control {
            width: 100%;
            height: 56px;
            padding: 0 18px;
            border-radius: 6px;
        }

        .talk-to-expert-section select.form-control {
            padding-right: 32px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .talk-to-expert-section textarea.form-control {
            min-height: 160px;
            padding: 16px 18px;
        }
    </style>
    <main>
        <div class="slider-area">
            <div class="single-sliders slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-7 col-lg-9 col-md-9">
                            <div class="hero-caption hero-caption2">
                                <h2>Talk to an Expert</h2>
                                <p>Tell us about your goals, challenges, or upcoming project. Our team will connect with you to share the best path forward and schedule a call if needed.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="contact-section section-padding talk-to-expert-section">
            <div class="container">
                <?php if ($success): ?>
                    <div class="alert success-alert">
                        <span class="alert-icon"></span>
                        <span class="alert-message">Thanks for reaching out! Our experts will contact you shortly.</span>
                        <span class="alert-close">&times;</span>
                    </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <h2 class="contact-title">Share your project details</h2>
                    </div>
                    <div class="col-lg-7">
                        <form class="form-contact contact_form talk-to-expert-form" action="talk-to-expert.php" method="post" novalidate>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="name" placeholder="Your name" value="<?php echo htmlspecialchars($formData['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" type="email" name="email" placeholder="Work email" value="<?php echo htmlspecialchars($formData['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="company" placeholder="Company (optional)" value="<?php echo htmlspecialchars($formData['company'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="phone" placeholder="Phone number" value="<?php echo htmlspecialchars($formData['phone'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <select class="form-control" name="project_type">
                                            <option value="">How can we help?</option>
                                            <option value="AI & Automation" <?php echo $formData['project_type'] === 'AI & Automation' ? 'selected' : ''; ?>>AI &amp; Automation</option>
                                            <option value="Custom Software" <?php echo $formData['project_type'] === 'Custom Software' ? 'selected' : ''; ?>>Custom Software</option>
                                            <option value="Product Discovery" <?php echo $formData['project_type'] === 'Product Discovery' ? 'selected' : ''; ?>>Product Discovery</option>
                                            <option value="Team Augmentation" <?php echo $formData['project_type'] === 'Team Augmentation' ? 'selected' : ''; ?>>Team Augmentation</option>
                                            <option value="Other" <?php echo $formData['project_type'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <select class="form-control" name="budget">
                                            <option value="">Estimated budget (optional)</option>
                                            <option value="Under $10k" <?php echo $formData['budget'] === 'Under $10k' ? 'selected' : ''; ?>>Under $10k</option>
                                            <option value="$10k - $25k" <?php echo $formData['budget'] === '$10k - $25k' ? 'selected' : ''; ?>>$10k - $25k</option>
                                            <option value="$25k - $50k" <?php echo $formData['budget'] === '$25k - $50k' ? 'selected' : ''; ?>>$25k - $50k</option>
                                            <option value="$50k+" <?php echo $formData['budget'] === '$50k+' ? 'selected' : ''; ?>>$50k+</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control w-100" name="message" rows="6" placeholder="Tell us about your project, challenges, or goals" required><?php echo htmlspecialchars($formData['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="button button-contactForm boxed-btn">Submit inquiry</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4 offset-lg-1">
                        <div class="media contact-info mb-4">
                            <span class="contact-info__icon"><i class="ti-calendar"></i></span>
                            <div class="media-body">
                                <h3>Book a call instantly</h3>
                                <p>Pick a time that works best using our Calendly scheduler.</p>
                            </div>
                        </div>
                        <div class="calendly-inline-widget" data-url="https://calendly.com/syntrex/30min" style="min-width: 320px; height: 630px;"></div>
                        <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php require_once 'templates/footer.php'; ?>
