<?php
//zend by 多点乐资源
namespace App\Modules\Api\Foundation;

abstract class Transformer
{
	public function transformCollection(array $map)
	{
		return array_map(array($this, 'transform'), $map);
	}

	abstract public function transform(array $map);
}


?>
