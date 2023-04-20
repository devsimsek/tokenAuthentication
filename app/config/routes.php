<?php
/**
 * Example routing schema,
 * $config['path/{pattern}'] = 'controller/method';
 * Pattern Shortcuts;
 * {url}, {id}, {all}
 * or
 * $config['path/{pattern}'] = ['controller/method', 'request_type'];
 * request_type = User request type. such as post, get and delete.
 */
$config['/'] = 'home';

$config['/api/auth'] = 'Auth/index';
$config['/api/auth/signin'] = 'Auth/signin';
$config['/api/auth/signup'] = 'Auth/signup';
$config['/api/auth/session'] = ['Auth/session', "POST"]; // check session exists, Request type: post - Body: {"token": token, "expire": expire_timestamp}
