<?php
namespace ActionKit;

use ActionKit\ActionTemplate\ActionTemplate;
use ActionKit\Exception\UndefinedTemplateException;
use Exception;
use UniversalCache;
use ReflectionClass;

/**
 * Action Generator Synopsis
 *
 *    $generator = new ActionGenerator;
 *    $generator->registerTemplate('TwigActionTemplate', new ActionKit\ActionTemplate\TwigActionTemplate());
 *
 *    $className = 'User\Action\BulkDeleteUser';
 *    $generatedAction = $generator->generate('TwigActionTemplate',
 *        $className,
 *        array(
 *            'template' => '@ActionKit/RecordAction.html.twig',
 *            'variables' => array(
 *                'record_class' => 'User\\Model\\User',
 *                'base_class' => 'ActionKit\\RecordAction\\CreateRecordAction'
 *            )
 *        )
 *    );
 *
 *    require $cacheFile;
 *
 */
class ActionGenerator
{
    protected $templates = array();

    /**
     * The new generate method to generate action class with action template
     *
     * @param string $templateName
     * @param string $class
     * @param array $actionArgs template arguments
     * @return ActionKit\GeneratedAction
     */
    public function generate($templateName, $class, array $actionArgs = array())
    {
        $actionTemplate = $this->getTemplate($templateName);
        $generatedAction = $actionTemplate->generate($class, $actionArgs);
        return $generatedAction;
    }


    /**
     * generateAt generates the action code at $classFilePath
     *
     * @param string $classFilePath
     * @param string $class
     * @param string $templateName
     * @param array $actionArgs template arguments
     * @return ActionKit\GeneratedAction
     */
    public function generateAt($classFilePath, $templateName, $class, array $actionArgs = array())
    {
        $generatedAction = $this->generate($templateName, $class, $actionArgs);
        $generatedAction->writeTo($classFilePath);
        return $generatedAction;
    }


    /**
     * generateUnderDirectory generates the action code under a directory path
     *
     * @param string $directory The directory for placing generated class
     * @param string $class
     * @param string $templateName
     * @param array $actionArgs template arguments
     * @return ActionKit\GeneratedAction
     */
    public function generateUnderDirectory($directory, $templateName, $class, array $actionArgs = array())
    {
        $generatedAction = $this->generate($templateName, $class, $actionArgs);
        $classPath = $generatedAction->getPsrClassPath();
        $path = $directory . DIRECTORY_SEPARATOR . $classPath;

        if ($dir = dirname($path)) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $generatedAction->writeTo($path);
        return $generatedAction;
    }

    /**
     * register action template
     * @param object $template the action template object
     */
    public function registerTemplate($templateName, ActionTemplate $template)
    {
        $this->templates[$templateName] = $template;
    }

    /**
     * load action template object with action template name
     * @param string $templateName the action template name
     * @return object action template object
     */
    public function getTemplate($templateName)
    {
        if (isset($this->templates[$templateName])) {
            return $this->templates[$templateName];
        } else {
            throw new UndefinedTemplateException("load $templateName template failed.");
        }
    }
}
