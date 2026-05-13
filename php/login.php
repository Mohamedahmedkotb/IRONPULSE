<?php
session_start();
require_once 'db.php';

// ── Only handle POST ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../html/login.html');
    exit;
}

// ── Sanitize inputs ──────────────────────────────────────────────────────────
$email    = trim(htmlspecialchars($_POST['email']    ?? ''));
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

$error = '';

// ── Basic validation ─────────────────────────────────────────────────────────
if (empty($email) || empty($password)) {
    $error = 'Please enter both your email and password.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
}

// ── Database lookup ──────────────────────────────────────────────────────────
if (empty($error)) {
    $stmt = $conn->prepare("SELECT name, email, password FROM newusers WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // ── Successful login ─────────────────────────────────────────
                session_regenerate_id(true); // Prevent session fixation

                $_SESSION['user_id'] = $user['email']; // Using email since id column is missing
                $_SESSION['name']    = $user['name'];
                $_SESSION['email']   = $user['email'];

                // ── Remember me: set cookie for 30 days ──────────────────────
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                }

                $conn->close();
                header('Location: Userprofile.php');
                exit;
            } else {
                // Wrong password — use a generic message for security
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
        $stmt->close();
    } else {
        $error = 'A database error occurred. Please try again later.';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Error — IRONPULSE</title>
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
            max-width: 440px;
            width: 100%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            text-align: center;
        }

        .icon { font-size: 3rem; margin-bottom: 16px; }

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

        .error-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 28px;
            text-align: left;
        }

        .error-box .err-icon {
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .error-box p {
            font-size: 0.875rem;
            color: #991b1b;
            font-weight: 500;
            line-height: 1.5;
        }

        .btn {
            display: block;
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
            display: block;
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
        <div class="icon">🔒</div>
        <h1>Login Failed</h1>
        <p class="subtitle">We couldn't sign you in. Please check your details.</p>

        <div class="error-box">
            <span class="err-icon">⚠️</span>
            <p><?= htmlspecialchars($error) ?></p>
        </div>

        <a href="javascript:history.back()" class="btn">← Try Again</a>
        <a href="../html/signup.html" class="btn-ghost">Don't have an account? Sign up</a>
    </div>
</body>
</html>
