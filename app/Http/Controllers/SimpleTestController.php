<?php

namespace App\Http\Controllers;

class SimpleTestController extends Controller
{
    public function test($id)
    {
        return "Simple test working! ID: " . $id;
    }
}