<?php

namespace App\Controllers;

use League\Flysystem\Filesystem;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ReceiptController
{

    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function store(Request $request, Response $response, array $args): Response
    {
        $file = $request->getUploadedFiles()['receipt'];
        $fileName = $file->getClientFilename();

        $this->filesystem->write('receipts/' . $fileName, $file->getStream()->getContents());

        return $response;
    }
}