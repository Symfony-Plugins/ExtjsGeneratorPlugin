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
   * Returns the getter either non-developped: 'getFoo' or developped: '$class->getFoo()'.
   *
   * @param string  $column     The column name
   * @param boolean $developed  true if you want developped method names, false otherwise
   * @param string  $prefix     The prefix value
   *
   * @return string PHP code
   */
  public function getColumnGetter($column, $developed = false, $prefix = '')
  {
    $columnArr = explode('-', $column);
    $relatedGetter = '';
    $className = $this->getModelClass();

    for($i = 0; $i <= count($columnArr) - 1; $i ++)
    {
      if(count($columnArr) > 1)
      {
        if(! isset($map)) $map = call_user_func(array(
          $className . 'Peer',
          'getTableMap'
        ));

        try
        {
          $column = $map->getColumn($columnArr[$i]);
        }
        catch(PropelException $e)
        {
          //not a real column
          if($i == count($columnArr) - 1)
          {
            $getter = 'get' . sfInflector::camelize($columnArr[$i]);
          }
          else
          {
            $relatedGetter .= 'get' . sfInflector::camelize($columnArr[$i]) . '()->';
          }
          continue;

        }

        /* @var $column ColumnMap */
        if($column->getRelatedTableName())
        {
          $map = $column->getRelatedTable();
          $className = $map->getPhpName();
          $relatedGetter .= 'get' . ucfirst($column->getRelation()->getName()) . '()->';
        }
        else
        {
          $column = $columnArr[$i];
        }
      }
    }

    if(! isset($getter))
    {
      try
      {
        $getter = 'get' . call_user_func(array(
          constant($className . '::PEER'),
          'translateFieldName'
        ), $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
      }
      catch(PropelException $e)
      {
        // not a real column
        $getter = 'get' . sfInflector::camelize($column);
      }
    }

    if(! $developed)
    {
      return $getter;
    }

    return sprintf('$%s%s->%s%s()', $prefix, $this->getSingularName(), $relatedGetter, $getter);
  }

  /**
   * Returns the type of a column.
   *
   * @param  object $column A column object
   *
   * @return string The column type
   */
  public function getType($column)
  {
    if($column->isForeignKey())
    {
      return 'ForeignKey';
    }

    switch($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        return 'Boolean';
      case PropelColumnTypes::DATE:
      case PropelColumnTypes::TIMESTAMP:
        return 'Date';
      case PropelColumnTypes::TIME:
        return 'Time';
      case PropelColumnTypes::INTEGER:
        return 'Integer';
      case PropelColumnTypes::FLOAT:
        return 'Float';
      case PropelColumnTypes::LONGVARCHAR:
        return 'Text';
      default:
        return 'String';
    }
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
      $html = sprintf("$html ? call_user_func_array(%s, array_merge(array(%s), %s)) : '&nbsp;'", $this->asPhp($renderer), $html, $this->asPhp($field->getRendererArguments()));
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
      $html = sprintf("false !== strtotime($html) ? format_date(%s, \"%s\") : '&nbsp;'", $html, $field->getConfig('date_format', 'f'));
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
      $fieldArr['name'] = sprintf($form[$field->getName()]->getParent()->getWidget()->getNameFormat(), $field->getName());
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
      'header' => "[?php echo __('" . $field->getConfig('label', '', true) . "', array(), '" . $this->getI18nCatalogue() . "'); ?]",
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
      return sprintf("\$columnModel->attributes['%s_objectactions'] = \$sfExtjs3Plugin->asVar('Ext.ComponentMgr.create({xtype: \'%s\', header:\'&nbsp;\'})')", $this->getModuleName(), $this->getModuleName() . 'objectactions');
    }

    //TODO refactor this to provide il8n support for header
    return sprintf("\$columnModel->attributes['%s_%s'] = \$sfExtjs3Plugin->asVar('Ext.ComponentMgr.createPlugin('.\$sfExtjs3Plugin->asAnonymousClass(%s).')')", $field->getName(), $field->getConfig('plugin'), $this->asPhp(array_merge(array(
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
        return "\$topToolbar->config_array['items'][] = array('xtype' => 'tbfill');";

      case 'new':
        $configArr['icon'] = 'table_row_insert';
        $configArr['name'] = 'New';
        $configArr['help'] = 'Create a new record';
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
        return "\$formpanel->config_array['tbar'][] = array('xtype' => 'tbfill');";

      case 'cancel':
        $configArr['name'] = 'Close/Cancel';
        $configArr['icon'] = 'decline';
        break;

      case 'reload':
        $configArr['icon'] = 'page_white_refresh_arrows';
        $hide_when_new = true;
        break;

      case 'save':
        $configArr['icon'] = 'page_white_accept';
        $type = 'submit';
        break;

      case 'savenew':
        $configArr['icon'] = 'page_white_add';
        $configArr['name'] = 'Save as New';
        $type = 'submit';
        $hide_when_new = true;
        break;

      case 'delete':
        $configArr['icon'] = 'page_white_delete';
        $hide_when_new = true;
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
        $partialStr .= "include_partial('" . $partial . "', array('sfExtjs3Plugin' => \$sfExtjs3Plugin, '$objName' => \$$objName, 'className' => \$className));\n";
        $this->createPartialFile('_' . $partial, '<?php // @object $sfExtjs3Plugin, $className and @object $' . $objName . ' provided ?>');
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
    return sprintf('<?php
// constructor
$configArr["parameters"] = "c";
$configArr["source"] = "
// %1$s config
this.%1$s_config = ".(isset($%1$s->config_array) ? $sfExtjs3Plugin->asAnonymousClass($%1$s->config_array) : \'{}\').";

// combine %1$s config with arguments
Ext.app.sf.$className.superclass.constructor.call(this, Ext.apply(this.%1$s_config, c));";
$%1$s->attributes["constructor"] = $sfExtjs3Plugin->asMethod($configArr);', $objName);
  }

  protected function createStandardInitComponentPartial($objName)
  {
    return sprintf('<?php
// initComponent
$configArr["source"] = "Ext.app.sf.$className.superclass.initComponent.apply(this, arguments);";
$%1$s->attributes["initComponent"] = $sfExtjs3Plugin->asMethod($configArr);', $objName);
  }

  protected function createStandardInitEventsPartial($objName)
  {
    return sprintf('<?php
// initEvents
$configArr["source"] = "Ext.app.sf.$className.superclass.initEvents.apply(this);";
$%1$s->attributes["initEvents"] = $sfExtjs3Plugin->asMethod($configArr);', $objName);
  }
}
