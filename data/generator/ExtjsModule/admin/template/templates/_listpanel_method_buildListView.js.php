[?php // @object $sfExtjs3Plugin string $className and @object $listpanel provided
$listView = new stdClass();
$listviewPlugins = array();
$listView->config_array = array(  
  'store' => 'this.ds',
<?php foreach ($this->configuration->getListviewConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

<?php 
foreach ($this->configuration->getValue('list.display') as $name => $field)
{
  echo $this->addCredentialCondition(sprintf("%s;\n", $this->renderColumnField($field, 'listView')), $field->getConfig());
  
  if($field->isPlugin())
  {
    echo $this->addCredentialCondition(sprintf("%s;\n", $this->renderColumnPlugin($field, 'listView')), $field->getConfig());
  }
}
?>

// get plugins from generator
<?php 
foreach ($this->configuration->getValue('list.display') as $name => $field)
{
  if($plugin = $this->renderListViewPlugin($field)) echo sprintf("%s;\n", $plugin);
}
?>
//merge plugins set from listview.params with generated plugins
if(isset($listView->config_array['plugins']))
{
  $listView->config_array['plugins'] = array_merge($listviewPlugins, $listView->config_array['plugins']);
}
elseif(count($listviewPlugins))
{
  $listView->config_array['plugins'] = $listviewPlugins;
}

// buildListView
$listpanel->methods['buildListView'] = $sfExtjs3Plugin->asMethod("return ".$sfExtjs3Plugin->asAnonymousClass($listView->config_array).";");
if(isset($listView->variables)) $listpanel->variables = array_merge($listView->variables, $listpanel->variables);
if(isset($listView->methods)) $listpanel->methods = array_merge($listView->methods, $listpanel->methods);
?]