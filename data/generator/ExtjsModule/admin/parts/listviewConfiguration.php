  public function getListviewConfig()
  {
    $stripedRowTpl = "new Ext.XTemplate('<tpl for=\"rows\">',
  '<dl class=\"x-grid3-row {[xindex % 2 === 0 ? \"\" :  \"x-grid3-row-alt\"]}\">',
  '<tpl for=\"parent.columns\">',
  '<dt style=\"width:{[values.width*100]}%;text-align:{align};\">',
  '<em unselectable=\"on\"<tpl if=\"cls\">  class=\"{cls}</tpl>\">{[values.tpl.apply(parent)]}',
  '</em></dt></tpl><div class=\"x-clear\"></div></dl></tpl>'
)";
      
    return array_merge(array(
      'xtype' => 'listview',
      'trackOver' => sfConfig::get('app_extjs_gen_plugin_list_trackMouseOver', true),
      'reserveScrollOffset' => true,
<?php if(sfConfig::get('app_extjs_gen_plugin_gridpanel_trackMouseOver', true)): ?>      
      'overClass' => 'x-grid3-row-over',
<?php endif; ?>       
<?php if (sfConfig::get('app_extjs_gen_plugin_list_stripeRows', true)): ?>      
      'tpl' => $stripedRowTpl,
<?php endif; ?>      
      'stateful' => true,
      'stateId' => '<?php echo $this->params['route_prefix'] ?>listview',
      'plugins' => $this->getListviewPlugins()
    ), <?php echo $this->asPhp(isset($this->config['listview']['config']) ? $this->config['listview']['config'] : array()) ?>);
<?php unset($this->config['listview']['config']) ?>
  }

  public function getListviewType()
  {
    return '<?php echo isset($this->config['datastore']['grouping']) && isset($this->config['datastore']['grouping']['field']) ? 'Grouping' : 'Grid' ?>';
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
