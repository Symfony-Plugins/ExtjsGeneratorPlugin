  public function getListParams(){}

  public function getListLayout(){}  

  public function getExportBooleanAsString()
  {
    return <?php echo isset($this->config['export']['boolean_as_string']) ? (bool)$this->config['export']['boolean_as_string'] : true ?>;
<?php unset($this->config['export']['boolean_as_string']) ?>
  }
  
  public function getExportDateFormat()
  {
    return <?php echo $this->asPhp(isset($this->config['export']['default_date_format']) ? $this->config['export']['default_date_format'] : false) ?>;
<?php unset($this->config['export']['default_date_format']) ?>
  }
  
  public function getExportBooleanStringValues()
  {
    return <?php echo $this->asPhp(isset($this->config['export']['boolean_string_values']) ? $this->config['export']['boolean_string_values'] : array()) ?>;
<?php unset($this->config['export']['boolean_string_values']) ?>
  }

  public function getListTitle()
  {
<?php
if(isset($this->config['list']['title']))
{
  $title = $this->config['list']['title'];
} else if(isset($this->params['object_name']))
{
  $title = sfInflector::humanize($this->params['object_name']). ' List';
}
else
{
  $title = sfInflector::humanize($this->getModuleName()). ' List';
}
?>
    return '<?php echo $this->escapeString($title) ?>';
<?php unset($this->config['list']['title']) ?>
  }

  public function getEditTitle()
  {
<?php
if(isset($this->config['edit']['title']))
{
  $title = $this->config['edit']['title'];
} else if(isset($this->params['object_name']))
{
  $title = 'Edit '.sfInflector::humanize($this->params['object_name']);
}
else
{
  $title = 'Edit '.sfInflector::humanize($this->getModuleName());
}
?>
    return '<?php echo $this->escapeString($title) ?>';
<?php unset($this->config['edit']['title']) ?>
  }

  public function getNewTitle()
  {
<?php
if(isset($this->config['new']['title']))
{
  $title = $this->config['new']['title'];
} else if(isset($this->params['object_name']))
{
  $title = 'New '.sfInflector::humanize($this->params['object_name']);
}
else
{
  $title = 'New '.sfInflector::humanize($this->getModuleName());
}
?>
    return '<?php echo $this->escapeString($title) ?>';
<?php unset($this->config['new']['title']) ?>
  }
  
  public function getExportTitle()
  {
<?php
if(isset($this->config['export']['title']))
{
  $title = $this->config['export']['title'];
} else if(isset($this->params['object_name']))
{
  $title = sfInflector::humanize($this->params['object_name']);
}
else
{
  $title = sfInflector::humanize($this->getModuleName());
}
?>
    return '<?php echo $this->escapeString($title) ?>';
<?php unset($this->config['export']['title']) ?>
  }

  public function getEditDisplay()
  {
    return <?php echo $this->asPhp(isset($this->config['edit']['display']) ? $this->config['edit']['display'] : array()) ?>;
<?php unset($this->config['edit']['display']) ?>
  }

  public function getNewDisplay()
  {
    return <?php echo $this->asPhp(isset($this->config['new']['display']) ? $this->config['new']['display'] : array()) ?>;
<?php unset($this->config['new']['display']) ?>
  }

<?php foreach (array('list', 'filter', 'form', 'export') as $context): ?>
  public function get<?php echo ucfirst($context) ?>Display()
  {
<?php if (isset($this->config[$context]['display'])): ?>
    return <?php echo $this->asPhp($this->config[$context]['display']) ?>;
<?php elseif (isset($this->config[$context]['hide'])): ?>
    return <?php echo $this->asPhp(array_diff($this->getAllFieldNames(false), $this->config[$context]['hide'])) ?>;
<?php else: $allFieldNames = $this->getAllFieldNames(false);
if(
  $context == 'list' &&
  (
    (isset($this->config['list']['object_actions']) && count($this->config['list']['object_actions'])) ||
    !isset($this->config['list']['object_actions'])
  )
): ?>
<?php $allFieldNames[] = '^object_actions' ?>
<?php $allFieldNames['id'] = '+id' ?>
<?php endif;?>
    return <?php echo $this->asPhp($allFieldNames) ?>;
<?php endif; ?>
<?php unset($this->config[$context]['display'], $this->config[$context]['hide']) ?>
  }

<?php endforeach; ?>
  public function getFieldsDefault()
  {
    return array(
<?php foreach ($this->getDefaultFieldsConfiguration() as $name => $params): ?>
      '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
    );
  }

<?php $filterDefaults = array(); ?>
<?php foreach (array('list', 'filter', 'form', 'edit', 'new', 'export') as $context): ?>
  public function getFields<?php echo ucfirst($context) ?>()
  {
    return array(
<?php foreach ($this->getFieldsConfiguration($context) as $name => $params): ?>
<?php if($context == 'filter' && isset($params['widgetOptions']['defaultValue'])): ?>
<?php $filterDefaults[$name] = $params['widgetOptions']['defaultValue']; ?>
<?php endif;?>
      '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
    );
  }

<?php endforeach; ?>
<?php if(count($filterDefaults)):?>
  public function getFilterDefaults()
  {
    return array(
<?php foreach ($filterDefaults as $name => $value): ?>
      '<?php echo $name ?>' => <?php echo $this->asPhp($value) ?>,
<?php endforeach; ?>
    );
  }
<?php endif;?>    