<?php

namespace WgxMq;


interface MqClientInterface
{
    /**
     * @param string $type
     * @param \DateTime $execDt
     * @param string $content
     * @return mixed
     */
    public function add($type, \DateTime $execDt, $content);

    /**
     * @param MessageTuple[] $msgArr
     * @return mixed
     */
    public function addMulti($msgArr);

}