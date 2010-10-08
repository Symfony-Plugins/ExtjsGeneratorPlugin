<?php
require_once dirname(__FILE__).'/ExtjsGenerateAdminTask.class.php';

/**
 * Generates a Propel admin module.
 *
 * @package    symfony
 * @subpackage ExtjsGeneratorPlugin
 * @author     Benjamin Runnels <kraven@kraven.org>
 */
class ExtjsGeneratePluginAdminTask extends ExtjsGenerateAdminTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('plugin', sfCommandArgument::REQUIRED, 'The plugin name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
      new sfCommandArgument('route_or_model', sfCommandArgument::REQUIRED, 'The route name or the model class')
    ));

    $this->addOptions(array(
      new sfCommandOption('theme', null, sfCommandOption::PARAMETER_REQUIRED, 'The theme name', 'admin'),
      new sfCommandOption('singular', null, sfCommandOption::PARAMETER_REQUIRED, 'The singular name', null),
      new sfCommandOption('plural', null, sfCommandOption::PARAMETER_REQUIRED, 'The plural name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('actions-base-class', null, sfCommandOption::PARAMETER_REQUIRED, 'The base class for the actions', 'sfActions')
    ));

    $this->namespace = 'extjs';
    $this->name = 'generate-plugin-admin';
    $this->briefDescription = 'Generates an Extjs Propel admin module in a plugin';

    $this->detailedDescription = <<<EOF
The [extjs:generate-admin|INFO] task generates an Extjs Propel admin module in an existing plugin:

  [./symfony extjs:generate-plugin-admin sfExamplePlugin article Article|INFO]

The task creates an [article|COMMENT] module in the [sfExamplePlugin|COMMENT] plugin for the
[Article|COMMENT] model.

The task creates a route for you in [sfExamplePlugin/config/routing.yml|COMMENT].

You can also generate an Extjs Propel admin module by passing a route name:

  [./symfony extjs:generate-plugin-admin sfExamplePlugin article article_route|INFO]

The task creates an [article|COMMENT] module in the [sfExamplePlugin|COMMENT] plugin for the
[article_route|COMMENT] route definition found in [sfExamplePlugin/config/routing.yml|COMMENT].

For the filters and batch actions to work properly, you need to add
the [with_wildcard_routes|COMMENT] option to the route:

  article:
    class: ExtjsPropel15RouteCollection
    options:
      model:                Article
      with_wildcard_routes: true
EOF;
  }
  
  protected function execute($arguments = array(), $options = array())
  {
    $options['module'] = $arguments['module'];
    $arguments['application'] = $arguments['plugin'];
    parent::execute($arguments, $options);  
  }
  
  protected function generateForRoute($arguments, $options)
  {
    $routeOptions = $arguments['route']->getOptions();

    if(! $arguments['route'] instanceof ExtjsPropel15RouteCollection)
    {
      throw new sfCommandException(sprintf('The route "%s" is not a Propel collection route.', $arguments['route_name']));
    }

    $module = $routeOptions['module'];
    $model = $routeOptions['model'];

    // execute the propel:generate-module task
    $task = new ExtjsGeneratePluginModuleTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->setConfiguration($this->configuration);

    $this->logSection('app', sprintf('Generating plugin admin module "%s" for model "%s"', $module, $model));

    return $task->run(array(
      $arguments['plugin'],
      $module,
      $model
    ), array(
      'theme' => $options['theme'],
      'route-prefix' => $routeOptions['name'],
      'with-propel-route' => true,
      'generate-in-cache' => true,
      'non-verbose-templates' => true,
      'singular' => $options['singular'],
      'plural' => $options['plural'],
      'actions-base-class' => $options['actions-base-class']
    ));
  }
  
  protected function getRouting($arguments)
  {
    $pluginDir = sfConfig::get('sf_plugins_dir').'/'.$arguments['plugin'];
    $routing = $pluginDir . '/config/routing.yml';
    
    if(!file_exists($routing))
    {
      $this->getFilesystem()->touch($routing);
    }
    
    return $routing;
  }
}