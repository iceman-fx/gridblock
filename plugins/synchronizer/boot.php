<?php

if (rex_backend_login::hasSession()) {
    GridblockSynchronizer::sync();
}
