  public function getListpanelConfig()
  {
    return array_merge(array(
      'title'           => $this->getListTitle(),
      'plugins'         => $this->getListpanelPlugins(),
<?php if (sfConfig::get('app_extjs_gen_plugin_list_tabbed', true)) echo "      'header' => false," ?>
    ), <?php echo $this->asPhp(isset($this->config['listpanel']['config']) ? $this->config['listpanel']['config'] : array()) ?>);
<?php unset($this->config['listpanel']['config']) ?>
  }

  public function getListpanelPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array();
  if(isset($this->config['listpanel']['plugins']))
  {
    if(!is_array($this->config['listpanel']['plugins']))
    {
      $pluginsArr[] = $this->config['listpanel']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['listpanel']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['listpanel']['plugins']) ?>
  }

  public function getListpanelPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['listpanel']['partials']) ? $this->config['listpanel']['partials'] : array()) ?>;
<?php unset($this->config['listpanel']['partials']) ?>
  }

  public function getListpanelExtends()
  {
    return <?php echo isset($this->config['listpanel']['extends']) ? "'{$this->config['listpanel']['extends']}'" : 'null' ?>;
<?php unset($this->config['listpanel']['extends']) ?>
  }
