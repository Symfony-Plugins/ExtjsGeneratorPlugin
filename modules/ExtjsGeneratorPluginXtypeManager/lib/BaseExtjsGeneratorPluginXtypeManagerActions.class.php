<?php

class BaseExtjsGeneratorPluginXtypeManagerActions extends sfActions
{

  public function executeFind(sfWebRequest $request)
  {
    $xtype = $request->getParameter('xtype');
    $this->forward404Unless($xtype);

    $components = array(
      'objectActions' => 'objectactions',
      'topToolbar' => 'toptoolbar',
      'bottomToolbar' => 'bottomtoolbar',
      'datastore' => 'store',
      'columnRenderers' => 'columnrenderers',
      'columnModel' => 'columnmodel',
      'gridpanel' => 'gridpanel',
      'tabpanel' => 'tabpanel',
      'filterpanel' => 'filterpanel',
      'formpanel' => 'formpanel',
      'listpanel' => 'listpanel'
    );

    foreach($components as $action => $component)
    {
      if(strstr($xtype, $component))
      {
        // Forward to the "real" javascript action
        $this->forward(@array_shift(explode($component, $xtype, 2)), $action);
      }
    }
  }
}
