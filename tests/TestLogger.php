<?php

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        $msg = sprintf('Eventflit: %s: %s', strtoupper($level), $msg);
        $replacement = array();

        foreach ($context as $k => $v) {
            $replacement['{'.$k.'}'] = $v;
        }

        print_r("\n".strtr($msg, $replacement));
    }
}
