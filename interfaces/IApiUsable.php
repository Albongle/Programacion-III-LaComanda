<?php 

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface IApiUsable{ 

	public function CargarUno(Request $request, Response $response, $args);

}
?>