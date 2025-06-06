<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: white; border: 1px solid #ccc; padding: 30px; border-radius: 8px;">
        <tr>
            <td>
                <h2 style="color: #2c3e50; margin-bottom: 10px;">Email Verification Code</h2>
                <p style="margin-top: 0;">Hi {{ $user->first_name ?? 'User' }},</p>

                <p>Your one-time password (OTP) is:</p>

                <p style="font-size: 28px; font-weight: bold; color: #333; margin: 20px 0;">{{ $otpCode }}</p>

                <p>This code will expire in <strong>10 minutes</strong>.</p>
                <p>Please do not share this code with anyone.</p>

                <br>
                <p>Thank you for verifying your email!</p>

                <p>Regards,<br>
                <strong>BuyChemJapan</strong></p>

                <hr style="margin-top: 40px; border: none; border-top: 1px solid #ddd;">
                <p style="color: #999; font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
            </td>
        </tr>
    </table>
</body>
</html>
