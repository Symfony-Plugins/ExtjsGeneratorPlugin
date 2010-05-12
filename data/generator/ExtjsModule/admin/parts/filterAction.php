  public function executeFilter(sfWebRequest $request)
  {
    // pager
    if ($request->hasParameter('_reset'))
    {
      $this->setFilters($this->configuration->getFilterDefaults());
      $this->forward($this->getModuleName(), 'index');
    }

    $this->filters = $this->configuration->getFilterForm($this->getFilters());

    $this->filters->bind($request->getParameter($this->filters->getName()));
    if ($this->filters->isValid())
    {
      $this->setFilters($this->filters->getValues());
      $this->forward($this->getModuleName(), 'index');
    }
    
    $limit = $request->getParameter('limit', $this->configuration->getPagerMaxPerPage());
    $page = floor($request->getParameter('start', 0) / $limit)+1;
    $this->setPage($page);

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();

    $this->setTemplate('index');
  }
