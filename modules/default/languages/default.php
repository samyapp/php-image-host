<?php
// this just ensures noone can run this script without going through the application
if( !defined('PIH' ) ) {
    header('HTTP/1.0 404 Not Found');
    exit();
}
/*

PHP Image Host
www.phpace.com/php-image-host

Copyright (c) 2004,2008,2009 Sam Yapp

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/
/**
 * Default translations file
 * Translations are php strings, stored in a php array.
 * Translations are loaded after all other initialization is done and have
 * access to (and can include) the following variables:
 */

$lang = array();

/*
 * Used in multiple files
 */
$lang['of'] = 'of';
$lang['unlimited'] = 'unlimited';
$lang['Back'] = 'Back';
$lang['Images'] = 'Images';
$lang['to'] = 'to';
$lang['Next'] = 'Next';
$lang['Previous'] = 'Previous';
$lang['Image'] = 'Image';
$lang['back to thumbnails'] = 'back to thumbnails';
$lang['by'] = 'by';
$lang['in'] = 'in';
$lang['jpeg, gif and png'] = 'jpeg, gif and png';
$lang['Yes'] = 'Yes';
$lang['Unlimited*'] = 'Unlimited*';
$lang['No'] = 'No';
$lang['Yes - some loss of quality'] = 'Yes - some loss of quality';
$lang['No - no loss of quality'] = 'No - no loss of quality';
$lang['URL added to bottom'] = 'URL added to bottom';
$lang['None'] = 'None';
$lang['Yes - '] = 'Yes - ';
$lang['No - 1 image at a time'] = 'No - 1 image at a time';
$lang[' at once'] = ' at once';
$lang['Username:'] = 'Username:';
$lang['Login'] = 'Login';
$lang['Email:'] = 'Email:';
$lang['Password:'] = 'Password:';
$lang['in gallery'] = 'in gallery';
$lang['Rename'] = 'Rename';
$lang['Resize'] = 'Resize';
$lang['Rotate'] = 'Rotate';
$lang['Page'] = 'Page';
$lang['max'] = 'max';
$lang['Create A New Gallery'] = 'Create A New Gallery';
$lang['Gallery Name:'] = 'Gallery Name:';
$lang['Gallery Introduction:'] = 'Gallery Introduction:';
$lang['(optionally) enter a short introduction to your gallery.'] = '(optionally) enter a short introduction to your gallery.';
$lang['We respect your privacy. We do not store the email addresses submitted via this form.'] = 'We respect your privacy. We do not store the email addresses submitted via this form.';
$lang['Your Name:'] = 'Your Name:';
$lang['Enter the username you chose when you signed up.'] = 'Enter the username you chose when you signed up.';
$lang['No Gallery'] = 'No Gallery';
$lang['Random Images'] = 'Random Images';
$lang['Site News'] = 'Site News';
$lang['Latest Site News'] = 'Latest Site News';
$lang['News Archive'] = 'News Archive';
$lang['read more'] = '...read more';
/*********************
 * account_info.phtml
 *********************/
$lang['mb of'] = 'mb of';
$lang['Account Summary For'] = 'Account Summary For';
$lang['logout'] = 'logout';
$lang['Bandwidth Used:'] = 'Bandwidth Used:';
$lang['Space Used:'] = 'Space Used:';
$lang['Images Uploaded:'] = 'Images Uploaded:';
$lang['Monthly Bandwidth Limit Exceeded!'] = 'Monthly Bandwidth Limit Exceeded!';
$lang['Username'] = 'Username';
$lang['Password'] = 'Password';
$lang['Login'] = 'Login';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'of', 'unlimited'



/*********************
 * browse.phtml
 *********************/
$lang['next'] = 'next';
$lang['by'] = 'by';
$lang['in'] = 'in';
$lang['size'] = 'size';
$lang['Uploaded by'] = 'Uploaded by';
$lang['on'] = 'on';
$lang['in gallery'] = 'in gallery';
$lang['Clickable Thumbnail HTML'] = 'Clickable Thumbnail HTML';
$lang['Thumbnail Forum BB Code'] = 'Thumbnail Forum BB Code';
$lang['Thumbnail URL'] = 'Thumbnail URL';
$lang['Image HTML'] = 'Image HTML';
$lang['Image Forum BB Code'] = 'Image Forum BB Code';
$lang['Image URL'] = 'Image URL';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Back', 'Images', 'to', 'of', 'Next', 'Previous', 'Image', 'back to thumbnails'



/*********************
 * confirm.phtml
 *********************/
$lang['Confirm Your Account'] = 'Confirm Your Account';
$lang['Confirmation ID#:'] = 'Confirmation ID#:';
$lang['Enter the confirmation id included in your sign-up email. If you have lost this email we can'] = 'Enter the confirmation id included in your sign-up email. If you have lost this email we can';
$lang['resend it'] = 'resend it';
$lang['Confirm Account'] = 'Confirm Account';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Username:', 'Enter the username you chose when you signed up.'



/*********************
 * contact.phtml
 *********************/
$lang['Your Email:'] = 'Your Email:';
$lang['Subject:'] = 'Subject:';
$lang['Message:'] = 'Message:';
$lang['Submit Message'] = 'Submit Message';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Your Name:'



/*********************
 * galleries.phtml
 *********************/
$lang['The following errors occurred:'] = 'The following errors occurred:';
$lang['Edit Gallery'] = 'Edit Gallery';
$lang['Update Gallery Details'] = 'Update Gallery Details';
$lang['Manage Your Image Galleries'] = 'Manage Your Image Galleries';
$lang['images'] = 'images';
$lang['Edit'] = 'Edit';
$lang['Delete'] = 'Delete';
$lang['Gallery URL:'] = 'Gallery URL:';
$lang['HTML Link:'] = 'HTML Link:';
$lang['Email The Selected Galleries To Your Friends!'] = 'Email The Selected Galleries To Your Friends!';
$lang['Enter Your Friends Email Address:'] = 'Enter Your Friends Email Address:';
$lang['Enter a single email address to send your selected galleries to in each of the boxes above.'] = 'Enter a single email address to send your selected galleries to in each of the boxes above.';
$lang['You can send '] = 'You can send ';
$lang[' emails at once.'] = ' emails at once.';
$lang['Enter the email address of the friend you want to send these galleries to in the box above.'] = 'Enter the email address of the friend you want to send these galleries to in the box above.';
$lang['Short Message (max 255 characters)'] = 'Short Message (max 255 characters)';
$lang['This message will be included in the email.'] = 'This message will be included in the email.';
$lang['Email The Selected Galleries'] = 'Email The Selected Galleries';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Create A New Gallery', 'Gallery Name:', 'Gallery Introduction:', '(optionally) enter a short introduction to your gallery.', 'We respect your privacy. We do not store the email addresses submitted via this form.'



/*********************
 * images.phtml
 *********************/
$lang['Please correct the following errors:'] = 'Please correct the following errors:';
$lang['You are currently exceeding the uploaded image allowance for your account.'] = 'You are currently exceeding the uploaded image allowance for your account.';
$lang['Your'] = 'Your';
$lang['account allows you to have'] = 'account allows you to have';
$lang['images at a time.'] = 'images at a time.';
$lang['Please delete some images or'] = 'Please delete some images or';
$lang['upgrade your account'] = 'upgrade your account';
$lang['Images in order of'] = 'Images in order of';
$lang['Name (a-z)'] = 'Name (a-z)';
$lang['Name (z-a)'] = 'Name (z-a)';
$lang['Newest first'] = 'Newest first';
$lang['Oldest first'] = 'Oldest first';
$lang['Galleries:'] = 'Galleries:';
$lang['All Images'] = 'All Images';
$lang['Bandwidth:'] = 'Bandwidth:';
$lang['Public (change)'] = 'Public (change)';
$lang['Private (change)'] = 'Private (change)';
$lang['BB Code'] = 'BB Code';
$lang['Clickable Thumb'] = 'Clickable Thumb';
$lang['Uploaded on the'] = 'Uploaded on the';
$lang['In Gallery:'] = 'In Gallery:';
$lang['View'] = 'View';
$lang['Email The Selected Images To Your Friends!'] = 'Email The Selected Images To Your Friends!';
$lang['Friends Email:'] = 'Friends Email:';
$lang['Enter the email address of each friend you want to send these images to in one of the boxes above.'] = 'Enter the email address of each friend you want to send these images to in one of the boxes above.';
$lang['Enter the email address of the friend you want to send these images to.'] = 'Enter the email address of the friend you want to send these images to.';
$lang['We respect your privacy. We do not store the email addresses submitted via this form.'] = 'We respect your privacy. We do not store the email addresses submitted via this form.';
$lang['Short Message'] = 'Short Message';
$lang['characters'] = 'characters';
$lang['Email The Selected Images'] = 'Email The Selected Images';
$lang['Add The Selected Images To One Of Your Galleries'] = 'Add The Selected Images To One Of Your Galleries';
$lang['No Gallery'] = 'No Gallery';
$lang['Manage Galleries'] = 'Manage Galleries';
$lang['Add Selected Images To This Gallery'] = 'Add Selected Images To This Gallery';
$lang['Remove The Selected Images From Your Account'] = 'Remove The Selected Images From Your Account';
$lang['Delete The Selected Images'] = 'Delete The Selected Images';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Images', 'to', 'of', 'in gallery', 'Page', 'Previous', 'Next', 'Rename', 'Resize', 'Rotate', 'max'



/*********************
 * index.phtml
 *********************/
$lang['uploaded by'] = 'uploaded by';
$lang['Latest Images'] = 'Latest Images';
$lang['more images...'] = 'more images...';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Random Images'



/*********************
 * join.phtml
 *********************/
$lang['Create An Account'] = 'Create An Account';
$lang['Your Name:'] = 'Your Name:';
$lang['Enter the name you wish to be addressed by in any correspondence with this site.'] = 'Enter the name you wish to be addressed by in any correspondence with this site.';
$lang['Choose a username. Your username must be between 6 and 20 characters long and can only contain alphanumeric characters (a-z and 0-9).'] = 'Choose a username. Your username must be between 6 and 20 characters long and can only contain alphanumeric characters (a-z and 0-9).';
$lang['Enter your email address. If you forget your password, we can send a reminder to this email address.'] = 'Enter your email address. If you forget your password, we can send a reminder to this email address.';
$lang['We will send a confirmation email to this address containing instructions on how to finish creating your account.'] = 'We will send a confirmation email to this address containing instructions on how to finish creating your account.';
$lang['Repeat Email:'] = 'Repeat Email:';
$lang['Please repeat your email address here to confirm you have entered it correctly.'] = 'Please repeat your email address here to confirm you have entered it correctly.';
$lang['Choose a password to use when logging in to this site. Your password must be between 6 and 20 characters long and contain only alphanumeric characters (a-z and 0-9).'] = 'Choose a password to use when logging in to this site. Your password must be between 6 and 20 characters long and contain only alphanumeric characters (a-z and 0-9).';
$lang['Repeat Password:'] = 'Repeat Password:';
$lang['Please repeat the password you have chosen to confirm its spelling.'] = 'Please repeat the password you have chosen to confirm its spelling.';
$lang['I have read and agree to the '] = 'I have read and agree to the ';
$lang['terms and conditions'] = 'terms and conditions';
$lang['of this site.'] = 'of this site.';
$lang['Create Account'] = 'Create Account';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Username:', 'Email:', 'Password:'



/*********************
 * layout.phtml
 *********************/
$lang['Home'] = 'Home';
$lang['Browse Images'] = 'Browse';
$lang['Sign Up'] = 'Join';
$lang['My Images'] = 'My Images';
$lang['My Galleries'] = 'My Galleries';
$lang['Upload'] = 'Upload';
$lang['Upgrade'] = 'Upgrade';
$lang['The following error occurred:'] = 'The following error occurred:';
$lang['Faq'] = 'Faq';
$lang['Contact'] = 'Contact';
$lang['Terms'] = 'Terms';
$lang['Privacy Policy'] = 'Privacy Policy';
$lang['All content copyright'] = 'All content copyright';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Random Images', 'Login'



/*********************
 * login.phtml
 *********************/
$lang['Password:'] = 'Password:';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Login', 'Username:'



/*********************
 * plans.phtml
 *********************/
$lang['Non-members'] = 'Non-members';
$lang['Features'] = 'Features';
$lang['Supported image formats'] = 'Supported image formats';
$lang['Browse your uploaded images'] = 'Browse your uploaded images';
$lang['Image thumbnails?'] = 'Image thumbnails?';
$lang['Number of images allowed'] = 'Number of images allowed';
$lang['Storage Space'] = 'Storage Space';
$lang['Monthly Bandwidth'] = 'Monthly Bandwidth';
$lang['Maximum upload file size'] = 'Maximum upload file size';
$lang['Maximum image dimensions'] = 'Maximum image dimensions';
$lang['Auto-resize oversized images?'] = 'Auto-resize oversized images?';
$lang['All images converted to jpegs?'] = 'All images converted to jpegs?';
$lang['Branding added on to images'] = 'Branding added on to images';
$lang['Simultaneous uploads?'] = 'Simultaneous uploads?';
$lang['Rename Your Images?'] = 'Rename Your Images?';
$lang['Resize Your Images?'] = 'Resize Your Images?';
$lang['Rotate Your Images?'] = 'Rotate Your Images?';
$lang['Image Galleries?'] = 'Image Galleries?';
$lang['Email images to your friends'] = 'Email images to your friends';
$lang['Price'] = 'Price';
$lang['FREE'] = 'FREE';
$lang['Join Now!'] = 'Join Now!';
$lang['Upgrade Now'] = 'Upgrade Now';
$lang['Other limitations (bandwidth, storage space, etc, still apply).'] = 'Other limitations (bandwidth, storage space, etc, still apply).';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'jpeg, gif and png', 'Yes', 'Unlimited*', 'No', 'Yes - some loss of quality', 'No - no loss of quality', 'URL added to bottom', 'None', 'Yes - ', 'No - 1 image at a time', ' at once'



/*********************
 * random.phtml
 *********************/
$lang['Random Images'] = 'Random Images';
$lang['Get More Images'] = 'Get More Images';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'by', 'in'



/*********************
 * remind.phtml
 *********************/
$lang['Request Password Reminder'] = 'Request Password Reminder';
$lang['Either'] = 'Either';
$lang['Enter the username you chose when you signed up.'] = 'Enter the username you chose when you signed up.';
$lang['Or'] = 'Or';
$lang['Email:'] = 'Email:';
$lang['Enter the email address you entered when you signed up.'] = 'Enter the email address you entered when you signed up.';
$lang['Send Password Reminder'] = 'Send Password Reminder';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'Username:'



/*********************
 * rename.phtml
 *********************/
$lang['Rename'] = 'Rename';
$lang['Rename Image'] = 'Rename Image';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'to'



/*********************
 * resize.phtml
 *********************/
$lang['Resize'] = 'Resize';
$lang['New Size:'] = 'New Size:';
$lang['max'] = 'max';
$lang['Resize Image'] = 'Resize Image';
$lang['save resized image as a new image?'] = 'save resized image as a new image?';
$lang['tip!'] = 'tip!';
$lang['Leave either the width or height as 0 to have the image resized proportionally.'] = 'Leave either the width or height as 0 to have the image resized proportionally.';


/*********************
 * rotate.phtml
 *********************/
$lang['Rotate'] = 'Rotate';
$lang['Clockwise'] = 'Clockwise';
$lang['Anti-clockwise'] = 'Anti-clockwise';
$lang['Rotate Image'] = 'Rotate Image';


/*********************
 * upgrade.phtml
 *********************/
$lang['Upgrade To A '] = 'Upgrade To A ';
$lang['Account'] = 'Account';
$lang['Username:'] = 'Username:';
$lang['1 Month'] = '1 Month';
$lang['3 Months @'] = '3 Months @';
$lang['6 Months'] = '6 Months';
$lang['12 Months:'] = '12 Months:';


/*********************
 * upload.phtml
 *********************/
$lang['The following image(s) were successfully uploaded:'] = 'The following image(s) were successfully uploaded:';
$lang['click for full size'] = 'click for full size';
$lang['Upload Images From Your PC'] = 'Upload Images From Your PC';
$lang['Click the "Browse" button to select an image to upload from your PC.'] = 'Click the "Browse" button to select an image to upload from your PC.';
$lang['You can (optionally) enter a new name for your image. You will also be able to rename your images after you have uploaded them.'] = 'You can (optionally) enter a new name for your image. You will also be able to rename your images after you have uploaded them.';
$lang['Name:'] = 'Name:';
$lang['Add Uploaded Images To Gallery:'] = 'Add Uploaded Images To Gallery:';
$lang['You can (optionally) select one of your galleries to add these images to.'] = 'You can (optionally) select one of your galleries to add these images to.';
$lang['Make these images public? (available when browsing images)'] = 'Make these images public? (available when browsing images)';
$lang['Uploading - Please Wait...'] = 'Uploading - Please Wait...';
$lang['Upload Images'] = 'Upload Images';
// The following tokens are used in this and other templates
// and are defined at the top of this file.
// 'No Gallery'



/*
 *Content to display on the home page of the site.
*/
$lang['Home Page Content'] = <<<EOF
<h3>Welcome to {$this->config->sitename}!</h3>
<p>
We offer both free and paid hosting for your images and photographs, whether for sharing with friends,
 adding to auction listings or other online use.
</p>
<p>
You can start uploading immediately after you <a href="{$urls['join']}">create your free account</a>,
 and only <a href="{$urls['upgrade']}">upgrade to our paid image hosting</a> service if you require the extra 
features.
</p>
<p>

With either a free or paid account you can upload any of the three most popular web image formats
 (jpeg, png and gif). A brief summary of the features that our free and paid image hosting memberships 
offer is shown below.
</p>
<p>
If you have any further questions, please see our <a href="{$urls['faq']}">frequently asked questions</a>, 
<a href="{$urls['terms']}">terms &amp; conditions</a> and <a href="{$urls['privacy-policy']}">privacy policy</a> pages.
 If your query is not answered by these, please feel free to <a href="{$urls['contact']}">contact us directly</a>.
</p>

EOF;

/*
 *Displayed on the login, signup and confirm pages if the user is logged in.
*/
$lang['Logged In Content'] = <<<EOF
<h3>Welcome back, {$username}!</h3>
<p>
You currently have {$user_images} images uploaded to your {$sitename} {$user_type_name} account. 
You can upload a maximum of {$user_max_images} images to your {$user_storage}mb of storage space.
You can view your images on <a href="{$urls['images']}">this page</a>, 
and upload more images <a href="{$urls['upload']}">here</a>.
</p>

<p>
Please ensure you are familiar with our <a href="{$urls['terms']}">Terms &amp; Conditions</a> and 

<a href="{$urls['privacy-policy']}">Privacy Policy</a>.
</p>
<p>
If you have any questions about your {$sitename} account, please check our 
<a href="{$urls['faq']}">Frequently Asked Questions</a> page. 
If you cannot find an answer to your question there, you can contact us via 
<a href="{$urls['contact']}">this form</a>.</p><p>Thank-you for hosting your images with {$sitename}.
</p>
EOF;

/*
 *The terms and conditions page for your site.
*/
$lang['Terms Content'] = <<<EOF
<h3>{$sitename} Terms &amp; Conditions of Use</h3>

<p>
{$sitename} provides a free and paid image and photo hosting service. 
Users of this service may upload as many digital images as they wish (withing the limits of their accounts) 
and can then let others view the images by letting them know the url of the image.
</p>
<p>
You are authorized to sign up for and use 1 (one) free account only. 
Any members suspected of creating more than 1 (one) free account may have their accounts deleted and 
all images removed at any time.
</p>
<p>
This service is offered to those looking for personal image hosting. If any of your images uses excessive bandwidth we may delete the image from our servers with no prior notice and, at our discretion, suspend or terminate your account.
</p>
<p>
All images are the copyright of their respective owners. You may not use any of the images on this site for any purpose without the permission of the copyright holder.
</p>
<p>
You <strong>must</strong> own the copyright, or have permission from the copyright holder for any images
 you upload to this website.

</p>
<p>
You <strong>must not</strong> upload any images containing material that is of an adult nature, illegal, defamatory, promotes racism, hatred or discrimination.
</p>
<p>
Any images found to break our terms and conditions will be removed with no warning. 
Anyone who uploads an image that violates our terms may be banned from using this site with no prior notice.
</p>
<p>
In the case of violation of our terms, we may share any and all information we hold on the user concerned with any interested 3rd parties.
</p>
<p>
If you believe one of our members has uploaded images to our site that break our terms and conditions please 
<a href="{$urls['contact']}">contact us</a> so we can take approriate action.

</p>
<p>
Please read our <a href="{$urls['privacy-policy']}">privacy policy</a> and <a href="{$urls['faq']}">frequently asked questions</a> for further information.
</p>
EOF;

/*
 *The privacy policy for your site.
*/
$lang['Privacy Policy Content'] = <<<EOF
<h3>{$sitename} Privacy Policy</h3>
<p>
This is the web site of <b>{$sitename}</b>.

</p>
<p>
If you want to contact us, please do so via the form on our <a href="{$urls['contact']}">contact page</a>.
</p>
<p>
For each visitor to our Web page, our Web server automatically recognizes your ip address.
 </p>
<p>
We collect aggregate information on what pages consumers access or visit and any information
 you submit to us.
</p>
<p>
The information we collect is used to improve the content of our Web page, used to customize the content 
and/or layout of our page for each individual visitor, and is not shared with other organizations 
for commercial or other purposes, 
with the sole exception of cases where a user of our services breaks our terms &amp; conditions. 
Under these circumstances, we may share your information with relevant law 
enforcement agencies and other relevant 3rd parties.
</p>

<p>
With respect to cookies: We use cookies to store visitors preferences,
 customize Web page content based on visitors' browser type or other information that the 
visitor sends and store your upload settings.
</p>
<p>
With respect to Ad Servers: To try and bring you offers that are of interest to you, we have relationships 
with other companies that we allow to place ads on our Web pages. 
As a result of your visit to our site, ad server companies may collect information such as your domain type,
 your IP address and clickstream information.
 For further information, consult the privacy policies of the companies advertising on our site. 
</p>
<p>
From time to time, we may use customer information for new, unanticipated uses not previously 
disclosed in our privacy notice. If our information practices change at some time in the future we 
will use for these new purposes only data collected from the time of the policy change forward .
</p>
<p>
Upon request we provide site visitors with access to all information that we maintain about them, 
including contact information (e.g., name, address, phone number).
</p>
<p>
Consumers can access this information by contacting us through our <a href="{$urls['contact']}">contact page</a>.
 </p>

<p>
If you feel that this site is not following its stated information policy, or have any other questions
 regarding the operation of this site, please contact or us via the form on our
 <a href="{$urls['contact']}">contact page</a>.
</p>
EOF;

/*
 *Content for the password reminder page.
*/
$lang['Remind Content'] = <<<EOF
<h3>Forgotten Your Password?</h3>
<p>
If you have forgotten the login details for your {$sitename} image hosting account, please enter either your 
username <strong>or</strong> the email address you signed up with in the form below
 and we will send you a reminder.
</p>
<p>

The reminder will be sent to the email address you used when you opened your image hosting account.
</p>
EOF;

/*
 *Displayed when the user submits the password reminder form.
*/
$lang['Reminder Sent Content'] = <<<EOF
<h3>Password Reminder Sent</h3>
<p>
An email containing your {$sitename} login details has been sent to your email address. 
You should receive this email within the next 10 minutes.
</p>
<p>
Once you receive your username and password you can login <a href="{$urls['login']}">here</a>.
</p>
EOF;

/*
 *Displayed at the top of the login page.
*/
$lang['Login Content'] = <<<EOF
<h3>Login</h3>

<p>
Enter your username and password and click the &quot;Login&quot; button to login to your account.
</p>
<p>
If you do not yet have a {$sitename} account, you can <a href="{$urls['join']}">create one for free</a>.
</p>
<p>
If you have forgotten your login details we can <a href="{$urls['remind']}">email you a reminder</a>.
</p>
EOF;

/*
 *Message displayed if the user's account has been suspended.
*/
$lang['Suspended Message'] = <<<EOF
<p><strong>Your Account Has Been Suspended</strong></p>

<p>
Your {$sitename} image hosting account is currently suspended.
</p>
<p>
Please ensure you have read and agree to our <a href="{$urls['terms']}">terms &amp; conditions</a> and 
our <a href="{$urls['privacy-policy']}">privacy policy</a>.
 If you have any questions about your account that are not answered on our 
<a href="{$urls['faq']}">frequently asked questions page</a> then please <a href="{$urls['contact']}">contact us</a>.

</p>
EOF;

/*
 *Displayed on login attempt if account not confirmed.
*/
$lang['Unconfirmed Message'] = <<<EOF
<p><strong>You Must Confirm Your Email Address</strong></p>
<p>
You have not yet confirmed the email address you entered when you created your account. 
We require this for security purposes. 
An email was sent to your signup email address containing instructions on how to confirm it.
</p>
<p>
If you did not receive this email, or if you have deleted it, you can request a <a href="{$urls['remind']}">copy here</a>.</p>
EOF;

/*
 *Displayed at the top of the member's images page.
*/
$lang['Images Content'] = <<<EOF
<h3>My Images</h3>

<p>
All the images you have uploaded to this site are listed below.
 To view an image, click on the thumbnail or the image name.
</p>
<p>
If you want to share an image with other people on the internet, copy the code from one of the three boxes 
next to each image, depending on how you want to share the image:
</p>
<ul>
<li><strong>URL</strong> - Copy the text in the url field if all you need is a url to give to people.</li>
<li><strong>HTML</strong> - Copy the text in the html field if you want to link to the image from a page on another 
website.</li>
<li><strong>BB Code</strong> - Copy the text from the BB Code field if you want to include the image in a
 message on a website discussion forum that uses standard bulletin board codes 
(forums running phpbb, Invisionboard or VBulletin support this format of linking to images).</li>

</ul>
<p>You can also easily email links to your images to your friends by selecting the checkboxes next to each
 image you want to share submitting the email form at the bottom of this page.
 Just enter their email address and a short message and click the &quot;Send Email&quot; button.
</p>
<p>You can upload more images on the <a href="{$urls['upload']}">upload page</a>.</p>
EOF;

/*
 *Frequently asked questions page.
*/
$lang['Faq Content'] = <<<EOF
<h3>{$sitename} Frequently Asked Questions</h3>
<p>
This page lists common questions and answers about the {$sitename} service. If your question is not answered here, please use our <a href="{$urls['contact']}">contact form</a> to get in touch.

</p>
<h3>Account Features</h3>
<p><strong>What image formats are supported?</strong></p>
<p>
{$sitename} supports images uploaded in jpeg, gif and png format.
</p>
<p><strong>What is the maximum allowed size for uploaded images?</strong></p>
<p>The maximum filesize you can upload depends on whether you have a paid or free account, as does the
 maximum width and height allowed for uploaded images. Images with a larger filesize than the allowed maximum
 will be rejected. Images with larger widths and heights than your account allows may be automatically resized depending on your account.
</p>
<p>
You can view the maximum sizes allowed on our <a href="{$urls['index']}">home page</a>.
</p>

<p><strong>How many images can I upload?</strong></p>
<p>
The number of images you can upload depends on the type of account you have. Please see our account 
comparison table on the <a href="{$urls['index']}">{$sitename} home page</a> for further details. 
Note that there is no limit on the number of uploads you can do, just on the number of images stored in your
 account at any one time. This limit may be set by the storage space we give you, or a set maximum number of images. You can delete images from your account at any time to make room for new 
ones.
</p>
<p><strong>Where can I compare the features for free and paid accounts?</strong></p>
<p>
To view and compare all the features of our free and paid accounts, please see the table on 
our <a href="{$urls['index']}">home page</a>.</p>
<p><strong>Can I link to my images when I have uploaded them?</strong></p>

<p>
Yes. We allow you to link directly to your images once they are uploaded. We provide you with url to each
 image, as well as html code to link to it, and bbcode compatible with the most commonly found forums.
</p>
<p><strong>Can I view / delete / access my images after they are uploaded?</strong></p>
<p>
Yes. Once you login to your account you can browse a list of all your uploaded images together with 
a thumbnail preview of each image. From here you can find the url to each image, delete images you no
 longer need, and email links to the images to your friends.
</p>
<p><strong>Are there any types of image that are not allowed on {$sitename}?</strong></p>
<p>
Yes. Any images that contravene our <a href="{$urls['terms']}">terms and conditions</a> may not be uploaded 
to your account. We reserve the right to delete without any notice any images which we believe violate 
our <a href="{$urls['terms']}">terms and conditions</a>, and to suspend or terminate the accounts of any 
members who upload images that violate our <a href="{$urls['terms']}">terms</a>.

</p>
<h3>Membership</h3>
<p><strong>How do I sign up for an account?</strong></p>
<p>
You can sign up for a free account on the <a href="{$urls['join']}">sign up</a> page. 
You need to choose a username and password that you will use to upload images to your account.
 We also require you to enter a valid email address when you join. We send a confirmation email to this 
address with instructions on how to activate your account. 
</p>
<p><strong>Can I sign up for more than 1 account?</strong></p>
<p>
Free accounts are limited to 1 (one) per individual. If we catch anyone abusing this limit, we may delete all their 
accounts without any notice.
</p>
<p><strong>How do I access my account?</strong></p>

<p>
Once you have signed up and confirmed your account, you can login to it on the 
<a href="{$urls['login']}">login</a> page. Whilst logged in to your account you can 
<a href="{$urls['upload']}">upload images</a>, <a href="{$urls['images']}">browse the images</a> you have already
 uploaded, get the urls required to view or link to the images, email links to the images to your friends, 
and delete any of your images.
</p>
<p><strong>Help! I've forgotten my username or password.</strong></p>
<p>
If you forget your login details for your {$sitename} account we can send a reminder to the email address
 you registered with us when you created your account. Just follow the instructions on the
 <a href="{$urls['remind']}">password reminder</a> page.

</p>
<p><strong>How do I upgrade to a {$paid_account_name} account?</strong></p>
<p>
If you have outgrown the features provided by our {$free_account_name} account, you can upgrade to a 
paid account on the <a href="{$urls['upgrade']}">upgrade</a> page.
 A {$paid_account_name} costs \${$paid_account_cost} per month (discounts may be available if you pay for several months at a time). Payments are accepted through
 <a href="https://www.paypal.com/" target="_blank">PayPal</a>, and in most cases your account 
will be upgraded automatically after payment.
</p>
<h3>Unanswered Questions</h3>
<p>
If you have any further questions not answered in this document, please 
<a href="{$urls['contact']}">contact us</a> directly.

</p>
EOF;

/*
 *Message displayed at the top of the sign up page.
*/
$lang['Sign Up Content'] = <<<EOF
<h3>Open A Free Account</h3>
<p>
Our {$sitename} {$free_account_name} account is 100% free. Please fill in your details in the following form to
 create your image hosting account.
</p>
<p>
All fields are <strong>required</strong>.
 Please ensure the information you enter is accurate.
</p>
<p>
Please ensure you have read and agree to our <a href="{$urls['terms']}">terms &amp; conditions</a> 

and our <a href="{$urls['privacy-policy']}">privacy policy</a>. 
If you have any questions about {$sitename} that are not answered on our 
<a href="{$urls['faq']}">frequently asked questions page</a> then please <a href="{$urls['contact']}">contact us</a>.
</p>
EOF;

/*
 *Displayed to new members when the sign up process is complete.
*/
$lang['Signed Up - Complete Content'] = <<<EOF
<h3>Your Account Is Now Active</h3>
<p>
Thank you for joining {$sitename}. Your {$free_account_name} image hosting account has been created and is now active.
 You can login to your account <a href="{$urls['login']}">here</a>.

</p>
<p>
Please ensure you have read and agree to our <a href="{$urls['terms']}">terms &amp; conditions</a>
 and our <a href="{$urls['privacy-policy']}">privacy policy</a>. 
If you have any questions about {$sitename} that are not answered on our 
<a href="{$urls['faq']}">frequently asked questions page</a> then please <a href="{$urls['contact']}">contact us</a>.
</p>

EOF;

/*
 *Displayed when a member signs up but needs to confirm their email address.
*/
$lang['Signed Up - Unconfirmed Content'] = <<<EOF
<h3>Please Confirm Your Account</h3>
<p>
Thank you for signing up for our {$free_account_name} image hosting account.
 Before you can login to?your account you must confirm that the email address you entered when you signed 
up is valid.
</p>
<p>
We have sent an email to this address containing instructions on how to finish setting up your account.
</p>
EOF;

/*
 *Displayed at the top of the confirm email address page.
*/
$lang['Confirm Content'] = <<<EOF
<h3>Confirm Your Email Address</h3>
<p>

Before you can login to your {$sitename} account, we require you to confirm that the email address you gave when you signed up is real.
</p>
<p>
An email has been sent to this address with instructions on how to confirm your account. If you have not received, or have deleted this email message you can <a href="{$urls['remind']}">request a reminder</a>.
</p>
EOF;

/*
 *Displayed when a member completes the email confirmation stage of signup.
*/
$lang['Email Confirmed Content'] = <<<EOF
<h3>Your Email Address Has Been Confirmed!</h3>
<p>Thank you for confirming your email address. Your account is now <a href="{$urls['login']}">ready to use</a>.</p>
EOF;

/*
 *Displayed at the top of the contact form page.
*/
$lang['Contact Content'] = <<<EOF
<h3>Contact Us</h3>

<p>
Please use the form below if you have any comments, suggestions or other feedback regarding {$sitename}.
</p>
<p>
Please also use this form to report any problems with your {$sitename} account. You may also find our <a href="{$urls['faq']}">frequently asked questions</a> page provides an answer to your query.
</p>
<p><strong>Reporting Abuse</strong></p>
<p>
We take all reports of abuse of our service seriously. If you have information that our service is being used to illegally host copyrighted, adult, or any other?images that breach our <a href="{$urls['terms']}">terms &amp; conditions</a> please use this form to report them, including full details, <strong>especially the url of the offending image.</strong>

</p>
<p><strong>Please ensure that you enter your email address correctly.</strong></p>
<p>
Otherwise we will be unable to respond to your query. We will not share your email address with any other parties. You can view our <a href="{$urls['privacy-policy']}">Privacy Policy</a> for further information.
</p>
EOF;

/*
 *Displayed when someone submits the contact form.
*/
$lang['Contact Submitted Content'] = <<<EOF
<h3>Your Message Has Been Sent</h3>
<p>
Thank-you for contacting us at {$sitename}. Your feedback is important to us.
</p>

<p>
We aim to respond to all queries within 48 hours.</p><p>Best Regards,
</p>
<p>The {$sitename} Team. </p>
EOF;

/*
 *Displayed at the top of the upload images page.
*/
$lang['Upload Content'] = <<<EOF
<h3>Upload Your Images</h3>
<p>
Use the form below to upload your images to {$sitename}. You may upload {$user_simultaneous_uploads} images at a time using this form.
</p>
<p>
We accept images in jpeg, gif and png format. The maximum filesize we allow is {$user_max_upload_size}k. Images larger than {$user_max_image_width}x{$user_max_image_height} pixels will be resized to fit to this size.
</p>

<p>
You currently have {$user_images} out of a maximum of {$user_max_images} images uploaded. You can manage these images <a href="{$urls['images']}">here</a>.
</p>
<p>You have currently used {$user_bandwidth_used} mb out of your allowed {$user_bandwidth}mb of bandwidth this month, and are currently using {$user_storage_used}mb of your allowed {$user_storage}mb  space.</p>
<p>
If you have any questions about uploading your images and photographs, please read our
 <a href="{$urls['faq']}">frequently asked questions</a>, <a href="{$urls['terms']}">terms &amp; conditions</a> and <a href="{$urls['privacy-policy']}">privacy policy</a>. Alternatively, you can <a href="{$urls['contact']}">contact us directly</a>.

</p>
EOF;

/*
 *Displayed at the top of the upload images page.
*/
$lang['Upload Zip Content'] = <<<EOF
<h3>Upload a zip archive of images</h3>
<p>
Use the form below to upload your images in a zip archive.
</p>
EOF;

/*
 *Displayed on pages requiring login if user is not logged in.
*/
$lang['Not Logged In Content'] = <<<EOF
<p><strong>You Must Be Logged In To Access This Page</strong></p>
<p>
You can login to your account by entering your username and password in the form below.
</p>
<p>
If you do not have an account with {$sitename} you can <a href="{$urls['join']}">create one for free</a>. 
If you have forgotten your username or password, we can <a href="{$urls['remind']}">email you a reminder</a>.
</p>
EOF;

/*
 *Displayed on the images page when the user has not uploaded any images.
*/
$lang['No Images Content'] = <<<EOF
<p><strong>No images found matching your criteria.</strong></p>

EOF;

/*
 *Displayed on the signup page if you uncheck the "allow signups" option on the settings page.
*/
$lang['No New Members Content'] = <<<EOF
<h3>Sign-Up Disabled</h3>
<p>{$sitename} is not currently accepting any new members. Please try again at later date.</p>
EOF;

/*
 *Message displayed on the upload page when a member has reached their upload limit.
*/
$lang['Image Limit Reached Content'] = <<<EOF
<p><strong>Maximum Upload Limit Reached</strong></p>
<p>
You have reached your account image limit ({$user_max_images} images, {$user_storage}mb of storage). 
If you want to upload any more images, you must:
</p>
<ul><li><strong>Either</strong> delete some of your <a href="{$urls['images']}">existing images</a> 

(select the checkboxes next to the images to delete and click the &quot;Delete Images&quot; button).</li>
<li><strong>Or</strong> <a href="{$urls['upgrade']}">upgrade your account</a> for more storage space.
 If your account is already a {$paid_type_name}, you may <a href="{$urls['join']}">sign up for new one</a>
 for extra storage space.</li></ul>

<p>If you have any questions about this limit or our policies in general, please <a href="{$urls['contact']}">contact us</a>.</p>

EOF;

/*
 *Displayed on the upgrade.php page when the user is not logged in.
*/
$lang['Upgrade - Not Logged In Content'] = <<<EOF
<p><strong>Please Login or Create A Free Account</strong></p>
<p>
In order to upgrade to a {$paid_account_name} account, you must first have a free account.
</p>
<p>
If you have already created an account and confirmed your email address, please <a href="{$urls['login']}">log in to your account</a> and return to this page for upgrade instructions.
</p>
<p>
If you do not have an account with us, please <a href="{$urls['join']}">sign up for a free account</a>, follow the instructions to confirm your account and then return to this page if you need to upgrade it.

</p>
<p>
For further information about this site please see our <a href="{$urls['faq']}">frequently asked questions</a>, 
<a href="{$urls['terms']}">terms &amp; conditions</a>, 
and <a href="{$urls['privacy-policy']}">privacy policy</a> pages. You can contact us using the form <a href="{$urls['contact']}">on this page</a>.
</p>
EOF;

/*
 *Intro text on the upgrade account page.
*/
$lang['Upgrade Content'] = <<<EOF
<h3>Upgrade Your Account</h3>

<p>
The {$sitename} {$paid_account_name} account gives you access to extra image storage, larger file size limits and other extra features.
</p>
<p>
A {$paid_account_name} account costs \${$paid_account_cost} per month. Payments are processed by <a target="_blank" href="http://www.paypal.com/">PayPal</a> and are recurring. Options for paying every 1, 3, 6 or 12 months are available below.
</p>
EOF;

/*
 *Message displayed on the upgrade page to members who are logged in and currently on the free plan.
*/
$lang['Upgrade - Logged In Content'] = <<<EOF
<p><strong>Upgrade Now!</strong></p>
<p>
Upgrading your account is a simple process that usually takes just a few minutes.
</p>

<p>
Just check that the information we have listed for your account below is correct, then click the subscribe button below that corresponds with the period of time you wish to upgrade for.
</p>
<p>
Payments will recur whenever due. You can cancel your subscription payments at any time from within your <a href="http://www.paypal.com/">PayPal</a> account.
</p>
EOF;

/*
 *Message displayed on the upgrade account page if the member has already upgraded.
*/
$lang['Upgrade - Already Upgraded Content'] = <<<EOF
<p><strong>You Already Have A {$paid_account_name} Account</strong></p>
<p>
Your account has already been upgraded.
</p>

EOF;

/*
 *Content displayed on the images.php page if the user is not logged in.
*/
$lang['Images - Not Logged In Content'] = <<<EOF
<p><strong>My Images</strong></p>
<p>
All the images you have uploaded to this site are listed below. 
To view an image, click on the thumbnail or the image name.
</p>
<p>
If you want to share an image with other people on the internet, copy the code from one of the three
 boxes next to each image, depending on how you want to share the image:
</p>
<ul><li><strong>URL</strong> - Copy the text in the url field if all you need is a url to give to people. 
</li>
<li><strong>HTML</strong> - Copy the text in the html field if you want to link to the image from a page on 
another website. </li>

<li><strong>BB Code</strong> - Copy the text from the BB Code field if you want to include the image in a 
message on a website discussion forum that uses standard bulletin board codes (forums running phpbb, 
Invisionboard or VBulletin support this format of linking to images).</li></ul>
<p>
You can also easily email links to your images to your friends by selecting the checkboxes next to each image you 
want to share submitting the email form at the bottom of this page. Just enter their email address and a short message and click the &quot;Send Email&quot; button.
</p>
EOF;

/*
 *Content displayed on the upload.php page if the user is not logged in.
*/
$lang['Upload - Not Logged In Content'] = <<<EOF
<p><strong>Upload Your Images</strong></p>
<p>
Use the form below to upload your images to {$sitename}. You may upload {$user_simultaneous_uploads} images at a time using this form.

</p>
<p>
We accept images in jpeg, gif and png format. The maximum filesize we allow is {$user_max_upload_size} bytes. Images larger than {$user_max_image_width}x{$user_max_image_height} pixels will be resized to fit to this size.
</p>
<p>
If you have any questions about uploading your images and photographs, please read our <a href="{$urls['faq']}">frequently asked questions</a>, <a href="{$urls['terms']}">terms &amp; conditions</a> and privacy policy. Alternatively, you can contact us directly.
</p>
EOF;

/*
 *Displayed on the upload.php page if admin disables uploads.
*/
$lang['Uploads Suspended Content'] = <<<EOF
<h3>Image Uploads Temporarily Suspended</h3>

<p>
For technical reasons {$sitename} has temporarily suspended all image uploading.
</p>
<p>{$sitename} apologises for any inconvenience this may cause.</p>
EOF;

/*
 *Displayed when someone accesses certain pages and their ip has been banned.
*/
$lang['IP Banned Content'] = <<<EOF
<h3>Access Denied</h3>
<p>Access to the features offered on this page have been denied because your I.P. address has been involved in abuse of our services.
</p>
<p>
If you believe this is an error on our part, please <a href="{$urls['contact']}">contact us</a>.
</p>

EOF;

/*
 *displayed at the top of the "Resize Image" page.
*/
$lang['Resize Image Content'] = <<<EOF
<h3>Resize Image</h3>
<p>Use the form below to resize your image. The new image can be up to {$user_max_image_width}x{$user_max_image_height} pixels.</p>
<p>You can ensure that the image is resized proportionally by only entering a value for <b>either</b> the width <b>or</b> the height.</p>
<p>To save the resized image as a copy of the original instead of overwriting the original, check the box at the bottom of the form.</p>

EOF;

/*
 *Displayed when an image has been resized.
*/
$lang['Image Resized Content'] = <<<EOF
<h3>Image Resized</h3>
<p>Your image has been resized. To view this image or to resize another image, <a href="{$urls['images']}">click here to return to your images page</a>.</p>
EOF;

/*
 *Displayed on the resize.php page if the user is not allowed to resize images.
*/
$lang['Cannot Resize Content'] = <<<EOF
<h3>Sorry, your account does not allow image resizing.</h3>
<p>If you would like to be able to resize your images, please <a href="{$urls['upgrade']}">upgrade your account.</a></p>

EOF;

/*
 *Displayed on the rename.php page if the user is not allowed to rename images.
*/
$lang['Cannot Rename Content'] = <<<EOF
<h3>Sorry, your account does not allow you to rename image.</h3>
<p>If you would like to be able to rename your images, please <a href="{$urls['upgrade']}">upgrade your account.</a></p>
EOF;

/*
 *displayed at the top of the rename.php page.
*/
$lang['Rename Image Content'] = <<<EOF
<h3>Rename Image</h3>
<p>Enter a new name for this image. The name must be unique, and can contain any alphanumeric character, plus the characters "-" and "_". The name must begin and end with a number or letter.</p>
<p>You do not need to include the file extension (eg., jpg, png, etc) in the new name. This will be added automatically.</p>

EOF;

/*
 *Displayed when a user has renamed an image.
*/
$lang['Image Renamed Content'] = <<<EOF
<h3>Image Renamed</h3>
<p>Your image has been renamed. To rename another image, select the rename option on your <a href="{$urls['images']}">images</a> page.</p>
EOF;

/*
 *Displayed at the top of the galleries.php page.
*/
$lang['Galleries Content'] = <<<EOF
<h3>My Image Galleries</h3>
<p>{$sitename} allows you to organize their images into galleries, so if you have several related images to share with your friends or family, you can send them just the url of the gallery instead of a separate url to each image.</p>

<p>{$free_account_name} accounts can create up to {$free_max_galleries} galleries and <a href="{$urls['upgrade']}">{$paid_account_name}</a> accounts can have up to {$paid_max_galleries} image galleries.</p>
<p>There is no limit to the number of images you can display in each gallery, other than the maximum number allowed in your account.</p>
<p>To add or remove images from your galleries, <a href="{$urls['galleries']}">create one or more galleries</a>, then browse to your <a href="{$urls['images']}">My Images</a> page, select the checkboxes next to the images you want to add to a gallery, and select the gallery from the list at the bottom of that page.</p>
EOF;

/*
 *Displayed on the gallery.php page if no gallery specified.
*/
$lang['Gallery Not Found Content'] = <<<EOF
<h3>Gallery Not Found!</h3>

<p>Sorry, the image gallery you specified does not exist.</p>
EOF;

/*
 *Displayed on the rename and resize page if no image selected.
*/
$lang['No Image Selected Content'] = <<<EOF
<h3>No Image Selected!</h3>
<p>Please return to the <a href="{$urls['images']}">My Images</a> page and select the image you want to perform this operation on.</p>
EOF;

/*
 *Displayed on the galleries.php page if the user's account does not allow galleries.
*/
$lang['Galleries Not Allowed Content'] = <<<EOF
<h3>Please upgrade your account</h3>

<p>Your account does not allow you to create any image galleries. For the ability to create up to {$paid_max_galleries} image galleries, please <a href="{$urls['upgrade']}">upgrade to a {$paid_account_name} account</a>.</p>
EOF;

/*
 *Displayed on the upload page when a member cannot upload images due to bandwidth
*/
$lang['Monthly Bandwidth Limit Reached Content'] = <<<EOF
<h3>Monthly Bandwidth Limit Reached</h3>
<p>Sorry, you cannot upload any more images at this time as you have reached your monthly bandwidth limit ({$user_bandwidth}mb).</p>
<p>If you currently have a {$free_account_name} account you can increase your bandwidth allowance to {$paid_bandwidth}mb per month by <a href="{$urls['upgrade']}">upgrading</a> to a {$paid_account_name} account.</p>
EOF;

/*
 *Displayed at top of rotate image page
*/
$lang['Rotate Image Content'] = <<<EOF
<h3>Rotate your image 90, 180 or 270 degrees.</h3>

EOF;

/*
 *Displayed on rotate image page when user does not have permission to rotate the image.
*/
$lang['Cannot Rotate Content'] = <<<EOF
You do not have permission to rotate images.
EOF;

/*
 *Displayed once an image has been rotated.
*/
$lang['Image Rotated Content'] = <<<EOF
Image Rotated! If the full size version of the image does not appear to have rotated please refresh the browser page to clear the old version from your cache.
EOF;

/*
 *Displayed on browse page when no images to display
*/
$lang['Browse - No Images Content'] = <<<EOF
<h3>Browse Images</h3>
<p>
No images to display.
</p>
EOF;

