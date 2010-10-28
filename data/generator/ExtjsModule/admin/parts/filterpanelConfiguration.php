  public function getFilterpanelConfig()
  {
    return array_merge(array(
      'title'      => 'Filters',
      'autoScroll' => true,
      'bodyStyle'  => 'padding:5px 10px 0px 10px; border-bottom: 1px solid #99BBE8',
      'labelAlign' => 'top',
      'border' => false,
      'plugins'   => $this->getFilterpanelPlugins(),
      'defaults'   => array('anchor' => '100%'),
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

  public function getFilterpanelExtends()
  {
    return <?php echo isset($this->config['filterpanel']['extends']) ? "'{$this->config['filterpanel']['extends']}'" : 'null' ?>;
<?php unset($this->config['filterpanel']['extends']) ?>
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
