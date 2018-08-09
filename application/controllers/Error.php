<?php

/**
 * 错误控制类
 */
class ErrorController extends \Our\Controller\AbstractIndex
{

    public function errorAction(Exception $exception)
    {
        $code = $exception->getCode();
        $msg = $exception->getMessage();
        if ($exception instanceof \Illuminate\Database\QueryException || $exception instanceof \PDOException) {
            $this->_handlerDbException($exception);
            $msg = "Server error.";
        } else {
            switch ($code) {
                case YAF_ERR_NOTFOUND_MODULE:
                case YAF_ERR_NOTFOUND_CONTROLLER:
                case YAF_ERR_NOTFOUND_ACTION:
                case YAF_ERR_CALL_FAILED:
                case YAF_ERR_AUTOLOAD_FAILED:
                    //$title = 'Not Found';
                    $msg = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found on this server.';
                    break;
                case \Error\CodeConfigModel::CANNOT_CONNECT_DATABASE:
                    $this->_handlerDbException($exception);
                    $msg = "Technical error.";
                    break;
                default:
                    break;
            }
        }
        YAF_ENVIRON != 'product' && ($msg = $msg . "<div style='display: none;'>($code - {$exception->getMessage()})</div>");
        $this->_showError($code, $msg);
    }

    /**
     * 错误页面入口
     */
    public function indexAction()
    {
        $code = intval($this->getRequest()->getQuery('code'));
        \Error\ErrorModel::throwException($code);
    }

    private function _showError($code, $msg)
    {
        if ($this->getRequest()->isXmlHttpRequest() && strpos($_SERVER['HTTP_ACCEPT'], 'json') !== false) {
            exit(json_encode(['success' => false, 'errorMsg' => $msg]));
        } else {
            $this->getView()->assign('code', $code);
            $this->getView()->assign('errorMsg', $msg);
        }
    }

    private function _handlerDbException(Exception $exception)
    {
        $msg = "[MESSAGE]: " . $exception->getMessage() . "\n";
        $msg .= "[DATETIME]: " . date('Y-m-d H:i:s') . " GMT\n";
        $msg .= "[IP]: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $msg .= "[SERVER]: " . $_SERVER['SERVER_ADDR'] . "\n";
        $msg .= "[URL]: http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'] . "\n\n";
        @file_put_contents(LOG_PATH . '/error.log', $msg, FILE_APPEND);
    }
}
