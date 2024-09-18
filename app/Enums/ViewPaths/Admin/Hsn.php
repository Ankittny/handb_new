<?php

namespace App\Enums\ViewPaths\Admin;

enum Hsn
{
    const LIST = [
        URI => 'list',
        VIEW => 'admin-views.hsn.list'
    ];

    const ADD = [
        URI => 'add',
        VIEW => ''
    ];
    const UPDATEDATA = [
        URI => 'updatedata',
        VIEW => ''
    ];
    
    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.hsn.update-view'
    ];

    const DEL = [
        URI => 'delete',
        VIEW => ''
    ];
   
}
