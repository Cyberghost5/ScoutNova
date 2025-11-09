<!DOCTYPE html>
<html lang='en-US'>

<head>
    <meta content='text/html; charset=utf-8' http-equiv='Content-Type' />
    <title>Pod Rating Update - <?php echo $settings['site_name']; ?></title>
    <meta name='description' content='Pod Rating Notification Email Template.'>
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
                    style='max-width:670px; background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);'>
                    <tr>
                        <td style='height:40px;'>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style='padding:0 35px;'>
                            <h1 style='color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;'>Your Pod Rating Has Been Updated</h1>
                            <p style='font-size:15px; color:#455056; margin:8px 0 0; line-height:24px;'>Your video has been rated and your POD rating has been updated.<br>Below are your current ratings:</p>
                            <span style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                                <p style='color:#455056; font-size:14px;line-height:24px; margin:0 0 10px 0; font-weight: 600;'>Total Score: <span style='color:#1e1e2d; font-weight: 700;'><?php echo $total_score; ?></span></p>
                                <p style='color:#455056; font-size:14px;line-height:24px; margin:0 0 10px 0; font-weight: 600;'>Consistency Index: <span style='color:#1e1e2d; font-weight: 700;'><?php echo $consistency_index_new; ?>%</span></p>
                                <p style='color:#455056; font-size:14px;line-height:24px; margin:0 0 10px 0; font-weight: 600;'>Category: <span style='color:#1e1e2d; font-weight: 700;'><?php echo ucfirst($category); ?></span></p>
                                <!-- <p style='color:#455056; font-size:14px;line-height:24px; margin:0; font-weight: 600;'>AI Confidence: <span style='color:#1e1e2d; font-weight: 700;'><?php echo $ai_confidence; ?></span></p> -->
                            </div>
                            <span style='display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;'></span>
                            <p style='color:#455056; font-size:15px;line-height:24px; margin:0; font-weight: 500;'>Thank you for using our service! Keep up the great work.</p>
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