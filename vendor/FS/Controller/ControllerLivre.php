<?php

namespace FS\Controller;

abstract class ControllerLivre extends Controller {

    protected function isAllowed() {
        return true;
    }

}
