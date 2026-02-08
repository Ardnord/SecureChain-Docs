<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Halaman tidak ditemukan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 16px;
            color: #202124;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .container {
            text-align: center;
            max-width: 600px;
            padding: 20px;
        }

        .error-code {
            font-size: 96px;
            font-weight: 300;
            color: #202124;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        h1 {
            font-size: 24px;
            font-weight: 400;
            color: #202124;
            margin-bottom: 10px;
        }

        .error-message {
            font-size: 16px;
            color: #5f6368;
            margin-bottom: 30px;
            line-height: 1.5;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .suggestions {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
            margin-top: 40px;
        }

        a {
            text-decoration: none;
            color: #1a73e8;
            font-weight: 500;
            padding: 10px 24px;
            border-radius: 4px;
            transition: all 0.2s;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
        }

        a:hover {
            background-color: #f8f9fa;
            border-color: #dadce0;
        }

        .btn-primary {
            background-color: #1a73e8;
            color: white;
            border: 1px solid #1a73e8;
        }

        .btn-primary:hover {
            background-color: #1765cc;
            border-color: #1765cc;
        }

        @media (max-width: 600px) {
            .error-code {
                font-size: 72px;
            }

            h1 {
                font-size: 20px;
            }

            .error-message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <h1>Halaman tidak ditemukan</h1>
        <p class="error-message">
            Halaman yang Anda cari tidak dapat ditemukan. Halaman mungkin telah dihapus atau URL mungkin tidak benar.
        </p>
        
        <div class="suggestions">
            <a href="<?= base_url('/') ?>" class="btn-primary">Kembali ke Beranda</a>
            <a href="javascript:history.back()">Kembali ke halaman sebelumnya</a>
        </div>
    </div>
</body>
</html>
