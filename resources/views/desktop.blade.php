<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desktop Version Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        h1 {
            margin-bottom: 1.5rem;
            font-size: 2.5rem;
        }

        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .device-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            h1 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="device-icon">💻</div>
        <h1>Desktop Version Required</h1>
        <p>This page is optimized for desktop viewing. Please access it from a desktop or laptop computer for the best
            experience.</p>
        <p>If you believe you're seeing this message in error, please contact our support team.</p>
    </div>
</body>

</html>
