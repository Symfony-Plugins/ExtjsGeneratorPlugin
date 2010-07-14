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
   * Returns the name of the filterMethod for the column.
   *
   * @param $field string the column name
   * @return string filter method.
   */
  public function getFilterForColumn($columnName)
  {
    $columnArr = explode('-', $columnName);
    $className = $this->table->getPhpName();

    for($i = 0; $i <= count($columnArr) - 1; $i ++)
    {
      $column = $columnArr[$i];

      if(! isset($map))
      {
        $map = call_user_func(array(
          $className . 'Peer',
          'getTableMap'
        ));
      }

      try
      {
        $fieldName = call_user_func(array(
          $className . 'Peer',
          'translateFieldName'
        ), sfInflector::camelize(strtolower($columnArr[$i])), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
      }
      catch(PropelException $e)
      {
        $fieldName = strtolower($columnArr[$i]);
      }

      try
      {
        $column = $map->getColumn($fieldName);
      }
      catch(PropelException $e)
      {
        $relationName = sfInflector::camelize($fieldName);
        try
        {
          $relation = $map->getRelation($relationName);
        }
        catch(PropelException $e)
        {
          try
          {
            // also try lcfirst as relations could start with either
            $relationName = (string)(strtolower(substr($relationName, 0, 1)) . substr($relationName, 1));
            $relation = $map->getRelation($relationName);
          }
          catch(PropelException $e)
          {
            //not a real column but we try using it anyhow
            unset($relationName);
            continue;
          }
        }

        $map = $relation->getLocalTable();
        $relationColumns = $relation->getLocalColumns();
        $column = $relationColumns[0];
        $className = $column->getTable()->getPhpName();
      }
    }

    $phpName = ($column instanceof ColumnMap) ? $column->getPhpName() : sfInflector::camelize($column);

    return isset($relationName) ? 'filterBy' . $relationName . '.' . $phpName . '()' : null;
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

    $withEmpty = $column->isNotNull() && ! $column->isForeignKey() ? array(
      "'with_empty' => false"
    ) : array();

    switch($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        $options[] = "'mode' => 'local', 'defaultValue' => '', 'allowClear' => false, 'context' => 'filter', 'choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')";
        break;
    }

    $options = array_merge($options, $withEmpty);

    if($column->isForeignKey())
    {
      $options[] = sprintf("'context' => 'filter', 'model' => '%s'", $this->getForeignTable($column)->getClassname());

      $refColumn = $this->getForeignTable($column)->getColumn($column->getRelatedColumnName());
      if(! $refColumn->isPrimaryKey())
      {
        $options[] = sprintf("'key_method' => 'get%s'", $refColumn->getPhpName());
      }
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
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
