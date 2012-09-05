<?php

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
$dbschema = array (
  'account_types' =>
  array (
    'fields' =>
    array (
      'type_type' =>
      array (
        'field' => 'type_type',
        'type' => 'varchar(10)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'type_name' =>
      array (
        'field' => 'type_name',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'cost_1' =>
      array (
        'field' => 'cost_1',
        'type' => 'decimal(10,2)',
        'null' => false,
        'default' => '4.95',
        'extra' => '',
      ),
      'max_images' =>
      array (
        'field' => 'max_images',
        'type' => 'int(11)',
        'null' => false,
        'default' => '20',
        'extra' => '',
      ),
      'max_upload_size' =>
      array (
        'field' => 'max_upload_size',
        'type' => 'int(11)',
        'null' => false,
        'default' => '2048',
        'extra' => '',
      ),
      'max_image_width' =>
      array (
        'field' => 'max_image_width',
        'type' => 'int(11)',
        'null' => false,
        'default' => '800',
        'extra' => '',
      ),
      'max_image_height' =>
      array (
        'field' => 'max_image_height',
        'type' => 'int(11)',
        'null' => false,
        'default' => '600',
        'extra' => '',
      ),
      'auto_jpeg' =>
      array (
        'field' => 'auto_jpeg',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'jpeg_quality' =>
      array (
        'field' => 'jpeg_quality',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '65',
        'extra' => '',
      ),
      'add_branding' =>
      array (
        'field' => 'add_branding',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'simultaneous_uploads' =>
      array (
        'field' => 'simultaneous_uploads',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'auto_resize' =>
      array (
        'field' => 'auto_resize',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'email_friends' =>
      array (
        'field' => 'email_friends',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'images_per_page' =>
      array (
        'field' => 'images_per_page',
        'type' => 'int(10) unsigned',
        'null' => false,
        'default' => '5',
        'extra' => '',
      ),
      'max_galleries' =>
      array (
        'field' => 'max_galleries',
        'type' => 'int(11)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'resize_images' =>
      array (
        'field' => 'resize_images',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'rename_images' =>
      array (
        'field' => 'rename_images',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'bandwidth' =>
      array (
        'field' => 'bandwidth',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'storage' =>
      array (
        'field' => 'storage',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'cost_3' =>
      array (
        'field' => 'cost_3',
        'type' => 'decimal(10,2)',
        'null' => false,
        'default' => '14.95',
        'extra' => '',
      ),
      'cost_6' =>
      array (
        'field' => 'cost_6',
        'type' => 'decimal(10,2)',
        'null' => false,
        'default' => '24.95',
        'extra' => '',
      ),
      'cost_12' =>
      array (
        'field' => 'cost_12',
        'type' => 'decimal(10,2)',
        'null' => false,
        'default' => '44.00',
        'extra' => '',
      ),
      'rotate_images' =>
      array (
        'field' => 'rotate_images',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'allow_zip_uploads' =>
            array(
                'field' => 'allow_zip_uploads',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => '',
            ),
      'zip_uploads_max_images' =>
            array(
                'field' => 'zip_uploads_max_images',
                'type' => 'int',
                'null' => false,
                'default' => 10,
                'extra' => '',
            ),
      'zip_uploads_max_size' =>
            array(
                'field' => 'zip_uploads_max_size',
                'type' => 'int',
                'null' => false,
                'default' => 8,
                'extra' => '',
            ),
       'captions' =>
            array(
                'field' => 'captions',
                'type' => "enum('none','captions','descriptions')",
                'null' => false,
                'default' => 'none',
                'extra' => ''
            ),
    ),
    'pk' => 'type_type',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'type_type',
      ),
      'nameindex' =>
      array (
        0 => 'type_name',
      ),
    ),
  ),
  'ads' =>
  array (
    'fields' =>
    array (
      'ad_id' =>
      array (
        'field' => 'ad_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => NULL,
        'extra' => 'auto_increment',
      ),
      'name' =>
      array (
        'field' => 'name',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'content' =>
      array (
        'field' => 'content',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'views' =>
      array (
        'field' => 'views',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'live' =>
      array (
        'field' => 'live',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'groupname' =>
      array (
        'field' => 'groupname',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
    ),
    'pk' => 'ad_id',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'ad_id',
      ),
      'viewsindex' =>
      array (
        0 => 'live',
        1 => 'views',
      ),
      'nameindex' =>
      array (
        0 => 'name',
      ),
    ),
  ),
  'banned_ips' =>
  array (
    'fields' =>
    array (
      'ip' =>
      array (
        'field' => 'ip',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'added' =>
      array (
        'field' => 'added',
        'type' => 'timestamp',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
    ),
    'pk' => 'ip',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'ip',
      ),
    ),
  ),
  'galleries' =>
  array (
    'fields' =>
    array (
      'gallery_id' =>
      array (
        'field' => 'gallery_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => NULL,
        'extra' => 'auto_increment',
      ),
      'user_id' =>
      array (
        'field' => 'user_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'gallery_name' =>
      array (
        'field' => 'gallery_name',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'gallery_intro' =>
      array (
        'field' => 'gallery_intro',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
    ),
    'pk' => 'gallery_id',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'gallery_id',
      ),
      'userindex' =>
      array (
        0 => 'user_id',
      ),
    ),
  ),
  'images' =>
  array (
    'fields' =>
    array (
      'image_id' =>
      array (
        'field' => 'image_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => NULL,
        'extra' => 'auto_increment',
      ),
      'user_id' =>
      array (
        'field' => 'user_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'name' =>
      array (
        'field' => 'name',
        'type' => 'varchar(150)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'type' =>
      array (
        'field' => 'type',
        'type' => 'varchar(5)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'width' =>
      array (
        'field' => 'width',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'height' =>
      array (
        'field' => 'height',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'uploaded' =>
      array (
        'field' => 'uploaded',
        'type' => 'datetime',
        'null' => false,
        'default' => '0000-00-00 00:00:00',
        'extra' => '',
      ),
      'filesize' =>
      array (
        'field' => 'filesize',
        'type' => 'int(10) unsigned',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'ip' =>
      array (
        'field' => 'ip',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'checked' =>
      array (
        'field' => 'checked',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'gallery_id' =>
      array (
        'field' => 'gallery_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'bandwidth' =>
      array (
        'field' => 'bandwidth',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'public' =>
      array (
        'field' => 'public',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'rating' =>
       array(
            'field' => 'rating',
            'type' => 'float',
            'null' => false,
            'default' => '0',
            'extra' => ''
       ),
       'votes' => array(
            'field' => 'votes',
            'type' => 'int',
            'null' => false,
            'default' => '0',
            'extra' => ''
       ),
       'views' => array(
            'field' => 'views',
                'type' => 'int',
                'null' => false,
                'default' => 0,
                'extra' => ''
       ),
       'thumb_type' =>
      array (
        'field' => 'thumb_type',
        'type' => 'varchar(5)',
        'null' => false,
        'default' => 'jpg',
        'extra' => '',
      ),
      'caption' => array(
        'field' => 'caption',
        'type' => 'varchar(60)',
        'null' => false,
        'default' => '',
        'extra' => ''
      ),
      'description' => array(
        'field' => 'description',
        'type' => 'varchar(255)',
        'null' => false,
        'default' => '',
        'extra' => ''
      ),
      'category_id' => array(
            'field' => 'category_id',
            'type' => 'int',
            'null' => true,
            'default' => '',
            'extra'=>'',
      ),
    ),
    'pk' => 'image_id',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'image_id',
      ),
      'userindex' =>
      array (
        0 => 'user_id',
        1 => 'name',
      ),
      'useruploadedindex' =>
      array (
        0 => 'user_id',
        1 => 'uploaded',
      ),
      'nameindex' =>
      array (
        0 => 'name',
      ),
      'galindex' =>
      array (
        0 => 'gallery_id',
      ),
      'uploadedindex' =>
      array (
        0 => 'uploaded',
        1 => 'public',
      ),
      'catindex' => array(
         0 => 'category_id',
         1 => 'uploaded'
      )
    ),
  ),
  'page_content' =>
  array (
    'fields' =>
    array (
      'content_id' =>
      array (
        'field' => 'content_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => NULL,
        'extra' => 'auto_increment',
      ),
      'name' =>
      array (
        'field' => 'name',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'description' =>
      array (
        'field' => 'description',
        'type' => 'varchar(255)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'content' =>
      array (
        'field' => 'content',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
    ),
    'pk' => 'content_id',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'content_id',
      ),
      'nameindex' =>
      array (
        0 => 'name',
      ),
    ),
  ),
  'paypalpayments' =>
  array (
    'fields' =>
    array (
      'payment_id' =>
      array (
        'field' => 'payment_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => NULL,
        'extra' => 'auto_increment',
      ),
      'user_id' =>
      array (
        'field' => 'user_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'txn_id' =>
      array (
        'field' => 'txn_id',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'dated' =>
      array (
        'field' => 'dated',
        'type' => 'datetime',
        'null' => false,
        'default' => '0000-00-00 00:00:00',
        'extra' => '',
      ),
      'amount' =>
      array (
        'field' => 'amount',
        'type' => 'decimal(10,2)',
        'null' => false,
        'default' => '0.00',
        'extra' => '',
      ),
      'paypal_email' =>
      array (
        'field' => 'paypal_email',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'paypal_data' =>
      array (
        'field' => 'paypal_data',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'sub_id' =>
      array (
        'field' => 'sub_id',
        'type' => 'varchar(32)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
    ),
    'pk' => 'payment_id',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'payment_id',
      ),
      'txnindex' =>
      array (
        0 => 'txn_id',
      ),
      'userindex' =>
      array (
        0 => 'user_id',
      ),
      'emailindex' =>
      array (
        0 => 'paypal_email',
      ),
      'datedindex' =>
      array (
        0 => 'dated',
      ),
    ),
  ),
  'settings' =>
  array (
    'fields' =>
    array (
      'admin_username' =>
      array (
        'field' => 'admin_username',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => 'admin',
        'extra' => '',
      ),
      'admin_password' =>
      array (
        'field' => 'admin_password',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => 'password',
        'extra' => '',
      ),
      'admin_email' =>
      array (
        'field' => 'admin_email',
        'type' => 'varchar(150)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'sitename' =>
      array (
        'field' => 'sitename',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'siteurl' =>
      array (
        'field' => 'siteurl',
        'type' => 'varchar(150)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'email_confirmation' =>
      array (
        'field' => 'email_confirmation',
        'type' => 'tinyint(3) unsigned',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'signup_email_from' =>
      array (
        'field' => 'signup_email_from',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'signup_email_subject' =>
      array (
        'field' => 'signup_email_subject',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => 'Welcome to {sitename}',
        'extra' => '',
      ),
      'signup_email_template' =>
      array (
        'field' => 'signup_email_template',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'email_confirmation_key' =>
      array (
        'field' => 'email_confirmation_key',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => 'enter something random here',
        'extra' => '',
      ),
      'allow_signups' =>
      array (
        'field' => 'allow_signups',
        'type' => 'tinyint(3) unsigned',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'thumbnail_width' =>
      array (
        'field' => 'thumbnail_width',
        'type' => 'int(10) unsigned',
        'null' => false,
        'default' => '120',
        'extra' => '',
      ),
      'thumbnail_height' =>
      array (
        'field' => 'thumbnail_height',
        'type' => 'int(10) unsigned',
        'null' => false,
        'default' => '120',
        'extra' => '',
      ),
      'image_folder' =>
      array (
        'field' => 'image_folder',
        'type' => 'varchar(255)',
        'null' => false,
        'default' => './images',
        'extra' => '',
      ),
      'thumb_folder' =>
      array (
        'field' => 'thumb_folder',
        'type' => 'varchar(255)',
        'null' => false,
        'default' => './thumbs',
        'extra' => '',
      ),
      'image_url' =>
      array (
        'field' => 'image_url',
        'type' => 'varchar(255)',
        'null' => false,
        'default' => '/images/',
        'extra' => '',
      ),
      'thumb_url' =>
      array (
        'field' => 'thumb_url',
        'type' => 'varchar(255)',
        'null' => false,
        'default' => '/thumbs/',
        'extra' => '',
      ),
      'allow_uploads' =>
      array (
        'field' => 'allow_uploads',
        'type' => 'tinyint(3) unsigned',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'branding_text' =>
      array (
        'field' => 'branding_text',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => 'hosted by www.mysite.com',
        'extra' => '',
      ),
      'branding_color' =>
      array (
        'field' => 'branding_color',
        'type' => 'varchar(10)',
        'null' => false,
        'default' => '#ffffff',
        'extra' => '',
      ),
      'branding_bgcolor' =>
      array (
        'field' => 'branding_bgcolor',
        'type' => 'varchar(10)',
        'null' => false,
        'default' => '#000000',
        'extra' => '',
      ),
      'branding_transparency' =>
      array (
        'field' => 'branding_transparency',
        'type' => 'tinyint(3) unsigned',
        'null' => false,
        'default' => '50',
        'extra' => '',
      ),
      'branding_size' =>
      array (
        'field' => 'branding_size',
        'type' => 'tinyint(3) unsigned',
        'null' => false,
        'default' => '8',
        'extra' => '',
      ),
      'min_branding_width' =>
      array (
        'field' => 'min_branding_width',
        'type' => 'int(10) unsigned',
        'null' => false,
        'default' => '200',
        'extra' => '',
      ),
      'min_branding_height' =>
      array (
        'field' => 'min_branding_height',
        'type' => 'int(10) unsigned',
        'null' => false,
        'default' => '150',
        'extra' => '',
      ),
      'email_images_subject' =>
      array (
        'field' => 'email_images_subject',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => 'Have a look at my pictures',
        'extra' => '',
      ),
      'email_images_template' =>
      array (
        'field' => 'email_images_template',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'reminder_email_from' =>
      array (
        'field' => 'reminder_email_from',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'reminder_email_subject' =>
      array (
        'field' => 'reminder_email_subject',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => 'your password reminder',
        'extra' => '',
      ),
      'reminder_email_template' =>
      array (
        'field' => 'reminder_email_template',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'paypal_email' =>
      array (
        'field' => 'paypal_email',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'payment_email_subject' =>
      array (
        'field' => 'payment_email_subject',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => 'Your account has been upgraded!',
        'extra' => '',
      ),
      'payment_email_message' =>
      array (
        'field' => 'payment_email_message',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'installed' =>
      array (
        'field' => 'installed',
        'type' => 'tinyint(3) unsigned',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'ban_ips' =>
      array (
        'field' => 'ban_ips',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'gallery_max_image_width' =>
      array (
        'field' => 'gallery_max_image_width',
        'type' => 'int(11)',
        'null' => false,
        'default' => '500',
        'extra' => '',
      ),
      'gallery_max_image_height' =>
      array (
        'field' => 'gallery_max_image_height',
        'type' => 'int(11)',
        'null' => false,
        'default' => '500',
        'extra' => '',
      ),
      'gallery_url_mode' =>
      array (
        'field' => 'gallery_url_mode',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => 'querystring',
        'extra' => '',
      ),
      'bandwidth_checked' =>
      array (
        'field' => 'bandwidth_checked',
        'type' => 'date',
        'null' => false,
        'default' => '0000-00-00',
        'extra' => '',
      ),
      'bandwidth_reset_checked' =>
      array (
        'field' => 'bandwidth_reset_checked',
        'type' => 'date',
        'null' => false,
        'default' => '0000-00-00',
        'extra' => '',
      ),
      'notify_bandwidth_exceeded' =>
      array (
        'field' => 'notify_bandwidth_exceeded',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'bandwidth_exceeded_from' =>
      array (
        'field' => 'bandwidth_exceeded_from',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => 'admin@example.com',
        'extra' => '',
      ),
      'bandwidth_exceeded_subject' =>
      array (
        'field' => 'bandwidth_exceeded_subject',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => 'monthly bandwidth exceeded',
        'extra' => '',
      ),
      'bandwidth_exceeded_message' =>
      array (
        'field' => 'bandwidth_exceeded_message',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'payment_notifications' =>
      array (
        'field' => 'payment_notifications',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'paypal_account_email' =>
      array (
        'field' => 'paypal_account_email',
        'type' => 'varchar(150)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'browse_per_page' =>
      array (
        'field' => 'browse_per_page',
        'type' => 'int(11)',
        'null' => false,
        'default' => '12',
        'extra' => '',
      ),
      'browse_images' =>
      array (
        'field' => 'browse_images',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'browse_checked_only' =>
      array (
        'field' => 'browse_checked_only',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'browse_max_image_width' =>
      array (
        'field' => 'browse_max_image_width',
        'type' => 'int(11)',
        'null' => false,
        'default' => 450,
        'extra' => '',
      ),
      'upload_public_default' =>
      array (
        'field' => 'upload_public_default',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'sidebar_images' =>
      array (
        'field' => 'sidebar_images',
        'type' => 'int(11)',
        'null' => false,
        'default' => '4',
        'extra' => '',
      ),
      'sidebar_top_html' =>
      array (
        'field' => 'sidebar_top_html',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'sidebar_bottom_html' =>
      array (
        'field' => 'sidebar_bottom_html',
        'type' => 'text',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'sidebar_image_width' =>
      array (
        'field' => 'sidebar_image_width',
        'type' => 'int(11)',
        'null' => false,
        'default' => '75',
        'extra' => '',
      ),
      'sidebar_account_pos' =>
      array (
        'field' => 'sidebar_account_pos',
        'type' => 'enum(\'navbar\',\'top\',\'bottom\',\'before\',\'after\')',
        'null' => false,
        'default' => 'after',
        'extra' => '',
      ),
      'random_per_page' =>
      array (
        'field' => 'random_per_page',
        'type' => 'int(11)',
        'null' => false,
        'default' => '6',
        'extra' => '',
      ),
      'sidebar_image_type' =>
      array (
        'field' => 'sidebar_image_type',
        'type' => 'enum(\'recent\',\'random\')',
        'null' => false,
        'default' => 'random',
        'extra' => '',
      ),
      'home_page_show_plans' =>
      array (
        'field' => 'home_page_show_plans',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'upgrade_show_plans' =>
      array (
        'field' => 'upgrade_show_plans',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'home_page_images' =>
      array (
        'field' => 'home_page_images',
        'type' => 'int(11)',
        'null' => false,
        'default' => '6',
        'extra' => '',
      ),
      'home_page_which_images' =>
      array (
        'field' => 'home_page_which_images',
        'type' => 'enum(\'none\',\'recent\',\'random\')',
        'null' => false,
        'default' => 'none',
        'extra' => '',
      ),
      'home_page_image_width' =>
      array (
        'field' => 'home_page_image_width',
        'type' => 'int(11)',
        'null' => false,
        'default' => '450',
        'extra' => '',
      ),
      'home_page_thumb_width' =>
      array (
        'field' => 'home_page_thumb_width',
        'type' => 'int(11)',
        'null' => false,
        'default' => 120,
        'extra' => '',
      ),
      'home_page_single_image' =>
      array (
        'field' => 'home_page_single_image',
        'type' => 'enum(\'none\',\'random\',\'recent\')',
        'null' => false,
        'default' => 'none',
        'extra' => '',
      ),
      'hotlink_thumbnails' =>
      array (
        'field' => 'hotlink_thumbnails',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'hotlink_images' =>
      array (
        'field' => 'hotlink_images',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'monitor_thumbnail_bandwidth' =>
      array (
        'field' => 'monitor_thumbnail_bandwidth',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'monitor_image_bandwidth' =>
      array (
        'field' => 'monitor_image_bandwidth',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'show_errors' =>
      array (
        'field' => 'show_errors',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '1',
        'extra' => '',
      ),
      'browse_image_links' =>
      array (
        'field' => 'browse_image_links',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'browse_thumb_links' =>
      array (
        'field' => 'browse_thumb_links',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'browse_thumb_embed' =>
      array (
        'field' => 'browse_thumb_embed',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'browse_image_embed' =>
      array (
        'field' => 'browse_image_embed',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'browse_image_bbcode' =>
      array (
        'field' => 'browse_image_bbcode',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'browse_thumb_bbcode' =>
      array (
        'field' => 'browse_thumb_bbcode',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'theme' =>
        array(
            'field' => 'theme',
            'type' => 'varchar(40)',
            'null' => false,
            'default' => 'default',
            'extra' => '',
        ),
      'stylesheet' =>
        array(
            'field' => 'stylesheet',
            'type' => 'varchar(40)',
            'null' => false,
            'default' => 'default/styles/default.css',
            'extra' => '',
        ),
       'language' =>
            array(
            'field' => 'language',
            'type' => 'varchar(40)',
            'null' => false,
            'default' => 'default',
            'extra' => '',
          ),
       'rewrite_urls' =>
            array(
              'field' => 'rewrite_urls',
              'type' => 'tinyint',
              'null' => false,
              'default' => 0,
              'extra' => '',
            ),
       'imagick_path' =>
            array(
                'field' => 'imagick_path',
                'type' => 'varchar(255)',
                'null' => false,
                'default' => '',
                'extra' => '',
            ),
        'image_tool' =>
            array(
                'field' => 'image_tool',
                'type' => 'varchar(20)',
                'null' => false,
                'default' => 'gd',
                'extra' => '',
            ),
         'temp_dir' =>
            array(
                'field' => 'temp_dir',
                'type' => 'varchar(150)',
                'null' => false,
                'default' => '',
                'extra' => '',
            ),
         'rewrite_old_urls' => array(
            'field' => 'rewrite_old_urls',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => ''
         ),
         'htaccess_no_indexes' => array(
                'field' => 'htaccess_no_indexes',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => '',
         ),
         'debug_imagick' => array(
                'field' => 'debug_imagick',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => ''
         ),
         'sidebar_news_items' => array(
                'field' => 'sidebar_news_items',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => ''
         ),
         'homepage_news_items' => array(
                'field' => 'homepage_news_items',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => ''
         ),
         'image_ratings' => array(
                'field' => 'image_ratings',
                'type' => "enum('off', 'members', 'anyone')",
                'null' => false,
                'default' => 'off',
                'extra' => ''
         ),
         'log_image_views' => array(
            'field' => 'log_image_views',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => ''
         ),
         'disable_site' => array(
                'field' => 'disable_site',
                'type' => 'tinyint',
                'null' => false,
                'default' => 0,
                'extra' => '',
         ),
         'site_disabled_message' => array(
                'field' => 'site_disabled_message',
                'type' => 'text',
                'null' => false,
                'default' => '',
                'extra' => ''
         ),
         'branding_font' => array(
                'field' => 'branding_font',
                'type' => 'varchar(100)',
                'null' => false,
                'default' => 'AIRSTREA.TTF',
                'extra' => ''
         ),
         'thumb_format' => array(
            'field' => 'thumb_format',
            'type' => "enum('jpeg','original','auto')",
            'null' => false,
            'default' => 'jpeg',
            'extra' => ''
         ),
         'anonymous_uploads' => array(
            'field' => 'anonymous_uploads',
            'type' => 'tinyint',
            'null' => false,
            'default' => 0,
            'extra' => ''
         ),
         'anonymous_account' => array(
            'field' => 'anonymous_account',
                'type' => 'varchar(30)',
                'null' => false,
                'default' => '',
                'extra' => '',
         ),
      'dbversion' =>
        array(
          'field' => 'dbversion',
            'type' => 'varchar(20)',
            'null' => false,
            'default' => '1.4.0.18',
            'extra' => '',
        ),
    ),
    'pk' => '',
    'keys' =>
    array (
    ),
  ),
  'news' => array(
    'fields' => array(
        'news_id' => array(
            'field' => 'news_id',
            'type' => 'int',
            'null' => false,
            'default' => NULL,
            'extra' => 'auto_increment',
            ),
         'status' => array(
                'field' => 'status',
                'type' => 'enum(\'unpublished\',\'published\',\'archived\',\'hidden\')',
                'null' => false,
                'default' => 'unpublished',
                'extra' => ''
         ),
         'headline' => array(
                'field' => 'headline',
                'type' => 'varchar(100)',
                'null' => false,
                'default' => '',
                'extra' => ''
         ),
         'summary' => array(
                'field' => 'summary',
                'type' => 'varchar(255)',
                'null' => false,
                'default' => '',
                'extra' => ''
         ),
         'details' => array(
                'field' => 'details',
                'type' => 'text',
                'null' => false,
                'default' => '',
                'extra' => '',
         ),
         'published' => array(
            'field' => 'published',
            'type' => 'datetime',
            'null' => false,
            'default' => '0000-00-00 00:00:00',
            'extra' => '',
         )
    ),
    'pk' => 'news_id',
    'keys' => array(
      'PRIMARY' =>
      array (
        0 => 'news_id',
      ),
      'pubindex' => array(
         0 => 'published',
         1 => 'status'
      ),
      'sindex' => array(
         0 => 'status'
      )
    )
  ),

  'categories' =>
  array (
    'fields' =>
    array (
      'category_id' =>
      array (
        'field' => 'category_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => NULL,
        'extra' => 'auto_increment',
      ),
      'category_name' =>
      array (
        'field' => 'category_name',
        'type' => 'varchar(40)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'category_intro' =>
      array (
        'field' => 'category_intro',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
    ),
    'pk' => 'category_id',
    'keys' => array(
      'PRIMARY' =>
      array (
        0 => 'category_id',
      ),
      'cindex' => array(
         0 => 'category_name'
      ),
    )
  ),

  'tags' => array(
    'fields' => array(
         'tag_id' => array(
                'field' => 'tag_id',
                'type' => 'int',
                'null' => false,
                'default' => 0,
                'extra' => 'auto_increment'
         ),
         'tag' => array(
                'field' => 'tag',
                'type' => 'varchar(30)',
                'null' => false,
                'default' => '',
                'extra' => ''
         )
    ),
    'pk' => 'tag_id',
    'keys' => array(
        'PRIMARY' => array(
                0 => 'tag_id'
        ),
        'tindex' => array(
                0 => 'tag'
        )
    ),
  ),

  'tags_images' => array(
    'fields' => array(
         'tag_id' => array(
                'field' => 'tag_id',
                'type' => 'int',
                'null' => false,
                'default' => 0,
                'extra' => '',
         ),
         'image_id' => array(
                'field' => 'image_id',
                'type' => 'int',
                'null' => false,
                'default' => 0,
                'extra' => ''
         )
    ),
    'pk' => 'tag_id,image_id',
    'keys' => array(
        'PRIMARY' => array(
                0 => 'tag_id',
                1 => 'image_id'
        ),
        'itindex' => array(
                0 => 'image_id'
        )
    ),
  ),

  'comments' => array(
        'fields' => array(
            'comment_id' => array(
                'field' => 'comment_id',
                'type' => 'int',
                'null' => false,
                'default' => 0,
                'extra' => 'auto_increment'
            ),
            'author_id' => array(
                'field' => 'author_id',
                'type' => 'int',
                'null' => false,
                'default' => 0,
                'extra' => ''
            ),
            'image_id' => array(
                'field' => 'image_id',
                'type' => 'int',
                'null' => false,
                'default' => 0,
                'extra' => ''
            ),
            'comment' => array(
                'field' => 'comment',
                'type' => 'varchar(255)',
                'null' => false,
                'default' => '',
                'extra' => ''
            ),
            'posted' => array(
                'field' => 'posted',
                'type' => 'datetime',
                'null' => false,
                'default' => '',
                'extra' => '',
            ),
            'status' => array(
                'field' => 'status',
                'type' => "enum('unmoderated', 'ok', 'abusive', 'spam')",
                'null' => false,
                'default' => '',
                'extra' => ''
            ),

        ),
        'pk' => 'comment_id',
        'keys' => array(
            'PRIMARY' => array(
                0 => 'comment_id'
            ),
            'aindex' => array(
                0 => 'author_id'
            ),
            'iindex' => array(
                0 => 'image_id',
                1 => 'posted'
            ),
            'pindex' => array(
                0 => 'posted'
            )
        )
  ),

  'users' =>
  array (
    'fields' =>
    array (
      'user_id' =>
      array (
        'field' => 'user_id',
        'type' => 'int(11)',
        'null' => false,
        'default' => NULL,
        'extra' => 'auto_increment',
      ),
      'status' =>
      array (
        'field' => 'status',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'name' =>
      array (
        'field' => 'name',
        'type' => 'varchar(50)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'username' =>
      array (
        'field' => 'username',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'pass' =>
      array (
        'field' => 'pass',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'email' =>
      array (
        'field' => 'email',
        'type' => 'varchar(150)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'joined' =>
      array (
        'field' => 'joined',
        'type' => 'datetime',
        'null' => false,
        'default' => '0000-00-00 00:00:00',
        'extra' => '',
      ),
      'updated' =>
      array (
        'field' => 'updated',
        'type' => 'datetime',
        'null' => false,
        'default' => '0000-00-00 00:00:00',
        'extra' => '',
      ),
      'lastlogin' =>
      array (
        'field' => 'lastlogin',
        'type' => 'datetime',
        'null' => false,
        'default' => '0000-00-00 00:00:00',
        'extra' => '',
      ),
      'ip' =>
      array (
        'field' => 'ip',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'account_type' =>
      array (
        'field' => 'account_type',
        'type' => 'varchar(10)',
        'null' => false,
        'default' => 'free',
        'extra' => '',
      ),
      'paid_until' =>
      array (
        'field' => 'paid_until',
        'type' => 'date',
        'null' => false,
        'default' => '0000-00-00',
        'extra' => '',
      ),
      'loginip' =>
      array (
        'field' => 'loginip',
        'type' => 'varchar(20)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'bandwidth_exceeded' =>
      array (
        'field' => 'bandwidth_exceeded',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'bandwidth_reset' =>
      array (
        'field' => 'bandwidth_reset',
        'type' => 'date',
        'null' => false,
        'default' => '0000-00-00',
        'extra' => '',
      ),
      'deleted_images_bandwidth' =>
      array (
        'field' => 'deleted_images_bandwidth',
        'type' => 'int(11)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'sub_id' =>
      array (
        'field' => 'sub_id',
        'type' => 'varchar(100)',
        'null' => false,
        'default' => '',
        'extra' => '',
      ),
      'sub_months' =>
      array (
        'field' => 'sub_months',
        'type' => 'tinyint(4)',
        'null' => false,
        'default' => '0',
        'extra' => '',
      ),
      'sub_amount' =>
      array (
        'field' => 'sub_amount',
        'type' => 'decimal(10,2)',
        'null' => false,
        'default' => '0.00',
        'extra' => '',
      ),
    ),
    'pk' => 'user_id',
    'keys' =>
    array (
      'PRIMARY' =>
      array (
        0 => 'user_id',
      ),
      'loginindex' =>
      array (
        0 => 'username',
        1 => 'pass',
      ),
      'joinedindex' =>
      array (
        0 => 'joined',
        1 => 'status',
      ),
      'emailindex' =>
      array (
        0 => 'email',
        1 => 'username',
      ),
      'statusindex' =>
      array (
        0 => 'status',
      ),
    ),
  ),
);