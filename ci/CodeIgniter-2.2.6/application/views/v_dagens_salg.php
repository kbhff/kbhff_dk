<!DOCTYPE html>
<html lang="da">
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?></title>

<link rel="stylesheet" href="<?php echo base_url(); ?>ressources/kbhff_2012.css" type="text/css" media="screen" />
<?php echo isset($library_src) ? $library_src : ''; ?>
<script type="text/javascript" charset="utf-8" src="/ressources/jquery.form.js"></script>
<script type="text/javascript" src="/ressources/jquery/jquery.datepick.js"></script>
<script type="text/javascript" src="/ressources/jquery/jquery.datepick-da.js"></script>
<link rel="STYLESHEET" type="text/css" href="/ressources/1st.datepick.css">
<script language="JavaScript" type="text/javascript">
$(function() {
	$('#dato').datepick({ dateFormat: 'yyyy-mm-dd' });
	$('#dato2').datepick({ dateFormat: 'yyyy-mm-dd' });
});
</script>
<link rel="shortcut icon" href="/img/favicon.ico" />
</head>
<body>
<span id="tt">
<span ID="title" style="float: left;" onClick="window.location.href='/minside/';" title="Til min forside">K&Oslash;BENHAVNS<br>
F&Oslash;DEVAREF&AElig;LLESSKAB <span id="green">/ MEDLEMSSYSTEM</span></span>
<button class="form_button" style="float: right; margin-top:33px;" onClick="window.location.href='http://kbhff.dk';">G&Aring; TIL KBHFF</button>
<img src="/img/banner.jpg" alt="K&oslash;benhavns F&oslash;devare F&aelig;llesskab" width="800" height="188" border="0">
	<?php 
		echo getMenu(site_url(), $this->session->userdata('permissions'), $this->session->userdata('uid')); 
	?>
<h1><?php echo $heading;?></h1>
<?php echo $content;?>
<br>
<table>
<tr class="theader"><td>Ydelse</td><td align="right">Kontant</td><td  align="right" class="grey">Mobilepay</td></tr>
<?php 
echo ('<tr><td>Grøntposer</td><td align="right">' . $kontant_groent . '</td><td class="grey" align="right">' . $mobilepay_groent . "</td></tr>\n");
echo ('<tr><td>Frugtposer</td><td align="right">' . $kontant_frugt . '</td><td  class="grey" align="right">' . $mobilepay_frugt . "</td></tr>\n");
echo ('<tr><td>Aspargesposer</td><td align="right">' . $kontant_asparges . '</td><td  class="grey" align="right">' . $mobilepay_asparges . "</td></tr>\n");
echo ('<tr><td>Stofposeandele</td><td align="right">' . $kontant_stofpose . '</td><td  class="grey" align="right">' . $mobilepay_stofpose . "</td></tr>\n");
echo ('<tr><td>Indmeldelser</td><td align="right">' . $kontant_indmeldelse . '</td><td  class="grey" align="right">' . $mobilepay_indmeldelse . "</td></tr>\n");
echo ('<tr><td>Kontingenter</td><td align="right">' . $kontant_kontingent . '</td><td  class="grey" align="right">' . $mobilepay_kontingent . "</td></tr>\n");
?>
</table>
<br>
<br>
<hr>

<table class="posts">
<tr class="odd">
<td>&nbsp;</td>
<td><strong>Afhentningsdag</strong></td>
<td><strong>Sidste ordre</strong></td>
</tr>
<?php 

	$classes = Array('even', 'odd');
	$count = 0;
	foreach ($afhentningsdage as $afhentningsdag)
	{
		echo '		<tr class="'.$classes[$count%2].'"'.">\n		";
		echo '			<td><a href="/admin/dagens_salg/' . $afhentningsdag['division'] . '/' . $afhentningsdag['uid'] .'">Se dagens salg</a></td>'."\n";
		echo '			<td>'.$afhentningsdag['pickupdate'].'</td>'."\n";
		echo '			<td>'.$afhentningsdag['lastorder'].'</td>'."\n";
		echo "		</tr>\n";
		$count++;
	}


?>
</table>
<br>
</span>
<hr align="left" id="bottomhr">
<?php echo isset($script_head) ? $script_head : ''; ?>
<?php echo isset($script_foot) ? $script_foot : ''; ?>
</body>
</html>