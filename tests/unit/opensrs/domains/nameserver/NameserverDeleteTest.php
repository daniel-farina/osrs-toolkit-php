<?php

use opensrs\domains\nameserver\NameserverDelete;

/**
 * @group nameserver
 * @group NameserverDelete
 */
class NameserverDeleteTest extends PHPUnit_Framework_TestCase
{
    protected $func = 'nsDelete';

    protected $validSubmission = array(
        'cookie' => '',

        'attributes' => array(
            /*
             * Required
             *
             * domain: relevant domain, required
             *   only if cookie is not set
             * ipaddress: IPv4 address of the
             *   nameserver. always required for
             *   .DE, otherwise required only if
             *   ipv6 is not submitted
             * ipv6: ipv6 address of the nameserver
             *   * not supported for .cn domains
             * name: fully qualified domain name
             *   for the nameserver
             */
            'domain' => '',
            'ipaddress' => '',
            'ipv6' => '',
            'name' => '',
            ),
        );

    /**
     * Valid submission should complete with no
     * exception thrown.
     *
     *
     * @group validsubmission
     */
    public function testValidSubmission()
    {
        $data = json_decode(json_encode($this->validSubmission));

        // assign_ns request
        $data->cookie = md5(time());

        // generate a random IPv4
        $data->attributes->ipaddress = long2ip(mt_rand());

        // generate a random (fake) IPv6
        $data->attributes->ipv6 = implode(':', str_split(sha1(dechex(mt_rand(0, 2147483647))), 4));
        $data->attributes->name = 'ns1.'.$data->attributes->domain;

        $ns = new NameserverDelete('array', $data);

        $this->assertTrue($ns instanceof NameserverDelete);
    }

    /**
     * Data Provider for Invalid Submission test.
     */
    public function submissionFields()
    {
        return array(
            'missing name' => array('name'),
            );
    }

    /**
     * Invalid submission should throw an exception.
     *
     *
     * @dataProvider submissionFields
     * @group invalidsubmission
     */
    public function testInvalidSubmissionFieldsMissing($field, $parent = 'attributes', $message = null)
    {
        $data = json_decode(json_encode($this->validSubmission));

        // assign_ns request
        $data->cookie = md5(time());

        // generate a random IPv4
        $data->attributes->ipaddress = long2ip(mt_rand());

        // generate a random (fake) IPv6
        $data->attributes->ipv6 = implode(':', str_split(sha1(dechex(mt_rand(0, 2147483647))), 4));
        $data->attributes->name = 'ns1.'.$data->attributes->domain;

        if (is_null($message)) {
            $this->setExpectedExceptionRegExp(
              'opensrs\Exception',
              "/$field.*not defined/"
              );
        } else {
            $this->setExpectedExceptionRegExp(
              'opensrs\Exception',
              "/$message/"
              );
        }

        // clear field being tested
        if (is_null($parent)) {
            unset($data->$field);
        } else {
            unset($data->$parent->$field);
        }

        $ns = new NameserverDelete('array', $data);
    }

    /**
     * Invalid submission should throw an exception.
     *
     *
     * @group invalidsubmission
     */
    public function testInvalidSubmissionFieldsCookieAndDomainSent()
    {
        $data = json_decode(json_encode($this->validSubmission));

        // assign_ns request
        $data->cookie = md5(time());
        $data->attributes->domain = 'phptest'.time().'.com';

        // generate a random IPv4
        $data->attributes->ipaddress = long2ip(mt_rand());

        // generate a random (fake) IPv6
        $data->attributes->ipv6 = implode(':', str_split(sha1(dechex(mt_rand(0, 2147483647))), 4));
        $data->attributes->name = 'ns1.'.$data->attributes->domain;

        $this->setExpectedExceptionRegExp(
          'opensrs\Exception',
        '/.*cookie.*domain.*cannot.*one.*call.*/'
          );

        $ns = new NameserverDelete('array', $data);
    }

    /**
     * Invalid submission should throw an exception.
     *
     *
     * @group invalidsubmission
     */
    public function testInvalidSubmissionFieldsNoCookieOrDomainSent()
    {
        $data = json_decode(json_encode($this->validSubmission));

        // generate a random IPv4
        $data->attributes->ipaddress = long2ip(mt_rand());

        // generate a random (fake) IPv6
        $data->attributes->ipv6 = implode(':', str_split(sha1(dechex(mt_rand(0, 2147483647))), 4));
        $data->attributes->name = 'ns1.'.$data->attributes->domain;

        $this->setExpectedExceptionRegExp(
          'opensrs\Exception',
        '/(cookie|domain).*not.*defined/'
          );

        $ns = new NameserverDelete('array', $data);
    }
}
