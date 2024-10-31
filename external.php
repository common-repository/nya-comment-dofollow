<?php
/*
File Name: PopUp Window script for Nya Commens DoFollow
Plugin URI: http://www.muzhiks.com/nya_comments_dofollow_v1
Version: 1.0
Author: Andrew Aronsky
Author URI: http://www.muzhiks.com/
*/
require('../../../wp-blog-header.php');
global $nya_options;
$nya_options = get_option('nya_options');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title>
	<?php
	echo $nya_options['title'];
	?>
</title>
<style>
body {background: #dff2fb; margin:0px; border: none; overflow-y: hidden;}
#image {position: absolute; width: 400px; height: 400px; margin: 0px;}
	#image img {border: none;}
#question {position: absolute; top: 330px; width: 400px; font-size: 20px; color: #000; font-weight: bold;}
</style>
</head>
<body>
<div id="image">
<img src="images/follow_link.jpg" width="400" height="400" alt="Навигация по сайту" usemap="#answers">
  <p><map name="answers">
  <area shape="circle" coords="92, 155, 42" href="javascript: self.close ()" title="Close Window" alt="Close">
  <area shape="circle" coords="244, 118, 42" href="<?php echo $_GET['url']; ?>" title="Follow Link" alt="Follow URL" target="_blank" onClick="return follow(this.href)">
  </map></p>
<div id="question">
		<?php
		global $nya_options;
		$plink=parse_url($_GET['url']);
		echo $nya_options['text'].' ';
		echo $plink['host'];
		?>
</div>
</div>
<script language="JavaScript">
	function follow(url) {
		self.close();
		window.open(url, "");
		return false;
		}
</script>
</body>
</html>
