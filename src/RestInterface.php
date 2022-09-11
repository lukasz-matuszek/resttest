<?php

namespace Lib;

interface RestInterface
{

    /* retrieve data */
    public function get(array $data,   string $apiUrl): array|null;

    /* create new item */
    public function post(array $data,  string $apiUrl): array|null;

    /* update an item */
    public function put(array $data,  string $apiUrl): array|null;

    /* update item's attributes  */
    public function patch(array $data,   string $apiUrl): array|null;

    /* delete an item */
    public function delete(array $data,   string $apiUrl): array|null;

}