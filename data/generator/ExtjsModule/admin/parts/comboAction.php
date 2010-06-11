  public function executeCombo(sfWebRequest $request)
  {
    sfConfig::set('sf_escaping_strategy', false);
    $request->setRequestFormat('json');
    $this->forward404Unless($request->getParameter('model'));

    $widget = new ExtjsWidgetFormPropelChoice(array(
      'model' => $request->getParameter('model'),
      'method' => $request->getParameter('method', '__toString'),
      'key_method' => $request->getParameter('key_method', 'getPrimaryKey'),
      'order_by' => $request->getParameter('order_by', null),
      'group_by' => $request->getParameter('group_by', null),
      //'query_methods' => json_decode($request->getParameter('query_methods', array())),
      //'criteria' => $request->getParameter('criteria', null),
      //'connection' => $request->getParameter('connection', null),
      //'multiple' => $request->getParameter('multiple', false),
    ));

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