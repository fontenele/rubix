<?php

namespace Rubix\View\Helper;

use Zend\View\Helper\AbstractHelper;

class BasePath extends AbstractHelper {

    public function __invoke($public = true) {
        if(!CONTROLLER_ROUTE_HOST) {
            return APPLICATION_URL;
        }else{
            return APPLICATION_URL . ($public ? 'public/' : '');
        }
    }

    /**
     * Set the base path.
     *
     * @param  string $basePath
     * @return self
     */
    public function setBasePath($basePath) {
        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

}
