  public function executeFilterpanel(sfWebRequest $request)
  {
    $this->getFilterForm();
    sfConfig::set('sf_escaping_strategy', false);
  }
