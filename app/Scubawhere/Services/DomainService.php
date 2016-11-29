<?php

namespace Scubawhere\Services;

use Scubawhere\Context;
use Aws\Route53\Route53Client;
use Scubawhere\Exceptions\Http\HttpInternalServerError;
use Scubawhere\Exceptions\Http\HttpPreconditionFailed;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

/**
 * Class DomainService
 * @package Scubawhere\Services
 *
 * Service object responsible for managing a companies sub domain for front facing applications
 * such as the customer sign up.
 */
class DomainService {

    /** @var Route53Client */
    protected $client;
    /** @var string */
    protected $base_domain       = '.scubawhere.com';
    /** @var string */
    protected $scubawhere_domain = 'scubawhererms.puzntmrpqp.eu-central-1.elasticbeanstalk.com';
    /** @var string */
    protected $hosted_zone_id    = 'Z3C7FNWG55PAQK';

    /**
     * @todo Pull credentials from secure s3 bucket or use env variables
     */
    public function __construct()
    {
        $this->client = Route53Client::factory(array(
            'credentials'   => array(
                'key'       => 'AKIAIDSABYCUP5PJ5IDQ',
                'secret'    => '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'
            )
        ));
    }

    /**
     * Ensure the string provided as the subdomain will create a fully qualified domain
     *
     * @param $subdomain
     * @return bool
     */
    public function validateDomain($subdomain)
    {
        $url = 'http://' . $subdomain . $this->base_domain;
        return ! filter_var($url, FILTER_VALIDATE_URL) === false ? true : false;
    }

    /**
     * Check that the company has not already registered a subdomian
     *
     * At the moment we are NOT allowing customers to re register a sub domain
     *
     * @return bool
     */
    protected function hasCompanyRegistered()
    {
        $subdomain = Context::get()->alias;
        return ! is_null($subdomain);
    }

    /**
     * Update the companies row in the DB with the new subdomain
     *
     * @param $subdomain
     * @throws HttpUnprocessableEntity
     */
    protected function updateCompanyAlias($subdomain)
    {
        if(!Context::get()->update(array('alias' => $subdomain))) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, Context::get()->errors()->all());
        }
    }

    /**
     * Register the subdomain with route53 to scubawhere's hosted zone
     *
     * @param $subdomain
     * @return \Guzzle\Service\Resource\Model
     * @throws HttpInternalServerError
     * @throws HttpPreconditionFailed
     * @throws HttpUnprocessableEntity
     */
    public function createSubdomain($subdomain)
    {
        if(!$this->validateDomain($subdomain)) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The subdomain is not valid, please enter a valid domain name']);
        }

        if($this->hasCompanyRegistered()) {
            throw new HttpPreconditionFailed(__CLASS__.__METHOD__,
                ['This account has already registered a sub domain, if you need to change this, please contact us at support@scubawhere.com']);
        }

        try {
            $result = $this->client->changeResourceRecordSets(array(
                // HostedZoneId is required
                'HostedZoneId' => $this->hosted_zone_id,
                // ChangeBatch is required
                'ChangeBatch' => array(
                    'Comment' => 'string',
                    // Changes is required
                    'Changes' => array(
                        array(
                            // Action is required
                            'Action' => 'CREATE',
                            // ResourceRecordSet is required
                            'ResourceRecordSet' => array(
                                // Name is required
                                'Name' => $subdomain . '.scubawhere.com.',
                                // Type is required
                                'Type' => 'CNAME',
                                'TTL' => 600,
                                'ResourceRecords' => array(
                                    array(
                                        // Value is required
                                        'Value' => $this->scubawhere_domain,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ));
        } catch(Exception $e) {
            throw new HttpInternalServerError(__CLASS__.__METHOD__);
        }

        $this->updateCompanyAlias($subdomain);

        return $result;
    }

}