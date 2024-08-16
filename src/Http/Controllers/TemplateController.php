<?php
namespace Lcloss\Template\Http\Controllers;

class TemplateController
{
    public function index()
    {
        return view('template.dist.index');
    }

    public function page(string $page)
    {
        return view('template.dist.' . $page);
    }
}
