<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include_http_metas() ?>
<meta name="title" content="ExtjsGeneratorPlugin Ext.ux.IconMgr Preview" />
<title>ExtjsGeneratorPlugin Ext.ux.IconMgr Preview</title>
<style type="text/css">
<!--
body {
  font-family: Trebuchet MS, arial, sans-serif; color: #000; background-color: #fff; width: 8000px;
}

p.preview {
  margin: 0 4px 0px 0; color: #666; float: left;
}

h3 {
  color: #666; text-decoration: underline;
}
-->
</style>
</head>
<body>
<h3>Total Icons Avaliable: <?php echo count($icons) ?></h3>
<p class="preview">
<?php $i=0; foreach ($icons as $name => $icon):?>
<?php if($i==33): $i=0 ?>
</p>
<p class="preview">
<?php endif;?>
    <img src="<?php echo $icon ?>" width="16" height="16" /> <?php echo $name ?> <br />
<?php $i++; endforeach;?>
</p>
</body>
</html>
