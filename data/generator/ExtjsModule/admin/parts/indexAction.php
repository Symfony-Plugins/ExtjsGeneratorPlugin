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
      sfConfig::set('sf_escaping_strategy', false);
      
      // pager
      $limit = $request->getParameter('limit', $this->configuration->getPagerMaxPerPage());
      $page = floor($request->getParameter('start', 0) / $limit)+1;
      $this->setPage($page);

      $this->pager = $this->getPager();
      $this->sort = $this->getSort();
    }
    
    if($request->getRequestFormat() == 'csv')
    {
      $this->setLayout(false);
      sfConfig::set('sf_escaping_strategy', false);
      $this->getResponse()->setHttpHeader('Content-Type', 'application/csv', true);
      $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment; filename="<?php echo $this->configuration->getExportTitle() ?>.csv"', true);
      $this->getResponse()->setHttpHeader('Pragma','public', true);
      $query = $this->buildQuery();
      $query->setFormatter(ModelCriteria::FORMAT_ON_DEMAND);
      $this-><?php echo $this->getSingularName() ?>s = $query->find();
    }

    // dynamic javascript
    if($request->getRequestFormat() == 'js')
    {
      $this->getFilterForm();
      sfConfig::set('sf_escaping_strategy', false);
      return;
    }
  }
