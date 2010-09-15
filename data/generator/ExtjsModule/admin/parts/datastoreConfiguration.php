  public function getDatastoreConfig()
  {
    $config = array(
      'remoteSort' => true,
      'paramNames' => array(
        'dir' => 'sort_type'
      )
    );

    if(count($this->getDatastoreGroupingConfig())) $config = array_merge($this->getDatastoreGroupingConfig(), $config);
    if(count($this->getDatastoreSortConfig())) $config = array_merge($this->getDatastoreSortConfig(), $config);
        
    return array_merge($config, <?php echo $this->asPhp(isset($this->config['datastore']['config']) ? $this->config['datastore']['config'] : array()) ?>);
<?php unset($this->config['datastore']['config']) ?>
  }
  
  public function getDatastoreType()
  {
<?php 
  $type = 'Store'; 
  $groupingConfig = array(); 
  if(isset($this->config['datastore']['grouping']) && isset($this->config['datastore']['grouping']['field'])) 
  {
    $type = 'Grouping'.$type;
  } 
?>
    return '<?php echo $type ?>';   
  }
  
  public function getDatastoreGroupingConfig()
  {
<?php $groupingConfig = array(); if(isset($this->config['datastore']['grouping']) && isset($this->config['datastore']['grouping']['field'])): ?>
<?php $groupingConfig['groupField'] = isset($this->config['datastore']['grouping']['start_grouped']) ? $this->config['datastore']['grouping']['field'] : null ?>
<?php //$groupingConfig['remoteGroup'] = true ?>
<?php $groupingConfig['sortInfo'] = array('field' => $this->config['datastore']['grouping']['field'], 'direction' => 'asc')?>
<?php endif;?>
    return <?php echo $this->asPhp($groupingConfig) ?>;
<?php unset($this->config['datastore']['grouping']) ?>
  }
    
  public function getDatastoreSortConfig()
  {
<?php
$sortConfig = array();
if(isset($this->config['list']['sort']))
{
  
  if(is_array($this->config['list']['sort']))
  {
    $sortConfig = array('field' => $this->config['list']['sort'][0], 'direction' => $this->config['list']['sort'][1]);
  }
  else
  {
    $sortConfig = array('field' => $this->config['list']['sort'], 'direction' => 'asc');
  }
} 
?>
    return <?php echo $this->asPhp($sortConfig) ?>;
  }
  
  public function getDatastorePartials()
  {
    return <?php echo $this->asPhp(isset($this->config['datastore']['partials']) ? $this->config['datastore']['partials'] : array()) ?>;
<?php unset($this->config['datastore']['partials']) ?>
  }
