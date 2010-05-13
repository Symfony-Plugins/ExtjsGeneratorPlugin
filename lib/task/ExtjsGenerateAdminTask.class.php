<?php
require_once(sfConfig::get('sf_plugins_dir').'/sfPropel15Plugin/lib/task/sfPropelGenerateAdminTask.class.php');

/**
 * Generates a Propel admin module.
 *
 * @package    symfony
 * @subpackage ExtjsGeneratorPlugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class ExtjsGenerateAdminTask extends sfPropelGenerateAdminTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('route_or_model', sfCommandArgument::REQUIRED, 'The route name or the model class'),
    ));

    $this->addOptions(array(
      new sfCommandOption('module', null, sfCommandOption::PARAMETER_REQUIRED, 'The module name', null),
      new sfCommandOption('theme', null, sfCommandOption::PARAMETER_REQUIRED, 'The theme name', 'admin'),
      new sfCommandOption('singular', null, sfCommandOption::PARAMETER_REQUIRED, 'The singular name', null),
      new sfCommandOption('plural', null, sfCommandOption::PARAMETER_REQUIRED, 'The plural name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('actions-base-class', null, sfCommandOption::PARAMETER_REQUIRED, 'The base class for the actions', 'sfActions'),
    ));

    $this->namespace = 'extjs';
    $this->name = 'generate-admin';
    $this->briefDescription = 'Generates an Extjs Propel admin module';

    $this->detailedDescription = <<<EOF
The [extjs:generate-admin|INFO] task generates an Extjs Propel admin module:

  [./symfony extjs:generate-admin frontend Article|INFO]

The task creates a module in the [%frontend%|COMMENT] application for the
[%Article%|COMMENT] model.

The task creates a route for you in the application [routing.yml|COMMENT].

You can also generate an Extjs Propel admin module by passing a route name:

  [./symfony extjs:generate-admin frontend article|INFO]

The task creates a module in the [%frontend%|COMMENT] application for the
[%article%|COMMENT] route definition found in [routing.yml|COMMENT].

For the filters and batch actions to work properly, you need to add
the [with_wildcard_routes|COMMENT] option to the route:

  article:
    class: ExtjsPropel15RouteCollection
    options:
      model:                Article
      with_wildcard_routes: true
EOF;
  }

  protected function generateForRoute($arguments, $options)
  {
    $routeOptions = $arguments['route']->getOptions();

    if (!$arguments['route'] instanceof ExtjsPropel15RouteCollection)
    {
      throw new sfCommandException(sprintf('The route "%s" is not a Propel collection route.', $arguments['route_name']));
    }

    $module = $routeOptions['module'];
    $model = $routeOptions['model'];

    // execute the propel:generate-module task
    $task = new ExtjsGenerateModuleTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->setConfiguration($this->configuration);

    $this->logSection('app', sprintf('Generating admin module "%s" for model "%s"', $module, $model));

    return $task->run(array($arguments['application'], $module, $model), array(
      'theme'                 => $options['theme'],
      'route-prefix'          => $routeOptions['name'],
      'with-propel-route'     => true,
      'generate-in-cache'     => true,
      'non-verbose-templates' => true,
      'singular'              => $options['singular'],
      'plural'                => $options['plural'],
      'actions-base-class'    => $options['actions-base-class'],
    ));
  }
}
