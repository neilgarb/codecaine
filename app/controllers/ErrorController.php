<?php

class ErrorController extends App_Controller_Action 
{
	public function errorAction ()
	{
		header('HTTP/1.1 404 Not Found');

		$error = $this->_getParam('error_handler');
		$this->view->exception = $error->exception;
		
		var_dump($this->view->exception);
		die;

		$this->_title('Not Found');
	}
}
