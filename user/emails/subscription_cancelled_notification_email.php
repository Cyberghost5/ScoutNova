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
                            <p>
                                This is to confirm that your subscription has been successfully cancelled. We're sorry to see you go, but we appreciate the time you've spent with us.
                            </p>
                            
                            <p>
                                You will continue to have access to your subscription benefits until the end of your current billing cycle, which is on <strong><?php echo date('F j, Y - g:i A', strtotime($subscription['next_payment_date'])); ?></strong>.
                            </p>

                            <p>
                                If you change your mind, you can always resubscribe at any time by visiting your dashboard.
                            </p>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo $settings['site_url'] ?>login" style="background-color: #1e3a8ae6; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">Go to My Dashboard</a>
                            </div>

                            <p>
                                If you have any questions or need help, our support team is always ready to assist you — just reply to this email.
                            </p>

                            <p style="margin-top: 30px; font-size: 14px; color: #555;">
                                Keep pushing,  
                                <br>
                                The <strong><?php echo $settings['site_name']; ?> Team</strong>
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