<?php

namespace App\Core\Tools\Contracts;

interface HasViews
{
    public function viewsPath(): string;

    public function viewsNamespace(): string;
}
