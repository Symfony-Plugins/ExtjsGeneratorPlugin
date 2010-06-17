  public function executeIndex(sfWebRequest $request)
  {
    // filtering
    if ($request->getParameter('filters'))
    {
      $this->setFilters($request->getParameter('filters'));
    }

    // sorting
    if ($request->getParameter('sort'))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }

    if($request->getRequestFormat() == 'json')
    {
      // pager
      $limit = $request->getParameter('limit', $this->configuration->getPagerMaxPerPage());
      $page = floor($request->getParameter('start', 0) / $limit)+1;
      $this->setPage($page);

      $this->pager = $this->getPager();
      $this->sort = $this->getSort();
    }

    // dynamic javascript
    if($request->getRequestFormat() == 'js')
    {
      $this->getFilterForm();
      sfConfig::set('sf_escaping_strategy', false);
      return;
    }
  }
