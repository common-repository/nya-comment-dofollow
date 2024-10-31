<?php

/*
Plugin Name: Nya Comments DoFollow
Plugin URI: http://muzhiks.com/nya_comments_dofollow_eng
Description: Directs through a redirect all links to home pages from comments except the last. The link in the last comment becomes dofollow.
Version: 1.2	
Author: Andrew Aronsky
Author URI: http://www.muzhiks.com/
*/
//ini_set('display_errors',1);
//error_reporting(E_ALL);
function init_textdomain() {
     if (function_exists('load_plugin_textdomain')) {
		$temp=stristr(nya_url(),'wp-content');
         load_plugin_textdomain('nya_comments',$temp);
     }
 }



global $wpdb, $nya_options;
$nya_options = get_option('nya_options');
register_activation_hook( __FILE__, 'activate_nya' );
register_deactivation_hook(__FILE__, 'deactivate_nya' );
add_action('init', 'init_textdomain');

if ( is_admin() )
	{ 
	add_action('admin_menu', 'nya_settings');
	add_action( 'admin_init', 'register_nya_settings' ); 
	}

function nya_url( $path = '' ) 
	{
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) 
		{ 
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$folder.=$path;
		return plugins_url( $folder );
		}
	return plugins_url( $path, __FILE__ );
	}

function activate_nya()
	{
	global $nya_options;
	$nya_options=array( 'mode' => 'dofollow_last',
						'window' => 'redir',
						'user_list' => '',
						'adress' => '',
						'admin' => 'on'
						);
	add_option('nya_options',$nya_options);
	}

function deactivate_nya()
	{
	global $nya_options;
	delete_option ('nya_options');
	}
	
function nofollow_del($text)
    {
	global $comment, $wpdb, $post, $nya_options;
	if (get_comment_type()!='comment')
		return $text; //Do not change pingbacks
	$comment_id = get_comment_ID();
	//echo $comment_id.'<br />';
	$admin_id=none;
	if ($nya_options['admin']=='on')
		$admin_id=1;
	$max_comment_id=$wpdb->get_var("SELECT MAX(comment_ID) FROM {$wpdb->comments} WHERE comment_post_ID=$post->ID AND comment_type <> 'pingback' AND comment_approved=1 AND user_id <> $admin_id");
	//echo $max_comment_id;
	$urel=strstr($text, "href='");
	$urel=substr($urel, 6);
	$urel=strtok($urel, "'");
	if (($nya_options['mode']=='dofollow_all')||(($nya_options['mode']=='dofollow_last')&&($comment_id==$max_comment_id))||(($nya_options['mode']=='dofollow_list')&&(is_in_list($urel))))
		{
		$text='<a href="'.$urel.'" target="_blank"  rel="external"  class="url" >'.get_comment_author().'</a>';
		return $text;		
		}		
	else 
		{
		if ($nya_options['window']=='redir')
			{
			$text='<a href="'.nya_url("/redir.php").'?url='.$urel.'" target="_blank">'.get_comment_author().'</a>';
			return $text;
			}
		if (($nya_options['window']=='own')&&($nya_options['adress']))
			$text='<a href="'.$nya_options['adress'].'?url='.$urel.'" target="_blank">'.get_comment_author().'</a>';
		else 
			$text=get_comment_author();
		return $text;
		}
    }

function is_in_list($link)
	{
	global $nya_options;
	$larray=parse_url($link);
	if (stripos ($nya_options['user_list'],$larray['host']))
		return TRUE;
	return FALSE;
	}

function register_nya_settings() 
	{
	if (function_exists('register_setting'))
		{
		register_setting( 'nya_options_group', 'nya_options' );
		return TRUE;
		}
	else
		{
		if (function_exists('add_option_update_handler'))
			{
			add_option_update_handler ( 'nya_options_group', 'nya_options' );
			return TRUE;
			}
		}
	return FALSE;
	}

function nya_settings() 
	{
	add_options_page('Nya Comments DoFollow', 'Nya Comments DoFollow', 9, basename(__FILE__), 'nya_options_form');
	}	
	
function nya_options_form()
	{ 
	global $nya_options;

?>
	<div class="wrap">
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<h2><?php _e('Nya Comments DoFollow Options:','nya_comments') ?> </h2> 
			

			<table class="form-table">
											
					
					<tr valign="top">
						<th scope="row"><strong><?php _e('Mode','nya_comments')?></strong></th>
						<td>
							<p><input name="nya_options[mode]" id="mode" type="radio" value="dofollow_all"  onclick="javascript:TurnOnOff('admin','dis')" <?php checked("dofollow_all", $nya_options['mode']); ?>  /><?php _e('Dofollow All','nya_comments')?></p>
							<p><input name="nya_options[mode]" id="mode" type="radio" value="dofollow_last" onclick="javascript:TurnOnOff('admin')"  <?php checked("dofollow_last", $nya_options['mode']); ?>  /><?php _e('Dofollow Last','nya_comments')?></p>
							<p><?php _e('Ignore admin\'s comments','nya_comments')?><input name="nya_options[admin]" <?php if ($nya_options['mode']!='dofollow_last') echo 'disabled'; ?> type="checkbox" id="admin" value="on" <?php checked('on', $nya_options['admin']); ?> /> </p>
							<p><input name="nya_options[mode]" id="mode" type="radio" value="dofollow_list" onclick="javascript:TurnOnOff('admin','dis')"<?php checked("dofollow_list", $nya_options['mode']); ?>  /><?php _e('Dofollow domains from the list','nya_comments')?></p>
						</td>
						</tr>
						
<script language="JavaScript">
function TurnOnOff(elemID,wtd)
{
  var el = document.getElementById(elemID);
  if (wtd=="dis")
	el.disabled='disabled'
else 
	el.disabled='';
}
</script>

					<tr valign="top">
						<th scope="row"><strong><?php _e('List of domains','nya_comments')?></strong></th>
						<td>
						<p><?php _e('Specify domains which will be dofollow','nya_comments')?></p>
						<textarea name="nya_options[user_list]" cols="64" rows="10" id="user_list"><?php echo $nya_options['user_list']; ?></textarea>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><strong><?php _e('Not Dofollow links:','nya_comments')?></strong></th>
						<td>
							<p><input name="nya_options[window]" id="window" type="radio" value="redir"  onclick="javascript:TurnOnOff('adress','dis');" <?php checked("redir", $nya_options['window']); ?>  /><?php _e('Redirect','nya_comments')?></p>
							<p><input name="nya_options[window]" id="window" type="radio" value="nolink" onclick="javascript:TurnOnOff('adress','dis');"  <?php checked("nolink", $nya_options['window']); ?>  /><?php _e('To leave as the text','nya_comments')?></p>
							<p><input name="nya_options[window]" id="window" type="radio" value="own" onclick="javascript:TurnOnOff('adress');"  <?php checked("own", $nya_options['window']); ?>  /><?php _e('Redirect through own page','nya_comments')?></p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><strong><?php _e('Own redirect page adress:','nya_comments')?></strong></th>
						<td>
						<p><?php _e('Create new page, add in it a line','nya_comments')?> <code> < a href="< ?php echo $_GET['url'];? >" class="url">Go< /a> </code> <?php _e('and specify its full address here.','nya_comments')?></p>
							<input type="text" name="nya_options[adress]" id="adress" class="regular-text code" value="<?php echo $nya_options['adress']; ?>" <?php  if (($nya_options['window']=='redir')||($nya_options['window']=='nolink')) echo "disabled"; ?> />
						</td>
					</tr>
					
					<tr valign="top">
						<td>
						<p><?php _e('For more information visit <a href="http://muzhiks.com/nya_comments_dofollow_eng" target="_blank">official page of the plug-in.</a>','nya_comments')?></p>
						</td>
					</tr>
					
					
				</table>
				
				<p class="submit">
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="nya_options" />
					<input type="submit" name="update" value="Submit">
				</p>
		</form>

	</div>
<?php }
	
	
add_filter('get_comment_author_link','nofollow_del');

?>