<!DOCTYPE html>
<html lang='en-US'>

<head>
    <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
    <title>Player Verification Confirmation - <?php echo $settings['site_name']; ?></title>
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
                            <h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>You're Now Verified!</h1>
                            <p>
                                Great news! Your <?php echo ucfirst($type); ?> profile has been successfully <?php echo $status; ?> by our team at <strong><?php echo $settings['site_name']; ?></strong>.
                            </p>

                            <?php if($status === 'approved'): ?>

                            <?php if($type === 'player'): ?>
                            <p>
                                You'll now see a <strong>verified badge</strong> (blue checkmark) next to your profile name, helping scouts and agents identify you as an authentic and trusted player.
                            </p>
                            <p>Here's what this means for you:</p>
                            <ul>
                                <li>🌍 You'll appear higher in search results for scouts and agents.</li>
                                <li>💬 Scouts can reach out to you directly with greater confidence.</li>
                                <li>🎯 You've earned credibility as a verified athlete on the platform.</li>
                            </ul>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo $settings['site_url'] ?>login" style="background-color: #1e3a8ae6; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">View My Verified Profile</a>
                            </div>
                            <p>
                                Keep building your career — upload more highlights, track your POD, and let your performance speak for you.
                            </p>
                            <p style="margin-top: 30px; font-size: 14px; color: #555;">
                                Congratulations again,  
                                <br><strong>The <?php echo $settings['site_name']; ?> Verification Team</strong>
                            </p>

                            <?php elseif($type === 'scout/agent'): ?>
                            <p>
                                You'll now see a <strong>verified badge</strong> (blue checkmark) next to your profile name, helping players identify you as an authentic and trusted agent.
                            </p>
                            <p>Here's what this means for you:</p>
                            <ul>
                                <li>🌍 You'll appear higher in search results for players.</li>
                                <li>💬 Players can reach out to you directly with greater confidence.</li>
                                <li>🎯 You've earned credibility as a verified agent on the platform.</li>
                            </ul>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo $settings['site_url'] ?>login" style="background-color: #1e3a8ae6; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">View My Verified Profile</a>
                            </div>
                            <p>
                                Keep building your career — connect with more players, manage your clients, and let your professionalism speak for you.
                            </p>
                            <p style="margin-top: 30px; font-size: 14px; color: #555;">
                                Congratulations again,  
                                <br><strong>The <?php echo $settings['site_name']; ?> Verification Team</strong>
                            </p>
                            <?php endif; ?>

                            <?php elseif($status === 'rejected'): ?>
                            <p>
                                Unfortunately, we were unable to verify your profile at this time. Please review the information and documents you submitted to ensure they meet our verification criteria.
                            </p>
                            <p>
                                You can resubmit your verification request by logging into your account and following the verification process again.
                            </p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?php echo $settings['site_url'] ?>login" style="background-color: #1e3a8ae6; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">Resubmit Verification</a>
                            </div>
                            <p style="margin-top: 30px; font-size: 14px; color: #555;">
                                Don't be discouraged — we're here to help you through the process.  
                                <br><strong>The <?php echo $settings['site_name']; ?> Verification Team</strong>
                            </p>
                            <?php endif; ?>

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