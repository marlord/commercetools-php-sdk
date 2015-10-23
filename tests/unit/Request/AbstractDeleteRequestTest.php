<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 * @created: 10.02.15, 10:29
 */

namespace Commercetools\Core\Request;

use GuzzleHttp\Message\Response;
use Commercetools\Core\AccessorTrait;
use Commercetools\Core\Client\HttpMethod;

/**
 * Class AbstractCreateRequestTest
 * @package Commercetools\Core\Request
 * @method AbstractDeleteRequest getRequest($class, array $args = [])
 */
class AbstractDeleteRequestTest extends \PHPUnit_Framework_TestCase
{
    use AccessorTrait;

    const ABSTRACT_DELETE_REQUEST = '\Commercetools\Core\Request\AbstractDeleteRequest';

    public function testGetId()
    {
        $request = $this->getRequest(static::ABSTRACT_DELETE_REQUEST, ['', '']);
        $request->setId('id');
        $this->assertSame('id', $request->getId());
    }

    public function testGetVersion()
    {
        $request = $this->getRequest(static::ABSTRACT_DELETE_REQUEST, ['', '']);
        $request->setVersion('version');
        $this->assertSame('version', $request->getVersion());
    }


    public function testHttpRequestMethod()
    {
        $request = $this->getRequest(static::ABSTRACT_DELETE_REQUEST, ['id', 'version']);
        $httpRequest = $request->httpRequest();

        $this->assertSame(HttpMethod::DELETE, $httpRequest->getMethod());
    }

    public function testHttpRequestPath()
    {
        $request = $this->getRequest(static::ABSTRACT_DELETE_REQUEST, ['id', 'version']);
        $httpRequest = $request->httpRequest();

        $this->assertSame('test/id?version=version', (string)$httpRequest->getUri());
    }

    public function testHttpRequestObject()
    {
        $request = $this->getRequest(static::ABSTRACT_DELETE_REQUEST, ['id', 'version']);
        $httpRequest = $request->httpRequest();

        $this->assertSame('test/id?version=version', (string)$httpRequest->getUri());
    }

    public function testBuildResponse()
    {
        $guzzleResponse = $this->getMock('\GuzzleHttp\Psr7\Response', [], [], '', false);
        $request = $this->getRequest(static::ABSTRACT_DELETE_REQUEST, ['id', 'version']);
        $response = $request->buildResponse($guzzleResponse);

        $this->assertInstanceOf('\Commercetools\Core\Response\ResourceResponse', $response);
    }
}
