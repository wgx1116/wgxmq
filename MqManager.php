<?php

namespace WgxMq;


class MqManager
{
    /** @var MqClientInterface */
    private static $mqClient;
    /** @var MqProcessorInterface */
    private static $mqProcessor;

    /**
     * @return MqClientInterface
     */
    public static function getMqClient()
    {
        return self::$mqClient;
    }

    /**
     * @return MqProcessorInterface
     */
    public static function getMqProcessor()
    {
        return self::$mqProcessor;
    }

    /**
     * @param MqClientInterface $mqClient
     * @param MqProcessorInterface $mqProcessor
     */
    public static function init(MqClientInterface $mqClient = null, MqProcessorInterface $mqProcessor = null)
    {
        self::$mqClient = $mqClient;
        self::$mqProcessor = $mqProcessor;
    }

}