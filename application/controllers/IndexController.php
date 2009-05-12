<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body

        // testing Doctrine
        $q = Doctrine_Query::create()
            ->from('Test t')
            ->limit(2)
            ->execute();
            var_dump($q->toArray());

            // TODO: test doctrine session handler
        $s = new Zend_Session_Namespace();
        $s->testString = 'foo';
        $s->testInt = 123;

    }


}

