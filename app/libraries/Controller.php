<?php 

class Controller  
{
    public function view($view, $data = [])
    {
        if (file_exists('../app/views/'.$view.'.php')) {
            require_once '../app/views/'.$view.'.php';
        } else {
            die('File tampilan tidak ada');
        }
    }

    public function model($model)
    {
        require_once '../app/models/'.$model.'.php';

        return new $model();
    }
}
