<?php

/**
 * Base actions for the ExtjsGeneratorPlugin IconMgrPreview module.
 * 
 * @package     ExtjsGeneratorPlugin
 * @subpackage  IconMgrPreview
 * @author      Benjamin Runnels <benjamin.r.runnels@citi.com>
 * @version     SVN: $Id: BaseActions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
abstract class BaseIconMgrPreviewActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $type = $this->getRequestParameter('type', false);
    $this->icons = array();
    $fulldir = dirname(__FILE__).'/../../../web/Ext.ux.IconMgr/icons/';
    $webdir = '/ExtjsGeneratorPlugin/Ext.ux.IconMgr/icons/';
    $dir = dir($fulldir);
    while(false !== ($entry = $dir->read()))
    {
      if($entry[0] == '.' || $entry[0] == '..') continue;
      $path_parts = pathinfo($fulldir.$entry);
      if(!isset($path_parts['extension'])) continue;
      if($path_parts['extension'] == 'png')
      {
        if($type && !strstr($entry, $type))
        {
          continue;
        }
        else
        {
          $this->icons[$path_parts['filename']] = $webdir.$entry;
        }
      }
    }
    $dir->close();
    ksort($this->icons);
    $this->setLayout(false);
  }
}
