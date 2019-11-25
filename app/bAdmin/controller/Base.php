<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;

class Base extends BaseController
{
    const WEBSITE_CONFIG_PATH = __DIR__.'/../config/' . 'website_config.json';
}
