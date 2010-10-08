<?php

require_once dirname(__FILE__).'/ExtjsGenerateModuleTask.class.php';

/**
 * Wraps the generate module task to create a plugin module
 * 
 * @package    symfony
 * @subpackage ExtjsGeneratorPlugin
 * @author     Benjamin Runnels <kraven@kraven.org>
 */
class ExtjsGeneratePluginModuleTask extends ExtjsGenerateModuleTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('plugin', sfCommandArgument::REQUIRED, 'The plugin name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model class name'),
//      new sfCommandArgument('application', sfCommandArgument::OPTIONAL, 'The application name', 'frontend')
    ));
    
    $this->addOptions(array(
      new sfCommandOption('theme', null, sfCommandOption::PARAMETER_REQUIRED, 'The theme name', 'default'),
      new sfCommandOption('generate-in-cache', null, sfCommandOption::PARAMETER_NONE, 'Generate the module in cache'),
      new sfCommandOption('non-verbose-templates', null, sfCommandOption::PARAMETER_NONE, 'Generate non verbose templates'),
      new sfCommandOption('with-show', null, sfCommandOption::PARAMETER_NONE, 'Generate a show method'),
      new sfCommandOption('singular', null, sfCommandOption::PARAMETER_REQUIRED, 'The singular name', null),
      new sfCommandOption('plural', null, sfCommandOption::PARAMETER_REQUIRED, 'The plural name', null),
      new sfCommandOption('route-prefix', null, sfCommandOption::PARAMETER_REQUIRED, 'The route prefix', null),
      new sfCommandOption('with-propel-route', null, sfCommandOption::PARAMETER_NONE, 'Whether you will use a Propel route'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('actions-base-class', null, sfCommandOption::PARAMETER_REQUIRED, 'The base class for the actions', 'sfActions')
    ));

    $this->namespace = 'extjs';
    $this->name = 'generate-plugin-module';
    $this->briefDescription = 'Generates an ExtjsGenerator module in a plugin';

    $this->detailedDescription = <<<EOF
The [generate:plugin-module|INFO] task an ExtjsGenerator module in an existing plugin:

  [./symfony extjs:generate-plugin-module sfExamplePlugin article Article|INFO]

The task creates a [%module%|COMMENT] module in the [%plugin%|COMMENT] plugin
for the model class [%model%|COMMENT].

The generator can use a customized theme by using the [--theme|COMMENT] option:

  [./symfony extjs:generate-module --theme="custom" sfExamplePlugin article Article|INFO]

This way, you can create your very own module generator with your own conventions.

You can also change the default actions base class (default to sfActions) of
the generated modules:

  [./symfony extjs:generate-module --actions-base-class="ProjectActions" sfExamplePlugin article Article|INFO]

If a module with the same name already exists in the plugin, a
[sfCommandException|COMMENT] is thrown.
EOF;
  }
  
  protected function execute($arguments = array(), $options = array())
  {
    $arguments['application'] = $arguments['plugin'];
    parent::execute($arguments, $options);
  }
    
  protected function getModuleDirectory(Array $arguments)
  {
    $plugin = $arguments['plugin'];
    $module = $arguments['module'];
    $this->constants['PROJECT_NAME'] = $plugin;

    // verify the plugin exists
    if (!in_array($plugin, $this->configuration->getPlugins()))
    {
      // otherwise check the plugins directory
      $root = sfConfig::get('sf_plugins_dir').'/'.$plugin;
      if(!(is_dir($root) && count(sfFinder::type('any')->in($root)) > 0))
      {
        throw new sfCommandException(sprintf($boolean ? 'Plugin "%s" does not exist' : 'Plugin "%s" exists', $plugin));  
      }
    }

    // validate the module name
    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $module))
    {
      throw new sfCommandException(sprintf('The module name "%s" is invalid.', $module));
    }

    $pluginDir = sfConfig::get('sf_plugins_dir').'/'.$plugin;
    $moduleDir = $pluginDir.'/modules/'.$module;

    if (is_dir($moduleDir))
    {
      throw new sfCommandException(sprintf('The module "%s" already exists in the "%s" plugin.', $moduleDir, $plugin));
    }
    return $moduleDir;
  }
}
