<?php
use ActionKit\ServiceContainer;
use ActionKit\ActionTemplate\CodeGenActionTemplate;
use ActionKit\ActionRunner;

class ActionWithUser extends \LazyRecord\Testing\ModelTestCase
{
    public function getModels()
    {
        return array( 
            'User\Model\UserSchema',
            'Product\Model\ProductSchema',
        );
    }
    
    public function testRunAndJsonOutput()
    {
        $container = new ServiceContainer;
        $generator = $container['generator'];
        $generator->registerTemplate('CodeGenActionTemplate', new CodeGenActionTemplate());
        $runner = new ActionRunner($container);
        ok($runner);
        $runner->registerAutoloader();
        $runner->registerAction('CodeGenActionTemplate', array(
            'namespace' => 'User',
            'model' => 'User',
            'types' => array(
                ['name'=>'Create', 'allowedRoles'=>['user', 'admin']],
                ['name'=>'Update'],
                ['name'=>'Delete']
            )
        ));

        $result = $runner->run('User::Action::CreateUser',[ 
            'email' => 'foo@foo'
        ]);
        ok($result);
    }
}
