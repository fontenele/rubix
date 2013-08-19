<?php

namespace %s\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;

/**
 * %s
 *
 * @package %s
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 */
class IndexController extends Controller {

    public function init() {
        $this->addBreadcrumb('%s', '/%s/index');
    }

}
