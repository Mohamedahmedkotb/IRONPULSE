<?php
session_start();
include 'db.php';

// ── Only handle POST ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../html/signup.html');
    exit;
}

// ── Sanitize inputs ─────────────────────────────────────────────────────────
$name             = trim(htmlspecialchars($_POST['name']            ?? ''));
$email            = trim(htmlspecialchars($_POST['email']           ?? ''));
$password         = $_POST['password']         ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

$errors = [];

// ── Server-side validation ──────────────────────────────────────────────────
if (empty($name)) {
    $errors[] = 'Full name is required.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match.';
}

// ── If validation passed, check DB ─────────────────────────────────────────
if (empty($errors)) {
    // Check if email already registered
    $check = $conn->prepare("SELECT email FROM newusers WHERE email = ?");
    if ($check) {
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = 'That email address is already registered. <a href="./login.html">Log in instead?</a>';
        }
        $check->close();
    } else {
        $errors[] = 'Database error. Please try again later.';
    }
}

// ── Insert if still no errors ───────────────────────────────────────────────
$success = false;
if (empty($errors)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt   = $conn->prepare("INSERT INTO newusers (name, email, password) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $hashed);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = 'Could not create account. Please try again.';
        }
        $stmt->close();
    } else {
        $errors[] = 'Database error: ' . $conn->error;
    }
}

$conn->close();

// ── Redirect on success ─────────────────────────────────────────────────────
if ($success) {
    header('Location: ../html/login.html?registered=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Error — IRONPULSE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 40px;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            text-align: center;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 28px;
        }

        .error-list {
            list-style: none;
            margin-bottom: 32px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
        }

        .error-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.875rem;
            color: #991b1b;
            line-height: 1.5;
        }

        .error-list li::before {
            content: '✕';
            flex-shrink: 0;
            font-weight: 700;
            color: #ef4444;
        }

        .error-list li a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
            box-shadow: 0 4px 14px rgba(59,130,246,0.35);
            margin-bottom: 12px;
        }

        .btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .btn-ghost {
            display: inline-block;
            width: 100%;
            padding: 13px;
            background: transparent;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-ghost:hover {
            border-color: #d1d5db;
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>
        <h1>Sign Up Failed</h1>
        <p class="subtitle">Please fix the following errors and try again.</p>

        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>

        <a href="javascript:history.back()" class="btn">← Go Back &amp; Try Again</a>
        <a href="../html/login.html" class="btn-ghost">Already have an account? Log in</a>
    </div>
</body>
</html>
