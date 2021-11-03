<?php
if (!$this->hasConfig()) {
    // copy data directory
    rex_dir::copy($this->getPath('data'), $this->getDataPath());
    $this->setConfig('date_install', time());
}
