<?php
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\TwigFunction;

class Twig
{
    protected $CI;
    protected $twig;

    public function __construct($params = [])
    {
        $this->CI =& get_instance();

        // Make sure URL helper is loaded
        $this->CI->load->helper('url');

        $loader = new FilesystemLoader(APPPATH . 'views');

        $this->twig = new Environment($loader, [
            'cache' => APPPATH . 'cache/twig',
            'debug' => true,
        ]);

        // Register base_url() function for Twig
        $this->twig->addFunction(new TwigFunction('base_url', function ($uri = '') {
            return base_url($uri);
        }));

        
        $this->twig->addFunction(new TwigFunction('jdate', function ($data = '') {
            return jdate($data);
        }));

        $this->twig->addFunction(new TwigFunction('toNum', function ($data = '') {
            return FarsiNum::toNumber($data);
        }));

        $this->twig->addFunction(new TwigFunction('toPrc', function ($data = '') {
            return FarsiNum::toPrice($data);
        }));

        $this->twig->addFunction(new TwigFunction('flash', function ($data = '') {
            return $this->CI->session->flashdata($data);
        }));

        $this->twig->addFunction(new TwigFunction('session', function ($data = '') {
            return $this->CI->session->userdata($data);
        }));

        $this->twig->addFunction(new TwigFunction('json', function ($data = '') {
            return json_encode($data);
        }));
    }

    public function render($template, $data = [])
    {
        echo $this->twig->render($template, $data);
    }
}
