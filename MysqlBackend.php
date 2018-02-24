<?php

namespace WgxMq;


use Wgx\PDOEx;

class MysqlBackend
{
    const MqClientTableMaxCnt = 10;
    const MqProcessorTableMaxCnt = 8;

    /** @var PDOEx */
    protected $pdoEx;

    public function __construct(PDOEx $pdoEx)
    {
        $this->pdoEx = $pdoEx;
    }
}