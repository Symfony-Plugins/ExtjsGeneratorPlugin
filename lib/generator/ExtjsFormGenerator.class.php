<?php

/**
 * Extjs generator.
 *
 * @package    symfony
 * @subpackage ExtjsGenerator
 * @author     Benjamin Runnels <benjamin.r.runnels@citi.com>
 */
class ExtjsFormGenerator extends sfPropelFormGenerator
{

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);
    
    $this->setGeneratorClass('ExtjsForm');
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
    
    if(! isset($this->params['form_dir_name']))
    {
      $this->params['form_dir_name'] = 'form';
    }
    
    $this->dbMap = Propel::getDatabaseMap($this->params['connection']);
    
    $this->loadBuilders();
    
    // create the project base class for all forms
    $file = sfConfig::get('sf_lib_dir') . '/form/BaseExtjsFormPropel.class.php';
    if(! file_exists($file))
    {
      if(! is_dir($directory = dirname($file)))
      {
        mkdir($directory, 0777, true);
      }
      
      file_put_contents($file, $this->evalTemplate('ExtjsFormBaseTemplate.php'));
    }
    
    // create a form class for every Propel class
    foreach($this->dbMap->getTables() as $tableName => $table)
    {
      $behaviors = $table->getBehaviors();
      if(isset($behaviors['symfony']['form']) && 'false' === $behaviors['symfony']['form'])
      {
        continue;
      }
      
      $this->table = $table;
      
      // find the package to store forms in the same directory as the model classes
      $packages = explode('.', constant(constant($table->getClassname() . '::PEER') . '::CLASS_DEFAULT'));
      array_pop($packages);
      if(false === $pos = array_search($this->params['model_dir_name'], $packages))
      {
        throw new InvalidArgumentException(sprintf('Unable to find the model dir name (%s) in the package %s.', $this->params['model_dir_name'], constant(constant($table->getClassname() . '::PEER') . '::CLASS_DEFAULT')));
      }
      $packages[$pos] = $this->params['form_dir_name'];
      $baseDir = sfConfig::get('sf_root_dir') . '/' . implode(DIRECTORY_SEPARATOR, $packages);
      
      if(! is_dir($baseDir . '/base'))
      {
        mkdir($baseDir . '/base', 0777, true);
      }
      
      file_put_contents($baseDir . '/base/BaseExtjs' . ucfirst($table->getClassname()) . 'Form.class.php', $this->evalTemplate('ExtjsFormGeneratedTemplate.php'));
      if(! file_exists($classFile = $baseDir . '/Extjs' . ucfirst($table->getClassname()) . 'Form.class.php'))
      {
        file_put_contents($classFile, $this->evalTemplate('ExtjsFormTemplate.php'));
      }
    }
  }

  /**
   * Returns an array of RelationMap objects for a one-to-one tables if they exist.
   *
   * @return array RelationMaps.
   */
  public function getOneToOneTables()
  {
    $tables = array();
    foreach($this->table->getRelations() as $relation)
    {
      if($relation->getType() == RelationMap::ONE_TO_ONE)
      {
        $tables[] = $relation;
      }
    }
    return $tables;
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
        $name = 'InputCheckbox';
        break;
      case PropelColumnTypes::CLOB:
      case PropelColumnTypes::LONGVARCHAR:
        $name = 'InputTextArea';
        break;
      case PropelColumnTypes::DATE:
        $name = 'Date';
        break;
      case PropelColumnTypes::TIME:
        $name = 'Time';
        break;
      case PropelColumnTypes::TIMESTAMP:
        $name = 'DateTime';
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
        $name = 'InputNumberField';
        break;
      default:
        $name = 'InputText';
    }
    
    if($column->isPrimaryKey())
    {
      $name = 'InputHidden';
    }
    else if($column->isForeignKey())
    {
      $name = 'PropelChoice';
    }
    
    return sprintf('ExtjsWidgetForm%s', $name);
  }

  public function getValidatorClassForColumn(ColumnMap $column)
  {
    if($column->isPrimaryKey() || $column->isForeignKey())
    {
      return 'ExtjsValidatorPropelChoice';
    }
    
    return parent::getValidatorClassForColumn($column);
  }
}
