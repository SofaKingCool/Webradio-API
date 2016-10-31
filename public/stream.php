<?php

// Include bootstrapping script
require __DIR__ . "/../bootstrap.php";

// Show a JSON error, because streaming is not supported yet (soon™)
error(404, "Unsupported");
