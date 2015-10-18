<?php
use SfpDiactoros\Response\SwitchingEmitter;
use SfpDiactoros\Stream\RewindFpassthruStream;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\ServerRequest;
use Relay\RelayBuilder;

require_once __DIR__.'/../vendor/autoload.php';

$queue = [new SwitchingEmitter, function($request, $response){
    // $ dd if=/dev/zero of=/tmp/tempfile bs=1M count=10
    $image = '/tmp/tempfile';
    $response = $response->withHeader('Content-Type', 'image/jpeg')
        ->withHeader('Content-Length', (string) filesize($image));
    $stream = new RewindFpassthruStream($image);
    return $response->withBody($stream);
}];

$relayBuilder = new RelayBuilder();
$relay = $relayBuilder->newInstance($queue);
$relay(ServerRequestFactory::fromGlobals(), new Response());

