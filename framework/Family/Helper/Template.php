<?php
//file framework/Helper/Template.php
namespace Family\Helper;


use Family\Core\Config;
use Family\Core\Singleton;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Template
{
    use Singleton;

    public $template;

    public function __construct()
    {
        $loader = new FilesystemLoader(Config::get('template.path'));
        $this->template = new Environment($loader, array(
            'cache' => Config::get('template.cache'),
        ));
    }
}