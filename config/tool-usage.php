<?php

return [
    'anonymous' => ['max_executions' => 20, 'window_seconds' => 3600],
    'authenticated' => ['max_executions' => 100, 'window_seconds' => 3600],
    'premium' => ['max_executions' => 500, 'window_seconds' => 3600],
];
