<?php
//多点乐资源
namespace App\Http\Controllers;

class ExampleController extends Controller
{
	public function __construct()
	{
	}

	public function index()
	{
		return array('key' => 'example api.');
	}
}

?>
