<?php

namespace Vinelab\Minion;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */
abstract class Provider
{
    /**
     * The topic prefix.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Constructor.
     *
     * @param \Vinelab\Minion\Client $client
     */
    public function __construct($client)
    {
        $client->setDelegateProvider($this);
        $client->setTopicPrefix($this->prefix);

        $this->setClient($client);
    }

    /**
     * Boot this provider. This is the best place to have
     * your subscriptions/registrations for your RPCs and PubSub.
     */
    abstract public function boot();

    /**
     * Set the client instance.
     *
     * @param \Mulkave\Minion\Client $client
     */
    private function setClient(Client $client)
    {
        $this->minionClient = $client;
    }

    /**
     * Get the client instance.
     *
     * @return \Vinelab\Minion\Client
     */
    protected function getClient()
    {
        return $this->minionClient;
    }

    /**
     * Override to redirect non-existing calls to the client.
     * This allows calling client methods directly from the provider which is useful
     * when passing the Client to a registered Closure.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return string
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getClient(), $name], $arguments);
    }

    /**
     * Publish to a topic with the given data.
     *
     * @param string      $topic
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array       $options
     *
     * @return \React\Promise\Promise
     */
    public function publish($topic, $arguments = null, $argumentsKw = null, $options = null)
    {
        return $this->getClient()->getSession()
            ->publish($this->prefix . $topic, $arguments, $argumentsKw, $options);
    }
}
