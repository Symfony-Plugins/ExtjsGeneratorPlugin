  public function getTabpanelConfig()
  {
    return array_merge(array(
      'deferredRender'  => true,
      'enableTabScroll' => true,
      'tabWidth'        => 200,
      'plugins'         => $this->getTabpanelPlugins(),
      'activeTab'       => $this->getTabpanelActiveTab(),
    ), <?php echo $this->asPhp(isset($this->config['tabpanel']['config']) ? $this->config['tabpanel']['config'] : array()) ?>);
<?php unset($this->config['tabpanel']['config']) ?>
  }

  public function getTabpanelPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array("'tabclosemenu'");
  if(isset($this->config['tabpanel']['plugins']))
  {
    if(!is_array($this->config['tabpanel']['plugins']))
    {
      $pluginsArr[] = $this->config['tabpanel']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['tabpanel']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['tabpanel']['plugins']) ?>
  }

  public function getTabpanelActiveTab()
  {
    return <?php echo isset($this->config['tabpanel']['active_tab']) ? $this->config['tabpanel']['active_tab'] : 0 ?>;
<?php unset($this->config['tabpanel']['active_tab']) ?>
  }
  
  public function getTabpanelPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['tabpanel']['partials']) ? $this->config['tabpanel']['partials'] : array()) ?>;
<?php unset($this->config['tabpanel']['partials']) ?>
  }      
