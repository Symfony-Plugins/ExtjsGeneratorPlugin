  public function executeEdit(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject($this->configuration->getQueryMethods());
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);
<?php echo $this->getFormCustomization('edit') ?>
  }
