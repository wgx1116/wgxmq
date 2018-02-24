<?php

namespace WgxMq;


use Unirest\Request;
use Unirest\Request\Body;
use Wgx\PDOEx;

class MqProcessorMysqlBackend extends MysqlBackend implements MqProcessorInterface
{
    /**
     * @param int $pno - 进程编号，非PID，当n个进程共同处理消息队列的时候，将进程依次编号 1..n
     * @return mixed
     */
    public function process($pno)
    {
        $pno = intval($pno);
        if ($pno < 1 || $pno > self::MqProcessorTableMaxCnt) {
            exit;
        }
        $table = "mq_${pno}";

        $mqSubscribeConfig = self::getMqSubscribeConfig();
        if (empty($mqSubscribeConfig)) {
            exit;
        }

        $now = new \DateTime();
        //$this->pdoEx->preExecuteSqlCallback = function ($sql, $p) {
        //    echo 'debug';
        //};
        $result = $this->pdoEx->select($table, '*', array('exec_dt' => array('<=' => $now->format(PDOEx::SqlDateTimeFormat))), null, array('id' => 'asc'), 1000)->getAll();
        $this->pdoEx->preExecuteSqlCallback = null;
        if (empty($result)) {
            exit;
        }
        foreach ($result as $msg) {
            $id = intval($msg['id']);
            $failLog = empty($msg['fail_log']) ? null : trim($msg['fail_log']);
            if ($failLog) {
                $failLog = json_decode($failLog, true);
            }
            $type = trim($msg['type']);
            $content = trim($msg['content']);

            if ($failLog) {
                $handlers = $failLog;
            } else {
                if (empty($mqSubscribeConfig[$type])) {
                    $this->pdoEx->delete($table, array('id' => $id));
                    continue;
                } else {
                    $handlers = array();
                    $t = $mqSubscribeConfig[$type];
                    foreach ($t as $url) {
                        $url = trim(strval($url));
                        if (empty($url)) {
                            continue;
                        }
                        $handlers[$url] = 0;
                    }
                }
            }
            if (empty($handlers)) {
                $this->pdoEx->delete($table, array('id' => $id));
                continue;
            }

            $newFailLog = array();
            foreach ($handlers as $url => $failCnt) {
                $resp = Request::post($url,
                    array('Content-Type' => 'application/json'),
                    Body::Json(array('content' => $content))
                );
                if ($resp->code >= 400) {
                    $newFailLog[$url] = $failCnt + 1;
                }
            }
            if (empty($newFailLog)) {
                $this->pdoEx->delete($table, array('id' => $id));
            } else {
                $minFailCnt = min($newFailLog);
                if ($minFailCnt < 3) {
                    $this->pdoEx->update($table, array('fail_log' => json_encode($newFailLog)), array('id' => $id));
                } else {
                    $this->pdoEx->insert('mq_fail', array(
                        'create_dt' => $msg['create_dt'],
                        'exec_dt' => $msg['exec_dt'],
                        'fail_log' => json_encode($newFailLog),
                        'type' => $type,
                        'content' => $content,
                    ));
                    $this->pdoEx->delete($table, array('id' => $id));
                }
            }
        }
    }

    private static function getMqSubscribeConfig()
    {
        static $mqSubscribeConfig = null;

        if (!$mqSubscribeConfig) {
            $mqSubscribeConfig = require BASEPATH . '/config/mqsubscribe.php';
        }

        return $mqSubscribeConfig;
    }
}