  public function getBottomToolbarConfig()
  {
    return array_merge(array(
      'plugins'     => $this->getBottomToolbarPlugins(),
      'pageSize'    => $this->getPagerMaxPerPage(),
      'displayInfo' => true,
      'displayMsg'  => 'Displaying '.$this->getObjectName().'s {0} - {1} of {2}',
      'emptyMsg'    => 'No '.$this->getObjectName().'s to display',
    ), <?php echo $this->asPhp(isset($this->config['bottom_toolbar']['config']) ? $this->config['bottom_toolbar']['config'] : array()) ?>);
<?php unset($this->config['bottom_toolbar']['config']) ?>
  }

  public function getBottomToolbarPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array();
  if(isset($this->config['bottom_toolbar']['plugins']))
  {
    if(!is_array($this->config['bottom_toolbar']['plugins']))
    {
      $pluginsArr[] = $this->config['bottom_toolbar']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['bottom_toolbar']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['bottom_toolbar']['plugins']) ?>
  }

  public function getBottomToolbarPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['bottom_toolbar']['partials']) ? $this->config['bottom_toolbar']['partials'] : array()) ?>;
<?php unset($this->config['bottom_toolbar']['partials']) ?>
  }

  public function getBottomToolbarExtends()
  {
    return <?php echo isset($this->config['bottom_toolbar']['extends']) ? "'{$this->config['bottom_toolbar']['extends']}'" : 'null' ?>;
<?php unset($this->config['bottom_toolbar']['extends']) ?>
  }

  public function bottomToolbarIsDisabled()
  {
    return <?php echo isset($this->config['bottom_toolbar']['disabled']) && $this->config['bottom_toolbar']['disabled'] === true ? 'true' : 'false' ?>;
<?php unset($this->config['bottom_toolbar']['disabled']) ?>
  }
