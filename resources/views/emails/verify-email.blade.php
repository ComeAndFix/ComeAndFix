<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Come & Fix</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
            padding: 50px 30px;
            text-align: center;
        }
        
        .brand-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .brand-name {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .verify-button-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 48px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
        }
        
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 152, 0, 0.4);
        }
        
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
        
        .alternative-text {
            font-size: 14px;
            color: #718096;
            margin-bottom: 15px;
        }
        
        .url-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            word-break: break-all;
            font-size: 13px;
            color: #4a5568;
            font-family: 'Courier New', monospace;
        }
        
        .security-notice {
            background-color: #fff3e0;
            border-left: 4px solid #FF9800;
            padding: 15px;
            margin-top: 30px;
            border-radius: 4px;
        }
        
        .security-notice p {
            font-size: 14px;
            color: #5d4037;
            margin: 0;
        }
        
        .email-footer {
            background-color: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-text {
            font-size: 14px;
            color: #718096;
            margin-bottom: 10px;
        }
        
        .footer-brand {
            font-size: 14px;
            color: #FF9800;
            font-weight: 600;
        }
        
        .social-links {
            margin-top: 20px;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #718096;
            text-decoration: none;
            font-size: 14px;
        }
        
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .verify-button {
                padding: 14px 36px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="brand-name">Come&Fix</div>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <h1 class="greeting">Welcome to Come & Fix! 👋</h1>
            
            <p class="message">
                Thank you for registering with Come & Fix! We're excited to have you join our community of customers and skilled professionals.
            </p>
            
            <p class="message">
                To get started and access all features, please verify your email address by clicking the button below:
            </p>
            
            <div class="verify-button-container">
                <a href="{{ $url }}" class="verify-button">
                    Verify Email Address
                </a>
            </div>
            
            <div class="security-notice">
                <p>
                    <strong>🔒 Security Notice:</strong> This verification link will expire in 60 minutes. If you didn't create an account with Come & Fix, please ignore this email.
                </p>
            </div>
            
            <div class="divider"></div>
            
            <p class="alternative-text">
                If you're having trouble clicking the button, copy and paste the URL below into your web browser:
            </p>
            
            <div class="url-box">
                {{ $url }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                © {{ date('Y') }} Come & Fix. All rights reserved.
            </p>
            <p class="footer-brand">
                Your trusted platform for home repair services
            </p>
        </div>
    </div>
</body>
</html>
