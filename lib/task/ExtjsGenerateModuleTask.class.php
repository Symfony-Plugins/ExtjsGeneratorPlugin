<?php
require_once (sfConfig::get('sf_plugins_dir') . '/sfPropel15Plugin/lib/task/sfPropelGenerateModuleTask.class.php');

/**
 * Generates a Propel module.
 *
 * @package    symfony
 * @subpackage ExtjsGeneratorPlugin
 * @author     Benjamin Runnels <kraven@kraven.org>
 */
class ExtjsGenerateModuleTask extends sfPropelGenerateModuleTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model class name')
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
    $this->name = 'generate-module';
    $this->briefDescription = 'Generates an ExtjsGenerator module';

    $this->detailedDescription = <<<EOF
The [extjs:generate-module|INFO] task generates an ExtjsGenerator module:

  [./symfony extjs:generate-module frontend article Article|INFO]

The task creates an [article|COMMENT] module in the [frontend|COMMENT] application
for the model class [Article|COMMENT].

You can also create an empty module that inherits its actions and templates from
a runtime generated module in [%sf_app_cache_dir%/modules/auto%module%|COMMENT] by
using the [--generate-in-cache|COMMENT] option:

  [./symfony extjs:generate-module --generate-in-cache frontend article Article|INFO]

The generator can use a customized theme by using the [--theme|COMMENT] option:

  [./symfony extjs:generate-module --theme="custom" frontend article Article|INFO]

This way, you can create your very own module generator with your own conventions.

You can also change the default actions base class (default to sfActions) of
the generated modules:

  [./symfony extjs:generate-module --actions-base-class="ProjectActions" frontend article Article|INFO]
EOF;
  }

  protected function executeGenerate($arguments = array(), $options = array())
  {     
    // generate module
    $tmpDir = sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . md5(uniqid(rand(), true));
    $generatorManager = new sfGeneratorManager($this->configuration, $tmpDir);
    $generatorManager->generate('ExtjsGenerator', array(
      'model_class' => $arguments['model'],
      'moduleName' => $arguments['module'],
      'theme' => $options['theme'],
      'non_verbose_templates' => $options['non-verbose-templates'],
      'with_show' => $options['with-show'],
      'singular' => $options['singular'],
      'plural' => $options['plural'],
      'route_prefix' => $options['route-prefix'],
      'with_propel_route' => $options['with-propel-route'],
      'actions_base_class' => $options['actions-base-class']
    ));

    $moduleDir = $this->getModuleDirectory($arguments);
//    var_export($moduleDir);

    // copy our generated module
    $this->getFilesystem()->mirror($tmpDir . DIRECTORY_SEPARATOR . 'auto' . ucfirst($arguments['module']), $moduleDir, sfFinder::type('any'));

    if(! $options['with-show'])
    {
      $this->getFilesystem()->remove($moduleDir . '/templates/showSuccess.php');
    }

    // change module name
    $finder = sfFinder::type('file')->name('*.php');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '', '', array(
      'auto' . ucfirst($arguments['module']) => $arguments['module']
    ));

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $this->constants);

    // create basic test
    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir') . DIRECTORY_SEPARATOR . 'task' . DIRECTORY_SEPARATOR . 'generator' . DIRECTORY_SEPARATOR . 'skeleton' . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'actionsTest.php', sfConfig::get('sf_test_dir') . DIRECTORY_SEPARATOR . 'functional' . DIRECTORY_SEPARATOR . $arguments['application'] . DIRECTORY_SEPARATOR . $arguments['module'] . 'ActionsTest.php');

    // customize test file
    $this->getFilesystem()->replaceTokens(sfConfig::get('sf_test_dir') . DIRECTORY_SEPARATOR . 'functional' . DIRECTORY_SEPARATOR . $arguments['application'] . DIRECTORY_SEPARATOR . $arguments['module'] . 'ActionsTest.php', '##', '##', $this->constants);

    // delete temp files
    $this->getFilesystem()->remove(sfFinder::type('any')->in($tmpDir));
  }

  protected function executeInit($arguments = array(), $options = array())
  {
    $moduleDir = $this->getModuleDirectory($arguments);

    // create basic application structure
    $finder = sfFinder::type('any')->discard('.sf');
    $dirs = $this->configuration->getGeneratorSkeletonDirs('ExtjsModule', $options['theme']);

    foreach($dirs as $dir)
    {
      if(is_dir($dir))
      {
        $this->getFilesystem()->mirror($dir, $moduleDir, $finder);
        break;
      }
    }

    // move configuration file
    if(file_exists($config = $moduleDir . '/lib/configuration.php'))
    {
      if(file_exists($target = $moduleDir . '/lib/' . $arguments['module'] . 'GeneratorConfiguration.class.php'))
      {
        $this->getFilesystem()->remove($config);
      }
      else
      {
        $this->getFilesystem()->rename($config, $target);
      }
    }

    // move helper file
    if(file_exists($config = $moduleDir . '/lib/helper.php'))
    {
      if(file_exists($target = $moduleDir . '/lib/' . $arguments['module'] . 'GeneratorHelper.class.php'))
      {
        $this->getFilesystem()->remove($config);
      }
      else
      {
        $this->getFilesystem()->rename($config, $target);
      }
    }

    // create basic test
    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir') . DIRECTORY_SEPARATOR . 'task' . DIRECTORY_SEPARATOR . 'generator' . DIRECTORY_SEPARATOR . 'skeleton' . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'actionsTest.php', sfConfig::get('sf_test_dir') . DIRECTORY_SEPARATOR . 'functional' . DIRECTORY_SEPARATOR . $arguments['application'] . DIRECTORY_SEPARATOR . $arguments['module'] . 'ActionsTest.php');

    // customize test file
    $this->getFilesystem()->replaceTokens(sfConfig::get('sf_test_dir') . DIRECTORY_SEPARATOR . 'functional' . DIRECTORY_SEPARATOR . $arguments['application'] . DIRECTORY_SEPARATOR . $arguments['module'] . 'ActionsTest.php', '##', '##', $this->constants);

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->constants['CONFIG'] = sprintf(<<<EOF
    model_class:            %s
    theme:                  %s
    non_verbose_templates:  %s
    with_show:              %s
    singular:               %s
    plural:                 %s
    route_prefix:           %s
    with_propel_route:      %s
    actions_base_class:     %s
EOF
    , $arguments['model'], $options['theme'], $options['non-verbose-templates'] ? 'true' : 'false', $options['with-show'] ? 'true' : 'false', $options['singular'] ? $options['singular'] : '~', $options['plural'] ? $options['plural'] : '~', $options['route-prefix'] ? $options['route-prefix'] : '~', $options['with-propel-route'] ? $options['with-propel-route'] : 'false', $options['actions-base-class']);
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $this->constants);
  }
  
  protected function getModuleDirectory(Array $arguments)
  {
    return sfConfig::get('sf_app_module_dir') . '/' . $arguments['module'];
  }
}
