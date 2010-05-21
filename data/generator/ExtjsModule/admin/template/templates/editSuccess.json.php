[?php
use_helper('I18N', 'Date');

<?php
$form = $this->configuration->getForm();
foreach ($this->configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit')  as $fieldset => $fields)
{
  foreach ($fields as $name => $field)
  {
    echo $this->addCredentialCondition(sprintf("\$dataArray['%s'] = %s;\n", $name, "\$form->getDefault('$name')"));
  }
}
?>

echo json_encode(array(
  'totalCount' => 1,
  'data' => array($dataArray),
  'title' => <?php echo $this->getI18NString('edit.title', 'Edit '.$this->configuration->getObjectName(), false) ?>
));
?]