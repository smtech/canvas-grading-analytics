<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/constants.inc.php';

use smtech\GradingAnalytics\Toolbox;
use smtech\ReflexiveCanvasLTI\LTI\ToolProvider;
use Battis\DataUtilities;

@session_start(); // TODO I don't feel good about suppressing warnings

/* prepare the toolbox */
if (empty($_SESSION[Toolbox::class])) {
    $_SESSION[Toolbox::class] = Toolbox::fromConfiguration(CONFIG_FILE);
}
$toolbox =& $_SESSION[Toolbox::class];

/* identify the tool's Canvas instance URL */
if (empty($_SESSION[CANVAS_INSTANCE_URL])) {
    if (!empty($_SESSION[ToolProvider::class]['canvas']['api_domain'])) {
        $_SESSION[CANVAS_INSTANCE_URL] =
            'https://' . $_SESSION[ToolProvider::class]['canvas']['api_domain'];
    } elseif (!empty($_SERVER['HTTP_REFERER'])) {
        $_SESSION[CANVAS_INSTANCE_URL] =
            'https://' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    } else {
        $_SESSION[CANVAS_INSTANCE_URL] =
            'https://' . parse_url($toolbox->config(Toolbox::TOOL_CANVAS_API)['url'], PHP_URL_HOST);
    }
}

if (!defined('IGNORE_UI')) {
    $toolbox->smarty_assign([
        'category' => DataUtilities::titleCase(preg_replace('/[\-_]+/', ' ', basename(__DIR__)))
    ]);
    if (!empty($_SESSION[CANVAS_INSTANCE_URL])) {
        $toolbox->smarty_assign('canvasInstanceUrl', $_SESSION[CANVAS_INSTANCE_URL]);
    }
}
