<?php

namespace FS\View\Helper;

use Zend\View\Helper\BasePath as AbstractBasePath;
use Zend\View\Exception;

class BasePath extends AbstractBasePath {

    /**
     * Returns site's base path, or file with base path prepended.
     *
     * $file is appended to the base path for simplicity.
     *
     * @param  string|null $file
     * @return string
     */
    public function __invoke($file = null) {
        return APPLICATION_URL;
    }

}
