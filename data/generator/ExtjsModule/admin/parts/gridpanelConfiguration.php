  public function getGridpanelConfig()
  {
    return array_merge(array(
      'title'           => $this->getListTitle(),
      'cm'              => 'this.cm',      
      'autoScroll'      => true,
      'clicksToEdit'    => 1,
      'trackMouseOver'  => false,
      'loadMask'        => false,
      'stripeRows'      => true,
      'stateful'        => true,
      'stateId'         => '<?php echo $this->getModuleName() ?>gridpanel',
      'viewConfig'      => array(
        'forceFit'      => true,
      ),
      'sm'              => 'this.cm.sm',
      'plugins'         => $this->getFormpanelPlugins(),
<?php if (sfConfig::get('extjs_gen_plugin_list_tabbed')) echo "      'header' => false," ?>                                
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
