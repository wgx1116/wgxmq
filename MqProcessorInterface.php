<?php

namespace WgxMq;


interface MqProcessorInterface
{
    /**
     * @param int $pno - 进程编号，非PID，当n个进程共同处理消息队列的时候，将进程依次编号 1..n
     * @return mixed
     */
    public function process($pno);
}