<?php

namespace WgxMq;


use Wgx\PDOEx;

class MqClientMysqlBackend extends MysqlBackend implements MqClientInterface
{
    /**
     * @param string $type
     * @param \DateTime $execDt
     * @param string $content
     * @return mixed
     */
    public function add($type, \DateTime $execDt, $content)
    {
        if (empty($type) || empty($execDt) || empty($content)) {
            return;
        }
        $type = trim(strval($type));
        $content = trim(strval($content));
        if (empty($type) || empty($content)) {
            return;
        }

        $table = self::randomAMqTableForClientAdd();
        if (empty($table)) {
            return;
        }

        $now = new \DateTime();

        $this->pdoEx->insert($table, array(
            'create_dt' => $now->format(PDOEx::SqlDateTimeFormat),
            'exec_dt' => $execDt->format(PDOEx::SqlDateTimeFormat),
            'type' => $type,
            'content' => $content,
        ));
    }

    /**
     * @param MessageTuple[] $msgArr
     * @return mixed
     */
    public function addMulti($msgArr)
    {
        if (empty($msgArr) || !is_array($msgArr)) {
            return;
        }

        $now = new \DateTime();
        $nowStr = $now->format(PDOEx::SqlDateTimeFormat);
        $data = array();
        foreach ($msgArr as $msg) {
            $type = empty($msg->type) ? null : trim(strval($msg->type));
            $content = empty($msg->content) ? null : trim(strval($msg->content));
            if (empty($type) || empty($msg->execDt) || empty($content)) {
                continue;
            }
            $data[] = array($nowStr, $msg->execDt->format(PDOEx::SqlDateTimeFormat), $type, $content);
        }

        $table = self::randomAMqTableForClientAdd();
        if (empty($table)) {
            return;
        }

        $this->pdoEx->insertMultiValue($table, array('create_dt', 'exec_dt', 'type', 'content'), $data);
    }

    /**
     * @return null|string
     */
    private static function randomAMqTableForClientAdd()
    {
        srand(time());
        $clientTableMaxCnt = min(self::MqClientTableMaxCnt, self::MqProcessorTableMaxCnt);
        if ($clientTableMaxCnt < 1) {
            return null;
        }

        $r = rand(1, $clientTableMaxCnt);

        return "mq_${r}";
    }
}