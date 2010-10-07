  public function getListviewConfig()
  {
    $stripedRowTpl = <<<EOF
new Ext.XTemplate('<tpl for="rows">',
  '<dl class="x-grid3-row {[xindex % 2 === 0 ? "" :  "x-grid3-row-alt"]}">',
    '<tpl for="parent.columns">',
      '<dt style="width:{[values.width*100]}%;text-align:{align};">',
        '<em unselectable="on"<tpl if="cls"> class="{cls}</tpl>">',
          '{[values.tpl.apply(parent)]}',
        '</em>',
      '</dt>',
    '</tpl>',
    '<div class="x-clear"></div>',
  '</dl>',
'</tpl>')
EOF;
      
    $defaultConfig = array(
      'xtype' => $this->getListviewXtype() . 'listview',
      'scrollOffset' => 20,        
<?php if (sfConfig::get('app_extjs_gen_plugin_list_trackMouseOver', true)): ?>           
      'trackOver' => true,
<?php else: ?>   
      'overClass' => false,   
<?php endif; ?>           
<?php if (sfConfig::get('app_extjs_gen_plugin_list_stripeRows', true)): ?>      
      'tpl' => $stripedRowTpl,
<?php endif; ?>      
      'stateful' => true,
      'stateId' => '<?php echo $this->params['route_prefix'] ?>listview',
      'plugins' => $this->getListviewPlugins()
    );
    
    if($this->getListBatchActions())
    {
      $defaultConfig['plugins'] = array_merge(array("'lvcheckboxselection'"), $defaultConfig['plugins']);
      $defaultConfig['multiSelect'] = true;
      unset($defaultConfig['trackOver'], $defaultConfig['overClass']);
    }
      
    return array_merge($defaultConfig, <?php echo $this->asPhp(isset($this->config['listview']['config']) ? $this->config['listview']['config'] : array()) ?>);
<?php unset($this->config['listview']['config']) ?>
  }

  public function getListviewXtype()
  {
    return '<?php echo isset($this->config['datastore']['grouping']) && isset($this->config['datastore']['grouping']['field']) && isset($this->config['datastore']['grouping']['start_grouped']) && $this->config['datastore']['grouping']['start_grouped'] ? 'grouping' : '' ?>';
  }

  public function getListviewGroupTextTpl()
  {
    return '<?php echo isset($this->config['datastore']['grouping']['groupTextTpl']) ? $this->config['datastore']['grouping']['groupTextTpl'] : null  ?>';
  }

  public function getListviewPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array();
  if(isset($this->config['listview']['plugins']))
  {
    if(!is_array($this->config['listview']['plugins']))
    {
      $pluginsArr[] = $this->config['listview']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['listview']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['listview']['plugins']) ?>
  }

  public function getListviewPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['listview']['partials']) ? $this->config['listview']['partials'] : array()) ?>;
<?php unset($this->config['listview']['partials']) ?>
  }
