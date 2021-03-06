<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Controller;

class CreateAdminAccountTest extends \PHPUnit_Framework_TestCase
{
    public function testIndexAction()
    {
        $controller = new CreateAdminAccount();
        $viewModel = $controller->indexAction();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $viewModel);
        $this->assertTrue($viewModel->terminate());
    }
}
