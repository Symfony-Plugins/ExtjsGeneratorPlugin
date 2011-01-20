[?php // @object $sfExtjs3Plugin string $className and @object columnModel provided
<?php
if (count($this->configuration->getListRowExpanderDisplay())) :
  $fields = $this->configuration->getListRowExpanderDisplay();
  $template = '';
  foreach ($fields as $field)
  {
    $template .= "<p><b>{$this->configuration->getValue("list.$field.label", sfInflector::humanize($field))}:</b>{" . $field . "}</p><br/>";
  }

  $this->createPartialFile('_columnModel_rowExpander', "<?php
\$columnModel->expander = \"
// row expander
this.{$this->getModuleName()}_rowExpander = new Ext.ux.grid.plugin.RowExpander({
  tpl : new Ext.Template(
    '$template'
  )
});
\";
");
?>
// expand columns renderer partial
include_partial('columnModel_rowExpander', array('columnModel' => $columnModel));
<?php endif; ?>
$expander = (isset($columnModel->expander)) ? $columnModel->expander : '';
// constructor
$configArr['parameters'] = 'c';
$configArr['source'] = "$expander
// columnModel config
this.columnModel_config = " . ( isset($columnModel->config_array) && count($columnModel->config_array) ? $sfExtjs3Plugin->asAnonymousClass($columnModel->config_array) : '{}' ) .
";

// combine columnModel config with arguments
Ext.app.sf.$className.superclass.constructor.call(this, Ext.apply(this.columnModel_config, c));";
$columnModel->methods["constructor"] = $sfExtjs3Plugin->asMethod($configArr);