<?php

/*
File Name: Sample redirect page for Nya Commens DoFollow
Plugin URI: http://www.muzhiks.com/nya_comments_dofollow_v1
Version: 1.0
Author: Andrew Aronsky
Author URI: http://www.muzhiks.com/
*/
require('../../../wp-blog-header.php');
header('Location: '.$_GET['url']);
?>