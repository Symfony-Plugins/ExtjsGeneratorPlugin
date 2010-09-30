  public function getFilterpanelConfig()
  {
    return array_merge(array(
      'deferredRender' => true,
      'title'      => 'Filters',
      'autoScroll' => true,
      'bodyStyle'  => 'padding: 5px 0px 0px 10px; position: relative;',
      'labelAlign' => 'top',
      'plugins'   => $this->getFilterpanelPlugins(),
      'defaults'   => array('anchor' => '85%'),
    ), <?php echo $this->asPhp(isset($this->config['filterpanel']['config']) ? $this->config['filterpanel']['config'] : array()) ?>);
<?php unset($this->config['filterpanel']['config']) ?>
  }

  public function getFilterpanelPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array();
  if(isset($this->config['filterpanel']['plugins']))
  {
    if(!is_array($this->config['filterpanel']['plugins']))
    {
      $pluginsArr[] = $this->config['filterpanel']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['filterpanel']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['filterpanel']['plugins']) ?>
  }
  
  public function getFilterpanelPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['filterpanel']['partials']) ? $this->config['filterpanel']['partials'] : array()) ?>;
<?php unset($this->config['filterpanel']['partials']) ?>
  }
  
  public function filterpanelIsDisabled()
  {
    return <?php echo isset($this->config['filterpanel']['disabled']) && $this->config['filterpanel']['disabled'] === true ? 'true' : 'false' ?>;
<?php unset($this->config['filterpanel']['disabled']) ?>
  }
  
  public function hasFilterForm()
  {
    return <?php echo !isset($this->config['filter']['class']) || false !== $this->config['filter']['class'] ? 'true' : 'false' ?>;
  }

  public function getFilterFormClass()
  {
    return '<?php echo isset($this->config['filter']['class']) ? $this->config['filter']['class'] : 'Extjs' .ucfirst($this->getModelClass()).'FormFilter' ?>';
<?php unset($this->config['filter']['class']) ?>
  } 
