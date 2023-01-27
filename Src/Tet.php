<?php

namespace Tet;

include("Common/Collection.php");
include("Common/Params.php");
include("Common/Result.php");
include("Common/Utils.php");
include("Common/ErrorHandler.php");
include("Common/Fasades.php");

include("HTTP/Header.php");
include("HTTP/Headers.php");

include("HTTP/Server.php");
include("HTTP/ServerRequest.php");

include("HTTP/Client.php");
include("HTTP/ClientRequest.php");
include("HTTP/Response.php");

include("Filesystem/Filesystem.php");
include("Filesystem/Path.php");
include("Filesystem/File.php");
include("Filesystem/Directory.php");

include("Database/Db.php");
include("Database/FieldCollection.php");
include("Database/Table.php");
include("Database/Row.php");
include("Database/Mysql.php");
include("Database/Query.php");

include("Mail/Mail.php");


include("Routing/Router.php");
include("Routing/Routes.php");
include("Routing/Route.php");


use Tet\HTTP\Response;
use Tet\Routing\Route;
/**
 * Обеспечивает необходимый функционал для разработки несложных API:
 * - получение параметров запроса
 * - работа с базой MySQL
 * - работа с файлами
 * - формирование ответа в пользовательской функции
 * @author Sergey V. Afanasyev <sergey.v.afanasyev@gmail.com>
 */

class Tet
{

    protected Router $router;
    protected Fasades $fasades;

    function __construct()
    {
        (new ErrorHandler)->setErrorHandler();
        (new ErrorHandler)->setExeptionHandler();
    }


    // function autoload(string $path)
    // {
    //     $files = (new FileSystem)->getDirectory($path)->getFileList(["*.php"]);
    //     foreach ($files as $key => $file) {
    //         //include($file);
    //     }
    // }


    function getRouter(): Router
    {
        if (!isset($this->router)) $this->router = new Router;
        return $this->router;
    }

    function getFasades(): Fasades
    {
        if (!isset($this->fasades)) $this->fasades = new Fasades;
        return $this->fasades;
    }

    function run(): bool
    {
        $route = $this->router->getMatchedRoute();
        if (!$route) return false; 
        $response = $this->executeRouteCallback($route);       
        $this->fasades->getServer()->sendResponse($response);
        return true;
    }

    private function executeRouteCallback(Route $route):Response
    {
        switch (gettype($route->callback)) {
            case 'object':
            case 'array':
                return call_user_func_array($route->callback, array($this->fasades, $route->getArguments()));
                break;
            default:
                return $route->callback;
        };
    }

}