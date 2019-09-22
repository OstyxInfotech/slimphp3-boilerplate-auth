<?php

return [
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "headers.allow" => ["Authorization"],
    "headers.expose" => [],
    "credentials" => false,
    "cache" => 0,
];