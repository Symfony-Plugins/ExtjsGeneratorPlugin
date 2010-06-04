<?php

/**
 * Model generator field.
 *
 * @package    symfony
 * @subpackage generator
 * @author Benjamin Runnels
 */
class ExtjsModelGeneratorConfigurationField extends sfModelGeneratorConfigurationField
{
  protected $hasflag = false;

  /**
   * Constructor.
   *
   * @param string $name   The field name
   * @param array  $config The configuration for this field
   */
  public function __construct($name, $config)
  {
    $this->name = $name;
    $this->config = $config;

    if(isset($this->config['flag']))
    {
      $this->setFlag($this->config['flag']);
      unset($this->config['flag']);
    }
    if(isset($config['flag']) && $config['flag'] !== null) $this->hasflag = true;
  }

  public function getKey()
  {
    $fieldArr = self::splitFieldWithFlag($this->getName());
    return $fieldArr[0];
  }

  public function setColumnModelRenderer($renderer)
  {
    $this->config['column_model_renderer'] = $renderer;
  }

  public function getColumnModelRenderer()
  {
    if(! isset($this->config['column_model_renderer']))
    {
      switch($this->getType())
      {
        case 'Time':
        case 'Date':
          $format = $this->getConfig('date_format', sfConfig::get('app_extjs_gen_plugin_format_date', 'm/d/Y'));
          $renderer = "function(value){ return Ext.util.Format.date(value, '$format'); }";
          break;
        case 'Text':
          $renderer = 'this.formatLongstring';
          break;
        case 'Boolean':
          $renderer = 'this.formatBoolean';
          break;
        case 'Int':
        case 'Float':
          $renderer = 'this.formatNumber';
          break;
        default:
          $renderer = false;
      }
      if($this->isLink()) $renderer = 'this.renderLink';

      $this->setColumnModelRenderer($renderer);
    }
    return $this->config['column_model_renderer'];
  }

  public function setReaderFieldType($type)
  {
    $this->config['reader_field_type'] = $type;
  }

  public function getReaderFieldType()
  {
    if(! isset($this->config['reader_field_type']))
    {
      switch($this->getType())
      {
        case 'Text':
        case 'String':
          $type = 'string';
          break;
        case 'Time':
        case 'Date':
          $type = 'date';
          break;
        case 'Integer':
          $type = 'int';
          break;
        case 'Float':
          $type = 'float';
          break;
        case 'Boolean':
          $type = 'boolean';
          break;
        default:
          $type = 'auto';
      }
      $this->setReaderFieldType($type);
    }
    return $this->config['reader_field_type'];
  }

  /**
   * Returns the configuration value for a given key.
   *
   * If the key is null, the method returns all the configuration array.
   *
   * @param string  $key     A key string
   * @param mixed   $default The default value if the key does not exist
   * @param Boolean $escaped Whether to escape single quote (false by default)
   *
   * @return mixed The configuration value associated with the key
   */
  public function getConfig($key = null, $default = null, $escaped = false)
  {
    if(null === $key)
    {
      return $this->config;
    }

    $value = ExtjsModelGeneratorConfiguration::getFieldConfigValue($this->config, $key, $default);

    return $escaped ? str_replace("'", "\\'", $value) : $value;
  }

  /**
   * Sets or unsets the invisible flag.
   *
   * @param Boolean $boolean true if the field is invisible, false otherwise
   */
  public function setInvisible($boolean)
  {
    $this->config['is_invisible'] = $boolean;
  }

  /**
   * Returns true if the column is invisible.
   *
   * @return boolean true if the column is invisible, false otherwise
   */
  public function isInvisible()
  {
    return isset($this->config['is_invisible']) ? $this->config['is_invisible'] : false;
  }

  /**
   * Sets or unsets the hidden flag.
   *
   * @param Boolean $boolean true if the field is hidden, false otherwise
   */
  public function setHidden($boolean)
  {
    $this->config['is_hidden'] = $boolean;
  }

  /**
   * Returns true if the column is hidden.
   *
   * @return boolean true if the column is hidden, false otherwise
   */
  public function isHidden()
  {
    return isset($this->config['is_hidden']) ? $this->config['is_hidden'] : false;
  }

  /**
   * Sets or unsets the plugin flag.
   *
   * @param Boolean $boolean true if the field is a plugin, false otherwise
   */
  public function setPlugin($boolean)
  {
    $this->config['is_plugin'] = $boolean;
  }

  /**
   * Returns true if the column is a plugin.
   *
   * @return boolean true if the column is a plugin, false otherwise
   */
  public function isPlugin()
  {
    if($this->getConfig('plugin') !== null)
    {
      $this->setPlugin(true);
    }
    return isset($this->config['is_plugin']) ? $this->config['is_plugin'] : false;
  }

  static public function splitFieldWithFlag($field)
  {
    if(in_array($flag = $field[0], array(
      '=',
      '_',
      '~',
      '+',
      '^'
    )))
    {
      $field = substr($field, 1);
    }
    else
    {
      $flag = null;
    }

    return array(
      $field,
      $flag
    );
  }

  public function hasFlag()
  {
    return $this->hasflag;
  }

  /**
   * Sets a flag.
   *
   * The flag can be =, _, +, ^, or ~.
   *
   * @param string $flag The flag
   */
  public function setFlag($flag)
  {
    if(null === $flag)
    {
      return;
    }

    switch($flag)
    {
      case '+':
        $this->setInvisible(true);
        break;
      case '-':
        $this->setHidden(true);
        break;
      case '^':
        $this->setPlugin(true);
        break;
      default:
        parent::setFlag($flag);
    }
  }

  /**
   * Gets the flag associated with the field.
   *
   * The flag will be
   *
   * * = for a link
   * * _ for a partial
   * * ~ for a component
   * * + for a invisible field
   * * - for a hidden field
   * * ^ for a plugin
   *
   * @return string The flag
   */
  public function getFlag()
  {
    if($this->isPlugin())
    {
      return '^';
    }
    elseif($this->isInvisible())
    {
      return '+';
    }
    elseif($this->isHidden())
    {
      return '-';
    }

    return parent::getFlag();
  }
}
