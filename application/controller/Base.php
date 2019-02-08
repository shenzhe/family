<?php

//file: application/controller/Index.php
namespace controller;


use Family\MVC\Controller;

abstract class Base extends Controller
{
    /**
     * @param $data
     * @param string $tplFile
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @desc 渲染模板
     */
    public function template($data, $tplFile = '')
    {
        if ('' == $tplFile) {
            $tplFile = $this->request->getAttribute(self::_CONTROLLER_KEY_)
                . DS . $this->request->getAttribute(self::_METHOD_KEY_)
                . '.twig';
        }
        return $this->template->render($tplFile, $data);
    }

    public function json($data)
    {
        return json_encode([
            'code' => 0,
            'msg' => '',
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    public function render($data)
    {
        return [
            'code' => 0,
            'msg' => '',
            'data' => $data
        ];
    }


}