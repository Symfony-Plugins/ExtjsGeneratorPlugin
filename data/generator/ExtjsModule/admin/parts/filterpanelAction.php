  public function executeFilterpanel(sfWebRequest $request)
  {
    $this->filters = $this->configuration->getFilterForm($this->getFilters());  
    sfConfig::set('sf_escaping_strategy', false);
  }
