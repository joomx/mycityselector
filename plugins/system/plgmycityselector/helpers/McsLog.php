<?php

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
    public static function render()
    {
        $html = '';
        if (McsData::get('debug_mode') && !empty(self::$logs)) {
            $html .= '<div id="mcs-logs" style="margin: 10px; border: 1px solid blue; padding: 10px 4px;">';
            $html .= '<h4>MCS LOG</h4>';
            foreach (self::$logs as $log) {
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
        }
        return $html;
    }

}