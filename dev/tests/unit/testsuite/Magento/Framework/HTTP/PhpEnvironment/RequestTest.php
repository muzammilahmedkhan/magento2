<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\HTTP\PhpEnvironment;

use Zend\Stdlib\Parameters;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var array
     */
    private $serverArray;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');

        // Stash the $_SERVER array to protect it from modification in test
        $this->serverArray = $_SERVER;
    }

    public function tearDown()
    {
        $_SERVER = $this->serverArray;
    }

    private function getModel($uri = null)
    {
        return new Request($uri);
    }

    public function testSetPathInfoWithNullValue()
    {
        $this->model = $this->getModel();
        $actual = $this->model->setPathInfo();
        $this->assertEquals($this->model, $actual);
    }

    public function testSetPathInfoWithValue()
    {
        $this->model = $this->getModel();
        $expected = 'testPathInfo';
        $this->model->setPathInfo($expected);
        $this->assertEquals($expected, $this->model->getPathInfo());
    }

    public function testSetPathInfoWithQueryPart()
    {
        $uri = 'http://test.com/node?queryValue';
        $this->model = $this->getModel($uri);
        $this->model->setPathInfo();
        $this->assertEquals('/node', $this->model->getPathInfo());
    }

    /**
     * @param string $name
     * @param string $default
     * @param string $result
     * @dataProvider getServerProvider
     */
    public function testGetServer($name, $default, $result)
    {
        $this->model = $this->getModel();
        $this->model->setServer(new Parameters([
            'HTTPS' => 'off',
            'DOCUMENT_ROOT' => '/test',
            'HTTP_ACCEPT' => '',
            'HTTP_CONNECTION' => 'http-connection',
            'HTTP_REFERER' => 'http-referer',
            'HTTP_X_FORWARDED_FOR' => 'x-forwarded',
            'HTTP_USER_AGENT' => 'user-agent',
            'PATH_INFO' => 'path-info',
            'QUERY_STRING' => '',
            'REMOTE_HOST' => 'remote-host',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => 'request-uri',
            'SERVER_NAME' => 'server-name',
        ]));
        $this->assertEquals($result, $this->model->getServer($name, $default));
    }

    /**
     * @return array
     */
    public function getServerProvider()
    {
        return [
            ['HTTPS', '', 'off'],
            ['DOCUMENT_ROOT', '', '/test'],
            ['ORIG_PATH_INFO', 'orig-path-info', 'orig-path-info'],
            ['PATH_INFO', '', 'path-info'],
            ['QUERY_STRING', '', ''],
            ['REMOTE_HOST', 'test', 'remote-host'],
            ['REQUEST_METHOD', '', 'GET'],
            ['REQUEST_URI', 'test', 'request-uri'],
            ['SERVER_NAME', 'test', 'server-name'],

            ['HTTP_ACCEPT', 'http-accept', ''],
            ['HTTP_CONNECTION', '', 'http-connection'],
            ['HTTP_HOST', 'http-host', 'http-host'],
            ['HTTP_REFERER', '', 'http-referer'],
            ['HTTP_USER_AGENT', '', 'user-agent'],
            ['HTTP_X_FORWARDED_FOR', '', 'x-forwarded'],

            ['Accept', 'accept', 'accept'],
            ['Connection', '', ''],
            ['Host', 'http-host', 'http-host'],
            ['Referer', 'referer', 'referer'],
            ['User-Agent', '', ''],
            ['X-Forwarded-For', 'test', 'test'],
        ];
    }

    public function testGetAliasWhenAliasExists()
    {
        $this->model = $this->getModel();
        $this->model->setAlias('AliasName', 'AliasTarget');
        $this->assertEquals('AliasTarget', $this->model->getAlias('AliasName'));
    }

    public function testGetAliasWhenAliasesIsNull()
    {
        $this->model = $this->getModel();
        $this->assertNull($this->model->getAlias('someValue'));
    }

    public function testSetPostValue()
    {
        $this->model = $this->getModel();

        $post = ['one' => '111', 'two' => '222'];
        $this->model->setPostValue($post);
        $this->assertEquals($post, $this->model->getPost()->toArray());

        $this->model->setPost(new Parameters([]));
        $this->assertEmpty($this->model->getPost()->toArray());

        $post = ['post_var' => 'post_value'];
        $this->model->setPostValue($post);
        $this->model->setPostValue('post_var 2', 'post_value 2');
        $this->assertEquals(
            ['post_var' => 'post_value', 'post_var 2' => 'post_value 2'],
            $this->model->getPost()->toArray()
        );
    }

    public function testGetFiles()
    {
        $this->model = $this->getModel();

        $files = ['one' => '111', 'two' => '222'];
        $this->model->setFiles(new Parameters($files));

        $this->assertEquals($files, $this->model->getFiles()->toArray());

        foreach ($files as $key => $value) {
            $this->assertEquals($value, $this->model->getFiles($key));
        }

        $this->assertNull($this->model->getFiles('no_such_file'));
        $this->assertEquals('default', $this->model->getFiles('no_such_file', 'default'));
    }

    public function testGetBaseUrlWithUrl()
    {
        $this->model = $this->getModel();
        $this->model->setBaseUrl('http:\/test.com\one/two');
        $this->assertEquals('http://test.com/one/two', $this->model->getBaseUrl());
    }

    public function testGetBaseUrlWithEmptyUrl()
    {
        $this->model = $this->getModel();
        $this->assertEmpty($this->model->getBaseUrl());
    }

    public function testGetAliasWhenAliasSet()
    {
        $this->model = $this->getModel();
        $this->model->setAlias('AliasName', 'AliasTarget');
        $this->assertEquals('AliasTarget', $this->model->getAlias('AliasName'));
    }

    public function testGetAliasWhenAliasAreEmpty()
    {
        $this->model = $this->getModel();
        $this->assertNull($this->model->getAlias(''));
    }
}
