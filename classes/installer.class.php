<?php
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

*/set_time_limit(0);
class installer
{

    var $vars = array();
    var $errors = array();

    function __construct($app)
    {
        $this->app = $app;
		$domain = $_SERVER['HTTP_HOST'];
		$this->vars['sitename'] = 'My Image Host';
		$baseurl = 'http://'.$domain;
		$path = preg_replace('/\/index.*$/i', '',$_SERVER['SCRIPT_NAME']);
		$this->vars['siteurl'] = 'http://'.$domain.$path;
		if( !preg_match('/\/$/i', $this->vars['siteurl']) ) $this->vars['siteurl'] .= '/';
		$this->vars['image_url'] = $this->vars['siteurl'].'images/';
		$this->vars['thumb_url'] = $this->vars['siteurl'].'thumbs/';
		$this->vars['image_folder'] = realpath(dirname(__FILE__).'/../images/');
        if( !in_array(substr($this->vars['image_folder'],strlen($this->vars['image_folder'])-1), array('/', '\\') ) ) {
            $this->vars['image_folder'] .= '/';
        }
		$this->vars['thumb_folder'] = realpath(dirname(__FILE__).'/../thumbs/');
        if( !in_array(substr($this->vars['thumb_folder'],strlen($this->vars['thumb_folder'])-1), array('/', '\\') ) ) {
            $this->vars['thumb_folder'] .= '/';
        }
        $this->vars['temp_dir'] = realpath(dirname(__FILE__).'/../temp');

        $domain = preg_replace('/^www\.(.{1,})\.(.{1,})$/i', '$1.$2', $domain);
		$this->vars['admin_email'] = 'admin@'.$domain;
		$this->vars['paypal_email'] = 'paypal@'.$domain;
		$this->vars['reminder_email_from'] = 'reminder@'.$domain;
		$this->vars['signup_email_from'] = 'newmember@'.$domain;
    }

    function installer($app)
    {
        $this->__construct($app);
    }
    
    function render()
    {
?>
<h3>Setup Custom Settings</h3>
<p>
    This form contains some settings that must be set for php image host to function correctly.
</p>
<p>
    Please visit the <a target="_blank" href="http://forum.phpace.com/">support forum</a> for information and
    help.
</p>
<form action="" method="post">
<?php if( $this->errors ) {?>
    <div style="color: red; border: 1px solid red; padding: 1em;">
        <h3>Pleast correct the following errors:</h3>
        <ul>
        <?php foreach( $this->errors as $what ) { ?>
            <li><?php echo htmlspecialchars($what)?></li>
        <?php } ?>
        </ul>
    </div>
<?php } ?>
<table align="center" cellspacing="1" cellpadding="0" border="0" >
<tr>
	<td class="label">Site Name:</td>
	<td class="field"><input type="text" name="install[sitename]" value="<?php echo htmlspecialchars($this->vars['sitename']);?>" />
		<br />Enter the name for your site (eg. "My Image Host").
	</td>
</tr>
<tr>
	<td class="label">Site Url:</td>
	<td class="field"><input type="text" name="install[siteurl]" value="<?php echo htmlspecialchars($this->vars['siteurl']);?>" />
		<br />Enter the full url for your site, including http, domain name, and trailing slash: eg: "http://www.myimagehost.com/".
	</td>
</tr>
<tr>
	<td class="label">Image URL:</td>
	<td class="field"><input type="text" name="install[image_url]" value="<?php echo htmlspecialchars($this->vars['image_url']);?>" />
		<br />Enter the full url to the folder where uploaded images are stored (by default this is http://www.yoursite.com/images/).
	</td>
</tr>
<tr>
	<td class="label">Thumbnail URL:</td>
	<td class="field"><input type="text" name="install[thumb_url]" value="<?php echo htmlspecialchars($this->vars['thumb_url']);?>" />
		<br />Enter the full url to the folder where thumbnail images are stored (by default this is http://www.yoursite.com/thumbs/).
	</td>
</tr>
<tr>
	<td class="label">Full path to images folder:</td>
	<td class="field"><input type="text" name="install[image_folder]" value="<?php echo htmlspecialchars($this->vars['image_folder']);?>" />
		<br />Enter the full directory path on your server to the folder where images are stored (eg. /home/user/public_html/images/).
	</td>
</tr>
<tr>
	<td class="label">Full path to thumbnail images folder:</td>
	<td class="field"><input type="text" name="install[thumb_folder]" value="<?php echo htmlspecialchars($this->vars['thumb_folder']);?>" />
		<br />Enter the full directory path on your server to the folder where thumbnail images are stored (eg. /home/user/public_html/thumbs/).
	</td>
</tr>
<tr>
	<td class="label">Full path to temporary folder:</td>
	<td class="field"><input type="text" name="install[temp_dir]" value="<?php echo htmlspecialchars($this->vars['temp_dir']);?>" />
        <br />The directory to store temporary files (uploaded images from zip archives, etc) in. The permissions on this directory
        must be set so that the script can create subdirectories and files in it.	</td>
</tr>
<tr>
	<td class="label">Admin Email Address:</td>
	<td class="field"><input type="text" name="install[admin_email]" value="<?php echo htmlspecialchars($this->vars['admin_email']);?>" />
		<br />Your email address. Messages submitted via the contact form will be sent to this email address.
	</td>
</tr>
<tr>
	<td class="label">PayPal Email Address:</td>
	<td class="field"><input type="text" name="install[paypal_email]" value="<?php echo htmlspecialchars($this->vars['paypal_email']);?>" />
		<br />Your paypal email address. This is the paypal account that payments for account upgrades are paid to.
	</td>
</tr>
<tr>
	<td class="label">Send Password Reminders From:</td>
	<td class="field"><input type="text" name="install[reminder_email_from]" value="<?php echo htmlspecialchars($this->vars['reminder_email_from']);?>" />
		<br />Enter the email address shown as the "from" address when password reminders are emailed to your members.
	</td>
</tr>
<tr>
	<td class="label">Send Signup Emails From:</td>
	<td class="field"><input type="text" name="install[signup_email_from]" value="<?php echo htmlspecialchars($this->vars['signup_email_from']);?>" />
		<br />Enter the email address shown as the "from" address when emails are sent when a member opens an account.
	</td>
</tr>

<tr>
	<td class="label">&nbsp;</td>
	<td class="field"><input type="submit" name="submit" value="Install Php Image Host" /></td>
</tr>
</table>
</form
<?php
    }

    function postInstall()
    {
		$sql = "INSERT INTO account_types (type_type) VALUES ('free'), ('paid') ";
		$this->app->query($sql);
        $sfields = array('admin_username', 'admin_password', 'reminder_email_template',
                        'email_images_template', 'bandwidth_exceeded_message',
                        'signup_email_template'
        );
        $svalues = array('admin', 'password');
        // reminder email template
        $svalues[] = <<<EOF
Hi there,

This is a password reminder:

Your username: {username}
Your password: {password}

<confirm>
Please confirm your email address by visiting the url below:

{confirmurl}
</confirm>
You can login at {siteurl}?cmd=login

Thanks, the {sitename} team.
EOF;
        // email images template
        $svalues[] = <<<EOF
{message}

Click the links below to view the images in your web browser. If clicking the links does not work, copy them and paste them into your browser's address bar.

{imagelinks}

This message was sent to you from {sitename} ({siteurl}) on behalf of the sender.
If you believe you have received this message in error please accept our apologies.
If you wish to complain about the sender of this message, please contact us at:
{siteurl}?cmd=contact

Thanks.
EOF;
        // bandwidth exceeded message template
        $svalues[] = <<<EOF
You have exceeded your monthly bandwidth limit on {sitename}.

Your images cannot be viewed until your bandwidth usage is reset at the beginning of the next month of your membership.
EOF;
        // signup email template
        $svalues[] = <<<EOF
Welcome to {sitename}

Your username is: {username}

Your password is what you entered when you signed up.
<confirm>
Please confirm your email address by visiting the url below:
{confirmurl}
</confirm>

Thank you for joining {sitename}!
EOF;
        for( $i = 0; $i < count($svalues); $i++ ) {
            $svalues[$i] = $this->app->db->escape($svalues[$i]);
        }
        $sql = "INSERT INTO settings (".join(',',$sfields).") VALUES ('".join("','",$svalues)."') ";
        $this->app->query($sql);
    	$sql = "UPDATE settings SET paypal_account_email='".addslashes($this->vars['paypal_email'])."' ";
		foreach( $this->vars as $n=>$v ) $sql .= ",$n='".$this->app->db->escape($v)."' ";
		$res = $this->app->query($sql, 'Update Installation Settings');
        return $res;
    }

    function setOptions($options)
    {
        foreach( $options as $name => $value ) {
            $this->vars[$name] = $value;
        }
        return $this->checkVars();
    }

    function checkVars()
    {
        $local = preg_match('#localhost#i', $_SERVER['HTTP_HOST']);
		if( !$this->app->validateemail($this->vars['admin_email']) ) $this->errors['admin_email'] = 'You must enter a valid admin email address.';
		if( !$this->app->validateemail($this->vars['paypal_email']) ) $this->errors['paypal_email'] = 'You must enter a valid paypal email address.';
		if( !$this->app->validateemail($this->vars['reminder_email_from']) ) $this->errors['reminder_email_from'] = 'You must enter a valid email address to send password reminders from.';
		if( !$this->app->validateemail($this->vars['signup_email_from'])) $this->errors['signup_email_from'] = 'You must enter a valid email address to send new account emails to.';
		if( !is_dir($this->vars['image_folder'])  ){
			$this->errors['image_folder'] = 'You must enter the full path to the folder where images are stored.';
		}elseif(  !is_writable($this->vars['image_folder']) ){
			$this->errors['image_folder'] =  'You must set the permissions for the images folder so that php can write to it.';
		}else{
			if( !preg_match('/\/$/i',$this->vars['image_folder']) ) $this->vars['image_folder'].='/';
		}

        if( !is_dir($this->vars['temp_dir'])  ){
			$this->errors['temp_dir'] = 'You must enter the full path to the folder where temporary files are stored.';
		}elseif(  !is_writable($this->vars['temp_dir']) ){
			$this->errors['temp_dir'] =  'You must set the permissions for the temporary folder so that php can write to it.';
		}

        if( !is_dir($this->vars['thumb_folder'])  ){
			$this->errors['thumb_folder'] = 'You must enter the full path to the folder where thumbnail images are stored.';
		}elseif(  !is_writable($this->vars['thumb_folder']) ){
			$this->errors['thumb_folder'] =  'You must set the permissions for the thumbnail images folder so that php can write to it.';
		}else{
			if( !preg_match('/\/$/i',$this->vars['thumb_folder']) ) $this->vars['thumb_folder'].='/';
		}
		if( !preg_match('/\/$/i',$this->vars['thumb_url']) ) $this->vars['thumb_url'].='/';
		if( !preg_match('/\/$/i',$this->vars['image_url']) ) $this->vars['image_url'].='/';
		if( !preg_match('/\/$/i',$this->vars['siteurl']) ) $this->vars['siteurl'].='/';
        if( !$local ){
            if( !preg_match('/^http.*:\/\/.{1,}\..{2,}\/$/i', $this->vars['siteurl'] ) ){
                $this->errors[] = 'You must enter the full url for your site, including the "http://", and the trailing "/".';
    		}
        }
        return (count($this->errors) == 0);
    }
}
