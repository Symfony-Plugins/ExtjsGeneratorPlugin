<?php

class ExtjsGeneratorPluginRouting
{

  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $event->getSubject()->prependRoute('extjs_gen_plugin_get_xtype', new sfRoute('/js/getXtype/:xtype/*.:sf_format', array(
      'module' => 'ExtjsGeneratorPluginXtypeManager',
      'action' => 'find',
      'sf_format' => 'js'
    ), array(
      'sf_format' => 'js'
    )));

  }
}
