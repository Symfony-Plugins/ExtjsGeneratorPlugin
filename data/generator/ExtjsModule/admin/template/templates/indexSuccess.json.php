[?php
use_helper('I18N', 'Date');
$dataArray = array();
foreach ($pager->getResults() as $i => $<?php echo $this->getSingularName() ?>)
{
<?php $key = sfInflector::underscore($this->getPrimaryKeys(true)) ?>
<?php $fields = $this->configuration->getValue('list.display') ?>
<?php if(!array_key_exists($key, $fields)) $fields = array( $key => $this->configuration->getFieldConfiguration('list', $key)) + $fields ?>
<?php foreach ($fields as $name => $field): ?>
<?php if(($field->hasFlag() && $field->isPlugin()) || $field->isHidden()) continue ?>
<?php echo $this->addCredentialCondition(sprintf("  \$dataArray[\$i]['%s'] = %s;\n", $name, $this->renderField($field))) ?>
<?php endforeach; ?>
}
echo json_encode(array('totalCount' => $pager->getNbResults(), 'data' => $dataArray));
?]