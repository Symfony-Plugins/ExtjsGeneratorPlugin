  public function executeCombo(sfWebRequest $request)
  {
    sfConfig::set('sf_escaping_strategy', false);
    $request->setRequestFormat('json');
    $this->forward404Unless($request->getParameter('model'));

    $widgetConfig = array(
      'model' => $request->getParameter('model'),
      'php_name' => $request->getParameter('php_name', null),
      'method' => $request->getParameter('method', '__toString'),
      'key_method' => $request->getParameter('key_method', 'getPrimaryKey'),
      'order_by' => $request->getParameter('order_by', null),
      'group_by' => $request->getParameter('group_by', null),
      'query_methods' => $request->getParameter('query_methods', array()),
      'multiple' => $request->getParameter('multiple', false),
    );
    if(count($widgetConfig['order_by'])) $widgetConfig['order_by'] = json_decode($widgetConfig['order_by']);
    if(count($widgetConfig['query_methods'])) $widgetConfig['query_methods'] = json_decode($widgetConfig['query_methods']);

    // if you want typeAhead to filter php_name must be provided
    if($request->getParameter('php_name', false))
    {
      $criteria = PropelQuery::from($request->getParameter('model'));

      // only limit if not paging
      if(sfConfig::get('app_extjs_gen_plugin_remote_combo_pageSize', 0) == 0)
      {
        $criteria->limit(sfConfig::get('app_extjs_gen_plugin_remote_combo_limit', 50));
      }

      if($request->getParameter('query', false))
      {
        $query = str_replace('*', '%', $request->getParameter('query'));
        $criteria->where($request->getParameter('model').'.'.$request->getParameter('php_name').' '.Criteria::LIKE.' ?', '%' . $query . '%');
      }
      $widgetConfig['criteria'] = $criteria;
    }

    $widget = new ExtjsWidgetFormPropelChoice($widgetConfig);

    if($request->getParameter('limit', false))
    {
      $totalcount = $widget->getCount();
      $c = $widget->getCriteria();
      $c->limit($request->getParameter('limit'));
      $c->offset($request->getParameter('start', 0));
      $widget->setCriteria($c);
    }

    $options = array();
    foreach ($widget->getChoices() as $key => $option)
    {
      $options[] = array('value' => $key, 'display' => $option);
    }

    if(!isset($totalcount)) $totalcount = count($options);

    $this->jsonStr = json_encode(array(
      'totalCount' => $totalcount,
      'data' => $options
    ));
  }
