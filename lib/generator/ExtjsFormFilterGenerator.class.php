<?php

/**
 * Extjs generator.
 *
 * @package    symfony
 * @subpackage ExtjsGeneratorPlugin
 * @author     Benjamin Runnels <benjamin.r.runnels@citi.com>
 */
class ExtjsFormFilterGenerator extends sfPropelFormFilterGenerator
{

  /** Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);
    
    $this->setGeneratorClass('ExtjsFormFilter');
  }

  /**
   * Generates classes and templates in cache.
   *
   * @param array $params The parameters
   *
   * @return string The data to put in configuration cache
   */
  public function generate($params = array())
  {
    $this->params = $params;
    
    if(! isset($this->params['connection']))
    {
      throw new sfParseException('You must specify a "connection" parameter.');
    }
    
    if(! isset($this->params['model_dir_name']))
    {
      $this->params['model_dir_name'] = 'model';
    }
    
    if(! isset($this->params['filter_dir_name']))
    {
      $this->params['filter_dir_name'] = 'filter';
    }
    
    $this->loadBuilders();
    
    $this->dbMap = Propel::getDatabaseMap($this->params['connection']);
    
    // create the project base class for all forms
    $file = sfConfig::get('sf_lib_dir') . '/filter/BaseExtjsFormFilterPropel.class.php';
    if(! file_exists($file))
    {
      if(! is_dir($directory = dirname($file)))
      {
        mkdir($directory, 0777, true);
      }
      
      file_put_contents($file, $this->evalTemplate('ExtjsFormFilterBaseTemplate.php'));
    }
    
    // create a form class for every Propel class
    foreach($this->dbMap->getTables() as $tableName => $table)
    {
      $behaviors = $table->getBehaviors();
      if(isset($behaviors['symfony']['filter']) && 'false' === $behaviors['symfony']['filter'])
      {
        continue;
      }
      
      $this->table = $table;
      
      // find the package to store filter forms in the same directory as the model classes
      $packages = explode('.', constant(constant($table->getClassname() . '::PEER') . '::CLASS_DEFAULT'));
      array_pop($packages);
      if(false === $pos = array_search($this->params['model_dir_name'], $packages))
      {
        throw new InvalidArgumentException(sprintf('Unable to find the model dir name (%s) in the package %s.', $this->params['model_dir_name'], constant(constant($table->getClassname() . '::PEER') . '::CLASS_DEFAULT')));
      }
      $packages[$pos] = $this->params['filter_dir_name'];
      $baseDir = sfConfig::get('sf_root_dir') . '/' . implode(DIRECTORY_SEPARATOR, $packages);
      
      if(! is_dir($baseDir . '/base'))
      {
        mkdir($baseDir . '/base', 0777, true);
      }
      
      file_put_contents($baseDir . '/base/BaseExtjs' . ucfirst($table->getClassname()) . 'FormFilter.class.php', $this->evalTemplate('ExtjsFormFilterGeneratedTemplate.php'));
      if(! file_exists($classFile = $baseDir . '/Extjs' . ucfirst($table->getClassname()) . 'FormFilter.class.php'))
      {
        file_put_contents($classFile, $this->evalTemplate('ExtjsFormFilterTemplate.php'));
      }
    }
  }

  /**
   * Returns a sfWidgetForm class name for a given column.
   *
   * @param  ColumnMap  $column A ColumnMap object
   *
   * @return string    The name of a subclass of sfWidgetForm
   */
  public function getWidgetClassForColumn(ColumnMap $column)
  {
    switch($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        $name = 'Choice';
        break;
      case PropelColumnTypes::CLOB:
      case PropelColumnTypes::LONGVARCHAR:
        $name = 'FilterInputTextArea';
        break;
      case PropelColumnTypes::DATE:
      case PropelColumnTypes::TIME:
      case PropelColumnTypes::TIMESTAMP:
        $name = 'FilterDate';
        break;
      case PropelColumnTypes::NUMERIC:
      case PropelColumnTypes::DECIMAL:
      case PropelColumnTypes::TINYINT:
      case PropelColumnTypes::SMALLINT:
      case PropelColumnTypes::INTEGER:
      case PropelColumnTypes::BIGINT:
      case PropelColumnTypes::REAL: 
      case PropelColumnTypes::FLOAT: 
      case PropelColumnTypes::DOUBLE: 
        $name = 'FilterInputNumberField';
        break;
      default:
        $name = 'FilterInputText';
    }
    
    if($column->isForeignKey())
    {
      $name = 'PropelChoice';
    }
    
    return sprintf('ExtjsWidgetForm%s', $name);
  }
  
  /**
   * Returns a PHP string representing options to pass to a widget for a given column.
   *
   * @param  ColumnMap $column  A ColumnMap object
   *
   * @return string    The options to pass to the widget as a PHP string
   */
  public function getWidgetOptionsForColumn(ColumnMap $column)
  {
    $options = array();

    $withEmpty = $column->isNotNull() && !$column->isForeignKey() ? array("'with_empty' => false") : array();
    switch ($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        $options[] = "'defaultValue' => '', 'allowClear' => false, 'context' => 'filter', 'choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')";
        break;
      default:
        $options = array_merge($options, $withEmpty);
    }

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => \'%s\'', $this->getForeignTable($column)->getClassname());

      $refColumn = $this->getForeignTable($column)->getColumn($column->getRelatedColumnName());
      if (!$refColumn->isPrimaryKey())
      {
        $options[] = sprintf('\'key_method\' => \'get%s\'', $refColumn->getPhpName());
      }
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }
}
