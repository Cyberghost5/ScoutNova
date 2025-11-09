<!DOCTYPE html>
<html lang='en-US'>

<head>
    <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
    <title>Video Upload Confirmation - <?php echo $settings['site_name']; ?></title>
    <meta name='description' content='Video Upload Notification Email Template.'>
    <style type='text/css'>
        a:hover {text-decoration: underline !important;}
    </style>
</head>

<body marginheight='0' topmargin='0' marginwidth='0' style='margin: 0px; background-color: #f2f3f8;' leftmargin='0'>
    <table cellspacing='0' border='0' cellpadding='0' width='100%' bgcolor='#f2f3f8'
    style='@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;'>
    <tr>
        <td>
            <table style='background-color: #f2f3f8; max-width:670px; margin:0 auto;' width='100%' border='0'
            align='center' cellpadding='0' cellspacing='0'>
            <tr>
                <td style='height:80px;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center;'>
                    <a href='<?php echo $settings['site_url']; ?>' title='logo' target='_blank'>
                        <img width='100' src='<?php echo $settings['site_url']; ?>assets/images/<?php echo $settings['favicon']; ?>'>
                    </a>
                </td>
            </tr>
            <tr>
                <td style='height:20px;'>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table width='95%' border='0' align='center' cellpadding='0' cellspacing='0'
                    style='max-width:670px; background:#fff; border-radius:3px; -webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
                    <tr>
                        <td style='height:40px;'>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style='padding:0 35px;'>
                            <h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Hi <?php echo $user['firstname']; ?>,</h1>
                            <p>We've received your request to verify your scout profile on <strong><?php echo $settings['site_name']; ?></strong>. Our verification team will review your details and documents shortly.
                            </p>
                            <p>
                                You'll receive an update via email once your verification status changes — typically within <strong>24-48 hours</strong>.
                            </p>

                            <p>Here's what happens next:</p>
                            <ul>
                                <li>🕵️‍♂️ Our team reviews your information and supporting files.</li>
                                <li>📩 You'll receive an email once verification is approved or rejected.</li>
                                <li>🔒 Verified scouts get a blue checkmark badge and appear higher in search results.</li>
                            </ul>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo $settings['site_url'] ?>login" style="background-color: #4d4bec; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">Go to My Dashboard</a>
                            </div>

                            <p>
                                Thank you for helping keep our scout community authentic and trustworthy.
                            </p>
                            <p style="margin-top: 30px; font-size: 14px; color: #555;">
                                Best regards, <br>
                                <strong>The <?php echo $settings['site_name']; ?> Verification Team</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='height:40px;'>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style='height:20px;'>&nbsp;</td>
        </tr>
        <tr>
            <td style='text-align:center;'>
                <p style='font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;'>&copy; <?php echo date('Y'); ?> <strong><?php echo $settings['site_name']; ?></strong> </p>
            </td>
        </tr>
        <tr>
            <td style='height:80px;'>&nbsp;</td>
        </tr>
    </table>
</td>
</tr>
</table>
</body>
</html>