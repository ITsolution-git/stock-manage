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
define('FILEUPLOAD', $_SERVER['DOCUMENT_ROOT'] . "/stokkup/public/uploads/");
define('CURRENT_URL','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);


define ('GET_RECORDS','Get Records.');
define ('NO_RECORDS','No Records Found.');
define ('INSERT_RECORD','Record Inserted Successfully.');
define ('MISSING_PARAMS','Invalid Parameters.');
define ('INSERT_ERROR','Record Cannot Inserted, Please Try Again.');
define ('UPDATE_RECORD','Record Updated Successfully.');
define ('DELETE_RECORD','Record Deleted Successfully.');
define ('CONFIRM_MESSAGE','Are you sure to delete this record ?');

