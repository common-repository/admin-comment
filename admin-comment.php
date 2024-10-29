<?php
/*
Plugin Name: comment-admin
Plugin URI: http://jessai.fr.nf/archives/11
Description: You an choose the color of admin's comments.
Version: 2.2.1
Author: jessai
Author URI: http://jessai.fr.nf
*/
if(get_option('PF_COMMENTS_OK') == '' || get_option('PF_COMMENTS_OK') == 'NO') {
	delete_option('PF_COMMENTS_COLOR');
		
	add_option('PF_COMMENTS_COLOR', '#CDDDE7', 'Valeur hexadecimale de la couleur du fond', false);
}


function commentadmininit() {
	$url = get_settings('siteurl');

?>

    <style type="text/css">
		.bypostauthor
		{
  			background: <?php echo get_option("PF_COMMENTS_COLOR"); ?>;
			}
    </style>
<?php
}

function pf_showMessagecomments($message) {
	echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
}
	
function pf_options_comments() {
	if($_POST['Submit']) {
		if(get_option('PF_COMMENTS_OK') == '')	add_option('PF_COMMENTS_OK', 'YES', "Setting indicating", false);
		
		if(!ereg("^[#]{1}[0-9a-fA-F]{6}$",trim($_POST['PF_COMMENTS_COLOR']))) {
			pf_showMessage("La valeur pour la couleur du fond doit &ecirc;tre au format h&eacute;xad&eacute;cimal.");
		} else {
			update_option('PF_COMMENTS_COLOR',trim($_POST['PF_COMMENTS_COLOR']));
		}

		pf_showMessagecomments("Param&egrave;tres sauvegard&eacute;s.");
	}
?>
		<script language="javascript">
	if(!window.$){
			var head = document.getElementsByTagName('head').item(0);
			var script = document.createElement('script');
			script.src = "<?PHP echo $url; ?>/wp-content/plugins/admin-comment/jquery.js";
			script.type = 'text/javascript';
			script.id = 'loadScript';
			head.appendChild(script)
	}
	</script>   
	<div class="wrap">
 <script type="text/javascript" src="<?PHP echo $url; ?>/wp-content/plugins/admin-comment/farbtastic.js"></script>
 <link rel="stylesheet" href="<?PHP echo $url; ?>/wp-content/plugins/admin-comment/farbtastic.css" type="text/css" />
 
 <script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
    $('#demo').hide();
    var p = $('#picker').farbtastic('#PF_COMMENTS_COLOR');
    p.css('opacity', 0.00);
    var selected;
    $('#PF_COMMENTS_COLOR')
      .focus(function() {
        if (selected) {
          $(selected).css('opacity',1).removeClass('colorwell-selected');
        }
        p.css('opacity', 1);
        $(selected = this).css('opacity', 1).addClass('colorwell-selected');
      });
  });
 </script>
 <div id="demo" style="color: red; font-size: 1.4em">jQuery.js is not present. You must install jQuery in this folder for the demo to work.</div>
	<h2>Param&egrave;tres du plugin : Marquer commentaires administrateur</h2>
	<form method="post">
	<p class="submit">
		<input type="submit" name="Submit" value="Mettre &agrave; jour les options &raquo;" />
	</p>
	<fieldset class="options">
		<table class="editform optiontable">
	        <tr valign="middle">
				<th scope="row">Couleur du fond :</th>
				
				<td ><input type="text" id="PF_COMMENTS_COLOR" name="PF_COMMENTS_COLOR" value="<?PHP echo get_option("PF_COMMENTS_COLOR"); ?>" size="10"  maxlength="7" /></td><td id="picker" ></td>
			</tr>
		</table>
	</fieldset>
    <p class="submit">
        <input type="submit" name="Submit" value="Mettre &agrave; jour les options &raquo;" />
    </p>
    </form>
    
    </div>
<?php
}

function pf_comments_add_admin() {
	add_options_page('PF Comments', 'Commentaires admin', 9, basename(__FILE__), 'pf_options_comments');
}

function my_comments_template() {
	$theme = explode('themes/', TEMPLATEPATH );
	$destination = dirname(__FILE__).'/comments'.$theme[1].'.php';
	if (!file_exists($destination)) {
		$fs = fopen($destination,'w');	
		$source = TEMPLATEPATH.'/comments.php';
		$lignes = file($source);
		foreach($lignes as $ligne) {
			$mystring = trim($ligne);
			$findme   = '<li class="<?';
			$pos = strpos($mystring, $findme);
			if ($pos === false){
				fwrite($fs,$ligne);
			}
			else {
				$ligne = '	<?php $oddcomment = addcomment_replace($comment);?>  '.$ligne;
				fwrite($fs,$ligne);
			}
			/*if (trim($ligne) == '<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">')
			{
				$ligne = '	<?php $oddcomment = addcomment_replace($comment);?>  <li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">';
			}*/
			//fwrite($fs,$ligne);
		}
		fclose($fs);
	}
	return $destination;
}

function addcomment_replace($content) {
	global $comment;
		    if ($content->user_id == 1)
		    {
			    $oddcomment = "admin";
		    }
		    else
		    {
			    $oddcomment = "alt";
		    }
	return $oddcomment;
}

//add_filter('comments_template','my_comments_template');
add_action('wp_head', 'commentadmininit');
add_action('admin_menu', 'pf_comments_add_admin');
?>