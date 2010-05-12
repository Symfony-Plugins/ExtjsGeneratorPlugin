[?php
use_helper('I18N', 'Date');

<?php 
$form = $this->configuration->getForm();
$key = sfInflector::underscore($this->getPrimaryKeys(true));
$needsId = true;
foreach ($this->configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit')  as $fieldset => $fields)
{
  foreach ($fields as $name => $field)
  {
    if($name == $key) $needsId = false;
    echo $this->addCredentialCondition(sprintf("\$dataArray['%s'] = %s;\n", $name, "\$form->getDefault('$name')"));
  }
}
if($needsId)
{
  echo sprintf("\$dataArray['%s'] = %s;\n", $key, "\$form->getDefault('$key')");
}
?>

echo json_encode(array(
  'totalCount' => 1, 
  'data' => array($dataArray),
  'title' => <?php echo $this->getI18NString('edit.title', 'Edit '.$this->configuration->getObjectName(), false) ?>
));
?]