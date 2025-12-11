<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function breakfast()
    {
        return view('all-day-breakfast-menu');
    }

    public function mainCourses()
    {
        return view('main-courses-menu');
    }

    public function pasta(){
         return view('pasta-menu');
    } 

    public function chicken(){
            return view('chicken-menu');
    }

    
    public function drinks(){
            return view('drinks-menu');
    }

    public function pizza(){
            return view('pizza-menu');
    }

    public function snacks(){
        return view('snacks-menu');
    }
}

