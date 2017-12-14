<?php
namespace App\Contracts;

interface PostContract
{
    public static function fetchAll();

    public static function fetch($slug);
}
