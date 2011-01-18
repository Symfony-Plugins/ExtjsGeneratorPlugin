  public function getGridpanelConfig()
  {
    return array_merge(array(
      'title'           => $this->getListTitle(),
      'autoScroll'      => true,
      'clicksToEdit'    => 1,
      'trackMouseOver'  => sfConfig::get('app_extjs_gen_plugin_list_trackMouseOver', true),
<?php if (sfConfig::get('app_extjs_gen_plugin_list_loadMask', true)): ?>
      'loadMask'        => true,
<?php endif; ?>
<?php if (sfConfig::get('app_extjs_gen_plugin_list_stripeRows', true)): ?>
      'stripeRows'      => true,
<?php endif; ?>
      'stateful'        => true,
      'stateId'         => '<?php echo $this->params['route_prefix'] ?>gridpanel',
      'viewConfig'      => array(
        'forceFit'      => sfConfig::get('app_extjs_gen_plugin_gridpanel_forceFit', false),
      ),
      'sm'              => 'this.getColumnModel().sm',
      'plugins'         => $this->getGridpanelPlugins(),
<?php if (sfConfig::get('app_extjs_gen_plugin_list_tabbed', true)) echo "      'header' => false," ?>
    ), <?php echo $this->asPhp(isset($this->config['gridpanel']['config']) ? $this->config['gridpanel']['config'] : array()) ?>);
<?php unset($this->config['gridpanel']['config']) ?>
  }

  public function getGridpanelType()
  {
    return '<?php echo isset($this->config['datastore']['grouping']) && isset($this->config['datastore']['grouping']['field']) ? 'Grouping' : 'Grid' ?>';
  }

  public function getGridpanelGroupTextTpl()
  {
    return '<?php echo isset($this->config['datastore']['grouping']['groupTextTpl']) ? $this->config['datastore']['grouping']['groupTextTpl'] : null  ?>';
  }

  public function getGridpanelPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array();
  if(isset($this->config['gridpanel']['plugins']))
  {
    if(!is_array($this->config['gridpanel']['plugins']))
    {
      $pluginsArr[] = $this->config['gridpanel']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['gridpanel']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['gridpanel']['plugins']) ?>
  }

  public function getGridpanelPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['gridpanel']['partials']) ? $this->config['gridpanel']['partials'] : array()) ?>;
<?php unset($this->config['gridpanel']['partials']) ?>
  }

  public function getGridpanelExtends()
  {
    return <?php echo isset($this->config['gridpanel']['extends']) ? "'{$this->config['gridpanel']['extends']}'" : 'null' ?>;
<?php unset($this->config['gridpanel']['extends']) ?>
  }
