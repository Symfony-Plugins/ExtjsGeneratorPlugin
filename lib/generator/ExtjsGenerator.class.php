<?php

/**
 * Extjs generator.
 *
 * @package    symfony
 * @subpackage ExtjsGenerator
 * @author     Benjamin Runnels <benjamin.r.runnels@citi.com>
 */
class ExtjsGenerator extends sfPropelGenerator
{

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('ExtjsModule');
  }

  /**
   * Returns the default configuration for fields.
   *
   * @return array An array of default configuration for all fields
   */
  public function getDefaultFieldsConfiguration()
  {
    $fields = array();

    $names = array();
    foreach($this->getTableMap()->getColumns() as $column)
    {
      $name = $this->translateColumnName($column);
      $names[] = $name;
      $fields[$name] = array_merge(array(
        'is_link' => (boolean)$column->isPrimaryKey(),
        'is_real' => true,
        'getter' => 'get' . $column->getPhpName(),
        'model' => $column->getTable()->getPhpName(),
        'php_name' => $column->getPhpName(),
        'field_name' => $name,
        'type' => $this->getType($column)
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    foreach($this->getManyToManyTables() as $tables)
    {
      $name = sfInflector::underscore($tables['middleTable']->getClassname()) . '_list';
      $names[] = $name;
      $fields[$name] = array_merge(array(
        'is_link' => false,
        'is_real' => false,
        'type' => 'Text'
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    foreach($this->getOneToOneTables() as $oneToOne)
    {
      foreach($oneToOne->getLocalTable()->getColumns() as $column)
      {
        $name = $this->translateColumnName($column);
        $names[] = $name;
        $fields[$name] = array_merge(array(
          'is_link' => (boolean)$column->isPrimaryKey(),
          'is_real' => true,
          'getter' => sprintf('get%s()->get%s', $oneToOne->getName(), $column->getPhpName()),
          'model' => $column->getTable()->getPhpName(),
          'php_name' => $column->getPhpName(),
          'field_name' => $name,
          'relation_name' => $oneToOne->getName(),
          'sort_method' => sprintf('orderBy%s.%s', $oneToOne->getName(), $column->getPhpName()),
          'type' => $this->getType($column)
        ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
      }
    }

    if(isset($this->config['fields']))
    {
      foreach($this->config['fields'] as $name => $params)
      {
        if(in_array($name, $names))
        {
          continue;
        }

        $fields[$name] = array_merge(ExtjsGeneratorUtil::getColumnParams($name, $this->getModelClass()), is_array($params) ? $params : array());
      }
    }

    unset($this->config['fields']);

    return $fields;
  }

  /**
   * Returns an array of RelationMap objects for a one-to-one tables if they exist.
   *
   * @return array RelationMaps.
   */
  public function getOneToOneTables()
  {
    $tables = array();
    foreach($this->getTableMap()->getRelations() as $relation)
    {
      if($relation->getType() == RelationMap::ONE_TO_ONE)
      {
        $tables[] = $relation;
      }
    }
    return $tables;
  }

  /**
   * Returns the getter either non-developped: 'getFoo' or developped: '$class->getFoo()'.
   *
   * @param string  $column     The column name
   * @param boolean $developed  true if you want developped method names, false otherwise
   * @param string  $prefix     The prefix value
   *
   * @return string PHP code
   */
  public function getColumnGetter($column, $developed = false, $prefix = '', $modelClass = null)
  {
    if(!$modelClass) $modelClass = $this->getSingularName();
    $defaults = $this->configuration->getFieldsDefault();

    if(isset($defaults[$column]))
    {
      $getter = $defaults[$column]['getter'];
    }
    else
    {
      $params = ExtjsGeneratorUtil::getColumnParams($column, sfInflector::camelize($modelClass));
      $getter = $params['getter'];
    }

    if(! $developed)
    {
      return $getter;
    }

    if(strpos($getter,'()->'))
    {
      $relatedGetters = explode('->', $getter);
      for($i = 0; $i < count($relatedGetters); $i++)
      {
        $relatedGetters[$i] = ($i == 0) ? $relatedGetters[$i] : $relatedGetters[$i-1] . '->' .$relatedGetters[$i];
      }

      $getter = sprintf('$%s->%s()', $modelClass, array_pop($relatedGetters));
      $relatedCheck = implode(sprintf(') && is_object($%s->',$modelClass), $relatedGetters);


      return sprintf("(is_object(\$%s->%s)) ? %s : ''", $modelClass, $relatedCheck, $getter);
    }

    return sprintf('$%s%s->%s()', $prefix, $modelClass, $getter);
  }

  /**
   * Returns HTML code for a field.
   *
   * @param sfModelGeneratorConfigurationField $field The field
   *
   * @return string HTML code
   */
  public function renderField($field)
  {
    $html = $this->getColumnGetter($field->getName(), true);

    if($renderer = $field->getRenderer())
    {
      $html = sprintf("$html !== null ? call_user_func_array(%s, array_merge(array(%s), %s)) : ''", $this->asPhp($renderer), $html, $this->asPhp($field->getRendererArguments()));
    }
    //        else //    {
    //      return sprintf("get_component('%s', '%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $field->getName(), $this->getSingularName(), $this->getSingularName());
    //    }
    //    else if ($field->isPartial())
    //    {
    //      return sprintf("get_partial('%s/%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $field->getName(), $this->getSingularName(), $this->getSingularName());
    //    }
    if('Date' == $field->getType())
    {
      $html = sprintf("false !== strtotime($html) ? format_date(%s, \"%s\") : ''", $html, $field->getConfig('date_format', 'f'));
    }

    return $html;
  }

  /**
   * Returns sfExtjs3Plugin code for a json reader field.
   *
   * @param ExtjsModelGeneratorConfigurationField $field The field
   *
   * @return string php code
   */
  public function renderJsonReaderField(ExtjsModelGeneratorConfigurationField $field, $form = null)
  {
    if($field->isComponent() || $field->isPartial() || $field->getKey() == 'expander' || $field->getKey() == 'object_actions') return false;

    $fieldArr = array(
      'name' => $field->getName(),
      'type' => $field->getReaderFieldType()
    );

    if(isset($form))
    {
      $fieldArr['mapping'] = $field->getName();
      $fieldArr['name'] = sprintf($form->getWidgetSchema()->getOption('name_format'), $field->getName());
      if($fieldArr['type'] == 'date') $fieldArr['dateFormat'] = 'Y-m-d H:i:s';
    }

    return sprintf("\$readerFields[] = %s", $this->asPhp($fieldArr));
  }

  /**
   * Returns sfExtjs3Plugin code for a column model field.
   *
   * @param ExtjsModelGeneratorConfigurationField $field The field
   *
   * @return string php code
   */
  public function renderColumnModelField($field)
  {
    if($field->isComponent() || $field->isPartial() || $field->isInvisible() || $field->isHidden()) return false;

    if($field->isPlugin())
    {
      if($field->getKey() == 'expander')
      {
        return sprintf("\$columnModel->config_array['columns'][] = %s", $this->asPhp(array(
          'xtype' => 'rowexpander'
        )));
      }

      if($field->getKey() == 'object_actions')
      {
        return sprintf("\$columnModel->config_array['columns'][] = 'this.%s_objectactions'", $this->getModuleName());
      }

      return sprintf("\$columnModel->config_array['columns'][] = 'this.%s_%s'", $field->getName(), $field->getConfig('plugin'));
    }

    $colArr = array(
      'header' => "[?php echo __('" . addslashes($field->getConfig('label', '', true)) . "', array(), '" . $this->getI18nCatalogue() . "'); ?]",
      'dataIndex' => $field->getName()
    );

    if($field->getColumnModelRenderer()) $colArr['renderer'] = $field->getColumnModelRenderer();
    return sprintf("\$columnModel->config_array['columns'][] = %s", $this->asPhp(array_merge($colArr, $field->getConfig('config', array()))));
  }

  /**
   * Returns sfExtjs3Plugin code for a column model plugin.
   *
   * @param ExtjsModelGeneratorConfigurationField $field The field
   *
   * @return string php code
   */
  public function renderColumnModelPlugin($field)
  {
    if(! $field->isPlugin()) return false;

    if($field->getKey() == 'object_actions')
    {
      return sprintf("\$columnModel->variables['%s_objectactions'] = \$sfExtjs3Plugin->asVar('Ext.ComponentMgr.create({xtype: \'%s\', header:\'&nbsp;\'})')", $this->getModuleName(), $this->getModuleName() . 'objectactions');
    }

    //TODO refactor this to provide il8n support for header
    return sprintf("\$columnModel->variables['%s_%s'] = \$sfExtjs3Plugin->asVar('Ext.ComponentMgr.createPlugin('.\$sfExtjs3Plugin->asAnonymousClass(%s).')')", $field->getName(), $field->getConfig('plugin'), $this->asPhp(array_merge(array(
      'ptype' => $field->getConfig('plugin'),
      //'header' => "__('" . $field->getConfig('label', '', true) . "', array(), '" . $this->getI18nCatalogue() . "')",
      'header' => $field->getConfig('label', '', true),
      'dataIndex' => $field->getName()
    ), $field->getConfig('config', array()))));
  }

  /**
   * Returns sfExtjs3Plugin code for a gridpanel plugin.
   *
   * @param ExtjsModelGeneratorConfigurationField $field The field
   *
   * @return string php code
   */
  public function renderGridPanelPlugin($field)
  {
    if(! $field->isPlugin()) return false;

    //    if($field->getKey() == 'expander')
    //    {
    //      return sprintf("\$gridpanelPlugins[] = %s", $this->asPhp(array(
    //        'xtype' => 'rowexpander'
    //      )));
    //    }


    if($field->getKey() == 'object_actions')
    {
      return sprintf("\$gridpanelPlugins[] = 'this.cm.%s_objectactions'", $this->getModuleName());
    }

    return sprintf("\$gridpanelPlugins[] = 'this.cm.%s_%s'", $field->getName(), $field->getConfig('plugin'));
  }

  /**
   * Wraps content with a credential condition.
   *
   * @param string $content The content
   * @param array  $params  The parameters
   *
   * @return string HTML code
   */
  public function addCredentialCondition($content, $params = array())
  {
    if(isset($params['credentials']))
    {
      $credentials = $this->asPhp($params['credentials']);

      return <<<EOF
if (\$sf_user->hasCredential($credentials))
{
  $content}

EOF;
    }
    else
    {
      return $content;
    }
  }

  /**
   * Removes trailing digits from a string
   *
   * @param string $string
   *
   * @return string
   */
  public function removeTrailingNumbers($string)
  {
    while(is_numeric(substr($string, - 1)))
    {
      $string = substr($string, 0, - 1);
    }
    return $string;
  }

  /**
   * Returns php array code for an object action button.
   *
   * @param string  $actionName The action name
   * @param array   $params     The parameters
   *
   * @return string php array
   */
  public function getObjectActionButton($actionName, $params)
  {
    $originalName = $actionName;
    $realName = ($actionName[0] == '_') ? substr($actionName, 1) : $actionName;
    $configArr = array(
      'icon' => 'page_white',
      'help' => ucfirst($realName),
      'hide' => 'false',
      'handler' => 'this.' . $originalName,
      'scope' => 'this'
    );

    switch($realName)
    {
      case 'edit':
        $configArr['icon'] = 'pencil';
        break;
      case 'delete':
        $configArr['icon'] = 'cross';
        break;
    }

    // merge params after setting our built in actions so defaults can be overridden
    $configArr = array_merge($configArr, $params);

    return <<<EOF
\$objectActions->config_array['actions'][] = array(
  'iconCls' => \$sfExtjs3Plugin->asVar("Ext.ux.IconMgr.getIcon('{$configArr['icon']}')"),
  'qtip' => '{$configArr['help']}',
  'hide' => {$configArr['hide']},
  'cb' => '{$configArr['handler']}',
  'scope' => \$sfExtjs3Plugin->asVar('{$configArr['scope']}'),
);
EOF;
  }

  /**
   * Returns php array code for a list action button.
   *
   * @param string  $actionName The action name
   * @param array   $params     The parameters
   *
   * @return string php array
   */
  public function getListActionButton($actionName, $params)
  {
    $originalName = $actionName;
    $realName = ($actionName[0] == '_') ? substr($actionName, 1) : $actionName;

    $configArr = array(
      'icon' => 'page_white',
      'handler' => 'this.' . $originalName,
      'name' => ucfirst($realName),
      'xtype' => 'tbbutton'
    );

    //actionnames must be unique in the generator so need to strip off the number at the end for text, separator, and spacer
    $trimmedName = $this->removeTrailingNumbers($realName);
    if(in_array($trimmedName, array(
      'text',
      'separator',
      'spacer'
    ))) $realName = $trimmedName;

    switch($realName)
    {
      case 'text':
        $text = isset($params['name']) ? $params['name'] : $configArr['name'];
        return "\$topToolbar->config_array['items'][] = array('xtype' => 'tbtext', 'text' => '$text');";

      case 'separator':
      case 'spacer':
      case 'fill':
        return "\$topToolbar->config_array['items'][] = array('xtype' => 'tb$realName');";

      case 'new':
        $configArr['icon'] = 'table_row_insert';
        $configArr['name'] = 'New';
        $configArr['help'] = 'Create a new record';
        break;

      case 'export':
        $configArr['icon'] = 'page_white_csv';
        $configArr['name'] = 'CSV Export';
        $configArr['help'] = 'Export list to CSV';
        break;
    }

    // merge params after setting our built in actions so defaults can be overridden
    $configArr = array_merge($configArr, $params);

    // you can pass a class instead of an icon name with the class parameter
    $iconCls = isset($configArr['class']) ? $configArr['class'] : "\$sfExtjs3Plugin->asVar(\"Ext.ux.IconMgr.getIcon('{$configArr['icon']}')\")";

    $handler = isset($configArr['handler_function']) ? "\$sfExtjs3Plugin->asMethod('{$configArr['handler_function']}')" : "\$sfExtjs3Plugin->asVar('this.$actionName')";

    $configStr = <<<EOF
\$topToolbar->config_array['items'][] = array(
  'xtype' => '{$configArr['xtype']}',
  'text' => '{$configArr['name']}',
  'iconCls' => $iconCls,
  'scope' => \$sfExtjs3Plugin->asVar('this'),
  'handler' => $handler,
EOF;

    // no default help
    if(isset($configArr['help'])) $configStr .= "  'tooltip' => '{$configArr['help']}',";
    return $configStr . "\n);";
  }

  /**
   * Returns php array code for an edit action button.
   *
   * @param string  $actionName The action name
   * @param array   $params     The parameters
   *
   * @return string php array
   */
  public function getEditActionButton($actionName, $params)
  {
    $originalName = $actionName;
    $realName = ($actionName[0] == '_') ? substr($actionName, 1) : $actionName;

    $configArr = array(
      'icon' => 'page_white',
      'handler' => 'this.' . $originalName,
      'name' => ucfirst($realName),
      'xtype' => 'tbbutton'
    );

    //actionnames must be unique in the generator so need to strip off the number at the end for text, separator, and spacer
    $trimmedName = $this->removeTrailingNumbers($realName);
    if(in_array($trimmedName, array(
      'text',
      'separator',
      'spacer'
    ))) $realName = $trimmedName;

    switch($realName)
    {
      case 'text':
        $text = isset($params['name']) ? $params['name'] : $configArr['name'];
        return "\$formpanel->config_array['tbar'][] = array('xtype' => 'tbtext', 'text' => '$text');";

      case 'separator':
      case 'spacer':
      case 'fill':
        return "\$formpanel->config_array['tbar'][] = array('xtype' => 'tb$realName');";

      case 'cancel':
        $configArr['name'] = 'Close/Cancel';
        $configArr['icon'] = 'decline';
        break;

      case 'reload':
        $configArr['icon'] = 'page_white_refresh_arrows';
        $configArr['hideWhenNew'] = true;
        break;

      case 'save':
        $configArr['icon'] = 'page_white_accept';
        $type = 'submit';
        break;

      case 'savenew':
        $configArr['icon'] = 'page_white_add';
        $configArr['name'] = 'Save as New';
        $type = 'submit';
        $configArr['hideWhenNew'] = true;
        break;

      case 'delete':
        $configArr['icon'] = 'page_white_delete';
        $configArr['hideWhenNew'] = true;
        break;

    }

    // merge params after setting our built in actions so defaults can be overridden
    $configArr = array_merge($configArr, $params);

    // you can pass a class instead of an icon name with the class parameter
    $iconCls = isset($configArr['class']) ? $configArr['class'] : "\$sfExtjs3Plugin->asVar(\"Ext.ux.IconMgr.getIcon('{$configArr['icon']}')\")";

    $handler = isset($configArr['handler_function']) ? "\$sfExtjs3Plugin->asMethod('{$configArr['handler_function']}')" : "\$sfExtjs3Plugin->asVar('this.$actionName')";

    $configStr = <<<EOF
\$formpanel->config_array['tbar'][] = array(
  'xtype' => '{$configArr['xtype']}',
  'text' => '{$configArr['name']}',
  'iconCls' => $iconCls,
  'scope' => \$sfExtjs3Plugin->asVar('this'),
  'handler' => $handler,
EOF;

    // no default help
    if(isset($configArr['help'])) $configStr .= "  'tooltip' => '{$configArr['help']}',";
    if(isset($configArr['hideWhenNew'])) $configStr .= "  'hideWhenNew' => '{$configArr['hideWhenNew']}',";
    return $configStr . "\n);";
  }

  public function getCustomPartials($objName)
  {
    $partialStr = '';
    $partials = call_user_func(array(
      $this->configuration,
      'get' . ucfirst($objName) . 'Partials'
    ));
    if(count($partials))
    {
      $partialStr .= "\n// generator custom partials\n";
      foreach($partials as $partial)
      {
        if($partial[0] == '_') $partial = substr($partial, 1);
        $partialStr .= "include_partial('" . $partial . "', array('sfExtjs3Plugin' => \$sfExtjs3Plugin, '$objName' => \$$objName, 'className' => \$className));\n";

        $partialContent = <<<EOF
<?php
// @object \$sfExtjs3Plugin string \$className and @object $$objName provided
/*
*** Method example with no parameters
\${$objName}->methods['MethodName'] = \$sfExtjs3Plugin->asMethod("
  //method code
");

*** Method example with parameters
\$configArr->['parameters'] = 'grid, record, action, row, col';
\$configArr->['source'] = "
  //method code
");
\${$objName}->methods['MethodName'] = \$sfExtjs3Plugin->asMethod(\$configArr);

*** Variable example
\${$objName}->variables['VariableName'] = \$sfExtjs3Plugin->asVar("
  //variable creation or string
");
*/
?>
EOF;

        $this->createPartialFile('_' . $partial, $partialContent);
      }
    }
    return $partialStr;
  }

  public function getStandardPartials($objName, $partialsArr = array('constructor','initComponent','initEvents'))
  {
    $partialStr = "\n/* %1\$s methods and variables */\n";
    foreach($partialsArr as $partials)
    {
      switch($partials)
      {
        case 'constructor':
          $this->createPartialFile('_' . $objName . '_method_constructor', $this->createStandardConstructorPartial($objName));
          $partialStr .= "// constructor\n" . 'include_partial("%1$s_method_constructor", array("sfExtjs3Plugin" => $sfExtjs3Plugin, "%1$s" => $%1$s, "className" => $className));' . "\n";
          break;
        case 'initComponent':
          $this->createPartialFile('_' . $objName . '_method_initComponent', $this->createStandardInitComponentPartial($objName));
          $partialStr .= "// initComponent\n" . 'include_partial("%1$s_method_initComponent", array("sfExtjs3Plugin" => $sfExtjs3Plugin, "%1$s" => $%1$s, "className" => $className));' . "\n";
          break;
        case 'initEvents':
          $this->createPartialFile('_' . $objName . '_method_initEvents', $this->createStandardInitEventsPartial($objName));
          $partialStr .= "// initEvents\n" . 'include_partial("%1$s_method_initEvents", array("sfExtjs3Plugin" => $sfExtjs3Plugin, "%1$s" => $%1$s, "className" => $className));' . "\n";
          break;
      }
    }
    return sprintf($partialStr, $objName);
  }

  /**
   * Creates a partial file if it does not exist
   *
   * @param string The partial filename
   * @param string The contents of the partial file
   *
   * @return null
   */
  public function createPartialFile($partialName, $contents = '')
  {
    $cacheFile = sfConfig::get('sf_module_cache_dir') . DIRECTORY_SEPARATOR . 'auto' . ucfirst($this->getModuleName()) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . "$partialName.js.php";
    file_put_contents($cacheFile, $contents);
    chmod($cacheFile, 0666);
  }

  protected function createStandardConstructorPartial($objName)
  {
    return sprintf('<?php // @object \$sfExtjs3Plugin string \$className and @object %1$s provided
// constructor
$configArr["parameters"] = "c";
$configArr["source"] = "
// %1$s config
this.%1$s_config = ".(isset($%1$s->config_array) ? $sfExtjs3Plugin->asAnonymousClass($%1$s->config_array) : \'{}\').";

// combine %1$s config with arguments
Ext.app.sf.$className.superclass.constructor.call(this, Ext.apply(this.%1$s_config, c));";
$%1$s->methods["constructor"] = $sfExtjs3Plugin->asMethod($configArr);', $objName);
  }

  protected function createStandardInitComponentPartial($objName)
  {
    return sprintf('<?php // @object \$sfExtjs3Plugin string \$className and @object %1$s provided
// initComponent
$configArr["source"] = "Ext.app.sf.$className.superclass.initComponent.apply(this, arguments);";
$%1$s->methods["initComponent"] = $sfExtjs3Plugin->asMethod($configArr);', $objName);
  }

  protected function createStandardInitEventsPartial($objName)
  {
    return sprintf('<?php // @object \$sfExtjs3Plugin string \$className and @object %1$s provided
// initEvents
$configArr["source"] = "Ext.app.sf.$className.superclass.initEvents.apply(this);";
$%1$s->methods["initEvents"] = $sfExtjs3Plugin->asMethod($configArr);', $objName);
  }

  /**
   * Get the code to modify a form object based on fields configuration.
   *
   * Configuration attributes considered for customization:
   *  * type
   *  * widgetClass
   *  * widgetOptions
   *  * widgetAttributes (same effect as the 'attributes' attribute)
   *  * validatorClass
   *  * validatorOptions
   *  * validatorMessages
   *
   * This also removes unused fields from the display list.
   *
   * <code>
   * form:
   *   display: [foo1, foo2]
   *   fields:
   *     foo1: { widgetOptions: { bar: baz } }
   *     foo2: { widgetClass: sfWidgetFormInputText, validatorClass: sfValidatorPass }
   *     foo3: { type: plain }
   * $form->getWidget('foo1')->setOption('bar', 'baz');
   * $form->setWidget('foo2', new sfWidgetFormInputText());
   * $form->setValidator('foo2', new sfValidatorPass());
   * $form->setWidget('foo3', new sfWidgetFormPlain());
   * $form->setValidator('foo3', new sfValidatorPass(array('required' => false)));
   * $form->mergePostValidator(new sfValidatorSchemaRemove(array('fields' => array('foo3'))));
   * unset($form['foo']);
   * </code>
   *
   * @param string $view Choices are 'edit', 'new', or 'filter'
   * @param string $formVariableName The name of the variable referencing the form.
   *                                 Choices are 'form', or 'filters'
   *
   * @return string the form customization code
   */
  public function getFormCustomization($view, $formVariableName = 'form', $withCredentialCheck = true)
  {
    $customization = '';
    $form = $this->configuration->getForm(); // fallback field definition

    //add active_one_to_one_relations array to the form from with in generator
    if($this->configuration->getWiths() && $view != 'filter')
    {
      $withs = array();
      foreach($this->configuration->getWiths() as $with)
      {
        foreach($this->getTableMap()->getRelations() as $relation)
        {
          if($relation->getType() == RelationMap::ONE_TO_ONE && $relation->getName() == $with)
          {
            $withs[] = $with;
          }
        }
      }
      if(count($withs)) $customization .= sprintf("    \$this->%s->active_one_to_one_relations = %s;\n", $formVariableName, $this->asPhp($withs));
    }

    $defaultFieldNames = array_keys($form->getWidgetSchema()->getFields());
    $unusedFields = array_combine($defaultFieldNames, $defaultFieldNames);
    $fieldsets = ($view == 'filter') ? array('NONE' => $this->configuration->getFormFilterFields($form)) : $this->configuration->getFormFields($form, $view);
    $plainFields = array();
    $credentialFields = array();

    foreach($fieldsets as $fieldset => $fields)
    {
      foreach($fields as $fieldName => $field)
      {
        // one-to-many widget creation
        if($field->getConfig('relation_name', false) && strpos($fieldName, '-'))
        {
          $generatorClass = sprintf('ExtjsForm%sGenerator', $view == 'filter' ? ucfirst($view) : '');
          $gen = new $generatorClass($this->getGeneratorManager());

          $relationMap = call_user_func(array(
            $field->getConfig('model') . 'Peer',
            'getTableMap'
          ));

          $column = $relationMap->getColumn($field->getConfig('field_name'));

          $widgetConfig = array_merge(array(
            'widgetClass' => $gen->getWidgetClassForColumn($column),
            'widgetOptions' => $gen->getWidgetOptionsForColumn($column),
            'validatorClass' => $gen->getValidatorClassForColumn($column),
            'validatorOptions' => $gen->getValidatorOptionsForColumn($column)
          ), $field->getConfig('widget', array()));

          // widget creation
          $widgetOptions = (isset($widgetConfig['widgetOptions']) && $widgetConfig['widgetOptions'] != '') ? $widgetConfig['widgetOptions'] : $this->asPhp(array());
          $widgetAttributes = (isset($widgetConfig['widgetAttributes'])) ? $widgetConfig['widgetAttributes'] : $this->asPhp(array());

          $customization .= sprintf("    \$this->%s->setWidget('%s', new %s(%s, %s));\n", $formVariableName, $fieldName, $widgetConfig['widgetClass'], $widgetOptions, $widgetAttributes);

          // validator creation
          $validatorOptions = (isset($widgetConfig['validatorOptions']) && $widgetConfig['validatorOptions'] != '') ? $widgetConfig['validatorOptions'] : $this->asPhp(array());
          $validatorMessages = (isset($widgetConfig['validatorMessages'])) ? $widgetConfig['validatorMessages'] : $this->asPhp(array());

          $format = 'new %s(%s, %s)';
          if(in_array($class = $widgetConfig['widgetClass'], array('sfValidatorInteger', 'sfValidatorNumber')))
          {
            $format = 'new sfValidatorSchemaFilter(\'text\', new %s(%s, %s))';
          }

          $customization .= sprintf("    \$this->%s->setValidator('%s', $format);\n", $formVariableName, $fieldName, $widgetConfig['validatorClass'], $validatorOptions, $validatorMessages);

          if($view == 'edit' || $view == 'update')
          {
            $customization .= sprintf("    \$this->%s->setDefault('%s', %s);\n", $formVariableName, $fieldName, $this->getColumnGetter($fieldName, true, '', 'this->'.$this->getSingularName()));
          }
        }
        else
        {
          // plain widget
          if($field->getConfig('type', false) == 'plain')
          {
            $plainFields[] = $fieldName;
            $customization .= sprintf("    \$this->%s->setWidget('%s', new sfWidgetFormPlain());\n", $formVariableName, $fieldName);
            $customization .= sprintf("    \$this->%s->setValidator('%s', new sfValidatorPass(array('required' => false)));\n", $formVariableName, $fieldName);
          }

          // widget customization
          if(! $widgetConfig = $field->getConfig('widget', array()))
          {
            if($widgetClass = $field->getConfig('widgetClass', false))
            {
              $widgetConfig['class'] = $widgetClass;
            }
            if($widgetOptions = $field->getConfig('widgetOptions', false))
            {
              $widgetConfig['options'] = $widgetOptions;
            }
            if($widgetAttributes = $field->getConfig('widgetAttributes', false))
            {
              $widgetConfig['attributes'] = $widgetAttributes;
            }
          }

          // custom combo config option for local
          if($field->getConfig('combo', false))
          {
            if(!isset($widgetConfig['class']))
            {
              $widgetConfig['class'] = 'ExtjsWidgetFormPropelChoice';
            }

            $options = array(
              'model' => $field->getConfig('model'),
              'group_by' => $field->getConfig('php_name')
            );

            $widgetConfig['options'] = (isset($widgetConfig['options'])) ? array_merge($options, $widgetConfig['options']) : $options;

            if(!isset($widgetConfig['attributes']))
            {
              $widgetConfig['attributes'] = array(
                'forceSelection' => false
              );
            }
          }

          //hidden fields
          if($field->isInvisible())
          {
            $widgetConfig['class'] = 'ExtjsWidgetFormInputHidden';
            if(isset($widgetConfig['options']['defaultValue']))
            {
              $widgetConfig['options'] = array('defaultValue' => $widgetConfig['options']['defaultValue']);
            }
            else
            {
              unset($widgetConfig['options']);
            }
          }

          if($widgetConfig)
          {
            $options = (isset($widgetConfig['options'])) ? $widgetConfig['options'] : array();
            if($view == 'filter') $options['context'] = 'filter';
            $attributes = (isset($widgetConfig['attributes'])) ? $widgetConfig['attributes'] : array();
            if(isset($widgetConfig['class']))
            {
              $class = $widgetConfig['class'];
              $customization .= sprintf("    \$this->%s->setWidget('%s', new %s(%s, %s));\n", $formVariableName, $fieldName, $class, $this->asPhp($options), $this->asPhp($attributes));
            }
            else
            {
              foreach($options as $name => $value)
              {
                $customization .= sprintf("    \$this->%s->getWidget('%s')->setOption('%s', %s);\n", $formVariableName, $fieldName, $name, $this->asPhp($value));
              }
              foreach($attributes as $name => $value)
              {
                $customization .= sprintf("    \$this->%s->getWidget('%s')->setAttribute('%s', %s);\n", $formVariableName, $fieldName, $name, $this->asPhp($value));
              }
            }
          }

          // validator configuration
          if(! $validatorConfig = $field->getConfig('validator', array()))
          {
            if($validatorClass = $field->getConfig('validatorClass', false))
            {
              $validatorConfig['class'] = $validatorClass;
            }
            if($validatorOptions = $field->getConfig('validatorOptions', false))
            {
              $validatorConfig['options'] = $validatorOptions;
            }
            if($validatorMessages = $field->getConfig('validatorMessages', false))
            {
              $validatorConfig['messages'] = $validatorMessages;
            }
          }
          if($validatorConfig)
          {
            $options = (isset($validatorConfig['options'])) ? $validatorConfig['options'] : array();
            $messages = (isset($validatorConfig['messages'])) ? $validatorConfig['messages'] : array();
            if(isset($validatorConfig['class']))
            {
              $class = $validatorConfig['class'];
              $customization .= sprintf("    \$this->%s->setValidator('%s', new %s(%s, %s));\n", $formVariableName, $fieldName, $class, $this->asPhp($options), $this->asPhp($messages));
            }
            else
            {
              foreach($options as $name => $value)
              {
                $customization .= sprintf("    \$this->%s->getValidator('%s')->setOption('%s', %s);\n", $formVariableName, $fieldName, $name, $this->asPhp($value));
              }
              foreach($messages as $name => $value)
              {
                $customization .= sprintf("    \$this->%s->getValidator('%s')->setMessage('%s', %s);\n", $formVariableName, $fieldName, $name, $this->asPhp($value));
              }
            }
          }
        }

        // this field is used
        if(isset($unusedFields[$fieldName]))
        {
//          if($field->getConfig())
          unset($unusedFields[$fieldName]);
        }

        if($withCredentialCheck && $field->getConfig('credentials'))
        {
          $credentialFields[$fieldName] = $field->getConfig('credentials');
        }

      }
    }

    // remove plain fields from validation
    if(! empty($plainFields))
    {
      $customization .= sprintf("    \$this->%s->mergePostValidator(new sfValidatorSchemaRemove(array('fields' => %s)));\n", $formVariableName, $this->asPhp($plainFields));
    }

    // add credential check if enabled
    if(! empty($credentialFields))
    {
      foreach($credentialFields as $field => $credentials)
      {
        $customization .= sprintf("    if(!\$this->getUser()->hasCredential(%s)) unset(\$this->%s['%s']);\n", $this->asPhp($credentials), $formVariableName, $field);
      }
    }

    // remove unused fields
    if(! empty($unusedFields))
    {
      foreach($unusedFields as $field)
      {
        // ignore primary keys, CSRF, and embedded forms
        if($form->getWidget($field) instanceof sfWidgetFormInputHidden || $form->getWidget($field) instanceof sfWidgetFormSchemaDecorator)
        {
          continue;
        }
        $customization .= sprintf("    unset(\$this->%s['%s']);\n", $formVariableName, $field);
      }
    }

    return $customization;
  }
}
