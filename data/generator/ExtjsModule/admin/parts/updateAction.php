  public function executeUpdate(sfWebRequest $request)
  {
    $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject($this->configuration->getQueryMethods());
    $this->form = $this->configuration->getForm($this-><?php echo $this->getSingularName() ?>);
<?php echo $this->getFormCustomization('edit') ?>
    if($request->getParameter('cmd') == 'from_request')
    {
      foreach ($this->configuration->getFormFields($this->form, $this->form->isNew() ? 'new' : 'edit') as $fieldset => $fields)
      {
        foreach ($fields as $name => $field)
        {
          $sfGuardUser = $request->getParameter('sf_guard_user', array());
          //TODO: figure out how to get the right csrf token for the form on the client without the form being bound to an object
          //if(!isset($sfGuardUser[$field->getName()]) && $field->getName() != $this->form->getCSRFFieldName()) unset($this->form[$name]);
          if(!isset($sfGuardUser[$field->getName()])) unset($this->form[$name]);
        }
      }
    }
    $this->processForm($request, $this->form);

    if($request->getContentType() == 'multipart/form-data')
    {
      sfConfig::set('sf_web_debug', false);
      $request->setFormat('json', 'text/html');
    }
  }
