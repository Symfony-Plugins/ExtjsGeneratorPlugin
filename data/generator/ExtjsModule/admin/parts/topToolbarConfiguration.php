  public function getTopToolbarConfig()
  {
    return array_merge(array(
      'plugins'   => $this->getTopToolbarPlugins(),
      'autoWidth' => false,
      'height'    => 26,
    ), <?php echo $this->asPhp(isset($this->config['top_toolbar']['config']) ? $this->config['top_toolbar']['config'] : array()) ?>);
<?php unset($this->config['top_toolbar']['config']) ?>
  }

  public function getTopToolbarPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array();
  if(isset($this->config['top_toolbar']['plugins']))
  {
    if(!is_array($this->config['top_toolbar']['plugins']))
    {
      $pluginsArr[] = $this->config['top_toolbar']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['bottom_toolbar']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['top_toolbar']['plugins']) ?>
  }
  
  public function getTopToolbarPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['top_toolbar']['partials']) ? $this->config['top_toolbar']['partials'] : array()) ?>;
<?php unset($this->config['top_toolbar']['partials']) ?>
  }
  
  public function topToolbarIsDisabled()
  {
    return <?php echo isset($this->config['top_toolbar']['disabled']) && $this->config['top_toolbar']['disabled'] === true ? 'true' : 'false' ?>;
<?php unset($this->config['top_toolbar']['disabled']) ?>
  }
