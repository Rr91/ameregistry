<?php


class shopRegistrypagePluginBackendLayout extends shopBackendLayout {

    public function execute() {
        parent::execute();
        $this->assign('page', 'registrypage');
    }

}