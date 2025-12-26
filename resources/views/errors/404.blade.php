<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Come & Fix</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Jost:wght@700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --brand-orange: #FF9800;
            --brand-orange-hover: #F57C00;
            --brand-dark: #2C2C2C;
            --text-gray: #666666;
            --bg-gray: #F1F2F4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-gray);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-dark);
            overflow: hidden;
        }

        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            position: relative;
        }

        .error-code {
            font-family: 'Jost', sans-serif;
            font-size: 10rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--brand-orange), #FFB74D);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .error-code::after {
            content: '404';
            position: absolute;
            top: 4px;
            left: 4px;
            z-index: -1;
            background: rgba(0, 0, 0, 0.05);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .error-title {
            font-family: 'Jost', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
        }

        .error-message {
            color: var(--text-gray);
            font-size: 1.125rem;
            line-height: 1.6;
            margin-bottom: 3rem;
        }

        .action-btns {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background-color: var(--brand-orange);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        }

        .btn-primary:hover {
            background-color: var(--brand-orange-hover);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 152, 0, 0.4);
        }

        .btn-secondary {
            background-color: white;
            color: var(--brand-dark);
            border: 2px solid #EAEAEA;
        }

        .btn-secondary:hover {
            border-color: var(--brand-orange);
            color: var(--brand-orange);
            transform: translateY(-3px);
            background-color: rgba(255, 152, 0, 0.02);
        }

        /* Decorative Elements */
        .icon-floating {
            position: absolute;
            font-size: 3rem;
            color: var(--brand-orange);
            opacity: 0.1;
            animation: float 6s infinite ease-in-out;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }

        .icon-1 { top: -10%; left: 10%; animation-delay: 0s; }
        .icon-2 { bottom: 10%; right: 10%; animation-delay: 2s; }
        .icon-3 { top: 20%; right: 5%; animation-delay: 4s; }
        .icon-4 { bottom: -5%; left: 20%; animation-delay: 1s; }

        @media (max-width: 768px) {
            .error-code { font-size: 7rem; }
            .error-title { font-size: 2rem; }
            .action-btns { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Floating Icons -->
        <i class="bi bi-wrench icon-floating icon-1"></i>
        <i class="bi bi-hammer icon-floating icon-2"></i>
        <i class="bi bi-tools icon-floating icon-3"></i>
        <i class="bi bi-gear-wide-connected icon-floating icon-4"></i>

        <div class="error-code">404</div>
        <h1 class="error-title">Oops! Wrong Turn?</h1>
        <p class="error-message">
            It seems like the page you're looking for has been moved or doesn't exist. 
            Don't worry, our handymen can fix almost anything, but they can't find this page!
        </p>
        
        <div class="action-btns">
            <a href="/" class="btn btn-primary">
                <i class="bi bi-house-door-fill"></i>
                Back to Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Go Back
            </a>
        </div>
    </div>
</body>
</html>
