<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

date_default_timezone_set('America/Chicago');

define('PATH', 'localhost');
define('UPLOAD_PATH', url() . '/uploads/');
define('THEME_IMAGES', url() . '/img/');
define('FILEUPLOAD', base_path() . "/public/uploads/");
define('CURRENT_URL','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);


define ('GET_RECORDS','Get Records.');
define ('NO_RECORDS','No Records Found.');
define ('INSERT_RECORD','Record Inserted Successfully.');
define ('MISSING_PARAMS','Invalid Parameters.');
define ('INSERT_ERROR','Record Cannot Inserted, Please Try Again.');
define ('UPDATE_RECORD','Record Updated Successfully.');
define ('DELETE_RECORD','Record Deleted Successfully.');
define ('CONFIRM_MESSAGE','Are you sure to delete this record ?');
define ('SOMETHING_WRONG','Something Wrong Please Try agin.');
define ('LOGIN_SUCCESS','Login Successfull');
define ('LOGIN_WRONG','Wrong Credential');
define('FILL_PARAMS','Please fill all required Parameters.');
define('ALREADY_BOX','Delete all boxes in the boxes tab to rebox shipment.');

define ('PASSWORD_NOT_MATCH','Password does not match!');
define ('PASSWORD_CHANGE','Password change Successfully, Please Login');

define ('MAIL_SEND','Email has been send Successfull.');
define ('MAIL_NOT_SEND','Email has not been send!');
define ('MAIL_LINK_EXPIRE','Sorry, Link has been expired. Please Try again!');

define ('CURRENT_DATE',date('Y-m-d'));
define ('CURRENT_DATETIME',date('Y-m-d H:i:s'));
define ('RECORDS_PER_PAGE',5);