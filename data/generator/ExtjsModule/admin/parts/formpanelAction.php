  public function executeFormpanel(sfWebRequest $request)
  {
    //$this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject($this->configuration->getQueryPartials());
    $this->form = $this->configuration->getForm();
    sfConfig::set('sf_escaping_strategy', false);
  }
