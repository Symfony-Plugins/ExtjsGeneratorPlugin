  public function executeCreate(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
<?php echo $this->getFormCustomization('new') ?>    
    $this-><?php echo $this->getSingularName() ?> = $this->form->getObject();

    $this->processForm($request, $this->form);
  
    if($request->getContentType() == 'multipart/form-data')
    {
      sfConfig::set('sf_web_debug', false);
      $request->setFormat('json', 'text/html');
    }
  }
