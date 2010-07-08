  public function executeUpdate(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject($this->configuration->getQueryMethods());
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);
<?php echo $this->getFormCustomization('edit') ?>
    $this->processForm($request, $this->form);

    if($request->getContentType() == 'multipart/form-data')
    {
      sfConfig::set('sf_web_debug', false);
      $request->setFormat('json', 'text/html');
    }
  }
