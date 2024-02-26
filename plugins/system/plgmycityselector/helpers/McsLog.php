<?php
namespace joomx\mcs\plugin\helpers;


defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

/**
 * Log
 */
class McsLog
{

    const INFO = 1;
    const WARN = 2;
    const ERR = 3;

    private static $logs = [];

    /**
     * Добавляет сообщение в логи
     * @param string $message
     * @param int $type
     */
    public static function add($message, $type = self::INFO)
    {
        if (McsData::get('debug_mode')) {
            self::$logs[] = ['message' => $message, 'type' => $type];
        }
    }

    /**
     *
     */
    public static function render(&$body)
    {
        $html = '';
        if (!empty(self::$logs)) {
            if (McsData::get('debug_mode')) {
                if (McsData::get('debug_mode_hidden') == '1') {
                    $html .= "<!-- MCS DEBUG\n";
                    foreach (self::$logs as $log) {
                        $log['message'] = is_string($log['message']) ? $log['message'] : print_r($log['message'], true);
                        $html .= "{$log['message']}\n";
                    }
                    $html .= '-->';
                    $body = str_replace('</body>', $html . "\n</body>", $body);
                } else {
                    $html .= '<div id="mcs-logs" style="margin: 10px; border: 1px solid blue; padding: 10px 4px;">';
                    $html .= '<h4>MCS LOG</h4>';
                    foreach (self::$logs as $log) {
                        $log['message'] = is_string($log['message']) ? $log['message'] : print_r($log['message'], true);
                        $html .= '<div class="log-item" style="border-top: 1px dashed blue;">';
                        $style = 'color: black;';
                        if ($log['type'] == self::WARN) {
                            $style = 'color: orange; font-weight: bold;';
                        } else if ($log['type'] == self::ERR) {
                            $style = 'color: red; font-weight: bold;';
                        }
                        $html .= '<p style="' . $style . '">' . $log['message'] . '</p>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                    $body = str_replace('</body>', $html . "\n</body>", $body);
                }
                self::$logs = [];
            }
        }
    }


    public static function toFile($isAdminPage = null, $isEditMode = null)
    {
        if (McsData::get('log_to_file')) {
            $file = JPATH_ROOT . '/mcs.log';
            $output = "=== " . date('d-m-Y H:i:s') . " ===\n";
            $output .= "HTTP_USER_AGENT: {$_SERVER['HTTP_USER_AGENT']}\n";
            $output .= "HTTP_REFERER: {$_SERVER['HTTP_REFERER']}\n";
            $output .= "REQUEST_METHOD: {$_SERVER['REQUEST_METHOD']}\n";
            $output .= "REQUEST_URI: {$_SERVER['REQUEST_URI']}\n";
            $output .= "QUERY_STRING: {$_SERVER['QUERY_STRING']}\n";
            $output .= "Is admin page: ".($isAdminPage ? 'true' : 'false')."\n";
            $output .= "Is edit mode: ".($isEditMode ? 'true' : 'false')."\n";
            if (!empty(self::$logs)) {
                foreach (self::$logs as $log) {
                    $output .= "{$log['message']}\n";
                }
            }
            file_put_contents($file, $output . "\n", 8);
        }
    }

}
