<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #334155 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            max-width: 440px;
            width: 100%;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .brand-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #475569 0%, #1e293b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            color: #ffffff;
            font-size: 2rem;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.35);
        }
    </style>
</head>
<body>

<div class="login-card card bg-white">
    <div class="card-body p-4 p-sm-5">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-cart4"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1"><?= STORE_NAME ?></h4>
            <p class="text-muted small">Sistem Kasir & Analisis Market Basket (Apriori)</p>
        </div>

        <?php require __DIR__ . '/../layouts/alerts.php'; ?>

        <form action="<?= BASE_URL ?>index.php?page=login&action=process" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label small fw-semibold text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control bg-light border-start-0" id="username" name="username" placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control bg-light border-start-0" id="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center">
            <p class="small text-muted mb-2">Akun Demo Default:</p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillLogin('admin', 'admin123')">
                    <i class="bi bi-shield-lock me-1"></i> Admin (admin / admin123)
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillLogin('kasir', 'kasir123')">
                    <i class="bi bi-cart-check me-1"></i> Kasir (kasir / kasir123)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(user, pass) {
        document.getElementById('username').value = user;
        document.getElementById('password').value = pass;
    }
</script>

</body>
</html>

