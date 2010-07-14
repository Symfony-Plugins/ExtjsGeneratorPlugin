  public function executeCombo(sfWebRequest $request)
  {
    sfConfig::set('sf_escaping_strategy', false);
    $request->setRequestFormat('json');
    $this->forward404Unless($request->getParameter('model'));

    $widgetConfig = array(
      'model' => $request->getParameter('model'),
      'method' => $request->getParameter('method', '__toString'),
      'key_method' => $request->getParameter('key_method', 'getPrimaryKey'),
      'order_by' => $request->getParameter('order_by', null),
      'group_by' => $request->getParameter('group_by', null),
      'query_methods' => $request->getParameter('query_methods', array()),
      'multiple' => $request->getParameter('multiple', false),
    );
    if(count($widgetConfig['group_by'])) $$widgetConfig['group_by'] = json_decode($widgetConfig['group_by']);
    if(count($widgetConfig['query_methods'])) $$widgetConfig['query_methods'] = json_decode($widgetConfig['query_methods']);

    if($request->getParameter('query', false))
    {
      $criteria = PropelQuery::from($request->getParameter('model'));
      $criteria->add($request->getParameter('group_by'), '%' . $request->getParameter('query') . '%', Criteria::LIKE);
      $widgetConfig['criteria'] = $criteria;
    }

    $widget = new ExtjsWidgetFormPropelChoice($widgetConfig);

    $options = array();
    foreach ($widget->getChoices() as $key => $option)
    {
      $options[] = array('value' => $key, 'display' => $option);
    }

    $this->jsonStr = json_encode(array(
      'totalCount' => count($options),
      'data' => $options
    ));
  }
