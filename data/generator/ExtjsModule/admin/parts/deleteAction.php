  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject($this->configuration->getQueryPartials()))));

    $this->getRoute()->getObject($this->configuration->getQueryPartials())->delete();

    $this->getUser()->setFlash('notice', 'The item was deleted successfully.');
  }
