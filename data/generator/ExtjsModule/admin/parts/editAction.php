  public function executeEdit(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject($this->configuration->getQueryPartials());
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);
  }
