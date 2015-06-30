<?php
use ActionKit\ActionTemplate\FileBasedActionTemplate;
use ActionKit\ActionRunner;
use ActionKit\ActionGenerator;
use ActionKit\RecordAction\BaseRecordAction;
use ActionKit\ActionTemplate\RecordActionTemplate;

class ActionGeneratorTest extends PHPUnit_Framework_TestCase
{


    // TODO: should be moved to BaseRecordActionTest
    public function testCRUDClassFromBaseRecordAction()
    {
        $class = BaseRecordAction::createCRUDClass( 'App\Model\Post' , 'Create' );
        ok($class);
        is('App\Action\CreatePost', $class);
    }


    /**
     * @expectedException ActionKit\Exception\UndefinedTemplateException
     */
    public function testUndefinedTemplate()
    {
        $generator = new ActionGenerator();
        $template = $generator->getTemplate('UndefinedTemplate');
    }

    public function testRecordActionTemplate()
    {
        $generator = new ActionGenerator();
        $generator->registerTemplate('RecordActionTemplate', new ActionKit\ActionTemplate\RecordActionTemplate());
        $template = $generator->getTemplate('RecordActionTemplate');
        $this->assertInstanceOf('ActionKit\ActionTemplate\ActionTemplate', $template);

        $runner = new ActionKit\ActionRunner;
        $actionArgs = array(
            'namespace' => 'test',
            'model' => 'testModel',
            'types' => array(
                [ 'name' => 'Create'],
                [ 'name' => 'Update'],
                [ 'name' => 'Delete'],
                [ 'name' => 'BulkDelete']
            )
        );
        $template->register($runner, 'RecordActionTemplate', $actionArgs);
        is(4, count($runner->dynamicActions));

        $className = 'test\Action\UpdatetestModel';

        is(true, isset($runner->dynamicActions[$className]));

        $generatedAction = $generator->generate('RecordActionTemplate',
            $className,
            $runner->dynamicActions[$className]['actionArgs']);

        $generatedAction->load();

        ok(class_exists($className));
    }

    public function testFildBased()
    {
        $generator = new ActionKit\ActionGenerator();
        $generator->registerTemplate('FileBasedActionTemplate', new FileBasedActionTemplate());
        $template = $generator->getTemplate('FileBasedActionTemplate');
        $this->assertInstanceOf('ActionKit\ActionTemplate\ActionTemplate', $template);

        $runner = new ActionKit\ActionRunner;
        $template->register($runner, 'FileBasedActionTemplate', array(
            'action_class' => 'User\\Action\\BulkUpdateUser',
            'template' => '@ActionKit/RecordAction.html.twig',
            'variables' => array(
                'record_class' => 'User\\Model\\User',
                'base_class' => 'ActionKit\\RecordAction\\CreateRecordAction'
            )
        ));
        is(1, count($runner->dynamicActions));

        $className = 'User\Action\BulkUpdateUser';

        is(true, isset($runner->dynamicActions[$className]));

        $generatedAction = $generator->generate('FileBasedActionTemplate',
            $className,
            $runner->dynamicActions[$className]['actionArgs']);

        $generatedAction->load();

        ok(class_exists($className));
    }

    public function testWithoutRegister()
    {
        $generator = new ActionKit\ActionGenerator();
        $generator->registerTemplate('FileBasedActionTemplate', new ActionKit\ActionTemplate\FileBasedActionTemplate());

        $className = 'User\Action\BulkDeleteUser';

        $generatedAction = $generator->generate('FileBasedActionTemplate',
            $className,
            array(
                'template' => '@ActionKit/RecordAction.html.twig',
                'variables' => array(
                    'record_class' => 'User\\Model\\User',
                    'base_class' => 'ActionKit\\RecordAction\\CreateRecordAction'
                )
            )
        );
        $generatedAction->load();
        ok( class_exists( $className ) );
    }

}

