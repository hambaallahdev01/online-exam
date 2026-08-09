<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email Address</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; color: #333333;">
    <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 25px;">
            <h2 style="color: #312E81; margin: 0; font-size: 22px;">Ajenono Exam Platform</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Secure Online Assessment System</p>
        </div>

        <h3 style="color: #1e293b; font-size: 18px; margin-bottom: 15px;">Halo {{ $userName }},</h3>
        
        <p style="line-height: 1.6; font-size: 15px; color: #475569;">
            Terima kasih telah mendaftarkan akun di Ajenono Exam Platform. Silakan lakukan verifikasi alamat email Anda untuk mengaktifkan akun dan mulai menggunakan layanan platform.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verifyUrl }}" target="_blank" style="background-color: #312E81; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; font-size: 15px; display: inline-block;">
                Verifikasi Alamat Email
            </a>
        </div>

        <p style="line-height: 1.6; font-size: 13px; color: #64748b;">
            Jika tombol di atas tidak dapat diklik, salin dan tempel tautan berikut ke peramban web Anda:<br>
            <a href="{{ $verifyUrl }}" style="color: #6366F1; word-break: break-all;">{{ $verifyUrl }}</a>
        </p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;">

        <p style="font-size: 12px; color: #94a3b8; text-align: center; margin: 0;">
            Tautan ini berlaku selama 60 menit. Jika Anda tidak pernah merasa mendaftar di Ajenono Exam Platform, Anda dapat mengabaikan email ini.
        </p>
    </div>
</body>
</html>
