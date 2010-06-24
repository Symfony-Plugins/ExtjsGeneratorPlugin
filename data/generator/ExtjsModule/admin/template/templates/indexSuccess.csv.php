[?php
use_helper('I18N', 'Date');
$header = '';
<?php $fields = $this->configuration->getValue('export.display') ?>
<?php foreach ($fields as $name => $field): ?>
<?php if(($field->hasFlag() && $field->isPlugin()) || $field->isHidden()) continue ?>
<?php echo $this->addCredentialCondition(sprintf("\$header .= '\"'.str_replace('\"', '\"\"', '%s').'\",';\n", $field->getConfig('label', ''))) ?>
<?php endforeach; ?>
$header = preg_replace("/\n|\r/", " ", $header);
$header = preg_replace("/\s\s+/", " ", $header);
echo substr($header, 0, -1)."\n";
  
foreach ($<?php echo $this->getSingularName() ?>s as $<?php echo $this->getSingularName() ?>)
{
  $row = '';
<?php $fields = $this->configuration->getValue('export.display') ?>
<?php foreach ($fields as $name => $field): ?>
<?php if(($field->hasFlag() && $field->isPlugin()) || $field->isHidden()) continue ?>
<?php if($field->getType() == 'Text'): ?>
<?php echo $this->addCredentialCondition(sprintf("  \$row .= '\"'.str_replace('\"', '\"\"', (%s)).'\",';\n", $this->renderField($field))) ?>
<?php else:?>
<?php if($field->getType() == 'Boolean' && $this->configuration->getExportBooleanAsString() && !$field->getRenderer()): ?>
<?php $field->setRenderer('ExtjsGeneratorUtil::renderBooleanToString') ?>
<?php $field->setRendererArguments(array($this->configuration->getExportBooleanStringValues())) ?>
<?php endif;?>
<?php if($field->getType() == 'Date' && $this->configuration->getExportDateFormat() && !$field->getConfig('date_format')): ?>
<?php $field->setDateFormat($this->configuration->getExportDateFormat()) ?>
<?php endif;?>
<?php echo $this->addCredentialCondition(sprintf("  \$row .= '\"'.(%s).'\",';\n", $this->renderField($field))) ?>
<?php endif;?>
<?php endforeach; ?>
  $row = preg_replace("/\n|\r/", " ", $row);
  $row = preg_replace("/\s\s+/", " ", $row);
  echo substr($row, 0, -1)."\n";
}
?]