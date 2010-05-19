  public function getFormpanelConfig()
  {
    return array_merge(array(
      'xtype'               => 'form',
      'title'               => 'New '.$this->getObjectName(),
      'autoScroll'          => true,
      'labelWidth'          => 200,
      'labelAlign'          => 'right',
      'bodyStyle'           => 'padding: 10px 0px 10px 5px;',
      'trackResetOnLoad'    => true,
      'method'              => 'POST',
      'defaults'            => array('anchor' => '50%'),
      'plugins'             => $this->getFormpanelPlugins(),
    ), <?php echo $this->asPhp(isset($this->config['formpanel']['config']) ? $this->config['formpanel']['config'] : array()) ?>);
<?php unset($this->config['formpanel']['config']) ?>    
  }
  
  public function getFormFieldsetParams($fieldset)
  {
<?php 
$fieldsetConfigs = array();
if(isset($this->config['form']))
{
  foreach($this->config['form'] as $key => $config)
  {
    if(strstr($key, 'params_'))
    {
      $fieldsetConfigs[$key] = $config;
      unset($this->config['form'][$key]);
    }
  }
}
?>
    $fieldsetConfigs = <?php echo $this->asPhp($fieldsetConfigs) ?>;
    return isset($fieldsetConfigs[$fieldset]) ? $fieldsetConfigs[$fieldset] : array();
  }

  public function getFormpanelPlugins()
  {
<?php
  // default plugins go in this array
  $pluginsArr = array();
  if(isset($this->config['formpanel']['plugins']))
  {
    if(!is_array($this->config['formpanel']['plugins']))
    {
      $pluginsArr[] = $this->config['formpanel']['plugins'];
    }
    else
    {
      $pluginsArr = array_merge($pluginsArr, $this->config['formpanel']['plugins']);
    }
  }
?>
    return <?php echo $this->asPhp($pluginsArr) ?>;
<?php unset($this->config['formpanel']['plugins']) ?>
  }

  public function getFormpanelPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['formpanel']['partials']) ? $this->config['formpanel']['partials'] : array()) ?>;
<?php unset($this->config['formpanel']['partials']) ?>
  }      
  