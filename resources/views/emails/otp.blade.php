<div style="font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd;">
    <h2>Halo, {{ $userName }}!</h2>
    <p>Seseorang mencoba login ke akun Restoran Anda. Gunakan kode OTP di bawah ini untuk memverifikasi:</p>
    <div style="font-size: 24px; font-weight: bold; color: #2d89ef; letter-spacing: 5px;">
        {{ $otp }}
    </div>
    <p>Kode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapapun.</p>
</div>