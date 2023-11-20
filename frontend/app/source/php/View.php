<?php

namespace NavetSearch;

class View
{
    public function loadControllerData($view) {

        $view = ucfirst(trim(str_replace(' ', '', ucwords(str_replace(array("-","/"), ' ', $view))), "/"));
        if(file_exists(__DIR__ . "/Controller/" . $view . ".php")) {
            $class = "NavetSearch\\Controller\\".$view; 
            $obj = new $class;
            return $obj->data; 
        }

        return []; 
    }

    /**
     * @param $view
     * @param array $data
     */
    public function show($view, $data = array(), $blade)
    {
        //Run view
        try {
            $result = $blade->make(
                'pages.' . $view,
                array_merge(
                    $data, 
                    $this->loadControllerData($view)
                )
            )->render();

            $result = preg_replace('/(id|href)=""/', "", $result);

            if(class_exists("tidy")) {
                $tidy = new \tidy;
                $tidy->parseString($result, array(
                    'indent'         => true,
                    'output-xhtml'   => true,
                    'wrap'           => 5000,
                    'show-body-only' => false
                ), 'utf8');
                
                $tidy->cleanRepair();

                if(isset($tidy->value)) {
                    echo $tidy->value;
                }

            } else {
                echo $result; 
            }

        } catch (\Throwable $e) {
            echo $blade->make(
                'pages.E404',
                array_merge(
                    $data,
                    $this->loadControllerData("E404"),
                    array('errorMessage' => print_r(['line' => $e->getLine(), 'file' => $e->getFile(), 'other' => $e], true))
                )
            )->render();
        }
    }

    /**
     * Proxy for accessing provate props
     *
     * @return string Array of values
     */
    public static function accessProtected($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}
