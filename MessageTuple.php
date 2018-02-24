<?php

namespace WgxMq;


class MessageTuple
{
    /** @var string */
    public $type;
    /** @var \DateTime */
    public $execDt;
    /** @var string */
    public $content;

    /**
     * MessageTuple constructor.
     * @param string $type
     * @param \DateTime $execDt
     * @param string $content
     */
    public function __construct($type, \DateTime $execDt, $content)
    {
        $this->type = $type;
        $this->execDt = $execDt;
        $this->content = $content;
    }

}