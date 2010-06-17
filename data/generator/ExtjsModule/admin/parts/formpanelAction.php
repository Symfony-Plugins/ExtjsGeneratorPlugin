  public function executeFormpanel(sfWebRequest $request)
  {
    //$this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject($this->configuration->getQueryMethods());
    $this->form = $this->configuration->getForm();
    sfConfig::set('sf_escaping_strategy', false);
<?php echo $this->getFormCustomization('form') ?>  }
