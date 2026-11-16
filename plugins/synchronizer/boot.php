<?php

if ('cli' !== PHP_SAPI && rex::isBackend() && rex::getUser()) {
    GridblockSynchronizer::syncThrottled();
}
